<?php namespace Archon;

use Archon\IO\EDIRevB;
use Closure;
use ArrayObject;
use DateTime;
use PDO;
use RuntimeException;
use Archon\IO\CSV;
use Archon\IO\FWF;

class DataFrameLegacy extends ArrayObject {

    private $dataframe = [
        'columns' => [],
        'data' => []
    ];

    public function __construct($columns=NULL, $data=NULL) {
        // TODO: Write unit tests for DataFrames created with duplicate column names!!! This causes data loss!!!

        if ($columns && $data) {
            $this->dataframe['columns'] = $columns;
            foreach($data as $row) {
                array_push($this->dataframe['data'], array_combine($columns, $row));
            }
        } elseif ($data) {
            $this->dataframe['columns'] = array_keys($data[0]);
            $this->dataframe['data'] = $data;
        } elseif ($columns) {
            $this->dataframe['columns'] = $columns;
            array_push($this->dataframe['data'], array_combine($columns, array_fill(0, sizeof($columns), Null)));
        }
    }

    /**
     * Allows user retrieve DataFrame subsets from a two-dimensional array by simply requesting an element of
     * the instantiated DataFrame.
     *
     * ie: $foo_df = $df['foo'];
     *
     * @param mixed $key
     * @return DataFrame
     */
    public function offsetGet($key) {
        $col = array_column($this->dataframe['data'], $key);
        foreach($col as $i => $row) $col[$i] = [$key => $row];
        return new DataFrame($columns=[$key], $data=$col);
    }

    /**
     * Allows user to retrieve DataFrame subsets from a two-dimensional array by simply requesting a field name.
     *
     * ie: $foo_df = $df->foo;
     *
     * @param $key
     * @return DataFrame
     */
    public function __get($key) {
        return $this->offsetGet($key);
    }

    /**
     * Allows user set DataFrame columns from a Closure, string, or another single-column DataFrame.
     *
     * ie:
     *      $df['bar'] = $df['foo'];
     *      $df['bar'] = $df->foo;
     *      $df['foo'] = function ($foo) { return $foo + 1; };
     *      $df['foo'] = "bar";
     *
     * @param mixed $key
     * @param mixed $value
     */
    public function offsetSet($key, $value) {
        if ($value instanceof DataFrame) {
            if ($value->width() !== 1) throw new RuntimeException("Error: Can only apply DataFrame to DataFrame of a single column.");

            if (!$this->has_column($key)) $this->dataframe['columns'][] = $key;
            $new_column = $value->to_array();
            // Apply one DataFrame to another ie: $df['key'] = $df['another_key'];
            foreach($this->dataframe['data'] as $i => &$row) {
                if ($new_column[$i] === NULL) {
                    echo "<pre>";
                    var_dump($key);
                    var_dump($value);
                    echo "<pre>";
                    throw new RuntimeException("Error: Attempting to copy non-existent DataFrame.");
                }
                $row[$key] = array_pop($new_column[$i]);
            }

        } elseif ($value instanceof Closure) {
            // Apply closure ie: $df['key'] = function($value) { return "new value"; };
            foreach ($this->dataframe['data'] as &$row) $row[$key] = $value($row[$key]);
        } else {
            // Apply global value ie: $df['key'] = "all keys will equal this";
            $this->dataframe['columns'][] = $key;
            foreach($this->dataframe['data'] as &$row) $row[$key] = $value;
        }
    }

    /**
     * Allows user to assign DataFrame subsets by field name.
     *
     * ie:
     *      $df->bar = $df['foo'];
     *      $df->bar = $df->foo;
     *      $df->foo = function ($foo) { return $foo + 1; };
     *      $df->foo = "bar";
     *
     * @param $key
     * @param $value
     */
    public function __set($key, $value) {
        $this->offsetSet($key, $value);
    }

    /**
     * No idea what this function does, but it isn't necessary for the time being.
     *
     * @param mixed $key
     * @return mixed
     */
    public function offsetExists($key) {
        // TODO: Investigate offsetExists.
        echo "offsetExists: {$key}";
        return $key;
    }

