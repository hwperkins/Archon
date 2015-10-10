<?php namespace Archon\IO\EDI\X12835;

class PaidClaims {

    // CONSTRAINT `fk_edi_paid_claims_id` FOREIGN KEY (`payment_id`) REFERENCES `{$this->schema}`.`edi_claim_payment` (`id`) ON DELETE CASCADE

    public function get_id() {
        // TODO: IMPLEMENT 835
        /*` BIGINT(20) NOT NULL AUTO_INCREMENT PRIMARY KEY, */
    }

    public function get_payment_id() {
        // TODO: IMPLEMENT 835
        /*` BIGINT(20) NOT NULL COMMENT 'FOREIGN KEY', */
    }

  /* STATISTICS (TS3) */
    public function get_provider_id() {
        // TODO: IMPLEMENT 835
        /*` CHAR(15) DEFAULT NULL, */
    }

    public function get_facility_type() {
        // TODO: IMPLEMENT 835
        /*` CHAR(3) DEFAULT NULL, */
    }

    public function get_fiscal_period_date() {
        // TODO: IMPLEMENT 835
        /*` date DEFAULT NULL, */
    }

    public function get_claim_count() {
        // TODO: IMPLEMENT 835
        /*` INT(6) DEFAULT NULL, */
    }

    public function get_position_in_summary() {
        // TODO: IMPLEMENT 835
        /*` INT(6) DEFAULT NULL, */
    }

    public function get_total_charges() {
        // TODO: IMPLEMENT 835
        /*` CHAR(15) DEFAULT NULL, */
    }

  /* CLAIM DATA */
    public function get_claim_id() {
        // TODO: IMPLEMENT 835
        /*` VARCHAR(20) DEFAULT NULL, */
    }

    public function get_claim_position_in_file() {
        // TODO: IMPLEMENT 835
        /*` INT(6) DEFAULT NULL, */
    }

    public function get_claim_status() {
        // TODO: IMPLEMENT 835
        /*` CHAR(2) DEFAULT NULL, */
    }

    public function get_claim_amount() {
        // TODO: IMPLEMENT 835
        /*` VARCHAR(10) DEFAULT NULL, */
    }

    public function get_claim_paid() {
        // TODO: IMPLEMENT 835
        /*` VARCHAR(10) DEFAULT NULL, */
    }

    public function get_patient_responsibility() {
        // TODO: IMPLEMENT 835
        /*` CHAR(10) DEFAULT NULL, */
    }

    public function get_insurance_type() {
        // TODO: IMPLEMENT 835
        /*` CHAR(2) DEFAULT NULL, */
    }

    public function get_payers_claim_id() {
        // TODO: IMPLEMENT 835
        /*` VARCHAR(50) DEFAULT NULL, */
    }

    public function get_facility_type_code() {
        // TODO: IMPLEMENT 835
        /*` CHAR(3) DEFAULT NULL, */
    }

    public function get_claim_frequency() {
        // TODO: IMPLEMENT 835
        /*` CHAR(1) DEFAULT NULL, */
    }

    public function get_drg_code() {
        // TODO: IMPLEMENT 835
        /*` CHAR(4) DEFAULT NULL, */
    }

    public function get_drg_weight() {
        // TODO: IMPLEMENT 835
        /*` DECIMAL(15,4) DEFAULT NULL, */
    }

    public function get_discharge_fraction() {
        // TODO: IMPLEMENT 835
        /*` DECIMAL(6,4) DEFAULT NULL, */
    }

  /* REF */
    public function get_pre_determination_num() {
        // TODO: IMPLEMENT 835
        /*` VARCHAR(50) DEFAULT NULL, */
    }

    public function get_prior_auth_num() {
        // TODO: IMPLEMENT 835
        /*` VARCHAR(50) DEFAULT NULL, */
    }

    public function get_authorization_num() {
        // TODO: IMPLEMENT 835
        /*` VARCHAR(50) DEFAULT NULL, */
    }

    public function get_repriced_claim_num() {
        // TODO: IMPLEMENT 835
        /*` VARCHAR(50) DEFAULT NULL, */
    }

    public function get_class_of_contract() {
        // TODO: IMPLEMENT 835
        /*` VARCHAR(50) DEFAULT NULL, */
    }

