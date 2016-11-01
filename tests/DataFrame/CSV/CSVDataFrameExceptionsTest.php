<?php namespace Archon\Tests\DataFrame\CSV;

use Archon\DataFrame;

class CSVDataFrameExceptionsTest extends \PHPUnit_Framework_TestCase
{

    public function testOverwriteFailCSV()
    {
        $fileName = __DIR__.DIRECTORY_SEPARATOR.'TestFiles'.DIRECTORY_SEPARATOR.'testCSVOverwrite.csv';

        $df = DataFrame::fromArray([
            ['a' => 1, 'b' => 2, 'c' => 3],
            ['a' => 4, 'b' => 5, 'c' => 6],
            ['a' => 7, 'b' => 8, 'c' => 9],
        ]);

        $this->setExpectedException('Archon\Exceptions\FileExistsException');
        $df->toCSV($fileName);
    }

    public function testInvalidOption()
    {
        $fileName = __DIR__.DIRECTORY_SEPARATOR.'TestFiles'.DIRECTORY_SEPARATOR.'testCSVOverwrite.csv';

        $this->setExpectedException('Archon\Exceptions\UnknownOptionException');
        DataFrame::fromCSV($fileName, ['invalid_option' => 0]);

    }

    public function testUnknownDelimiter()
    {
        $fileName = __DIR__.DIRECTORY_SEPARATOR.'TestFiles'.DIRECTORY_SEPARATOR.'testCSVUnknownDelimiter.csv';

        $this->setExpectedException('RuntimeException');
        DataFrame::fromCSV($fileName);
    }
}
