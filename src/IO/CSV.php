<?php

/**
 * Contains the CSV class.
 * @package   DataFrame
 * @author    Howard Gehring <hwgehring@gmail.com>
 * @copyright 2015 Howard Gehring <hwgehring@gmail.com>
 * @license   https://github.com/HWGehring/Archon/blob/master/LICENSE BSD-3-Clause
 * @link      https://github.com/HWGehring/Archon
 * @since     0.1.0
 */

namespace Archon\IO;

use Archon\Exceptions\FileExistsException;
use Archon\Exceptions\InvalidColumnException;
use RuntimeException;

/**
 * The CSV class contains implementation details for reading and writing files in the CSV format.
 * @package   Archon\IO
 * @author    Howard Gehring <hwgehring@gmail.com>
 * @copyright 2015 Howard Gehring <hwgehring@gmail.com>
 * @license   https://github.com/HWGehring/Archon/blob/master/LICENSE BSD-3-Clause
 * @link      https://github.com/HWGehring/Archon
 * @since     0.1.0
 */
final class CSV
{

    private $defaultOptions = [
        'sep' => null,
        'nlsep' => "\n",
        'columns' => null,
        'colline' => 0,
        'colmap' => null,
        'mapping' => null,
        'quote' => "\"",
        'escape' => "\\",
        'overwrite' => false,
        'include' => null,
        'exclude' => null
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
     *      mapping: colmap alias
     *      quote:   The character used to specify literal quoted segments (default: ")
     *      escape:  The character used to escape quotes or other special characters (default: \)
     *      include: Whitelist Regular Expression
     *      exclude: Blacklist Regular Expression
     * @param  array $options The option map.
     * @return array Returns multi-dimensional array of row-column strings.
     * @throws InvalidColumnException
     * @since  0.1.0
     */
    public function loadFile(array $options = [])
    {
        $fileName = $this->fileName;
        $options = Options::setDefaultOptions($options, $this->defaultOptions);

        $sepOpt = $options['sep'];
        $columnsOpt = $options['columns'];
        $collineOpt = $options['colline'];
        $quoteOpt = $options['quote'];
        $escapeOpt = $options['escape'];
        $includeRegexOpt = $options['include'];
        $excludeRegexOpt = $options['exclude'];

        $colmapOpt = $options['colmap'] ?? $options['mapping'];

        $fileData = file_get_contents($fileName);
        $fileData = $this->scrubRawData($fileData, $options);

        if ($sepOpt === null) {
            $sepOpt = self::autoDetectDelimiter($fileData);
        }

        /**
         * Determines how to assign columns of the CSV
         * First checks if options specify a line of the file to use
         * Otherwise uses columns specified by user
         */
        if ($columnsOpt === null) {
            $columns = $fileData[$collineOpt];
            $columns = str_getcsv($columns, $sepOpt, $quoteOpt, $escapeOpt);
            $columns = array_map('trim', array_map('trim', $columns));

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

            unset($fileData[$collineOpt]);
        } else {
            $columns = $columnsOpt;
        }

        $fileData = $includeRegexOpt ? preg_grep($includeRegexOpt, $fileData) : $fileData;
        $fileData = $excludeRegexOpt ? preg_grep($excludeRegexOpt, $fileData, PREG_GREP_INVERT) : $fileData;

        /**
         * Parses each trimmed line with str_getcsv as an associative array
         * Skips lines which trim to empty string
         */
        foreach ($fileData as $i => $line) {
            $line = trim($line);

            $line = str_getcsv($line, $sepOpt, $quoteOpt, $escapeOpt);

            if (count($columns) != count($line)) {
                throw new InvalidColumnException("Column count of line {$i} does not match column count of header.");
            }

            $fileData[$i] = array_map('trim', $this->applyColMapToRowKeys($line, $columns));
        }

        $fileData = array_values($fileData);
        return $fileData;
    }

    /**
     * Will rename the associative array key for a row to its isometric column value. This is done because row elements
     * initially have no associative array key, but the column array has already been transformed based on user
     * specification, or the column line of the CSV file.
     * @param  array $row
     * @param  array $columns
     * @return array
     * @since  0.1.0
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

    private function scrubRawData($fileData, $options) {
        $options = Options::setDefaultOptions($options, $this->defaultOptions);
        $nlsepOpt = $options['nlsep'];

        $fileData = trim($fileData);

        // Remove non-ASCII characters from each line of the file
        $fileData = preg_replace("/[^[:ascii:]]/", "", $fileData);
        $fileData = str_replace("\f", '', $fileData); // remove form feed

        $fileData = preg_split("/\r\n|\n|\r|{$nlsepOpt}/", $fileData);

        foreach ($fileData as $i => &$line) {
            try {
                $inputEncoding = mb_detect_encoding($line, mb_detect_order(), true);
                $line = iconv($inputEncoding, "UTF-8", $line);
            } catch (\Exception $e) {
                throw new \Exception("Detected illegal character {$i}: {$line}");
            }
        }

        // Remove whitespace/empty lines
        $fileData = preg_grep('/^\s*$/', $fileData, PREG_GREP_INVERT);

        return $fileData;
    }

    /**
     * Auto detects the delimiter used in a given CSV file from a list of given delimiters.
     *
     * @param array $data
     * @return int|mixed|null|string
     */
    private function autoDetectDelimiter(array $data) {
        $delimiters = [
            ',',
            '\t',
            ';',
            '|',
            ':'
        ];

        $results = [];

        $rowCount = 0;
        foreach ($data as $row) {
            $rowCount += 1;

            foreach ($delimiters as $delimiter) {
                $fields = preg_split('/['.$delimiter.']/', $row);

                if (count($fields) > 1) {
                    $results[$delimiter] = $results[$delimiter] ?? 0;
                    $results[$delimiter] += 1;
                }
            }

            if ($rowCount === 5) break;
        }

        $delimiter = null;
        $highestCount = 0;
        foreach ($results as $result => $count) {
            if ($count > $highestCount) {
                $highestCount = $count;
                $delimiter = $result;
            }
        }

        if ($delimiter === null) {
            throw new RuntimeException("Error: Could not auto-detect CSV delimiter. Please specify 'sep' option where loading CSV.");
        }

        return $delimiter;
    }

    /**
     * Saves the provided two-dimensional array to the file which the CSV class was instantiated with.
     * Options include:
     *      sep:       The CSV separator (default: ,)
     *      quote:     The character used to specify literal quoted segments (default: ")
     *      overwrite: Boolean option for specifying whether a file which exists should be overwritten (default: false)
     * @param  array $data
     * @param  array $options
     * @throws FileExistsException
     * @throws \Archon\Exceptions\UnknownOptionException
     * @since  0.1.0
     */
    public function saveFile(array $data, array $options = [])
    {
        $fileName = $this->fileName;
        $options = Options::setDefaultOptions($options, $this->defaultOptions);

        $overwriteOpt = $options['overwrite'];
        $sepOpt = $options['sep'] ?? ',';
        $quoteOpt = $options['quote'];
        $escapeOpt = $options['escape'];

        if (file_exists($fileName) and $overwriteOpt === false) {
            throw new FileExistsException("Write failed. File {$fileName} exists.");
        }

        $file = fopen($fileName, 'w');

        $columns = array_keys($data[0]);
        fputcsv($file, $columns, $sepOpt, $quoteOpt, $escapeOpt);

        foreach($data as $row) {
            fputcsv($file, $row);
        }

        fclose($file);
    }
}
