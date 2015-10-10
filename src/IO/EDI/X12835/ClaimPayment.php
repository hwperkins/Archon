<?php namespace Archon\IO\EDI\X12835;

use Archon\IO\EDI\X12Transaction;

/**
 * Class ClaimPayment
 * @package MSHA\EDI\X12835
 *
 * Uses Loop Segments:
 *  1000: Transaction-level
 *      DTM:    Production Date
 *      BPR:    Financial Information
 *      TRN:    Reassociation Trace Number
 *      CUR:    Foreign Currency Information
 *      REF:    Receiver Identification
 *
 *  1000A: Payer Identification
 *      N1_PR:     Payer Identification
 *      N3:     Payer Address
 *      N4:     Payer City, State, ZIP
 *      REF:    Additional Payer Identification
 *      PER:    Payer Contact Information
 *
 *  1000B: Payee Identification
 *      N1_PE:     Payee Identification
 *      N3:     Payee Address
 *      N4:     Payee City, State, ZIP
 *      REF:    Payee Additional Identification
 *      RDM:    Remittance Delivery Method
 *
 *  2000: Header Number
 *      TS3:    Provider Summary Information
 *      TS2:    Provider Supplemental Summary Information
 */
class ClaimPayment {

    private $segments = [];

    public function __construct(X12Transaction $transaction) {
        $parent = $transaction->get_parent();
        $this->filename = $parent->get_filename();

        $this->set_segment('isa', $parent->get_isa());

        $segments = $transaction->get_segments();
        foreach($segments as $i => $segment) {
            $this->parse_segment($segments, $i, $segment);
        }
    }

    private function parse_segment(array $segments, $index, array $segment) {
        $segment_name = strtolower($segment[0]);
        switch($segment_name) {
            case 'bpr': // Financial information
            case 'trn': // Reassociation trace number
            case 'cur': // Foreign Currency
                $this->set_segment($segment_name, $segment);
                return;
        }

        $segment_name = strtolower($segment[0]).'_'.strtolower($segment[1]);;
        switch($segment_name) {
            case 'ref_ev':
            case 'ref_f2':
            case 'ref_2u':
            case 'dtm_405':
                $this->set_segment($segment_name, $segment);
                return;
            case 'n1_pr': // Payer Identification
                $this->set_segment('n1_pr', $segment); // Payer Identification
                $this->set_segment('n3_pr', $segments[$index + 1]); // Payer Address
                $this->set_segment('n4_pr', $segments[$index + 2]); // Payer City, State, ZIP
                return;
            case 'n1_pe': // Payee Identification
                $this->set_segment('n1_pe', $segment); // Payee Identification
                $this->set_segment('n3_pe', $segments[$index + 1]); // Payee Address
                $this->set_segment('n4_pe', $segments[$index + 2]); // Payee City, State, ZIP
                return;
        }

    }

    private function set_segment($name, array $segment) {
            $this->$$name = $segment;
    }

    private function get_segment($segment, $index = NULL) {
        if ($index === NULL AND isset($this->segments[$segment])) {
            return $this->segments[$segment];
        }
        
        if (isset($this->segments[$segment]) AND isset($this->segments[$segment][$index])) {
            return $this->segments[$segment][$index];
        }
        
        return NULL;
    }

    public function get_id() {
        /*` BIGINT(20) NOT NULL AUTO_INCREMENT PRIMARY KEY COMMENT 'FOREIGN KEY',*/
    }

    /** Loop 1000: ISA 06 */
    public function get_trading_partner_id() {
        return $this->get_segment('isa', 6);
    }

    public function get_edi_file_name() {
        return $this->filename;
    }

    public function get_file_date() {
        // TODO: IMPLEMENT
    }

    public function get_file_time() {
        // TODO: IMPLEMENT
    }

    public function get_load_date() {
        // TODO: IMPLEMENT
    }

    public function get_load_time() {
        // TODO: IMPLEMENT
    }

    /** Loop 1000: BPR 01 */
    public function get_transaction_handling_code() {
        return $this->get_segment('bpr', 1);
    }

    /** Loop 1000: BPR 02 */
    public function get_payment_amount() {
        return $this->get_segment('bpr', 2);
    }

    /** Loop 1000: BPR 03 */
    public function get_debit_credit() {
        return $this->get_segment('bpr', 3);
    }

    /** Loop 1000: BPR 04 */
    public function get_payment_method() {
        return $this->get_segment('bpr', 4);
    }

    /** Loop 1000: BPR 05 */
    public function get_payment_format() {
        return $this->get_segment('bpr', 5);
    }

    /** Loop 1000: BPR 06 */
    public function get_sending_bank_no_qual() {
        return $this->get_segment('bpr', 6);
    }

    /** Loop 1000: BPR 07 */
    public function get_sending_bank_no() {
        return $this->get_segment('bpr', 7);
    }

    /** Loop 1000: BPR 09 */
    public function get_sender_account_no() {
        return $this->get_segment('bpr', 9);
    }

    /** Loop 1000: BPR 10 */
    public function get_payer_id() {
        return $this->get_segment('bpr', 10);
    }

