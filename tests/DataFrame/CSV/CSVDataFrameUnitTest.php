<?php namespace Archon\Tests\DataFrame\Core;

use Archon\DataFrame;

class CSVDataFrameUnitTest extends \PHPUnit_Framework_TestCase {

    public function testFromCSV() {
        $fileName = __DIR__.DIRECTORY_SEPARATOR.'TestFiles'.DIRECTORY_SEPARATOR.'testCSV.csv';

        $df = DataFrame::fromCSV($fileName);

        $this->assertEquals([
            ['a' => 1, 'b' => 2, 'c' => 3],
            ['a' => 4, 'b' => 5, 'c' => 6],
        ], $df->toArray());
    }

    public function testFromCSVcolMap() {
        $fileName = __DIR__.DIRECTORY_SEPARATOR.'TestFiles'.DIRECTORY_SEPARATOR.'testCSV.csv';

        $df = DataFrame::fromCSV($fileName, [
            'colmap' => [
                'a' => 'x',
                'b' => 'y',
                'c' => 'z'
            ]
        ]);

        $this->assertEquals([
            ['x' => 1, 'y' => 2, 'z' => 3],
            ['x' => 4, 'y' => 5, 'z' => 6],
        ], $df->toArray());
    }

    public function testFromCSVcolMapToNull() {
        $fileName = __DIR__.DIRECTORY_SEPARATOR.'TestFiles'.DIRECTORY_SEPARATOR.'testCSV.csv';

        $df = DataFrame::fromCSV($fileName, [
            'colmap' => [
                'a' => 'x',
                'b' => null,
                'c' => 'z'
            ]
        ]);

        $this->assertEquals([
            ['x' => 1, 'z' => 3],
            ['x' => 4, 'z' => 6],
        ], $df->toArray());
    }

    public function testFromCSVcolMapToNull_2() {
        $fileName = __DIR__.DIRECTORY_SEPARATOR.'TestFiles'.DIRECTORY_SEPARATOR.'testCSV.csv';

        $df = DataFrame::fromCSV($fileName, [
            'colmap' => [
                'a' => 'x',
                'b' => null,
                'c' => 'z',
                'doesnt_exist' => 'b',
                'doesnt_exist_either' => null,
            ]
        ]);

        $this->assertEquals([
            ['x' => 1, 'z' => 3],
            ['x' => 4, 'z' => 6],
        ], $df->toArray());
    }

}
