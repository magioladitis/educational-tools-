from pathlib import Path
import subprocess
import tempfile

ROOT = Path(__file__).resolve().parents[1]
component = (ROOT / 'includes' / 'components' / 'deadline-card.php').read_text(encoding='utf-8')
objection_page = (ROOT / 'odigos-enstasis.php').read_text(encoding='utf-8')
objection_js = (ROOT / 'includes' / 'objection-guide-ui.js').read_text(encoding='utf-8')
paidagogiki = (ROOT / 'paidagogiki-eparkeia.php').read_text(encoding='utf-8')
saek = (ROOT / 'dikaioma-ypodiefthynti-saek.php').read_text(encoding='utf-8')

checks = [
    ('embedded deadline cards default to seven-day archive window', ": 7;" in component and "archive_after_days" in component),
    ('deadline items are filtered before outer card renders', '$visibleItems = array();' in component and 'if (count($items) === 0)' in component and 'return;' in component),
    ('objection deadline banner has seven-day lifecycle wrapper', '$objectionDeadlineArchiveCutoff = time() - (7 * 86400);' in objection_page),
    ('objection JS tolerates archived deadline banner', "if(!box) return;" in objection_js and "if(document.getElementById('deadlineStatus'))" in objection_js),
    ('pedagogical deadline copy is historical rather than falsely current', 'Η πρόσκληση αφορούσε αιτήσεις συμμετοχής' in paidagogiki),
    ('SAEK source deadline is expressed in past tense', 'Η προθεσμία αιτήσεων ήταν' in saek),
]

# Runtime contract for the shared renderer: an old item disappears by default,
# a future item renders, and archive_after_days=0 remains an explicit opt-out.
php = r'''<?php
require __DIR__ . '/includes/components/deadline-card.php';
ob_start();
renderDeadlineCard(array(
  'title' => 'OLD',
  'items' => array(array('title' => 'old-item', 'end' => '2000-01-01T00:00:00+00:00'))
));
$old = ob_get_clean();
ob_start();
renderDeadlineCard(array(
  'title' => 'FUTURE',
  'items' => array(array('title' => 'future-item', 'end' => '2099-01-01T00:00:00+00:00'))
));
$future = ob_get_clean();
ob_start();
renderDeadlineCard(array(
  'title' => 'HISTORY',
  'archive_after_days' => 0,
  'items' => array(array('title' => 'history-item', 'end' => '2000-01-01T00:00:00+00:00'))
));
$history = ob_get_clean();
echo json_encode(array('old' => $old, 'future' => $future, 'history' => $history));
'''
probe = ROOT / '_deadline_lifecycle_probe.php'
probe.write_text(php, encoding='utf-8')
try:
    proc = subprocess.run(['php', probe.name], cwd=ROOT, capture_output=True, text=True)
    if proc.returncode != 0:
        runtime = {'old': 'ERROR', 'future': '', 'history': ''}
    else:
        import json
        runtime = json.loads(proc.stdout)
finally:
    probe.unlink(missing_ok=True)

checks += [
    ('old deadline renders no empty shell by default', runtime.get('old') == ''),
    ('future deadline still renders normally', 'future-item' in runtime.get('future', '') and 'FUTURE' in runtime.get('future', '')),
    ('historical opt-out remains available', 'history-item' in runtime.get('history', '') and 'HISTORY' in runtime.get('history', '')),
]

failed = False
for label, ok in checks:
    print(('PASS' if ok else 'FAIL') + ': ' + label)
    failed = failed or not ok

raise SystemExit(1 if failed else 0)
