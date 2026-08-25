from pathlib import Path
import subprocess, sys
from lxml import html

ROOT = Path(__file__).resolve().parents[1]
PAGES = {
    'ypologismos-morion-apospasis-dimos.php': (1, ['grandTotal', 'totalBar']),
    'ypologismos-morion-apospasis-evropaika-scholeia.php': (2, ['preInterviewTotal', 'finalTotal', 'finalHelp']),
    'ypologismos-morion-apospasis-psifiako-frontistirio.php': (1, ['totalScore', 'totalBar']),
    'ypologismos-morion-apospasis-sde.php': (1, ['totalScore', 'totalBar']),
}
checks=[]
def check(name, cond): checks.append((name, bool(cond)))

for fn,(expected,ids_expected) in PAGES.items():
    src=(ROOT/fn).read_text(encoding='utf-8')
    check(fn+': canonical helper count', src.count('calculatorScoreHeader(array(') == expected)
    check(fn+': no manual Result h2', '<h2>Αποτέλεσμα</h2>' not in src)
    proc=subprocess.run(['php',fn],cwd=ROOT,capture_output=True,text=True)
    check(fn+': PHP render', proc.returncode == 0)
    if proc.returncode:
        continue
    doc=html.document_fromstring(proc.stdout)
    headers=doc.xpath("//*[contains(concat(' ', normalize-space(@class), ' '), ' result-score-header ')]")
    check(fn+': rendered header count', len(headers) == expected)
    check(fn+': all headers accessible groups', all(h.get('role')=='group' and h.get('aria-label')=='Αποτέλεσμα' for h in headers))
    ids=doc.xpath('//*[@id]/@id')
    check(fn+': no duplicate ids', len(ids)==len(set(ids)))
    for i in ids_expected:
        check(fn+': preserves '+i, i in ids)

# Legacy wrapper patterns removed only where R8 migrated them.
check('DIMOS manual total header removed', '<div class="total">' not in (ROOT/'ypologismos-morion-apospasis-dimos.php').read_text())
check('Digital manual big-total header removed', '<div class="big-total">' not in (ROOT/'ypologismos-morion-apospasis-psifiako-frontistirio.php').read_text())
check('SDE manual big-total header removed', '<div class="big-total">' not in (ROOT/'ypologismos-morion-apospasis-sde.php').read_text())

e=(ROOT/'ypologismos-morion-apospasis-evropaika-scholeia.php').read_text()
check('European manual stage wrapper removed', '<div class="stage"><div class="stage-label">' not in e)

failed=[n for n,v in checks if not v]
for n,v in checks: print(('PASS' if v else 'FAIL')+': '+n)
print(f'\n{sum(v for _,v in checks)}/{len(checks)} PASS')
sys.exit(1 if failed else 0)