    /**
     * No idea what this function does, but it isn't necessary for the time being.
     *
     * @param mixed $key
     * @return mixed
     */
    public function offsetUnset($key) {
        echo "offsetUnset: {$key}";
        return $key;
    }

    /**
     * Allows user to "array_merge" two DataFrames so that the rows of one are appended to the rows of another.
     *
     * @param mixed $other
     * @return $this
     */
    public function append($other) {
        $other = $other->to_array();
        $this->dataframe['data'] = array_merge($this->dataframe['data'], $other);
        return $this;
    }

    /**
     * Allows user to apply functions to entire columns of a DataFrame. May be combined with other features
     * to allow the user to copy a transformation of a column to another new or existing column.
     *
     * ie:
     *      $df->holds_numbers = $df->holds_numbers->apply(function ($x) { return $x + 1; });
     *      $df->first_name = $df->full_name->apply(function ($name) { return parse_name($name); });
     *
     * @param Closure $f
     * @return DataFrame
     */
    public function apply(Closure $f) {
        if ($this->width() !== 1) throw new RuntimeException("Error: Can only apply function to DataFrame of a single column.");

        $columns = $this->dataframe['columns'];
        $data = $this->dataframe['data'];
        foreach($data as $i => &$row) $row[key($row)] = $f($row[key($row)]);
        return new DataFrame($columns, $data);
    }

    public function for_each(Closure $f) {
        foreach($this->dataframe['data'] as &$row) {
            $f($row);
        }
    }

    public function sum($columns = []) {
        if ($columns == []) {
            $columns = $this->dataframe['columns'];
        } else {
            foreach($columns as $column) {
                $this->has_column($column, TRUE);
            }
        }

        $df = [];
        foreach($columns as $column) {
            $df[$column] = array_sum(array_column($this->dataframe['data'], $column));
        }

        return DataFrame::from_array([$df]);
    }

    /**
     * Allows user to apply type default values to certain columns when necessary. This is usually utilized
     * in conjunction with a database to avoid certain invalid type defaults (ie: dates of 0000-00-00).
     *
     * ie:
     *      $df->map_types([
     *          'some_amount' => 'DECIMAL',
     *          'some_int' => 'INT',
     *          'some_date' => 'DATE'
     *      ]);
     *
     * @param array $type_map
     * @param string $date_format
     */
    public function map_types(array $type_map, $date_format = 'mdY') {
        foreach ($this->dataframe['data'] as &$row) {
            foreach($type_map as $column => $type) {
                /*
                 * Type conversion for base types or empty strings ONLY
                 * Reason being that if a piece of data is actually bad then
                 * we want the database (or PHP) to throw an error.
                 * PHP type conversion is EXTREMELY WEAK and will happily convert
                 * ie: '0.1234asdf' into '0.1234' when cast as float.
                 */
                $value = $row[$column];
                switch($type) {
                    case ('FLOAT'):
                        if ($value === '0' || $value === '') {
                            $row[$column] = '0.00';
                        }
                        break;
                    case ('DECIMAL'):
                        if ($value === '0' || $value === '') {
                            $row[$column] = '0.00';
                        }
                        break;
                    case 'INT':
                        if ($value === '') {
                            $row[$column] = '0';
                        }
                        break;
                    case 'DATE':
                        if ($value === '') {
                            $row[$column] = '0001-01-01';
                        } else {
                            $value = DateTime::createFromFormat($date_format, $value);
                            $row[$column] = $value->format('Y-m-d');
                        }
                        break;
                }
            }
        }
    }

    /**
     * Returns number of columns in DataFrame.
     *
     * @return int
     */
    public function width() {
        return sizeof($this->dataframe['columns']);
    }

    /**
     * Returns number of rows in DataFrame.
     *
     * @return int
     */
    public function length() {
        return sizeof($this->dataframe['data']);
    }

