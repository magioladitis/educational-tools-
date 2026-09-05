#!/usr/bin/env python3
from pathlib import Path
import json, subprocess, textwrap
ROOT=Path(__file__).resolve().parents[1]

# Real myschool section snapshots from a Corfu day Gymnasium, 2026-09-05.
# Teacher names are intentionally NOT persisted here; only course hours and branch codes
# relevant to the regulatory cross-audit are retained.
observed_hours = {
    'Α΄': {
        'Αρχαία Ελληνικά Κείμενα από Μετάφραση':2,
        'Αρχαία Ελληνική Γλώσσα':2,
        'Βιολογία':1,
        'Γεωλογία - Γεωγραφία':1,
        'Γλωσσική Διδασκαλία':3,
        'Εργαστήρια Δεξιοτήτων':1,
        'Θρησκευτικά':2,
        'Ιστορία':2,
        'Καλλιτεχνικά':1,
        'Μαθηματικά':4,
        'Μουσική':1,
        'Νεοελληνική Λογοτεχνία':2,
        'Οικιακή Οικονομία':2,
        'Πληροφορική':4,
        'Τεχνολογία':2,
        'Φυσική':1,
        'Φυσική Αγωγή':2,
    },
    'Β΄': {
        'Αρχαία Ελληνικά Κείμενα από Μετάφραση':2,
        'Αρχαία Ελληνική Γλώσσα':2,
        'Βιολογία':1,
        'Γεωλογία - Γεωγραφία':2,
        'Γλωσσική Διδασκαλία':2,
        'Εργαστήρια Δεξιοτήτων':1,
        'Θρησκευτικά':2,
        'Ιστορία':2,
        'Καλλιτεχνικά':1,
        'Μαθηματικά':4,
        'Μουσική':1,
        'Νεοελληνική Λογοτεχνία':2,
        'Πληροφορική':2,
        'Τεχνολογία':2,
        'Φυσική':2,
        'Φυσική Αγωγή':2,
        'Χημεία':1,
    },
    'Γ΄': {
        'Αρχαία Ελληνικά Κείμενα από Μετάφραση':2,
        'Αρχαία Ελληνική Γλώσσα':2,
        'Βιολογία':1,
        'Γλωσσική Διδασκαλία':2,
        'Εργαστήρια Δεξιοτήτων':1,
        'Θρησκευτικά':2,
        'Ιστορία':2,
        'Καλλιτεχνικά':1,
        'Κοινωνική και Πολιτική Αγωγή':3,
        'Μαθηματικά':4,
        'Μουσική':1,
        'Νεοελληνική Λογοτεχνία':2,
        'Οικονομικά':1,
        'Πληροφορική':1,
        'Τεχνολογία':1,
        'Φυσική':2,
        'Φυσική Αγωγή':2,
        'Χημεία':1,
    },
}

