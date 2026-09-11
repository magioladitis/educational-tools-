<?php require_once __DIR__ . '/includes/config.php'; require_once __DIR__ . '/includes/teacher-specialties.php'; ?>
<!doctype html>
<html lang="el">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="Υπολογισμός μορίων και βασικός έλεγχος δικαιώματος για απόσπαση εκπαιδευτικών σε ελληνόγλωσσες εκπαιδευτικές μονάδες του εξωτερικού.">
  <title>Μόρια Απόσπασης στο Εξωτερικό</title>
  <link rel="stylesheet" href="<?php echo htmlspecialchars(edu_asset_url('assets/common.css'), ENT_QUOTES, 'UTF-8'); ?>">
</head>
<body class="edu-ui edu-page-abroad">
<main class="page-shell">
  <?php require_once __DIR__ . '/includes/header.php'; ?>
<?php require_once __DIR__ . '/includes/components/calculator-layout.php'; ?>

  <?php calculatorHero(array(
    'title' => 'Μόρια Απόσπασης στο Εξωτερικό',
    'intro' => 'Ενδεικτικός υπολογισμός μορίων και βασικός έλεγχος δικαιώματος για απόσπαση σε ελληνόγλωσσες εκπαιδευτικές μονάδες του εξωτερικού.',
    'meta_class' => 'hero-meta',
    'badges' => array('Βασικός Πίνακας: έως 185*', 'Εναλλακτικός: έως 165*', 'Β2: προαπαιτούμενο · 0 μόρια', 'Υ.Α. 83046/Η2/2020', 'Παράρτημα ΙΙΙ 2026-2027 ενσωματωμένο', 'Παράρτημα V · επιμίσθια ενσωματωμένα')
  )); ?>

  <?php calculatorColumnsStart(); ?>
    <?php calculatorMainStart(); ?>

      <?php calculatorCardStart(); ?>
        <h2>1. Βασικός έλεγχος δικαιώματος</h2>
        <p class="subtitle">Τα πεδία ξεκινούν ουδέτερα ώστε το εργαλείο να μη θεωρεί καμία κρίσιμη προϋπόθεση ως δεδομένη.</p>

        <div class="field-grid">
          <div class="field full">
            <label for="specialty">Κλάδος / ειδικότητα
              <small>Οι επιλογές προέρχονται από το Παράρτημα ΙΙΙ της πρόσκλησης 11771/Η2/30-01-2026.</small>
            </label>
            <select id="specialty">
              <option value="">— Επίλεξε ειδικότητα —</option>
