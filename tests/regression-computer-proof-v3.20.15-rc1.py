#!/usr/bin/env python3
from pathlib import Path
import re, subprocess, sys, hashlib, json, tempfile
ROOT=Path(sys.argv[1] if len(sys.argv)>1 else '/mnt/data/asep-toolkit-v3.20.15-rc1-production')
BASE=Path('/mnt/data/asep-toolkit-v3.20.14-rc2-production')
PAGES=['ypologismos-morion.php','ypologismos-morion-1gt-2024.php','ypologismos-morion-1ea-2025.php','ypologismos-morion-2ea-2025.php','ypologismos-morion-3ea-2025.php','ypologismos-morion-4ea-2025.php']
passed=failed=0

def t(name, cond, detail=''):
    global passed,failed
    if cond:
        passed+=1; print('✓ '+name)
    else:
        failed+=1; print('✗ '+name+((' — '+detail) if detail else ''))

def text(p): return p.read_text(encoding='utf-8')
def sha(p): return hashlib.sha256(p.read_bytes()).hexdigest()

comp=text(ROOT/'includes/components/asep-computer-proof.php')
js=text(ROOT/'includes/asep-computer-proof.js')
css=text(ROOT/'assets/common.css')

print('=== ASEP computer-proof component ===')
t('component exists', (ROOT/'includes/components/asep-computer-proof.php').exists())
t('UI JS exists', (ROOT/'includes/asep-computer-proof.js').exists())
t('component is presentation-only: no scoring assignment', not re.search(r'points\s*[+*=]|computerPoints|academicPoints|score\s*=', comp, re.I))
t('UI JS contains no scoring logic', not re.search(r'computerPoints|academicPoints|score\s*=|\+\s*(4|20)\b', js))
t('component states proof check does not imply scoring', 'τρόπο απόδειξης' in comp and 'μοριοδότηση' in comp)
for needle in [
    'Πιστοποιητικό γνώσης πληροφορικής ή χειρισμού Η/Υ',
    'Π.Δ. 85/2022',
    'τουλάχιστον τέσσερα μαθήματα',
    'Κρατικό Πιστοποιητικό Πληροφορικής',
    'Εθνική Σχολή Δημόσιας Διοίκησης και Αυτοδιοίκησης',
    'Τ.Π.Ε.) Α΄ επιπέδου'
]: t('proof method text: '+needle[:38], needle in comp)
t('exactly 6 proof choices plus placeholder', len(re.findall(r'<option value="(?:certificate|informatics-title|four-courses|state-certificate|esdda|tpe-a)"',comp))==6)
t('three knowledge objects are named', all(x in comp for x in ['επεξεργασία κειμένου','υπολογιστικά φύλλα','υπηρεσίες διαδικτύου']))
t('component contains no PE86 scoring implementation', not re.search(r'\$specialty|\$branch|if\s*\([^)]*PE86|computerPoints|points\s*[+*=]', comp, re.I))
t('PE86 appears only as explanatory warning in component', comp.count('ΠΕ86')==1)

print('\n=== Six ASEP calculators ===')
for f in PAGES:
    s=text(ROOT/f)
    t(f+': requires shared computer component', "includes/components/asep-computer-proof.php" in s)
    t(f+': loads shared computer UI module', 'includes/asep-computer-proof.js?v=3.20.15-rc1' in s)
    t(f+': declares shared renderer', 'renderAsepComputerProof' in s)
    t(f+': keeps input id computer', "'input_id' => 'computer'" in s)
    t(f+': old computer label removed', 'Πιστοποιημένη γνώση χειρισμού Η/Υ Α΄ επιπέδου' not in s and 'Πιστοποιημένη γνώση Η/Υ ' not in s)
    r=subprocess.run(['php',str(ROOT/f)],capture_output=True,text=True)
    t(f+': PHP render succeeds',r.returncode==0,r.stderr.strip())
    out=r.stdout
    t(f+': rendered component exactly once',out.count('data-component="asep-computer-proof"')==1)
    t(f+': rendered quick check exactly once',out.count('Γρήγορος έλεγχος τρόπου απόδειξης')==1)
    t(f+': rendered 6 proof options',sum(out.count('value="'+v+'"') for v in ['certificate','informatics-title','four-courses','state-certificate','esdda','tpe-a'])==6)
    t(f+': training-proof still rendered once',out.count('id="trainingProof"')==1)
    t(f+': social component still rendered once',out.count('data-component="asep-social-criteria"')==1)
    t(f+': three-month service component still rendered once',out.count('data-component="asep-three-month-service"')==1)

# score labels explicit per page
for f in ['ypologismos-morion.php','ypologismos-morion-1ea-2025.php','ypologismos-morion-2ea-2025.php','ypologismos-morion-3ea-2025.php']:
    t(f+': explicitly passes 4 points label', "'points_text' => '4 μόρια'" in text(ROOT/f))
for f in ['ypologismos-morion-1gt-2024.php','ypologismos-morion-4ea-2025.php']:
    t(f+': explicitly passes 20 points label', "'points_text' => '20 μόρια'" in text(ROOT/f))

print('\n=== PE86 locks ===')
main=text(ROOT/'ypologismos-morion.php'); ea3=text(ROOT/'ypologismos-morion-3ea-2025.php'); acad=text(ROOT/'includes/academic-calculations.js')
t('1GE/2GE explicitly keeps PE86 no-score note', "'restriction_note' => 'Δεν μοριοδοτείται στον ΠΕ86.'" in main)
t('3EA explicitly keeps PE86 no-score note', "'restriction_note' => 'Δεν μοριοδοτείται στον ΠΕ86.'" in ea3)
t('3EA still disables computer checkbox for PE86', "$('computer').disabled=(sp==='ΠΕ86')" in ea3)
t('3EA still excludes PE86 from +4 calculation', "$('computer').checked && sp!=='ΠΕ86'" in ea3)
t('1GE shared academic module still excludes PE86', 'specialty === "ΠΕ86"' in acad and 'computerPoints = RULES.computerPoints' in acad)
t('academic calculation module byte-identical to rc2', sha(ROOT/'includes/academic-calculations.js')==sha(BASE/'includes/academic-calculations.js'))

print('\n=== Regression isolation ===')
# Inline JS unchanged in all six pages.
pat=re.compile(r'<script(?![^>]*\bsrc=)[^>]*>(.*?)</script>',re.S|re.I)
for f in PAGES:
    t(f+': inline JS byte-for-byte unchanged', pat.findall(text(ROOT/f))==pat.findall(text(BASE/f)))
for f in ['ypologismos-morion-mitroo-sde.php','ypologismos-morion-apospasis-sde.php','ypologismos-morion-onaseia.php']:
    t(f+': non-ASEP computer tool unchanged', sha(ROOT/f)==sha(BASE/f))
for f in ['service-calculations.js','social-calculations.js','te-academic-calculations.js','sde-registry-calculations.js']:
    t(f+': calculation module unchanged', sha(ROOT/'includes'/f)==sha(BASE/'includes'/f))

t('responsive/scoped CSS exists', all(x in css for x in ['.asep-computer-proof-panel','@media (max-width:650px)','overflow-wrap:anywhere']))
t('component CSS is scoped under body.edu-ui', 'body.edu-ui .asep-computer-proof-panel' in css)

print('\n=== Result ===')
print('PASS:',passed)
print('FAIL:',failed)
sys.exit(1 if failed else 0)
