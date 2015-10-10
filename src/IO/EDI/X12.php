<?php namespace Archon\IO\EDI;

class X12 {

    private $filename = NULL;

    private $segment_sep = NULL;
    private $element_sep = NULL;
    private $subelement_sep = NULL;

    private $isa = NULL;
    private $gs = NULL;
    private $transactions = [];
    private $ge = NULL;
    private $iea = NULL;

    public function set_isa($isa) {
        $this->isa = $isa;
        return $this;
    }

    public function get_isa() {
        return $this->isa;
    }

    public function set_isa_control($isa_control) {
        $isa = $this->get_isa();
        $isa[13] = $isa_control;
        $this->set_isa($isa);
        return $this;
    }

    public function get_isa_control() {
        return $this->get_isa()[13];
    }

    public function set_gs($gs) {
        if($gs[0] != 'GS') {
            throw new EDIException("Error: {$this->filename} does not contain a valid GS segment and may be malformed: {$gs[0]}");
        } else {
            $this->gs = $gs;
            return $this;
        }
    }

    public function get_gs() {
        return $this->gs;
    }

    public function set_gs_control($gs_control) {
        $gs = $this->get_gs();
        $gs[6] = $gs_control;
        $this->set_gs($gs);
        return $this;
    }

    public function get_gs_control() {
        return $this->get_gs()[6];
    }

    public function set_ge($ge) {
        if ($ge[0] != 'GE') {
            throw new EDIException("Error: {$this->filename} does not contain a valid GE segment and may be malformed: {$ge[0]}.");
        } elseif ($this->get_gs_control() != $ge[2]) {
            throw new EDIException("Error: {$this->filename} does not contain a valid GS/GE interchange control. GS: {$this->get_gs_control()}, GE: {$ge[2]}");
        } else {
            $this->ge = $ge;
            return $this;
        }
    }

    public function get_ge() {
        return $this->ge;
    }

    public function set_iea($iea) {
        if ($iea[0] != 'IEA') {
            throw new EDIException("Error: {$this->filename} does not contain a valid IEA segment and may be malformed: {$iea[0]}");
        } elseif ($this->get_isa_control() != $iea[2]) {
            throw new EDIException("Error: {$this->filename} does not contain a valid ISA/IEA interchange control. ISA: {$this->get_isa_control()}, IEA: {$iea[0]}");
        } else {
            $this->iea = $iea;
            return $this;
        }
    }

    public function get_iea() {
        return $this->iea;
    }

    public function set_version($version) {
        $isa = $this->get_isa();
        $isa[12] = $version;
        $this->set_isa($isa);
        return $this;
    }

    public function get_version() {
        return $this->get_isa()[12];
    }

    public function set_segment_sep($segment_sep) {
        $this->segment_sep = $segment_sep;
        return $this;
    }

    public function get_segment_sep() {
        return $this->segment_sep;
    }

    public function set_element_sep($element_sep) {
        $this->element_sep = $element_sep;
        return $this;
    }

    public function get_element_sep() {
        return $this->element_sep;
    }

    public function set_subelement_sep($subelement_sep) {
        $this->subelement_sep = $subelement_sep;
        return $this;
    }

    public function get_subelement_sep() {
        return $this->subelement_sep;
    }

    public function set_transactions(array $transactions) {
        $this->transactions = $transactions;
        return $this;
    }

    /**
     * @return X12Transaction[]
     */
    public function get_transactions() {
        return $this->transactions;
    }

    public function set_filename($filename) {
        $this->filename = $filename;
        return $this;
    }

    public function get_filename() {
        return $this->filename;
    }

    public function add_transaction(X12Transaction $transaction) {
        if ($transaction->get_implementation() === NULL) {
            throw new EDIException("Error: {$this->filename} does not contain a valid ST segment. No implementation found.");
        } elseif ($transaction->get_st_control() != $transaction->get_se_control()) {
            throw new EDIException("Error: {$this->filename} does not contain a valid ST/SE interchange control. ST: {$transaction->get_st_control()}, SE: {$transaction->get_se_control()}");
        }

        $this->transactions[] = $transaction;
        return $this;
    }

    private static function parse_envelope($filename, $contents) {
        if (substr($contents, 0, 3) != 'ISA') {
            throw new EDIException("Error: {$filename} does not contain a valid ISA segment.");
        } else {
            $isa_segment = substr($contents, 0, 106);
        }

        // Parse fixed separators from ISA segment
        $segment_sep = substr($isa_segment, -1);
        $element_sep = substr($isa_segment, -3, 1);
        $subelement_sep = substr($isa_segment, -2, 1);

        // Split X12 into segments
        $segments = array_filter(explode($segment_sep, $contents));
        unset($contents);

        // Populate X12 object
        $edi = new X12();
        $edi->set_filename(basename($filename))
            ->set_isa(explode($element_sep, array_shift($segments)))
            ->set_gs(explode($element_sep, array_shift($segments)))
            ->set_iea(explode($element_sep, array_pop($segments)))
            ->set_ge(explode($element_sep, array_pop($segments)))
            ->set_segment_sep($segment_sep)
            ->set_element_sep($element_sep)
            ->set_subelement_sep($subelement_sep);

        // Populate transactions
        $txn = new X12Transaction($edi);
        foreach($segments as &$segment) {
            $segment = explode($element_sep, $segment);

            if($segment[0] == 'ST') {
                $txn->set_st($segment);
            } elseif($segment[0] == 'SE') {
                $txn->set_se($segment);
                // Transaction closed, add it to the aggregate and create new one
                $edi->add_transaction($txn);
                $txn = new X12Transaction($edi);
            } else {
                $txn->add_segment($segment);
            }
        }

        return $edi;
    }

    public static function from_file($filename) {
        $contents = trim(file_get_contents($filename));
        $contents = str_replace(["\r", "\n", "\r\n"], "", $contents);
        $edi = X12::parse_envelope($filename, $contents);
        unset($contents);
        return $edi;
    }

}