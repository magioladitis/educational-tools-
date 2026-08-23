<!doctype html>
<html lang="el">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="Υπολογισμός μορίων Μητρώου ΣΔΕ για ωρομίσθιο εκπαιδευτικό προσωπικό, Συμβούλους Ψυχολόγους και Συμβούλους Σταδιοδρομίας βάσει της Υ.Α. 75975/Κ1, ΦΕΚ Β' 3224/25.06.2025.">
  <title>Μόρια Μητρώου ΣΔΕ</title>
<link rel="stylesheet" href="assets/common.css?v=3.20.13">
</head>
<body class="edu-ui edu-calc-sde edu-page-sde-registry">
<main class="page-shell">
<?php require_once __DIR__ . '/includes/header.php'; ?>
<?php require_once __DIR__ . '/includes/components/calculator-layout.php'; ?>

<?php calculatorHeroStart(); ?>
  <h1>Μόρια Μητρώου ΣΔΕ</h1>
  <p>Ενιαίος υπολογιστής για ωρομίσθιο Εκπαιδευτικό Προσωπικό, Συμβούλους Ψυχολόγους και Συμβούλους Σταδιοδρομίας στα Σχολεία Δεύτερης Ευκαιρίας.</p>
  <div class="hero-meta">
    <span>3 κατηγορίες υποψηφίων</span><span>Βασική βαθμολογία έως 40</span><span>Κοινωνικές προσαυξήσεις</span><span>Live υπολογισμός</span><span>ΦΕΚ Β΄ 3224/25.06.2025</span>
  </div>
<?php calculatorHeroEnd(); ?>

