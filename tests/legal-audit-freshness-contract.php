<?php
require_once dirname(__DIR__) . '/includes/legal-audit.php';
require_once dirname(__DIR__) . '/includes/legal-sources.php';

$fail = 0;
function audit_check($ok, $label) {
    global $fail;
    if ($ok) echo "PASS: " . $label . "\n";
    else { echo "FAIL: " . $label . "\n"; $fail++; }
}

$rows = legalAuditCatalogueRows();
audit_check(count($rows) >= 37, 'catalogue coverage includes all public tools plus legal sub-pages');

$assign = legalAuditForPage('anatheseis-mathimaton.php');
audit_check($assign['status'] === 'verified', 'assignments has explicit verified status');
audit_check($assign['last_verified'] === '2026-10-07', 'assignments verification date is explicit');

$unknown = legalAuditForPage('non-existent-tool.php');
audit_check($unknown['status'] === 'pending', 'unknown tools fail closed to pending');
audit_check(!legalAuditIsPubliclyVisible($unknown), 'pending status is not presented as a verified public badge');

$source = legalSourceByKey('kallitexnika_assignments_2026_5940');
audit_check(isset($source['status']) && $source['status'] === 'active', '5940 source is active');
audit_check(isset($source['last_verified']) && $source['last_verified'] === '2026-10-07', '5940 source stores explicit verification date');
audit_check(isset($source['repeals']) && in_array('kallitexnika_assignments_2018', $source['repeals'], true), '5940 records repeal lineage');

$old = legalSourceByKey('kallitexnika_assignments_2018');
audit_check(isset($old['status']) && $old['status'] === 'historical', 'repealed 2018 artistic-school source is historical');
audit_check(isset($old['repealed_by']) && $old['repealed_by'] === 'kallitexnika_assignments_2026_5940', '2018 source points to repealing source');

exit($fail ? 1 : 0);
