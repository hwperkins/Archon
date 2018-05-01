<?php

/**
 * Contains the DataFrameCore class.
 * @package   DataFrame
 * @author    Howard Gehring <hwgehring@gmail.com>
 * @copyright 2015 Howard Gehring <hwgehring@gmail.com>
 * @license   https://github.com/HWGehring/Archon/blob/master/LICENSE BSD-3-Clause
 * @link      https://github.com/HWGehring/Archon
 * @since     0.1.0
 */

namespace Archon;

use Archon\Exceptions\DataFrameException;
use Archon\Exceptions\InvalidColumnException;
use Closure;
use Countable;
use DateTime;
use Exception;
use Iterator;
use ArrayAccess;
use PDO;
use RuntimeException;

/**
 * The DataFrameCore class acts as the implementation for the various data manipulation features of the DataFrame class.
 * @package   Archon
 * @author    Howard Gehring <hwgehring@gmail.com>
 * @copyright 2015 Howard Gehring <hwgehring@gmail.com>
 * @license   https://github.com/HWGehring/Archon/blob/master/LICENSE BSD-3-Clause
 * @link      https://github.com/HWGehring/Archon
 * @since     0.1.0
 */
abstract class DataFrameCore implements ArrayAccess, Iterator, Countable
{

    /* *****************************************************************************************************************
     *********************************************** Core Implementation ***********************************************
     ******************************************************************************************************************/

    protected $data = [];
    protected $columns = [];

    protected function __construct(array $data)
    {
        if (count($data) > 0) {
            $this->data = array_values($data);
            $this->columns = array_keys(current($data));
        }
    }

    /**
     * Returns the DataFrame's columns as an array.
     * @return array
     * @since  0.1.0
     */
    public function columns()
    {
        return $this->columns;
    }

    /**
     * Returns a specific row index of the DataFrame.
     * @param  $index
     * @return array
     * @since  0.1.0
     */
    public function getIndex($index)
    {
        return $this->data[$index];
    }

    /**
     * Applies a user-defined function to each row of the DataFrame. The parameters of the function include the row
     * being iterated over, and optionally the index. ie: apply(function($el, $ix) { ... })
     * @param  Closure $f
     * @return DataFrameCore
     * @since  0.1.0
     */
    public function apply(Closure $f)
    {
        if (count($this->columns()) > 1) {
            foreach ($this->data as $i => &$row) {
                $row = $f($row, $i);
            }
        }

        if (count($this->columns()) === 1) {
            foreach ($this->data as $i => &$row) {
                $row[key($row)] = $f($row[key($row)], $i);
            }
        }

        return $this;
    }

    /**
     * Apply new values to specific rows of the DataFrame using row index.
     * applyByIndex([2=>'F',3=>'M','5'=>'F'], 'gender');
     * @param  Array $values
     * @param  $column
     * @return DataFrameCore
     * @since  0.1.0
     */
    public function applyByIndex(array $values, $column)
    {
        $this->mustHaveColumn($column);
        foreach($values as $index => $value){
          $this->data[$index][$column] = $value;
        }
        return $this;
    }
    /**
     * Filter DataFrame rows using user-defined function. The parameters of the function include the row
     * being iterated over, and the index. ie: filter(function($row, $index) { ... })
     * @param  Closure $f
     * @return DataFrameCore
     * @since  0.1.0
     */
    public function filter(Closure $f)
    {
        return DataFrame::fromArray(array_filter($this->data, $f, ARRAY_FILTER_USE_BOTH));
    }
    