<?php calculatorColumnsStart(); ?>
<?php calculatorMainStart(); ?>
  <?php calculatorCardStart(array('header_variant' => 'section-head', 'title_html' => '1. Κατηγορία &amp; προϋποθέσεις ένταξης', 'subtitle_html' => 'Η επιλογή κατηγορίας αλλάζει αυτόματα τα απαιτούμενα προσόντα, τις επιμορφώσεις και την εμπειρία που μοριοδοτείται.')); ?>
    <div class="field-grid">
      <div class="field full"><label for="role">Κατηγορία υποψηφίου</label><select id="role" onchange="roleChanged()"><option value="">— Επίλεξε —</option><option value="educator">Εκπαιδευτικό Προσωπικό</option><option value="psychologist">Σύμβουλος Ψυχολόγος</option><option value="career">Σύμβουλος Σταδιοδρομίας</option></select></div>
    </div>

    <div id="educatorEligibility" class="hidden">
      <div class="field-grid edu-mt-13">
        <div class="field"><label for="specialty">Κλάδος / ειδικότητα</label><select id="specialty" onchange="specialtyChanged()"><option value="">— Επίλεξε —</option></select></div>
        <div class="field"><label for="eoppepAdultTrainer">Πιστοποίηση εκπαιδευτικής επάρκειας Εκπαιδευτή Ενηλίκων ΕΟΠΠΕΠ <small>Δεν δίνει μόρια, αλλά οι πιστοποιημένοι εκπαιδευτές απασχολούνται κατά προτεραιότητα.</small></label><select id="eoppepAdultTrainer" onchange="calculate()"><option value="no">Όχι / δεν έχει δηλωθεί</option><option value="yes">Ναι</option></select></div>
      </div>
      <div id="assignmentPanel" class="note">Επίλεξε κλάδο για να εμφανιστούν τα γνωστικά αντικείμενα Α΄/Β΄ ανάθεσης.</div>
    </div>

    <div id="psychEligibility" class="hidden">
      <div class="field-grid edu-mt-13">
        <div class="field"><label for="psychDegree">Πτυχίο Ψυχολογίας ή ισότιμο/αντίστοιχο</label><select id="psychDegree" onchange="calculate()"><option value="">— Επίλεξε —</option><option value="yes">Ναι</option><option value="no">Όχι</option></select></div>
        <div class="field"><label for="psychLicense">Άδεια άσκησης επαγγέλματος Ψυχολόγου</label><select id="psychLicense" onchange="calculate()"><option value="">— Επίλεξε —</option><option value="yes">Ναι</option><option value="no">Όχι</option></select></div>
        <div class="field"><label for="fppBefore1993">Πτυχίο Φ.Π.Ψ. πριν το 1993;</label><select id="fppBefore1993" onchange="psychFppChanged()"><option value="no">Όχι</option><option value="yes">Ναι</option></select></div>
        <div class="field hidden" id="psychMasterForFppWrap"><label for="psychMasterForFpp">Μεταπτυχιακό στην Ψυχολογία <small>Απαιτείται επιπρόσθετα για πτυχιούχους Φ.Π.Ψ. πριν το 1993.</small></label><select id="psychMasterForFpp" onchange="calculate()"><option value="">— Επίλεξε —</option><option value="yes">Ναι</option><option value="no">Όχι</option></select></div>
      </div>
    </div>

    <div id="careerEligibility" class="hidden">
      <div class="field-grid edu-mt-13">
        <div class="field"><label for="tertiaryDegree">Πτυχίο Α.Ε.Ι. ή ισότιμο της αλλοδαπής</label><select id="tertiaryDegree" onchange="calculate()"><option value="">— Επίλεξε —</option><option value="yes">Ναι</option><option value="no">Όχι</option></select></div>
        <div class="field"><label for="careerQualification">Εξειδίκευση στη Συμβουλευτική / Επαγγελματικό Προσανατολισμό</label><select id="careerQualification" onchange="calculate()"><option value="none">— Δεν διαθέτω / δεν έχω επιλέξει —</option><option value="phd">Διδακτορικό στο πεδίο</option><option value="master">Μεταπτυχιακό στο πεδίο</option><option value="pesyp">ΠΕΣΥΠ ΑΣΠΑΙΤΕ/ΣΕΛΕΤΕ</option><option value="eoppep">Πιστοποίηση επάρκειας Συμβούλου Σταδιοδρομίας/Επαγγελματικού Προσανατολισμού από ΕΟΠΠΕΠ</option></select><div class="small edu-mt-7">Αν η εξειδίκευση είναι διδακτορικό, μεταπτυχιακό ή ΠΕΣΥΠ, δήλωσέ την και στην ενότητα «Εκπαίδευση» για να υπολογιστούν τα αντίστοιχα μόρια.</div></div>
      </div>
    </div>
    <div id="eligibilityInline"></div>
  <?php calculatorCardEnd(); ?>

  <?php calculatorCardStart(array('header_variant' => 'section-head', 'title_html' => '2. Εκπαίδευση', 'subtitle_html' => 'Τυπικά προσόντα έως 18 μόρια + επιμόρφωση έως 4 μόρια.', 'cap_html' => 'έως 22')); ?>
    <h3>Τυπικά προσόντα</h3>
    <div class="field-grid">
      <div class="field"><label for="phd">Διδακτορικό <small id="phdTargetHint">Η συναφής κατεύθυνση αλλάζει ανά κατηγορία.</small></label><select id="phd" onchange="calculate()"><option value="none">Δεν διαθέτω</option><option value="target">Στη μοριοδοτούμενη κατεύθυνση — 11 μόρια</option><option value="other">Σε άλλη κατεύθυνση — 9 μόρια</option></select></div>
      <div class="field"><label for="master">Μεταπτυχιακό <small>Αν υπάρχει και διδακτορικό, το πρώτο μεταπτυχιακό δεν προσμετράται.</small></label><select id="master" onchange="calculate()"><option value="none">Δεν διαθέτω</option><option value="target">Στη μοριοδοτούμενη κατεύθυνση — 8 μόρια</option><option value="other">Σε άλλη κατεύθυνση — 6 μόρια</option></select></div>
      <div class="field"><div class="check-row"><input id="secondDegree" type="checkbox" onchange="calculate()"><label for="secondDegree">Δεύτερο πτυχίο τριτοβάθμιας — 3 μόρια</label></div></div>
      <div class="field"><div class="check-row"><input id="secondPhd" type="checkbox" onchange="calculate()"><label for="secondPhd">Δεύτερο διδακτορικό — 2 μόρια</label></div></div>
      <div class="field"><div class="check-row"><input id="secondMaster" type="checkbox" onchange="calculate()"><label for="secondMaster">Δεύτερο μεταπτυχιακό — 1 μόριο</label></div></div>
      <div class="field"><div class="check-row"><input id="extraCredential" type="checkbox" onchange="calculate()"><label for="extraCredential" id="extraCredentialLabel">Πρόσθετο προσόν — 1 μόριο</label></div></div>
    </div>
    <div class="note">Τα τυπικά προσόντα έχουν ανώτατο όριο 18 μορίων. Αν δηλωθούν ταυτόχρονα πρώτο διδακτορικό και πρώτο μεταπτυχιακό, μοριοδοτείται μόνο το διδακτορικό.</div>

    <h3>Επιμόρφωση</h3>
    <div class="field-grid">
      <div class="field"><label for="trainingSdeHours">Σε θέματα ΣΔΕ — ώρες <small id="trainingSdeMax">0,25 / 100 ώρες</small></label><input id="trainingSdeHours" type="number" min="0" step="1" value="0" oninput="calculate()"></div>
      <div class="field"><label for="trainingAdultHours">Στις αρχές Εκπαίδευσης Ενηλίκων — ώρες <small id="trainingAdultMax">0,25 / 100 ώρες</small></label><input id="trainingAdultHours" type="number" min="0" step="1" value="0" oninput="calculate()"></div>
      <div class="field full hidden" id="trainingThematicWrap"><label for="trainingThematicHours" id="trainingThematicLabel">Θεματική επιμόρφωση — ώρες</label><input id="trainingThematicHours" type="number" min="0" step="1" value="0" oninput="calculate()"></div>
    </div>
    <div class="warning">Μοριοδοτούνται μόνο ολοκληρωμένες επιμορφώσεις. <strong>Κάθε επιμέρους επιμόρφωση κάτω από 15 ώρες λαμβάνει 0 μόρια.</strong> Καταχώρισε μόνο επιλέξιμες ώρες. Δεν μοριοδοτούνται ημερίδες, διημερίδες ή συνέδρια.</div>
    <div class="danger hidden" id="trainingMinimumWarning" aria-live="polite"></div>
    <div class="warning hidden" id="educatorTrainingProof">Για το Εκπαιδευτικό Προσωπικό, το δικαιολογητικό πρέπει να αναφέρει σαφώς φορέα, αντικείμενο, χρονικό διάστημα και διάρκεια αποκλειστικά σε ώρες. Αν δεν αναφέρονται ώρες, μπορούν να αποδειχθούν με το πρόγραμμα της επιμόρφωσης· Υπεύθυνη Δήλωση του υποψηφίου δεν γίνεται αποδεκτή.</div>
  <?php calculatorCardEnd(); ?>

  <?php calculatorCardStart(array('header_variant' => 'section-head', 'title_html' => '3. Εμπειρία', 'subtitle_html' => 'Επίλεξε κατηγορία για να εμφανιστούν τα σωστά πεδία.', 'subtitle_attrs' => array('id' => 'experienceSubtitle'), 'cap_html' => 'έως 13')); ?>
    <div id="educatorExperience" class="field-grid hidden">
      <div class="field"><label for="expSdeHours">Διδασκαλία στα ΣΔΕ — ώρες <small>1 μόριο / 200 ώρες, έως 5.</small></label><input id="expSdeHours" type="number" min="0" step="1" value="0" oninput="calculate()"></div>
      <div class="field"><label for="expAdultHours">Εκπαίδευση Ενηλίκων εκτός ΣΔΕ — ώρες <small>0,5 μόριο / 100 ώρες, έως 4.</small></label><input id="expAdultHours" type="number" min="0" step="1" value="0" oninput="calculate()"></div>
      <div class="field full"><label for="expFormalHours">Τυπική εκπαίδευση ή επαγγελματική κατάρτιση — ώρες <small>Π/θμια, Δ/θμια, Τριτοβάθμια ή Επαγγελματική Κατάρτιση. 1 μόριο / 200 ώρες, έως 4.</small></label><input id="expFormalHours" type="number" min="0" step="1" value="0" oninput="calculate()"></div>
    </div>
    <div id="advisorExperience" class="field-grid hidden">
      <div class="field"><label for="expSdeMonths" id="expSdeMonthsLabel">Εμπειρία στα ΣΔΕ — μήνες</label><input id="expSdeMonths" type="number" min="0" max="480" step="1" value="0" oninput="calculate()"></div>
      <div class="field"><label for="expAdultCounsellingMonths" id="expAdultCounsellingLabel">Συμβουλευτικές υπηρεσίες σε ενήλικες — μήνες</label><input id="expAdultCounsellingMonths" type="number" min="0" max="480" step="1" value="0" oninput="calculate()"></div>
    </div>
    <div id="careerInconsistency" class="warning hidden"><strong>Σημείωση για το ΦΕΚ:</strong> στο άρθρο 12 §2.1 το λεκτικό αναφέρει «μέγιστο αριθμό μορίων 12», αλλά η στήλη του πίνακα δίνει 7 και η συνολική κατηγορία Επαγγελματικής Εμπειρίας είναι 13, ενώ το §2.2 δίνει 6. Ο υπολογιστής χρησιμοποιεί πλαφόν <strong>7</strong>, ως τη μοναδική τιμή που συμφωνεί εσωτερικά με το άθροισμα 13.</div>
  <?php calculatorCardEnd(); ?>

  <?php calculatorCardStart(array('header_variant' => 'section-head', 'title_html' => '4. Άλλα προσόντα', 'subtitle_html' => 'Δύο ξένες γλώσσες και γνώσεις Η/Υ.', 'cap_html' => 'έως 5')); ?>
    <div class="field-grid">
      <div class="field"><label for="language1">Ξένη γλώσσα 1</label><select id="language1" onchange="calculate()"><option value="">— Καμία —</option><option value="english">Αγγλικά</option><option value="french">Γαλλικά</option><option value="german">Γερμανικά</option><option value="italian">Ιταλικά</option><option value="spanish">Ισπανικά</option><option value="other1">Άλλη</option></select><label class="edu-tools-sr-only" for="languageLevel1">Επίπεδο ξένης γλώσσας 1</label><select id="languageLevel1" class="edu-mt-8" onchange="calculate()"><option value="none">— Επίπεδο —</option><option value="B2">Β2 — Καλή</option><option value="C1">C1 — Πολύ καλή</option><option value="C2">C2 — Άριστη</option></select></div>
      <div class="field"><label for="language2">Ξένη γλώσσα 2</label><select id="language2" onchange="calculate()"><option value="">— Καμία —</option><option value="english">Αγγλικά</option><option value="french">Γαλλικά</option><option value="german">Γερμανικά</option><option value="italian">Ιταλικά</option><option value="spanish">Ισπανικά</option><option value="other2">Άλλη</option></select><label class="edu-tools-sr-only" for="languageLevel2">Επίπεδο ξένης γλώσσας 2</label><select id="languageLevel2" class="edu-mt-8" onchange="calculate()"><option value="none">— Επίπεδο —</option><option value="B2">Β2 — Καλή</option><option value="C1">C1 — Πολύ καλή</option><option value="C2">C2 — Άριστη</option></select></div>
      <div class="field full" id="computerField"><div class="check-row"><input id="computer" type="checkbox" onchange="calculate()"><label for="computer" id="computerLabel">Πιστοποιημένη επιμόρφωση ΤΠΕ επιπέδου 1 ΥΠΑΙΘΑ ή αποδεικτικό γνώσης Η/Υ σύμφωνα με την Πρόσκληση — 2 μόρια</label></div><div id="computerPe86Note" class="warning hidden"><strong>ΠΕ86 Πληροφορικής:</strong> όταν το πτυχίο Πληροφορικής χρησιμοποιείται ως βασικό πτυχίο ένταξης στο Μητρώο, το πεδίο «Γνώση Χειρισμού Η/Υ» δεν αξιολογείται και δεν προσθέτει 2 μόρια.</div></div>
    </div>
  <?php calculatorCardEnd(); ?>

  <?php calculatorCardStart(array('header_variant' => 'section-head', 'title_html' => '5. Κοινωνικά κριτήρια', 'subtitle_html' => 'Οι προσαυξήσεις υπολογίζονται επί της βασικής βαθμολογίας που έχει συγκεντρωθεί.')); ?>
    <div class="field-grid">
      <div class="field"><label for="unemploymentMonths">Πλήρεις μήνες ανεργίας <small>+0,5% ανά μήνα, έως 10%.</small></label><input id="unemploymentMonths" type="number" min="0" step="1" value="0" oninput="calculate()"></div>
      <div class="field"><label for="unemploymentExtraDays">Επιπλέον ημέρες <small>15 ημέρες ή περισσότερες λογίζονται ως ένας ακόμη πλήρης μήνας.</small></label><input id="unemploymentExtraDays" type="number" min="0" max="30" step="1" value="0" oninput="calculate()"></div>
      <div class="field"><div class="check-row"><input id="threeChildren" type="checkbox" onchange="calculate()"><label for="threeChildren">Γονέας τρίτεκνης οικογένειας — +10%</label></div></div>
      <div class="field"><div class="check-row"><input id="singleParent" type="checkbox" onchange="calculate()"><label for="singleParent">Μέλος μονογονεϊκής οικογένειας — +10%</label></div></div>
      <div class="field"><div class="check-row"><input id="manyChildren" type="checkbox" onchange="calculate()"><label for="manyChildren">Μέλος πολύτεκνης οικογένειας — +10%</label></div></div>
      <div class="field"><div class="check-row"><input id="disability" type="checkbox" onchange="calculate()"><label for="disability">ΑμεΑ ≥50% υποψηφίου/τέκνου/συζύγου, με τις προβλεπόμενες προϋποθέσεις — +10%</label></div></div>
    </div>
    <div class="note">Το ΦΕΚ διατυπώνει κάθε ειδική κατηγορία ως προσαύξηση 10% επί της βαθμολογίας. Το εργαλείο προσθέτει κάθε κατηγορία που δηλώνεται ως εφαρμοζόμενη και εμφανίζει αναλυτικά την επίδρασή της.</div>
  <?php calculatorCardEnd(); ?>
