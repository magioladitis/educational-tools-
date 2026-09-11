#!/usr/bin/env python3
from pathlib import Path
import subprocess, json, re
ROOT=Path(__file__).resolve().parents[1]
PAGE=ROOT/'ypologismos-didaktikon-anagkon.php'
UI=ROOT/'includes'/'staffing-simulator-ui.js'
checks=[]
def check(name,cond): checks.append((name,bool(cond)))

def render(post, timeout=25):
    payload=json.dumps(post,ensure_ascii=False)
    php='<?php $_SERVER["REQUEST_METHOD"]="POST"; $_POST=json_decode('+json.dumps(payload,ensure_ascii=False)+', true); include "'+str(PAGE).replace('\\','/')+'";'
    p=subprocess.run(['php','-d','memory_limit=512M'],cwd=ROOT,text=True,input=php,capture_output=True,timeout=timeout)
    if p.returncode:
        print(p.stderr); raise SystemExit(p.returncode)
    return p.stdout

text=(PAGE.read_text(encoding='utf-8')+'\n'+UI.read_text(encoding='utf-8'))
check('compact personnel JSON parser exists', "staffingUiPayloadArrays('personnel_payload_json')" in text)
check('compact allocation JSON parser exists', "staffingUiPayloadArrays('allocation_payload_json')" in text)
check('cross-tab personnel state is emitted as one JSON hidden input', 'name="personnel_payload_json" value="' in text and "json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)" in text)
check('cross-tab allocation state is emitted as one JSON hidden input', 'name="allocation_payload_json" value="' in text)
check('client compacts repeated form controls before submit', 'function compactRepeatedFormState(form,prefix,payloadName)' in text and "compactRepeatedFormState(form,'personnel_','personnel_payload_json')" in text and "compactRepeatedFormState(form,'allocation_','allocation_payload_json')" in text)
check('client preserves disabled placeholders before disabling repeated controls', "payload[key].push(el.disabled?'':el.value);" in text and 'fields.forEach(function(el){el.disabled=true;});' in text)
check('director section band survives compact cross-tab state', "'personnel_director_sections_band'=>array()" in text and "$payload['personnel_director_sections_band'][]" in text)

# 100 people × 16 roster fields would exceed the common PHP max_input_vars=1000
# if sent as ordinary repeated POST variables.  The JSON payload must preserve all rows.
n=100
personnel_payload={
    'personnel_person_id':[f'p{i}' for i in range(n)],
    'personnel_display_name':[f'Εκπαιδευτικός {i}' for i in range(n)],
    'personnel_specialty_code':['ΠΕ03']*n,
    'personnel_secondary_specialty_code':['']*n,
    'personnel_required_teaching_hours':['21']*n,
    'personnel_service_years':['0']*n,
    'personnel_service_months':['0']*n,
    'personnel_service_days':['0']*n,
    'personnel_role':['teacher']*n,
    'personnel_assigned_external_hours':['0']*n,
    'personnel_director_sections_band':['']*n,
    'personnel_hours_branch':['']*n,
    'personnel_obligation_source':['manual']*n,
    'personnel_source_base_required_hours':['']*n,
    'personnel_source_reduction_hours':['']*n,
    'personnel_source_hours_at_unit':['']*n,
}
base={
    'school_type':'gymnasio','school_name':'Compact POST Contract',
    'gym_general_a':1,'gym_general_b':1,'gym_general_c':1,
    'gym_lang_a_fr':1,'gym_lang_a_de':0,'gym_lang_a_it':0,
    'gym_lang_b_fr':1,'gym_lang_b_de':0,'gym_lang_b_it':0,
    'gym_lang_c_fr':1,'gym_lang_c_de':0,'gym_lang_c_it':0,
    'staffing_action':'personnel','active_panel':'personnel',
    'personnel_payload_json':json.dumps(personnel_payload,ensure_ascii=False),
}
out=render(base)
check('100-row compact roster survives one POST variable', re.search(r'<strong>100</strong><span>εκπαιδευτικοί στο προσωρινό προσωπικό</span>',out) is not None)
check('last compact roster row survives parsing', 'Εκπαιδευτικός 99' in out)

# Cross-tab state should be compact in rendered HTML: one personnel payload rather
# than 16 hidden fields per teacher.  The visible personnel form itself still uses
# [] controls by design and is compacted in JS immediately before submit.
allocation_post=dict(base)
allocation_post['staffing_action']='allocation'
allocation_post['active_panel']='allocation'
aout=render(allocation_post)
allocation_form_start=aout.find('id="staffingAllocationForm"')
allocation_form_end=aout.find('</form>', allocation_form_start)
allocation_form=aout[allocation_form_start:allocation_form_end]
check('allocation form carries roster in one compact hidden payload', allocation_form.count('name="personnel_payload_json"')==1)
check('allocation form does not duplicate roster as repeated hidden arrays', 'type="hidden" name="personnel_person_id[]"' not in allocation_form and 'type="hidden" name="personnel_specialty_code[]"' not in allocation_form)

# 350 allocation rows × 3 controls would also exceed max_input_vars=1000.
# They are intentionally the same slot here: validation should reject them, but
# all rows must reach PHP and remain visible for correction.
allocation_count=350
large_alloc=dict(base)
large_alloc['staffing_action']='allocation'
large_alloc['active_panel']='allocation'
large_alloc['personnel_payload_json']=json.dumps({
    'personnel_person_id':['p-main'],
    'personnel_display_name':['Μαθηματικός'],
    'personnel_specialty_code':['ΠΕ03'],
    'personnel_secondary_specialty_code':[''],
    'personnel_required_teaching_hours':['21'],
    'personnel_service_years':['0'],
    'personnel_service_months':['0'],
    'personnel_service_days':['0'],
    'personnel_role':['teacher'],
    'personnel_assigned_external_hours':['0'],
    'personnel_director_sections_band':[''],
    'personnel_hours_branch':[''],
    'personnel_obligation_source':['manual'],
    'personnel_source_base_required_hours':[''],
    'personnel_source_reduction_hours':[''],
    'personnel_source_hours_at_unit':[''],
},ensure_ascii=False)
large_alloc['allocation_payload_json']=json.dumps({
    'allocation_person_id':['p-main']*allocation_count,
    'allocation_slot_id':['gym.mathimatika@Α΄|whole|section|1']*allocation_count,
    'allocation_hours':['1']*allocation_count,
},ensure_ascii=False)
large_out=render(large_alloc, timeout=30)
check('350-row compact allocation survives one POST variable', large_out.count('name="allocation_hours[]"') >= allocation_count)
check('all 350 compact allocation rows reach validation', re.search(r'<strong data-allocation-errors>350</strong>',large_out) is not None)

failed=[n for n,ok in checks if not ok]
for n,ok in checks: print(('PASS' if ok else 'FAIL')+': '+n)
print('RESULT %d PASS / %d FAIL' % (len(checks)-len(failed),len(failed)))
raise SystemExit(1 if failed else 0)
