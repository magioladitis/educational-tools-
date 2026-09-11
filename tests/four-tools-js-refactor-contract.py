#!/usr/bin/env python3
from pathlib import Path
import re, sys, subprocess, json, html
ROOT=Path(__file__).resolve().parents[1]
checks=[]
def check(name, cond): checks.append((name,bool(cond)))

pairs={
 'anatheseis-mathimaton.php':'includes/teaching-assignments-ui.js',
 'ypologismos-morion.php':'includes/asep-points-ui.js',
 'ypologismos-morion-apospasis-psifiako-frontistirio.php':'includes/digital-tutoring-detachment-ui.js',
 'ypologismos-morion-apospasis-dimos.php':'includes/dimos-detachment-ui.js',
}
for page,module in pairs.items():
    src=(ROOT/page).read_text(encoding='utf-8')
    js=(ROOT/module).read_text(encoding='utf-8')
    inline=[]
    for m in re.finditer(r'<script(?:\s[^>]*)?>(.*?)</script>',src,re.S|re.I):
        open_tag=src[m.start():src.find('>',m.start())+1]
        if 'src=' not in open_tag: inline.append(m.group(1))
    check(page+' has no inline script', not inline)
    check(page+' loads external controller', module in src)
    check(page+' has no HTML inline event attributes', not re.search(r'\bon(?:click|change|input|submit|keyup|keydown|blur|focus)\s*=',src,re.I))
    check(page+' has no PHP inline event attrs', not re.search(r"['\"]on(?:click|change|input|submit|keyup|keydown|blur|focus)['\"]\s*=>",src,re.I))
    check(module+' is non-trivial', len(js.splitlines()) >= 100)

assign=(ROOT/'anatheseis-mathimaton.php').read_text(encoding='utf-8')
assign_ui=(ROOT/'includes/teaching-assignments-ui.js').read_text(encoding='utf-8')
check('assignments uses neutral JSON template payload', 'id="teachingAssignmentsData"' in assign and 'json_encode(teachingAssignmentsData()' in assign)
check('assignments controller parses neutral payload', "document.getElementById('teachingAssignmentsData')" in assign_ui and 'JSON.parse' in assign_ui)
rendered=subprocess.run(['php', str(ROOT/'anatheseis-mathimaton.php')], cwd=ROOT, capture_output=True, text=True, check=True).stdout
m=re.search(r'<template id="teachingAssignmentsData">(.*?)</template>', rendered, re.S)
rendered_data=json.loads(html.unescape(m.group(1))) if m else None
direct_count=int(subprocess.run(['php','-r',"require 'includes/teaching-assignments-data.php'; echo count(teachingAssignmentsData());"], cwd=ROOT, capture_output=True, text=True, check=True).stdout)
check('assignments JSON payload renders as valid array', isinstance(rendered_data,list))
check('assignments JSON payload preserves complete dataset', isinstance(rendered_data,list) and len(rendered_data)==direct_count and direct_count>100)

points=(ROOT/'ypologismos-morion.php').read_text(encoding='utf-8')
points_ui=(ROOT/'includes/asep-points-ui.js').read_text(encoding='utf-8')
check('general points copy action externally wired', "id' => 'copyResultBtn'" in points and "addEventListener('click', copyResult)" in points_ui)
check('general points reset action externally wired', "id' => 'resetCalculatorBtn'" in points and "addEventListener('click', resetCalculator)" in points_ui)
check('general points live calculation retained', 'function liveCalculatePoints' in points_ui and 'AsepPeAcademic.validate' in points_ui)

digital=(ROOT/'ypologismos-morion-apospasis-psifiako-frontistirio.php').read_text(encoding='utf-8')
digital_ui=(ROOT/'includes/digital-tutoring-detachment-ui.js').read_text(encoding='utf-8')
check('digital specialty change externally wired', "getElementById('specialty').addEventListener('change', specialtyChanged)" in digital_ui)
check('digital EAE change externally wired', "getElementById('eaePosition').addEventListener('change', toggleEae)" in digital_ui)
check('digital reset externally wired', "id' => 'resetBtn'" in digital and "addEventListener('click', resetForm)" in digital_ui)

printer=(ROOT/'includes/education-print.js').read_text(encoding='utf-8')
check('posa-paravola excluded from generic print registry', "'posa-paravola.php'" not in printer)
check('other refactored calculators remain printable', all("'"+x+"'" in printer for x in ['ypologismos-morion.php','ypologismos-morion-apospasis-dimos.php','ypologismos-morion-apospasis-psifiako-frontistirio.php','anatheseis-mathimaton.php']))

config=(ROOT/'includes/config.php').read_text(encoding='utf-8')
check('release version 3.20.94', "define('EDU_TOOLS_VERSION', '3.20.94')" in config)

failed=[n for n,ok in checks if not ok]
for n,ok in checks: print(('PASS' if ok else 'FAIL')+': '+n)
print(f'RESULT {len(checks)-len(failed)} PASS / {len(failed)} FAIL')
sys.exit(1 if failed else 0)