<?php calculatorMainEnd(); ?>

<?php calculatorResultsStart(array('class' => 'results', 'attrs' => array('aria-live' => 'polite'))); ?>
  <?php calculatorCardStart(); ?>
    <div class="role-chip" id="roleChip">Επίλεξε κατηγορία</div><h2>Αποτέλεσμα</h2>
    <div class="big-total"><div class="context">Τελική βαθμολογία με προσαυξήσεις</div><div class="number" id="finalScore">0</div><div class="outof">Βασική βαθμολογία έως 40 + κοινωνικά κριτήρια</div></div>
    <div class="bar"><div id="baseBar"></div></div>
    <?php calculatorResultRow(array('label_html' => 'Εκπαίδευση', 'value_html' => '0 / 22', 'value_id' => 'educationScore')); ?>
    <?php calculatorResultRow(array('label_html' => 'Εμπειρία', 'value_html' => '0 / 13', 'value_id' => 'experienceScore')); ?>
    <?php calculatorResultRow(array('label_html' => 'Άλλα προσόντα', 'value_html' => '0 / 5', 'value_id' => 'otherScore')); ?>
    <?php calculatorResultRow(array('class' => 'result-row emphasis', 'label_html' => 'Βασική βαθμολογία', 'value_html' => '0 / 40', 'value_id' => 'baseScore')); ?>
    <?php calculatorResultRow(array('label_html' => 'Ανεργία', 'value_html' => '+0', 'value_id' => 'unemploymentScore')); ?>
    <?php calculatorResultRow(array('label_html' => 'Ειδικές κατηγορίες', 'value_html' => '+0', 'value_id' => 'specialScore')); ?>
    <div id="priorityStatus"></div><div id="eligibilityStatus"></div>
    <?php calculatorActions(array(array('attrs' => array('class' => 'primary', 'type' => 'button', 'onclick' => 'copySummary(this)'), 'html' => 'Αντιγραφή αποτελέσματος'), array('attrs' => array('class' => 'secondary', 'type' => 'button', 'onclick' => 'resetForm()'), 'html' => 'Μηδενισμός'))); ?>
  <?php calculatorCardEnd(); ?>
  <?php calculatorCardStart(); ?><h2>Αναλυτική μοριοδότηση</h2><div id="breakdown" class="subtitle">Επίλεξε κατηγορία και συμπλήρωσε τα στοιχεία.</div><?php calculatorCardEnd(); ?>
  <?php calculatorCardStart(); ?><h2>Δικαιολογητικά / έλεγχοι</h2><div id="checklist" class="subtitle">Θα προσαρμοστεί στις επιλογές σου.</div><?php calculatorCardEnd(); ?>
  <?php calculatorCardStart(); ?><h2>Ισοβαθμία</h2><p class="subtitle">Προηγείται ο υποψήφιος με διδακτορικό, έπειτα ο κάτοχος μεταπτυχιακού και τέλος ο κάτοχος πτυχίου κατά βαθμό πτυχίου. Αν παραμένει ισοβαθμία, προβλέπεται δημόσια κλήρωση.</p><?php calculatorCardEnd(); ?>
