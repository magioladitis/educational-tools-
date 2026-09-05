#!/usr/bin/env python3
from pathlib import Path
import subprocess, json, re
ROOT=Path(__file__).resolve().parents[1]
PAGE=ROOT/'ypologismos-didaktikon-anagkon.php'
checks=[]
def check(name,cond): checks.append((name,bool(cond)))

def render(post):
    payload=json.dumps(post,ensure_ascii=False)
    php='<?php $_SERVER["REQUEST_METHOD"]="POST"; $_POST=json_decode('+json.dumps(payload,ensure_ascii=False)+', true); include "'+str(PAGE).replace('\\','/')+'";'
    p=subprocess.run(['php'],cwd=ROOT,text=True,input=php,capture_output=True)
    if p.returncode:
        print(p.stderr); raise SystemExit(p.returncode)
    return p.stdout

base={
'school_type':'gymnasio','school_name':'Personnel Contract Gym',
'gym_general_a':2,'gym_general_b':2,'gym_general_c':1,
'gym_lang_a_fr':1,'gym_lang_a_de':1,'gym_lang_a_it':0,
'gym_lang_b_fr':2,'gym_lang_b_de':0,'gym_lang_b_it':0,
'gym_lang_c_fr':0,'gym_lang_c_de':0,'gym_lang_c_it':1,
'ethics_a_exempt':12,'ethics_a_timely':'1','ethics_a_equivalent':0,
'ethics_b_exempt':8,'ethics_b_timely':'1',
'ethics_c_exempt':10,'ethics_c_timely':'1',
'staffing_action':'personnel','active_panel':'personnel',
'personnel_person_id':['p1','p2','p3'],
'personnel_display_name':['Μαρία Μαθηματικού','Νίκος Φιλόλογος','Διευθυντής Δοκιμής'],
'personnel_specialty_code':['ΠΕ03','ΠΕ02','ΠΕ01'],
'personnel_service_years':[7,20,21],
'personnel_service_months':[0,0,0],
'personnel_service_days':[0,0,0],
'personnel_role':['teacher','teacher','director'],
'personnel_assigned_external_hours':[3,0,0],
'personnel_hours_branch':['','','']
}
out=render(base)
check('personnel tab active', 'data-staffing-tab="personnel"' in out and 'aria-selected="true">3. Εκπαιδευτικοί' in out)
check('personnel card rendered', '<h2>3. Εκπαιδευτικοί</h2>' in out)
check('allocation tab remains next stage', '4. Κατανομή μαθημάτων — επόμενο στάδιο' in out)
check('three people summary', re.search(r'<strong>3</strong><span>εκπαιδευτικοί στο προσωρινό προσωπικό</span>',out) is not None)
# PE03 7y = 21, external 3 => 18 available
def row_window(marker):
    start=out.find('data-search=\"'+marker+'\"')
    if start<0: return ''
    nxt=out.find('data-search=\"',start+20)
    return out[start:nxt if nxt>=0 else len(out)]
pe03=row_window('ΠΕ03 Μαρία Μαθηματικού')
check('PE03 row exists', bool(pe03))
check('PE03 required 21', '<strong data-required-hours>21</strong>' in pe03)
check('PE03 available 18', '<strong data-available-hours>18</strong>' in pe03)
# PE02 20y = 18
pe02=row_window('ΠΕ02 Νίκος Φιλόλογος')
check('PE02 twenty-year hours 18', '<strong data-required-hours>18</strong>' in pe02)
# director: 2+2+1 = 5 normal sections => auto band 3-5; 20y => 8 hours (10-2)
pedir=row_window('ΠΕ01 Διευθυντής Δοκιμής')
check('director auto 5 sections with 20y = 8 hours', '<strong data-required-hours>8</strong>' in pedir)
check('director auto section info rendered', 'data-director-section-count>5</strong>' in pedir and 'κλίμακα 3-5' in pedir)
check('director band is not editable', 'personnel-director-sections' not in pedir)
check('branch summary rendered', 'Σύνοψη ανά κλάδο' in out and 'data-personnel-branch="ΠΕ03"' in out)
check('branch summary avoids vacancy/surplus conclusion', 'Δεν χαρακτηρίζει τη διαφορά ως «κενό» ή «πλεόνασμα»' in out)
check('personnel add button exists', 'id="addPersonnelRow"' in out)
check('personnel filter exists', 'id="personnelFilter"' in out)
check('personnel template exists', 'id="personnelRowTemplate"' in out)
check('shared teaching-hours JS included', 'includes/teaching-hours-calculations.js' in out)
check('client uses shared secondary calculator', 'EducationTeachingHours.secondary' in out)
check('no automatic placement action', 'Πρότεινε κατανομή' not in out and 'Αυτόματη κατανομή' not in out)

# Missing school section counts must keep a director unresolved; there is no manual band fallback in the UI.
bad=dict(base)
bad['gym_general_a']=0
bad['gym_general_b']=0
bad['gym_general_c']=0
bad['personnel_person_id']=['p4']
bad['personnel_display_name']=['Διευθυντής χωρίς τμήματα']
bad['personnel_specialty_code']=['ΠΕ02']
bad['personnel_service_years']=[10]
bad['personnel_service_months']=[0]
bad['personnel_service_days']=[0]
bad['personnel_role']=['director']
bad['personnel_assigned_external_hours']=[0]
bad['personnel_hours_branch']=['']
b=render(bad)
check('director without school sections unresolved', 'Για Διευθυντή/ντρια χρειάζονται τα δηλωμένα κανονικά τμήματα της σχολικής μονάδας.' in b)
check('unresolved count one', re.search(r'<strong>1</strong><span>εγγραφές που χρειάζονται συμπλήρωση</span>',b) is not None)

text=PAGE.read_text(encoding='utf-8')
check('frontend uses personnel workload normalize', 'personnelWorkloadNormalizePerson' in text)
check('frontend still does not auto allocate', 'personnelWorkloadRosterPlan' not in text and 'personnelWorkloadEvaluatePerson' not in text)

failed=[n for n,ok in checks if not ok]
for n,ok in checks: print(('PASS' if ok else 'FAIL')+': '+n)
print('RESULT %d PASS / %d FAIL' % (len(checks)-len(failed),len(failed)))
raise SystemExit(1 if failed else 0)
