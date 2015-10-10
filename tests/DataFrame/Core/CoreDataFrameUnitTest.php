<?php namespace Archon\Tests\DataFrame\Core;

use Archon\DataFrame;

class CoreDataFrameUnitTest extends \PHPUnit_Framework_TestCase {

    public function test_from_array() {
        $df = DataFrame::from_array(['a', 'b', 'c']);
    }

}
