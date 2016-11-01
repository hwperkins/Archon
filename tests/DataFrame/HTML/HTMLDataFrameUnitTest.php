<?php namespace Archon\Tests\DataFrame\HTML;

use Archon\DataFrame;

class HTMLDataFrameUnitTest extends \PHPUnit_Framework_TestCase
{

    public function testToHTML()
    {
        $df = DataFrame::fromArray([
            ['a' => 1, 'b' => 2, 'c' => 3],
            ['a' => 4, 'b' => 5, 'c' => 6],
            ['a' => 7, 'b' => 8, 'c' => 9],
        ]);

        $expected = "<table>";
        $expected .= "<thead><tr><th>a</th><th>b</th><th>c</th></tr></thead>";
        $expected .= "<tfoot><tr><th>a</th><th>b</th><th>c</th></tr></tfoot>";
        $expected .= "<tbody>";
        $expected .= "<tr><td>1</td><td>2</td><td>3</td></tr>";
        $expected .= "<tr><td>4</td><td>5</td><td>6</td></tr>";
        $expected .= "<tr><td>7</td><td>8</td><td>9</td></tr>";
        $expected .= "</tbody>";
        $expected .= "</table>";

        $this->assertEquals($expected, $df->toHTML());
    }

    public function testPrettyToHTML()
    {
        $df = DataFrame::fromArray([
            ['a' => 1, 'b' => 2, 'c' => 3],
            ['a' => 4, 'b' => 5, 'c' => 6],
            ['a' => 7, 'b' => 8, 'c' => 9],
        ]);

        $expected = "<table>\n";
        $expected .= "    <thead>\n";
        $expected .= "        <tr>\n";
        $expected .= "            <th>a</th>\n";
        $expected .= "            <th>b</th>\n";
        $expected .= "            <th>c</th>\n";
        $expected .= "        </tr>\n";
        $expected .= "    </thead>\n";
        $expected .= "    <tfoot>\n";
        $expected .= "        <tr>\n";
        $expected .= "            <th>a</th>\n";
        $expected .= "            <th>b</th>\n";
        $expected .= "            <th>c</th>\n";
        $expected .= "        </tr>\n";
        $expected .= "    </tfoot>\n";
        $expected .= "    <tbody>\n";
        $expected .= "        <tr>\n";
        $expected .= "            <td>1</td>\n";
        $expected .= "            <td>2</td>\n";
        $expected .= "            <td>3</td>\n";
        $expected .= "        </tr>\n";
        $expected .= "        <tr>\n";
        $expected .= "            <td>4</td>\n";
        $expected .= "            <td>5</td>\n";
        $expected .= "            <td>6</td>\n";
        $expected .= "        </tr>\n";
        $expected .= "        <tr>\n";
        $expected .= "            <td>7</td>\n";
        $expected .= "            <td>8</td>\n";
        $expected .= "            <td>9</td>\n";
        $expected .= "        </tr>\n";
        $expected .= "    </tbody>\n";
        $expected .= "</table>";

        $this->assertEquals($expected, $df->toHTML(['pretty' => true]));
    }

    public function testClassIDOptions()
    {
        $df = DataFrame::fromArray([
            ['a' => 1, 'b' => 2, 'c' => 3],
            ['a' => 4, 'b' => 5, 'c' => 6],
            ['a' => 7, 'b' => 8, 'c' => 9],
        ]);

        $fnExpected = function ($tableString) {
            return $tableString."<thead><tr><th>a</th><th>b</th><th>c</th></tr></thead>"
                ."<tfoot><tr><th>a</th><th>b</th><th>c</th></tr></tfoot>"
                ."<tbody>"
                ."<tr><td>1</td><td>2</td><td>3</td></tr>"
                ."<tr><td>4</td><td>5</td><td>6</td></tr>"
                ."<tr><td>7</td><td>8</td><td>9</td></tr>"
                ."</tbody>"
                ."</table>";
        };

        $expected = $fnExpected("<table class='classname'>");
        $this->assertEquals($expected, $df->toHTML([
            'class' => 'classname'
        ]));

        $expected = $fnExpected("<table id='idname'>");
        $this->assertEquals($expected, $df->toHTML([
            'id' => 'idname'
        ]));

        $expected = $fnExpected("<table class='classname' id='idname'>");
        $this->assertEquals($expected, $df->toHTML([
            'class' => 'classname',
            'id' => 'idname'
        ]));

        $expected = $fnExpected('<table class="classname" id="idname">');
        $this->assertEquals($expected, $df->toHTML([
            'class' => 'classname',
            'id' => 'idname',
            'quote' => '"'
        ]));

    }

    public function testDataTable()
    {
        $df = DataFrame::fromArray([['a' => 1]]);

        $actual = $df->toHTML(['datatable' => true]);

        // Regex for the CSS ID because it's a UUID
        preg_match_all('/#\w*/', $actual, $matches);
        $matches = current($matches);
        $this->assertTrue(isset($matches[0]));

        $uuid = substr($matches[0], 1);
        $expected = "<table id='".$uuid."'>";
        $expected .= "<thead><tr><th>a</th></tr></thead>";
        $expected .= "<tfoot><tr><th>a</th></tr></tfoot>";
        $expected .= "<tbody>";
        $expected .= "<tr><td>1</td></tr>";
        $expected .= "</tbody>";
        $expected .= "</table>";

        // Defining this wrapper function because PHPStorm goes apeshit trying to interpret the generated JavaScript.
        $wrap = function ($openTag, $closingTag) {
            return function ($data) use ($openTag, $closingTag) {
                return $openTag . $data . $closingTag;
            };
        };

        $scriptTag = $wrap('<script>', '</script>');
        $expected .= $scriptTag('$(document).ready(function() {$(\''.$matches[0].'\').DataTable();});');

        $this->assertEquals($expected, $actual);
    }

    public function testDataTableOptions()
    {
        $df = DataFrame::fromArray([['a' => 1]]);

        $actual = $df->toHTML([
            'id' => 'myid',
            'datatable' => '{ "key": value }',
        ]);

        $expected = "<table id='myid'>";
        $expected .= "<thead><tr><th>a</th></tr></thead>";
        $expected .= "<tfoot><tr><th>a</th></tr></tfoot>";
        $expected .= "<tbody>";
        $expected .= "<tr><td>1</td></tr>";
        $expected .= "</tbody>";
        $expected .= "</table>";

        // Defining this wrapper function because PHPStorm goes apeshit trying to interpret the generated JavaScript.
        $wrap = function ($openTag, $closingTag) {
            return function ($data) use ($openTag, $closingTag) {
                return $openTag . $data . $closingTag;
            };
        };

        $scriptTag = $wrap('<script>', '</script>');
        $expected .= $scriptTag('$(document).ready(function() {$(\'#myid\').DataTable({ "key": value });});');

        $this->assertEquals($expected, $actual);
    }

}
