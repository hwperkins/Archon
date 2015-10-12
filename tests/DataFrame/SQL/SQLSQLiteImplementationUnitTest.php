<?php namespace Archon\Tests\DataFrame\SQL;

use Archon\DataFrame;
use Archon\IO\SQL;
use PDO;

class SQLSQLiteImplementationUnitTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var PDO
     */
    private $pdo;

    public function setUp()
    {
        $dbFile = __DIR__.DIRECTORY_SEPARATOR.'SQLite'.DIRECTORY_SEPARATOR.'database.sqlite';
        $this->pdo = new PDO('sqlite:'.$dbFile);
    }

    public function testPrepareInsert()
    {
        $df = DataFrame::fromArray([
            ['a' => 1, 'b' => 2, 'c' => 3],
            ['a' => 4, 'b' => 5, 'c' => 6],
            ['a' => 7, 'b' => 8, 'c' => 9],
        ]);

        $sql = new SQL($df, $this->pdo);

        $query = $sql->prepareInsert('testTable');

        $expected = "INSERT INTO testTable (a, b, c) VALUES (1, 2, 3), (4, 5, 6), (7, 8, 9);";
        $this->assertEquals($expected, $query);
    }

    public function testPrepareChunkedInsert()
    {
        $df = DataFrame::fromArray([
            ['a' => 1, 'b' => 2, 'c' => 3],
            ['a' => 4, 'b' => 5, 'c' => 6],
            ['a' => 7, 'b' => 8, 'c' => 9],
        ]);

        $sql = new SQL($df, $this->pdo);
        $query = $sql->prepareInsert('testTable', 1);

        $expected = [
            "INSERT INTO testTable (a, b, c) VALUES (1, 2, 3);",
            "INSERT INTO testTable (a, b, c) VALUES (4, 5, 6);",
            "INSERT INTO testTable (a, b, c) VALUES (7, 8, 9);"
        ];

        $this->assertEquals($expected, $query);
    }
}
