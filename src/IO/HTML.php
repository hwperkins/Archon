<?php namespace Archon\IO;

/**
 * Created by PhpStorm.
 * User: hwgehring
 * Date: 10/10/2015
 * Time: 9:16 PM
 */

final class HTML {

    public function __construct(array $data) {
        $this->data = $data;
    }

    public function render(array $options) {
        $data = $this->data;

        $columns = current($data);
        $columns = array_keys($columns);
        $columns = $this->wrapTRTH($columns);

        foreach($data as &$row) {
            $row = $this->wrapTRTH($row);
        }

        $data = '<thead>'.$columns.'</thead><tfoot>'.$columns.'</tfoot><tbody>'.implode('', $data).'</tbody>';
        $data = $this->wrapTable($data);

        return $data;
    }

    private function wrapTRTH(array $data) {
        return '<tr><th>'.implode('</th><th>', $data).'</th></tr>';
    }

    private function wrapTable($data) {
        return "<table>".$data."</table>";
    }

    private function setDefaultOptions(array $options) {

        return $options;
    }
}