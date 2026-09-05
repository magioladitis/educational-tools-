#!/usr/bin/env python3
"""Contract for the server-side timetable -> assignments -> workload model."""
from pathlib import Path
import json
import subprocess

ROOT = Path(__file__).resolve().parents[1]

php = r'''
require "includes/teaching-workload-model.php";
echo json_encode(array(
  "model" => teachingWorkloadModel(),
  "summary" => teachingWorkloadModelSummary(),
  "lowercase_probe" => teachingWorkloadNormalizeText("Εικαστικό Εργαστήρι") === teachingWorkloadNormalizeText("Εικαστικό εργαστήρι")
), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
'''
raw = subprocess.check_output(['php', '-r', php], cwd=ROOT, text=True)
payload = json.loads(raw)
model = payload['model']
summary = payload['summary']

checks = []
def check(name, condition):
    checks.append((name, bool(condition)))

def by_id(instance_id):
    return next((x for x in model if x.get('instance_id') == instance_id), None)

check('2037 grade instances preserved', len(model) == 2037 and summary.get('instances') == 2037)
check('instance ids unique', len({x.get('instance_id') for x in model}) == len(model))
check('UTF-8 lowercase works without mbstring dependency', payload.get('lowercase_probe') is True)

periodic = [x for x in model if x.get('hours_mode') == 'periodic']
check('six semester-periodic timetable instances preserved', len(periodic) == 6)
check('periodic instances do not expose a misleading fixed total', all('hours_total' not in x and x.get('period_hours') for x in periodic))
check('fixed instances expose exact total hours', all(x.get('hours_total') == x.get('hours_value') for x in model if x.get('hours_mode') == 'fixed'))

expected_statuses = {
    'direct': 1792,
    'alias': 99,
    'components': 80,
    'choice_dependent': 31,
    'thematic_dependent': 6,
    'regulatory_gap': 29,
}
check('resolution status counts exact', summary.get('statuses') == expected_statuses)
check('no unresolved/ambiguous top-level instances', not any(
    x.get('resolution_status') in {'unresolved_assignment', 'ambiguous_assignment_context'}
    for x in model
))

# Direct/alias rows must resolve to a real priority-preserving assignment payload.
for x in model:
    if x.get('resolution_status') not in {'direct', 'alias'}:
        continue
    resolution = x.get('assignment_resolution') or {}
    check(f'direct resolution: {x.get("instance_id")}', resolution.get('status') == 'resolved')
    assignment = resolution.get('assignment') or {}
    check(f'direct assignment carries priority: {x.get("instance_id")}', any(
        assignment.get(k) for k in ('A', 'B', 'C', 'special_codes')
    ) or assignment.get('B_all_others') or assignment.get('C_all_others')
       or assignment.get('A_all_pe') or assignment.get('special_all_pe'))

# Combined theory/lab timetable rows stay split and their component hours remain exact.
component_rows = [x for x in model if x.get('resolution_status') == 'components']
check('80 theory/lab workload rows', len(component_rows) == 80)
for x in component_rows:
    components = x.get('components') or []
    check(f'component pair present: {x.get("instance_id")}', len(components) == 2)
    check(f'component kinds exact: {x.get("instance_id")}', {c.get('kind') for c in components} == {'theory', 'lab'})
    check(f'component hours exact: {x.get("instance_id")}', sum(int(c.get('hours', 0)) for c in components) == x.get('hours_value'))
    check(f'component assignments resolved: {x.get("instance_id")}', all(c.get('status') == 'resolved' for c in components))
check('160/160 component assignment targets resolved', summary.get('component_targets') == 160 and summary.get('component_targets_resolved') == 160)

# Every choice branch must lead to a real assignment target; total slot hours stay on the option.
choice_rows = [x for x in model if x.get('resolution_status') == 'choice_dependent']
check('31 choice-dependent workload instances', len(choice_rows) == 31)
for x in choice_rows:
    options = x.get('choice_options') or []
    check(f'choice options present: {x.get("instance_id")}', bool(options))
    for option in options:
        check(f'choice option resolved: {x.get("instance_id")} / {option.get("label")}', option.get('status') == 'resolved')
        check(f'choice option preserves slot hours: {x.get("instance_id")} / {option.get("label")}', option.get('hours_total') == x.get('hours_value'))
        check(f'choice targets present: {x.get("instance_id")} / {option.get("label")}', bool(option.get('targets')))
        check(f'choice targets resolved: {x.get("instance_id")} / {option.get("label")}', all(t.get('status') == 'resolved' for t in option.get('targets', [])))
check('173/173 choice options resolved', summary.get('choice_options') == 173 and summary.get('choice_options_resolved') == 173)

