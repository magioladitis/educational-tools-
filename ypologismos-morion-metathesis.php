<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/teacher-specialties.php';
// Shared specialty labels, supplied by the existing site-wide PHP registry.
$transferSpecialtyLabels = array();
foreach (teacherSpecialties() as $specialtyCode => $specialtyData) {
    $canonicalSpecialtyCode = teacherSpecialtyCanonicalCode($specialtyCode);
    if ($canonicalSpecialtyCode !== '' && isset($specialtyData['label'])) {
        $transferSpecialtyLabels[$canonicalSpecialtyCode] = (string) $specialtyData['label'];
    }
}
?>
<!DOCTYPE html>
<html lang="el">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <?php require __DIR__ . '/includes/head-pwa.php'; ?>
  <title>Μεταθέσεις μόνιμων εκπαιδευτικών Δ.Ε.</title>
  <link rel="stylesheet" href="<?php echo htmlspecialchars(edu_asset_url('assets/common.css'), ENT_QUOTES, 'UTF-8'); ?>">
  <link rel="stylesheet" href="<?php echo htmlspecialchars(edu_asset_url('assets/transfer-secondary.css'), ENT_QUOTES, 'UTF-8'); ?>">
  <link rel="stylesheet" href="<?php echo htmlspecialchars(edu_asset_url('assets/transfer-level-switch.css'), ENT_QUOTES, 'UTF-8'); ?>">
</head>
<body class="edu-ui edu-calc-standard edu-page-transfer">
<?php require_once __DIR__ . '/includes/header.php'; ?>
<?php require_once __DIR__ . '/includes/components/calculator-layout.php'; ?>
<?php require_once __DIR__ . '/includes/components/transfer-social-criteria.php'; ?>
<?php require_once __DIR__ . '/includes/components/transfer-service-help.php'; ?>

