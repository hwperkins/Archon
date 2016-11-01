<?php namespace Archon\Tests\DataFrame\SQL;

use Archon\DataFrame;
use PDO;
use PDOException;

class SQLDataFrameExceptionTest extends \PHPUnit_Framework_TestCase
{

    public function testRollback()
    {
        // This test is tricky. We want to assert that a failed commit will roll back the database.

        $pdo = new PDO('sqlite::memory:');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->exec("CREATE TABLE testTable (a TEXT NOT NULL, b TEXT, c TEXT);");

        // The NOT NULL constraint on column a is what we'll be using to trigger a rollback.

        // First let's commit some data to a database
        $good = DataFrame::fromArray([
            ['a' => 1, 'b' => 2, 'c' => 3],
            ['a' => 4, 'b' => 5, 'c' => 6],
            ['a' => 7, 'b' => 8, 'c' => 9],
        ]);
        $good->toSQL('testTable', $pdo);

        // and make sure the output exactly matches the input.
        $result = $pdo->query("SELECT * FROM testTable;")->fetchAll(PDO::FETCH_ASSOC);
        $this->assertEquals($result, $good->toArray());

        /*
         * Now we attempt committing something that violates the schema constraint
         * The trick here is to commit this in the 3rd chunk with a chunk size of 1
         * so that we know two prepared statements have been executed with a
         * rollback triggering off of the 3rd.
         */
        $bad = DataFrame::fromArray([
            ['a' => 1, 'b' => 2, 'c' => 3],
            ['a' => 4, 'b' => 5, 'c' => 6],
            ['a' => null, 'b' => 8, 'c' => 9], // <- ding
        ]);

        try {
            $this->setExpectedException('PDOException');
            $bad->toSQL('testTable', $pdo, ['chunksize' => 1]);
        } catch (PDOException $e) {
            /*
             * We throw the original exception back here so that we can perform one
             * more assertion in the finally block.
             *
             * If we didn't do this then PHPUnit would terminate as soon as the
             * expected exception was detected.
             */
            throw $e;
        } finally {
            /*
             * Now that the exception has been asserted, we make sure the data in
             * the database still matches what we originally committed from the
             * first valid dataframe.
             */
            $query = $pdo->query("SELECT * FROM testTable;");
            $result = $query->fetchAll(PDO::FETCH_ASSOC);
            $this->assertEquals($result, $good->toArray());

            $pdo->exec("DROP TABLE testTable;");
        }
    }
}
