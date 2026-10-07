from pathlib import Path
root=Path(__file__).resolve().parents[1]
page=(root/'kena-sxoleion-kalypsi.php').read_text(encoding='utf-8')
admin=(root/'kena-sxoleion-admin.php').read_text(encoding='utf-8')
engine=(root/'includes/vacancies-coverage-engine.php').read_text(encoding='utf-8')
checks={
 'admin auth reused': 'vacanciesIsAdmin()' in page and "vacancies-auth.php" in page,
 'csrf upload protected': 'vacanciesCsrfValid' in page and 'vacanciesCsrfToken' in page,
 '4.8 + 5.1 required': 'name="stat48"' in page and 'name="stat51"' in page,
 'no persistence claim': 'δεν αποθηκεύονται' in page and 'Δεν γράφει στη βάση' in page,
 'aggregate 5.1 warning': 'δεν περιέχει κωδικό/ονομασία σχολικής μονάδας' in page,
 'admin navigation': 'kena-sxoleion-kalypsi.php' in admin,
 'secondary specialty support': 'secondary_code' in engine,
 'signed sum remaining': "['remaining_hours'] += $remaining" in engine,
 'A/B evidence': "['A']" in engine and "['B']" in engine,
 'three decision views': all(x in page for x in ['Ανά εκπαιδευτικό','Ανά σχολείο','Ανά ακάλυπτο κενό']),
 'movement-aware ranking': 'vacanciesCoverageDynamicCandidateScore' in engine and 'continued_destination' in engine,
 'geography not overstated': 'Δεν χρησιμοποιείται ακόμη πραγματική γεωγραφική απόσταση' in page,
}
failed=[k for k,v in checks.items() if not v]
for k,v in checks.items(): print(('PASS' if v else 'FAIL')+': '+k)
if failed: raise SystemExit(1)
print('vacancies coverage page contract: PASS')
