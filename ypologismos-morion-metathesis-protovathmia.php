<?php
/**
 * Primary transfer toolkit with advisory eligibility checks (2026–2027).
 * Isolated UI/rules; shares the proven EducationTransfer scoring module.
 * Do not route Primary applicants into the Secondary-specific document/eligibility UI.
 */
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/teacher-specialties.php';
// Only the branches relevant to Primary teacher applications, not a list of EAE eligibility.
$primaryTransferBranchCodes = array('ΠΕ05', 'ΠΕ06', 'ΠΕ07', 'ΠΕ08', 'ΠΕ11', 'ΠΕ60', 'ΠΕ61',
    'ΠΕ70', 'ΠΕ71', 'ΠΕ73', 'ΠΕ79.01', 'ΠΕ86', 'ΠΕ91.01', 'ΠΕ91.02');
$primaryTransferSpecialtyLabels = array();
$registry = teacherSpecialties();
foreach ($primaryTransferBranchCodes as $code) {
    if (isset($registry[$code]['label'])) $primaryTransferSpecialtyLabels[$code] = (string) $registry[$code]['label'];
}
?>
<!DOCTYPE html>
<html lang="el">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <?php require __DIR__ . '/includes/head-pwa.php'; ?>
  <title>Μεταθέσεις εκπαιδευτικών Πρωτοβάθμιας — Γενική, ΕΑΕ, Διαπολιτισμικά και ΠΕ73</title>
  <link rel="stylesheet" href="<?php echo htmlspecialchars(edu_asset_url('assets/common.css'), ENT_QUOTES, 'UTF-8'); ?>">
  <link rel="stylesheet" href="<?php echo htmlspecialchars(edu_asset_url('assets/transfer-primary.css'), ENT_QUOTES, 'UTF-8'); ?>">
  <link rel="stylesheet" href="<?php echo htmlspecialchars(edu_asset_url('assets/transfer-level-switch.css'), ENT_QUOTES, 'UTF-8'); ?>">
