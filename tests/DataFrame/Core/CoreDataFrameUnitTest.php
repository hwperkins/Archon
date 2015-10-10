<?php namespace Archon\Tests\DataFrame\Core;

use Archon\DataFrame;

class CoreDataFrameUnitTest extends \PHPUnit_Framework_TestCase {

    public function testFromArray() {
        $df = DataFrame::fromArray(['a', 'b', 'c']);
        $this->assertEquals(['a', 'b', 'c'], $df->toArray());
    }

}
