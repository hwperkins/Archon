<?php namespace Archon\Tests\DataFrame\Core;

use Archon\DataFrame;
use PDO;
use PHPUnit\Framework\TestCase;

class CoreDataFrameTypesUnitTest extends TestCase
{

    /** @var DataFrame */
    private $df;

    private $input = [
        ['a' => 1, 'b' => 2, 'c' => 3],
        ['a' => 4, 'b' => 5, 'c' => 6],
        ['a' => 7, 'b' => 8, 'c' => 9],
    ];

    public function setUp()
    {
        $this->df = DataFrame::fromArray($this->input);
    }

    public function test() {
        $this->assertTrue(true);
    }
}