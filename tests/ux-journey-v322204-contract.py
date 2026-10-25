#!/usr/bin/env python3
"""Static UX invariants for the 3.22.204 progressive-enhancement patch."""
from pathlib import Path
import re
root=Path(__file__).resolve().parents[1]
page=(root/'ergaleia.php').read_text()
js=(root/'assets/tools-directory.js').read_text()
common=(root/'assets/common.js').read_text()
ped=(root/'paidagogiki-eparkeia.php').read_text()
assert page.index('id="tools-directory"')<page.index('id="tool-categories"')<page.index('id="toolGroups"')<page.index('id="eduOfflineSaveAll"')
assert 'id="resetToolDirectory"' in page
assert "document.body.classList.toggle('edu-directory-filtering'" in js
assert "setFilter('all', false)" in js
assert '.app-box' in common and "main.setAttribute('role', 'main')" in common
for role in ('proofType','namedPostgraduateSearch','namedSpecialProgramSearch','namedSpecialProgramExactMatch'):
    assert re.search(r'<(?:select|input)\b(?=[^>]*data-role="'+role+r'")(?=[^>]*aria-label=)[^>]*>',ped),role
for name in ('nomiki-epikairotita.php','elegxos-proypotheseon-diefthynti.php'):
    assert 'assets/common.js' in (root/name).read_text()
version="'3.22.204'"
for name in ('includes/config.php','service-worker.js','assets/offline-registry.js'):
    assert version in (root/name).read_text(),name
print('PASS: search-first directory, recovery, accessible landmarks and labels, shared UX, release cache')