<?php
$abroadSpecialties = array('ΠΕ01', 'ΠΕ02', 'ΠΕ03', 'ΠΕ04.01', 'ΠΕ04.02', 'ΠΕ04.03', 'ΠΕ04.04', 'ΠΕ04.05', 'ΠΕ05', 'ΠΕ06', 'ΠΕ07', 'ΠΕ08', 'ΠΕ11', 'ΠΕ60', 'ΠΕ70', 'ΠΕ78', 'ΠΕ79.01', 'ΠΕ80', 'ΠΕ82', 'ΠΕ83', 'ΠΕ85', 'ΠΕ86', 'ΠΕ88.04');
foreach ($abroadSpecialties as $internalCode) {
    echo '              <option value="' . htmlspecialchars($internalCode, ENT_QUOTES, 'UTF-8') . '">'
        . htmlspecialchars(teacherSpecialtyDisplay($internalCode), ENT_QUOTES, 'UTF-8')
        . "</option>\n";
}
?>            </select>
            <div id="specialtyAvailability" class="info hidden"></div>
          </div>

          <div class="field">
            <label for="preference1">1η προτίμηση χώρας / περιοχής
              <small>Εμφανίζονται μόνο οι επιλογές όπου προβλέπεται η ειδικότητά σου.</small>
            </label>
            <select id="preference1" disabled>
              <option value="">— Επίλεξε πρώτα ειδικότητα —</option>
            </select>
          </div>

          <div class="field">
            <label for="preference2">2η προτίμηση <small>Προαιρετική.</small></label>
            <select id="preference2" disabled>
              <option value="">— Επίλεξε πρώτα ειδικότητα —</option>
            </select>
          </div>

          <div class="field">
            <label for="preference3">3η προτίμηση <small>Προαιρετική.</small></label>
            <select id="preference3" disabled>
              <option value="">— Επίλεξε πρώτα ειδικότητα —</option>
            </select>
          </div>

          <div class="field">
            <label>Κανόνας προτιμήσεων
              <small>Μπορούν να δηλωθούν έως 3 προτιμήσεις. Για τη Γερμανία, Ντίσελντορφ και Μόναχο αποτελούν ξεχωριστές προτιμήσεις.</small>
            </label>
            <div class="note edu-mt-0">Ο έλεγχος του Παραρτήματος ΙΙΙ γίνεται πλέον αυτόματα — δεν χρειάζεται να δηλώσεις εσύ αν η ειδικότητα είναι αποδεκτή.</div>
          </div>

          <div class="field">
            <label for="educationYears">Συνολικά έτη εκπαιδευτικής υπηρεσίας
              <small>Απαιτούνται τουλάχιστον 5 έτη.</small>
            </label>
            <input type="number" id="educationYears" min="0" max="50" step="1" inputmode="numeric" value="" placeholder="π.χ. 12">
          </div>

          <div class="field">
            <label for="teachingYears">Έτη διδακτικής υπηρεσίας μετά το ΦΕΚ διορισμού
              <small>Απαιτούνται τουλάχιστον 3 έτη σε σχολεία Πρωτοβάθμιας ή Δευτεροβάθμιας Εκπαίδευσης.</small>
            </label>
            <input type="number" id="teachingYears" min="0" max="50" step="1" inputmode="numeric" value="" placeholder="π.χ. 8">
          </div>

          <div class="field">
            <label for="blockingIssue">Κώλυμα απόσπασης
              <small>Υπάρχει κάποιο από τα κωλύματα της τρέχουσας πρόσκλησης;</small>
            </label>
            <select id="blockingIssue">
              <option value="">— Επίλεξε —</option>
              <option value="no">Όχι</option>
              <option value="yes">Ναι / πιθανόν</option>
            </select>
          </div>

          <div class="field">
            <label for="tableType">Αξιολογικός πίνακας</label>
            <select id="tableType">
              <option value="">— Επίλεξε —</option>
              <option value="main">Βασικός Πίνακας — γλώσσα χώρας υποδοχής</option>
              <option value="alternative">Εναλλακτικός Πίνακας — Αγγλικά / Γαλλικά / Γερμανικά</option>
            </select>
          </div>

          <div class="field">
            <label for="bilingualPosition">Η θέση απαιτεί διδασκαλία σε δύο γλώσσες;
              <small>Σε αυτή την περίπτωση απαιτείται τουλάχιστον Γ1/C1 στη γλώσσα της χώρας υποδοχής.</small>
            </label>
            <select id="bilingualPosition">
              <option value="">— Επίλεξε —</option>
              <option value="no">Όχι</option>
              <option value="yes">Ναι</option>
            </select>
          </div>
        </div>

        <div id="preferenceNotes"></div>

        <div id="stipendComparison" class="stipend-panel hidden" aria-live="polite"></div>

        <?php calculatorDisclosureStart(array(
          'summary' => 'Τι θεωρείται κώλυμα στην τρέχουσα πρόσκληση;',
          'open' => true,
          'attrs' => array('data-mobile-collapsed' => 'true')
        )); ?>
          <ul class="criteria-list">
            <li>Προηγούμενη διακοπή απόσπασης για πλημμελή άσκηση καθηκόντων, ανεπάρκεια ή αδικαιολόγητες/μακρές απουσίες.</li>
            <li>Ορισμένες ποινικές ή πειθαρχικές περιπτώσεις ή εκκρεμής Ε.Δ.Ε.</li>
            <li>Ολοκλήρωση του προβλεπόμενου χρόνου στα Ευρωπαϊκά Σχολεία.</li>
            <li>Οικειοθελής διακοπή ή ανάκληση απόσπασης όταν δεν έχει παρέλθει η προβλεπόμενη διετία.</li>
            <li>Υπηρεσία με απόσπαση στο εξωτερικό κατά το έτος που ορίζει η πρόσκληση ή συμπλήρωση τριετίας με επιμίσθιο.</li>
            <li>Ειδικοί περιορισμοί για επιχορηγούμενα σχολεία της Βαυαρίας.</li>
          </ul>
          <div class="note">Ο παραπάνω κατάλογος είναι συνοπτικός. Για οριακή περίπτωση απαιτείται έλεγχος του πλήρους κειμένου της εκάστοτε πρόσκλησης.</div>
        <?php calculatorDisclosureEnd(); ?>
      <?php calculatorCardEnd(); ?>

      <?php calculatorCardStart(); ?>
        <h2>2. Τίτλοι σπουδών</h2>
        <p class="subtitle">Οι τίτλοι μοριοδοτούνται αθροιστικά σύμφωνα με τον πίνακα του άρθρου 3.</p>

        <div class="check">
          <input type="checkbox" id="phd">
          <label for="phd">Διδακτορική διατριβή / διδακτορικό δίπλωμα <small>50 μόρια</small></label>
        </div>
        <div class="check">
          <input type="checkbox" id="master">
          <label for="master">Μεταπτυχιακός τίτλος σπουδών <small>25 μόρια</small></label>
        </div>
        <div class="check">
          <input type="checkbox" id="secondMaster">
          <label for="secondMaster">Δεύτερο μεταπτυχιακό <small>15 μόρια</small></label>
        </div>
        <div class="check">
          <input type="checkbox" id="secondDegree">
          <label for="secondDegree">Δεύτερο πτυχίο Α.Ε.Ι. <small>15 μόρια</small></label>
        </div>
      <?php calculatorCardEnd(); ?>

      <?php calculatorCardStart(); ?>
        <h2>3. Γλωσσομάθεια αξιολογικού πίνακα</h2>
        <p class="subtitle">Το Β2 είναι το ελάχιστο επίπεδο συμμετοχής και δεν μοριοδοτείται.</p>

        <div id="mainLanguageWrap" class="field">
          <label for="primaryLevel" id="primaryLevelLabel">Επίπεδο γλώσσας χώρας υποδοχής</label>
          <select id="primaryLevel">
            <option value="">— Επίλεξε —</option>
            <option value="b2">Β2 — Καλή γνώση · 0 μόρια</option>
            <option value="c1">Γ1 / C1 — Πολύ καλή γνώση</option>
            <option value="c2">Γ2 / C2 — Άριστη γνώση</option>
          </select>
        </div>

        <div id="alternativeFields" class="field-grid hidden edu-mt-13">
          <div class="field">
            <label for="alternativeLanguage">Εναλλακτική γλώσσα</label>
            <select id="alternativeLanguage">
              <option value="">— Επίλεξε —</option>
              <option value="english">Αγγλικά</option>
              <option value="french">Γαλλικά</option>
              <option value="german">Γερμανικά</option>
            </select>
          </div>
          <div class="field">
            <label for="alternativeDifferentFromCountry">Η παραπάνω γλώσσα είναι διαφορετική από τη γλώσσα της χώρας απόσπασης;</label>
            <select id="alternativeDifferentFromCountry">
              <option value="">— Επίλεξε —</option>
              <option value="yes">Ναι</option>
              <option value="no">Όχι</option>
            </select>
          </div>
        </div>

        <div id="hostBilingualWrap" class="field hidden edu-mt-13">
          <label for="hostBilingualLevel">Γλώσσα χώρας υποδοχής για δίγλωσση διδασκαλία
            <small>Στον Εναλλακτικό Πίνακα, αν η θέση απαιτεί διδασκαλία σε δύο γλώσσες, δηλώνεται ξεχωριστά το επίπεδο της γλώσσας της χώρας.</small>
          </label>
          <select id="hostBilingualLevel">
            <option value="">— Επίλεξε —</option>
            <option value="b2">Β2</option>
            <option value="c1">Γ1 / C1</option>
            <option value="c2">Γ2 / C2</option>
          </select>
        </div>

        <div id="primaryLanguageHelp" class="note">Επίλεξε πρώτα Βασικό ή Εναλλακτικό Πίνακα.</div>
      <?php calculatorCardEnd(); ?>

      <?php calculatorCardStart(); ?>
        <h2>4. Δεύτερη ξένη γλώσσα</h2>
        <p class="subtitle">Πρόκειται για πρόσθετη, διαφορετική ξένη γλώσσα από εκείνη που χρησιμοποιείται για την κατάταξη στον πίνακα.</p>

        <div class="field-grid">
          <div class="field">
            <label for="secondLanguageLevel">Επίπεδο δεύτερης ξένης γλώσσας</label>
            <select id="secondLanguageLevel">
              <option value="none">Δεν δηλώνω δεύτερη ξένη γλώσσα</option>
              <option value="b2">Β2 — 10 μόρια</option>
              <option value="c1">Γ1 / C1 — 20 μόρια</option>
              <option value="c2">Γ2 / C2 — 30 μόρια</option>
            </select>
          </div>

          <div class="field" id="secondLanguageDistinctWrap">
            <label for="secondLanguageDistinct">Είναι διαφορετική γλώσσα από εκείνη του αξιολογικού πίνακα;</label>
            <select id="secondLanguageDistinct">
              <option value="">— Επίλεξε —</option>
              <option value="yes">Ναι</option>
              <option value="no">Όχι</option>
            </select>
          </div>
        </div>
      <?php calculatorCardEnd(); ?>

      <?php calculatorCardStart(); ?>
        <h2>5. Κριτήρια ισοβαθμίας</h2>
        <p class="subtitle">Δεν προσθέτουν μόρια. Εφαρμόζονται με την ακόλουθη σειρά όταν υπάρχει ισοβαθμία στον ίδιο πίνακα.</p>
        <?php calculatorDisclosureStart(array(
          'summary' => 'Σειρά κριτηρίων ισοβαθμίας',
          'open' => true,
          'attrs' => array('data-mobile-collapsed' => 'true')
        )); ?>
          <ol class="criteria-list">
            <li>Σειρά προτίμησης της συγκεκριμένης χώρας.</li>
            <li>Επίπεδο γλωσσομάθειας.</li>
            <li>Χηρεία με παιδιά.</li>
            <li>Μονογονεϊκή οικογένεια.</li>
            <li>Διάζευξη με παιδιά υπό την επιμέλεια του/της εκπαιδευτικού.</li>
            <li>Αριθμός τέκνων.</li>
            <li>Γνώση τρίτης ξένης γλώσσας: Γ2, έπειτα Γ1, έπειτα Β2.</li>
            <li>Συνολική υπηρεσία από το ΦΕΚ διορισμού.</li>
          </ol>
          <div class="note">Αν εξακολουθεί η ισοβαθμία, λαμβάνονται υπόψη η ημερομηνία και η σειρά δημοσίευσης του διορισμού στο ΦΕΚ.</div>
        <?php calculatorDisclosureEnd(); ?>
      <?php calculatorCardEnd(); ?>

    <?php calculatorMainEnd(); ?>

    <?php calculatorResultsStart(array('attrs' => array('aria-live' => 'polite'))); ?>
      <h2>Αποτέλεσμα</h2>

      <?php calculatorScoreHeader(array(
        'variant' => 'staged',
        'class' => 'big-total',
        'value_id' => 'grandTotal',
        'value_html' => '0',
        'value_class' => 'number',
        'cap_id' => 'totalOutOf',
        'cap_html' => 'Επίλεξε αξιολογικό πίνακα',
        'cap_class' => 'outof'
      )); ?>

      <div class="bar"><div id="scoreBar"></div></div>

      <?php calculatorResultRow(array('label_html' => 'Τίτλοι σπουδών', 'value_html' => '0', 'value_id' => 'academicResult')); ?>
      <?php calculatorResultRow(array('label_html' => 'Γλώσσα πίνακα', 'label_id' => 'primaryResultLabel', 'value_html' => '0', 'value_id' => 'primaryResult')); ?>
      <?php calculatorResultRow(array('label_html' => 'Δεύτερη ξένη γλώσσα', 'value_html' => '0', 'value_id' => 'secondLanguageResult')); ?>
      <?php calculatorResultRow(array('label_html' => 'Πίνακας', 'value_html' => '—', 'value_id' => 'tableResult')); ?>

      <div id="eligibilityStatus" role="status" aria-live="polite"></div>

      <?php calculatorActions(array(array('attrs' => array('type' => 'button', 'class' => 'secondary', 'id' => 'copyBtn'), 'html' => 'Αντιγραφή'), array('attrs' => array('type' => 'button', 'class' => 'secondary', 'id' => 'resetBtn'), 'html' => 'Καθαρισμός'))); ?>

      <div class="note">
        *Τα 185/165 είναι το θεωρητικό άθροισμα των επιμέρους μοριοδοτούμενων κριτηρίων του ΦΕΚ και όχι ξεχωριστό συνολικό πλαφόν που αναγράφεται στην απόφαση.
      </div>
    <?php calculatorResultsEnd(); ?>
  <?php calculatorColumnsEnd(); ?>

  <?php sourceCardStart(); ?>
    <p><strong>Πηγή:</strong> Υ.Α. 83046/Η2/30-06-2020, ΦΕΚ Β΄ 2687/01.07.2020, ιδίως άρθρα 2–3. Η πρόσκληση 11771/Η2/30-01-2026 για το σχολικό έτος 2026-2027 και το 2027 Νοτίου Ημισφαιρίου εξακολουθεί να χρησιμοποιεί την παραπάνω Υ.Α. για την κατάρτιση των πινάκων και τη μοριοδότηση. Ο πίνακας χωρών/ειδικοτήτων έχει ενσωματωθεί από το <strong>Παράρτημα ΙΙΙ</strong> και τα μηνιαία επιμίσθια από το <strong>Παράρτημα V</strong> της πρόσκλησης 11771/Η2/30-01-2026 (ΑΔΑ: 9Η5Ο46ΝΚΠΔ-Λ91).</p>
    <p>Στο Παράρτημα V τα ποσά αναγράφονται σε ευρώ και σημειώνεται ότι το επιμίσθιο είναι <strong>αφορολόγητο από 01-01-2012</strong> (ν. 4038/2012). Ο ισχύων Κώδικας Φορολογίας Εισοδήματος εξαιρεί επίσης το επίδομα αλλοδαπής των λοιπών δημόσιων πολιτικών υπηρεσιών από το εισόδημα μισθωτής εργασίας (άρθρο 14 παρ. 1γ ν. 4172/2013).</p>
    <p>Ως προς τις ειδικές κρατήσεις, η παλαιά εισφορά <strong>1% υπέρ ΤΠΔΥ έχει καταργηθεί</strong> με το άρθρο 84 ν. 4997/2022, ενώ η ειδική εισφορά αλληλεγγύης για την καταπολέμηση της ανεργίας <strong>2%</strong> της παρ. 2α του άρθρου 38 ν. 3986/2011 εξακολουθεί να ισχύει. Επειδή το Παράρτημα V της πρόσκλησης 2026 αναφέρει το αφορολόγητο αλλά δεν επαναλαμβάνει αναλυτικό πίνακα κρατήσεων, το εργαλείο εμφανίζει το 98% ως <strong>ενδεικτικό ποσό μετά τη νόμιμη κράτηση 2%</strong> και όχι ως δεσμευτική εκκαθάριση μισθοδοσίας.</p>
    <p>Το «ενδεικτικό 12μηνο» είναι απλός πολλαπλασιασμός του μηνιαίου ποσού × 12. Τυχόν φορολογικές υποχρεώσεις στη χώρα υποδοχής, τραπεζικά έξοδα ή άλλες προσωπικές/τοπικές επιβαρύνσεις δεν περιλαμβάνονται.</p>
    <?php sourceCardLinksStart(); ?><?php sourceCardLink('https://www.minedu.gov.gr/news/63949-30-01-26-prosklisi-ekdilosis-endiaferontos-ekpaideftikon-gia-apospasi-sto-eksoteriko-apo-to-sxoliko-etos-2026-2027-kai-apo-to-etos-2027-notiou-imisfairiou', 'Επίσημη ανακοίνωση ΥΠΑΙΘΑ'); ?><?php sourceCardLink('https://www.minedu.gov.gr/publications/docs2026/9%CE%975%CE%9F46%CE%9D%CE%9A%CE%A0%CE%94-%CE%9B91_%CE%A0%CE%A1%CE%9F%CE%A3%CE%9A%CE%9B%CE%97%CE%A3%CE%97_%CE%95%CE%9D%CE%94%CE%99%CE%91%CE%A6%CE%95%CE%A1%CE%9F%CE%9D%CE%A4%CE%9F%CE%A3_%CE%93%CE%99%CE%91_%CE%A3%CE%A7_%CE%95%CE%A4%CE%9F%CE%A3_2026-2027-%CE%9D.%CE%97_2027.pdf', 'Πρόσκληση — Παράρτημα V (PDF)'); ?><?php sourceCardLink('https://elib.aade.gr/elib/printview?d=%2Fgr%2Fact%2F2013%2F4172%2Fmain%2Fsec%2F1%2Fpart%2F6%2Fart%2F67%2F', 'ΚΦΕ — άρθρο 14 παρ. 1γ'); ?><?php sourceCardLink('https://www.efka.gov.gr/el/menoy/sychnes-eroteseis/asphalisi-eisphores/asphalismenoi/misthotoi-demosioy-tomea/epistrophe-achreostetos-katabletheison-eisphoron/epistrophe-tes-eidikes-eisphoras-1-yper-tpdy', 'e-ΕΦΚΑ — κατάργηση 1% ΤΠΔΥ'); ?><?php sourceCardLink('https://www.e-nomothesia.gr/kat-oikonomia/n-3986-2011.html', 'ν. 3986/2011 — άρθρο 38'); ?><?php sourceCardLinksEnd(); ?>
    <?php sourceCardDisclaimerStart(); ?>Το εργαλείο αφορά τις ελληνόγλωσσες εκπαιδευτικές μονάδες εξωτερικού και δεν υποκαθιστά τον έλεγχο της πρόσκλησης, της μισθοδοτικής εκκαθάρισης και των ειδικών τοπικών προϋποθέσεων.<?php sourceCardDisclaimerEnd(); ?>
  <?php sourceCardEnd(); ?>

  <?php require_once __DIR__ . '/includes/footer.php'; ?>
</main>

<script src="<?php echo htmlspecialchars(edu_asset_url('includes/abroad-calculations.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
<script src="<?php echo htmlspecialchars(edu_asset_url('includes/abroad-ui.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
  <script src="<?php echo htmlspecialchars(edu_asset_url('assets/common.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
</body>
</html>
