<?php
/**
 * Strong contract test for includes/legal-sources.php.
 *
 * Run: php tests/legal-sources-contract-test.php
 * Exit 0 = registry/consumer contract is valid. Exit 1 = release-blocking failure.
 * PHP 5.6 compatible.
 */

$root = dirname(__DIR__);
$registryFile = $root . '/includes/legal-sources.php';
$failures = array();
$warnings = array();

function legalSourcesContractFail(&$failures, $message)
{
    $failures[] = $message;
}

function legalSourcesContractWarn(&$warnings, $message)
{
    $warnings[] = $message;
}

function legalSourcesContractNextSignificant($tokens, $index)
{
    $count = count($tokens);
    for ($i = $index; $i < $count; $i++) {
        $token = $tokens[$i];
        if (is_array($token) && in_array($token[0], array(T_WHITESPACE, T_COMMENT, T_DOC_COMMENT), true)) {
            continue;
        }
        return $i;
    }
    return null;
}

/**
 * Read authored top-level registry keys from the PHP source itself.
 * This avoids PHP's silent overwrite behaviour for duplicate array keys and
 * is independent of whitespace/indentation formatting.
 */
function legalSourcesContractAuthoredRegistryKeys($source)
{
    $tokens = token_get_all($source);
    $count = count($tokens);

    for ($i = 0; $i < $count; $i++) {
        if (!is_array($tokens[$i]) || $tokens[$i][0] !== T_VARIABLE || $tokens[$i][1] !== '$registry') {
            continue;
        }

        $eq = legalSourcesContractNextSignificant($tokens, $i + 1);
        if ($eq === null || $tokens[$eq] !== '=') {
            continue;
        }
        $arr = legalSourcesContractNextSignificant($tokens, $eq + 1);
        if ($arr === null || !is_array($tokens[$arr]) || $tokens[$arr][0] !== T_ARRAY) {
            continue; // e.g. static $registry = null
        }
        $open = legalSourcesContractNextSignificant($tokens, $arr + 1);
        if ($open === null || $tokens[$open] !== '(') {
            continue;
        }

        $keys = array();
        $depth = 1;
        for ($j = $open + 1; $j < $count && $depth > 0; $j++) {
            $token = $tokens[$j];
            if (is_string($token)) {
                if ($token === '(') $depth++;
                if ($token === ')') $depth--;
                continue;
            }

            if ($depth !== 1 || $token[0] !== T_CONSTANT_ENCAPSED_STRING) {
                continue;
            }

            $arrow = legalSourcesContractNextSignificant($tokens, $j + 1);
            $value = ($arrow === null) ? null : legalSourcesContractNextSignificant($tokens, $arrow + 1);
            if ($arrow === null || !is_array($tokens[$arrow]) || $tokens[$arrow][0] !== T_DOUBLE_ARROW) {
                continue;
            }
            if ($value === null || !is_array($tokens[$value]) || $tokens[$value][0] !== T_ARRAY) {
                continue;
            }

            $literal = $token[1];
            if (strlen($literal) >= 2 && $literal[0] === "'" && substr($literal, -1) === "'") {
                $key = substr($literal, 1, -1);
                $key = str_replace(array("\\'", "\\\\"), array("'", "\\"), $key);
                $keys[] = $key;
            }
        }
        return $keys;
    }

    return array();
}

function legalSourcesContractIsExactDate($value)
{
    if (!is_string($value) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
        return false;
    }
    $date = DateTime::createFromFormat('!Y-m-d', $value);
    return $date instanceof DateTime && $date->format('Y-m-d') === $value;
}

function legalSourcesContractDmyToYmd($day, $month, $year)
{
    $value = $year . '-' . $month . '-' . $day;
    return legalSourcesContractIsExactDate($value) ? $value : null;
}

function legalSourcesContractLastDmyDate($text)
{
    if (!is_string($text)) return null;
    if (!preg_match_all('/(\d{2})-(\d{2})-(\d{4})/u', $text, $matches, PREG_SET_ORDER) || empty($matches)) {
        return null;
    }
    $last = $matches[count($matches) - 1];
    return legalSourcesContractDmyToYmd($last[1], $last[2], $last[3]);
}

