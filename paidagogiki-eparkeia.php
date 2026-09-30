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

if (!function_exists('paidagogikiAppendixProgramOptions')) {
    function paidagogikiAppendixProgramOptions($programs) {
        $groups = array(
            'postgraduate_prior' => 'ΠΜΣ — εγκεκριμένοι τίτλοι προϊσχύοντος πλαισίου (14–38)',
            'special_program' => 'Προπτυχιακά / ειδικά προγράμματα Π.Δ.Ε. (39–52)',
        );
        foreach ($groups as $section => $groupLabel) {
            echo '<optgroup label="' . htmlspecialchars($groupLabel, ENT_QUOTES, 'UTF-8') . '">';
            foreach ($programs as $row => $program) {
                if ((isset($program['section']) ? $program['section'] : '') !== $section) {
                    continue;
                }
                $label = $row . ' — ' . $program['title'] . ' — ' . $program['provider'];
                echo '<option value="' . (int)$row . '">' . htmlspecialchars($label, ENT_QUOTES, 'UTF-8') . '</option>';
            }
            echo '</optgroup>';
        }
        echo '<option value="unknown">Δεν βρίσκω / δεν γνωρίζω τον ακριβή τίτλο</option>';
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
        <p>Δήλωσε κάθε τίτλο ή αποδεικτικό χωριστά. Αν διαθέτεις περισσότερα από ένα, το εργαλείο ελέγχει <strong>όλες τις ανεξάρτητες διαδρομές</strong>.</p>
      </div>
      <button id="addCredentialBtn" class="ped-secondary-action" type="button">+ Προσθήκη άλλου τίτλου / αποδεικτικού</button>
    </div>

    <div id="credentialsList">
      <article class="ped-credential" data-credential-index="0">
        <div class="ped-credential-heading">
          <h3>Αποδεικτικό 1</h3>
        </div>

        <div class="question">
          <label for="proofType">Τι είδους πτυχίο ή αποδεικτικό διαθέτεις;</label>
          <select id="proofType" data-role="proofType">
            <option value="">-- Επιλογή --</option>
            <?php foreach ($pedagogicalCompetenceData['proof_types'] as $value => $label): ?>
              <option value="<?php echo htmlspecialchars($value, ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($label, ENT_QUOTES, 'UTF-8'); ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <div id="aeiCertificateQuestions" class="hidden" data-section="aei_certificate">
          <div class="question">
            <label for="aeiCertificateEligibility">Για τη συγκεκριμένη βεβαίωση ισχύει η μεταβατική προϋπόθεση της 2ΓΕ/2026;</label>
            <select id="aeiCertificateEligibility" data-role="aeiCertificateEligibility">
              <option value="">-- Επιλογή --</option>
              <option value="yes">Ναι — εισαγωγή έως 2026–2027 και το Τμήμα/Σχολή χορηγούσε την πιστοποίηση κατά τον χρόνο εισαγωγής</option>
              <option value="no">Όχι</option>
              <option value="unknown">Δεν είμαι σίγουρος/η</option>
            </select>
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
          <div id="foreignEducationQuestions" class="question hidden" data-subsection="foreignEducation">
            <label for="foreignEducationEvidence">Υπάρχει το απαιτούμενο αποδεικτικό/αναγνώριση ότι ο τίτλος εμπίπτει στις επιστήμες της αγωγής;</label>
            <select id="foreignEducationEvidence" data-role="foreignEducationEvidence">
              <option value="">-- Επιλογή --</option>
              <option value="yes">Ναι</option>
              <option value="no">Όχι</option>
              <option value="unknown">Δεν είμαι σίγουρος/η</option>
            </select>
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
        </div>

        <div id="professorSchoolQuestions" class="hidden" data-section="professor_school">
          <div class="question">
            <label for="professorSchoolMatch">Το συγκεκριμένο πτυχίο — όχι απλώς ο κλάδος σου — ανήκει στις «καθηγητικές σχολές» που καλύπτει η προκήρυξη;</label>
            <select id="professorSchoolMatch" data-role="professorSchoolMatch">
              <option value="">-- Επιλογή --</option>
              <option value="yes">Ναι</option>
              <option value="no">Όχι</option>
              <option value="unknown">Δεν είμαι σίγουρος/η</option>
            </select>
          </div>
          <div class="question">
            <label for="entryYear">Έτος εισαγωγής στο συγκεκριμένο Τμήμα</label>
            <select id="entryYear" data-role="entryYear">
              <option value="">-- Επιλογή --</option>
              <option value="up_to_2014">Μέχρι και το ακαδημαϊκό έτος 2014-2015</option>
              <option value="from_2015">Από το ακαδημαϊκό έτος 2015-2016 και μετά</option>
              <option value="unknown">Δεν είμαι σίγουρος/η</option>
            </select>
          </div>
          <div class="question">
            <label for="graduationYear">Έτος κτήσης του συγκεκριμένου πτυχίου</label>
            <select id="graduationYear" data-role="graduationYear">
              <option value="">-- Επιλογή --</option>
              <option value="up_to_2017">Έως και το ακαδημαϊκό έτος 2017-2018</option>
              <option value="from_2018">Από το ακαδημαϊκό έτος 2018-2019 και μετά</option>
              <option value="unknown">Δεν είμαι σίγουρος/η</option>
            </select>
          </div>
        </div>

        <div id="appendixNamedProgramQuestions" class="hidden" data-section="appendix_named_program">
          <div class="question">
            <label for="appendixRow">Επίλεξε τον ακριβή τίτλο / πρόγραμμα</label>
            <select id="appendixRow" data-role="appendixRow">
              <option value="">-- Επιλογή τίτλου --</option>
              <?php paidagogikiAppendixProgramOptions($pedagogicalCompetenceData['appendix_named_programs']); ?>
            </select>
            <p class="field-help">Η λίστα περιλαμβάνει όλες τις εγγραφές <strong>14–52</strong> του πίνακα της 2ΓΕ/2026. Επίλεξε με βάση <strong>και την ονομασία και τον φορέα</strong>, όχι μόνο επειδή ο τίτλος μοιάζει.</p>
          </div>
          <div class="appendix-program-details hidden" data-role="appendixProgramDetails" aria-live="polite"></div>
          <div class="question hidden" data-subsection="appendixExactMatch">
            <label for="appendixExactMatch">Ο τίτλος και ο φορέας του δικού σου δικαιολογητικού ταυτίζονται ακριβώς με την παραπάνω εγγραφή;</label>
            <select id="appendixExactMatch" data-role="appendixExactMatch">
              <option value="">-- Επιλογή --</option>
              <option value="yes">Ναι — έχω ελέγξει ακριβή τίτλο και φορέα</option>
              <option value="no">Όχι — υπάρχει διαφορά</option>
              <option value="unknown">Δεν είμαι σίγουρος/η</option>
            </select>
          </div>
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
        <label>Τι είδους πτυχίο ή αποδεικτικό διαθέτεις;</label>
        <select data-role="proofType">
          <option value="">-- Επιλογή --</option>
          <?php foreach ($pedagogicalCompetenceData['proof_types'] as $value => $label): ?>
            <option value="<?php echo htmlspecialchars($value, ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($label, ENT_QUOTES, 'UTF-8'); ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="hidden" data-section="aei_certificate"><div class="question"><label>Για τη συγκεκριμένη βεβαίωση ισχύει η μεταβατική προϋπόθεση της 2ΓΕ/2026;</label><select data-role="aeiCertificateEligibility"><option value="">-- Επιλογή --</option><option value="yes">Ναι — εισαγωγή έως 2026–2027 και το Τμήμα/Σχολή χορηγούσε την πιστοποίηση κατά τον χρόνο εισαγωγής</option><option value="no">Όχι</option><option value="unknown">Δεν είμαι σίγουρος/η</option></select></div></div>
      <div class="hidden" data-section="education_msc_phd"><div class="question"><label>Ο τίτλος είναι ημεδαπής ή αλλοδαπής;</label><select data-role="educationDegreeOrigin"><option value="">-- Επιλογή --</option><option value="domestic">Ημεδαπής</option><option value="foreign">Αλλοδαπής</option><option value="unknown">Δεν είμαι σίγουρος/η</option></select></div><div class="question hidden" data-subsection="foreignEducation"><label>Υπάρχει το απαιτούμενο αποδεικτικό/αναγνώριση ότι ο τίτλος εμπίπτει στις επιστήμες της αγωγής;</label><select data-role="foreignEducationEvidence"><option value="">-- Επιλογή --</option><option value="yes">Ναι</option><option value="no">Όχι</option><option value="unknown">Δεν είμαι σίγουρος/η</option></select></div></div>
      <div class="hidden" data-section="pedagogical_department"><div class="question"><label>Σε ποια κατηγορία Παιδαγωγικού Τμήματος ανήκει ο τίτλος σου;</label><select data-role="pedagogicalDepartmentType"><option value="">-- Επιλογή --</option><?php foreach ($pedagogicalCompetenceData['pedagogical_departments'] as $value => $label): ?><option value="<?php echo htmlspecialchars($value, ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($label, ENT_QUOTES, 'UTF-8'); ?></option><?php endforeach; ?><option value="unknown">Δεν είμαι σίγουρος/η</option></select></div></div>
      <div class="hidden" data-section="epath"><div class="question"><label>Η ημερομηνία κτήσης του πτυχίου Ε.Π.Α.Θ. είναι προγενέστερη της 12ης Ιουνίου 2018;</label><select data-role="epathDate"><option value="">-- Επιλογή --</option><option value="before">Ναι, είναι πριν από 12/6/2018</option><option value="after">Όχι, είναι από 12/6/2018 και μετά</option><option value="unknown">Δεν είμαι σίγουρος/η</option></select></div></div>
      <div class="hidden" data-section="professor_school"><div class="question"><label>Το συγκεκριμένο πτυχίο ανήκει στις «καθηγητικές σχολές» που καλύπτει η προκήρυξη;</label><select data-role="professorSchoolMatch"><option value="">-- Επιλογή --</option><option value="yes">Ναι</option><option value="no">Όχι</option><option value="unknown">Δεν είμαι σίγουρος/η</option></select></div><div class="question"><label>Έτος εισαγωγής στο συγκεκριμένο Τμήμα</label><select data-role="entryYear"><option value="">-- Επιλογή --</option><option value="up_to_2014">Μέχρι και το 2014-2015</option><option value="from_2015">Από το 2015-2016 και μετά</option><option value="unknown">Δεν είμαι σίγουρος/η</option></select></div><div class="question"><label>Έτος κτήσης του συγκεκριμένου πτυχίου</label><select data-role="graduationYear"><option value="">-- Επιλογή --</option><option value="up_to_2017">Έως και το 2017-2018</option><option value="from_2018">Από το 2018-2019 και μετά</option><option value="unknown">Δεν είμαι σίγουρος/η</option></select></div></div>
      <div class="hidden" data-section="appendix_named_program">
        <div class="question"><label>Επίλεξε τον ακριβή τίτλο / πρόγραμμα</label><select data-role="appendixRow"><option value="">-- Επιλογή τίτλου --</option><?php paidagogikiAppendixProgramOptions($pedagogicalCompetenceData['appendix_named_programs']); ?></select><p class="field-help">Επίλεξε με βάση και την ονομασία και τον φορέα. Η διαθέσιμη ονομαστική βάση προέρχεται από την 2ΓΕ/2026.</p></div>
        <div class="appendix-program-details hidden" data-role="appendixProgramDetails" aria-live="polite"></div>
        <div class="question hidden" data-subsection="appendixExactMatch"><label>Ο τίτλος και ο φορέας του δικού σου δικαιολογητικού ταυτίζονται ακριβώς με την παραπάνω εγγραφή;</label><select data-role="appendixExactMatch"><option value="">-- Επιλογή --</option><option value="yes">Ναι — έχω ελέγξει ακριβή τίτλο και φορέα</option><option value="no">Όχι — υπάρχει διαφορά</option><option value="unknown">Δεν είμαι σίγουρος/η</option></select></div>
      </div>
    </article>
  </template>

  <div class="question ped-opsyd-question">
    <label for="opsyd">Κατάσταση σχετικού αποδεικτικού στον Ο.Π.ΣΥ.Δ.</label>
    <select id="opsyd">
      <option value="">-- Δεν το έχω ελέγξει / δεν απαντώ --</option>
      <option value="yes">Εμφανίζεται / έχει καταχωριστεί</option>
      <option value="no">Δεν εμφανίζεται / δεν έχει καταχωριστεί</option>
      <option value="unknown">Δεν είμαι σίγουρος/η</option>
    </select>
    <p class="field-help"><strong>Ξεχωριστός έλεγχος:</strong> η καταχώριση στον Ο.Π.ΣΥ.Δ. επηρεάζει τη χρήση του προσόντος στη διαδικασία, όχι το αν ο τίτλος σου αποτελεί από μόνος του αποδεικτικό Π.Δ.Ε.</p>
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
  <p>Η βάση αναφοράς του εργαλείου αξιοποιεί τις προκηρύξεις Α.Σ.Ε.Π. <strong>1ΓΕ/2026</strong> και <strong>2ΓΕ/2026</strong>. Η ονομαστική λίστα τίτλων/προγραμμάτων έχει μεταγραφεί από το <strong>Παράρτημα Ε΄ της 2ΓΕ/2026</strong> (σελ. <strong>2469–2473</strong>, 53 εγγραφές) και λειτουργεί ως βοήθημα τεκμηρίωσης. <strong>Σε άλλη προκήρυξη υπερισχύουν οι ειδικοί όροι της αντίστοιχης διαδικασίας.</strong></p>
  <?php sourceCardLinksStart(); ?><?php sourceCardLink('https://info.asep.gr/node/78700', '1ΓΕ/2026 — ΑΣΕΠ ↗'); ?><?php sourceCardLink('https://info.asep.gr/node/78701', '2ΓΕ/2026 — ΑΣΕΠ ↗'); ?><?php sourceCardLink($pedagogicalCompetenceData['source']['url'], 'Παράρτημα Ε΄ 2ΓΕ/2026 — σελ. 2469–2473 ↗'); ?><?php sourceCardLinksEnd(); ?>
<?php sourceCardEnd(); ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
  <script src="<?php echo htmlspecialchars(edu_asset_url('assets/common.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
</body>
</html>