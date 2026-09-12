<?php
/**
 * Source registry contract audit.
 *
 * Usage:
 *   php tools/source-registry-audit.php
 *
 * Exit 0 when legal/reference registries and their relationships are valid.
 * PHP 5.6 compatible; no web/UI dependencies.
 */

require_once dirname(__DIR__) . '/includes/legal-sources.php';
require_once dirname(__DIR__) . '/includes/reference-sources.php';

$errors = array();
$warnings = array();

function sourceAuditAdd(&$bucket, $message)
{
    $bucket[] = $message;
}

function sourceAuditIsNonEmptyArray($value)
{
    return is_array($value) && count($value) > 0;
}

$legal = legalSourcesRegistry();
$legalRequired = legalSourcesRequiredFields();
$relationPairs = array(
    array('amends', 'amended_by'),
    array('supplements', 'supplemented_by'),
    array('corrects', 'corrected_by'),
);

foreach ($legal as $key => $entry) {
    foreach ($legalRequired as $field) {
        if (!isset($entry[$field]) || trim((string) $entry[$field]) === '') {
            sourceAuditAdd($errors, 'legal:' . $key . ' missing required field ' . $field);
        }
    }

    if (!isset($entry['citation_title']) || trim((string) $entry['citation_title']) === '') {
        sourceAuditAdd($errors, 'legal:' . $key . ' missing citation_title');
    }
    if (!sourceAuditIsNonEmptyArray(isset($entry['school_types']) ? $entry['school_types'] : null)) {
        sourceAuditAdd($errors, 'legal:' . $key . ' missing/non-array school_types');
    }
    if (!sourceAuditIsNonEmptyArray(isset($entry['topics']) ? $entry['topics'] : null)) {
        sourceAuditAdd($errors, 'legal:' . $key . ' missing/non-array topics');
    }

    // Normalized registries should model every legal act as its own node.
    if (isset($entry['amendments'])) {
        sourceAuditAdd($errors, 'legal:' . $key . ' still contains nested amendments');
    }

    if (!isset($entry['date']) || trim((string) $entry['date']) === '') {
        sourceAuditAdd($warnings, 'legal:' . $key . ' has no exact decision date');
    } elseif (!preg_match('/^\d{4}-\d{2}-\d{2}$/', (string) $entry['date'])) {
        sourceAuditAdd($errors, 'legal:' . $key . ' has invalid date format');
    }

    foreach ($relationPairs as $pair) {
        $forward = $pair[0];
        $reverse = $pair[1];
        if (!isset($entry[$forward])) {
            continue;
        }
        if (!is_array($entry[$forward])) {
            sourceAuditAdd($errors, 'legal:' . $key . ' relation ' . $forward . ' must be an array');
            continue;
        }
        foreach ($entry[$forward] as $targetKey) {
            if (!isset($legal[$targetKey])) {
                sourceAuditAdd($errors, 'legal:' . $key . ' relation ' . $forward . ' points to unknown ' . $targetKey);
                continue;
            }
            $reverseTargets = isset($legal[$targetKey][$reverse]) && is_array($legal[$targetKey][$reverse])
                ? $legal[$targetKey][$reverse]
                : array();
            if (!in_array($key, $reverseTargets, true)) {
                sourceAuditAdd($errors, 'legal:' . $key . ' relation ' . $forward . ' lacks reverse ' . $reverse . ' on ' . $targetKey);
            }
        }
    }
}

$references = referenceSourcesRegistry();
$referenceRequired = referenceSourcesRequiredFields();
$allowedKinds = referenceSourceKinds();
foreach ($references as $key => $entry) {
    foreach ($referenceRequired as $field) {
        if (!isset($entry[$field]) || trim((string) $entry[$field]) === '') {
            sourceAuditAdd($errors, 'reference:' . $key . ' missing required field ' . $field);
        }
    }
    if (!isset($entry['kind']) || !in_array($entry['kind'], $allowedKinds, true)) {
        sourceAuditAdd($errors, 'reference:' . $key . ' has unsupported kind');
    }
    if (!sourceAuditIsNonEmptyArray(isset($entry['topics']) ? $entry['topics'] : null)) {
        sourceAuditAdd($errors, 'reference:' . $key . ' missing/non-array topics');
    }
}

printf("Legal sources:     %d\n", count($legal));
printf("Reference sources: %d\n", count($references));
printf("Errors:            %d\n", count($errors));
printf("Warnings:          %d\n", count($warnings));

foreach ($errors as $message) {
    echo '[ERROR] ' . $message . "\n";
}
foreach ($warnings as $message) {
    echo '[WARN]  ' . $message . "\n";
}

if (count($errors) === 0) {
    echo "\nSource registries are structurally valid.\n";
    exit(0);
}

exit(1);
