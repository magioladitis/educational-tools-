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
check('historical sources remain accessible without per-year audit legend', '<summary>Πηγές προηγούμενων ετών</summary>' in PAGE)
check('user-confirmed 2024 detachment date corrected', "'history' => array('30/04–11/05/2020','20–27/04/2021','05–15/04/2022','06–18/04/2023','08–17/04/2024'" in DATA)
check('2026 transfer circular corrected and sourced', "'30/04/2026'" in DATA and '52463%CE%952_30-04-2026' in DATA)
check('metatakseis outcome split by category', "'metatakseis-to-eep'" in DATA and "'metatakseis-to-primary'" in DATA and "'metatakseis-to-secondary'" in DATA)
check('official Ministry sources are present', DATA.count('https://www.minedu.gov.gr/') >= 18)
check('catalog entry added', "'number' => 36" in CATALOG and "xronodiagramma-ypiresiakon-metavolon.php" in CATALOG)
check('timeline JS loaded', 'service-timeline.js' in PAGE and 'data-timeline-filter' in JS)
check('timeline CSS scoped', 'body.edu-page-service-timeline' in CSS)
check('timeline retains broad milestone coverage after removing misleading research rows', DATA.count("'id' =>") >= 29)
check('resignation withdrawal rule sourced', "'resignation-withdrawal'" in DATA and 'μέσα σε έναν μήνα από την ημερομηνία της αίτησης' in DATA and "'11/03/2026'" in DATA)
check('detachment withdrawal deadlines sourced', "'bodies-detachment-application-withdrawal'" in DATA and "'27/04/2026'" in DATA and "'detachment-application-withdrawal'" in DATA and "'20/05/2026'" in DATA)
check('metataksi withdrawal sourced', "'metatakseis-application-withdrawal'" in DATA and "'27/05/2026'" in DATA)
check('primary functional gaps circular documented', "'functional-gaps-primary-circular'" in DATA and "73719/Ε2" in DATA and "official-document-copy" in DATA)
check('secondary functional gaps protocol verified', "'functional-gaps-secondary-circular'" in DATA and "05/06/2026 · 74045/Ε2" in DATA and "official-document-copy" in DATA and "αρ. πρωτ. υπό διασταύρωση" not in DATA)
check('legacy functional-gaps row replaced by level-specific rows', "'functional-gaps-history'" not in DATA and "'functional-gaps-primary-circular'" in DATA and "'functional-gaps-secondary-circular'" in DATA)
check('non-standard transfer-objection row removed and guidance retained', "'id' => 'transfer-objections'" not in DATA and 'δεν υπάρχει μία ενιαία προθεσμία για όλες τις περιπτώσεις' in DATA)
check('unverified current-date warning remains supported by page', 'Δεν έχει ανακοινωθεί επίσημη ημερομηνία' in PAGE)
check('newly appointed application window verified', "'newly-appointed-detachment-applications'" in DATA and "'26/08–01/09/2026'" in DATA)
check('version remains in 3.22 release family', "EDU_TOOLS_VERSION', '3.22." in CONFIG)
check('cache remains in 3.22 release family', "CACHE_PREFIX + '3.22." in SW)

check('historical verification markers supported', 'verified_history_indices' in DATA and 'verifiedHistoryIndices' in PAGE)
check('2020 temporary points correction locked', "'14/12/2020'" in DATA and "'07/12/2020'" not in DATA)
check('2024 temporary points correction locked', "'25/11/2024'" in DATA)
check('2020 detachment circular correction locked', "'16/04/2020'" in DATA)
check('2022 primary detachment announcement correction locked', "'07/07/2022'" in DATA)

check('transfer applications history fully verified', "'id' => 'metatheseis-applications'" in DATA and "'verified_history_indices' => array(0,1,2,3,4,5,6)" in DATA and "12–25/11/2019" in DATA)
check('2019-2020 transfer withdrawal corrected to January', "'31/01/2020'" in DATA and "'31/12/2019'" not in DATA and 'ήδη πραγματοποιημένης μετάθεσης δεν προβλέπεται' in DATA)
check('2020 mutual transfer deadlines split by level', "'22/04/2020'" in DATA and "'11/05/2020'" in DATA and 'mutual-transfer-applications-pe' in DATA and 'mutual-transfer-applications-de' in DATA)
check('2021 mutual transfer deadlines split by level', "'08/04/2021'" in DATA and "'09/04/2021'" in DATA)
check('2022 mutual transfer deadlines split by level', "'02/04/2022'" in DATA and "'04/04/2022'" in DATA)
check('mutual transfer split deadlines retained', 'mutual-transfer-applications-pe' in DATA and 'mutual-transfer-applications-de' in DATA)

