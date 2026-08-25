from pathlib import Path
import re, sys
root=Path(__file__).resolve().parents[1]
checks=[]
def check(name, cond):
    checks.append((name, bool(cond)))

pages={
 'abroad':'ypologismos-morion-apospasis-exoteriko.php',
 'leadership':'ypologismos-morion-diefthynton-ypodiefthynton-sde.php',
 'registry':'ypologismos-morion-mitroo-sde.php',
}
for key,fn in pages.items():
    s=(root/fn).read_text()
    check(f'{key}: uses staged score header', "'variant' => 'staged'" in s and 'calculatorScoreHeader(array(' in s)
    check(f'{key}: keeps big-total compatibility class', "'class' => 'big-total'" in s)

s=(root/pages['abroad']).read_text()
check('abroad: grandTotal ID preserved', "'value_id' => 'grandTotal'" in s)
check('abroad: totalOutOf ID preserved', "'cap_id' => 'totalOutOf'" in s)
s=(root/pages['leadership']).read_text()
for i in ('totalContext','totalScore','totalOutOf'):
    check(f'leadership: {i} ID preserved', i in s)
s=(root/pages['registry']).read_text()
check('registry: finalScore ID preserved', "'value_id' => 'finalScore'" in s)

# R8: European Schools keeps its two-stage semantics, now through two shared score headers.
e=(root/'ypologismos-morion-apospasis-evropaika-scholeia.php').read_text()
check('European Schools: first-stage score preserved', 'id="preInterviewTotal"' in e and '1ο στάδιο · πριν τη συνέντευξη' in e)
check('European Schools: final score preserved', "'value_id' => 'finalTotal'" in e and 'Τελική βαθμολογία' in e)
check('European Schools: uses two shared score headers', e.count('calculatorScoreHeader(array(') == 2)

# No cache bump for PHP-only structural refactor.
config=(root/'includes/config.php').read_text()
check('R5 keeps 3.20.54 cache version', "EDU_TOOLS_VERSION', '3.20.54" in config)

failed=[n for n,v in checks if not v]
for n,v in checks: print(('PASS' if v else 'FAIL')+': '+n)
print(f'\n{sum(v for _,v in checks)}/{len(checks)} PASS')
sys.exit(1 if failed else 0)
