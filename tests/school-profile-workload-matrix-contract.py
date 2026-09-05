#!/usr/bin/env python3
"""Contract for school-specific branch workload eligibility matrix."""
from pathlib import Path
import json, subprocess

ROOT=Path(__file__).resolve().parents[1]
php=r'''
require "includes/school-profile-workload.php";
require "includes/school-profile-eneegyl-kerkyra-2026.php";
require "includes/school-profile-eeeek-kerkyra-2026.php";
$m=teachingWorkloadModel();
$en=schoolProfileWorkloadMatrix(schoolProfileEneegylKerkyra2026(),$m);
$ee=schoolProfileWorkloadMatrix(schoolProfileEeeekKerkyra2026(),$m);
echo json_encode(array('en'=>$en,'ee'=>$ee),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
'''
payload=json.loads(subprocess.check_output(['php','-d','memory_limit=512M','-r',php],cwd=ROOT,text=True))
en=payload['en']; ee=payload['ee']
checks=[]
def check(name,cond): checks.append((name,bool(cond)))

check('ENEEGYL matrix ready', en['readiness']=='ready_for_eligibility_matrix')
check('ENEEGYL fixed curriculum hours preserved', en['summary']['realized_fixed_curriculum_hours']==396)
check('assignment units preserve fixed curriculum total', en['summary']['assignment_unit_hours']==396)
check('all fixed unit hours covered by at least one staffing leaf', en['summary']['covered_unit_hours']==396 and en['summary']['uncovered_unit_hours']==0)
check('exclusive + shared partition unit hours', en['summary']['exclusive_top_unit_hours']+en['summary']['shared_top_unit_hours']==396)
check('ordered + special partition fixed hours', en['summary']['ordered_top_unit_hours']+en['summary']['special_top_unit_hours']==396)
check('ordered exclusive/shared partition ordered hours', en['summary']['ordered_exclusive_top_unit_hours']+en['summary']['ordered_shared_top_unit_hours']==en['summary']['ordered_top_unit_hours'])
check('dependency count preserved', en['summary']['active_dependency_instances']==10)
check('regulatory gaps preserved/excluded', en['summary']['active_regulatory_gap_instances']==4 and en['summary']['active_regulatory_gap_curriculum_hours']==8)
check('matrix has staffing leaf codes', en['summary']['staffing_leaf_codes_with_claims']>0)

# Parent family codes must not appear as staffing rows when leaf branches exist.
for parent in ('ΠΕ04','ΠΕ79','ΠΕ87','ΠΕ88','ΠΕ89','ΠΕ91'):
    check(f'parent family {parent} excluded from staffing rows', parent not in en['codes'])

# Known concrete branches must appear and partition their top hours exactly.
for code in ('ΠΕ02','ΠΕ03','ΠΕ80','ΠΕ88.01','ΠΕ86'):
    row=en['codes'][code]
    check(f'{code} top partition exact', row['top_priority_hours']==row['exclusive_top_priority_hours']+row['shared_top_priority_hours'])
    check(f'{code} ordered/special partition exact', row['top_priority_hours']==row['ordered_top_priority_hours']+row['special_top_priority_hours'])
    check(f'{code} ordered exclusive/shared exact', row['ordered_top_priority_hours']==row['ordered_exclusive_top_priority_hours']+row['ordered_shared_top_priority_hours'])
    check(f'{code} no negative buckets', min(row['top_priority_hours'],row['exclusive_top_priority_hours'],row['shared_top_priority_hours'],row['fallback_hours'])>=0)

# PE03: existing school-profile aggregate had 109 all-eligible hours; new matrix must
# distinguish top/fallback rather than repeat the misleading single total.
pe03=en['codes']['ΠΕ03']
check('PE03 has both top and fallback opportunities', pe03['top_priority_hours']>0 and pe03['fallback_hours']>0)
check('PE03 eligibility total remains compatible with old 109 total', sum(pe03['eligible_hours_by_priority'].values())==109)

# At least one unit must be truly exclusive and one must be shared at top priority.
check('ENEEGYL has exclusive top hours', en['summary']['exclusive_top_unit_hours']>0)
check('ENEEGYL has shared top hours', en['summary']['shared_top_unit_hours']>0)

# EEEEK profile deliberately has no section-count inference yet; it must stay structure-only.
check('EEEEK matrix structure-only', ee['readiness']=='structure_only')
check('EEEEK no fabricated fixed curriculum hours', ee['summary']['realized_fixed_curriculum_hours']==0)
check('EEEEK no fabricated assignment units', ee['summary']['assignment_unit_count']==0 and ee['summary']['assignment_unit_hours']==0)
check('EEEEK no fabricated code rows', ee['summary']['staffing_leaf_codes_with_claims']==0 and ee['codes']==[])

# Internal-only architecture: no public page loads this layer.
for page in ('orologio-programma-mathimaton.php','anatheseis-mathimaton.php'):
    text=(ROOT/page).read_text(encoding='utf-8')
    check(f'workload matrix not loaded by {page}', 'school-profile-workload.php' not in text)

# Semantics must explicitly deny final allocation/vacancy meaning.
check('semantics deny final teacher allocation', en['semantics']['final_teacher_allocation'] is False)
check('semantics deny vacancy calculation', en['semantics']['vacancy_calculation'] is False)
check('regulatory gaps excluded by semantics', en['semantics']['regulatory_gaps_excluded'] is True)

failed=[n for n,ok in checks if not ok]
for n,ok in checks: print(('PASS' if ok else 'FAIL')+': '+n)
print('EN_SUMMARY '+json.dumps(en['summary'],ensure_ascii=False,sort_keys=True))
print('EE_SUMMARY '+json.dumps(ee['summary'],ensure_ascii=False,sort_keys=True))
print('PE03 '+json.dumps(pe03,ensure_ascii=False,sort_keys=True))
print('RESULT %d PASS / %d FAIL' % (len(checks)-len(failed),len(failed)))
raise SystemExit(1 if failed else 0)
