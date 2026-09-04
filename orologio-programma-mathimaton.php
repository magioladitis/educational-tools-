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
  <?php
  // Εσωτερική σημείωση / roadmap — να μην εμφανίζεται στο public UI:
  // Το εργαλείο έχει σχεδιαστεί ώστε σε επόμενη φάση να συνδεθεί με τις
  // Α΄/Β΄/Γ΄ αναθέσεις και με το υποχρεωτικό ωράριο κάθε εκπαιδευτικού.
  ?>
  <?php calculatorHero(array(
    'title_html' => 'Ώρες Μαθημάτων στο Εβδομαδιαίο Ωρολόγιο Πρόγραμμα',
    'intro' => 'Επίλεξε τύπο σχολείου και τάξη για να δεις τις εβδομαδιαίες ώρες κάθε μαθήματος, όπως ισχύουν για το σχολικό έτος 2026–2027.',
    'meta_class' => 'meta',
    'badges' => array('2026–2027', 'Γυμνάσιο & ΓΕΛ', 'ΕΠΑ.Λ. & Π.ΕΠΑ.Λ.', 'ΕΝ.Ε.Ε.ΓΥ.-Λ.', 'Ημερήσιο & Εσπερινό', 'Καλλιτεχνικά', 'Μουσικά')
  )); ?>

  <?php calculatorColumnsStart(); ?>
    <?php calculatorMainStart(); ?>
      <?php calculatorCardStart(); ?>
        <h2>Ωρολόγιο πρόγραμμα</h2>
        <p class="cap">Το εργαλείο εμφανίζει το επίσημο εβδομαδιαίο πρόγραμμα ανά τάξη. Στα ΓΕΛ οι Ομάδες Προσανατολισμού εμφανίζονται χωριστά από τη Γενική Παιδεία. Στα Καλλιτεχνικά Σχολεία επιλέγεται κατεύθυνση. Στη Β΄–Γ΄ τάξη ΕΠΑ.Λ./Π.ΕΠΑ.Λ. επιλέγεται τομέας και στη Γ΄ τάξη επιλέγεται επιπλέον ειδικότητα. Στο Λύκειο ΕΝ.Ε.Ε.ΓΥ.-Λ. επιλέγεται τομέας στη Β΄–Δ΄ τάξη και ειδικότητα στη Δ΄. Στον Τομέα Υγείας της Β΄/Γ΄ τάξης εμφανίζεται επιπλέον η προβλεπόμενη από το ΦΕΚ περίπτωση διδασκαλίας.</p>

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
          <div id="trackField" class="field" hidden>
            <label id="trackLabel" for="track">Κατεύθυνση</label>
            <select id="track"></select>
          </div>
          <div id="variantField" class="field" hidden>
            <label id="variantLabel" for="variant">Περίπτωση</label>
            <select id="variant"></select>
          </div>
          <div id="specialtyField" class="field" hidden>
            <label id="specialtyLabel" for="specialty">Ειδικότητα</label>
            <select id="specialty"></select>
          </div>
        </div>

        <div id="programSummary" class="timetable-summary" aria-live="polite"></div>
        <div id="timetableResults" aria-live="polite"></div>

      <?php calculatorCardEnd(); ?>
    <?php calculatorMainEnd(); ?>

    <?php calculatorResultsStart(array('class' => 'card results')); ?>
        <h2>Τι σημαίνει «ώρες»</h2>
        <p class="cap">Οι ώρες αφορούν το εβδομαδιαίο ωρολόγιο πρόγραμμα του συγκεκριμένου τμήματος/ομάδας και όχι το ατομικό υποχρεωτικό ωράριο ενός εκπαιδευτικού.</p>
        <p class="help">Στη Γ΄ ΓΕΛ ορισμένα μαθήματα Γενικής Παιδείας εξαρτώνται από την Ομάδα Προσανατολισμού. Δεν πρέπει να αθροίζονται σαν να τα παρακολουθούν όλοι οι μαθητές.</p>
        <p class="help">Στο Β΄ Εσπερινού ΓΕΛ οι ενδείξεις <strong>1/2</strong> και <strong>2/1</strong> διατηρούνται όπως ακριβώς στο ΦΕΚ και δεν μετατρέπονται σε τεχνητό μέσο όρο.</p>
        <p class="help">Στα επαγγελματικά μαθήματα: <strong>Θ</strong> = θεωρία, <strong>Ε</strong> = εργαστήριο, <strong>Σ</strong> = σχέδιο και <strong>ΠΑ</strong> = πρακτική άσκηση.</p>
