<?php require_once __DIR__ . '/includes/config.php'; ?>
<!DOCTYPE html>
<html lang="el">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="Ενδεικτικός υπολογισμός μορίων απόσπασης εκπαιδευτικών από ΠΥΣΠΕ/ΠΥΣΔΕ σε ΠΥΣΠΕ/ΠΥΣΔΕ για το διδακτικό έτος 2026-2027.">
  <title>Μόρια Απόσπασης</title>

  <link rel="stylesheet" href="<?php echo htmlspecialchars(edu_asset_url('assets/common.css'), ENT_QUOTES, 'UTF-8'); ?>">
</head>

<body class="edu-ui edu-page-detachment">
<?php require_once __DIR__ . '/includes/header.php'; ?>
<?php require_once __DIR__ . '/includes/components/calculator-layout.php'; ?>

<?php calculatorContainerStart(array('class' => 'app-box edu-modernized')); ?>
<?php calculatorHero(array(
  'class' => 'hero edu-legacy-hero',
  'title' => 'Μόρια Απόσπασης',
  'intro_html' => 'Υπολόγισε <strong>ενδεικτικά</strong> τα μόριά σου για απόσπαση <strong>από ΠΥΣΠΕ/ΠΥΣΔΕ σε ΠΥΣΠΕ/ΠΥΣΔΕ</strong>. Ορισμένα κριτήρια εξαρτώνται από τη συγκεκριμένη περιοχή που ζητάς, γι’ αυτό ο υπολογισμός πρέπει να γίνεται χωριστά για κάθε περιοχή ενδιαφέροντος.',
  'intro_attrs' => array('class' => 'intro')
)); ?>

  <?php calculatorColumnsStart(); ?>
    <?php calculatorMainStart(array('tag' => 'main')); ?>

  <?php calculatorCardStart(array('tag' => 'div', 'class' => 'section')); ?>
    <h2>Α. Προκαταρκτικός έλεγχος δικαιώματος / κωλύματος</h2>

    <div class="question">
      <label for="appointmentStatus">Πότε έγινε ο μόνιμος διορισμός σας;</label>
      <select id="appointmentStatus">
        <option value="">— Επιλογή —</option>
        <option value="not_new">Διορίστηκα έως το 2023</option>
        <option value="before_2024_09_01">Διορίστηκα το 2024, πριν από 01-09-2024</option>
        <option value="sep_2024">Διορίστηκα από 01-09-2024 έως 30-09-2024</option>
        <option value="after_2024_09_30">Διορίστηκα από 01-10-2024 και μετά</option>
      </select>
    </div>

    <p class="note">
      Η εγκύκλιος αναφέρει ρητά ότι όσοι διορίστηκαν πριν από 01-09-2024 μπορούν να υποβάλουν αίτηση
      ανεξάρτητα από τη διετία, ενώ όσοι διορίστηκαν μετά τις 30-09-2024 μπορούν να υποβάλουν αίτηση
      ΠΥΣΠΕ/ΠΥΣΔΕ → ΠΥΣΠΕ/ΠΥΣΔΕ μόνο εφόσον υπάγονται στις προβλεπόμενες κατ’ εξαίρεση περιπτώσεις.
    </p>

    <h3>Βασικά κωλύματα που μπορεί να εμποδίζουν την εξέταση της αίτησης</h3>
    <div class="compact-checks">
      <div class="check-row">
        <input type="checkbox" id="obstacleMusicExclusive">
        <label for="obstacleMusicExclusive">ΠΕ79.01 / ΤΕ16 με αποκλειστικό διορισμό και τοποθέτηση σε Μουσικό Σχολείο από το 2006 και μετά</label>
      </div>
      <div class="check-row">
        <input type="checkbox" id="obstacleLeader">
        <label for="obstacleLeader">Στέλεχος εκπαίδευσης με θητεία που λήγει μετά τις 31-08-2026</label>
      </div>
      <div class="check-row">
        <input type="checkbox" id="obstacleTermDetachment">
        <label for="obstacleTermDetachment">Απόσπαση με θητεία που δεν λήγει έως 31-08-2026</label>
      </div>
      <div class="check-row">
        <input type="checkbox" id="obstacleActiveDetachment">
        <label for="obstacleActiveDetachment">Άλλη απόσπαση που δεν λήγει έως 31-08-2026</label>
      </div>
      <div class="check-row">
        <input type="checkbox" id="obstacleESK">
        <label for="obstacleESK">Κώλυμα διετίας από μη ανάληψη / ανάκληση απόσπασης ή μετάταξης μέσω ΕΣΚ (χωρίς την εξαίρεση λόγων υγείας)</label>
      </div>
      <div class="check-row">
        <input type="checkbox" id="obstacleSuspension">
        <label for="obstacleSuspension">Κατάσταση αργίας ή αναστολή άσκησης καθηκόντων</label>
      </div>
      <div class="check-row">
        <input type="checkbox" id="obstacleEaeGeneral">
        <label for="obstacleEaeGeneral">Διορισμός στην ΕΑΕ χωρίς συμπλήρωση 5 ετών και αίτημα απόσπασης στη Γενική Εκπαίδευση</label>
      </div>
    </div>

    <p class="note">
      Ο έλεγχος είναι βοηθητικός. Για ΠΕ61/ΠΕ71 η εγκύκλιος επισημαίνει ειδικά ότι δεν είναι δυνατή απόσπαση στη Γενική Εκπαίδευση.
    </p>
  <?php calculatorCardEnd(); ?>

  <?php calculatorCardStart(array('tag' => 'div', 'class' => 'section')); ?>
    <h2>Β. Έλεγχος πιθανής απόσπασης κατά προτεραιότητα</h2>

    <div class="check-row">
      <input type="checkbox" id="prioritySpecialCategory">
      <label for="prioritySpecialCategory">
        Ανήκω σε <strong>ειδική κατηγορία μετάθεσης</strong>
        (π.χ. πολύτεκνος/η, ειδική περίπτωση λόγω πάθησης ή γονέας τέκνου με αναπηρία 67% και άνω).
      </label>
    </div>
    <p class="note">
      Οι ειδικές κατηγορίες καθορίζονται από το άρθρο 13 του π.δ. 50/1996 και τις λοιπές διατάξεις
      στις οποίες παραπέμπει η εγκύκλιος. Η επιλογή εδώ αποτελεί δήλωση του χρήστη και δεν πιστοποιεί
      από μόνη της την υπαγωγή σε ειδική κατηγορία.
    </p>

    <div class="check-row">
      <input type="checkbox" id="priorityNewSelfSpouse75">
      <label for="priorityNewSelfSpouse75">
        Είμαι νεοδιόριστος/η και εγώ ή ο/η σύζυγος έχουμε αναπηρία <strong>75% και άνω</strong>.
      </label>
    </div>

    <div class="check-row">
      <input type="checkbox" id="priorityNewChild67">
      <label for="priorityNewChild67">
        Είμαι νεοδιόριστος/η και έχω τέκνο με αναπηρία <strong>67% και άνω</strong>.
      </label>
    </div>

    <div class="question">
      <label for="priorityCoServiceCategory">Κατηγορία κατά προτεραιότητα που συνδέεται με σύζυγο / συμβιούντα</label>
      <select id="priorityCoServiceCategory">
        <option value="none">Καμία από τις παρακάτω</option>
        <option value="uniformed">Σύζυγος/συμβιών στρατιωτικού, ΕΛ.ΑΣ., Πυροσβεστικού, Λιμενικού, προσωπικού Καταστημάτων Κράτησης, πληρώματος ασθενοφόρου ΕΚΑΒ ή άλλης περίπτωσης της κατηγορίας γ</option>
        <option value="judicial">Σύζυγος δικαστικού λειτουργού ή κύριου προσωπικού ΝΣΚ</option>
        <option value="university">Σύζυγος μέλους ΔΕΠ, ΕΔΙΠ, ΕΕΠ ή ΕΤΕΠ</option>
      </select>
    </div>

    <div class="check-row">
      <input type="checkbox" id="priorityElected">
      <label for="priorityElected">
        Είμαι αιρετός/ή ΟΤΑ σε μία από τις προβλεπόμενες ιδιότητες (π.χ. περιφερειακός/δημοτικός σύμβουλος, δήμαρχος κ.λπ.).
      </label>
    </div>

    <div class="check-row">
      <input type="checkbox" id="priorityFirstPreference">
      <label for="priorityFirstPreference">
        Για την κατά προτεραιότητα περίπτωση που βασίζεται σε συνυπηρέτηση, έχω δηλώσει ως <strong>1η προτίμηση</strong> το συγκεκριμένο ΠΥΣΠΕ/ΠΥΣΔΕ.
      </label>
    </div>

    <?php calculatorDisclosureStart(array(
      'summary' => 'Πώς λειτουργεί η κατά προτεραιότητα απόσπαση;',
      'open' => true,
      'attrs' => array('data-mobile-collapsed' => 'true')
    )); ?>
      <p>
        Οι κατά προτεραιότητα αιτήσεις εξετάζονται συγκρινόμενες μόνο μεταξύ τους. Για τις περιπτώσεις συνυπηρέτησης,
        αν δεν δηλωθεί ως πρώτη προτίμηση το ΠΥΣΠΕ/ΠΥΣΔΕ όπου υπάρχει η συνυπηρέτηση, δεν ισχύει η κατά προτεραιότητα απόσπαση.
        Στην κατά προτεραιότητα διαδικασία Αθήνα και Θεσσαλονίκη <strong>δεν</strong> αντιμετωπίζονται ενιαία.
      </p>
    <?php calculatorDisclosureEnd(); ?>
  <?php calculatorCardEnd(); ?>

  <?php calculatorCardStart(array('tag' => 'div', 'class' => 'section')); ?>
    <h2>Περιοχή για την οποία γίνεται ο υπολογισμός</h2>

    <div class="question">
      <label for="requestedArea">ΠΥΣΠΕ / ΠΥΣΔΕ ενδιαφέροντος (προαιρετικό)</label>
      <input type="text" id="requestedArea" placeholder="π.χ. ΠΥΣΔΕ Κέρκυρας">
    </div>

    <p class="note">
      Η συνυπηρέτηση, η εντοπιότητα, ορισμένοι λόγοι υγείας γονέων και οι σπουδές
      μπορεί να δίνουν μόρια μόνο για συγκεκριμένο ΠΥΣΠΕ/ΠΥΣΔΕ.
    </p>
  <?php calculatorCardEnd(); ?>

  <?php calculatorCardStart(array('tag' => 'div', 'class' => 'section')); ?>
    <h2>1. Συνολική υπηρεσία</h2>

    <div class="field-grid three">
      <div class="question">
        <label for="serviceYears">Έτη</label>
        <input type="number" id="serviceYears" min="0" max="50" step="1" value="0">
      </div>
      <div class="question">
        <label for="serviceMonths">Μήνες</label>
        <input type="number" id="serviceMonths" min="0" max="11" step="1" value="0">
      </div>
      <div class="question">
        <label for="serviceDays">Υπόλοιπο ημερών</label>
        <input type="number" id="serviceDays" min="0" max="30" step="1" value="0">
      </div>
    </div>

    <?php calculatorDisclosureStart(array(
      'summary' => 'Κλίμακα μοριοδότησης συνολικής υπηρεσίας',
      'open' => true,
      'attrs' => array('data-mobile-collapsed' => 'true')
    )); ?>
      <table class="mini-table">
        <tr><th>Χρόνος υπηρεσίας</th><th>Μοριοδότηση</th></tr>
        <tr><td>1 έως και 10 έτη</td><td>1 μονάδα ανά έτος</td></tr>
        <tr><td>Πάνω από 10 έως και 20 έτη</td><td>1,5 μονάδα ανά έτος</td></tr>
        <tr><td>Πάνω από 20 έτη</td><td>2 μονάδες ανά έτος</td></tr>
      </table>
      <p>
        Ο χρόνος συνολικής υπηρεσίας ταυτίζεται με τον χρόνο που υπολογίζεται στις μεταθέσεις.
        Υπόλοιπο <strong>15 ημερών και άνω</strong> υπολογίζεται ως ένας πλήρης μήνας.
      </p>
    <?php calculatorDisclosureEnd(); ?>
  <?php calculatorCardEnd(); ?>

  <?php calculatorCardStart(array('tag' => 'div', 'class' => 'section')); ?>
    <h2>2. Κριτήρια που συνδέονται με την περιοχή απόσπασης</h2>

    <h3>Συνυπηρέτηση — 10 μόρια</h3>
    <div class="question">
      <label for="coServiceType">Ποια περίπτωση περιγράφει τον/τη σύζυγο;</label>
      <select id="coServiceType">
        <option value="none">Δεν ζητώ / δεν δικαιούμαι συνυπηρέτηση</option>
        <option value="public_organic">Υπηρετεί οργανικά (όχι με απόσπαση) σε υπηρεσία του δημόσιου τομέα στο συγκεκριμένο ΠΥΣΠΕ/ΠΥΣΔΕ</option>
        <option value="teacher_term">Είναι εκπαιδευτικός που υπηρετεί με θητεία στο συγκεκριμένο ΠΥΣΠΕ/ΠΥΣΔΕ</option>
        <option value="public_contract">Εργάζεται στο δημόσιο με σύμβαση ορισμένου/αορίστου χρόνου ή ως αναπληρωτής/ωρομίσθιος</option>
        <option value="private">Εργάζεται στον ιδιωτικό τομέα</option>
        <option value="unemployed_all_year">Ήταν άνεργος/η σε όλο το τελευταίο έτος</option>
      </select>
    </div>

    <div class="compact-checks">
      <div class="check-row">
        <input type="checkbox" id="coServiceOneYearSameArea">
        <label for="coServiceOneYearSameArea">Για τις περιπτώσεις σύμβασης/ιδιωτικού τομέα, το τελευταίο έτος εργασίας ή ανεργίας αφορά το ίδιο ΠΥΣΠΕ/ΠΥΣΔΕ.</label>
      </div>
      <div class="check-row">
        <input type="checkbox" id="coServiceWorkedDay">
        <label for="coServiceWorkedDay">Υπήρξε τουλάχιστον <strong>1 ημέρα εργασίας</strong> στην περιοχή μέσα στο τελευταίο έτος.</label>
      </div>
    </div>

    <div class="check-row">
      <input type="checkbox" id="locality">
      <label for="locality">
        <strong>Εντοπιότητα</strong> στο συγκεκριμένο ΠΥΣΠΕ/ΠΥΣΔΕ — <strong>4 μόρια</strong>
      </label>
    </div>

    <p class="note">
      Στην απλή μοριοδότηση συνυπηρέτησης, Α΄/Β΄/Γ΄/Δ΄ Αθήνας αντιμετωπίζονται ενιαία και αντίστοιχα
      Α΄/Β΄ Θεσσαλονίκης αντιμετωπίζονται ενιαία. Η εντοπιότητα ισχύει σε επίπεδο ΠΥΣΠΕ/ΠΥΣΔΕ.
    </p>
  <?php calculatorCardEnd(); ?>

  <?php calculatorCardStart(array('tag' => 'div', 'class' => 'section')); ?>
    <h2>3. Οικογενειακοί λόγοι</h2>

    <div class="field-grid">
      <div class="question">
        <label for="familyStatus">Οικογενειακή κατάσταση</label>
        <select id="familyStatus">
          <option value="none">Καμία μοριοδοτούμενη περίπτωση</option>
          <option value="married">Έγγαμος/η ή σύμφωνο συμβίωσης — 4 μόρια</option>
          <option value="divorced_custody">Διαζευγμένος/η ή σε διάσταση με νόμιμη επιμέλεια — 4 μόρια</option>
          <option value="widowed_child">Σε χηρεία με άγαμο ανήλικο ή σπουδάζον παιδί — 12 μόρια</option>
          <option value="widowed_nochild">Σε χηρεία χωρίς μοριοδοτούμενο παιδί — 4 μόρια</option>
          <option value="single_parent">Άγαμος/η με άγαμο ανήλικο ή σπουδάζον παιδί — 6 μόρια</option>
        </select>
      </div>
      <div class="question">
        <label for="eligibleChildren">Αριθμός τέκνων που μοριοδοτούνται</label>
        <input type="number" id="eligibleChildren" min="0" max="20" step="1" value="0">
      </div>
    </div>

    <?php calculatorDisclosureStart(array(
      'summary' => 'Πώς μοριοδοτούνται τα τέκνα;',
      'open' => true,
      'attrs' => array('data-mobile-collapsed' => 'true')
    )); ?>
      <p>
        Τέκνα: 5 μόρια για το πρώτο, 6 για το δεύτερο, 8 για το τρίτο και 10 για κάθε επόμενο,
        εφόσον είναι άγαμα ανήλικα ή σπουδάζουν. <strong>Ειδικά για το 2025-2026</strong>, τέκνο που φοιτά
        στη Γ΄ Λυκείου δικαιούται μόρια ακόμη και αν έχει συμπληρώσει το 18ο έτος και δεν έχει ακόμη
        εγγραφεί σε ανώτερη/ανώτατη δημόσια σχολή.
      </p>
    <?php calculatorDisclosureEnd(); ?>
  <?php calculatorCardEnd(); ?>

  <?php calculatorCardStart(array('tag' => 'div', 'class' => 'section')); ?>
    <h2>4. Σοβαροί λόγοι υγείας</h2>

    <h3>Α. Εκπαιδευτικός, τέκνο ή/και σύζυγος</h3>
    <div class="field-grid">
      <div class="question">
        <label for="healthPerson">Σε ποιο πρόσωπο αφορά η υψηλότερη μοριοδοτούμενη περίπτωση;</label>
        <select id="healthPerson">
          <option value="none">Κανένα</option>
          <option value="self">Τον/την εκπαιδευτικό</option>
          <option value="spouse">Τον/τη σύζυγο</option>
          <option value="child">Τέκνο</option>
        </select>
      </div>
      <div class="question">
        <label for="healthSelfFamily">Ποσοστό αναπηρίας</label>
        <select id="healthSelfFamily">
          <option value="0">Καμία</option>
          <option value="5">50%–66% — 5 μόρια</option>
          <option value="20">67%–79% — 20 μόρια</option>
          <option value="30">80% και άνω — 30 μόρια</option>
        </select>
      </div>
    </div>

    <div class="check-row">
      <input type="checkbox" id="healthChildProtected">
      <label for="healthChildProtected">Αν αφορά τέκνο: είναι προστατευόμενο μέλος ή διαμένει με τον/την εκπαιδευτικό.</label>
    </div>

    <h3>Β. Γονείς του/της εκπαιδευτικού</h3>
    <div class="field-grid">
      <div class="question">
        <label for="healthParents">Υψηλότερη περίπτωση γονέα</label>
        <select id="healthParents">
          <option value="0">Καμία</option>
          <option value="1">Αναπηρία 50%–66% — 1 μόριο</option>
          <option value="3">Αναπηρία 67% και άνω — 3 μόρια</option>
        </select>
      </div>
      <div class="check-row">
        <input type="checkbox" id="parentLocationEligible">
        <label for="parentLocationEligible">Ο γονέας είναι δημότης από διετίας και διαμένει σε δήμο της περιοχής όπου ζητείται η απόσπαση.</label>
      </div>
    </div>

    <div class="check-row">
      <input type="checkbox" id="siblingHealth">
      <label for="siblingHealth">Αδελφός/ή με αναπηρία 67% και άνω και δικαστική απόφαση επιμέλειας / δικαστικής συμπαράστασης — <strong>5 μόρια</strong></label>
    </div>

    <div class="check-row">
      <input type="checkbox" id="ivf">
      <label for="ivf">Θεραπεία εξωσωματικής γονιμοποίησης του/της εκπαιδευτικού ή του/της συζύγου — <strong>3 μόρια</strong></label>
    </div>

    <?php calculatorDisclosureStart(array(
      'summary' => 'Προϋποθέσεις μοριοδότησης σοβαρών λόγων υγείας',
      'open' => true,
      'attrs' => array('data-mobile-collapsed' => 'true')
    )); ?>
      <p>
        Η μοριοδότηση σοβαρών λόγων υγείας δεν γίνεται προσθετικά <strong>εντός της ίδιας κατηγορίας</strong>
        όταν ο λόγος συντρέχει σε περισσότερα του ενός συγγενικά πρόσωπα. Απαιτείται εν ισχύ γνωμάτευση
        των αρμόδιων υγειονομικών επιτροπών ή ΚΕΠΑ. Για γονέα απαιτούνται επιπλέον τα προβλεπόμενα
        δικαιολογητικά εντοπιότητας και μόνιμης κατοικίας.
      </p>
    <?php calculatorDisclosureEnd(); ?>
  <?php calculatorCardEnd(); ?>

  <?php calculatorCardStart(array('tag' => 'div', 'class' => 'section')); ?>
    <h2>5. Λοιποί λόγοι — σπουδές</h2>

    <div class="question">
      <label for="studyType">Τύπος σπουδών</label>
      <select id="studyType">
        <option value="none">Δεν ζητώ μόρια σπουδών</option>
        <option value="eligible">Μεταπτυχιακές σπουδές ή απόκτηση άλλου τίτλου ΑΕΙ</option>
        <option value="eap">Σπουδές στο Ελληνικό Ανοικτό Πανεπιστήμιο (δεν μοριοδοτούνται)</option>
        <option value="phd">Διδακτορικό (δεν μοριοδοτείται με αυτό το κριτήριο)</option>
      </select>
    </div>

    <div class="compact-checks">
      <div class="check-row">
        <input type="checkbox" id="studyDifferentArea">
        <label for="studyDifferentArea">Η σχολή εδρεύει σε διαφορετική περιοχή από την περιοχή οργανικής μου.</label>
      </div>
      <div class="check-row">
        <input type="checkbox" id="studyRequestedArea">
        <label for="studyRequestedArea">Το ΠΥΣΠΕ/ΠΥΣΔΕ που υπολογίζω είναι εκείνο στο οποίο βρίσκεται η σχολή.</label>
      </div>
      <div class="check-row">
        <input type="checkbox" id="studyWithinDuration">
        <label for="studyWithinDuration">Βρίσκομαι μέσα στον προβλεπόμενο χρόνο φοίτησης.</label>
      </div>
    </div>

    <?php calculatorDisclosureStart(array(
      'summary' => 'Προϋποθέσεις μοριοδότησης σπουδών',
      'open' => true,
      'attrs' => array('data-mobile-collapsed' => 'true')
    )); ?>
      <p>
        Εφόσον πληρούνται όλες οι προϋποθέσεις: <strong>2 μόρια</strong>. Τα ΠΥΣΠΕ/ΠΥΣΔΕ Αττικής και Θεσσαλονίκης
        αντιμετωπίζονται ενιαία για το κριτήριο των σπουδών. Δεν δίνονται οι 2 μονάδες για ΕΑΠ ή διδακτορικό.
      </p>
    <?php calculatorDisclosureEnd(); ?>
  <?php calculatorCardEnd(); ?>

  <?php calculatorActions(array(
    array('id' => 'calculateBtn', 'label' => 'Έλεγχος & προβολή αποτελέσματος', 'class' => 'primary-btn', 'attrs' => array('type' => 'button')),
    array('id' => 'resetBtn', 'label' => 'Καθαρισμός', 'class' => 'secondary-btn', 'attrs' => array('type' => 'button'))
  )); ?>

  <?php calculatorInlineResult(array('id' => 'result', 'class' => 'result', 'attrs' => array('role' => 'status', 'aria-live' => 'polite'))); ?>
    <?php calculatorMainEnd(); ?>

    <?php calculatorResultsStart(array('class' => 'card results', 'aria_live' => 'polite')); ?>
      <?php calculatorScoreHeader(array(
        'value_id' => 'grandTotal',
        'value_html' => '0,00',
        'label' => 'συνολικά μόρια απόσπασης'
      )); ?>
      <?php calculatorResultRow(array('label_html' => 'Συνολική υπηρεσία', 'value_html' => '0,00', 'value_id' => 'resService')); ?>
      <?php calculatorResultRow(array('label_html' => 'Συνυπηρέτηση', 'value_html' => '0,00', 'value_id' => 'resCoService')); ?>
      <?php calculatorResultRow(array('label_html' => 'Εντοπιότητα', 'value_html' => '0,00', 'value_id' => 'resLocality')); ?>
      <?php calculatorResultRow(array('label_html' => 'Οικογενειακοί λόγοι', 'value_html' => '0,00', 'value_id' => 'resFamily')); ?>
      <?php calculatorResultRow(array('label_html' => 'Σοβαροί λόγοι υγείας', 'value_html' => '0,00', 'value_id' => 'resHealth')); ?>
      <?php calculatorResultRow(array('label_html' => 'Σπουδές', 'value_html' => '0,00', 'value_id' => 'resStudies')); ?>
      <?php calculatorResultMessage(array(
        'id' => 'sidebarStatus',
        'variant' => 'status',
        'text' => 'Συμπλήρωσε τα στοιχεία σου για ζωντανό υπολογισμό.'
      )); ?>
      <?php calculatorResultMessage(array(
        'variant' => 'disclaimer',
        'text' => 'Η κατά προτεραιότητα εξέταση και τα κωλύματα δεν προσθέτουν μόρια· εμφανίζονται χωριστά στον έλεγχο του αποτελέσματος.'
      )); ?>
    <?php calculatorResultsEnd(); ?>
  <?php calculatorColumnsEnd(); ?>


  <?php sourceCardStart(); ?>
    <p><strong>Βάση υπολογισμού:</strong> Εγκύκλιος Υ.ΠΑΙ.Θ.Α. 41297/Ε2/02-04-2026 για τις αποσπάσεις εκπαιδευτικών του διδακτικού έτους 2026-2027. Ο υπολογιστής αφορά την <strong>Ενότητα Α – Αποσπάσεις με κριτήρια μοριοδότησης</strong>.</p>
    <?php sourceCardLinksStart(); ?><?php sourceCardLink('https://www.minedu.gov.gr/site/64683-02-04-26-prosklisi-ekpaideftikon-protovathmias-kai-defterovathmias-ekpaidefsis-gia-ypovoli-aitiseon-apospaseon-apo-pyspe-pysde-se-pyspe-pysde-se-domes-e-a-e-ke-d-a-s-y-mousika-kai-kallitexnika-sxoleia-gia-to-didaktiko-etos-2026-2027', 'Επίσημη πρόσκληση — Υ.ΠΑΙ.Θ.Α. ↗'); ?><?php sourceCardLinksEnd(); ?>
    <?php sourceCardDisclaimerStart(); ?>Δεν υποκαθιστά τον επίσημο έλεγχο της αίτησης.<?php sourceCardDisclaimerEnd(); ?>
  <?php sourceCardEnd(); ?>

  <p class="small-note">
    <strong>Σημαντικό:</strong> Το αποτέλεσμα είναι ενδεικτικό. Η εφαρμογή κάνει βοηθητικό έλεγχο ορισμένων
    βασικών κωλυμάτων και περιπτώσεων κατά προτεραιότητα, αλλά δεν αποτελεί πλήρη διοικητικό ή νομικό
    έλεγχο ούτε πιστοποιεί την επάρκεια των δικαιολογητικών. Σε περίπτωση διαφοράς ισχύουν αποκλειστικά
    η επίσημη εγκύκλιος, η εφαρμογή ΟΠΣΥΔ και ο έλεγχος των αρμόδιων υπηρεσιών.
  </p>
<?php calculatorContainerEnd(); ?>


<?php require_once __DIR__ . '/includes/footer.php'; ?>
  <script src="<?php echo htmlspecialchars(edu_asset_url('includes/detachment-calculations.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
  <script src="<?php echo htmlspecialchars(edu_asset_url('includes/detachment-ui.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
  <script src="<?php echo htmlspecialchars(edu_asset_url('assets/common.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
</body>
</html>
