from pathlib import Path
import json,re,subprocess
ROOT=Path(__file__).resolve().parents[1]

def check(label, cond):
    if not cond: raise AssertionError(label)
    print('OK:',label)

proc=subprocess.run(['php','-r','echo json_encode(require "includes/service-timeline-data.php", JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);'],cwd=ROOT,text=True,capture_output=True)
check('timeline data loads', proc.returncode==0)
data=json.loads(proc.stdout); events=data['events']; by={e['id']:e for e in events}
check('46 service-timeline processes unchanged', len(events)==46)
det=by['eep-evp-detachment-application-withdrawal']
expected=[None,None,'15/06/2022','02/06/2023','22/05/2024','03/06/2025','08/06/2026']
check('EEP-EVP detachment withdrawal five-year history', det['history']==expected)
check('EEP-EVP detachment withdrawal verified years 2022-2026', det['verified_history_indices']==[2,3,4,5,6])
check('EEP-EVP detachment withdrawal five historical sources', len(det.get('historical_sources',[]))==5)
check('EEP-EVP detachment withdrawal historical coverage flag', det.get('source_coverage')=='historical')
check('2022 deadline', det['history'][2]=='15/06/2022')
check('2023 deadline', det['history'][3]=='02/06/2023')
check('2024 deadline', det['history'][4]=='22/05/2024')
check('2025 deadline', det['history'][5]=='03/06/2025')
check('2026 deadline', det['history'][6]=='08/06/2026')
check('legacy years intentionally not synthesized', det['history'][:2]==[None,None] and '2019–2020' in det['note'] and '2020–2021' in det['note'])
check('post-results withdrawal kept distinct', 'μετά τα αποτελέσματα' in det['note'])
check('official source trail', all(('minedu.gov.gr' in x['url']) or ('diavgeia.gov.gr' in x['url']) for x in det['historical_sources']))

config=(ROOT/'includes/config.php').read_text(encoding='utf-8')
sw=(ROOT/'service-worker.js').read_text(encoding='utf-8')
ver=re.search(r"EDU_TOOLS_VERSION', '([0-9.]+)'", config); cache=re.search(r"CACHE_PREFIX \+ '([0-9.]+)'", sw)
check('version 3.22.55', bool(ver) and ver.group(1)=='3.22.55')
check('cache 3.22.55', bool(cache) and cache.group(1)=='3.22.55')
print('RESULT: service timeline v3.22.55 EEP-EVP detachment withdrawal history contract PASS')