# v3.22.34 — historical detachment batch retained
for event_id in [
    'detachments-circular',
    'detachments-applications',
    'bodies-detachment-application-withdrawal',
    'detachment-application-withdrawal',
    'first-primary-detachments',
    'first-secondary-detachments',
]:
    anchor = "'id' => '" + event_id + "'"
    start = DATA.find(anchor)
    check(event_id + ' exists', start >= 0)
    next_event = DATA.find("\n        array(\n            'id' =>", start + len(anchor))
    block = DATA[start: next_event if next_event >= 0 else len(DATA)]
    check(event_id + ' all historical years verified', "'verified_history_indices' => array(0,1,2,3,4,5,6)" in block)
    check(event_id + ' historical sources attached', "'historical_sources' => array(" in block)

check('2024 detachment application correction remains locked', "'history' => array('30/04–11/05/2020','20–27/04/2021','05–15/04/2022','06–18/04/2023','08–17/04/2024'" in DATA)
check('2024 PYSPE/PYSDE withdrawal deadline verified', "'17/05/2024'" in DATA)
check('2020 body withdrawal deadline verified', "'12/05/2020'" in DATA)
check('2021 body withdrawal deadline verified', "'31/05/2021'" in DATA)
check('2024 first PYSDE announcement corrected to 28 June', "'28/06/2024'" in DATA and "'27/06/2024'" not in DATA)
check('2022 first PYSDE nuance retained in source trail', 'πρώτη συνδυασμένη ανακοίνωση με ΠΥΣΔΕ→ΠΥΣΔΕ 06/07/2022' in DATA and '07/07/2022' in DATA)


# v3.22.35 — metataksi 2021 + newly appointed historical completion
def event_block(event_id):
    anchor = "'id' => '" + event_id + "'"
    start = DATA.find(anchor)
    check(event_id + ' exists for v3.22.35', start >= 0)
    nxt = DATA.find("\n        array(\n            'id' =>", start + len(anchor))
    return DATA[start: nxt if nxt >= 0 else len(DATA)]

met_circular = event_block('metatakseis-circular')
check('2021 metataksi circular is fully verified', "'verified_history_indices' => array(0,1,2,3,4,5,6)" in met_circular)
check('2021 metataksi source attached', '50923/Ε2/07-05-2021' in met_circular and 'dipeira.gov.gr' in met_circular)

met_apps = event_block('metatakseis-applications')
check('2021 metataksi application window verified', "'verified_history_indices' => array(0,1,2,3,4,5,6)" in met_apps and '10–17/05/2021' in met_apps)
met_withdrawal = event_block('metatakseis-application-withdrawal')
check('2021 metataksi withdrawal verified', "'verified_history_indices' => array(0,1,2,3,4,5,6)" in met_withdrawal and '31/05/2021' in met_withdrawal)

new_circular = event_block('newly-appointed-detachment-circular')
check('2021 newly appointed invitation corrected to document date', "'12/08/2021'" in new_circular and "'21/08/2021'" not in new_circular)
check('newly appointed invitations 2021-2026 fully verified', "'verified_history_indices' => array(1,2,3,4,5,6)" in new_circular)
check('newly appointed invitation historical sources complete', all(x in new_circular for x in ['100239/Ε2/12-08-2021','101238/Ε2/17-08-2022','91225/Ε2/17-08-2023','94121/Ε2/21-08-2024','103036/Ε2/27-08-2025']))

new_apps = event_block('newly-appointed-detachment-applications')
check('unsupported 2021 application start removed', 'έως 24/08/2021 15:00' in new_apps and '21–24/08/2021' not in new_apps)
check('2022 deadline time retained', '19–23/08/2022 15:00' in new_apps)
check('newly appointed application windows 2021-2026 fully verified', "'verified_history_indices' => array(1,2,3,4,5,6)" in new_apps)
check('2021 no-invented-start guidance documented', 'μόνο καταληκτική ημερομηνία 24/08/2021 στις 15:00' in new_apps and 'όχι ξεχωριστή ημερομηνία έναρξης' in new_apps)
