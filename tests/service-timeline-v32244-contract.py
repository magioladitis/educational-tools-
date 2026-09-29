from pathlib import Path
import re, subprocess
ROOT=Path(__file__).resolve().parents[1]
PAGE=(ROOT/'xronodiagramma-ypiresiakon-metavolon.php').read_text(encoding='utf-8')
DATA=(ROOT/'includes/service-timeline-data.php').read_text(encoding='utf-8')
CSS=(ROOT/'assets/common.css').read_text(encoding='utf-8')
JS=(ROOT/'assets/service-timeline.js').read_text(encoding='utf-8')
CONFIG=(ROOT/'includes/config.php').read_text(encoding='utf-8')
SW=(ROOT/'service-worker.js').read_text(encoding='utf-8')

def check(label, cond):
    if not cond: raise AssertionError(label)
    print('OK:', label)

check('per-year green verification ticks removed', 'timeline-history-verified' not in PAGE and 'timeline-history-verified' not in CSS)
check('technical tick legend removed', 'Το ✓ δείχνει' not in PAGE and 'timeline-history-disclaimer' not in PAGE and 'timeline-history-disclaimer' not in CSS)
check('repeated verified badge removed', '✓ Επιβεβαιωμένη από επίσημη πηγή' not in PAGE and 'timeline-source-state--verified' not in CSS)
check('unverified future state remains supported', 'Δεν έχει ανακοινωθεί επίσημη ημερομηνία' in PAGE and 'timeline-source-state--research' in CSS)
check('partial historical sourcing cue stays available if needed', 'timeline-history-summary-note' in CSS and ('Πηγές υπό συμπλήρωση' in PAGE or 'historyNeedsSources' in PAGE))
check('empty history years are visually de-emphasized', "' is-empty'" in PAGE and 'timeline-history-item.is-empty' in CSS)
check('hero does not use verified-count wording', 'διαδικασίες με επιβεβαιωμένη ημερομηνία' not in PAGE)
check('current-cycle copy is concise', 'Όταν δημοσιευτεί, η ημερομηνία θα προστεθεί εδώ.' in PAGE)
check('default all-count line is suppressed', "defaultView = activeFilter === 'all'" in JS and 'status.hidden = defaultView' in JS)
check('sources remain collapsed and accessible', '<summary>Επίσημες πηγές</summary>' in PAGE and '<summary>Πηγές προηγούμενων ετών</summary>' in PAGE)
check('bottom source copy remains user-facing', 'Οι ημερομηνίες βασίζονται σε επίσημες ανακοινώσεις' in PAGE)
for phrase in [
    'Η ημερομηνία αφορά την επίσημη εγκύκλιο αποσπάσεων',
    'Η ημερομηνία αφορά την έκδοση της ετήσιας εγκυκλίου μετατάξεων',
    'Η περίοδος αφορά την υποβολή αιτήσεων μετάταξης του αντίστοιχου κύκλου',
    'Για το 2022 ως πρώτη ανακοίνωση καταγράφεται',
    'υπουργική απόφαση έχει ημερομηνία 13/08',
    'Η ημερομηνία αφορά την πρώτη επίσημη ανακοίνωση αποτελεσμάτων της συγκεκριμένης κατηγορίας',
]:
    check('redundant/audit note removed: '+phrase, phrase not in DATA)
ver=re.search(r"EDU_TOOLS_VERSION', '([0-9.]+)'",CONFIG)
cache=re.search(r"CACHE_PREFIX \+ '([0-9.]+)'",SW)
check('version is at least 3.22.44', bool(ver) and tuple(map(int,ver.group(1).split('.'))) >= (3,22,44))
check('cache is at least 3.22.44', bool(cache) and tuple(map(int,cache.group(1).split('.'))) >= (3,22,44))
proc=subprocess.run(['php','xronodiagramma-ypiresiakon-metavolon.php'],cwd=ROOT,text=True,capture_output=True)
check('timeline PHP renders',proc.returncode==0)
if proc.returncode==0:
    html=proc.stdout
    check('no green verification badges render', 'Επιβεβαιωμένη από επίσημη πηγή' not in html and 'timeline-history-verified' not in html)
    check('partial-source cue may disappear when coverage is complete', 'timeline-history' in html)
print('RESULT: service timeline v3.22.44 contract PASS')
