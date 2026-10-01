from pathlib import Path
import subprocess
from lxml import html

ROOT = Path(__file__).resolve().parents[1]
page = (ROOT / 'paidagogiki-eparkeia.php').read_text(encoding='utf-8')
ui = (ROOT / 'includes' / 'pedagogical-competence-ui.js').read_text(encoding='utf-8')
data = (ROOT / 'includes' / 'pedagogical-competence-data.php').read_text(encoding='utf-8')
checks = []

def check(name, condition):
    ok = bool(condition)
    checks.append((name, ok))
    print(('PASS' if ok else 'FAIL') + ': ' + name)

check('proof types are grouped by legal/user-facing route', "'proof_type_groups' => array(" in data and 'Πτυχίο που μπορεί να πιστοποιεί Π.Δ.Ε. εξ ορισμού' in data and 'Πρόσθετο αποδεικτικό / πιστοποιητικό' in data)
check('named Appendix programs are not a top-level proof type', "'appendix_named_program' => 'Ρητά κατονομαζόμενος" not in data)
check('article 99 is nested under AEI certificate subtype', "'article99' => 'Πιστοποιητικό Π.Δ.Ε. του άρθρου 99" in data and "'article99' => 'Πιστοποιητικό Π.Δ.Ε." not in data.split("'aei_certificate_subtypes'",1)[0])
check('professor-school confirmation question removed', 'professorSchoolMatch' not in page and 'professorSchoolMatch' not in ui)
check('professor-school legal accordion remains', 'Τι σημαίνει «καθηγητική σχολή»;' in page)
check('rows 39–52 are nested in AEI certificate flow', "paidagogikiProgramOptions($pedagogicalCompetenceData['appendix_named_programs'], 'special_program')" in page and 'namedSpecialProgramRow' in ui)
check('rows 14–38 stay nested inside MSc/PhD flow', 'Δεν είσαι βέβαιος/η; Έλεγξε παλαιότερο εγκεκριμένο Π.Μ.Σ.' in page and 'namedPostgraduateRow' in ui)
check('domestic route asks evidence before opening old named list', 'domesticEducationEvidence' in page and 'shouldOfferNamedPostgraduate' in ui)
check('clear domestic evidence bypasses named-list search', 'Δεν χρειάζεται να ψάξεις στην αναλυτική λίστα' in ui)

proc = subprocess.run(['php', 'paidagogiki-eparkeia.php'], cwd=ROOT, capture_output=True, text=True)
check('PHP page renders', proc.returncode == 0)
if proc.returncode == 0:
    doc = html.document_fromstring(proc.stdout)
    top_select = doc.get_element_by_id('proofType')
    values = top_select.xpath('.//option/@value')
    check('render does not expose legacy named-program top-level option', 'appendix_named_program' not in values)
    groups = top_select.xpath('./optgroup')
    check('render has grouped proof-type optgroups', len(groups) >= 3)
    check('render has no professor-school match select', not doc.xpath('//*[@data-role="professorSchoolMatch"]'))

failed = [name for name, ok in checks if not ok]
print(f'RESULT: {len(checks)-len(failed)}/{len(checks)} PASS')
raise SystemExit(1 if failed else 0)