    /** Loop 1000: BPR 11 */
    public function get_payer_division() {
        return $this->get_segment('bpr', 11);
    }

    /** Loop 1000: BPR 12 */
    public function get_receiving_bank_no_qual() {
        return $this->get_segment('bpr', 12);
    }

    /** Loop 1000: BPR 13 */
    public function get_receiving_bank_no() {
        return $this->get_segment('bpr', 13);
    }

    /** Loop 1000: BPR 14 */
    public function get_receiver_account_no_qual() {
        return $this->get_segment('bpr', 14);
    }

    /** Loop 1000: BPR 15 */
    public function get_receiver_account_no() {
        return $this->get_segment('bpr', 15);
    }

    /** Loop 1000: BPR 16 */
    public function get_effective_date() {
        return $this->get_segment('bpr', 16);
    }

  /* TRANSACTION SET DATA */

    /** Loop 1000: TRN 02 */
    public function get_check_number() {
        return $this->get_segment('trn', 2);
    }

    /** Loop 1000: TRN 03 */
    public function get_trn_payer_id() {
        return $this->get_segment('trn', 3);
    }

    /** Loop 1000 */
    public function get_supplemental_code() {
        // TODO: IMPLEMENT 835
    }

    /** Loop 1000: CUR 02 */
    public function get_currency_code() {
        return $this->get_segment('cur', 2);
    }

    /** Loop 1000: REF 02 - EV */
    public function get_receiver_id() {
        return $this->get_segment('ref_ev', 2);
    }

    /** Loop 1000: REF 02 - F2 */
    public function get_local_version_id() {
        return $this->get_segment('ref_f2', 2);
    }

    /** Loop 1000: DTM 02 - 405 */
    public function get_production_date() {
        return $this->get_segment('dtm_405', 2);
    }

  /* PAYER INFORMATION */

    /** Loop 1000A: N1 02 - PR */
    public function get_payer_name() {
        return $this->get_segment('n1_pr', 2);
    }

    /** Loop 1000A: N1 04 */
    public function get_plan_id() {
        return $this->get_segment('n1_pr', 4);
    }

    /** Loop 1000A: N3 01 */
    public function get_payer_address1() {
        return $this->get_segment('n3_pr', 1);
    }

    /** Loop 1000A: N3 04 */
    public function get_payer_address2() {
        return $this->get_segment('n3_pr', 4);
    }

    /** Loop 1000A: N4 01 */
    public function get_payer_city() {
        return $this->get_segment('n4_pr', 1);
    }

    /** Loop 1000A: N4 02 */
    public function get_payer_state() {
        return $this->get_segment('n4_pr', 2);
    }

    /** Loop 1000A: N4 03 */
    public function get_payer_zip() {
        return $this->get_segment('n4_pr', 3);
    }

    /** Loop 1000A: REF 02 - 2U */
    public function get_payer_id_number() {
        return $this->get_segment(ref_2u, 2);
    }

    /** Loop 1000A: REF 02 - EO */
    public function get_submitter_id() {
        return $this->get_segment(ref_eo, 2);
    }

    /** Loop 1000A: REF 02 - HI */
    public function get_hin() {
        return $this->get_segment(ref_hi, 2);
    }

    /** Loop 1000A: REF 02 - NF */
    public function get_naic() {
        return $this->get_segment(ref_nf, 2);
    }

    /** Loop 1000A: PER 02 - 366 */
    public function get_payer_contact() {
        return $this->get_segment(per_366, 2);
    }

    /** Loop 1000A: PER 04 - TE */
    public function get_payer_telephone() {

    }

    /** Loop 1000A: PER 06 - FX */
    public function get_payer_fax() {

    }

    /** Loop 1000A: PER 08 - EM */
    public function get_payer_email() {

    }

    /** Loop 1000A: PER 02 - BL */
    public function get_payer_technical_contact() {

    }

    /** Loop 1000A: PER 04 - TE */
    public function get_payer_technical_telephone() {

    }

    /** Loop 1000A: PER 06 - FX */
    public function get_payer_technical_website() {

    }

    /** Loop 1000A: PER 08 - EM */
    public function get_payer_technical_email() {

    }

    /** Loop 1000A: PER 08 - UR */
    public function get_payer_website() {

    }

  /* PAYEE INFORMATION */

    /** Loop 1000B: N1 02 - PE */
    public function get_payee_name() {

    }

    /** Loop 1000B: N1 03 */
    public function get_payee_id_qual() {

    }

    /** Loop 1000B: N1 04 */
    public function get_payee_id() {

    }

    /** Loop 1000B: N3 01 */
    public function get_payee_address1() {

    }

    /** Loop 1000B: N3 04 */
    public function get_payee_address2() {

    }

    /** Loop 1000B: N4 01 */
    public function get_payee_city() {

    }

    /** Loop 1000B: N4 02 */
    public function get_payee_state() {

    }

    /** Loop 1000B: N4 03 */
    public function get_payee_zip() {

    }

    /** Loop 1000B: REF 01 */
    public function get_payee_other_id_1_qual() {

    }

    /** Loop 1000B: REF 02 */
    public function get_payee_other_id_1() {

    }