<?php calculatorResultsEnd(); ?>
<?php calculatorColumnsEnd(); ?>

<section class="edu-source-card" aria-labelledby="sourcesTitle">
  <h2 id="sourcesTitle">Πηγές / Νομική βάση</h2>
  <p><strong>Υ.Α. 75975/Κ1 — ΦΕΚ Β΄ 3224/25.06.2025</strong>, «Κανονισμός Διαχείρισης του Μητρώου Ωρομίσθιου Εκπαιδευτικού Προσωπικού, Συμβούλων Σταδιοδρομίας και Συμβούλων Ψυχολόγων στα Σχολεία Δεύτερης Ευκαιρίας (Σ.Δ.Ε.) — Καθορισμός της διαδικασίας και των κριτηρίων επιλογής και μοριοδότησης».</p>
  <p><strong>Άρθρο 3:</strong> προϋποθέσεις ένταξης και κλάδοι εκπαιδευτικού προσωπικού. <strong>Άρθρα 10–12:</strong> μοριοδότηση Εκπαιδευτικού Προσωπικού, Συμβούλων Ψυχολόγων και Συμβούλων Σταδιοδρομίας. <strong>Άρθρο 14:</strong> προτεραιότητα πιστοποιημένων Εκπαιδευτών Ενηλίκων ΕΟΠΠΕΠ.</p>
  <p><strong>Πρόσκληση Μητρώου ΣΔΕ 2025–2026, Κεφάλαιο Γ §9:</strong> οι υποψήφιοι ΠΕ86 που χρησιμοποιούν πτυχίο Πληροφορικής ως βασικό πτυχίο ένταξης δεν αξιολογούνται στο πεδίο «Γνώση Χειρισμού Η/Υ».</p>
  <div class="source-links"><a href="https://gsvetlly.minedu.gov.gr/publications/docs2023/DELTIA_TYPOU/2025/%CE%91%CE%A0%CE%9F%CE%A6%CE%91%CE%A3%CE%97_%CE%93%CE%99%CE%91_%CE%A3%CE%94%CE%95_%CE%A6%CE%95%CE%9A_3224%CE%92-25.06.2025.pdf" target="_blank" rel="noopener noreferrer">Υ.Α. 75975/Κ1 — ΦΕΚ Β΄ 3224/2025 (ΥΠΑΙΘΑ) ↗</a> <a href="https://gsvetlly.minedu.gov.gr/nea-anakoinoseis/512-prosklese-ekdeloses-endiapherontos-ypoboles-aiteses-entaxes-kai-epikairopoieses-prosthekes-stoicheion-sto-metroo-oromisthiou-ekpaideutikou-prosopikou-symboulon-stadiodromias-kai-symboulon-psychologon-ton-scholeion-deuteres-eukairias-metroo-s-d-e-1o-stadio" target="_blank" rel="noopener noreferrer">Πρόσκληση Μητρώου ΣΔΕ 2025–2026 — ΥΠΑΙΘΑ ↗</a></div>
  <p class="source-disclaimer">Ο υπολογισμός είναι ενημερωτικός. Ειδικά για την ασυμφωνία του άρθρου 12 §2.1, το εργαλείο χρησιμοποιεί το πλαφόν 7 μορίων που συμφωνεί με τη στήλη του πίνακα και με το συνολικό όριο των 13 μορίων της κατηγορίας.</p>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
