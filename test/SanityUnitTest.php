<?php namespace Archon\Test;

/**
 * Created by PhpStorm.
 * User: hwgehring
 * Date: 8/31/2015
 * Time: 10:04 AM
 */
class SanityUnitTest extends \PHPUnit_Framework_TestCase {

    <?php namespace photos;

    final class UniqueChars {

        public function __construct($quiz_string) {
            $this->quiz_string = $quiz_string;
        }

        public function occurrences() {
            $quiz_string = str_split(strtoupper($this->quiz_string));

            $char_set = [];
            foreach($quiz_string as $char) {
                $result[$char] = isset($result[$char]) ? $result[$char] += 1 : 1;
            }

            $result = [];
            foreach($char_set as $char => $i) {
                $result[] = "Letter {$char} is found {$i} times.";
            }

            return $result;
        }
    }
    public function test_sanity() {

    }

}
