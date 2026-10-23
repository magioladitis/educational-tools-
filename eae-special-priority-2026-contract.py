from pathlib import Path
p=Path(__file__).resolve().parent
page=(p/'ypologismos-morion-2ea-2025.php').read_text()
ui=(p/'includes/asep-2ea-ui.js').read_text()
core=(p/'includes/eep-eligibility-calculations.js').read_text()
checks={
'PE23 experience threshold exactly 50': 'Τουλάχιστον 50 μήνες προϋπηρεσίας' in page,
'PE23 title route': 'Διδακτορικό ή μεταπτυχιακό στη Σχολική Ψυχολογία' in page,
'PE23 no specialist route': 'value="none"' in page,
'PE23 priority group 1': 'Ομάδα πρόταξης 1: εξειδίκευση Σχολικής Ψυχολογίας + ΠΔΕ' in ui,
'PE23 priority group 2': 'Ομάδα πρόταξης 2: εξειδίκευση Σχολικής Ψυχολογίας χωρίς ΠΔΕ' in ui,
'PE23 priority group 3': 'Ομάδα πρόταξης 3: χωρίς εξειδίκευση, με ΠΔΕ' in ui,
'PE23 priority group 4': 'Ομάδα 4: χωρίς εξειδίκευση και χωρίς ΠΔΕ' in ui,
'PE23 specialty switch': "b==='ΠΕ23'" in ui,
'PE31 no scoring qualifying 3 years': 'Επαγγελματική εμπειρία τριών ετών που αποτελεί τυπικό προσόν δεν μοριοδοτείται.' in ui,
'PE31 no scoring qualifying masters': "['ΠΕ21','ΠΕ22','ΠΕ31'].includes(b)" in ui,
'PE31 specialized routes': "'ΠΕ31': Object.freeze" in core,
'PE23 license required': "'ΠΕ23': Object.freeze" in core and 'Άδεια άσκησης επαγγέλματος Ψυχολόγου' in core,
}
for k,v in checks.items(): print(('PASS' if v else 'FAIL')+' '+k)
assert all(checks.values())
print(f'{len(checks)} assertions passed')
