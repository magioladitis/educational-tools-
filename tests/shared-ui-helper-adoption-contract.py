from pathlib import Path
ROOT=Path(__file__).resolve().parents[1]
checks=[]
def check(name, cond):
 print(('PASS ' if cond else 'FAIL ')+name); checks.append(bool(cond))
core=(ROOT/'includes/education-core.js').read_text()
for token in ['parseNumber: parseNumber','formatPoints: formatPoints','summaryLines: summaryLines','resetControls: resetControls']:
 check('EducationCore exports '+token.split(':')[0], token in core)
for fn in ['asep-4ea-ui.js','asep-5ea-ui.js']:
 s=(ROOT/'includes'/fn).read_text()
 check(fn+' uses shared formatter','EducationCore.formatPoints' in s)
 check(fn+' uses shared summary builder','EducationCore.summaryLines' in s)
 check(fn+' uses shared reset','EducationCore.resetControls' in s)
 check(fn+' no local fixed-2 formatter',"toLocaleString('el-GR',{minimumFractionDigits:2,maximumFractionDigits:2})" not in s)
for fn in ['asep-pe-academic.js','asep-te-academic.js','asep-de-academic.js']:
 s=(ROOT/'includes'/fn).read_text()
 check(fn+' uses shared number parser','EducationCore.parseNumber' in s)
print('RESULT %d PASS / %d FAIL'%(sum(checks),len(checks)-sum(checks)))
raise SystemExit(0 if all(checks) else 1)
