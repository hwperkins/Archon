# Archon: PHP Data Analysis Library

[![Build Status](https://img.shields.io/travis/HWGehring/Archon.svg?style=flat-square)](https://travis-ci.org/HWGehring/Archon)
[![Coverage Status](https://img.shields.io/coveralls/HWGehring/Archon.svg?style=flat-square)](https://coveralls.io/github/HWGehring/Archon?branch=master)
[![Latest Stable Version](https://img.shields.io/packagist/v/archon/dataframe.svg?style=flat-square)](https://packagist.org/packages/archon/dataframe)
[![License](https://img.shields.io/packagist/l/archon/dataframe.svg?style=flat-square)](https://packagist.org/packages/archon/dataframe)

Archon is a PHP library which is designed to make working tabular/relational data, files, and databases easy.

## Installation

### Using Composer:

```sh
composer require archon/dataframe
```

```json
{
    "require": {
        "archon/dataframe": "0.2.0"
    }
}
```

## Examples

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

with support for [DataTables.js](http://datatables.net/):

```php
$dataTable = $df->toHTML(['datatable' => '{ "optionKey": "optionValue" }']);
```

### Extracting the raw array:

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

### Basic operations:

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
$df = $df->apply(function ($row) {
    $row['a'] = $row['c'] + 1;
    return $row;
});
```

Applying functions to columns directly:
```php
$df['a'] = function ($el) {
    return $el + 3;
};
```
```php
$df['a'] = $df['c']->apply(function ($el) {
    return $el + 1;
});
```

```php
--------------------------
Array
(
    [0] => Array
        (
            [a] => 4
            [b] => 2
            [c] => 3
        )

    [1] => Array
        (
            [a] => 7
            [b] => 5
            [c] => 6
        )

    [2] => Array
        (
            [a] => 10
            [b] => 8
            [c] => 9
        )

)
```
Extracting columns:
```php
$a = $df['a'];
print_r($a->toArray());
-----------------------
Array
(
    [0] => Array
        (
            [a] => 1
        )

    [1] => Array
        (
            [a] => 4
        )

    [2] => Array
        (
            [a] => 7
        )

)
```
