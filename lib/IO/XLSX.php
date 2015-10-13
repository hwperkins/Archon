<?php

/**
 * Contains the XLSX class.
 * @package   DataFrame
 * @author    Howard Gehring <hwgehring@gmail.com>
 * @copyright 2015 Howard Gehring <hwgehring@gmail.com>
 * @license   https://github.com/HWGehring/Archon/blob/master/LICENSE BSD-3-Clause
 * @link      https://github.com/HWGehring/Archon
 * @since     0.3.0
 */

namespace Archon\IO;

use PHPExcel;
use PHPExcel_IOFactory;
use PHPExcel_Worksheet;

/**
 * The XLSX class contains implementation details for reading and writing data to and from instances of PHPExcel,
 * PHPExcel_Worksheet, and the XLSX file format in general.
 * @package   Archon\IO
 * @author    Howard Gehring <hwgehring@gmail.com>
 * @copyright 2015 Howard Gehring <hwgehring@gmail.com>
 * @license   https://github.com/HWGehring/Archon/blob/master/LICENSE BSD-3-Clause
 * @link      https://github.com/HWGehring/Archon
 * @since     0.3.0
 */
class XLSX
{

    private $defaultOptions = [
        'colrow' => 1,
        'sheetname' => null
    ];

    public function __construct($fileName)
    {
        $this->fileName = $fileName;
    }

    /**
     * Loads the file which the CSV class was instantiated with.
     * Options include:
     *      colrow:    The row of the spreadsheet which contains column data (default: 1)
     *      sheetname: The name of the worksheet to load. Defaults to first worksheet (default: null)
     * @param  array $options
     * @return array
     * @throws \Archon\Exceptions\UnknownOptionException
     * @throws \PHPExcel_Exception
     * @since  0.3.0
     */
    public function loadFile(array $options)
    {
        $fileName = $this->fileName;

        $options = Options::setDefaultOptions($options, $this->defaultOptions);
        $colRowOpt = $options['colrow'];
        $sheetNameOpt = $options['sheetname'];

        $xlsx = PHPExcel_IOFactory::load($fileName);

        if ($sheetNameOpt === null) {
            $sheet = $xlsx->getActiveSheet();
        } else {
            $sheet = $xlsx->getSheetByName($sheetNameOpt);
        }

        $columns = [];
        $data = [];

        $highestColumn = $sheet->getHighestColumn();
        $highestColumn++;

        foreach ($sheet->getRowIterator($colRowOpt) as $i => $row) {
            for ($column = 'A'; $column != $highestColumn; $column++) {
                /*
                 * If the current row is the column row then assemble our columns.
                 */
                if ($i === $colRowOpt) {
                    $columns[$column] = $sheet->getCell($column.$i)->__toString();
                    continue;
                }

                $currentColumnName = $columns[$column];
                $data[$i][$currentColumnName] = $sheet->getCell($column.$i)->__toString();
            }
        }

        $data = array_values($data);
        return $data;
    }

    /**
     * Converts the columns and data passed to an XLSX worksheet and adds that worksheet to an instance of PHPExcel
     * @param  PHPExcel $excel
     * @param  $worksheetTitle
     * @param  array $data
     * @param  array $columns
     * @return PHPExcel_Worksheet
     * @throws \PHPExcel_Exception
     * @since  0.3.0
     */
    public static function saveToWorksheet(PHPExcel &$excel, $worksheetTitle, array $data, array $columns)
    {
        // Check if this is a brand new spreadsheet
        if ($excel->getSheetCount() === 1) {
            $sheet = $excel->getActiveSheet();
            $sheetName = $sheet->getCodeName();

            $colCount = $sheet->getHighestColumn();
            $rowCount = $sheet->getHighestRow();

            $cell = $sheet->getCell('A1')->getValue();

            // If this is a brand new spreadsheet then remove the first worksheet
            if ($sheetName === 'Worksheet' and $colCount === 'A' and $rowCount === 1 and $cell === null) {
                $excel->removeSheetByIndex(0);
            }
        }

        $worksheet = new PHPExcel_Worksheet($excel, $worksheetTitle);

        $wsArray = [$columns];
        foreach ($data as $row) {
            $wsArray[] = array_values($row);
        }

        $worksheet->fromArray($wsArray);
        $excel->addSheet($worksheet);
        return $worksheet;
    }
}
