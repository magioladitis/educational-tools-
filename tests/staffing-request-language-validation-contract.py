#!/usr/bin/env python3
from pathlib import Path
import json, subprocess, re
ROOT=Path(__file__).resolve().parents[1]
PAGE=ROOT/'ypologismos-didaktikon-anagkon.php'
PROFILE=ROOT/'includes/school-profile-general-education.php'
checks=[]
def check(name, cond): checks.append((name,bool(cond)))

def render(payload, method='POST'):
    data=json.dumps(payload,ensure_ascii=False)
    php='<?php $_SERVER["REQUEST_METHOD"]='+json.dumps(method)+'; $_POST=json_decode('+json.dumps(data)+', true); include '+json.dumps(str(PAGE))+';'
    p=subprocess.run(['php'],cwd=ROOT,input=php,text=True,capture_output=True)
    if p.returncode:
        print(p.stderr); raise SystemExit(p.returncode)
    return p.stdout

text=PAGE.read_text(encoding='utf-8')
css=(ROOT/'assets/staffing-simulator.css').read_text(encoding='utf-8')
check('no fetch to endpoint', 'fetch(' not in text)
check('no direct form.submit path', re.search(r'\bform\.submit\s*\(', text) is None)
check('no reload or redirect path', not any(x in text for x in ['location.reload','location.assign','location.replace','window.location=','window.location =']))
check('exactly one centralized requestSubmit', text.count('form.requestSubmit()')==1)
check('requestSubmit is behind explicit click action', "button.addEventListener('click'" in text and "data-staffing-request-action" in text and "form.dataset.explicitRequest='1'" in text)
check('submit guard blocks unarmed submits', "if(!armed || form.dataset.requestInFlight==='1')" in text and 'event.preventDefault()' in text)
check('request gate has in-flight duplicate protection', "form.dataset.requestInFlight='1'" in text)
check('four explicit request actions only', text.count('data-staffing-request-action="')==4)
check('no native staffing submit button', 'type="submit" name="staffing_action"' not in text)
check('initial GET defers specialty workload-model build', re.search(r'\$personnelSpecialtyOptions\s*=\s*\$submitted\s*\?\s*staffingUiPersonnelSpecialtyOptions', text) is not None)
check('explicit calculation reuses one workload model for matrix and specialty catalogue', '$teachingModel = teachingWorkloadModel();' in text and 'schoolProfileWorkloadMatrix($profile, $teachingModel)' in text and 'staffingUiPersonnelSpecialtyOptions($displayMatrix, $teachingModel)' in text)
check('allocation status uses its own full-width row', '.allocation-status{grid-column:1/-1;grid-row:2;' in css)
check('allocation grid uses shrinkable columns', 'grid-template-columns:minmax(0,1.65fr) minmax(0,.9fr) minmax(86px,.42fr) auto' in css)

# A POST without an explicit action must not execute the expensive profile/workload branch.
rogue=render({'school_type':'gymnasio','gym_general_a':2,'gym_general_b':2,'gym_general_c':2})
check('rogue POST does not unlock results', 'data-staffing-tab="results" role="tab" aria-selected="false" disabled' in rogue)
check('rogue POST does not build staffing matrix', 'id="staffingMatrixTable"' not in rogue)

valid={
 'staffing_action':'profile','school_type':'gymnasio','gym_general_a':2,'gym_general_b':2,'gym_general_c':1,
 'gym_lang_a_fr':1,'gym_lang_a_de':1,'gym_lang_a_it':0,
 'gym_lang_b_fr':2,'gym_lang_b_de':0,'gym_lang_b_it':0,
 'gym_lang_c_fr':0,'gym_lang_c_de':0,'gym_lang_c_it':1,
}
page=render(valid)
check('explicit profile action executes calculation', 'id="staffingMatrixTable"' in page)
check('Gym French max follows A general sections', re.search(r'<input(?=[^>]*id="gym_lang_a_fr")(?=[^>]*max="2")[^>]*>',page) is not None)
check('Gym German max is independent and also 2', re.search(r'<input(?=[^>]*id="gym_lang_a_de")(?=[^>]*max="2")[^>]*>',page) is not None)
check('Gym Italian max is independent and also 2', re.search(r'<input(?=[^>]*id="gym_lang_a_it")(?=[^>]*max="2")[^>]*>',page) is not None)

