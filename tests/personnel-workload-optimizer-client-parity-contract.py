#!/usr/bin/env python3
from pathlib import Path
import itertools, json, random, subprocess, sys
ROOT=Path(__file__).resolve().parents[1]
MOD=ROOT/'includes'/'personnel-workload-calculations.js'
UI=ROOT/'includes'/'staffing-simulator-ui.js'
FAIL=[]
def check(label,ok,detail=''):
    if ok: print('  ✔ '+label)
    else:
        FAIL.append(label+((': '+detail) if detail else ''))
        print('  ✘ '+label+((' — '+detail) if detail else ''))

def objective(rows):
    o={'covered':0,'top':0,'b':0,'primary':0}
    for r in rows:
        h=max(0,int(r.get('hours',0))); p=r.get('priority','')
        o['covered']+=h
        if p in ('A','SPECIAL'): o['top']+=h
        elif p=='B': o['b']+=h
        if r.get('specialty_source')=='primary': o['primary']+=h
    return o

def normalized_allocations(rows):
    return sorted((r.get('slot_id',''),r.get('person_id',''),int(r.get('hours',0)),r.get('priority',''),r.get('specialty_source',''),r.get('used_specialty_code','')) for r in rows)

def route(slot,person):
    primary=person.get('specialty_code',''); secondary=person.get('secondary_specialty_code','')
    candidates=[]
    for source,code in [('primary',primary),('secondary',secondary)]:
        if not code or (source=='secondary' and code==primary): continue
        priority=None
        for p in ('A','B','C','SPECIAL'):
            if code in slot.get('eligible_by_priority',{}).get(p,[]): priority=p; break
        if priority is not None: candidates.append(({'A':1,'SPECIAL':1,'B':2,'C':3}.get(priority,99),0 if source=='primary' else 1,priority,code,source))
    if not candidates: return None
    candidates.sort()
    _,_,p,c,s=candidates[0]
    return {'priority':p,'used_specialty_code':c,'specialty_source':s}

def brute_force(sc):
    slots=sc['slots']; people={p['person_id']:p for p in sc['people']}; pstate=sc['person_state']; sstate=sc['slot_state']
    open_slots=[sid for sid in sorted(slots) if int(sstate.get(sid,{}).get('remaining_hours',0))>0 and not sstate.get(sid,{}).get('atomic_blocked')]
    rem={pid:int(st.get('remaining_hours',0)) for pid,st in pstate.items()}
    brem={pid:int(st.get('b_remaining_hours',0)) for pid,st in pstate.items()}
    best=(-1,-1,-1,-1); best_rows=[]
    def rec(i,rows,cur):
        nonlocal best,best_rows
        if i==len(open_slots):
            key=(cur['covered'],cur['top'],cur['b'],cur['primary'])
            if key>best: best=key; best_rows=[dict(x) for x in rows]
            return
        sid=open_slots[i]; need=int(sstate[sid]['remaining_hours']); sl=slots[sid]
        # uncovered
        rec(i+1,rows,cur)
        for pid,p in people.items():
            m=route(sl,p)
            if not m or rem.get(pid,0)<need or (m['priority']=='B' and brem.get(pid,0)<need): continue
            rem[pid]-=need
            if m['priority']=='B': brem[pid]-=need
            row={'person_id':pid,'slot_id':sid,'hours':need,**m}
            rows.append(row)
            nxt=dict(cur);nxt['covered']+=need
            if m['priority'] in ('A','SPECIAL'): nxt['top']+=need
            elif m['priority']=='B': nxt['b']+=need
            if m['specialty_source']=='primary': nxt['primary']+=need
            rec(i+1,rows,nxt)
            rows.pop()
            if m['priority']=='B': brem[pid]+=need
            rem[pid]+=need
    rec(0,[],{'covered':0,'top':0,'b':0,'primary':0})
    return {'covered':best[0],'top':best[1],'b':best[2],'primary':best[3]},best_rows

module=MOD.read_text(encoding='utf-8'); ui=UI.read_text(encoding='utf-8')
check('shared module exports objective comparator','objectiveCompare: objectiveCompare' in module)
check('shared module exports objective calculator','objectiveForRows: objectiveForRows' in module)
check('shared module exports optimizer','optimizeRemaining: optimizeRemaining' in module)
check('UI delegates optimizer to shared module','PersonnelWorkloadCalculations.optimizeRemaining' in ui)
frag=ui[ui.index('function allocationOptimizeRemaining'):ui.index('function specialtyBuildReport')]
check('UI no longer owns branch-and-bound implementation','function search()' not in frag and 'new Map(dp)' not in frag)

