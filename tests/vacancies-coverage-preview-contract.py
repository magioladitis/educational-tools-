from pathlib import Path
root=Path(__file__).resolve().parents[1]
page=(root/'kena-sxoleion-kalypsi.php').read_text()
model=(root/'includes/vacancies-model.php').read_text()
auth=(root/'includes/vacancies-auth.php').read_text()
css=(root/'assets/vacancies.css').read_text()
checks={
 'load action exists': 'coverage_action" value="load' in page,
 'match action separate': 'coverage_action" value="match' in page,
 'side-by-side preview': 'vacancy-coverage-preview-grid' in page and 'Εκπαιδευτικοί με υπόλοιπο (4.8)' in page and 'Κενά επιλεγμένου γύρου ΔΔΕ' in page,
 'matcher guarded': 'catch (Throwable $e)' in page,
 'session cache': "vacancies_coverage_preview" in page,
 'logout clears preview': "unset($_SESSION['vacancies_coverage_preview'])" in auth,
 'old schema address fallback': "information_schema.COLUMNS" in model and "'' AS school_address" in model,
 'preview responsive css': '.vacancy-coverage-preview-grid' in css,
 'vacancy source selector': 'name="vacancy_source"' in page and 'Κενά γύρου ΔΔΕ' in page and 'Εκτίμηση Κενών από myschool (5.1)' in page,
 'reconciliation summary': 'Συμφωνία Κενών ΔΔΕ ↔ 5.1' in page and 'myschool_deficit_hours' in page,
}
failed=[]
for label,ok in checks.items():
    print(('PASS' if ok else 'FAIL')+': '+label)
    if not ok: failed.append(label)
if failed: raise SystemExit(1)
print(f'vacancies coverage preview contract: PASS ({len(checks)}/{len(checks)})')
