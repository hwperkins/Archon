<?php namespace Archon\Tests\DataFrame\Core;

use Archon\DataFrame;
use PHPUnit\Framework\TestCase;

class CoreDataFrameUnitTest extends TestCase
{

    /** @var DataFrame */
    private $df;

    private $input = [
        ['a' => 1, 'b' => 2, 'c' => 3],
        ['a' => 4, 'b' => 5, 'c' => 6],
        ['a' => 7, 'b' => 8, 'c' => 9],
    ];

    public function setUp()
    {
        $this->df = DataFrame::fromArray($this->input);
    }

    public function testFromArray()
    {
        $this->assertEquals($this->input, $this->df->toArray());
    }

    public function testColumns()
    {
        $this->assertEquals(['a', 'b', 'c'], $this->df->columns());
    }

    public function testRemoveColumn()
    {
        $df = $this->df;

        $df->removeColumn('a');
        $expected = [
            ['b' => 2, 'c' => 3],
            ['b' => 5, 'c' => 6],
            ['b' => 8, 'c' => 9],
        ];

        $this->assertEquals($expected, $df->toArray());
    }

    public function testForEach()
    {
        foreach ($this->df as $i => $row) {
            $this->assertEquals($row, $this->input[$i]);
        }
    }

    public function testOffsetGet()
    {
        $a = $this->df['a'];
        $b = $this->df['b'];

        $this->assertEquals([['a' => 1], ['a' => 4], ['a' => 7]], $a->toArray());
        $this->assertEquals([['b' => 2], ['b' => 5], ['b' => 8]], $b->toArray());
    }

    public function testOffsetSetValue()
    {
        $df = $this->df;
        $df['a'] = 321;

        $expected = [
            ['a' => 321, 'b' => 2, 'c' => 3],
            ['a' => 321, 'b' => 5, 'c' => 6],
            ['a' => 321, 'b' => 8, 'c' => 9],
        ];

        $this->assertEquals($expected, $df->toArray());
    }

    public function testOffsetSetClosure()
    {
        $df = $this->df;

        $add = function ($x) {
            return function ($y) use ($x) {
                return $x + $y;
            };
        };

        $df['a'] = $add(10);
        $df['b'] = $add(20);
        $df['c'] = $add(30);

        $expected = [
            ['a' => 11, 'b' => 22, 'c' => 33],
            ['a' => 14, 'b' => 25, 'c' => 36],
            ['a' => 17, 'b' => 28, 'c' => 39],
        ];

        $this->assertEquals($expected, $df->toArray());
    }

    public function testOffsetSetDataframe()
    {
        $df = $this->df;

        $df['a'] = $df['b'];

        $expected = [
            ['a' => 2, 'b' => 2, 'c' => 3],
            ['a' => 5, 'b' => 5, 'c' => 6],
            ['a' => 8, 'b' => 8, 'c' => 9],
        ];

        $this->assertEquals($expected, $df->toArray());
    }

    public function testOffsetSetNewColumn()
    {
        $df = $this->df;

        $df['d'] = $df['c']->apply(function ($el) {
            return $el + 1;
        });

        $expected = [
            ['a' => 1, 'b' => 2, 'c' => 3, 'd' => 4],
            ['a' => 4, 'b' => 5, 'c' => 6, 'd' => 7],
            ['a' => 7, 'b' => 8, 'c' => 9, 'd' => 10],
        ];

        $this->assertEquals($expected, $df->toArray());
    }

    public function testApplyDataFrame()
    {
        $df = $this->df;

        $df->apply(function ($row) {
            $row['b'] = $row['a'] + 2;
            $row['c'] = $row['b'] + 2;
            return $row;
        });

        $expected = [
            ['a' => 1, 'b' => 3, 'c' => 5],
            ['a' => 4, 'b' => 6, 'c' => 8],
            ['a' => 7, 'b' => 9, 'c' => 11],
        ];

        $this->assertEquals($expected, $df->toArray());
    }

    public function testIsset()
    {
        $this->assertEquals(true, isset($this->df['a']));
        $this->assertEquals(false, isset($this->df['foo']));
    }

    public function testApplyIndexMapValues()
    {
        $df = $this->df;

        $df->applyIndexMap([
            0 => 0,
            2 => 0,
        ], 'a');

        $this->assertEquals([
            ['a' => 0, 'b' => 2, 'c' => 3],
            ['a' => 4, 'b' => 5, 'c' => 6],
            ['a' => 0, 'b' => 8, 'c' => 9],
        ], $df->toArray());
    }

    public function testApplyIndexMapFunction()
    {
        $df = $this->df;

        $df->applyIndexMap([
            0 => function($row) {
                $row['a'] = 10;
                return $row;
            },
            2 => function($row) {
                $row['c'] = 20;
                return $row;
            },
        ]);

        $this->assertEquals([
            ['a' => 10, 'b' => 2, 'c' => 3],
            ['a' => 4, 'b' => 5, 'c' => 6],
            ['a' => 7, 'b' => 8, 'c' => 20],
        ], $df->toArray());
    }

    public function testApplyIndexMapValueFunction()
    {
        $df = $this->df;

        $my_function = function($value) {
            if ($value < 4) {
                return 0;
            } else if ($value > 4) {
                return 1;
            } else {
                return $value;
            }
        };

        $df->applyIndexMap([
            0 => $my_function,
            2 => $my_function,
        ], 'a');

        $this->assertEquals([
            ['a' => 0, 'b' => 2, 'c' => 3],
            ['a' => 4, 'b' => 5, 'c' => 6],
            ['a' => 1, 'b' => 8, 'c' => 9],
        ], $df->toArray());
    }

    public function testApplyIndexMapArray()
    {
        $df = $this->df;

        $df->applyIndexMap([
            1 => [ 'a' => 301, 'b' => 404, 'c' => 500 ],
        ]);

        $this->assertEquals([
            ['a' => 1, 'b' => 2, 'c' => 3],
            ['a' => 301, 'b' => 404, 'c' => 500],
            ['a' => 7, 'b' => 8, 'c' => 9],
        ], $df->toArray());
    }

    public function testFilter()
    {
        $df = $this->df;

        $df = $df->array_filter(function($row) {
            return $row['a'] > 4 || $row['a'] < 4;
        });

        $this->assertEquals([
            [ 'a' => 1, 'b' => 2, 'c' => 3 ],
            [ 'a' => 7, 'b' => 8, 'c' => 9 ],
        ], $df->toArray());
    }

    public function testOffsetSetValueArray()
    {
        $df = $this->df;

        $df[] = [ 'a' => 10, 'b' => 11, 'c' => 12 ];

        $this->assertEquals([
            [ 'a' => 1, 'b' => 2, 'c' => 3 ],
            [ 'a' => 4, 'b' => 5, 'c' => 6 ],
            [ 'a' => 7, 'b' => 8, 'c' => 9 ],
            [ 'a' => 10, 'b' => 11, 'c' => 12 ],
        ], $df->toArray());
    }

    public function testAppend()
    {
        $df1 = $this->df;
        $df2 = $this->df;

        $df1->append($df2);

        $this->assertEquals([
            [ 'a' => 1, 'b' => 2, 'c' => 3 ],
            [ 'a' => 4, 'b' => 5, 'c' => 6 ],
            [ 'a' => 7, 'b' => 8, 'c' => 9 ],
            [ 'a' => 1, 'b' => 2, 'c' => 3 ],
            [ 'a' => 4, 'b' => 5, 'c' => 6 ],
            [ 'a' => 7, 'b' => 8, 'c' => 9 ],
        ], $df1->toArray());
    }
}