# Visible teacher branch codes from the same snapshots. Only rows where a teacher is
# visibly assigned are included. Expected priority is against the current assignment table.
observed_assignments = [
    ('Α΄','Αρχαία Ελληνικά Κείμενα από Μετάφραση','ΠΕ02','A'),
    ('Α΄','Αρχαία Ελληνική Γλώσσα','ΠΕ02','A'),
    ('Α΄','Βιολογία','ΠΕ04.01','B'),
    ('Α΄','Γεωλογία - Γεωγραφία','ΠΕ03','C'),
    ('Α΄','Εργαστήρια Δεξιοτήτων','ΠΕ01','B'),
    ('Α΄','Θρησκευτικά','ΠΕ01','A'),
    ('Α΄','Ιστορία','ΠΕ02','A'),
    ('Α΄','Μαθηματικά','ΠΕ03','A'),
    ('Α΄','Νεοελληνική Λογοτεχνία','ΠΕ02','A'),
    ('Α΄','Οικιακή Οικονομία','ΠΕ80','A'),
    ('Α΄','Πληροφορική','ΠΕ86','A'),
    ('Α΄','Τεχνολογία','ΠΕ82','A'),
    ('Α΄','Φυσική','ΠΕ04.01','A'),

    ('Β΄','Αρχαία Ελληνικά Κείμενα από Μετάφραση','ΠΕ02','A'),
    ('Β΄','Αρχαία Ελληνική Γλώσσα','ΠΕ02','A'),
    ('Β΄','Βιολογία','ΠΕ04.01','B'),
    ('Β΄','Γεωλογία - Γεωγραφία','ΠΕ04.01','B'),
    ('Β΄','Γλωσσική Διδασκαλία','ΠΕ02','A'),
    ('Β΄','Εργαστήρια Δεξιοτήτων','ΠΕ82','A'),
    ('Β΄','Θρησκευτικά','ΠΕ01','A'),
    ('Β΄','Ιστορία','ΠΕ02','A'),
    ('Β΄','Μαθηματικά','ΠΕ03','A'),
    ('Β΄','Νεοελληνική Λογοτεχνία','ΠΕ02','A'),
    ('Β΄','Πληροφορική','ΠΕ86','A'),
    ('Β΄','Τεχνολογία','ΠΕ82','A'),
    ('Β΄','Φυσική','ΠΕ04.01','A'),
    ('Β΄','Χημεία','ΠΕ04.01','B'),

    ('Γ΄','Αρχαία Ελληνικά Κείμενα από Μετάφραση','ΠΕ02','A'),
    ('Γ΄','Αρχαία Ελληνική Γλώσσα','ΠΕ02','A'),
    ('Γ΄','Βιολογία','ΠΕ04.01','B'),
    ('Γ΄','Γλωσσική Διδασκαλία','ΠΕ02','A'),
    ('Γ΄','Εργαστήρια Δεξιοτήτων','ΠΕ82','A'),
    ('Γ΄','Θρησκευτικά','ΠΕ01','A'),
    ('Γ΄','Ιστορία','ΠΕ02','A'),
    ('Γ΄','Κοινωνική και Πολιτική Αγωγή','ΠΕ80','B'),
    ('Γ΄','Μαθηματικά','ΠΕ03','A'),
    ('Γ΄','Νεοελληνική Λογοτεχνία','ΠΕ02','A'),
    ('Γ΄','Οικονομικά','ΠΕ80','A'),
    ('Γ΄','Τεχνολογία','ΠΕ82','A'),
    ('Γ΄','Φυσική','ΠΕ04.01','A'),
    ('Γ΄','Χημεία','ΠΕ04.01','B'),
]

php=textwrap.dedent(r'''<?php
require __DIR__ . '/includes/school-profile-general-education.php';
require __DIR__ . '/includes/school-profile-workload.php';
$p=schoolProfileBuildDayGymnasium2026(array(
  'profile_id'=>'corfu-myschool-cross-audit',
  'general_sections'=>array('Α΄'=>1,'Β΄'=>1,'Γ΄'=>1),
  // The section screenshots do not contain the separate foreign-language group rows.
  'second_foreign_language_groups'=>array(
    'Α΄'=>array('Γαλλικά'=>0,'Γερμανικά'=>0,'Ιταλικά'=>0),
    'Β΄'=>array('Γαλλικά'=>0,'Γερμανικά'=>0,'Ιταλικά'=>0),
    'Γ΄'=>array('Γαλλικά'=>0,'Γερμανικά'=>0,'Ιταλικά'=>0),
  ),
  // Directly observed from staffing hours: A and B are split, C is not.
  'technology_informatics_split_sections'=>array('Α΄'=>1,'Β΄'=>1,'Γ΄'=>0),
  'ethics_by_grade'=>array(
    'Α΄'=>array('exempt_students'=>0,'within_fifth_day'=>true),
    'Β΄'=>array('exempt_students'=>0,'within_fifth_day'=>true),
    'Γ΄'=>array('exempt_students'=>0,'within_fifth_day'=>true),
  ),
));
$m=schoolProfileWorkloadMatrix($p);
echo json_encode($m, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
''')
php=php.replace("__DIR__ . '/includes/", "'"+str(ROOT).replace("'","\\'")+"/includes/")
proc=subprocess.run(['php'],input=php,text=True,capture_output=True,cwd=ROOT)
if proc.returncode:
    print(proc.stderr); raise SystemExit(proc.returncode)