    /**
     * Removes requested column.
     *
     * ie:
     *      $df->remove_column('junk');
     *
     * @param $column_name
     */
    public function remove_column($column_name) {
        $this->has_column($column_name, $strict = TRUE);

        foreach($this->dataframe['data'] as &$row) {
            unset($row[$column_name]);
        }

        if(($key = array_search($column_name, $this->dataframe['columns'])) !== FALSE) {
            unset($this->dataframe['columns'][$key]);
        }
    }

    /**
     * Returns whether specific column exists.
     *
     * ie:
     *      if ($df->has_column('junk') $df->remove_column('junk);
     *
     * @param $column_name
     * @param bool|FALSE $strict
     * @return bool
     */
    public function has_column($column_name, $strict = FALSE) {
        if (array_search($column_name, $this->dataframe['columns']) === FALSE) {
            if ($strict === TRUE) {
                var_dump($this->dataframe['columns']);
                echo "</br>";
                var_dump($column_name);
                echo "</br>";
                throw new RuntimeException("Error: {$column_name} doesn't exist in DataFrame.");
            }
            return FALSE;
        } else {
            return TRUE;
        }
    }

    /**
     * Renames specific column.
     *
     * ie:
     *      $df->rename_column('old_name', 'new_name');
     *
     * @param $from
     * @param $to
     */
    public function rename_column($from, $to) {
        $this->has_column($from, $strict = TRUE);

        foreach($this->dataframe['data'] as &$row) {
            $keys = array_keys($row);
            $index = array_search($from, $keys);
            $keys[$index] = $to;
            $row = array_combine($keys, $row);
        }

        if(($key = array_search($from, $this->dataframe['columns'])) !== FALSE) {
            $this->dataframe['columns'][$key] = $to;
        }
    }

    /**
     * Creates DataFrame from a two-dimensional array.
     *
     * @param array $associative_array
     * @return DataFrame
     */
    public static function from_array(array $associative_array) {
        return new DataFrame($columns=NULL, $data=$associative_array);
    }

    /**
     * Returns DataFrame as a two-dimensional array.
     *
     * @return mixed
     */
    public function to_array() {
        return $this->dataframe['data'];
    }

    /**
     * Creates a DataFrame from a SQL query.
     *
     * ie:
     *
     *      $df = DataFrame::from_sql('SELECT * FROM table;', $db);
     *
     * @param $sql
     * @param PDO $db
     * @return DataFrame
     */
    public static function from_sql($sql, PDO $db) {
        $data = $db->query($sql);
        if ($data === []) {
            echo "<pre>".$sql."</pre></br>";
            throw new \PDOException("Error: No results returned.");
        }

        $columns = array_keys($data[0]);
        return new DataFrame($columns, $data);
    }

    /**
     * Generates an UPDATE statement from a given DataFrame and commits it to database.
     *
     * @param $schema
     * @param $table
     * @param PDO $db
     * @param array $args
     * @return int
     */
    public function to_sql($schema, $table, PDO $db, array $args = []) {
        $chunk_size = isset($args['chunk_size']) ? $args['chunk_size'] : 5000;
        $replace = isset($args['replace']) ? $args['replace'] : FALSE;

        $columns = $this->dataframe['columns'];
        $missing_columns = [];
        foreach($columns as $col) {
            // TODO: Implement in PDODriver
            if (!$db->has_column($schema, $table, $col)) $missing_columns[] = $col;
        }

        if (sizeof($missing_columns) !== 0) {
            $s = sizeof($missing_columns) > 1 ? "s" : "";
            $columns = implode(", ", $missing_columns);
            throw new RuntimeException("Error: Table {$table} in schema {$schema} does not contain the column{$s}: {$columns}.");
        }

        $insertion_array = $this->prepare_chunked_insert($schema, $table, $chunk_size, $replace);
        $affected_rows = 0;
        foreach($insertion_array as $sql) {
            $affected_rows += $db->exec($sql);
        }
        return $affected_rows;
    }

