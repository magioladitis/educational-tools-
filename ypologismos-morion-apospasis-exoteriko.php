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
$abroadSpecialties = array('PE01', 'PE02', 'PE03', 'PE04.01', 'PE04.02', 'PE04.03', 'PE04.04', 'PE04.05', 'PE05', 'PE06', 'PE07', 'PE08', 'PE11', 'PE60', 'PE70', 'PE78', 'PE79.01', 'PE80', 'PE82', 'PE83', 'PE85', 'PE86', 'PE88.04');
foreach ($abroadSpecialties as $internalCode) {
    echo '              <option value="' . htmlspecialchars($internalCode, ENT_QUOTES, 'UTF-8') . '">'
        . htmlspecialchars(teacherSpecialtyDisplayFromInternal($internalCode), ENT_QUOTES, 'UTF-8')
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

        <details>
          <summary>Τι θεωρείται κώλυμα στην τρέχουσα πρόσκληση;</summary>
          <ul class="criteria-list">
            <li>Προηγούμενη διακοπή απόσπασης για πλημμελή άσκηση καθηκόντων, ανεπάρκεια ή αδικαιολόγητες/μακρές απουσίες.</li>
            <li>Ορισμένες ποινικές ή πειθαρχικές περιπτώσεις ή εκκρεμής Ε.Δ.Ε.</li>
            <li>Ολοκλήρωση του προβλεπόμενου χρόνου στα Ευρωπαϊκά Σχολεία.</li>
            <li>Οικειοθελής διακοπή ή ανάκληση απόσπασης όταν δεν έχει παρέλθει η προβλεπόμενη διετία.</li>
            <li>Υπηρεσία με απόσπαση στο εξωτερικό κατά το έτος που ορίζει η πρόσκληση ή συμπλήρωση τριετίας με επιμίσθιο.</li>
            <li>Ειδικοί περιορισμοί για επιχορηγούμενα σχολεία της Βαυαρίας.</li>
          </ul>
          <div class="note">Ο παραπάνω κατάλογος είναι συνοπτικός. Για οριακή περίπτωση απαιτείται έλεγχος του πλήρους κειμένου της εκάστοτε πρόσκλησης.</div>
        </details>
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

      <?php calculatorActions(array(array('attrs' => array('type' => 'button', 'class' => 'secondary', 'id' => 'copyBtn'), 'html' => 'Αντιγραφή'), array('attrs' => array('type' => 'button', 'class' => 'secondary', 'id' => 'resetBtn'), 'html' => 'Μηδενισμός'))); ?>

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
<script>
(function(){
  "use strict";
  const $ = id => document.getElementById(id);
  const fmt = n => Number(n || 0).toLocaleString('el-GR', {maximumFractionDigits: 1});

  const SPECIALTY_LABELS = Object.freeze(<?php
$abroadSpecialtyLabels = array();
foreach ($abroadSpecialties as $internalCode) {
    $abroadSpecialtyLabels[$internalCode] = teacherSpecialtyDisplayFromInternal($internalCode);
}
echo json_encode($abroadSpecialtyLabels, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
?>);

  // Παράρτημα ΙΙΙ — Πίνακας χωρών/ειδικοτήτων, πρόσκληση 11771/Η2/30-01-2026.
  const DESTINATIONS = Object.freeze([
    // Ασία
    {id:"az", name:"Αζερμπαϊτζάν", continent:"Ασία", specs:["PE70","PE02"]},
    {id:"am", name:"Αρμενία", continent:"Ασία", specs:["PE70","PE02"]},
    {id:"ge", name:"Γεωργία", continent:"Ασία", specs:["PE70","PE02","PE11","PE79.01"]},
    {id:"kz", name:"Καζακστάν", continent:"Ασία", specs:["PE70","PE02"]},
    {id:"uae", name:"Η.Α.Ε.", continent:"Ασία", specs:["PE70","PE02"]},
    {id:"jo", name:"Ιορδανία", continent:"Ασία", specs:["PE70","PE02"]},
    {id:"il", name:"Ισραήλ", continent:"Ασία", specs:["PE70","PE02","PE03","PE04.01","PE04.02","PE04.03","PE04.04","PE04.05","PE06","PE86"]},
    {id:"qa", name:"Κατάρ", continent:"Ασία", specs:["PE60","PE70","PE02"]},
    {id:"uz", name:"Ουζμπεκιστάν", continent:"Ασία", specs:["PE70"]},
    {id:"tr", name:"Τουρκία", continent:"Ασία", specs:["PE02","PE03","PE04.01","PE04.02","PE04.04","PE06","PE11","PE79.01","PE60","PE70","PE86"]},

    // Αφρική
    {id:"eg", name:"Αίγυπτος", continent:"Αφρική", specs:["PE01","PE02","PE03","PE04.01","PE04.02","PE04.03","PE04.04","PE06","PE11","PE60","PE70","PE78","PE79.01","PE86"]},
    {id:"et", name:"Αιθιοπία", continent:"Αφρική", specs:["PE70"]},
    {id:"zm", name:"Ζάμπια", continent:"Αφρική", specs:["PE70"]},
    {id:"zw", name:"Ζιμπάμπουε", continent:"Αφρική", specs:["PE70"]},
    {id:"cd", name:"Λ. Δ. Κονγκό", continent:"Αφρική", specs:["PE60","PE70","PE02","PE03","PE04.01","PE04.02","PE86","PE06","PE05","PE11","PE80"]},
    {id:"mg", name:"Μαδαγασκάρη", continent:"Αφρική", specs:["PE70","PE02"]},
    {id:"za", name:"Νότια Αφρική", continent:"Αφρική", specs:["PE60","PE70","PE02","PE79.01","PE06","PE86","PE11"]},
    {id:"tn", name:"Τυνησία", continent:"Αφρική", specs:["PE70","PE02"]},

    // Ωκεανία
    {id:"au", name:"Αυστραλία", continent:"Ωκεανία", specs:["PE02","PE06","PE60","PE70"]},
    {id:"nz", name:"Νέα Ζηλανδία", continent:"Ωκεανία", specs:["PE70"]},

    // Ευρώπη
    {id:"al", name:"Αλβανία", continent:"Ευρώπη", specs:["PE60","PE70","PE02","PE04.04","PE08","PE11","PE79.01","PE83","PE86","PE06"]},
    {id:"at", name:"Αυστρία", continent:"Ευρώπη", specs:["PE60","PE70","PE02"]},
    {id:"be", name:"Βέλγιο", continent:"Ευρώπη", specs:["PE60","PE70","PE01","PE02","PE03","PE04.01","PE04.02","PE04.04","PE05","PE06","PE08","PE11","PE78","PE79.01","PE80","PE86"]},
    {id:"bg", name:"Βουλγαρία", continent:"Ευρώπη", specs:["PE60","PE70","PE02","PE11"]},
    {id:"fr", name:"Γαλλία", continent:"Ευρώπη", specs:["PE60","PE70","PE02"]},
    {id:"de_du", name:"Γερμανία — Σ.Γ.Ε. Ντίσελντορφ", continent:"Ευρώπη", specs:["PE01","PE02","PE03","PE04.01","PE04.02","PE04.04","PE04.05","PE06","PE07","PE11","PE79.01","PE78","PE80","PE86","PE60","PE70"]},
    {id:"de_mu", name:"Γερμανία — Σ.Γ.Ε. Μονάχου", continent:"Ευρώπη", specs:["PE01","PE02","PE03","PE04.01","PE04.02","PE04.04","PE06","PE07","PE08","PE11","PE60","PE70","PE78","PE79.01","PE80","PE82","PE85","PE86","PE88.04"]},
    {id:"dk", name:"Δανία", continent:"Ευρώπη", specs:["PE70","PE02"]},
    {id:"ch", name:"Ελβετία", continent:"Ευρώπη", specs:["PE60","PE70","PE02"]},
    {id:"ie", name:"Ιρλανδία", continent:"Ευρώπη", specs:["PE70","PE02"]},
    {id:"es", name:"Ισπανία", continent:"Ευρώπη", specs:["PE60","PE70","PE02"]},
    {id:"it", name:"Ιταλία", continent:"Ευρώπη", specs:["PE70","PE02"]},
    {id:"hr", name:"Κροατία", continent:"Ευρώπη", specs:["PE02"]},
    {id:"lt", name:"Λιθουανία", continent:"Ευρώπη", specs:["PE70","PE02"]},
    {id:"lu", name:"Λουξεμβούργο", continent:"Ευρώπη", specs:["PE70","PE02"]},
    {id:"mt", name:"Μάλτα", continent:"Ευρώπη", specs:["PE70"]},
    {id:"me", name:"Μαυροβούνιο", continent:"Ευρώπη", specs:["PE70","PE02"]},
    {id:"md", name:"Μολδαβία", continent:"Ευρώπη", specs:["PE70","PE02"]},
    {id:"nl", name:"Ολλανδία", continent:"Ευρώπη", specs:["PE60","PE70","PE02"]},
    {id:"hu", name:"Ουγγαρία", continent:"Ευρώπη", specs:["PE60","PE70","PE02","PE11"]},
    {id:"pl", name:"Πολωνία", continent:"Ευρώπη", specs:["PE70"]},
    {id:"pt", name:"Πορτογαλία", continent:"Ευρώπη", specs:["PE70","PE02"]},
    {id:"ro", name:"Ρουμανία", continent:"Ευρώπη", specs:["PE70","PE60","PE01","PE02","PE03","PE04.01","PE04.02","PE04.04","PE06","PE11","PE79.01","PE80","PE86"]},
    {id:"rs", name:"Σερβία", continent:"Ευρώπη", specs:["PE70","PE60","PE02","PE11"]},
    {id:"sk", name:"Σλοβακία", continent:"Ευρώπη", specs:["PE70","PE02"]},
    {id:"si", name:"Σλοβενία", continent:"Ευρώπη", specs:["PE70","PE02"]},
    {id:"se", name:"Σουηδία", continent:"Ευρώπη", specs:["PE70","PE02"]},
    {id:"no", name:"Νορβηγία", continent:"Ευρώπη", specs:["PE70","PE02"]},

    // Αμερική
    {id:"ar", name:"Αργεντινή", continent:"Αμερική", specs:["PE02","PE60","PE70"]},
    {id:"ve", name:"Βενεζουέλα", continent:"Αμερική", specs:["PE70","PE02"]},
    {id:"br", name:"Βραζιλία", continent:"Αμερική", specs:["PE70","PE02"]},
    {id:"us", name:"Η.Π.Α.", continent:"Αμερική", specs:["PE01","PE02","PE03","PE06","PE08","PE11","PE60","PE70","PE79.01"]},
    {id:"ca", name:"Καναδάς", continent:"Αμερική", specs:["PE60","PE70","PE02"]},
    {id:"mx", name:"Μεξικό", continent:"Αμερική", specs:["PE70"]},
    {id:"uy", name:"Ουρουγουάη", continent:"Αμερική", specs:["PE70","PE02","PE11"]},
    {id:"pa", name:"Παναμάς", continent:"Αμερική", specs:["PE70"]},
    {id:"pe", name:"Περού", continent:"Αμερική", specs:["PE70"]},
    {id:"cl", name:"Χιλή", continent:"Αμερική", specs:["PE70","PE02"]}
  ]);

  // Παράρτημα V — μηνιαίο επιμίσθιο εκπαιδευτικών σε ευρώ.
  // Για χώρες Κ.Α.Κ. εφαρμόζεται το κοινό ποσό 1.425 €, ενώ Σερβία/Μαυροβούνιο το κοινό ποσό 1.062 €.
  const STIPEND_BY_DESTINATION_ID = Object.freeze({
    az:1425, am:1425, ge:1425, kz:1425, uae:1237, jo:697, il:946, qa:1166, uz:1425, tr:1328,
    eg:971, et:926, zm:855, zw:855, cd:971, mg:914, za:914, tn:887,
    au:1230, nz:1230,
    al:784, at:1438, be:1224, bg:822, fr:1431, de_du:1334, de_mu:1334, dk:1230, ch:2350, ie:1334, es:822,
    it:939, hr:1062, lt:790, lu:1205, mt:939, me:1062, md:1425, nl:1224, hu:1192, pl:1192, pt:822, ro:1192,
    rs:1062, sk:1166, si:1017, se:950, no:960,
    ar:1295, ve:1004, br:817, us:1943, ca:1220, mx:1580, uy:1290, pa:1580, pe:900, cl:1000
  });

  const DESTINATION_BY_ID = Object.freeze(Object.fromEntries(DESTINATIONS.map(d => [d.id, d])));
  const CONTINENT_ORDER = ["Ασία","Αφρική","Ωκεανία","Ευρώπη","Αμερική"];

  const allIds = [
    'specialty','preference1','preference2','preference3','educationYears','teachingYears','blockingIssue',
    'tableType','bilingualPosition','phd','master','secondMaster','secondDegree',
    'primaryLevel','alternativeLanguage','alternativeDifferentFromCountry',
    'hostBilingualLevel','secondLanguageLevel','secondLanguageDistinct'
  ];

  function normalizeYears(id){
    const el = $(id);
    if(!el || el.value === '') return;
    el.value = String(Math.min(50, Math.max(0, Math.floor(Number(el.value) || 0))));
  }

  function eligibleDestinations(specialty){
    if(!specialty) return [];
    return DESTINATIONS.filter(d => d.specs.includes(specialty));
  }

  function destinationName(id){
    return DESTINATION_BY_ID[id]?.name || "";
  }

  function stipendFor(id){
    return Number(STIPEND_BY_DESTINATION_ID[id] || 0);
  }

  function euro(value){
    return Number(value || 0).toLocaleString('el-GR', {style:'currency', currency:'EUR', maximumFractionDigits:0});
  }

  function euroCents(value){
    return Number(value || 0).toLocaleString('el-GR', {style:'currency', currency:'EUR', minimumFractionDigits:2, maximumFractionDigits:2});
  }

  const CURRENT_STIPEND_DEDUCTION_RATE = 0.02;

  function updateStipendComparison(){
    const preferenceIds = [$('preference1').value, $('preference2').value, $('preference3').value];
    const selected = preferenceIds
      .map((id, index) => id ? {id, index:index+1, name:destinationName(id), stipend:stipendFor(id)} : null)
      .filter(Boolean);
    const box = $('stipendComparison');

    if(!selected.length){
      box.classList.add('hidden');
      box.innerHTML = '';
      return;
    }

    const rows = selected.map(item => {
      const annual = item.stipend * 12;
      return `<tr><td><strong>${item.index}η</strong></td><td>${item.name}</td><td class="amount">${euro(item.stipend)} / μήνα</td><td class="amount">${euro(annual)}</td></tr>`;
    }).join('');

    let highlight = '';
    if(selected.length > 1){
      const highest = selected.reduce((best, item) => item.stipend > best.stipend ? item : best, selected[0]);
      highlight = `<div class="stipend-highlight"><strong>Μεγαλύτερο ονομαστικό επιμίσθιο από τις επιλογές σου:</strong> ${highest.name} — ${euro(highest.stipend)} / μήνα.</div>`;
    }

    const estimatedNetRows = selected.map(item => {
      const deduction = item.stipend * CURRENT_STIPEND_DEDUCTION_RATE;
      const afterDeduction = item.stipend - deduction;
      return `<li><strong>${item.name}:</strong> ${euroCents(item.stipend)} − ${euroCents(deduction)} (2%) = <strong>${euroCents(afterDeduction)} / μήνα</strong></li>`;
    }).join('');

    const taxInfo = `
      <div class="stipend-tax-info">
        <strong class="title">🧾 Φορολογία &amp; ειδικές κρατήσεις του επιμισθίου</strong>
        <div class="stipend-tax-grid">
          <span>Φόρος εισοδήματος στην Ελλάδα</span><b>0% — αφορολόγητο</b>
          <span>Ειδική εισφορά ΤΠΔΥ</span><b>0% — καταργήθηκε</b>
          <span>Ειδική εισφορά αλληλεγγύης ν. 3986/2011</span><b>2%</b>
        </div>
        <div class="stipend-footnote">Η κράτηση 2% είναι η ειδική εισφορά για την καταπολέμηση της ανεργίας του άρθρου 38 ν. 3986/2011· είναι διαφορετική από την παλαιά φορολογική «εισφορά αλληλεγγύης» του ΚΦΕ.</div>
        <ul class="stipend-net-list">${estimatedNetRows}</ul>
        <div class="stipend-footnote"><strong>Προσοχή:</strong> το ποσό μετά το 2% είναι ενδεικτικό ως προς την ελληνική μισθοδοσία. Η πρόσκληση 2026 επιβεβαιώνει ρητά το αφορολόγητο, αλλά δεν επαναλαμβάνει αναλυτικό πίνακα κρατήσεων. Δεν συνυπολογίζονται τυχόν υποχρεώσεις στη χώρα υποδοχής ή τραπεζικά έξοδα.</div>
      </div>`;

    box.innerHTML = `
      <h3>💶 Επιμίσθια Παραρτήματος V</h3>
      <p class="stipend-intro">Τα ποσά είναι τα μηνιαία επιμίσθια της πρόσκλησης 11771/Η2/30-01-2026. Το 12μηνο είναι ενδεικτικός υπολογισμός ×12.</p>
      <div class="edu-overflow-x-auto">
        <table class="stipend-table">
          <thead><tr><th>Προτίμηση</th><th>Χώρα / περιοχή</th><th>Μηνιαίο</th><th>Ενδεικτικό 12μηνο</th></tr></thead>
          <tbody>${rows}</tbody>
        </table>
      </div>
      ${highlight}
      ${taxInfo}
      <div class="stipend-footnote">Παράρτημα V: το επιμίσθιο είναι αφορολόγητο από 01-01-2012. Η σύγκριση εξακολουθεί να βασίζεται στα ονομαστικά ποσά της πρόσκλησης και δεν συνυπολογίζει κόστος ζωής ή άλλες οικονομικές παραμέτρους.</div>`;
    box.classList.remove('hidden');
  }

  function rebuildPreferenceOptions(){
    const specialty = $('specialty').value;
    const selects = [$('preference1'), $('preference2'), $('preference3')];
    const eligible = eligibleDestinations(specialty);

    if(!specialty){
      selects.forEach(sel => {
        sel.disabled = true;
        sel.innerHTML = '<option value="">— Επίλεξε πρώτα ειδικότητα —</option>';
      });
      $('specialtyAvailability').classList.add('hidden');
      $('specialtyAvailability').innerHTML = '';
      $('preferenceNotes').innerHTML = '';
      updateStipendComparison();
      return;
    }

    // Καθαρισμός παλιών/μη επιτρεπτών ή διπλών επιλογών, με προτεραιότητα 1η → 2η → 3η.
    const allowedIds = new Set(eligible.map(d => d.id));
    const values = selects.map(sel => allowedIds.has(sel.value) ? sel.value : '');
    if(values[1] && values[1] === values[0]) values[1] = '';
    if(values[2] && (values[2] === values[0] || values[2] === values[1])) values[2] = '';

    selects.forEach((sel, index) => {
      const ownValue = values[index];
      const selectedElsewhere = new Set(values.filter((v, i) => i !== index && v));
      sel.disabled = false;
      sel.innerHTML = '';

      const blank = document.createElement('option');
      blank.value = '';
      blank.textContent = index === 0 ? '— Επίλεξε 1η προτίμηση —' : '— Καμία —';
      sel.appendChild(blank);

      CONTINENT_ORDER.forEach(continent => {
        const items = eligible.filter(d => d.continent === continent);
        if(!items.length) return;
        const group = document.createElement('optgroup');
        group.label = continent;
        items.forEach(d => {
          const option = document.createElement('option');
          option.value = d.id;
          const stipend = stipendFor(d.id);
          option.textContent = stipend ? `${d.name} · ${euro(stipend)}/μήνα` : d.name;
          option.disabled = selectedElsewhere.has(d.id) && d.id !== ownValue;
          group.appendChild(option);
        });
        sel.appendChild(group);
      });
      sel.value = ownValue;
    });

    const list = eligible.map(d => `<li>${d.name}${stipendFor(d.id) ? ` — <strong>${euro(stipendFor(d.id))}/μήνα</strong>` : ''}</li>`).join('');
    const specialtyLabel = SPECIALTY_LABELS[specialty] || specialty;
    $('specialtyAvailability').innerHTML =
      `<strong>${specialtyLabel}</strong>: το Παράρτημα ΙΙΙ προβλέπει <strong>${eligible.length}</strong> διαθέσιμες χώρες/περιοχές.` +
      `<details><summary>Προβολή όλων των διαθέσιμων επιλογών</summary><ul class="criteria-list">${list}</ul></details>`;
    $('specialtyAvailability').classList.remove('hidden');

    updatePreferenceNotes();
    updateStipendComparison();
  }

  function updatePreferenceNotes(){
    const specialty = $('specialty').value;
    const selected = [$('preference1').value, $('preference2').value, $('preference3').value].filter(Boolean);
    const notes = [];

    if(selected.includes('de_mu')){
      notes.push('Για τη Γερμανία — Σ.Γ.Ε. Μονάχου ισχύουν ειδικές προϋποθέσεις για τα επιχορηγούμενα σχολεία της Βαυαρίας και προηγούμενη άδεια διδασκαλίας από τη γερμανική υπηρεσία, όπου απαιτείται.');
      if(specialty === 'PE78') notes.push('Στο Σ.Γ.Ε. Μονάχου η ΠΕ78 αφορά ειδικά Κοινωνιολόγους.');
      if(specialty === 'PE80') notes.push('Στο Σ.Γ.Ε. Μονάχου η ΠΕ80 αφορά ειδικά Οικονομολόγους και, για τα γερμανόφωνα μαθήματα, απαιτείται αυξημένη γερμανομάθεια.');
      if(specialty === 'PE82') notes.push('Για ΠΕ82 στη Βαυαρία επισημαίνεται αυξημένη γερμανομάθεια (Γ1) για τη διδασκαλία των μαθημάτων στη γερμανική.');
      if(specialty === 'PE03') notes.push('Στη Βαυαρία τα Μαθηματικά διδάσκονται και στη γερμανική· για διδασκαλία γερμανόφωνων μαθημάτων απαιτείται Γ1.');
      if(specialty === 'PE11') notes.push('Στα Γυμνάσια Μονάχου/Νυρεμβέργης η Φυσική Αγωγή κατανέμεται ανά φύλο μαθητών και οι αποσπάσεις εξαρτώνται από τις αντίστοιχες κενές θέσεις.');
    }

    if(selected.includes('ch')){
      notes.push('Για την Ελβετία απαιτείται τουλάχιστον Β1 στην ομιλούμενη γλώσσα του τόπου εργασίας (προφορικός και γραπτός λόγος), πέρα από τον γενικό έλεγχο του πίνακα.');
    }

    $('preferenceNotes').innerHTML = notes.length
      ? '<div class="warning"><strong>Ειδικές επισημάνσεις για τις προτιμήσεις σου:</strong><ul class="edu-list-compact"><li>' + notes.join('</li><li>') + '</li></ul></div>'
      : '';
  }

  function updateUI(){
    rebuildPreferenceOptions();
    const tableType = $('tableType').value;
    const bilingual = $('bilingualPosition').value;

    $('alternativeFields').classList.toggle('hidden', tableType !== 'alternative');

    if(tableType === 'main'){
      $('primaryLevelLabel').textContent = 'Επίπεδο γλώσσας χώρας υποδοχής';
      $('primaryLanguageHelp').textContent =
        'Βασικός Πίνακας: Β2 = 0 μόρια, Γ1/C1 = 30 μόρια, Γ2/C2 = 50 μόρια.';
    } else if(tableType === 'alternative'){
      $('primaryLevelLabel').textContent = 'Επίπεδο εναλλακτικής γλώσσας (Αγγλικά / Γαλλικά / Γερμανικά)';
      $('primaryLanguageHelp').textContent =
        'Εναλλακτικός Πίνακας: Β2 = 0 μόρια, Γ1/C1 = 20 μόρια, Γ2/C2 = 30 μόρια.';
    } else {
      $('primaryLevelLabel').textContent = 'Επίπεδο γλώσσας αξιολογικού πίνακα';
      $('primaryLanguageHelp').textContent = 'Επίλεξε πρώτα Βασικό ή Εναλλακτικό Πίνακα.';
    }

    $('hostBilingualWrap').classList.toggle(
      'hidden',
      !(tableType === 'alternative' && bilingual === 'yes')
    );

    const secondUsed = $('secondLanguageLevel').value !== 'none';
    $('secondLanguageDistinctWrap').classList.toggle('hidden', !secondUsed);
    if(!secondUsed) $('secondLanguageDistinct').value = '';
  }

  function values(){
    normalizeYears('educationYears');
    normalizeYears('teachingYears');

    return {
      specialty: $('specialty').value,
      specialtySelected: $('specialty').value !== '',
      preference1: $('preference1').value,
      preference2: $('preference2').value,
      preference3: $('preference3').value,
      preferenceSelected: $('preference1').value !== '',
      branchAllowed: ($('specialty').value && $('preference1').value) ? 'yes' : '',
      educationYears: $('educationYears').value,
      educationYearsAnswered: $('educationYears').value !== '',
      teachingYears: $('teachingYears').value,
      teachingYearsAnswered: $('teachingYears').value !== '',
      blockingIssue: $('blockingIssue').value,
      tableType: $('tableType').value,
      bilingualPosition: $('bilingualPosition').value,

      phd: $('phd').checked,
      master: $('master').checked,
      secondMaster: $('secondMaster').checked,
      secondDegree: $('secondDegree').checked,

      primaryLevel: $('primaryLevel').value,
      alternativeLanguage: $('alternativeLanguage').value,
      alternativeDifferentFromCountry: $('alternativeDifferentFromCountry').value,
      hostBilingualLevel: $('hostBilingualLevel').value,

      secondLanguageLevel: $('secondLanguageLevel').value,
      secondLanguageDistinct: $('secondLanguageDistinct').value
    };
  }

  function calculate(){
    updateUI();
    const r = AbroadSecondment.calculate(values());

    $('grandTotal').textContent = fmt(r.total);
    $('academicResult').textContent = fmt(r.academic);
    $('primaryResult').textContent = fmt(r.primaryLanguagePoints);
    $('secondLanguageResult').textContent = fmt(r.secondLanguagePoints);

    if(r.tableType === 'main'){
      $('tableResult').textContent = 'Βασικός';
      $('primaryResultLabel').textContent = 'Γλώσσα χώρας';
      $('totalOutOf').textContent = 'θεωρητικά έως 185';
    } else if(r.tableType === 'alternative'){
      $('tableResult').textContent = 'Εναλλακτικός';
      $('primaryResultLabel').textContent = 'Εναλλακτική γλώσσα';
      $('totalOutOf').textContent = 'θεωρητικά έως 165';
    } else {
      $('tableResult').textContent = '—';
      $('primaryResultLabel').textContent = 'Γλώσσα πίνακα';
      $('totalOutOf').textContent = 'Επίλεξε αξιολογικό πίνακα';
    }

    const pct = r.theoreticalMax ? Math.min(100, (r.total / r.theoreticalMax) * 100) : 0;
    $('scoreBar').style.width = pct + '%';

    const boxes = [];
    if(r.unanswered.length){
      boxes.push(
        '<div class="info"><strong>Χρειάζονται ακόμη στοιχεία:</strong><ul class="edu-list-compact"><li>'
        + r.unanswered.join('</li><li>') + '</li></ul></div>'
      );
    }

    if(r.issues.length){
      boxes.push(
        '<div class="danger"><strong>Ο βασικός έλεγχος δεν είναι θετικός:</strong><ul class="edu-list-compact"><li>'
        + r.issues.join('</li><li>') + '</li></ul></div>'
      );
    } else if(r.eligible){
      boxes.push(
        '<div class="success"><strong>Ο βασικός έλεγχος είναι θετικός.</strong> Με τα δηλωμένα στοιχεία καλύπτονται οι βασικές προϋποθέσεις για τον επιλεγμένο πίνακα. Ο συνδυασμός ειδικότητας και 1ης προτίμησης έχει ήδη ελεγχθεί αυτόματα στο Παράρτημα ΙΙΙ. Απαιτείται πάντως έλεγχος όλων των δικαιολογητικών και των ειδικών τοπικών προϋποθέσεων.</div>'
      );
    }

    if(r.warnings.length){
      boxes.push(
        '<div class="warning"><strong>Παρατηρήσεις:</strong><ul class="edu-list-compact"><li>'
        + r.warnings.join('</li><li>') + '</li></ul></div>'
      );
    }

    $('eligibilityStatus').innerHTML = boxes.join('');
    return r;
  }

  async function copySummary(){
    const r = calculate();
    const table = r.tableType === 'main'
      ? 'Βασικός Πίνακας'
      : (r.tableType === 'alternative' ? 'Εναλλακτικός Πίνακας' : 'Δεν επιλέχθηκε');

    const lines = [
      'Μόρια Απόσπασης στο Εξωτερικό',
      `Πίνακας: ${table}`,
      `Ειδικότητα: ${SPECIALTY_LABELS[$('specialty').value] || 'Δεν επιλέχθηκε'}`,
      `1η προτίμηση: ${destinationName($('preference1').value) || 'Δεν επιλέχθηκε'}`,
      `2η προτίμηση: ${destinationName($('preference2').value) || '—'}`,
      `3η προτίμηση: ${destinationName($('preference3').value) || '—'}`,
      `Επιμίσθιο 1ης: ${$('preference1').value ? euro(stipendFor($('preference1').value)) + ' / μήνα' : '—'}`,
      `Επιμίσθιο 2ης: ${$('preference2').value ? euro(stipendFor($('preference2').value)) + ' / μήνα' : '—'}`,
      `Επιμίσθιο 3ης: ${$('preference3').value ? euro(stipendFor($('preference3').value)) + ' / μήνα' : '—'}`,
      `Τίτλοι σπουδών: ${fmt(r.academic)}`,
      `Γλώσσα πίνακα: ${fmt(r.primaryLanguagePoints)}`,
      `Δεύτερη ξένη γλώσσα: ${fmt(r.secondLanguagePoints)}`,
      `Σύνολο: ${fmt(r.total)}`,
      `Βασικός έλεγχος: ${r.eligible ? 'ΘΕΤΙΚΟΣ' : 'ΜΗ ΟΛΟΚΛΗΡΩΜΕΝΟΣ / ΜΗ ΘΕΤΙΚΟΣ'}`,
      'Ενδεικτικός υπολογισμός βάσει της Υ.Α. 83046/Η2/30-06-2020 (Β΄ 2687).'
    ];

    try{
      await navigator.clipboard.writeText(lines.join('\n'));
      $('copyBtn').textContent = 'Αντιγράφηκε ✓';
      setTimeout(() => $('copyBtn').textContent = 'Αντιγραφή', 1400);
    }catch(e){
      alert(lines.join('\n'));
    }
  }

  function reset(){
    document.querySelectorAll('input[type="checkbox"]').forEach(el => el.checked = false);
    document.querySelectorAll('input[type="number"]').forEach(el => el.value = '');
    document.querySelectorAll('select').forEach(el => el.selectedIndex = 0);
    $('secondLanguageLevel').value = 'none';
    updateUI();
    calculate();
    $('specialty').focus();
  }

  allIds.forEach(id => {
    const el = $(id);
    if(!el) return;
    el.addEventListener('input', calculate);
    el.addEventListener('change', calculate);
  });

  $('copyBtn').addEventListener('click', copySummary);
  $('resetBtn').addEventListener('click', reset);

  updateUI();
  calculate();
})();
</script>
  <script src="<?php echo htmlspecialchars(edu_asset_url('assets/common.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
</body>
</html>
