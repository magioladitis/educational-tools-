<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/teacher-specialties.php';
$paidagogikiSpecialties = array(
    'ΠΕ01', 'ΠΕ02', 'ΠΕ03', 'ΠΕ04', 'ΠΕ05', 'ΠΕ06', 'ΠΕ07', 'ΠΕ08', 'ΠΕ11',
    'ΠΕ33', 'ΠΕ34', 'ΠΕ40', 'ΠΕ41', 'ΠΕ60', 'ΠΕ70', 'ΠΕ73', 'ΠΕ78', 'ΠΕ79.01',
    'ΠΕ79.02', 'ΠΕ80', 'ΠΕ81', 'ΠΕ82', 'ΠΕ83', 'ΠΕ84', 'ΠΕ85', 'ΠΕ86', 'ΠΕ87',
    'ΠΕ88', 'ΠΕ89', 'ΠΕ90', 'ΠΕ91'
);
?>
<!DOCTYPE html>
<html lang="el">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Έχω Παιδαγωγική και Διδακτική Επάρκεια;</title>
<link rel="stylesheet" href="<?php echo htmlspecialchars(edu_asset_url('assets/common.css'), ENT_QUOTES, 'UTF-8'); ?>">
</head>

<body class="edu-ui edu-guide-standard edu-guide-pedagogy">
<?php require_once __DIR__ . '/includes/header.php'; ?>
<?php require_once __DIR__ . '/includes/components/deadline-card.php'; ?>

<div class="app-box edu-modernized">
<section class="hero edu-legacy-hero">
<h1>Έχω Παιδαγωγική και Διδακτική Επάρκεια;</h1>
<p class="intro">
    Το εργαλείο παρέχει <strong>ενδεικτικό</strong> έλεγχο με βάση τις κατηγορίες
    αποδεικτικών της Παιδαγωγικής και Διδακτικής Επάρκειας του Παραρτήματος Ε΄.
  </p>
</section>

