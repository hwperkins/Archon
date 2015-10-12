<?php namespace Archon\Tests\DataFrame\SQL;

use Archon\DataFrame;
use Archon\IO\SQL;
use PDO;

class SQLDataFrameUnitTest extends \PHPUnit_Framework_TestCase
{

    public function testToSQL()
    {
        $df = DataFrame::fromArray([
            ['a' => 1, 'b' => 2, 'c' => 3],
            ['a' => 4, 'b' => 5, 'c' => 6],
            ['a' => null, 'b' => 8, 'c' => 9],
        ]);

        $pdo = new PDO('sqlite:memory');
        $pdo->exec("DROP TABLE IF EXISTS testTable;");
        $pdo->exec("CREATE TABLE testTable (a TEXT NOT NULL, b TEXT, c TEXT);");
        $df->toSQL($pdo, 'testTable');
        $query = $pdo->query("SELECT * FROM testTable;");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        $this->assertEquals($result, $df->toArray());
    }
}
