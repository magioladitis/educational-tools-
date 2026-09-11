<?php require_once __DIR__ . '/includes/config.php'; ?>
<?php require_once __DIR__ . '/includes/teacher-specialties.php'; ?>
<!doctype html>
<html lang="el">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="Υπολογισμός μορίων Μητρώου ΣΔΕ για ωρομίσθιο εκπαιδευτικό προσωπικό, Συμβούλους Ψυχολόγους και Συμβούλους Σταδιοδρομίας βάσει της Υ.Α. 75975/Κ1, ΦΕΚ Β' 3224/25.06.2025.">
  <title>Μόρια Μητρώου ΣΔΕ</title>
<link rel="stylesheet" href="<?php echo htmlspecialchars(edu_asset_url('assets/common.css'), ENT_QUOTES, 'UTF-8'); ?>">
</head>
<body class="edu-ui edu-calc-sde edu-page-sde-registry">
<main class="page-shell">
<?php require_once __DIR__ . '/includes/header.php'; ?>
<?php require_once __DIR__ . '/includes/components/calculator-layout.php'; ?>

<?php calculatorHero(array(
  'title' => 'Μόρια Μητρώου ΣΔΕ',
  'intro' => 'Ενιαίος υπολογιστής για ωρομίσθιο Εκπαιδευτικό Προσωπικό, Συμβούλους Ψυχολόγους και Συμβούλους Σταδιοδρομίας στα Σχολεία Δεύτερης Ευκαιρίας.',
  'meta_class' => 'hero-meta',
  'badges' => array('3 κατηγορίες υποψηφίων', 'Βασική βαθμολογία έως 40', 'Κοινωνικές προσαυξήσεις', 'Live υπολογισμός', 'ΦΕΚ Β΄ 3224/25.06.2025')
)); ?>

<?php calculatorColumnsStart(); ?>
<?php calculatorMainStart(); ?>
  <?php calculatorCardStart(array('header_variant' => 'section-head', 'title_html' => '1. Κατηγορία &amp; προϋποθέσεις ένταξης', 'subtitle_html' => 'Η επιλογή κατηγορίας αλλάζει αυτόματα τα απαιτούμενα προσόντα, τις επιμορφώσεις και την εμπειρία που μοριοδοτείται.')); ?>
    <div class="field-grid">
      <div class="field full"><label for="role">Κατηγορία υποψηφίου</label><select id="role"><option value="">— Επίλεξε —</option><option value="educator">Εκπαιδευτικό Προσωπικό</option><option value="psychologist">Σύμβουλος Ψυχολόγος</option><option value="career">Σύμβουλος Σταδιοδρομίας</option></select></div>
    </div>

    <div id="educatorEligibility" class="hidden">
      <div class="field-grid edu-mt-13">
        <div class="field"><label for="specialty">Κλάδος / ειδικότητα</label><select id="specialty"><option value="">— Επίλεξε —</option><?php
