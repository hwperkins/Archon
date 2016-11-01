<?php

/**
 * Contains the DataFrame class.
 * @package   DataFrame
 * @author    Howard Gehring <hwgehring@gmail.com>
 * @copyright 2015 Howard Gehring <hwgehring@gmail.com>
 * @license   https://github.com/HWGehring/Archon/blob/master/LICENSE BSD-3-Clause
 * @link      https://github.com/HWGehring/Archon
 * @since     0.1.0
 */

namespace Archon;

use Archon\IO\CSV;
use Archon\IO\FWF;
use Archon\IO\HTML;
use Archon\IO\JSON;
use Archon\IO\SQL;
use Archon\IO\XLSX;
use PDO;
use PHPExcel;
use PHPExcel_Worksheet;

/**
 * The DataFrame class acts as an interface to various underlying data structure, file format, and database
 * implementations.
 * @package   Archon
 * @author    Howard Gehring <hwgehring@gmail.com>
 * @copyright 2015 Howard Gehring <hwgehring@gmail.com>
 * @license   https://github.com/HWGehring/Archon/blob/master/LICENSE BSD-3-Clause
 * @link      https://github.com/HWGehring/Archon
 * @since     0.1.0
 */
final class DataFrame extends DataFrameCore
{

    protected function __construct(array $data)
    {
        parent::__construct($data);
    }

    /**
     * Factory method for creating a DataFrame from a CSV file.
     * @param  $fileName
     * @param  array $options
     * @return DataFrame
     * @since  0.1.0
     */
    public static function fromCSV($fileName, $options = [])
    {
        $csv = new CSV($fileName);
        $data = $csv->loadFile($options);
        return new DataFrame($data);
    }

    /**
     * Outputs a DataFrame to a CSV file.
     * @param  $fileName
     * @param  array $options
     * @return $this
     * @throws \Archon\Exceptions\FileExistsException
     * @since  0.1.0
     */
    public function toCSV($fileName, $options = [])
    {
        $csv = new CSV($fileName);
        $csv->saveFile($this->data, $options);
        return $this;
    }

    /**
     * Factory method for creating a DataFrame from a fixed-width file.
     * @param  $fileName
     * @param  array $colSpecs
     * @param  array $options
     * @return DataFrame
     * @since  0.1.0
     */
    public static function fromFWF($fileName, array $colSpecs, array $options = [])
    {
        $fwf = new FWF($fileName);
        $data = $fwf->loadFile($colSpecs, $options);
        return new DataFrame($data);
    }

    /**
     * Factory method for creating a DataFrame from an XLSX worksheet.
     * @param  $fileName
     * @param  array $options
     * @return DataFrame
     * @since  0.3.0
     */
    public static function fromXLSX($fileName, array $options = [])
    {
        $xlsx = new XLSX($fileName);
        $data = $xlsx->loadFile($options);
        return new DataFrame($data);
    }

    /**
     * Output a DataFrame as a PHPExcel worksheet.
     * @param PHPExcel $excel
     * @param $worksheetTitle
     * @return PHPExcel_Worksheet
     * @since  0.3.0
     */
    public function toXLSXWorksheet(PHPExcel &$excel, $worksheetTitle)
    {
        $worksheet = XLSX::saveToWorksheet($excel, $worksheetTitle, $this->data, $this->columns);
        return $worksheet;
    }

    /**
     * Factory method for instantiating a DataFrame from a SQL query.
     * @param  PDO $pdo
     * @param  $sqlQuery
     * @return DataFrame
     * @since  0.3.0
     */
    public static function fromSQL($sqlQuery, PDO $pdo)
    {
        $sql = new SQL($pdo);
        $data = $sql->select($sqlQuery);
        return new DataFrame($data);
    }

    /**
     * Commits a DataFrame to a SQL database.
     * @param PDO $pdo
     * @param $tableName
     * @param array $options
     * @since 0.2.0
     */
    public function toSQL($tableName, PDO $pdo, array $options = [])
    {
        $sql = new SQL($pdo);
        $sql->insertInto($tableName, $this->columns, $this->data, $options);
    }

    /**
     * Factory method for instantiating a DataFrame from a JSON string.
     * @param  $jsonString
     * @param  array $options
     * @return mixed
     * @since  0.4.0
     */
    public static function fromJSON($jsonString, array $options = [])
    {
        $json = new JSON();
        $data = $json->decodeJSON($jsonString, $options);
        return new DataFrame($data);
    }

    /**
     * Converts a DataFrame to a JSON string.
     * @param  array $options
     * @return string
     * @since  0.4.0
     */
    public function toJSON(array $options = [])
    {
        $json = new JSON();
        $data = $json->encodeJSON($this->data, $options);
        return $data;
    }

    /**
     * Outputs a DataFrame to an HTML string.
     * @param  array $options
     * @return array
     * @throws \Archon\Exceptions\NotYetImplementedException
     * @since  0.1.0
     */
    public function toHTML($options = [])
    {
        $html = new HTML($this->data);
        $output = $html->assembleTable($options);
        return $output;
    }

    /**
     * Factory method for creating a DataFrame from a two-dimensional associative array.
     * @param  array $data
     * @return DataFrame
     * @since  0.1.0
     */
    public static function fromArray(array $data)
    {
        return new DataFrame($data);
    }

    /**
     * Outputs a DataFrame as a two-dimensional associative array.
     * @return array
     * @since 0.1.0
     */
    public function toArray()
    {
        return $this->data;
    }
}
