<?php require_once __DIR__ . '/includes/config.php'; ?>
<?php require_once __DIR__ . '/includes/weekly-timetable-data.php'; ?>
<!doctype html>
<html lang="el">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Ώρες Μαθημάτων στο Εβδομαδιαίο Ωρολόγιο Πρόγραμμα</title>
  <link rel="stylesheet" href="<?php echo htmlspecialchars(edu_asset_url('assets/common.css'), ENT_QUOTES, 'UTF-8'); ?>">
  <link rel="stylesheet" href="<?php echo htmlspecialchars(edu_asset_url('assets/weekly-timetable.css'), ENT_QUOTES, 'UTF-8'); ?>">

</head>
<body class="edu-ui edu-calc-standard edu-page-weekly-timetable">
<?php require_once __DIR__ . '/includes/header.php'; ?>
<?php require_once __DIR__ . '/includes/components/calculator-layout.php'; ?>

<main id="weeklyTimetableTool" class="app">
  <?php calculatorHero(array(
    'title_html' => 'Ώρες Μαθημάτων στο Εβδομαδιαίο Ωρολόγιο Πρόγραμμα',
    'intro' => 'Επίλεξε τύπο σχολείου και τάξη για να δεις τις εβδομαδιαίες ώρες κάθε μαθήματος, όπως ισχύουν για το σχολικό έτος 2026–2027.',
    'meta_class' => 'meta',
    'badges' => array('2026–2027', 'Γυμνάσιο', 'ΓΕΛ', 'Ημερήσιο & Εσπερινό', 'Έτοιμο για μελλοντική διασύνδεση με αναθέσεις')
  )); ?>

  <?php calculatorColumnsStart(); ?>
    <?php calculatorMainStart(); ?>
      <?php calculatorCardStart(); ?>
        <h2>Ωρολόγιο πρόγραμμα</h2>
        <p class="cap">Το εργαλείο εμφανίζει το επίσημο εβδομαδιαίο πρόγραμμα ανά τάξη. Στα ΓΕΛ οι Ομάδες Προσανατολισμού εμφανίζονται χωριστά από τη Γενική Παιδεία.</p>

        <div class="field-grid">
          <div class="field">
            <label for="schoolType">Τύπος σχολείου</label>
            <select id="schoolType">
              <?php foreach (weeklyTimetableSchoolTypes() as $code => $info): ?>
                <option value="<?php echo htmlspecialchars($code, ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($info['label'], ENT_QUOTES, 'UTF-8'); ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="field">
            <label for="grade">Τάξη</label>
            <select id="grade"></select>
          </div>
        </div>

        <div id="programSummary" class="timetable-summary" aria-live="polite"></div>
        <div id="timetableResults" aria-live="polite"></div>

        <div class="architecture-note">
          Το εργαλείο έχει σχεδιαστεί ώστε σε επόμενη φάση να συνδεθεί με τις <strong>Α΄/Β΄/Γ΄ αναθέσεις</strong> και με το <strong>υποχρεωτικό ωράριο</strong> κάθε εκπαιδευτικού.
        </div>
      <?php calculatorCardEnd(); ?>
    <?php calculatorMainEnd(); ?>

    <?php calculatorResultsStart(array('class' => 'card results')); ?>
        <h2>Τι σημαίνει «ώρες»</h2>
        <p class="cap">Οι ώρες αφορούν το εβδομαδιαίο ωρολόγιο πρόγραμμα του συγκεκριμένου τμήματος/ομάδας και όχι το ατομικό υποχρεωτικό ωράριο ενός εκπαιδευτικού.</p>
        <p class="help">Στη Γ΄ ΓΕΛ ορισμένα μαθήματα Γενικής Παιδείας εξαρτώνται από την Ομάδα Προσανατολισμού. Δεν πρέπει να αθροίζονται σαν να τα παρακολουθούν όλοι οι μαθητές.</p>
        <p class="help">Στο Β΄ Εσπερινού ΓΕΛ οι ενδείξεις <strong>1/2</strong> και <strong>2/1</strong> διατηρούνται όπως ακριβώς στο ΦΕΚ και δεν μετατρέπονται σε τεχνητό μέσο όρο.</p>
<?php calculatorResultsEnd(); ?>
  <?php calculatorColumnsEnd(); ?>
</main>

<?php sourceCardStart(); ?>
  <?php sourceCardDisclaimerStart(); ?>
    Τα δεδομένα της παρούσας πρώτης έκδοσης καλύπτουν τη Γενική Δευτεροβάθμια Εκπαίδευση. Οι ειδικές δομές (Ε.Α.Ε., ΕΝ.Ε.Ε.ΓΥ.-Λ., ΕΠΑ.Λ./Π.ΕΠΑ.Λ., Καλλιτεχνικά και Μουσικά) θα προστεθούν στο ίδιο dataset με την ίδια λογική σταθερών κλειδιών.
  <?php sourceCardDisclaimerEnd(); ?>
  <?php sourceCardLinksStart(); ?>
    <?php sourceCardLink('https://www.minedu.gov.gr/images/joomlart/PDFs/PHEK%20B%202132_09_04_26_OP%20EM%20GYMN.pdf', 'ΦΕΚ Β΄ 2132/2026 — Ημερήσιο Γυμνάσιο ↗'); ?>
    <?php sourceCardLink('https://www.minedu.gov.gr/images/joomlart/PDFs/PHEK%20B%202106_09_04_26_OP%20EM%20GEL_ESP%20Gymnasio.pdf', 'ΦΕΚ Β΄ 2106/2026 — Ημερήσιο ΓΕΛ & Εσπερινό Γυμνάσιο ↗'); ?>
    <?php sourceCardLink('https://dide.ira.sch.gr/wp-content/uploads/2026/04/%CE%A6%CE%95%CE%9A-%CE%92-2102_09_04_26_%CE%A9%CE%A0-%CE%95%CE%A3%CE%A0-%CE%93%CE%95%CE%9B.pdf', 'ΦΕΚ Β΄ 2102/2026 — Εσπερινό ΓΕΛ ↗'); ?>
  <?php sourceCardLinksEnd(); ?>