    public function prepare_chunked_insert($schema, $table, $chunk_size, $replace) {
        $insertion_array = [];
        $chunked_data = array_chunk($this->dataframe['data'], $chunk_size);
        foreach($chunked_data as $chunk) {
            $insertion_array[] = $this->prepare_insert($schema, $table, $chunk, $replace);
        }

        return $insertion_array;
    }

    public function prepare_insert($schema, $table, $data=NULL, $replace) {
        $data = $data == NULL ? $this->dataframe['data'] : $data;

        $columns = implode(', ', array_map(function($col){ return "`{$col}`"; }, $this->dataframe['columns']));
        $escape_fields = function ($arr) { foreach($arr as $field) yield "'{$field}'"; };
        $prepare_values = function($arr) use ($escape_fields) { foreach ($arr as $fields) yield implode(", ", iterator_to_array($escape_fields($fields))); };
        $values_sql = function($arr) use ($prepare_values) { return "(".implode("),".PHP_EOL."(", iterator_to_array($prepare_values($arr))).")"; };

        $insert = $replace ? "REPLACE" : "INSERT";
        $schema_table = "`{$schema}`.`{$table}`";

        $sql = "{$insert} INTO $schema_table ({$columns}) ".PHP_EOL."VALUES ".PHP_EOL.$values_sql($data).";";
        return $sql;
    }

    /**
     * Instantiates a DataFrame from a fixed-width-file.
     *
     * ie:
     *      $df = DataFrame::from_fwf('file.txt', [
     *          'col1' => [0, 10],
     *          'col2' => [10, 20]
     *      ], [
     *          'include' => 'REGEX-TO-INCLUDE-LINES-OF',
     *          'exclude' => 'REGEX-TO-EXCLUDE-LINES-OF'
     *      ]);
     *
     * @param $filename
     * @param $colspecs
     * @param array $args
     * @return DataFrame
     */
    public static function from_fwf($filename, $colspecs, array $args = []) {
        $is_good_line = isset($args['include']) ? $args['include'] : NULL;
        $is_bad_line = isset($args['exclude']) ? $args['exclude'] : NULL;
        $header = isset($args['header']) ? $args['header'] : FALSE;

        $fwf = FWF::load_fwf($filename, $colspecs, $is_good_line, $is_bad_line, $header);
        return new DataFrame($fwf['columns'], $fwf['data']);
    }

    public function to_fwf() {
        // TODO: Implement
    }

    /**
     * Instantiates a DataFrame from a CSV file.
     *
     * $df = DataFrame::from_csv('file.csv');
     *
     * $df = DataFrame::from_csv('file.csv', [
     *      'columns' => [
     *          'my_col1_name',
     *          'my_col2_name'
     *      ]
     * ]);
     *
     * $df = DataFrame::from_csv('file.csv', [
     *      'sep' => '*',
     *      'nlsep' => '~',
     *      'mapping' => [
     *          'col1' => 'my_col1_name',
     *          'col2' => 'my_col2_name'
     *      ]
     * ]);
     *
     * @param $filename
     * @param array $args
     * @return DataFrame
     */
    public static function from_csv($filename, array $args = []) {
        $sep = isset($args['sep']) ? $args['sep'] : "|"; // Default to pipe-delimited
        $nlsep = isset($args['nlsep']) ? $args['nlsep'] : PHP_EOL;
        $columns = isset($args['columns']) ? $args['columns'] : NULL;
        $colrow = isset($args['colrow']) && $args['columns'] === NULL ? $args['colrow'] : 0; // Default to first row of file as columns
        $mapping = isset($args['mapping']) ? $args['mapping'] : NULL;
        $quote_string = isset($args['quote_string']) ? $args['quote_string'] : '"';
        $escape_character = isset($args['escape_character']) ? $args['escape_character'] : "\\";

        $csv = CSV::load_csv($filename, $sep, $nlsep, $columns, $colrow, $mapping, $quote_string, $escape_character);
        return new DataFrame($csv['columns'], $csv['data']);
    }

