from pathlib import Path
root = Path(__file__).resolve().parents[1]
p = root / 'ypologismos-misthologikou-klimakiou.php'
s = p.read_text(encoding='utf-8')
ui = (root / 'includes' / 'salary-ui.js').read_text(encoding='utf-8')
app = s + '\n' + ui
checks = {
    'field exists': 'id="disabilityTaxTreatment"' in s,
    'none option': 'value="none">Χωρίς ειδική ρύθμιση' in s,
    '67 option': 'value="disability67_79"' in s and '67%–79,99%' in s,
    '80 option': 'value="disability80plus"' in s and 'Αναπηρία ≥80%' in s,
    'info panel': "'id' => 'disabilityTaxInfoPanel'" in s,
    '200 euro explanation': 'μείωση φόρου έως 200 € τον χρόνο' in s,
    '80 exemption explanation': 'μηνιαία παρακράτηση φόρου γίνεται 0 €' in s,
    'result row': "value_id' => 'disabilityTaxReliefResult'" in s,
    'calculator wiring': "disabilityTaxTreatment: byId('disabilityTaxTreatment').value" in app,
    'result wiring': "byId('disabilityTaxReliefResult').textContent" in app,
    'reset wiring': "byId('disabilityTaxTreatment').value = 'none';" in app,
    'print parameter': 'Αναπηρία — φορολογία:' in app,
    'print tax relief': 'Μείωση / απαλλαγή λόγω αναπηρίας:' in app,
    'AADE source': 'Φορολογικά δικαιώματα ΑμεΑ' in s,
    'insurance unaffected': 'δεν μηδενίζει τις ασφαλιστικές ή λοιπές κρατήσεις' in s,
}
failed=[name for name, ok in checks.items() if not ok]
if failed:
    raise SystemExit('FAIL: ' + ', '.join(failed))
print(f"Salary disability tax UI contract: PASS {len(checks)}/{len(checks)}")
