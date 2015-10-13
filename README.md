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
        "archon/dataframe": "0.4.0"
    }
}
```

### Requirements
 - PHP 5.5 or higher
 - php_pdo_sqlite extension
 - php_mbstring extension
 
### Dependencies
 - [PHPOffice/PHPExcel](https://github.com/PHPOffice/PHPExcel): 1.8.1
 - [gajus/dindent](https://github.com/gajus/dindent): 2.0.2

### License
 - [BSD-3-Clause](http://opensource.org/licenses/BSD-3-Clause)

## Data Format Examples

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
sun moon stars
--------------
1    2     3
4    5     6
7    8     9
```

```php
$df = DataFrame::fromFWF($fileName, [
	'a' => [0, 1],
    'b' => [5, 6],
    'c' => [11, 12]
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
$df = DataFrame::fromSQL($pdo, 'SELECT foo, bar, baz FROM table_name;');
```

### Committing to a database:

```php
$pdo = new PDO('sqlite::memory:');
$affected = $df->toSQL($pdo, 'table_name');
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

### Extracting as a two-dimensional array:

```php
$myArray = $df->toArray();
print_r($myArray);
```

Outputs:
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

Applying values columns via functional proxy of other columns:
```php
$df['a'] = $df['c']->apply(function ($el, $key) {
    return $el + 1;
});
```
