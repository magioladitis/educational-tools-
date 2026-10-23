from pathlib import Path
import re
p = Path(__file__).resolve().parents[1] / 'includes' / 'teaching-timetable-crosswalk.php'
s=p.read_text()
a=s.index('function teachingTimetableEneegylRegulatoryGapCourseIds()')
b=s.index('\n}',a)
ids=re.findall(r"'(eneegyl\.lykeio\.c\.[^']+)'",s[a:b])
assert len(ids)==17, (len(ids),ids)
assert len(set(ids))==17
assert 'Η τροποποίηση Β΄ 5733/2026 αφορά άλλες αναθέσεις' in s
assert 'Απαιτείται διευκρίνιση ΥΠΑΙΘΑ' in s
print('PASS 17 ENEEGYL legal gaps guarded')
