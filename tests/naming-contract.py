from pathlib import Path
import re, sys, subprocess
from bs4 import BeautifulSoup
ROOT=Path(__file__).resolve().parents[1]
PAGES=['ypologismos-morion.php','ypologismos-morion-1ea-2025.php','ypologismos-morion-2ea-2025.php','ypologismos-morion-3ea-2025.php','ypologismos-morion-1gt-2024.php','ypologismos-morion-4ea-2025.php']
checks=[]
def ck(name,cond):
    checks.append((name,bool(cond))); print(('PASS ' if cond else 'FAIL ')+name)
canonical_roles={
 'regular':'regularMonths','difficult':'difficultMonths',
 'three-month-regular-2020':'threeMonthRegular2020',
 'three-month-difficult-2020':'threeMonthDifficult2020',
 'three-month-regular-2021':'threeMonthRegular2021',
 'three-month-difficult-2021':'threeMonthDifficult2021'
}
for page in PAGES:
    proc=subprocess.run(['php',str(ROOT/page)],capture_output=True,text=True)
    ck(page+' PHP render',proc.returncode==0)
    soup=BeautifulSoup(proc.stdout,'html.parser')
    ids=[x.get('id') for x in soup.select('[id]')]
    ck(page+' no duplicate ids',len(ids)==len(set(ids)))
    for role,cid in canonical_roles.items():
        el=soup.select_one('[data-service-role="'+role+'"]')
        ck(page+' '+role+' canonical id',bool(el and el.get('id')==cid))

s2=(ROOT/'ypologismos-morion-2ea-2025.php').read_text(encoding='utf-8')
s3=(ROOT/'ypologismos-morion-3ea-2025.php').read_text(encoding='utf-8')
s4=(ROOT/'ypologismos-morion-4ea-2025.php').read_text(encoding='utf-8')
s1=(ROOT/'ypologismos-morion-1ea-2025.php').read_text(encoding='utf-8')
r2=subprocess.run(['php',str(ROOT/'ypologismos-morion-2ea-2025.php')],capture_output=True,text=True)
r3=subprocess.run(['php',str(ROOT/'ypologismos-morion-3ea-2025.php')],capture_output=True,text=True)
soup2=BeautifulSoup(r2.stdout,'html.parser')
soup3=BeautifulSoup(r3.stdout,'html.parser')
ck('2EA mscCount', bool(soup2.select_one('[data-msc-id="mscCount"]') and soup2.select_one('#mscCount')))
ck('3EA degreeGrade', bool(soup3.select_one('[data-degree-id="degreeGrade"]') and soup3.select_one('#degreeGrade')))
ck('3EA mscCount', bool(soup3.select_one('[data-msc-id="mscCount"]') and soup3.select_one('#mscCount')))
ck('3EA pedagogical', bool(soup3.select_one('#pedagogical')) and 'renderAsepPedagogicalProof' in s3)
ck('3EA signLanguage', bool(soup3.select_one('#signLanguage')) and 'renderEaeSensoryPriority' in s3)
ck('4EA seminar400', 'id="seminar400"' in s4 and "array('seminar400')" in s4)
ck('1EA canonical social ids', "'marriage_id' => 'marriageYears4Plus'" in s1 and "'mental_id' => 'candidateMentalCondition'" in s1)
ck('2EA canonical social ids', "'marriage_id' => 'marriageYears4Plus'" in s2 and "'mental_id' => 'candidateMentalCondition'" in s2)
old=[r'id="degree"',r'id="masters"',r'id="pde"',r"'eng_id' => 'sign'",r'id="auxSeminar400"',r'id="publicMonths"',r'id="hardMonths"',r'id="normalMonths"',r"'marriage_id' => 'marriage4'",r"'mental_id' => 'mental'"]
combined='\n'.join((ROOT/p).read_text(encoding='utf-8') for p in PAGES)
for token in old: ck('obsolete alias absent: '+token, token not in combined)
p=sum(v for _,v in checks); f=len(checks)-p
print('RESULT',p,'PASS /',f,'FAIL')
sys.exit(1 if f else 0)
