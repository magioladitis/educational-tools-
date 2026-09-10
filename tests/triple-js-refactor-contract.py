from pathlib import Path
import re, subprocess, json
from lxml import html
ROOT=Path(__file__).resolve().parents[1]
checks=[]
def check(n,c): checks.append((n,bool(c))); print(('PASS' if c else 'FAIL')+': '+n)
items=[
 ('dikaioma-symmetoxis.php','includes/eligibility-guide-ui.js'),
 ('orologio-programma-mathimaton.php','includes/weekly-timetable-ui.js'),
 ('odigos-enstasis.php','includes/objection-guide-ui.js')]
for page_name,js_name in items:
    page=(ROOT/page_name).read_text(encoding='utf8'); js=(ROOT/js_name).read_text(encoding='utf8')
    check(page_name+' external controller', f"edu_asset_url('{js_name}')" in page)
    check(page_name+' no inline JS', re.search(r'<script(?![^>]*\bsrc=)[^>]*>\s*\S',page,re.I|re.S) is None)
    check(page_name+' no inline handlers', re.search(r'\son(?:click|change|input|submit|blur|keyup|keydown|focus)\s*=',page,re.I) is None)
    p=subprocess.run(['php',page_name],cwd=ROOT,capture_output=True,text=True)
    check(page_name+' PHP render',p.returncode==0)
    if p.returncode==0:
        doc=html.document_fromstring(p.stdout); ids=doc.xpath('//*[@id]/@id')
        check(page_name+' no duplicate ids',len(ids)==len(set(ids)))
        check(page_name+' rendered controller',js_name in p.stdout)

page=(ROOT/'dikaioma-symmetoxis.php').read_text(encoding='utf8'); ui=(ROOT/'includes/eligibility-guide-ui.js').read_text(encoding='utf8')
check('eligibility buttons have stable ids','id="eligibilityCheckBtn"' in page and 'id="eligibilityResetBtn"' in page)
check('eligibility external click wiring','checkButton.addEventListener("click", checkEligibility)' in ui and 'resetButton.addEventListener("click", resetForm)' in ui)
check('eligibility controller exported','EligibilityGuideUI = Object.freeze' in ui)

page=(ROOT/'odigos-enstasis.php').read_text(encoding='utf8'); ui=(ROOT/'includes/objection-guide-ui.js').read_text(encoding='utf8')
for event,needle in [('reason','reason.addEventListener(\'change\', updateExtraQuestions)'),('recognition','recognition.addEventListener(\'change\', updateRecognitionDate)'),('submission','submission.addEventListener(\'change\', updateResubmissionInfo)'),('paravolo','paravolo.addEventListener(\'change\', updateParavoloUI)'),('code','paravoloCode.addEventListener(\'input\', validateParavoloCode)'),('button','guidanceBtn.addEventListener(\'click\', showGuidance)')]:
    check('objection '+event+' external wiring',needle in ui)
check('objection controller exported','ObjectionGuideUI = Object.freeze' in ui)

page=(ROOT/'orologio-programma-mathimaton.php').read_text(encoding='utf8'); ui=(ROOT/'includes/weekly-timetable-ui.js').read_text(encoding='utf8')
check('weekly neutral JSON template','<template id="weeklyTimetableData">' in page)
check('weekly controller parses payload',"JSON.parse(dataText || '{}')" in ui)
check('weekly PHP no JS data injection','var schools = <?php' not in page and 'var allRows = <?php' not in page)
p=subprocess.run(['php','orologio-programma-mathimaton.php'],cwd=ROOT,capture_output=True,text=True)
if p.returncode==0:
    doc=html.document_fromstring(p.stdout); t=doc.get_element_by_id('weeklyTimetableData'); payload=json.loads(''.join(t.itertext()))
    check('weekly rendered payload has schools',len(payload.get('schools',{}))>=10)
    check('weekly rendered payload has rows',len(payload.get('rows',[]))>1000)
    check('weekly rendered payload has ethics policy',payload.get('ethicsPolicy',{}).get('minimum_exempt_students_per_grade')==10)

failed=[n for n,o in checks if not o]
print(f'RESULT {len(checks)-len(failed)} PASS / {len(failed)} FAIL')
raise SystemExit(1 if failed else 0)