function legalSourcesContractHttpsUrl($url)
{
    if (!is_string($url) || trim($url) === '') return false;
    $parts = @parse_url($url);
    return is_array($parts)
        && isset($parts['scheme'], $parts['host'])
        && strtolower($parts['scheme']) === 'https'
        && trim($parts['host']) !== '';
}

function legalSourcesContractCheckEtDeepLink($key, $url, &$failures)
{
    $parts = @parse_url($url);
    if (!is_array($parts) || empty($parts['host']) || strtolower($parts['host']) !== 'search.et.gr') {
        return;
    }
    $path = isset($parts['path']) ? rtrim($parts['path'], '/') : '';
    if ($path !== '/el/fek') {
        return;
    }
    $query = array();
    if (isset($parts['query'])) parse_str($parts['query'], $query);
    if (empty($query['fekId']) || !ctype_digit((string) $query['fekId'])) {
        legalSourcesContractFail($failures, "[$key] search.et.gr FEK link is not a document deep link with numeric fekId: $url");
    }
}

function legalSourcesContractCheckStringArray($key, $field, $value, $allowEmpty, &$failures)
{
    if (!is_array($value)) {
        legalSourcesContractFail($failures, "[$key] field '$field' must be an array");
        return;
    }
    if (!$allowEmpty && count($value) === 0) {
        legalSourcesContractFail($failures, "[$key] field '$field' must not be empty");
        return;
    }
    $seen = array();
    foreach ($value as $item) {
        if (!is_string($item) || trim($item) === '') {
            legalSourcesContractFail($failures, "[$key] field '$field' contains a non-string/empty value");
            continue;
        }
        if (isset($seen[$item])) {
            legalSourcesContractFail($failures, "[$key] field '$field' contains duplicate value '$item'");
        }
        $seen[$item] = true;
    }
}

// --- 0: registry file and API surface --------------------------------------
if (!is_file($registryFile)) {
    fwrite(STDERR, "Missing $registryFile\n");
    exit(1);
}
$source = file_get_contents($registryFile);
if ($source === false) {
    fwrite(STDERR, "Could not read $registryFile\n");
    exit(1);
}
require $registryFile;

$requiredFunctions = array(
    'legalSourcesRegistry',
    'legalSourcesRequiredFields',
    'legalSourceByKey',
    'legalSourcesForKeys',
    'legalSourceUrl',
    'legalSourceCompactFek',
    'legalSourceCompactDecision',
    'legalSourceLinksForKeys',
);
foreach ($requiredFunctions as $functionName) {
    if (!function_exists($functionName)) {
        legalSourcesContractFail($failures, "Missing required helper function $functionName()");
    }
}
if (!empty($failures)) {
    foreach ($failures as $failure) fwrite(STDERR, "  ✘ $failure\n");
    exit(1);
}

$registry = legalSourcesRegistry();
$required = legalSourcesRequiredFields();
if (!is_array($registry) || empty($registry)) {
    legalSourcesContractFail($failures, 'legalSourcesRegistry() must return a non-empty array');
}
if (!is_array($required) || empty($required)) {
    legalSourcesContractFail($failures, 'legalSourcesRequiredFields() must return a non-empty array');
}

// --- 1: duplicate authored keys, independent of indentation ----------------
$authoredKeys = legalSourcesContractAuthoredRegistryKeys($source);
if (empty($authoredKeys)) {
    legalSourcesContractFail($failures, 'Could not locate authored top-level registry keys in raw source');
} else {
    $seenAuthored = array();
    foreach ($authoredKeys as $key) {
        if (isset($seenAuthored[$key])) {
            legalSourcesContractFail($failures, "Duplicate authored registry key '$key' (PHP would silently overwrite the first one)");
        }
        $seenAuthored[$key] = true;
    }
    if (count($seenAuthored) !== count($registry)) {
        legalSourcesContractFail($failures, 'Authored unique-key count does not match loaded registry count');
    }
}