<?php calculatorContainerStart(array('class' => 'app')); ?>
  <?php calculatorHero(array(
    'title' => 'Μεταθέσεις μόνιμων εκπαιδευτικών Δ.Ε.',
    'intro_html' => 'Εργαλείο για εκπαιδευτικούς <strong>Δευτεροβάθμιας Εκπαίδευσης</strong>: ενδεικτική μοριοδότηση όπου εφαρμόζεται και προσωποποιημένος κατάλογος ελέγχου δικαιολογητικών ανά τύπο μετάθεσης.',
    'badges' => array('Βάση: εγκύκλιος 2026–2027 (138014/Ε2)', 'Μ.Σ.Δ.', 'Δυσπρόσιτα / ειδικές υπηρετήσεις')
  )); ?>

  <!-- Only the grade switch is new on the stabilized Secondary page.
       All Secondary form fields, data and calculations remain unchanged. -->
  <nav class="transfer-level-switch" aria-label="Επιλογή βαθμίδας">
    <a href="ypologismos-morion-metathesis-protovathmia.php">Πρωτοβάθμια</a>
    <strong aria-current="page">Δευτεροβάθμια</strong>
  </nav>
  <?php calculatorColumnsStart(); ?>
    <?php calculatorMainStart(); ?>
      <?php calculatorCardStart(array('title' => 'Α. Τύπος διαδικασίας και υπηρεσιακά στοιχεία')); ?>
        <div class="field-grid">
          <div class="field transfer-main-mode-field">
            <label for="mode">Τύπος διαδικασίας</label>
            <select id="mode">
              <option value="region">Από περιοχή σε περιοχή — Γενική [001.ΔΕ001]</option>
              <option value="music">Μουσικά Σχολεία [002.ΜΟΥΣ001]</option>
              <option value="art">Καλλιτεχνικά Σχολεία [002.ΚΑΛ001]</option>
              <option value="intercultural">Σχολεία Διαπολιτισμικής Εκπαίδευσης [002.ΔΙΑΠ002]</option>
              <option value="local">Οριστική τοποθέτηση / βελτίωση [003.ΔΕΒ01]</option>
              <option value="smeae">ΣΜΕΑΕ / ΕΕΕΕΚ [001.ΔΕΣΚΕ]</option>
              <option value="eae_sensory">Σχολεία Κωφών / Τυφλών — ΕΝΓ / Braille [001.ΔΕΚΤ]</option>
              <option value="kedasy">ΚΕΔΑΣΥ [001.ΔΕΣΚΕ.ΚΔΣ]</option>
            </select>
          </div>
          <div class="field" id="transferModeGuidance" role="status" aria-live="polite" style="grid-column:1/-1; padding:12px; border:1px solid #b8c5d5; border-radius:8px;" >Επιλέξτε τύπο διαδικασίας για τις αντίστοιχες οδηγίες.</div>
        </div>
        <?php calculatorDisclosure(array(
          'id' => 'transferMultipleApplicationsHelp',
          'summary' => 'Μπορώ να υποβάλω περισσότερες από μία αιτήσεις μετάθεσης;',
          'html' => '<p><strong>Ναι</strong>, εφόσον πληροίτε τις προϋποθέσεις κάθε τύπου αίτησης. Μπορείτε να υποβάλετε χωριστές αιτήσεις, για παράδειγμα για ΕΑΕ/ΚΕΔΑΣΥ, Μουσικά, Καλλιτεχνικά, Διαπολιτισμικά ή μετάθεση από περιοχή σε περιοχή.</p>'
            . '<p>Οι μεταθέσεις εξετάζονται με την εξής σειρά: <strong>ΕΑΕ/ΚΕΔΑΣΥ → Μουσικά → Καλλιτεχνικά → Διαπολιτισμικά → από περιοχή σε περιοχή → αμοιβαίες</strong>. Εάν ικανοποιηθεί αίτηση σε προηγούμενη κατηγορία, δεν εξετάζονται οι επόμενες αιτήσεις μετάθεσης.</p>'
            . '<p><strong>Προσοχή:</strong> Η οριστική τοποθέτηση/βελτίωση αποτελεί διαφορετικό τύπο αίτησης. Ειδικά όποιος έχει οργανική θέση σε ΕΑΕ, Μουσικό, Καλλιτεχνικό ή Διαπολιτισμικό σχολείο και ζητά μετάθεση στη Γενική της περιοχής όπου ανήκει το σχολείο, υποβάλλει αίτηση «από περιοχή σε περιοχή» και όχι αίτηση βελτίωσης.</p>'
            . '<p><strong>Αμοιβαίες μεταθέσεις:</strong> αποτελούν χωριστή διαδικασία. Η αίτηση γίνεται εντός <strong>15 ημερών από την ανακοίνωση των μεταθέσεων</strong> και δεν αποτελεί μέρος του αριθμητικού προελέγχου της παρούσας φόρμας. Ελέγξτε τις ειδικές προϋποθέσεις, την ίδια ειδικότητα και την υπηρεσιακή επιβεβαίωση στην εγκύκλιο.</p>'
            . '<p class="small-note">Πηγή: <a href="https://www.minedu.gov.gr/site/71035-15-10-26-metatheseis-ekpaideutikon-deuterobathmias-ekpaideuses-scholikou-etous-2026-2027" target="_blank" rel="noopener noreferrer">Εγκύκλιος Δ.Ε. 138014/Ε2/15-10-2026 ↗</a>, Μέρος Τρίτο, Κεφ. Η΄, παρ. 5–6.</p>'
        )); ?>
        <div id="transferGeneralOriginField" class="field" style="margin:14px 0;">
          <label for="transferGeneralOrigin">Υπηρεσιακή προέλευση</label>
          <select id="transferGeneralOrigin">
            <option value="general">Υπηρετώ στη Γενική / Επαγγελματική Εκπαίδευση</option>
            <option value="appointed_eae">Διορίστηκα απευθείας στην Ειδική Αγωγή</option>
            <option value="transferred_eae">Μετατέθηκα στην Ειδική Αγωγή από τη Γενική</option>
            <option value="other">Άλλη περίπτωση / δεν είμαι βέβαιος</option>
          </select>
        </div>
        <section id="transferGeneralFromEaePanel" style="display:none;padding:14px; margin:14px 0; border:1px solid #b8c5d5; border-radius:10px;" aria-label="Ειδικός έλεγχος επιστροφής από ΕΑΕ στη Γενική Εκπαίδευση">
          <h3 style="margin:0 0 8px">Ειδικός έλεγχος επιστροφής από ΕΑΕ στη Γενική Εκπαίδευση</h3>
          <p class="small-note">Αφορά μόνο όσους <strong>διορίστηκαν στην ΕΑΕ</strong> ή <strong>μετατέθηκαν στην ΕΑΕ από τη Γενική</strong>. Οι προϋποθέσεις είναι διαφορετικές· δεν ισχύει πενταετία για όλους.</p>
          <div id="transferGeneralEaeAppointed" style="display:none">
            <div class="field-grid">
              <div class="field"><label for="transferGeneralEaeYears">Έτη υπηρεσίας σε σχολικές μονάδες ΕΑΕ από τον διορισμό</label>
                <input id="transferGeneralEaeYears" type="number" min="0" max="50" step="0.1" value="0" inputmode="decimal"></div>
              <div class="field"><label for="transferGeneralEaeAreaYears">Σχολικά έτη στην περιοχή διορισμού</label>
                <input id="transferGeneralEaeAreaYears" type="number" min="0" max="45" step="0.1" value="0" inputmode="decimal"></div>
            </div>
          </div>
          <div id="transferGeneralEaeTransferred" style="display:none">
            <div class="field-grid">
              <div class="field"><label for="transferGeneralEaeSinceTransfer">Έτη διδακτικής υπηρεσίας στην ΕΑΕ μετά τη μετάθεση</label>
                <input id="transferGeneralEaeSinceTransfer" type="number" min="0" max="50" step="0.1" value="0" inputmode="decimal"></div>
            </div>
            <div class="check-row"><label><input id="transferGeneralEaeOrganic" type="checkbox"> Έχω υπηρετήσει την οργανική μου θέση στην ΕΑΕ για ένα έτος</label></div>
          </div>
          <div id="transferGeneralEaeAdvice" role="status" aria-live="polite" aria-atomic="true" class="transfer-eligibility-message small-note"></div>
          <p class="small-note">Νομική βάση: εγκύκλιος <strong>138014/Ε2/15-10-2026</strong>, Μέρος Δεύτερο, Κεφ. Δ΄ (σελ. 35), άρθρο 48 §3 ν. 4415/2016 και άρθρο 62 §5α ν. 4589/2019. Ο προέλεγχος δεν πιστοποιεί δικαίωμα μετάθεσης ούτε καλύπτει όλες τις εξαιρέσεις.</p>
        </section>
      <?php calculatorCardEnd(); ?>

      <section id="transferEaePanel" class="calculator-card" style="display:none; padding:18px; border:1px solid #b8c5d5; border-radius:12px; margin-bottom:16px;">
        <h2>Προέλεγχος δικαιώματος μετάθεσης στην ΕΑΕ</h2>
        <p class="small-note">Δηλώστε τα προσόντα ΕΑΕ που διαθέτετε. Αρκεί μία αποδεκτή διαδρομή εξειδίκευσης· το δικαίωμα εξετάζεται <strong>συνδυαστικά</strong> με την προέλευση, τον χρόνο υπηρεσίας και τον τύπο θέσης. Οι ενδείξεις αποτελούν προέλεγχο, όχι διοικητική απόφαση.</p>
        <div class="field-grid">
          <div class="field"><label for="transferEaeOrigin">Από ποια εκπαίδευση ζητείται η μετάθεση;</label>
            <select id="transferEaeOrigin">
              <option value="general">Από Γενική / Επαγγελματική Εκπαίδευση προς ΕΑΕ</option>
              <option value="eae">Από δομή ΕΑΕ προς άλλη δομή ΕΑΕ</option>
            </select>
          </div>
          <div class="field"><label for="transferEaeArea">Περιοχή της μετάθεσης</label>
            <select id="transferEaeArea"><option value="other">Άλλη περιοχή μετάθεσης</option><option value="same">Ίδια περιοχή μετάθεσης / βελτίωση</option></select>
          </div>
          <div class="field" id="transferEaeDestinationField"><label for="transferEaeDestination">Δομή προορισμού</label>
            <select id="transferEaeDestination"><option value="smeae">ΣΜΕΑΕ / ΕΕΕΕΚ</option><option value="deaf">Σχολείο Κωφών (ΕΝΓ)</option><option value="blind">Σχολείο Τυφλών (Braille)</option><option value="kedasy">ΚΕΔΑΣΥ</option></select>
          </div>
          <div class="field"><label for="transferEaeBranch">Κλάδος</label>
            <select id="transferEaeBranch"><option value="other">Άλλος κλάδος Δ.Ε. / δεν έχω επιλέξει</option><option value="pe02">ΠΕ02 Φιλολόγων</option><option value="pe03">ΠΕ03 Μαθηματικών</option><option value="pe04">ΠΕ04 Φυσικών Επιστημών</option><option value="pe11">ΠΕ11 Φυσικής Αγωγής</option></select>
          </div>
          <fieldset class="transfer-eae-qualifications" id="transferEaeQualificationsFieldset" style="grid-column:1/-1">
            <legend>Προσόντα εξειδίκευσης στην ΕΑΕ</legend>
            <p class="small-note">Επιλέξτε <strong>όσα προσόντα διαθέτετε</strong>. Για τη θεμελίωση αρκεί μία αποδεκτή διαδρομή· η υπηρεσία ελέγχει την αναγνώριση και τη συνάφεια.</p>
            <div class="transfer-eae-qualification-list">
              <label><input type="checkbox" data-eae-qualification="doctorate"> Διδακτορικό στην ΕΑΕ ή στη Σχολική Ψυχολογία</label>
              <label><input type="checkbox" data-eae-qualification="masters"> Μεταπτυχιακό στην ΕΑΕ ή στη Σχολική Ψυχολογία</label>
              <label><input type="checkbox" data-eae-qualification="two_year"> Πτυχίο διετούς μετεκπαίδευσης στην ΕΑΕ των Διδασκαλείων</label>
              <label><input type="checkbox" data-eae-qualification="experience"> Πτυχίο ΑΕΙ με τουλάχιστον πενταετή αποδεδειγμένη προϋπηρεσία στην ΕΑΕ</label>
              <label id="transferEaePe11QualificationRow"><input type="checkbox" data-eae-qualification="pe11"> ΠΕ11: πτυχίο ΤΕΦΑΑ με προβλεπόμενη κύρια ειδικότητα Ειδικής/Προσαρμοσμένης Φυσικής Αγωγής</label>
            </div>
            <select id="transferEaeQualification" hidden aria-hidden="true" tabindex="-1"><option value="none">Καμία επιλογή</option><option value="doctorate">Διδακτορικό</option><option value="masters">Μεταπτυχιακό</option><option value="two_year">Διετής μετεκπαίδευση</option><option value="experience">Πενταετής υπηρεσία</option><option value="pe11">ΠΕ11</option></select>
          </fieldset>
          <p class="small-note" id="transferEaeRouteNote" style="grid-column:1/-1"></p>
          <div class="field" id="transferEaeExperienceRow" style="display:none"><label for="transferEaeExperienceYears">Αποδεδειγμένα έτη προϋπηρεσίας στην ΕΑΕ (διαδρομή πενταετίας)</label><input id="transferEaeExperienceYears" type="number" min="0" max="50" step="0.1" inputmode="decimal" value="0"></div>
          <div class="field" id="transferEaeGeneralYearsRow"><label for="transferEaeTeachingYears">Διδακτικά έτη από διορισμό (Γενική → ΕΑΕ) <small>Έως 50 έτη</small></label><input id="transferEaeTeachingYears" type="number" min="0" max="50" step="0.1" inputmode="decimal" value="0"></div>
        </div>
        <div class="field" id="transferEaeSensoryCertificationRow" style="display:none;margin-top:12px">
          <label for="transferEaeSensoryCertification">Πιστοποιημένη γνώση ΕΝΓ ή Braille (ανάλογα με τη σχολική μονάδα)</label>
          <select id="transferEaeSensoryCertification">
            <option value="unknown">Δεν έχει δηλωθεί / χρειάζεται έλεγχος</option>
            <option value="yes">Διαθέτω την απαιτούμενη πιστοποίηση</option>
            <option value="no">Δεν διαθέτω την απαιτούμενη πιστοποίηση</option>
          </select>
          <p class="small-note">Απλή παρακολούθηση μαθημάτων δεν αρκεί. Η εγκυρότητα και ο φορέας πιστοποίησης ελέγχονται από την αρμόδια υπηρεσία.</p>
        </div>
        <div id="transferEaeNewlyRow" class="check-row"><label><input type="checkbox" id="transferEaeNewly"> Νεοδιόριστος: χρειάζεται έλεγχος διετίας στην περιοχή διορισμού</label></div>
        <div class="field" id="transferEaeAreaYearsRow" style="display:none; margin-top:10px"><label for="transferEaeAreaYears">Έτη υπηρεσίας στην περιοχή διορισμού</label><input id="transferEaeAreaYears" type="number" min="0" max="45" step="0.1" inputmode="decimal" value="0"></div>
        <div class="field-grid" style="margin-top:14px">
          <div class="field"><label for="transferEaePreviousYear">Έχετε αποστείλει ηλεκτρονικό φάκελο ΕΑΕ σε προηγούμενο κύκλο;</label>
            <select id="transferEaePreviousYear"><option value="none">Όχι / δεν γνωρίζω</option><option value="2025">Ναι, το 2025</option><option value="2026">Ναι, το 2026</option><option value="other">Ναι, σε άλλο έτος</option></select>
          </div>
        </div>
        <div class="check-row"><label><input type="checkbox" id="transferEaeNewDocuments"> Έχω νέα στοιχεία ή δικαιολογητικά στον φάκελο ΕΑΕ που δεν είχαν υποβληθεί.</label></div>
        <div id="transferEaeDossierAdvice" role="status" aria-live="polite" class="small-note" style="margin:10px 0"></div>
        <div id="transferEaeAdvice" role="status" aria-live="polite" aria-atomic="true" class="transfer-eligibility-review" style="padding:12px; margin-top:14px;"></div>
        <p class="small-note">Νομική βάση: εγκύκλιος 138014/Ε2/15-10-2026, Μέρος Δεύτερο, κεφάλαιο Δ΄ (σελ. 34–38). Προσόντα, πενταετής διαδρομή και ειδικοί όροι υπόκεινται σε υπηρεσιακό έλεγχο.</p>
      </section>

      <section id="transferQualificationPanel" class="calculator-card" style="display:none; padding:18px; border:1px solid #b8c5d5; border-radius:12px; margin-bottom:16px;">
        <h2 id="transferQualificationTitle">Ειδική διαδικασία προσόντων</h2>
        <p id="transferQualificationIntro"></p>
        <div class="field-grid">
          <div class="field" id="transferEducationTypeField"><label for="transferEducationType">Κατηγορία αντικειμένου</label><select id="transferEducationType"><option value="general">Γενική παιδεία / κοινός κλάδος</option><option value="specialized">Μουσική / καλλιτεχνική ειδίκευση</option></select></div>
        </div>
        <div id="transferMusicEligibility" style="display:none">
          <div class="field-grid">
            <div class="field"><label for="transferMusicTeachingYears">Έτη διδακτικής εμπειρίας σε σχολεία Δευτεροβάθμιας</label><input id="transferMusicTeachingYears" type="number" min="0" max="50" step="0.1" inputmode="decimal" value="0"></div>
            <div class="field"><label for="transferMusicException">Εξαίρεση από την πενταετία</label><select id="transferMusicException"><option value="none">Δεν επικαλούμαι εξαίρεση</option><option value="direct_music">Εκπαιδευτικός μουσικής παιδείας με απευθείας διορισμό σε Μουσικό</option><option value="primary_transfer">Μετάταξη από Πρωτοβάθμια σε Μουσικό ή Καλλιτεχνικό</option></select></div>
          </div>
          <div class="check-row"><label><input id="transferMusicOrganic" type="checkbox"> Έχω υπηρετήσει την οργανική μου θέση (ή υπάρχει ειδική νόμιμη εξαίρεση προς έλεγχο)</label></div>
          <div id="transferMusicSpecializationPanel" style="display:none; margin-top:14px">
            <label for="transferMusicSubject">Αντικείμενο μουσικής εξειδίκευσης</label>
            <select id="transferMusicSubject">
              <option value="unspecified">Δεν επιλέχθηκε ακόμη</option>
              <option value="theory">Θεωρητικά της μουσικής</option>
              <option value="traditional">Ελληνική παραδοσιακή μουσική</option>
              <option value="ensemble">Διεύθυνση χορωδιακών / ορχηστρικών συνόλων</option>
              <option value="instrument">Ευρωπαϊκό μουσικό όργανο</option>
              <option value="other">Άλλο μουσικό αντικείμενο — έλεγχος ειδικότητας</option>
            </select>
          </div>
          <div class="field-grid" style="margin-top:14px"><div class="field"><label for="transferMusicPreviousYear">Έχει υποβληθεί προηγούμενος ηλεκτρονικός φάκελος για Μουσικά;</label>
            <select id="transferMusicPreviousYear"><option value="none">Όχι / δεν γνωρίζω</option><option value="2021">2021</option><option value="2022">2022</option><option value="2023">2023</option><option value="2024">2024</option><option value="2025">2025</option><option value="2026">2026</option><option value="other">Άλλο έτος</option></select></div></div>
          <div class="check-row"><label><input id="transferMusicNewDocuments" type="checkbox"> Υπάρχουν νέα στοιχεία/δικαιολογητικά για τον φάκελο Μουσικών.</label></div>
          <div id="transferMusicDossierAdvice" role="status" aria-live="polite" class="small-note" style="margin:10px 0"></div>
          <div id="transferMusicAdvice" role="status" aria-live="polite" aria-atomic="true" class="transfer-eligibility-message"></div>
        </div>
        <div id="transferArtEligibility" style="display:none">
          <div class="field-grid">
            <div class="field"><label for="transferArtBranch">Κλάδος / ειδικότητα — οργανικά κενά Καλλιτεχνικών 2026–2027</label>
              <select id="transferArtBranch"><option value="">— Επιλέξτε κλάδο / ειδικότητα —</option></select>
              <span class="small-note">Εμφανίζονται μόνο κλάδοι με προκηρυγμένο οργανικό κενό Δ.Ε.</span>
            </div>
            <div class="field"><label for="transferArtSchool">Καλλιτεχνικό σχολείο με κενό στον κλάδο</label>
              <select id="transferArtSchool" disabled><option value="">— Επιλέξτε πρώτα κλάδο —</option></select>
            </div>
            <div class="field" id="transferArtSpecializationRow" style="display:none"><label for="transferArtSpecialization">Ειδίκευση προκηρυγμένου κενού</label>
              <select id="transferArtSpecialization"><option value="">— Επιλέξτε ειδίκευση —</option></select>
              <span class="small-note">Π.χ. Ζωγραφική, Γλυπτική, Πιάνο, Παραδοσιακοί χοροί. Όπου δεν ορίζεται ιδιαίτερη ειδίκευση, δεν απαιτείται άλλη επιλογή.</span>
            </div>
            <div class="field" id="transferArtOrientationRow" style="display:none"><label for="transferArtOrientation">Κατεύθυνση / σχετικό αντικείμενο</label>
              <select id="transferArtOrientation"><option value="">Επιλέξτε (για συμβουλές)</option>
                <option value="visual">Εικαστικές Τέχνες</option><option value="theater">Θέατρο – Κινηματογράφος</option>
                <option value="dance">Χορός</option><option value="other">Άλλο / διαθεματικό αντικείμενο</option>
              </select>
            </div>
            <div class="field"><label for="transferArtTeachingYears">Διδακτική εμπειρία σε σχολεία Δ.Ε. (έτη)</label>
              <input id="transferArtTeachingYears" type="number" min="0" max="50" step="0.1" inputmode="decimal" value="0">
            </div>
            <div class="field"><label for="transferArtException">Εξαίρεση από την πενταετία</label>
              <select id="transferArtException"><option value="none">Δεν επικαλούμαι εξαίρεση</option>
                <option value="direct_music">Μουσική παιδεία με απευθείας διορισμό σε Μουσικό Σχολείο</option>
                <option value="primary_transfer">Μετάταξη από Πρωτοβάθμια σε Μουσικό ή Καλλιτεχνικό Σχολείο</option>
              </select>
            </div>
          </div>
          <p class="small-note" id="transferArtAvailabilitySummary" role="status" aria-live="polite">Επιλέξτε κλάδο και σχολείο από τις 65 προκηρυγμένες θέσεις Δ.Ε. 2026–2027.</p>
          <div class="check-row"><label><input id="transferArtOrganic" type="checkbox"> Έχω υπηρετήσει την οργανική μου θέση (ή υπάρχει ειδική εξαίρεση προς υπηρεσιακό έλεγχο)</label></div>
          <div class="field-grid" style="margin-top:14px"><div class="field"><label for="transferArtPreviousYear">Προηγούμενος ηλεκτρονικός φάκελος για Καλλιτεχνικά</label>
            <select id="transferArtPreviousYear"><option value="none">Όχι / δεν γνωρίζω</option>
              <option value="2021">2021</option><option value="2022">2022</option><option value="2023">2023</option>
              <option value="2024">2024</option><option value="2025">2025</option><option value="2026">2026</option><option value="other">Άλλο έτος</option>
            </select></div></div>
          <div class="check-row"><label><input id="transferArtNewDocuments" type="checkbox"> Έχω νέα στοιχεία ή δικαιολογητικά που δεν είχαν συμπεριληφθεί στον παλαιότερο φάκελο.</label></div>
          <div id="transferArtDossierAdvice" role="status" aria-live="polite" class="small-note" style="margin:10px 0"></div>
          <div id="transferArtAdvice" role="status" aria-live="polite" aria-atomic="true" class="transfer-eligibility-message"></div>
          <p class="small-note">Βάση αναφοράς: εγκύκλιος 138014/Ε2/15-10-2026, Κεφ. ΣΤ΄ (σελ. 41–43). Οι όροι πενταετίας/οργανικής εξετάζονται έως 31/08/2027. Ο τελικός έλεγχος γίνεται από τα αρμόδια όργανα.</p>
        </div>
        <div id="transferInterculturalEligibility" style="display:none">
          <p class="small-note">Προέλεγχος του Κεφαλαίου Ζ΄ της εγκυκλίου 138014/Ε2/15-10-2026 (σελ. 43–45). Οι προϋποθέσεις υπηρέτησης οργανικής και τα δηλωμένα προσόντα ελέγχονται από τη ΔΔΕ. <strong>Δεν υπάρχει βαθμολογική κατάταξη προσόντων.</strong></p>
          <div class="field-grid">
            <div class="field"><label for="transferInterculturalBranch">Κλάδος / ειδικότητα Δ.Ε. — οργανικά κενά 2026–2027</label>
              <select id="transferInterculturalBranch"><option value="">— Επιλέξτε κλάδο / ειδικότητα —</option></select>
              <span class="small-note">Ενεργοί μόνο οι κλάδοι με τουλάχιστον μία προκηρυγμένη οργανική θέση Δευτεροβάθμιας.</span></div>
            <div class="field"><label for="transferInterculturalSchool">Σχολείο με οργανικό κενό στον κλάδο</label>
              <select id="transferInterculturalSchool" disabled><option value="">— Επιλέξτε πρώτα κλάδο —</option></select></div>
            <div class="field"><label for="transferInterculturalOrganic">Έχει υπηρετηθεί η κατεχόμενη οργανική θέση έως 31/08/2027;</label>
              <select id="transferInterculturalOrganic"><option value="unknown">Δεν έχει ελεγχθεί</option><option value="yes">Ναι, σύμφωνα με τα υπηρεσιακά στοιχεία</option><option value="no">Όχι</option></select></div>
            <div class="field"><label for="transferInterculturalCurrent">Υπηρετείτε οργανικά ήδη σε Πειραματικό Σχολείο Διαπολιτισμικής Εκπαίδευσης;</label>
              <select id="transferInterculturalCurrent"><option value="no">Όχι</option><option value="yes">Ναι</option><option value="unknown">Δεν είμαι βέβαιος/η</option></select></div>
            <div class="field" id="transferInterculturalYearsRow" style="display:none"><label for="transferInterculturalYears">Συμπληρωμένα έτη πραγματικής υπηρεσίας στην οργανική αυτού του σχολείου</label>
              <input id="transferInterculturalYears" type="number" min="0" max="50" step="0.1" value="0" inputmode="decimal"></div>
            <input id="transferInterculturalVacancy" type="hidden" value="unknown">
            <div class="field"><label for="transferInterculturalLanguageCase">Σύνθεση μαθητικού πληθυσμού στο σχολείο ενδιαφέροντος</label>
              <select id="transferInterculturalLanguageCase"><option value="unknown">Δεν γνωρίζω / απαιτείται έλεγχος</option><option value="usual">Γενική περίπτωση (ελέγχεται γλώσσα προέλευσης)</option><option value="roma">Οι περισσότεροι μαθητές είναι Ρομά — ειδική εξαίρεση γλώσσας</option></select></div>
            <div class="field"><label for="transferInterculturalPreviousYear">Υπάρχει προηγούμενος ηλεκτρονικός φάκελος διαπολιτισμικών;</label>
              <select id="transferInterculturalPreviousYear"><option value="none">Όχι / δεν γνωρίζω</option><option value="2021">2021</option><option value="2022">2022</option><option value="2023">2023</option><option value="2024">2024</option><option value="2025">2025</option><option value="2026">2026</option><option value="other">Άλλο έτος</option></select></div>
          </div>
          <p id="transferInterculturalAvailabilitySummary" class="small-note" role="status" aria-live="polite">Επιλέξτε κλάδο και σχολείο από τις προκηρυγμένες οργανικές θέσεις Δ.Ε. 2026–2027.</p>
          <div class="check-row"><label><input id="transferInterculturalNewDocuments" type="checkbox"> Έχω νέα στοιχεία/δικαιολογητικά που δεν περιλαμβάνονται στον προηγούμενο φάκελο.</label></div>
          <h3 style="margin:18px 0 6px">Πρόσθετα προσόντα — κατά σειρά προτεραιότητας της εγκυκλίου</h3>
          <p class="small-note">Σημειώστε μόνο όσα πράγματι διαθέτετε. Η σειρά αφορά την περιγραφή της εγκυκλίου, <strong>όχι αριθμητική βαθμολόγηση</strong>. Η γλωσσική εξαίρεση αφορά σχολεία με πλειοψηφία μαθητών Ρομά.</p>
          <div id="transferInterculturalQualifications">
            <div class="check-row"><label><input type="checkbox" data-intercultural-qual="origin_language"> α) Επαρκής γνώση της ξένης γλώσσας προέλευσης της πλειονότητας των μαθητών</label></div>
            <div class="check-row"><label><input type="checkbox" data-intercultural-qual="greek_second"> β) Εξειδίκευση στη διδασκαλία Ελληνικής ως δεύτερης ή ξένης γλώσσας</label></div>
            <div class="check-row"><label><input type="checkbox" data-intercultural-qual="intercultural_studies"> γ) Μετεκπαίδευση / μεταπτυχιακές σπουδές στη Διαπολιτισμική Αγωγή</label></div>
            <div class="check-row"><label><input type="checkbox" data-intercultural-qual="pedagogy_studies"> δ) Μετεκπαίδευση / μεταπτυχιακές σπουδές στις επιστήμες της αγωγής</label></div>
            <div class="check-row"><label><input type="checkbox" data-intercultural-qual="subject_studies"> ε) Μετεκπαίδευση / μεταπτυχιακές σπουδές στο αντικείμενο διδασκαλίας</label></div>
            <div class="check-row"><label><input type="checkbox" data-intercultural-qual="school_experience"> στ) Διδακτική εμπειρία σε Σχολεία Διαπολιτισμικής Εκπαίδευσης</label></div>
            <div class="check-row"><label><input type="checkbox" data-intercultural-qual="reception_experience"> ζ) Διδακτική εμπειρία σε Τάξεις Υποδοχής ή Φροντιστηριακά Τμήματα</label></div>
            <div class="check-row"><label><input type="checkbox" data-intercultural-qual="international_language"> η) Επαρκής γνώση Αγγλικών, Γαλλικών ή Γερμανικών</label></div>
            <div class="check-row"><label><input type="checkbox" data-intercultural-qual="seminars"> θ) Επιμορφωτικά σεμινάρια Διαπολιτισμικής Εκπαίδευσης από προβλεπόμενους φορείς</label></div>
          </div>
          <div id="transferInterculturalAdvice" role="status" aria-live="polite" aria-atomic="true" class="transfer-eligibility-message"></div>
          <div id="transferInterculturalDossierAdvice" role="status" aria-live="polite" class="small-note" style="margin:10px 0"></div>
          <p class="small-note">Πηγή φίλτρου: «ΟΡΓΑΝΙΚΑ ΚΕΝΑ ΔΙΑΠΟΛΙΤΙΣΜΙΚΑ ΔΕ 2026–2027», 23 κενά σε 13 κλάδους/ειδικότητες. Ελέγξτε πριν από την υποβολή τυχόν νεότερες επίσημες ανακοινώσεις. Οργανικές κενές θέσεις ανά κλάδο/ειδικότητα: <a href="https://teachers.minedu.gov.gr/" target="_blank" rel="noopener noreferrer">teachers.minedu.gov.gr ↗</a>. Αίτηση στο ίδιο σύστημα· όταν χρειάζεται νέος φάκελος, δικαιολογητικά μόνο μέσω <a href="https://mydocs.minedu.gov.gr/" target="_blank" rel="noopener noreferrer">mydocs.minedu.gov.gr ↗</a>, σε μία αποστολή.</p>
        </div>
        <p class="small-note" id="transferQualificationFootnote">Ο έλεγχος δεν αντικαθιστά την εξέταση από το αρμόδιο υπηρεσιακό συμβούλιο ή την Επιστημονική Επιτροπή Μουσικής Παιδείας. Εγκύκλιος 2026–2027, Κεφ. Ε΄, σελ. 39–41.</p>
      </section>

      <aside id="transferInterculturalNotes" class="calculator-card" style="display:none; padding:16px; border:1px solid #b8c5d5; border-radius:12px; margin:14px 0" aria-label="Σημειώσεις Διαπολιτισμικής Εκπαίδευσης">
        <h3 style="margin:0 0 8px">Χρήσιμες σημειώσεις για Διαπολιτισμικά</h3>
        <p class="small-note"><strong>Αλληλεπίδραση με άλλες αιτήσεις.</strong> Σε περίπτωση ικανοποίησης μετάθεσης σε ΕΑΕ/ΚΕΔΑΣΥ, Μουσικό ή Καλλιτεχνικό Σχολείο, δεν εξετάζεται η αίτηση για Διαπολιτισμικό. Αν ικανοποιηθεί αίτηση σε Διαπολιτισμικό, δεν λαμβάνονται υπόψη οι άλλες αιτήσεις.</p>
        <p class="small-note"><strong>Μετάθεση προς Γενική μέσα στην ίδια περιοχή.</strong> Εκπαιδευτικός με οργανική σε Διαπολιτισμικό που ζητά σχολείο Γενικής στην ίδια περιοχή υποβάλλει αίτηση «από περιοχή σε περιοχή» και όχι βελτίωσης.</p>
      </aside>

      <div id="transferMsdCard">
      <?php calculatorCardStart(array('title' => 'Β. Μονάδες μετάθεσης')); ?>
        <div id="transferNumericalInputs">
        <div class="field" style="margin-bottom:12px">
          <label for="serviceMode">Καταχώριση συνολικής υπηρεσίας</label>
          <select id="serviceMode">
            <option value="points" selected>Απευθείας καταχώριση μορίων προϋπηρεσίας (προεπιλογή)</option>
            <option value="direct">Απευθείας καταχώριση ετών, μηνών, ημερών</option>
            <option value="periods">Υπολογισμός από επιμέρους περιόδους υπηρεσίας</option>
          </select>
          <?php transferServiceHelp('secondary', 'service'); ?>
        </div>
        <div id="servicePointsDirectPanel">
          <div class="field">
            <label for="serviceDirectPoints">Μόρια συνολικής υπηρεσίας</label>
            <input id="serviceDirectPoints" type="text" inputmode="decimal" maxlength="6" data-transfer-direct-points-cap="125" autocomplete="off" placeholder="π.χ. 21,04" aria-describedby="serviceDirectStatus">
            <div class="field-hint" id="serviceDirectStatus" role="status" aria-live="polite"></div>
          </div>
        </div>
        <div id="serviceDirectPanel" style="display:none">
        <div class="field-grid">
          <div class="field">
            <label for="serviceYears">Συνολική αναγνωρισμένη υπηρεσία — έτη</label>
            <input id="serviceYears" type="number" min="0" max="50" step="1" value="0" inputmode="numeric">
          </div>
          <div class="field">
            <label for="serviceMonths">Επιπλέον μήνες</label>
            <input id="serviceMonths" type="number" min="0" max="11" step="1" value="0" inputmode="numeric">
          </div>
          <div class="field">
            <label for="serviceDays">Επιπλέον ημέρες <small>15+ → 1 μήνας</small></label>
            <input id="serviceDays" type="number" min="0" max="29" step="1" value="0" inputmode="numeric">
          </div>
        </div>
        </div>
        <div id="servicePeriodsPanel" style="display:none">
          <p class="small-note">Προσθέστε τις <strong>διακριτές, μη επικαλυπτόμενες</strong> περιόδους που αναγνωρίζονται για συνολική υπηρεσία. Μην καταχωρίζετε ξανά παράλληλη υπηρέτηση για τις ίδιες ημερομηνίες. Επιβεβαιώστε το αποτέλεσμα από το υπηρεσιακό μητρώο.</p>
          <div id="servicePeriodRows"></div>
          <button type="button" class="add-row" id="addServicePeriod">+ Προσθήκη περιόδου υπηρεσίας</button>
          <p id="servicePeriodStatus" class="field-hint" role="status" aria-live="polite"></p>
        </div>
        </div>

        <div class="field" style="margin-bottom:14px">
          <label for="msdMode">Τρόπος καταχώρισης Μ.Σ.Δ.</label>
          <select id="msdMode">
            <option value="direct" selected>Απευθείας καταχώριση συνολικών μορίων Μ.Σ.Δ.</option>
            <option value="periods">Υπολογισμός από τις υπηρετήσεις μου</option>
          </select>
          <?php transferServiceHelp('secondary', 'msd'); ?>
        </div>
        <div id="msdPeriodsPanel" style="display:none">
          <p class="small-note">Προσθέστε μία γραμμή για κάθε διαφορετική υπηρέτηση/κατηγορία. Οι ημέρες ≥15 λογίζονται ως πλήρης μήνας. Σε παράλληλη υπηρεσία μοιράστε τις ημέρες ανά εβδομάδα (άθροισμα έως 5/5 <em>στην ίδια χρονική περίοδο</em>). Μην προσθέτετε δύο πλήρεις πενθήμερες υπηρετήσεις για τις ίδιες ημέρες. Αν την ίδια ημέρα υπηρετήσατε σε δύο σχολεία, λαμβάνεται υπόψη το σχολείο με τις περισσότερες μονάδες. Για σύνθετα επικαλυπτόμενα διαστήματα προτιμήστε τα επίσημα συνολικά Μ.Σ.Δ. του Μητρώου.</p>
          <div id="msdRows"></div>
          <button class="add-row" id="addMsdRow" type="button">+ Προσθήκη υπηρέτησης</button>

        <?php calculatorDisclosure(array(
          'summary' => 'Πότε διπλασιάζονται οι Μ.Σ.Δ. απομακρυσμένων σχολείων;',
          'html' => '<strong>Διπλασιασμός απομακρυσμένων:</strong> από το 2018–2019 αφορά πραγματική υπηρεσία σε σχολεία κατηγοριών Ι΄, ΙΑ΄, ΙΒ΄ και ΙΓ΄, όταν έχει συμπληρωθεί τουλάχιστον διετής συνεχόμενη υπηρεσία. Στον υπολογιστή ενεργοποίησέ τον μόνο όταν το δικαίωμα έχει πράγματι θεμελιωθεί.',
          'open' => true,
          'attrs' => array('data-mobile-collapsed' => 'true')
        )); ?>
        </div>
        <div id="msdDirectPanel">
          <div class="field">
            <label for="msdDirectPoints">Συνολικές μονάδες συνθηκών διαβίωσης από το επίσημο μητρώο</label>
            <input id="msdDirectPoints" type="text" inputmode="decimal" maxlength="7" data-transfer-direct-points-cap="1000" autocomplete="off" placeholder="π.χ. 48,75" aria-describedby="msdDirectStatus">
            <div class="field-hint" id="msdDirectStatus" role="status" aria-live="polite"></div>
          </div>
        </div>
      <?php calculatorCardEnd(); ?>
      </div>

      <div id="transferSocialCriteriaCard">
        <?php transferSocialCriteriaCard('secondary'); ?>
      </div>

      <?php calculatorActions(array(
        array('id' => 'resetBtn', 'class' => 'secondary', 'label' => 'Καθαρισμός')
      )); ?>
      <?php calculatorDisclosure(array(
        'id' => 'transferDeadlines',
        'class' => 'transfer-deadlines',
        'summary_html' => 'Σημαντικές προθεσμίες: <strong>Αιτήσεις έως 02/11/2026</strong>',
        'html' => '<p><strong>Ηλεκτρονική αίτηση: 16/10–02/11/2026.</strong> Η προσωρινή αποθήκευση δεν είναι οριστικοποίηση. Κρατήστε το PDF της οριστικοποιημένης αίτησης με τον αριθμό πρωτοκόλλου.</p>'
          . '<p><strong>Ανάκληση αίτησης:</strong> μέσω <a href="https://teachers.minedu.gov.gr/" target="_blank" rel="noopener noreferrer">teachers.minedu.gov.gr ↗</a> έως <strong>31/12/2026, ώρα 15:00</strong>. Η αίτηση ακυρώνεται οριστικά και δεν ενεργοποιείται ή υποβάλλεται εκ νέου. <strong>Ανάκληση ήδη πραγματοποιημένης μετάθεσης δεν προβλέπεται.</strong></p>'
          . '<p><strong>Αμοιβαίες μεταθέσεις:</strong> αίτηση εντός 15 ημερών από την ανακοίνωση των μεταθέσεων.</p>'
      )); ?>

      <section id="transferDocumentsPanel" class="card">
        <h2 style="margin-top:0">Δ. Προσωπική λίστα δικαιολογητικών</h2>
        <div id="transferDocumentActionSummary" class="transfer-doc-action-summary" role="note" hidden></div>
        <p class="small-note">Η λίστα προσαρμόζεται στις επιλογές σας. Μόνο οι ενέργειες που πρέπει να ετοιμάσετε έχουν checkbox· οι υπηρεσιακοί έλεγχοι και τα αυτεπαγγέλτως αναζητούμενα εμφανίζονται ενημερωτικά.</p>
        <p id="transferDocumentProgress" class="edu-transfer-doc__progress" role="status" aria-live="polite" aria-atomic="true"></p>
        <div id="transferDocumentChecklist" aria-label="Προσωποποιημένη λίστα ελέγχου δικαιολογητικών"></div>
        <button type="button" class="secondary" id="transferPrintChecklist">Εκτύπωση λίστας δικαιολογητικών</button>
      </section>
      <section class="transfer-email-feature" id="transferEmailFeature" aria-labelledby="transferEmailFeatureTitle">
        <h3 id="transferEmailFeatureTitle">Προετοιμασία μηνύματος υποβολής δικαιολογητικών</h3>
        <p class="transfer-email-feature__lead" id="transferEmailFeatureLead">Ετοιμάστε θέμα και κείμενο μηνύματος για τα δικαιολογητικά που απαιτούν αποστολή. Ο τρόπος υποβολής προσαρμόζεται στην αίτησή σας.</p>
        <button type="button" id="transferEmailFeatureOpen" class="transfer-email-feature__cta" aria-controls="transferSubmissionGuidance" aria-expanded="false">Δημιουργία μηνύματος υποβολής ↓</button>
        <p class="transfer-email-feature__helper" id="transferEmailFeatureHelper">Το μήνυμα δημιουργείται στη συσκευή σας· δεν αποστέλλεται από την Εργαλειοθήκη.</p>
        <?php calculatorDisclosure(array(
          'id' => 'transferSubmissionGuidance',
          'class' => 'transfer-email-feature__disclosure',
          'summary' => 'Πού αποστέλλονται τα δικαιολογητικά; — Οδηγίες και υπόδειγμα',
          'html' => '<div id="transferSubmissionGuidanceBody" class="transfer-submission-content">Η διαδρομή email ή mydocs εξαρτάται από τον τύπο αίτησης και το δικαιολογητικό. Δείτε τις προσωπικές οδηγίες και το υπόδειγμα μηνύματος.</div>'
        )); ?>
      </section>
      <?php calculatorResultMessage(array(
        'variant' => 'disclaimer',
        'html' => '<strong>Τι περιλαμβάνει:</strong> υπολογίζει πληροφοριακά τα βασικά μόρια και παρέχει <strong>προέλεγχο</strong> θεμελίωσης για ορισμένες περιπτώσεις ΕΑΕ/Μουσικών/Καλλιτεχνικών/Διαπολιτισμικών. Δεν πιστοποιεί δικαίωμα μετάθεσης, σειρά κατάταξης, όλες τις εξαιρέσεις ή ειδικές κατηγορίες προτεραιότητας. Οι υπηρεσιακές πράξεις και η νεότερη εγκύκλιος υπερισχύουν.'
      )); ?>
    <?php calculatorMainEnd(); ?>

    <div id="transferScoringResults">
    <?php require_once __DIR__ . '/includes/components/transfer-score-card.php'; ?>
    <?php calculatorResultsStart(array('aria_live' => 'polite')); ?>
      <?php transferScoreCard('secondary'); ?>
    <?php calculatorResultsEnd(); ?>
    </div>
  <?php calculatorColumnsEnd(); ?>
