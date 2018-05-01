<?php namespace Archon\Tests\DataFrame\Core;

use Archon\DataFrame;
use Archon\DataType;
use PDO;
use PHPUnit\Framework\TestCase;

class CoreDataFrameTypesUnitTest extends TestCase
{

    public function testConvertNumericInteger() {
        $df = DataFrame::fromArray([
            ['numeric' => 1,              'integer' => 3.14,           ],
            ['numeric' => -4,             'integer' => '$5.23',        ],
            ['numeric' => 7.23,           'integer' => '8x',           ],
            ['numeric' => .3,             'integer' => '8-',           ],
            ['numeric' => '$3,000,000.4', 'integer' => 'asdf',         ],
            ['numeric' => '3.0-',         'integer' => "$3,456,789.23" ],
        ]);

        $df->convertTypes([
            'numeric' => DataType::NUMERIC,
            'integer' => DataType::INTEGER,
        ]);

        foreach ($df as $row) {
            $this->assertTrue(is_numeric($row['numeric']));
            $this->assertTrue(is_integer($row['integer']));
        }

        $this->assertSame([
            [ 'numeric' => 1,           'integer' => 3,      ],
            [ 'numeric' => -4,          'integer' => 5,      ],
            [ 'numeric' => 7.23,        'integer' => 8,      ],
            [ 'numeric' => 0.3,         'integer' => -8,     ],
            [ 'numeric' => '3000000.4', 'integer' => 0,      ],
            [ 'numeric' => '-3.0',      'integer' => 3456789 ],
        ], $df->toArray());
    }


}