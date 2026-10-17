<?php
/**
 * Pilot for general transfers and isolated EAE/KEDASY prechecks in Primary Education (2026–2027).
 * Isolated UI/rules; shares the proven EducationTransfer scoring module.
 * Do not route Primary applicants into the Secondary-specific document/eligibility UI.
 */
require_once __DIR__ . '/includes/config.php';
?>
<!DOCTYPE html>
<html lang="el">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <?php require __DIR__ . '/includes/head-pwa.php'; ?>
  <title>Μεταθέσεις εκπαιδευτικών Πρωτοβάθμιας — Γενική και ΕΑΕ</title>
  <link rel="stylesheet" href="<?php echo htmlspecialchars(edu_asset_url('assets/common.css'), ENT_QUOTES, 'UTF-8'); ?>">
  <style>
    #primaryServiceDurationPanel[hidden], #primaryServicePointsPanel[hidden], #primaryEmailDetails[hidden], #primaryEaeFields[hidden], #primaryGeneralOrganicField[hidden], #primaryEligibility[hidden], #primaryEaeYearsField[hidden], #primaryEaeGeneralYearsField[hidden], #primaryEaeAppointmentYearsField[hidden], #primaryPreviousDossierField[hidden], #primaryMydocsNotice[hidden], #primaryEmailOpen[hidden] { display:none!important; }
    .primary-transfer-switch { display:flex; gap:10px; align-items:center; flex-wrap:wrap; margin:12px 0 18px; }
    .primary-transfer-switch a,.primary-transfer-switch strong { display:inline-flex; padding:8px 12px; border-radius:9px; border:1px solid #9bb2cf; text-decoration:none; }
    .primary-transfer-switch strong { background:#e8f1fc;color:#17446d; }
    .primary-transfer-notice {border:1px solid #e2bc6b; background:#fff9ec; padding:12px; border-radius:10px; margin:12px 0; color:#4c390e; }
    .primary-transfer-info {border:1px solid #a4b7c9; padding:12px; border-radius:10px; margin:12px 0;}
    .primary-doc-row {border-top:1px solid #d4dce6; padding:12px 0;}
    .primary-doc-row label {display:flex;gap:10px;align-items:flex-start;}
    .primary-doc-kind {font-size:.84rem; color:#516477; margin:4px 0 0 27px;}
    .primary-email-feature {padding:16px; border:1px solid #93b7db; border-radius:12px; margin:16px 0;}
    .primary-email-feature textarea {min-height:145px; width:100%; font-family:inherit;}
    .primary-email-feature input {width:100%;}
    .primary-inline-actions {display:flex; gap:8px;flex-wrap:wrap;margin:10px 0;}
    .primary-inline-actions button,.primary-inline-actions a {min-height:40px;}
    .primary-score-section {font-variant-numeric:tabular-nums;}
    #primaryEaeFields[hidden], #primaryMydocsNotice[hidden], #primaryKedasyGeoNote[hidden], #primaryMydocsLink[hidden] {display:none!important;}
    .primary-eae-qualifications { display:grid; gap:8px; padding:10px 0; }
    .primary-eae-qualifications label { display:flex; gap:10px; align-items:flex-start; }
    .primary-eae-status {border:1px solid #a4b7c9; border-radius:10px; padding:12px; margin:12px 0;}
    .primary-eae-status[data-status="problem"] {background:#fff1f0;border-color:#b42318;color:#8f2219;}
    .primary-eae-status[data-status="review"] {background:#fff7e6;border-color:#bb7510;color:#684000;}
    .primary-eae-status[data-status="positive"] {background:#edf8ef;border-color:#26814c;color:#195b33;}
    .primary-eae-status ul {margin:6px 0 0 20px; padding:0;}
    .primary-doc-kind a {text-decoration:underline;}
    .primary-total {font-size:2rem; font-weight:700;}
    @media (max-width:410px) {.primary-inline-actions > * {width:100%;text-align:center;}}
    @media print {.primary-transfer-switch,.primary-inline-actions,.primary-email-feature,.primary-no-print{display:none!important}}
  </style>
</head>
<body class="edu-ui edu-calc-standard edu-page-transfer">
<?php require __DIR__ . '/includes/header.php'; ?>
<?php require_once __DIR__ . '/includes/components/calculator-layout.php'; ?>
<?php calculatorContainerStart(array('class'=>'app')); ?>
  <?php calculatorHero(array(
    'title'=>'Μεταθέσεις μόνιμων εκπαιδευτικών Πρωτοβάθμιας',
    'intro_html'=>'Πιλοτική επέκταση για <strong>γενικές μεταθέσεις και προέλεγχο ΕΑΕ/ΚΕΔΑΣΥ</strong>. Κοινή μοριοδότηση και απομονωμένοι κανόνες Πρωτοβάθμιας.',
    'badges'=>array('Εγκύκλιος Π.Ε. 138023/Ε2/15-10-2026', 'Πιλοτική Γενική + ΕΑΕ/ΚΕΔΑΣΥ')
  )); ?>
  <nav class="primary-transfer-switch" aria-label="Επιλογή βαθμίδας">
    <strong aria-current="page">Πρωτοβάθμια</strong>
    <a href="ypologismos-morion-metathesis.php">Δευτεροβάθμια (πλήρης 3.22.151)</a>
  </nav>
  <p class="primary-transfer-notice"><strong>Σταδιακή επέκταση:</strong> υποστηρίζονται γενικές μεταθέσεις και <strong>προκαταρκτικός</strong> έλεγχος μετάθεσης σε περιοχές ΕΑΕ ή ΚΕΔΑΣΥ. Διαπολιτισμικά, Μειονοτικά, αμοιβαίες, ΕΕΕΕΚ, Σχολεία Κωφών/Τυφλών και ειδικές κατηγορίες προτεραιότητας <strong>δεν καλύπτονται ακόμη πλήρως</strong>. Συμβουλευτείτε την εγκύκλιο και τη ΔΠΕ οργανικής.</p>
  <?php calculatorColumnsStart(); ?>
    <?php calculatorMainStart(); ?>
      <?php calculatorCardStart(array('title'=>'Α. Διαδικασία και υπηρεσιακός προέλεγχος')); ?>
        <div class="field"><label for="primaryMode">Τύπος αίτησης</label>
          <select id="primaryMode"><option value="region">Γενική μετάθεση από περιοχή σε περιοχή (Π.Ε.)</option><option value="eae">Μετάθεση σε περιοχή μετάθεσης για ΕΑΕ</option><option value="kedasy">Μετάθεση σε ΚΕΔΑΣΥ (Π.Ε.)</option></select>
        </div>
        <div id="primaryGeneralOrganicField" class="field"><label for="primaryOrganic">Υπηρέτηση οργανικής θέσης έως 31/08/2027</label>
          <select id="primaryOrganic">
            <option value="unknown">Δεν γνωρίζω / απαιτείται έλεγχος</option>
            <option value="yes">Συμπληρώνω ένα έτος στην οργανική</option>
            <option value="no">Δεν συμπληρώνω ένα έτος / επικαλούμαι ειδική περίπτωση</option>
          </select>
        </div>
        <p id="primaryEligibility" class="primary-transfer-info" role="status" aria-live="polite"></p>
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
            <p>Για ΚΕΔΑΣΥ Πρωτοβάθμιας προβλέπονται ΠΕ60, ΠΕ61, ΠΕ70, ΠΕ71. Ο προέλεγχος δεν αποφασίζει για αναγνώριση τίτλων, συνάφεια, υπολογισμό ημερών ή εξαιρέσεις.</p>
          </details>
        </div>
        <details><summary>Σημαντικές προθεσμίες και εξαιρέσεις</summary>
          <p>Ηλεκτρονική αίτηση: <strong>16/10 έως 02/11/2026</strong> στο <a href="https://teachers.minedu.gov.gr/" target="_blank" rel="noopener noreferrer">επίσημο μητρώο</a>. Μέχρι την ίδια ημερομηνία αποστέλλονται τα συνήθη δικαιολογητικά.</p>
          <p>Ανάκληση αίτησης έως 31/12/2026. Οι αμοιβαίες μεταθέσεις ακολουθούν ξεχωριστή προθεσμία 15 ημερών από την ανακοίνωση των μεταθέσεων. Εξαιρέσεις για νεογέννητα και ειδικές κατηγορίες περιγράφονται στην εγκύκλιο.</p>
        </details>
      <?php calculatorCardEnd(); ?>
      <?php calculatorCardStart(array('title'=>'Β. Μονάδες μετάθεσης')); ?>
        <p class="small-note">Τα μόρια προϋπηρεσίας και οι Μ.Σ.Δ. καταχωρίζονται χωριστά όπως στο <a href="https://teachers.minedu.gov.gr/" target="_blank" rel="noopener noreferrer">επίσημο μητρώο</a>. Δεν αθροίζονται διπλά οι υπηρετήσεις.</p>
        <div class="field"><label for="primaryServiceMode">Καταχώριση συνολικής υπηρεσίας</label>
          <select id="primaryServiceMode"><option value="points">Ακριβή μόρια από επίσημο μητρώο</option><option value="direct">Έτη, μήνες, ημέρες</option></select>
        </div>
        <div id="primaryServicePointsPanel" class="field"><label for="primaryServicePoints">Μόρια συνολικής υπηρεσίας</label>
          <input id="primaryServicePoints" type="text" inputmode="decimal" maxlength="6" placeholder="π.χ. 21,04" autocomplete="off">
        </div>
        <div id="primaryServiceDurationPanel" class="field-grid" hidden>
          <div class="field"><label for="primaryYears">Έτη</label><input id="primaryYears" type="number" min="0" max="50" step="1" value="0"></div>
          <div class="field"><label for="primaryMonths">Μήνες</label><input id="primaryMonths" type="number" min="0" max="11" step="1" value="0"></div>
          <div class="field"><label for="primaryDays">Ημέρες</label><input id="primaryDays" type="number" min="0" max="29" step="1" value="0"></div>
        </div>
        <div class="field"><label for="primaryMsd">Συνολικές Μ.Σ.Δ. από το επίσημο μητρώο</label>
          <input id="primaryMsd" type="text" inputmode="decimal" maxlength="7" autocomplete="off" placeholder="π.χ. 48,75">
          <div class="field-hint">Έως δύο δεκαδικά. Τεχνικό όριο προστασίας 1.000 μορίων, όχι νομοθετικό.</div>
        </div>
        <div class="field-grid"><div class="field"><label for="primaryChildren">Τέκνα που μοριοδοτούνται</label><input id="primaryChildren" type="number" min="0" max="20" step="1" value="0"></div></div>
        <div class="check-row"><label><input type="checkbox" id="primaryFamily"> Γάμος / σύμφωνο συμβίωσης / μοριοδοτούμενη οικογενειακή περίπτωση (+4)</label></div>
        <div class="check-row"><label><input type="checkbox" id="primaryChildrenExtra"> Χρειάζεται να αποστείλω πρόσθετο αποδεικτικό για τέκνο (ειδική περίπτωση)</label></div>
        <div class="check-row"><label><input type="checkbox" id="primaryCoservice"> Δικαιούμαι συνυπηρέτηση στην εξεταζόμενη περιοχή (+4)</label></div>
        <div class="check-row"><label><input type="checkbox" id="primaryLocality"> Δικαιούμαι εντοπιότητα στην εξεταζόμενη περιοχή (+4)</label></div>
        <div class="check-row"><label><input type="checkbox" id="primaryFirst"> Πρόκειται για την περιοχή πρώτης προτίμησης (+2)</label></div>
        <p id="primaryKedasyGeoNote" class="primary-transfer-notice" hidden><strong>Ειδικά για ΚΕΔΑΣΥ:</strong> η εντοπιότητα και η συνυπηρέτηση κρίνονται αποκλειστικά για τον <strong>δήμο όπου ανήκει το ΚΕΔΑΣΥ</strong>, όχι γενικά για ολόκληρη την περιοχή μετάθεσης (Εγκύκλιος 138023/Ε2, Μέρος Γ΄, Κεφ. ΣΤ΄, παρ. 7στ).</p>
        <details><summary>Οδηγίες για τα κριτήρια</summary><p>Συνυπηρέτηση: ελέγξτε τον τύπο απασχόλησης συζύγου και τις ειδικές εξαιρέσεις της εγκυκλίου, πριν επιλέξετε το κριτήριο. Εντοπιότητα: απαιτείται η νόμιμη διετία δημοτικότητας· η απλή κατοικία δεν αρκεί. Τα οικογενειακά μόρια εξαρτώνται από τις ακριβείς περιπτώσεις που προβλέπει το ισχύον πλαίσιο.</p></details>
        <p id="primaryValidation" role="status" aria-live="polite" class="field-hint"></p>
      <?php calculatorCardEnd(); ?>
      <?php calculatorCardStart(array('title'=>'Γ. Προσωπική λίστα δικαιολογητικών')); ?>
        <p class="small-note">Οι υπηρεσιακοί και αυτεπάγγελτοι έλεγχοι εμφανίζονται ενημερωτικά· checkbox έχουν μόνο ενέργειες προετοιμασίας. Οι τίτλοι ΕΑΕ υποβάλλονται μέσω mydocs, μόνο όταν απαιτείται νέος ή συμπληρωματικός φάκελος.</p>
        <div id="primaryChecklist" aria-live="polite"></div>
        <p id="primaryChecklistProgress" role="status" aria-live="polite"></p>
      <?php calculatorCardEnd(); ?>
      <section class="primary-email-feature" id="primaryEmailFeature">
        <p id="primaryMydocsNotice" class="primary-transfer-info" hidden></p>
        <p id="primaryMydocsLink" class="small-note" hidden><a href="https://mydocs.minedu.gov.gr/" target="_blank" rel="noopener noreferrer">mydocs ΥΠΑΙΘΑ ↗</a> — αποκλειστικά για δικαιολογητικά προσόντων ΕΑΕ, εφόσον πρέπει να υποβληθούν.</p>
        <h3 id="primaryEmailHeading">Έτοιμο email υποβολής δικαιολογητικών</h3>
        <p id="primaryEmailLead">Η Εργαλειοθήκη συντάσσει θέμα και κείμενο μόνο αν η αίτησή σας περιλαμβάνει ενέργεια αποστολής στη ΔΠΕ οργανικής.</p>
        <button type="button" id="primaryEmailOpen" aria-expanded="false" aria-controls="primaryEmailDetails">Δημιουργία μηνύματος υποβολής ↓</button>
        <div id="primaryEmailDetails" hidden>
          <div class="field-grid">
            <div class="field"><label for="primaryName">Ονοματεπώνυμο</label><input id="primaryName" type="text" maxlength="100" autocomplete="off"></div>
            <div class="field"><label for="primarySpecialty">Κλάδος / ειδικότητα</label><input id="primarySpecialty" type="text" maxlength="35" placeholder="π.χ. ΠΕ70" autocomplete="off"></div>
            <div class="field"><label for="primaryRegistry">Αριθμός μητρώου</label><input id="primaryRegistry" type="text" maxlength="30" autocomplete="off"></div>
            <div class="field"><label for="primaryProtocol">Αρ. πρωτοκόλλου αίτησης</label><input id="primaryProtocol" type="text" maxlength="90" autocomplete="off"></div>
            <div class="field"><label for="primaryDpe">ΔΠΕ οργανικής</label><input id="primaryDpe" type="text" maxlength="110" placeholder="π.χ. ΔΠΕ Κέρκυρας" autocomplete="off"></div>
          </div>
          <div class="field"><label for="primaryEmailSubject">Θέμα email</label><input id="primaryEmailSubject" readonly type="text"></div>
          <div class="field"><label for="primaryEmailBody">Κείμενο email</label><textarea id="primaryEmailBody" readonly></textarea></div>
          <div class="primary-inline-actions">
            <button type="button" id="primaryCopyEmail" class="secondary">Αντιγραφή θέματος και κειμένου</button>
            <a id="primaryOpenMail" href="#" style="display:none">Άνοιγμα στο email μου ↗</a>
          </div>
          <p class="small-note">Η εφαρμογή <strong>δεν αποστέλλει</strong> email, δεν γνωρίζει τη διεύθυνση της ΔΠΕ σας και δεν επισυνάπτει αρχεία. Ελέγξτε παραλήπτη και συνημμένα πριν την αποστολή.</p>
        </div>
      </section>
      <p class="primary-transfer-notice"><strong>Προκαταρκτικός οδηγός:</strong> η Εργαλειοθήκη δεν υποκαθιστά το ΥΠΑΙΘΑ, τη ΔΠΕ ή τη νομοθεσία και δεν ελέγχει ακόμη όλες τις ειδικές κατηγορίες ή υπηρεσιακές εξαιρέσεις. Οι μονάδες στο επίσημο μητρώο υπερισχύουν.</p>
    <?php calculatorMainEnd(); ?>
    <?php calculatorResultsStart(array('aria_live'=>'polite')); ?>
      <div class="primary-score-section"><h2>Ενδεικτική μοριοδότηση</h2><div class="primary-total" id="primaryTotal">—</div>
        <p id="primaryTotalStatus" role="status" aria-live="polite">Συμπληρώστε τα μόρια από το μητρώο.</p>
        <div id="primaryBreakdown"></div></div>
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
<script src="<?php echo htmlspecialchars(edu_asset_url('includes/transfer-calculations.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
<script src="<?php echo htmlspecialchars(edu_asset_url('includes/transfer-primary-eae.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
<script src="<?php echo htmlspecialchars(edu_asset_url('includes/transfer-primary.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
</body>
</html>
