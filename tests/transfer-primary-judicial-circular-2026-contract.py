from pathlib import Path
p=Path(__file__).resolve().parents[1]
pe=(p/'includes/transfer-primary.js').read_text()
de=(p/'includes/transfer-documents.js').read_text()
assert 'Μέσω mydocs: πιστοποιητικό οικογενειακής κατάστασης, βεβαίωση εργασίας' in pe
assert 'πιστοποιητικό οικογενειακής κατάστασης, βεβαίωση εργασίας και έγγραφο περιφέρειας' in pe
assert 'Πιστοποιητικό οικογενειακής κατάστασης αυτεπάγγελτα.' not in pe
assert 'Το πιστοποιητικό οικογενειακής κατάστασης αναζητείται αυτεπάγγελτα.' not in pe
assert 'Εγκύκλιος Π.Ε. §2.2.2.' in pe
assert 'Εγκύκλιος Π.Ε. §2.2.3.' in pe
assert '§2.2.4' not in pe
assert 'πιστοποιητικό οικογενειακής κατάστασης, βεβαίωση εργασίας' in de
assert '138014/Ε2, Μέρος Πρώτο, Κεφ. Α, §2.2.3.' in de
print('Primary/secondary judicial spouse legal split: PASS (8 assertions)')
