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

use Archon\Exceptions\InvalidColumnException;
use PDO;
use PDOException;
use RecursiveArrayIterator;
use RecursiveIteratorIterator;
use RuntimeException;

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
        'chunksize' => 5000,
        'replace' => false,
        'ignore' => false
    ];

    public function __construct(PDO $pdo)
    {
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->pdo = $pdo;
    }

    /**
     * Performs a SQL select, returning an associative array of the results.
     * @param  $sqlQuery
     * @return array
     * @since  0.3.0
     */
    public function select($sqlQuery)
    {
        $pdo = $this->pdo;
        $query = $pdo->query($sqlQuery);
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    /**
     * Performs a SQL insert transaction to provided table, crafting the SQL statement using an array of columns
     * and a two-dimensional array of data.
     * @param  $tableName
     * @param  array $columns
     * @param  array $data
     * @param  array $options
     * @return int
     * @throws InvalidColumnException
     * @since  0.2.0
     */
    public function insertInto($tableName, array $columns, array $data, $options = [])
    {
        if (count($data) === 0) {
            return 0;
        }

        try {
            $this->identifyAnyMissingColumns($columns, $tableName);
        } catch (PDOException $pdoe) {
            // If this function throws a PDO exception then it's probably just a unit test running a SQLite query
            // SQLite doesn't support "show columns like" syntax
        } catch (InvalidColumnException $ice) {
            throw $ice;
        }

        $pdo = $this->pdo;

        $options = Options::setDefaultOptions($options, $this->defaultOptions);
        $chunksizeOpt = $options['chunksize'];

        $pdo->beginTransaction();
        try {
            $data = array_chunk($data, $chunksizeOpt);
            $affected = $this->insertChunkedData($pdo, $tableName, $columns, $data, $options);
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
     * @param array $options
     * @return int
     * @since  0.2.0
     */
    private function insertChunkedData(PDO $pdo, $tableName, array $columns, array $data, array $options)
    {
        $affected = 0;
        foreach ($data as $chunk) {
            $sql = $this->createPreparedStatement($tableName, $columns, $chunk, $options);
            $stmt = $pdo->prepare($sql);
            $chunk = $this->flattenArray($chunk);
            $stmt->execute($chunk);
            $affected += $stmt->rowCount();
        }

        return $affected;
    }

    /**
     * Transforms a table string, array of columns, and array of data into a prepared statement.
     * @internal
     * @param  $tableName
     * @param  array $columns
     * @param  array $data
     * @param array $options
     * @return string
     * @since  0.2.0
     */
    private function createPreparedStatement($tableName, array $columns, array $data, array $options)
    {
        $replace_opt = $options['replace'];
        $ignore_opt = $options['ignore'];

        if ($replace_opt === true and $ignore_opt === true) {
            throw new RuntimeException("REPLACE and INSERT IGNORE are mutually exclusive. Please choose only one.");
        }

        $columns = '('.implode(', ', $columns).')';

        foreach ($data as &$row) {
            $row = array_fill(0, count($row), '?');
            $row = '('.implode(', ', $row).')';
        }
        $data = implode(', ', $data);

        if ($replace_opt === true) {
            $insert = 'REPLACE';
        } elseif ($ignore_opt === true) {
            $insert = 'INSERT IGNORE';
        } else {
            $insert = 'INSERT';
        }

        return "{$insert} INTO {$tableName} {$columns} VALUES {$data};";
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

    /**
     * Identifies any missing columns in the database which we may be attempting to insert.
     *
     * @param array $columns
     * @param $tableName
     * @throws InvalidColumnException
     */
    private function identifyAnyMissingColumns(array $columns, $tableName) {
        $db_columns = array_column($this->pdo->query("SHOW COLUMNS FROM {$tableName};")->fetchAll(), 'Field');

        $missingColumns = array_diff($columns, $db_columns);

        if (count($missingColumns) !== 0) {
            $s = count($missingColumns) > 1 ? 's' : '';
            $missingColumns = "`".implode("`, `", $missingColumns)."`";
            throw new InvalidColumnException("Error: Table {$tableName} does not contain the column{$s}: {$missingColumns}.");
        }
    }
}
