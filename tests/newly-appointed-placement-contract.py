from pathlib import Path
import re
import subprocess

ROOT = Path(__file__).resolve().parents[1]
PAGE = ROOT / 'ypologismos-morion-topothetisis-neodioriston.php'
MODULE = ROOT / 'includes' / 'newly-appointed-placement-calculations.js'
TOOLS = ROOT / 'ergaleia.php'
checks = 0

def check(name, cond):
    global checks
    if not cond:
        raise AssertionError(name)
    checks += 1

page = PAGE.read_text(encoding='utf-8')
module = MODULE.read_text(encoding='utf-8')
tools = TOOLS.read_text(encoding='utf-8')

check('page exists', PAGE.exists())
check('module exists', MODULE.exists())
check('standard calculator', 'edu-calc-standard' in page and 'edu-page-newly-appointed-placement' in page)
check('shared layout', 'calculatorHero(' in page and 'calculatorColumnsStart(' in page and 'calculatorScoreHeader(' in page)
check('no inline style', '<style' not in page.lower())
check('three criteria', all(x in page for x in ['familyResult', 'coServiceResult', 'localityResult']))
check('family status dropdown', '<select id="familyStatus">' in page and 'familyStatusEligible' not in page)
check('placement family status options', all(x in page for x in ['Έγγαμος/η ή σύμφωνο συμβίωσης — 4 μόρια', 'Σε χηρεία, με μοριοδοτούμενο τέκνο — 4 μόρια', 'Άγαμος/η γονέας με μοριοδοτούμενο τέκνο — 4 μόρια', 'Διαζευγμένος/η ή σε διάσταση με νόμιμη επιμέλεια — 4 μόρια']))
check('children mapping ui', '1ο: +4' in page and '3ο: +6' in page and 'κάθε επόμενο: +7' in page)
check('co service four', 'συνυπηρέτηση στον Δήμο του σχολείου (+4)' in page)
check('locality four', 'εντοπιότητα στον Δήμο του σχολείου (+4)' in page)
check('two year locality', 'τουλάχιστον δύο έτη' in page)
check('tie breaker order', 'συνυπηρέτηση → εντοπιότητα → οικογενειακοί λόγοι → συνολική υπηρεσία' in page)
check('two year appointment stay', 'τουλάχιστον δύο σχολικά έτη' in page and '5128/2024' in page)
check('five year eae', 'τουλάχιστον πέντε έτη' in page)
check('legal correction', 'άρθρο 3 του π.δ. 154/1996' in page and 'άρθρο 2 του π.δ. 144/1997' in page)
check('module four point criteria', 'input.coService ? 4 : 0' in module and 'input.locality ? 4 : 0' in module)
check('module family dropdown mapping', "status === 'married'" in module and "status === 'widowed_parent'" in module and "status === 'single_parent'" in module and "status === 'divorced_custody'" in module)
check('module child formula', '14 + ((n - 3) * 7)' in module)
check('toolbox card', 'href="ypologismos-morion-topothetisis-neodioriston.php"' in tools)
check('toolbox count', '31 διαθέσιμα εργαλεία' in tools and 'Εμφανίζονται 31 εργαλεία.' in tools)
check('no calculate button', 'calculateBtn' not in page)
check('live calculation children', "byId('eligibleChildren').addEventListener('input'" in page and 'render();' in page)
check('live calculation checks', "['familyStatus', 'coService', 'locality'].forEach" in page and "addEventListener('change', render)" in page)
ids = re.findall(r'\bid="([^"]+)"', page)
check('no duplicate literal ids', len(ids) == len(set(ids)))
proc = subprocess.run(['php', '-l', str(PAGE)], capture_output=True, text=True)
check('php lint', proc.returncode == 0)

node_code = """
const c=require('./includes/newly-appointed-placement-calculations.js');
function eq(a,b,msg){if(a!==b){throw new Error(msg+': '+a+' != '+b)}}
eq(c.childPoints(0),0,'child0');
eq(c.childPoints(1),4,'child1');
eq(c.childPoints(2),8,'child2');
eq(c.childPoints(3),14,'child3');
eq(c.childPoints(4),21,'child4');
eq(c.familyStatusPoints('married',0),4,'married');
eq(c.familyStatusPoints('single_parent',0),0,'single parent needs child');
eq(c.familyStatusPoints('single_parent',1),4,'single parent with child');
eq(c.familyStatusPoints('widowed_parent',1),4,'widowed parent with child');
eq(c.familyStatusPoints('divorced_custody',1),4,'divorced custody with child');
let r=c.calculate({familyStatus:'married',eligibleChildren:3,coService:true,locality:true});
eq(r.total,26,'total');
eq(r.familyPoints,18,'family');
let r2=c.calculate({familyStatus:'single_parent',eligibleChildren:0,coService:false,locality:false});
eq(r2.total,0,'single parent zero child total');
eq(r2.familyStatusRequiresChild,true,'requires child flag');
"""
proc = subprocess.run(['node', '-e', node_code], cwd=ROOT, capture_output=True, text=True)
check('node regression', proc.returncode == 0)

print(f'Newly appointed placement contract: PASS {checks}')
