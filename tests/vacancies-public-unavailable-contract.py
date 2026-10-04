#!/usr/bin/env python3
from pathlib import Path
import subprocess, sys
ROOT=Path(__file__).resolve().parents[1]
PASS=FAIL=0

def check(cond,msg):
    global PASS,FAIL
    if cond:
        PASS+=1; print('PASS',msg)
    else:
        FAIL+=1; print('FAIL',msg)

login=(ROOT/'kena-sxoleion-login.php').read_text(encoding='utf-8')
check('Η εφαρμογή δεν είναι διαθέσιμη εδώ' in login, 'public unavailable state uses neutral user-facing title')
check('λειτουργεί σε ξεχωριστό περιβάλλον' in login, 'public unavailable state explains availability without technical detail')
check('Επιστροφή στα εργαλεία' in login and 'href="ergaleia.php"' in login, 'public unavailable state offers a safe return action')
check('$serviceReady = is_file(vacanciesConfigPath()) && vacanciesDbReady();' in login, 'availability condition remains server-derived')
check(not (ROOT/'includes/vacancies-config.php').exists(), 'release package omits private vacancies-config.php')
check('Η υπηρεσία δεν είναι προσωρινά διαθέσιμη' in login, 'account-layer unavailable state is also non-technical')
for forbidden in (
    'Δεν υπάρχει ακόμη το ιδιωτικό',
    'Έλεγξε τα στοιχεία της MariaDB',
    'Δεν έχει εγκατασταθεί ακόμη η διαχείριση λογαριασμών',
    'sql/vacancies-v1.5-accounts-school-profile.sql',
):
    check(forbidden not in login, 'user-facing login source omits technical setup detail: '+forbidden)

# Render the config-less release state exactly as users.sch.gr sees it.
proc=subprocess.run(['php', str(ROOT/'kena-sxoleion-login.php')], cwd=ROOT, capture_output=True, text=True)
html=proc.stdout
check(proc.returncode == 0, 'config-less vacancies login renders successfully')
check('Η εφαρμογή δεν είναι διαθέσιμη εδώ' in html, 'rendered config-less state is neutral')
check('Επιστροφή στα εργαλεία' in html, 'rendered config-less state has return action')
for forbidden in ('vacancies-config.php','MariaDB','configuration','migration','sql/'):
    check(forbidden not in html, 'rendered public state does not expose '+forbidden)

check(subprocess.run(['php','-l',str(ROOT/'kena-sxoleion-login.php')],capture_output=True).returncode==0, 'vacancies login PHP syntax')
print(f'RESULT {PASS} PASS / {FAIL} FAIL')
sys.exit(1 if FAIL else 0)
