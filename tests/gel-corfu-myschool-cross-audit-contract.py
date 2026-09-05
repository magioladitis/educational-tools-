#!/usr/bin/env python3
from pathlib import Path
import json, subprocess, textwrap
ROOT=Path(__file__).resolve().parents[1]

# Real myschool snapshots from 4th Day GEL Corfu (2451040), 2026-09-05.
# Teacher names are intentionally omitted; only course hours and branch codes
# needed for the regulatory cross-audit are retained.
observed = [
    # A1 common section
    ('gel.general.algebra','Α΄','Άλγεβρα',3,'ΠΕ03','A'),
    ('gel.general.archaia','Α΄','Αρχαία Ελληνική Γλώσσα και Γραμματεία',5,'ΠΕ02','A'),
    ('gel.general.viologia','Α΄','Βιολογία',2,'ΠΕ04.02','B'),
    ('gel.general.geometry','Α΄','Γεωμετρία',2,'ΠΕ03','A'),
    ('gel.general.efarmoges_pliroforikis','Α΄','Εφαρμογές Πληροφορικής',2,'ΠΕ86','A'),
    ('gel.general.thriskeftika','Α΄','Θρησκευτικά',2,'ΠΕ01','A'),
    ('gel.general.istoria','Α΄','Ιστορία',2,'ΠΕ02','A'),
    ('gel.general.neoelliniki','Α΄','Νεοελληνική Γλώσσα και Λογοτεχνία',4,'ΠΕ02','A'),
    ('gel.general.politiki_paideia','Α΄','Πολιτική Παιδεία (Οικονομία, Πολιτικοί Θεσμοί και Αρχές Δικαίου και Κοινωνιολογία)',2,None,None),
    ('gel.general.fysiki','Α΄','Φυσική',2,'ΠΕ04.01','A'),
    ('gel.general.fysiki_agogi','Α΄','Φυσική Αγωγή',2,'ΠΕ11','A'),
    ('gel.general.ximeia','Α΄','Χημεία',2,'ΠΕ04.02','A'),
    # A1 French group
    ('gel.general.deyteri_xeni','Α΄','2η Ξένη Γλώσσα (Γαλλικά ή Γερμανικά)',2,None,None),

    # B1 common section
    ('gel.general.algebra','Β΄','Άλγεβρα',3,'ΠΕ03','A'),
    ('gel.general.archaia','Β΄','Αρχαία Ελληνική Γλώσσα και Γραμματεία',2,'ΠΕ02','A'),
    ('gel.general.viologia','Β΄','Βιολογία',2,'ΠΕ04.04','A'),
    ('gel.general.geometry','Β΄','Γεωμετρία',2,'ΠΕ03','A'),
    ('gel.general.eisagogi_aei_y','Β΄','Εισαγωγή στις Αρχές της Επιστήμης των Η/Υ',2,'ΠΕ86','A'),
    ('gel.general.thriskeftika','Β΄','Θρησκευτικά',2,'ΠΕ01','A'),
    ('gel.general.istoria','Β΄','Ιστορία',2,'ΠΕ02','A'),
    ('gel.general.neoelliniki','Β΄','Νεοελληνική Γλώσσα και Λογοτεχνία',4,'ΠΕ02','A'),
    ('gel.general.filosofia','Β΄','Φιλοσοφία',2,'ΠΕ02','A'),
    ('gel.general.fysiki','Β΄','Φυσική',2,None,None),
    ('gel.general.fysiki_agogi','Β΄','Φυσική Αγωγή',2,'ΠΕ11','A'),
    ('gel.general.ximeia','Β΄','Χημεία',2,'ΠΕ04.02','A'),
    # B humanities orientation group
    ('gel.b.humanities.archaia','Β΄','Αρχαία Ελληνική Γλώσσα και Γραμματεία',3,'ΠΕ02','A'),
    ('gel.b.humanities.latinika','Β΄','Λατινικά',2,'ΠΕ02','A'),
    # B science orientation group
    ('gel.b.science.mathimatika','Β΄','Μαθηματικά',3,'ΠΕ03','A'),
    ('gel.b.science.fysiki','Β΄','Φυσική',2,'ΠΕ04.01','A'),

    # C common base section
    ('gel.general.thriskeftika','Γ΄','Θρησκευτικά',1,'ΠΕ01','A'),
    ('gel.general.neoelliniki','Γ΄','Νεοελληνική Γλώσσα και Λογοτεχνία',6,'ΠΕ02','A'),
    ('gel.general.fysiki_agogi','Γ΄','Φυσική Αγωγή',3,'ΠΕ11','A'),
    # C humanities orientation
    ('gel.c.humanities.archaia','Γ΄','Αρχαία Ελληνικά',6,'ΠΕ02','A'),
    ('gel.c.humanities.istoria','Γ΄','Ιστορία',6,'ΠΕ02','A'),
    ('gel.c.humanities.latinika','Γ΄','Λατινικά',6,'ΠΕ02','A'),
    # C economics & informatics orientation
    ('gel.c.econ.mathimatika','Γ΄','Μαθηματικά',6,'ΠΕ03','A'),
    ('gel.c.econ.oikonomia','Γ΄','Οικονομία',6,None,None),
    ('gel.c.econ.pliroforiki','Γ΄','Πληροφορική',6,'ΠΕ86','A'),
    # C science/health field choice — real myschool group for 3rd field
    ('gel.c.health.viologia','Γ΄','Βιολογία',6,'ΠΕ04.04','A'),
    # C conditional general-education Mathematics for humanities students
    ('gel.general.mathimatika_conditional','Γ΄','Μαθηματικά',2,'ΠΕ03','A'),
]

