<?php namespace Archon\Tests\DataFrame\Core;

use Archon\DataFrame;

class CoreDataFrameUnitTest extends \PHPUnit_Framework_TestCase {

    public function testFromArray() {
        $df = DataFrame::fromArray([
            [
                'a',
                'b',
                'c'
            ]
        ]);

        $this->assertEquals([['a', 'b', 'c']], $df->toArray());
        $this->assertEquals([0, 1, 2], $df->columns());

    }

    public function testFromArrayColumns_1() {
        $df = DataFrame::fromArray([
            [
                'a' => 'A',
                'b' => 'B',
                'c' => 'C'
            ]
        ]);

        $this->assertEquals(['a', 'b', 'c'], $df->columns());
    }

    public function testFromArrayColumns_2() {
        $df = DataFrame::fromArray([
            [
                'A',
                'B',
                'C'
            ]
        ], [ 'columns' => ['a', 'b', 'c'] ]);

        $this->assertEquals(['a', 'b', 'c'], $df->columns());
    }

    public function testForEach() {
        $input = [
            ['a' => 1, 'b' => 2, 'c' => 3],
            ['a' => 4, 'b' => 5, 'c' => 6],
            ['a' => 7, 'b' => 8, 'c' => 9],
        ];

        $df = DataFrame::fromArray($input);

        foreach($df as $i => $row) {
            $this->assertEquals($row, $input[$i]);
        }
    }

    public function testOffsetGet() {
        $input = [
            ['a' => 1, 'b' => 2],
            ['a' => 3, 'b' => 4],
            ['a' => 5, 'b' => 6],
        ];

        $df = DataFrame::fromArray($input);
        $a = $df['a'];
        $b = $df['b'];

        $this->assertEquals([['a' => 1],['a' => 3],['a' => 5]], $a->toArray());
        $this->assertEquals([['b' => 2],['b' => 4],['b' => 6]], $b->toArray());
        $this->assertEquals($input, $df->toArray());
    }
}
