from pathlib import Path
import json, re, subprocess
ROOT = Path(__file__).resolve().parents[1]

def check(label, cond):
    if not cond:
        raise AssertionError(label)
    print('OK:', label)

proc = subprocess.run([
    'php','-r',
    'echo json_encode(require "includes/service-timeline-data.php", JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);'
], cwd=ROOT, text=True, capture_output=True)
check('timeline data loads', proc.returncode == 0)
data = json.loads(proc.stdout)
by = {e['id']: e for e in data['events']}
e = by['metatheseis-circular']

check('current official sources contain only PE/DE 2025-2026',
      len(e.get('sources', [])) == 2 and all('2025–2026' in x['label'] for x in e['sources']))
check('six prior years moved to historical sources',
      [x['year'] for x in e.get('historical_sources', [])] == [
          '2019-2020','2020-2021','2021-2022','2022-2023','2023-2024','2024-2025'])
check('all historical timeline values remain verified',
      e.get('verified_history_indices') == [0,1,2,3,4,5,6])
check('all circular source links are official Ministry links',
      all('minedu.gov.gr' in x['url'] for x in e['sources'] + e['historical_sources']))

page = (ROOT/'xronodiagramma-ypiresiakon-metavolon.php').read_text(encoding='utf-8')
check('historical sources stay nested inside history UI',
      'Πηγές προηγούμενων ετών' in page and 'timeline-historical-sources' in page)
check('current sources keep separate official sources UI',
      '<summary>Επίσημες πηγές</summary>' in page)

config=(ROOT/'includes/config.php').read_text(encoding='utf-8')
sw=(ROOT/'service-worker.js').read_text(encoding='utf-8')
ver=re.search(r"EDU_TOOLS_VERSION', '([0-9.]+)'", config)
cache=re.search(r"CACHE_PREFIX \+ '([0-9.]+)'", sw)
check('version at least 3.22.57', bool(ver) and tuple(map(int, ver.group(1).split('.'))) >= (3,22,57))
check('cache matches configured release', bool(ver) and bool(cache) and cache.group(1) == ver.group(1))
print('RESULT: service timeline source consistency v3.22.57 contract PASS')