invalid=dict(valid)
invalid.update({'gym_lang_a_fr':3,'gym_lang_a_de':2,'gym_lang_a_it':2})
invalid_page=render(invalid)
check('backend reports over-limit French clearly', 'Οι ομάδες «Γαλλικά» της Α΄ τάξης (3) δεν μπορούν να ξεπερνούν τα 2 κανονικά τμήματα της ίδιας τάξης.' in invalid_page)
check('backend does not reject independent German=2', 'second_foreign_language_groups_exceeds_general_sections:Γερμανικά' not in invalid_page)
check('backend does not reject independent Italian=2', 'second_foreign_language_groups_exceeds_general_sections:Ιταλικά' not in invalid_page)

# Direct builder contract: invalid value is preserved, never silently clamped.
probe='''<?php
require %s;
$p=schoolProfileBuildDayGymnasium2026(array(
 'general_sections'=>array('Α΄'=>2,'Β΄'=>1,'Γ΄'=>1),
 'second_foreign_language_groups'=>array(
  'Α΄'=>array('Γαλλικά'=>3,'Γερμανικά'=>2,'Ιταλικά'=>2),
  'Β΄'=>array('Γαλλικά'=>1,'Γερμανικά'=>0,'Ιταλικά'=>0),
  'Γ΄'=>array('Γαλλικά'=>1,'Γερμανικά'=>0,'Ιταλικά'=>0)
 )
));
echo json_encode($p, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
''' % json.dumps(str(PROFILE))
p=subprocess.run(['php'],cwd=ROOT,input=probe,text=True,capture_output=True)
if p.returncode:
    print(p.stderr); raise SystemExit(p.returncode)
obj=json.loads(p.stdout)
groups=obj['structures']['gymnasio']['choice_option_sections']['Α΄']['gym.deyteri_xeni']
issues=obj.get('validation_issues',[])
check('backend preserves over-limit value instead of clamping', groups['Γαλλικά']==3)
check('backend emits per-language validation issue', any('Γαλλικά:3>2' in x for x in issues))
check('sum across languages is not capped', groups['Γερμανικά']==2 and groups['Ιταλικά']==2 and not any('Γερμανικά:2>2' in x or 'Ιταλικά:2>2' in x for x in issues))

gel_probe='''<?php
require %s;
$p=schoolProfileBuildDayGel2026(array(
 'general_sections'=>array('Α΄'=>3,'Β΄'=>2,'Γ΄'=>2),
 'second_foreign_language_groups'=>array(
  'Α΄'=>array('Γαλλικά'=>3,'Γερμανικά'=>3),
  'Β΄'=>array('Γαλλικά'=>3,'Γερμανικά'=>2)
 )
));
echo json_encode($p, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
''' % json.dumps(str(PROFILE))
p=subprocess.run(['php'],cwd=ROOT,input=gel_probe,text=True,capture_output=True)
if p.returncode:
    print(p.stderr); raise SystemExit(p.returncode)
gel_obj=json.loads(p.stdout)
gel_groups=gel_obj['structures']['gel']['choice_option_sections']
gel_issues=gel_obj.get('validation_issues',[])
check('GEL A languages may each equal A section count', gel_groups['Α΄']['gel.general.deyteri_xeni']['Γαλλικά']==3 and gel_groups['Α΄']['gel.general.deyteri_xeni']['Γερμανικά']==3 and not any(':Α΄:second_foreign_language_groups_exceeds_general_sections:' in x for x in gel_issues))
check('GEL B over-limit language value is preserved', gel_groups['Β΄']['gel.general.deyteri_xeni']['Γαλλικά']==3)
check('GEL B over-limit language emits validation issue', any('gel:Β΄:second_foreign_language_groups_exceeds_general_sections:Γαλλικά:3>2' == x for x in gel_issues))

failed=[n for n,ok in checks if not ok]
for n,ok in checks: print(('PASS' if ok else 'FAIL')+': '+n)
print('RESULT %d PASS / %d FAIL' % (len(checks)-len(failed),len(failed)))
raise SystemExit(1 if failed else 0)
