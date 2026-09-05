#!/usr/bin/env python3
"""Contract for the 2026 Ethics-class formation rules and UI bridge."""
from pathlib import Path
import json
import subprocess

ROOT = Path(__file__).resolve().parents[1]

php = r'''
require "includes/ethics-class-formation.php";
$cases = array(
  "one_9" => ethicsClassFormationEvaluate("gymnasio", 1, 9, true),
  "one_10" => ethicsClassFormationEvaluate("gymnasio", 1, 10, true),
  "one_10_late" => ethicsClassFormationEvaluate("gymnasio", 1, 10, false),
  "multi_unknown" => ethicsClassFormationEvaluate("gel", 3, 14, true, null),
  "multi_dedicated" => ethicsClassFormationEvaluate("gel", 3, 14, true, 1),
  "multi_parallel" => ethicsClassFormationEvaluate("gel", 3, 14, true, 0),
  "eneegyl" => ethicsClassFormationEvaluate("eneegyl_gymnasio", 2, 12, true),
  "epal" => ethicsClassFormationEvaluate("epal", 2, 12, true),
  "music" => ethicsClassFormationEvaluate("mousiko_gymnasio", 2, 12, true, 0),
);
echo json_encode(array("policy"=>ethicsClassFormationPolicy(),"cases"=>$cases), JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
'''
payload = json.loads(subprocess.check_output(['php','-r',php], cwd=ROOT, text=True))
p = payload['policy']; c = payload['cases']
checks=[]
def check(name, cond): checks.append((name, bool(cond)))

check('decision exact', p['decision'] == '108070/Δ2/13-08-2026')
check('FEK exact', p['fek'] == 'Β΄ 5231/18-08-2026')
check('effective 2026-2027', p['effective_from_school_year'] == '2026-2027')
check('minimum exactly 10 per grade', p['minimum_exempt_students_per_grade'] == 10)
check('deadline exactly fifth day', p['deadline_day_after_classes_start'] == 5)
check('regular/evening gym-gel in scope', all(x in p['scope_school_codes'] for x in ('gymnasio','esperino_gymnasio','gel','esperino_gel')))
check('music/art gym-gel taxonomy in scope', all(x in p['scope_school_codes'] for x in ('mousiko_gymnasio','mousiko_gel','kallitexniko_gymnasio','kallitexniko_gel')))
check('vocational/special structures not auto-scoped', all(x not in p['scope_school_codes'] for x in ('epal','esperino_epal','pepal','eneegyl_gymnasio','eneegyl_lykeio')))

check('9 exempt -> fallback', c['one_9']['status']=='fallback_article_22_3' and c['one_9']['eligible'] is False and c['one_9']['ethics_groups']==0)
check('10 exempt one section -> parallel separate rooms', c['one_10']['status']=='parallel_single_section' and c['one_10']['eligible'] is True and c['one_10']['ethics_groups']==1 and c['one_10']['parallel_same_hour'] is True and c['one_10']['requires_separate_rooms'] is True)
check('10 but late -> fallback', c['one_10_late']['status']=='fallback_article_22_3' and c['one_10_late']['eligible'] is False)
check('multi section equivalence is never guessed', c['multi_unknown']['status']=='needs_equivalence_decision' and c['multi_unknown']['ethics_groups'] is None)
check('explicit equivalent section -> dedicated without section-count growth', c['multi_dedicated']['status']=='dedicated_equivalent_sections' and c['multi_dedicated']['ethics_groups']==1 and c['multi_dedicated']['total_grade_sections_change']==0)
check('explicit no equivalent -> consolidated parallel', c['multi_parallel']['status']=='consolidated_parallel' and c['multi_parallel']['ethics_groups']==1 and c['multi_parallel']['parallel_same_hour'] is True)
check('ENEEGYL guard', c['eneegyl']['status']=='scope_not_confirmed' and c['eneegyl']['eligible'] is None)
check('EPAL guard', c['epal']['status']=='scope_not_confirmed' and c['epal']['eligible'] is None)
check('Music Gym applies gymnasium family rule', c['music']['status']=='consolidated_parallel')

page=(ROOT/'orologio-programma-mathimaton.php').read_text(encoding='utf-8')
check('timetable loads ethics helper', "includes/ethics-class-formation.php" in page)
check('timetable exposes public policy only', 'ethicsClassFormationPublicPolicy()' in page)
check('timetable source card links decision', '108070/Δ2/2026' in page and '5231/2026' in page)
check('timetable note includes 10 threshold', '10 απαλλασσόμενοι/ες ανά τάξη' in page)
check('timetable note includes fifth day', 'πέμπτη ημέρα' in page)
check('timetable passes school to combiner', 'combineReligionEthics(rows, school)' in page)
check('out-of-scope UI guard present', 'δεν εφαρμόζεται αυτόματα σε αυτή τη δομή' in page)

failed=[n for n,ok in checks if not ok]
for n,ok in checks: print(('PASS' if ok else 'FAIL')+': '+n)
print(f'RESULT {len(checks)-len(failed)} PASS / {len(failed)} FAIL')
raise SystemExit(1 if failed else 0)
