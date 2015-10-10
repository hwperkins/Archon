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

}
