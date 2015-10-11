<?php namespace Archon;

use Iterator;
use ArrayAccess;

/**
 * @link https://github.com/HWGehring/Archon for the canonical source repository
 * @license https://github.com/HWGehring/Archon/blob/master/LICENSE BSD 3-Clause
 */
class DataFrameCore implements ArrayAccess, Iterator {

    protected $data = [];
    protected $columns = [];

    private $pointer = 0;

    protected function __construct(array $data) {
        $this->data = $data;
        $this->columns = array_keys(current($data));
    }

    public function columns() {
        return $this->columns;
    }

    public function offsetGet($key) {
        $col = array_column($this->data, $key);
        return DataFrame::fromArray($col);
    }

    public function offsetSet($key, $value) {
        $this->columns[] = $key;
        foreach($this as $row) $row[$key] = $value;
    }

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

    public function offsetExists($offset) {
        foreach($this as $row) {
            if (!array_key_exists($offset, $row)) {
                return FALSE;
            }
        }

        return TRUE;
    }

    public function offsetUnset($offset) {
        if (!isset($this[$offset])) {
            throw new \RuntimeException("Key {$offset} not found in DataFrame.");
        }

        foreach($this as $i => $row) {
            unset($this->data[$i][$offset]);
        }

        if(($key = array_search($offset, $this->columns)) !== FALSE) {
            unset($this->columns[$key]);
        }
    }

}