    /**
     * Allows SQL to be used to perform operations on the DataFrame
     *
     * Table name will always be 'dataframe'
     *
     * @param $sql
     * @param PDO $pdo
     * @return DataFrame
     * @throws DataFrameException
     */
    public function query($sql, PDO $pdo = null)
    {
        $sql = trim($sql);
        $queryType = trim(strtoupper(strtok($sql, ' ')));

        if ($pdo === null) {
            $pdo = new PDO('sqlite::memory:');
        }

        $driver = $pdo->getAttribute(PDO::ATTR_DRIVER_NAME);
        if ($driver === 'sqlite') {
            $sqlColumns = implode(', ', $this->columns);
        } elseif ($driver === 'mysql') {
            $sqlColumns = implode(' VARCHAR(255), ', $this->columns) . ' VARCHAR(255)';
        } else {
            throw new DataFrameException("{$driver} is not yet supported for DataFrame query.");
        }

        $pdo->exec("DROP TABLE IF EXISTS dataframe;");
        $pdo->exec("CREATE TABLE IF NOT EXISTS dataframe ({$sqlColumns});");

        $df = DataFrame::fromArray($this->data);
        $df->toSQL('dataframe', $pdo);

        if ($queryType === 'SELECT') {
            $result = $pdo->query($sql);
        } else {
            $pdo->exec($sql);
            $result = $pdo->query("SELECT * FROM dataframe;");
        }

        $results = $result->fetchAll(PDO::FETCH_ASSOC);

        $pdo->exec("DROP TABLE IF EXISTS dataframe;");

        return DataFrame::fromArray($results);
    }

    /**
     * Assertion that the DataFrame must have the column specified. If not then an exception is thrown.
     * @param  $columnName
     * @throws InvalidColumnException
     * @since  0.1.0
     */
    public function mustHaveColumn($columnName)
    {
        if ($this->hasColumn($columnName) === false) {
            throw new InvalidColumnException("{$columnName} doesn't exist in DataFrame");
        }
    }

