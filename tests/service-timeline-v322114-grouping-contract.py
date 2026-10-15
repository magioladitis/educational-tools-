from pathlib import Path
from subprocess import check_output
from bs4 import BeautifulSoup
from zipfile import ZipFile
from xml.etree import ElementTree as ET

ROOT=Path(__file__).resolve().parents[1]
php = r'''$d=require "''' + str(ROOT/'includes/service-timeline-data.php') + r'''"; require "''' + str(ROOT/'includes/service-timeline-view.php') + r'''"; echo json_encode(array('original'=>$d['events'],'presented'=>serviceTimelinePresentation($d['events']),'xlsx'=>serviceTimelineChronologicalRawEvents($d['events'])), JSON_UNESCAPED_UNICODE);'''
import json
j=json.loads(check_output(['php','-r',php]))
original=j['original']; presented=j['presented']; flat=j['xlsx']
checks=0
def check(title, condition):
    global checks
    assert condition, title
    checks += 1

check('all source events preserved', len(original)==len(flat))
check('group count', len(presented)==len(original)-10)
combined={x['id']:x for x in presented if x['children']}
check('seven shared-event groups',len(combined)==7)
withdraw=combined['transfer-withdrawal']
check('withdrawal contains 3 personnel rows',len(withdraw['children'])==3)
check('withdrawal levels', [x['levels'][0] for x in withdraw['children']]==['pe','de','eep-evp'])
check('withdrawal retains 15:00 only where official', [x['latest'] for x in withdraw['children']]==['31/12/2026','31/12/2026 · 15:00','31/12/2026 · 15:00'])
check('all timeline events have unique ids',len({x['id'] for x in original})==len(original))
check('source event ids unchanged',sorted([x['id'] for x in original]) == sorted([x['id'] for x in flat]))
check('all current first', all((x['sort_date'] or '')>='2026-10-01' for x in presented[:11]))
check('current sorts ascending',[x['sort_date'] for x in presented[:11]] == sorted(x['sort_date'] for x in presented[:11]))
check('archive follows', all((x['sort_date'] or '')<'2026-10-01' for x in presented[11:]))
check('no PE/de cross-links in current withdrawal', all('Π.Ε.' not in str(x['sources']) for x in withdraw['children'][1:]))
check('no DE in PE withdrawal', 'Δ.Ε.' not in str(withdraw['children'][0]['sources']))
check('no misplaced grade in historical DE withdrawal',all('Π.Ε.' not in s['label'] for s in withdraw['children'][1]['historical_sources']))
check('grade filters attached to shared pieces', 'data-timeline-part' in (ROOT/'xronodiagramma-ypiresiakon-metavolon.php').read_text())
check('front end hides unrelated sources', 'part.hidden = activeLevel' in (ROOT/'assets/service-timeline.js').read_text())
check('version consistent',"'3.22.114'" in (ROOT/'includes/config.php').read_text() and "'3.22.114'" in (ROOT/'service-worker.js').read_text())
print(f'{checks} presentation checks PASS')
