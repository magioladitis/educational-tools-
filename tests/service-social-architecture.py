from pathlib import Path
import re, sys, subprocess
ROOT=Path(__file__).resolve().parents[1]
pages=[
 'ypologismos-morion.php','ypologismos-morion-1gt-2024.php','ypologismos-morion-1ea-2025.php',
 'ypologismos-morion-2ea-2025.php','ypologismos-morion-3ea-2025.php','ypologismos-morion-4ea-2025.php']
expected={
 'ypologismos-morion.php': {'regular','difficult','three-month-regular-2020','three-month-difficult-2020','three-month-regular-2021','three-month-difficult-2021','private','digital-tutoring'},
 'ypologismos-morion-1gt-2024.php': {'regular','difficult','three-month-regular-2020','three-month-difficult-2020','three-month-regular-2021','three-month-difficult-2021'},
 'ypologismos-morion-1ea-2025.php': {'regular','difficult','three-month-regular-2020','three-month-difficult-2020','three-month-regular-2021','three-month-difficult-2021'},
 'ypologismos-morion-2ea-2025.php': {'regular','difficult','three-month-regular-2020','three-month-difficult-2020','three-month-regular-2021','three-month-difficult-2021'},
 'ypologismos-morion-3ea-2025.php': {'regular','difficult','three-month-regular-2020','three-month-difficult-2020','three-month-regular-2021','three-month-difficult-2021','private','digital-tutoring'},
 'ypologismos-morion-4ea-2025.php': {'regular','difficult','three-month-regular-2020','three-month-difficult-2020','three-month-regular-2021','three-month-difficult-2021','private'},
}
passn=fail=0
def t(name,ok):
 global passn,fail
 if ok: print('PASS',name);passn+=1
 else: print('FAIL',name);fail+=1

# Static source architecture
for name in pages:
 s=(ROOT/name).read_text()
 t(name+' uses service controller','AsepServiceController' in s)
 t(name+' uses social controller','AsepSocialCriteria' in s)
 t(name+' no direct EducationSocial.calculate','EducationSocial.calculate(' not in s)
 t(name+' no direct service primitives',not re.search(r'EducationService\.(regularPublic|difficult|threeMonthRegular2020|threeMonthRegular2021|threeMonthDifficult2020|threeMonthDifficult2021|privateSchool)\(',s))
 t(name+' service controller asset',"edu_asset_url('includes/asep-service-controller.js')" in s)
 t(name+' social controller asset',"edu_asset_url('includes/asep-social-criteria.js')" in s)

# Rendered contracts — render on demand; do not depend on stored snapshots.
for name in pages:
 p=subprocess.run(['php', name], cwd=ROOT, capture_output=True, text=True)
 t(name+' PHP render', p.returncode==0 and not p.stderr.strip())
 html=p.stdout
 roles=set(re.findall(r'data-service-role="([^"]+)"',html))
 t(name+' exact service roles', roles==expected[name])
 t(name+' one service root', html.count('data-component="asep-service-criteria"')==1)
 t(name+' one social root', html.count('data-component="asep-social-criteria"')==1)
 t(name+' social mapping present', all(x in html for x in ['data-children-id=','data-candidate-id=','data-spouse-id=','data-child-id=','data-marriage-id=','data-mental-id=']))

# Shared component contracts
three=(ROOT/'includes/components/asep-three-month-service.php').read_text()
for role in ['three-month-regular-2020','three-month-difficult-2020','three-month-regular-2021','three-month-difficult-2021']:
 t('three-month role '+role, role in three)
social=(ROOT/'includes/components/asep-social-criteria.php').read_text()
t('social component default id', "'socialCriteria'" in social)
t('social warning mode supported','warning_mode' in social)
svc=(ROOT/'includes/service-calculations.js').read_text()
t('service aggregator exported','calculateAsepService' in svc)

print(f'RESULT {passn} PASS / {fail} FAIL')
sys.exit(1 if fail else 0)
