<?php namespace Archon;

use Archon\IO\CSV;

class DataFrame extends DataFrameCore {

    protected function __construct(array $data) {
        parent::__construct($data);
    }

    public static function fromCSV($fileName, $options = []) {
        $data = CSV::fromFile($fileName, $options);
        return new DataFrame($data);
    }

    public static function fromArray(array $data, array $options = []) {
        $first_row = current($data);
        $columns = isset($options['columns']) ? $options['columns'] : array_keys($first_row);

        foreach ($data as &$row) {
            $row = array_combine($columns, $row);
        }

        return new DataFrame($data);
    }

    public function toArray() {
        return $this->data;
    }

}