from pathlib import Path
root = Path(__file__).resolve().parents[1]
php = (root / 'ypologismos-morion-metathesis.php').read_text(encoding='utf-8')
js = (root / 'includes/transfer-ui.js').read_text(encoding='utf-8')
assert "summary' => 'Πού βλέπω τις υπηρετήσεις μου;'" in php
assert "'id' => 'msdRegistryHelp'" in php
assert "'open' => true" not in php.split("'id' => 'msdRegistryHelp'", 1)[1].split(')); ?>', 1)[0]
assert 'Δώστε τον ακριβή αριθμό Μ.Σ.Δ.' in php
assert 'π.χ. 48,75. Οι υπηρετήσεις' in php
assert 'Πληκτρολογήστε τον ακριβή αριθμό Μ.Σ.Δ.' not in php
assert "Συμπληρώστε έναν μη αρνητικό αριθμό με έως δύο δεκαδικά, π.χ. 48,75." not in js
assert "var directInvalid =" in js
assert "aria-invalid" in js
assert "'3.22.125'" in (root / 'includes/config.php').read_text(encoding='utf-8')
print('OK: M.S.D. compact help and conditional validation in 3.22.120')
