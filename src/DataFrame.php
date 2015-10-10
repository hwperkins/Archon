<?php namespace Archon;

use Iterator;
use ArrayAccess;
use Closure;
use InvalidArgumentException;

use Assert\Assertion;

class DataFrame implements ArrayAccess, Iterator {

    private $data = [];
    private $pointer = 0;

    public function __construct(array $data, array $columns = NULL) {
        if ($this->is_valid_2d_array($data) === FALSE) {
            throw new InvalidArgumentException('DataFrame data is not a valid two-dimensional array.');
        };

        $this->data = $data;
    }

    private function is_valid_2d_array(array $array) {
        if (is_array($array) === FALSE) return FALSE;
        if (count($array) <= 0) return FALSE;

        $first_element = $this->first_element_of($array);

        foreach($array as $row) {
            if (is_array($row) === FALSE) return FALSE;
            if (count($row) !== count($first_element)) return FALSE;
        }

        return TRUE;
    }

    private function first_element_of(array &$array) {
        $first_element = array_shift($array);
        $array = array_unshift($array, $first_element);
        return $first_element;
    }

    public function to_array() {
        return $this->data;
    }

    public function offsetGet($key) {
        $col = array_column($this->data, $key);
        foreach($col as $i => $row) $col[$i] = [$key => $row];
        return new DataFrame($columns=[$key], $data=$col);
    }

    public function __get($key) {
        return $this->offsetGet($key);
    }

    public function offsetSet($key, $value) {
        $this->columns[] = $key;
        foreach($this as $row) $row[$key] = $value;
    }

    public function __set($key, $value) {
        $this->offsetSet($key, $value);
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

    private function apply(Closure $callback) {
        foreach($this as $row) {
            $callback($row);
        }
    }

    public function offsetExists($offset) {
        $this->apply(function($row) {

        });
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