    public function get_medical_record_num() {
        // TODO: IMPLEMENT 835
        /*` VARCHAR(50) DEFAULT NULL, */
    }

    public function get_orig_ref_no() {
        // TODO: IMPLEMENT 835
        /*` VARCHAR(50) DEFAULT NULL, */
    }

    public function get_member_id() {
        // TODO: IMPLEMENT 835
        /*` VARCHAR(20) DEFAULT NULL, */
    }

    public function get_ssn() {
        // TODO: IMPLEMENT 835
        /*` VARCHAR(12) DEFAULT NULL, */
    }

    public function get_policy_number() {
        // TODO: IMPLEMENT 835
        /*` VARCHAR(20) DEFAULT NULL, */
    }

    public function get_group_number() {
        // TODO: IMPLEMENT 835
        /*` VARCHAR(50) DEFAULT NULL, */
    }

    public function get_employer_id() {
        // TODO: IMPLEMENT 835
        /*` VARCHAR(20) DEFAULT NULL, */
    }

    public function get_other_insured_group_no() {
        // TODO: IMPLEMENT 835
        /*` VARCHAR(50) DEFAULT NULL, */
    }

    public function get_adj_repriced_claim_reference_no() {
        // TODO: IMPLEMENT 835
        /*` VARCHAR(20) DEFAULT NULL, */
    }

  /* DTM */
    public function get_received_date() {
        // TODO: IMPLEMENT 835
        /*` VARCHAR(12) DEFAULT NULL, */
    }

    public function get_statement_begin() {
        // TODO: IMPLEMENT 835
        /*` VARCHAR(12) DEFAULT NULL, */
    }

    public function get_statement_end() {
        // TODO: IMPLEMENT 835
        /*` VARCHAR(12) DEFAULT NULL, */
    }

    public function get_expiration_date() {
        // TODO: IMPLEMENT 835
        /*` VARCHAR(12) DEFAULT NULL, */
    }

  /* PER */
    public function get_claim_office_contact() {
        // TODO: IMPLEMENT 835
        /*` VARCHAR(60) DEFAULT NULL, */
    }

    public function get_claim_office_phone() {
        // TODO: IMPLEMENT 835
        /*` VARCHAR(10) DEFAULT NULL, */
    }

    public function get_claim_office_ext() {
        // TODO: IMPLEMENT 835
        /*` CHAR(3) DEFAULT NULL, */
    }

    public function get_claim_office_fax() {
        // TODO: IMPLEMENT 835
        /*` VARCHAR(10) DEFAULT NULL, */
    }

    public function get_claim_office_email() {
        // TODO: IMPLEMENT 835
        /*` VARCHAR(80) DEFAULT NULL, */
    }

  /* AMT */
    public function get_coverage_amount() {
        // TODO: IMPLEMENT 835
        /*` DECIMAL(15,2) DEFAULT NULL, */
    }

    public function get_patient_paid() {
        // TODO: IMPLEMENT 835
        /*` DECIMAL(15,2) DEFAULT NULL, */
    }

    public function get_discount_amount() {
        // TODO: IMPLEMENT 835
        /*` DECIMAL(15,2) DEFAULT NULL, */
    }

    public function get_tax() {
        // TODO: IMPLEMENT 835
        /*` DECIMAL(15,2) DEFAULT NULL, */
    }

    public function get_amt_qual_1() {
        // TODO: IMPLEMENT 835
        /*` CHAR(3) DEFAULT NULL, */
    }

    public function get_amt_1() {
        // TODO: IMPLEMENT 835
        /*` DECIMAL(15,2) DEFAULT NULL, */
    }

    public function get_amt_qual_2() {
        // TODO: IMPLEMENT 835
        /*` CHAR(3) DEFAULT NULL, */
    }

    public function get_amt_2() {
        // TODO: IMPLEMENT 835
        /*` DECIMAL(15,2) DEFAULT NULL, */
    }

  /* QTY */
    public function get_qty_qual_1() {
        // TODO: IMPLEMENT 835
        /*` CHAR(2) DEFAULT NULL, */
    }

    public function get_qty_1() {
        // TODO: IMPLEMENT 835
        /*` DECIMAL(15,4) DEFAULT NULL, */
    }

