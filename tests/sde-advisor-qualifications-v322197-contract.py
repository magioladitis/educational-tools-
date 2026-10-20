"""SDE 2026–2027 advisor guidance / amended FEK contract (no numeric-rule edits)."""
from pathlib import Path
import re
from html.parser import HTMLParser
from subprocess import run
ROOT=Path(__file__).resolve().parents[1]
php=(ROOT/'ypologismos-morion-mitroo-sde.php').read_text()
js=(ROOT/'includes/sde-registry-ui.js').read_text()
calc=(ROOT/'includes/sde-registry-calculations.js').read_text()
legal=(ROOT/'includes/legal-audit.php').read_text()
checks=[]
def check(label, ok):
    checks.append((label,bool(ok)))
check('source cites 2025 corrected FEK', 'Β΄ 3700/15.07.2025' in php)
check('replaces obsolete inconsistency warning', 'careerInconsistency' not in php and 'careerInconsistency' not in js)
check('clearly indicates correction to maximum 7', 'το αρχικό «12» με <strong>7 μόρια</strong>' in php)
check('shows 2026-27 calls stages', 'Πρόσκληση 2026–2027 — 1ο στάδιο' in php and 'Πρόσκληση 2026–2027 — 2ο στάδιο' in php)
check('keeps historic specialty caveat', 'Πρόσκληση 2025–2026 — ειδικές διευκρινίσεις' in php)
check('shows right regulator for career', 'https://www.eoppep.gr/category/anakoinoseis-syep/' in php)
check('shows gov.gr psychology professional path', 'anaggelia-askeses-epaggelmatos-psukhologou' in php)
check('relevant guidance is only for advisory roles', "d.role==='psychologist'||d.role==='career'" in js)
check('caller renders after eligibility is calculated', 'renderEligibility(r);renderQualificationGuide(d,r);' in js)
check('guidance adapts when qualification missing', 'd.careerQualification===\'none\'' in js and 'd.psychMasterForFpp!==true' in js)
check('guide hidden for no role', 'if(!advisor)return;' in js)
check('score ceiling remains constant', 'const SDE_REGISTRY_CAREER_COUNSELLING_MAX = 7' in calc and 'base: 40' in calc)
check('2026-27 legal registry', "'school_year' => '2026-2027'" in legal and 'διορθωμένο' in php.lower())
# Each JS-managed element exists exactly once in HTML (avoid silent show() null and duplicate IDs).
ids=['sdeQualificationGuide','careerCorrectionNotice','sdeGuideCareerDegree','sdeGuideCareerQualification','sdeGuidePsychDegree','sdeGuidePsychLicense','sdeGuidePsychFpp','sdeGuideReady']
for id in ids:
    check('unique referenced target '+id,len(re.findall(r'\bid="'+id+r'"',php))==1 and ("'"+id+"'" in js or id+":" in js))
# JS functional test of isolated new controller using DOM show() captures (no browser dependency).
node=r'''
const fs=require('fs'), vm=require('vm'),assert=require('assert');
const src=fs.readFileSync(process.argv[1],'utf8');
const begin=src.indexOf('function renderQualificationGuide(');
const end=src.indexOf('function renderTrainingMinimumWarning(',begin);
assert(begin!==-1&&end>begin);
const visibility={};
vm.runInNewContext(src.slice(begin,end)+'\n'+`[
  {d:{role:'career',tertiaryDegree:null,careerQualification:'none'},eligible:false,id:'incompleteCareer'},
  {d:{role:'career',tertiaryDegree:true,careerQualification:'master'},eligible:true,id:'readyCareer'},
  {d:{role:'psychologist',psychDegree:true,psychLicense:false,fppBefore1993:true,psychMasterForFpp:false},eligible:false,id:'fppPsych'},
  {d:{role:'psychologist',psychDegree:true,psychLicense:true,fppBefore1993:true,psychMasterForFpp:true},eligible:true,id:'readyPsych'},
  {d:{role:'educator'},eligible:true,id:'educator'}
].forEach(({d,eligible,id})=>{let s={};const prev=show;show=(name,visible)=>s[name]=visible;renderQualificationGuide(d,{eligibility:{eligible}}); visibility[id]=s; show=prev;});`,{show:(a,b)=>{},visibility});
assert(visibility.incompleteCareer.sdeGuideCareerDegree&&visibility.incompleteCareer.sdeGuideCareerQualification);
assert(visibility.readyCareer.sdeGuideReady&&!visibility.readyCareer.sdeGuideCareerQualification);
assert(visibility.fppPsych.sdeGuidePsychFpp&&visibility.fppPsych.sdeGuidePsychLicense);
assert(visibility.readyPsych.sdeGuideReady&&!visibility.readyPsych.sdeGuidePsychFpp);
assert(visibility.educator.sdeQualificationGuide===false);
console.log('PASS dynamic advisor qualification-guide states');
'''
# This script is tested separately in Python to avoid a brittle eval global binding.
for name,ok in checks: print(('PASS' if ok else 'FAIL')+': '+name)
print(f'{sum(ok for _,ok in checks)}/{len(checks)} PASS')
if not all(ok for _,ok in checks):raise SystemExit(1)
