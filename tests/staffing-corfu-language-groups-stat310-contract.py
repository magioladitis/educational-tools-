#!/usr/bin/env python3
from pathlib import Path
import csv
ROOT=Path(__file__).resolve().parents[1]
REG=ROOT/'data/school_registry_v1-dde-kerkyras-2026-2027-full.csv'
SRC=ROOT/'data/myschool-stat3_10-language-groups-dde-kerkyras-2026-09-06.csv'
checks=[]
def check(name,cond): checks.append((name,bool(cond)))
with REG.open(encoding='utf-8-sig',newline='') as f: reg=list(csv.DictReader(f,delimiter=';'))
with SRC.open(encoding='utf-8-sig',newline='') as f: src=list(csv.DictReader(f,delimiter=';'))
by_code={r['Κωδικός Υπουργείου']:r for r in reg}
obs={}
for r in src:
    obs.setdefault(r['school_code'],{}).setdefault(r['grade'],{})[r['language']]=int(r['groups'])
check('normalized stat3_10 has 256 rows', len(src)==256)
check('normalized stat3_10 resolves 35 schools', len(obs)==35)
check('all stat3_10 school codes exist in Corfu registry', set(obs)<=set(by_code))
supported=[r for r in reg if r['Είδος σχολείου'] in ('Ημερήσιο Γυμνάσιο','Ημερήσιο Γενικό Λύκειο (ΓΕΛ)')]
check('all 24 supported schools are present in stat3_10', len(supported)==24 and all(r['Κωδικός Υπουργείου'] in obs for r in supported))
cols={
 ('Α','Γαλλικά'):'Α Γαλλικά ομάδες',('Α','Γερμανικά'):'Α Γερμανικά ομάδες',('Α','Ιταλικά'):'Α Ιταλικά ομάδες',
 ('Β','Γαλλικά'):'Β Γαλλικά ομάδες',('Β','Γερμανικά'):'Β Γερμανικά ομάδες',('Β','Ιταλικά'):'Β Ιταλικά ομάδες',
 ('Γ','Γαλλικά'):'Γ Γαλλικά ομάδες',('Γ','Γερμανικά'):'Γ Γερμανικά ομάδες',('Γ','Ιταλικά'):'Γ Ιταλικά ομάδες'}
for row in supported:
    code=row['Κωδικός Υπουργείου']; d=obs[code]
    grades=('Α','Β','Γ') if row['Είδος σχολείου']=='Ημερήσιο Γυμνάσιο' else ('Α','Β')
    for grade in grades:
        for lang in ('Γαλλικά','Γερμανικά') + (('Ιταλικά',) if row['Είδος σχολείου']=='Ημερήσιο Γυμνάσιο' else ()): 
            expected=d.get(grade,{}).get(lang,0)
            actual=int(row[cols[(grade,lang)]] or 0)
            check(f'{code} {grade} {lang} matches stat3_10', actual==expected)
    if row['Είδος σχολείου']=='Ημερήσιο Γυμνάσιο':
        check(f'{code} gym second-language pending cleared', row['Εκκρεμή πεδία']=='')
    else:
        check(f'{code} GEL language pending cleared', '2ης ξένης γλώσσας' not in row['Εκκρεμή πεδία'])
check('2nd Gymnasium B French=1 German=3', by_code['2401020']['Β Γαλλικά ομάδες']=='1' and by_code['2401020']['Β Γερμανικά ομάδες']=='3')
check('3rd GEL A French=4 German=4', by_code['2451030']['Α Γαλλικά ομάδες']=='4' and by_code['2451030']['Α Γερμανικά ομάδες']=='4')
check('Lefkimmi Gym has zero French and three German in A', by_code['2402010']['Α Γαλλικά ομάδες']=='0' and by_code['2402010']['Α Γερμανικά ομάδες']=='3')
failed=[n for n,ok in checks if not ok]
for n,ok in checks: print(('PASS' if ok else 'FAIL')+': '+n)
print('RESULT %d PASS / %d FAIL' % (len(checks)-len(failed),len(failed)))
raise SystemExit(1 if failed else 0)
