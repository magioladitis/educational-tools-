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
'personnel_person_id':['p1','p2'],'personnel_display_name':['Μαρία Μαθηματικού','Νίκος Φιλόλογος'],'personnel_specialty_code':['ΠΕ03','ΠΕ02'],'personnel_secondary_specialty_code':['',''],
'personnel_required_teaching_hours':[21,18],
'personnel_service_years':[7,20],'personnel_service_months':[0,0],'personnel_service_days':[0,0],'personnel_role':['teacher','teacher'],'personnel_assigned_external_hours':[3,0],'personnel_hours_branch':['',''],
'allocation_person_id':['p1'],'allocation_slot_id':['gym.mathimatika@Α΄|whole|section|1'],'allocation_hours':[4]
}
out=render(base)
check('allocation tab active', 'data-staffing-tab="allocation"' in out and 'aria-selected="true">4. Κατανομή μαθημάτων' in out)
check('allocation card rendered', '<h2>4. Κατανομή μαθημάτων</h2>' in out)
check('manual not automatic note', 'Χειροκίνητη κατανομή με αυτόματο έλεγχο — όχι αυτόματη τοποθέτηση.' in out)
check('A1 math option visible', 'Α1 · Μαθηματικά · 4 ώρ.' in out)
check('split group safe label visible', 'πρόσθετη ομάδα χωρισμού 1 · Πληροφορική' in out)
check('valid primary A assignment uses compact label', 'Α΄ ανάθεση ✓' in out and 'μέσω κύριας ειδικότητας ΠΕ03' not in out)
check('assigned summary four', re.search(r'<strong data-allocation-assigned>4</strong>',out) is not None)
check('person assigned four', re.search(r'data-allocation-person-summary="p1"[\s\S]*?<strong data-person-assigned>4</strong>',out) is not None)
check('person remaining 14 after external 3 and allocation 4', re.search(r'data-allocation-person-summary="p1"[\s\S]*?<strong data-person-remaining>14</strong>',out) is not None)
check('person summary shows required A and B metrics', re.search(r'data-allocation-person-summary="p1"[\s\S]*?<strong data-person-required>21</strong>[\s\S]*?<strong data-person-a>4</strong>[\s\S]*?<strong data-person-b class="">0/10</strong>',out) is not None)
check('two synchronized allocation views exist', 'data-allocation-view="slots"' in out and 'Ανά μάθημα / τμήμα' in out and 'data-allocation-view="people"' in out and 'Ανά εκπαιδευτικό' in out)
check('add allocation button exists', 'id="addAllocationRow"' in out)
check('allocation template exists', 'id="allocationRowTemplate"' in out)
check('server slot plan used', 'personnelWorkloadRosterSlotPlan' in PAGE.read_text(encoding='utf-8'))
check('live validation data embedded', 'allocationPeopleData=' in out and 'allocationSlotsData=' in out)
check('eligible slot filter wired', 'allocationPopulateSlotsForPerson' in PAGE.read_text(encoding='utf-8'))
check('personnel changes stale allocation tab', 'markPersonnelDirty' in PAGE.read_text(encoding='utf-8'))
check('sidebar marks manual allocation ready', '<span>Χειροκίνητη κατανομή μαθημάτων</span><strong>✓</strong>' in out)
check('automatic placements still disabled', '<span>Αυτόματες τοποθετήσεις</span><strong>Όχι ακόμη</strong>' in out and 'Πρότεινε κατανομή' not in out)
check('personnel rows use compact required-hours label', '<label title="Υποχρεωτικό ωράριο">Υ.Ω.</label>' in out and 'Υ.Ω. = Υποχρεωτικό ωράριο.' in out)
slot_selects=re.findall(r'<select name="allocation_slot_id\[\]" class="allocation-slot">([\s\S]*?)</select>',out)
check('slot selectors omit lessons with no eligible teacher', bool(slot_selects) and all('Θρησκευτικά' not in block for block in slot_selects))
check('hidden no-eligible slots are explained without removing their uncovered hours', 'χωρίς επιλέξιμο εκπαιδευτικό' in out and 'Οι ώρες τους εξακολουθούν να υπολογίζονται στις ακάλυπτες ώρες.' in out)
check('fully covered slots become unavailable in other allocation rows', 'function updateAllocationSlotOptionAvailability(slotAssigned)' in PAGE.read_text(encoding='utf-8') and "dynamicallyDisabled=full&&current!==sid" in PAGE.read_text(encoding='utf-8') and '· καλύφθηκε' in PAGE.read_text(encoding='utf-8'))
check('allocation UI explains automatic slot deactivation', 'η επιλογή του γίνεται αυτόματα ανενεργή στις υπόλοιπες γραμμές κατανομής' in out)

