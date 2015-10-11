<?php namespace Archon\Tests\DataFrame\HTML;

use Archon\DataFrame;

class HTMLDataFrameUnitTest extends \PHPUnit_Framework_TestCase {

    public function testToHTML() {
        $df = DataFrame::fromArray([
            ['a' => 1, 'b' => 2, 'c' => 3],
            ['a' => 4, 'b' => 5, 'c' => 6],
            ['a' => 7, 'b' => 8, 'c' => 9],
        ]);

        $expected = "<table>";

        $expected .= "<thead>";
        $expected .= "<tr><th>a</th><th>b</th><th>c</th></tr>";
        $expected .= "</thead>";

        $expected .= "<tfoot>";
        $expected .= "<tr><th>a</th><th>b</th><th>c</th></tr>";
        $expected .= "</tfoot>";

        $expected .= "<tbody>";
        $expected .= "<tr><th>1</th><th>2</th><th>3</th></tr>";
        $expected .= "<tr><th>4</th><th>5</th><th>6</th></tr>";
        $expected .= "<tr><th>7</th><th>8</th><th>9</th></tr>";
        $expected .= "</tbody>";

        $expected .= "</table>";

        $this->assertEquals($expected, $df->toHTML());
    }

}
