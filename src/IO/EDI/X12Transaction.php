<?php namespace Archon\IO\EDI;

class X12Transaction {

    private $st = NULL;
    private $segments = [];
    private $se = NULL;

    public function __construct(X12 $parent) {
        $this->parent = $parent;
    }

    /**
     * @return X12
     */
    public function get_parent() {
        return $this->parent;
    }

    public function set_st($st) {
        $this->st = $st;
        return $this;
    }

    public function get_st() {
        return $this->st;
    }

    public function set_implementation($implementation) {
        $st = $this->get_st();
        $st[1] = $implementation;
        $this->set_st($st);
        return $this;
    }

    public function get_implementation() {
        return $this->get_st()[1];
    }

    public function set_st_control($st_control) {
        $st = $this->get_st();
        $st[2] = $st_control;
        $this->set_st($st);
        return $this;
    }

    public function get_st_control() {
        return $this->get_st()[2];
    }

    public function set_se($se) {
        $this->se = $se;
        return $this;
    }

    public function get_se() {
        return $this->se;
    }

    public function set_se_control($se_control) {
        $se = $this->get_se();
        $se[2] = $se_control;
        $this->set_se($se);
        return $this;
    }

    public function get_se_control() {
        return $this->get_se()[2];
    }

    public function set_segments($segments) {
        $this->segments = $segments;
        return $this;
    }

    public function get_segments() {
        return $this->segments;
    }

    public function add_segment($segment) {
        $this->segments[] = $segment;
        return $this;
    }

}