// --- 2: schema, dates, canonical URLs, alternates ---------------------------
$scalarFields = array('title', 'citation_title', 'decision', 'fek', 'date', 'url', 'valid_from');
$arrayFields = array('school_types', 'topics');
$relationFields = array('amends', 'amended_by', 'corrects', 'corrected_by', 'supplements', 'supplemented_by');
$relationPairs = array(
    array('amends', 'amended_by'),
    array('corrects', 'corrected_by'),
    array('supplements', 'supplemented_by'),
);

foreach ($registry as $key => $entry) {
    if (!is_string($key) || !preg_match('/^[a-z0-9_]+$/', $key)) {
        legalSourcesContractFail($failures, "Invalid registry key '$key' (expected lowercase snake_case)");
    }
    if (!is_array($entry)) {
        legalSourcesContractFail($failures, "[$key] registry entry must be an array");
        continue;
    }

    foreach ($required as $field) {
        if (!isset($entry[$field]) || (is_string($entry[$field]) && trim($entry[$field]) === '')) {
            legalSourcesContractFail($failures, "[$key] missing required field '$field'");
        }
    }
    foreach ($scalarFields as $field) {
        if (!isset($entry[$field]) || !is_string($entry[$field]) || trim($entry[$field]) === '') {
            legalSourcesContractFail($failures, "[$key] field '$field' must be a non-empty string");
        }
    }
    foreach ($arrayFields as $field) {
        legalSourcesContractCheckStringArray($key, $field, isset($entry[$field]) ? $entry[$field] : null, false, $failures);
    }

    if (isset($entry['date']) && !legalSourcesContractIsExactDate($entry['date'])) {
        legalSourcesContractFail($failures, "[$key] invalid date '{$entry['date']}' (expected real YYYY-MM-DD date)");
    }

    if (!empty($entry['decision']) && !empty($entry['date'])) {
        $decisionDate = legalSourcesContractLastDmyDate($entry['decision']);
        if ($decisionDate !== null && $decisionDate !== $entry['date']) {
            legalSourcesContractFail($failures, "[$key] decision date $decisionDate does not match date {$entry['date']}");
        }
    }

    if (!empty($entry['fek']) && !empty($entry['date'])) {
        $publicationDate = legalSourcesContractLastDmyDate($entry['fek']);
        if ($publicationDate !== null && $publicationDate < $entry['date']) {
            legalSourcesContractFail($failures, "[$key] FEK publication date $publicationDate predates decision date {$entry['date']}");
        }
    }

    if (!empty($entry['url'])) {
        if (!legalSourcesContractHttpsUrl($entry['url'])) {
            legalSourcesContractFail($failures, "[$key] canonical url must be a valid HTTPS URL: {$entry['url']}");
        } else {
            legalSourcesContractCheckEtDeepLink($key, $entry['url'], $failures);
        }
    }

    if (isset($entry['alternate_urls'])) {
        if (!is_array($entry['alternate_urls'])) {
            legalSourcesContractFail($failures, "[$key] alternate_urls must be an array");
        } else {
            foreach ($entry['alternate_urls'] as $variant => $url) {
                if (!is_string($variant) || trim($variant) === '') {
                    legalSourcesContractFail($failures, "[$key] alternate_urls contains an empty/non-string variant name");
                    continue;
                }
                if (!legalSourcesContractHttpsUrl($url)) {
                    legalSourcesContractFail($failures, "[$key] alternate url '$variant' must be a valid HTTPS URL: $url");
                    continue;
                }
                legalSourcesContractCheckEtDeepLink($key . ':' . $variant, $url, $failures);
                if (!empty($entry['url']) && $url === $entry['url']) {
                    legalSourcesContractWarn($warnings, "[$key] alternate url '$variant' duplicates canonical url");
                }
            }
        }
    }

    if (isset($entry['amendments'])) {
        legalSourcesContractFail($failures, "[$key] still contains legacy nested 'amendments'; model each legal act as its own registry node");
    }

    foreach ($relationFields as $field) {
        if (!isset($entry[$field])) continue;
        legalSourcesContractCheckStringArray($key, $field, $entry[$field], true, $failures);
        if (!is_array($entry[$field])) continue;
        foreach ($entry[$field] as $target) {
            if ($target === $key) {
                legalSourcesContractFail($failures, "[$key] relation '$field' self-references the same key");
            } elseif (!isset($registry[$target])) {
                legalSourcesContractFail($failures, "[$key] relation '$field' points to unknown key '$target'");
            }
        }
    }
}

