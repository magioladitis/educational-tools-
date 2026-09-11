#!/usr/bin/env python3
from pathlib import Path
import re, subprocess, sys
from bs4 import BeautifulSoup

ROOT = Path(__file__).resolve().parents[1]
checks = []

def check(name, condition):
    checks.append((name, bool(condition)))

pairs = {
    'ypologismos-morion-diefthynton-ypodiefthynton-sde.php': 'includes/sde-leadership-ui.js',
    'posa-paravola.php': 'includes/paravola-ui.js',
    'ypologismos-morion-apospasis-sde.php': 'includes/sde-detachment-ui.js',
    'metatropi-klimakas.php': 'includes/grade-scale-converter-ui.js',
}

for page, module in pairs.items():
    src = (ROOT / page).read_text(encoding='utf-8')
    js = (ROOT / module).read_text(encoding='utf-8')
    inline = []
    for m in re.finditer(r'<script\b([^>]*)>(.*?)</script>', src, re.S | re.I):
        if 'src=' not in m.group(1):
            inline.append(m.group(2))
    check(page + ' has no inline script', not inline)
    check(page + ' loads external UI controller', module in src)
    check(page + ' has no HTML inline event attributes', not re.search(r'\bon(?:click|change|input|submit|keyup|keydown|blur|focus)\s*=', src, re.I))
    check(page + ' has no PHP inline event attrs', not re.search(r"['\"]on(?:click|change|input|submit|keyup|keydown|blur|focus)['\"]\s*=>", src, re.I))
    check(module + ' remains substantial after extraction', len(js.splitlines()) >= 100)
    node = subprocess.run(['node', '--check', str(ROOT / module)], capture_output=True, text=True)
    check(module + ' syntax', node.returncode == 0)
    php = subprocess.run(['php', str(ROOT / page)], cwd=ROOT, capture_output=True, text=True)
    check(page + ' PHP render', php.returncode == 0)
    if php.returncode == 0:
        soup = BeautifulSoup(php.stdout, 'html.parser')
        ids = [el.get('id') for el in soup.select('[id]')]
        check(page + ' no duplicate rendered ids', len(ids) == len(set(ids)))

lead_page = (ROOT / 'ypologismos-morion-diefthynton-ypodiefthynton-sde.php').read_text(encoding='utf-8')
lead_ui = (ROOT / 'includes/sde-leadership-ui.js').read_text(encoding='utf-8')
for dep in ['includes/language-calculations.js', 'includes/language-pair-lock.js', 'includes/sde-leadership-calculations.js']:
    check('leadership dependency precedes UI: ' + dep, lead_page.index(dep) < lead_page.index('includes/sde-leadership-ui.js'))
check('leadership copy has stable external-binding id', "id' => 'sdeLeadershipCopyBtn'" in lead_page)
check('leadership reset has stable external-binding id', "id' => 'sdeLeadershipResetBtn'" in lead_page)
lead_change = ['role','permanentTeacher','tertiaryDegree','assignmentEligible','computerKnowledge','adultEducationExperience','adminQualifications','blockingIssue','phd','master','esdda','secondDegree','language1','languageLevel1','languageAppointment1','language2','languageLevel2','languageAppointment2']
lead_input = ['educationalServiceYears','sdeTeachingYears','sdeTeachingHours','sdeTransferredYears','adultNonformalHours','schoolTeachingYears','schoolTeachingHours','schoolTransferredYears','sdeDirectorYears','sdeDeputyYears','otherAdminYears','trainingHours','interviewScore']
for field in lead_change + lead_input + ['sdeLeadershipCopyBtn','sdeLeadershipResetBtn']:
    check('leadership UI references binding target ' + field, ("'" + field + "'") in lead_ui)
check('leadership keeps role-specific change handler', "$('role').addEventListener('change', roleChanged)" in lead_ui)
check('leadership keeps interview normalization before recalc', "normalizeBoundedScore(this); calculate();" in lead_ui)
check('leadership keeps language pair synchronization', "LanguagePairLock.sync" in lead_ui and "languageChanged" in lead_ui)