# Golden + random deterministic scenarios.
def sx(sid,h,emap,grade='Α΄'):
    by={'A':[],'B':[],'C':[],'SPECIAL':[]}
    for code,p in emap.items(): by[p].append(code)
    top=next((p for p in ('A','SPECIAL','B','C') if by[p]),None)
    return {'slot_id':sid,'unit_id':sid,'slot_label':sid,'grade':grade,'subject':sid,'capacity_hours':h,'eligible_by_priority':by,'top_priority':top}

def scenario(name,slots,people,pstate,sstate=None):
    if sstate is None: sstate={sid:{'remaining_hours':sl['capacity_hours'],'atomic_blocked':False} for sid,sl in slots.items()}
    return {'name':name,'slots':slots,'people':people,'person_state':pstate,'slot_state':sstate}

scenarios=[]
scenarios.append(scenario('golden_6_vs_3_2_2',{
 's6':sx('s6',6,{'ΠΕ03':'A'}),'s3':sx('s3',3,{'ΠΕ03':'A'}),'s2a':sx('s2a',2,{'ΠΕ03':'A'}),'s2b':sx('s2b',2,{'ΠΕ03':'A'})},
 [{'person_id':'m','specialty_code':'ΠΕ03'}],{'m':{'remaining_hours':7,'b_assignment_hours':0,'b_remaining_hours':10}}))
scenarios.append(scenario('golden_special_beats_b',{'x':sx('x',4,{'ΠΕ01':'SPECIAL','ΠΕ02':'B'})},
 [{'person_id':'sp','specialty_code':'ΠΕ01'},{'person_id':'b','specialty_code':'ΠΕ02'}],
 {'sp':{'remaining_hours':4,'b_assignment_hours':0,'b_remaining_hours':10},'b':{'remaining_hours':4,'b_assignment_hours':0,'b_remaining_hours':10}}))
scenarios.append(scenario('golden_b_limit',{
 'b1':sx('b1',5,{'ΠΕ78':'B'}),'b2':sx('b2',5,{'ΠΕ78':'B'}),'b3':sx('b3',2,{'ΠΕ78':'B'})},
 [{'person_id':'b','specialty_code':'ΠΕ78'}],{'b':{'remaining_hours':20,'b_assignment_hours':0,'b_remaining_hours':10}}))
scenarios.append(scenario('golden_scarcity',{
 'anc':sx('anc',4,{'ΠΕ02':'A'}),'hist':sx('hist',2,{'ΠΕ02':'A','ΠΕ78':'B'})},
 [{'person_id':'phil','specialty_code':'ΠΕ02'},{'person_id':'soc','specialty_code':'ΠΕ78'}],
 {'phil':{'remaining_hours':4,'b_assignment_hours':0,'b_remaining_hours':10},'soc':{'remaining_hours':2,'b_assignment_hours':0,'b_remaining_hours':10}}))
scenarios.append(scenario('golden_secondary_route',{'x':sx('x',3,{'ΠΕ02':'A','ΠΕ03':'A'})},
 [{'person_id':'p','specialty_code':'ΠΕ03','secondary_specialty_code':'ΠΕ02'}],{'p':{'remaining_hours':3,'b_assignment_hours':0,'b_remaining_hours':10}}))
scenarios.append(scenario('golden_atomic_blocked',{'x':sx('x',4,{'ΠΕ03':'A'})},
 [{'person_id':'p','specialty_code':'ΠΕ03'}],{'p':{'remaining_hours':4,'b_assignment_hours':0,'b_remaining_hours':10}},
 {'x':{'remaining_hours':2,'atomic_blocked':True}}))
# Deliberate multiple-optimum fixture: both engines are allowed to choose a
# different teacher for s1 as long as the certified lexicographic objective is identical.
scenarios.append(scenario('golden_equivalent_optima',{
 's1':sx('s1',2,{'ΠΕ01':'A','ΠΕ03':'B','ΠΕ04.01':'C','ΠΕ80':'C'}),
 's2':sx('s2',5,{'ΠΕ03':'B','ΠΕ01':'SPECIAL','ΠΕ02':'SPECIAL','ΠΕ04.01':'SPECIAL'})},
 [{'person_id':'p1','specialty_code':'ΠΕ80'},{'person_id':'p2','specialty_code':'ΠΕ80','secondary_specialty_code':'ΠΕ02'}],
 {'p1':{'remaining_hours':10,'b_assignment_hours':5,'b_remaining_hours':5},'p2':{'remaining_hours':11,'b_assignment_hours':5,'b_remaining_hours':5}}))

# Safety-budget fixture: >28 people in one connected component must use the
# documented best-known heuristic fallback and mark the result uncertified.
fb_people=[{'person_id':f'f{i:02d}','specialty_code':'ΠΕ03'} for i in range(1,30)]
fb_state={p['person_id']:{'remaining_hours':1,'b_assignment_hours':0,'b_remaining_hours':10} for p in fb_people}
scenarios.append(scenario('fallback_large_component',{'f1':sx('f1',1,{'ΠΕ03':'A'}),'f2':sx('f2',1,{'ΠΕ03':'A'})},fb_people,fb_state))

