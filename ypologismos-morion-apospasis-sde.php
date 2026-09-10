<?php require_once __DIR__ . '/includes/config.php'; ?>
<?php require_once __DIR__ . '/includes/teacher-specialties.php'; ?>
<?php require_once __DIR__ . '/includes/components/asep-language-selector.php'; ?>
<!doctype html>
<html lang="el">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="Υπολογισμός μορίων απόσπασης μονίμων εκπαιδευτικών στα Σχολεία Δεύτερης Ευκαιρίας (ΣΔΕ) και έλεγχος αποδεκτών ειδικοτήτων/γραμματισμών σύμφωνα με το ΦΕΚ Β' 4088/03.07.2026.">
  <title>Υπολογισμός μορίων απόσπασης στα ΣΔΕ</title>
<link rel="stylesheet" href="<?php echo htmlspecialchars(edu_asset_url('assets/common.css'), ENT_QUOTES, 'UTF-8'); ?>">
</head>
<body class="edu-ui edu-calc-sde edu-page-sde-apospasis">
<main class="page-shell">
  <?php require_once __DIR__ . '/includes/header.php'; ?>
<?php require_once __DIR__ . '/includes/components/calculator-layout.php'; ?>
<?php require_once __DIR__ . '/includes/components/deadline-card.php'; ?>

  <?php calculatorHero(array(
    'title' => 'Υπολογισμός μορίων απόσπασης στα ΣΔΕ',
    'intro' => 'Ενδεικτικός υπολογισμός μορίων και έλεγχος αποδεκτής ειδικότητας/γραμματισμού για απόσπαση μονίμων εκπαιδευτικών στα Σχολεία Δεύτερης Ευκαιρίας.',
    'meta_class' => 'hero-meta',
    'badges' => array('Σύνολο: 40 μόρια', 'Εκπαίδευση: 22', 'Διδακτική εμπειρία: 13', 'Άλλα προσόντα: 5')
)); ?>

  <div class="success sde-update-banner"><strong>Ενημερωμένος πίνακας μοριοδότησης:</strong> ο υπολογισμός εφαρμόζει τη Διόρθωση Σφάλματος του ΦΕΚ Β΄ 4199/10.07.2026 (Εκπαίδευση 22, Άλλα προσόντα 5, Η/Υ 2 και μοριοδότηση τυπικής εκπαίδευσης από το 1ο έτος).</div>

  <?php
renderDeadlineCard(array(
    'title' => '📅 Προθεσμία αιτήσεων ΣΔΕ 2026–2027',
    'intro' => 'Η επίσημη πρόσκληση όρισε υποβολή αιτήσεων από <strong>Τρίτη 14 Ιουλίου 2026</strong> έως και <strong>Δευτέρα 20 Ιουλίου 2026</strong>.',
    'items' => array(array(
        'title' => 'Απόσπαση μόνιμων εκπαιδευτικών στα ΣΔΕ',
        'meta_html' => 'Ηλεκτρονική υποβολή για το σχολικό έτος <strong>2026–2027</strong>.',
        'start' => '2026-07-14T00:00:00+03:00',
        'end_exclusive' => '2026-07-21T00:00:00+03:00',
        'source_url' => 'https://diavgeia.gov.gr/doc/%CE%A19%CE%A0646%CE%9D%CE%9A%CE%A0%CE%94-%CE%A36%CE%A5?inline=true',
        'source_label' => 'Επίσημη πρόσκληση — ΑΔΑ Ρ9Π646ΝΚΠΔ-Σ6Υ ↗',
        'closed_text' => 'Η προθεσμία αιτήσεων ΣΔΕ 2026–2027 έχει λήξει.'
    )),
    'note_html' => '<strong>Σημείωση ώρας:</strong> η πρόσκληση αναφέρει ημερομηνίες χωρίς συγκεκριμένη ώρα. Για το countdown θεωρείται τεχνικά ως όριο το τέλος της 20ής Ιουλίου σε ώρα Ελλάδας· υπερισχύει πάντοτε η επίσημη πρόσκληση.'
));
?>

  <?php calculatorColumnsStart(); ?>
    <?php calculatorMainStart(); ?>
      <?php calculatorCardStart(array('header_variant' => 'section-head', 'title_html' => '1. Ειδικότητα &amp; αποδεκτοί γραμματισμοί', 'subtitle_html' => 'Επίλεξε τον κλάδο σου για να εμφανιστούν οι Α΄/Β΄ αναθέσεις που προβλέπονται στο άρθρο 5.')); ?>
        <div class="field-grid">
          <div class="field">
            <label for="specialty">Κλάδος / ειδικότητα</label>
            <select id="specialty">
              <option value="">— Επιλογή —</option>
