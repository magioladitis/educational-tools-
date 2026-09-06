#!/usr/bin/env python3
from pathlib import Path
import subprocess,json,re
ROOT=Path(__file__).resolve().parents[1]
PAGE=ROOT/'ypologismos-didaktikon-anagkon.php'
SRC=PAGE.read_text(encoding='utf-8')
checks=[]
def check(name,cond): checks.append((name,bool(cond)))
def render(post):
    payload=json.dumps(post,ensure_ascii=False)
    php='<?php $_SERVER["REQUEST_METHOD"]="POST"; $_POST=json_decode('+json.dumps(payload,ensure_ascii=False)+', true); include "'+str(PAGE).replace('\\','/')+'";'
    p=subprocess.run(['php','-d','memory_limit=512M'],cwd=ROOT,text=True,input=php,capture_output=True)
    if p.returncode: print(p.stderr); raise SystemExit(p.returncode)
    return p.stdout
base={
'school_type':'gymnasio','school_name':'Vacancy Contract Gym','gym_general_a':2,'gym_general_b':1,'gym_general_c':1,
'gym_lang_a_fr':1,'gym_lang_a_de':1,'gym_lang_a_it':0,'gym_lang_b_fr':1,'gym_lang_b_de':0,'gym_lang_b_it':0,'gym_lang_c_fr':1,'gym_lang_c_de':0,'gym_lang_c_it':0,
'gym_tech_split_a':1,'gym_tech_split_b':0,'gym_tech_split_c':0,
'staffing_action':'allocation','active_panel':'allocation',
'personnel_person_id':['p1'],'personnel_display_name':['Μαρία Μαθηματικού'],'personnel_specialty_code':['ΠΕ03'],'personnel_secondary_specialty_code':[''],
'personnel_required_teaching_hours':[21],'personnel_service_years':[7],'personnel_service_months':[0],'personnel_service_days':[0],'personnel_role':['teacher'],'personnel_assigned_external_hours':[3],'personnel_hours_branch':[''],
'allocation_person_id':['p1'],'allocation_slot_id':['gym.mathimatika@Α΄|whole|section|1'],'allocation_hours':[4]
}
out=render(base)
check('fifth tab exists', '>5. Κενά μαθημάτων</button>' in out)
check('vacancy card rendered', '<h2>5. Κενά μαθημάτων</h2>' in out)
check('vacancy tab is enabled with resolved personnel', '<button type="button" class="mode-tab" data-staffing-tab="vacancies" role="tab" aria-selected="false">5. Κενά μαθημάτων</button>' in out)
check('vacancies described as uncovered after allocation not official act', 'ώρες μαθημάτων που απομένουν ακάλυπτες μετά την τρέχουσα κατανομή' in out and 'επίσημα λειτουργικά κενά' in out)
check('live link to tab four explained', 'Ζωντανή εικόνα της Καρτέλας 4.' in out)
check('vacancy summary metrics exist', all(x in out for x in ['data-vacancy-total','data-vacancy-slots','data-vacancy-no-staff','data-vacancy-has-staff']))
check('vacancy filter exists', 'id="vacancyFilter"' in out and 'π.χ. Β1, Μαθηματικά ή ΠΕ03' in out)
check('vacancy table includes assignment branches', '<th>Κλάδοι ανάθεσης</th>' in out and '<strong>Α΄:</strong> ΠΕ03' in out)
check('fully allocated A1 math starts hidden', re.search(r'<tr data-vacancy-row="gym\.mathimatika@Α΄\|whole\|section\|1"[^>]* hidden>',out) is not None)
check('unallocated A2 math remains visible with four hours', re.search(r'<tr data-vacancy-row="gym\.mathimatika@Α΄\|whole\|section\|2"[^>]*>[\s\S]*?<span class="vacancy-hours" data-vacancy-hours>4</span>',out) is not None)
check('slots with no current eligible teacher still exist in vacancies', 'data-vacancy-row="gym.archaia_glossa@Α΄|whole|section|1"' in out)
check('client vacancy view recomputes from allocations', 'function updateVacancyView(slotAssigned,personAssigned)' in SRC and 'updateVacancyView(slotAssigned,personAssigned);' in SRC)
check('current staff check includes both specialties', 'allocationBestAssignment(person,slot)' in SRC and 'vacancyEligiblePeopleWithRemaining' in SRC)
check('personnel edits stale both allocation and vacancy tabs', "const vacanciesTab=document.querySelector('[data-staffing-tab=\"vacancies\"]');" in SRC and "vacanciesTab.disabled=true" in SRC)
check('primary assignment labels are compact', 'Α΄ ανάθεση ✓' in out and 'μέσω κύριας ειδικότητας ΠΕ03' not in out)
secondary=dict(base)
secondary.update({
'personnel_person_id':['p2'],'personnel_display_name':['Πληροφορικός / Μαθηματικός'],'personnel_specialty_code':['ΠΕ86'],'personnel_secondary_specialty_code':['ΠΕ03'],
'personnel_required_teaching_hours':[21],'personnel_service_years':[0],'personnel_assigned_external_hours':[0],
'allocation_person_id':['p2'],'allocation_slot_id':['gym.mathimatika@Α΄|whole|section|1'],'allocation_hours':[4]
})
sout=render(secondary)
check('secondary specialty remains explicit in assignment label', 'Α΄ ανάθεση · μέσω 2ης ειδικότητας ΠΕ03 ✓' in sout)

failed=[n for n,ok in checks if not ok]
for n,ok in checks: print(('PASS' if ok else 'FAIL')+': '+n)
print('RESULT %d PASS / %d FAIL' % (len(checks)-len(failed),len(failed)))
raise SystemExit(1 if failed else 0)
