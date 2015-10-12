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
        $expected .= "<tr><th>1</th><th>2</th><th>3</th></tr>";
        $expected .= "<tr><th>4</th><th>5</th><th>6</th></tr>";
        $expected .= "<tr><th>7</th><th>8</th><th>9</th></tr>";
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
        $expected .= "            <th>1</th>\n";
        $expected .= "            <th>2</th>\n";
        $expected .= "            <th>3</th>\n";
        $expected .= "        </tr>\n";
        $expected .= "        <tr>\n";
        $expected .= "            <th>4</th>\n";
        $expected .= "            <th>5</th>\n";
        $expected .= "            <th>6</th>\n";
        $expected .= "        </tr>\n";
        $expected .= "        <tr>\n";
        $expected .= "            <th>7</th>\n";
        $expected .= "            <th>8</th>\n";
        $expected .= "            <th>9</th>\n";
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
                ."<tr><th>1</th><th>2</th><th>3</th></tr>"
                ."<tr><th>4</th><th>5</th><th>6</th></tr>"
                ."<tr><th>7</th><th>8</th><th>9</th></tr>"
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
}
