from pathlib import Path
import re
import subprocess

ROOT = Path(__file__).resolve().parents[1]
checks = 0

def check(name, cond):
    global checks
    if not cond:
        raise AssertionError(name)
    checks += 1

pages = {
    'ypologismos-didaktikou-orariou.php': 'teaching-hours-calculations.js',
    'ypologismos-misthologikou-klimakiou.php': 'salary-scale-calculations.js',
}
for name, module in pages.items():
    text = (ROOT / name).read_text(encoding='utf-8')
    check(f'{name}: exists', bool(text))
    check(f'{name}: no inline style block', '<style' not in text.lower())
    check(f'{name}: shared css', "assets/common.css" in text)
    check(f'{name}: standard calculator body', 'edu-calc-standard' in text)
    check(f'{name}: calculatorHero', 'calculatorHero(' in text)
    check(f'{name}: calculatorColumnsStart', 'calculatorColumnsStart(' in text)
    check(f'{name}: calculatorResultsStart', 'calculatorResultsStart(' in text)
    check(f'{name}: calculatorScoreHeader', 'calculatorScoreHeader(' in text)
    check(f'{name}: calculatorResultRow', 'calculatorResultRow(' in text)
    check(f'{name}: source card', 'sourceCardStart(' in text)
    check(f'{name}: module', module in text)
    ids = re.findall(r'\bid="([^"]+)"', text)
    check(f'{name}: no duplicate literal IDs', len(ids) == len(set(ids)))
    proc = subprocess.run(['php', '-l', str(ROOT / name)], capture_output=True, text=True)
    check(f'{name}: php lint', proc.returncode == 0)

hours = (ROOT / 'includes' / 'teaching-hours-calculations.js').read_text(encoding='utf-8')
check('hours module namespace', 'EducationTeachingHours' in hours)
check('hours primary support', 'function primary(' in hours)
check('hours secondary support', 'function secondary(' in hours)
check('hours lab responsibility support', 'lab_responsible' in hours and 'epal_ek_lab_sector' in hours)
check('hours service 40-year cap', 'Math.min(40, nonNegativeInteger(years))' in hours)
check('hours primary vice director support', 'vice_director' in hours)
page_hours = (ROOT / 'ypologismos-didaktikou-orariou.php').read_text(encoding='utf-8')
check('hours UI years max 40', 'id="serviceYears" type="number" min="0" max="40"' in page_hours)
check('hours UI months max 11', 'id="serviceMonths" type="number" min="0" max="11"' in page_hours)
check('hours UI days max 29', 'id="serviceDays" type="number" min="0" max="29"' in page_hours)
check('hours UI hard-clamps years while typing', "el.id === 'serviceYears'" in page_hours and 'clampBoundedIntegerInput(el, 40)' in page_hours)
check('hours UI hard-clamps months while typing', "el.id === 'serviceMonths'" in page_hours and 'clampBoundedIntegerInput(el, 11)' in page_hours)
check('hours UI hard-clamps days while typing', "el.id === 'serviceDays'" in page_hours and 'clampBoundedIntegerInput(el, 29)' in page_hours)
check('hours EEP option', '<option value="eep">Ειδικό Εκπαιδευτικό Προσωπικό (ΕΕΠ)</option>' in page_hours)
check('hours EEP support wording', '25 ώρες έως και 5 έτη' in page_hours and '21 ώρες πάνω από 20 έτη' in page_hours)
check('hours EBP option', '<option value="ebp">Ειδικό Βοηθητικό Προσωπικό (ΕΒΠ)</option>' in page_hours)
check('hours EBP support wording', 'υποχρεωτικό ωράριο <em>υποστηρικτικού έργου</em>' in page_hours and '30 ώρες' in page_hours)
check('hours EEP/EBP legal source', '66079/Δ3/2018' in page_hours and 'Β΄ 1585' in page_hours)
check('hours 2021 circular source', '141076/Ε3/04-11-2021' in page_hours)
check('hours 2024 labs/library clarification', '132906/Ε3/06-11-2024' in page_hours)
check('hours work-vs-teaching note', '6 ώρες ημερησίως ή 30 ώρες εβδομαδιαίως' in page_hours)
check('hours secondary immediate reduction note', 'από τη συμπλήρωση του απαιτούμενου χρόνου υπηρεσίας' in page_hours)
check('hours library special-case note', 'Σύστημα Δικτύου Σχολικών Βιβλιοθηκών' in page_hours and '<strong>3 ώρες</strong>' in page_hours)
check('hours secondary boundary clarification', 'έως 6 έτη: 23 ώρες' in page_hours and '6 έτη και 1 ημέρα έως 12 έτη: 21 ώρες' in page_hours and '12 έτη και 1 ημέρα' in page_hours and '20 συμπληρωμένα έτη' in page_hours)
check('hours next reduction result row', "'value_id' => 'nextReductionResult'" in page_hours and 'Χρόνος μέχρι την επόμενη μείωση ωραρίου' in page_hours)
check('hours next reduction engine', 'function nextReduction(' in hours and 'nextReductionLabel' in hours and 'Δεν προβλέπεται περαιτέρω μείωση' in hours)

