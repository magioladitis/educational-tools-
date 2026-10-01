<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/teacher-specialties.php';
$pedagogicalCompetenceData = require __DIR__ . '/includes/pedagogical-competence-data.php';
$paidagogikiSpecialties = array(
    'ΠΕ01', 'ΠΕ02', 'ΠΕ03', 'ΠΕ04', 'ΠΕ05', 'ΠΕ06', 'ΠΕ07', 'ΠΕ08', 'ΠΕ11',
    'ΠΕ33', 'ΠΕ34', 'ΠΕ40', 'ΠΕ41', 'ΠΕ60', 'ΠΕ70', 'ΠΕ73', 'ΠΕ78', 'ΠΕ79.01',
    'ΠΕ79.02', 'ΠΕ80', 'ΠΕ81', 'ΠΕ82', 'ΠΕ83', 'ΠΕ84', 'ΠΕ85', 'ΠΕ86', 'ΠΕ87',
    'ΠΕ88', 'ΠΕ89', 'ΠΕ90', 'ΠΕ91'
);

if (!function_exists('paidagogikiProofTypeOptions')) {
    function paidagogikiProofTypeOptions($data) {
        foreach ($data['proof_type_groups'] as $group) {
            echo '<optgroup label="' . htmlspecialchars($group['label'], ENT_QUOTES, 'UTF-8') . '">';
            foreach ($group['items'] as $value) {
                if (!isset($data['proof_types'][$value])) {
                    continue;
                }
                echo '<option value="' . htmlspecialchars($value, ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars($data['proof_types'][$value], ENT_QUOTES, 'UTF-8') . '</option>';
            }
            echo '</optgroup>';
        }
    }
}

if (!function_exists('paidagogikiProgramOptions')) {
    function paidagogikiProgramOptions($programs, $section) {
        foreach ($programs as $row => $program) {
            if ((isset($program['section']) ? $program['section'] : '') !== $section) {
                continue;
            }
            $label = $program['title'] . ' — ' . $program['provider'];
            echo '<option value="' . (int)$row . '">' . htmlspecialchars($label, ENT_QUOTES, 'UTF-8') . '</option>';
        }
        echo '<option value="unknown">Δεν βρίσκω / δεν γνωρίζω τον ακριβή τίτλο</option>';
    }
}

if (!function_exists('paidagogikiProfessorSchoolHelp')) {
    function paidagogikiProfessorSchoolHelp() {
        echo '<details class="ped-professor-school-help">';
        echo '<summary>Τι σημαίνει «καθηγητική σχολή»;</summary>';
        echo '<div class="ped-professor-school-help__body">';
        echo '<p><strong>Ο ν. 3194/2003, άρθρο 8 παρ. 2, δίνει συγκεκριμένο νομικό ορισμό:</strong> ως καθηγητικές σχολές νοούνται τα πανεπιστημιακά Τμήματα των οποίων οι πτυχιούχοι μπορούν να διοριστούν στην εκπαίδευση χωρίς να απαιτείται πρόσθετο πτυχίο ή πιστοποιητικό παιδαγωγικής κατάρτισης.</p>';
        echo '<p>Άρα <strong>δεν αρκεί ότι ένα πτυχίο οδηγεί σε εκπαιδευτικό κλάδο</strong>. Ελέγχεται το συγκεκριμένο Τμήμα και το συγκεκριμένο πτυχίο. Η αρχική απαρίθμηση του νόμου έχει τροποποιηθεί και συμπληρωθεί μεταγενέστερα, γι’ αυτό πρέπει να ελέγχονται και οι όροι της διαδικασίας που σε αφορά.</p>';
        echo '<p class="field-help">Με την επιλογή «Πτυχίο καθηγητικής σχολής» δηλώνεις ήδη ότι το συγκεκριμένο πτυχίο σου ανήκει σε αυτή την κατηγορία. Αν δεν είσαι βέβαιος/η, επίλεξε «Δεν είμαι σίγουρος/η ποια κατηγορία ταιριάζει».</p>';
        echo '</div>';
        echo '</details>';
    }
}

if (!function_exists('paidagogikiNamedPostgraduateHelp')) {
    function paidagogikiNamedPostgraduateHelp($data) {
        echo '<details class="ped-reference-help">';
        echo '<summary>Δεν είσαι βέβαιος/η; Έλεγξε παλαιότερο εγκεκριμένο Π.Μ.Σ.</summary>';
        echo '<div class="ped-professor-school-help__body">';
        echo '<p>Άνοιξε αυτή τη λίστα <strong>μόνο αν δεν μπορείς ήδη να τεκμηριώσεις</strong> ότι ο τίτλος σου είναι στις επιστήμες της αγωγής. Η ονομαστική βάση χρησιμοποιείται ως εναλλακτικός έλεγχος παλαιότερων εγκεκριμένων Π.Μ.Σ., όχι ως υποχρεωτικό βήμα για όλους.</p>';
        echo '<label>Αναζήτησε πρώτα με τίτλο ή φορέα</label>';
        echo '<input type="search" data-role="namedPostgraduateSearch" class="ped-program-search" autocomplete="off" placeholder="π.χ. Ειδική Αγωγή, ΕΚΠΑ, Αιγαίου">';
        echo '<label>Επίλεξε μόνο αν βρεις ακριβή αντιστοίχιση</label>';
        echo '<select data-role="namedPostgraduateRow"><option value="">-- Επιλογή τίτλου --</option>';
        paidagogikiProgramOptions($data['appendix_named_programs'], 'postgraduate_prior');
        echo '</select>';
        echo '<div class="appendix-program-details hidden" data-role="namedPostgraduateDetails" aria-live="polite"></div>';
        echo '<div class="question hidden" data-subsection="namedPostgraduateExactMatch">';
        echo '<label>Ο τίτλος και ο φορέας του δικού σου μεταπτυχιακού ταυτίζονται ακριβώς με την επιλεγμένη εγγραφή;</label>';
        echo '<select data-role="namedPostgraduateExactMatch"><option value="">-- Επιλογή --</option><option value="yes">Ναι — έχω ελέγξει ακριβή τίτλο και φορέα</option><option value="no">Όχι — υπάρχει διαφορά</option><option value="unknown">Δεν είμαι σίγουρος/η</option></select>';
        echo '</div>';
        echo '</div>';
        echo '</details>';
    }
}
?>
<!DOCTYPE html>
<html lang="el">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <?php require __DIR__ . '/includes/head-pwa.php'; ?>
  <title>Έχω Παιδαγωγική και Διδακτική Επάρκεια;</title>
<link rel="stylesheet" href="<?php echo htmlspecialchars(edu_asset_url('assets/common.css'), ENT_QUOTES, 'UTF-8'); ?>">
</head>

<body class="edu-ui edu-guide-standard edu-guide-pedagogy">
<?php require_once __DIR__ . '/includes/header.php'; ?>
<?php require_once __DIR__ . '/includes/components/deadline-card.php'; ?>

<div class="app-box edu-modernized">
<section class="hero edu-legacy-hero">
<h1>Έχω Παιδαγωγική και Διδακτική Επάρκεια;</h1>
<p class="intro">
    Το εργαλείο παρέχει <strong>ενδεικτικό</strong> έλεγχο των πτυχίων και αποδεικτικών
    που μπορούν να θεμελιώνουν Παιδαγωγική και Διδακτική Επάρκεια.
  </p>
</section>

<?php
renderDeadlineCard(array(
    'title' => '📅 Αιτήσεις ΕΠΠΑΙΚ ΑΣΠΑΙΤΕ 2026–2027',
    'intro' => 'Η πρόσκληση αφορούσε αιτήσεις συμμετοχής στην κλήρωση για το Ετήσιο Πρόγραμμα Παιδαγωγικής Κατάρτισης (ΕΠΠΑΙΚ) της ΑΣΠΑΙΤΕ.',
    'items' => array(array(
        'title' => 'ΕΠΠΑΙΚ 2026–2027',
        'meta_html' => 'Ηλεκτρονικές αιτήσεις έως <strong>Τρίτη 25 Αυγούστου 2026, ώρα 19:00</strong>.',
        'start' => '2026-06-16T12:00:00+03:00',
        'end' => '2026-08-25T19:00:00+03:00',
        'source_url' => 'https://www.aspete.gr/wp-content/uploads/2026/06/6%CE%A7%CE%9B546%CE%A88%CE%A7%CE%99-3%CE%9E%CE%92-%CE%A0%CE%A1%CE%9F%CE%A3%CE%9A%CE%9B%CE%97%CE%A3%CE%97-%CE%95%CE%A0%CE%A0%CE%91%CE%99%CE%9A-2026-2027.pdf',
        'source_label' => 'Επίσημη πρόσκληση ΑΣΠΑΙΤΕ — ΑΔΑ 6ΧΛ546Ψ8ΧΙ-3ΞΒ ↗'
    )),
    'note_html' => 'Η κάρτα ενημερώνει για την <strong>προθεσμία αίτησης στο ΕΠΠΑΙΚ</strong>. Για το αν και με ποιο αποδεικτικό θεμελιώνεται Παιδαγωγική και Διδακτική Επάρκεια σε συγκεκριμένη διαδικασία, εφαρμόζονται οι κανόνες της αντίστοιχης προκήρυξης.'
));
?>

  <div class="question">
    <label for="specialty">Κλάδος / ειδικότητα για τον οποίο κάνεις τον έλεγχο</label>
    <select id="specialty">
      <option value="">-- Επιλογή --</option>
      <?php foreach ($paidagogikiSpecialties as $code): ?>
        <option value="<?php echo htmlspecialchars($code, ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars(teacherSpecialtyDisplay($code), ENT_QUOTES, 'UTF-8'); ?></option>
      <?php endforeach; ?>
    </select>
    <p class="field-help">Ο κλάδος χρησιμοποιείται ως πλαίσιο ελέγχου. <strong>Δεν ακυρώνει δεύτερο πτυχίο ή άλλο ανεξάρτητο αποδεικτικό Π.Δ.Ε.</strong></p>
  </div>

  <section class="ped-credentials" aria-labelledby="credentialsTitle">
    <div class="ped-credentials-head">
      <div>
        <h2 id="credentialsTitle">Πτυχία και αποδεικτικά Π.Δ.Ε.</h2>
        <p id="credentialsHelp">Δήλωσε κάθε τίτλο ή αποδεικτικό χωριστά. Οι επιλογές είναι οργανωμένες σε <strong>πτυχία που μπορεί να δίνουν Π.Δ.Ε. εξ ορισμού</strong> και σε <strong>πρόσθετα αποδεικτικά</strong>. Αν διαθέτεις περισσότερα από ένα, το εργαλείο ελέγχει όλες τις ανεξάρτητες διαδρομές. Μπορείς να καταχωρίσεις έως <strong>6</strong> αποδεικτικά.</p>
      </div>
      <button id="addCredentialBtn" class="ped-secondary-action" type="button" aria-describedby="credentialsHelp">+ Προσθήκη άλλου τίτλου / αποδεικτικού</button>
    </div>

    <div id="credentialsList">
      <article class="ped-credential" data-credential-index="0">
        <div class="ped-credential-heading">
          <h3>Αποδεικτικό 1</h3>
        </div>

        <div class="question">
          <label for="proofType">Ποιο πτυχίο ή αποδεικτικό θέλεις να ελέγξεις;</label>
          <select id="proofType" data-role="proofType">
            <option value="">-- Επιλογή --</option>
            <?php paidagogikiProofTypeOptions($pedagogicalCompetenceData); ?>
          </select>
        </div>

        <div id="aeiCertificateQuestions" class="hidden" data-section="aei_certificate">
          <div class="question">
            <label for="aeiCertificateSubtype">Τι είδους βεβαίωση / πιστοποιητικό Α.Ε.Ι. διαθέτεις;</label>
            <select id="aeiCertificateSubtype" data-role="aeiCertificateSubtype">
              <option value="">-- Επιλογή --</option>
              <?php foreach ($pedagogicalCompetenceData['aei_certificate_subtypes'] as $value => $label): ?>
                <option value="<?php echo htmlspecialchars($value, ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($label, ENT_QUOTES, 'UTF-8'); ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <div id="aeiStandardCertificateQuestions" class="hidden" data-subsection="aeiStandardCertificate">
            <div class="question">
              <label for="aeiEntryPeriod">Πότε εισήχθηκες στο συγκεκριμένο Τμήμα / Σχολή;</label>
              <select id="aeiEntryPeriod" data-role="aeiEntryPeriod">
                <option value="">-- Επιλογή --</option>
                <option value="up_to_2026">Έως και το ακαδημαϊκό έτος 2026-2027</option>
                <option value="from_2027">Από το ακαδημαϊκό έτος 2027-2028 και μετά</option>
                <option value="unknown">Δεν είμαι σίγουρος/η</option>
              </select>
              <p class="field-help">Η διαθέσιμη βάση 1ΓΕ/2026–2ΓΕ/2026 περιλαμβάνει μεταβατική προϋπόθεση με όριο την εισαγωγή έως το 2026-2027.</p>
            </div>
            <div class="question hidden" data-subsection="aeiCertifiedAtEntry">
              <label for="aeiCertifiedAtEntry">Κατά τον χρόνο εισαγωγής σου, το Τμήμα / η Σχολή χορηγούσε αυτή την πιστοποίηση Π.Δ.Ε.;</label>
              <select id="aeiCertifiedAtEntry" data-role="aeiCertifiedAtEntry">
                <option value="">-- Επιλογή --</option>
                <option value="yes">Ναι</option>
                <option value="no">Όχι</option>
                <option value="unknown">Δεν είμαι σίγουρος/η</option>
              </select>
            </div>
            <div class="ped-flow-guidance hidden" data-role="aeiStandardGuidance" aria-live="polite"></div>
          </div>

          <div id="aeiNamedSpecialProgramQuestions" class="hidden" data-subsection="aeiNamedSpecialProgram">
            <div class="ped-flow-guidance is-info">Χρησιμοποίησε την αναλυτική λίστα μόνο αν το δικαιολογητικό σου προέρχεται από παλαιότερο ειδικό / προπτυχιακό πρόγραμμα Π.Δ.Ε. Αν έχεις πιστοποιητικό του άρθρου 99, επίλεξέ το στην προηγούμενη ερώτηση.</div>
            <div class="question">
              <label for="namedSpecialProgramSearch">Αναζήτησε πρώτα με τίτλο ή φορέα</label>
              <input id="namedSpecialProgramSearch" type="search" data-role="namedSpecialProgramSearch" class="ped-program-search" autocomplete="off" placeholder="π.χ. ΟΠΑ, Πανεπιστήμιο Κρήτης, ΔΠΘ">
              <label for="namedSpecialProgramRow">Επίλεξε τον ακριβή τίτλο / πρόγραμμα</label>
              <select id="namedSpecialProgramRow" data-role="namedSpecialProgramRow">
                <option value="">-- Επιλογή τίτλου --</option>
                <?php paidagogikiProgramOptions($pedagogicalCompetenceData['appendix_named_programs'], 'special_program'); ?>
              </select>
              <p class="field-help">Επίλεξε με βάση <strong>και τον τίτλο και τον φορέα</strong>. Αν δεν υπάρχει ακριβής αντιστοίχιση, μην επιλέξεις παρόμοιο πρόγραμμα.</p>
            </div>
            <div class="appendix-program-details hidden" data-role="namedSpecialProgramDetails" aria-live="polite"></div>
            <div class="question hidden" data-subsection="namedSpecialProgramExactMatch">
              <label for="namedSpecialProgramExactMatch">Ο τίτλος και ο φορέας του δικού σου δικαιολογητικού ταυτίζονται ακριβώς με την επιλεγμένη εγγραφή;</label>
              <select id="namedSpecialProgramExactMatch" data-role="namedSpecialProgramExactMatch">
                <option value="">-- Επιλογή --</option>
                <option value="yes">Ναι — έχω ελέγξει ακριβή τίτλο και φορέα</option>
                <option value="no">Όχι — υπάρχει διαφορά</option>
                <option value="unknown">Δεν είμαι σίγουρος/η</option>
              </select>
            </div>
          </div>
        </div>

        <div id="educationDegreeQuestions" class="hidden" data-section="education_msc_phd">
          <div class="question">
            <label for="educationDegreeOrigin">Ο τίτλος είναι ημεδαπής ή αλλοδαπής;</label>
            <select id="educationDegreeOrigin" data-role="educationDegreeOrigin">
              <option value="">-- Επιλογή --</option>
              <option value="domestic">Ημεδαπής</option>
              <option value="foreign">Αλλοδαπής</option>
              <option value="unknown">Δεν είμαι σίγουρος/η</option>
            </select>
          </div>
          <div class="question hidden" data-subsection="domesticEducation">
            <label for="domesticEducationEvidence">Μπορείς ήδη να τεκμηριώσεις ότι ο τίτλος είναι στις επιστήμες της αγωγής;</label>
            <select id="domesticEducationEvidence" data-role="domesticEducationEvidence">
              <option value="">-- Επιλογή --</option>
              <option value="yes">Ναι — προκύπτει από τον τίτλο / τη σχετική βεβαίωση</option>
              <option value="no">Όχι — γνωρίζω ότι δεν ανήκει στις επιστήμες της αγωγής</option>
              <option value="unknown">Δεν είμαι σίγουρος/η</option>
            </select>
          </div>
          <div id="foreignEducationQuestions" class="question hidden" data-subsection="foreignEducation">
            <label for="foreignEducationEvidence">Υπάρχει το απαιτούμενο αποδεικτικό/αναγνώριση ότι ο τίτλος εμπίπτει στις επιστήμες της αγωγής;</label>
            <select id="foreignEducationEvidence" data-role="foreignEducationEvidence">
              <option value="">-- Επιλογή --</option>
              <option value="yes">Ναι</option>
              <option value="no">Όχι</option>
              <option value="unknown">Δεν είμαι σίγουρος/η</option>
            </select>
          </div>
          <div class="ped-flow-guidance hidden" data-role="educationDegreeGuidance" aria-live="polite"></div>
          <div class="hidden" data-subsection="namedPostgraduateReference">
            <?php paidagogikiNamedPostgraduateHelp($pedagogicalCompetenceData); ?>
          </div>
        </div>

        <div id="pedagogicalDepartmentQuestions" class="hidden" data-section="pedagogical_department">
          <div class="question">
            <label for="pedagogicalDepartmentType">Σε ποια κατηγορία Παιδαγωγικού Τμήματος ανήκει ο τίτλος σου;</label>
            <select id="pedagogicalDepartmentType" data-role="pedagogicalDepartmentType">
              <option value="">-- Επιλογή --</option>
              <?php foreach ($pedagogicalCompetenceData['pedagogical_departments'] as $value => $label): ?>
                <option value="<?php echo htmlspecialchars($value, ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($label, ENT_QUOTES, 'UTF-8'); ?></option>
              <?php endforeach; ?>
              <option value="unknown">Δεν είμαι σίγουρος/η</option>
            </select>
          </div>
        </div>

        <div id="epathQuestions" class="hidden" data-section="epath">
          <div class="question">
            <label for="epathDate">Η ημερομηνία κτήσης του πτυχίου Ε.Π.Α.Θ. είναι προγενέστερη της 12ης Ιουνίου 2018;</label>
            <select id="epathDate" data-role="epathDate">
              <option value="">-- Επιλογή --</option>
              <option value="before">Ναι, είναι πριν από 12/6/2018</option>
              <option value="after">Όχι, είναι από 12/6/2018 και μετά</option>
              <option value="unknown">Δεν είμαι σίγουρος/η</option>
            </select>
          </div>
          <div class="ped-flow-guidance hidden" data-role="epathGuidance" aria-live="polite"></div>
        </div>

        <div id="professorSchoolQuestions" class="hidden" data-section="professor_school">
          <?php paidagogikiProfessorSchoolHelp(); ?>
          <div class="question">
            <label for="entryYear">Έτος εισαγωγής στο συγκεκριμένο Τμήμα</label>
            <select id="entryYear" data-role="entryYear">
              <option value="">-- Επιλογή --</option>
              <option value="up_to_2014">Μέχρι και το ακαδημαϊκό έτος 2014-2015</option>
              <option value="from_2015">Από το ακαδημαϊκό έτος 2015-2016 και μετά</option>
              <option value="unknown">Δεν είμαι σίγουρος/η</option>
            </select>
          </div>
          <div class="question hidden" data-subsection="professorGraduation">
            <label for="graduationYear">Έτος κτήσης του συγκεκριμένου πτυχίου</label>
            <select id="graduationYear" data-role="graduationYear">
              <option value="">-- Επιλογή --</option>
              <option value="up_to_2017">Έως και το ακαδημαϊκό έτος 2017-2018</option>
              <option value="from_2018">Από το ακαδημαϊκό έτος 2018-2019 και μετά</option>
              <option value="unknown">Δεν είμαι σίγουρος/η</option>
            </select>
          </div>
          <div class="ped-flow-guidance hidden" data-role="professorSchoolGuidance" aria-live="polite"></div>
        </div>
      </article>
    </div>
  </section>

  <template id="credentialTemplate">
    <article class="ped-credential" data-credential-index="__INDEX__">
      <div class="ped-credential-heading">
        <h3>Αποδεικτικό __NUMBER__</h3>
        <button type="button" class="ped-remove-credential" data-action="removeCredential" title="Αφαίρεση αποδεικτικού" aria-label="Αφαίρεση αποδεικτικού"><svg viewBox="0 0 24 24" width="18" height="18" aria-hidden="true" focusable="false"><path d="M3 6h18"/><path d="M8 6V4h8v2"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v5"/><path d="M14 11v5"/></svg></button>
      </div>
      <div class="question">
        <label>Ποιο πτυχίο ή αποδεικτικό θέλεις να ελέγξεις;</label>
        <select data-role="proofType">
          <option value="">-- Επιλογή --</option>
          <?php paidagogikiProofTypeOptions($pedagogicalCompetenceData); ?>
        </select>
      </div>
      <div class="hidden" data-section="aei_certificate">
        <div class="question"><label>Τι είδους βεβαίωση / πιστοποιητικό Α.Ε.Ι. διαθέτεις;</label><select data-role="aeiCertificateSubtype"><option value="">-- Επιλογή --</option><?php foreach ($pedagogicalCompetenceData['aei_certificate_subtypes'] as $value => $label): ?><option value="<?php echo htmlspecialchars($value, ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($label, ENT_QUOTES, 'UTF-8'); ?></option><?php endforeach; ?></select></div>
        <div class="hidden" data-subsection="aeiStandardCertificate"><div class="question"><label>Πότε εισήχθηκες στο συγκεκριμένο Τμήμα / Σχολή;</label><select data-role="aeiEntryPeriod"><option value="">-- Επιλογή --</option><option value="up_to_2026">Έως και το ακαδημαϊκό έτος 2026-2027</option><option value="from_2027">Από το ακαδημαϊκό έτος 2027-2028 και μετά</option><option value="unknown">Δεν είμαι σίγουρος/η</option></select><p class="field-help">Η διαθέσιμη βάση 1ΓΕ/2026–2ΓΕ/2026 περιλαμβάνει μεταβατική προϋπόθεση με όριο την εισαγωγή έως το 2026-2027.</p></div><div class="question hidden" data-subsection="aeiCertifiedAtEntry"><label>Κατά τον χρόνο εισαγωγής σου, το Τμήμα / η Σχολή χορηγούσε αυτή την πιστοποίηση Π.Δ.Ε.;</label><select data-role="aeiCertifiedAtEntry"><option value="">-- Επιλογή --</option><option value="yes">Ναι</option><option value="no">Όχι</option><option value="unknown">Δεν είμαι σίγουρος/η</option></select></div><div class="ped-flow-guidance hidden" data-role="aeiStandardGuidance" aria-live="polite"></div></div>
        <div class="hidden" data-subsection="aeiNamedSpecialProgram"><div class="ped-flow-guidance is-info">Χρησιμοποίησε την αναλυτική λίστα μόνο αν το δικαιολογητικό σου προέρχεται από παλαιότερο ειδικό / προπτυχιακό πρόγραμμα Π.Δ.Ε. Αν έχεις πιστοποιητικό του άρθρου 99, επίλεξέ το στην προηγούμενη ερώτηση.</div><div class="question"><label>Αναζήτησε πρώτα με τίτλο ή φορέα</label><input type="search" data-role="namedSpecialProgramSearch" class="ped-program-search" autocomplete="off" placeholder="π.χ. ΟΠΑ, Πανεπιστήμιο Κρήτης, ΔΠΘ"><label>Επίλεξε τον ακριβή τίτλο / πρόγραμμα</label><select data-role="namedSpecialProgramRow"><option value="">-- Επιλογή τίτλου --</option><?php paidagogikiProgramOptions($pedagogicalCompetenceData['appendix_named_programs'], 'special_program'); ?></select><p class="field-help">Επίλεξε με βάση και τον τίτλο και τον φορέα. Αν δεν υπάρχει ακριβής αντιστοίχιση, μην επιλέξεις παρόμοιο πρόγραμμα.</p></div><div class="appendix-program-details hidden" data-role="namedSpecialProgramDetails" aria-live="polite"></div><div class="question hidden" data-subsection="namedSpecialProgramExactMatch"><label>Ο τίτλος και ο φορέας του δικαιολογητικού σου ταυτίζονται ακριβώς;</label><select data-role="namedSpecialProgramExactMatch"><option value="">-- Επιλογή --</option><option value="yes">Ναι — έχω ελέγξει ακριβή τίτλο και φορέα</option><option value="no">Όχι — υπάρχει διαφορά</option><option value="unknown">Δεν είμαι σίγουρος/η</option></select></div></div>
      </div>
      <div class="hidden" data-section="education_msc_phd"><div class="question"><label>Ο τίτλος είναι ημεδαπής ή αλλοδαπής;</label><select data-role="educationDegreeOrigin"><option value="">-- Επιλογή --</option><option value="domestic">Ημεδαπής</option><option value="foreign">Αλλοδαπής</option><option value="unknown">Δεν είμαι σίγουρος/η</option></select></div><div class="question hidden" data-subsection="domesticEducation"><label>Μπορείς ήδη να τεκμηριώσεις ότι ο τίτλος είναι στις επιστήμες της αγωγής;</label><select data-role="domesticEducationEvidence"><option value="">-- Επιλογή --</option><option value="yes">Ναι — προκύπτει από τον τίτλο / τη σχετική βεβαίωση</option><option value="no">Όχι — γνωρίζω ότι δεν ανήκει στις επιστήμες της αγωγής</option><option value="unknown">Δεν είμαι σίγουρος/η</option></select></div><div class="question hidden" data-subsection="foreignEducation"><label>Υπάρχει το απαιτούμενο αποδεικτικό/αναγνώριση ότι ο τίτλος εμπίπτει στις επιστήμες της αγωγής;</label><select data-role="foreignEducationEvidence"><option value="">-- Επιλογή --</option><option value="yes">Ναι</option><option value="no">Όχι</option><option value="unknown">Δεν είμαι σίγουρος/η</option></select></div><div class="ped-flow-guidance hidden" data-role="educationDegreeGuidance" aria-live="polite"></div><div class="hidden" data-subsection="namedPostgraduateReference"><?php paidagogikiNamedPostgraduateHelp($pedagogicalCompetenceData); ?></div></div>
      <div class="hidden" data-section="pedagogical_department"><div class="question"><label>Σε ποια κατηγορία Παιδαγωγικού Τμήματος ανήκει ο τίτλος σου;</label><select data-role="pedagogicalDepartmentType"><option value="">-- Επιλογή --</option><?php foreach ($pedagogicalCompetenceData['pedagogical_departments'] as $value => $label): ?><option value="<?php echo htmlspecialchars($value, ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($label, ENT_QUOTES, 'UTF-8'); ?></option><?php endforeach; ?><option value="unknown">Δεν είμαι σίγουρος/η</option></select></div></div>
      <div class="hidden" data-section="epath"><div class="question"><label>Η ημερομηνία κτήσης του πτυχίου Ε.Π.Α.Θ. είναι προγενέστερη της 12ης Ιουνίου 2018;</label><select data-role="epathDate"><option value="">-- Επιλογή --</option><option value="before">Ναι, είναι πριν από 12/6/2018</option><option value="after">Όχι, είναι από 12/6/2018 και μετά</option><option value="unknown">Δεν είμαι σίγουρος/η</option></select></div><div class="ped-flow-guidance hidden" data-role="epathGuidance" aria-live="polite"></div></div>
      <div class="hidden" data-section="professor_school"><?php paidagogikiProfessorSchoolHelp(); ?><div class="question"><label>Έτος εισαγωγής στο συγκεκριμένο Τμήμα</label><select data-role="entryYear"><option value="">-- Επιλογή --</option><option value="up_to_2014">Μέχρι και το 2014-2015</option><option value="from_2015">Από το 2015-2016 και μετά</option><option value="unknown">Δεν είμαι σίγουρος/η</option></select></div><div class="question hidden" data-subsection="professorGraduation"><label>Έτος κτήσης του συγκεκριμένου πτυχίου</label><select data-role="graduationYear"><option value="">-- Επιλογή --</option><option value="up_to_2017">Έως και το 2017-2018</option><option value="from_2018">Από το 2018-2019 και μετά</option><option value="unknown">Δεν είμαι σίγουρος/η</option></select></div><div class="ped-flow-guidance hidden" data-role="professorSchoolGuidance" aria-live="polite"></div></div>
    </article>
  </template>

  <div id="opsydQuestion" class="question ped-opsyd-question hidden">
    <label for="opsyd">Κατάσταση θετικού αποδεικτικού στον Ο.Π.ΣΥ.Δ.</label>
    <select id="opsyd">
      <option value="">-- Δεν το έχω ελέγξει / δεν απαντώ --</option>
      <option value="yes">Εμφανίζεται / έχει καταχωριστεί</option>
      <option value="no">Δεν εμφανίζεται / δεν έχει καταχωριστεί</option>
      <option value="unknown">Δεν είμαι σίγουρος/η</option>
    </select>
    <p class="field-help"><strong>Ξεχωριστό δεύτερο στάδιο:</strong> εμφανίζεται μόνο όταν τουλάχιστον ένα δηλωμένο αποδεικτικό θεμελιώνει Π.Δ.Ε. Η καταχώριση στον Ο.Π.ΣΥ.Δ. αφορά τη χρήση του προσόντος στη διαδικασία, όχι την ίδια τη θεμελίωσή του.</p>
  </div>

  <button id="checkEparkeiaBtn" class="guide-submit" type="button" data-edu-primary-action="true" data-edu-result-target="#result">Έλεγχος Παιδαγωγικής και Διδακτικής Επάρκειας</button>

  <div id="result" class="result" role="status" aria-live="polite"></div>

  <p class="small-note">
    Το αποτέλεσμα είναι ενδεικτικό. Δεν αντικαθιστά την επίσημη προκήρυξη, τις οδηγίες του Α.Σ.Ε.Π.,
    τον έλεγχο του Ο.Π.ΣΥ.Δ. ή τον έλεγχο των αρμόδιων υπηρεσιών. Η αναγνώριση επαγγελματικής
    ισοδυναμίας τίτλου δεν καλύπτει από μόνη της το ζήτημα της Παιδαγωγικής και Διδακτικής Επάρκειας.
  </p>
</div>

<?php
$pedagogicalCompetenceBrowserReference = json_encode(
    array('appendix_named_programs' => $pedagogicalCompetenceData['appendix_named_programs']),
    JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT
);
?>
<div id="pedagogicalCompetenceReference" class="hidden" aria-hidden="true"
     data-reference-json="<?php echo htmlspecialchars($pedagogicalCompetenceBrowserReference, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?>"></div>
<script src="<?php echo htmlspecialchars(edu_asset_url('includes/pedagogical-competence-ui.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>

<?php sourceCardStart(); ?>
  <p>Η βάση αναφοράς του εργαλείου αξιοποιεί τις προκηρύξεις Α.Σ.Ε.Π. <strong>1ΓΕ/2026</strong> και <strong>2ΓΕ/2026</strong>. Οι ονομαστικές εγγραφές παλαιότερων Π.Μ.Σ. και ειδικών προγραμμάτων χρησιμοποιούνται <strong>μόνο ως βοηθητική τεκμηρίωση μέσα στην αντίστοιχη κατηγορία αποδεικτικού</strong> και δεν αποτελούν ξεχωριστή διαδρομή. <strong>Σε άλλη προκήρυξη υπερισχύουν οι ειδικοί όροι της αντίστοιχης διαδικασίας.</strong></p>
  <?php sourceCardLinksStart(); ?><?php sourceCardLink('https://info.asep.gr/node/78700', '1ΓΕ/2026 — ΑΣΕΠ ↗'); ?><?php sourceCardLink('https://info.asep.gr/node/78701', '2ΓΕ/2026 — ΑΣΕΠ ↗'); ?><?php sourceCardLink($pedagogicalCompetenceData['source']['url'], 'Παράρτημα Ε΄ 2ΓΕ/2026 — σελ. 2469–2473 ↗'); ?><?php sourceCardLink('https://www.dsanet.gr/Epikairothta/Nomothesia/n3194_03.htm', 'ν. 3194/2003 — άρθρο 8 παρ. 2 ↗'); ?><?php sourceCardLink('https://www.minedu.gov.gr/site/33962-12-04-18-prosklisi-gia-entaksi-stous-pinakes-anapliroton-genikis-paideias-mousikon-kai-eidikis-agogis-kai-ekpaidefsis-sxol-etous-2018-2019-sp-924', 'ΥΠΑΙΘ 2018 — εφαρμογή ορισμού καθηγητικών σχολών ↗'); ?><?php sourceCardLinksEnd(); ?>
<?php sourceCardEnd(); ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
  <script src="<?php echo htmlspecialchars(edu_asset_url('assets/common.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
</body>
</html>