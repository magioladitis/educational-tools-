/*
 * Browser UI controller for orologio-programma-mathimaton.php.
 * PHP provides timetable/policy data as a neutral JSON payload; this controller
 * owns selectors, filtering and result rendering.
 */
(function () {
  'use strict';

  var dataTemplate = document.getElementById('weeklyTimetableData');
  var dataText = dataTemplate && dataTemplate.content ? dataTemplate.content.textContent : '{}';
  var payload = JSON.parse(dataText || '{}');
  var schools = payload.schools || {};
  var allRows = payload.rows || [];
  var ethicsPolicy = payload.ethicsPolicy || {scope_school_codes: [], minimum_exempt_students_per_grade: 10, deadline_day_after_classes_start: 5};
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
    html += info.total_display
      ? '<span class="total">' + esc(info.total_display) + '</span>'
      : '<span class="total">Σύνολο: ' + esc(info.total) + ' ώρες</span>';
    summary.innerHTML = html;
  }

  function combineReligionEthics(rows, school) {
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
      var ethicsInScope = ethicsPolicy.scope_school_codes.indexOf(school) !== -1;
      if (ethicsInScope) {
        publicRow.noteText = 'Η Ηθική απευθύνεται αποκλειστικά στους/στις απαλλασσόμενους/ες από τα Θρησκευτικά. Από το 2026–2027 απαιτούνται τουλάχιστον ' + ethicsPolicy.minimum_exempt_students_per_grade + ' απαλλασσόμενοι/ες ανά τάξη, με το όριο να έχει συμπληρωθεί έως την ' + ethicsPolicy.deadline_day_after_classes_start + 'η ημέρα από την έναρξη των μαθημάτων. Αν λειτουργεί ένα τμήμα, τα δύο μαθήματα διδάσκονται την ίδια ώρα σε διακριτές αίθουσες.';
      } else {
        publicRow.noteText = 'Η Ηθική προβλέπεται για τους/τις μαθητές/ήτριες που απαλλάσσονται από τα Θρησκευτικά. Ο ειδικός κανόνας συγκρότησης με τουλάχιστον 10 απαλλασσόμενους/ες της Υ.Α. 108070/Δ2/2026 αφορά ρητά Γυμνάσιο/Γενικό Λύκειο και δεν εφαρμόζεται αυτόματα σε αυτή τη δομή.';
      }
      publicRow.mode = '';
      combined.push(publicRow);
    });

    return combined;
  }

  function hoursBadgeText(row) {
    var text = String(row.hoursText == null ? '' : row.hoursText);
    // Στα επαγγελματικά μαθήματα το hours_display περιέχει ήδη την κατανομή
    // Θ/Ε/Σ/ΠΑ και δεν χρειάζεται δεύτερη κατάληξη «ώρ.».
    if (!/^\d/.test(text)) return text;
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

    rows = combineReligionEthics(rows, school);

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
