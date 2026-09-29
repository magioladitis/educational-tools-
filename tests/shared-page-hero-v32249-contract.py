#!/usr/bin/env python3
from pathlib import Path
import re, subprocess
ROOT=Path(__file__).resolve().parents[1]
COMP=(ROOT/'includes/components/page-hero.php').read_text(encoding='utf-8')
CALC=(ROOT/'includes/components/calculator-layout.php').read_text(encoding='utf-8')
HEADER=(ROOT/'includes/header.php').read_text(encoding='utf-8')
PAGE=(ROOT/'xronodiagramma-ypiresiakon-metavolon.php').read_text(encoding='utf-8')
JS=(ROOT/'assets/common.js').read_text(encoding='utf-8')
CSS=(ROOT/'assets/common.css').read_text(encoding='utf-8')
CONFIG=(ROOT/'includes/config.php').read_text(encoding='utf-8')
SW=(ROOT/'service-worker.js').read_text(encoding='utf-8')

def check(label, cond):
    if not cond: raise AssertionError(label)
    print('OK:', label)

check('canonical page hero component exists', 'function eduPageHero(' in COMP and 'function eduPageHeroStart(' in COMP)
check('canonical hero exposes shared contract', "data-edu-hero'] = 'true'" in COMP and 'data-edu-hero-info' in COMP)
check('header exposes canonical hero globally', "components/page-hero.php" in HEADER)
check('calculator hero delegates to canonical component', 'eduPageHero($hero);' in CALC and 'eduPageHeroStart($config);' in CALC)
check('timeline migrated to canonical component', "eduPageHero(array(" in PAGE and "'class' => 'timeline-hero'" in PAGE and '<section class="timeline-hero">' not in PAGE)
check('mobile JS prioritises canonical hero marker', '[data-edu-hero="true"]' in JS)
check('mobile JS has resilient custom hero fallback', 'section[class*="-hero"]' in JS)
check('mobile JS recognises explicit info nodes', "child.getAttribute('data-edu-hero-info') === 'true'" in JS)
check('shared CSS styles canonical hero on mobile', '[data-edu-hero="true"]' in CSS)
check('accessible info control retained', 'Πληροφορίες εργαλείου' in JS and "aria-expanded" in JS)
for rel in ['includes/components/page-hero.php','includes/components/calculator-layout.php','includes/header.php','xronodiagramma-ypiresiakon-metavolon.php']:
    proc=subprocess.run(['php','-l',str(ROOT/rel)],capture_output=True,text=True)
    check('PHP syntax '+rel, proc.returncode==0)
proc=subprocess.run(['node','--check',str(ROOT/'assets/common.js')],capture_output=True,text=True)
check('common JS syntax',proc.returncode==0)
ver=re.search(r"EDU_TOOLS_VERSION', '([0-9.]+)'",CONFIG); cache=re.search(r"CACHE_PREFIX \+ '([0-9.]+)'",SW)
check('version at least 3.22.49', bool(ver) and tuple(map(int, ver.group(1).split('.'))) >= (3,22,49))
check('cache matches current release', bool(ver and cache) and cache.group(1)==ver.group(1))
print('RESULT: shared page hero v3.22.49 contract PASS')
