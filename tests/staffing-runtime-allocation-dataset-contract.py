#!/usr/bin/env python3
from pathlib import Path
import subprocess, json, re, html
ROOT=Path(__file__).resolve().parents[1]
PAGE=ROOT/'ypologismos-didaktikon-anagkon.php'
UI=ROOT/'includes'/'staffing-simulator-ui.js'
checks=[]
def check(name, cond): checks.append((name,bool(cond)))

def render(post):
    payload=json.dumps(post,ensure_ascii=False)
    php='<?php $_SERVER["REQUEST_METHOD"]="POST"; $_POST=json_decode('+json.dumps(payload,ensure_ascii=False)+',true); include '+repr(str(PAGE))+';'
    p=subprocess.run(['php','-d','memory_limit=512M'],cwd=ROOT,text=True,input=php,capture_output=True,timeout=30)
    if p.returncode:
        print(p.stderr); raise SystemExit(p.returncode)
    return p.stdout

personnel={
 'personnel_person_id':['pe05-user','eae-user'],
 'personnel_display_name':['ΕΚΠΑΙΔΕΥΤΙΚΟΣ ΠΕ05','ΕΚΠΑΙΔΕΥΤΙΚΟΣ ΕΑΕ'],
 'personnel_specialty_code':['ΠΕ05','ΠΕ03.50'],
 'personnel_secondary_specialty_code':['ΠΕ02',''],
 'personnel_required_teaching_hours':['20','23'],
 'personnel_service_years':['0','0'],'personnel_service_months':['0','0'],'personnel_service_days':['0','0'],
 'personnel_role':['teacher','teacher'],'personnel_assigned_external_hours':['6','0'],
 'personnel_director_sections_band':['',''],'personnel_hours_branch':['',''],
 'personnel_obligation_source':['myschool_stat4_8','myschool_stat4_8'],
 'personnel_source_base_required_hours':['20','23'],'personnel_source_reduction_hours':['0','0'],'personnel_source_hours_at_unit':['14','23'],
}
post={
 'school_type':'gymnasio','school_name':'1ο ΗΜΕΡΗΣΙΟ ΓΥΜΝΑΣΙΟ ΚΕΡΚΥΡΑΣ','school_code':'2401010',
 'gym_general_a':1,'gym_general_b':1,'gym_general_c':1,
 'gym_lang_a_fr':1,'gym_lang_a_de':0,'gym_lang_a_it':0,
 'gym_lang_b_fr':0,'gym_lang_b_de':0,'gym_lang_b_it':0,
 'gym_lang_c_fr':0,'gym_lang_c_de':0,'gym_lang_c_it':0,
 'staffing_action':'allocation','active_panel':'allocation',
 'personnel_payload_json':json.dumps(personnel,ensure_ascii=False),
 'allocation_payload_json':json.dumps({'allocation_person_id':[],'allocation_slot_id':[],'allocation_hours':[]},ensure_ascii=False),
}
out=render(post)
m=re.search(r'<script type="application/json" id="staffingRuntimeConfig">(.*?)</script>',out,re.S)
config={}
if m:
    try: config=json.loads(html.unescape(m.group(1)))
    except Exception: pass
people=config.get('allocationPeople',{})
slots=config.get('allocationSlots',{})
slot_id='gym.deyteri_xeni@Α΄|choice|1|_|section|1'
check('runtime allocation people serializes as keyed object', isinstance(people,dict))
check('runtime allocation slots serializes as keyed object', isinstance(slots,dict))
check('PE05 teacher is keyed in runtime people', 'pe05-user' in people)
check('PE03.50 EAE remains excluded from General runtime people', 'eae-user' not in people)
check('exact French A group slot exists in runtime slots', slot_id in slots)
slot=slots.get(slot_id,{})
check('exact French slot label survives runtime JSON', slot.get('label')=='Α΄ · Γαλλικά · Ομάδα 1 · 2η Ξένη Γλώσσα (Γαλλικά / Γερμανικά / Ιταλικά) · 2 ώρ.')
check('French slot has PE05 A assignment', 'ΠΕ05' in slot.get('eligible_by_priority',{}).get('A',[]))
check('PE05 teacher keeps PE05 primary and PE02 secondary', people.get('pe05-user',{}).get('specialty_code')=='ΠΕ05' and people.get('pe05-user',{}).get('secondary_specialty_code')=='ΠΕ02')