    /** Loop 1000B: REF 01 */
    public function get_payee_other_id_2_qual() {

    }

    /** Loop 1000B: REF 02 */
    public function get_payee_other_id_2() {

    }

    /** Loop 1000B: REF 01 */
    public function get_payee_other_id_3_qual() {

    }

    /** Loop 1000B: REF 02 */
    public function get_payee_other_id_3() {

    }

    /** Loop 1000B: RDM 01 */
    public function get_remittance_delivery_code() {

    }

    /** Loop 1000B: RDM 02 */
    public function get_remittance_delivery_contact() {

    }

    /** Loop 1000B: RDM 03 */
    public function get_remittance_delivery_comm_number() {

    }

  /* SUMMARY INFORMATION */

    /** Loop 2000: TS3 01 */
    public function get_provider_id() {

    }

    /** Loop 2000: TS3 02 */
    public function get_facility_type_code() {

    }

    /** Loop 2000: TS3 04 */
    public function get_total_claim_count() {

    }

    /** Loop 2000: TS3 03 */
    public function get_fiscal_period_end_date() {

    }

    /** Loop 2000: TS3 05 */
    public function get_total_claim_charge_amount() {

    }

    /** Loop 2000: TS3 06 */
    public function get_total_covered_charge_amount() {

    }

    /** Loop 2000: TS3 07 */
    public function get_total_noncovered_charge_amount() {

    }

    /** Loop 2000: TS3 08 */
    public function get_total_denied_charge_amount() {

    }

    /** Loop 2000: TS3 09 */
    public function get_total_provider_payment_amount() {

    }

    /** Loop 2000: TS3 10 */
    public function get_total_interest_amount() {

    }

    /** Loop 2000: TS3 11 */
    public function get_total_contractual_adjustment_amount() {

    }

    /** Loop 2000: TS3 12 */
    public function get_total_gramm_rudman_reduction_amount() {

    }

    /** Loop 2000: TS3 13 */
    public function get_total_msp_payer_amount() {

    }

    /** Loop 2000: TS3 14 */
    public function get_total_blood_deductible_amount() {

    }

    /** Loop 2000: TS3 15 */
    public function get_total_non_lab_charge_amount() {

    }

    /** Loop 2000: TS3 16 */
    public function get_total_coinsurance_amount() {

    }

    /** Loop 2000: TS3 17 */
    public function get_total_hcpcs_reported_charge_amount() {

    }

    /** Loop 2000: TS3 18 */
    public function get_total_hcpcs_payable_amount() {

    }

    /** Loop 2000: TS3 19 */
    public function get_total_deductible_amount() {

    }

    /** Loop 2000: TS3 20 */
    public function get_total_professional_component_amount() {

    }

    /** Loop 2000: TS3 21 */
    public function get_total_msp_patient_liability_met_amount() {

    }

    /** Loop 2000: TS3 22 */
    public function get_total_patient_reimbursement_amount() {

    }

    /** Loop 2000: TS3 23 */
    public function get_total_pip_claim_count() {

    }

    /** Loop 2000: TS3 24 */
    public function get_total_pip_adjustment_amount() {

    }

  /* SUPPLEMENTAL SUMMARY INFORMATION */

    /** Loop 2000: TS2 01 */
    public function get_total_drg_amount() {

    }

    /** Loop 2000: TS2 02 */
    public function get_total_federal_specific_amount() {

    }

    /** Loop 2000: TS2 03 */
    public function get_total_hospital_specific_amount() {

    }

    /** Loop 2000: TS2 04 */
    public function get_total_disproportionate_share_amount() {

    }

    /** Loop 2000: TS2 05 */
    public function get_total_capital_amount() {

    }

    /** Loop 2000: TS2 06 */
    public function get_total_indirect_medical_education_amount() {

    }

    /** Loop 2000: TS2 07 */
    public function get_total_outlier_day_count() {

    }

    /** Loop 2000: TS2 08 */
    public function get_total_day_outlier_amount() {

    }

    /** Loop 2000: TS2 09 */
    public function get_total_cost_outlier_amount() {

    }

    /** Loop 2000: TS2 10 */
    public function get_average_drg_length_of_stay() {

    }

    /** Loop 2000: TS2 11 */
    public function get_total_discharge_count() {

    }

    /** Loop 2000: TS2 12 */
    public function get_total_cost_report_day_count() {

    }

    /** Loop 2000: TS2 13 */
    public function get_total_covered_day_count() {

    }

    /** Loop 2000: TS2 14 */
    public function get_total_noncovered_day_count() {

    }

    /** Loop 2000: TS2 15 */
    public function get_total_msp_pass_through_amount() {

    }

    /** Loop 2000: TS2 16 */
    public function get_average_drg_weight() {

    }

    /** Loop 2000: TS2 17 */
    public function get_total_pps_capital_fsp_drg_amount() {

    }

    /** Loop 2000: TS2 18 */
    public function get_total_pps_capital_hsp_drg_amount() {

    }

    /** Loop 2000: TS2 19 */
    public function get_total_pps_dsh_drg_amount() {

    }

}