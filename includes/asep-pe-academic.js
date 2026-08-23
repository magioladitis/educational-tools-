/*
 * Shared ASEP PE academic UI/controller.
 * Scoring: EducationAcademic.
 * Languages: AsepLanguageSelector / EducationLanguages.
 * Proof widgets remain presentation-only.
 */
(function (global) {
  'use strict';

  function byId(id) {
    return id ? document.getElementById(id) : null;
  }

  function getContainer(ref) {
    return typeof ref === 'string' ? byId(ref) : ref;
  }

  function idOf(container, key, fallback) {
    return container && container.dataset[key] ? container.dataset[key] : fallback;
  }

  function valueOf(id) {
    var el = byId(id);
    if (!el) return '';
    if (el.type === 'checkbox') return el.checked ? 'yes' : 'no';
    return el.value;
  }

  function yes(id) {
    var el = byId(id);
    if (!el || el.disabled) return false;
    if (el.type === 'checkbox') return !!el.checked;
    return el.value === 'yes';
  }

  function number(value) {
    var n = Number(String(value == null ? '' : value).trim().replace(',', '.'));
    return Number.isFinite(n) ? n : 0;
  }

  function specialtyId(container) { return idOf(container, 'specialtyId', 'specialty'); }
  function degreeId(container) { return idOf(container, 'degreeId', 'degreeGrade'); }
  function secondDegreeId(container) { return idOf(container, 'secondDegreeId', 'secondDegree'); }
  function phdId(container) { return idOf(container, 'phdId', 'phd'); }
  function mscId(container) { return idOf(container, 'mscId', 'mscCount'); }
  function languageId(container) { return idOf(container, 'languageId', 'asepLanguages'); }
  function computerId(container) { return idOf(container, 'computerId', 'computer'); }
  function trainingId(container) { return idOf(container, 'trainingId', 'training'); }
  function trainingProofId(container) { return idOf(container, 'trainingProofId', 'trainingProof'); }

  function sync(containerRef) {
    var container = getContainer(containerRef);
    if (!container) return null;

    var specialty = valueOf(specialtyId(container));
    var computer = byId(computerId(container));
    if (computer) {
      var excluded = specialty === 'ΠΕ86';
      computer.disabled = excluded;
      if (excluded) {
        if (computer.type === 'checkbox') computer.checked = false;
        else computer.value = 'no';
      }
    }

    if (global.AsepLanguageSelector) global.AsepLanguageSelector.sync(languageId(container));
    if (global.AsepComputerProof) global.AsepComputerProof.syncAll();
    if (global.TrainingProof) global.TrainingProof.syncAll();
    return getState(container);
  }

  function getState(containerRef) {
    var container = getContainer(containerRef);
    if (!container) return null;
    var languages = global.AsepLanguageSelector
      ? global.AsepLanguageSelector.calculate(languageId(container))
      : { points: 0, details: [], warnings: [] };

    return {
      specialty: valueOf(specialtyId(container)),
      degreeGrade: valueOf(degreeId(container)),
      secondDegree: yes(secondDegreeId(container)),
      phd: yes(phdId(container)),
      mscCount: parseInt(valueOf(mscId(container)), 10) || 0,
      languages: languages,
      computer: yes(computerId(container)),
      training: yes(trainingId(container))
    };
  }

  function validate(containerRef) {
    var container = getContainer(containerRef);
    var state = getState(container);
    if (!state) return { valid: false, specialty: '', degreeGrade: 0, degreeValid: false };
    var grade = number(state.degreeGrade);
    return {
      valid: !!state.specialty && grade >= 5 && grade <= 10,
      specialty: state.specialty,
      degreeGrade: grade,
      degreeValid: grade >= 5 && grade <= 10
    };
  }

  function calculate(containerRef) {
    var container = getContainer(containerRef);
    if (!container) throw new Error('Δεν βρέθηκε το κοινό block ακαδημαϊκών προσόντων ΠΕ.');
    sync(container);
    var state = getState(container);
    return global.EducationAcademic.calculate({
      specialty: state.specialty,
      degreeGrade: state.degreeGrade,
      secondDegree: state.secondDegree,
      phd: state.phd,
      mscCount: state.mscCount,
      languagePoints: state.languages.points,
      languageDetails: state.languages.details,
      languageWarnings: state.languages.warnings,
      computer: state.computer,
      training: state.training
    });
  }

  function trainingWarning(containerRef) {
    var container = getContainer(containerRef);
    if (!container || !global.TrainingProof) return '';
    return global.TrainingProof.warning(trainingProofId(container));
  }

  function trainingSummary(containerRef) {
    var container = getContainer(containerRef);
    if (!container || !global.TrainingProof) return '';
    return global.TrainingProof.summary(trainingProofId(container));
  }

  function reset(containerRef, options) {
    var container = getContainer(containerRef);
    if (!container) return;

    var degree = byId(degreeId(container));
    if (degree) degree.value = '';

    [secondDegreeId(container), phdId(container), computerId(container), trainingId(container)].forEach(function (id) {
      var el = byId(id);
      if (!el) return;
      if (el.type === 'checkbox') el.checked = false;
      else el.value = 'no';
    });

    var msc = byId(mscId(container));
    if (msc) msc.value = '0';

    if (global.AsepLanguageSelector) {
      global.AsepLanguageSelector.reset(languageId(container), { silent: true });
    }

    var proof = byId(trainingProofId(container));
    if (proof) {
      proof.querySelectorAll('input[type="radio"]').forEach(function (radio) { radio.checked = false; });
    }

    sync(container);
    if (!(options && options.silent)) emitChange(container);
  }

  function emitChange(container) {
    if (!container) return;
    container.dispatchEvent(new CustomEvent('asep-pe-academic-change', {
      bubbles: true,
      detail: getState(container)
    }));
  }

  function init(container) {
    if (!container || container.dataset.peAcademicReady === '1') return;
    container.dataset.peAcademicReady = '1';
    var specialty = byId(specialtyId(container));
    if (specialty) specialty.addEventListener('change', function () { sync(container); emitChange(container); });
    sync(container);
  }

  function initAll() {
    document.querySelectorAll('[data-component="asep-pe-academic"]').forEach(init);
  }

  global.AsepPeAcademic = Object.freeze({
    initAll: initAll,
    sync: sync,
    getState: getState,
    validate: validate,
    calculate: calculate,
    reset: reset,
    trainingWarning: trainingWarning,
    trainingSummary: trainingSummary
  });

  initAll();
  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', initAll);
})(typeof window !== 'undefined' ? window : globalThis);
