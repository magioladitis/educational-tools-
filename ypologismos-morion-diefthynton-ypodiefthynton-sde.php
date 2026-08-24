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

  <?php calculatorHeroStart(); ?>
    <h1>Μόρια Διευθυντών &amp; Υποδιευθυντών ΣΔΕ</h1>
    <p>Υπολόγισε τα μόρια επιλογής για θέσεις ευθύνης στα Σχολεία Δεύτερης Ευκαιρίας και κάνε βασικό έλεγχο των προϋποθέσεων συμμετοχής.</p>
    <div class="hero-meta">
      <span>Διευθυντής: έως 100</span>
      <span>Υποδιευθυντής: έως 75</span>
      <span>Τυπικά προσόντα: 25</span>
      <span>Συνέντευξη: μόνο Διευθυντές, έως 25</span>
      <span>ΦΕΚ Β΄ 3037/19.06.2025</span>
    </div>
  <?php calculatorHeroEnd(); ?>

  <?php calculatorColumnsStart(); ?>
    <?php calculatorMainStart(); ?>
      <?php calculatorCardStart(array('header_variant' => 'section-head', 'title_html' => '1. Θέση &amp; βασικές προϋποθέσεις', 'subtitle_html' => 'Η θέση αλλάζει αυτόματα τα ελάχιστα έτη υπηρεσίας, τις απαιτούμενες ώρες διδακτικού έργου και τα επιμέρους πλαφόν.')); ?>
        <div class="field-grid">
          <div class="field full">
            <label for="role">Θέση υποψηφιότητας</label>
            <select id="role" onchange="roleChanged()">
              <option value="">— Επίλεξε —</option>
              <option value="director">Διευθυντής Σ.Δ.Ε.</option>
              <option value="deputy">Υποδιευθυντής Σ.Δ.Ε.</option>
            </select>
          </div>
          <div class="field">
            <label for="permanentTeacher">Είσαι εν ενεργεία μόνιμος/η εκπαιδευτικός Π/θμιας ή Δ/θμιας;</label>
            <select id="permanentTeacher" onchange="calculate()"><option value="">— Επίλεξε —</option><option value="yes">Ναι</option><option value="no">Όχι</option></select>
          </div>
          <div class="field">
            <label for="educationalServiceYears">Συνολική εκπαιδευτική υπηρεσία <small id="serviceRequirement">Επίλεξε θέση για να εμφανιστεί το ελάχιστο.</small></label>
            <input type="number" id="educationalServiceYears" min="0" max="40" step="0.01" placeholder="π.χ. 12" oninput="calculate()">
          </div>
          <div class="field hidden" id="tertiaryDegreeWrap">
            <label for="tertiaryDegree">Πτυχίο τριτοβάθμιας εκπαίδευσης ή ισότιμος τίτλος</label>
            <select id="tertiaryDegree" onchange="calculate()"><option value="">— Επίλεξε —</option><option value="yes">Ναι</option><option value="no">Όχι</option></select>
          </div>
          <div class="field">
            <label for="assignmentEligible">Μπορείς να καλύψεις το απαιτούμενο διδακτικό έργο στα γνωστικά αντικείμενα των ΣΔΕ; <small id="assignmentRequirement">Επίλεξε θέση.</small></label>
            <select id="assignmentEligible" onchange="calculate()"><option value="">— Επίλεξε —</option><option value="yes">Ναι</option><option value="no">Όχι / δεν είμαι βέβαιος</option></select>
          </div>
          <div class="field">
            <label for="computerKnowledge">Γνώση πληροφορικής / χειρισμού Η/Υ <small>Τεκμαίρεται για τον κλάδο ΠΕ86.</small></label>
            <select id="computerKnowledge" onchange="calculate()"><option value="">— Επίλεξε —</option><option value="yes">Ναι</option><option value="pe86">Ναι — είμαι ΠΕ86</option><option value="no">Όχι</option></select>
          </div>
          <div class="field">
            <label for="adultEducationExperience">Γνώση και εμπειρία στην εκπαίδευση ενηλίκων</label>
            <select id="adultEducationExperience" onchange="calculate()"><option value="">— Επίλεξε —</option><option value="yes">Ναι</option><option value="no">Όχι / δεν είμαι βέβαιος</option></select>
          </div>
          <div class="field hidden" id="adminQualificationsWrap">
            <label for="adminQualifications">Σημαντικά διοικητικά προσόντα <small>Προϋπόθεση που αναφέρεται ειδικά για τους υποψήφιους Διευθυντές.</small></label>
            <select id="adminQualifications" onchange="calculate()"><option value="">— Επίλεξε —</option><option value="yes">Ναι</option><option value="no">Όχι / δεν είμαι βέβαιος</option></select>
          </div>
          <div class="field full">
            <label for="blockingIssue">Συντρέχει κάποιο κώλυμα συμμετοχής του άρθρου 3;</label>
            <select id="blockingIssue" onchange="calculate()"><option value="">— Επίλεξε —</option><option value="no">Όχι</option><option value="yes">Ναι / πιθανόν</option></select>
            <details><summary>Ενδεικτικά κωλύματα</summary><ul class="criteria-list"><li>δοκιμαστική υπηρεσία, διαθεσιμότητα ή αργία,</li><li>ορισμένες τελεσίδικες ποινικές ή πειθαρχικές καταδίκες,</li><li>υποχρεωτική υπηρεσία / απαγόρευση υπηρεσιακών μεταβολών,</li><li>θητεία σε άλλη θέση που λήγει μετά την έναρξη της νέας θητείας,</li><li>ορισμένες περιπτώσεις πρόσφατης ανάκλησης/διακοπής απόσπασης,</li><li>υποχρεωτική αποχώρηση λόγω συνταξιοδότησης εντός του προβλεπόμενου έτους.</li></ul></details>
          </div>
        </div>
      <?php calculatorCardEnd(); ?>

      <?php calculatorCardStart(array('header_variant' => 'section-head', 'title_html' => '2. Τυπικά προσόντα', 'subtitle_html' => 'Οι συναφείς μεταπτυχιακές σπουδές σε Εκπαίδευση Ενηλίκων, Συνεχιζόμενη Εκπαίδευση, Διά Βίου Μάθηση ή Διοίκηση Εκπαιδευτικών Μονάδων λαμβάνουν το μέγιστο.', 'cap_html' => 'έως 25')); ?>
        <div class="field-grid">
          <div class="field">
            <label for="phd">Διδακτορικό</label>
            <select id="phd" onchange="calculate()"><option value="none">Κανένα</option><option value="relevant">Συναφές στις προβλεπόμενες κατευθύνσεις — 8</option><option value="other">Άλλη ειδίκευση — 6</option></select>
          </div>
          <div class="field">
            <label for="master">Μεταπτυχιακό / integrated master</label>
            <select id="master" onchange="calculate()"><option value="none">Κανένα</option><option value="relevant">Συναφές στις προβλεπόμενες κατευθύνσεις — 4</option><option value="other">Άλλη ειδίκευση — 2</option></select>
          </div>
          <div class="field">
            <label for="esdda">Απόφοιτος ΕΣΔΔΑ <small>+5 μόρια</small></label>
            <select id="esdda" onchange="calculate()"><option value="no">Όχι</option><option value="yes">Ναι</option></select>
          </div>
          <div class="field">
            <label for="secondDegree">Δεύτερο πτυχίο τριτοβάθμιας <small>+3 μόρια. Δεν λαμβάνεται υπόψη ΑΣΠΑΙΤΕ/ΣΕΛΕΤΕ μονοετούς φοίτησης.</small></label>
            <select id="secondDegree" onchange="calculate()"><option value="no">Όχι</option><option value="yes">Ναι</option></select>
          </div>
          <div class="field">
            <label for="language1">Ξένη γλώσσα 1</label>
            <select id="language1" onchange="languageChanged()"><option value="">— Καμία —</option><option value="english">Αγγλικά</option><option value="french">Γαλλικά</option><option value="german">Γερμανικά</option><option value="italian">Ιταλικά</option><option value="spanish">Ισπανικά</option><option value="other1">Άλλη</option></select>
            <label class="edu-tools-sr-only" for="languageLevel1">Επίπεδο γλώσσας 1</label><select id="languageLevel1" onchange="calculate()" class="edu-mt-8"><option value="">— Επίπεδο —</option><option value="B2">Β2 — Καλή</option><option value="C1">C1 — Πολύ καλή</option><option value="C2">C2 — Άριστη</option></select>
            <label class="edu-tools-sr-only" for="languageAppointment1">Προσόν διορισμού γλώσσας 1</label><select id="languageAppointment1" onchange="calculate()" class="edu-mt-8"><option value="no">Δεν αποτέλεσε προσόν διορισμού</option><option value="yes">Αποτέλεσε προσόν διορισμού — 0 μόρια</option></select>
          </div>
          <div class="field">
            <label for="language2">Ξένη γλώσσα 2</label>
            <select id="language2" onchange="languageChanged()"><option value="">— Καμία —</option><option value="english">Αγγλικά</option><option value="french">Γαλλικά</option><option value="german">Γερμανικά</option><option value="italian">Ιταλικά</option><option value="spanish">Ισπανικά</option><option value="other2">Άλλη</option></select>
            <label class="edu-tools-sr-only" for="languageLevel2">Επίπεδο γλώσσας 2</label><select id="languageLevel2" onchange="calculate()" class="edu-mt-8"><option value="">— Επίπεδο —</option><option value="B2">Β2 — Καλή</option><option value="C1">C1 — Πολύ καλή</option><option value="C2">C2 — Άριστη</option></select>
            <label class="edu-tools-sr-only" for="languageAppointment2">Προσόν διορισμού γλώσσας 2</label><select id="languageAppointment2" onchange="calculate()" class="edu-mt-8"><option value="no">Δεν αποτέλεσε προσόν διορισμού</option><option value="yes">Αποτέλεσε προσόν διορισμού — 0 μόρια</option></select>
          </div>
        </div>
        <div class="note">Το εργαλείο ταξινομεί αυτόματα τις δύο επιλέξιμες γλώσσες ώστε η ισχυρότερη να μοριοδοτείται ως 1η. Η ίδια γλώσσα λαμβάνεται μόνο μία φορά, στο ανώτερο επίπεδο.</div>
      <?php calculatorCardEnd(); ?>

      <?php calculatorCardStart(array('header_variant' => 'section-head', 'title_html' => '3. Διδακτική εμπειρία', 'subtitle_html' => 'Τα όρια αλλάζουν ανά θέση. Στην ωρομίσθια απασχόληση στα ΣΔΕ και στις σχολικές μονάδες/ΣΑΕΚ/ΕΣΚ, 650 ώρες αντιστοιχούν σε ένα έτος.', 'cap_html' => 'έως —', 'cap_attrs' => array('id' => 'teachingMax'))); ?>
        <div class="field-grid">
          <div class="field">
            <label for="sdeTeachingYears">Διδακτικό έργο στα ΣΔΕ — πλήρη έτη <small>Μην περιλαμβάνεις εδώ χρόνο διοικητικής θητείας που παίρνει μόρια στην ενότητα 4.</small></label>
            <input type="number" id="sdeTeachingYears" min="0" max="40" step="0.01" value="0" oninput="calculate()">
          </div>
          <div class="field">
            <label for="sdeTeachingHours">Διδακτικό έργο στα ΣΔΕ — ώρες ωρομίσθιας απασχόλησης</label>
            <input type="number" id="sdeTeachingHours" min="0" step="1" value="0" oninput="calculate()">
          </div>
          <div class="field">
            <label for="sdeTransferredYears">Έτη διοικητικής θητείας σε ΣΔΕ που δεν πήραν διοικητικά μόρια λόγω πλαφόν <small>Χρησιμοποίησέ το μόνο αν ο συγκεκριμένος χρόνος επιτρέπεται να μεταφερθεί στη διδακτική εμπειρία.</small></label>
            <input type="number" id="sdeTransferredYears" min="0" max="40" step="0.01" value="0" oninput="calculate()">
          </div>
          <div class="field">
            <label for="adultNonformalHours">Μη τυπική εκπαίδευση ενηλίκων — ώρες <small>ΝΕΛΕ, ΚΕΕ, ΚΔΒΜ, ΚΕΔΙΒΙΜ, ΚΕΚ, ΠΕΚ, ΠΕΚΕΣ, ΕΚΔΔΑ. 0,5 μόριο ανά 100 ώρες.</small></label>
            <input type="number" id="adultNonformalHours" min="0" step="1" value="0" oninput="calculate()">
          </div>
          <div class="field">
            <label for="schoolTeachingYears">Π/θμια – Δ/θμια – ΣΑΕΚ – ΕΣΚ — πλήρη διδακτικά έτη <small>Χωρίς περίοδο διοικητικής θητείας που μοριοδοτείται στην ενότητα 4.</small></label>
            <input type="number" id="schoolTeachingYears" min="0" max="40" step="0.01" value="0" oninput="calculate()">
          </div>
          <div class="field">
            <label for="schoolTeachingHours">Π/θμια – Δ/θμια – ΣΑΕΚ – ΕΣΚ — ώρες ωρομίσθιας απασχόλησης</label>
            <input type="number" id="schoolTeachingHours" min="0" step="1" value="0" oninput="calculate()">
          </div>
          <div class="field full">
            <label for="schoolTransferredYears">Έτη διοικητικής θητείας που δεν πήραν διοικητικά μόρια λόγω πλαφόν και μπορούν να προσμετρηθούν ως διδακτικά <small>Μην δηλώσεις χρόνο που έχει ήδη μοριοδοτηθεί διοικητικά.</small></label>
            <input type="number" id="schoolTransferredYears" min="0" max="40" step="0.01" value="0" oninput="calculate()">
          </div>
        </div>
        <div class="warning">Δεν προσμετράται χρόνος άδειας άνευ αποδοχών, εκπαιδευτικής άδειας ή απόσπασης σε θέση με διοικητικά καθήκοντα, ούτε προϋπηρεσία που αναγνωρίστηκε κατά τον διορισμό στην τυπική εκπαίδευση. Το διδακτικό έργο στην τριτοβάθμια εκπαίδευση δεν θεωρείται διδακτικό έργο στην Εκπαίδευση Ενηλίκων.</div>
      <?php calculatorCardEnd(); ?>

      <?php calculatorCardStart(array('header_variant' => 'section-head', 'title_html' => '4. Διοικητική εμπειρία', 'subtitle_html' => 'Η ίδια χρονική περίοδος δεν μπορεί να μοριοδοτηθεί και ως διοικητική και ως διδακτική εμπειρία.', 'cap_html' => 'έως —', 'cap_attrs' => array('id' => 'adminMax'))); ?>
        <div class="field-grid">
          <div class="field">
            <label for="sdeDirectorYears">Στα ΣΔΕ ως Διευθυντής — σχολικά έτη <small>2 μόρια ανά έτος.</small></label>
            <input type="number" id="sdeDirectorYears" min="0" max="40" step="0.01" value="0" oninput="calculate()">
          </div>
          <div class="field">
            <label for="sdeDeputyYears">Στα ΣΔΕ ως Υποδιευθυντής — σχολικά έτη <small>1 μόριο ανά έτος.</small></label>
            <input type="number" id="sdeDeputyYears" min="0" max="40" step="0.01" value="0" oninput="calculate()">
          </div>
          <div class="field full">
            <label for="otherAdminYears">Σε σχολικές μονάδες Π/θμιας ή Δ/θμιας, ΣΑΕΚ ή ΕΣΚ ως Διευθυντής/Υποδιευθυντής — σχολικά έτη <small>1 μόριο ανά έτος.</small></label>
            <input type="number" id="otherAdminYears" min="0" max="40" step="0.01" value="0" oninput="calculate()">
          </div>
        </div>
        <div class="overflow-box" id="overflowHint">Επίλεξε θέση για να υπολογιστούν τα επιμέρους πλαφόν και τυχόν διοικητικός χρόνος που μένει εκτός μοριοδότησης.</div>
      <?php calculatorCardEnd(); ?>

      <?php calculatorCardStart(array('header_variant' => 'section-head', 'title_html' => '5. Επιμόρφωση', 'subtitle_html' => 'Επιμόρφωση στις αρχές Εκπαίδευσης Ενηλίκων, σε θέματα ΣΔΕ ή στη Διοίκηση Εκπαιδευτικών Μονάδων από φορείς του δημόσιου ή ευρύτερου δημόσιου τομέα.', 'cap_html' => 'έως 5')); ?>
        <div class="field-grid"><div class="field full"><label for="trainingHours">Συνολικές επιλέξιμες ώρες ολοκληρωμένων επιμορφώσεων <small>0,5 μόρια ανά 100 ώρες. Κάθε επιμέρους επιμόρφωση κάτω των 15 ωρών λαμβάνει 0 μόρια. Μην συμπεριλαμβάνεις ημερίδες, διημερίδες ή συνέδρια.</small></label><input type="number" id="trainingHours" min="0" step="1" value="0" oninput="calculate()"></div></div>
      <?php calculatorCardEnd(); ?>

      <?php calculatorCardStart(array('id' => 'interviewCard', 'class' => 'card hidden', 'header_variant' => 'section-head', 'title_html' => '6. Συνέντευξη', 'subtitle_html' => 'Μόνο για τους υποψήφιους Διευθυντές. Αν δεν έχει πραγματοποιηθεί ακόμη, άφησε το πεδίο κενό για να δεις το σύνολο πριν από τη συνέντευξη.', 'cap_html' => 'έως 25')); ?>
        <div class="field-grid"><div class="field full"><label for="interviewScore">Βαθμολογία συνέντευξης</label><input type="number" id="interviewScore" min="0" max="25" step="0.01" placeholder="0–25" oninput="normalizeBoundedScore(this);calculate()"></div></div>
      <?php calculatorCardEnd(); ?>
    <?php calculatorMainEnd(); ?>

    <?php calculatorResultsStart(array('class' => 'results', 'attrs' => array('aria-live' => 'polite'))); ?>
      <?php calculatorCardStart(); ?>
        <div class="role-chip" id="roleChip">Επίλεξε θέση</div>
        <h2>Αποτέλεσμα</h2>
        <div class="big-total"><div class="context" id="totalContext">Μόρια κριτηρίων</div><div class="number" id="totalScore">0</div><div class="outof" id="totalOutOf">από 75 μόρια</div></div>
        <div class="bar"><div id="totalBar"></div></div>
        <?php calculatorResultRow(array('label_html' => 'Τυπικά προσόντα', 'value_html' => '0 / 25', 'value_id' => 'formalScore')); ?>
        <?php calculatorResultRow(array('label_html' => 'Διδακτική εμπειρία', 'value_html' => '0 / —', 'value_id' => 'teachingScore')); ?>
        <?php calculatorResultRow(array('label_html' => 'Διοικητική εμπειρία', 'value_html' => '0 / —', 'value_id' => 'adminScore')); ?>
        <?php calculatorResultRow(array('label_html' => 'Επιμόρφωση', 'value_html' => '0 / 5', 'value_id' => 'trainingScore')); ?>
        <?php calculatorResultRow(array('class' => 'result-row hidden', 'id' => 'interviewRow', 'label_html' => 'Συνέντευξη', 'value_html' => '— / 25', 'value_id' => 'interviewResult')); ?>
        <?php calculatorResultRow(array('class' => 'result-row emphasis', 'id' => 'criteriaRow', 'label_html' => 'Σύνολο πριν συνέντευξη', 'value_html' => '0 / 75', 'value_id' => 'criteriaScore')); ?>
        <div id="eligibilityStatus" role="status" aria-live="polite"></div>
        <?php calculatorActions(array(array('attrs' => array('class' => 'primary', 'type' => 'button', 'onclick' => 'copySummary(this)'), 'html' => 'Αντιγραφή αποτελέσματος'), array('attrs' => array('class' => 'secondary', 'type' => 'button', 'onclick' => 'resetForm()'), 'html' => 'Μηδενισμός'))); ?>
      <?php calculatorCardEnd(); ?>

      <?php calculatorCardStart(); ?><h2>Ανάλυση μορίων</h2><div id="breakdown" class="subtitle">Συμπλήρωσε τα στοιχεία σου.</div><?php calculatorCardEnd(); ?>
      <?php calculatorCardStart(); ?><h2>Σημαντικός κανόνας</h2><p class="subtitle">Χρόνος Διευθυντή/Υποδιευθυντή που λογίζεται και ως διδακτικός δεν μοριοδοτείται και στις δύο κατηγορίες για την ίδια περίοδο. Αν διοικητικός χρόνος μένει εκτός λόγω πλαφόν, μπορεί να προσμετρηθεί στο αντίστοιχο πεδίο διδακτικής εμπειρίας όπου προβλέπεται.</p><?php calculatorCardEnd(); ?>
      <?php calculatorCardStart(); ?><h2>Σε περίπτωση ισοβαθμίας</h2><p class="subtitle">Στον τελικό πίνακα προηγείται ο υποψήφιος με περισσότερες μονάδες στη συνέντευξη, όπου αυτή προβλέπεται. Αν εξακολουθεί η ισοβαθμία ή δεν προβλέπεται συνέντευξη, εξετάζονται τα κριτήρια με τη σειρά που αναγράφονται στο άρθρο 4.</p><?php calculatorCardEnd(); ?>
    <?php calculatorResultsEnd(); ?>
  <?php calculatorColumnsEnd(); ?>

  <?php sourceCardStart(); ?><p><strong>Πηγή:</strong> Υ.Α. 70621/Κ1, ΦΕΚ Β΄ 3037/19.06.2025 «Καθορισμός κριτηρίων και διαδικασίας επιλογής Διευθυντών και Υποδιευθυντών Σχολείων Δεύτερης Ευκαιρίας (Σ.Δ.Ε.)».</p><?php sourceCardDisclaimerStart(); ?>Το εργαλείο παρέχει ενδεικτικό υπολογισμό· η τελική κρίση ανήκει στην αρμόδια Επιτροπή Επιλογής.<?php sourceCardDisclaimerEnd(); ?><?php sourceCardEnd(); ?>
  <?php require_once __DIR__ . '/includes/footer.php'; ?>
