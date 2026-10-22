#!/usr/bin/env python3
"""Cross-level render and core DOM contract; run without a browser or network."""
from pathlib import Path
from bs4 import BeautifulSoup
from collections import Counter
import subprocess

ROOT = Path(__file__).resolve().parents[1]
CASES = {
    'primary': ('ypologismos-morion-metathesis-protovathmia.php', ['primaryChecklist', 'primaryPrintChecklist', 'primaryEmailOpen', 'primaryScoreResults', 'primaryMsdPeriodsRows']),
    'secondary': ('ypologismos-morion-metathesis.php', ['transferDocumentChecklist', 'transferPrintChecklist', 'transferEmailFeatureOpen', 'transferScoringResults', 'msdRows']),
}
for level, (file, ids_required) in CASES.items():
    result = subprocess.run(['php', file], cwd=ROOT, capture_output=True, text=True, check=True)
    assert '<html' in result.stdout.lower() and '</html>' in result.stdout.lower(), f'{level}: no HTML'
    soup = BeautifulSoup(result.stdout, 'html.parser')
    ids = [e['id'] for e in soup.select('[id]')]
    duplicates = {id_ for id_, count in Counter(ids).items() if count > 1}
    assert not duplicates, f'{level}: duplicate ids {duplicates}'
    for id_ in ids_required:
        assert soup.find(id=id_) is not None, f'{level}: missing {id_}'
    for label in soup.select('label[for]'):
        assert soup.find(id=label['for']) is not None, f'{level}: broken label {label["for"]}'
    for button_id in [id_ for id_ in ids_required if 'PrintChecklist' in id_]:
        assert soup.find(id=button_id).get('type') == 'button'
    assert soup.find('html').get('lang') == 'el'
    assert soup.find('meta', attrs={'name':'viewport'}) is not None
    print(f'PASS {level}: render; {len(ids)} unique IDs; required controls; label targets; lang/viewport')
print('PASS cross-level rendered HTML contracts')
