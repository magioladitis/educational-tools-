from pathlib import Path
root=Path(__file__).resolve().parents[1]
page=(root/'kena-sxoleion-kalypsi.php').read_text()
xlsx=(root/'includes/vacancies-xlsx.php').read_text()
checks={
 'xlsx helper required': "includes/vacancies-xlsx.php" in page,
 'export action exists': "$coverageAction === 'export_preview'" in page,
 'export button exists': 'Εξαγωγή Excel (2 φύλλα)' in page,
 'csrf protected post': 'vacanciesCsrfValid' in page and 'coverage_action" value="export_preview"' in page,
 'two sheet builder': 'vacanciesXlsxBuildCoveragePreview' in xlsx,
 'teacher sheet name': 'Εκπαιδευτικοί 4.8' in xlsx,
 'vacancy sheet name': 'Κενά ΔΔΕ' in xlsx,
 'teacher columns': all(x in xlsx for x in ['Α.Μ.','Κύρια ειδικότητα','2η ειδικότητα','Υπόλοιπο ωρών','Σχολεία που υπηρετεί']),
 'vacancy columns': all(x in xlsx for x in ['Κωδικός σχολείου','Περιγραφή ειδικότητας','Ώρες κενού','Εκτίμηση Κενών από myschool','Σημείωση 5.1']),
 'xlsx response content type': 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' in page,
 'export before matcher': page.index("coverageAction === 'export_preview'") < page.index("coverageAction === 'match'"),
}
failed=[]
for label,ok in checks.items():
    print(('PASS' if ok else 'FAIL')+': '+label)
    if not ok: failed.append(label)
if failed: raise SystemExit(1)
print(f'vacancies coverage excel contract: PASS ({len(checks)}/{len(checks)})')