matrix=json.loads(proc.stdout)

# Build lookup of realized units.
units={(u['grade'],u['subject']):u for u in matrix['units']}
checks=[]
def check(name,cond): checks.append((name,bool(cond)))

# Hours: compare every row visible in each screenshot.
for grade,subjects in observed_hours.items():
    for subject,hours in subjects.items():
        u=units.get((grade,subject))
        check(f'{grade} {subject}: myschool hours={hours}', u is not None and u['school_hours']==hours)

check('A1 snapshot visible subject-hours sum = 33', sum(observed_hours['Α΄'].values())==33)
check('B1 snapshot visible subject-hours sum = 31', sum(observed_hours['Β΄'].values())==31)
check('C1 snapshot visible subject-hours sum = 31', sum(observed_hours['Γ΄'].values())==31)

# The exact split signature visible in the screenshots.
check('A1 split signature Informatics 4h', units[('Α΄','Πληροφορική')]['school_hours']==4)
check('A1 split signature Technology 2h', units[('Α΄','Τεχνολογία')]['school_hours']==2)
check('A1 split signature Home Economics 2h', units[('Α΄','Οικιακή Οικονομία')]['school_hours']==2)
check('B1 split signature Informatics 2h', units[('Β΄','Πληροφορική')]['school_hours']==2)
check('B1 split signature Technology 2h', units[('Β΄','Τεχνολογία')]['school_hours']==2)
check('C1 no-split signature Informatics 1h', units[('Γ΄','Πληροφορική')]['school_hours']==1)
check('C1 no-split signature Technology 1h', units[('Γ΄','Τεχνολογία')]['school_hours']==1)

# Assignment priority validation against the visibly assigned branch codes.
# One harmless spelling alias is normalized in the literal observation list.
for grade,subject,code,priority in observed_assignments:
    u=units.get((grade,subject))
    got=None
    if u:
        for p in ('A','B','C','SPECIAL'):
            if code in u.get('eligible_by_priority',{}).get(p,[]):
                got=p; break
    check(f'{grade} {subject}: {code} priority {priority}', got==priority)

# Foreign languages are a separate grouping in myschool and are not visible in these
# three class-section captures. Verify we did NOT interpret their absence as zero curriculum.
check('English remains a separate 2h unit in A', units.get(('Α΄','Αγγλικά'),{}).get('school_hours')==2)
check('English remains a separate 2h unit in B', units.get(('Β΄','Αγγλικά'),{}).get('school_hours')==2)
check('English remains a separate 2h unit in C', units.get(('Γ΄','Αγγλικά'),{}).get('school_hours')==2)

# Skills Workshops show distinct real branches in the snapshots (PE01 in A1, PE82 in B1/C1),
# supporting the frontend collapse while preserving the full backend assignment matrix.
check('Skills A1 PE01 remains B-assignment backend eligibility', 'ΠΕ01' in units[('Α΄','Εργαστήρια Δεξιοτήτων')]['eligible_by_priority']['B'])
check('Skills B1 PE82 remains A-assignment backend eligibility', 'ΠΕ82' in units[('Β΄','Εργαστήρια Δεξιοτήτων')]['eligible_by_priority']['A'])
check('Skills C1 PE82 remains A-assignment backend eligibility', 'ΠΕ82' in units[('Γ΄','Εργαστήρια Δεξιοτήτων')]['eligible_by_priority']['A'])

failed=[n for n,ok in checks if not ok]
for n,ok in checks:
    print(('PASS' if ok else 'FAIL')+': '+n)
print('OBSERVED_SUMS A=%d B=%d C=%d' % tuple(sum(observed_hours[g].values()) for g in ('Α΄','Β΄','Γ΄')))
print('MODEL_TOTAL_WITH_ENGLISH_NO_SECOND_FOREIGN=%d' % matrix['summary']['assignment_unit_hours'])
print('RESULT %d PASS / %d FAIL' % (len(checks)-len(failed),len(failed)))
raise SystemExit(1 if failed else 0)
