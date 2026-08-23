/*
 * Shared ASEP TE academic UI/controller for 1GT/2024 and 4EA/2025.
 * Scoring remains in TEAcademic (te-academic-calculations.js).
 */
(function (global) {
  'use strict';

  function byId(id) { return id ? document.getElementById(id) : null; }
  function getContainer(ref) { return typeof ref === 'string' ? byId(ref) : ref; }
  function idOf(container, key, fallback) {
    return container && container.dataset[key] ? container.dataset[key] : fallback;
  }
  function valueOf(id) { var el = byId(id); return el ? el.value : ''; }
  function checked(id) { var el = byId(id); return !!(el && !el.disabled && el.checked); }
  function number(value) {
    var n = Number(String(value == null ? '' : value).trim().replace(',', '.'));
    return Number.isFinite(n) ? n : 0;
  }
  function formatDefault(value) {
    var n = Math.round(((Number(value) || 0) + Number.EPSILON) * 100) / 100;
    return n.toLocaleString('el-GR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
  }

  function branchId(c) { return idOf(c, 'branchId', 'branch'); }
  function gradeScaleId(c) { return idOf(c, 'gradeScaleId', 'gradeScale'); }
  function degreeId(c) { return idOf(c, 'degreeId', 'degreeGrade'); }
  function textGradeId(c) { return idOf(c, 'textGradeId', 'te16TextGrade'); }
  function normalizedInfoId(c) { return idOf(c, 'normalizedInfoId', 'normalizedGradeInfo'); }
  function gradeWarningId(c) { return idOf(c, 'gradeWarningId', 'gradeWarning'); }
  function secondTitleId(c) { return idOf(c, 'secondTitleId', 'secondTitle'); }
  function secondTitleLabelId(c) { return idOf(c, 'secondTitleLabelId', 'secondTitleLabel'); }
  function languageId(c) { return idOf(c, 'languageId', 'asepLanguages'); }
  function computerId(c) { return idOf(c, 'computerId', 'computer'); }
  function trainingId(c) { return idOf(c, 'trainingId', 'training'); }
  function trainingProofId(c) { return idOf(c, 'trainingProofId', 'trainingProof'); }
  function subtotalId(c) { return idOf(c, 'subtotalId', 'academicSubtotal'); }

  function extraTrainingIds(c) {
    var raw = c && c.dataset.extraTrainingIds ? c.dataset.extraTrainingIds : '';
    return raw.split(',').map(function (x) { return x.trim(); }).filter(Boolean);
  }

  function branchFamily(value) {
    var v = global.EducationCore.normalizeSpecialtyCode(value);
    if (v.indexOf('ΤΕ16') === 0) return 'TE16';
    if (v.indexOf('ΤΕ01') === 0) return 'TE01';
    if (v.indexOf('ΤΕ02') === 0) return 'TE02';
    return '';
  }

  function setSecondTitleLabel(c, family) {
    var label = byId(secondTitleLabelId(c));
    if (!label) return;
    label.textContent = family === 'TE16'
      ? 'Δεύτερο πτυχίο από το οποίο προκύπτει μουσική ειδίκευση, αναγνωρισμένου μη Ανώτατου Εκπαιδευτικού Ιδρύματος'
      : 'Πτυχίο επιπέδου 5 / Ι.Ε.Κ. ίδιας ειδικότητας';
  }

  function syncGradeUI(c) {
    var scaleEl = byId(gradeScaleId(c));
    var degree = byId(degreeId(c));
    var numericWrap = byId('numericGradeWrap');
    var textWrap = byId('te16TextWrap');
    if (!scaleEl) return;

    var scale = scaleEl.value;
    var textual = scale === 'te16text';
    if (numericWrap) numericWrap.classList.toggle('hidden', textual);
    if (textWrap) textWrap.classList.toggle('hidden', !textual);

    if (!textual && degree) {
      var min = scale === '10' ? 5 : 10;
      var max = scale === '10' ? 10 : 20;
      degree.min = String(min);
      degree.max = String(max);
      degree.placeholder = scale === '10'
        ? 'π.χ. 7,50'
        : (c.dataset.degreePlaceholder20 || 'π.χ. 15,00');
    }
  }

  function sync(c) {
    c = getContainer(c);
    if (!c) return null;

    var family = branchFamily(valueOf(branchId(c)));
    var scale = byId(gradeScaleId(c));
    setSecondTitleLabel(c, family);

    if (scale) {
      if (scale.dataset.auto !== 'off') scale.value = family === 'TE16' ? '10' : '20';
      var textOption = Array.from(scale.options || []).find(function (o) { return o.value === 'te16text'; });
      if (textOption) textOption.disabled = family !== 'TE16';
      if (family !== 'TE16' && scale.value === 'te16text') scale.value = '20';
    }

    syncGradeUI(c);
    if (global.AsepLanguageSelector) global.AsepLanguageSelector.sync(languageId(c));
    if (global.AsepComputerProof) global.AsepComputerProof.syncAll();
    if (global.TrainingProof) global.TrainingProof.syncAll();
    return readState(c);
  }

  function trainingActive(c) {
    if (checked(trainingId(c))) return true;
    return extraTrainingIds(c).some(function (id) { return checked(id); });
  }

  function readState(c) {
    c = getContainer(c);
    if (!c) return null;
    var scale = valueOf(gradeScaleId(c));
    var rawGrade = number(valueOf(degreeId(c)));
    var degreeEl = byId(degreeId(c));
    var hasGrade = !!(degreeEl && degreeEl.value !== '');
    var min = scale === '10' ? 5 : (scale === '20' ? 10 : 0);
    var max = scale === '10' ? 10 : 20;
    var valid = scale === 'te16text' || (hasGrade && rawGrade >= min && rawGrade <= max);
    var languages = global.AsepLanguageSelector
      ? global.AsepLanguageSelector.calculate(languageId(c))
      : { points: 0, accepted: [], warnings: [] };

    return {
      branch: valueOf(branchId(c)),
      family: branchFamily(valueOf(branchId(c))),
      gradeScale: scale,
      rawDegreeGrade: rawGrade,
      degreePresent: hasGrade,
      minDegreeGrade: min,
      maxDegreeGrade: max,
      degreeValid: valid,
      te16TextGrade: number(valueOf(textGradeId(c))),
      secondTitle: checked(secondTitleId(c)),
      languages: languages,
      computer: checked(computerId(c)),
      training: trainingActive(c)
    };
  }

  function calculate(containerRef, formatter) {
    var c = getContainer(containerRef);
    if (!c) throw new Error('Δεν βρέθηκε το κοινό block ακαδημαϊκών προσόντων ΤΕ.');
    sync(c);
    var state = readState(c);
    var result = global.TEAcademic.calculate({
      gradeScale: state.gradeScale,
      degreeGrade: state.degreeValid ? state.rawDegreeGrade : 0,
      te16TextGrade: state.te16TextGrade,
      secondTitle: state.secondTitle,
      languagePoints: state.languages.points,
      computer: state.computer,
      training: state.training
    });

    var fmt = typeof formatter === 'function' ? formatter : formatDefault;
    var info = byId(normalizedInfoId(c));
    var warning = byId(gradeWarningId(c));
    if (state.gradeScale !== 'te16text' && state.degreePresent && !state.degreeValid) {
      if (info) info.textContent = 'Μη έγκυρος βαθμός: επιτρέπεται ' + state.minDegreeGrade + '–' + state.maxDegreeGrade + '. Δεν υπολογίζονται μόρια βαθμού.';
      if (warning) warning.classList.remove('hidden');
    } else {
      if (info) info.textContent = 'Αναγμένος βαθμός: ' + fmt(result.normalizedGrade) + ' / 20 · Μόρια βαθμού: ' + fmt(result.degreePoints) + ' / 60';
      if (warning) warning.classList.add('hidden');
    }

    var subtotal = byId(subtotalId(c));
    if (subtotal) subtotal.textContent = fmt(result.points) + ' / 120';

    return {
      result: result,
      languages: state.languages,
      state: state,
      points: result.points
    };
  }

  function getState(containerRef, formatter) { return calculate(containerRef, formatter); }

  function clampDegree(c) {
    c = getContainer(c);
    if (!c) return;
    var degree = byId(degreeId(c));
    var scale = valueOf(gradeScaleId(c));
    if (!degree || degree.value === '' || scale === 'te16text') return;
    var min = scale === '10' ? 5 : 10;
    var max = scale === '10' ? 10 : 20;
    var raw = Number(String(degree.value).trim().replace(',', '.'));
    if (!Number.isFinite(raw)) { degree.value = ''; return; }
    degree.value = String(Math.min(max, Math.max(min, raw)));
  }

  function trainingWarning(containerRef) {
    var c = getContainer(containerRef);
    return c && global.TrainingProof ? global.TrainingProof.warning(trainingProofId(c)) : '';
  }

  function trainingSummary(containerRef) {
    var c = getContainer(containerRef);
    return c && global.TrainingProof ? global.TrainingProof.summary(trainingProofId(c)) : '';
  }

  function reset(containerRef, options) {
    var c = getContainer(containerRef);
    if (!c) return;
    var scale = byId(gradeScaleId(c));
    if (scale) { scale.dataset.auto = 'on'; scale.value = '20'; }
    var degree = byId(degreeId(c));
    if (degree) degree.value = '';
    var textGrade = byId(textGradeId(c));
    if (textGrade) textGrade.value = '0';
    [secondTitleId(c), computerId(c), trainingId(c)].concat(extraTrainingIds(c)).forEach(function (id) {
      var el = byId(id);
      if (el && el.type === 'checkbox') el.checked = false;
    });
    if (global.AsepLanguageSelector) global.AsepLanguageSelector.reset(languageId(c), { silent: true });
    var proof = byId(trainingProofId(c));
    if (proof) proof.querySelectorAll('input[type="radio"]').forEach(function (r) { r.checked = false; });
    sync(c);
    if (!(options && options.silent)) emitChange(c);
  }

  function emitChange(c) {
    if (!c) return;
    c.dispatchEvent(new CustomEvent('asep-te-academic-change', { bubbles: true, detail: readState(c) }));
  }

  function init(c) {
    if (!c || c.dataset.teAcademicReady === '1') return;
    c.dataset.teAcademicReady = '1';
    var branch = byId(branchId(c));
    var scale = byId(gradeScaleId(c));
    var degree = byId(degreeId(c));
    if (branch) branch.addEventListener('change', function () {
      if (scale) scale.dataset.auto = 'on';
      sync(c);
      emitChange(c);
    });
    if (scale) scale.addEventListener('change', function () {
      scale.dataset.auto = 'off';
      syncGradeUI(c);
      emitChange(c);
    });
    if (degree) degree.addEventListener('change', function () {
      clampDegree(c);
      emitChange(c);
    });
    sync(c);
  }

  function initAll() {
    document.querySelectorAll('[data-component="asep-te-academic"]').forEach(init);
  }

  global.AsepTeAcademic = Object.freeze({
    initAll: initAll,
    sync: sync,
    getState: getState,
    calculate: calculate,
    reset: reset,
    trainingWarning: trainingWarning,
    trainingSummary: trainingSummary,
    branchFamily: branchFamily
  });

  initAll();
  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', initAll);
})(typeof window !== 'undefined' ? window : globalThis);