    public function get_qty_qual_2() {
        // TODO: IMPLEMENT 835
        /*` CHAR(2) DEFAULT NULL, */
    }

    public function get_qty_2() {
        // TODO: IMPLEMENT 835
        /*` DECIMAL(15,4) DEFAULT NULL, */
    }

  /* MIA */
    public function get_covered_days_or_visits_count() {
        // TODO: IMPLEMENT 835
        /*` INT(10) DEFAULT NULL, */
    }

    public function get_pps_operating_outlier_amount() {
        // TODO: IMPLEMENT 835
        /*` DECIMAL(15,2) DEFAULT NULL, */
    }

    public function get_lifetime_psychiatric_days_count() {
        // TODO: IMPLEMENT 835
        /*` INT(10) DEFAULT NULL, */
    }

    public function get_claim_drg_amount() {
        // TODO: IMPLEMENT 835
        /*` DECIMAL(15,2) DEFAULT NULL, */
    }

    public function get_mia_claim_payment_remark_code_1() {
        // TODO: IMPLEMENT 835
        /*` VARCHAR(50) DEFAULT NULL, */
    }

    public function get_mia_claim_payment_remark_code_2() {
        // TODO: IMPLEMENT 835
        /*` VARCHAR(50) DEFAULT NULL, */
    }

    public function get_mia_claim_payment_remark_code_3() {
        // TODO: IMPLEMENT 835
        /*` VARCHAR(50) DEFAULT NULL, */
    }

    public function get_mia_claim_payment_remark_code_4() {
        // TODO: IMPLEMENT 835
        /*` VARCHAR(50) DEFAULT NULL, */
    }

    public function get_mia_claim_payment_remark_code_5() {
        // TODO: IMPLEMENT 835
        /*` VARCHAR(50) DEFAULT NULL, */
    }

    public function get_claim_disproportionate_share_amount() {
        // TODO: IMPLEMENT 835
        /*` DECIMAL(15,2) DEFAULT NULL, */
    }

    public function get_claim_msp_passthrough_amount() {
        // TODO: IMPLEMENT 835
        /*` DECIMAL(15,2) DEFAULT NULL, */
    }

    public function get_claim_pps_capital_amount() {
        // TODO: IMPLEMENT 835
        /*` DECIMAL(15,2) DEFAULT NULL, */
    }

    public function get_pps_capital_fsp_drg_amount() {
        // TODO: IMPLEMENT 835
        /*` DECIMAL(15,2) DEFAULT NULL, */
    }

    public function get_pps_capital_hsp_drg_amount() {
        // TODO: IMPLEMENT 835
        /*` DECIMAL(15,2) DEFAULT NULL, */
    }

    public function get_pps_capital_dsh_drg_amount() {
        // TODO: IMPLEMENT 835
        /*` DECIMAL(15,2) DEFAULT NULL, */
    }

    public function get_old_capital_amount() {
        // TODO: IMPLEMENT 835
        /*` DECIMAL(15,2) DEFAULT NULL, */
    }

    public function get_pps_capital_ime_amount() {
        // TODO: IMPLEMENT 835
        /*` DECIMAL(15,2) DEFAULT NULL, */
    }

    public function get_pps_operating_hospital_specific_drg_amount() {
        // TODO: IMPLEMENT 835
        /*` DECIMAL(15,2) DEFAULT NULL, */
    }

    public function get_cost_report_day_count() {
        // TODO: IMPLEMENT 835
        /*` INT(10) DEFAULT NULL, */
    }

    public function get_pps_operating_federal_specific_drg_amount() {
        // TODO: IMPLEMENT 835
        /*` DECIMAL(15,2) DEFAULT NULL, */
    }

    public function get_claim_pps_capital_outlier_amount() {
        // TODO: IMPLEMENT 835
        /*` DECIMAL(15,2) DEFAULT NULL, */
    }

    public function get_claim_indirect_teaching_amount() {
        // TODO: IMPLEMENT 835
        /*` DECIMAL(15,2) DEFAULT NULL, */
    }

    public function get_nonpayable_professional_component_amount() {
        // TODO: IMPLEMENT 835
        /*` DECIMAL(15,2) DEFAULT NULL, */
    }

