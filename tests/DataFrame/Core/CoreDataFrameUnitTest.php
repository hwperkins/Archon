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

}
