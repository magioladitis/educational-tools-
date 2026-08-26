from pathlib import Path
import re
import subprocess

ROOT = Path(__file__).resolve().parents[1]
PAGE = ROOT / 'ypologismos-morion-metathesis.php'
MODULE = ROOT / 'includes' / 'transfer-calculations.js'
TOOLS = ROOT / 'ergaleia.php'
CONFIG = ROOT / 'includes' / 'config.php'
checks = 0

def check(name, cond):
    global checks
    if not cond:
        raise AssertionError(name)
    checks += 1

page = PAGE.read_text(encoding='utf-8')
module = MODULE.read_text(encoding='utf-8')
tools = TOOLS.read_text(encoding='utf-8')
config = CONFIG.read_text(encoding='utf-8')

check('page exists', PAGE.exists())
check('module exists', MODULE.exists())
check('no inline style', '<style' not in page.lower())
check('standard calculator', 'edu-calc-standard' in page and 'edu-page-transfer' in page)
check('shared layout', 'calculatorHero(' in page and 'calculatorColumnsStart(' in page and 'calculatorScoreHeader(' in page)
check('source card', 'sourceCardStart(' in page)
check('module loaded', 'includes/transfer-calculations.js' in page)
check('six result criteria', all(x in page for x in ['servicePointsResult','msdPointsResult','coServiceResult','familyResult','localityResult','firstPreferenceResult']))
check('dynamic MSD rows', 'id="msdRows"' in page and 'id="addMsdRow"' in page)
check('prison UI', 'κατάστημα κράτησης (+5/έτος)' in page)
check('digital UI', 'Ψηφιακό Φροντιστήριο (+6/έτος)' in page)
check('listed +2 UI', 'ΥΠΑΙΘΑ / ΠΔΕ / ΔΔΕ / ΙΕΠ κ.ά. (+2/έτος)' in page)
check('remote double UI', 'διπλασιασμός απομακρυσμένης' in page and 'Ι΄–ΙΓ΄' in page)
check('parallel days UI', 'Ημέρες / εβδομάδα' in page and 'έως 5/5' in page)
check('15 day rule UI', '15+ → 1 μήνας' in page)
check('first version disclaimer', '<strong>Πρώτη έκδοση:</strong>' in page and 'ειδικές κατηγορίες' in page)
check('official circular source', '129787/Ε2/15-10-2025' in page)
check('module category map', all(token in module for token in ['A: 1','I: 10','IA: 11','IB: 12','IG: 14']))
check('module 2.5 service', '2.5 / 12' in module)
check('module prison bonus', "type === 'prison'" in module and 'return 5' in module)
check('module digital bonus', "type === 'digital_tutoring'" in module and 'return 6' in module)
check('module listed bonus2', "type === 'listed_service_bonus2'" in module and 'return 2' in module)
check('module remote categories', 'REMOTE_DOUBLE_CATEGORIES' in module and all(k in module for k in ['I: true','IA: true','IB: true','IG: true']))
check('module local first preference guard', "input.mode === 'local' ? 0" in module)
check('toolbox filter', 'data-filter="metatheseis"' in tools)
check('toolbox card', 'href="ypologismos-morion-metathesis.php"' in tools and 'class="tool-number">27<' in tools)
check('toolbox count 27', '27 διαθέσιμα εργαλεία' in tools and 'Εμφανίζονται 27 εργαλεία.' in tools)
check('asset version 3.20.61', "EDU_TOOLS_VERSION', '3.20.61'" in config)
ids = re.findall(r'\bid="([^"]+)"', page)
check('no duplicate literal ids', len(ids) == len(set(ids)))
proc = subprocess.run(['php','-l',str(PAGE)], capture_output=True, text=True)
check('php lint', proc.returncode == 0)

print(f'Transfer calculator contract: PASS {checks}')
