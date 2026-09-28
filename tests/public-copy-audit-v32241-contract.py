from pathlib import Path

ROOT = Path(__file__).resolve().parents[1]

public_files = [
    ROOT / 'ypologismos-morion-metathesis.php',
    ROOT / 'ypologismos-morion-apospasis-sde.php',
    ROOT / 'ypologismos-morion-anapliroti-psifiako-frontistirio.php',
    ROOT / 'ypologismos-morion-onaseia.php',
    ROOT / 'orologio-programma-mathimaton.php',
    ROOT / 'ypologismos-misthologikou-klimakiou.php',
    ROOT / 'ypologismos-didaktikon-anagkon.php',
    ROOT / 'kena-sxoleion-users.php',
    ROOT / 'assets/app-experience.js',
    ROOT / 'includes/staffing-simulator-ui.js',
    ROOT / 'includes/myschool-stat51-import.js',
    ROOT / 'includes/myschool-staff-import.js',
]

forbidden = [
    'Πρώτη έκδοση',
    'πρώτη έκδοση',
    'Η τρέχουσα έκδοση',
    'στην παρούσα έκδοση',
    'portable schema',
    'browser-side',
    'component ήταν',
    'atomic πρόταση',
    'Τεχνικό όριο ασφαλείας',
    'countdown χρησιμοποιεί τεχνικά',
    'Προσωρινό μητρώο browser',
    'τρέχουσα καρτέλα του browser',
    'συνεδρία του browser',
    'CSV importer',
    'προστέθηκε στον browser',
    'πιστοποιήθηκε από τον optimizer',
    'εσωτερική εξισορρόπηση είναι έγκυρη και atomic',
    'ελέγχονται ξανά στον server',
    'τοπικό importer',
]

failures = []
for path in public_files:
    text = path.read_text(encoding='utf-8')
    for phrase in forbidden:
        if phrase in text:
            failures.append(f'{path.name}: still exposes developer-facing phrase: {phrase!r}')

required = {
    'ypologismos-morion-metathesis.php': [
        'Τι περιλαμβάνει:',
        'Ο υπολογισμός βασίζεται στην εγκύκλιο μεταθέσεων',
    ],
    'ypologismos-didaktikon-anagkon.php': [
        'Το αρχείο παραμένει στη συσκευή σου.',
        'Μέγιστο όριο καταχώρισης:',
        'Η συγκεκριμένη περίπτωση είναι πολύ σύνθετη για να επιβεβαιωθεί',
        'Τα στοιχεία παραμένουν προσωρινά μόνο στη συγκεκριμένη καρτέλα.',
    ],
    'includes/staffing-simulator-ui.js': [
        'Η αυτόματη πρόταση προστέθηκε:',
        'Ο έλεγχος ωραρίων ολοκληρώθηκε:',
        'Ο έλεγχος κατανομής ολοκληρώθηκε:',
    ],
    'assets/app-experience.js': [
        'Αν το πρόγραμμα περιήγησής σου υποστηρίζει εγκατάσταση εφαρμογών',
    ],
}
for name, phrases in required.items():
    text = (ROOT / name).read_text(encoding='utf-8')
    for phrase in phrases:
        if phrase not in text:
            failures.append(f'{name}: missing expected end-user wording: {phrase!r}')

config = (ROOT / 'includes/config.php').read_text(encoding='utf-8')
worker = (ROOT / 'service-worker.js').read_text(encoding='utf-8')
if "define('EDU_TOOLS_VERSION', '3.22.41');" not in config:
    failures.append('includes/config.php: EDU_TOOLS_VERSION is not 3.22.41')
if "CACHE_PREFIX + '3.22.41'" not in worker:
    failures.append('service-worker.js: cache is not 3.22.41')

if failures:
    raise SystemExit('\n'.join(failures))

print('Public copy audit v3.22.41: PASS')