    /**
     * Returns a boolean of whether the specified column exists.
     * @param  $columnName
     * @return bool
     * @since  0.1.0
     */
    public function hasColumn($columnName)
    {
        if (array_search($columnName, $this->columns) === false) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Adds a new column to the DataFrame.
     * @internal
     * @param $columnName
     * @since 0.1.0
     */
    private function addColumn($columnName)
    {
        if (!$this->hasColumn($columnName)) {
            $this->columns[] = $columnName;
        }
    }

        /**
     * Adds multiple columns to the DataFrame.
     * @internal
     * @param $columnNames
     * @since 1.0.1
     */

    private function addColumns($columnNames)
    {
        foreach($columnNames as $columnName){
            $this->addColumn($columnName);
        }
    }
    /**
     * Renames specific column.
     *
     * ie:
     *      $df->renameColumn('old_name', 'new_name');
     *
     * @param $from
     * @param $to
     */
    public function renameColumn($from, $to) {
        $this->mustHaveColumn($from);

        foreach ($this as $i => $row) {
            $keys = array_keys($row);
            $index = array_search($from, $keys);
            $keys[$index] = $to;
            $this->data[$i] = array_combine($keys, $row);
        }

        $key = array_search($from, $this->columns);
        if(($key) !== false) {
            $this->columns[$key] = $to;
        }
    }

    /**
     * Removes a column (and all associated data) from the DataFrame.
     * @param $columnName
     * @since 0.1.0
     */
    public function removeColumn($columnName)
    {
        unset($this[$columnName]);
    }

    /**
     * Allows user to "array_merge" two DataFrames so that the rows of one are appended to the rows of another.
     *
     * @param $other
     * @return $this
     */
    public function append(DataFrame $other)
    {
        if (count($other) <= 0) {
            return $this;
        }

        $columns = $this->columns;

        foreach ($other as $row) {
            $newRow = [];
            foreach ($columns as $column) {
                $newRow[$column] = $row[$column];
            }

            $this->data[] = $newRow;
        }

        return $this;
    }

    /**
     * Replaces all occurences within the DataFrame of regex $pattern with string $replacement
     * @param $pattern
     * @param $replacement
     */
    public function pregReplace($pattern, $replacement)
    {
        foreach($this->data as &$row) {
            $row = preg_replace($pattern, $replacement, $row);
        }
    }

    /**
     * Allows user to apply type default values to certain columns when necessary. This is usually utilized
     * in conjunction with a database to avoid certain invalid type defaults (ie: dates of 0000-00-00).
     *
     * ie:
     *      $df->mapTypes([
     *          'some_amount' => 'DECIMAL',
     *          'some_int'    => 'INT',
     *          'some_date'   => 'DATE'
     *      ], ['Y-m-d'], 'm/d/Y');
     *
     * @param array $typeMap
     * @param null|string $fromDateFormat The date format of the input.
     * @param null|string $toDateFormat The date format of the output.
     * @throws Exception
     */
    public function convertTypes(array $typeMap, $fromDateFormat = null, $toDateFormat = null)
    {
        foreach ($this as $i => $row) {
            foreach ($typeMap as $column => $type) {
                if ($type == 'DECIMAL') {
                    $this->data[$i][$column] = $this->convertDecimal($row[$column]);
                } elseif ($type == 'INT') {
                    $this->data[$i][$column] = $this->convertInt($row[$column]);
                } elseif ($type == 'DATE') {
                    $this->data[$i][$column] = $this->convertDate($row[$column], $fromDateFormat, $toDateFormat);
                } elseif ($type == 'CURRENCY') {
                    $this->data[$i][$column] = $this->convertCurrency($row[$column]);
                } elseif ($type == 'ACCOUNTING') {
                    $this->data[$i][$column] = $this->convertAccounting($row[$column]);
                }
            }
        }
    }

    private function convertDecimal($value)
    {
        $value = str_replace(['$', ',', ' '], '', $value);

        if (substr($value, 1) == '.') {
            $value = '0'.$value;
        }

        if ($value == '0' || $value == '' || $value == '-0.00') {
            return '0.00';
        }

        if (substr($value, -1) == '-') {
            $value = '-'.substr($value, 0, -1);
        }

        return $value;

    }

    private function convertInt($value)
    {
        if ($value === '') {
            return '0';
        }

        if (substr($value, -1) === '-') {
            $value = '-'.substr($value, 0, -1);
        }

        return str_replace(',', '', $value);
    }

    private function convertDate($value, $fromFormat, $toFormat)
    {
        if ($value === '') {
            return '0001-01-01';
        }

        if (is_array($fromFormat)) {
            $errorParsingDate = false;
            $currentFormat = null;

            foreach ($fromFormat as $dateFormat) {
                $currentFormat = $dateFormat;
                $oldDateTime = DateTime::createFromFormat($dateFormat, $value);
                if ($oldDateTime === false) {
                    $errorParsingDate = true;
                    continue;
                } else {
                    $newDateString = $oldDateTime->format($toFormat);
                    return $newDateString;
                }
            }

            if ($errorParsingDate === true) {
                throw new RuntimeException("Error parsing date string '{$value}' with date format {$currentFormat}");
            }

        } else {

            $oldDateTime = DateTime::createFromFormat($fromFormat, $value);
            if ($oldDateTime === false) {
                throw new RuntimeException("Error parsing date string '{$value}' with date format {$fromFormat}");
            }

            $newDateString = $oldDateTime->format($toFormat);
            return $newDateString;
        }

        throw new RuntimeException("Error parsing date string: '{$value}' with date format: {$fromFormat}");
    }

    private function convertCurrency($value)
    {
        $value = explode('.', $value);
        $value[1] = $value[1] ?? '00';
        $value[0] = ($value[0] == '' or $value[0] == '-') ? '0' : $value[0];
        $value[1] = ($value[1] == '' or $value[1] == '0') ? '00' : $value[1];

        $dollars = number_format($value[0]).'.'.$value[1];

        if (substr($dollars, 0, 1) == '-') {
            $dollars = '-$'.substr($dollars, 1);
        } else {
            $dollars = '$'.$dollars;
        }

        return $dollars;
    }

    private function convertAccounting($value)
    {
        $value = explode('.', $value);
        $value[1] = $value[1] ?? '00';
        $value[0] = ($value[0] == '' or $value[0] == '-') ? '0' : $value[0];
        $value[1] = ($value[1] == '' or $value[1] == '0') ? '00' : $value[1];

        $dollars = number_format($value[0]) . '.' . $value[1];

        if (substr($dollars, 0, 1) == '-') {
            $dollars = '('.substr($dollars, 1).')';
        }

        return '$'.$dollars;
    }

    /**
     * Will group data similar to a SQL group by.
     *
     * @param $columns
     * @return DataFrame
     */
    public function groupBy($columns)
    {
        $groupedData = [];

        $uniqueColumns = [];
        foreach($this->data as $row) {

            if (is_array($columns)) {
                $uniqueData = null;
                foreach ($columns as $column) {
                    $uniqueData .= $row[$column];
                }
            } else {
                $uniqueData = $row[$columns];
            }

            if (isset($uniqueColumns[$uniqueData])) {
                continue;
            } else {
                $uniqueColumns[$uniqueData] = true;
                $groupedData[] = $row;
            }
        }

        return DataFrame::fromArray($groupedData);
    }

    /* *****************************************************************************************************************
     ******************************************* ArrayAccess Implementation ********************************************
     ******************************************************************************************************************/

    /**
     * Provides isset($df['column']) functionality.
     * @internal
     * @param  mixed $columnName
     * @return bool
     * @since  0.1.0
     */
    public function offsetExists($columnName)
    {
        foreach ($this as $row) {
            if (!array_key_exists($columnName, $row)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Allows user retrieve DataFrame subsets from a two-dimensional array by
     * simply requesting an element of the instantiated DataFrame.
     *      ie: $fooDF = $df['foo'];
     * @internal
     * @param  mixed $columnName
     * @return DataFrame
     * @throws InvalidColumnException
     * @since  0.1.0
     */
    public function offsetGet($columnName)
    {
        $this->mustHaveColumn($columnName);

        $getColumn = function ($el) use ($columnName) {
            return $el[$columnName];
        };

        $data = array_map($getColumn, $this->data);

        foreach ($data as &$row) {
            $row = [$columnName => $row];
        }

        return new DataFrame($data);
    }

    /**
     * Allows user set DataFrame columns from a Closure, value, array, or another single-column DataFrame.
     *      ie:
     *          $df[$targetColumn] = $rightHandSide
     *          $df['bar'] = $df['foo'];
     *          $df['bar'] = $df->foo;
     *          $df['foo'] = function ($foo) { return $foo + 1; };
     *          $df['foo'] = 'bar';
     *          $df[] = [['gender'=>'Female','name'=>'Luy'],['title'=>'Mr','name'=>'Noah']];
     * @internal
     * @param  mixed $targetColumn
     * @param  mixed $rightHandSide
     * @throws DataFrameException
     * @since  0.1.0
     */
    public function offsetSet($targetColumn, $rightHandSide)
    {
        if ($rightHandSide instanceof DataFrame) {
            $this->offsetSetDataFrame($targetColumn, $rightHandSide);
        } else if ($rightHandSide instanceof Closure) {
            $this->offsetSetClosure($targetColumn, $rightHandSide);
        } else {
            $this->offsetSetValue($targetColumn, $rightHandSide);
        }
    }

    /**
     * Allows user set DataFrame columns from a single-column DataFrame.
     *      ie:
     *          $df['bar'] = $df['foo'];
     * @internal
     * @param  $targetColumn
     * @param  DataFrame $df
     * @throws DataFrameException
     * @since  0.1.0
     */
    private function offsetSetDataFrame($targetColumn, DataFrame $df)
    {
        if (count($df->columns()) !== 1) {
            $msg = "Can only set a new column from a DataFrame with a single ";
            $msg .= "column.";
            throw new DataFrameException($msg);
        }

        if (count($df) != count($this)) {
            $msg = "Source and target DataFrames must have identical number ";
            $msg .= "of rows.";
            throw new DataFrameException($msg);
        }

        $this->addColumn($targetColumn);

        foreach ($this as $i => $row) {
            $this->data[$i][$targetColumn] = current($df->getIndex($i));
        }
    }

    /**
     * Allows user set DataFrame columns from a Closure.
     *      ie:
     *          $df['foo'] = function ($foo) { return $foo + 1; };
     * @internal
     * @param $targetColumn
     * @param Closure $f
     * @since 0.1.0
     */
    private function offsetSetClosure($targetColumn, Closure $f)
    {
        foreach ($this as $i => $row) {
            $this->data[$i][$targetColumn] = $f($row[$targetColumn]);
        }
    }

    /**
     * Allows user set DataFrame columns from a variable and add new rows to Dataframe
     *      ie:
     *          $df['foo'] = 'bar';
     *          $df[] = [['gender'=>'Female','name'=>'Luy'],['title'=>'Mr','name'=>'Noah']];
     * @internal
     * @param $targetColumn
     * @param $value
     * @since 0.1.0
     */
    private function offsetSetValue($targetColumn, $value)
    {
        if(trim($targetColumn!='')){
          $this->addColumn($targetColumn);
          foreach ($this as $i => $row) {
              $this->data[$i][$targetColumn] = $value;
          }
        }elseif(is_array($value)){
          foreach($value as $row){
            $this->addColumns(array_keys($row));
            $this->data[] = $row;
          }
        }
    }

    /**
     * Allows user to remove columns from the DataFrame using unset.
     *      ie: unset($df['column'])
     * @param  mixed $offset
     * @throws InvalidColumnException
     * @since  0.1.0
     */
    public function offsetUnset($offset)
    {
        $this->mustHaveColumn($offset);

        foreach ($this as $i => $row) {
            unset($this->data[$i][$offset]);
        }

        if (($key = array_search($offset, $this->columns)) !== false) {
            unset($this->columns[$key]);
        }
    }

    /* *****************************************************************************************************************
     ********************************************* Iterator Implementation *********************************************
     ******************************************************************************************************************/

    private $pointer = 0;

    /**
     * Return the current element
     * @link   http://php.net/manual/en/iterator.current.php
     * @return mixed Can return any type.
     * @since  0.1.0
     */
    public function current()
    {
        return $this->data[$this->key()];
    }

    /**
     * Move forward to next element
     * @link   http://php.net/manual/en/iterator.next.php
     * @return void Any returned value is ignored.
     * @since  0.1.0
     */
    public function next()
    {
        $this->pointer++;
    }

    /**
     * Return the key of the current element
     * @link   http://php.net/manual/en/iterator.key.php
     * @return mixed scalar on success, or null on failure.
     * @since  0.1.0
     */
    public function key()
    {
        return $this->pointer;
    }

    /**
     * Checks if current position is valid
     * @link   http://php.net/manual/en/iterator.valid.php
     * @return boolean The return value will be casted to boolean and then evaluated.
     *                 Returns true on success or false on failure.
     * @since  0.1.0
     */
    public function valid()
    {
        return isset($this->data[$this->key()]);
    }

    /**
     * Rewind the Iterator to the first element
     * @link   http://php.net/manual/en/iterator.rewind.php
     * @return void Any returned value is ignored.
     * @since  0.1.0
     */
    public function rewind()
    {
        $this->pointer = 0;
    }

    /* *****************************************************************************************************************
     ******************************************** Countable Implementation *********************************************
     ******************************************************************************************************************/

    /**
     * Count elements of an object
     * @link   http://php.net/manual/en/countable.count.php
     * @return int The custom count as an integer.
     *             The return value is cast to an integer.
     * @since  0.1.0
     */
    public function count()
    {
        return count($this->data);
    }
}
