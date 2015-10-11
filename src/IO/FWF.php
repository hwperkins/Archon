<?php namespace Archon\IO;

/**
 * @link https://github.com/HWGehring/Archon for the canonical source repository
 * @license https://github.com/HWGehring/Archon/blob/master/LICENSE BSD 3-Clause
 */
class FWF {

    public static function load_fwf($filename, $colspecs, $is_good_line=NULL, $is_bad_line=NULL, $header=FALSE) {
        $file_data = array_filter(explode("\n", file_get_contents($filename)));

        // TODO: User-specified columns
        $raw_data = [];

        $file_data = $is_good_line ? preg_grep($is_good_line, $file_data) : $file_data;
        $file_data = $is_bad_line ? preg_grep($is_bad_line, $file_data, PREG_GREP_INVERT) : $file_data;

        foreach($file_data as $file_line) {
            $mem_line = [];
            foreach($colspecs as $colname => $coords) {
                $field = trim(substr($file_line, $coords[0], $coords[1] - $coords[0]));

                // Handle type suggestions
                if (isset($coords[2])) {
                    if ($field === "") {
                        $field = [
                            "" => "",
                            "STRING" => "",
                            "DATE" => "0001-01-01",
                            "FLOAT" => "0.00",
                            "DECIMAL" => "0.00",
                            "INT" => "0"
                        ][$coords[2]];
                    } elseif ($coords[2] == "DATE") {
                        $field = date("Y-m-d", strtotime($field));
                    } elseif ($coords[2] == "DECIMAL") {
                        // Fix placement of negative sign for CPSI systems.
                        if (substr($field, -1) === '-') {
                            $field = '-'.substr($field, 0, -1);
                        }
                    }
                }
                $mem_line[$colname] = $field;
            }
            $raw_data[] = $mem_line;
        }

        $columns = array_keys($colspecs);

        return [
            "columns" => $columns,
            "data" => $raw_data
        ];
    }
}
