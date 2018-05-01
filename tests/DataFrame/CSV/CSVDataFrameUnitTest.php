<?php namespace Archon\Tests\DataFrame\CSV;

use Archon\DataFrame;
use PHPUnit\Framework\TestCase;

class CSVDataFrameUnitTest extends TestCase
{

    public function testFromCSV()
    {
        $fileName = __DIR__.DIRECTORY_SEPARATOR.'TestFiles'.DIRECTORY_SEPARATOR.'testCSV.csv';

        $df = DataFrame::fromCSV($fileName);

        $this->assertEquals([
            ['a' => 1, 'b' => 2, 'c' => 3],
            ['a' => 4, 'b' => 5, 'c' => 6],
        ], $df->toArray());
    }

    public function testFromCSVDirty()
    {
        $fileName = __DIR__.DIRECTORY_SEPARATOR.'TestFiles'.DIRECTORY_SEPARATOR.'testCSVdirty.csv';

        $df = DataFrame::fromCSV($fileName, [
            'include' => '/^([1-9]|a)/',
            'exclude' => '/^([7]|junk)/'
        ]);

        $this->assertEquals([
            ['a' => 1, 'b' => 2, 'c' => 3],
            ['a' => 4, 'b' => 5, 'c' => 6],
        ], $df->toArray());
    }

    public function testFromCSVNoHeader()
    {
        $fileName = __DIR__.DIRECTORY_SEPARATOR.'TestFiles'.DIRECTORY_SEPARATOR.'testCSV.csv';

        $df = DataFrame::fromCSV($fileName, ['columns' => ['x', 'y', 'z']]);

        $this->assertEquals([
            ['x' => 'a', 'y' => 'b', 'z' => 'c'],
            ['x' => 1, 'y' => 2, 'z' => 3],
            ['x' => 4, 'y' => 5, 'z' => 6],
        ], $df->toArray());
    }

    public function testFromCSVcolMap()
    {
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

    public function testCSVMappingAlias()
    {
        $fileName = __DIR__.DIRECTORY_SEPARATOR.'TestFiles'.DIRECTORY_SEPARATOR.'testCSV.csv';

        $df1 = DataFrame::fromCSV($fileName, [
            'colmap' => [
                'a' => 'x',
                'b' => 'y',
                'c' => 'z'
            ]
        ]);

        $df2 = DataFrame::fromCSV($fileName, [
            'mapping' => [
                'a' => 'x',
                'b' => 'y',
                'c' => 'z'
            ]
        ]);

        $this->assertEquals($df1->toArray(), $df2->toArray());
    }

    public function testFromCSVcolMapToNull()
    {
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

    public function testFromCSVcolMapToNull2()
    {
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

    public function testSaveCSV()
    {
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

        $expected = "a,b,c\n1,2,3\n4,5,6\n7,8,9\n";

        $this->assertEquals($expected, $data);
    }
}
