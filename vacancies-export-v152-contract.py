from pathlib import Path
root = Path(__file__).resolve().parents[1]
admin = (root/'kena-sxoleion-admin.php').read_text()
export = (root/'kena-sxoleion-export.php').read_text()
model = (root/'includes/vacancies-model.php').read_text()
xlsx = (root/'includes/vacancies-xlsx.php').read_text()
checks = {
    'admin has export button': 'Εξαγωγή Excel' in admin and 'kena-sxoleion-export.php?round=' in admin,
    'export is admin-only': 'vacanciesIsAdmin()' in export,
    'xlsx content type': 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' in export,
    'legacy specialty rows': "'ΠΕ01','ΠΕ02','ΠΕ03'" in model and "'SKILLS_GYM','TECH_GYM'" in model,
    'missing submission stays blank': 'vacanciesXlsxBlankCell' in xlsx and "!isset($submitted[$schoolId])" in xlsx,
    'vacancy sign negative': "'vacancy' ? -$hours : $hours" in model,
    'summary total row and col': "'ΣΥΝΟΛΟ'" in xlsx and "'ΣΥΝ'" in xlsx,
    'notes sheet': 'Παρατηρήσεις' in xlsx and 'school_note' in xlsx,
    'pure php zip fallback': 'vacanciesXlsxZipBinary' in xlsx,
}
failed = [k for k,v in checks.items() if not v]
for k,v in checks.items(): print(('OK' if v else 'FAIL'), k)
raise SystemExit(1 if failed else 0)
