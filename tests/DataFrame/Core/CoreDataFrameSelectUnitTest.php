<?php namespace Archon\Tests\DataFrame\Core;

use Archon\DataFrame;

class CoreDataFrameSelectUnitTest extends \PHPUnit_Framework_TestCase
{

    public function testDataFrameSelect()
    {
        $df = DataFrame::fromArray([
            ['a' => 1, 'b' => 2, 'c' => 3],
            ['a' => 4, 'b' => 5, 'c' => 6],
            ['a' => 7, 'b' => 8, 'c' => 9],
        ]);

        $df = $df->query("SELECT a, c
        FROM dataframe
        WHERE a = '4'
          OR b = '2';");

        $expected = [
            ['a' => 1, 'c' => 3],
            ['a' => 4, 'c' => 6]
        ];

        $this->assertEquals($expected, $df->toArray());
    }

    public function testDataFrameSelectUpdate()
    {
        $df = DataFrame::fromArray([
            ['a' => 1, 'b' => 2, 'c' => 3],
            ['a' => 4, 'b' => 5, 'c' => 6],
            ['a' => 7, 'b' => 8, 'c' => 9],
        ]);

        $df = $df->query("UPDATE dataframe
        SET a = c * 2;");

        print_r($df['a']->toArray());
        $expected = [
            ['a' => 6, 'b' => 2, 'c' => 3],
            ['a' => 12, 'b' => 5, 'c' => 6],
            ['a' => 18, 'b' => 8, 'c' => 9],
        ];

        $this->assertEquals($expected, $df->toArray());
    }


}