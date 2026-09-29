from pathlib import Path
import json,re,subprocess
ROOT=Path(__file__).resolve().parents[1]
CONFIG=(ROOT/'includes/config.php').read_text(encoding='utf-8')
SW=(ROOT/'service-worker.js').read_text(encoding='utf-8')

def check(label, cond):
    if not cond: raise AssertionError(label)
    print('OK:',label)

proc=subprocess.run(['php','-r','echo json_encode(require "includes/service-timeline-data.php", JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);'],cwd=ROOT,text=True,capture_output=True)
check('timeline data loads',proc.returncode==0)
data=json.loads(proc.stdout); events=data['events']; by={e['id']:e for e in events}
check('42 events after newly-appointed results split',len(events)>=42)
inv=by['newly-appointed-detachment-circular']; apps=by['newly-appointed-detachment-applications']
check('invitation explicitly exceptional','κατ’ εξαίρεση' in inv['title'].lower())
check('applications explicitly exceptional','κατ’ εξαίρεση' in apps['title'].lower())
pe=by['newly-appointed-detachment-results-primary']; de=by['newly-appointed-detachment-results-secondary']
check('PE result level only',pe['levels']==['pe'])
check('DE result level only',de['levels']==['de'])
check('PE name announcement history',pe['history']==[None,'01/09/2021','31/08/2022','31/08/2023','13/09/2024','17/09/2025','11/09/2026'])
check('DE name announcement history',de['history']==[None,'31/08/2021','31/08/2022','31/08/2023','10/09/2024','11/09/2025','10/09/2026'])
check('PE historical sources complete',len(pe['historical_sources'])==6 and pe['verified_history_indices']==[1,2,3,4,5,6])
check('DE historical sources complete',len(de['historical_sources'])==6 and de['verified_history_indices']==[1,2,3,4,5,6])
check('latest result source present',pe['sources'] and de['sources'])
ver=re.search(r"EDU_TOOLS_VERSION', '([0-9.]+)'",CONFIG); cache=re.search(r"CACHE_PREFIX \+ '([0-9.]+)'",SW)
check('version 3.22.52',bool(ver) and ver.group(1)=='3.22.52')
check('cache matches release',bool(cache) and cache.group(1)=='3.22.52')
print('RESULT: service timeline v3.22.52 newly-appointed exceptional results contract PASS')
