from pathlib import Path
import re
root=Path(__file__).resolve().parents[1]
page=(root/'ypologismos-morion-metathesis.php').read_text(encoding='utf-8')
catalog=(root/'includes/tools-catalog.php').read_text(encoding='utf-8')
js=(root/'includes/transfer-ui.js').read_text(encoding='utf-8')
css=(root/'assets/common.css').read_text(encoding='utf-8')
title='Μεταθέσεις μόνιμων εκπαιδευτικών Δ.Ε.'
assert '<title>'+title+'</title>' in page
assert "'title' => '"+title+"'" in page and "'title' => '"+title+"'" in catalog
assert 'class="field transfer-main-mode-field"' in page
assert '@media (min-width: 651px)' in css and '.transfer-main-mode-field' in css
assert 'grid-column: 1 / -1' in css
assert '.actions:has(#resetBtn)' in css and 'grid-template-columns: minmax(240px, 60%)' in css
assert 'ΣΜΕΑΕ / ΕΕΕΕΚ — ενδεικτικά μόρια:' not in js
assert "if (mode === 'smeae')" in js and "else if (mode === 'eae_sensory')" in js and "else if (mode === 'kedasy')" in js
assert 'είτε έχεις τώρα οργανική στη Γενική είτε στην ΕΑΕ' in js
assert 'Για βελτίωση θέσης εντός της ίδιας περιοχής στην ΕΑΕ' in js
assert "byId('resetBtn').addEventListener('click', reset);" in js
assert '#mode' in css and 'id="mode"' in page
print('PASS 3.22.127 desktop select/reset, catalogue title, EAE text, unchanged reset binding')
