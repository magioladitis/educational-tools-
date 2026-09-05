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
'personnel_required_teaching_hours':[19,22,''],
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
check('allocation tab enabled after resolved personnel', 'data-staffing-tab="allocation"' in out and '>4. Κατανομή μαθημάτων</button>' in out and 'Κατανομή μαθημάτων — επόμενο στάδιο' not in out)
check('three people summary', re.search(r'<strong>3</strong><span>εκπαιδευτικοί στο προσωρινό προσωπικό</span>',out) is not None)
# PE03 7y = 21, external 3 => 18 available
def row_window(marker):
    start=out.find('data-search=\"'+marker+'\"')
    if start<0: return ''
    nxt=out.find('data-search=\"',start+20)
    return out[start:nxt if nxt>=0 else len(out)]
pe03=row_window('ΠΕ03 Μαρία Μαθηματικού')
check('PE03 row exists', bool(pe03))
check('PE03 manual required 19 preserved', 'name="personnel_required_teaching_hours[]"' in pe03 and 'value="19"' in pe03)
check('PE03 manual input exposes max 23', re.search(r'name="personnel_required_teaching_hours\[\]"[^>]*max="23"|max="23"[^>]*name="personnel_required_teaching_hours\[\]"', pe03) is not None)
check('PE03 available 16 from manual 19 minus external 3', '<strong data-available-hours>16</strong>' in pe03)
# PE02 uses the explicitly supplied value rather than deriving it from 20 years.
pe02=row_window('ΠΕ02 Νίκος Φιλόλογος')
check('PE02 manual required 22 preserved', 'name="personnel_required_teaching_hours[]"' in pe02 and 'value="22"' in pe02)
# director: 2+2+1 = 5 normal sections => auto band 3-5; 20y => 8 hours (10-2)
pedir=row_window('ΠΕ01 Διευθυντής Δοκιμής')
check('director auto 5 sections with 20y = 8 hours', 'name="personnel_required_teaching_hours[]"' in pedir and 'value="8"' in pedir and 'readonly' in pedir)
check('director auto section info rendered', 'data-director-section-count>5</strong>' in pedir and 'κλίμακα 3-5' in pedir)
check('director band is not editable', 'personnel-director-sections' not in pedir)
check('branch summary rendered', 'Σύνοψη ανά κλάδο' in out and 'data-personnel-branch="ΠΕ03"' in out)
check('branch summary avoids vacancy/surplus conclusion', 'Δεν χαρακτηρίζει τη διαφορά ως «κενό» ή «πλεόνασμα»' in out)
check('personnel add button exists', 'id="addPersonnelRow"' in out)
check('personnel filter exists', 'id="personnelFilter"' in out)
check('personnel template exists', 'id="personnelRowTemplate"' in out)
check('shared teaching-hours JS included', 'includes/teaching-hours-calculations.js' in out)
check('client uses shared secondary calculator', 'EducationTeachingHours.secondary' in out)
check('frontend dynamically limits PE manual hours to 23', "code.indexOf('ΠΕ')===0 ? 23 : 35" in out and 'requiredInput.max=String(manualHoursMax)' in out)
check('frontend PE over-limit message is explicit', 'Για κλάδο ΠΕ το υποχρεωτικό διδακτικό ωράριο δεν μπορεί να ξεπερνά τις 23 ώρες.' in out)
check('no automatic placement action', 'Πρότεινε κατανομή' not in out and 'Αυτόματη κατανομή' not in out)

# Missing school section counts must keep a director unresolved; there is no manual band fallback in the UI.
bad=dict(base)
bad['gym_general_a']=0
bad['gym_general_b']=0
bad['gym_general_c']=0
bad['personnel_person_id']=['p4']
bad['personnel_display_name']=['Διευθυντής χωρίς τμήματα']
bad['personnel_specialty_code']=['ΠΕ02']
bad['personnel_required_teaching_hours']=['']
bad['personnel_service_years']=[10]
bad['personnel_service_months']=[0]
bad['personnel_service_days']=[0]
bad['personnel_role']=['director']
bad['personnel_assigned_external_hours']=[0]
bad['personnel_hours_branch']=['']
b=render(bad)
check('director without school sections unresolved', 'Για Διευθυντή/ντρια χρειάζονται τα δηλωμένα κανονικά τμήματα της σχολικής μονάδας.' in b)
check('unresolved count one', re.search(r'<strong>1</strong><span>εγγραφές που χρειάζονται συμπλήρωση</span>',b) is not None)

teacher_missing=dict(base)
teacher_missing['personnel_person_id']=['p5']
teacher_missing['personnel_display_name']=['Εκπαιδευτικός χωρίς ωράριο']
teacher_missing['personnel_specialty_code']=['ΠΕ03']
teacher_missing['personnel_required_teaching_hours']=['']
teacher_missing['personnel_service_years']=[30]
teacher_missing['personnel_service_months']=[0]
teacher_missing['personnel_service_days']=[0]
teacher_missing['personnel_role']=['teacher']
teacher_missing['personnel_assigned_external_hours']=[0]
teacher_missing['personnel_hours_branch']=['']
tm=render(teacher_missing)
check('ordinary teacher requires explicit hours even with service years present', 'Συμπλήρωσε το υποχρεωτικό διδακτικό ωράριο του εκπαιδευτικού.' in tm)

teacher_over=dict(base)
teacher_over['personnel_person_id']=['p6']
teacher_over['personnel_display_name']=['ΠΕ με υπερβολικό ωράριο']
teacher_over['personnel_specialty_code']=['ΠΕ03']
teacher_over['personnel_required_teaching_hours']=[24]
teacher_over['personnel_service_years']=[0]
teacher_over['personnel_service_months']=[0]
teacher_over['personnel_service_days']=[0]
teacher_over['personnel_role']=['teacher']
teacher_over['personnel_assigned_external_hours']=[0]
teacher_over['personnel_hours_branch']=['']
to=render(teacher_over)
check('backend PE manual max rejects 24 clearly', 'Για κλάδο ΠΕ το υποχρεωτικό διδακτικό ωράριο δεν μπορεί να ξεπερνά τις 23 ώρες.' in to)
check('backend preserves rejected PE value 24', 'value="24"' in row_window('ΠΕ03 Μαρία Μαθηματικού') or 'value="24"' in to)
check('DE scale removed from personnel UI', 'Κλίμακα ωραρίου ΔΕ' not in out)
check('teaching-hours calculator link present', 'href="ypologismos-didaktikou-orariou.php"' in out)

text=PAGE.read_text(encoding='utf-8')
check('frontend uses personnel workload normalize', 'personnelWorkloadNormalizePerson' in text)
check('frontend still does not auto allocate', 'personnelWorkloadRosterPlan' not in text and 'personnelWorkloadEvaluatePerson' not in text)

failed=[n for n,ok in checks if not ok]
for n,ok in checks: print(('PASS' if ok else 'FAIL')+': '+n)
print('RESULT %d PASS / %d FAIL' % (len(checks)-len(failed),len(failed)))
raise SystemExit(1 if failed else 0)
