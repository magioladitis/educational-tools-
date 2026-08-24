from pathlib import Path
import re, subprocess, sys
from bs4 import BeautifulSoup

ROOT = Path(__file__).resolve().parents[1]
checks=[]
def ck(name, cond, detail=''):
    ok=bool(cond); checks.append((name,ok,detail)); print(('PASS ' if ok else 'FAIL ')+name+((' :: '+str(detail)) if detail and not ok else ''))

pages=sorted(ROOT.glob('*.php'))
rendered={}
camel=re.compile(r'^[a-z][A-Za-z0-9]*$')
for page in pages:
    p=subprocess.run(['php',str(page)],capture_output=True,text=True)
    ck(page.name+' PHP render',p.returncode==0,p.stderr.strip())
    soup=BeautifulSoup(p.stdout,'html.parser'); rendered[page.name]=soup
    ids=[x.get('id') for x in soup.select('[id]')]
    ck(page.name+' no duplicate ids',len(ids)==len(set(ids)),[x for x in set(ids) if ids.count(x)>1])
    missing=[]
    for lab in soup.find_all('label'):
        target=lab.get('for')
        if target and not soup.find(id=target): missing.append(target)
    ck(page.name+' label targets exist',not missing,missing)
    bad=[]
    for el in soup.find_all(['input','select','textarea']):
        i=el.get('id')
        if i and not camel.match(i): bad.append(i)
    ck(page.name+' server-rendered field ids camelCase',not bad,bad)

# Canonical ASEP specialty field on selectable-call pages.
for name in ['ypologismos-morion.php','ypologismos-morion-1gt-2024.php','ypologismos-morion-2ea-2025.php','ypologismos-morion-3ea-2025.php','ypologismos-morion-4ea-2025.php','ypologismos-morion-5ea-2022.php']:
    soup=rendered[name]
    ck(name+' canonical specialty id',bool(soup.select_one('#specialty')))
    ck(name+' no legacy branch field',not soup.select_one('#branch'))

# Canonical service ids across seven ASEP calculators.
service_pages=['ypologismos-morion.php','ypologismos-morion-1ea-2025.php','ypologismos-morion-1gt-2024.php','ypologismos-morion-2ea-2025.php','ypologismos-morion-3ea-2025.php','ypologismos-morion-4ea-2025.php','ypologismos-morion-5ea-2022.php']
roles={
 'regular':'regularMonths','difficult':'difficultMonths',
 'three-month-regular-2020':'threeMonthRegular2020','three-month-difficult-2020':'threeMonthDifficult2020',
 'three-month-regular-2021':'threeMonthRegular2021','three-month-difficult-2021':'threeMonthDifficult2021'}
for name in service_pages:
    soup=rendered[name]
    for role,cid in roles.items():
        el=soup.select_one('[data-service-role="'+role+'"]')
        ck(name+' '+role+'='+cid, bool(el and el.get('id')==cid))

# Canonical seminar proof radio/status ids wherever the shared component is rendered.
for name,soup in rendered.items():
    if soup.select_one('#trainingProof'):
        for cid in ['trainingProofDatesYes','trainingProofDatesNo','trainingProofDatesStatus']:
            ck(name+' '+cid, bool(soup.select_one('#'+cid)))
        for old in ['trainingDatesYes','trainingDatesNo','trainingDatesStatus']:
            ck(name+' old '+old+' absent', not soup.select_one('#'+old))

# DIMOS: language identity and level ids have one meaning across tools.
dimos=rendered['ypologismos-morion-apospasis-dimos.php']
for cid in ['language1','languageLevel1','language2','languageLevel2']:
    ck('DIMOS canonical '+cid,bool(dimos.select_one('#'+cid)))
for old in ['languageName1','languageName2']:
    ck('DIMOS old '+old+' absent',not dimos.select_one('#'+old))

# Sivitanidios exact social concepts aligned with SDE registry.
siv=rendered['ypologismos-morion-sivitanidios-saek.php']
for cid in ['threeChildren','manyChildren','singleParent']:
    ck('Sivitanidios canonical '+cid,bool(siv.select_one('#'+cid)))
for old in ['threeChildrenParent','manyChildrenMember','singleParentMember']:
    ck('Sivitanidios old '+old+' absent',not siv.select_one('#'+old))

# Dynamic field-id source patterns must also be camelCase.
dimos_src=(ROOT/'ypologismos-morion-apospasis-dimos.php').read_text(encoding='utf-8')
onas_src=(ROOT/'ypologismos-morion-onaseia.php').read_text(encoding='utf-8')
ck('DIMOS no underscored dynamic field suffix', '_solo' not in dimos_src and '_group' not in dimos_src)
ck('Onaseia no underscored service row ids', 'serviceYear_${' not in onas_src and 'serviceMonths_${' not in onas_src)

# Legacy production field ids from earlier migrations must remain absent.
prod='\n'.join(p.read_text(encoding='utf-8') for p in pages)
old_patterns=[r'id="degree"',r'id="masters"',r'id="pde"',r'id="auxSeminar400"',r'id="publicMonths"',r'id="hardMonths"',r'id="normalMonths"',r'id="branch"',r'id="trainingDatesYes"',r'id="trainingDatesNo"']
for pat in old_patterns:
    ck('obsolete production field absent: '+pat, re.search(pat,prod) is None)


# TE shared component API follows the canonical specialty terminology too.
te_component=(ROOT/'includes/components/asep-te-academic.php').read_text(encoding='utf-8')
te_controller=(ROOT/'includes/asep-te-academic.js').read_text(encoding='utf-8')
ck('TE shared component uses specialty_id', "'specialty_id'" in te_component)
ck('TE shared component no branch_id config', "'branch_id'" not in te_component)
ck('TE shared DOM metadata uses data-specialty-id', 'data-specialty-id' in te_component)
ck('TE shared DOM metadata no data-branch-id', 'data-branch-id' not in te_component)
ck('TE controller uses specialtyId', 'function specialtyId' in te_controller)
ck('TE controller no branchId helper', 'function branchId' not in te_controller)
ck('TE controller state uses specialty', 'specialty: valueOf(specialtyId(c))' in te_controller)
ck('TE controller no branch state key', 'branch: valueOf(' not in te_controller)

ck('Dynamic license requirement ids avoid underscore separator', 'licenseReq_' not in prod)
passed=sum(1 for _,v,_ in checks if v); failed=len(checks)-passed
print('RESULT',passed,'PASS /',failed,'FAIL')
sys.exit(1 if failed else 0)
