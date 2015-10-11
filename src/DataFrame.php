<?php namespace Archon;

use Archon\IO\CSV;
use Archon\IO\HTML;

final class DataFrame extends DataFrameCore {

    protected function __construct(array $data) {
        parent::__construct($data);
    }

    public static function fromCSV($fileName, $options = []) {
        $csv = new CSV($fileName);
        $data = $csv->loadFile($options);
        return new DataFrame($data);
    }

    public function toCSV($fileName, $options = []) {
        $csv = new CSV($fileName);
        $csv->saveFile($this->data, $options);
        return $this;
    }

    public function toHTML($options = []) {
        $html = new HTML($this->data);
        $output = $html->render($options);
        return $output;
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