    /**
     * Outputs DataFrame to CSV file.
     *
     * @param $filename
     * @param array $args
     */
    public function to_csv($filename, array $args = []) {
        $sep = isset($args['sep']) ? $args['sep'] : "|";
        $overwrite = isset($args['overwrite']) ? $args['overwrite'] : FALSE;

        CSV::save_csv($filename, $this->dataframe['data'], $sep, $overwrite);
    }

    /**
     * Outputs DataFrame to HTML table. Defaults to a DataTable (see: http://datatables.net )
     *
     * @param array $args
     * @return mixed
     */
    public function to_html(array $args = []) {
        $class = isset($args['class']) ? $args['class'] : uniqid();
        $id = isset($args['id']) ? $args['id'] : uniqid();
        $datatable = isset($args['datatable']) ? $args['datatable'] : TRUE;
        $excel = isset($args['excel']) ? $args['excel'] : FALSE;
        $limit = isset($args['limit']) ? $args['limit'] : 500;

        $table = function($d) use ($class, $id, $datatable) {
            $markup = "<table class='{$class}' id='{$id}'>".PHP_EOL.$d."</table>".PHP_EOL;
            if ($datatable) $markup .= "<script>
$(document).ready(function() {
    $('#{$id}').DataTable();
});
</script>";
            return $markup;
        };

        $tr_head = function($d) { return "<thead>".PHP_EOL.$d.PHP_EOL."</thead>".PHP_EOL; };
        $tr_body = function($d) { return "<tbody>".PHP_EOL.$d.PHP_EOL."</tbody>".PHP_EOL; };
        $tr_foot = function($d) { return "<tfoot>".PHP_EOL.$d.PHP_EOL."</tfoot>".PHP_EOL; };

        $th = function($d) { return "<th>".$d."</th>"; };
        $tr = function($d) { return "<tr>".$d."</tr>"; };
        $td = function($d) { return "<td>".$d."</td>"; };

        $column_data = implode(array_map($th, $this->dataframe['columns']));
        $html_head = $tr_head($tr($column_data));
        $html_foot = $excel ? "" : $tr_foot($tr($column_data));

        $html_body = '';
        $data = $limit > 0 ? array_slice($this->dataframe['data'], 0, $limit) : $this->dataframe['data'];
        foreach($data as $row) $html_body .= $tr(implode(array_map($td, $row)));
        $html_body = $tr_body($html_body);

        return $table($html_head . $html_foot . $html_body);
    }

    /**
     * Pretty-prints DataTable as a clean ASCII table instead of an associative array.
     *
     * @return string
     */
    public function __toString() {
        if ($this->dataframe['columns'] == [] && $this->dataframe['data'] == []) return "Empty DataFrame.\n";

        $rj_str = function($d, $len) { return sprintf("%' {$len}s", $d); };

        /* determine string lengths: if column str length is greater/less than data str length */
        $col_lengths = [];
        foreach($this->dataframe['columns'] as $colname) {
            $col = array_column($this->dataframe['data'], $colname);
            $data_len = array_map('strlen', $col);
            $max_len = max($data_len);
            $col_len = strlen($colname);
            $col_lengths[$colname] = $col_len > $max_len ? $col_len : $max_len;
        }

        /* construct column heading */
        $header = [];
        foreach($this->dataframe['columns'] as $colname) {
            $header[] = $rj_str($colname, $col_lengths[$colname]);
        }

        /* construct data body */
        $body = [];
        foreach($this->dataframe['data'] as $row) {
            $tmp_body = [];
            foreach($row as $colname => $field) {
                $tmp_body[] = $rj_str($field, $col_lengths[$colname]);
            }
            $body[] = implode(' | ', $tmp_body);
        }

        /* convert arrays to strings */
        $header = implode(' | ', $header);
        $divider = str_repeat("-", strlen($header));
        $body = implode("\n", $body);
        $result = implode("\n", [$header, $divider, $body]);

        return $result."\n";
    }
}