</head>
<body class="edu-ui edu-calc-standard edu-page-transfer">
<?php require __DIR__ . '/includes/header.php'; ?>
<?php require_once __DIR__ . '/includes/components/calculator-layout.php'; ?>
<?php require_once __DIR__ . '/includes/components/transfer-social-criteria.php'; ?>
<?php require_once __DIR__ . '/includes/components/transfer-service-help.php'; ?>
<?php calculatorContainerStart(array('class'=>'app')); ?>
  <?php calculatorHero(array(
    'title'=>'Μεταθέσεις μόνιμων εκπαιδευτικών Πρωτοβάθμιας',
    'intro_html'=>'Υπολογισμός μορίων και οδηγός δικαιολογητικών για <strong>γενικές μεταθέσεις, ΕΑΕ/ΚΕΔΑΣΥ, Διαπολιτισμικά, Μειονοτικά ΠΕ73</strong> και βελτίωση ή οριστική τοποθέτηση.',
    'badges'=>array('Εγκύκλιος Π.Ε. 138023/Ε2/15-10-2026', 'Μεταθέσεις και τοποθετήσεις 2026–2027')
  )); ?>
  <nav class="transfer-level-switch" aria-label="Επιλογή βαθμίδας">
    <strong aria-current="page">Πρωτοβάθμια</strong>
    <a href="ypologismos-morion-metathesis.php">Δευτεροβάθμια</a>
  </nav>
  <p class="small-note primary-transfer-guidance"><strong>Σημαντικό:</strong> Οι υπολογισμοί και οι προέλεγχοι είναι συμβουλευτικοί. Για την τελική επιβεβαίωση προϋποθέσεων, μορίων και δικαιολογητικών ισχύουν η εγκύκλιος 2026–2027 και ο έλεγχος της ΔΠΕ οργανικής.</p>
  <?php calculatorColumnsStart(); ?>
    <?php calculatorMainStart(); ?>
      <?php calculatorCardStart(array('title'=>'Α. Τύπος διαδικασίας και υπηρεσιακά στοιχεία')); ?>
        <div class="field"><label for="primaryMode">Τύπος αίτησης</label>
          <select id="primaryMode">
            <option value="region" data-official-code="001.ΠΕ001">Γενική από περιοχή σε περιοχή [001.ΠΕ001]</option>
            <option value="eae" data-official-code="001.ΣΜΚΔ001">ΣΜΕΑΕ / ΕΕΕΕΚ [001.ΣΜΚΔ001]</option>
            <option value="sensory" data-official-code="001.ΣΜΕΑ003">Σχολεία Κωφών / Τυφλών [001.ΣΜΕΑ003]</option>
            <option value="kedasy" data-official-code="001.ΣΜΚΔ001.ΚΔΣ">ΚΕΔΑΣΥ [001.ΣΜΚΔ001.ΚΔΣ]</option>
            <option value="intercultural" data-official-code="002.ΔΙΑΠ001">Διαπολιτισμικά Σχολεία [002.ΔΙΑΠ001]</option>
            <option value="minority">Μειονοτικά Σχολεία — κλάδος ΠΕ73 (Θράκη)</option>
            <option value="placement-general" data-official-code="003.ΠΕΒ01">Βελτίωση / Οριστική τοποθέτηση Γενικής [003.ΠΕΒ01]</option>
            <option value="placement-eae" data-official-code="003.ΠΕΒ01.ΕΙΔ">Βελτίωση / Οριστική τοποθέτηση ΕΑΕ [003.ΠΕΒ01.ΕΙΔ]</option>
          </select>
        </div>
        <div class="field" id="primaryDestinationField" hidden>
          <label for="primaryDestination">Ειδικός προορισμός για προέλεγχο</label>
          <select id="primaryDestination"></select>
        </div>
        <?php calculatorDisclosure(array(
          'id' => 'primaryMultipleApplicationsHelp',
          'summary' => 'Μπορώ να υποβάλω περισσότερες από μία αιτήσεις μετάθεσης;',
          'html' => '<p><strong>Ναι</strong>, εφόσον πληροίτε τις προϋποθέσεις κάθε διαδικασίας. Μπορείτε να υποβάλετε χωριστές αιτήσεις, για παράδειγμα για ΕΑΕ/ΚΕΔΑΣΥ, Διαπολιτισμικά ή μετάθεση από περιοχή σε περιοχή.</p>'
            . '<p>Οι μεταθέσεις εξετάζονται με τη σειρά: <strong>ΚΕΔΑΣΥ και περιοχές ΕΑΕ → Διαπολιτισμικά → από περιοχή σε περιοχή → αμοιβαίες</strong>. Αν ικανοποιηθεί αίτηση προηγούμενης κατηγορίας, η αίτηση επόμενης κατηγορίας δεν εξετάζεται.</p>'
            . '<p><strong>Προσοχή:</strong> Η οριστική τοποθέτηση/βελτίωση είναι διαφορετικός τύπος αίτησης. Εκπαιδευτικοί με οργανική σε ΕΑΕ ή Διαπολιτισμικό Σχολείο που ζητούν μετάθεση σε σχολείο Γενικής της ίδιας περιοχής υποβάλλουν αίτηση <strong>«από περιοχή σε περιοχή»</strong> και όχι βελτίωσης, με τις προβλεπόμενες υπηρεσιακές προϋποθέσεις.</p>'
            . '<p><strong>Αμοιβαίες μεταθέσεις:</strong> πρόκειται για χωριστή διαδικασία, με αίτηση μέσα σε <strong>15 ημέρες από την ανακοίνωση των μεταθέσεων</strong>. Τα Μειονοτικά Σχολεία ΠΕ73 έχουν ιδιαίτερη διαδικασία, η οποία καλύπτεται με χωριστό ενδεικτικό προέλεγχο. Η εφαρμογή δεν πιστοποιεί κενές οργανικές θέσεις ή τον τύπο ηλεκτρονικής αίτησης.</p>'
            . '<p class="small-note">Πηγή: <a href="https://www.minedu.gov.gr/site/71034-15-10-26-metatheseis-ekpaideutikon-protobathmias-ekpaideuses-scholikou-etous-2026-2027" target="_blank" rel="noopener noreferrer">Εγκύκλιος Π.Ε. 138023/Ε2/15-10-2026 ↗</a>, Μέρος Α΄ Κεφ. Α΄ §1.1 και Μέρος Γ΄ Κεφ. Δ΄ §4, Κεφ. Ζ΄ §9.</p>'
        )); ?>
        <section id="primaryMinorityFields" class="primary-transfer-notice" hidden aria-label="Προέλεγχος μεταθέσεων μειονοτικού προγράμματος ΠΕ73">
          <h3>Μειονοτικά Σχολεία — κλάδος ΠΕ73</h3>
          <p>Πρόκειται <strong>μόνο για το μειονοτικό πρόγραμμα</strong> των Μειονοτικών Σχολείων Θράκης. Δεν είναι η γενική διαδικασία εκπαιδευτικών του ελληνόγλωσσου προγράμματος.</p>
          <div class="field"><label for="primaryMinorityBranch">Υπηρεσιακός κλάδος / πρόγραμμα</label>
            <select id="primaryMinorityBranch">
              <option value="unknown">Δεν έχει επιβεβαιωθεί</option>
              <option value="pe73">ΠΕ73 — Μειονοτικό πρόγραμμα μειονοτικών σχολείων</option>
              <option value="other">Άλλος κλάδος ή ελληνόγλωσσο πρόγραμμα</option>
            </select>
          </div>
          <div class="field"><label for="primaryMinorityRoute">Διαδικασία</label>
            <select id="primaryMinorityRoute">
              <option value="between">Μετάθεση μεταξύ περιοχών μετάθεσης Θράκης</option>
              <option value="within">Βελτίωση θέσης μέσα στον νομό οργανικής</option>
            </select>
          </div>
          <div class="field"><label for="primaryMinorityOrigin">Περιφερειακή Ενότητα οργανικής θέσης</label>
            <select id="primaryMinorityOrigin">
              <option value="">Επιλέξτε Περιφερειακή Ενότητα</option>
              <option value="xanthi">Ξάνθη</option><option value="rodopi">Ροδόπη</option><option value="evros">Έβρος</option>
            </select>
          </div>
          <div id="primaryMinorityDestinationField" class="field">
            <label for="primaryMinorityDestination">Περιφερειακή Ενότητα προορισμού</label>
            <select id="primaryMinorityDestination">
              <option value="">Επιλέξτε Περιφερειακή Ενότητα</option>
              <option value="xanthi">Ξάνθη</option><option value="rodopi">Ροδόπη</option><option value="evros">Έβρος</option>
            </select>
          </div>
          <p id="primaryMinorityLocalityNote" class="small-note" hidden><strong>Βελτίωση εντός νομού:</strong> τα μόρια εντοπιότητας και συνυπηρέτησης εξαρτώνται από τον συγκεκριμένο δήμο και σχολείο. Δεν αποδίδονται μόρια πρώτης προτίμησης περιοχής. Η αριθμητική βαθμολογία είναι ενδεικτική.</p>
          <p id="primaryMinorityStatus" class="primary-eae-status" data-status="review" role="status" aria-live="polite"></p>
          <p class="small-note"><strong>Αρμοδιότητα:</strong> για μετάθεση από νομό σε νομό ΑΠΥΣΠΕ Ανατολικής Μακεδονίας–Θράκης· για βελτίωση εντός νομού το αρμόδιο ΠΥΣΠΕ. Δεν επινοείται ηλεκτρονικός κωδικός αίτησης: επιβεβαιώστε τον τύπο που εμφανίζεται στην <a href="https://teachers.minedu.gov.gr/" target="_blank" rel="noopener noreferrer">επίσημη εφαρμογή ↗</a>. Πηγή: εγκύκλιος Π.Ε. 138023/Ε2/15-10-2026, Μέρος Τρίτο, Κεφ. Ε΄.</p>
        </section>
        <div id="primaryGeneralOrganicField" class="field"><label for="primaryOrganic">Υπηρέτηση οργανικής θέσης έως 31/08/2027</label>
          <select id="primaryOrganic">
            <option value="unknown">Δεν γνωρίζω / απαιτείται έλεγχος</option>
            <option value="yes">Συμπληρώνω ένα έτος στην οργανική</option>
            <option value="no">Δεν συμπληρώνω ένα έτος / επικαλούμαι ειδική περίπτωση</option>
          </select>
        </div>
        <p id="primaryEligibility" class="primary-transfer-info" role="status" aria-live="polite"></p>
        <section id="primaryReturnFromEae" class="primary-transfer-notice" hidden aria-label="Έλεγχος επιστροφής από ΕΑΕ στη Γενική">
          <h3>Επιστροφή από ΕΑΕ στη Γενική Εκπαίδευση</h3>
          <div class="field"><label for="primaryReturnPath">Υπηρεσιακή προέλευση</label>
            <select id="primaryReturnPath"><option value="none">Δεν ζητώ επιστροφή από ΕΑΕ</option><option value="transferred">Διορίστηκα στη Γενική και μετατέθηκα σε ΕΑΕ</option><option value="appointed">Διορίστηκα αρχικά στην ΕΑΕ</option></select>
          </div>
          <div id="primaryReturnYearsField" class="field" hidden><label for="primaryReturnYears">Διδακτικά έτη υπηρεσίας στην ΕΑΕ από την αντίστοιχη μετάθεση / τον διορισμό</label><input type="number" id="primaryReturnYears" min="0" max="50" step="0.01" placeholder="π.χ. 5"></div>
          <div id="primaryReturnAreaYearsField" class="field" hidden><label for="primaryReturnAreaYears">Από αυτά, έτη στην περιοχή αρχικού διορισμού ΕΑΕ</label><input type="number" id="primaryReturnAreaYears" min="0" max="50" step="0.01" placeholder="π.χ. 2"></div>
          <p id="primaryReturnStatus" class="primary-transfer-info" role="status" aria-live="polite"></p>
          <p class="small-note">Εγκύκλιος Π.Ε. 138023/Ε2/2026, Μέρος Τρίτο, κεφ. ΣΤ΄ §2: απαιτείται διετία από μετάθεση σε ΕΑΕ και υπηρέτηση οργανικής ή πενταετία στην ΕΑΕ (με διετία στην περιοχή διορισμού) για αρχικό διορισμό στην ΕΑΕ. Ακόμη και για σχολείο Γενικής στην ίδια περιοχή, ακολουθείται αίτηση από περιοχή σε περιοχή.</p>
        </section>
        <p id="primaryPlacementNotice" class="primary-transfer-notice" hidden>Βελτίωση / οριστική τοποθέτηση: κατά την προθεσμία 16/10–02/11/2026 υποβάλλεται η αίτηση <strong>χωρίς προτιμήσεις σχολείων</strong>. Οι προτιμήσεις δηλώνονται σε μεταγενέστερο στάδιο που ανακοινώνεται από τη ΔΠΕ. Η εντοπιότητα και η συνυπηρέτηση επαληθεύονται για τον συγκεκριμένο προορισμό· η συνολική βαθμολογία είναι ενδεικτική. Το πεδίο «πρώτη προτίμηση περιοχής» δεν εφαρμόζεται εδώ.</p>
        <section id="primaryEaeRouteFields" class="primary-transfer-notice" hidden aria-label="Ειδικός προέλεγχος διαδρομής ΕΑΕ">
          <h3>Διαδρομή μετάθεσης ΕΑΕ — σωστός τύπος αίτησης</h3>
          <div class="field"><label for="primaryEaeMoveRoute">Από πού προς πού ζητάτε μετακίνηση;</label>
            <select id="primaryEaeMoveRoute">
              <option value="unknown">Επιλέξτε υπηρεσιακή διαδρομή</option>
              <option value="general-same">Γενική → ΕΑΕ, ίδια περιοχή μετάθεσης</option>
              <option value="general-other">Γενική → ΕΑΕ, άλλη περιοχή μετάθεσης</option>
              <option value="special-same">ΣΜΕΑΕ/ΕΕΕΕΚ/ΤΕ → ΣΜΕΑΕ/ΕΕΕΕΚ/ΤΕ, ίδια περιοχή</option>
              <option value="special-other">Δομή ΕΑΕ → δομή ΕΑΕ, άλλη περιοχή</option>
              <option value="kedasy-to-kedasy">ΚΕΔΑΣΥ → ΚΕΔΑΣΥ, ίδια ή άλλη Περιφερειακή Διεύθυνση</option>
              <option value="deaf-exchange">ΣΜΕΑΕ ↔ Σχολείο Κωφών/Τυφλών, ίδια ή άλλη περιοχή</option>
              <option value="other">Άλλη / σύνθετη διαδρομή (έλεγχος ΔΠΕ)</option>
            </select>
          </div>
          <div class="field"><label for="primaryEaeDisability80">Ειδική εξαίρεση λόγω αναπηρίας ιδίου ≥80% εφ’ όρου ζωής</label>
            <select id="primaryEaeDisability80">
              <option value="none">Δεν ζητώ την εξαίρεση αυτή</option>
              <option value="lifetime80">Εξετάζω την εξαίρεση με το προβλεπόμενο πιστοποιητικό</option>
              <option value="review">Χρειάζομαι διευκρίνιση για την ειδική κατηγορία</option>
            </select>
            <p class="field-hint">Η επιλογή παρέχει οδηγίες, δεν διαπιστώνει αναπηρία ή δικαίωμα προτεραιότητας. Δεν αποθηκεύεται στην εφαρμογή.</p>
          </div>
          <div id="primaryEaeRouteStatus" class="primary-eae-status" role="status" aria-live="polite" data-status="review"></div>
          <p class="small-note">Εγκύκλιος Π.Ε. 138023/Ε2/2026, Μέρος Τρίτο, Κεφ. ΣΤ΄ §7 και Κεφ. Γ΄: οι μετακινήσεις εντός περιοχής δεν ακολουθούν όλες τον ίδιο τύπο αίτησης.</p>
        </section>
        <div id="primaryEaeFields" hidden>
          <h3>Β.1 Προέλεγχος δικαιώματος μετάθεσης στην ΕΑΕ</h3>
          <div class="field"><label for="primaryEaeBranch">Κλάδος εκπαιδευτικού</label>
            <select id="primaryEaeBranch">
              <option value="">Επιλέξτε κλάδο</option>
              <option value="ΠΕ60">ΠΕ60 — Νηπιαγωγών</option><option value="ΠΕ61">ΠΕ61 — Νηπιαγωγών ΕΑΕ</option>
              <option value="ΠΕ70">ΠΕ70 — Δασκάλων</option><option value="ΠΕ71">ΠΕ71 — Δασκάλων ΕΑΕ</option>
              <option value="ΠΕ11">ΠΕ11 — Φυσικής Αγωγής</option><option value="ΠΕ06">ΠΕ06 — Αγγλικής</option>
              <option value="ΠΕ79.01">ΠΕ79.01 — Μουσικής</option><option value="ΠΕ86">ΠΕ86 — Πληροφορικής</option>
              <option value="άλλος">Άλλος κλάδος (υπηρεσιακή επαλήθευση)</option>
            </select>
          </div>
          <div class="field"><label for="primaryEaeOrigin">Προέλευση / αρχικός διορισμός</label>
            <select id="primaryEaeOrigin">
              <option value="unknown">Επιλέξτε υπηρεσιακή διαδρομή</option>
              <option value="general">Διορισμός στη Γενική → αίτηση προς ΕΑΕ</option>
              <option value="appointed-eae">Διορισμός απευθείας στην ΕΑΕ</option>
              <option value="transferred-eae">Ήδη σε ΕΑΕ από προηγούμενη μετάθεση</option>
            </select>
          </div>
          <div id="primaryEaeGeneralYearsField" class="field" hidden>
            <label for="primaryEaeGeneralYears">Διδακτικά έτη από διορισμό στη Γενική (0–50)</label>
            <input id="primaryEaeGeneralYears" type="number" min="0" max="50" step="0.01" placeholder="π.χ. 3">
          </div>
          <div id="primaryEaeAppointmentYearsField" class="field" hidden>
            <label for="primaryEaeAppointmentYears">Έτη στην περιοχή διορισμού ΕΑΕ (0–50)</label>
            <input id="primaryEaeAppointmentYears" type="number" min="0" max="50" step="0.01" placeholder="π.χ. 2">
          </div>
          <div class="field"><label for="primaryEaeOrganic">Έχει υπηρετηθεί η οργανική θέση;</label>
            <select id="primaryEaeOrganic"><option value="unknown">Χρειάζεται επαλήθευση</option><option value="yes">Ναι</option><option value="no">Όχι</option></select>
          </div>
          <fieldset class="primary-eae-qualifications"><legend><strong>Προσόντα εξειδίκευσης ΕΑΕ</strong> — σημειώστε όσα διαθέτετε</legend>
            <label><input type="checkbox" id="primaryEaePhd"> Διδακτορικό στην ΕΑΕ / Σχολική Ψυχολογία</label>
            <label><input type="checkbox" id="primaryEaeMasters"> Μεταπτυχιακό στην ΕΑΕ / Σχολική Ψυχολογία</label>
            <label><input type="checkbox" id="primaryEaeRetraining"> Διετής μετεκπαίδευση στην ΕΑΕ των Διδασκαλείων</label>
            <label><input type="checkbox" id="primaryEaeExperience"> Πτυχίο ΑΕΙ + αποδεδειγμένη πενταετής υπηρεσία στην ΕΑΕ</label>
            <label><input type="checkbox" id="primaryEaePe11"> Πτυχίο ΤΕΦΑΑ με προβλεπόμενη κύρια ειδικότητα ΕΑΕ (ΠΕ11)</label>
          </fieldset>
          <div id="primaryEaeYearsField" class="field" hidden>
            <label for="primaryEaeYears">Αποδεδειγμένα διδακτικά έτη ΕΑΕ για διαδρομή πενταετίας (0–50)</label>
            <input id="primaryEaeYears" type="number" min="0" max="50" step="0.01" placeholder="π.χ. 5">
          </div>
          <div id="primaryEaeCertificationField" class="field" hidden>
            <label for="primaryEaeCertification" id="primaryEaeCertificationLabel">Πιστοποιημένη γνώση ΕΝΓ / Braille</label>
            <select id="primaryEaeCertification">
              <option value="unknown">Δεν έχει επιβεβαιωθεί η αναγκαία πιστοποίηση</option>
              <option value="yes">Ναι — διαθέτω αναγνωρισμένη πιστοποίηση</option>
              <option value="no">Όχι — δεν διαθέτω πιστοποιημένη γνώση</option>
            </select>
            <p class="field-hint">Δεν αρκεί απλή βεβαίωση παρακολούθησης. Η αναγνώριση του αποδεικτικού ελέγχεται από την υπηρεσία.</p>
          </div>
          <div id="primaryEaeCertificationDossierField" class="field" hidden>
            <label for="primaryEaeCertificationDossier">Υποβολή αποδεικτικού ΕΝΓ / Braille</label>
            <select id="primaryEaeCertificationDossier">
              <option value="unknown">Χρειάζεται έλεγχος αν υπάρχει ήδη το αποδεικτικό στον φάκελο</option>
              <option value="new">Νέα ή συμπληρωματική υποβολή μέσω mydocs</option>
              <option value="previous">Το αναγνωρισμένο αποδεικτικό υπάρχει ήδη στον σχετικό φάκελο</option>
            </select>
          </div>
          <div class="field" id="primaryPreviousDossierField">
            <label for="primaryEaeDossier">Φάκελος προσόντων ΕΑΕ</label>
            <select id="primaryEaeDossier">
              <option value="unknown">Δεν γνωρίζω αν χρειάζεται νέος φάκελος</option>
              <option value="previous">Έχω υποβάλει φάκελο 2025–26 (μετάθεση) ή 2026 (απόσπαση) χωρίς νέα στοιχεία</option>
              <option value="new">Πρώτη υποβολή ή νέα/συμπληρωματικά στοιχεία</option>
            </select>
          </div>
          <div id="primaryEaeEligibility" class="primary-eae-status" role="status" aria-live="polite" data-status="review"></div>
          <details><summary>Προσόντα και έλεγχος: νομική βάση</summary>
            <p>Εγκύκλιος 138023/Ε2/15-10-2026, Μέρος Τρίτο, Κεφάλαιο ΣΤ΄. Η Γενική → ΕΑΕ απαιτεί τριετία από τον διορισμό και υπηρέτηση οργανικής. Ο διορισμός στην ΕΑΕ υπόκειται στη διετία της περιοχής διορισμού. Τα προσόντα τεκμηριώνονται σύμφωνα με το άρθρο 16 ν. 3699/2008.</p>
            <p>Για ΚΕΔΑΣΥ Πρωτοβάθμιας προβλέπονται ΠΕ60, ΠΕ61, ΠΕ70, ΠΕ71· για ΕΕΕΕΚ ΠΕ71 και ΠΕ70 με εξειδίκευση ΕΑΕ. Στα Σχολεία Κωφών απαιτείται πιστοποιημένη ΕΝΓ και στα Σχολεία Τυφλών πιστοποιημένη γραφή Braille (παρ. 3–5). Ο προέλεγχος δεν αποφασίζει για αναγνώριση τίτλων, συνάφεια, υπολογισμό ημερών ή εξαιρέσεις.</p>
          </details>
        </div>
        <div id="primaryInterculturalFields" hidden>
          <h3>Β.2 Προέλεγχος μετάθεσης σε Διαπολιτισμικό Σχολείο</h3>
          <p class="small-note">Εγκύκλιος 138023/Ε2/15-10-2026, Μέρος Τρίτο, Κεφ. Δ΄. Η επιλογή γίνεται βάσει προσόντων και οργανικών κενών, <strong>όχι μορίων μετάθεσης</strong>.</p>
          <div class="field"><label for="primaryInterVacancyBranch">Κλάδος / ειδικότητα — οργανικά κενά Π.Ε. 2026–2027</label>
            <select id="primaryInterVacancyBranch"><option value="">Επιλέξτε κλάδο με προκηρυγμένη οργανική θέση</option></select>
          </div>
          <div id="primaryInterVacancySchoolField" class="field" hidden><label for="primaryInterVacancySchool">Σχολείο με οργανικό κενό στον κλάδο</label>
            <select id="primaryInterVacancySchool"><option value="">Επιλέξτε σχολείο</option></select>
          </div>
          <p id="primaryInterVacancySummary" class="small-note" role="status" aria-live="polite">Επιλέξτε κλάδο και σχολείο από τα οργανικά κενά Διαπολιτισμικών Πρωτοβάθμιας.</p>
          <p class="small-note">Πηγή: «ΟΡΓΑΝΙΚΑ ΚΕΝΑ ΔΙΑΠΟΛΙΤΙΣΜΙΚΑ ΠΕ 2026–2027» (συνημμένος πίνακας): <strong>16 κενά σε 6 σχολικές μονάδες</strong>. Η κοινή γραμμή <strong>ΠΕ70+ΠΕ71 (13 κενά)</strong> δεν αθροίζεται χωριστά για κάθε κλάδο. Η γλώσσα του αρχείου είναι πληροφοριακή, όχι αυτόματη πιστοποίηση γλωσσικού προσόντος. Επαληθεύστε τυχόν μεταβολές στο <a href="https://teachers.minedu.gov.gr/" target="_blank" rel="noopener noreferrer">επίσημο σύστημα ↗</a>.</p>
          <div class="field"><label for="primaryInterOrganic">Υπηρέτηση οργανικής θέσης έως 31/08/2027</label>
            <select id="primaryInterOrganic"><option value="unknown">Χρειάζεται έλεγχος</option><option value="yes">Συμπληρώνω ένα έτος</option><option value="no">Δεν συμπληρώνω ένα έτος</option></select>
          </div>
          <div class="field"><label for="primaryInterCurrent">Ήδη υπηρετώ σε Διαπολιτισμικό Σχολείο και ζητώ άλλο</label>
            <select id="primaryInterCurrent"><option value="no">Όχι</option><option value="yes">Ναι</option></select>
          </div>
          <div class="field" id="primaryInterCurrentYearsField" hidden><label for="primaryInterCurrentYears">Έτη υπηρεσίας στο Διαπολιτισμικό (0–50)</label><input id="primaryInterCurrentYears" type="number" min="0" max="50" step="0.01" placeholder="π.χ. 2"></div>
          <div class="field"><label for="primaryInterInclusion">Η προκηρυχθείσα θέση αφορά Τμήμα Ένταξης;</label>
            <select id="primaryInterInclusion"><option value="no">Όχι</option><option value="yes">Ναι</option><option value="unknown">Δεν γνωρίζω</option></select>
          </div>
          <div class="field" id="primaryInterEaeQualificationField" hidden><label for="primaryInterEaeQualification">Προσόντα ΕΑΕ για Τμήμα Ένταξης</label>
            <select id="primaryInterEaeQualification"><option value="unknown">Δεν έχει ακόμη επιβεβαιωθεί</option><option value="yes">Διαθέτω προβλεπόμενη εξειδίκευση ΕΑΕ (προς επαλήθευση)</option><option value="no">Δεν διαθέτω προβλεπόμενη εξειδίκευση</option></select>
          </div>
          <fieldset class="primary-eae-qualifications" id="primaryInterQualFieldset"><legend><strong>Πρόσθετα προσόντα προτεραιότητας</strong> — δηλώστε μόνο όσα τεκμηριώνονται</legend>
            <label><input type="checkbox" data-primary-inter-qual="origin-language"> Γλώσσα προέλευσης πλειονότητας μαθητών (με ειδική εξαίρεση)</label>
            <label><input type="checkbox" data-primary-inter-qual="greek-second"> Διδασκαλία ελληνικής ως δεύτερης/ξένης γλώσσας</label>
            <label><input type="checkbox" data-primary-inter-qual="inter-studies"> Μετεκπαίδευση ή μεταπτυχιακές σπουδές Διαπολιτισμικής Αγωγής</label>
            <label><input type="checkbox" data-primary-inter-qual="education-studies"> Σπουδές επιστημών αγωγής</label>
            <label><input type="checkbox" data-primary-inter-qual="subject-studies"> Σπουδές στο διδακτικό αντικείμενο</label>
            <label><input type="checkbox" data-primary-inter-qual="inter-experience"> Διδακτική εμπειρία σε Διαπολιτισμικά Σχολεία</label>
            <label><input type="checkbox" data-primary-inter-qual="reception-experience"> Διδακτική εμπειρία σε Τάξεις Υποδοχής/Φροντιστηριακά Τμήματα</label>
            <label><input type="checkbox" data-primary-inter-qual="international-language"> Διεθνής γλώσσα (Αγγλικά, Γαλλικά, Γερμανικά)</label>
            <label><input type="checkbox" data-primary-inter-qual="seminars"> Προβλεπόμενα σεμινάρια Διαπολιτισμικής Εκπαίδευσης</label>
          </fieldset>
          <div class="field"><label for="primaryInterDossier">Φάκελος προσόντων για Διαπολιτισμικά</label>
            <select id="primaryInterDossier"><option value="unknown">Δεν γνωρίζω αν χρειάζεται συμπληρωματική υποβολή</option><option value="previous">Έχει ήδη υποβληθεί φάκελος 2025–26 χωρίς νέα στοιχεία</option><option value="new">Πρώτη υποβολή ή υπάρχουν νέα/συμπληρωματικά στοιχεία</option></select>
          </div>
          <div class="primary-eae-status" id="primaryInterStatus" data-status="review" role="status" aria-live="polite"></div>
          <details><summary>Νομική βάση, σειρά προσόντων και αποδεικτικά</summary>
            <p>Η εγκύκλιος απαριθμεί τα πρόσθετα προσόντα κατά σειρά προτεραιότητας. Η δήλωσή τους δεν αποτελεί αυτόματη θεμελίωση ή κατάταξη. Για σχολεία με πλειονότητα μαθητών Ρομά δεν απαιτείται γνώση ξένης γλώσσας βάσει της ειδικής εξαίρεσης. Για θέση Τμήματος Ένταξης απαιτούνται και οι προβλεπόμενες προϋποθέσεις ΕΑΕ.</p>
            <p>Για ξενόγλωσσους τίτλους: αναγνώριση και επίσημη μετάφραση. Για διδακτορικό: περίληψη διατριβής. Για μεταπτυχιακό: αναλυτική βαθμολογία και περίληψη εργασίας, εφόσον υπάρχει. Για δημοσιεύσεις: εξώφυλλο και πρώτη σελίδα. <strong>Δεν προσκομίζεται βιογραφικό σημείωμα.</strong> Ελέγξτε τα διαθέσιμα οργανικά κενά.</p>
          </details>
        </div>
        <details><summary>Σημαντικές προθεσμίες και εξαιρέσεις</summary>
          <p>Ηλεκτρονική αίτηση: <strong>16/10 έως 02/11/2026</strong> στο <a href="https://teachers.minedu.gov.gr/" target="_blank" rel="noopener noreferrer">επίσημο μητρώο</a>. Μέχρι την ίδια ημερομηνία αποστέλλονται τα συνήθη δικαιολογητικά.</p>
          <p>Ανάκληση αίτησης έως 31/12/2026. Οι αμοιβαίες μεταθέσεις ακολουθούν ξεχωριστή προθεσμία 15 ημερών από την ανακοίνωση των μεταθέσεων. Εξαιρέσεις για νεογέννητα και ειδικές κατηγορίες περιγράφονται στην εγκύκλιο.</p>
        </details>
      <?php calculatorCardEnd(); ?>
      <div id="primaryScoreMobile" class="primary-score-mobile" role="status" aria-live="polite" aria-label="Σύνοψη μορίων">
        <strong id="primaryMobileTotal">0</strong> <span id="primaryMobileLabel">γνωστές μονάδες (μερικό άθροισμα)</span>
        <p id="primaryMobileStatus">Για πλήρη βαθμολογία καταχωρίστε προϋπηρεσία και Μ.Σ.Δ.</p>
        <a href="#primaryScoreResults">Αναλυτικά μόρια ↓</a>
      </div>
      <div id="primaryScoringCard">
      <?php calculatorCardStart(array('title'=>'Β. Μονάδες μετάθεσης')); ?>
        <p class="small-note">Τα μόρια προϋπηρεσίας και οι Μ.Σ.Δ. καταχωρίζονται χωριστά όπως στο <a href="https://teachers.minedu.gov.gr/" target="_blank" rel="noopener noreferrer">επίσημο μητρώο</a>. Δεν αθροίζονται διπλά οι υπηρετήσεις.</p>
        <div class="field"><label for="primaryServiceMode">Καταχώριση συνολικής υπηρεσίας</label>
          <select id="primaryServiceMode"><option value="points">Ακριβή μόρια από επίσημο μητρώο</option><option value="direct">Έτη, μήνες, ημέρες (ενιαίο σύνολο)</option><option value="periods">Υπολογισμός από επιμέρους περιόδους υπηρεσίας</option></select>
        </div>
          <?php transferServiceHelp('primary', 'service'); ?>
        <div id="primaryServicePointsPanel" class="field"><label for="primaryServicePoints">Μόρια συνολικής υπηρεσίας</label>
          <input id="primaryServicePoints" type="text" inputmode="decimal" maxlength="6" data-primary-points-cap="125" placeholder="π.χ. 21,04" autocomplete="off">
        </div>
        <div id="primaryServiceDurationPanel" class="field-grid" hidden>
          <div class="field"><label for="primaryYears">Έτη</label><input id="primaryYears" type="number" min="0" max="50" step="1" value="0"></div>
          <div class="field"><label for="primaryMonths">Μήνες</label><input id="primaryMonths" type="number" min="0" max="11" step="1" value="0"></div>
          <div class="field"><label for="primaryDays">Ημέρες</label><input id="primaryDays" type="number" min="0" max="29" step="1" value="0"></div>
        </div>
        <div id="primaryServicePeriodsPanel" hidden>
          <p class="field-hint">Προσθέστε μόνο αναγνωρισμένες περιόδους χωρίς χρονική επικάλυψη. Τα υπόλοιπα ημερών αθροίζονται πριν εφαρμοστεί ο κανόνας των 15 ημερών. Ελέγξτε τις περιόδους στο υπηρεσιακό μητρώο.</p>
          <div id="primaryServicePeriodsRows"></div>
          <button type="button" class="add-row" id="primaryAddServicePeriod">+ Προσθήκη περιόδου υπηρεσίας</button>
          <p id="primaryServicePeriodStatus" class="field-hint" role="status" aria-live="polite"></p>
        </div>
        <div class="field"><label for="primaryMsdMode">Τρόπος καταχώρισης Μ.Σ.Δ.</label>
          <select id="primaryMsdMode"><option value="direct">Ακριβείς συνολικές Μ.Σ.Δ. από το μητρώο</option><option value="periods">Υπολογισμός από επιμέρους υπηρετήσεις</option></select>
        </div>
          <?php transferServiceHelp('primary', 'msd'); ?>
        <div id="primaryMsdDirectPanel">
        <div class="field"><label for="primaryMsd">Συνολικές Μ.Σ.Δ. από το επίσημο μητρώο</label>
          <input id="primaryMsd" type="text" inputmode="decimal" maxlength="7" data-primary-points-cap="1000" autocomplete="off" placeholder="π.χ. 48,75">
        </div>
        </div>
        <div id="primaryMsdPeriodsPanel" hidden>
          <p class="field-hint">Για κάθε υπηρέτηση δηλώστε την κατηγορία σχολείου Π.Ε. (Α΄–ΙΓ΄), τη διάρκεια και τις ημέρες ανά εβδομάδα. Οι παράλληλες υπηρετήσεις σε σχολεία της ίδιας περιόδου επιμερίζονται έως 5/5 συνολικά. Ο διπλασιασμός Ι΄–ΙΓ΄ επιλέγεται μόνο αν έχει επιβεβαιωθεί το δικαίωμα διετούς συνεχούς πραγματικής υπηρεσίας.</p>
          <div id="primaryMsdPeriodsRows"></div>
          <button type="button" class="add-row" id="primaryAddMsdPeriod">+ Προσθήκη υπηρέτησης Μ.Σ.Δ.</button>
          <p id="primaryMsdPeriodStatus" class="field-hint" role="status" aria-live="polite"></p>
        </div>
        <p id="primaryValidation" role="status" aria-live="polite" class="field-hint"></p>
      <?php calculatorCardEnd(); ?>
      </div>
      <p id="primaryInterculturalScoreNote" class="primary-transfer-info" hidden>Στα Διαπολιτισμικά δεν εμφανίζεται συγκριτική αριθμητική μοριοδότηση: ο έλεγχος αφορά τα απαιτούμενα προσόντα, τις προϋποθέσεις και τα διαθέσιμα οργανικά κενά.</p>
      <div id="primarySocialCriteriaCard">
        <?php transferSocialCriteriaCard('primary', <<<'SOCIAL_PRIMARY_EXTRAS'

<p id="primaryKedasyGeoNote" class="primary-transfer-notice" hidden><strong>Ειδικά για ΚΕΔΑΣΥ:</strong> η εντοπιότητα και η συνυπηρέτηση κρίνονται αποκλειστικά για τον <strong>δήμο όπου ανήκει το ΚΕΔΑΣΥ</strong>, όχι γενικά για ολόκληρη την περιοχή μετάθεσης (Εγκύκλιος 138023/Ε2, Μέρος Γ΄, Κεφ. ΣΤ΄, παρ. 7στ).</p>
SOCIAL_PRIMARY_EXTRAS
        ); ?>
      </div>
      <?php calculatorCardStart(array('title'=>'Δ. Προσωπική λίστα δικαιολογητικών')); ?>
        <p class="small-note">Οι υπηρεσιακοί και αυτεπάγγελτοι έλεγχοι εμφανίζονται ενημερωτικά· checkbox έχουν μόνο ενέργειες προετοιμασίας. Οι τίτλοι ΕΑΕ και τα προσόντα Διαπολιτισμικών υποβάλλονται μέσω mydocs μόνο όταν απαιτείται νέος ή συμπληρωματικός φάκελος.</p>
        <div id="primaryChecklist" aria-live="polite"></div>
        <p id="primaryChecklistProgress" role="status" aria-live="polite"></p>
        <button type="button" class="secondary" id="primaryPrintChecklist">Εκτύπωση λίστας δικαιολογητικών</button>
        <p id="primaryPrintFeedback" role="status" aria-live="polite" class="small-note"></p>
      <?php calculatorCardEnd(); ?>
      <section class="transfer-email-feature" id="primaryEmailFeature" aria-labelledby="primaryEmailHeading">
        <h3 id="primaryEmailHeading">Προετοιμασία μηνύματος υποβολής δικαιολογητικών</h3>
        <p id="primaryEmailLead" class="transfer-email-feature__lead">Δημιουργήστε θέμα και σώμα μηνύματος με τη μορφή της εγκυκλίου Πρωτοβάθμιας 138023/Ε2/2026.</p>
        <button type="button" id="primaryEmailOpen" class="transfer-email-feature__cta" aria-expanded="false" aria-controls="primaryEmailDetails">Δημιουργία μηνύματος υποβολής ↓</button>
        <p class="transfer-email-feature__helper">Το υπόδειγμα δημιουργείται στη συσκευή σας. Δεν γίνεται αυτόματη αποστολή, επιλογή παραλήπτη ή επισύναψη αρχείων.</p>
        <p id="primaryMydocsNotice" class="primary-transfer-info" hidden></p>
        <p id="primaryMydocsLink" class="small-note" hidden><a href="https://mydocs.minedu.gov.gr/" target="_blank" rel="noopener noreferrer">mydocs ΥΠΑΙΘΑ ↗</a> — για προσόντα ΕΑΕ όταν απαιτείται νέος/συμπληρωματικός φάκελος.</p>
        <details id="primaryEmailDetails" class="edu-disclosure transfer-email-feature__disclosure">
          <summary class="edu-disclosure__summary"><span>Πού αποστέλλονται τα δικαιολογητικά; — Οδηγίες και υπόδειγμα</span><span aria-hidden="true" class="edu-disclosure__chevron">›</span></summary>
          <div class="edu-disclosure__body"><div class="transfer-submission-content">
            <p id="primarySubmissionInstruction" class="small-note">Η διαδρομή email ή mydocs εξαρτάται από το δικαιολογητικό και τον τύπο αίτησης.</p>
            <section class="transfer-submission-template" aria-label="Υπόδειγμα θέματος και σώματος αποστολής">
              <h4>Θέμα και σώμα μηνύματος — υπόδειγμα</h4>
              <p class="transfer-submission-template__note" id="primaryTemplateNote">Η μορφή ενημερώνεται σύμφωνα με την εγκύκλιο Πρωτοβάθμιας.</p>
              <details class="transfer-submission-template__personalize" id="primaryPersonalize">
                <summary>Δημιουργία έτοιμου μηνύματος (προαιρετικά)</summary>
                <div class="transfer-submission-template__personal-body">
                  <p class="transfer-submission-template__private-note">Συμπληρώστε όσα στοιχεία θέλετε από την <strong>οριστικοποιημένη</strong> αίτηση. Όσα λείπουν διατηρούνται σε αγκύλες. Δεν αποστέλλονται ή αποθηκεύονται.</p>
                  <div class="transfer-submission-template__fields">
                    <label class="transfer-submission-template__field is-wide" for="primaryName"><span>Ονοματεπώνυμο</span><input id="primaryName" type="text" maxlength="120" placeholder="π.χ. Παπαδόπουλος Νικόλαος" autocomplete="off"></label>
                    <label class="transfer-submission-template__field" for="primarySpecialty"><span>Κλάδος / ειδικότητα</span><select id="primarySpecialty" name="transfer-personal-branch"><option value="">— Επιλέξτε ειδικότητα —</option></select></label>
                    <label class="transfer-submission-template__field" for="primaryRegistry"><span>Αριθμός μητρώου</span><input id="primaryRegistry" type="text" maxlength="15" placeholder="Α.Μ. εκπαιδευτικού" autocomplete="off"></label>
                    <label class="transfer-submission-template__field is-wide" for="primaryProtocol"><span>Αριθμός πρωτοκόλλου οριστικοποιημένης αίτησης</span><input id="primaryProtocol" type="text" maxlength="150" placeholder="Αντιγραφή από teachers.minedu.gov.gr" autocomplete="off"></label>
                    <label class="transfer-submission-template__field is-wide" id="primaryDpeField" for="primaryDpe"><span>ΔΠΕ οργανικής</span><input id="primaryDpe" type="text" maxlength="120" placeholder="π.χ. ΔΠΕ Κέρκυρας" autocomplete="off"></label>
                  </div>
                  <button type="button" class="secondary transfer-submission-template__clear" id="primaryClearEmail">Καθαρισμός πεδίων</button>
                </div>
              </details>
              <div class="transfer-submission-template__item"><div class="transfer-submission-template__bar"><strong>Θέμα:</strong><button type="button" class="secondary" id="primaryCopySubject">Αντιγραφή</button></div><p class="transfer-submission-template__value" id="primaryEmailSubject"></p></div>
              <div class="transfer-submission-template__item"><div class="transfer-submission-template__bar"><strong>Σώμα κειμένου:</strong><button type="button" class="secondary" id="primaryCopyBody">Αντιγραφή</button></div><p class="transfer-submission-template__value" id="primaryEmailBody"></p></div>
              <p class="transfer-submission-template__email-action" id="primaryMailAction"><a class="transfer-email-feature__mail-link" id="primaryOpenMail" href="#">Άνοιγμα στο email μου ↗</a> <span>Ο παραλήπτης δεν συμπληρώνεται αυτόματα· προσθέστε εσείς τα συνημμένα. Δεν πραγματοποιείται αποστολή.</span></p>
              <p class="transfer-submission-template__source" id="primaryEmailSource">Πηγή: εγκύκλιος Π.Ε. 138023/Ε2/15-10-2026, §2.2.1.</p>
              <p class="transfer-submission-template__feedback" id="primaryCopyFeedback" role="status" aria-live="polite"></p>
            </section>
          </div></div>
        </details>
      </section>
      <p class="primary-transfer-notice"><strong>Προκαταρκτικός οδηγός:</strong> η Εργαλειοθήκη δεν υποκαθιστά το ΥΠΑΙΘΑ, τη ΔΠΕ ή τη νομοθεσία και δεν ελέγχει ακόμη όλες τις ειδικές κατηγορίες ή υπηρεσιακές εξαιρέσεις. Οι μονάδες στο επίσημο μητρώο υπερισχύουν.</p>
    <?php calculatorMainEnd(); ?>
    <?php require_once __DIR__ . '/includes/components/transfer-score-card.php'; ?>
    <?php calculatorResultsStart(array('aria_live'=>'polite')); ?>
      <div id="primaryScoreResults">
        <?php transferScoreCard('primary'); ?>
        <p id="primaryRuntimeWarning" class="primary-runtime-warning" role="alert" hidden></p>
      </div>
    <?php calculatorResultsEnd(); ?>
  <?php calculatorColumnsEnd(); ?>
