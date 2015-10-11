<?php namespace Archon;

use Archon\IO\CSV;
use Archon\IO\FWF;
use Archon\IO\HTML;

/**
 * @link https://github.com/HWGehring/Archon for the canonical source repository
 * @license https://github.com/HWGehring/Archon/blob/master/LICENSE BSD 3-Clause
 */
final class DataFrame extends DataFrameCore
{

    protected function __construct(array $data)
    {
        parent::__construct($data);
    }

    public static function fromCSV($fileName, $options = [])
    {
        $csv = new CSV($fileName);
        $data = $csv->loadFile($options);
        return new DataFrame($data);
    }

    public function toCSV($fileName, $options = [])
    {
        $csv = new CSV($fileName);
        $csv->saveFile($this->data, $options);
        return $this;
    }

    public static function fromFWF($fileName, array $colSpecs, array $options = [])
    {
        $fwf = new FWF($fileName);
        $data = $fwf->loadFile($colSpecs, $options);
        return new DataFrame($data);
    }

    public function toHTML($options = [])
    {
        $html = new HTML($this->data);
        $output = $html->renderTable($options);
        return $output;
    }

    /**
     * @param array $data
     * @param array $options
     * @return DataFrame
     */
    public static function fromArray(array $data, array $options = [])
    {
        $firstRow = current($data);
        $columns = isset($options['columns']) ? $options['columns'] : array_keys($firstRow);

        foreach ($data as &$row) {
            $row = array_combine($columns, $row);
        }

        return new DataFrame($data);
    }

    public function toArray()
    {
        return $this->data;
    }
}