$sdeRegistrySpecialties = array(
    'ΠΕ01', 'ΠΕ02', 'ΠΕ03', 'ΠΕ04.01', 'ΠΕ04.02', 'ΠΕ04.03', 'ΠΕ04.04', 'ΠΕ04.05',
    'ΠΕ06', 'ΠΕ08', 'ΠΕ78', 'ΠΕ79.01', 'ΠΕ80', 'ΠΕ85', 'ΠΕ86', 'ΠΕ87.01', 'ΠΕ88.01',
    'ΠΕ88.05', 'ΠΕ89.01', 'ΠΕ91', 'ΤΕ16'
);
foreach ($sdeRegistrySpecialties as $code) {
    echo '<option value="' . htmlspecialchars($code, ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars(teacherSpecialtyDisplay($code), ENT_QUOTES, 'UTF-8') . '</option>';
}
?></select></div>
        <div class="field"><label for="eoppepAdultTrainer">Πιστοποίηση εκπαιδευτικής επάρκειας Εκπαιδευτή Ενηλίκων ΕΟΠΠΕΠ <small>Δεν δίνει μόρια, αλλά οι πιστοποιημένοι εκπαιδευτές απασχολούνται κατά προτεραιότητα.</small></label><select id="eoppepAdultTrainer"><option value="no">Όχι / δεν έχει δηλωθεί</option><option value="yes">Ναι</option></select></div>
      </div>
      <div id="assignmentPanel" class="note">Επίλεξε κλάδο για να εμφανιστούν τα γνωστικά αντικείμενα Α΄/Β΄ ανάθεσης.</div>
    </div>

    <div id="psychEligibility" class="hidden">
      <div class="field-grid edu-mt-13">
        <div class="field"><label for="psychDegree">Πτυχίο Ψυχολογίας ή ισότιμο/αντίστοιχο</label><select id="psychDegree"><option value="">— Επίλεξε —</option><option value="yes">Ναι</option><option value="no">Όχι</option></select></div>
        <div class="field"><label for="psychLicense">Άδεια άσκησης επαγγέλματος Ψυχολόγου</label><select id="psychLicense"><option value="">— Επίλεξε —</option><option value="yes">Ναι</option><option value="no">Όχι</option></select></div>
        <div class="field"><label for="fppBefore1993">Πτυχίο Φ.Π.Ψ. πριν το 1993;</label><select id="fppBefore1993"><option value="no">Όχι</option><option value="yes">Ναι</option></select></div>
        <div class="field hidden" id="psychMasterForFppWrap"><label for="psychMasterForFpp">Μεταπτυχιακό στην Ψυχολογία <small>Απαιτείται επιπρόσθετα για πτυχιούχους Φ.Π.Ψ. πριν το 1993.</small></label><select id="psychMasterForFpp"><option value="">— Επίλεξε —</option><option value="yes">Ναι</option><option value="no">Όχι</option></select></div>
      </div>
    </div>

    <div id="careerEligibility" class="hidden">
      <div class="field-grid edu-mt-13">
        <div class="field"><label for="tertiaryDegree">Πτυχίο Α.Ε.Ι. ή ισότιμο της αλλοδαπής</label><select id="tertiaryDegree"><option value="">— Επίλεξε —</option><option value="yes">Ναι</option><option value="no">Όχι</option></select></div>
        <div class="field"><label for="careerQualification">Εξειδίκευση στη Συμβουλευτική / Επαγγελματικό Προσανατολισμό</label><select id="careerQualification"><option value="none">— Δεν διαθέτω / δεν έχω επιλέξει —</option><option value="phd">Διδακτορικό στο πεδίο</option><option value="master">Μεταπτυχιακό στο πεδίο</option><option value="pesyp">ΠΕΣΥΠ ΑΣΠΑΙΤΕ/ΣΕΛΕΤΕ</option><option value="eoppep">Πιστοποίηση επάρκειας Συμβούλου Σταδιοδρομίας/Επαγγελματικού Προσανατολισμού από ΕΟΠΠΕΠ</option></select><div class="small edu-mt-7">Αν η εξειδίκευση είναι διδακτορικό, μεταπτυχιακό ή ΠΕΣΥΠ, δήλωσέ την και στην ενότητα «Εκπαίδευση» για να υπολογιστούν τα αντίστοιχα μόρια.</div></div>
      </div>
    </div>
    <div id="eligibilityInline"></div>
  <?php calculatorCardEnd(); ?>

  <?php calculatorCardStart(array('header_variant' => 'section-head', 'title_html' => '2. Εκπαίδευση', 'subtitle_html' => 'Τυπικά προσόντα έως 18 μόρια + επιμόρφωση έως 4 μόρια.', 'cap_html' => 'έως 22')); ?>
    <h3>Τυπικά προσόντα</h3>
    <div class="field-grid">
      <div class="field"><label for="phd">Διδακτορικό <small id="phdTargetHint">Η συναφής κατεύθυνση αλλάζει ανά κατηγορία.</small></label><select id="phd"><option value="none">Δεν διαθέτω</option><option value="target">Στη μοριοδοτούμενη κατεύθυνση — 11 μόρια</option><option value="other">Σε άλλη κατεύθυνση — 9 μόρια</option></select></div>
      <div class="field"><label for="master">Μεταπτυχιακό <small>Αν υπάρχει και διδακτορικό, το πρώτο μεταπτυχιακό δεν προσμετράται.</small></label><select id="master"><option value="none">Δεν διαθέτω</option><option value="target">Στη μοριοδοτούμενη κατεύθυνση — 8 μόρια</option><option value="other">Σε άλλη κατεύθυνση — 6 μόρια</option></select></div>
      <div class="field"><div class="check-row"><input id="secondDegree" type="checkbox"><label for="secondDegree">Δεύτερο πτυχίο τριτοβάθμιας — 3 μόρια</label></div></div>
      <div class="field"><div class="check-row"><input id="secondPhd" type="checkbox"><label for="secondPhd">Δεύτερο διδακτορικό — 2 μόρια</label></div></div>
      <div class="field"><div class="check-row"><input id="secondMaster" type="checkbox"><label for="secondMaster">Δεύτερο μεταπτυχιακό — 1 μόριο</label></div></div>
      <div class="field"><div class="check-row"><input id="extraCredential" type="checkbox"><label for="extraCredential" id="extraCredentialLabel">Πρόσθετο προσόν — 1 μόριο</label></div></div>
    </div>
    <div class="note">Τα τυπικά προσόντα έχουν ανώτατο όριο 18 μορίων. Αν δηλωθούν ταυτόχρονα πρώτο διδακτορικό και πρώτο μεταπτυχιακό, μοριοδοτείται μόνο το διδακτορικό.</div>

    <h3>Επιμόρφωση</h3>
    <div class="field-grid">
      <div class="field"><label for="trainingSdeHours">Σε θέματα ΣΔΕ — ώρες <small id="trainingSdeMax">0,25 / 100 ώρες</small></label><input id="trainingSdeHours" type="number" min="0" step="1" value="0"></div>
      <div class="field"><label for="trainingAdultHours">Στις αρχές Εκπαίδευσης Ενηλίκων — ώρες <small id="trainingAdultMax">0,25 / 100 ώρες</small></label><input id="trainingAdultHours" type="number" min="0" step="1" value="0"></div>
      <div class="field full hidden" id="trainingThematicWrap"><label for="trainingThematicHours" id="trainingThematicLabel">Θεματική επιμόρφωση — ώρες</label><input id="trainingThematicHours" type="number" min="0" step="1" value="0"></div>
    </div>
    <div class="warning">Μοριοδοτούνται μόνο ολοκληρωμένες επιμορφώσεις. <strong>Κάθε επιμέρους επιμόρφωση κάτω από 15 ώρες λαμβάνει 0 μόρια.</strong> Καταχώρισε μόνο επιλέξιμες ώρες. Δεν μοριοδοτούνται ημερίδες, διημερίδες ή συνέδρια.</div>
    <div class="danger hidden" id="trainingMinimumWarning" aria-live="polite"></div>
    <div class="warning hidden" id="educatorTrainingProof">Για το Εκπαιδευτικό Προσωπικό, το δικαιολογητικό πρέπει να αναφέρει σαφώς φορέα, αντικείμενο, χρονικό διάστημα και διάρκεια αποκλειστικά σε ώρες. Αν δεν αναφέρονται ώρες, μπορούν να αποδειχθούν με το πρόγραμμα της επιμόρφωσης· Υπεύθυνη Δήλωση του υποψηφίου δεν γίνεται αποδεκτή.</div>
  <?php calculatorCardEnd(); ?>

  <?php calculatorCardStart(array('header_variant' => 'section-head', 'title_html' => '3. Εμπειρία', 'subtitle_html' => 'Επίλεξε κατηγορία για να εμφανιστούν τα σωστά πεδία.', 'subtitle_attrs' => array('id' => 'experienceSubtitle'), 'cap_html' => 'έως 13')); ?>
    <div id="educatorExperience" class="field-grid hidden">
      <div class="field"><label for="expSdeHours">Διδασκαλία στα ΣΔΕ — ώρες <small>1 μόριο / 200 ώρες, έως 5.</small></label><input id="expSdeHours" type="number" min="0" step="1" value="0"></div>
      <div class="field"><label for="expAdultHours">Εκπαίδευση Ενηλίκων εκτός ΣΔΕ — ώρες <small>0,5 μόριο / 100 ώρες, έως 4.</small></label><input id="expAdultHours" type="number" min="0" step="1" value="0"></div>
      <div class="field full"><label for="expFormalHours">Τυπική εκπαίδευση ή επαγγελματική κατάρτιση — ώρες <small>Π/θμια, Δ/θμια, Τριτοβάθμια ή Επαγγελματική Κατάρτιση. 1 μόριο / 200 ώρες, έως 4.</small></label><input id="expFormalHours" type="number" min="0" step="1" value="0"></div>
    </div>
    <div id="advisorExperience" class="field-grid hidden">
      <div class="field"><label for="expSdeMonths" id="expSdeMonthsLabel">Εμπειρία στα ΣΔΕ — μήνες</label><input id="expSdeMonths" type="number" min="0" max="600" step="1" value="0"></div>
      <div class="field"><label for="expAdultCounsellingMonths" id="expAdultCounsellingLabel">Συμβουλευτικές υπηρεσίες σε ενήλικες — μήνες</label><input id="expAdultCounsellingMonths" type="number" min="0" max="600" step="1" value="0"></div>
    </div>
    <div id="careerInconsistency" class="warning hidden"><strong>Σημείωση για το ΦΕΚ:</strong> στο άρθρο 12 §2.1 το λεκτικό αναφέρει «μέγιστο αριθμό μορίων 12», αλλά η στήλη του πίνακα δίνει 7 και η συνολική κατηγορία Επαγγελματικής Εμπειρίας είναι 13, ενώ το §2.2 δίνει 6. Ο υπολογιστής χρησιμοποιεί πλαφόν <strong>7</strong>, ως τη μοναδική τιμή που συμφωνεί εσωτερικά με το άθροισμα 13.</div>
  <?php calculatorCardEnd(); ?>

  <?php calculatorCardStart(array('header_variant' => 'section-head', 'title_html' => '4. Άλλα προσόντα', 'subtitle_html' => 'Δύο ξένες γλώσσες και γνώσεις Η/Υ.', 'cap_html' => 'έως 5')); ?>
    <div class="field-grid">
      <div class="field"><label for="language1">Ξένη γλώσσα 1</label><select id="language1"><option value="">— Καμία —</option><option value="english">Αγγλικά</option><option value="french">Γαλλικά</option><option value="german">Γερμανικά</option><option value="italian">Ιταλικά</option><option value="spanish">Ισπανικά</option><option value="other1">Άλλη</option></select><label class="edu-tools-sr-only" for="languageLevel1">Επίπεδο ξένης γλώσσας 1</label><select id="languageLevel1" class="edu-mt-8"><option value="none">— Επίπεδο —</option><option value="B2">Β2 — Καλή</option><option value="C1">C1 — Πολύ καλή</option><option value="C2">C2 — Άριστη</option></select></div>
      <div class="field"><label for="language2">Ξένη γλώσσα 2</label><select id="language2"><option value="">— Καμία —</option><option value="english">Αγγλικά</option><option value="french">Γαλλικά</option><option value="german">Γερμανικά</option><option value="italian">Ιταλικά</option><option value="spanish">Ισπανικά</option><option value="other2">Άλλη</option></select><label class="edu-tools-sr-only" for="languageLevel2">Επίπεδο ξένης γλώσσας 2</label><select id="languageLevel2" class="edu-mt-8"><option value="none">— Επίπεδο —</option><option value="B2">Β2 — Καλή</option><option value="C1">C1 — Πολύ καλή</option><option value="C2">C2 — Άριστη</option></select></div>
      <div class="field full"><div class="note">Η ίδια κατονομασμένη γλώσσα δεν μπορεί να επιλεγεί δύο φορές. Η επιλογή «Άλλη» παραμένει διαθέσιμη και στα δύο πεδία, επειδή μπορεί να αφορά διαφορετικές γλώσσες.</div></div>
      <div class="field full"><div class="note"><strong>ΠΕ06 Αγγλικής:</strong> η άριστη γνώση της Αγγλικής δεν μοριοδοτείται στο πεδίο ξένης γλώσσας, σύμφωνα με την Πρόσκληση Μητρώου ΣΔΕ 2025–2026.</div></div>
      <div class="field full" id="computerField"><div class="check-row"><input id="computer" type="checkbox"><label for="computer" id="computerLabel">Πιστοποιημένη επιμόρφωση ΤΠΕ επιπέδου 1 ΥΠΑΙΘΑ ή αποδεικτικό γνώσης Η/Υ σύμφωνα με την Πρόσκληση — 2 μόρια</label></div><div id="computerPe86Note" class="warning hidden"><strong>ΠΕ86 Πληροφορικής:</strong> όταν το πτυχίο Πληροφορικής χρησιμοποιείται ως βασικό πτυχίο ένταξης στο Μητρώο, το πεδίο «Γνώση Χειρισμού Η/Υ» δεν αξιολογείται και δεν προσθέτει 2 μόρια.</div></div>
    </div>
  <?php calculatorCardEnd(); ?>

  <?php calculatorCardStart(array('header_variant' => 'section-head', 'title_html' => '5. Κοινωνικά κριτήρια', 'subtitle_html' => 'Οι προσαυξήσεις υπολογίζονται επί της βασικής βαθμολογίας που έχει συγκεντρωθεί.')); ?>
    <div class="field-grid">
      <div class="field"><label for="unemploymentMonths">Πλήρεις μήνες ανεργίας <small>+0,5% ανά μήνα, έως 10%.</small></label><input id="unemploymentMonths" type="number" min="0" step="1" value="0"></div>
      <div class="field"><label for="unemploymentExtraDays">Επιπλέον ημέρες <small>15 ημέρες ή περισσότερες λογίζονται ως ένας ακόμη πλήρης μήνας.</small></label><input id="unemploymentExtraDays" type="number" min="0" max="30" step="1" value="0"></div>
      <div class="field"><div class="check-row"><input id="threeChildren" type="checkbox"><label for="threeChildren">Γονέας τρίτεκνης οικογένειας — +10%</label></div></div>
      <div class="field"><div class="check-row"><input id="singleParent" type="checkbox"><label for="singleParent">Μέλος μονογονεϊκής οικογένειας — +10%</label></div></div>
      <div class="field"><div class="check-row"><input id="manyChildren" type="checkbox"><label for="manyChildren">Μέλος πολύτεκνης οικογένειας — +10%</label></div></div>
      <div class="field"><div class="check-row"><input id="disability" type="checkbox"><label for="disability">ΑμεΑ ≥50% υποψηφίου/τέκνου/συζύγου, με τις προβλεπόμενες προϋποθέσεις — +10%</label></div></div>
    </div>
    <div class="note">Το ΦΕΚ διατυπώνει κάθε ειδική κατηγορία ως προσαύξηση 10% επί της βαθμολογίας. Το εργαλείο προσθέτει κάθε κατηγορία που δηλώνεται ως εφαρμοζόμενη και εμφανίζει αναλυτικά την επίδρασή της.</div>
  <?php calculatorCardEnd(); ?>
<?php calculatorMainEnd(); ?>

<?php calculatorResultsStart(array('class' => 'results', 'attrs' => array('aria-live' => 'polite'))); ?>
  <?php calculatorCardStart(); ?>
    <div class="role-chip" id="roleChip">Επίλεξε κατηγορία</div><h2>Αποτέλεσμα</h2>
    <?php calculatorScoreHeader(array(
      'variant' => 'staged',
      'class' => 'big-total',
      'context_html' => 'Τελική βαθμολογία με προσαυξήσεις',
      'context_attrs' => array('class' => 'context'),
      'value_id' => 'finalScore',
      'value_html' => '0',
      'value_class' => 'number',
      'cap_html' => 'Βασική βαθμολογία έως 40 + κοινωνικά κριτήρια',
      'cap_class' => 'outof'
    )); ?>
    <div class="bar"><div id="baseBar"></div></div>
    <?php calculatorResultRow(array('label_html' => 'Εκπαίδευση', 'value_html' => '0 / 22', 'value_id' => 'educationScore')); ?>
    <?php calculatorResultRow(array('label_html' => 'Εμπειρία', 'value_html' => '0 / 13', 'value_id' => 'experienceScore')); ?>
    <?php calculatorResultRow(array('label_html' => 'Άλλα προσόντα', 'value_html' => '0 / 5', 'value_id' => 'otherScore')); ?>
    <?php calculatorResultRow(array('class' => 'result-row emphasis', 'label_html' => 'Βασική βαθμολογία', 'value_html' => '0 / 40', 'value_id' => 'baseScore')); ?>
    <?php calculatorResultRow(array('label_html' => 'Ανεργία', 'value_html' => '+0', 'value_id' => 'unemploymentScore')); ?>
    <?php calculatorResultRow(array('label_html' => 'Ειδικές κατηγορίες', 'value_html' => '+0', 'value_id' => 'specialScore')); ?>
    <div id="priorityStatus"></div><div id="eligibilityStatus"></div>
    <?php calculatorActions(array(array('attrs' => array('class' => 'secondary', 'type' => 'button', 'id' => 'copyBtn'), 'html' => 'Αντιγραφή'), array('attrs' => array('class' => 'secondary', 'type' => 'button', 'id' => 'resetBtn'), 'html' => 'Καθαρισμός'))); ?>
  <?php calculatorCardEnd(); ?>
  <?php calculatorCardStart(); ?><h2>Αναλυτική μοριοδότηση</h2><div id="breakdown" class="subtitle">Επίλεξε κατηγορία και συμπλήρωσε τα στοιχεία.</div><?php calculatorCardEnd(); ?>
  <?php calculatorCardStart(); ?><h2>Δικαιολογητικά / έλεγχοι</h2><div id="checklist" class="subtitle">Θα προσαρμοστεί στις επιλογές σου.</div><?php calculatorCardEnd(); ?>
  <?php calculatorDisclosure(array('summary' => 'Ισοβαθμία', 'html' => 'Προηγείται ο υποψήφιος με διδακτορικό, έπειτα ο κάτοχος μεταπτυχιακού και τέλος ο κάτοχος πτυχίου κατά βαθμό πτυχίου. Αν παραμένει ισοβαθμία, προβλέπεται δημόσια κλήρωση.', 'class' => 'edu-result-disclosure', 'open' => true, 'attrs' => array('data-mobile-collapsed' => 'true'))); ?>
<?php calculatorResultsEnd(); ?>
<?php calculatorColumnsEnd(); ?>

<?php sourceCardStart(); ?>
  <p><strong>Υ.Α. 75975/Κ1 — ΦΕΚ Β΄ 3224/25.06.2025</strong>, «Κανονισμός Διαχείρισης του Μητρώου Ωρομίσθιου Εκπαιδευτικού Προσωπικού, Συμβούλων Σταδιοδρομίας και Συμβούλων Ψυχολόγων στα Σχολεία Δεύτερης Ευκαιρίας (Σ.Δ.Ε.) — Καθορισμός της διαδικασίας και των κριτηρίων επιλογής και μοριοδότησης».</p>
  <p><strong>Άρθρο 3:</strong> προϋποθέσεις ένταξης και κλάδοι εκπαιδευτικού προσωπικού. <strong>Άρθρα 10–12:</strong> μοριοδότηση Εκπαιδευτικού Προσωπικού, Συμβούλων Ψυχολόγων και Συμβούλων Σταδιοδρομίας. <strong>Άρθρο 14:</strong> προτεραιότητα πιστοποιημένων Εκπαιδευτών Ενηλίκων ΕΟΠΠΕΠ.</p>
  <p><strong>Πρόσκληση Μητρώου ΣΔΕ 2025–2026, Κεφάλαιο Γ §§8–9:</strong> οι ΠΕ06 δεν μοριοδοτούνται στο πεδίο ξένης γλώσσας για την άριστη γνώση της Αγγλικής· οι ΠΕ86 που χρησιμοποιούν πτυχίο Πληροφορικής ως βασικό πτυχίο ένταξης δεν αξιολογούνται στο πεδίο «Γνώση Χειρισμού Η/Υ».</p>
  <?php sourceCardLinksStart(); ?><?php sourceCardLink('https://gsvetlly.minedu.gov.gr/publications/docs2023/DELTIA_TYPOU/2025/%CE%91%CE%A0%CE%9F%CE%A6%CE%91%CE%A3%CE%97_%CE%93%CE%99%CE%91_%CE%A3%CE%94%CE%95_%CE%A6%CE%95%CE%9A_3224%CE%92-25.06.2025.pdf', 'Υ.Α. 75975/Κ1 — ΦΕΚ Β΄ 3224/2025 (ΥΠΑΙΘΑ) ↗'); ?><?php sourceCardLink('https://gsvetlly.minedu.gov.gr/nea-anakoinoseis/512-prosklese-ekdeloses-endiapherontos-ypoboles-aiteses-entaxes-kai-epikairopoieses-prosthekes-stoicheion-sto-metroo-oromisthiou-ekpaideutikou-prosopikou-symboulon-stadiodromias-kai-symboulon-psychologon-ton-scholeion-deuteres-eukairias-metroo-s-d-e-1o-stadio', 'Πρόσκληση Μητρώου ΣΔΕ 2025–2026 — ΥΠΑΙΘΑ ↗'); ?><?php sourceCardLinksEnd(); ?>
  <?php sourceCardDisclaimerStart(); ?>Ο υπολογισμός είναι ενημερωτικός. Ειδικά για την ασυμφωνία του άρθρου 12 §2.1, το εργαλείο χρησιμοποιεί το πλαφόν 7 μορίων που συμφωνεί με τη στήλη του πίνακα και με το συνολικό όριο των 13 μορίων της κατηγορίας.<?php sourceCardDisclaimerEnd(); ?>
<?php sourceCardEnd(); ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
</main>
<script src="<?php echo htmlspecialchars(edu_asset_url('includes/language-calculations.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
<script src="<?php echo htmlspecialchars(edu_asset_url('includes/language-pair-lock.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
<script src="<?php echo htmlspecialchars(edu_asset_url('includes/sde-registry-calculations.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
<script src="<?php echo htmlspecialchars(edu_asset_url('includes/sde-registry-ui.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
<script src="<?php echo htmlspecialchars(edu_asset_url('assets/common.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
</body></html>
