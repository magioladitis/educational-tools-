#!/usr/bin/env python3
import json, pathlib, subprocess, sys, tempfile
ROOT=pathlib.Path(__file__).resolve().parents[1]
FAIL=[]
def check(label, ok):
    if ok: print('  ✔ '+label)
    else: FAIL.append(label); print('  ✘ '+label)

page=(ROOT/'ypologismos-didaktikon-anagkon.php').read_text(encoding='utf-8')
ui=(ROOT/'includes/staffing-simulator-ui.js').read_text(encoding='utf-8')
module=(ROOT/'includes/personnel-workload-calculations.js').read_text(encoding='utf-8')
check('client workload module exists', bool(module.strip()))
check('staffing page loads client workload module', 'includes/personnel-workload-calculations.js' in page)
check('client workload module loads before staffing simulator UI', 'includes/personnel-workload-calculations.js' in page and page.find('includes/personnel-workload-calculations.js') < page.find('includes/staffing-simulator-ui.js'))
check('staffing UI consumes shared director band helper', 'PersonnelWorkloadCalculations.directorSectionsBandFromCount' in ui)
check('staffing UI consumes shared secondary obligation helper', 'PersonnelWorkloadCalculations.secondaryObligation' in ui)

cases=[
 {'id':'manual_pe','p':{'person_id':'p1','specialty_code':'ΠΕ03','role':'teacher','required_teaching_hours':'21','service':{'years':25},'assigned_external_hours':3}},
 {'id':'manual_pe_over','p':{'person_id':'p2','specialty_code':'ΠΕ03','role':'teacher','required_teaching_hours':'24','service':{'years':0}}},
 {'id':'myschool','p':{'person_id':'p3','specialty_code':'ΠΕ02','role':'teacher','required_teaching_hours':'18','obligation_source':'myschool_stat4_8','source_base_required_hours':20,'source_reduction_hours':2,'source_hours_at_unit':18,'service':{'years':14,'months':2,'days':3}}},
 {'id':'director_4_19y','p':{'person_id':'p4','specialty_code':'ΠΕ03','role':'director','school_general_section_count':4,'service':{'years':19,'months':11,'days':29}}},
 {'id':'director_4_20y','p':{'person_id':'p5','specialty_code':'ΠΕ03','role':'director','school_general_section_count':4,'service':{'years':20}}},
 {'id':'vice_20y','p':{'person_id':'p6','specialty_code':'ΠΕ03','role':'vice_or_sector','service':{'years':20}}},
 {'id':'te_lab','p':{'person_id':'p7','specialty_code':'ΤΕ01.04','role':'lab_responsible','service':{'years':8}}},
 {'id':'de_needs','p':{'person_id':'p8','specialty_code':'ΔΕ01.05','role':'lab_responsible','service':{'years':5}}},
 {'id':'de_explicit','p':{'person_id':'p9','specialty_code':'ΔΕ01.05','hours_branch':'DE01_ARCH','role':'lab_responsible','service':{'years':5}}},
]
php_code='''<?php
require %s;
$cases=json_decode(%s,true);
$out=array('helpers'=>array(),'cases'=>array());
foreach(array(array(-2,14,40),array(5,12,31),array(51,20,99),array('7','3','9')) as $v){$k=implode('|',$v);$out['helpers']['service_days'][$k]=personnelWorkloadServiceDays($v[0],$v[1],$v[2]);}
foreach(array(0,1,29,30,359,360,7219,7200) as $d){$out['helpers']['service_label'][(string)$d]=personnelWorkloadServiceLabel($d);}
foreach(array(array('ΠΕ03',null),array('PE03',null),array('ΤΕ01.04',null),array('ΔΕ01.05',null),array('ΔΕ01.05','DE01_ARCH'),array('ΠΕ03','BAD')) as $v){$k=$v[0].'|'.($v[1]===null?'null':$v[1]);$out['helpers']['branch'][$k]=personnelWorkloadHoursBranchForSpecialty($v[0],$v[1]);}
foreach(array(-1,0,1,3,5,6,9,10,12,13,99) as $n){$out['helpers']['band'][(string)$n]=personnelWorkloadDirectorSectionsBandFromCount($n);}
foreach(array('PE','TE01','DE01_ARCH','DE01_TECH') as $b){foreach(array(0,2160,2161,4320,4321,7199,7200) as $d){$x=personnelWorkloadSecondaryTeacherBaseHours($b,$d);$out['helpers']['base'][$b][(string)$d]=$x;}}
foreach($cases as $c){$out['cases'][$c['id']]=array('obligation'=>personnelWorkloadSecondaryObligation($c['p']),'normalized'=>personnelWorkloadNormalizePerson($c['p']));}
echo json_encode($out,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
''' % (json.dumps(str(ROOT/'includes/personnel-workload.php')), json.dumps(json.dumps(cases,ensure_ascii=False)))
php=subprocess.run(['php'],input=php_code,text=True,capture_output=True,cwd=ROOT)
if php.returncode!=0:
    print(php.stderr); sys.exit(1)
