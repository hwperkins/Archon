<?php namespace DataFrame\Core;

use Archon\DataFrame;
use Exception;

class CoreDataFrameUnitTest extends \PHPUnit_Framework_TestCase {

    public function test_null_from_array() {
        try {
            new DataFrame(NULL);
        } catch (Exception $expected) {
            return;
        }

        $this->fail("Exception was not thrown for from_array(NULL)");
    }

    public function test_empty_from_array() {
        try {
            new DataFrame([]);
        } catch (Exception $expected) {
            return;
        }

        $this->fail("Exception was not thrown for from_array([])");
    }

    public function test_uneven_from_array() {
        try {
            new DataFrame([
                [0],
                [0,1]
            ]);
        } catch (Exception $expected) {
            return;
        }

        $this->fail("Exception was not thrown for from_array with uneven rows");
    }

}
