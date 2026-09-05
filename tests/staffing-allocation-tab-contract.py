#!/usr/bin/env python3
from pathlib import Path
import subprocess,json,re
ROOT=Path(__file__).resolve().parents[1]
PAGE=ROOT/'ypologismos-didaktikon-anagkon.php'
checks=[]
def check(name,cond): checks.append((name,bool(cond)))
def render(post):
    payload=json.dumps(post,ensure_ascii=False)
    php='<?php $_SERVER["REQUEST_METHOD"]="POST"; $_POST=json_decode('+json.dumps(payload,ensure_ascii=False)+', true); include "'+str(PAGE).replace('\\','/')+'";'
    p=subprocess.run(['php','-d','memory_limit=512M'],cwd=ROOT,text=True,input=php,capture_output=True)
    if p.returncode: print(p.stderr); raise SystemExit(p.returncode)
    return p.stdout
base={
'school_type':'gymnasio','school_name':'Allocation Contract Gym','gym_general_a':2,'gym_general_b':1,'gym_general_c':1,
'gym_lang_a_fr':1,'gym_lang_a_de':1,'gym_lang_a_it':0,'gym_lang_b_fr':1,'gym_lang_b_de':0,'gym_lang_b_it':0,'gym_lang_c_fr':1,'gym_lang_c_de':0,'gym_lang_c_it':0,
'gym_tech_split_a':1,'gym_tech_split_b':0,'gym_tech_split_c':0,
'staffing_action':'allocation','active_panel':'allocation',
'personnel_person_id':['p1','p2'],'personnel_display_name':['Μαρία Μαθηματικού','Νίκος Φιλόλογος'],'personnel_specialty_code':['ΠΕ03','ΠΕ02'],
'personnel_service_years':[7,20],'personnel_service_months':[0,0],'personnel_service_days':[0,0],'personnel_role':['teacher','teacher'],'personnel_assigned_external_hours':[3,0],'personnel_hours_branch':['',''],
'allocation_person_id':['p1'],'allocation_slot_id':['gym.mathimatika@Α΄|whole|section|1'],'allocation_hours':[4]
}
out=render(base)
check('allocation tab active', 'data-staffing-tab="allocation"' in out and 'aria-selected="true">4. Κατανομή μαθημάτων' in out)
check('allocation card rendered', '<h2>4. Κατανομή μαθημάτων</h2>' in out)
check('manual not automatic note', 'Χειροκίνητη κατανομή με αυτόματο έλεγχο — όχι αυτόματη τοποθέτηση.' in out)
check('A1 math option visible', 'Α1 · Μαθηματικά · 4 ώρ.' in out)
check('split group safe label visible', 'πρόσθετη ομάδα χωρισμού 1 · Πληροφορική' in out)
check('valid A assignment status', 'Α΄ ανάθεση ✓' in out)
check('assigned summary four', re.search(r'<strong data-allocation-assigned>4</strong>',out) is not None)
check('person assigned four', re.search(r'data-allocation-person-summary="p1"[\s\S]*?<strong data-person-assigned>4</strong>',out) is not None)
check('person remaining 14 after external 3 and allocation 4', re.search(r'data-allocation-person-summary="p1"[\s\S]*?<strong data-person-remaining>14</strong>',out) is not None)
check('add allocation button exists', 'id="addAllocationRow"' in out)
check('allocation template exists', 'id="allocationRowTemplate"' in out)
check('server slot plan used', 'personnelWorkloadRosterSlotPlan' in PAGE.read_text(encoding='utf-8'))
check('live validation data embedded', 'allocationPeopleData=' in out and 'allocationSlotsData=' in out)
check('eligible slot filter wired', 'allocationPopulateSlotsForPerson' in PAGE.read_text(encoding='utf-8'))
check('personnel changes stale allocation tab', 'markPersonnelDirty' in PAGE.read_text(encoding='utf-8'))
check('sidebar marks manual allocation ready', '<span>Χειροκίνητη κατανομή μαθημάτων</span><strong>✓</strong>' in out)
check('automatic placements still disabled', '<span>Αυτόματες τοποθετήσεις</span><strong>Όχι ακόμη</strong>' in out and 'Πρότεινε κατανομή' not in out)

bad=dict(base)
bad['allocation_person_id']=['p2']
bad['allocation_slot_id']=['gym.mathimatika@Α΄|whole|section|1']
bad['allocation_hours']=[4]
b=render(bad)
check('ineligible PE02 math shown as error', 'Ο κλάδος του εκπαιδευτικού δεν έχει ανάθεση στο συγκεκριμένο μάθημα.' in b)
check('invalid row counter one', re.search(r'<strong data-allocation-errors>1</strong>',b) is not None)

over=dict(base)
over['allocation_person_id']=['p1','p1']
over['allocation_slot_id']=['gym.mathimatika@Α΄|whole|section|1','gym.mathimatika@Α΄|whole|section|1']
over['allocation_hours']=[4,4]
o=render(over)
check('same slot over-allocation surfaced', 'Το ίδιο τμήμα / ομάδα έχει κατανεμηθεί πάνω από τις διαθέσιμες ώρες του.' in o)
check('slot over summary four', re.search(r'<strong data-allocation-over>4</strong>',o) is not None)

failed=[n for n,ok in checks if not ok]
for n,ok in checks: print(('PASS' if ok else 'FAIL')+': '+n)
print('RESULT %d PASS / %d FAIL' % (len(checks)-len(failed),len(failed)))
raise SystemExit(1 if failed else 0)