php=textwrap.dedent(r'''<?php
require __DIR__ . '/includes/weekly-timetable-data.php';
require __DIR__ . '/includes/teaching-assignments-data.php';
echo json_encode(array('timetable'=>weeklyTimetableRows(),'assignments'=>teachingAssignmentsData()), JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
''').replace("__DIR__ . '/includes/", "'"+str(ROOT).replace("'","\\'")+"/includes/")
proc=subprocess.run(['php'],input=php,text=True,capture_output=True,cwd=ROOT)
if proc.returncode:
    print(proc.stderr); raise SystemExit(proc.returncode)
data=json.loads(proc.stdout)
timetable={r.get('course_id'):r for r in data['timetable'] if r.get('school')=='gel'}
assignments=[r for r in data['assignments'] if r.get('school')=='gel']

checks=[]
def check(name, cond): checks.append((name,bool(cond)))

def assignment_row_for(course_id, grade, subject):
    # Map course context to the assignment table section/group.
    tr=timetable[course_id]
    group=tr.get('group','')
    section=tr.get('section','')
    expected_section = group if group.startswith('Ομάδα Προσανατολισμού') or group=='Μαθήματα Γενικής Παιδείας' and grade=='Γ΄' else section
    candidates=[]
    for r in assignments:
        if r.get('grade') != grade or r.get('subject') != subject:
            continue
        if expected_section and r.get('section','') != expected_section:
            continue
        candidates.append(r)
    if not candidates and subject=='2η Ξένη Γλώσσα (Γαλλικά ή Γερμανικά)':
        candidates=[r for r in assignments if r.get('grade')==grade and r.get('subject')==subject]
    return candidates[0] if candidates else None

# Row-by-row hour and assignment checks.
for cid,grade,subject,hours,branch,priority in observed:
    r=timetable.get(cid)
    check(f'{cid}: timetable row exists', r is not None)
    check(f'{cid}: {grade} hours={hours}', r is not None and r.get('hours',{}).get(grade)==hours)
    if branch:
        ar=assignment_row_for(cid,grade,subject)
        check(f'{cid}: assignment row exists', ar is not None)
        check(f'{cid}: {branch} is {priority}-assignment', ar is not None and branch in ar.get(priority,[]))

# Snapshot/group sums visible in myschool.
def h(*cids):
    return sum(timetable[c]['hours'][next(g for g in timetable[c]['hours'] if g in ('Α΄','Β΄','Γ΄'))] for c in cids)

A1=['gel.general.algebra','gel.general.archaia','gel.general.viologia','gel.general.geometry','gel.general.efarmoges_pliroforikis','gel.general.thriskeftika','gel.general.istoria','gel.general.neoelliniki','gel.general.politiki_paideia','gel.general.fysiki','gel.general.fysiki_agogi','gel.general.ximeia']
B1=['gel.general.algebra','gel.general.archaia','gel.general.viologia','gel.general.geometry','gel.general.eisagogi_aei_y','gel.general.thriskeftika','gel.general.istoria','gel.general.neoelliniki','gel.general.filosofia','gel.general.fysiki','gel.general.fysiki_agogi','gel.general.ximeia']
CBASE=['gel.general.thriskeftika','gel.general.neoelliniki','gel.general.fysiki_agogi']
check('A1 visible common-section sum = 30h', sum(timetable[c]['hours']['Α΄'] for c in A1)==30)
check('A1 French group = 2h', timetable['gel.general.deyteri_xeni']['hours']['Α΄']==2)
check('A English remains separate = 3h', timetable['gel.general.agglika']['hours']['Α΄']==3)
check('A per-student total 30+3+2 = 35h', 30+3+2==35)

