<?php

/**
 * Contains the CSV class.
 * @package   DataFrame
 * @author    Howard Gehring <hwgehring@gmail.com>
 * @copyright 2015 Howard Gehring <hwgehring@gmail.com>
 * @license   https://github.com/HWGehring/Archon/blob/master/LICENSE BSD-3-Clause
 * @link      https://github.com/HWGehring/Archon
 */

namespace Archon\IO;

use Archon\Exceptions\FileExistsException;

/**
 * The CSV class contains implementation details for reading and writing files in the CSV format.
 * Options may be passed to the reading/writing functions which specify line and segment separators, characters to use
 * as literal quotes, characters to use to escape special characters, etc.
 * @package   Archon\IO
 * @author    Howard Gehring <hwgehring@gmail.com>
 * @copyright 2015 Howard Gehring <hwgehring@gmail.com>
 * @license   https://github.com/HWGehring/Archon/blob/master/LICENSE BSD-3-Clause
 * @link      https://github.com/HWGehring/Archon
 */
class CSV
{

    private $defaultOptions = [
        'sep' => ',',
        'nlsep' => "\n",
        'columns' => null,
        'colline' => 0,
        'colmap' => null,
        'quote' => "\"",
        'escape' => "\\",

        'overwrite' => false
    ];

    public function __construct($fileName)
    {
        $this->fileName = $fileName;
    }

    /**
     * Loads the file which the CSV class was instantiated with.
     * Options include:
     *      sep:     The CSV separator (default: ,)
     *      nlsep:   The new line separator (default: \n)
     *      columns: Optional column names to use (default: null)
     *      colline: The line of the CSV file where columns are specified (default: 0)
     *      colmap:  Optional mapping for renaming columns from what they are in the file to what the user wants them
     *               to be once loaded into memory (default: null)
     *      quote:   The character used to specify literal quoted segments (default: ")
     *      escape:  The character used to escape quotes or other special characters (default: \)
     * @param array $options The option map.
     * @return array|string  Returns multi-dimensional array of row-column strings.
     * @throws \Archon\Exceptions\UnknownOptionException
     */
    public function loadFile(array $options = [])
    {
        $fileName = $this->fileName;
        $options = Options::setDefaultOptions($options, $this->defaultOptions);

        $sepOpt = $options['sep'];
        $nlsepOpt = $options['nlsep'];
        $columnsOpt = $options['columns'];
        $collineOpt = $options['colline'];
        $colmapOpt = $options['colmap'];
        $quoteOpt = $options['quote'];
        $escapeOpt = $options['escape'];

        $fileData = file_get_contents($fileName);
        $fileData = explode($nlsepOpt, $fileData);

        /**
         * Determines how to assign columns of the CSV
         * First checks if options specify a line of the file to use
         * Otherwise uses columns specified by user
         */
        if ($columnsOpt === null) {
            $columns = $fileData[$collineOpt];
            $columns = str_getcsv($columns, $sepOpt, $quoteOpt, $escapeOpt);
            unset($fileData[$collineOpt]);
        } else {
            $columns = $columnsOpt;
        }

        /**
         * Rename columns if a colmap exists
         * Columns which are mapped to null are flagged for removal
         */
        if ($colmapOpt !== null) {
            foreach ($columns as &$column) {
                if (array_search($column, array_keys($colmapOpt)) !== false) {
                    $column = $colmapOpt[$column];
                }
            }
        }

        /**
         * Parses each trimmed line with str_getcsv as an associative array
         * Skips lines which trim to empty string
         */
        foreach ($fileData as $i => $line) {
            $line = trim($line);

            if ($line === '') {
                unset($fileData[$i]);
                continue;
            }

            if ($line !== '') {
                $line = str_getcsv($line, $sepOpt, $quoteOpt, $escapeOpt);
                $fileData[$i] = $this->applyColMapToRowKeys($line, $columns);
            }
        }

        $fileData = array_values($fileData);
        return $fileData;
    }

    /**
     * Will rename the associative array key for a row to its isometric column value. This is done because row elements
     * initially have no associative array key, but the column array has already been transformed based on user
     * specification, or the column line of the CSV file.
     * @param array $row
     * @param array $columns
     * @return array
     */
    private function applyColMapToRowKeys(array $row, array $columns)
    {
        $newRow = [];

        foreach ($row as $i => $column) {
            if ($columns[$i] === null) {
                /* Skip colums which are associated to null, because they represent row elements which the user
                 * does not wish to load from their file.
                 */
                continue;
            }

            // Assign the row element an associative key equal to the column it relates to.
            $newRow[$columns[$i]] = $column;
        }

        return $newRow;
    }

    /**
     * Saves the provided two-dimensional array to the file which the CSV class was instantiated with.
     * Options include:
     *      sep:       The CSV separator (default: ,)
     *      quote:     The character used to specify literal quoted segments (default: ")
     *      overwrite: Boolean option for specifying whether a file which exists should be overwritten (default: false)
     * @param array $data
     * @param array $options
     * @throws FileExistsException
     * @throws \Archon\Exceptions\UnknownOptionException
     */
    public function saveFile(array $data, array $options = [])
    {
        $fileName = $this->fileName;
        $options = Options::setDefaultOptions($options, $this->defaultOptions);

        $overwriteOpt = $options['overwrite'];
        $sepOpt = $options['sep'];
        $quoteOpt = $options['quote'];

        if (file_exists($fileName) and $overwriteOpt === false) {
            throw new FileExistsException("Write failed. File {$fileName} exists.");
        }

        $quoted = function ($elem) use ($quoteOpt) {
            return $quoteOpt . $elem . $quoteOpt;
        };

        $header = current($data);
        $header = array_keys($header);
        $header = array_map($quoted, $header);

        $output = [];
        $output[] = implode($sepOpt, $header);

        foreach ($data as $row) {
            $row = array_map($quoted, $row);
            $output[] = implode($sepOpt, $row);
        }

        $output = implode(PHP_EOL, $output);

        file_put_contents($fileName, $output);

    }
}
