<?php
/**
 * Legal metadata mapping for school-profile rules.
 *
 * Kept outside the runtime profile implementation so legal metadata changes
 * cannot alter or invalidate canonical/scoped workload data. No HTML here.
 */

require_once __DIR__ . '/legal-sources.php';

if (!function_exists('schoolProfileGeneralEducationLegalSourceKeys')) {
    function schoolProfileGeneralEducationLegalSourceKeys()
    {
        return array('gymnasio_technology_informatics_groups_2020');
    }
}

if (!function_exists('schoolProfileGeneralEducationLegalLinks')) {
    function schoolProfileGeneralEducationLegalLinks()
    {
        return legalSourceLinksForKeys(schoolProfileGeneralEducationLegalSourceKeys());
    }
}