</main>
<script src="<?php echo htmlspecialchars(edu_asset_url('includes/sde-leadership-calculations.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
<script>
  const $ = id => document.getElementById(id);
  const val = id => $(id).value;
  const num = id => Math.max(0, Number($(id).value || 0));
  const EXPERIENCE_YEAR_MAX = 40;
  const boundedYears = (id, preserveBlank = false) => {
    const el = $(id);
    if (!el || el.value === '') return preserveBlank ? '' : 0;
    let value = Number(el.value);
    if (!Number.isFinite(value)) value = 0;
    value = Math.min(EXPERIENCE_YEAR_MAX, Math.max(0, value));
    if (String(value) !== el.value) el.value = String(value);
    return value;
  };
  const normalizeBoundedScore = el => {
    if (!el || el.value === '') return;
    let value = Number(el.value);
    if (!Number.isFinite(value)) { el.value = ''; return; }
    const min = el.min !== '' ? Number(el.min) : 0;
    const max = el.max !== '' ? Number(el.max) : Infinity;
    value = Math.min(max, Math.max(min, value));
    el.value = String(Math.round(value * 100) / 100);
  };
  const yes = id => val(id) === 'yes';
  const fmt = value => {
    const x = Number(value || 0);
    return x.toLocaleString('el-GR', { maximumFractionDigits: 2, minimumFractionDigits: Number.isInteger(x) ? 0 : 1 });
  };

  function collectData(){
    return {
      role: val('role'), permanentTeacher: val('permanentTeacher'), educationalServiceYears: boundedYears('educationalServiceYears', true),
      tertiaryDegree: val('tertiaryDegree'), assignmentEligible: val('assignmentEligible'),
      computerKnowledge: val('computerKnowledge') === 'pe86' ? 'yes' : val('computerKnowledge'),
      adultEducationExperience: val('adultEducationExperience'), adminQualifications: val('adminQualifications'), blockingIssue: val('blockingIssue'),
      phd: val('phd'), master: val('master'), esdda: yes('esdda'), secondDegree: yes('secondDegree'),
      language1: val('language1'), languageLevel1: val('languageLevel1'), languageAppointment1: yes('languageAppointment1'),
      language2: val('language2'), languageLevel2: val('languageLevel2'), languageAppointment2: yes('languageAppointment2'),
      sdeTeachingYears: boundedYears('sdeTeachingYears'), sdeTeachingHours: num('sdeTeachingHours'), sdeTransferredYears: boundedYears('sdeTransferredYears'),
      adultNonformalHours: num('adultNonformalHours'), schoolTeachingYears: boundedYears('schoolTeachingYears'), schoolTeachingHours: num('schoolTeachingHours'), schoolTransferredYears: boundedYears('schoolTransferredYears'),
      sdeDirectorYears: boundedYears('sdeDirectorYears'), sdeDeputyYears: boundedYears('sdeDeputyYears'), otherAdminYears: boundedYears('otherAdminYears'), trainingHours: num('trainingHours'),
      interviewScore: val('interviewScore')
    };
  }

  function roleChanged(){
    const role = val('role');
    const cfg = SDELeadership.ROLE[role];
    $('tertiaryDegreeWrap').classList.toggle('hidden', role !== 'director');
    $('adminQualificationsWrap').classList.toggle('hidden', role !== 'director');
    $('interviewCard').classList.toggle('hidden', role !== 'director');
    $('interviewRow').classList.toggle('hidden', role !== 'director');
    if (role !== 'director') $('interviewScore').value = '';
    if (cfg){
      $('serviceRequirement').textContent = 'Απαιτούνται τουλάχιστον ' + cfg.minServiceYears + ' έτη.';
      $('assignmentRequirement').textContent = 'Απαιτείται δυνατότητα κάλυψης ' + cfg.requiredTeachingHours + ' ωρών διδακτικού έργου.';
      $('teachingMax').textContent = 'έως ' + cfg.max.teaching;
      $('adminMax').textContent = 'έως ' + cfg.max.admin;
    } else {
      $('serviceRequirement').textContent = 'Επίλεξε θέση για να εμφανιστεί το ελάχιστο.';
      $('assignmentRequirement').textContent = 'Επίλεξε θέση.';
      $('teachingMax').textContent = 'έως —'; $('adminMax').textContent = 'έως —';
    }
    calculate();
  }

  function languageChanged(){
    const l1 = val('language1'); const l2 = val('language2');
    if (l1 && l2 && l1 === l2 && !l1.startsWith('other')) {
      $('language2').value = '';
      $('languageLevel2').value = '';
    }
    calculate();
  }

  function renderEligibility(result){
    const box = $('eligibilityStatus');
    const e = result.eligibility;
    if (!result.role){ box.className='warning'; box.innerHTML='<strong>Επίλεξε θέση υποψηφιότητας.</strong>'; return; }
    if (e.status === 'eligible') { box.className='success'; box.innerHTML='<strong>✅ Οι βασικές προϋποθέσεις φαίνεται να πληρούνται.</strong>'; return; }
    if (e.status === 'not-eligible') { box.className='danger'; box.innerHTML='<strong>⚠️ Έλεγχος βασικών προϋποθέσεων:</strong><ul class="criteria-list">'+e.issues.map(x=>'<li>'+x+'</li>').join('')+'</ul>'; return; }
    box.className='warning'; box.innerHTML='<strong>Χρειάζονται ακόμη στοιχεία:</strong><ul class="criteria-list">'+e.missing.map(x=>'<li>'+x+'</li>').join('')+'</ul>';
  }

  function renderBreakdown(result){
    const groups = [
      ['Τυπικά προσόντα', result.formal.details], ['Διδακτική εμπειρία', result.teaching.details],
      ['Διοικητική εμπειρία', result.admin.details], ['Επιμόρφωση', result.training.details]
    ];
    let html = '';
    groups.forEach(([title, items]) => {
      html += '<h3>'+title+'</h3>';
      if (!items.length) html += '<p class="subtitle">0 μόρια</p>';
      else html += '<ul class="breakdown-list">'+items.map(i=>'<li><span>'+i.label+'</span><strong>'+fmt(i.points)+'</strong></li>').join('')+'</ul>';
    });
    if (result.role === 'director') html += '<h3>Συνέντευξη</h3><p class="subtitle">'+(result.interviewEntered ? fmt(result.interview)+' / 25' : 'Δεν έχει καταχωριστεί ακόμη.')+'</p>';
    if (result.warnings.length) html += '<div class="warning">'+result.warnings.join('<br>')+'</div>';
    $('breakdown').innerHTML = html || 'Συμπλήρωσε τα στοιχεία σου.';
  }

  function renderOverflow(result){
    if (!result.config){ $('overflowHint').textContent='Επίλεξε θέση για να υπολογιστούν τα επιμέρους πλαφόν και τυχόν διοικητικός χρόνος που μένει εκτός μοριοδότησης.'; return; }
    const o = result.admin.overflow;
    const sde = o.sdeDirector + o.sdeDeputy;
    if (!sde && !o.other){
      $('overflowHint').innerHTML='<strong>Δεν προκύπτει διοικητικός χρόνος πάνω από τα επιμέρους πλαφόν.</strong> Θυμήσου ότι ίδια περίοδος δεν δηλώνεται ταυτόχρονα ως διοικητική και διδακτική.';
    } else {
      $('overflowHint').innerHTML='<strong>Πιθανός χρόνος εκτός διοικητικής μοριοδότησης λόγω πλαφόν:</strong><br>ΣΔΕ: '+fmt(sde)+' έτη · Λοιπές δομές: '+fmt(o.other)+' έτη.<br><small>Ο χρόνος δεν μεταφέρεται αυτόματα. Εφόσον πληροί τον κανόνα του ΦΕΚ, δήλωσέ τον μόνο στο αντίστοιχο πεδίο μεταφοράς της διδακτικής εμπειρίας.</small>';
    }
  }

  function calculate(){
    const result = SDELeadership.calculate(collectData());
    const cfg = result.config;
    $('roleChip').textContent = cfg ? cfg.label : 'Επίλεξε θέση';
    $('formalScore').textContent = fmt(result.formal.total) + ' / 25';
    $('teachingScore').textContent = fmt(result.teaching.total) + ' / ' + (cfg ? cfg.max.teaching : '—');
    $('adminScore').textContent = fmt(result.admin.total) + ' / ' + (cfg ? cfg.max.admin : '—');
    $('trainingScore').textContent = fmt(result.training.total) + ' / 5';
    $('criteriaScore').textContent = fmt(result.criteria) + ' / 75';

    let main = result.criteria, denom = 75, context='Μόρια κριτηρίων';
    if (result.role === 'director') {
      $('interviewResult').textContent = result.interviewEntered ? fmt(result.interview) + ' / 25' : '— / 25';
      if (result.interviewEntered) { main = result.final; denom = 100; context='Τελικό σύνολο'; }
      else context='Πριν από τη συνέντευξη';
      $('criteriaRow').classList.remove('hidden');
    } else if (result.role === 'deputy') {
      context='Τελικό σύνολο';
      $('criteriaRow').classList.add('hidden');
    } else {
      $('criteriaRow').classList.remove('hidden');
    }
    $('totalScore').textContent = fmt(main);
    $('totalOutOf').textContent = 'από ' + denom + ' μόρια';
    $('totalContext').textContent = context;
    $('totalBar').style.width = Math.min(100, denom ? (main/denom*100) : 0) + '%';
    renderEligibility(result); renderBreakdown(result); renderOverflow(result);
    return result;
  }

  async function copySummary(button){
    const r = calculate();
    const cfg = r.config;
    const lines = ['Μόρια Διευθυντών / Υποδιευθυντών ΣΔΕ', cfg ? 'Θέση: '+cfg.label : 'Θέση: —', 'Τυπικά προσόντα: '+fmt(r.formal.total)+'/25', 'Διδακτική εμπειρία: '+fmt(r.teaching.total)+'/'+(cfg?cfg.max.teaching:'—'), 'Διοικητική εμπειρία: '+fmt(r.admin.total)+'/'+(cfg?cfg.max.admin:'—'), 'Επιμόρφωση: '+fmt(r.training.total)+'/5', 'Σύνολο κριτηρίων: '+fmt(r.criteria)+'/75'];
    if (r.role === 'director') lines.push('Συνέντευξη: '+(r.interviewEntered ? fmt(r.interview)+'/25' : 'δεν έχει καταχωριστεί'), 'Τελικό: '+(r.final == null ? '— /100' : fmt(r.final)+'/100'));
    else if (r.role === 'deputy') lines.push('Τελικό: '+fmt(r.criteria)+'/75');
    lines.push('Πηγή: Υ.Α. 70621/Κ1, ΦΕΚ Β΄ 3037/19.06.2025');
    try { await navigator.clipboard.writeText(lines.join('\n')); if(button){const old=button.textContent;button.textContent='Αντιγράφηκε ✓';setTimeout(()=>button.textContent=old,1200);} } catch(e){ alert(lines.join('\n')); }
  }

  function resetForm(){
    document.querySelectorAll('input[type="number"]').forEach(i => i.value = (i.id === 'educationalServiceYears' || i.id === 'interviewScore') ? '' : '0');
    document.querySelectorAll('select').forEach(s => {
      if (['phd','master'].includes(s.id)) s.value='none';
      else if (['esdda','secondDegree','languageAppointment1','languageAppointment2'].includes(s.id)) s.value='no';
      else s.selectedIndex=0;
    });
    roleChanged(); window.scrollTo({top:0,behavior:'smooth'});
  }

  roleChanged();
</script>
<script src="<?php echo htmlspecialchars(edu_asset_url('assets/common.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
</body>
</html>
