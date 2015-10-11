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

    public function testSaveCSV() {
        $fileName = __DIR__.DIRECTORY_SEPARATOR.'TestFiles'.DIRECTORY_SEPARATOR.'testCSVSave.csv';
        if (file_exists($fileName)) {
            unlink($fileName);
        }

        $df = DataFrame::fromArray([
            ['a' => 1, 'b' => 2, 'c' => 3],
            ['a' => 4, 'b' => 5, 'c' => 6],
            ['a' => 7, 'b' => 8, 'c' => 9],
        ]);

        $q = "\"";
        $df->toCSV($fileName, ['quote' => $q]);

        $data = file_get_contents($fileName);

        if (file_exists($fileName)) {
            unlink($fileName);
        } else {
            $this->fail("File should exist but does not: {$fileName}");
        }

        $expected = $q.'a'.$q.','.$q.'b'.$q.','.$q.'c'.$q.PHP_EOL;
        $expected .= $q.'1'.$q.','.$q.'2'.$q.','.$q.'3'.$q.PHP_EOL;
        $expected .= $q.'4'.$q.','.$q.'5'.$q.','.$q.'6'.$q.PHP_EOL;
        $expected .= $q.'7'.$q.','.$q.'8'.$q.','.$q.'9'.$q;

        $this->assertEquals($expected, $data);
    }

}