sde_page = (ROOT / 'ypologismos-morion-apospasis-sde.php').read_text(encoding='utf-8')
sde_ui = (ROOT / 'includes/sde-detachment-ui.js').read_text(encoding='utf-8')
for dep in ['includes/language-calculations.js', 'includes/asep-language-selector.js', 'includes/sde-calculations.js']:
    check('SDE detachment dependency precedes UI: ' + dep, sde_page.index(dep) < sde_page.index('includes/sde-detachment-ui.js'))
check('SDE detachment reset has stable external-binding id', "id' => 'sdeDetachmentResetBtn'" in sde_page)
sde_change = ['specialty','mathInfoDegree','formerPE09','formerPE1208','teleEducation','blockingIssue','phd','master','secondDegree','secondPhd','secondMaster','computer']
sde_input = ['eligibilitySchoolYears','formalEducationYears','sdeTrainingHours','adultTrainingHours','sdeYears','sdeHourlyHours','adultEducationHours']
for field in sde_change + sde_input + ['sdeDetachmentResetBtn']:
    check('SDE detachment UI references binding target ' + field, ("'" + field + "'") in sde_ui)
check('SDE specialty-specific UI retained', "sp === 'ΠΕ86'" in sde_ui and "sp.startsWith('ΠΕ04.')" in sde_ui)
check('SDE language custom event retained', "document.addEventListener('asep-language-change', calculate)" in sde_ui)

par_page = (ROOT / 'posa-paravola.php').read_text(encoding='utf-8')
par_ui = (ROOT / 'includes/paravola-ui.js').read_text(encoding='utf-8')
check('paravola keeps both specialty groups', 'ΠΕ60' in par_ui and 'ΠΕ91' in par_ui)
check('paravola keeps one/two proclamation mapping', '1ΓΕ/2026' in par_ui and '2ΓΕ/2026' in par_ui)
check('paravola keeps 15 euro unit cost', 'const costPerParavolo = 15;' in par_ui)
check('paravola keeps duplicate-specialty protection', 'updateDuplicateProtection' in par_ui and 'option.disabled' in par_ui)
check('paravola has no print trigger', 'window.print' not in par_page and 'window.print' not in par_ui)
check('paravola still excluded from generic print registry', "'posa-paravola.php'" not in (ROOT / 'includes/education-print.js').read_text(encoding='utf-8'))

scale_page = (ROOT / 'metatropi-klimakas.php').read_text(encoding='utf-8')
scale_ui = (ROOT / 'includes/grade-scale-converter-ui.js').read_text(encoding='utf-8')
check('scale print button now has id and no onclick', 'id="printBtn"' in scale_page and 'onclick=' not in scale_page)
check('scale print externally wired', "getElementById('printBtn').addEventListener('click'" in scale_ui and 'window.print()' in scale_ui)
check('scale preserves decimal range validation', 'grade < 1 || grade > 10' in scale_ui)
check('scale preserves 10-to-20 conversion', 'grade20 = round2(grade10 * 2)' in scale_ui)
check('scale preserves 1GE points rule', 'grade10 * 2.5' in scale_ui)
check('scale preserves 1GT points rule', 'grade20 * 3' in scale_ui)
check('scale preserves hundredths fraction', 'denominator:100' in scale_ui)

config = (ROOT / 'includes/config.php').read_text(encoding='utf-8')
check('release version 3.21.05', "define('EDU_TOOLS_VERSION', '3.21.05')" in config)

failed = [name for name, ok in checks if not ok]
for name, ok in checks:
    print(('PASS' if ok else 'FAIL') + ': ' + name)
print(f'RESULT {len(checks)-len(failed)} PASS / {len(failed)} FAIL')
sys.exit(1 if failed else 0)
