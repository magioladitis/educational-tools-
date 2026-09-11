<?php require_once __DIR__ . '/includes/config.php'; ?>
<!doctype html>
<html lang="el">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="Υπολογισμός μορίων επιλογής Διευθυντών και Υποδιευθυντών Σχολείων Δεύτερης Ευκαιρίας (ΣΔΕ) βάσει της Υ.Α. 70621/Κ1, ΦΕΚ Β' 3037/19.06.2025.">
  <title>Μόρια Διευθυντών & Υποδιευθυντών ΣΔΕ</title>
<link rel="stylesheet" href="<?php echo htmlspecialchars(edu_asset_url('assets/common.css'), ENT_QUOTES, 'UTF-8'); ?>">
</head>
<body class="edu-ui edu-calc-sde edu-page-sde-leadership">
<main class="page-shell">
  <?php require_once __DIR__ . '/includes/header.php'; ?>
<?php require_once __DIR__ . '/includes/components/calculator-layout.php'; ?>

  <?php calculatorHero(array(
    'title_html' => 'Μόρια Διευθυντών &amp; Υποδιευθυντών ΣΔΕ',
    'intro' => 'Υπολόγισε τα μόρια επιλογής για θέσεις ευθύνης στα Σχολεία Δεύτερης Ευκαιρίας και κάνε βασικό έλεγχο των προϋποθέσεων συμμετοχής.',
    'meta_class' => 'hero-meta',
    'badges' => array('Διευθυντής: έως 100', 'Υποδιευθυντής: έως 75', 'Τυπικά προσόντα: 25', 'Συνέντευξη: μόνο Διευθυντές, έως 25', 'ΦΕΚ Β΄ 3037/19.06.2025')
  )); ?>

  <?php calculatorColumnsStart(); ?>
    <?php calculatorMainStart(); ?>
      <?php calculatorCardStart(array('header_variant' => 'section-head', 'title_html' => '1. Θέση &amp; βασικές προϋποθέσεις', 'subtitle_html' => 'Η θέση αλλάζει αυτόματα τα ελάχιστα έτη υπηρεσίας, τις απαιτούμενες ώρες διδακτικού έργου και τα επιμέρους πλαφόν.')); ?>
        <div class="field-grid">
          <div class="field full">
            <label for="role">Θέση υποψηφιότητας</label>
            <select id="role">
              <option value="">— Επίλεξε —</option>
              <option value="director">Διευθυντής Σ.Δ.Ε.</option>
              <option value="deputy">Υποδιευθυντής Σ.Δ.Ε.</option>
            </select>
          </div>
          <div class="field">
            <label for="permanentTeacher">Είσαι εν ενεργεία μόνιμος/η εκπαιδευτικός Π/θμιας ή Δ/θμιας;</label>
            <select id="permanentTeacher"><option value="">— Επίλεξε —</option><option value="yes">Ναι</option><option value="no">Όχι</option></select>
          </div>
          <div class="field">
            <label for="educationalServiceYears">Συνολική εκπαιδευτική υπηρεσία <small id="serviceRequirement">Επίλεξε θέση για να εμφανιστεί το ελάχιστο.</small></label>
            <input type="number" id="educationalServiceYears" min="0" max="50" step="0.01" placeholder="π.χ. 12">
          </div>
          <div class="field hidden" id="tertiaryDegreeWrap">
            <label for="tertiaryDegree">Πτυχίο τριτοβάθμιας εκπαίδευσης ή ισότιμος τίτλος</label>
            <select id="tertiaryDegree"><option value="">— Επίλεξε —</option><option value="yes">Ναι</option><option value="no">Όχι</option></select>
          </div>
          <div class="field">
            <label for="assignmentEligible">Μπορείς να καλύψεις το απαιτούμενο διδακτικό έργο στα γνωστικά αντικείμενα των ΣΔΕ; <small id="assignmentRequirement">Επίλεξε θέση.</small></label>
            <select id="assignmentEligible"><option value="">— Επίλεξε —</option><option value="yes">Ναι</option><option value="no">Όχι / δεν είμαι βέβαιος</option></select>
          </div>
          <div class="field">
            <label for="computerKnowledge">Γνώση πληροφορικής / χειρισμού Η/Υ <small>Τεκμαίρεται για τον κλάδο ΠΕ86.</small></label>
            <select id="computerKnowledge"><option value="">— Επίλεξε —</option><option value="yes">Ναι</option><option value="pe86">Ναι — είμαι ΠΕ86</option><option value="no">Όχι</option></select>
          </div>
          <div class="field">
            <label for="adultEducationExperience">Γνώση και εμπειρία στην εκπαίδευση ενηλίκων</label>
            <select id="adultEducationExperience"><option value="">— Επίλεξε —</option><option value="yes">Ναι</option><option value="no">Όχι / δεν είμαι βέβαιος</option></select>
          </div>
          <div class="field hidden" id="adminQualificationsWrap">
            <label for="adminQualifications">Σημαντικά διοικητικά προσόντα <small>Προϋπόθεση που αναφέρεται ειδικά για τους υποψήφιους Διευθυντές.</small></label>
            <select id="adminQualifications"><option value="">— Επίλεξε —</option><option value="yes">Ναι</option><option value="no">Όχι / δεν είμαι βέβαιος</option></select>
          </div>
          <div class="field full">
            <label for="blockingIssue">Συντρέχει κάποιο κώλυμα συμμετοχής του άρθρου 3;</label>
            <select id="blockingIssue"><option value="">— Επίλεξε —</option><option value="no">Όχι</option><option value="yes">Ναι / πιθανόν</option></select>
            <?php calculatorDisclosureStart(array('summary' => 'Ενδεικτικά κωλύματα', 'open' => true, 'attrs' => array('data-mobile-collapsed' => 'true'))); ?><ul class="criteria-list"><li>δοκιμαστική υπηρεσία, διαθεσιμότητα ή αργία,</li><li>ορισμένες τελεσίδικες ποινικές ή πειθαρχικές καταδίκες,</li><li>υποχρεωτική υπηρεσία / απαγόρευση υπηρεσιακών μεταβολών,</li><li>θητεία σε άλλη θέση που λήγει μετά την έναρξη της νέας θητείας,</li><li>ορισμένες περιπτώσεις πρόσφατης ανάκλησης/διακοπής απόσπασης,</li><li>υποχρεωτική αποχώρηση λόγω συνταξιοδότησης εντός του προβλεπόμενου έτους.</li></ul><?php calculatorDisclosureEnd(); ?>
          </div>
        </div>
      <?php calculatorCardEnd(); ?>

      <?php calculatorCardStart(array('header_variant' => 'section-head', 'title_html' => '2. Τυπικά προσόντα', 'subtitle_html' => 'Οι συναφείς μεταπτυχιακές σπουδές σε Εκπαίδευση Ενηλίκων, Συνεχιζόμενη Εκπαίδευση, Διά Βίου Μάθηση ή Διοίκηση Εκπαιδευτικών Μονάδων λαμβάνουν το μέγιστο.', 'cap_html' => 'έως 25')); ?>
        <div class="field-grid">
          <div class="field">
            <label for="phd">Διδακτορικό</label>
            <select id="phd"><option value="none">Κανένα</option><option value="relevant">Συναφές στις προβλεπόμενες κατευθύνσεις — 8</option><option value="other">Άλλη ειδίκευση — 6</option></select>
          </div>
          <div class="field">
            <label for="master">Μεταπτυχιακό / integrated master</label>
            <select id="master"><option value="none">Κανένα</option><option value="relevant">Συναφές στις προβλεπόμενες κατευθύνσεις — 4</option><option value="other">Άλλη ειδίκευση — 2</option></select>
          </div>
          <div class="field">
            <label for="esdda">Απόφοιτος ΕΣΔΔΑ <small>+5 μόρια</small></label>
            <select id="esdda"><option value="no">Όχι</option><option value="yes">Ναι</option></select>
          </div>
          <div class="field">
            <label for="secondDegree">Δεύτερο πτυχίο τριτοβάθμιας <small>+3 μόρια. Δεν λαμβάνεται υπόψη ΑΣΠΑΙΤΕ/ΣΕΛΕΤΕ μονοετούς φοίτησης.</small></label>
            <select id="secondDegree"><option value="no">Όχι</option><option value="yes">Ναι</option></select>
          </div>
          <div class="field">
            <label for="language1">Ξένη γλώσσα 1</label>
            <select id="language1"><option value="">— Καμία —</option><option value="english">Αγγλικά</option><option value="french">Γαλλικά</option><option value="german">Γερμανικά</option><option value="italian">Ιταλικά</option><option value="spanish">Ισπανικά</option><option value="other1">Άλλη</option></select>
            <label class="edu-tools-sr-only" for="languageLevel1">Επίπεδο γλώσσας 1</label><select id="languageLevel1" class="edu-mt-8"><option value="">— Επίπεδο —</option><option value="B2">Β2 — Καλή</option><option value="C1">C1 — Πολύ καλή</option><option value="C2">C2 — Άριστη</option></select>
            <label class="edu-tools-sr-only" for="languageAppointment1">Προσόν διορισμού γλώσσας 1</label><select id="languageAppointment1" class="edu-mt-8"><option value="no">Δεν αποτέλεσε προσόν διορισμού</option><option value="yes">Αποτέλεσε προσόν διορισμού — 0 μόρια</option></select>
          </div>
          <div class="field">
            <label for="language2">Ξένη γλώσσα 2</label>
            <select id="language2"><option value="">— Καμία —</option><option value="english">Αγγλικά</option><option value="french">Γαλλικά</option><option value="german">Γερμανικά</option><option value="italian">Ιταλικά</option><option value="spanish">Ισπανικά</option><option value="other2">Άλλη</option></select>
            <label class="edu-tools-sr-only" for="languageLevel2">Επίπεδο γλώσσας 2</label><select id="languageLevel2" class="edu-mt-8"><option value="">— Επίπεδο —</option><option value="B2">Β2 — Καλή</option><option value="C1">C1 — Πολύ καλή</option><option value="C2">C2 — Άριστη</option></select>
            <label class="edu-tools-sr-only" for="languageAppointment2">Προσόν διορισμού γλώσσας 2</label><select id="languageAppointment2" class="edu-mt-8"><option value="no">Δεν αποτέλεσε προσόν διορισμού</option><option value="yes">Αποτέλεσε προσόν διορισμού — 0 μόρια</option></select>
          </div>
        </div>
        <div class="note">Το εργαλείο ταξινομεί αυτόματα τις δύο επιλέξιμες γλώσσες ώστε η ισχυρότερη να μοριοδοτείται ως 1η. Η ίδια κατονομασμένη γλώσσα δεν μπορεί να επιλεγεί δύο φορές. Η επιλογή «Άλλη» παραμένει διαθέσιμη και στα δύο πεδία, επειδή μπορεί να αφορά διαφορετικές γλώσσες.</div>
      <?php calculatorCardEnd(); ?>

      <?php calculatorCardStart(array('header_variant' => 'section-head', 'title_html' => '3. Διδακτική εμπειρία', 'subtitle_html' => 'Τα όρια αλλάζουν ανά θέση. Στην ωρομίσθια απασχόληση στα ΣΔΕ και στις σχολικές μονάδες/ΣΑΕΚ/ΕΣΚ, 650 ώρες αντιστοιχούν σε ένα έτος.', 'cap_html' => 'έως —', 'cap_attrs' => array('id' => 'teachingMax'))); ?>
        <div class="field-grid">
          <div class="field">
            <label for="sdeTeachingYears">Διδακτικό έργο στα ΣΔΕ — πλήρη έτη <small>Μην περιλαμβάνεις εδώ χρόνο διοικητικής θητείας που παίρνει μόρια στην ενότητα 4.</small></label>
            <input type="number" id="sdeTeachingYears" min="0" max="50" step="0.01" value="0">
          </div>
          <div class="field">
            <label for="sdeTeachingHours">Διδακτικό έργο στα ΣΔΕ — ώρες ωρομίσθιας απασχόλησης</label>
            <input type="number" id="sdeTeachingHours" min="0" step="1" value="0">
          </div>
          <div class="field">
            <label for="sdeTransferredYears">Έτη διοικητικής θητείας σε ΣΔΕ που δεν πήραν διοικητικά μόρια λόγω πλαφόν <small>Χρησιμοποίησέ το μόνο αν ο συγκεκριμένος χρόνος επιτρέπεται να μεταφερθεί στη διδακτική εμπειρία.</small></label>
            <input type="number" id="sdeTransferredYears" min="0" max="50" step="0.01" value="0">
          </div>
          <div class="field">
            <label for="adultNonformalHours">Μη τυπική εκπαίδευση ενηλίκων — ώρες <small>ΝΕΛΕ, ΚΕΕ, ΚΔΒΜ, ΚΕΔΙΒΙΜ, ΚΕΚ, ΠΕΚ, ΠΕΚΕΣ, ΕΚΔΔΑ. 0,5 μόριο ανά 100 ώρες.</small></label>
            <input type="number" id="adultNonformalHours" min="0" step="1" value="0">
          </div>
          <div class="field">
            <label for="schoolTeachingYears">Π/θμια – Δ/θμια – ΣΑΕΚ – ΕΣΚ — πλήρη διδακτικά έτη <small>Χωρίς περίοδο διοικητικής θητείας που μοριοδοτείται στην ενότητα 4.</small></label>
            <input type="number" id="schoolTeachingYears" min="0" max="50" step="0.01" value="0">
          </div>
          <div class="field">
            <label for="schoolTeachingHours">Π/θμια – Δ/θμια – ΣΑΕΚ – ΕΣΚ — ώρες ωρομίσθιας απασχόλησης</label>
            <input type="number" id="schoolTeachingHours" min="0" step="1" value="0">
          </div>
          <div class="field full">
            <label for="schoolTransferredYears">Έτη διοικητικής θητείας που δεν πήραν διοικητικά μόρια λόγω πλαφόν και μπορούν να προσμετρηθούν ως διδακτικά <small>Μην δηλώσεις χρόνο που έχει ήδη μοριοδοτηθεί διοικητικά.</small></label>
            <input type="number" id="schoolTransferredYears" min="0" max="50" step="0.01" value="0">
          </div>
        </div>
        <div class="warning">Δεν προσμετράται χρόνος άδειας άνευ αποδοχών, εκπαιδευτικής άδειας ή απόσπασης σε θέση με διοικητικά καθήκοντα, ούτε προϋπηρεσία που αναγνωρίστηκε κατά τον διορισμό στην τυπική εκπαίδευση. Το διδακτικό έργο στην τριτοβάθμια εκπαίδευση δεν θεωρείται διδακτικό έργο στην Εκπαίδευση Ενηλίκων.</div>
      <?php calculatorCardEnd(); ?>

      <?php calculatorCardStart(array('header_variant' => 'section-head', 'title_html' => '4. Διοικητική εμπειρία', 'subtitle_html' => 'Η ίδια χρονική περίοδος δεν μπορεί να μοριοδοτηθεί και ως διοικητική και ως διδακτική εμπειρία.', 'cap_html' => 'έως —', 'cap_attrs' => array('id' => 'adminMax'))); ?>
        <div class="field-grid">
          <div class="field">
            <label for="sdeDirectorYears">Στα ΣΔΕ ως Διευθυντής — σχολικά έτη <small>2 μόρια ανά έτος.</small></label>
            <input type="number" id="sdeDirectorYears" min="0" max="50" step="0.01" value="0">
          </div>
          <div class="field">
            <label for="sdeDeputyYears">Στα ΣΔΕ ως Υποδιευθυντής — σχολικά έτη <small>1 μόριο ανά έτος.</small></label>
            <input type="number" id="sdeDeputyYears" min="0" max="50" step="0.01" value="0">
          </div>
          <div class="field full">
            <label for="otherAdminYears">Σε σχολικές μονάδες Π/θμιας ή Δ/θμιας, ΣΑΕΚ ή ΕΣΚ ως Διευθυντής/Υποδιευθυντής — σχολικά έτη <small>1 μόριο ανά έτος.</small></label>
            <input type="number" id="otherAdminYears" min="0" max="50" step="0.01" value="0">
          </div>
        </div>
        <div class="overflow-box" id="overflowHint">Επίλεξε θέση για να υπολογιστούν τα επιμέρους πλαφόν και τυχόν διοικητικός χρόνος που μένει εκτός μοριοδότησης.</div>
      <?php calculatorCardEnd(); ?>

      <?php calculatorCardStart(array('header_variant' => 'section-head', 'title_html' => '5. Επιμόρφωση', 'subtitle_html' => 'Επιμόρφωση στις αρχές Εκπαίδευσης Ενηλίκων, σε θέματα ΣΔΕ ή στη Διοίκηση Εκπαιδευτικών Μονάδων από φορείς του δημόσιου ή ευρύτερου δημόσιου τομέα.', 'cap_html' => 'έως 5')); ?>
        <div class="field-grid"><div class="field full"><label for="trainingHours">Συνολικές επιλέξιμες ώρες ολοκληρωμένων επιμορφώσεων <small>0,5 μόρια ανά 100 ώρες. Κάθε επιμέρους επιμόρφωση κάτω των 15 ωρών λαμβάνει 0 μόρια. Μην συμπεριλαμβάνεις ημερίδες, διημερίδες ή συνέδρια.</small></label><input type="number" id="trainingHours" min="0" step="1" value="0"></div></div>
      <?php calculatorCardEnd(); ?>

      <?php calculatorCardStart(array('id' => 'interviewCard', 'class' => 'card hidden', 'header_variant' => 'section-head', 'title_html' => '6. Συνέντευξη', 'subtitle_html' => 'Μόνο για τους υποψήφιους Διευθυντές. Αν δεν έχει πραγματοποιηθεί ακόμη, άφησε το πεδίο κενό για να δεις το σύνολο πριν από τη συνέντευξη.', 'cap_html' => 'έως 25')); ?>
        <div class="field-grid"><div class="field full"><label for="interviewScore">Βαθμολογία συνέντευξης</label><input type="number" id="interviewScore" min="0" max="25" step="0.01" placeholder="0–25"></div></div>
      <?php calculatorCardEnd(); ?>
    <?php calculatorMainEnd(); ?>

    <?php calculatorResultsStart(array('class' => 'results', 'attrs' => array('aria-live' => 'polite'))); ?>
      <?php calculatorCardStart(); ?>
        <div class="role-chip" id="roleChip">Επίλεξε θέση</div>
        <h2>Αποτέλεσμα</h2>
        <?php calculatorScoreHeader(array(
          'variant' => 'staged',
          'class' => 'big-total',
          'context_id' => 'totalContext',
          'context_html' => 'Μόρια κριτηρίων',
          'context_attrs' => array('class' => 'context'),
          'value_id' => 'totalScore',
          'value_html' => '0',
          'value_class' => 'number',
          'cap_id' => 'totalOutOf',
          'cap_html' => 'από 75 μόρια',
          'cap_class' => 'outof'
        )); ?>
        <div class="bar"><div id="totalBar"></div></div>
        <?php calculatorResultRow(array('label_html' => 'Τυπικά προσόντα', 'value_html' => '0 / 25', 'value_id' => 'formalScore')); ?>
        <?php calculatorResultRow(array('label_html' => 'Διδακτική εμπειρία', 'value_html' => '0 / —', 'value_id' => 'teachingScore')); ?>
        <?php calculatorResultRow(array('label_html' => 'Διοικητική εμπειρία', 'value_html' => '0 / —', 'value_id' => 'adminScore')); ?>
        <?php calculatorResultRow(array('label_html' => 'Επιμόρφωση', 'value_html' => '0 / 5', 'value_id' => 'trainingScore')); ?>
        <?php calculatorResultRow(array('class' => 'result-row hidden', 'id' => 'interviewRow', 'label_html' => 'Συνέντευξη', 'value_html' => '— / 25', 'value_id' => 'interviewResult')); ?>
        <?php calculatorResultRow(array('class' => 'result-row emphasis', 'id' => 'criteriaRow', 'label_html' => 'Σύνολο πριν συνέντευξη', 'value_html' => '0 / 75', 'value_id' => 'criteriaScore')); ?>
        <div id="eligibilityStatus" role="status" aria-live="polite"></div>
        <?php calculatorActions(array(array('attrs' => array('class' => 'secondary', 'type' => 'button', 'id' => 'sdeLeadershipCopyBtn'), 'html' => 'Αντιγραφή'), array('attrs' => array('class' => 'secondary', 'type' => 'button', 'id' => 'sdeLeadershipResetBtn'), 'html' => 'Καθαρισμός'))); ?>
      <?php calculatorCardEnd(); ?>

      <?php calculatorCardStart(); ?><h2>Ανάλυση μορίων</h2><div id="breakdown" class="subtitle">Συμπλήρωσε τα στοιχεία σου.</div><?php calculatorCardEnd(); ?>
      <?php calculatorDisclosure(array('summary' => 'Σημαντικός κανόνας', 'html' => 'Χρόνος Διευθυντή/Υποδιευθυντή που λογίζεται και ως διδακτικός δεν μοριοδοτείται και στις δύο κατηγορίες για την ίδια περίοδο. Αν διοικητικός χρόνος μένει εκτός λόγω πλαφόν, μπορεί να προσμετρηθεί στο αντίστοιχο πεδίο διδακτικής εμπειρίας όπου προβλέπεται.', 'class' => 'edu-result-disclosure', 'open' => true, 'attrs' => array('data-mobile-collapsed' => 'true'))); ?>
      <?php calculatorDisclosure(array('summary' => 'Σε περίπτωση ισοβαθμίας', 'html' => 'Στον τελικό πίνακα προηγείται ο υποψήφιος με περισσότερες μονάδες στη συνέντευξη, όπου αυτή προβλέπεται. Αν εξακολουθεί η ισοβαθμία ή δεν προβλέπεται συνέντευξη, εξετάζονται τα κριτήρια με τη σειρά που αναγράφονται στο άρθρο 4.', 'class' => 'edu-result-disclosure', 'open' => true, 'attrs' => array('data-mobile-collapsed' => 'true'))); ?>
    <?php calculatorResultsEnd(); ?>
  <?php calculatorColumnsEnd(); ?>

  <?php sourceCardStart(); ?><p><strong>Πηγή:</strong> Υ.Α. 70621/Κ1, ΦΕΚ Β΄ 3037/19.06.2025 «Καθορισμός κριτηρίων και διαδικασίας επιλογής Διευθυντών και Υποδιευθυντών Σχολείων Δεύτερης Ευκαιρίας (Σ.Δ.Ε.)».</p><?php sourceCardDisclaimerStart(); ?>Το εργαλείο παρέχει ενδεικτικό υπολογισμό· η τελική κρίση ανήκει στην αρμόδια Επιτροπή Επιλογής.<?php sourceCardDisclaimerEnd(); ?><?php sourceCardEnd(); ?>
  <?php require_once __DIR__ . '/includes/footer.php'; ?>
</main>
<script src="<?php echo htmlspecialchars(edu_asset_url('includes/language-calculations.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
<script src="<?php echo htmlspecialchars(edu_asset_url('includes/language-pair-lock.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
<script src="<?php echo htmlspecialchars(edu_asset_url('includes/sde-leadership-calculations.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
<script src="<?php echo htmlspecialchars(edu_asset_url('includes/sde-leadership-ui.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
<script src="<?php echo htmlspecialchars(edu_asset_url('assets/common.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
</body>
</html>
