# Archon: PHP Data Analysis Library

[![Build Status](https://img.shields.io/travis/HWGehring/Archon.svg?style=flat-square)](https://travis-ci.org/HWGehring/Archon)
[![Coverage Status](https://img.shields.io/coveralls/HWGehring/Archon.svg?style=flat-square)](https://coveralls.io/github/HWGehring/Archon?branch=master)
[![Latest Stable Version](https://img.shields.io/packagist/v/archon/dataframe.svg?style=flat-square)](https://packagist.org/packages/archon/dataframe)
[![License](https://img.shields.io/packagist/l/archon/dataframe.svg?style=flat-square)](https://packagist.org/packages/archon/dataframe)

Archon is a PHP library designed to make working with tabular/relational data, files, and databases easy. The core component of the library is the DataFrame class - a tabular data structure which raises the level of abstraction when working with tabular, two-dimensional data. 

## Installation

### Using Composer:

```sh
composer require archon/dataframe
```

```json
{
    "require": {
        "archon/dataframe": "1.1.1"
    }
}
```

### Requirements
 - PHP 7.1 or higher
 - php_pdo_sqlite extension
 - php_mbstring extension
 
### Dependencies
 - [PHPOffice/PHPExcel](https://github.com/PHPOffice/PHPExcel): 1.8.1
 - [gajus/dindent](https://github.com/gajus/dindent): 2.0.2

### License
 - [BSD-3-Clause](http://opensource.org/licenses/BSD-3-Clause)

## Data Format Examples

### Import required library

`use Archon\DataFrame;`

### Instantiating from an array:

```php
$df = DataFrame::fromArray([
    ['a' => 1, 'b' => 2, 'c' => 3],
    ['a' => 4, 'b' => 5, 'c' => 6],
    ['a' => 7, 'b' => 8, 'c' => 9],
]);
```

### Reading a CSV file:

```
x|y|z
1|2|3
4|5|6
7|8|9
```

```php
$df = DataFrame::fromCSV($fileName, [
    'sep' => '|',
    'colmap' => [
	    'x' => 'a',
        'y' => 'b',
        'z' => 'c'
    ]
]);
```

### Writing a CSV file:

```php
$df->toCSV($fileName);
```

```
"a","b","c"
"1","2","3"
"4","5","6"
"7","8","9"
```

### Reading a fixed-width file:

```
foo bar baz
-----------
1   2   3
4   5   6
7   8   9
```

```php
$df = DataFrame::fromFWF($fileName, [
	'a' => [0, 1],
    'b' => [4, 5],
    'c' => [8, 9]
], ['include' => '^[0-9]']);

```

### Reading an XLSX spreadsheet:

```php
$dfA = DataFrame::fromXLSX($fileName, ['sheetname' => 'Sheet A']);
$dfB = DataFrame::fromXLSX($fileName, ['sheetname' => 'Sheet B']);
$dfC = DataFrame::fromXLSX($fileName, ['sheetname' => 'Sheet C']);
```

### Writing an XLSX spreadsheet:

```php
$phpExcel = new PHPExcel();
$dfA->toXLSXWorksheet($phpExcel, 'Sheet A');
$dfB->toXLSXWorksheet($phpExcel, 'Sheet B');
$dfC->toXLSXWorksheet($phpExcel, 'Sheet C');
$writer = new PHPExcel_Writer_Excel2007($phpExcel);
$writer->save($fileName);
```

### Querying from a database:

```php
$pdo = new PDO('sqlite::memory:');
$df = DataFrame::fromSQL('SELECT foo, bar, baz FROM table_name;', $pdo);
```

### Committing to a database:

```php
$pdo = new PDO('sqlite::memory:');
$affected = $df->toSQL('table_name', $pdo);
echo sprintf('%d rows committed to database.', $affected);
```

### Displaying an HTML table:

```php
$html = $df->toHTML(['class' => 'myclass', 'id' => 'myid']);
```

<table>
<thead><tr><th>a</th><th>b</th><th>c</th></tr></thead>
<tfoot><tr><th>a</th><th>b</th><th>c</th></tr></tfoot>
<tbody>
<tr><th>1</th><th>2</th><th>3</th></tr>
<tr><th>4</th><th>5</th><th>6</th></tr>
<tr><th>7</th><th>8</th><th>9</th></tr>
</tbody>
</table>

With support for [DataTables.js](http://datatables.net/):

```php
$dataTable = $df->toHTML(['datatable' => '{ "optionKey": "optionValue" }']);
```

### Converting to JSON:

```php
$json = $df->toJSON();
```

### Creating from JSON:

```php
$df = DataFrame::fromJSON('[
    {"a": 1, "b": 2, "c": 3},
    {"a": 4, "b": 5, "c": 6},
    {"a": 7, "b": 8, "c": 9}
]');
```

### Extracting the underlying two-dimensional array:

```php
$myArray = $df->toArray();
print_r($myArray);
```

```php
Array
(
    [0] => Array
        (
            [a] => 1
            [b] => 2
            [c] => 3
        )

    [1] => Array
        (
            [a] => 4
            [b] => 5
            [c] => 6
        )

    [2] => Array
        (
            [a] => 7
            [b] => 8
            [c] => 9
        )

)
```

## Basic Operations

Getting column names:
```php
$df->columns()
--------------
Array
(
    [0] => a
    [1] => b
    [2] => c
)
```

Adding columns:
```php
$df['key'] = 'value';
```

Removing columns:
```php
unset($df['key']);
```

Counting rows:
```php
count($df);
```

Iterating over rows:
```php
foreach ($df as $i => $row) {
   echo $i.': '.implode('-', $row).PHP_EOL; 
}
--------------------------
0: 1-2-3
1: 4-5-6
2: 7-8-9
```

## Advanced Operations

Applying functions to rows:
```php
$df = $df->apply(function ($row, $index) {
    $row['a'] = $row['c'] + 1;
    return $row;
});
```

Applying functions to columns directly:
```php
$df['a'] = function ($el, $key) {
    return $el + 3;
};
```

Applying values to columns via function application of other columns:
```php
$df['a'] = $df['c']->apply(function ($el, $key) {
    return $el + 1;
});
```

Applying types:
```php
$df = DataFrame::fromArray([
    ['my_date'           => '11/20/16'],
    ['my_other_date'     => '2/12/2016'],
    ['my_decimal'        => '5,000.20'],
    ['my_int'            => '10-'],
    ['my_currency'       => '12345.67']
]);

$df->convertTypes([
    'my_date'       => 'DATE',
    'my_other_date' => 'DATE',
    'my_decimal'    => 'DECIMAL',
    'my_int'        => 'INT',
    'my_currency'   => 'CURRENCY'
], ['m/d/y', 'd/m/Y'], 'Y-m-d');

print_r($df->toArray());
```

```php
Array
(
    [0] => Array
        (
            [my_date] => '2016-11-20'
            [my_other_date] => '2016-12-2'
            [my_decimal] => '5000.20'
            [my_int] => '-10'
            [my_currency] => '$12,345.67'
        )

)
```

Manipulating DataFrame using SQL:
```php
$df = DataFrame::fromArray([
    ['a' => 1, 'b' => 2, 'c' => 3],
    ['a' => 4, 'b' => 5, 'c' => 6],
    ['a' => 7, 'b' => 8, 'c' => 9],
]);

$df = $df->query("

SELECT
  a,
  b
FROM dataframe
WHERE a = '4'
  OR b = '2';

");

print_r($df->toArray());
```

```php
Array
(
    [0] => Array
        (
            [a] => 1
            [b] => 2
        )

    [1] => Array
        (
            [a] => 4
            [b] => 5
        )

)
```

```php
$df = DataFrame::fromArray([
    ['a' => 1, 'b' => 2, 'c' => 3],
    ['a' => 4, 'b' => 5, 'c' => 6],
    ['a' => 7, 'b' => 8, 'c' => 9],
]);

$df = $df->query("

UPDATE dataframe
SET a = c * 2;

");

print_r($df['a']->to_array());
```

```php
Array
(
    [0] => Array
        (
            [a] => 6
        )

    [1] => Array
        (
            [a] => 12
        )

    [2] => Array
        (
            [a] => 18
        )

)
```
