#!/usr/bin/env python3
from pathlib import Path
import subprocess,re
ROOT=Path(__file__).resolve().parents[1]
PAGE=ROOT/'ypologismos-didaktikon-anagkon.php'
CSS=ROOT/'assets'/'staffing-simulator.css'
SRC=PAGE.read_text(encoding='utf-8')
checks=[]
def check(name, cond): checks.append((name,bool(cond)))
p=subprocess.run(['php',str(PAGE)],cwd=ROOT,text=True,capture_output=True)
if p.returncode:
    print(p.stderr); raise SystemExit(p.returncode)
out=p.stdout

# Main six-step tab interface.
for key,panel in [('School','School'),('Results','Results'),('Personnel','Personnel'),('Allocation','Allocation'),('Vacancies','Vacancies'),('Specialties','Specialties')]:
    check('tab '+key+' controls panel', f'id="staffingTab{key}"' in out and f'aria-controls="staffingPanel{panel}"' in out)
    check('panel '+panel+' labelled by tab', f"'id'=>'staffingPanel{panel}'" in SRC and "'role'=>'tabpanel'" in SRC and f"'aria-labelledby'=>'staffingTab{key}'" in SRC)
check('main tabs use roving tabindex', 'tabIndex=active?0:-1' in SRC)
check('main tabs support arrow home end keys', 'function moveTabFocus(current,key)' in SRC and all(x in SRC for x in ["'ArrowRight'","'ArrowLeft'","'Home'","'End'"]))

# Allocation subtabs.
check('allocation subtabs have relationships', 'id="allocationViewTabSlots"' in SRC and 'aria-controls="allocationViewPanelSlots"' in SRC and 'id="allocationViewPanelPeople"' in SRC and 'aria-labelledby="allocationViewTabPeople"' in SRC)
check('allocation subtabs use roving tabindex', 'function activateAllocationView(button)' in SRC and 'b.tabIndex=active?0:-1' in SRC)

# Live state and repeated form controls.
check('school context state is polite live status', 'id="staffingContextState" role="status" aria-live="polite" aria-atomic="true"' in out)
check('csv/import statuses are live regions', all(f'id="{i}" role="status" aria-live="polite"' in SRC for i in ['schoolCsvStatus','schoolCsvActive','mySchoolStaffStatus','personnelCsvStatus']))
check('repeated personnel controls have accessible names', all(x in SRC for x in ['aria-label="Κλάδος εκπαιδευτικού"','aria-label="Ονοματεπώνυμο εκπαιδευτικού"','aria-label="Υποχρεωτικό ωράριο εκπαιδευτικού"','aria-label="Ρόλος εκπαιδευτικού"']))
check('allocation controls have accessible names', all(x in SRC for x in ['aria-label="Μάθημα και τμήμα προς κατανομή"','aria-label="Εκπαιδευτικός για την κατανομή"','aria-label="Ώρες κατανομής"']))
check('allocation uses one concise live summary', 'id="allocationLiveStatus" role="status" aria-live="polite" aria-atomic="true"' in SRC and "allocationLiveStatus.textContent='Κατανομή: '" in SRC and 'data-allocation-status role="status"' not in SRC)

# Data tables.
check('matrix has caption and scoped headers', 'Διδακτικές ώρες ανά κλάδο και προτεραιότητα ανάθεσης' in SRC and '<th scope="col">Κλάδος</th>' in SRC)
check('vacancy table has caption and scoped headers', 'Ακάλυπτες ώρες ανά τάξη, μάθημα και διαθέσιμο κλάδο' in SRC and '<th scope="col">Τάξη</th>' in SRC)
check('specialty table has caption and scoped headers', 'Προτεινόμενα κενά και πλεονάσματα ανά κλάδο' in SRC and '<th scope="col">Κλάδος / γραμμή δήλωσης</th>' in SRC)
check('details tables also have captions', 'Αιτιολόγηση επιλογής κλάδου για κοινά κενά' in SRC and 'Προτεινόμενη εσωτερική κάλυψη από υπάρχον προσωπικό' in SRC)

css=CSS.read_text(encoding='utf-8')
check('disabled workflow tabs have explicit visual state', '.mode-tab:disabled' in css and 'cursor:not-allowed' in css)

failed=[n for n,ok in checks if not ok]
for n,ok in checks: print(('PASS' if ok else 'FAIL')+': '+n)
print('RESULT %d PASS / %d FAIL' % (len(checks)-len(failed),len(failed)))
raise SystemExit(1 if failed else 0)
