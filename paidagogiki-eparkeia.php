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
    <select id="proofType" onchange="updateVisibility()">
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

  <button class="guide-submit" type="button" onclick="checkEparkeia()">Έλεγχος Παιδαγωγικής και Διδακτικής Επάρκειας</button>

  <div id="result" class="result" role="status" aria-live="polite"></div>

  <p class="small-note">
    Το αποτέλεσμα είναι ενδεικτικό. Δεν αντικαθιστά την επίσημη προκήρυξη, τις οδηγίες του Α.Σ.Ε.Π.,
    τον έλεγχο του Ο.Π.ΣΥ.Δ. ή τον έλεγχο των αρμόδιων υπηρεσιών. Η αναγνώριση επαγγελματικής
    ισοδυναμίας τίτλου δεν καλύπτει από μόνη της το ζήτημα της Παιδαγωγικής και Διδακτικής Επάρκειας.
  </p>
</div>

<script>
  function valueOf(id) {
    return document.getElementById(id).value;
  }

  function hide(id) {
    document.getElementById(id).classList.add("hidden");
  }

  function show(id) {
    document.getElementById(id).classList.remove("hidden");
  }

  function updateVisibility() {
    const proofType = valueOf("proofType");

    hide("pedagogicalDepartmentQuestions");
    hide("epathQuestions");
    hide("professorSchoolQuestions");

    document.getElementById("result").style.display = "none";

    if (proofType === "pedagogical_department") {
      show("pedagogicalDepartmentQuestions");
    }

    if (proofType === "epath") {
      show("epathQuestions");
    }

    if (proofType === "professor_school") {
      show("professorSchoolQuestions");
    }
  }

  function showResult(type, html) {
    const result = document.getElementById("result");
    result.style.display = "block";
    result.className = "result " + type;
    result.innerHTML = html;
  }

  function makeList(items) {
    if (!items.length) return "";
    return "<ul>" + items.map(item => "<li>" + item + "</li>").join("") + "</ul>";
  }

  function opsydNote(opsyd) {
    if (opsyd === "yes") {
      return "Έχεις δηλώσει ότι το σχετικό αποδεικτικό εμφανίζεται ή έχει καταχωριστεί στον Ο.Π.ΣΥ.Δ.";
    }

    if (opsyd === "no") {
      return "Χρειάζεται να ελέγξεις αν πρέπει να υποβάλεις αίτημα επικαιροποίησης στοιχείων στον Ο.Π.ΣΥ.Δ. και να μεταφορτώσεις το σχετικό δικαιολογητικό.";
    }

    return "Χρειάζεται έλεγχος στον Ο.Π.ΣΥ.Δ. για το αν το σχετικό αποδεικτικό έχει καταχωριστεί σωστά.";
  }

  function checkEparkeia() {
    const specialty = valueOf("specialty");
    const proofType = valueOf("proofType");
    const opsyd = valueOf("opsyd");

    if (!specialty || !proofType || !opsyd) {
      showResult(
        "unknown",
        "<h2>Λείπουν στοιχεία</h2><p>Παρακαλώ απάντησε στις βασικές ερωτήσεις.</p>"
      );
      return;
    }

    const documents = [];
    const notes = [];
    notes.push(opsydNote(opsyd));

    if (proofType === "aei_certificate") {
      documents.push("Βεβαίωση Παιδαγωγικής και Διδακτικής Επάρκειας από Τμήμα Α.Ε.Ι. ή από συνεργαζόμενα Τμήματα Α.Ε.Ι.");
      documents.push("Η βεβαίωση πρέπει να προκύπτει μετά από παρακολούθηση ομάδας μαθημάτων ή ειδικού προγράμματος σπουδών.");
      notes.push("Στις 1ΓΕ/2026 και 2ΓΕ/2026 η σχετική μεταβατική δυνατότητα αφορά αποφοίτους που είχαν εισαχθεί έως και το ακαδημαϊκό έτος 2026-2027 σε Τμήμα/Σχολή που χορηγούσε την πιστοποίηση κατά τον χρόνο εισαγωγής.");

      showResult(
        "positive",
        `
          <h2>Φαίνεται ότι διαθέτεις Παιδαγωγική και Διδακτική Επάρκεια</h2>
          <p>
            Η βεβαίωση Α.Ε.Ι. είναι μία από τις ρητές περιπτώσεις αποδεικτικών Π.Δ.Ε.
          </p>

          <div class="note-box">
            <strong>Ενδεικτικά δικαιολογητικά:</strong>
            ${makeList(documents)}
          </div>

          <div class="note-box">
            <strong>Προσοχή:</strong>
            ${makeList(notes)}
          </div>
        `
      );
      return;
    }

    if (proofType === "education_msc_phd") {
      documents.push("Μεταπτυχιακός τίτλος σπουδών στις επιστήμες της αγωγής ή διδακτορικό δίπλωμα στις επιστήμες της αγωγής.");
      documents.push("Αν πρόκειται για τίτλο αλλοδαπής, ελέγχεται και το ζήτημα της αναγνώρισης, όπου απαιτείται.");

      showResult(
        "positive",
        `
          <h2>Φαίνεται ότι διαθέτεις Παιδαγωγική και Διδακτική Επάρκεια</h2>
          <p>
            Δήλωσες μεταπτυχιακό ή διδακτορικό στις επιστήμες της αγωγής, που προβλέπεται ως αποδεικτικό Π.Δ.Ε.
          </p>

          <div class="note-box">
            <strong>Ενδεικτικά δικαιολογητικά:</strong>
            ${makeList(documents)}
          </div>

          <div class="note-box">
            <strong>Προσοχή:</strong>
            ${makeList(notes)}
          </div>
        `
      );
      return;
    }

    if (proofType === "old_certificate") {
      documents.push("Πιστοποιητικό παιδαγωγικής επάρκειας της παρ. 5 του άρθρου 4 του ν. 3027/2002.");

      showResult(
        "positive",
        `
          <h2>Φαίνεται ότι διαθέτεις Παιδαγωγική και Διδακτική Επάρκεια</h2>
          <p>
            Δήλωσες παλαιό πιστοποιητικό παιδαγωγικής επάρκειας που προβλέπεται στο Παράρτημα Ε΄.
          </p>

          <div class="note-box">
            <strong>Ενδεικτικά δικαιολογητικά:</strong>
            ${makeList(documents)}
          </div>

          <div class="note-box">
            <strong>Προσοχή:</strong>
            ${makeList(notes)}
          </div>
        `
      );
      return;
    }

    if (proofType === "pedagogical_department") {
      const departmentType = valueOf("pedagogicalDepartmentType");

      if (!departmentType) {
        showResult(
          "unknown",
          "<h2>Λείπει στοιχείο</h2><p>Παρακαλώ επίλεξε την κατηγορία Παιδαγωγικού Τμήματος.</p>"
        );
        return;
      }

      if (departmentType === "unknown") {
        showResult(
          "unknown",
          `
            <h2>Χρειάζεται περαιτέρω έλεγχος</h2>
            <p>
              Δεν είναι σαφές αν ο τίτλος σου ανήκει στις περιπτώσεις Παιδαγωγικών Τμημάτων
              που πιστοποιούν εξ ορισμού Π.Δ.Ε.
            </p>

            <div class="note-box">
              <strong>Τι να ελέγξεις:</strong>
              <ul>
                <li>Την ακριβή ονομασία του Τμήματος στο πτυχίο σου.</li>
                <li>Αν το Τμήμα περιλαμβάνεται στις περιπτώσεις του Παραρτήματος Ε΄.</li>
                <li>Την καταχώριση στον Ο.Π.ΣΥ.Δ.</li>
              </ul>
            </div>
          `
        );
        return;
      }

      documents.push("Πτυχίο Παιδαγωγικού Τμήματος ή αντίστοιχου Τμήματος που αναφέρεται στο Παράρτημα Ε΄.");

      showResult(
        "positive",
        `
          <h2>Φαίνεται ότι διαθέτεις Παιδαγωγική και Διδακτική Επάρκεια</h2>
          <p>
            Οι περιπτώσεις Παιδαγωγικών Τμημάτων του Παραρτήματος Ε΄ πιστοποιούν την Π.Δ.Ε.
            εξ ορισμού με την αποφοίτηση από το αντίστοιχο Τμήμα.
          </p>

          <div class="note-box">
            <strong>Ενδεικτικά δικαιολογητικά:</strong>
            ${makeList(documents)}
          </div>

          <div class="note-box">
            <strong>Προσοχή:</strong>
            ${makeList(notes)}
          </div>
        `
      );
      return;
    }

    if (proofType === "aspaite") {
      documents.push("Πτυχίο Α.Σ.ΠΑΙ.Τ.Ε.");

      showResult(
        "positive",
        `
          <h2>Φαίνεται ότι διαθέτεις Παιδαγωγική και Διδακτική Επάρκεια</h2>
          <p>
            Το πτυχίο Α.Σ.ΠΑΙ.Τ.Ε. περιλαμβάνεται στις περιπτώσεις που πιστοποιούν Π.Δ.Ε.
            εξ ορισμού.
          </p>

          <div class="note-box">
            <strong>Ενδεικτικά δικαιολογητικά:</strong>
            ${makeList(documents)}
          </div>

          <div class="note-box">
            <strong>Προσοχή:</strong>
            ${makeList(notes)}
          </div>
        `
      );
      return;
    }

    if (proofType === "aspaite_eppaik") {
      documents.push("Πιστοποιητικό Παιδαγωγικής και Διδακτικής Επάρκειας του Ετήσιου Προγράμματος Παιδαγωγικής Κατάρτισης (ΕΠΠΑΙΚ) της Α.Σ.ΠΑΙ.Τ.Ε. / πρώην ΠΑΤΕΣ–ΣΕΛΕΤΕ.");

      showResult(
        "positive",
        `
          <h2>Φαίνεται ότι διαθέτεις Παιδαγωγική και Διδακτική Επάρκεια</h2>
          <p>Δήλωσες πιστοποιητικό ΕΠΠΑΙΚ της Α.Σ.ΠΑΙ.Τ.Ε., το οποίο αποτελεί αποδεικτικό Π.Δ.Ε.</p>
          <div class="note-box"><strong>Ενδεικτικό δικαιολογητικό:</strong>${makeList(documents)}</div>
          <div class="note-box"><strong>Προσοχή:</strong>${makeList(notes)}</div>
        `
      );
      return;
    }

    if (proofType === "article99") {
      documents.push("Πιστοποιητικό Παιδαγωγικής και Διδακτικής Επάρκειας του άρθρου 99 του ν. 4957/2022.");
      documents.push("Το πιστοποιητικό πρέπει να έχει χορηγηθεί βάσει ειδικού προγράμματος σπουδών από Α.Ε.Ι.");

      showResult(
        "positive",
        `
          <h2>Φαίνεται ότι διαθέτεις Παιδαγωγική και Διδακτική Επάρκεια</h2>
          <p>
            Δήλωσες πιστοποιητικό Π.Δ.Ε. του άρθρου 99 του ν. 4957/2022, που προβλέπεται στο Παράρτημα Ε΄.
          </p>

          <div class="note-box">
            <strong>Ενδεικτικά δικαιολογητικά:</strong>
            ${makeList(documents)}
          </div>

          <div class="note-box">
            <strong>Προσοχή:</strong>
            ${makeList(notes)}
          </div>
        `
      );
      return;
    }

    if (proofType === "epath") {
      const epathDate = valueOf("epathDate");

      if (!epathDate) {
        showResult(
          "unknown",
          "<h2>Λείπει στοιχείο</h2><p>Παρακαλώ δήλωσε την ημερομηνία κτήσης του πτυχίου Ε.Π.Α.Θ.</p>"
        );
        return;
      }

      if (epathDate === "before") {
        documents.push("Πτυχίο Ειδικής Παιδαγωγικής Ακαδημίας Θεσσαλονίκης με ημερομηνία κτήσης πριν από 12/6/2018.");

        showResult(
          "positive",
          `
            <h2>Φαίνεται ότι διαθέτεις Παιδαγωγική και Διδακτική Επάρκεια</h2>
            <p>
              Το πτυχίο Ε.Π.Α.Θ. με ημερομηνία κτήσης προγενέστερη της 12ης Ιουνίου 2018
              περιλαμβάνεται στις περιπτώσεις εξ ορισμού Π.Δ.Ε.
            </p>

            <div class="note-box">
              <strong>Ενδεικτικά δικαιολογητικά:</strong>
              ${makeList(documents)}
            </div>

            <div class="note-box">
              <strong>Προσοχή:</strong>
              ${makeList(notes)}
            </div>
          `
        );
        return;
      }

      if (epathDate === "after") {
        showResult(
          "warning",
          `
            <h2>Δεν φαίνεται να καλύπτεται η περίπτωση Ε.Π.Α.Θ.</h2>
            <p>
              Για την περίπτωση Ε.Π.Α.Θ. το Παράρτημα Ε΄ αναφέρεται σε πτυχίο με ημερομηνία
              κτήσης προγενέστερη της 12ης Ιουνίου 2018.
            </p>

            <div class="note-box">
              <strong>Τι να ελέγξεις:</strong>
              <ul>
                <li>Αν διαθέτεις άλλο αποδεικτικό Π.Δ.Ε.</li>
                <li>Αν υπάρχει σχετική καταχώριση ή δυνατότητα επικαιροποίησης στον Ο.Π.ΣΥ.Δ.</li>
              </ul>
            </div>
          `
        );
        return;
      }

      showResult(
        "unknown",
        `
          <h2>Χρειάζεται περαιτέρω έλεγχος</h2>
          <p>
            Δεν είναι σαφές αν το πτυχίο Ε.Π.Α.Θ. πληροί τη χρονική προϋπόθεση του Παραρτήματος Ε΄.
          </p>
        `
      );
      return;
    }

    if (proofType === "professor_school") {
      const entryYear = valueOf("entryYear");
      const graduationYear = valueOf("graduationYear");

      if (!entryYear || !graduationYear) {
        showResult(
          "unknown",
          "<h2>Λείπουν στοιχεία</h2><p>Παρακαλώ απάντησε στις ερωτήσεις για το έτος εισαγωγής και το έτος κτήσης πτυχίου.</p>"
        );
        return;
      }

      if (entryYear === "up_to_2014") {
        documents.push("Πτυχίο καθηγητικής σχολής.");
        documents.push("Βεβαίωση εγγραφής στο Τμήμα, για την απόδειξη ότι η εισαγωγή έγινε έως και το ακαδημαϊκό έτος 2014-2015.");

        showResult(
          "positive",
          `
            <h2>Φαίνεται ότι διαθέτεις Παιδαγωγική και Διδακτική Επάρκεια</h2>
            <p>
              Για πτυχίο καθηγητικής σχολής με έτος εισαγωγής μέχρι και το ακαδημαϊκό έτος 2014-2015,
              η Π.Δ.Ε. μπορεί να πιστοποιείται εξ ορισμού.
            </p>

            <div class="note-box">
              <strong>Ενδεικτικά δικαιολογητικά:</strong>
              ${makeList(documents)}
            </div>

            <div class="note-box">
              <strong>Προσοχή:</strong>
              ${makeList(notes)}
            </div>
          `
        );
        return;
      }

      if (graduationYear === "up_to_2017") {
        documents.push("Πτυχίο καθηγητικής σχολής με κτήση έως και το ακαδημαϊκό έτος 2017-2018.");

        showResult(
          "positive",
          `
            <h2>Φαίνεται ότι διαθέτεις Παιδαγωγική και Διδακτική Επάρκεια</h2>
            <p>
              Για πτυχίο καθηγητικής σχολής με κτήση έως και το ακαδημαϊκό έτος 2017-2018,
              η Π.Δ.Ε. μπορεί να πιστοποιείται εξ ορισμού.
            </p>

            <div class="note-box">
              <strong>Ενδεικτικά δικαιολογητικά:</strong>
              ${makeList(documents)}
            </div>

            <div class="note-box">
              <strong>Προσοχή:</strong>
              ${makeList(notes)}
            </div>
          `
        );
        return;
      }

      if (entryYear === "from_2015" && graduationYear === "from_2018") {
        showResult(
          "warning",
          `
            <h2>Δεν φαίνεται να καλύπτεσαι αυτομάτως μόνο από το πτυχίο</h2>
            <p>
              Για όσους/ες εισήχθησαν από το ακαδημαϊκό έτος 2015-2016 και μετά
              και είναι πτυχιούχοι από το ακαδημαϊκό έτος 2018-2019 και εφεξής,
              δεν προκύπτει αυτομάτως Π.Δ.Ε. μόνο από το πτυχίο καθηγητικής σχολής.
            </p>

            <div class="note-box">
              <strong>Τι χρειάζεται να ελέγξεις:</strong>
              <ul>
                <li>Αν διαθέτεις βεβαίωση Π.Δ.Ε. από Α.Ε.Ι.</li>
                <li>Αν έχεις μεταπτυχιακό ή διδακτορικό στις επιστήμες της αγωγής.</li>
                <li>Αν διαθέτεις πιστοποιητικό Π.Δ.Ε. του άρθρου 99 του ν. 4957/2022.</li>
                <li>Αν υπάρχει άλλο αποδεικτικό Π.Δ.Ε. που προβλέπεται στο Παράρτημα Ε΄.</li>
                <li>Αν το σχετικό αποδεικτικό είναι καταχωρισμένο στον Ο.Π.ΣΥ.Δ.</li>
              </ul>
            </div>
          `
        );
        return;
      }

      showResult(
        "unknown",
        `
          <h2>Χρειάζεται περαιτέρω έλεγχος</h2>
          <p>
            Οι απαντήσεις για το έτος εισαγωγής ή το έτος κτήσης πτυχίου δεν αρκούν
            για ασφαλές συμπέρασμα.
          </p>

          <div class="note-box">
            Έλεγξε το Παράρτημα Ε΄, το Τμήμα αποφοίτησης και την καταχώριση στον Ο.Π.ΣΥ.Δ.
          </div>
        `
      );
      return;
    }

    if (proofType === "none") {
      showResult(
        "negative",
        `
          <h2>Δεν φαίνεται να προκύπτει Παιδαγωγική και Διδακτική Επάρκεια</h2>
          <p>
            Με βάση τις απαντήσεις σου, δεν δήλωσες κάποιο από τα αποδεικτικά Π.Δ.Ε.
            που προβλέπονται στο Παράρτημα Ε΄.
          </p>

          <div class="note-box">
            <strong>Τι να ελέγξεις:</strong>
            <ul>
              <li>Αν το Τμήμα σου χορηγεί σχετική βεβαίωση Π.Δ.Ε.</li>
              <li>Αν έχεις μεταπτυχιακό ή διδακτορικό στις επιστήμες της αγωγής.</li>
              <li>Αν έχεις πιστοποιητικό Π.Δ.Ε. από Α.Ε.Ι. ή άλλο προβλεπόμενο αποδεικτικό.</li>
              <li>Αν υπάρχει σχετική καταχώριση στον Ο.Π.ΣΥ.Δ.</li>
            </ul>
          </div>
        `
      );
      return;
    }

    showResult(
      "unknown",
      `
        <h2>Χρειάζεται περαιτέρω έλεγχος</h2>
        <p>
          Δεν είναι σαφές με ποιο αποδεικτικό πιστοποιείται η Παιδαγωγική και Διδακτική Επάρκεια.
        </p>

        <div class="note-box">
          Καλό είναι να ελέγξεις το Παράρτημα Ε΄ της προκήρυξης και την καταχώριση στον Ο.Π.ΣΥ.Δ.
        </div>
      `
    );
  }
</script>


<?php sourceCardStart(); ?>
  <p>Προκηρύξεις Α.Σ.Ε.Π. <strong>1ΓΕ/2026</strong> και <strong>2ΓΕ/2026</strong>, με βάση τις προβλεπόμενες κατηγορίες αποδεικτικών Παιδαγωγικής και Διδακτικής Επάρκειας του <strong>Παραρτήματος Ε΄</strong> και τις σχετικές νομοθετικές παραπομπές που περιλαμβάνονται σε αυτό.</p>
  <?php sourceCardLinksStart(); ?><?php sourceCardLink('https://info.asep.gr/node/78700', '1ΓΕ/2026 — ΑΣΕΠ ↗'); ?><?php sourceCardLink('https://info.asep.gr/node/78701', '2ΓΕ/2026 — ΑΣΕΠ ↗'); ?><?php sourceCardLinksEnd(); ?>
<?php sourceCardEnd(); ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
  <script src="<?php echo htmlspecialchars(edu_asset_url('assets/common.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
</body>
</html>