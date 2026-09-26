#!/usr/bin/env python3
from pathlib import Path
import json, subprocess, sys
ROOT=Path(__file__).resolve().parents[1]
MOD=ROOT/'includes/personnel-workload-calculations.js'
FAIL=[]
def check(name,ok,detail=''):
    print(('  ✔ ' if ok else '  ✘ ')+name+((' — '+detail) if detail else ''))
    if not ok: FAIL.append(name+((': '+detail) if detail else ''))
def obj(rows):
    o={'covered':0,'top':0,'b':0,'primary':0}
    for r in rows:
        h=int(r.get('hours',0));p=r.get('priority','');o['covered']+=h
        if p in ('A','SPECIAL'):o['top']+=h
        elif p=='B':o['b']+=h
        if r.get('specialty_source')=='primary':o['primary']+=h
    return o
php=r'''
require "includes/teaching-allocation-engine.php";
require "includes/school-profile-general-education.php";
function mkstate($slots,$people,$hours){$ps=[];foreach($people as $p){$id=$p['person_id'];$h=isset($hours[$id])?$hours[$id]:8;$ps[$id]=['remaining_hours'=>$h,'b_assignment_hours'=>0,'b_remaining_hours'=>10];}$ss=[];foreach($slots as $sid=>$s)$ss[$sid]=['remaining_hours'=>(int)$s['capacity_hours'],'atomic_blocked'=>false];return [$ps,$ss];}
$gym=schoolProfileBuildDayGymnasium2026([
 'profile_id'=>'phase3-real-gym','general_sections'=>['Α΄'=>1,'Β΄'=>1,'Γ΄'=>1],
 'second_foreign_language_groups'=>['Α΄'=>['Γαλλικά'=>1,'Γερμανικά'=>0,'Ιταλικά'=>0],'Β΄'=>['Γαλλικά'=>0,'Γερμανικά'=>1,'Ιταλικά'=>0],'Γ΄'=>['Γαλλικά'=>0,'Γερμανικά'=>0,'Ιταλικά'=>1]],
 'technology_informatics_split_sections'=>['Α΄'=>0,'Β΄'=>0,'Γ΄'=>0],'ethics_by_grade'=>[]]);
$gymA=schoolProfileBuildDayGymnasium2026([
 'profile_id'=>'phase3-real-gym-a','general_sections'=>['Α΄'=>1,'Β΄'=>0,'Γ΄'=>0],
 'second_foreign_language_groups'=>['Α΄'=>['Γαλλικά'=>0,'Γερμανικά'=>0,'Ιταλικά'=>0],'Β΄'=>['Γαλλικά'=>0,'Γερμανικά'=>0,'Ιταλικά'=>0],'Γ΄'=>['Γαλλικά'=>0,'Γερμανικά'=>0,'Ιταλικά'=>0]],
 'technology_informatics_split_sections'=>['Α΄'=>0,'Β΄'=>0,'Γ΄'=>0],'ethics_by_grade'=>[]]);
$gel=schoolProfileBuildDayGel2026([
 'profile_id'=>'phase3-real-gel','general_sections'=>['Α΄'=>1,'Β΄'=>1,'Γ΄'=>1],
 'second_foreign_language_groups'=>['Α΄'=>['Γαλλικά'=>1,'Γερμανικά'=>0],'Β΄'=>['Γαλλικά'=>0,'Γερμανικά'=>1]],
 'orientation_sections'=>['Β΄'=>['humanities'=>1,'science'=>1],'Γ΄'=>['humanities'=>1,'science_health'=>1,'economics_it'=>1]],
 'grade_c_science_health_field_groups'=>['Μαθηματικά'=>1,'Βιολογία'=>1],
 'grade_c_conditional_groups'=>['Μαθηματικά'=>0,'Ιστορία'=>0],'ethics_by_grade'=>[]]);
$cases=[];
foreach([
 ['name'=>'gym_math_single','profile'=>$gym,'people'=>[['person_id'=>'m','specialty_code'=>'ΠΕ03']],'hours'=>['m'=>7]],
 ['name'=>'gym_te16_music_unique','profile'=>$gymA,'people'=>[['person_id'=>'t','specialty_code'=>'ΤΕ16']],'hours'=>['t'=>1]],
 ['name'=>'gym_phil_soc','profile'=>$gym,'people'=>[['person_id'=>'p','specialty_code'=>'ΠΕ02'],['person_id'=>'s','specialty_code'=>'ΠΕ78']],'hours'=>['p'=>8,'s'=>6]],
 ['name'=>'gel_math_single','profile'=>$gel,'people'=>[['person_id'=>'m','specialty_code'=>'ΠΕ03']],'hours'=>['m'=>7]],
 ['name'=>'gel_mixed','profile'=>$gel,'people'=>[['person_id'=>'p','specialty_code'=>'ΠΕ02'],['person_id'=>'e','specialty_code'=>'ΠΕ80'],['person_id'=>'m','specialty_code'=>'ΠΕ03']],'hours'=>['p'=>8,'e'=>8,'m'=>8]],
] as $c){
 $matrix=schoolProfileWorkloadMatrix($c['profile']);$slots=personnelWorkloadAllocationSlots($c['profile'],$matrix);[$ps,$ss]=mkstate($slots,$c['people'],$c['hours']);$r=teachingAllocationEngineSolveRemaining($slots,$c['people'],$ps,$ss);$cases[]=['name'=>$c['name'],'slots'=>$slots,'people'=>$c['people'],'person_state'=>$ps,'slot_state'=>$ss,'policy'=>teachingAllocationEngineClientContract(),'php'=>$r];
}
echo json_encode($cases,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
'''
p=subprocess.run(['php','-d','memory_limit=1024M','-r',php],text=True,capture_output=True,cwd=ROOT)
if p.returncode: print(p.stderr);sys.exit(1)
cases=json.loads(p.stdout)
node=r'''const fs=require('fs'),vm=require('vm');global.window=global;vm.runInThisContext(fs.readFileSync(process.argv[1],'utf8'));vm.runInThisContext(fs.readFileSync(process.argv[2],'utf8'));const cs=JSON.parse(fs.readFileSync(0,'utf8')),W=global.PersonnelWorkloadCalculations;process.stdout.write(JSON.stringify(cs.map(c=>W.optimizeRemaining(c.slots,c.people,c.person_state,c.slot_state,c.policy))));'''
n=subprocess.run(['node','-e',node,str(ROOT/'includes'/'specialty-code-normalization.js'),str(MOD)],input=json.dumps(cases,ensure_ascii=False),text=True,capture_output=True,cwd=ROOT)
if n.returncode: print(n.stderr);sys.exit(1)
js=json.loads(n.stdout)
for c,j in zip(cases,js):
    po=obj(c['php']['allocations']);jo=obj(j['allocations'])
    check(c['name']+' real-profile objective parity',po==jo,f'PHP={po} JS={jo}' if po!=jo else '')
    check(c['name']+' remains atomic',len({r['slot_id'] for r in j['allocations']})==len(j['allocations']))
    if c['php']['summary'].get('maximum_coverage_certified'):
        check(c['name']+' JS certifies when PHP certifies',j['summary'].get('maximum_coverage_certified') is True)
byname={c['name']:(c,j) for c,j in zip(cases,js)}
te16c,te16j=byname['gym_te16_music_unique']
expected=[('gym.mousiki@Α΄|whole|section|1','t',1,'A','ΤΕ16','primary')]
def exact(rows): return sorted((r.get('slot_id'),r.get('person_id'),int(r.get('hours',0)),r.get('priority'),r.get('used_specialty_code'),r.get('specialty_source')) for r in rows)
check('real-profile TE16 fixture has exact expected server assignment',exact(te16c['php']['allocations'])==expected)
check('real-profile TE16 fixture has exact expected client assignment',exact(te16j['allocations'])==expected)
if FAIL:
    print('\nReal-profile optimizer parity: FAIL (%d)'%len(FAIL));sys.exit(1)
print('\nReal-profile optimizer parity: PASS')
