from pathlib import Path
import re
import subprocess
import textwrap

ROOT = Path(__file__).resolve().parents[1]
engine = (ROOT/'includes/abroad-calculations.js').read_text(encoding='utf-8')
rules = (ROOT/'includes/abroad-preference-rules.js').read_text(encoding='utf-8')
ui = (ROOT/'includes/abroad-ui.js').read_text(encoding='utf-8')
page = (ROOT/'ypologismos-morion-apospasis-exoteriko.php').read_text(encoding='utf-8')

checks=[]
def check(name, cond):
    checks.append((name,bool(cond)))
    print(('PASS' if cond else 'FAIL')+': '+name)

check('legacy primaryLevel removed from abroad implementation', 'primaryLevel' not in engine and 'primaryLevel' not in ui and 'primaryLevel' not in page)
check('legacy primaryLanguagePoints removed from abroad implementation', 'primaryLanguagePoints' not in engine and 'primaryLanguagePoints' not in ui)
check('country language has semantic input id', 'id="countryLanguageLevel"' in page and "$('countryLanguageLevel').value" in ui)
check('alternative language has semantic input id', 'id="alternativeLanguageLevel"' in page and "$('alternativeLanguageLevel').value" in ui)
check('UI sends both semantic language fields', 'countryLanguageLevel:' in ui and 'alternativeLanguageLevel:' in ui)
check('engine reads both semantic language fields', 'options.countryLanguageLevel' in engine and 'options.alternativeLanguageLevel' in engine)
check('preference rules are data-driven', 'const PREFERENCE_RULES = Object.freeze({' in rules and 'specialtyNotes:' in rules)
check('UI has no destination-specific note branches', "selected.includes('de_mu')" not in ui and "selected.includes('ch')" not in ui)
check('UI resolves notes through shared preference rules', 'AbroadPreferenceRules.notesFor(selected, specialty)' in ui)
check('preference rules loaded before UI', page.find('abroad-preference-rules.js') < page.find('abroad-ui.js'))
check('copy summary uses semantic result', 'fmt(r.tableLanguagePoints)' in ui)

node_script = textwrap.dedent(f'''
  const vm = require('vm');
  const fs = require('fs');
  const sandbox = {{ window: {{ EducationCore: {{ MAX_SERVICE_YEARS: 50 }} }} }};
  vm.createContext(sandbox);
  vm.runInContext(fs.readFileSync({str(ROOT/'includes/abroad-calculations.js')!r}, 'utf8'), sandbox);
  const calc = sandbox.window.AbroadSecondment.calculate;
  const base = {{
    specialtySelected:true, preferenceSelected:true, branchAllowed:'yes',
    educationYears:5, educationYearsAnswered:true,
    teachingYears:3, teachingYearsAnswered:true,
    blockingIssue:'no', bilingualPosition:'no', secondLanguageLevel:'none'
  }};
  const main = calc(Object.assign({{}}, base, {{
    tableType:'main', countryLanguageLevel:'c1', alternativeLanguageLevel:'c2'
  }}));
  const alternative = calc(Object.assign({{}}, base, {{
    tableType:'alternative', countryLanguageLevel:'c2', alternativeLanguageLevel:'c1',
    alternativeLanguage:'english', alternativeDifferentFromCountry:'yes'
  }}));
  const missingAlternative = calc(Object.assign({{}}, base, {{
    tableType:'alternative', countryLanguageLevel:'c2', alternativeLanguageLevel:'',
    alternativeLanguage:'english', alternativeDifferentFromCountry:'yes'
  }}));
  if (main.tableLanguagePoints !== 30) throw new Error('main must use countryLanguageLevel only');
  if (alternative.tableLanguagePoints !== 20) throw new Error('alternative must use alternativeLanguageLevel only');
  if (!missingAlternative.unanswered.includes('επίπεδο εναλλακτικής γλώσσας')) throw new Error('alternative level requirement missing');

  vm.runInContext(fs.readFileSync({str(ROOT/'includes/abroad-preference-rules.js')!r}, 'utf8'), sandbox);
  const noteApi = sandbox.window.AbroadPreferenceRules;
  const pe82 = noteApi.notesFor(['de_mu'], 'ΠΕ82');
  const ch = noteApi.notesFor(['ch'], 'ΠΕ02');
  const none = noteApi.notesFor(['fr'], 'ΠΕ02');
  if (pe82.length !== 2 || !pe82.some(x => x.includes('Γ1'))) throw new Error('PE82 Munich rule missing');
  if (ch.length !== 1 || !ch[0].includes('Ελβετία')) throw new Error('Switzerland rule missing');
  if (none.length !== 0) throw new Error('unexpected note for rule-less preference');
''')
proc = subprocess.run(['node','-e',node_script], cwd=ROOT, capture_output=True, text=True)
check('semantic language behavior works', proc.returncode == 0)
if proc.returncode != 0:
    print(proc.stderr)

failed=[n for n,ok in checks if not ok]
print(f'RESULT: {len(checks)-len(failed)}/{len(checks)} PASS')
raise SystemExit(1 if failed else 0)