<?php sourceCardEnd(); ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
<script src="<?php echo htmlspecialchars(edu_asset_url('assets/common.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
<script>
(function () {
  'use strict';

  var schools = <?php echo json_encode(weeklyTimetableSchoolTypes(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>;
  var allRows = <?php echo json_encode(weeklyTimetableRows(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>;
  var schoolSelect = document.getElementById('schoolType');
  var gradeSelect = document.getElementById('grade');
  var results = document.getElementById('timetableResults');
  var summary = document.getElementById('programSummary');

  function esc(value) {
    return String(value == null ? '' : value)
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#039;');
  }

  function refreshGrades() {
    var school = schoolSelect.value;
    var grades = schools[school] && schools[school].grades ? schools[school].grades : [];
    var previous = gradeSelect.value;
    gradeSelect.innerHTML = '';
    grades.forEach(function (grade) {
      var option = document.createElement('option');
      option.value = grade;
      option.textContent = grade + ' τάξη';
      gradeSelect.appendChild(option);
    });
    if (grades.indexOf(previous) !== -1) gradeSelect.value = previous;
    render();
  }

  function rowForGrade(row, grade) {
    if (!row.hours || typeof row.hours[grade] === 'undefined') return null;
    var copy = {};
    Object.keys(row).forEach(function (key) { copy[key] = row[key]; });
    copy.hoursText = row.hours_display && typeof row.hours_display[grade] !== 'undefined'
      ? row.hours_display[grade]
      : String(row.hours[grade]);
    copy.conditionText = row.condition_by_grade && row.condition_by_grade[grade]
      ? row.condition_by_grade[grade]
      : (row.condition || '');
    copy.noteText = row.note_by_grade && row.note_by_grade[grade]
      ? row.note_by_grade[grade]
      : (row.note || '');
    return copy;
  }

  function renderSummary(school, grade) {
    summary.innerHTML = '';
    var info = schools[school] && schools[school].program ? schools[school].program[grade] : null;
    if (!info) return;
    var html = '';
    if (info.parts) {
      Object.keys(info.parts).forEach(function (label) {
        html += '<span>' + esc(label) + ': <strong>' + esc(info.parts[label]) + '</strong> ώρες</span>';
      });
    }
    html += '<span class="total">Σύνολο: ' + esc(info.total) + ' ώρες</span>';
    summary.innerHTML = html;
  }

  function render() {
    var school = schoolSelect.value;
    var grade = gradeSelect.value;
    if (!school || !grade) {
      results.innerHTML = '<p class="help">Επίλεξε τύπο σχολείου και τάξη.</p>';
      summary.innerHTML = '';
      return;
    }

    renderSummary(school, grade);

    var rows = allRows.map(function (row) {
      if (row.school !== school) return null;
      return rowForGrade(row, grade);
    }).filter(Boolean);

    var groups = [];
    var grouped = {};
    rows.forEach(function (row) {
      var group = row.group || 'Πρόγραμμα';
      if (!grouped[group]) {
        grouped[group] = [];
        groups.push(group);
      }
      grouped[group].push(row);
    });

    var html = '';
    groups.forEach(function (group) {
      html += '<section class="timetable-group"><h3>' + esc(group) + '</h3>';
      grouped[group].forEach(function (row) {
        var details = [];
        if (row.section) details.push('<span>' + esc(row.section) + '</span>');
        if (row.conditionText) details.push('<strong>Προϋπόθεση:</strong> ' + esc(row.conditionText));
        if (row.noteText) details.push(esc(row.noteText));
        if (row.mode === 'alternative') details.push('Εναλλακτική διδασκαλία στην ίδια ωριαία ζώνη');
        if (row.mode === 'choice') details.push('Επιλογή στην ίδια ωριαία ζώνη');
        html += '<div class="result-row">'
          + '<span><strong>' + esc(row.subject) + '</strong>'
          + (details.length ? '<small>' + details.join(' · ') + '</small>' : '')
          + '</span>'
          + '<span class="hours-badge">' + esc(row.hoursText) + ' ώρ.</span>'
          + '</div>';
      });
      html += '</section>';
    });

    results.innerHTML = html || '<p class="help">Δεν υπάρχουν καταχωρισμένα μαθήματα για την επιλογή αυτή.</p>';
  }

  schoolSelect.addEventListener('change', refreshGrades);
  gradeSelect.addEventListener('change', render);
  refreshGrades();
}());
</script>
</body>
</html>