</main>
<script src="includes/sde-registry-calculations.js?v=3.20.33"></script>
<script>
const $=id=>document.getElementById(id); const val=id=>$(id)?.value||''; const checked=id=>!!$(id)?.checked;
const fmt=x=>Number(x||0).toLocaleString('el-GR',{maximumFractionDigits:2,minimumFractionDigits:Number.isInteger(Number(x||0))?0:1});
function boolSelect(id){const v=val(id);return v==='yes'?true:v==='no'?false:null;}

function fillSpecialties(){const s=$('specialty');Object.entries(SDERegistryCalc.SPECIALTIES).forEach(([v,t])=>{const o=document.createElement('option');o.value=v;o.textContent=t;s.appendChild(o);});}
function show(id,on){$(id).classList.toggle('hidden',!on);}
function pe86ComputerExcluded(){return val('role')==='educator'&&val('specialty')==='PE86';}
function updateComputerUI(){const excluded=pe86ComputerExcluded(),c=$('computer');if(excluded)c.checked=false;c.disabled=excluded;show('computerPe86Note',excluded);}
function specialtyChanged(){updateComputerUI();calculate();}
function roleChanged(){
  const role=val('role');
  show('educatorEligibility',role==='educator');show('psychEligibility',role==='psychologist');show('careerEligibility',role==='career');
  show('educatorExperience',role==='educator');show('advisorExperience',role==='psychologist'||role==='career');show('trainingThematicWrap',role==='psychologist'||role==='career');show('educatorTrainingProof',role==='educator');show('careerInconsistency',role==='career');
  if(role==='educator'){$('phdTargetHint').textContent='Μέγιστη μοριοδότηση για Εκπαίδευση Ενηλίκων, Συνεχιζόμενη Εκπαίδευση ή Διά Βίου Εκπαίδευση.';$('extraCredentialLabel').textContent='Αποδεικτικό Παιδαγωγικής και Διδακτικής Επάρκειας κατά ΑΣΕΠ — 1 μόριο';$('trainingSdeMax').textContent='0,25 / 100 ώρες, έως 2 μόρια';$('trainingAdultMax').textContent='0,25 / 100 ώρες, έως 2 μόρια';$('experienceSubtitle').textContent='Διδακτική εμπειρία σε ώρες: ΣΔΕ, Εκπαίδευση Ενηλίκων εκτός ΣΔΕ και τυπική εκπαίδευση/κατάρτιση.';}
  if(role==='psychologist'){$('phdTargetHint').textContent='Μέγιστη μοριοδότηση για μεταπτυχιακές σπουδές στην Ψυχολογία.';$('extraCredentialLabel').textContent='ΠΕΣΥΠ ΑΣΠΑΙΤΕ/ΣΕΛΕΤΕ — 1 μόριο';$('trainingSdeMax').textContent='0,25 / 100 ώρες, έως 1 μόριο';$('trainingAdultMax').textContent='0,25 / 100 ώρες, έως 1 μόριο';$('trainingThematicLabel').textContent='Στο θεματικό αντικείμενο της Ψυχολογίας — ώρες (έως 2 μόρια)';$('expSdeMonthsLabel').innerHTML='Στα ΣΔΕ ως Σύμβουλος Ψυχολόγος — μήνες <small>0,25 μόρια/μήνα, έως 7.</small>';$('expAdultCounsellingLabel').innerHTML='Σε προγράμματα παροχής συμβουλευτικών υπηρεσιών σε ενήλικες — μήνες <small>0,25 μόρια/μήνα, έως 6.</small>';$('experienceSubtitle').textContent='Επαγγελματική εμπειρία σε μήνες ως Σύμβουλος Ψυχολόγος.';}
  if(role==='career'){$('phdTargetHint').textContent='Μέγιστη μοριοδότηση για Συμβουλευτική και Επαγγελματικό Προσανατολισμό / Συμβουλευτική Σταδιοδρομίας.';$('extraCredentialLabel').textContent='ΠΕΣΥΠ ΑΣΠΑΙΤΕ/ΣΕΛΕΤΕ — 1 μόριο';$('trainingSdeMax').textContent='0,25 / 100 ώρες, έως 1 μόριο';$('trainingAdultMax').textContent='0,25 / 100 ώρες, έως 1 μόριο';$('trainingThematicLabel').textContent='Στο θεματικό αντικείμενο του Επαγγελματικού Προσανατολισμού — ώρες (έως 2 μόρια)';$('expSdeMonthsLabel').innerHTML='Στα ΣΔΕ ως Σύμβουλος Σταδιοδρομίας — μήνες <small>0,25 μόρια/μήνα, πλαφόν 7 στο εργαλείο.</small>';$('expAdultCounsellingLabel').innerHTML='Σε προγράμματα συμβουλευτικών υπηρεσιών απασχόλησης και επιχειρηματικότητας σε ενήλικες — μήνες <small>0,25 μόρια/μήνα, έως 6.</small>';$('experienceSubtitle').textContent='Επαγγελματική εμπειρία σε μήνες ως Σύμβουλος Σταδιοδρομίας.';}
  updateComputerUI();
  calculate();
}
function psychFppChanged(){show('psychMasterForFppWrap',val('fppBefore1993')==='yes');calculate();}
function data(){
 const role=val('role');
 return {role,specialty:val('specialty'),eoppepAdultTrainer:val('eoppepAdultTrainer')==='yes',psychDegree:boolSelect('psychDegree'),psychLicense:boolSelect('psychLicense'),fppBefore1993:val('fppBefore1993')==='yes',psychMasterForFpp:boolSelect('psychMasterForFpp'),tertiaryDegree:boolSelect('tertiaryDegree'),careerQualification:val('careerQualification'),phd:val('phd'),master:val('master'),secondDegree:checked('secondDegree'),secondPhd:checked('secondPhd'),secondMaster:checked('secondMaster'),extraCredential:checked('extraCredential'),extraCredentialLabel:$('extraCredentialLabel').textContent.replace(/ — 1 μόριο$/,''),trainingSdeHours:val('trainingSdeHours'),trainingAdultHours:val('trainingAdultHours'),trainingThematicHours:val('trainingThematicHours'),thematicTrainingLabel:role==='psychologist'?'Επιμόρφωση στο αντικείμενο της Ψυχολογίας':'Επιμόρφωση στον Επαγγελματικό Προσανατολισμό',expSdeHours:val('expSdeHours'),expAdultHours:val('expAdultHours'),expFormalHours:val('expFormalHours'),expSdeMonths:val('expSdeMonths'),expAdultCounsellingMonths:val('expAdultCounsellingMonths'),language1:val('language1'),languageLevel1:val('languageLevel1'),language2:val('language2'),languageLevel2:val('languageLevel2'),computer:checked('computer'),unemploymentMonths:val('unemploymentMonths'),unemploymentExtraDays:val('unemploymentExtraDays'),threeChildren:checked('threeChildren'),singleParent:checked('singleParent'),manyChildren:checked('manyChildren'),disability:checked('disability')};
}
function renderAssignments(r){
 const p=$('assignmentPanel'); if(val('role')!=='educator'){p.innerHTML='';return;} if(!val('specialty')){p.className='note';p.innerHTML='Επίλεξε κλάδο για να εμφανιστούν τα γνωστικά αντικείμενα Α΄/Β΄ ανάθεσης.';return;} if(!r.assignments.length){p.className='danger';p.innerHTML='Ο κλάδος δεν αντιστοιχεί στον πίνακα του άρθρου 3.';return;} p.className='note';p.innerHTML='<strong>Αναθέσεις για τον κλάδο:</strong><div class="assignment-list">'+r.assignments.map(x=>'<div class="assignment-item"><span>'+x.literacy+'</span><strong>'+x.assignment+'</strong></div>').join('')+'</div>';
}
function renderEligibility(r){const e=r.eligibility;let html='';if(e.blockers.length)html='<div class="danger"><strong>Δεν φαίνεται να πληρούνται οι προϋποθέσεις ένταξης:</strong><ul>'+e.blockers.map(x=>'<li>'+x+'</li>').join('')+'</ul></div>';else if(e.pending.length)html='<div class="warning"><strong>Χρειάζεται συμπλήρωση:</strong><ul>'+e.pending.map(x=>'<li>'+x+'</li>').join('')+'</ul></div>';else if(val('role'))html='<div class="success"><strong>Βασικός έλεγχος:</strong> οι δηλωμένες προϋποθέσεις ένταξης φαίνονται να καλύπτονται.</div>';$('eligibilityInline').innerHTML=html;$('eligibilityStatus').innerHTML=html;}
function renderTrainingMinimumWarning(d){
 const items=[];
 const add=(hours,label)=>{const h=Number(hours)||0;if(h>0&&h<15)items.push(label+' ('+h+' ώρες)');};
 add(d.trainingSdeHours,'Επιμόρφωση σε θέματα ΣΔΕ');
 add(d.trainingAdultHours,'Επιμόρφωση στις αρχές Εκπαίδευσης Ενηλίκων');
 if(d.role==='psychologist'||d.role==='career')add(d.trainingThematicHours,d.thematicTrainingLabel||'Θεματική επιμόρφωση');
 const box=$('trainingMinimumWarning');
 if(items.length){box.classList.remove('hidden');box.innerHTML='<strong>0 μόρια για επιμόρφωση κάτω από 15 ώρες:</strong> '+items.join(' · ');}else{box.classList.add('hidden');box.innerHTML='';}
}
function renderBreakdown(r){let items=[];r.education.formal.details.forEach(x=>items.push(x));r.education.training.details.forEach(x=>items.push(x));r.experience.details.forEach(x=>items.push(x));r.other.details.forEach(x=>items.push(x));let h='<ul class="breakdown-list">'+items.map(x=>'<li><span>'+x.label+'</span><strong>'+fmt(x.points)+'</strong></li>').join('')+'</ul>';if(r.warnings.length)h+='<div class="warning">'+r.warnings.join('<br>')+'</div>';$('breakdown').innerHTML=items.length?h:'Δεν έχουν προκύψει ακόμη μόρια.';}
function renderChecklist(r,d){let a=[];if(d.role==='educator'){a.push('Τίτλος σπουδών που αντιστοιχεί στον δηλωμένο κλάδο.');if(d.eoppepAdultTrainer)a.push('Πιστοποίηση εκπαιδευτικής επάρκειας Εκπαιδευτή Ενηλίκων ΕΟΠΠΕΠ.');}if(d.role==='psychologist'){a.push('Πτυχίο Ψυχολογίας.','Άδεια άσκησης επαγγέλματος Ψυχολόγου.');if(d.fppBefore1993)a.push('Μεταπτυχιακό στην Ψυχολογία λόγω πτυχίου Φ.Π.Ψ. πριν το 1993.');}if(d.role==='career'){a.push('Πτυχίο Α.Ε.Ι. ή ισότιμο.','Δικαιολογητικό εξειδίκευσης/πιστοποίησης στη Συμβουλευτική και τον Επαγγελματικό Προσανατολισμό.');}if(d.phd!=='none')a.push('Δικαιολογητικό διδακτορικού.');if(d.master!=='none')a.push('Δικαιολογητικό μεταπτυχιακού.');if(Number(d.trainingSdeHours)>=15||Number(d.trainingAdultHours)>=15||((d.role==='psychologist'||d.role==='career')&&Number(d.trainingThematicHours)>=15))a.push('Βεβαιώσεις ολοκληρωμένων επιμορφώσεων με τα απαιτούμενα στοιχεία.');if(d.language1||d.language2)a.push('Πιστοποιητικά ξένης γλώσσας σύμφωνα με ΑΣΕΠ.');if(d.computer&&!pe86ComputerExcluded())a.push('Πιστοποίηση ΤΠΕ επιπέδου 1 ή αποδεικτικό γνώσης Η/Υ σύμφωνα με την Πρόσκληση.');if(r.social.selected.length||r.social.unemploymentPercent)a.push('Δικαιολογητικά κοινωνικών κριτηρίων / ανεργίας.');$('checklist').innerHTML=a.length?'<ul class="criteria-list">'+a.map(x=>'<li>'+x+'</li>').join('')+'</ul>':'Συμπλήρωσε τα στοιχεία για δυναμικό checklist.';}
function calculate(){const d=data(),r=SDERegistryCalc.calculateAll(d);$('roleChip').textContent=SDERegistryCalc.ROLE[d.role]||'Επίλεξε κατηγορία';$('educationScore').textContent=fmt(r.education.total)+' / 22';$('experienceScore').textContent=fmt(r.experience.total)+' / 13';$('otherScore').textContent=fmt(r.other.total)+' / 5';$('baseScore').textContent=fmt(r.base)+' / 40';$('finalScore').textContent=fmt(r.final);$('unemploymentScore').textContent='+'+fmt(r.social.unemploymentPoints)+' ('+fmt(r.social.unemploymentPercent)+'%)';$('specialScore').textContent='+'+fmt(r.social.specialPoints)+' ('+r.social.specialPercent+'%)';$('baseBar').style.width=Math.min(100,r.base/40*100)+'%';renderAssignments(r);renderEligibility(r);renderTrainingMinimumWarning(d);renderBreakdown(r);renderChecklist(r,d);$('priorityStatus').innerHTML=r.eoppepPriority?'<div class="priority"><strong>Προτεραιότητα ΕΟΠΠΕΠ:</strong> δηλώθηκε πιστοποίηση εκπαιδευτικής επάρκειας Εκπαιδευτή Ενηλίκων. Η απόφαση προβλέπει προτεραιότητα των πιστοποιημένων εκπαιδευτών.</div>':'';}
function copySummary(btn){const d=data(),r=SDERegistryCalc.calculateAll(d);let t='Μόρια Μητρώου ΣΔΕ\nΚατηγορία: '+(SDERegistryCalc.ROLE[d.role]||'—')+'\nΕκπαίδευση: '+fmt(r.education.total)+'/22\nΕμπειρία: '+fmt(r.experience.total)+'/13\nΆλλα προσόντα: '+fmt(r.other.total)+'/5\nΒασική βαθμολογία: '+fmt(r.base)+'/40\nΑνεργία: +'+fmt(r.social.unemploymentPoints)+' ('+fmt(r.social.unemploymentPercent)+'%)\nΕιδικές κατηγορίες: +'+fmt(r.social.specialPoints)+' ('+r.social.specialPercent+'%)\nΤελικό: '+fmt(r.final);navigator.clipboard?.writeText(t).then(()=>{const old=btn.textContent;btn.textContent='Αντιγράφηκε ✓';setTimeout(()=>btn.textContent=old,1400);});}
function resetForm(){document.querySelectorAll('input[type="number"]').forEach(x=>x.value='0');document.querySelectorAll('input[type="checkbox"]').forEach(x=>x.checked=false);document.querySelectorAll('select').forEach(x=>x.selectedIndex=0);$('fppBefore1993').value='no';$('eoppepAdultTrainer').value='no';roleChanged();psychFppChanged();}
document.addEventListener('input',function(event){const el=event.target;if(!el||el.tagName!=='INPUT'||el.type!=='number'||el.value==='')return;let value=Number(el.value);if(!Number.isFinite(value))return;const min=el.getAttribute('min'),max=el.getAttribute('max');if(min!==null&&min!=='')value=Math.max(Number(min),value);if(max!==null&&max!=='')value=Math.min(Number(max),value);if(value!==Number(el.value))el.value=String(value);});
fillSpecialties();roleChanged();
</script>
<script src="assets/common.js?v=3.20.13"></script>
</body></html>