salary = (ROOT / 'includes' / 'salary-scale-calculations.js').read_text(encoding='utf-8')
check('salary module namespace', 'EducationSalaryScale' in salary)
check('salary categories', all(k in salary for k in ['PE:', 'TE:', 'DE:', 'YE:']))
check('salary integrated master', 'integrated' in salary and 'mk: 2' in salary)
check('salary doctorate', 'phd' in salary and 'mk: 6' in salary)
check('salary service 40-year cap', 'Math.min(40, nonNegativeInteger(years))' in salary)
check('salary Greek category labels', all(label in salary for label in ['label: "ΠΕ"', 'label: "ΤΕ"', 'label: "ΔΕ"', 'label: "ΥΕ"']))
page_salary = (ROOT / 'ypologismos-misthologikou-klimakiou.php').read_text(encoding='utf-8')
check('salary UI years max 40', 'id="serviceYears" type="number" min="0" max="40"' in page_salary)
check('salary UI months max 11', 'id="serviceMonths" type="number" min="0" max="11"' in page_salary)
check('salary UI hard-clamps years while typing', "el.id === 'serviceYears'" in page_salary and 'clampBoundedIntegerInput(el, 40)' in page_salary)
check('salary UI hard-clamps months while typing', "el.id === 'serviceMonths'" in page_salary and 'clampBoundedIntegerInput(el, 11)' in page_salary)
check('salary integrated master domestic note', 'Integrated Master αλλοδαπής δεν καλύπτεται' in page_salary and 'ελληνικού Α.Ε.Ι.' in page_salary)
check('salary recognized-promotion wording', 'Ανώτερο προσόν που έχει ήδη αναγνωριστεί για μισθολογική προώθηση από την υπηρεσία σας' in page_salary and 'Χωρίς αναγνωρισμένη προώθηση' in page_salary)
check('salary special cases caution', 'Ειδικές περιπτώσεις:' in page_salary and '<strong>ΤΕ16</strong>' in page_salary and 'Η κατηγορία <strong>ΔΕ</strong> δεν αποκλείεται αυτομάτως' in page_salary)
check('salary no self-certification', 'Μην επιλέγεις +2 ή +6 Μ.Κ.' in page_salary and 'ήδη αναγνωριστεί από την υπηρεσία σου' in page_salary)
check('salary suspended period module', 'function suspendedServiceMonths(' in salary and 'Math.min(24' in salary and 'countableServiceMonths' in salary)
check('salary suspended years UI', 'id="suspendedYears" type="number" min="0" max="2"' in page_salary)
check('salary suspended months UI', 'id="suspendedMonths" type="number" min="0" max="11"' in page_salary)
check('salary suspension legal note', 'Αναστολή μισθολογικής εξέλιξης 2016–2017' in page_salary and '01-01-2016 έως 31-12-2017' in page_salary and 'δεν λαμβάνεται υπόψη για μισθολογική εξέλιξη' in page_salary)
check('salary suspension result rows', 'suspendedServiceResult' in page_salary and 'countableServiceResult' in page_salary)
check('salary suspension UI clamp', 'clampSuspendedInputs' in page_salary and "el.id === 'suspendedYears' || el.id === 'suspendedMonths'" in page_salary)
check('salary suspension GLK source', '2/31029/ΔΕΠ/06-05-2016' in page_salary)

tools = (ROOT / 'ergaleia.php').read_text(encoding='utf-8')
check('toolbox count 27 hero', '27 διαθέσιμα εργαλεία' in tools)
check('toolbox count 27 results', 'Εμφανίζονται 27 εργαλεία.' in tools)
check('toolbox service filter', 'data-filter="ypiresiaka"' in tools)
check('toolbox teaching card', 'href="ypologismos-didaktikou-orariou.php"' in tools)
check('toolbox salary card', 'href="ypologismos-misthologikou-klimakiou.php"' in tools)
check('toolbox card count', len(re.findall(r'class="tool-card"', tools)) == 27)
check('toolbox numbers 25/26/27', 'class="tool-number">25<' in tools and 'class="tool-number">26<' in tools and 'class="tool-number">27<' in tools)

print(f'Service tools R12 contract: PASS {checks}')
