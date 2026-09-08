#!/usr/bin/env python3
from pathlib import Path

ROOT=Path(__file__).resolve().parents[1]
PAGE=(ROOT/'ypologismos-didaktikon-anagkon.php').read_text(encoding='utf-8')
checks=[]
def check(name,cond): checks.append((name,bool(cond)))

check('daily gym timetable source is exact', 'Υ.Α. 44257/Δ2/08-04-2026 — ΦΕΚ Β΄ 2132/09-04-2026 · Ημερήσιο Γυμνάσιο ↗' in PAGE)
check('daily GEL timetable source is exact', 'Υ.Α. 43684/Δ2/07-04-2026 — ΦΕΚ Β΄ 2106/09-04-2026 · Ημερήσιο ΓΕΛ ↗' in PAGE)
check('evening gym timetable source is exact', 'Υ.Α. 43751/Δ2/07-04-2026 — ΦΕΚ Β΄ 2106/09-04-2026 · Εσπερινό Γυμνάσιο ↗' in PAGE)
check('evening GEL timetable source is exact', 'Υ.Α. 43706/Δ2/07-04-2026 — ΦΕΚ Β΄ 2102/09-04-2026 · Εσπερινό ΓΕΛ ↗' in PAGE)
check('general assignments source is exact', 'Υ.Α. 54058/Δ2/05-05-2026 — ΦΕΚ Β΄ 2583/07-05-2026 · Αναθέσεις Γυμνασίου / ΓΕΛ ↗' in PAGE)
check('ethics source remains exact', 'Υ.Α. 108070/Δ2/2026 — ΦΕΚ Β΄ 5231/2026 · Ηθική ↗' in PAGE)
check('vague assignments label removed', "'ΥΠΑΙΘΑ — Αναθέσεις Γυμνασίου / ΓΕΛ ↗'" not in PAGE)

failed=[n for n,ok in checks if not ok]
for n,ok in checks: print(('PASS' if ok else 'FAIL')+': '+n)
print('RESULT %d PASS / %d FAIL' % (len(checks)-len(failed),len(failed)))
raise SystemExit(1 if failed else 0)