php_out=json.loads(php.stdout)

labels={}
for c in cases:
    for key in ('specialty_code','secondary_specialty_code'):
        code=c['p'].get(key)
        if code:
            # Labels used by this vector set. Exact label parity matters for normalize outputs.
            labels.update({'ΠΕ03':'Μαθηματικοί','ΠΕ02':'Φιλόλογοι','ΤΕ01.04':'Ψυκτικοί','ΔΕ01.05':'Οικοδόμοι'})
node_script='''
const fs=require('fs'),vm=require('vm');
global.window=global; vm.runInThisContext(fs.readFileSync(%s,'utf8'));
const cases=%s, labels=%s;
const opt={specialtyLabels:labels,isKnownSpecialty:(c)=>Object.prototype.hasOwnProperty.call(labels,c)};
const W=global.PersonnelWorkloadCalculations;
const out={helpers:{service_days:{},service_label:{},branch:{},band:{},base:{}},cases:{}};
[[-2,14,40],[5,12,31],[51,20,99],['7','3','9']].forEach(v=>out.helpers.service_days[v.join('|')]=W.serviceDays(v[0],v[1],v[2]));
[0,1,29,30,359,360,7219,7200].forEach(d=>out.helpers.service_label[String(d)]=W.serviceLabel(d));
[['ΠΕ03',null],['PE03',null],['ΤΕ01.04',null],['ΔΕ01.05',null],['ΔΕ01.05','DE01_ARCH'],['ΠΕ03','BAD']].forEach(v=>out.helpers.branch[v[0]+'|'+(v[1]===null?'null':v[1])]=W.hoursBranchForSpecialty(v[0],v[1]));
[-1,0,1,3,5,6,9,10,12,13,99].forEach(n=>out.helpers.band[String(n)]=W.directorSectionsBandFromCount(n));
['PE','TE01','DE01_ARCH','DE01_TECH'].forEach(b=>[0,2160,2161,4320,4321,7199,7200].forEach(d=>out.helpers.base[b]||(out.helpers.base[b]={}),d=>{}));
['PE','TE01','DE01_ARCH','DE01_TECH'].forEach(b=>{out.helpers.base[b]={};[0,2160,2161,4320,4321,7199,7200].forEach(d=>out.helpers.base[b][String(d)]=W.secondaryTeacherBaseHours(b,d));});
cases.forEach(c=>out.cases[c.id]={obligation:W.secondaryObligation(c.p,opt),normalized:W.normalizePerson(c.p,opt)});
process.stdout.write(JSON.stringify(out));
''' % (json.dumps(str(ROOT/'includes/personnel-workload-calculations.js')),json.dumps(cases,ensure_ascii=False),json.dumps(labels,ensure_ascii=False))
node=subprocess.run(['node','-e',node_script],text=True,capture_output=True,cwd=ROOT)
if node.returncode!=0:
    print(node.stderr); sys.exit(1)
js_out=json.loads(node.stdout)
check('service-day normalization matches PHP', js_out['helpers']['service_days']==php_out['helpers']['service_days'])
check('service labels match PHP', js_out['helpers']['service_label']==php_out['helpers']['service_label'])
check('specialty branch resolution matches PHP', js_out['helpers']['branch']==php_out['helpers']['branch'])
check('director section bands match PHP', js_out['helpers']['band']==php_out['helpers']['band'])
check('secondary base-hour bands match PHP', js_out['helpers']['base']==php_out['helpers']['base'])
for cid in php_out['cases']:
    check(cid+' obligation matches PHP', js_out['cases'][cid]['obligation']==php_out['cases'][cid]['obligation'])
    check(cid+' normalized person matches PHP', js_out['cases'][cid]['normalized']==php_out['cases'][cid]['normalized'])

if FAIL:
    print('\nFAIL:',len(FAIL))
    for x in FAIL: print(' - '+x)
    sys.exit(1)
print('\nPersonnel workload client parity: PASS (%d checks)' % (5+5+len(cases)*2))