<?php calculatorContainerEnd(); ?>

<?php sourceCardStart(); ?>
  <p>Οι λίστες δικαιολογητικών είναι οδηγός προετοιμασίας και απαιτούν επαλήθευση με την εγκύκλιο της αντίστοιχης περιόδου αιτήσεων. Οι οδηγίες δικαιολογητικών και οι βασικές προϋποθέσεις επικαιροποιήθηκαν βάσει της εγκυκλίου μεταθέσεων Δ.Ε. 2026–2027 (138014/Ε2/15-10-2026). Ο αριθμητικός υπολογισμός παραμένει ο υφιστάμενος, βάσει του π.δ. 50/1996, και πρέπει να επιβεβαιώνεται με το επίσημο μητρώο.</p>
  <?php sourceCardLinksStart(); ?>
    <?php sourceCardLink('https://diavgeia.gov.gr/doc/ΨΚΩΖ46ΝΚΠΔ-63Κ?inline=true', 'Διαύγεια — Εγκύκλιος μεταθέσεων Δ.Ε. 2026–2027 · 138014/Ε2 ↗'); ?>
    <?php sourceCardLink('https://www.minedu.gov.gr/site/71035-15-10-26-metatheseis-ekpaideutikon-deuterobathmias-ekpaideuses-scholikou-etous-2026-2027', 'ΥΠΑΙΘΑ — Εγκύκλιος μεταθέσεων Δ.Ε. 2026–2027 · 138014/Ε2 ↗'); ?>
    <?php sourceCardLink('https://www.minedu.gov.gr/monimoi-metatakseis-metatheseis-apospaseis', 'ΥΠΑΙΘΑ — Νομοθεσία κινητικότητας / Π.Δ. 50/1996 ↗'); ?>
    <?php sourceCardLink('https://www.minedu.gov.gr/kinitikotita/metatheseis-egkyklioi-proskliseis', 'ΥΠΑΙΘΑ — Τρέχουσες εγκύκλιοι μεταθέσεων ↗'); ?>
    <?php sourceCardLink('https://teachers.minedu.gov.gr/', 'Online Σύστημα Μητρώου & Αιτήσεων Μετάθεσης ↗'); ?>
  <?php sourceCardLinksEnd(); ?>
  <?php sourceCardDisclaimerStart(); ?>Το αποτέλεσμα είναι ενημερωτικό. Οι καταχωρισμένες υπηρετήσεις και οι μονάδες στο επίσημο Μητρώο, καθώς και οι αποφάσεις της αρμόδιας Διεύθυνσης Εκπαίδευσης, υπερισχύουν.<?php sourceCardDisclaimerEnd(); ?>
