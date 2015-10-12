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

use Archon\DataFrame;
use PDO;

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

    public function __construct(DataFrame $df, PDO $pdo)
    {
        $this->columns = $df->columns();
        $this->data = $df->toArray();
        $this->pdo = $pdo;
    }

    public function prepareInsert($tableName, $chunkSize = 5000)
    {
        $data = $this->data;
        $data = array_chunk($data, $chunkSize);

        $chunks = [];
        foreach ($data as $chunk) {
            $chunks[] = $this->assembleInsert($tableName, $this->columns, $chunk);
        }

        if (count($chunks) === 1) {
            $chunks = current($chunks);
        }

        return $chunks;
    }

    public function assembleInsert($tableName, $columns, $data)
    {
        $parenthesis = $this->fnWrapArray('(', ', ', ')');
        $values = array_map($parenthesis, $data);
        $values = implode(', ', $values);
        $columns = $parenthesis($columns);

        $result = sprintf("INSERT INTO %s %s VALUES %s;", $tableName, $columns, $values);
        return $result;
    }

    private function fnWrapArray($left, $intersperse, $right)
    {
        return function (array $data) use ($left, $intersperse, $right) {
            $wrap = $this->fnWrapText($left, $right);
            return $wrap(implode($intersperse, $data));
        };
    }

    private function fnWrapText($left, $right)
    {
        return function ($data) use ($left, $right) {
            if (is_array($data) === true) {
                $data = implode('', $data);
            }

            return $left.$data.$right;
        };
    }
}
