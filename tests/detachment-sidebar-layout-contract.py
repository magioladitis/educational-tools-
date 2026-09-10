from pathlib import Path
import re

ROOT = Path(__file__).resolve().parents[1]
php = (ROOT / 'ypologismos-morion-apospasis.php').read_text(encoding='utf-8')
ui = (ROOT / 'includes' / 'detachment-ui.js').read_text(encoding='utf-8')
engine = (ROOT / 'includes' / 'detachment-calculations.js').read_text(encoding='utf-8')
css = (ROOT / 'assets/common.css').read_text(encoding='utf-8')

checks = []
def check(name, cond):
    checks.append((name, bool(cond)))

check('uses calculatorColumnsStart', 'calculatorColumnsStart()' in php)
check('uses calculatorMainStart', 'calculatorMainStart' in php)
check('uses calculatorResultsStart', 'calculatorResultsStart' in php)
check('uses canonical score header', 'calculatorScoreHeader' in php and "'value_id' => 'grandTotal'" in php)
for id_ in ['resService','resCoService','resLocality','resFamily','resHealth','resStudies','sidebarStatus']:
    check(f'has sidebar id {id_}', php.count(f"'{id_}'") >= 1 or php.count(f'"{id_}"') >= 1)
check('keeps detailed inline result', "calculatorInlineResult(array('id' => 'result'" in php)
check('loads pure calculation engine', "includes/detachment-calculations.js" in php and 'global.EducationDetachment' in engine)
check('loads external UI controller', "includes/detachment-ui.js" in php and 'global.EducationDetachmentUI' in ui)
check('updates sidebar from calculated total', 'updateSidebarSummary({' in ui and 'service: calc.service.total' in ui and 'family: calc.familyTotal' in ui)
check('reset clears sidebar', 'updateSidebarSummary();' in ui)
check('desktop two-column CSS', 'body.edu-page-detachment .layout {display:grid;' in css and 'grid-template-columns:minmax(0,1fr) 330px;' in css)
check('sticky sidebar CSS', 'body.edu-page-detachment .results {position:sticky;' in css)
check('mobile collapse CSS', '@media (max-width: 900px)' in css and 'body.edu-page-detachment .layout {grid-template-columns:1fr;}' in css)

# Static id uniqueness for literal HTML/helper ids in this page.
ids = re.findall(r"(?:id=\\?['\"]|['\"](?:value_id|id)['\"]\s*=>\s*['\"])([A-Za-z][A-Za-z0-9_:-]*)", php)
dups = sorted({x for x in ids if ids.count(x) > 1 and x not in {'result'}})
check('no obvious duplicate sidebar ids', not any(x in dups for x in ['grandTotal','resService','resCoService','resLocality','resFamily','resHealth','resStudies','sidebarStatus']))

failed = [name for name, ok in checks if not ok]
for name, ok in checks:
    print(('PASS' if ok else 'FAIL') + ' | ' + name)
print(f'RESULT: {len(checks)-len(failed)}/{len(checks)} PASS')
if failed:
    raise SystemExit(1)
