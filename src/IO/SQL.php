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
 * @since     0.1.0
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

    public function insertInto($tableName, array $columns, array $data, $options = [])
    {
        $pdo = $this->pdo;
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $options = Options::setDefaultOptions($options, $this->defaultOptions);
        $chunksizeOpt = $options['chunksize'];

        $data = array_chunk($data, $chunksizeOpt);

        $pdo->beginTransaction();
        try {
            $affected = $this->executeInsert($pdo, $tableName, $columns, $data);
        } catch (PDOException $e) {
            $pdo->rollBack();
            throw $e;
        }
        $pdo->commit();

        return $affected;
    }

    private function executeInsert(PDO $pdo, $tableName, array $columns, array $data)
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

    private function createPreparedStatement($tableName, array $columns, array $data)
    {
        $columns = '('.implode(', ', $columns).')';

        foreach ($data as &$row) {
            $row = array_fill(0, count($row), '?');
            $row = '('.implode(', ', $row).')';
        }
        $data = implode(', ', $data);

        return 'INSERT INTO '.$tableName.' '.$columns.' VALUES '.$data.';';
    }

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
