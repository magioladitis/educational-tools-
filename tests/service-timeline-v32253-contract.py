from pathlib import Path
import json,re,subprocess
ROOT=Path(__file__).resolve().parents[1]

def check(label, cond):
    if not cond: raise AssertionError(label)
    print('OK:',label)

proc=subprocess.run(['php','-r','echo json_encode(require "includes/service-timeline-data.php", JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);'],cwd=ROOT,text=True,capture_output=True)
check('timeline data loads', proc.returncode==0)
data=json.loads(proc.stdout); events=data['events']; by={e['id']:e for e in events}
check('46 service-timeline processes', len(events)==46)
check('PE process count 26', sum('pe' in e['levels'] for e in events)==26)
check('DE process count 27', sum('de' in e['levels'] for e in events)==27)
check('EEP-EVP process count 22', sum('eep-evp' in e['levels'] for e in events)==22)

pts=by['temporary-transfer-points-eep-evp']
check('EEP-EVP points publication', pts['levels']==['eep-evp'] and pts['latest']=='08/12/2025' and pts['history'][6]=='08/12/2025')
check('EEP-EVP points source official', pts['sources'] and all('minedu.gov.gr' in x['url'] for x in pts['sources']))
tr=by['transfer-application-withdrawal-eep-evp']
check('EEP-EVP transfer withdrawal deadline', tr['latest']=='31/12/2025 · 15:00' and 6 in tr['verified_history_indices'])
det=by['eep-evp-detachment-application-withdrawal']
check('EEP-EVP detachment withdrawal deadline', det['latest']=='08/06/2026' and det['levels']==['eep-evp'])
post=by['detachment-post-results-five-day-actions']
check('post-result rule has no synthetic date', post['latest']=='εντός 5 ημερών από την ανακοίνωση' and post['levels']==['pe','de','eep-evp'])
check('post-result rule official sources', len(post['sources'])==2 and all('minedu.gov.gr' in x['url'] for x in post['sources']))

pe=by['mutual-transfer-primary-result']
check('PE mutual transfer 2020 completed', pe['history'][0]=='27/05/2020' and 0 in pe['verified_history_indices'])
de=by['mutual-transfer-secondary-result']
check('DE mutual transfer 2020-2021 completed', de['history'][:2]==['16/06/2020','06/05/2021'] and de['verified_history_indices']==[0,1,2,3,4,5,6])
check('DE 2020 announcement/decision distinction documented', '15/06/2020' in de['note'] and '16/06/2020' in de['note'])
gaps=by['functional-gaps-secondary-circular']
check('DE functional gaps 2020 completed', gaps['history'][0]=='03/07/2020 · 85351/Ε2' and gaps['verified_history_indices']==[0,1,2,3,4,5,6])

config=(ROOT/'includes/config.php').read_text(encoding='utf-8')
sw=(ROOT/'service-worker.js').read_text(encoding='utf-8')
ver=re.search(r"EDU_TOOLS_VERSION', '([0-9.]+)'", config); cache=re.search(r"CACHE_PREFIX \+ '([0-9.]+)'", sw)
check('version at least 3.22.53', bool(ver) and tuple(map(int,ver.group(1).split('.'))) >= (3,22,53))
check('cache matches configured release', bool(cache) and bool(ver) and cache.group(1)==ver.group(1))
print('RESULT: service timeline v3.22.53 completion audit contract PASS')