<?php calculatorResultsEnd(); ?>
  <?php calculatorColumnsEnd(); ?>
</main>

<?php sourceCardStart(); ?>
  <?php sourceCardDisclaimerStart(); ?>
    Η τρέχουσα έκδοση καλύπτει Ημερήσιο και Εσπερινό Γυμνάσιο/ΓΕΛ, Καλλιτεχνικό και Μουσικό Γυμνάσιο/Γενικό Λύκειο, Γυμνάσιο και Λύκειο ΕΝ.Ε.Ε.ΓΥ.-Λ., Α΄–Γ΄ τάξη Ημερήσιου και τριετούς Εσπερινού ΕΠΑ.Λ., καθώς και Α΄–Γ΄ τάξη Π.ΕΠΑ.Λ., σύμφωνα με τα ισχύοντα ωρολόγια προγράμματα για το σχολικό έτος 2026–2027. Για τη Γ΄ Π.ΕΠΑ.Λ. οι τίτλοι μαθημάτων έχουν διασταυρωθεί και με το αυτοτελές ΦΕΚ αναθέσεων Β΄ 5510/2023.
  <?php sourceCardDisclaimerEnd(); ?>
  <?php sourceCardLinksStart(); ?>
    <?php sourceCardLink('https://www.minedu.gov.gr/images/joomlart/PDFs/PHEK%20B%202132_09_04_26_OP%20EM%20GYMN.pdf', 'ΦΕΚ Β΄ 2132/2026 — Ημερήσιο Γυμνάσιο ↗'); ?>
    <?php sourceCardLink('https://www.minedu.gov.gr/images/joomlart/PDFs/PHEK%20B%202106_09_04_26_OP%20EM%20GEL_ESP%20Gymnasio.pdf', 'ΦΕΚ Β΄ 2106/2026 — Ημερήσιο ΓΕΛ & Εσπερινό Γυμνάσιο ↗'); ?>
    <?php sourceCardLink('https://dide.ira.sch.gr/wp-content/uploads/2026/04/%CE%A6%CE%95%CE%9A-%CE%92-2102_09_04_26_%CE%A9%CE%A0-%CE%95%CE%A3%CE%A0-%CE%93%CE%95%CE%9B.pdf', 'ΦΕΚ Β΄ 2102/2026 — Εσπερινό ΓΕΛ ↗'); ?>
    <?php sourceCardLink('https://www.minedu.gov.gr/images/joomlart/PDFs/PHEK%20B%202104_09_04_26_OP%20KALL%20GYMN%20GEL%201.pdf', 'ΦΕΚ Β΄ 2104/2026 — Καλλιτεχνικά Σχολεία ↗'); ?>
    <?php sourceCardLink('https://www.minedu.gov.gr/images/joomlart/PDFs/PHEK%20B%202107_09_04_26_OP%20MOUSIKOU%20GYMN%20GEL.pdf', 'ΦΕΚ Β΄ 2107/2026 — Μουσικά Σχολεία ↗'); ?>
    <?php sourceCardLink('https://www.minedu.gov.gr/images/joomlart/PDFs/PHEK%202259tB22-04-2026%20me%20thema%20Orologio%20Programma%20ton%20mathematon%20tou%20Gymnasiou%20EN.E.E.GY-L.pdf', 'ΦΕΚ Β΄ 2259/2026 — Γυμνάσιο ΕΝ.Ε.Ε.ΓΥ.-Λ. ↗'); ?>
    <?php sourceCardLink('https://www.minedu.gov.gr/images/joomlart/PDFs/PHEK%202149%20t.B%2016-04-2026%20me%20thema%20Orologio%20programma%20tou%20Lykeiou%20ton%20Eniaion%20Eidikon%20Epangelmatikon%20Gymnasion%20-%20Lykeion%20EN.E.E.GY.-L.pdf', 'ΦΕΚ Β΄ 2149/2026 — Λύκειο ΕΝ.Ε.Ε.ΓΥ.-Λ. ↗'); ?>
    <?php sourceCardLink('https://www.minedu.gov.gr/images/joomlart/PDFs/2026_04_08_EXE_44260_tropop_YA_OPS_mathema_ETHIKE_A_B_EPAL_PHEK_2151B_16.04.2026.pdf', 'ΦΕΚ Β΄ 2151/2026 — Α΄/Β΄ Ημερήσιου ΕΠΑ.Λ. & Α΄ Εσπερινού ΕΠΑ.Λ. ↗'); ?>
    <?php sourceCardLink('https://www.minedu.gov.gr/images/joomlart/PDFs/2018.05_YA_OPS_AB_taxes_EPAL_N_4386_2016_NEO_PHEK_2187B_12.06.2018.pdf', 'ΦΕΚ Β΄ 2187/2018 — Α΄/Β΄ ΕΠΑ.Λ. ↗'); ?>
    <?php sourceCardLink('https://www.minedu.gov.gr/protovathmia-defterovathmia/orologio-programma-g-taksi---epaggelmatiki-ekpaidefsi', 'ΥΠΑΙΘΑ — Ισχύοντα ωρολόγια Γ΄ τάξης Επαγγελματικής Εκπαίδευσης ↗'); ?>
    <?php sourceCardLink('https://www.minedu.gov.gr/images/joomlart/PDFs/2017.04_YA_OPS_GD_taxes_EPAL_N.4386%20PHEK%201426%20B_26-4-17.pdf', 'ΦΕΚ Β΄ 1426/2017 — Γ΄ Ημερήσιου ΕΠΑ.Λ. ↗'); ?>
    <?php sourceCardLink('https://www.minedu.gov.gr/publications/docs2018/FEK_2636B.pdf', 'ΦΕΚ Β΄ 2636/2018 — Τριετές Εσπερινό ΕΠΑ.Λ. ↗'); ?>
    <?php sourceCardLink('https://www.minedu.gov.gr/publications/docs2018/orologio%CE%9D%CE%91%CE%A5%CE%A4.pdf', 'ΦΕΚ Β΄ 3224/2018 — Ναυτιλιακά ΕΠΑ.Λ. ↗'); ?>
    <?php sourceCardLink('https://www.minedu.gov.gr/images/joomlart/PDFs/2026_04_08_EXE_44286_tropop_YA_OPS_mathema_ETHIKE_B_PEPAL_PHEK_2136B_09.04.2026.pdf', 'ΦΕΚ Β΄ 2136/2026 — Α΄/Β΄ Π.ΕΠΑ.Λ. ↗'); ?>
    <?php sourceCardLink('https://www.minedu.gov.gr/images/joomlart/PDFs/2021_07_23_EXE_90217_YA_OPS_A_TAXES_P_EPAL_PHEK_3470B_29.07.2021.pdf', 'ΦΕΚ Β΄ 3470/2021 — Α΄ Π.ΕΠΑ.Λ. ↗'); ?>
    <?php sourceCardLink('https://www.minedu.gov.gr/images/joomlart/PDFs/2022_08_25_EXE_103460_YA_OPS_B_taxes_P_EPAL_n4763_2020_PHEK_4578B_30.08.2022.pdf', 'ΦΕΚ Β΄ 4578/2022 — Β΄ Π.ΕΠΑ.Λ. ↗'); ?>
    <?php sourceCardLink('https://www.minedu.gov.gr/images/joomlart/PDFs/2022_09_16_EXE_112468_tropop_YA_OPS_B_taxes_P_EPAL_n4763_2020_PHEK_4961B_22.09.2022.pdf', 'ΦΕΚ Β΄ 4961/2022 — Διόρθωση Β΄ Π.ΕΠΑ.Λ. ↗'); ?>
    <?php sourceCardLink('https://www.minedu.gov.gr/images/joomlart/PDFs/2023_08_28_EXE_93929_YA_OPS_G_taxes_P_EPAL_n4763_2020_PHEK_5251B_30.08.2023.pdf', 'ΦΕΚ Β΄ 5251/2023 — Γ΄ Π.ΕΠΑ.Λ. ↗'); ?>
    <?php sourceCardLink('https://www.minedu.gov.gr/publications/docs2023/2023_09_13_%CE%95%CE%9E%CE%95_101003_%CE%A5%CE%91_%CE%91%CE%BD%CE%B1%CE%B8%CE%AD%CF%83%CE%B5%CE%B9%CF%82_%CE%9C%CE%B1%CE%B8%CE%B7%CE%BC%CE%AC%CF%84%CF%89%CE%BD_%CE%93_%CE%A4%CE%AC%CE%BE%CE%B7%CF%82_%CE%A0_%CE%95%CE%A0%CE%91%CE%9B_%CE%A6%CE%95%CE%9A_5510%CE%92_18.09.2023.pdf', 'ΦΕΚ Β΄ 5510/2023 — Γ΄ Π.ΕΠΑ.Λ. / Αναθέσεις (διασταύρωση τίτλων) ↗'); ?>
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
  var trackField = document.getElementById('trackField');
  var trackSelect = document.getElementById('track');
  var trackLabel = document.getElementById('trackLabel');
  var variantField = document.getElementById('variantField');
  var variantSelect = document.getElementById('variant');
  var variantLabel = document.getElementById('variantLabel');
  var specialtyField = document.getElementById('specialtyField');
  var specialtySelect = document.getElementById('specialty');
  var specialtyLabel = document.getElementById('specialtyLabel');
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

  function currentTracks(school, grade) {
    var info = schools[school] || {};
    if (info.tracks_by_grade && info.tracks_by_grade[grade]) return info.tracks_by_grade[grade];
    return info.tracks || null;
  }

  function currentTrackLabel(school, grade) {
    var info = schools[school] || {};
    if (info.track_label_by_grade && info.track_label_by_grade[grade]) return info.track_label_by_grade[grade];
    return info.track_label || 'Κατεύθυνση';
  }

  function currentVariants(school, grade, track) {
    var info = schools[school] || {};
    if (!info.variants_by_grade_track || !info.variants_by_grade_track[grade]) return null;
    return info.variants_by_grade_track[grade][track] || null;
  }

  function currentVariantLabel(school, grade, track) {
    var info = schools[school] || {};
    if (info.variant_label_by_grade_track && info.variant_label_by_grade_track[grade] && info.variant_label_by_grade_track[grade][track]) {
      return info.variant_label_by_grade_track[grade][track];
    }
    return 'Περίπτωση';
  }

  function refreshVariants() {
    var school = schoolSelect.value;
    var grade = gradeSelect.value;
    var track = trackField.hidden ? '' : trackSelect.value;
    var variants = currentVariants(school, grade, track);
    var previous = variantSelect.value;
    variantSelect.innerHTML = '';

    if (!variants || !Object.keys(variants).length) {
      variantField.hidden = true;
      return;
    }

    variantField.hidden = false;
    variantLabel.textContent = currentVariantLabel(school, grade, track);
    Object.keys(variants).forEach(function (code) {
      var option = document.createElement('option');
      option.value = code;
      option.textContent = variants[code];
      variantSelect.appendChild(option);
    });
    if (Object.prototype.hasOwnProperty.call(variants, previous)) variantSelect.value = previous;
  }

  function currentSpecialties(school, grade, track) {
    var info = schools[school] || {};
    if (!info.specialties_by_grade_track || !info.specialties_by_grade_track[grade]) return null;
    return info.specialties_by_grade_track[grade][track] || null;
  }

  function currentSpecialtyLabel(school, grade) {
    var info = schools[school] || {};
    if (info.specialty_label_by_grade && info.specialty_label_by_grade[grade]) return info.specialty_label_by_grade[grade];
    return 'Ειδικότητα';
  }

  function refreshSpecialties() {
    var school = schoolSelect.value;
    var grade = gradeSelect.value;
    var track = trackField.hidden ? '' : trackSelect.value;
    var specialty = specialtyField.hidden ? '' : specialtySelect.value;
    var specialties = currentSpecialties(school, grade, track);
    var previous = specialtySelect.value;
    specialtySelect.innerHTML = '';

    if (!specialties || !Object.keys(specialties).length) {
      specialtyField.hidden = true;
      return;
    }

    specialtyField.hidden = false;
    specialtyLabel.textContent = currentSpecialtyLabel(school, grade);
    Object.keys(specialties).forEach(function (code) {
      var option = document.createElement('option');
      option.value = code;
      option.textContent = specialties[code];
      specialtySelect.appendChild(option);
    });
    if (Object.prototype.hasOwnProperty.call(specialties, previous)) specialtySelect.value = previous;
  }

  function refreshTracks() {
    var school = schoolSelect.value;
    var grade = gradeSelect.value;
    var tracks = currentTracks(school, grade);
    var previous = trackSelect.value;
    trackSelect.innerHTML = '';

    if (!tracks || !Object.keys(tracks).length) {
      trackField.hidden = true;
      variantField.hidden = true;
      variantSelect.innerHTML = '';
      specialtyField.hidden = true;
      specialtySelect.innerHTML = '';
      return;
    }

    trackField.hidden = false;
    trackLabel.textContent = currentTrackLabel(school, grade);
    Object.keys(tracks).forEach(function (code) {
      var option = document.createElement('option');
      option.value = code;
      option.textContent = tracks[code];
      trackSelect.appendChild(option);
    });
    if (Object.prototype.hasOwnProperty.call(tracks, previous)) trackSelect.value = previous;
    refreshSpecialties();
    refreshVariants();
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
    refreshTracks();
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

  function renderSummary(school, grade, track, variant, specialty) {
    summary.innerHTML = '';
    var info = schools[school] && schools[school].program ? schools[school].program[grade] : null;
    if (!info) return;
    var html = '';
    if (info.parts) {
      Object.keys(info.parts).forEach(function (label) {
        html += '<span>' + esc(label) + ': <strong>' + esc(info.parts[label]) + '</strong> ώρες</span>';
      });
    }
    var tracks = currentTracks(school, grade);
    if (track && tracks && tracks[track]) {
      html += '<span>' + esc(currentTrackLabel(school, grade)) + ': <strong>' + esc(tracks[track]) + '</strong></span>';
    }
    var variants = currentVariants(school, grade, track);
    if (variant && variants && variants[variant]) {
      html += '<span>' + esc(currentVariantLabel(school, grade, track)) + ': <strong>' + esc(variants[variant]) + '</strong></span>';
    }
    var specialties = currentSpecialties(school, grade, track);
    if (specialty && specialties && specialties[specialty]) {
      html += '<span>' + esc(currentSpecialtyLabel(school, grade)) + ': <strong>' + esc(specialties[specialty]) + '</strong></span>';
    }
    html += '<span class="total">Σύνολο: ' + esc(info.total) + ' ώρες</span>';
    summary.innerHTML = html;
  }

  function combineReligionEthics(rows) {
    var seen = {};
    var combined = [];

    rows.forEach(function (row) {
      var slot = row.slot_id || '';
      if (!/religion_ethics$/.test(slot)) {
        combined.push(row);
        return;
      }
      if (seen[slot]) return;
      seen[slot] = true;

      var peers = rows.filter(function (candidate) { return candidate.slot_id === slot; });
      if (peers.length < 2) {
        combined.push(row);
        return;
      }

      var publicRow = {};
      Object.keys(row).forEach(function (key) { publicRow[key] = row[key]; });
      publicRow.subject = 'Θρησκευτικά / Ηθική';
      publicRow.conditionText = '';
      publicRow.noteText = 'Η Ηθική διδάσκεται στους/στις μαθητές/ήτριες που απαλλάσσονται από το μάθημα των Θρησκευτικών.';
      publicRow.mode = '';
      combined.push(publicRow);
    });

    return combined;
  }

  function hoursBadgeText(row) {
    var text = String(row.hoursText == null ? '' : row.hoursText);
    // Στα επαγγελματικά μαθήματα το hours_display περιέχει ήδη την κατανομή
    // Θ/Ε/Σ/ΠΑ και δεν χρειάζεται δεύτερη κατάληξη «ώρ.».
    return /[ΘΕΣ]|ΠΑ/.test(text) ? text : text + ' ώρ.';
  }

  function render() {
    var school = schoolSelect.value;
    var grade = gradeSelect.value;
    var track = trackField.hidden ? '' : trackSelect.value;
    var variant = variantField.hidden ? '' : variantSelect.value;
    var specialty = specialtyField.hidden ? '' : specialtySelect.value;
    if (!school || !grade) {
      results.innerHTML = '<p class="help">Επίλεξε τύπο σχολείου και τάξη.</p>';
      summary.innerHTML = '';
      return;
    }

    renderSummary(school, grade, track, variant, specialty);

    var rows = allRows.map(function (row) {
      if (row.school !== school) return null;
      if (row.track && row.track !== track) return null;
      if (row.variant && row.variant !== variant) return null;
      if (row.specialty && row.specialty !== specialty) return null;
      return rowForGrade(row, grade);
    }).filter(Boolean);

    rows = combineReligionEthics(rows);

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
      var groupNote = '';
      grouped[group].some(function (row) {
        if (row.group_note) { groupNote = row.group_note; return true; }
        return false;
      });
      if (groupNote) html += '<p class="timetable-group-note">' + esc(groupNote) + '</p>';
      grouped[group].forEach(function (row) {
        var details = [];
        if (row.section) {
          details.push('<div class="timetable-course-detail timetable-course-section"><span class="timetable-detail-label">Ενότητα:</span> ' + esc(row.section) + '</div>');
        }
        if (row.conditionText) {
          details.push('<div class="timetable-course-detail timetable-course-condition"><span class="timetable-detail-label">Προϋπόθεση:</span> ' + esc(row.conditionText) + '</div>');
        }
        if (row.noteText) {
          details.push('<div class="timetable-course-detail timetable-course-note">' + esc(row.noteText) + '</div>');
        }
        if (row.mode === 'alternative') {
          details.push('<div class="timetable-course-detail timetable-course-mode">Εναλλακτική διδασκαλία στην ίδια ωριαία ζώνη</div>');
        }
        if (row.mode === 'choice') {
          details.push('<div class="timetable-course-detail timetable-course-mode">Επιλογή στην ίδια ωριαία ζώνη</div>');
        }
        html += '<div class="result-row timetable-course-row">'
          + '<div class="timetable-course-content">'
          + '<div class="timetable-course-title">' + esc(row.subject) + '</div>'
          + (details.length ? '<div class="timetable-course-meta">' + details.join('') + '</div>' : '')
          + '</div>'
          + '<span class="hours-badge">' + esc(hoursBadgeText(row)) + '</span>'
          + '</div>';
      });
      html += '</section>';
    });

    results.innerHTML = html || '<p class="help">Δεν υπάρχουν καταχωρισμένα μαθήματα για την επιλογή αυτή.</p>';
  }

  schoolSelect.addEventListener('change', refreshGrades);
  gradeSelect.addEventListener('change', function () { refreshTracks(); render(); });
  trackSelect.addEventListener('change', function () { refreshSpecialties(); refreshVariants(); render(); });
  variantSelect.addEventListener('change', render);
  specialtySelect.addEventListener('change', render);
  refreshGrades();
}());
</script>
</body>
</html>
