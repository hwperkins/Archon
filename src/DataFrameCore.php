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
use Iterator;
use ArrayAccess;

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
     * Removes a column (and all associated data) from the DataFrame.
     * @param $columnName
     * @since 0.1.0
     */
    public function removeColumn($columnName)
    {
        unset($this[$columnName]);
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
     * Allows user set DataFrame columns from a Closure, value, or another single-column DataFrame.
     *      ie:
     *          $df[$targetColumn] = $rightHandSide
     *          $df['bar'] = $df['foo'];
     *          $df['bar'] = $df->foo;
     *          $df['foo'] = function ($foo) { return $foo + 1; };
     *          $df['foo'] = 'bar';
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
     * Allows user set DataFrame columns from a variable.
     *      ie:
     *          $df['foo'] = 'bar';
     * @internal
     * @param $targetColumn
     * @param $value
     * @since 0.1.0
     */
    private function offsetSetValue($targetColumn, $value)
    {
        $this->addColumn($targetColumn);
        foreach ($this as $i => $row) {
            $this->data[$i][$targetColumn] = $value;
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
