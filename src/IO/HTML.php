<?php namespace Archon\IO;

/**
 * @link https://github.com/HWGehring/Archon for the canonical source repository
 * @license https://github.com/HWGehring/Archon/blob/master/LICENSE BSD 3-Clause
 */
final class HTML {

    const DEFAULT_READABLE_HTML = false;

    public function __construct(array $data) {
        $this->data = $data;
    }

    private function setDefaultOptions(array $options) {
        $options['readable'] = isset($options['readable']) ? $options['readable'] : self::DEFAULT_READABLE_HTML;
        return $options;
    }

    public function render(array $options) {
        $data = $this->data;
        $options = $this->setDefaultOptions($options);
        $readableOpt = $options['readable'];

        $columns = current($data);
        $columns = array_keys($columns);
        $columns = $this->wrapTRTH($columns);

        $header = $this->wrapTHead($columns);
        $footer = $this->wrapTFoot($columns);

        foreach($data as &$row) {
            $row = $this->wrapTRTH($row);
        }

        $data = implode('', $data);
        $data = $this->wrapTBody($data);
        $data = $header.$footer.$data;
        $data = $this->wrapTable($data);
        return $data;
    }

    private function wrapTRTH(array $data) {
        return '<tr><th>'.implode('</th><th>', $data).'</th></tr>';
    }

    private function wrapTHead($data) {
        return '<thead>'.$data.'</thead>';
    }

    private function wrapTFoot($data) {
        return '<tfoot>'.$data.'</tfoot>';
    }

    private function wrapTBody($data) {
        return '<tbody>'.$data.'</tbody>';
    }

    private function wrapTable($data) {
        return '<table>'.$data.'</table>';
    }

}