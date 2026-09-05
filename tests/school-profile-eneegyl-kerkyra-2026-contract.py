#!/usr/bin/env python3
"""Contract for the first concrete school profile: ENEEGYL Corfu 2026-2027."""
from pathlib import Path
import json
import subprocess

ROOT = Path(__file__).resolve().parents[1]
php = r'''
require "includes/school-profile.php";
require "includes/school-profile-eneegyl-kerkyra-2026.php";
$p=schoolProfileEneegylKerkyra2026();
$r=schoolProfileRealize($p);
$codes=array("ΠΕ03","ΠΕ80","ΠΕ88.01","ΠΕ01");
$a=array(); foreach($codes as $c){$a[$c]=schoolProfileAggregateByCode($p,$c);}
echo json_encode(array("profile"=>$p,"realized"=>$r,"aggregates"=>$a), JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
'''
payload=json.loads(subprocess.check_output(['php','-r',php],cwd=ROOT,text=True))
p=payload['profile']; r=payload['realized']; aggs=payload['aggregates']
checks=[]
def check(name,cond): checks.append((name,bool(cond)))

check('profile id exact', p['profile_id']=='eneegyl-kerkyra-2411001-2026-2027')
check('school year exact', p['school_year']=='2026-2027')
check('unit code exact', p['school']['unit_code']=='2411001')
check('school active', p['school']['status']=='active')
check('myschool values not labelled actual staffing', p['source']['values_are_actual_staffing_hours'] is False)
check('myschool values used for structural inference', p['source']['values_used_for_structural_inference'] is True)

gym=p['structures']['eneegyl_gymnasio']['general_sections']
check('Gym section profile A/B/C/D exact', gym=={'Α΄':2,'Β΄':2,'Γ΄':2,'Δ΄':1})
lyc=p['structures']['eneegyl_lykeio']
check('Lyceum common sections A/B/C/D exact', lyc['general_sections']=={'Α΄':2,'Β΄':1,'Γ΄':1,'Δ΄':1})
check('B tracks agriculture/admin exact', lyc['track_sections']['Β΄']=={'agriculture':1,'admin':1})
check('C tracks agriculture/admin exact', lyc['track_sections']['Γ΄']=={'agriculture':1,'admin':1})
check('D plant specialty exact', lyc['specialty_sections']['Δ΄']['agriculture']=={'plant':1})
check('D tourism specialty exact', lyc['specialty_sections']['Δ΄']['admin']=={'tourism':1})
choices=lyc['choice_sections']['Α΄']['eneegyl.lykeio.a.choices']
check('A Lyceum exactly three selected choices', set(choices)=={
 'eneegyl.lykeio.a.choice.oikonomia','eneegyl.lykeio.a.choice.synthesi','eneegyl.lykeio.a.choice.geoponia'})
check('each selected A choice runs in both sections', set(choices.values())=={2})
check('unselected health choice absent', 'eneegyl.lykeio.a.choice.agogi_ygeias' not in choices)
check('ENEEGYL ethics scope explicitly not confirmed', p['ethics']['formation_policy_scope']=='scope_not_confirmed')

s=r['summary']
check('realization summary exact', s=={
 'catalog_instances_considered':430,
 'active_instances':158,
 'fixed_staffing_instances':144,
 'dependency_instances':10,
 'regulatory_gap_instances':4,
 'fixed_curriculum_hours':396,
 'regulatory_gap_curriculum_hours':8,
})
slots={x['instance_id']:x for x in r['slots']}
check('A gym math realizes 6 school hours', slots['eneegyl.gym.mathimatika@Α΄']['section_count']==2 and slots['eneegyl.gym.mathimatika@Α΄']['curriculum_hours']==6)
check('B agriculture row realizes one sector section', slots['eneegyl.lykeio.b.agriculture.1@Β΄']['section_count']==1)
check('A economy choice realizes 4 hours', slots['eneegyl.lykeio.a.choice.oikonomia@Α΄']['section_count']==2 and slots['eneegyl.lykeio.a.choice.oikonomia@Α΄']['curriculum_hours']==4)
check('unselected A health course filtered out', 'eneegyl.lykeio.a.choice.agogi_ygeias@Α΄' not in slots)
check('inactive health sector filtered out', not any(x.get('track')=='health' for x in r['slots']))
check('religion/ethics slots guarded, not fixed staffing', all(
 x['staffing_status']=='dependency_unresolved' and x['dependency']['status']=='ethics_policy_scope_not_confirmed'
 for x in r['slots'] if x.get('slot_id','').endswith('religion_ethics')
))
check('active regulatory gaps stay hard stops', all(x['staffing_status']=='regulatory_gap' for x in r['slots'] if x['resolution_status']=='regulatory_gap'))
# Full curriculum reconstruction: fixed resolved + regulatory gaps + one non-duplicated religion/ethics slot = 417.
religion_slot_hours=13
check('structural curriculum total reconstructs to 417', s['fixed_curriculum_hours']+s['regulatory_gap_curriculum_hours']+religion_slot_hours==417)

pe80=aggs['ΠΕ80']
check('school-specific aggregate semantics safe', pe80['semantics']=={
 'school_specific':True,'based_on_profile':True,'actual_teacher_assignment':False,'unresolved_dependencies_excluded':True})
econ=[x for x in pe80['claims'] if x['instance_id']=='eneegyl.lykeio.a.choice.oikonomia@Α΄']
check('PE80 sees 4 school-specific hours in selected economy choice', len(econ)==1 and econ[0]['school_hours']==4 and econ[0]['school_section_count']==2)
check('PE03 school aggregate exact current snapshot', aggs['ΠΕ03']['fixed_hours_total']==109)

# Internal-only architecture must not leak profile into existing public pages.
for page in ('orologio-programma-mathimaton.php','anatheseis-mathimaton.php'):
    text=(ROOT/page).read_text(encoding='utf-8')
    check(f'school profile not loaded by {page}', 'includes/school-profile.php' not in text and 'school-profile-eneegyl-kerkyra-2026.php' not in text)

failed=[n for n,ok in checks if not ok]
for n,ok in checks: print(('PASS' if ok else 'FAIL')+': '+n)
print('SUMMARY '+json.dumps(s,ensure_ascii=False,sort_keys=True))
print(f'RESULT {len(checks)-len(failed)} PASS / {len(failed)} FAIL')
raise SystemExit(1 if failed else 0)
