<?php namespace Archon\IO;

use RuntimeException;

class CSV {

    const DEFAULT_SEGMENT_SEPARATOR = ',';
    const DEFAULT_NEWLINE_SEPARATOR = "\n";
    const DEFAULT_QUOTE_STRING = "\"";
    const DEFAULT_ESCAPE_CHARACTER = "\\";
    const DEFAULT_COLUMN_FILE_LINE = 0;

    private function __construct() {

    }

    public static function fromFile($fileName, array $options) {
        $csv = new CSV();
        $options = $csv->setDefaultOptions($options);
        return $csv->loadFile($fileName, $options);
    }

    private function setDefaultOptions(array &$options) {
        $options['sep'] = isset($options['sep']) ? $options['sep'] : self::DEFAULT_SEGMENT_SEPARATOR;
        $options['nlsep'] = isset($options['nlsep']) ? $options['nlsep'] : self::DEFAULT_NEWLINE_SEPARATOR;
        $options['columns'] = isset($options['columns']) ? $options['columns'] : null;
        $options['colline'] = isset($options['colline']) ? $options['colline'] : self::DEFAULT_COLUMN_FILE_LINE;
        $options['colmap'] = isset($options['colmap']) ? $options['colmap'] : null;
        $options['quote'] = isset($options['quote']) ? $options['quote'] : self::DEFAULT_QUOTE_STRING;
        $options['escape'] = isset($options['escape']) ? $options['escape'] : self::DEFAULT_ESCAPE_CHARACTER;
        return $options;
    }

    private function loadFile($fileName, array $options) {
        $sepOpt = $options['sep'];
        $nlsepOpt = $options['nlsep'];
        $columnsOpt = $options['columns'];
        $collineOpt = $options['colline'];
        $colmapOpt = $options['colmap']; // TODO
        $quoteOpt = $options['quote'];
        $escapeOpt = $options['escape'];

        $fileData = file_get_contents($fileName);
        $fileData = explode($nlsepOpt, $fileData);

        if ($columnsOpt === null) {
            $columns = $fileData[$collineOpt];
            $columns = str_getcsv($columns, $sepOpt, $quoteOpt, $escapeOpt);
            unset($fileData[$collineOpt]);
        } else {
            $columns = $columnsOpt;
        }

        foreach($fileData as &$line) {
            $line = trim($line);
            if ($line !== '') {
                $line = str_getcsv($line, $sepOpt, $quoteOpt, $escapeOpt);
                $line = array_combine($columns, $line);
            } else {
                // Remove blank lines
                unset($line);
            }
        }

        $fileData = array_values($fileData);
        return $fileData;
    }

    public static function load_csv($filename, $sep, $nlsep, $columns, $colrow, $mapping, $quote_string, $escape_character) {
        $pre_csv = trim(file_get_contents($filename));
        $pre_csv = explode($nlsep, $pre_csv);

        $csv = [];
        foreach($pre_csv as $line) {
            if (trim($line) !== '') $csv[] = $line;
        }
        unset($pre_csv);

        foreach($csv as &$line) {
            $line = str_getcsv($line, $sep, $quote_string, $escape_character);
        }

        $trim_recursive = function($input) use (&$trim_recursive) {
            if (is_array($input)) {
                return array_map($trim_recursive, $input);
            } else {
                return trim($input);
            }
        };

        $file_data = $trim_recursive($csv);

        if ($columns === NULL) {
            // Extract columns from $colrow line of file
            $columns = $file_data[$colrow];
            unset($file_data[$colrow]);
        }

        foreach ($file_data as &$file_line) {
            if (sizeof($columns) != sizeof($file_line)) {
                echo print_r($columns) . "</br>";
                echo print_r($file_line) . "</br>";
                throw new RuntimeException("Column length does not match exploded length of line.");
            }

            $file_line = array_combine($columns, $file_line);
        }

        // If we specify no custom mapping then the CSV is fully parsed at this moment
        if ($mapping === NULL) return $file_data;

        if (!is_array($mapping)) throw new RuntimeException("Error: CSV Mapping must be associative array.");

        // Identify columns which we should remove (mapping is associated to null)
        // and columns which we should rename
        $columns_to_remove = [];
        $columns_to_rename = [];
        foreach($mapping as $key => $value) {
            if ($value === NULL) {
                $columns_to_remove[$key] = $value;
            } else {
                $columns_to_rename[$key] = $value;
            }
        }

        foreach($columns_to_remove as $key => $value) {
            $col_index = array_search($key, $columns);
            if ($col_index === FALSE) continue; // Skip mappings that don't exist so we can use mappings as a "catch-all"
            unset($columns[$col_index]);
            foreach($file_data as &$file_line) {
                unset($file_line[$key]);
            }
        }

        foreach($columns_to_rename as $key => $value) {
            $col_index = array_search($key, $columns);
            if ($col_index === FALSE) continue; // Skip mappings that don't exist so we can use mappings as a "catch-all"
            $columns[$col_index] = $value;
        }

        foreach($file_data as &$file_line) {
            $file_line = array_combine($columns, array_values($file_line));
        }

        return $file_data;
    }

    public static function save_csv($filename, array $data, $sep, $overwrite) {
        if (!file_exists($filename) or $overwrite) {
            $output = []; // First, implode rows of data on delimiter
            foreach($data as $row) {
                $output[] = implode($sep, $row);
            }

            $output = implode(PHP_EOL, $output); // Then implode on line break

            file_put_contents($filename, $output);
        } else {
            throw new RuntimeException("Write failed. File {$filename} exists.");
        }
    }
}