rng=random.Random(20260926); codes=['ΠΕ01','ΠΕ02','ΠΕ03','ΠΕ04.01','ΠΕ80']; priorities=['A','SPECIAL','B','C']
for case in range(80):
    np=rng.randint(1,4); ns=rng.randint(1,8)
    people=[]; pstate={}
    for i in range(np):
        primary=rng.choice(codes); secondary=rng.choice(codes+['',''])
        if secondary==primary: secondary=''
        pid=f'p{i+1}'
        people.append({'person_id':pid,'specialty_code':primary,'secondary_specialty_code':secondary})
        rem=rng.randint(1,10); brem=rng.randint(0,min(10,rem+4)); pstate[pid]={'remaining_hours':rem,'b_assignment_hours':10-brem,'b_remaining_hours':brem}
    slots={}; sstate={}
    for j in range(ns):
        sid=f's{j+1}'; h=rng.randint(1,4); shuffled=codes[:]; rng.shuffle(shuffled); use=shuffled[:rng.randint(1,min(4,len(shuffled)))]
        emap={}; used_pr=[]
        for c in use:
            p=rng.choice(priorities); emap[c]=p; used_pr.append(p)
        slots[sid]=sx(sid,h,emap,grade=str(rng.randint(1,3)))
        sstate[sid]={'remaining_hours':h,'atomic_blocked':rng.random()<0.08}
    scenarios.append(scenario(f'random_{case:03d}',slots,people,pstate,sstate))

payload=json.dumps(scenarios,ensure_ascii=False,separators=(',',':'))
php_code=r'''require "includes/teaching-allocation-engine.php";$sc=json_decode(stream_get_contents(STDIN),true);$out=[];foreach($sc as $s){$r=teachingAllocationEngineSolveRemaining($s['slots'],$s['people'],$s['person_state'],$s['slot_state']);$out[]=['name'=>$s['name'],'allocations'=>$r['allocations'],'summary'=>$r['summary'],'people'=>$r['people'],'slots'=>$r['slots']];}echo json_encode($out,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);'''
php=subprocess.run(['php','-d','memory_limit=1024M','-r',php_code],input=payload,text=True,capture_output=True,cwd=ROOT)
if php.returncode: print(php.stderr);sys.exit(1)
php_out=json.loads(php.stdout)
node_script=r'''const fs=require('fs'),vm=require('vm');global.window=global;vm.runInThisContext(fs.readFileSync(process.argv[1],'utf8'));vm.runInThisContext(fs.readFileSync(process.argv[2],'utf8'));const sc=JSON.parse(fs.readFileSync(0,'utf8')),W=global.PersonnelWorkloadCalculations;const out=sc.map(s=>{const r=W.optimizeRemaining(s.slots,s.people,s.person_state,s.slot_state);return {name:s.name,allocations:r.allocations,summary:r.summary,people:r.people,slots:r.slots};});process.stdout.write(JSON.stringify(out));'''
node=subprocess.run(['node','-e',node_script,str(ROOT/'includes'/'specialty-code-normalization.js'),str(MOD)],input=payload,text=True,capture_output=True,cwd=ROOT)
if node.returncode: print(node.stderr);sys.exit(1)
js_out=json.loads(node.stdout)
check('PHP and JS returned same scenario count',len(js_out)==len(php_out)==len(scenarios))
parity_ok=True; certified_ok=True; invariant_ok=True; detail=''
exact_solution_count=0; equivalent_optimum_count=0
for sc,p,j in zip(scenarios,php_out,js_out):
    po=objective(p['allocations']); jo=objective(j['allocations'])
    if po!=jo:
        parity_ok=False; detail=f"{sc['name']} PHP={po} JS={jo}"; break
    if normalized_allocations(p['allocations']) == normalized_allocations(j['allocations']): exact_solution_count += 1
    else: equivalent_optimum_count += 1
    if sc['name'].startswith(('golden_','random_')) and (not p['summary'].get('maximum_coverage_certified') or not j['summary'].get('maximum_coverage_certified')):
        certified_ok=False; detail=f"uncertified {sc['name']}"; break
    # Structural invariants for JS output.
    slot_seen=set(); assigned={pid:0 for pid in sc['person_state']}; bassigned={pid:0 for pid in sc['person_state']}
    for row in j['allocations']:
        sid=row['slot_id'];pid=row['person_id'];h=int(row['hours'])
        if sid in slot_seen or sid not in sc['slots'] or pid not in sc['person_state'] or h!=int(sc['slot_state'][sid]['remaining_hours']) or sc['slot_state'][sid].get('atomic_blocked'):
            invariant_ok=False;detail=f"atomic/identity invariant {sc['name']}";break
        slot_seen.add(sid); assigned[pid]+=h
        if row.get('priority')=='B': bassigned[pid]+=h
        m=route(sc['slots'][sid],next(x for x in sc['people'] if x['person_id']==pid))
        if not m or m['priority']!=row.get('priority') or m['specialty_source']!=row.get('specialty_source'):
            invariant_ok=False;detail=f"route invariant {sc['name']}";break
    if not invariant_ok: break
    for pid in assigned:
        if assigned[pid]>int(sc['person_state'][pid]['remaining_hours']) or bassigned[pid]>int(sc['person_state'][pid]['b_remaining_hours']):
            invariant_ok=False;detail=f"capacity/B invariant {sc['name']}";break
    if not invariant_ok: break
