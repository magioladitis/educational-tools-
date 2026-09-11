#!/usr/bin/env python3
from pathlib import Path
import re, subprocess, json, gzip, sys
ROOT=Path(__file__).resolve().parents[1]
PAGE=ROOT/'ypologismos-didaktikon-anagkon.php'
UI=ROOT/'includes'/'staffing-simulator-ui.js'
PASS=FAIL=0

def check(cond,msg):
    global PASS,FAIL
    if cond:
        PASS+=1; print('PASS',msg)
    else:
        FAIL+=1; print('FAIL',msg)

page=PAGE.read_text(encoding='utf-8')
ui=UI.read_text(encoding='utf-8')

check(UI.exists(),'external staffing controller exists')
check("edu_asset_url('includes/staffing-simulator-ui.js')" in page,'staffing controller uses cache-busted asset URL')
check('type="application/json" id="staffingRuntimeConfig"' in page,'runtime data is non-executable JSON')
check('<script>\n' not in page and '<script>\r\n' not in page,'legacy executable inline script removed')
check('<?php' not in ui,'external controller contains no PHP interpolation')
check(ui.count('addEventListener(')==62,'listener count preserved at 62')
check(len(re.findall(r'querySelector(?:All)?\s*\(',ui))==165,'querySelector count preserved at 165')
check(ui.count('setInterval(')==0,'no polling interval introduced')
check(ui.count('setTimeout(')==7,'timeout count unchanged')
check(ui.count('staffingRuntimeConfig=JSON.parse(')==1,'runtime JSON parsed exactly once')
check("staffingRuntimeConfigNode.textContent=''" in ui,'runtime JSON text released after parsing')
check(ui.count('const allocationPeopleData=')==1 and 'staffingRuntimeConfig.allocationPeople' in ui,'allocation people dataset initialized once')
check(ui.count('const allocationSlotsData=')==1 and 'staffingRuntimeConfig.allocationSlots' in ui,'allocation slots dataset initialized once')
check(ui.count('const specialtyLabelsData=')==1 and 'staffingRuntimeConfig.specialtyLabels' in ui,'specialty labels dataset initialized once')
check(len(ui.encode('utf-8')) < 165000,'external controller remains below 165 KB uncompressed')
check(len(gzip.compress(ui.encode('utf-8'),9)) < 37000,'external controller remains below 37 KB gzip')

# Dependency order: existing calculation/import modules must load before the controller.
order=['teaching-hours-calculations.js','school-profile-csv-import.js','personnel-csv-import.js','myschool-staff-import.js','myschool-stat51-import.js','staffing-simulator-ui.js']
positions=[page.find(x) for x in order]
check(all(x>=0 for x in positions) and positions==sorted(positions),'dependency load order preserved')

# Default server render: the former 161 KB inline controller must no longer travel in every HTML response.
r=subprocess.run(['php',str(PAGE)],cwd=ROOT,text=True,capture_output=True)
check(r.returncode==0,'default staffing page renders')
html=r.stdout
check(len(html.encode('utf-8')) < 60000,'default HTML payload stays below 60 KB')
m=re.search(r'<script type="application/json" id="staffingRuntimeConfig">(.*?)</script>',html,re.S)
config=None
if m:
    try: config=json.loads(m.group(1))
    except Exception: config=None
check(isinstance(config,dict),'runtime config renders as valid JSON')
if isinstance(config,dict):
    check(set(['maxBasicSections','initialAllocation','hasCalculatedResults','allocationPeople','allocationSlots','specialtyLabels']).issubset(config.keys()),'runtime config exposes required keys')
    check(config.get('maxBasicSections')==120,'runtime config preserves 120-section safety cap')

# Syntax checks are part of the performance contract because parser failure would negate caching benefits.
check(subprocess.run(['node','--check',str(UI)],capture_output=True).returncode==0,'external controller passes JS syntax check')
check(subprocess.run(['php','-l',str(PAGE)],capture_output=True).returncode==0,'staffing page passes PHP syntax check')

print(f'RESULT {PASS} PASS / {FAIL} FAIL')
sys.exit(1 if FAIL else 0)
