<?php
/**
 * Created by PhpStorm.
 * User: hwgeh_000
 * Date: 8/9/2015
 * Time: 7:33 AM
 */

namespace DataFrame\EDI;


class HETS270UnitTest extends \PHPUnit_Framework_TestCase {

    public function test_hets_270_submission() {

        // SEE: https://www.cms.gov/Research-Statistics-Data-and-Systems/CMS-Information-Technology/HETSHelp/downloads/HETS270271CompanionGuide5010.pdf

        /*
         * Interchange Control Structure (ISA/IEA)
         * ASC X12 270/271 version 005010X279A1 TR3
         */

        // Reference: ISA
        // Name: Interchange Control Header
        // X12 Codes: N/A
        // Notes/Comments: N/A

        // Reference: ISA01
        // Name: Authorization Information Qualifier
        // X12 Codes: 00
        // Notes/Comments: HETS always expects "00".

        // Reference: ISA03
        // Name: Security Information Qualifier
        // X12 Codes: 00
        // Notes/Comments: HETS always expects "00".

        // Reference: ISA05
        // Name: Interchange ID Qualifier
        // X12 Codes: ZZ
        // Notes/Comments: HETS always expects "ZZ"

        // Reference: ISA06
        // Name: Interchange Sender ID
        // X12 Codes: N/A
        // Notes/Comments: HETS always expects the Trading Partner Submitter ID assigned by CMS.

        // Reference: ISA07
        // Name: Interchange ID Qualifier
        // X12 Codes: ZZ
        // Notes/Comments: HETS always expects "ZZ".

        // Reference: ISA08
        // Name: Interchange Receiver ID
        // X12 Codes: N/A
        // Notes/Comments: HETS always expects "CMS".

        // Reference:  ISA14
        // Name: Acknowledgment Requested
        // X12 Codes: 0,1
        // Notes/Comments: HETS will not return the TA1 acknowledgement receipt of a rel time transaction even if acknowledgment is requested.

        /*
         * Functional Group Structure (GS/GE)
         */

        // Reference: GS
        // Name: Functional Group Header
        // X12 Codes: N/A
        // Notes/Comments: N/A

        // Reference: GS02
        // Name: Application Sender's Code
        // X12 Codes: N/A
        // Notes/Comments: HETS always expects the Trading Partner Submitter ID assigned by CMS.

        // Reference: GS03
        // Name: Application Receiver's Code
        // X12 Codes: N/A
        // Notes/Comments: HETS always expects "CMS".

        /*
         * Transaction Set Header/Trailer (ST/SE)
         * Please follow the rules as specified by the ASC X12 270/271 version 005010X270A1 TR3.
         */

        /*
         * Preferred 270 Request Delimiters
         */

        // Character: *
        // Name: Asterisk
        // Delimiter: Data Element Separator

        // Character: |
        // Name: Pipe
        // Delimiter: Component Element Separator

        // Character: ~
        // Name: Tilde
        // Delimiter: Segment Terminator

        // Character: ^
        // Name: Carat
        // Delimiter: Repetition Separator

        /*
         * Each transaction must contain only one Patient Request.
         * Each 270 request must have only:
         * - one ISA/IEA
         * - one GS/GE
         * - one ST/SE
         * - and a single 2100C Subscriber Loop.
         */

    }
}