check('88 deterministic scenarios match PHP lexicographic objective',parity_ok,detail)
check('solution parity is classified as exact vs equivalent optimum',exact_solution_count+equivalent_optimum_count==len(scenarios),f'exact={exact_solution_count} equivalent={equivalent_optimum_count}')
check('all small differential scenarios are certified by both solvers',certified_ok,detail)
check('JS optimizer preserves atomic/capacity/B/eligibility invariants',invariant_ok,detail)

# Golden expectations independent of PHP.
byname={x['name']:x for x in js_out}
check('golden 6-vs-3+2+2 covers 7',objective(byname['golden_6_vs_3_2_2']['allocations'])['covered']==7 and sorted(r['hours'] for r in byname['golden_6_vs_3_2_2']['allocations'])==[2,2,3])
check('golden SPECIAL beats B',len(byname['golden_special_beats_b']['allocations'])==1 and byname['golden_special_beats_b']['allocations'][0]['person_id']=='sp' and byname['golden_special_beats_b']['allocations'][0]['priority']=='SPECIAL')
check('golden B hard limit remains 10h',objective(byname['golden_b_limit']['allocations'])['covered']==10 and objective(byname['golden_b_limit']['allocations'])['b']==10)
check('golden scarcity case reaches 6h',objective(byname['golden_scarcity']['allocations'])['covered']==6)
check('primary wins equal-priority route over secondary',len(byname['golden_secondary_route']['allocations'])==1 and byname['golden_secondary_route']['allocations'][0]['specialty_source']=='primary')
check('atomic-blocked slot is never auto-completed',byname['golden_atomic_blocked']['allocations']==[])
eqp=phpmap['golden_equivalent_optima'] if 'phpmap' in globals() else next(x for x in php_out if x['name']=='golden_equivalent_optima')
eqj=byname['golden_equivalent_optima']
check('documented equivalent-optimum fixture has same certified objective',objective(eqp['allocations'])==objective(eqj['allocations']) and eqp['summary'].get('maximum_coverage_certified') and eqj['summary'].get('maximum_coverage_certified'))
check('equivalent-optimum fixture may differ in exact rows',normalized_allocations(eqp['allocations'])!=normalized_allocations(eqj['allocations']))
fbp=next(x for x in php_out if x['name']=='fallback_large_component'); fbj=byname['fallback_large_component']
check('large-component safety fallback is explicit on both engines',not fbp['summary'].get('maximum_coverage_certified') and not fbj['summary'].get('maximum_coverage_certified'))
check('safety fallback still preserves objective parity',objective(fbp['allocations'])==objective(fbj['allocations']))
print('  ℹ optimizer solution classification: exact=%d, equivalent-optimum=%d' % (exact_solution_count,equivalent_optimum_count))

# Independent brute-force oracle on the first 30 small random scenarios (trim to <=3 people, <=6 slots).
oracle_cases=[]
for sc in scenarios[6:]:
    if len(sc['people'])<=3 and len(sc['slots'])<=6:
        oracle_cases.append(sc)
    if len(oracle_cases)>=30: break
jsmap={x['name']:x for x in js_out}; phpmap={x['name']:x for x in php_out}
oracle_ok=True; oracle_detail=''
for sc in oracle_cases:
    oo,_=brute_force(sc); po=objective(phpmap[sc['name']]['allocations']); jo=objective(jsmap[sc['name']]['allocations'])
    if po!=oo or jo!=oo:
        oracle_ok=False;oracle_detail=f"{sc['name']} oracle={oo} PHP={po} JS={jo}";break
check('30 randomized tiny cases match independent brute-force optimum',oracle_ok,oracle_detail)

if FAIL:
    print('\nOptimizer client parity: FAIL (%d)'%len(FAIL))
    for x in FAIL: print(' - '+x)
    sys.exit(1)
print('\nOptimizer client parity: PASS')
