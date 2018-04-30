<?php namespace Archon\Tests\DataFrame\Core;

use Archon\DataFrame;
use PHPUnit\Framework\TestCase;

class CoreDataFrameExceptionsTest extends TestCase
{

    public function setUp()
    {
        $this->input = [
            ['a' => 1, 'b' => 2, 'c' => 3],
            ['a' => 4, 'b' => 5, 'c' => 6],
            ['a' => 7, 'b' => 8, 'c' => 9],
        ];

        $this->df = DataFrame::fromArray($this->input);
    }

    public function testInvalidColumn()
    {
        $this->expectException('Archon\Exceptions\InvalidColumnException');
        $this->df['foo'];
    }

    public function testRemoveNonExistentColumn()
    {
        $this->expectException('Archon\Exceptions\DataFrameException');
        $this->df->removeColumn('foo');
    }

    public function testInvalidOffsetSet1()
    {
        $df = $this->df;

        $this->expectException('Archon\Exceptions\DataFrameException');
        $df['foo'] = $df;
    }

    public function testInvalidOffsetSet2()
    {
        $df = $this->df;
        $df2 = DataFrame::fromArray([['a' => 1, 'b' => 2, 'c' => 3]]);

        $this->expectException('Archon\Exceptions\DataFrameException');
        $df['a'] = $df2['a'];
    }
}
