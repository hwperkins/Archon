<?php namespace Archon\IO\EDI\X12835;

class ProviderLevelAdjustment {

    // CONSTRAINT `fk_provider_level_adjustments_id` FOREIGN KEY (`payment_id`) REFERENCES `{$this->schema}`.`edi_claim_payment` (`id`) ON DELETE CASCADE

    public function get_id() {
        // TODO: IMPLEMENT 835
        /*` BIGINT(20) AUTO_INCREMENT NOT NULL PRIMARY KEY, */
    }

    public function get_payment_id() {
        // TODO: IMPLEMENT 835
        /*` BIGINT(20) NOT NULL, */
    }

    public function get_provider_id() {
        // TODO: IMPLEMENT 835
        /*` VARCHAR(50) DEFAULT NULL, */
    }

    public function get_fiscal_period_date() {
        // TODO: IMPLEMENT 835
        /*` VARCHAR(12) DEFAULT NULL, */
    }

    public function get_adjustment_reason() {
        // TODO: IMPLEMENT 835
        /*` CHAR(3) DEFAULT NULL, */
    }

    public function get_adjustment_identifier() {
        // TODO: IMPLEMENT 835
        /*` VARCHAR(50) DEFAULT NULL, */
    }

    public function get_adjustment_amount() {
        // TODO: IMPLEMENT 835
        /*` DECIMAL(15,2) DEFAULT NULL, */
    }

}