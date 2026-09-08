#!/usr/bin/env python3
from pathlib import Path
import subprocess, json, re
ROOT=Path(__file__).resolve().parents[1]
PAGE=ROOT/'ypologismos-didaktikon-anagkon.php'
SRC=PAGE.read_text(encoding='utf-8')
checks=[]
def check(name, cond): checks.append((name,bool(cond)))
def render(post):
    payload=json.dumps(post,ensure_ascii=False)
    php='<?php $_SERVER["REQUEST_METHOD"]="POST"; $_POST=json_decode('+json.dumps(payload,ensure_ascii=False)+', true); include "'+str(PAGE).replace('\\','/')+'";'
    p=subprocess.run(['php','-d','memory_limit=512M'],cwd=ROOT,text=True,input=php,capture_output=True)
    if p.returncode:
        print(p.stderr)
        raise SystemExit(p.returncode)
    return p.stdout

base={
    'school_type':'gymnasio','school_registry_id':'corfu-2401070','school_name':'6ο ΓΥΜΝΑΣΙΟ ΚΕΡΚΥΡΑΣ','school_code':'2401070',
    'gym_general_a':'1','gym_general_b':'1','gym_general_c':'1',
    'gym_lang_a_fr':'0','gym_lang_a_de':'0','gym_lang_a_it':'0','gym_lang_b_fr':'0','gym_lang_b_de':'0','gym_lang_b_it':'0','gym_lang_c_fr':'0','gym_lang_c_de':'0','gym_lang_c_it':'0',
    'gym_tech_split_a':'0','gym_tech_split_b':'0','gym_tech_split_c':'0',
}

# Step 1 -> 2: school profile calculation must embed the identity into the next form.
profile_post=dict(base, staffing_action='profile', active_panel='results')
first=render(profile_post)
check('profile POST renders real school code in status bar', 'id="staffingContextCode"' in first and '2401070</strong>' in first)
check('profile POST keeps code in visible school field', re.search(r'name="school_code"[^>]*value="2401070"',first) is not None)
check('personnel form carries school code forward', '<input type="hidden" name="school_code" value="2401070">' in first)
check('personnel form carries registry id forward', '<input type="hidden" name="school_registry_id" value="corfu-2401070">' in first)

# Step 3 -> 4+: simulate a real personnel submit with one teacher so the allocation
# form is rendered. Both forms must retain the school identity for the next POST.
personnel_payload={
    'personnel_person_id':['p1'],'personnel_display_name':['Δοκιμή'],'personnel_specialty_code':['ΠΕ03'],'personnel_secondary_specialty_code':[''],
    'personnel_required_teaching_hours':['23'],'personnel_service_years':['0'],'personnel_service_months':['0'],'personnel_service_days':['0'],
    'personnel_role':['teacher'],'personnel_assigned_external_hours':['0'],'personnel_director_sections_band':[''],'personnel_hours_branch':[''],
    'personnel_obligation_source':['manual'],'personnel_source_base_required_hours':['23'],'personnel_source_reduction_hours':['0'],'personnel_source_hours_at_unit':['23']
}
personnel_post=dict(base, staffing_action='personnel', active_panel='personnel', personnel_payload_json=json.dumps(personnel_payload,ensure_ascii=False))
second=render(personnel_post)
check('personnel POST still renders school code in status bar', 'id="staffingContextCode"' in second and '2401070</strong>' in second)
check('personnel POST restores school code into tab-1 field', re.search(r'name="school_code"[^>]*value="2401070"',second) is not None)
check('allocation form is available in realistic chain', 'id="staffingAllocationForm"' in second)
check('personnel and allocation forms both carry school code', len(re.findall(r'<input type="hidden" name="school_code" value="2401070">',second)) >= 2)
check('personnel and allocation forms both carry registry id', len(re.findall(r'<input type="hidden" name="school_registry_id" value="corfu-2401070">',second)) >= 2)
check('CSV export reads persistent school_code field', "document.querySelector('[name=\"school_code\"]')" in SRC and "schoolCode=codeEl?(codeEl.value||'').trim():''" in SRC)
check('school identity keys explicitly include code and registry id', "'school_type','school_registry_id','school_code','school_name'" in SRC)

failed=[n for n,ok in checks if not ok]
for n,ok in checks: print(('PASS' if ok else 'FAIL')+': '+n)
print('RESULT %d PASS / %d FAIL' % (len(checks)-len(failed),len(failed)))
raise SystemExit(1 if failed else 0)
