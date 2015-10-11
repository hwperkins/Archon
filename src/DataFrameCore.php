<?php namespace Archon;

use Archon\Exceptions\DataFrameException;
use Closure;
use Countable;
use Iterator;
use ArrayAccess;
use RuntimeException;

/**
 * @link https://github.com/HWGehring/Archon for the canonical source repository
 * @license https://github.com/HWGehring/Archon/blob/master/LICENSE BSD 3-Clause
 */
class DataFrameCore implements ArrayAccess, Iterator, Countable {

    /* ****************************************************************************************************************
     ********************************************** Core Implementation ***********************************************
     *****************************************************************************************************************/

    protected $data = [];
    protected $columns = [];

    protected function __construct(array $data) {
        $this->data = $data;
        $this->columns = array_keys(current($data));
    }

    public function columns() {
        return $this->columns;
    }

    public function getIndex($index) {
        return $this->data[$index];
    }

    public function hasColumn($column_name, $strict = false) {
        if (array_search($column_name, $this->columns) === false) {
            if ($strict === true) {
                throw new RuntimeException("Error: {$column_name} doesn't exist in DataFrame.");
            }
            return false;
        } else {
            return true;
        }
    }

    private function addColumn($column_name) {
        if (!$this->hasColumn($column_name)) {
            $this->columns[] = $column_name;
        }
    }

    /* ****************************************************************************************************************
     ******************************************* ArrayAccess Implementation *******************************************
     *****************************************************************************************************************/

    /**
     * @param mixed $offset
     * @return bool
     */
    public function offsetExists($offset) {
        foreach($this as $row) {
            if (!array_key_exists($offset, $row)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Allows user retrieve DataFrame subsets from a two-dimensional array by simply requesting an element of
     * the instantiated DataFrame.
     *
     * ie: $foo_df = $df['foo'];
     *
     * @param mixed $key
     * @return DataFrame
     */
    public function offsetGet($key) {
        $data = array_map(function($el) use($key) { return $el[$key]; }, $this->data);

        foreach($data as &$row) {
            $row = [$key => $row];
        }

        return new DataFrame($data);
    }

    public function offsetSet($targetColumn, $rightHandSide) {
        if ($rightHandSide instanceof DataFrame) {
            $this->offsetSetDataFrame($targetColumn, $rightHandSide);
        } elseif ($rightHandSide instanceof Closure) {
            $this->offsetSetClosure($targetColumn, $rightHandSide);
        } else {
            $this->offsetSetValue($targetColumn, $rightHandSide);
        }
    }


    private function offsetSetDataFrame($targetColumn, DataFrame $df) {
        if (count($df->columns()) !== 1) {
            throw new DataFrameException("Can only set a new column from a DataFrame with a single column.");
        }

        if (count($df) != count($this)) {
            throw new DataFrameException("Source and target DataFrames have identical number of rows.");
        }

        $this->addColumn($targetColumn);

        foreach($this as $i => $row) {
            $this->data[$i][$targetColumn] = current($df->getIndex($i));
        }
    }

    private function offsetSetClosure($targetColumn, Closure $f) {
        foreach($this as $i => $row) {
            $this->data[$i][$targetColumn] = $f($row[$targetColumn]);
        }
    }

    private function offsetSetValue($targetColumn, $value) {
        $this->addColumn($targetColumn);
        foreach($this as $i => $row) {
            $this->data[$i][$targetColumn] = $value;
        }
    }

    public function offsetUnset($offset) {
        if (!isset($this[$offset])) {
            throw new \RuntimeException("Key {$offset} not found in DataFrame.");
        }

        foreach($this as $i => $row) {
            unset($this->data[$i][$offset]);
        }

        if(($key = array_search($offset, $this->columns)) !== false) {
            unset($this->columns[$key]);
        }
    }

    /* ****************************************************************************************************************
     ********************************************* Iterator Implementation ********************************************
     *****************************************************************************************************************/

    private $pointer = 0;

    public function current() {
        return $this->data[$this->key()];
    }

    public function next() {
        $this->pointer++;
    }

    public function key() {
        return $this->pointer;
    }

    public function valid() {
        return isset($this->data[$this->key()]);
    }

    public function rewind() {
        $this->pointer = 0;
    }

    /* ****************************************************************************************************************
     ********************************************* Countable Implementation *******************************************
     *****************************************************************************************************************/

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Count elements of an object
     * @link http://php.net/manual/en/countable.count.php
     * @return int The custom count as an integer.
     * </p>
     * <p>
     * The return value is cast to an integer.
     */
    public function count() {
        return count($this->data);
    }

}