    public function get_pps_capital_exception_amount() {
        // TODO: IMPLEMENT 835
        /*` DECIMAL(15,2) DEFAULT NULL, */
    }

  /* MOA */
    public function get_reimbursement_rate() {
        // TODO: IMPLEMENT 835
        /*` DECIMAL(6,2) DEFAULT NULL, */
    }

    public function get_claim_hcpcs_payable_amount() {
        // TODO: IMPLEMENT 835
        /*` DECIMAL(15,2) DEFAULT NULL, */
    }

    public function get_moa_claim_payment_remark_code_1() {
        // TODO: IMPLEMENT 835
        /*` VARCHAR(50) DEFAULT NULL, */
    }

    public function get_moa_claim_payment_remark_code_2() {
        // TODO: IMPLEMENT 835
        /*` VARCHAR(50) DEFAULT NULL, */
    }

    public function get_moa_claim_payment_remark_code_3() {
        // TODO: IMPLEMENT 835
        /*` VARCHAR(50) DEFAULT NULL, */
    }

    public function get_moa_claim_payment_remark_code_4() {
        // TODO: IMPLEMENT 835
        /*` VARCHAR(50) DEFAULT NULL, */
    }

    public function get_moa_claim_payment_remark_code_5() {
        // TODO: IMPLEMENT 835
        /*` VARCHAR(50) DEFAULT NULL, */
    }

    public function get_claim_esrd_payment_amount() {
        // TODO: IMPLEMENT 835
        /*` DECIMAL(15,2) DEFAULT NULL, */
    }

    public function get_nonpayable_prof_component_amount() {
        // TODO: IMPLEMENT 835
        /*` DECIMAL(15,2) DEFAULT NULL, */
    }

  /* PATIENT */
    public function get_patient_last() {
        // TODO: IMPLEMENT 835
        /*` VARCHAR(60) DEFAULT NULL, */
    }

    public function get_patient_first() {
        // TODO: IMPLEMENT 835
        /*` VARCHAR(35) DEFAULT NULL, */
    }

    public function get_patient_middle() {
        // TODO: IMPLEMENT 835
        /*` VARCHAR(25) DEFAULT NULL, */
    }

    public function get_patient_suffix() {
        // TODO: IMPLEMENT 835
        /*` VARCHAR(10) DEFAULT NULL, */
    }

    public function get_patient_id_qual() {
        // TODO: IMPLEMENT 835
        /*` CHAR(2) DEFAULT NULL, */
    }

    public function get_patient_id() {
        // TODO: IMPLEMENT 835
        /*` VARCHAR(20) DEFAULT NULL, */
    }

  /* INSURED */
    public function get_insured_last() {
        // TODO: IMPLEMENT 835
        /*` VARCHAR(60) DEFAULT NULL, */
    }

    public function get_insured_first() {
        // TODO: IMPLEMENT 835
        /*` VARCHAR(35) DEFAULT NULL, */
    }

    public function get_insured_middle() {
        // TODO: IMPLEMENT 835
        /*` VARCHAR(25) DEFAULT NULL, */
    }

    public function get_insured_suffix() {
        // TODO: IMPLEMENT 835
        /*` VARCHAR(10) DEFAULT NULL, */
    }

    public function get_insured_id_qual() {
        // TODO: IMPLEMENT 835
        /*` CHAR(2) DEFAULT NULL, */
    }

    public function get_insured_id() {
        // TODO: IMPLEMENT 835
        /*` VARCHAR(20) DEFAULT NULL, */
    }

  /* CORRECTED INSURED */
    public function get_corrected_insured_last() {
        // TODO: IMPLEMENT 835
        /*` VARCHAR(60) DEFAULT NULL, */
    }

    public function get_corrected_insured_first() {
        // TODO: IMPLEMENT 835
        /*` VARCHAR(35) DEFAULT NULL, */
    }

    public function get_corrected_insured_middle() {
        // TODO: IMPLEMENT 835
        /*` VARCHAR(25) DEFAULT NULL, */
    }

    public function get_corrected_insured_suffix() {
        // TODO: IMPLEMENT 835
        /*` VARCHAR(10) DEFAULT NULL, */
    }

    public function get_corrected_insured_id_qual() {
        // TODO: IMPLEMENT 835
        /*` CHAR(2) DEFAULT NULL, */
    }