// --- 3: bidirectional graph checks, in BOTH directions ----------------------
foreach ($registry as $key => $entry) {
    foreach ($relationPairs as $pair) {
        $forward = $pair[0];
        $reverse = $pair[1];

        $forwardRefs = (isset($entry[$forward]) && is_array($entry[$forward])) ? $entry[$forward] : array();
        foreach ($forwardRefs as $target) {
            if (!isset($registry[$target])) continue;
            $backRefs = (isset($registry[$target][$reverse]) && is_array($registry[$target][$reverse]))
                ? $registry[$target][$reverse] : array();
            if (!in_array($key, $backRefs, true)) {
                legalSourcesContractFail($failures, "[$key] '$forward' -> '$target' but [$target] lacks '$reverse' -> '$key'");
            }
            if (!empty($entry['date']) && !empty($registry[$target]['date']) && $entry['date'] < $registry[$target]['date']) {
                legalSourcesContractFail($failures, "[$key] '$forward' -> '$target' has older date {$entry['date']} than target {$registry[$target]['date']}");
            }
        }

        $reverseRefs = (isset($entry[$reverse]) && is_array($entry[$reverse])) ? $entry[$reverse] : array();
        foreach ($reverseRefs as $sourceKey) {
            if (!isset($registry[$sourceKey])) continue;
            $sourceForward = (isset($registry[$sourceKey][$forward]) && is_array($registry[$sourceKey][$forward]))
                ? $registry[$sourceKey][$forward] : array();
            if (!in_array($key, $sourceForward, true)) {
                legalSourcesContractFail($failures, "[$key] '$reverse' -> '$sourceKey' but [$sourceKey] lacks '$forward' -> '$key'");
            }
        }
    }
}

// --- 4: duplicate canonical URL logic --------------------------------------
$byUrl = array();
$byAct = array();
foreach ($registry as $key => $entry) {
    if (!empty($entry['url'])) {
        if (!isset($byUrl[$entry['url']])) $byUrl[$entry['url']] = array();
        $byUrl[$entry['url']][] = $key;
    }
    if (!empty($entry['decision']) && !empty($entry['fek'])) {
        $actId = $entry['decision'] . "\n" . $entry['fek'];
        if (!isset($byAct[$actId])) $byAct[$actId] = array();
        $byAct[$actId][] = $key;
    }
}
foreach ($byUrl as $url => $keysForUrl) {
    if (count($keysForUrl) < 2) continue;
    for ($i = 0; $i < count($keysForUrl); $i++) {
        for ($j = $i + 1; $j < count($keysForUrl); $j++) {
            $a = $keysForUrl[$i];
            $b = $keysForUrl[$j];
            $titleA = !empty($registry[$a]['citation_title']) ? $registry[$a]['citation_title'] : $registry[$a]['title'];
            $titleB = !empty($registry[$b]['citation_title']) ? $registry[$b]['citation_title'] : $registry[$b]['title'];
            if ($titleA === $titleB) {
                legalSourcesContractFail($failures, "[$a] and [$b] have same canonical url AND same citation title; likely duplicate registry nodes");
            }
            if ($registry[$a]['fek'] !== $registry[$b]['fek']) {
                legalSourcesContractFail($failures, "[$a] and [$b] share canonical url but claim different FEKs ({$registry[$a]['fek']} vs {$registry[$b]['fek']})");
            }
        }
    }
}
foreach ($byAct as $actId => $keysForAct) {
    if (count($keysForAct) > 1) {
        legalSourcesContractFail($failures, 'Same decision + FEK is represented by multiple keys: ' . implode(', ', $keysForAct));
    }
}

