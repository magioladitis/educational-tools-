#!/usr/bin/env python3
from pathlib import Path
import json,re,subprocess,sys
ROOT=Path(__file__).resolve().parents[1]
MOD=ROOT/'includes'/'personnel-workload-calculations.js'
PAGE=ROOT/'ypologismos-didaktikon-anagkon.php'
WORK=ROOT/'includes'/'personnel-workload.php'
ENGINE=ROOT/'includes'/'teaching-allocation-engine.php'
FAIL=[]
def check(name,ok,detail=''):
    print(('  ✔ ' if ok else '  ✘ ')+name+((' — '+detail) if detail else ''))
    if not ok: FAIL.append(name+((': '+detail) if detail else ''))

page=PAGE.read_text(encoding='utf-8'); js=MOD.read_text(encoding='utf-8'); work=WORK.read_text(encoding='utf-8'); eng=ENGINE.read_text(encoding='utf-8')
check('page ships server-generated optimizer policy', "'optimizerPolicy' => teachingAllocationEngineClientContract()" in page)
check('allocation slots ship canonical eligible_by_priority', "'eligible_by_priority'=>$slot['eligible_by_priority']" in page)
check('policy documents canonical eligibility source', "'eligibility_source'=>'server_allocation_slots.eligible_by_priority'" in work)
check('client consumes runtime optimizer policy', 'optimizerPolicyData' in (ROOT/'includes'/'staffing-simulator-ui.js').read_text(encoding='utf-8'))
check('client optimizer accepts policy input', 'function optimizeRemaining(slotsInput, peopleInput, personStateInput, slotStateInput, policyInput)' in js)
check('server objective reads policy order', "$policy['objective_order']" in eng)
check('server priority rank reads shared policy', "$policy['priority_ranks'][$priority]" in work)

# No subject/specialty assignment table is allowed inside optimizer module. Generic ΠΕ/ΤΕ/ΔΕ normalization is fine.
optimizer_part=js[js.index('  function priorityForSlotCode('):]
hardcoded=re.findall(r"['\"]((?:ΠΕ|ΤΕ|ΔΕ)\d{1,2}(?:\.\d{1,2})?)['\"]",optimizer_part)
check('optimizer contains no hard-coded specialty assignment codes', not hardcoded, ', '.join(sorted(set(hardcoded))))

# Read the PHP policy itself.
php_policy="require 'includes/teaching-allocation-engine.php'; echo json_encode(teachingAllocationEngineClientContract(),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);"
p=subprocess.run(['php','-r',php_policy],cwd=ROOT,text=True,capture_output=True)
if p.returncode: print(p.stderr);sys.exit(1)
policy=json.loads(p.stdout)
check('policy schema/version is explicit',policy.get('schema')=='staffing_optimizer_policy_v1')
check('policy algorithm is explicit',policy.get('algorithm')=='component_bnb_symmetry_v1')
check('policy objective is documented',policy.get('objective_order')==['covered','top','b','primary'])
check('policy priority ranks document SPECIAL=A',policy.get('priority_ranks')=={'A':1,'SPECIAL':1,'B':2,'C':3})
check('policy B limit is explicit',policy.get('b_limit_hours')==10)
check('equivalent-optimum semantics are explicit',policy.get('solution_equivalence')=='same_lexicographic_objective_and_invariants' and policy.get('assignment_identity_guaranteed') is False)
check('client/server safety budgets are explicit',policy.get('global_node_budget')==30000 and policy.get('component_node_limit')==12000 and policy.get('max_component_people')==28)

# Canonical data-drift fixture: Gymnasium Music includes TE16 in A after the 2026 shift.
php_fixture=r'''
require 'includes/school-profile-general-education.php';require 'includes/teaching-allocation-engine.php';
$p=schoolProfileBuildDayGymnasium2026(['profile_id'=>'policy-contract','general_sections'=>['Α΄'=>1,'Β΄'=>0,'Γ΄'=>0],
'second_foreign_language_groups'=>['Α΄'=>['Γαλλικά'=>0,'Γερμανικά'=>0,'Ιταλικά'=>0],'Β΄'=>['Γαλλικά'=>0,'Γερμανικά'=>0,'Ιταλικά'=>0],'Γ΄'=>['Γαλλικά'=>0,'Γερμανικά'=>0,'Ιταλικά'=>0]],
'technology_informatics_split_sections'=>['Α΄'=>0,'Β΄'=>0,'Γ΄'=>0],'ethics_by_grade'=>[]]);
$m=schoolProfileWorkloadMatrix($p);$slots=personnelWorkloadAllocationSlots($p,$m);$slot=null;foreach($slots as $s){if($s['subject']==='Μουσική'&&$s['grade']==='Α΄'){$slot=$s;break;}}
echo json_encode(['slot'=>$slot,'policy'=>teachingAllocationEngineClientContract()],JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
'''
p=subprocess.run(['php','-r',php_fixture],cwd=ROOT,text=True,capture_output=True)
if p.returncode: print(p.stderr);sys.exit(1)
fixture=json.loads(p.stdout); slot=fixture['slot']
check('canonical Gymnasium Music data has TE16 in A tier','ΤΕ16' in slot['eligible_by_priority'].get('A',[]))
node=r'''const fs=require('fs'),vm=require('vm');global.window=global;vm.runInThisContext(fs.readFileSync(process.argv[1],'utf8'));const x=JSON.parse(fs.readFileSync(0,'utf8'));const W=global.PersonnelWorkloadCalculations;const p={person_id:'te16',specialty_code:'ΤΕ16'};const a=W.bestAssignmentForSlot(x.slot,p,x.policy);const changed=JSON.parse(JSON.stringify(x.slot));changed.eligible_by_priority.A=(changed.eligible_by_priority.A||[]).filter(c=>c!=='ΤΕ16');changed.eligible_by_priority.B=(changed.eligible_by_priority.B||[]).concat(['ΤΕ16']);const b=W.bestAssignmentForSlot(changed,p,x.policy);const custom=Object.assign({},x.policy,{priority_ranks:Object.assign({},x.policy.priority_ranks,{A:2,B:1})});const dual={eligible_by_priority:{A:['ΠΕ01'],B:['ΠΕ02'],C:[],SPECIAL:[]}};const c=W.bestAssignmentForSlot(dual,{specialty_code:'ΠΕ01',secondary_specialty_code:'ΠΕ02'},custom);process.stdout.write(JSON.stringify({a:a,b:b,c:c}));'''
n=subprocess.run(['node','-e',node,str(MOD)],cwd=ROOT,input=json.dumps(fixture,ensure_ascii=False),text=True,capture_output=True)
if n.returncode: print(n.stderr);sys.exit(1)
out=json.loads(n.stdout)
check('client reads TE16 A tier from server slot data',out['a'] and out['a']['priority']=='A')
check('client follows a simulated TE16 tier shift without code change',out['b'] and out['b']['priority']=='B')
check('client follows server-supplied priority ranks',out['c'] and out['c']['specialty_source']=='secondary' and out['c']['priority']=='B')

if FAIL:
    print('\nOptimizer policy/source contract: FAIL (%d)'%len(FAIL))
    for x in FAIL: print(' - '+x)
    sys.exit(1)
print('\nOptimizer policy/source contract: PASS')
