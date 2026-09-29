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
tr=by['transfer-application-withdrawal-eep-evp']
expected=[
    'έως 2 ημέρες πριν από ΚΥΣΕΕΠ',
    '05/04/2021',
    '08/03/2022 · 23:59',
    '31/12/2022 · 15:00',
    '31/12/2023 · 15:00',
    '31/12/2024 · 15:00',
    '31/12/2025 · 15:00',
]
check('EEP-EVP withdrawal full seven-year history', tr['history']==expected)
check('EEP-EVP withdrawal all years verified', tr['verified_history_indices']==[0,1,2,3,4,5,6])
check('EEP-EVP withdrawal seven historical sources', len(tr.get('historical_sources',[]))==7)
check('EEP-EVP withdrawal historical coverage flag', tr.get('source_coverage')=='historical')
check('2019-20 relative KYSSEEP rule documented', 'δύο ημέρες πριν' in tr['note'] and 'ΚΥΣΕΕΠ' in tr['note'])
check('2021-22 exceptional March deadline documented', '08/03/2022 23:59' in tr['note'])
check('2022-23 onward Dec 31 pattern documented', '2022–2023' in tr['note'] and '31/12 15:00' in tr['note'])
check('2020-21 official Diavgeia source retained', 'diavgeia.gov.gr' in tr['historical_sources'][1]['url'])
check('remaining historical sources ministry-hosted', all('minedu.gov.gr' in x['url'] for i,x in enumerate(tr['historical_sources']) if i != 1))

config=(ROOT/'includes/config.php').read_text(encoding='utf-8')
sw=(ROOT/'service-worker.js').read_text(encoding='utf-8')
ver=re.search(r"EDU_TOOLS_VERSION', '([0-9.]+)'", config); cache=re.search(r"CACHE_PREFIX \+ '([0-9.]+)'", sw)
check('version at least 3.22.54', bool(ver) and tuple(map(int,ver.group(1).split('.'))) >= (3,22,54))
check('cache matches configured release', bool(cache) and bool(ver) and cache.group(1)==ver.group(1))
print('RESULT: service timeline v3.22.54 EEP-EVP withdrawal history contract PASS')
