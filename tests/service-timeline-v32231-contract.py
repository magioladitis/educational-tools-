from pathlib import Path

ROOT = Path(__file__).resolve().parents[1]
PAGE = (ROOT / 'xronodiagramma-ypiresiakon-metavolon.php').read_text(encoding='utf-8')
DATA = (ROOT / 'includes' / 'service-timeline-data.php').read_text(encoding='utf-8')
CATALOG = (ROOT / 'includes' / 'tools-catalog.php').read_text(encoding='utf-8')
CSS = (ROOT / 'assets' / 'common.css').read_text(encoding='utf-8')
JS = (ROOT / 'assets' / 'service-timeline.js').read_text(encoding='utf-8')
CONFIG = (ROOT / 'includes' / 'config.php').read_text(encoding='utf-8')
SW = (ROOT / 'service-worker.js').read_text(encoding='utf-8')

def check(label, condition):
    if not condition:
        raise AssertionError(label)
    print('OK:', label)

check('timeline page added', 'Χρονοδιάγραμμα Εκπαιδευτικών' in PAGE)
check('verified-only default exists', 'timelineVerifiedOnly' in PAGE and 'checked' in PAGE)
check('historical source disclaimer exists', 'Οι παλαιότερες ημερομηνίες' in PAGE)
check('user-confirmed 2024 detachment date corrected', "'history' => array('30/04–11/05/2020','20–27/04/2021','05–15/04/2022','06–18/04/2023','08–17/04/2024'" in DATA)
check('2026 transfer circular corrected and sourced', "'30/04/2026'" in DATA and '52463%CE%952_30-04-2026' in DATA)
check('metatakseis outcome split by category', "'metatakseis-to-eep'" in DATA and "'metatakseis-to-primary'" in DATA and "'metatakseis-to-secondary'" in DATA)
check('official Ministry sources are present', DATA.count('https://www.minedu.gov.gr/') >= 18)
check('catalog entry added', "'number' => 36" in CATALOG and "xronodiagramma-ypiresiakon-metavolon.php" in CATALOG)
check('timeline JS loaded', 'service-timeline.js' in PAGE and 'data-timeline-filter' in JS)
check('timeline CSS scoped', 'body.edu-page-service-timeline' in CSS)
check('source expansion includes all working-sheet milestones', DATA.count("'id' =>") >= 32)
check('resignation window sourced', "'resignation-withdrawal'" in DATA and "11/03/2026" in DATA)
check('detachment withdrawal deadlines sourced', "'bodies-detachment-application-withdrawal'" in DATA and "'27/04/2026'" in DATA and "'detachment-application-withdrawal'" in DATA and "'20/05/2026'" in DATA)
check('metataksi withdrawal sourced', "'metatakseis-application-withdrawal'" in DATA and "'27/05/2026'" in DATA)
check('primary functional gaps circular documented', "'functional-gaps-primary-circular'" in DATA and "73719/Ε2" in DATA and "official-document-copy" in DATA)
check('secondary functional gaps protocol conflict preserved', "'functional-gaps-secondary-circular'" in DATA and "74045/Ε2" in DATA and "74047/Ε2" in DATA and "αρ. πρωτ. υπό διασταύρωση" in DATA)
check('legacy functional gaps history preserved separately', "'functional-gaps-history'" in DATA and "Ιστορικό αρχείου" in DATA)
check('transfer objections no longer presented as a 2026 fixed deadline', "Αιτήσεις θεραπείας / επανεξέτασης" in DATA and "δεν εντοπίστηκε ενιαία δημοσιευμένη προθεσμία" in DATA and "αμοιβαίες μεταθέσεις" in DATA)
check('verification label override supported', 'verification_label' in DATA and 'verificationLabel' in PAGE)
check('newly appointed application window verified', "'newly-appointed-detachment-applications'" in DATA and "'26/08–01/09/2026'" in DATA)
check('version bumped', "EDU_TOOLS_VERSION', '3.22.31'" in CONFIG)
check('cache bumped', "CACHE_PREFIX + '3.22.31'" in SW)
