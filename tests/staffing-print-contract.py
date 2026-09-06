#!/usr/bin/env python3
from pathlib import Path
import json, subprocess, re
ROOT=Path(__file__).resolve().parents[1]
PAGE=ROOT/'ypologismos-didaktikon-anagkon.php'
checks=[]
def check(name,cond): checks.append((name,bool(cond)))

def render(payload, method='POST'):
    data=json.dumps(payload,ensure_ascii=False)
    php='<?php $_SERVER["REQUEST_METHOD"]='+json.dumps(method)+'; $_POST=json_decode('+json.dumps(data,ensure_ascii=False)+', true); include '+json.dumps(str(PAGE))+';'
    p=subprocess.run(['php'],cwd=ROOT,input=php,text=True,capture_output=True)
    if p.returncode:
        print(p.stderr); raise SystemExit(p.returncode)
    return p.stdout

text=PAGE.read_text(encoding='utf-8')
css=(ROOT/'assets/staffing-simulator.css').read_text(encoding='utf-8')
get=render({},'GET')
check('print button hidden before first calculation', 'id="staffingPrintButton"' not in get)
check('print report hidden before first calculation', 'id="staffingPrintReport"' not in get)

base={
 'staffing_action':'personnel','active_panel':'personnel','school_type':'gymnasio','school_name':'1ο Γυμνάσιο Δοκιμής',
 'gym_general_a':2,'gym_general_b':2,'gym_general_c':1,
 'gym_lang_a_fr':1,'gym_lang_a_de':1,'gym_lang_a_it':0,
 'gym_lang_b_fr':1,'gym_lang_b_de':1,'gym_lang_b_it':0,
 'gym_lang_c_fr':0,'gym_lang_c_de':1,'gym_lang_c_it':0,
 'personnel_person_id':['p1'],'personnel_display_name':['Μαρία Παπαδοπούλου'],
 'personnel_specialty_code':['ΠΕ03'],'personnel_required_teaching_hours':[20],
 'personnel_service_years':[0],'personnel_service_months':[0],'personnel_service_days':[0],
 'personnel_role':['teacher'],'personnel_assigned_external_hours':[2],'personnel_hours_branch':['']
}
out=render(base)
check('print button appears only after calculation', 'id="staffingPrintButton"' in out and '>Εκτύπωση</button>' in out)
check('print button is non-submit action', re.search(r'<button[^>]*type="button"[^>]*id="staffingPrintButton"',out) is not None)
check('dedicated print report exists', 'id="staffingPrintReport"' in out)
check('print report identifies school', '1ο Γυμνάσιο Δοκιμής' in out and 'σχολικό έτος 2026–2027' in out)
check('print report contains staffing matrix', '<h2>Διδακτικές ανάγκες ανά κλάδο</h2>' in out and 'class="print-matrix"' in out)
check('print report contains personnel table', '<h2>Εκπαιδευτικοί και διαθέσιμο ωράριο</h2>' in out and 'Μαρία Παπαδοπούλου' in out)
check('print report contains vacancies table', '<h2>Κενά μαθημάτων</h2>' in out and 'class="print-vacancies"' in out and 'data-print-vacancy-row' in out)
check('print output includes simulation disclaimer', 'Δεν συνιστά από μόνο του επίσημη πράξη προσδιορισμού λειτουργικών κενών' in out)
check('print click invokes browser print only', 'window.print();' in text)
check('print click does not submit or request', 'staffingPrintButton.addEventListener' in text and 'data-staffing-request-action="print"' not in text)
check('print media uses dedicated report', '@page{size:A4 landscape' in css and 'body.edu-page-staffing-simulator> *:not(.staffing-print-report):not(script)' in css)
check('explicit server request actions remain exactly four', text.count('data-staffing-request-action="')==4)

failed=[n for n,ok in checks if not ok]
for n,ok in checks: print(('PASS' if ok else 'FAIL')+': '+n)
print('RESULT %d PASS / %d FAIL' % (len(checks)-len(failed),len(failed)))
raise SystemExit(1 if failed else 0)
