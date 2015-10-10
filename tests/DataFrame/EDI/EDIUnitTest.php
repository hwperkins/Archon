<?php namespace DataFrame\EDI;

use Archon\IO\EDI\X12;
use Archon\IO\EDI\X12835\ClaimPayment;

class EDIUnitTest extends \PHPUnit_Framework_TestCase {

    public function test_835_spec() {
        $file = __DIR__.DIRECTORY_SEPARATOR.'835'.DIRECTORY_SEPARATOR.'test_1.835';
        $edi = X12::from_file($file);
        $transactions = $edi->get_transactions();
        $transaction = $transactions[0];
        $claim_payment = new ClaimPayment($transaction);
        var_dump($claim_payment);
    }

    public function test_270_spec() {
        $file = __DIR__.DIRECTORY_SEPARATOR.'270'.DIRECTORY_SEPARATOR.'test_1.270';

        $edi = X12::from_file($file);

        // X12 VERSION
        $this->assertEquals('00400', $edi->get_version());
        $edi->set_version('12345');
        $this->assertEquals('12345', $edi->get_version());

        // ISA CONTROL
        $this->assertEquals('000006768', $edi->get_isa_control());
        $edi->set_isa_control('123456789');
        $this->assertEquals('123456789', $edi->get_isa_control());

        // SEGMENT SEPARATOR
        $this->assertEquals('~', $edi->get_segment_sep());
        $edi->set_segment_sep('#');
        $this->assertEquals('#', $edi->get_segment_sep());

        // ELEMENT SEPARATOR
        $this->assertEquals('*', $edi->get_element_sep());
        $edi->set_element_sep('|');
        $this->assertEquals('|', $edi->get_element_sep());

        // SUBELEMENT SEPARATOR
        $this->assertEquals('>', $edi->get_subelement_sep());
        $edi->set_subelement_sep('^');
        $this->assertEquals('^', $edi->get_subelement_sep());

        // ASSERT ISA UPDATES ACCORDINGLY
        $new_isa = "ISA|00|          |00|          |12|ABCCOM         |01|999999999      |120117|1719|U|12345|123456789|0|P|>";
        $this->assertEquals(implode($edi->get_element_sep(), $edi->get_isa()), $new_isa);



        $transactions = $edi->get_transactions();

        $transaction = $transactions[0];

        // MUTATE THE ST TRANSACTION
        $this->assertEquals('270', $transaction->get_implementation());
        $transaction->set_implementation('123');
        $this->assertEquals('123', $transaction->get_implementation());

        $this->assertEquals('1234', $transaction->get_st_control());
        $transaction->set_st_control('4321');
        $this->assertEquals('4321', $transaction->get_st_control());

        // ASSERT THE ST UPDATES ACCORDINGLY
        $this->assertEquals(implode($edi->get_element_sep(), $transaction->get_st()), "ST|123|4321");


        $this->assertEquals([
            ['BHT', '0022', '13', '1', '20010820', '1330'],
            ['HL', '1', '', '20', '1'],
            ['NM1', 'PR', '2', '', '', '', '', '', 'PI', '123456789'],
            ['HL', '2', '1', '21', '1'],
            ['NM1', '1P', '2', '', '', '', '', '', 'SV', '987654321'],
            ['HL', '3', '2', '22', '0'],
            ['NM1', 'IL', '1', 'DOE', 'JANE', '', '', '', 'MI', '345678901'],
            ['EQ', '30', '', 'FAM']
        ], $transaction->get_segments());



        $transaction = $transactions[1];
        $this->assertEquals('270', $transaction->get_implementation());
        $this->assertEquals('1234', $transaction->get_st_control());
        $this->assertEquals([
            ['BHT', '0022', '13', '1', '20010820', '1330'],
            ['HL', '1', '', '20', '1'],
            ['NM1', 'PR', '2', '', '', '', '', '', 'PI', '123456789'],
            ['HL', '2', '1', '21', '1'],
            ['NM1', '1P', '2', '', '', '', '', '', 'SV', '987654321'],
            ['HL', '3', '2', '22', '0'],
            ['NM1', 'IL', '1', 'DOE', 'JANE', '', '', '', 'MI', '345678901'],
            ['EQ', '30', '', 'FAM']
        ], $transaction->get_segments());

    }

}
