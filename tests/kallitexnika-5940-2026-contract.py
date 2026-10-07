#!/usr/bin/env python3
"""Regression contract for FEK B 5940/05.10.2026 Artistic Schools assignments."""
from pathlib import Path
import json
import subprocess
from test_runtime_helpers import render_php

ROOT = Path(__file__).resolve().parents[1]
php = r'''
require "includes/teaching-assignments-data.php";
echo json_encode(teachingAssignmentsData(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
'''
rows = json.loads(subprocess.check_output(['php', '-r', php], cwd=ROOT, text=True))
checks=[]
def check(name, cond): checks.append((name, bool(cond)))

def row(school, grade, section, subject):
    hits=[r for r in rows if r.get('school')==school and r.get('grade','')==grade and r.get('section','')==section and r.get('subject')==subject]
    check('unique row: '+grade+' '+subject+' '+section, len(hits)==1)
    return hits[0] if len(hits)==1 else {}

# Καλλιτεχνικό Γυμνάσιο: ΤΕ16 is A assignment, not B, in all three Music rows.
for section in ['Κατεύθυνση Εικαστικών Τεχνών','Κατεύθυνση Θεάτρου – Κινηματογράφου','Κατεύθυνση Χορού']:
    r=row('kallitexniko_gymnasio','',section,'Μουσική')
    check('Gym Music TE16 A: '+section, r.get('A')==['ΠΕ79.01','ΤΕ16'])
    check('Gym Music no B: '+section, not r.get('B'))

# Classical Dance: pianist can be PE79.01 or TE16, both within A assignment.
for grade in ['', 'Α΄','Β΄','Γ΄']:
    school='kallitexniko_gymnasio' if grade=='' else 'kallitexniko_gel'
    r=row(school,grade,'Κατεύθυνση Χορού','Κλασικός Χορός')
    check('Classical Dance TE16 A '+(grade or 'Gym'), r.get('A')==['Ειδικός πίνακας ΚΛΑΣΙΚΟΥ ΧΟΡΟΥ','ΠΕ79.01','ΤΕ16'])
    check('Classical Dance no B '+(grade or 'Gym'), not r.get('B'))

# A Lyceum changes.
r=row('kallitexniko_gel','Α΄','Κατεύθυνση Θεάτρου – Κινηματογράφου','Φωνητική – Ορθοφωνία')
check('A Lyceum Voice TE16 A', r.get('A')==['ΠΕ91.02','ΠΕ79.01','ΤΕ16'] and not r.get('B'))
r=row('kallitexniko_gel','Α΄','Κατεύθυνση Χορού','Ρυθμός – Μετρική – Κίνηση')
check('A Lyceum Rhythm TE16 also A', r.get('A')==['Ειδικός πίνακας ΚΙΝΗΣΗ–ΧΟΡΟΣ','ΠΕ79.01','ΤΕ16'])
check('A Lyceum Rhythm B retained', r.get('B')==['ΠΕ11','ΠΕ79.01','ΤΕ16'])
r=row('kallitexniko_gel','Α΄','Κατεύθυνση Εικαστικών Τεχνών','Ιστορία Τέχνης')
check('A Lyceum Art History footnote 2', 'διδάσκεται από έναν/μία εκπαιδευτικό' in r.get('note','') and 'χωρισμός τμήματος' in r.get('note',''))

# B/C Voice-Song and B Music-Metric-Improvisation.
for grade in ['Β΄','Γ΄']:
    r=row('kallitexniko_gel',grade,'Κατεύθυνση Θεάτρου – Κινηματογράφου','Φωνητική – Τραγούδι')
    check(grade+' Voice-Song TE16 A', r.get('A')==['ΠΕ79.01','ΤΕ16','ΠΕ91.02'] and not r.get('B'))
r=row('kallitexniko_gel','Β΄','Κατεύθυνση Χορού','Μουσική – Μετρική – Αυτοσχεδιασμός')
check('B Lyceum Music-Metric TE16 A', r.get('A')==['Ειδικός πίνακας ΚΙΝΗΣΗ–ΧΟΡΟΣ','ΠΕ79.01','ΤΕ16'] and not r.get('B'))

# Legal/source/UI contract.
legal=(ROOT/'includes/legal-sources.php').read_text(encoding='utf-8')
assign_legal=(ROOT/'includes/teaching-assignments-legal.php').read_text(encoding='utf-8')
weekly_legal=(ROOT/'includes/weekly-timetable-legal.php').read_text(encoding='utf-8')
check('5940 registry key', "'kallitexnika_assignments_2026_5940'" in legal)
check('5940 official et.gr PDF URL', 'https://www.et.gr/api/DownloadFekPdf?fek_pdf=2026/B/5940' in legal)
check('assignment active map uses 5940', "kallitexnika_assignments_2026_5940" in assign_legal)
check('assignment active map drops old 2024 key', "$keys[] = 'kallitexnika_assignments_2024'" not in assign_legal)
check('weekly cross-check uses 5940', "kallitexnika_assignments_2026_5940" in weekly_legal)
for label, html in [('assignments',render_php('anatheseis-mathimaton.php')),('timetable',render_php('orologio-programma-mathimaton.php'))]:
    check(label+' public cites 5940/2026', '5940/2026' in html)

failed=[n for n,ok in checks if not ok]
for n,ok in checks: print(('PASS' if ok else 'FAIL')+': '+n)
print(f'RESULT {len(checks)-len(failed)} PASS / {len(failed)} FAIL')
raise SystemExit(1 if failed else 0)
