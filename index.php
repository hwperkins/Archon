<?php include 'vendor/autoload.php';

$df = new \Archon\DataFrame(['a', 'b', 'c'], [
    ['a' => 1, 'b' => 2, 'c' => 3]
]);

echo $df->to_html();