#!/usr/bin/env python3
from pathlib import Path

ROOT = Path(__file__).resolve().parents[1]
checks = []

def ok(name, cond):
    checks.append((name, bool(cond)))

# Global policy: raw <details> are reserved for the staffing simulator's structural
# input panels/popovers. Ordinary calculator/guide help must use the shared
# calculator disclosure component so mobile collapse behavior stays centralized.
raw_details = []
for p in sorted(ROOT.glob('*.php')):
    text = p.read_text(encoding='utf-8', errors='ignore')
    if '<details' in text:
        raw_details.append((p.name, text.count('<details')))
ok('only staffing simulator retains structural raw details', raw_details == [('ypologismos-didaktikon-anagkon.php', 11)])

expected_mobile = {
    'posa-paravola.php': 'Οδηγίες για την έκδοση και πληρωμή παραβόλου',
    'anatheseis-mathimaton.php': 'Τι σημαίνουν οι αναθέσεις;',
    'ypologismos-morion-apospasis-exoteriko.php': 'Τι θεωρείται κώλυμα στην τρέχουσα πρόσκληση;',
    'ypologismos-morion-apospasis-evropaika-scholeia.php': 'Ειδικές γλωσσικές απαιτήσεις θέσεων 2026',
    'ypologismos-morion-apospasis-sde.php': 'Ενδεικτικά κωλύματα',
    'ypologismos-morion-diefthynton-ypodiefthynton-sde.php': 'Ενδεικτικά κωλύματα',
}
for filename, summary in expected_mobile.items():
    text = (ROOT/filename).read_text(encoding='utf-8')
    pos = text.find(summary)
    window = text[max(0, pos-350):pos+500] if pos >= 0 else ''
    ok(f'{filename}: helper uses shared disclosure', pos >= 0 and 'calculatorDisclosure' in window)
    ok(f'{filename}: helper open on desktop', "'open' => true" in window)
    ok(f'{filename}: helper collapses on mobile', "data-mobile-collapsed' => 'true" in window)

# Reference-heavy tables/result breakdown should use common styling while
# remaining closed by default on all viewports.
closed_helpers = {
    'ypologismos-morion-apospasis-psifiako-frontistirio.php': 'Προβολή όλων των θέσεων του Παραρτήματος Ι',
    'ypologismos-morion-apospasis-sde.php': 'Προβολή όλων των αποδεκτών ειδικοτήτων / αναθέσεων',
    'ypologismos-morion-apospasis-evropaika-scholeia.php': 'Αναλυτική κατανομή μορίων',
}
for filename, summary in closed_helpers.items():
    text = (ROOT/filename).read_text(encoding='utf-8')
    pos = text.find(summary)
    window = text[max(0,pos-300):pos+350] if pos >= 0 else ''
    ok(f'{filename}: large helper uses shared disclosure', pos >= 0 and 'calculatorDisclosure' in window)
    ok(f'{filename}: large helper not forced open', "'open' => true" not in window)

# Result-only explanatory cards in SDE tools should be compact on phones.
for filename, summary in [
    ('ypologismos-morion-diefthynton-ypodiefthynton-sde.php', 'Σημαντικός κανόνας'),
    ('ypologismos-morion-diefthynton-ypodiefthynton-sde.php', 'Σε περίπτωση ισοβαθμίας'),
    ('ypologismos-morion-mitroo-sde.php', 'Ισοβαθμία'),
]:
    text=(ROOT/filename).read_text(encoding='utf-8')
    pos=text.find(summary)
    window=text[max(0,pos-300):pos+600] if pos >= 0 else ''
    ok(f'{filename}: {summary} is responsive result disclosure', 'edu-result-disclosure' in window and "data-mobile-collapsed' => 'true" in window)

failed=[n for n,p in checks if not p]
for n,p in checks:
    print(('PASS' if p else 'FAIL')+': '+n)
print(f'\n{len(checks)-len(failed)}/{len(checks)} PASS')
raise SystemExit(1 if failed else 0)
