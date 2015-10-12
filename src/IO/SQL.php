<?php

/**
 * Contains the SQL class.
 * @package   DataFrame
 * @author    Howard Gehring <hwgehring@gmail.com>
 * @copyright 2015 Howard Gehring <hwgehring@gmail.com>
 * @license   https://github.com/HWGehring/Archon/blob/master/LICENSE BSD-3-Clause
 * @link      https://github.com/HWGehring/Archon
 * @since     0.2.0
 */

namespace Archon\IO;

use PDO;
use PDOException;
use RecursiveArrayIterator;
use RecursiveIteratorIterator;

/**
 * The SQL class contains implementation details for reading and writing data to and from relational databases.
 * @package   Archon\IO
 * @author    Howard Gehring <hwgehring@gmail.com>
 * @copyright 2015 Howard Gehring <hwgehring@gmail.com>
 * @license   https://github.com/HWGehring/Archon/blob/master/LICENSE BSD-3-Clause
 * @link      https://github.com/HWGehring/Archon
 * @since     0.2.0
 */
final class SQL
{

    private $defaultOptions = [
        'chunksize' => 5000
    ];

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Performs a SQL insert transaction to provided table, crafting the SQL statement using an array of columns
     * and a two-dimensional array of data.
     * @param  $tableName
     * @param  array $columns
     * @param  array $data
     * @param  array $options
     * @return int
     * @throws \Archon\Exceptions\UnknownOptionException
     * @since  0.2.0
     */
    public function insertInto($tableName, array $columns, array $data, $options = [])
    {
        $pdo = $this->pdo;
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $options = Options::setDefaultOptions($options, $this->defaultOptions);
        $chunksizeOpt = $options['chunksize'];

        // Sanitize table name and columns
        $tableName = $pdo->quote($tableName);
        foreach ($columns as &$column) {
            $column = $pdo->quote($column);
        }

        $pdo->beginTransaction();
        try {
            $data = array_chunk($data, $chunksizeOpt);
            $affected = $this->insertChunkedData($pdo, $tableName, $columns, $data);
        } catch (PDOException $e) {
            $pdo->rollBack();
            throw $e;
        }
        $pdo->commit();

        return $affected;
    }


    /**
     * Transforms and executes a series of prepared statements from a chunked array.
     * @internal
     * @param  PDO $pdo
     * @param  $tableName
     * @param  array $columns
     * @param  array $data
     * @return int
     * @since  0.2.0
     */
    private function insertChunkedData(PDO $pdo, $tableName, array $columns, array $data)
    {
        $affected = 0;
        foreach ($data as $chunk) {
            $sql = $this->createPreparedStatement($tableName, $columns, $chunk);
            $stmt = $pdo->prepare($sql);
            $chunk = $this->flattenArray($chunk);
            $stmt->execute($chunk);
        }

        return $affected;
    }

    /**
     * Transforms a table string, array of columns, and array of data into a prepared statement.
     * @internal
     * @param  $tableName
     * @param  array $columns
     * @param  array $data
     * @return string
     * @since  0.2.0
     */
    private function createPreparedStatement($tableName, array $columns, array $data)
    {
        $columns = '('.implode(', ', $columns).')';

        foreach ($data as &$row) {
            $row = array_fill(0, count($row), '?');
            $row = '('.implode(', ', $row).')';
        }
        $data = implode(', ', $data);

        return sprintf("INSERT INTO %s %s VALUES %s;", $tableName, $columns, $data);
    }

    /**
     * Flattens a two-dimensional array into a one-dimensional array.
     * @internal
     * @param  array $array
     * @return array
     * @since  0.2.0
     */
    private function flattenArray(array $array)
    {
        $it = new RecursiveIteratorIterator(new RecursiveArrayIterator($array));

        $result = [];
        foreach ($it as $element) {
            $result[] = $element;
        }

        return $result;
    }
}