// --- 5: pinned official deep links for known ET records --------------------
$pinnedCanonicalUrls = array(
    'eae_assignments_2026_5610' => 'https://search.et.gr/el/fek/?fekId=805582',
    'epal_assignments_2026_5710' => 'https://search.et.gr/el/fek/?fekId=805702',
    'pepal_g_assignments_2026_5710' => 'https://search.et.gr/el/fek/?fekId=805702',
    'eneegyl_assignments_2026_5733' => 'https://search.et.gr/el/fek/?fekId=805734',
    'eeeek_assignments_2026_5733' => 'https://search.et.gr/el/fek/?fekId=805734',
);
foreach ($pinnedCanonicalUrls as $key => $expectedUrl) {
    if (!isset($registry[$key])) {
        legalSourcesContractFail($failures, "Pinned legal source '$key' is missing from registry");
    } elseif (!isset($registry[$key]['url']) || $registry[$key]['url'] !== $expectedUrl) {
        $actual = isset($registry[$key]['url']) ? $registry[$key]['url'] : '(missing)';
        legalSourcesContractFail($failures, "[$key] canonical URL regression: expected $expectedUrl, got $actual");
    }
}

// --- 6: helper-function behaviour -------------------------------------------
$sampleKeys = array_keys($registry);
if (!empty($sampleKeys)) {
    $firstKey = $sampleKeys[0];
    if (legalSourceByKey('key_pou_den_yparxei_sigoura_xxx') !== null) {
        legalSourcesContractFail($failures, 'legalSourceByKey() must return null for unknown key');
    }
    if (legalSourceByKey($firstKey) !== $registry[$firstKey]) {
        legalSourcesContractFail($failures, "legalSourceByKey('$firstKey') must match registry entry");
    }

    $deduped = legalSourcesForKeys(array($firstKey, $firstKey, 'anyparkto_key'));
    if (count($deduped) !== 1 || !isset($deduped[$firstKey])) {
        legalSourcesContractFail($failures, 'legalSourcesForKeys() must dedupe and ignore unknown keys');
    }
    $single = legalSourcesForKeys($firstKey);
    if (count($single) !== 1 || !isset($single[$firstKey])) {
        legalSourcesContractFail($failures, 'legalSourcesForKeys() must accept a scalar key');
    }

    if (legalSourceUrl($firstKey) !== $registry[$firstKey]['url']) {
        legalSourcesContractFail($failures, "legalSourceUrl('$firstKey') must return canonical URL");
    }
    if (legalSourceUrl('key_pou_den_yparxei_sigoura_xxx') !== null) {
        legalSourcesContractFail($failures, 'legalSourceUrl() must return null for unknown key');
    }

    foreach ($registry as $key => $entry) {
        if (!empty($entry['alternate_urls']) && is_array($entry['alternate_urls'])) {
            foreach ($entry['alternate_urls'] as $variant => $url) {
                if (legalSourceUrl($key, $variant) !== $url) {
                    legalSourcesContractFail($failures, "legalSourceUrl('$key', '$variant') did not resolve alternate URL");
                }
            }
            if (legalSourceUrl($key, 'variant_pou_den_yparxei') !== $entry['url']) {
                legalSourcesContractFail($failures, "legalSourceUrl('$key', unknown variant) must fall back to canonical URL");
            }
        }
    }

    if (legalSourceCompactFek('ΦΕΚ Β΄ 2132/09-04-2026') !== 'ΦΕΚ Β΄ 2132/2026') {
        legalSourcesContractFail($failures, 'legalSourceCompactFek() date compaction regression');
    }
    if (legalSourceCompactFek('κάτι χωρίς ημερομηνία') !== 'κάτι χωρίς ημερομηνία') {
        legalSourcesContractFail($failures, 'legalSourceCompactFek() must preserve non-matching text');
    }
    if (legalSourceCompactDecision('Υ.Α. 118380/Θ2/21-09-2021') !== 'Υ.Α. 118380/Θ2/2021') {
        legalSourcesContractFail($failures, 'legalSourceCompactDecision() date compaction regression');
    }

    if (legalSourceLinksForKeys(array('key_pou_den_yparxei_sigoura_xxx')) !== array()) {
        legalSourcesContractFail($failures, 'legalSourceLinksForKeys() must ignore unknown keys');
    }
    foreach ($registry as $key => $entry) {
        $links = legalSourceLinksForKeys(array($key));
        $baseLinks = array();
        foreach ($links as $link) {
            if (!is_array($link)
                || !isset($link['source_key'], $link['relation'], $link['url'], $link['label'])
                || trim((string) $link['url']) === ''
                || trim((string) $link['label']) === '') {
                legalSourcesContractFail($failures, "legalSourceLinksForKeys('$key') returned malformed link metadata");
                continue;
            }
            if ($link['source_key'] === $key && $link['relation'] === 'base') $baseLinks[] = $link;
        }
        if (count($baseLinks) !== 1 || $baseLinks[0]['url'] !== $entry['url']) {
            legalSourcesContractFail($failures, "legalSourceLinksForKeys('$key') must expose exactly one canonical base link");
        }
    }
}

