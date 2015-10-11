<?php namespace Archon\IO;

use RuntimeException;

class CSV {

    const DEFAULT_SEGMENT_SEPARATOR = ',';
    const DEFAULT_NEWLINE_SEPARATOR = "\n";
    const DEFAULT_QUOTE_STRING = "\"";
    const DEFAULT_ESCAPE_CHARACTER = "\\";
    const DEFAULT_COLUMN_FILE_LINE = 0;

    const DEFAULT_OVERWRITE_FILE = false;

    public function __construct($fileName) {
        $this->fileName = $fileName;
    }

    private function setDefaultOptions(array &$options) {
        $options['sep'] = isset($options['sep']) ? $options['sep'] : self::DEFAULT_SEGMENT_SEPARATOR;
        $options['nlsep'] = isset($options['nlsep']) ? $options['nlsep'] : self::DEFAULT_NEWLINE_SEPARATOR;
        $options['columns'] = isset($options['columns']) ? $options['columns'] : null;
        $options['colline'] = isset($options['colline']) ? $options['colline'] : self::DEFAULT_COLUMN_FILE_LINE;
        $options['colmap'] = isset($options['colmap']) ? $options['colmap'] : null;
        $options['quote'] = isset($options['quote']) ? $options['quote'] : self::DEFAULT_QUOTE_STRING;
        $options['escape'] = isset($options['escape']) ? $options['escape'] : self::DEFAULT_ESCAPE_CHARACTER;

        $options['overwrite'] = isset($options['overwrite']) ? $options['overwrite'] : self::DEFAULT_OVERWRITE_FILE;
        return $options;
    }

    public function loadFile(array $options = []) {
        $fileName = $this->fileName;
        $options = $this->setDefaultOptions($options);

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
        foreach($fileData as &$line) {
            $line = trim($line);

            if ($line === '') {
                unset($line);
                continue;
            }

            if ($line !== '') {
                $line = str_getcsv($line, $sepOpt, $quoteOpt, $escapeOpt);
                $line = $this->applyColMapToColumns($line, $columns);
            }
        }

        $fileData = array_values($fileData);
        return $fileData;
    }

    public function applyColMapToColumns(array $row, array $columns) {
        $newRow = [];

        foreach($row as $i => &$column) {
            if ($columns[$i] === null) {
                continue;
            }

            $newRow[$columns[$i]] = $column;
        }

        return $newRow;
    }

    public function saveFile(array $data, array $options = []) {
        $fileName = $this->fileName;
        $options = $this->setDefaultOptions($options);

        $overwriteOpt = $options['overwrite'];
        $sepOpt = $options['sep'];
        $quoteOpt = $options['quote'];

        if (file_exists($fileName) and $overwriteOpt !== false) {
            throw new RuntimeException("Write failed. File {$fileName} exists.");
        }

        $quoted = function($el) use ($quoteOpt) {
            return $quoteOpt . $el . $quoteOpt;
        };

        $header = current($data);
        $header = array_keys($header);
        $header = array_map($quoted, $header);

        $output = [];
        $output[] = implode($sepOpt, $header);

        foreach($data as $row) {
            $row = array_map($quoted, $row);
            $output[] = implode($sepOpt, $row);
        }

        $output = implode(PHP_EOL, $output);

        file_put_contents($fileName, $output);

    }
}
