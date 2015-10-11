<?php namespace Archon\IO;

use RuntimeException;

/**
 * @link https://github.com/HWGehring/Archon for the canonical source repository
 * @license https://github.com/HWGehring/Archon/blob/master/LICENSE BSD 3-Clause
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
                $fileData[$i] = $this->applyColMapToColumns($line, $columns);
            }
        }

        $fileData = array_values($fileData);
        return $fileData;
    }

    public function applyColMapToColumns(array $row, array $columns)
    {
        $newRow = [];

        foreach ($row as $i => &$column) {
            if ($columns[$i] === null) {
                continue;
            }

            $newRow[$columns[$i]] = $column;
        }

        return $newRow;
    }

    public function saveFile(array $data, array $options = [])
    {
        $fileName = $this->fileName;
        $options = Options::setDefaultOptions($options, $this->defaultOptions);

        $overwriteOpt = $options['overwrite'];
        $sepOpt = $options['sep'];
        $quoteOpt = $options['quote'];

        if (file_exists($fileName) and $overwriteOpt === false) {
            throw new RuntimeException("Write failed. File {$fileName} exists.");
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