// --- 7: consumer mappings must resolve only registered source keys ----------
$consumerFiles = array(
    $root . '/includes/teaching-assignments-legal.php',
    $root . '/includes/weekly-timetable-legal.php',
);
foreach ($consumerFiles as $file) {
    if (!is_file($file)) {
        legalSourcesContractFail($failures, 'Missing legal-source consumer file ' . substr($file, strlen($root) + 1));
    } else {
        require_once $file;
    }
}

$allSchoolTypes = array();
foreach ($registry as $entry) {
    if (isset($entry['school_types']) && is_array($entry['school_types'])) {
        foreach ($entry['school_types'] as $schoolType) $allSchoolTypes[$schoolType] = true;
    }
}
$schoolTypeList = array_keys($allSchoolTypes);

$consumerKeySets = array();
if (function_exists('teachingAssignmentsLegalSourceKeysForSchools')) {
    $consumerKeySets['teachingAssignmentsLegalSourceKeysForSchools'] = teachingAssignmentsLegalSourceKeysForSchools($schoolTypeList);
} else {
    legalSourcesContractFail($failures, 'Missing teachingAssignmentsLegalSourceKeysForSchools() consumer mapping helper');
}
if (function_exists('weeklyTimetableLegalSourceKeysForSchools')) {
    $consumerKeySets['weeklyTimetableLegalSourceKeysForSchools'] = weeklyTimetableLegalSourceKeysForSchools($schoolTypeList);
} else {
    legalSourcesContractFail($failures, 'Missing weeklyTimetableLegalSourceKeysForSchools() consumer mapping helper');
}
if (function_exists('weeklyTimetableLegalOverviewGroups')) {
    foreach (weeklyTimetableLegalOverviewGroups() as $index => $group) {
        $consumerKeySets['weeklyTimetableLegalOverviewGroups#' . $index] = $group;
    }
}

foreach ($consumerKeySets as $consumer => $keys) {
    if (!is_array($keys)) {
        legalSourcesContractFail($failures, "$consumer must return/provide an array of source keys");
        continue;
    }
    $seen = array();
    foreach ($keys as $key) {
        if (!is_string($key) || trim($key) === '') {
            legalSourcesContractFail($failures, "$consumer contains an empty/non-string source key");
            continue;
        }
        if (isset($seen[$key])) {
            legalSourcesContractFail($failures, "$consumer contains duplicate source key '$key'");
        }
        $seen[$key] = true;
        if (!isset($registry[$key])) {
            legalSourcesContractFail($failures, "$consumer references unknown legal source key '$key'");
        }
    }
}

// --- report -----------------------------------------------------------------
echo 'Legal sources contract — registry: ' . count($registry) . " entries\n";
foreach ($warnings as $warning) echo "  ⚠ $warning\n";
if (empty($failures)) {
    echo "  ✔ All legal-source contract checks passed\n";
    exit(0);
}

echo '  ✘ ' . count($failures) . " failure(s):\n";
foreach ($failures as $failure) echo "    - $failure\n";
exit(1);
