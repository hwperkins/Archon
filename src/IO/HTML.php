<?php namespace Archon\IO;

/**
 * Created by PhpStorm.
 * User: hwgehring
 * Date: 10/10/2015
 * Time: 9:16 PM
 */

final class HTML {

    public function __construct(array $data) {
        $this->data = $data;
    }

    public function render(array $options) {

    }

    private function setDefaultOptions(array $options) {

        return $options;
    }
}