    public function get_corrected_insured_id() {
        // TODO: IMPLEMENT 835
        /*` VARCHAR(20) DEFAULT NULL, */
    }

  /* RENDERING */
    public function get_rendering_last() {
        // TODO: IMPLEMENT 835
        /*` VARCHAR(60) DEFAULT NULL, */
    }

    public function get_rendering_first() {
        // TODO: IMPLEMENT 835
        /*` VARCHAR(35) DEFAULT NULL, */
    }

    public function get_rendering_middle() {
        // TODO: IMPLEMENT 835
        /*` VARCHAR(25) DEFAULT NULL, */
    }

    public function get_rendering_suffix() {
        // TODO: IMPLEMENT 835
        /*` VARCHAR(10) DEFAULT NULL, */
    }

    public function get_rendering_id_qual() {
        // TODO: IMPLEMENT 835
        /*` CHAR(2) DEFAULT NULL, */
    }

    public function get_rendering_id() {
        // TODO: IMPLEMENT 835
        /*` VARCHAR(50) DEFAULT NULL, */
    }

    public function get_blue_cross_id() {
        // TODO: IMPLEMENT 835
        /*` VARCHAR(15) DEFAULT NULL, */
    }

    public function get_medicare_id() {
        // TODO: IMPLEMENT 835
        /*` VARCHAR(20) DEFAULT NULL, */
    }

    public function get_medicaid_id() {
        // TODO: IMPLEMENT 835
        /*` VARCHAR(20) DEFAULT NULL, */
    }

    public function get_rend_prov_other_id_qual1() {
        // TODO: IMPLEMENT 835
        /*` CHAR(3) DEFAULT NULL, */
    }

    public function get_rend_prov_other_id_1() {
        // TODO: IMPLEMENT 835
        /*` VARCHAR(50) DEFAULT NULL, */
    }

    public function get_rend_prov_other_id_qual2() {
        // TODO: IMPLEMENT 835
        /*` CHAR(3) DEFAULT NULL, */
    }

    public function get_rend_prov_other_id_2() {
        // TODO: IMPLEMENT 835
        /*` VARCHAR(50) DEFAULT NULL, */
    }

  /* CROSS OVER PAYER*/
    public function get_cross_over_payer_name() {
        // TODO: IMPLEMENT 835
        /*` VARCHAR(60) DEFAULT NULL, */
    }

    public function get_cross_over_payer_id_qual() {
        // TODO: IMPLEMENT 835
        /*` CHAR(2) DEFAULT NULL, */
    }

    public function get_cross_over_payer_id() {
        // TODO: IMPLEMENT 835
        /*` VARCHAR(50) DEFAULT NULL, */
    }

  /* CORRECTED PAYER */
    public function get_corrected_payer_name() {
        // TODO: IMPLEMENT 835
        /*` VARCHAR(60) DEFAULT NULL, */
    }

    public function get_corrected_payer_id_qual() {
        // TODO: IMPLEMENT 835
        /*` CHAR(2) DEFAULT NULL, */
    }

    public function get_corrected_payer_id() {
        // TODO: IMPLEMENT 835
        /*` VARCHAR(50) DEFAULT NULL, */
    }

  /* OTHER SUBSCRIBER */
    public function get_other_subscriber_last() {
        // TODO: IMPLEMENT 835
        /*` VARCHAR(60) DEFAULT NULL, */
    }

    public function get_other_subscriber_first() {
        // TODO: IMPLEMENT 835
        /*` VARCHAR(35) DEFAULT NULL, */
    }

    public function get_other_subscriber_middle() {
        // TODO: IMPLEMENT 835
        /*` VARCHAR(25) DEFAULT NULL, */
    }

    public function get_other_subscriber_suffix() {
        // TODO: IMPLEMENT 835
        /*` VARCHAR(10) DEFAULT NULL, */
    }

    public function get_other_subscriber_id_qual() {
        // TODO: IMPLEMENT 835
        /*` CHAR(2) DEFAULT NULL, */
    }

    public function get_other_subscriber_id() {
        // TODO: IMPLEMENT 835
        /*` VARCHAR(50) DEFAULT NULL, */
    }


}