/* Shared UI/controller for ASEP 5EA/2022 DE academic scoring. */
(function (global) {
  'use strict';

  function byId(id) { return id ? document.getElementById(id) : null; }
  function resolve(ref) { return typeof ref === 'string' ? byId(ref) : ref; }
  function idOf(c, key, fallback) { return c && c.dataset[key] ? c.dataset[key] : fallback; }
  function number(value) {
    var n = Number(String(value == null ? '' : value).trim().replace(',', '.'));
    return Number.isFinite(n) ? n : 0;
  }
  function checked(id) { var el = byId(id); return !!(el && !el.disabled && el.checked); }
  function extraTrainingIds(c) {
    var raw = c && c.dataset.extraTrainingIds ? c.dataset.extraTrainingIds : '';
    return raw.split(',').map(function (id) { return id.trim(); }).filter(Boolean);
  }
  function anyExtraTraining(c) {
    return extraTrainingIds(c).some(function (id) { var el = byId(id); return !!(el && el.checked); });
  }
  function trainingActive(c) { return checked(idOf(c, 'trainingId', 'training')) || anyExtraTraining(c); }

  function syncTrainingInheritance(c) {
    if (!c) return;
    var training = byId(idOf(c, 'trainingId', 'training'));
    if (!training || training.type !== 'checkbox') return;
    var inherited = anyExtraTraining(c);
    var note = c.querySelector('[data-de-training-inherited-note]');
    if (note) note.classList.toggle('hidden', !inherited);
    if (inherited) {
      if (!training.checked) training.dataset.autoInherited = '1';
      training.checked = true;
      training.disabled = true;
      training.setAttribute('aria-describedby', 'trainingInheritedHelp');
    } else {
      training.disabled = false;
      training.removeAttribute('aria-describedby');
      if (training.dataset.autoInherited === '1') training.checked = false;
      delete training.dataset.autoInherited;
    }
    if (global.TrainingProof && typeof global.TrainingProof.syncAll === 'function') global.TrainingProof.syncAll();
  }
  function fmt(value) {
    var n = Math.round(((Number(value) || 0) + Number.EPSILON) * 100) / 100;
    return n.toLocaleString('el-GR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
  }

  function sanitizeYears(el) {
    if (!el || el.value === '') return;
    var value = Math.max(0, Math.floor(number(el.value)));
    value = Math.min(value, 5);
    el.value = String(value);
  }

  function readState(ref) {
    var c = resolve(ref);
    if (!c) return null;
    syncTrainingInheritance(c);
    var degreeId = idOf(c, 'degreeId', 'degreeGrade');
    var workId = idOf(c, 'workId', 'workExperienceYears');
    var languageId = idOf(c, 'languageId', 'asepLanguages');
    var degree = byId(degreeId);
    var work = byId(workId);
    sanitizeYears(work);
    var languages = global.AsepLanguageSelector
      ? global.AsepLanguageSelector.calculate(languageId)
      : { points: 0, accepted: [], warnings: [] };
    return {
      degreePresent: !!(degree && degree.value !== ''),
      degreeGrade: degree ? number(degree.value) : 0,
      workExperienceYears: work ? number(work.value) : 0,
      languages: languages,
      computer: checked(idOf(c, 'computerId', 'computer')),
      training: trainingActive(c)
    };
  }

  function calculate(ref, formatter) {
    var c = resolve(ref);
    if (!c) throw new Error('Δεν βρέθηκε το κοινό block ακαδημαϊκών 5ΕΑ/2022.');
    var state = readState(c);
    var result = global.DEAcademic.calculate({
      degreePresent: state.degreePresent,
      degreeGrade: state.degreeGrade,
      workExperienceYears: state.workExperienceYears,
      languagePoints: state.languages.points,
      computer: state.computer,
      training: state.training
    });
    var f = typeof formatter === 'function' ? formatter : fmt;
    var subtotal = byId(idOf(c, 'subtotalId', 'academicSubtotal'));
    if (subtotal) subtotal.textContent = f(result.points) + ' / 120';
    var warning = byId(idOf(c, 'warningId', 'academicWarning'));
    if (warning) {
      warning.textContent = result.warnings.concat(state.languages.warnings || []).join(' ');
      warning.classList.toggle('hidden', warning.textContent === '');
    }
    return { result: result, state: state, languages: state.languages, points: result.points };
  }

  function reset(ref, options) {
    var c = resolve(ref);
    if (!c) return;
    var degree = byId(idOf(c, 'degreeId', 'degreeGrade'));
    var work = byId(idOf(c, 'workId', 'workExperienceYears'));
    var computer = byId(idOf(c, 'computerId', 'computer'));
    var training = byId(idOf(c, 'trainingId', 'training'));
    if (degree) degree.value = '';
    if (work) work.value = '0';
    if (computer) computer.checked = false;
    if (training) { training.checked = false; training.disabled = false; delete training.dataset.autoInherited; training.removeAttribute('aria-describedby'); }
    extraTrainingIds(c).forEach(function (id) { var el = byId(id); if (el && el.type === 'checkbox') el.checked = false; });
    if (global.AsepLanguageSelector) global.AsepLanguageSelector.reset(idOf(c, 'languageId', 'asepLanguages'), { silent: true });
    var proof = byId(idOf(c, 'trainingProofId', 'trainingProof'));
    if (proof) proof.querySelectorAll('input[type="radio"]').forEach(function (r) { r.checked = false; });
    if (global.TrainingProof) global.TrainingProof.syncAll();
    if (!(options && options.silent)) c.dispatchEvent(new CustomEvent('asep-de-academic-change', { bubbles: true }));
  }

  function trainingSummary(ref) {
    var c = resolve(ref);
    return c && global.TrainingProof ? global.TrainingProof.summary(idOf(c, 'trainingProofId', 'trainingProof')) : '';
  }

  function init(c) {
    if (!c || c.dataset.deAcademicReady === '1') return;
    c.dataset.deAcademicReady = '1';
    var degree = byId(idOf(c, 'degreeId', 'degreeGrade'));
    var work = byId(idOf(c, 'workId', 'workExperienceYears'));
    if (degree && global.EducationCore && global.EducationCore.bindBoundedNumberInput) {
      global.EducationCore.bindBoundedNumberInput(degree, { min: 10, max: 20 });
    }
    if (work) {
      work.addEventListener('input', function () { sanitizeYears(work); });
      work.addEventListener('change', function () { sanitizeYears(work); });
    }
    extraTrainingIds(c).forEach(function (id) {
      var el = byId(id);
      if (el) el.addEventListener('change', function () { syncTrainingInheritance(c); });
    });
    syncTrainingInheritance(c);
  }

  function initAll() { document.querySelectorAll('[data-component="asep-de-academic"]').forEach(init); }

  global.AsepDeAcademic = Object.freeze({
    initAll: initAll,
    readState: readState,
    getState: calculate,
    calculate: calculate,
    reset: reset,
    trainingSummary: trainingSummary,
    syncTrainingInheritance: syncTrainingInheritance
  });

  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', initAll);
  else initAll();
})(typeof window !== 'undefined' ? window : globalThis);
