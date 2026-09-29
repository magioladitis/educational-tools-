from pathlib import Path
import io, re, subprocess, zipfile

ROOT = Path(__file__).resolve().parents[1]
PAGE = (ROOT/'xronodiagramma-ypiresiakon-metavolon.php').read_text(encoding='utf-8')
DATA = (ROOT/'includes/service-timeline-data.php').read_text(encoding='utf-8')
XLSX = (ROOT/'includes/service-timeline-xlsx.php').read_text(encoding='utf-8')
EXPORT = (ROOT/'xronodiagramma-ypiresiakon-metavolon-export.php').read_text(encoding='utf-8')
CSS = (ROOT/'assets/common.css').read_text(encoding='utf-8')
CONFIG = (ROOT/'includes/config.php').read_text(encoding='utf-8')
SW = (ROOT/'service-worker.js').read_text(encoding='utf-8')

def check(label, cond):
    if not cond:
        raise AssertionError(label)
    print('OK:', label)

check('hero process count removed', '<?php echo count($events); ?> διαδικασίες</span>' not in PAGE and 'διαδικασίες με επιβεβαιωμένη ημερομηνία' not in PAGE)
check('Excel export button is present', 'Εξαγωγή σε XLS' in PAGE and 'xronodiagramma-ypiresiakon-metavolon-export.php' in PAGE)
check('export control has dedicated styling', '.timeline-export-link' in CSS and '.timeline-control-actions' in CSS)
check('dedicated exporter exists', (ROOT/'xronodiagramma-ypiresiakon-metavolon-export.php').exists() and (ROOT/'includes/service-timeline-xlsx.php').exists())
check('export uses xlsx MIME and filename', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' in EXPORT and '.xlsx' in EXPORT)
check('chronology and sources sheets are generated', 'name="Χρονολόγιο"' in XLSX and 'name="Πηγές"' in XLSX)
for header in ['Κατηγορία','Διαδικασία','Τελευταία τιμή','Παρατηρήσεις','Έτος / κύκλος','Σύνδεσμος']:
    check('export header '+header, header in XLSX)
check('PE mutual-result history fully sourced for stored years', "'verified_history_indices' => array(3,4,5,6)" in DATA and '58489-31-05-24' in DATA and '61328-24-04-25' in DATA)
check('DE mutual-result history fully sourced for stored years', "'verified_history_indices' => array(2,3,4,5,6)" in DATA and '52084-11-05-22' in DATA and '58654%3A19-06-24' in DATA and '61662-30-05-25' in DATA)
check('old remaining-source copy removed', 'παλαιότερα αποτελέσματα αμοιβαίων μεταθέσεων' not in PAGE)
check('updated date bumped', "'updated_at' => '29/09/2026'" in DATA)
ver = re.search(r"EDU_TOOLS_VERSION', '([0-9.]+)'", CONFIG)
cache = re.search(r"CACHE_PREFIX \+ '([0-9.]+)'", SW)
check('version remains at or beyond 3.22.45', bool(ver) and tuple(map(int, ver.group(1).split('.'))) >= (3,22,45))
check('cache remains at or beyond 3.22.45', bool(cache) and tuple(map(int, cache.group(1).split('.'))) >= (3,22,45))

proc = subprocess.run(['php','xronodiagramma-ypiresiakon-metavolon-export.php'], cwd=ROOT, capture_output=True)
check('export endpoint executes', proc.returncode == 0)
check('export endpoint emits ZIP/XLSX bytes', proc.stdout.startswith(b'PK'))
if proc.returncode == 0 and proc.stdout.startswith(b'PK'):
    with zipfile.ZipFile(io.BytesIO(proc.stdout)) as zf:
        names = set(zf.namelist())
        check('xlsx has two worksheet parts', 'xl/worksheets/sheet1.xml' in names and 'xl/worksheets/sheet2.xml' in names)
        workbook = zf.read('xl/workbook.xml').decode('utf-8')
        check('xlsx workbook sheet names', 'Χρονολόγιο' in workbook and 'Πηγές' in workbook)
        sheet1 = zf.read('xl/worksheets/sheet1.xml').decode('utf-8')
        check('xlsx chronology contains mutual-transfer rows', 'Αμοιβαίες μεταθέσεις Π.Ε. — ανακοίνωση' in sheet1 and 'Αμοιβαίες μεταθέσεις Δ.Ε. — ανακοίνωση' in sheet1)

print('RESULT: service timeline v3.22.45 export contract PASS')