<?php calculatorContainerEnd(); ?>
<?php sourceCardStart(); ?>
  <p>Επίσημη βάση: εγκύκλιος μεταθέσεων Πρωτοβάθμιας <strong>138023/Ε2/15-10-2026</strong> (ΑΔΑ: 9Ρ7Ν46ΝΚΠΔ-5Κ5). Για τη γενική διαδικασία, τα δικαιολογητικά μοριοδότησης που απαιτούν αποστολή διαβιβάζονται με email στη ΔΠΕ οργανικής.</p>
  <?php sourceCardLinksStart(); ?>
    <?php sourceCardLink('https://www.minedu.gov.gr/site/71034-15-10-26-metatheseis-ekpaideutikon-protobathmias-ekpaideuses-scholikou-etous-2026-2027','ΥΠΑΙΘΑ — Εγκύκλιος Πρωτοβάθμιας 2026–2027 ↗'); ?>
    <?php sourceCardLink('https://teachers.minedu.gov.gr/','Επίσημο μητρώο εκπαιδευτικών / αιτήσεις ↗'); ?>
  <?php sourceCardLinksEnd(); ?>
<?php sourceCardEnd(); ?>
<?php require __DIR__ . '/includes/footer.php'; ?>
<script>
window.EducationTransferPrimarySpecialtyLabels = Object.freeze(<?php echo json_encode($primaryTransferSpecialtyLabels, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>);
</script>
<script src="<?php echo htmlspecialchars(edu_asset_url('includes/transfer-calculations.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
<script src="<?php echo htmlspecialchars(edu_asset_url('includes/transfer-primary-eae.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
<script src="<?php echo htmlspecialchars(edu_asset_url('includes/transfer-primary-eae-routes.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
<script src="<?php echo htmlspecialchars(edu_asset_url('includes/transfer-primary-minority.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
<script src="<?php echo htmlspecialchars(edu_asset_url('includes/transfer-email-component.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
<script src="<?php echo htmlspecialchars(edu_asset_url('includes/transfer-primary-intercultural.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
<script src="<?php echo htmlspecialchars(edu_asset_url('includes/transfer-primary-intercultural-vacancies.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
<script src="<?php echo htmlspecialchars(edu_asset_url('includes/transfer-social-input.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
<script src="<?php echo htmlspecialchars(edu_asset_url('includes/transfer-score-card.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
<script src="<?php echo htmlspecialchars(edu_asset_url('includes/transfer-html-escape.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
<script src="<?php echo htmlspecialchars(edu_asset_url('includes/transfer-primary-periods.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
<script src="<?php echo htmlspecialchars(edu_asset_url('includes/transfer-coservice-resources.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
<script src="<?php echo htmlspecialchars(edu_asset_url('includes/transfer-children-documents.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
<script src="<?php echo htmlspecialchars(edu_asset_url('includes/transfer-checklist.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
<script src="<?php echo htmlspecialchars(edu_asset_url('includes/transfer-checklist-print.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
<script src="<?php echo htmlspecialchars(edu_asset_url('includes/transfer-primary.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
<script>
// Explicitly diagnose missing/blocked JavaScript instead of leaving all scores as dashes.
window.addEventListener('load',function(){
  setTimeout(function(){
    if(window.EducationTransferPrimaryReady===true)return;
    var warning=document.getElementById('primaryRuntimeWarning');
    if(warning){
      warning.hidden=false;
      warning.textContent='Ο υπολογιστής δεν ξεκίνησε. Ελέγξτε ότι έχουν ανέβει όλα τα αρχεία includes/transfer-*.js της ίδιας έκδοσης και κάντε πλήρη ανανέωση (Ctrl+F5). Τα εμφανιζόμενα «—» δεν είναι αποτέλεσμα μοριοδότησης.';
    }
  },500);
});
</script>
</body>
</html>