<?php
renderDeadlineCard(array(
    'title' => '📅 Αιτήσεις ΕΠΠΑΙΚ ΑΣΠΑΙΤΕ 2026–2027',
    'intro' => 'Η ΑΣΠΑΙΤΕ δέχεται αιτήσεις συμμετοχής στην κλήρωση για το Ετήσιο Πρόγραμμα Παιδαγωγικής Κατάρτισης (ΕΠΠΑΙΚ).',
    'items' => array(array(
        'title' => 'ΕΠΠΑΙΚ 2026–2027',
        'meta_html' => 'Ηλεκτρονικές αιτήσεις έως <strong>Τρίτη 25 Αυγούστου 2026, ώρα 19:00</strong>.',
        'start' => '2026-06-16T12:00:00+03:00',
        'end' => '2026-08-25T19:00:00+03:00',
        'source_url' => 'https://www.aspete.gr/wp-content/uploads/2026/06/6%CE%A7%CE%9B546%CE%A88%CE%A7%CE%99-3%CE%9E%CE%92-%CE%A0%CE%A1%CE%9F%CE%A3%CE%9A%CE%9B%CE%97%CE%A3%CE%97-%CE%95%CE%A0%CE%A0%CE%91%CE%99%CE%9A-2026-2027.pdf',
        'source_label' => 'Επίσημη πρόσκληση ΑΣΠΑΙΤΕ — ΑΔΑ 6ΧΛ546Ψ8ΧΙ-3ΞΒ ↗'
    )),
    'note_html' => 'Η κάρτα ενημερώνει για την <strong>προθεσμία αίτησης στο ΕΠΠΑΙΚ</strong>. Για το αν και με ποιο αποδεικτικό θεμελιώνεται Παιδαγωγική και Διδακτική Επάρκεια σε συγκεκριμένη διαδικασία, εφαρμόζονται οι κανόνες της αντίστοιχης προκήρυξης.'
));
?>

  <div class="question">
    <label for="specialty">Κλάδος / ειδικότητα</label>
    <select id="specialty">
      <option value="">-- Επιλογή --</option>
      <?php foreach ($paidagogikiSpecialties as $code): ?>
        <option value="<?php echo htmlspecialchars($code, ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars(teacherSpecialtyDisplay($code), ENT_QUOTES, 'UTF-8'); ?></option>
      <?php endforeach; ?>
    </select>
  </div>

  <div class="question">
    <label for="proofType">Με ποιο δικαιολογητικό αποδεικνύεις την Παιδαγωγική και Διδακτική Επάρκεια;</label>
    <select id="proofType">
      <option value="">-- Επιλογή --</option>

      <option value="aei_certificate">
        Βεβαίωση Α.Ε.Ι. μετά από ομάδα μαθημάτων ή ειδικό πρόγραμμα σπουδών
      </option>

      <option value="education_msc_phd">
        Μεταπτυχιακός τίτλος ή διδακτορικό δίπλωμα στις επιστήμες της αγωγής
      </option>

      <option value="old_certificate">
        Πιστοποιητικό παιδαγωγικής επάρκειας της παρ. 5 του άρθρου 4 του ν. 3027/2002
      </option>

      <option value="pedagogical_department">
        Πτυχίο Παιδαγωγικού Τμήματος Α.Ε.Ι. ή αντίστοιχου Τμήματος που αναφέρεται στο Παράρτημα Ε΄
      </option>

      <option value="aspaite">
        Πτυχίο Α.Σ.ΠΑΙ.Τ.Ε.
      </option>

      <option value="aspaite_eppaik">
        Πιστοποιητικό ΕΠΠΑΙΚ Α.Σ.ΠΑΙ.Τ.Ε. / πρώην ΠΑΤΕΣ–ΣΕΛΕΤΕ
      </option>

      <option value="article99">
        Πιστοποιητικό Π.Δ.Ε. του άρθρου 99 του ν. 4957/2022
      </option>

      <option value="epath">
        Πτυχίο Ειδικής Παιδαγωγικής Ακαδημίας Θεσσαλονίκης (Ε.Π.Α.Θ.)
      </option>

      <option value="professor_school">
        Πτυχίο καθηγητικής σχολής
      </option>

      <option value="none">
        Δεν διαθέτω κάποιο από τα παραπάνω
      </option>

      <option value="unknown">
        Δεν είμαι σίγουρος/η
      </option>
    </select>
  </div>

  <div id="pedagogicalDepartmentQuestions" class="hidden">
    <div class="question">
      <label for="pedagogicalDepartmentType">Σε ποια κατηγορία Παιδαγωγικού Τμήματος ανήκει ο τίτλος σου;</label>
      <select id="pedagogicalDepartmentType">
        <option value="">-- Επιλογή --</option>
        <option value="pte70">Παιδαγωγικό Τμήμα Δημοτικής Εκπαίδευσης</option>
        <option value="pte60">Τμήμα Εκπαίδευσης και Αγωγής στην Προσχολική Ηλικία</option>
        <option value="fppy">Τμήμα Φιλοσοφίας - Παιδαγωγικής - Ψυχολογίας, εκτός Προγράμματος Ψυχολογίας ΦΠΨ ΕΚΠΑ</option>
        <option value="filosofia_paidagogiki">Τμήμα Φιλοσοφίας - Παιδαγωγικής</option>
        <option value="secondary_pedagogy">Παιδαγωγικό Τμήμα Δευτεροβάθμιας Εκπαίδευσης</option>
        <option value="special_education_teachers">Παιδαγωγικό Τμήμα Ειδικής Αγωγής Πανεπιστημίου Θεσσαλίας, κατεύθυνση Δασκάλων</option>
        <option value="unknown">Δεν είμαι σίγουρος/η</option>
      </select>
    </div>
  </div>

  <div id="epathQuestions" class="hidden">
    <div class="question">
      <label for="epathDate">Η ημερομηνία κτήσης του πτυχίου Ε.Π.Α.Θ. είναι προγενέστερη της 12ης Ιουνίου 2018;</label>
      <select id="epathDate">
        <option value="">-- Επιλογή --</option>
        <option value="before">Ναι, είναι πριν από 12/6/2018</option>
        <option value="after">Όχι, είναι από 12/6/2018 και μετά</option>
        <option value="unknown">Δεν είμαι σίγουρος/η</option>
      </select>
    </div>
  </div>

  <div id="professorSchoolQuestions" class="hidden">
    <div class="question">
      <label for="entryYear">Έτος εισαγωγής στο Τμήμα καθηγητικής σχολής</label>
      <select id="entryYear">
        <option value="">-- Επιλογή --</option>
        <option value="up_to_2014">Μέχρι και το ακαδημαϊκό έτος 2014-2015</option>
        <option value="from_2015">Από το ακαδημαϊκό έτος 2015-2016 και μετά</option>
        <option value="unknown">Δεν είμαι σίγουρος/η</option>
      </select>
    </div>

    <div class="question">
      <label for="graduationYear">Έτος κτήσης πτυχίου</label>
      <select id="graduationYear">
        <option value="">-- Επιλογή --</option>
        <option value="up_to_2017">Έως και το ακαδημαϊκό έτος 2017-2018</option>
        <option value="from_2018">Από το ακαδημαϊκό έτος 2018-2019 και μετά</option>
        <option value="unknown">Δεν είμαι σίγουρος/η</option>
      </select>
    </div>
  </div>

  <div class="question">
    <label for="opsyd">Το σχετικό αποδεικτικό εμφανίζεται ή έχει καταχωριστεί στον Ο.Π.ΣΥ.Δ.;</label>
    <select id="opsyd">
      <option value="">-- Επιλογή --</option>
      <option value="yes">Ναι</option>
      <option value="no">Όχι</option>
      <option value="unknown">Δεν είμαι σίγουρος/η</option>
    </select>
  </div>

  <button id="checkEparkeiaBtn" class="guide-submit" type="button">Έλεγχος Παιδαγωγικής και Διδακτικής Επάρκειας</button>

  <div id="result" class="result" role="status" aria-live="polite"></div>

  <p class="small-note">
    Το αποτέλεσμα είναι ενδεικτικό. Δεν αντικαθιστά την επίσημη προκήρυξη, τις οδηγίες του Α.Σ.Ε.Π.,
    τον έλεγχο του Ο.Π.ΣΥ.Δ. ή τον έλεγχο των αρμόδιων υπηρεσιών. Η αναγνώριση επαγγελματικής
    ισοδυναμίας τίτλου δεν καλύπτει από μόνη της το ζήτημα της Παιδαγωγικής και Διδακτικής Επάρκειας.
  </p>
</div>

<script src="<?php echo htmlspecialchars(edu_asset_url('includes/pedagogical-competence-ui.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>

<?php sourceCardStart(); ?>
  <p>Προκηρύξεις Α.Σ.Ε.Π. <strong>1ΓΕ/2026</strong> και <strong>2ΓΕ/2026</strong>, με βάση τις προβλεπόμενες κατηγορίες αποδεικτικών Παιδαγωγικής και Διδακτικής Επάρκειας του <strong>Παραρτήματος Ε΄</strong> και τις σχετικές νομοθετικές παραπομπές που περιλαμβάνονται σε αυτό.</p>
  <?php sourceCardLinksStart(); ?><?php sourceCardLink('https://info.asep.gr/node/78700', '1ΓΕ/2026 — ΑΣΕΠ ↗'); ?><?php sourceCardLink('https://info.asep.gr/node/78701', '2ΓΕ/2026 — ΑΣΕΠ ↗'); ?><?php sourceCardLinksEnd(); ?>
<?php sourceCardEnd(); ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
  <script src="<?php echo htmlspecialchars(edu_asset_url('assets/common.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
</body>
</html>