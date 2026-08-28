from pathlib import Path

ROOT = Path(__file__).resolve().parents[1]
failures = []

def text(path):
    return (ROOT / path).read_text(encoding='utf-8')

def check(name, condition):
    if condition:
        print('PASS', name)
    else:
        print('FAIL', name)
        failures.append(name)

core = text('includes/education-core.js')
check('shared service cap is 50 years', 'var MAX_SERVICE_YEARS = 50;' in core)
check('shared month cap derives from years', 'var MAX_SERVICE_MONTHS = MAX_SERVICE_YEARS * 12;' in core)

pages_50 = {
    'ypologismos-didaktikou-orariou.php': ['id="serviceYears" type="number" min="0" max="50"'],
    'ypologismos-misthologikou-klimakiou.php': ['id="serviceYears" type="number" min="0" max="50"'],
    'ypologismos-morion-metathesis.php': ['id="serviceYears" type="number" min="0" max="50"', 'class="msd-years" type="number" min="0" max="50"'],
    'ypologismos-morion-apospasis.php': ['id="serviceYears" min="0" max="50"'],
    'ypologismos-morion-apospasis-exoteriko.php': ['id="educationYears" min="0" max="50"', 'id="teachingYears" min="0" max="50"'],
    'ypologismos-morion-apospasis-evropaika-scholeia.php': ['id="teachingYears" aria-label="Έτη συνολικής αναγνωρισμένης διδακτικής υπηρεσίας" min="0" max="50"'],
    'ypologismos-morion-apospasis-sde.php': ['id="eligibilitySchoolYears" min="0" max="50"', 'id="formalEducationYears" min="0" max="50"', 'id="sdeYears" min="0" max="50"'],
    'ypologismos-morion-diefthynton-ypodiefthynton-sde.php': ['id="educationalServiceYears" min="0" max="50"', 'id="otherAdminYears" min="0" max="50"'],
    'ypologismos-morion-sivitanidios-saek.php': ['id="tertiaryTeachingYears" type="number" min="0" max="50"', 'id="primarySecondaryTeachingYears" type="number" min="0" max="50"'],
}
for path, needles in pages_50.items():
    body = text(path)
    for needle in needles:
        check(f'{path}: {needle}', needle in body)

month_pages = [
    ('ypologismos-morion.php', 'id="privateMonths" data-service-role="private" min="0" max="600"'),
    ('ypologismos-morion-3ea-2025.php', 'id="eaeMonths" class="service-months" data-eae-aux="months" type="number" min="0" max="600"'),
    ('ypologismos-morion-3ea-2025.php', 'id="privateMonths" class="service-months" data-service-role="private" type="number" min="0" max="600"'),
    ('ypologismos-morion-4ea-2025.php', 'id="eaeMonths" data-eae-aux="months" min="0" max="600"'),
    ('ypologismos-morion-4ea-2025.php', 'id="privateMonths" data-service-role="private" min="0" max="600"'),
    ('ypologismos-morion-5ea-2022.php', 'id="eaeMonths" data-eae-aux="months" min="0" max="600"'),
    ('ypologismos-morion-mitroo-sde.php', 'id="expSdeMonths" type="number" min="0" max="600"'),
    ('ypologismos-morion-mitroo-sde.php', 'id="expAdultCounsellingMonths" type="number" min="0" max="600"'),
]
for path, needle in month_pages:
    check(f'{path}: 600-month cap', needle in text(path))

check('private service engine uses 600 months', 'privateMaxMonths: 600,' in text('includes/service-calculations.js'))
check('transfer duration engine uses 50 years', 'integer(years, 0, 50)' in text('includes/transfer-calculations.js'))
check('SDE leadership engine uses 50 years', 'const MAX_EXPERIENCE_YEARS = 50;' in text('includes/sde-leadership-calculations.js'))
check('digital tutoring extra service allows 48 years beyond required 2', 'id="extraYears" min="0" max="48"' in text('ypologismos-morion-apospasis-psifiako-frontistirio.php'))

# Old arbitrary 40-year / 480-month service caps must not return.
legacy_needles = ['max="40"', 'max="480"', 'Math.min(40, nonNegativeInteger(years))', 'integer(years, 0, 60)', 'MAX_EXPERIENCE_YEARS = 40', 'privateMaxMonths: 480']
for path in list(ROOT.glob('ypologismos-*.php')) + list((ROOT/'includes').glob('*.js')):
    body = path.read_text(encoding='utf-8')
    for needle in legacy_needles:
        if needle in body:
            failures.append(f'legacy cap {needle} in {path.name}')
            print('FAIL', f'legacy cap {needle} in {path.name}')

if failures:
    raise SystemExit(1)
print('ALL 50-YEAR CAP CONTRACTS PASS')
