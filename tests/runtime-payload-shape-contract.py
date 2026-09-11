#!/usr/bin/env python3
import json, re, subprocess, sys
from pathlib import Path

ROOT = Path(__file__).resolve().parents[1]
passed = failed = 0

def check(label, cond, detail=''):
    global passed, failed
    if cond:
        passed += 1
        print(f'PASS {label}')
    else:
        failed += 1
        print(f'FAIL {label}' + (f' :: {detail}' if detail else ''))

def render(name):
    p = subprocess.run(['php', name], cwd=ROOT, stdout=subprocess.PIPE, stderr=subprocess.PIPE, text=True, timeout=30)
    check(f'{name} renders', p.returncode == 0, p.stderr.strip())
    return p.stdout

def extract_template(html, id_):
    m = re.search(rf'<template\s+id=["\']{re.escape(id_)}["\']>(.*?)</template>', html, re.S)
    check(f'{id_} payload exists', bool(m))
    if not m: return None
    import html as h
    try:
        return json.loads(h.unescape(m.group(1)))
    except Exception as e:
        check(f'{id_} JSON parses', False, str(e)); return None

def extract_script_json(html, id_):
    m = re.search(rf'<script\s+type=["\']application/json["\']\s+id=["\']{re.escape(id_)}["\']>(.*?)</script>', html, re.S)
    check(f'{id_} payload exists', bool(m))
    if not m: return None
    try:
        return json.loads(m.group(1))
    except Exception as e:
        check(f'{id_} JSON parses', False, str(e)); return None

assign = extract_template(render('anatheseis-mathimaton.php'), 'teachingAssignmentsData')
check('teaching assignments payload is array', isinstance(assign, list), type(assign).__name__ if assign is not None else 'none')
check('teaching assignments array non-empty', isinstance(assign, list) and len(assign) > 100)

weekly = extract_template(render('orologio-programma-mathimaton.php'), 'weeklyTimetableData')
check('weekly payload is object', isinstance(weekly, dict), type(weekly).__name__ if weekly is not None else 'none')
check('weekly schools is keyed object', isinstance((weekly or {}).get('schools'), dict))
check('weekly rows is array', isinstance((weekly or {}).get('rows'), list))
check('weekly ethicsPolicy is object', isinstance((weekly or {}).get('ethicsPolicy'), dict))

staff = extract_script_json(render('ypologismos-didaktikon-anagkon.php'), 'staffingRuntimeConfig')
check('staffing runtime payload is object', isinstance(staff, dict), type(staff).__name__ if staff is not None else 'none')
check('allocationPeople is JSON collection', isinstance((staff or {}).get('allocationPeople'), (dict, list)))
check('allocationSlots is JSON collection', isinstance((staff or {}).get('allocationSlots'), (dict, list)))
check('maxBasicSections is numeric', isinstance((staff or {}).get('maxBasicSections'), (int, float)))

# Controller-side guards must match PHP payload shapes.
staff_js = (ROOT/'includes/staffing-simulator-ui.js').read_text(encoding='utf-8')
check('staffing controller accepts allocationPeople object', "typeof staffingRuntimeConfig.allocationPeople==='object'" in staff_js)
check('staffing controller accepts allocationSlots object', "typeof staffingRuntimeConfig.allocationSlots==='object'" in staff_js)
check('staffing controller does not require allocationPeople Array.isArray', 'Array.isArray(staffingRuntimeConfig.allocationPeople)' not in staff_js)
check('staffing controller does not require allocationSlots Array.isArray', 'Array.isArray(staffingRuntimeConfig.allocationSlots)' not in staff_js)

weekly_js = (ROOT/'includes/weekly-timetable-ui.js').read_text(encoding='utf-8')
check('weekly controller treats schools as object', 'var schools = payload.schools || {};' in weekly_js)
check('weekly controller treats rows as array fallback', 'var allRows = payload.rows || [];' in weekly_js)
assign_js = (ROOT/'includes/teaching-assignments-ui.js').read_text(encoding='utf-8')
check('assignments controller parses array fallback', "JSON.parse(dataText || '[]')" in assign_js)

print(f'RESULT {passed} PASS / {failed} FAIL')
sys.exit(1 if failed else 0)