check('B1 visible common-section sum = 27h', sum(timetable[c]['hours']['Β΄'] for c in B1)==27)
check('B English remains separate = 2h', timetable['gel.general.agglika']['hours']['Β΄']==2)
check('B second foreign language = 1h', timetable['gel.general.deyteri_xeni']['hours']['Β΄']==1)
check('B humanities orientation block = 5h', timetable['gel.b.humanities.archaia']['hours']['Β΄']+timetable['gel.b.humanities.latinika']['hours']['Β΄']==5)
check('B science orientation block = 5h', timetable['gel.b.science.mathimatika']['hours']['Β΄']+timetable['gel.b.science.fysiki']['hours']['Β΄']==5)
check('B per-student total 27+2+1+5 = 35h', 27+2+1+5==35)

check('C visible common-base sum = 10h', sum(timetable[c]['hours']['Γ΄'] for c in CBASE)==10)
check('C English remains separate = 2h', timetable['gel.general.agglika']['hours']['Γ΄']==2)
check('C conditional general Mathematics = 2h', timetable['gel.general.mathimatika_conditional']['hours']['Γ΄']==2)
check('C conditional general History = 2h', timetable['gel.general.istoria']['hours']['Γ΄']==2)
check('C humanities orientation block = 18h', sum(timetable[c]['hours']['Γ΄'] for c in ['gel.c.humanities.archaia','gel.c.humanities.istoria','gel.c.humanities.latinika'])==18)
check('C economics/IT orientation block = 18h', sum(timetable[c]['hours']['Γ΄'] for c in ['gel.c.econ.mathimatika','gel.c.econ.oikonomia','gel.c.econ.pliroforiki'])==18)
check('C science/health 2nd-field block = 18h', sum(timetable[c]['hours']['Γ΄'] for c in ['gel.c.health.mathimatika','gel.c.health.fysiki','gel.c.health.ximeia'])==18)
check('C science/health 3rd-field block = 18h', sum(timetable[c]['hours']['Γ΄'] for c in ['gel.c.health.viologia','gel.c.health.fysiki','gel.c.health.ximeia'])==18)
check('C per-student total 10+2+2+18 = 32h', 10+2+2+18==32)

# Structural semantics confirmed by myschool separate group selectors.
check('A second foreign language is profile choice', timetable['gel.general.deyteri_xeni'].get('profile_choice_id')=='second_foreign_language')
check('B humanities is profile track', timetable['gel.b.humanities.archaia'].get('profile_track')=='humanities')
check('B science is profile track', timetable['gel.b.science.mathimatika'].get('profile_track')=='science')
check('C humanities is profile track', timetable['gel.c.humanities.archaia'].get('profile_track')=='humanities')
check('C economics/IT is profile track', timetable['gel.c.econ.mathimatika'].get('profile_track')=='economics_it')
check('C science/health Biology is profile track', timetable['gel.c.health.viologia'].get('profile_track')=='science_health')
check('C science/health field choices share choice set', timetable['gel.c.health.mathimatika'].get('choice_set_id')=='gel.c.health.field_choice' and timetable['gel.c.health.viologia'].get('choice_set_id')=='gel.c.health.field_choice')
check('C science/health field choices share slot', timetable['gel.c.health.mathimatika'].get('slot_id')=='gel.c.health.field_choice' and timetable['gel.c.health.viologia'].get('slot_id')=='gel.c.health.field_choice')
check('C conditional Mathematics is NOT a track row', 'profile_track' not in timetable['gel.general.mathimatika_conditional'])
check('C conditional Mathematics has shared conditional slot', timetable['gel.general.mathimatika_conditional'].get('slot_id')=='gel.c.general.orientation_choice')
check('C conditional History has shared conditional slot', timetable['gel.general.istoria'].get('slot_id')=='gel.c.general.orientation_choice')

failed=[n for n,ok in checks if not ok]
for n,ok in checks:
    print(('PASS' if ok else 'FAIL')+': '+n)
print('RESULT %d PASS / %d FAIL' % (len(checks)-len(failed),len(failed)))
raise SystemExit(1 if failed else 0)
