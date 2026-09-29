from pathlib import Path
import json, re, subprocess, io, zipfile
ROOT=Path(__file__).resolve().parents[1]
PAGE=(ROOT/'xronodiagramma-ypiresiakon-metavolon.php').read_text(encoding='utf-8')
JS=(ROOT/'assets/service-timeline.js').read_text(encoding='utf-8')
CSS=(ROOT/'assets/common.css').read_text(encoding='utf-8')
XLSX=(ROOT/'includes/service-timeline-xlsx.php').read_text(encoding='utf-8')
CONFIG=(ROOT/'includes/config.php').read_text(encoding='utf-8')
SW=(ROOT/'service-worker.js').read_text(encoding='utf-8')

def check(label, cond):
    if not cond: raise AssertionError(label)
    print('OK:', label)

# Read canonical data through PHP rather than source-text heuristics.
proc=subprocess.run(['php','-r','echo json_encode(require "includes/service-timeline-data.php", JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);'],cwd=ROOT,text=True,capture_output=True)
check('timeline data loads', proc.returncode==0 and proc.stdout.strip().startswith('{'))
data=json.loads(proc.stdout)
events=data['events']
check('at least 32 events retained', len(events)>=32)
check('every event has explicit levels', all(isinstance(e.get('levels'),list) and e['levels'] for e in events))
allowed={'pe','de','eep-evp'}
check('level metadata uses canonical values', all(set(e['levels']) <= allowed for e in events))
counts={k:sum(k in e['levels'] for e in events) for k in allowed}
check('PE coverage preserved', counts['pe']>=22)
check('DE coverage preserved', counts['de']>=23)
check('EEP-EVP coverage preserved', counts['eep-evp']>=9)
organic=next(e for e in events if e['id']=='organic-gaps-circular')
check('organic gaps correctly classified DE', organic['levels']==['de'])
for eid in ['detachments-circular','detachments-applications','detachment-application-withdrawal','first-bodies-detachments','newly-appointed-detachment-circular','newly-appointed-detachment-applications']:
    check(eid+' is educator PE/DE', next(e for e in events if e['id']==eid)['levels']==['pe','de'])
for eid in ['metatakseis-circular','metatakseis-applications','metatakseis-application-withdrawal','resignations-applications','resignation-withdrawal']:
    check(eid+' spans PE/DE/EEP-EVP', next(e for e in events if e['id']==eid)['levels']==['pe','de','eep-evp'])

# Public UI has an independent second filter group.
for value,label in [('all','Όλα'),('pe','Π.Ε.'),('de','Δ.Ε.'),('eep-evp','ΕΕΠ-ΕΒΠ')]:
    check('level chip '+value, ('data-timeline-level="'+value+'"') in PAGE and label in PAGE)
check('level filter label', 'Βαθμίδα / προσωπικό' in PAGE)
check('JS tracks active level', "var activeLevel = 'all';" in JS and 'levelMatch' in JS and "levels.indexOf(activeLevel)" in JS)
check('category and level combine', 'groupMatch && levelMatch && verifiedMatch' in JS)
check('level chip listener', "chip.getAttribute('data-timeline-level')" in JS)
check('filter layout styling', '.timeline-filter-stack' in CSS and '.timeline-filter-label' in CSS)

# XLS export exposes the same dimension.
check('XLS level helper', 'serviceTimelineXlsxLevelLabel' in XLSX)
check('XLS level column', "'Βαθμίδα / προσωπικό'" in XLSX)
proc=subprocess.run(['php','xronodiagramma-ypiresiakon-metavolon-export.php'],cwd=ROOT,capture_output=True)
check('xlsx export executes', proc.returncode==0 and proc.stdout.startswith(b'PK'))
if proc.returncode==0 and proc.stdout.startswith(b'PK'):
    with zipfile.ZipFile(io.BytesIO(proc.stdout)) as zf:
        sheet=zf.read('xl/worksheets/sheet1.xml').decode('utf-8')
        sources=zf.read('xl/worksheets/sheet2.xml').decode('utf-8')
        check('chronology sheet level header', 'Βαθμίδα / προσωπικό' in sheet)
        check('chronology sheet DE classification', 'Εγκύκλιος προσδιορισμού οργανικών κενών / πλεονασμάτων' in sheet and 'Δ.Ε.' in sheet)
        check('sources sheet level header', 'Βαθμίδα / προσωπικό' in sources)

ver=re.search(r"EDU_TOOLS_VERSION', '([0-9.]+)'",CONFIG); cache=re.search(r"CACHE_PREFIX \+ '([0-9.]+)'",SW)
check('version at least 3.22.48', bool(ver) and tuple(map(int, ver.group(1).split('.'))) >= (3,22,48))
check('cache matches release', bool(ver and cache) and cache.group(1)==ver.group(1))
print('RESULT: service timeline v3.22.48 level filters contract PASS')
