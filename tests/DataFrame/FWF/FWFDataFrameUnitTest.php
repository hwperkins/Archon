<?php namespace Archon\Tests\DataFrame\FWF;

use Archon\DataFrame;
use PHPUnit\Framework\TestCase;

class FWFDataFrameUnitTest extends TestCase
{

    public function testLoadFWF1()
    {
        $fileName = __DIR__.DIRECTORY_SEPARATOR.'TestFiles'.DIRECTORY_SEPARATOR.'testFWF.fwf';
        /*
junk
morejunk
stuff
morestuff
col1   col2   col3       col4
abc    qwer   12-34-5678 1234
123    tyui   90-12-3456 5678
xyz    opas   78-90-1234 9012
         */
        $df = DataFrame::fromFWF($fileName, [
            'col1' => ['*', 4],
            'col2' => [7, 11],
            'col3' => [14, 24],
            'col4' => [25, '*']
        ], ['include' => '/^.{14}\d{2}-\d{2}-\d{4}/']);
        $testArr = $df->toArray();

        $assertion = [
            [
                'col1' => 'abc',
                'col2' => 'qwer',
                'col3' => '12-34-5678',
                'col4' => '1234'
            ],
            [
                'col1' => '123',
                'col2' => 'tyui',
                'col3' => '90-12-3456',
                'col4' => '5678'
            ],
            [
                'col1' => 'xyz',
                'col2' => 'opas',
                'col3' => '78-90-1234',
                'col4' => '9012'
            ]
        ];

        $this->assertEquals($assertion, $testArr);
    }
}