<?php
$sdeSecondmentSpecialties = array(
    'PE01', 'PE02', 'PE03', 'PE04.01', 'PE04.02', 'PE04.03', 'PE04.04', 'PE04.05',
    'PE06', 'PE70', 'PE78', 'PE80', 'PE85', 'PE86', 'PE87.01', 'PE88.01', 'PE88.05'
);
foreach ($sdeSecondmentSpecialties as $code) {
    echo '<option value="' . htmlspecialchars($code, ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars(teacherSpecialtyDisplayFromInternal($code), ENT_QUOTES, 'UTF-8') . '</option>';
}
?>
              <option value="OTHER">Άλλη ειδικότητα</option>
            </select>
          </div>
          <div class="field">
            <label for="eligibilitySchoolYears">Διδακτική υπηρεσία σε σχολεία Πρωτοβάθμιας ή Δευτεροβάθμιας Εκπαίδευσης <small>Για δικαίωμα αίτησης απαιτούνται τουλάχιστον 2 χρόνια. Το πεδίο αυτό χρησιμοποιείται μόνο για τον έλεγχο επιλεξιμότητας.</small></label>
            <input type="number" id="eligibilitySchoolYears" min="0" max="50" step="0.01" value="" inputmode="decimal" placeholder="π.χ. 2">
          </div>
          <div class="field">
            <label for="formalEducationYears">Συνολικά πλήρη σχολικά έτη διδακτικού έργου στην τυπική εκπαίδευση <small>Πρωτοβάθμια / Δευτεροβάθμια / Τριτοβάθμια. Με τη διόρθωση Β΄ 4199/2026: 1 μόριο από το 1ο πλήρες σχολικό έτος, έως 4.</small></label>
            <input type="number" id="formalEducationYears" min="0" max="50" step="1" value="" inputmode="numeric" placeholder="π.χ. 6">
          </div>
          <div class="field hidden" id="mathInfoDegreeWrap">
            <label for="mathInfoDegree">Πληροίς την προϋπόθεση «πτυχίο Μαθηματικών ή Πληροφορικής» που αναγράφεται για τη Β΄ ανάθεση στα Μαθηματικά;</label>
            <select id="mathInfoDegree"><option value="no">Όχι / δεν είμαι βέβαιος</option><option value="yes">Ναι</option></select>
          </div>
          <div class="field hidden" id="formerPE09Wrap">
            <label for="formerPE09">Το πτυχίο σου αντιστοιχεί σε πρώην ΠΕ09 ή ΠΕ15; <small>Δίνει προτεραιότητα στην Κοινωνική Εκπαίδευση για ΠΕ80.</small></label>
            <select id="formerPE09"><option value="no">Όχι</option><option value="yes">Ναι</option></select>
          </div>
          <div class="field hidden" id="formerPE1208Wrap">
            <label for="formerPE1208">Το πτυχίο σου αντιστοιχεί σε πρώην ΠΕ12.08; <small>Δίνει προτεραιότητα στους σχετικούς γραμματισμούς για ΠΕ85.</small></label>
            <select id="formerPE1208"><option value="no">Όχι</option><option value="yes">Ναι</option></select>
          </div>
          <div class="field">
            <label for="teleEducation">Αποδέχεσαι παροχή εκπαιδευτικού έργου με σύγχρονη τηλεκπαίδευση; <small>Δεν δίνει μόρια, αλλά χρησιμοποιείται ως πρώτο κριτήριο σε περίπτωση ισοβαθμίας.</small></label>
            <select id="teleEducation"><option value="">— Επίλεξε —</option><option value="yes">Ναι</option><option value="no">Όχι</option></select>
          </div>
          <div class="field full">
            <label for="blockingIssue">Υπάρχει κάποιο κώλυμα υποβολής αίτησης του άρθρου 4;</label>
            <select id="blockingIssue"><option value="">— Επίλεξε —</option><option value="no">Όχι</option><option value="yes">Ναι / πιθανόν</option></select>
            <details><summary>Ενδεικτικά κωλύματα</summary><ul class="criteria-list"><li>δοκιμαστική υπηρεσία χωρίς πράξη μονιμοποίησης, διαθεσιμότητα ή αργία,</li><li>απαγόρευση υπηρεσιακών μεταβολών ή υποχρεωτική υπηρεσία,</li><li>υποχρεωτική υπηρεσία λόγω διορισμού σε δυσπρόσιτο,</li><li>θέση στελέχους ή θέση με θητεία,</li><li>ανάκληση/διακοπή απόσπασης σε δομή της Γ.Γ.Ε.Ε.Κ. &amp; Δ.Β.Μ. μέσα στην τελευταία τριετία, όπου εφαρμόζεται.</li></ul></details>
          </div>
        </div>
        <div id="assignmentBox" class="info">Επίλεξε ειδικότητα για να δεις τους αποδεκτούς γραμματισμούς.</div>
        <div class="note"><strong>Σειρά επιλογής:</strong> στις θέσεις των ΣΔΕ εφαρμόζεται η σειρά προτεραιότητας των γραμματισμών και της Α΄/Β΄ ανάθεσης, ανεξάρτητα από το συνολικό σκορ. Στα εκτός έδρας τμήματα λαμβάνεται υπόψη μόνο το σύνολο των μορίων. Σε ισοβαθμία προηγείται αρχικά όποιος έχει αποδεχτεί σύγχρονη τηλεκπαίδευση.</div>
        <details>
          <summary>Προβολή όλων των αποδεκτών ειδικοτήτων / αναθέσεων</summary>
          <div class="mapping-wrap">
            <table class="mapping-table">
              <thead><tr><th>Γνωστικό αντικείμενο</th><th>Α΄ ανάθεση</th><th>Β΄ ανάθεση</th></tr></thead>
              <tbody>
                <tr><td>Ελληνική Γλώσσα</td><td>ΠΕ02</td><td>—</td></tr>
                <tr><td>Μαθηματικά</td><td>ΠΕ03</td><td>ΠΕ04, ΠΕ86 (με πτυχίο Μαθηματικών ή Πληροφορικής)</td></tr>
                <tr><td>Πληροφορική</td><td>ΠΕ86</td><td>—</td></tr>
                <tr><td>Αγγλική Γλώσσα</td><td>ΠΕ06</td><td>—</td></tr>
                <tr><td>Κοινωνική Εκπαίδευση</td><td>ΠΕ78</td><td>ΠΕ01, ΠΕ02, ΠΕ80 (προτεραιότητα σε πτυχία πρώην ΠΕ09 και ΠΕ15)</td></tr>
                <tr><td>Επιστημονικός Γραμματισμός</td><td>ΠΕ04, ΠΕ85 (προτεραιότητα σε πτυχία πρώην ΠΕ12.08)</td><td>ΠΕ03, ΠΕ87.01, ΠΕ88.01</td></tr>
                <tr><td>Περιβαλλοντική Εκπαίδευση</td><td>ΠΕ04.05, ΠΕ88.01, ΠΕ88.05</td><td>ΠΕ04.01, ΠΕ04.02, ΠΕ04.03, ΠΕ04.04, ΠΕ85 (προτεραιότητα σε πτυχία πρώην ΠΕ12.08)</td></tr>
                <tr><td>Τμήματα προετοιμασίας για απολυτήριο Δημοτικού</td><td>ΠΕ70</td><td>—</td></tr>
              </tbody>
            </table>
          </div>
        </details>
      <?php calculatorCardEnd(); ?>

      <?php calculatorCardStart(array('header_variant' => 'section-head', 'title_html' => '2. Εκπαίδευση', 'subtitle_html' => 'Τυπικά προσόντα έως 18 + επιμόρφωση έως 4.', 'cap_html' => 'έως 22')); ?>
        <div class="field-grid">
          <div class="field">
            <label for="phd">Διδακτορικό <small>11 μόρια στην Εκπαίδευση Ενηλίκων / Συνεχιζόμενη / Διά Βίου, 10 σε άλλη κατεύθυνση.</small></label>
            <select id="phd"><option value="none">Κανένα</option><option value="adult">Εκπαίδευση Ενηλίκων / Συνεχιζόμενη / Διά Βίου — 11</option><option value="other">Άλλη κατεύθυνση — 10</option></select>
          </div>
          <div class="field">
            <label for="master">Μεταπτυχιακό <small>8 μόρια στην Εκπαίδευση Ενηλίκων / Συνεχιζόμενη / Διά Βίου, 7 σε άλλη κατεύθυνση. Αν δηλωθεί και διδακτορικό, αυτός ο τίτλος δεν προσμετράται.</small></label>
            <select id="master"><option value="none">Κανένα</option><option value="adult">Εκπαίδευση Ενηλίκων / Συνεχιζόμενη / Διά Βίου — 8</option><option value="other">Άλλη κατεύθυνση — 7</option></select>
          </div>
          <div class="field">
            <label for="secondDegree">Δεύτερο πτυχίο Τριτοβάθμιας Εκπαίδευσης <small>+4 μόρια. Δήλωσέ το μόνο αν δεν αποτέλεσε προσόν διορισμού.</small></label>
            <select id="secondDegree"><option value="no">Όχι</option><option value="yes">Ναι</option></select>
          </div>
          <div class="field">
            <label for="secondPhd">Δεύτερο διδακτορικό <small>2 μόρια στην Εκπαίδευση Ενηλίκων κ.λπ., 1 σε άλλη κατεύθυνση.</small></label>
            <select id="secondPhd"><option value="none">Κανένα</option><option value="adult">Εκπαίδευση Ενηλίκων / Συνεχιζόμενη / Διά Βίου — 2</option><option value="other">Άλλη κατεύθυνση — 1</option></select>
          </div>
          <div class="field">
            <label for="secondMaster">Δεύτερο μεταπτυχιακό <small>1 μόριο στην Εκπαίδευση Ενηλίκων κ.λπ.· σε άλλη κατεύθυνση εφαρμόζεται η μείωση κατά 1 μόριο, άρα 0. Χρησιμοποίησέ το μόνο για διακριτό δεύτερο μεταπτυχιακό τίτλο.</small></label>
            <select id="secondMaster"><option value="none">Κανένα</option><option value="adult">Εκπαίδευση Ενηλίκων / Συνεχιζόμενη / Διά Βίου — 1</option><option value="other">Άλλη κατεύθυνση — 0</option></select>
          </div>
          <div class="field">
            <label for="sdeTrainingHours">Ώρες ολοκληρωμένης επιμόρφωσης σε θέματα ΣΔΕ <small>0,25 μόρια ανά 100 ώρες, έως 2. Κάθε επιμέρους επιμόρφωση κάτω των 15 ωρών λαμβάνει 0 μόρια.</small></label>
            <input type="number" id="sdeTrainingHours" min="0" step="1" value="0">
          </div>
          <div class="field">
            <label for="adultTrainingHours">Ώρες ολοκληρωμένης επιμόρφωσης στις αρχές Εκπαίδευσης Ενηλίκων <small>0,25 μόρια ανά 100 ώρες, έως 2. Κάθε επιμέρους επιμόρφωση κάτω των 15 ωρών λαμβάνει 0 μόρια.</small></label>
            <input type="number" id="adultTrainingHours" min="0" step="1" value="0">
          </div>
        </div>
        <div class="note"><strong>Επιμόρφωση:</strong> δήλωσε μόνο ολοκληρωμένες επιμορφώσεις από δημόσιους ή ιδιωτικούς φορείς εκπαίδευσης. Δεν μοριοδοτούνται επιμορφώσεις κάτω των 15 ωρών, ημερίδες/διημερίδες/συνέδρια ούτε επιμόρφωση που ήταν προαπαιτούμενη για πιστοποίηση εκπαιδευτών Μητρώου ΕΟΠΠΕΠ/ΕΚΕΠΙΣ.</div>
        <div class="warning">Δεν μοριοδοτείται τίτλος σπουδών που αποτέλεσε προσόν διορισμού. Αν υπάρχει διδακτορικό και μεταπτυχιακός τίτλος, το ΦΕΚ ορίζει ότι μοριοδοτείται μόνο το διδακτορικό. Το εργαλείο δεν προσμετρά τον πρώτο μεταπτυχιακό όταν δηλώνεται διδακτορικό. Το «δεύτερο μεταπτυχιακό» παραμένει ξεχωριστό πεδίο, όπως στον πίνακα του ΦΕΚ.</div>
      <?php calculatorCardEnd(); ?>

      <?php calculatorCardStart(array('header_variant' => 'section-head', 'title_html' => '3. Διδακτική εμπειρία', 'subtitle_html' => 'Μοριοδοτείται μόνο διδακτική εμπειρία σύμφωνα με τους ειδικούς κανόνες του άρθρου 6.', 'cap_html' => 'έως 13')); ?>
        <div class="field-grid">
          <div class="field">
            <label for="sdeYears">Πλήρη σχολικά έτη διδακτικής εμπειρίας σε ΣΔΕ <small>1 μόριο ανά σχολικό έτος, έως 5.</small></label>
            <input type="number" id="sdeYears" min="0" max="50" step="1" value="0">
          </div>
          <div class="field">
            <label for="sdeHourlyHours">Ώρες ωρομίσθιας απασχόλησης σε ΣΔΕ <small>650 ώρες = 1 έτος = 1 μόριο. Μην καταχωρίζεις εδώ χρόνο που έχεις ήδη δηλώσει ως πλήρες έτος.</small></label>
            <input type="number" id="sdeHourlyHours" min="0" step="1" value="0">
          </div>
          <div class="field">
            <label for="adultEducationHours">Ώρες διδακτικού έργου στην Εκπαίδευση Ενηλίκων εκτός ΣΔΕ <small>0,5 μόριο ανά 100 ώρες, έως 4.</small></label>
            <input type="number" id="adultEducationHours" min="0" step="1" value="0">
          </div>
          <div class="field">
            <label>Τυπική εκπαίδευση <small>Με τη διόρθωση σφάλματος Β΄ 4199/2026: 1 μόριο ανά πλήρες σχολικό έτος, από το 1ο έτος, έως 4.</small></label>
            <div id="formalExperiencePreview" class="success">0 μόρια</div>
          </div>
        </div>
        <div class="note">Δεν προσμετράται χρόνος άδειας άνευ αποδοχών, εκπαιδευτικής άδειας ή απόσπασης σε θέση με διοικητικά καθήκοντα. Επίσης δεν μοριοδοτείται προϋπηρεσία που αναγνωρίστηκε κατά τον διορισμό στην τυπική εκπαίδευση.</div>
      <?php calculatorCardEnd(); ?>

      <?php calculatorCardStart(array('header_variant' => 'section-head', 'title_html' => '4. Άλλα προσόντα', 'subtitle_html' => 'Έως δύο ξένες γλώσσες και γνώση χειρισμού Η/Υ.', 'cap_html' => 'έως 5')); ?>
        <?php renderAsepLanguageSelector(array(
          'id' => 'sdeLanguages',
          'profile' => 'sde_secondment',
          'specialty_id' => 'specialty',
          'field_class' => 'field'
        )); ?>
        <div class="field-grid">
          <div class="field full">
            <label for="computer">Πιστοποιημένη γνώση Η/Υ / ΤΠΕ Α΄ επιπέδου ή πιστοποιητικό γνώσης Η/Υ σύμφωνα με ΑΣΕΠ <small>+2 μόρια. Για ΠΕ86 η γνώση τεκμαίρεται και τα μόρια αποδίδονται αυτόματα.</small></label>
            <select id="computer"><option value="no">Όχι</option><option value="yes">Ναι</option></select>
          </div>
        </div>
      <?php calculatorCardEnd(); ?>
    <?php calculatorMainEnd(); ?>

    <?php calculatorResultsStart(array('class' => 'results', 'aria_live' => 'polite')); ?>
      <?php calculatorCardStart(); ?>
        <?php calculatorScoreHeader(array(
          'variant' => 'capped',
          'class' => 'big-total',
          'value_id' => 'totalScore',
          'value_html' => '0',
          'value_class' => 'number',
          'cap_html' => 'από 40 μόρια',
          'cap_class' => 'outof'
        )); ?>
        <div class="bar"><div id="totalBar"></div></div>
        <?php calculatorResultRow(array('label_html' => 'Εκπαίδευση', 'value_html' => '0 / 22', 'value_id' => 'educationScore')); ?>
        <?php calculatorResultRow(array('label_html' => 'Διδακτική εμπειρία', 'value_html' => '0 / 13', 'value_id' => 'experienceScore')); ?>
        <?php calculatorResultRow(array('label_html' => 'Άλλα προσόντα', 'value_html' => '0 / 5', 'value_id' => 'otherScore')); ?>
        <div id="eligibilityStatus" role="status" aria-live="polite"></div>
        <?php calculatorActions(array(array('attrs' => array('class' => 'secondary', 'type' => 'button', 'id' => 'sdeDetachmentResetBtn'), 'html' => 'Καθαρισμός'))); ?>
      <?php calculatorCardEnd(); ?>

      <?php calculatorCardStart(); ?>
        <h2>Αποδεκτοί γραμματισμοί</h2>
        <div id="assignmentResult" class="subtitle">Επίλεξε ειδικότητα.</div>
      <?php calculatorCardEnd(); ?>

      <?php calculatorCardStart(); ?>
        <h2>Ανάλυση μορίων</h2>
        <div id="breakdown" class="subtitle">Συμπλήρωσε τα στοιχεία σου.</div>
      <?php calculatorCardEnd(); ?>
    <?php calculatorResultsEnd(); ?>
  <?php calculatorColumnsEnd(); ?>

  <?php sourceCardStart(); ?><p><strong>Νομική βάση:</strong> Υ.Α. 88422/Κ1, ΦΕΚ Β΄ 4088/03.07.2026, <strong>όπως διορθώθηκε με τη Διόρθωση Σφάλματος ΦΕΚ Β΄ 4199/10.07.2026</strong>.</p><p><strong>Πρόσκληση 2026–2027:</strong> 94386/Κ1/13.07.2026 — αιτήσεις από 14 έως και 20 Ιουλίου 2026.</p><?php sourceCardLinksStart(); ?><?php sourceCardLink('https://www.gsvetlly.minedu.gov.gr/nea-anakoinoseis/809-prosklese-ekdeloses-endiapherontos-gia-ten-ypobole-aiteseon-apospases-monimon-ekpaideutikon-protobathmias-kai-deuterobathmias-ekpaideuses-sta-scholeia-deuteres-eukairias-gia-to-scholiko-etos-2026-2027', 'Επίσημη πρόσκληση απόσπασης στα ΣΔΕ 2026–2027 — Γ.Γ.Ε.Ε.Κ. & Δ.Β.Μ. ↗'); ?><?php sourceCardLinksEnd(); ?><?php sourceCardDisclaimerStart(); ?>Το εργαλείο είναι ενδεικτικό. Οι τελικοί πίνακες καταρτίζονται από την αρμόδια Επιτροπή και ισχύουν οι όροι της εκάστοτε πρόσκλησης.<?php sourceCardDisclaimerEnd(); ?><?php sourceCardEnd(); ?>
  <?php require_once __DIR__ . '/includes/footer.php'; ?>
</main>

<script src="<?php echo htmlspecialchars(edu_asset_url('includes/language-calculations.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
<script src="<?php echo htmlspecialchars(edu_asset_url('includes/asep-language-selector.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
<script src="<?php echo htmlspecialchars(edu_asset_url('includes/sde-calculations.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
<script src="<?php echo htmlspecialchars(edu_asset_url('includes/sde-detachment-ui.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
  <script src="<?php echo htmlspecialchars(edu_asset_url('assets/common.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
</body>
</html>
