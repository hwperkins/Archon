<?php namespace Archon\Tests\DataFrame\XLSX;

use Archon\DataFrame;
use PHPExcel;
use PHPExcel_Writer_Excel2007;

class XLSXDataFrameUnitTest extends \PHPUnit_Framework_TestCase
{

    public function testLoadXLSX()
    {
        $fileName = __DIR__.DIRECTORY_SEPARATOR.'TestFiles'.DIRECTORY_SEPARATOR.'test.xlsx';

        // Suppress warning coming from PHPExcel date/time nonsense
        $xlsx = @DataFrame::fromXLSX($fileName);
        $xlsx = $xlsx->toArray();

        $assertion_array = [
            [
                "col_a" => "physician",
                "col_b" => "heal",
                "col_c" => "thyself",
                "col_d" => "",
                "col_e" => "lorem",
                "col_f" => "ipsum",
            ],
            [
                "col_a" => "",
                "col_b" => "physician",
                "col_c" => "heal",
                "col_d" => "thyself",
                "col_e" => "",
                "col_f" => "lorem",
            ],
            [
                "col_a" => "ipsum",
                "col_b" => "",
                "col_c" => "physician",
                "col_d" => "heal",
                "col_e" => "thyself",
                "col_f" => ""
            ],
            [
                "col_a" => "lorem",
                "col_b" => "ipsum",
                "col_c" => "",
                "col_d" => "physician",
                "col_e" => "heal",
                "col_f" => "thyself",
            ]
        ];

        $this->assertEquals($assertion_array, $xlsx);
    }

    public function testToXLSX()
    {
        $fileName = __DIR__.DIRECTORY_SEPARATOR.'TestFiles'.DIRECTORY_SEPARATOR.'test_to.xlsx';

        if (file_exists($fileName)) {
            unlink($fileName);
        }

        $sheetA = [
            [
                'a' => 'one',
                'b' => 'two',
                'c' => 'three'
            ],
            [
                'a' => 'four',
                'b' => 'five',
                'c' => 'six'
            ]
        ];

        $sheetB = [
            [
                'd' => 'seven',
                'e' => 'eight',
                'f' => 'nine'
            ],
            [
                'd' => 'ten',
                'e' => 'eleven',
                'f' => 'twelve'
            ]
        ];

        $sheetC = [
            [
                'g' => 'thirteen',
                'h' => 'fourteen',
                'i' => 'fifteen'
            ],
            [
                'g' => 'sixteen',
                'h' => 'seventeen',
                'i' => 'eighteen'
            ]
        ];

        $a = DataFrame::fromArray($sheetA);
        $b = DataFrame::fromArray($sheetB);
        $c = DataFrame::fromArray($sheetC);

        $xlsx = new PHPExcel();
        $a->toXLSXWorksheet($xlsx, 'A');
        $b->toXLSXWorksheet($xlsx, 'B');
        $c->toXLSXWorksheet($xlsx, 'C');

        $this->assertEquals($a->toArray(), $sheetA);
        $this->assertEquals($b->toArray(), $sheetB);
        $this->assertEquals($c->toArray(), $sheetC);

        $writer = new PHPExcel_Writer_Excel2007($xlsx);
        @$writer->save($fileName); // Suppress warning coming from PHPExcel date/time nonsense

        // Suppress warning coming from PHPExcel date/time nonsense
        @$a = DataFrame::fromXLSX($fileName, ['sheetname' => 'A']);
        @$b = DataFrame::fromXLSX($fileName, ['sheetname' => 'B']);
        @$c = DataFrame::fromXLSX($fileName, ['sheetname' => 'C']);

        $this->assertEquals($a->toArray(), $sheetA);
        $this->assertEquals($b->toArray(), $sheetB);
        $this->assertEquals($c->toArray(), $sheetC);

        unlink($fileName);
    }
}
