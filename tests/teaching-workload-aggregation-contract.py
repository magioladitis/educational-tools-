#!/usr/bin/env python3
"""Contract for the internal workload aggregation-by-teacher-code layer."""
from pathlib import Path
import json
import subprocess

ROOT = Path(__file__).resolve().parents[1]

php = r'''
require "includes/teaching-workload-aggregation.php";
$model = teachingWorkloadModel();
$codes = array("ΠΕ03","ΠΕ87.01","ΠΕ80","ΠΕ05","ΠΕ07","ΠΕ34","ΠΕ40","ΠΕ99","ΤΕ99.99");
$aggregates = array();
foreach ($codes as $code) {
    $aggregates[$code] = teachingWorkloadAggregateByCode($code, $model);
}
echo json_encode(array(
    "summary" => teachingWorkloadAggregationSummary($model),
    "known_codes" => teachingWorkloadKnownAssignmentCodes($model),
    "aggregates" => $aggregates,
), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
'''
raw = subprocess.check_output(['php', '-r', php], cwd=ROOT, text=True)
payload = json.loads(raw)
summary = payload['summary']
known_codes = payload['known_codes']
aggs = payload['aggregates']

checks = []
def check(name, condition):
    checks.append((name, bool(condition)))

def claims(code, **criteria):
    out = []
    for claim in aggs[code]['claims']:
        if all(claim.get(k) == v for k, v in criteria.items()):
            out.append(claim)
    return out

check('76 explicit assignment codes indexed', summary == {
    'known_codes': 76,
    'claims_across_known_codes': 8968,
    'claim_categories': {
        'choice': 393,
        'condition': 140,
        'fixed': 7869,
        'periodic': 25,
        'thematic': 169,
        'variant': 372,
    },
    'regulatory_gap_instances_excluded': 29,
})
check('known code index unique', len(known_codes) == len(set(known_codes)) == 76)
check('core codes present', all(code in known_codes for code in ('ΠΕ03','ΠΕ05','ΠΕ80','ΠΕ87.01','ΤΕ01.19')))

# Fixed totals are intentionally conservative curriculum-eligibility totals.
pe03 = aggs['ΠΕ03']
check('PE03 conservative totals exact', pe03['fixed_hours_by_priority'] == {'A':190,'B':43,'C':13,'SPECIAL':28})
check('PE03 fixed total exact', pe03['fixed_hours_total'] == 274)
check('PE03 condition rows excluded from fixed total', pe03['category_counts'] == {'condition':8,'fixed':123})
check('fixed total recomputes only from fixed claims', all(
    agg['fixed_hours_total'] == sum(int(c.get('hours', 0)) for c in agg['claims'] if c.get('category') == 'fixed')
    for agg in aggs.values()
))
check('aggregation states it is not actual staffing', all(
    agg['semantics'].get('actual_staffing_hours') is False
    and agg['semantics'].get('conditional_not_summed') is True
    for agg in aggs.values()
))

# Conditional categories never masquerade as fixed hours.
for code, agg in aggs.items():
    for claim in agg['claims']:
        if claim.get('category') in {'condition','periodic','variant','choice','thematic'}:
            check(f'conditional claim not fixed: {code}/{claim.get("instance_id")}/{claim.get("category")}', claim.get('category') != 'fixed')
check('periodic claims preserve period_hours', all(
    'period_hours' in c and 'hours' not in c
    for c in aggs['ΠΕ80']['claims'] if c.get('category') == 'periodic'
))
check('variant claims carry stable scope key', all(
    c.get('variant_scope_key')
    for c in aggs['ΠΕ87.01']['claims'] if c.get('category') == 'variant'
))

# Generic regulatory family codes apply to their sub-specialties.
family_claims = [c for c in aggs['ΠΕ87.01']['claims'] if c.get('code_match_mode') == 'family' and c.get('assignment_source_code') == 'ΠΕ87']
check('PE87 generic assignments cover PE87.01', bool(family_claims))

# Component rows retain exact component hours instead of duplicating the parent slot.
nurse_theory = claims('ΠΕ87.01', instance_id='eneegyl.lykeio.d.nurse.3@Δ΄', component_kind='theory')
check('component claim resolved separately', len(nurse_theory) == 1)
check('component claim exact 2 theory hours', nurse_theory and nurse_theory[0].get('hours') == 2 and nurse_theory[0].get('priority') == 'B')

# Branch-specific language choices remain mutually exclusive and code-specific.
expected = {'ΠΕ05':'Γαλλικά','ΠΕ07':'Γερμανικά','ΠΕ34':'Ιταλικά','ΠΕ40':'Ισπανικά'}
for code, label in expected.items():
    tourism = claims(code, instance_id='epal.g.tourism.08@Γ΄')
    check(f'tourism language branch exact for {code}', [x.get('choice_label') for x in tourism] == [label])
    check(f'tourism choice not added to fixed for {code}', all(x.get('category') == 'choice' and 'hours' not in x for x in tourism))

# PEPAL Health keeps the shared 2-distinct-from-9 semantics.
health_choice = [c for c in aggs['ΠΕ87.01']['claims'] if c.get('choice_group', {}).get('id') == 'pepal.b.health.special_courses']
check('PEPAL health choice group visible to aggregation', bool(health_choice))
check('PEPAL health choice requires 2 distinct', all(c['choice_group'].get('required') == 2 and c['choice_group'].get('distinct') is True for c in health_choice))
check('choice opportunities use slot_hours, not fixed hours', all('slot_hours' in c and 'hours' not in c for c in health_choice))

# Thematic PEPAL A stays non-fractional and now preserves special_notes.
pe80_thematic = [c for c in aggs['ΠΕ80']['claims'] if c.get('category') == 'thematic']
check('PE80 has six thematic opportunities', len(pe80_thematic) == 6)
check('thematic hours remain unattributed', all(c.get('hours_attribution') == 'not_fixed_by_regulation' and 'hours' not in c for c in pe80_thematic))
check('special_notes preserved in thematic payload', any(
    any(row.get('note') == 'κύρια διαθεματική ανάθεση · Οικονομίας' for row in c.get('eligible_thematic_rows', []))
    for c in pe80_thematic
))

# Wildcards are evaluated dynamically even for a code not present in the explicit index.
check('unknown PE code receives all-PE/all-others rules dynamically', aggs['ΠΕ99']['fixed_hours_by_priority'] == {'A':9,'B':10,'C':0,'SPECIAL':28})
check('unknown TE code receives all-others but not all-PE', aggs['ΤΕ99.99']['fixed_hours_by_priority'] == {'A':0,'B':10,'C':0,'SPECIAL':0})
check('ad-hoc wildcard probes are not injected into explicit code index', 'ΠΕ99' not in known_codes and 'ΤΕ99.99' not in known_codes)

# UI isolation remains absolute.
for page in ('orologio-programma-mathimaton.php', 'anatheseis-mathimaton.php'):
    text = (ROOT / page).read_text(encoding='utf-8')
    check(f'aggregation not loaded by {page}', 'teaching-workload-aggregation.php' not in text)

failed = [name for name, ok in checks if not ok]
for name, ok in checks:
    print(('PASS' if ok else 'FAIL') + ': ' + name)
print('SUMMARY ' + json.dumps(summary, ensure_ascii=False, sort_keys=True))
print(f'RESULT {len(checks)-len(failed)} PASS / {len(failed)} FAIL')
raise SystemExit(1 if failed else 0)
