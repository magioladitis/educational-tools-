#!/usr/bin/env python3
from pathlib import Path
import re, subprocess, sys
ROOT=Path(__file__).resolve().parents[1]
PASS=FAIL=0

def check(cond,msg):
    global PASS,FAIL
    if cond:
        PASS+=1; print('PASS',msg)
    else:
        FAIL+=1; print('FAIL',msg)

targets={
    'dikaiologitika-tekna-anapiria.php':'includes/children-disability-documents-ui.js',
    'ypologismos-morion-4ea-2025.php':'includes/asep-4ea-ui.js',
    'ypologismos-morion-5ea-2022.php':'includes/asep-5ea-ui.js',
    'ypologismos-morion-sivitanidios-saek.php':'includes/sivitanidios-saek-ui.js',
    'ypologismos-morion-mitroo-sde.php':'includes/sde-registry-ui.js',
}
for php,js in targets.items():
    ps=(ROOT/php).read_text(encoding='utf-8')
    js_text=(ROOT/js).read_text(encoding='utf-8')
    check(Path(ROOT/js).exists(), f'{php} external UI module exists')
    check(Path(js).name in ps, f'{php} loads {Path(js).name}')
    inline=re.findall(r'<script\b(?![^>]*\bsrc=)[^>]*>(.*?)</script>',ps,re.S|re.I)
    check(len(inline)==0, f'{php} has no inline script')
    handler=re.search(r'\bon(?:click|change|input|submit|keyup|keydown|blur|focus)\s*=|[\"\']on(?:click|change|input|submit|keyup|keydown|blur|focus)[\"\']\s*=>',ps,re.I)
    check(handler is None, f'{php} has no inline event handler')
    r=subprocess.run(['php','-l',str(ROOT/php)],capture_output=True,text=True)
    check(r.returncode==0, f'{php} PHP syntax')
    r=subprocess.run(['node','--check',str(ROOT/js)],capture_output=True,text=True)
    check(r.returncode==0, f'{js} JS syntax')

# Children/disability guide bindings.
children=(ROOT/'dikaiologitika-tekna-anapiria.php').read_text(encoding='utf-8')
children_js=(ROOT/'includes/children-disability-documents-ui.js').read_text(encoding='utf-8')
check('id="showDocumentsBtn"' in children, 'children guide submit button has stable id')
for token in ['criterionSelect.addEventListener("change",updateVisibility)',
              'disabilityPersonSelect.addEventListener("change",updateDisabilityPersonUI)',
              'showDocumentsBtn.addEventListener("click",showDocuments)']:
    check(token in children_js, 'children guide external binding: '+token.split('.addEventListener')[0])

# 4EA / 5EA UI controllers keep key behavior and action bindings.
js4=(ROOT/'includes/asep-4ea-ui.js').read_text(encoding='utf-8')
for token in ["AsepTeAcademic.getState('asepTeAcademic'","AsepEaeEligibility.getState('eaeEligibility'","$('copyBtn').addEventListener('click'","$('resetBtn').addEventListener('click'","AsepTeAcademic.sync('asepTeAcademic')"]:
    check(token in js4, '4EA UI controller keeps '+token)
js5=(ROOT/'includes/asep-5ea-ui.js').read_text(encoding='utf-8')
for token in ["AsepDeAcademic.getState('asepDeAcademic'","AsepEaeEligibility.getState('eaeEligibility'","$('copyBtn').addEventListener('click'","$('resetBtn').addEventListener('click'","AsepDeAcademic.reset('asepDeAcademic',{silent:true})"]:
    check(token in js5, '5EA UI controller keeps '+token)

# Sivitanidios: engine must load before UI and core interaction hooks stay external.
siv=(ROOT/'ypologismos-morion-sivitanidios-saek.php').read_text(encoding='utf-8')
siv_js=(ROOT/'includes/sivitanidios-saek-ui.js').read_text(encoding='utf-8')
check(siv.index('sivitanidios-saek-calculations.js') < siv.index('sivitanidios-saek-ui.js'), 'Sivitanidios engine loads before UI controller')
for token in ["scoreControls.forEach(el=>{","$('copyBtn').addEventListener('click'","$('resetBtn').addEventListener('click'","normalizeTrainingHours"]:
    check(token in siv_js, 'Sivitanidios UI keeps '+token)

# SDE registry: every former inline behavior has an explicit external binding.
sde=(ROOT/'ypologismos-morion-mitroo-sde.php').read_text(encoding='utf-8')
sde_js=(ROOT/'includes/sde-registry-ui.js').read_text(encoding='utf-8')
check(sde.index('language-calculations.js') < sde.index('sde-registry-calculations.js') < sde.index('sde-registry-ui.js'), 'SDE registry dependencies load before UI controller')
for btn in ['copyBtn','resetBtn']:
    check(f"'id' => '{btn}'" in sde, f'SDE registry {btn} stable action id')

expected_change={
 'role':'roleChanged','specialty':'specialtyChanged','fppBefore1993':'psychFppChanged',
 'language1':'languageChanged','language2':'languageChanged'
}
for ident,fn in expected_change.items():
    check(f'{ident}:{fn}' in sde_js, f'SDE special change binding {ident} -> {fn}')
for ident in ['eoppepAdultTrainer','psychDegree','psychLicense','psychMasterForFpp','tertiaryDegree','careerQualification','phd','master','secondDegree','secondPhd','secondMaster','extraCredential','languageLevel1','languageLevel2','computer','threeChildren','singleParent','manyChildren','disability']:
    check(f"'{ident}'" in sde_js, f'SDE calculate-on-change binding covers {ident}')
for ident in ['trainingSdeHours','trainingAdultHours','trainingThematicHours','expSdeHours','expAdultHours','expFormalHours','expSdeMonths','expAdultCounsellingMonths','unemploymentMonths','unemploymentExtraDays']:
    check(f"'{ident}'" in sde_js, f'SDE calculate-on-input binding covers {ident}')
check("copyBtn.addEventListener('click',()=>copySummary(copyBtn))" in sde_js, 'SDE copy binding external')
check("resetBtn.addEventListener('click',resetForm)" in sde_js, 'SDE reset binding external')

config=(ROOT/'includes/config.php').read_text(encoding='utf-8')
check("define('EDU_TOOLS_VERSION', '3.20.93');" in config,'asset version bumped to 3.20.93')

print(f'RESULT {PASS} PASS / {FAIL} FAIL')
sys.exit(1 if FAIL else 0)
