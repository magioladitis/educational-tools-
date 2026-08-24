from pathlib import Path
import re
import subprocess, sys
from bs4 import BeautifulSoup

ROOT=Path(__file__).resolve().parents[1]
checks=[]
def ck(name,cond):
    checks.append((name,bool(cond)))
    print(('PASS' if cond else 'FAIL'),name)

pages=['ypologismos-morion-1gt-2024.php','ypologismos-morion-4ea-2025.php']
for name in pages:
    s=(ROOT/name).read_text(encoding='utf-8')
    ck(name+' requires shared TE component', "includes/components/asep-te-academic.php" in s)
    ck(name+' loads shared TE controller', "edu_asset_url('includes/asep-te-academic.js')" in s)
    ck(name+' no direct TEAcademic.calculate', 'TEAcademic.calculate' not in s)
    ck(name+' no direct AsepLanguageSelector.calculate', 'AsepLanguageSelector.calculate' not in s)
    ck(name+' no local updateBranchUI', 'updateBranchUI' not in s)
    ck(name+' no local updateGradeUI', 'updateGradeUI' not in s)
    ck(name+' no direct TrainingProof.summary', "TrainingProof.summary('trainingProof')" not in s)
    ck(name+' renders grade-scale part', "'part' => 'grade-scale'" in s)
    ck(name+' renders degree-details part', "'part' => 'degree-details'" in s)
    ck(name+' renders qualifications part', "'part' => 'qualifications'" in s)

s4=(ROOT/'ypologismos-morion-4ea-2025.php').read_text(encoding='utf-8')
ck('4EA passes seminar400 twice (state + proof)', s4.count("'extra_training_ids' => array('seminar400')") >= 2)
ck('4EA has no local trainingActive', 'function trainingActive' not in s4)

for name,label in [(pages[0],'1GT'),(pages[1],'4EA')]:
    proc=subprocess.run(['php',name],cwd=ROOT,stdout=subprocess.PIPE,stderr=subprocess.PIPE,text=True)
    ck(label+' PHP render exit 0',proc.returncode==0)
    soup=BeautifulSoup(proc.stdout,'html.parser')
    ids=[x['id'] for x in soup.find_all(id=True)]
    ck(label+' rendered no duplicate ids',len(ids)==len(set(ids)))
    ck(label+' one TE academic root',len(soup.select('[data-component="asep-te-academic"]'))==1)
    lang=soup.select_one('#asepLanguages')
    ck(label+' language profile TE',bool(lang and lang.get('data-profile')=='te'))
    proof=soup.select_one('#trainingProof')
    ck(label+' uses canonical trainingProofDates radios',bool(proof and proof.select('input[name="trainingProofDates"]')))
    if label=='1GT':
        ck('1GT training proof trigger is training only',bool(proof and proof.get('data-input-ids')=='training'))
    else:
        ck('4EA training proof triggers training + aux400',bool(proof and proof.get('data-input-ids')=='training seminar400'))
        degree=soup.select_one('#asepTeAcademic')
        eae=soup.select_one('#mainCriterion')
        qual=soup.select_one('[data-component-part="asep-te-academic-qualifications"]')
        ck('4EA keeps EAE eligibility between degree and qualifications',bool(degree and eae and qual and degree.sourceline < eae.sourceline < qual.sourceline))

component=(ROOT/'includes/components/asep-te-academic.php').read_text(encoding='utf-8')
controller=(ROOT/'includes/asep-te-academic.js').read_text(encoding='utf-8')
ck('shared component owns TE language selector',"'profile' => 'te'" in component)
ck('shared component owns computer proof','renderAsepComputerProof' in component)
ck('shared component owns training proof','renderTrainingProof' in component)
ck('shared controller delegates scoring to TEAcademic','TEAcademic.calculate' in controller)
ck('shared controller canonicalizes specialty through EducationCore','EducationCore.normalizeSpecialtyCode' in controller)
ck('shared controller recognizes canonical Greek ΤΕ16',"v.indexOf('ΤΕ16')" in controller)
ck('shared controller supports extra training ids','extraTrainingIds' in controller)

p=sum(v for _,v in checks); f=len(checks)-p
print(f'RESULT {p} PASS / {f} FAIL')
sys.exit(1 if f else 0)