# PEPAL health group semantics survive into the workload layer.
pepal_health_a = by_id('pepal.b.health.eidiko_a@Β΄')
pepal_health_b = by_id('pepal.b.health.eidiko_b@Β΄')
check('PEPAL health slots share one choice group',
      pepal_health_a and pepal_health_b
      and pepal_health_a.get('choice_group', {}).get('id') == 'pepal.b.health.special_courses'
      and pepal_health_b.get('choice_group', {}).get('id') == 'pepal.b.health.special_courses')
check('PEPAL health requires two distinct choices',
      pepal_health_a.get('choice_group', {}).get('required') == 2
      and pepal_health_a.get('choice_group', {}).get('distinct') is True)

# Branch-specific foreign-language choices must expose only the selected branch code.
tourism = by_id('epal.g.tourism.08@Γ΄')
expected_language_codes = {'Γαλλικά': ['ΠΕ05'], 'Γερμανικά': ['ΠΕ07'], 'Ισπανικά': ['ΠΕ40'], 'Ιταλικά': ['ΠΕ34']}
actual_language_codes = {}
if tourism:
    for option in tourism.get('choice_options', []):
        target = (option.get('targets') or [{}])[0]
        actual_language_codes[option.get('label')] = (target.get('assignment') or {}).get('A')
check('EPAL tourism choice codes are branch-specific', actual_language_codes == expected_language_codes)

# ENEEGYL health: label-only choices are safely resolved against same-context
# theory/lab assignment prefixes, without inventing a component-hour split.
eneegyl_health = by_id('eneegyl.lykeio.b.health.4@Β΄')
check('ENEEGYL health all 9 options resolved', eneegyl_health and len(eneegyl_health.get('choice_options', [])) == 9 and all(o.get('status') == 'resolved' for o in eneegyl_health['choice_options']))
check('ENEEGYL health prefix bridge is explicit', eneegyl_health and all(o.get('targets_derived_from_assignment_prefix') is True for o in eneegyl_health['choice_options']))
check('ENEEGYL health does not invent component hours', eneegyl_health and all(
    len(o.get('targets', [])) == 1 or o.get('component_hours_status') == 'not_fixed_by_timetable_bridge'
    for o in eneegyl_health['choice_options']
))

# Context resolution must distinguish identical subject titles in different specialties.
mechanical = by_id('epal.g.mechanical_installations.05@Γ΄')
cooling = by_id('epal.g.cooling.02@Γ΄')
check('specialty context selects mechanical-installations assignment', mechanical and mechanical['assignment_resolution']['assignment'].get('A') == ['ΠΕ82', 'ΤΕ01.04'] and mechanical['assignment_resolution']['assignment'].get('B') == ['ΤΕ02.02'])
check('specialty context selects cooling assignment', cooling and cooling['assignment_resolution']['assignment'].get('A') == ['ΠΕ82'] and not cooling['assignment_resolution']['assignment'].get('B'))

# PEPAL A thematic blocks stay non-fractional: the legal thematic rows are linked,
# but no invented hour split per sub-unit is produced.
thematic = [x for x in model if x.get('resolution_status') == 'thematic_dependent']
check('six PEPAL A thematic blocks', len(thematic) == 6)
for x in thematic:
    check(f'thematic rows exist: {x.get("instance_id")}', bool(x.get('thematic_assignments')))
    check(f'thematic hours intentionally unsplit: {x.get("instance_id")}', x.get('component_hours_status') == 'not_fixed_by_regulation')

# Confirmed regulatory gaps remain hard stops: no assignment payload is invented.
gaps = [x for x in model if x.get('resolution_status') == 'regulatory_gap']
check('29 regulatory gaps preserved', len(gaps) == 29)
for x in gaps:
    check(f'gap has no assignment: {x.get("instance_id")}', x.get('assignment') is None)
    check(f'gap confirmed metadata: {x.get("instance_id")}', x.get('regulatory_gap', {}).get('confirmed') is True)
    check(f'gap has inference guard: {x.get("instance_id")}', bool(x.get('regulatory_gap', {}).get('inference_guard')))

# UI isolation: the new model is not loaded by either public tool.
for page in ('orologio-programma-mathimaton.php', 'anatheseis-mathimaton.php'):
    text = (ROOT / page).read_text(encoding='utf-8')
    check(f'no workload-model include in {page}', 'teaching-workload-model.php' not in text)

failed = [name for name, ok in checks if not ok]
for name, ok in checks:
    print(('PASS' if ok else 'FAIL') + ': ' + name)
print('SUMMARY ' + json.dumps(summary, ensure_ascii=False, sort_keys=True))
print(f'RESULT {len(checks)-len(failed)} PASS / {len(failed)} FAIL')
raise SystemExit(1 if failed else 0)
