#!/usr/bin/env python3
"""Directory's visual compaction must preserve labels and a keyboard-accessible deadline link."""
from pathlib import Path
import re
import subprocess
ROOT = Path(__file__).resolve().parents[1]
source = (ROOT/'ergaleia.php').read_text(encoding='utf-8')
css = (ROOT/'assets/common.css').read_text(encoding='utf-8')
header = (ROOT/'includes/header.php').read_text(encoding='utf-8')
assert 'ΚΑΤΑΛΟΓΟΣ' not in source
assert 'Βρες το εργαλείο που χρειάζεσαι' not in source
assert 'deadline-shortcut' not in source and 'deadline-shortcut' not in css
assert 'aria-labelledby="toolSearchHeading"' in source
assert '<h2 class="edu-tools-sr-only" id="toolSearchHeading">Αναζήτηση εργαλείων</h2>' in source
assert '.edu-tools-sr-only {' in css
assert '<input aria-label="Αναζήτηση εργαλείου"' in source
assert 'href="prothesmies.php"' in header and 'aria-label="Προθεσμίες"' in header
output = subprocess.run(['php','ergaleia.php'],cwd=str(ROOT),capture_output=True,text=True,check=True).stdout
assert 'aria-labelledby="toolSearchHeading"' in output
assert len(re.findall(r'href="prothesmies\.php"',output)) == 1
assert 'id="toolSearch"' in output
assert 'id="toolGroups"' in output
assert 'Εργαλειοθήκη Εκπαιδευτικού' in output
print('PASS: visually compact search, accessible screen-reader heading, search name and single nav deadline link')
