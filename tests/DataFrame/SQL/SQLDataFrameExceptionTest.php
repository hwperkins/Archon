<?php namespace Archon\Tests\DataFrame\SQL;

use Archon\DataFrame;
use PDO;
use PDOException;

class SQLDataFrameExceptionTest extends \PHPUnit_Framework_TestCase
{

    public function testRollback()
    {
        $df = DataFrame::fromArray([
            ['a' => 1, 'b' => 2, 'c' => 3],
            ['a' => 4, 'b' => 5, 'c' => 6],
            ['a' => 7, 'b' => 8, 'c' => 9],
        ]);

        $pdo = new PDO('sqlite:memory');
        $pdo->exec("DROP TABLE IF EXISTS testTable;");
        $pdo->exec("CREATE TABLE testTable (a TEXT NOT NULL, b TEXT, c TEXT);");
        $df->toSQL($pdo, 'testTable');
        $query = $pdo->query("SELECT * FROM testTable;");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        $this->assertEquals($result, $df->toArray());

        $df2 = DataFrame::fromArray([
            ['a' => 1, 'b' => 2, 'c' => 3],
            ['a' => 4, 'b' => 5, 'c' => 6],
            ['a' => 7, 'b' => 8, 'c' => 9],
        ]);

        $pdo = new PDO('sqlite:memory');
        $pdo->exec("DROP TABLE IF EXISTS testTable;");
        $pdo->exec("CREATE TABLE testTable (a TEXT NOT NULL, b TEXT, c TEXT);");
        $this->setExpectedException('PDOException');
        try {
            $df->toSQL($pdo, 'testTable', ['chunksize' => 1]);
        } catch (PDOException $e) {
            $query = $pdo->query("SELECT * FROM testTable;");
            $result = $query->fetchAll(PDO::FETCH_ASSOC);
            $this->assertEquals($result, $df->toArray());
            throw $e;
        }
    }
}
