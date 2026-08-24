from pathlib import Path
import subprocess, sys, re
from bs4 import BeautifulSoup
ROOT=Path(__file__).resolve().parents[1]
PAGES=[
'ypologismos-morion.php',
'ypologismos-morion-1ea-2025.php','ypologismos-morion-1gt-2024.php','ypologismos-morion-2ea-2025.php','ypologismos-morion-3ea-2025.php','ypologismos-morion-4ea-2025.php',
'ypologismos-morion-apospasis-dimos.php','ypologismos-morion-apospasis-evropaika-scholeia.php','ypologismos-morion-apospasis-exoteriko.php','ypologismos-morion-apospasis-psifiako-frontistirio.php','ypologismos-morion-apospasis-sde.php','ypologismos-morion-diefthynton-ypodiefthynton-sde.php','ypologismos-morion-mitroo-sde.php'
]
LEGACY=['ypologismos-morion-onaseia.php','ypologismos-morion-apospasis.php']
checks=[]
def ck(name,ok):
 checks.append(bool(ok)); print(('PASS ' if ok else 'FAIL ')+name)
for page in PAGES:
 s=(ROOT/page).read_text(encoding='utf-8')
 ck(page+' uses calculator-layout',"includes/components/calculator-layout.php" in s)
 ck(page+' shared columns', 'calculatorColumnsStart(' in s)
 ck(page+' shared main', 'calculatorMainStart(' in s)
 ck(page+' shared results', 'calculatorResultsStart(' in s)
 ck(page+' shared hero', ('calculatorHero(' in s or 'calculatorHeroStart(' in s))
 proc=subprocess.run(['php',str(ROOT/page)],capture_output=True,text=True)
 ck(page+' PHP render',proc.returncode==0)
 soup=BeautifulSoup(proc.stdout,'html.parser')
 ids=[x.get('id') for x in soup.select('[id]')]
 ck(page+' no duplicate ids',len(ids)==len(set(ids)))
 ck(page+' rendered hero',soup.select_one('.hero') is not None)
 ck(page+' rendered results',soup.select_one('aside.results, aside.result-card') is not None)
# Legacy pages were outside the Phase 2 migration scope. Later phases may migrate them,
# so Phase 2 regression must not assert that they remain outside the shared layout.
# Component must remain presentation-only: no known scoring namespaces/rules.
c=(ROOT/'includes/components/calculator-layout.php').read_text(encoding='utf-8')
for token in ['EducationAcademic','EducationService','EducationSocial','TEAcademic','EducationLanguages','calculateAsepService','120 μόρια','40 μόρια']:
 ck('layout component excludes business token '+token, token not in c)
passed=sum(checks); failed=len(checks)-passed
print('RESULT',passed,'PASS /',failed,'FAIL')
sys.exit(1 if failed else 0)
