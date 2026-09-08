#!/usr/bin/env python3
from pathlib import Path
import subprocess,json,re
ROOT=Path(__file__).resolve().parents[1]
PAGE=ROOT/'ypologismos-didaktikon-anagkon.php'
SRC=PAGE.read_text(encoding='utf-8')
CSS=(ROOT/'assets/staffing-simulator.css').read_text(encoding='utf-8')
checks=[]
def check(name,cond): checks.append((name,bool(cond)))
def render(post):
    payload=json.dumps(post,ensure_ascii=False)
    php='<?php $_SERVER["REQUEST_METHOD"]="POST"; $_POST=json_decode('+json.dumps(payload,ensure_ascii=False)+', true); include "'+str(PAGE).replace('\\','/')+'";'
    p=subprocess.run(['php','-d','memory_limit=512M'],cwd=ROOT,text=True,input=php,capture_output=True)
    if p.returncode: print(p.stderr); raise SystemExit(p.returncode)
    return p.stdout
base={
'school_type':'gymnasio','school_name':'6ο ΓΥΜΝΑΣΙΟ','school_code':'2401070','gym_general_a':1,'gym_general_b':1,'gym_general_c':1,
'gym_lang_a_fr':1,'gym_lang_a_de':0,'gym_lang_a_it':0,'gym_lang_b_fr':1,'gym_lang_b_de':0,'gym_lang_b_it':0,'gym_lang_c_fr':1,'gym_lang_c_de':0,'gym_lang_c_it':0,
'gym_tech_split_a':0,'gym_tech_split_b':0,'gym_tech_split_c':0,'staffing_action':'profile','active_panel':'specialties'
}
out=render(base)
check('sixth tab exists and enabled without personnel', '>6. Κενά / πλεονάσματα ειδικοτήτων</button>' in out and 'data-staffing-tab="specialties"' in out)
check('sixth panel heading rendered', '<h2>6. Κενά / πλεονάσματα ειδικοτήτων</h2>' in out)
check('smart choice wording explains equal best assignment only', 'περισσότερους από έναν ισότιμους κλάδους στην καλύτερη ανάθεση' in out and 'Δεν επιλέγεται χαμηλότερη ανάθεση' in out)
check('gymnasium DDE special rows rendered', 'GYM_SKILLS' in out and 'ΔΕΞΙΟΤΗΤΕΣ ΓΥΜΝΑΣΙΟΥ' in out and 'GYM_TECHNOLOGY' in out and 'ΤΕΧΝΟΛΟΓΙΑ ΓΥΜΝΑΣΙΟΥ' in out)
check('internal special bucket keys are not visible as table cell labels', '>GYM_SKILLS</td>' not in out and '>GYM_TECHNOLOGY</td>' not in out)
check('special rows use normal row typography', '.specialty-balance-special{font-weight:800}' not in CSS and '.specialty-balance-special td:first-child{font-weight:800' in CSS)
check('specialty balance table overrides legacy 850px minimum width', '.specialty-balance-table{min-width:0;table-layout:fixed}' in CSS)
check('specialty balance numeric columns are compact', '.specialty-balance-table th:nth-child(3)' in CSS and 'width:10%;text-align:center' in CSS)
check('signed declaration convention visible', 'έλλειμμα με πρόσημο −, πλεόνασμα χωρίς πρόσημο' in out)
check('portable DDE export schema declared', 'staffing_balance_v1' in out and 'Λήψη CSV για ΔΔΕ' in out)
check('CSV carries school identity columns', all(x in SRC for x in ['school_code','school_name','report_key','deficit_hours','surplus_hours','balance_hours']))
check('live smart report functions exist', 'function specialtyBuildReport(state)' in SRC and 'function renderSpecialtyBalance(state)' in SRC and 'renderSpecialtyBalance(state);' in SRC)
check('live auto balance uses second specialty eligibility', 'allocationBestAssignment(allocationPeopleData[pid],allocationSlotsData[sid])' in SRC and 'secondary_specialty_code' in SRC and 'specialty_source' in SRC)
check('automatic proposal respects B limit', 'b_remaining_hours:Math.max(0,10-bHours)' in SRC and "match.priority==='B'&&(ps.b_remaining_hours||0)<need" in SRC and "if(m.priority==='B')brem[pid]-=g.need" in SRC)
check('smart choice uses top candidates not fallback', 'function specialtyTopCandidates(slot)' in SRC and 'slot.top_priority' in SRC)
check('specialty tab becomes stale after personnel edits', 'const specialtiesTab=' in SRC and 'specialtiesTab.disabled=true' in SRC)
check('school-profile edits stale specialty tab too', "['results','personnel','allocation','vacancies','specialties']" in SRC)
check('print report contains specialty balance', '<h2>Κενά / πλεονάσματα ειδικοτήτων</h2>' in out and 'id="printSpecialtyBalanceBody"' in out)
# PE02 should be chosen for shared History in this no-personnel example.
check('server initial report smart-selects PE02 for shared History', re.search(r'<tr><td>Α1</td><td>Ιστορία</td><td>2</td><td><strong>ΠΕ02</strong></td><td>ΠΕ02, ΠΕ33</td></tr>',out) is not None)
check('new geology geography vacancy prefers PE04.05 over PE04.03', re.search(r'<td>Γεωλογία - Γεωγραφία</td><td>[^<]*</td><td><strong>ΠΕ04\.05</strong></td><td>ΠΕ04\.03, ΠΕ04\.05</td>',out) is not None)
check('UI explains legacy PE04.03 handling', 'ΠΕ04.03</strong> αντιμετωπίζεται ως legacy κλάδος' in out and 'προηγείται ο <strong>ΠΕ04.05</strong>' in out)
check('live report also excludes legacy PE04.03 from new vacancy candidates', "const legacyVacancyCodes={'ΠΕ04.03':true}" in SRC and 'excluded_legacy_candidate_codes' in SRC)

failed=[n for n,ok in checks if not ok]
for n,ok in checks: print(('PASS' if ok else 'FAIL')+': '+n)
print('RESULT %d PASS / %d FAIL' % (len(checks)-len(failed),len(failed)))
raise SystemExit(1 if failed else 0)
