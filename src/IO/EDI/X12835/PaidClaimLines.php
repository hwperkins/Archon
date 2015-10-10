<?php namespace Archon\IO\EDI\X12835;

class PaidClaimLines {

    // CONSTRAINT `fk_paid_claim_lines_id` FOREIGN KEY (`claim_id`) REFERENCES `{$this->schema}`.`edi_paid_claims` (`id`) ON DELETE CASCADE

    public function get_id() {
        // TODO: IMPLEMENT 835
        /*` BIGINT(20) NOT NULL AUTO_INCREMENT PRIMARY KEY, */
    }

    public function get_claim_id() {
        // TODO: IMPLEMENT 835
        /*` BIGINT(20) NOT NULL, */
    }

    /* SVC */
    public function get_procedure_code_qual() {
        // TODO: IMPLEMENT 835
        /*` CHAR(2) DEFAULT NULL, */
    }

    public function get_procedure_code() {
        // TODO: IMPLEMENT 835
        /*` VARCHAR(30) DEFAULT NULL, */
    }

    public function get_procedure_modifier_1() {
        // TODO: IMPLEMENT 835
        /*` CHAR(2) DEFAULT NULL, */
    }

    public function get_procedure_modifier_2() {
        // TODO: IMPLEMENT 835
        /*` CHAR(2) DEFAULT NULL, */
    }

    public function get_procedure_modifier_3() {
        // TODO: IMPLEMENT 835
        /*` CHAR(2) DEFAULT NULL, */
    }

    public function get_procedure_modifier_4() {
        // TODO: IMPLEMENT 835
        /*` CHAR(2) DEFAULT NULL, */
    }

    public function get_charged_amount() {
        // TODO: IMPLEMENT 835
        /*` VARCHAR(10) DEFAULT NULL, */
    }

    public function get_paid_amount() {
        // TODO: IMPLEMENT 835
        /*` VARCHAR(10) DEFAULT NULL, */
    }

    public function get_revenue_cCode() {
        // TODO: IMPLEMENT 835
        /*` VARCHAR(10) DEFAULT NULL, */
    }

    public function get_units_paid() {
        // TODO: IMPLEMENT 835
        /*` DECIMAL(15,4) DEFAULT NULL, */
    }

    public function get_submitted_procedure_code_qual() {
        // TODO: IMPLEMENT 835
        /*` CHAR(2) DEFAULT NULL, */
    }

    public function get_submitted_procedure_code() {
        // TODO: IMPLEMENT 835
        /*` VARCHAR(30) DEFAULT NULL, */
    }

    public function get_submitted_procedure_modifier_1() {
        // TODO: IMPLEMENT 835
        /*` CHAR(2) DEFAULT NULL, */
    }

    public function get_submitted_procedure_modifier_2() {
        // TODO: IMPLEMENT 835
        /*` CHAR(2) DEFAULT NULL, */
    }

    public function get_submitted_procedure_modifier_3() {
        // TODO: IMPLEMENT 835
        /*` CHAR(2) DEFAULT NULL, */
    }

    public function get_submitted_procedure_modifier_4() {
        // TODO: IMPLEMENT 835
        /*` CHAR(2) DEFAULT NULL, */
    }

    public function get_submitted_procedure_code_desc() {
        // TODO: IMPLEMENT 835
        /*` VARCHAR(80) DEFAULT NULL, */
    }

    public function get_units_charged() {
        // TODO: IMPLEMENT 835
        /*` DECIMAL(15,4) DEFAULT NULL, */
    }

    /* DTP */
    public function get_service_date() {
        // TODO: IMPLEMENT 835
        /*` VARCHAR(23) DEFAULT NULL, */
    }

    /* REF */
    public function get_provider_control_no() {
        // TODO: IMPLEMENT 835
        /*` VARCHAR(20) DEFAULT NULL, */
    }

    public function get_auth_no() {
        // TODO: IMPLEMENT 835
        /*` VARCHAR(50) DEFAULT NULL, */
    }

    public function get_attachment_code() {
        // TODO: IMPLEMENT 835
        /*` VARCHAR(50) DEFAULT NULL, */
    }

    public function get_location_number() {
        // TODO: IMPLEMENT 835
        /*` VARCHAR(50) DEFAULT NULL, */
    }

    public function get_prior_auth_no() {
        // TODO: IMPLEMENT 835
        /*` VARCHAR(50) DEFAULT NULL, */
    }

    public function get_healthcare_policy_id() {
        // TODO: IMPLEMENT 835
        /*` VARCHAR(50) DEFAULT NULL, */
    }

    public function get_apg_number() {
        // TODO: IMPLEMENT 835
        /*` VARCHAR(50) DEFAULT NULL, */
    }

    public function get_amb_payment_classification() {
        // TODO: IMPLEMENT 835
        /*` VARCHAR(50) DEFAULT NULL, */
    }

    public function get_predetermination_of_benefits_id() {
        // TODO: IMPLEMENT 835
        /*` VARCHAR(50) DEFAULT NULL, */
    }

    public function get_rate_code_no() {
        // TODO: IMPLEMENT 835
        /*` VARCHAR(50) DEFAULT NULL, */
    }

    public function get_state_license_no() {
        // TODO: IMPLEMENT 835
        /*` VARCHAR(50) DEFAULT NULL, */
    }

    public function get_blue_cross_provider_no() {
        // TODO: IMPLEMENT 835
        /*` VARCHAR(50) DEFAULT NULL, */
    }

    public function get_blue_shield_provider_no() {
        // TODO: IMPLEMENT 835
        /*` VARCHAR(50) DEFAULT NULL, */
    }

    public function get_medicare_provider_no() {
        // TODO: IMPLEMENT 835
        /*` VARCHAR(50) DEFAULT NULL, */
    }

    public function get_medicaid_provider_no() {
        // TODO: IMPLEMENT 835
        /*` VARCHAR(50) DEFAULT NULL, */
    }

    public function get_provider_upin() {
        // TODO: IMPLEMENT 835
        /*` VARCHAR(50) DEFAULT NULL, */
    }

    public function get_champus_id() {
        // TODO: IMPLEMENT 835
        /*` VARCHAR(50) DEFAULT NULL, */
    }

    public function get_facility_id() {
        // TODO: IMPLEMENT 835
        /*` VARCHAR(50) DEFAULT NULL, */
    }

    public function get_ncpdp_pharmacy_no() {
        // TODO: IMPLEMENT 835
        /*` VARCHAR(50) DEFAULT NULL, */
    }

    public function get_provider_commercial_no() {
        // TODO: IMPLEMENT 835
        /*` VARCHAR(50) DEFAULT NULL, */
    }

    public function get_national_provider_id() {
        // TODO: IMPLEMENT 835
        /*` VARCHAR(50) DEFAULT NULL, */
    }

    public function get_ssn() {
        // TODO: IMPLEMENT 835
        /*` VARCHAR(50) DEFAULT NULL, */
    }

    public function get_fed_tax_id() {
        // TODO: IMPLEMENT 835
        /*` VARCHAR(50) DEFAULT NULL, */
    }

    /* AMT */
    public function get_allowed_amount() {
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

    /* LQ */
    public function get_remark_codes() {
        // TODO: IMPLEMENT 835
        /*` VARCHAR(100) DEFAULT NULL, */
    }


}