<?php namespace Archon\IO\EDI\X12835;

class ClaimLevelAdjustments {

    // CONSTRAINT `fk_edi_claim_level_adjustments_id` FOREIGN KEY (`claim_id`) REFERENCES `{$this->schema}`.`edi_paid_claims` (`id`) ON DELETE CASCADE

    public function get_id() {
        // TODO: IMPLEMENT 835
        /* ` BIGINT(20) NOT NULL AUTO_INCREMENT PRIMARY KEY, */
    }

    public function get_claim_id() {
        // TODO: IMPLEMENT 835
        /* ` BIGINT(20) NOT NULL, */
    }

    public function get_adjustment_group() {
        // TODO: IMPLEMENT 835
        /* ` CHAR(2) DEFAULT NULL, */
    }

    public function get_adjustment_reason() {
        // TODO: IMPLEMENT 835
        /* ` CHAR(3) DEFAULT NULL, */
    }

    public function get_adjustment_amount() {
        // TODO: IMPLEMENT 835
        /* ` CHAR(10) DEFAULT NULL, */
    }

    public function get_adjustment_qty() {
        // TODO: IMPLEMENT 835
        /* ` CHAR(5) DEFAULT NULL, */
    }


}