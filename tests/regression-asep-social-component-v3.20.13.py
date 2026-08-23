from pathlib import Path
from bs4 import BeautifulSoup
import hashlib, re, subprocess, sys

ROOT = Path(__file__).resolve().parents[1]
BASE = Path('/mnt/data/asep-toolkit-v3.20.12-rc2-work/v3.20.12-rc2')
PAGES = {
    'ypologismos-morion.php': {'marriage':'marriageYears4Plus','mental':'candidateMentalCondition','warning':None,'subtotal':'socialSubtotal','aux':False},
    'ypologismos-morion-1gt-2024.php': {'marriage':'marriageYears4Plus','mental':'candidateMentalCondition','warning':'socialWarning','subtotal':'socialSubtotal','aux':False},
    'ypologismos-morion-1ea-2025.php': {'marriage':'marriage4','mental':'mental','warning':'socialWarning','subtotal':'socialSubtotal','aux':False},
    'ypologismos-morion-2ea-2025.php': {'marriage':'marriage4','mental':'mental','warning':'socialWarning','subtotal':'socialSubtotal','aux':False},
    'ypologismos-morion-3ea-2025.php': {'marriage':'marriageYears4Plus','mental':'candidateMentalCondition','warning':'socialWarnings','subtotal':None,'aux':True},
    'ypologismos-morion-4ea-2025.php': {'marriage':'marriageYears4Plus','mental':'candidateMentalCondition','warning':'socialWarning','subtotal':'socialSubtotal','aux':True},
}
NON_ASEP = [
    'ypologismos-morion-mitroo-sde.php','ypologismos-morion-apospasis-sde.php',
    'ypologismos-morion-diefthynton-ypodiefthynton-sde.php','ypologismos-morion-onaseia.php',
    'ypologismos-morion-apospasis.php','ypologismos-morion-apospasis-dimos.php',
    'ypologismos-morion-apospasis-exoteriko.php','ypologismos-morion-apospasis-evropaika-scholeia.php',
    'ypologismos-morion-apospasis-psifiako-frontistirio.php'
]
pass_n=0; fail_n=0

def ok(cond,msg):
    global pass_n,fail_n
    if cond:
        pass_n+=1; print('✓',msg)
    else:
        fail_n+=1; print('✗',msg)

def scripts(text):
    return re.findall(r'<script(?:\s[^>]*)?>(.*?)</script>', text, flags=re.S|re.I)

partial=(ROOT/'includes/components/asep-social-criteria.php').read_text(encoding='utf-8')
ok('fn(' not in partial and ' fn ' not in partial, 'Partial uses no PHP arrow functions')
ok('??' not in partial, 'Partial uses no null-coalescing operator')
ok(not re.search(r'function\s+renderAsepSocialCriteria\s*\([^)]*\)\s*:', partial), 'Partial uses no return type')
ok('EducationSocial' not in partial and '.calculate' not in partial, 'Partial contains no calculation-engine call')
ok('seminar400' not in partial and 'auxSeminar400' not in partial, 'Partial knows nothing about 400h seminar controls')

# Executable JS and existing training component must be byte-identical.
for rel in ['assets/common.js','includes/social-calculations.js','includes/service-calculations.js','includes/academic-calculations.js','includes/te-academic-calculations.js','includes/language-calculations.js','includes/components/training-proof.php']:
    ok((ROOT/rel).read_bytes()==(BASE/rel).read_bytes(), rel+' byte-identical to v3.20.12-rc2')

for name,cfg in PAGES.items():
    text=(ROOT/name).read_text(encoding='utf-8')
    old=(BASE/name).read_text(encoding='utf-8')
    ok(text.count("includes/components/asep-social-criteria.php")==1, name+': one social component include')
    ok(text.count('renderAsepSocialCriteria(array(')==1, name+': one renderer call')
    ok("'child_points' => 3" in text, name+': child points explicitly declared')
    ok("'min_disability_percent' => 50" in text, name+': 50% threshold explicitly declared')
    ok("'disability_rate' => '0,4'" in text, name+': 0.4 rate explicitly declared')
    ok("'spouse_min_marriage_years' => 4" in text, name+': four-year marriage rule explicitly declared')
    ok(scripts(text)==scripts(old), name+': inline JavaScript byte-for-byte unchanged')

    rendered=subprocess.check_output(['php',str(ROOT/name)],cwd=str(ROOT)).decode('utf-8')
    soup=BeautifulSoup(rendered,'html.parser')
    comps=soup.select('[data-component="asep-social-criteria"]')
    ok(len(comps)==1,name+': exactly one rendered social component')
    comp=comps[0] if comps else soup
    ids=[x.get('id') for x in soup.select('[id]')]
    ok(len(ids)==len(set(ids)),name+': no duplicate rendered IDs')
    for eid in ['children','candidateDisability','spouseDisability','childDisability',cfg['marriage'],cfg['mental']]:
        ok(comp.select_one('#'+eid) is not None,name+': rendered ID '+eid)
    labels=[' '.join(x.stripped_strings) for x in comp.find_all('label')]
    joined='\n'.join(labels)
    ok('Αριθμός επιλέξιμων τέκνων 3 μόρια ανά τέκνο.' in joined,name+': unified children description')
    ok('Αναπηρία υποψηφίου/ας (%) Από 50% και άνω' in joined,name+': unified candidate description')
    ok('Αναπηρία συζύγου (%) Από 50% και άνω, με έγγαμο βίο τουλάχιστον 4 ετών.' in joined,name+': unified spouse description')
    ok('Υψηλότερο ποσοστό αναπηρίας τέκνου (%) Από 50% και άνω.' in joined,name+': unified child description')
    note=comp.select_one('.asep-social-rule-note')
    ok(note is not None and 'ποσοστό × 0,4' in note.get_text(' ',strip=True),name+': common highest-percentage explanation')
    if cfg['warning']:
        ok(comp.select_one('#'+cfg['warning']) is not None,name+': warning region preserved')
    if cfg['subtotal']:
        ok(comp.select_one('#'+cfg['subtotal']) is not None,name+': social subtotal preserved')
    else:
        ok(comp.select_one('#socialSubtotal') is None,name+': no new subtotal introduced')
    child=comp.select_one('label[for="childDisability"]')
    child_text=child.get_text(' ',strip=True) if child else ''
    ok(('67%' in child_text)==cfg['aux'],name+': 67% EAE note scoped correctly')

for name in NON_ASEP:
    text=(ROOT/name).read_text(encoding='utf-8')
    ok('asep-social-criteria.php' not in text and 'renderAsepSocialCriteria' not in text,name+': excluded from ASEP social component')

# Digital Tutoring source link added in rc2 must survive this refactor.
digital=(ROOT/'ypologismos-morion-apospasis-psifiako-frontistirio.php').read_text(encoding='utf-8')
ok('934%CE%9946%CE%9D%CE%9A%CE%A0%CE%94-80%CE%9C?inline=true' in digital,'Digital Tutoring Diavgeia source preserved')

print('\nASEP social component v3.20.13: PASS',pass_n,'/ FAIL',fail_n)
sys.exit(1 if fail_n else 0)