<?php sourceCardEnd(); ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
<script src="<?php echo htmlspecialchars(edu_asset_url('includes/transfer-calculations.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
<script src="<?php echo htmlspecialchars(edu_asset_url('assets/common.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
<!-- One shared specialty-label registry, reused by the intercultural vacancy selector. -->
<script>
window.EducationTransferTeacherSpecialtyLabels = Object.freeze(<?php echo json_encode($transferSpecialtyLabels, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>);
</script>
<script src="<?php echo htmlspecialchars(edu_asset_url('includes/transfer-artistic-vacancies.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
<script src="<?php echo htmlspecialchars(edu_asset_url('includes/transfer-intercultural-vacancies.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
<script src="<?php echo htmlspecialchars(edu_asset_url('includes/transfer-html-escape.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
<script src="<?php echo htmlspecialchars(edu_asset_url('includes/transfer-eligibility.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
<script src="<?php echo htmlspecialchars(edu_asset_url('includes/transfer-email-component.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
<script src="<?php echo htmlspecialchars(edu_asset_url('includes/transfer-coservice-resources.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
<script src="<?php echo htmlspecialchars(edu_asset_url('includes/transfer-locality-resources.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
<script src="<?php echo htmlspecialchars(edu_asset_url('includes/transfer-children-documents.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
<script src="<?php echo htmlspecialchars(edu_asset_url('includes/transfer-checklist.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
<script src="<?php echo htmlspecialchars(edu_asset_url('includes/transfer-checklist-print.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
<script src="<?php echo htmlspecialchars(edu_asset_url('includes/transfer-documents.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
<script src="<?php echo htmlspecialchars(edu_asset_url('includes/transfer-social-input.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
<script src="<?php echo htmlspecialchars(edu_asset_url('includes/transfer-score-card.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
<script src="<?php echo htmlspecialchars(edu_asset_url('includes/transfer-ui.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
</body>
</html>
