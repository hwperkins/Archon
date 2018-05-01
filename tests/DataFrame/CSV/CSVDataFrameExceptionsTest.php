<?php namespace Archon\Tests\DataFrame\CSV;

use Archon\DataFrame;
use PHPUnit_Framework_TestCase;
use PHPUnit\Framework\TestCase;

class CSVDataFrameExceptionsTest extends TestCase
{

    public function testOverwriteFailCSV()
    {
        $fileName = __DIR__.DIRECTORY_SEPARATOR.'TestFiles'.DIRECTORY_SEPARATOR.'testCSVOverwrite.csv';

        $df = DataFrame::fromArray([
            ['a' => 1, 'b' => 2, 'c' => 3],
            ['a' => 4, 'b' => 5, 'c' => 6],
            ['a' => 7, 'b' => 8, 'c' => 9],
        ]);

        $this->expectException('Archon\Exceptions\FileExistsException');
        $df->toCSV($fileName);
    }

    public function testInvalidOption()
    {
        $fileName = __DIR__.DIRECTORY_SEPARATOR.'TestFiles'.DIRECTORY_SEPARATOR.'testCSVOverwrite.csv';

        $this->expectException('Archon\Exceptions\UnknownOptionException');
        DataFrame::fromCSV($fileName, ['invalid_option' => 0]);

    }

    public function testUnknownDelimiter()
    {
        $fileName = __DIR__.DIRECTORY_SEPARATOR.'TestFiles'.DIRECTORY_SEPARATOR.'testCSVUnknownDelimiter.csv';

        $this->expectException('RuntimeException');
        DataFrame::fromCSV($fileName);
    }

    public function testInvalidColumnCount()
    {
        $fileName = __DIR__.DIRECTORY_SEPARATOR.'TestFiles'.DIRECTORY_SEPARATOR.'testCSVInvalidColumnCount.csv';

        $this->expectException('Archon\Exceptions\InvalidColumnException');
        DataFrame::fromCSV($fileName);
    }

}