bad=dict(base)
bad['allocation_person_id']=['p2']
bad['allocation_slot_id']=['gym.mathimatika@Α΄|whole|section|1']
bad['allocation_hours']=[4]
b=render(bad)
check('ineligible PE02 math shown as error', 'Οι δηλωμένες ειδικότητες του εκπαιδευτικού δεν έχουν ανάθεση στο συγκεκριμένο μάθημα.' in b)
check('invalid row counter one', re.search(r'<strong data-allocation-errors>1</strong>',b) is not None)

over=dict(base)
over['allocation_person_id']=['p1','p1']
over['allocation_slot_id']=['gym.mathimatika@Α΄|whole|section|1','gym.mathimatika@Α΄|whole|section|1']
over['allocation_hours']=[4,4]
o=render(over)
check('same slot over-allocation surfaced', 'Το ίδιο τμήμα / ομάδα έχει κατανεμηθεί πάνω από τις διαθέσιμες ώρες του.' in o)
check('slot over summary four', re.search(r'<strong data-allocation-over>4</strong>',o) is not None)
check('overallocated rows are not counted as valid assigned hours', re.search(r'<strong data-allocation-assigned>0</strong>',o) is not None and re.search(r'data-allocation-person-summary="p1"[\s\S]*?<strong data-person-assigned>0</strong>',o) is not None)


secondary=dict(base)
secondary['personnel_person_id']=['p3']
secondary['personnel_display_name']=['Πληροφορικός με δεύτερη Μαθηματικού']
secondary['personnel_specialty_code']=['ΠΕ86']
secondary['personnel_secondary_specialty_code']=['ΠΕ03']
secondary['personnel_required_teaching_hours']=[21]
secondary['personnel_service_years']=[0]
secondary['personnel_service_months']=[0]
secondary['personnel_service_days']=[0]
secondary['personnel_role']=['teacher']
secondary['personnel_assigned_external_hours']=[0]
secondary['personnel_hours_branch']=['']
secondary['allocation_person_id']=['p3']
secondary['allocation_slot_id']=['gym.mathimatika@Α΄|whole|section|1']
secondary['allocation_hours']=[4]
sout=render(secondary)
check('secondary specialty unlocks math allocation', 'Α΄ ανάθεση · μέσω 2ης ειδικότητας ΠΕ03 ✓' in sout)
check('secondary specialty source appears in person summary', 'μέσω 2ης ΠΕ03: 4 ώρ.' in sout)

blimit=dict(base)
blimit.update({
 'gym_general_a':3,'gym_general_b':3,'gym_general_c':3,
 'gym_lang_a_fr':1,'gym_lang_a_de':1,'gym_lang_a_it':0,
 'gym_lang_b_fr':1,'gym_lang_b_de':1,'gym_lang_b_it':0,
 'gym_lang_c_fr':1,'gym_lang_c_de':1,'gym_lang_c_it':0,
 'gym_tech_split_a':0,'gym_tech_split_b':0,'gym_tech_split_c':0,
 'personnel_person_id':['pb'],'personnel_display_name':['Συνδυασμένη Β΄'],'personnel_specialty_code':['ΠΕ86'],'personnel_secondary_specialty_code':['ΠΕ80'],
 'personnel_required_teaching_hours':[23],'personnel_service_years':[0],'personnel_service_months':[0],'personnel_service_days':[0],'personnel_role':['teacher'],'personnel_assigned_external_hours':[0],'personnel_hours_branch':[''],
 'allocation_person_id':['pb','pb','pb','pb'],
 'allocation_slot_id':['gym.mathimatika@Α΄|whole|section|1','gym.kpa@Γ΄|whole|section|1','gym.kpa@Γ΄|whole|section|2','gym.kpa@Γ΄|whole|section|3'],
 'allocation_hours':[4,3,3,3]
})
bo=render(blimit)
bwarning='Οι ώρες μαθημάτων Β΄ ανάθεσης, από τη βασική και τη δεύτερη ειδικότητα συνολικά, υπερβαίνουν το όριο των 10 διδακτικών ωρών. Υπέρβαση επιτρέπεται μόνο κατ’ εξαίρεση, ύστερα από απόφαση ΠΥΣΔΕ και υπό τις προβλεπόμενες προϋποθέσεις.'
check('B assignment limit is strong warning not error', bwarning in bo and re.search(r'<strong data-allocation-errors>0</strong>',bo) is not None)
check('B assignment summary combines primary and secondary hours', '<strong data-person-b class="b-limit-over">13/10</strong>' in bo and 'Μέσω κύριας ΠΕ86: 4 ώρ. · μέσω 2ης ΠΕ80: 9 ώρ.' in bo)
check('secondary B rows record used specialty', 'Β΄ ανάθεση · μέσω 2ης ειδικότητας ΠΕ80' in bo)

failed=[n for n,ok in checks if not ok]
for n,ok in checks: print(('PASS' if ok else 'FAIL')+': '+n)
print('RESULT %d PASS / %d FAIL' % (len(checks)-len(failed),len(failed)))
raise SystemExit(1 if failed else 0)