ui=UI.read_text(encoding='utf-8')
people_line=re.search(r"^\s*const allocationPeopleData=.*;$",ui,re.M)
slots_line=re.search(r"^\s*const allocationSlotsData=.*;$",ui,re.M)
check('runtime people guard exists', people_line is not None)
check('runtime slots guard exists', slots_line is not None)
check('runtime people guard accepts keyed object', people_line is not None and 'typeof staffingRuntimeConfig.allocationPeople' in people_line.group(0) and ':{}' in people_line.group(0))
check('runtime slots guard accepts keyed object', slots_line is not None and 'typeof staffingRuntimeConfig.allocationSlots' in slots_line.group(0) and ':{}' in slots_line.group(0))
check('old Array.isArray people regression removed', 'Array.isArray(staffingRuntimeConfig.allocationPeople)' not in ui)
check('old Array.isArray slots regression removed', 'Array.isArray(staffingRuntimeConfig.allocationSlots)' not in ui)

# Execute the exact production guard statements against the real rendered config.
if people_line and slots_line:
    node=(
      'const staffingRuntimeConfig='+json.dumps(config,ensure_ascii=False)+';\n'+
      people_line.group(0).strip()+'\n'+slots_line.group(0).strip()+'\n'+
      'console.log(JSON.stringify({person:!!allocationPeopleData["pe05-user"],slot:!!allocationSlotsData['+json.dumps(slot_id,ensure_ascii=False)+'],personCount:Object.keys(allocationPeopleData).length,slotCount:Object.keys(allocationSlotsData).length}));'
    )
    n=subprocess.run(['node','-e',node],cwd=ROOT,text=True,capture_output=True,timeout=10)
    try: result=json.loads(n.stdout.strip())
    except Exception: result={}
    check('production JS guard resolves PE05 teacher client-side', result.get('person') is True)
    check('production JS guard resolves exact French slot client-side', result.get('slot') is True)
    check('production JS keeps non-empty keyed datasets', result.get('personCount',0)>=1 and result.get('slotCount',0)>=1)
else:
    check('production JS guard resolves PE05 teacher client-side',False)
    check('production JS guard resolves exact French slot client-side',False)
    check('production JS keeps non-empty keyed datasets',False)

# Server-side manual allocation for the exact pair must also be valid.
manual=dict(post)
manual['allocation_payload_json']=json.dumps({
 'allocation_person_id':['pe05-user'],
 'allocation_slot_id':[slot_id],
 'allocation_hours':['2'],
},ensure_ascii=False)
manual_out=render(manual)
check('exact French PE05 teacher allocation has no unknown-slot error', 'Δεν έχει επιλεγεί έγκυρο τμήμα / ομάδα και μάθημα.' not in manual_out)
check('exact French PE05 teacher allocation has no specialty error', 'δεν έχουν ανάθεση στο συγκεκριμένο μάθημα' not in manual_out)
check('exact French PE05 teacher allocation counts two assigned hours', re.search(r'<strong data-allocation-assigned>2</strong>',manual_out) is not None)

# Exact automatic-proposal regression from the reported screen.
auto=dict(post)
auto['staffing_action']='allocation_auto'
auto_out=render(auto)
check('automatic proposal contains exact French PE05 teacher row', slot_id in auto_out and 'ΠΕ05 / 2η ΠΕ02 · ΕΚΠΑΙΔΕΥΤΙΚΟΣ ΠΕ05' in auto_out)
check('automatic proposal exact row is server-valid', 'Δεν έχει επιλεγεί έγκυρο τμήμα / ομάδα και μάθημα.' not in auto_out)

failed=[n for n,ok in checks if not ok]
for n,ok in checks: print(('PASS' if ok else 'FAIL')+': '+n)
print('RESULT %d PASS / %d FAIL' % (len(checks)-len(failed),len(failed)))
raise SystemExit(1 if failed else 0)
