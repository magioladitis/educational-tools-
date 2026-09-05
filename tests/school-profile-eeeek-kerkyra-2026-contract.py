#!/usr/bin/env python3
"""Contract for the conservative EEEEK Corfu myschool profile (2026-2027)."""
from pathlib import Path
import json
import subprocess

ROOT = Path(__file__).resolve().parents[1]
php = r'''
require "includes/school-profile.php";
require "includes/school-profile-eeeek-kerkyra-2026.php";
require_once "includes/eeeek-regulatory-data.php";
$p=schoolProfileEeeekKerkyra2026();
$r=schoolProfileRealize($p);
$defs=eeeekWorkshopDefinitions();
$wanted=array();
foreach($defs as $d){
  if(in_array($d['subject'],array('Γεωπονίας-Τροφίμων-Περιβάλλοντος','Μαγειρικής','Ζαχαροπλαστικής'),true)) $wanted[$d['subject']]=$d;
}
echo json_encode(array('profile'=>$p,'realized'=>$r,'defs'=>$wanted), JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
'''
payload=json.loads(subprocess.check_output(['php','-r',php],cwd=ROOT,text=True))
p=payload['profile']; r=payload['realized']; defs=payload['defs']
checks=[]
def check(name,cond): checks.append((name,bool(cond)))

check('profile id exact', p['profile_id']=='eeeek-kerkyra-2441001-2026-2027')
check('school year exact', p['school_year']=='2026-2027')
check('unit code exact', p['school']['unit_code']=='2441001')
check('school active', p['school']['status']=='active')
check('day operation', p['school']['operation']=='day')
check('myschool values not actual staffing', p['source']['values_are_actual_staffing_hours'] is False)
check('structural inference allowed', p['source']['values_used_for_structural_inference'] is True)
check('section count inference forbidden', p['source']['values_used_for_section_count_inference'] is False)

s=p['structures']['eeeek']
check('all six grades observed active', s['active_grades']=={'Α΄':True,'Β΄':True,'Γ΄':True,'Δ΄':True,'Ε΄':True,'ΣΤ΄':True})
check('section counts deliberately absent', s['general_sections']==[] and s['section_count_status']=='not_inferred_from_vacancy_estimates')
for grade in ('Α΄','Β΄','Γ΄','Δ΄','Ε΄','ΣΤ΄'):
    ws=s['workshop_selections'][grade]
    check(f'{grade} main workshop exact', ws['main']['regulatory_subject']=='Γεωπονίας-Τροφίμων-Περιβάλλοντος')
    check(f'{grade} second workshop myschool label exact', ws['second']['myschool_label']=='Μαγειρικής-Ζαχαροπλαστικής')
    check(f'{grade} no third workshop observed', ws['third'] is None)

check('A-D redistribution unresolved', all(s['workshop_hour_redistribution'][g]=='unresolved_no_third_workshop_observed' for g in ('Α΄','Β΄','Γ΄','Δ΄')))
check('E no third base slot', s['workshop_hour_redistribution']['Ε΄']=='not_applicable_no_third_slot_in_base_timetable')
check('ST dynamic program', s['workshop_hour_redistribution']['ΣΤ΄']=='dynamic_individualized_program')

est=s['myschool_estimated_hours']
check('A observed main/second exact', est['Α΄']['Κύρια Ειδικότητα ΕΕΕΕΚ|Γεωπονίας - Τροφίμων - Περιβάλλοντος']==30 and est['Α΄']['Β΄ Ειδικότητα ΕΕΕΕΚ|Μαγειρικής-Ζαχαροπλαστικής']==8)
check('B observed main/second exact', est['Β΄']['Κύρια Ειδικότητα ΕΕΕΕΚ|Γεωπονίας - Τροφίμων - Περιβάλλοντος']==30 and est['Β΄']['Β΄ Ειδικότητα ΕΕΕΕΚ|Μαγειρικής-Ζαχαροπλαστικής']==8)
check('C observed main/second exact', est['Γ΄']['Κύρια Ειδικότητα ΕΕΕΕΚ|Γεωπονίας - Τροφίμων - Περιβάλλοντος']==15 and est['Γ΄']['Β΄ Ειδικότητα ΕΕΕΕΚ|Μαγειρικής-Ζαχαροπλαστικής']==4)
check('D observed informatika exact', est['Δ΄']['Γενικής Παιδείας|Πληροφορική']==2)
check('E observed main/second exact', est['Ε΄']['Κύρια Ειδικότητα ΕΕΕΕΚ|Γεωπονίας - Τροφίμων - Περιβάλλοντος']==30 and est['Ε΄']['Β΄ Ειδικότητα ΕΕΕΕΚ|Μαγειρικής-Ζαχαροπλαστικής']==8)
check('ST observed main/second exact', est['ΣΤ΄']['Κύρια Ειδικότητα ΕΕΕΕΚ|Γεωπονίας - Τροφίμων - Περιβάλλοντος']==15 and est['ΣΤ΄']['Β΄ Ειδικότητα ΕΕΕΕΚ|Μαγειρικής-Ζαχαροπλαστικής']==4)
check('observed estimate totals exact', {g:sum(v.values()) for g,v in est.items()}=={'Α΄':72,'Β΄':72,'Γ΄':36,'Δ΄':35,'Ε΄':68,'ΣΤ΄':41})

check('main workshop exists in regulatory 42', 'Γεωπονίας-Τροφίμων-Περιβάλλοντος' in defs)
check('cooking candidate exists', 'Μαγειρικής' in defs)
check('pastry candidate exists', 'Ζαχαροπλαστικής' in defs)
# Safe alias only because the assignment payloads (excluding subject label) are identical.
a={k:v for k,v in defs['Μαγειρικής'].items() if k!='subject'}
b={k:v for k,v in defs['Ζαχαροπλαστικής'].items() if k!='subject'}
check('cooking/pastry assignment equivalence exact', a==b)
check('combined myschool label not invented into regulatory dataset', 'Μαγειρικής-Ζαχαροπλαστικής' not in defs)

# With no section-count inference, generic curriculum realization must remain empty.
check('no staffing realization from vacancy estimates alone', r['summary']['active_instances']==0 and r['summary']['fixed_curriculum_hours']==0)
check('ethics scope not auto-expanded to EEEEK', p['ethics']['formation_policy_scope']=='scope_not_confirmed')

# Internal-only architecture.
for page in ('orologio-programma-mathimaton.php','anatheseis-mathimaton.php'):
    text=(ROOT/page).read_text(encoding='utf-8')
    check(f'EEEΕK school profile not loaded by {page}', 'school-profile-eeeek-kerkyra-2026.php' not in text)

failed=[n for n,ok in checks if not ok]
for n,ok in checks: print(('PASS' if ok else 'FAIL')+': '+n)
print('RESULT %d PASS / %d FAIL' % (len(checks)-len(failed),len(failed)))
raise SystemExit(1 if failed else 0)
