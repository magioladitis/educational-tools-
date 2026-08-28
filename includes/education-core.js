/*
 * Educational Tools — shared core contracts.
 *
 * Responsibilities:
 * - canonical specialty codes for shared engines (ΠΕ / ΤΕ / ΔΕ),
 * - canonical score-result shape: rawPoints / points / details / warnings,
 * - backwards-compatible aliases only where a caller explicitly requests them.
 */
(function (global) {
  'use strict';

  var MAX_SERVICE_YEARS = 50;
  var MAX_SERVICE_MONTHS = MAX_SERVICE_YEARS * 12;

  function finiteNumber(value) {
    var n = Number(value);
    return Number.isFinite(n) ? n : 0;
  }

  function clampServiceYears(value) {
    return Math.min(MAX_SERVICE_YEARS, Math.max(0, finiteNumber(value)));
  }

  function clampServiceMonths(value) {
    return Math.min(MAX_SERVICE_MONTHS, Math.max(0, finiteNumber(value)));
  }

  function decimalNumber(value) {
    var n = Number(String(value == null ? '' : value).trim().replace(',', '.'));
    return Number.isFinite(n) ? n : NaN;
  }

  function inputBound(element, name, explicitValue) {
    if (explicitValue !== undefined && explicitValue !== null && explicitValue !== '') {
      var explicitNumber = decimalNumber(explicitValue);
      return Number.isFinite(explicitNumber) ? explicitNumber : null;
    }
    if (!element || !element.getAttribute) return null;
    var raw = element.getAttribute(name);
    if (raw === null || raw === '') raw = element.getAttribute('data-' + name);
    var parsed = decimalNumber(raw);
    return Number.isFinite(parsed) ? parsed : null;
  }

  /*
   * Normalize a numeric input against its declared bounds.
   * During typing we clamp only the upper bound: clamping min=10 on the first
   * keystroke would make it impossible to type values such as 17.
   * On change/blur (commit phase) both lower and upper bounds are enforced.
   */
  function normalizeBoundedInput(element, options) {
    options = options || {};
    if (!element) return { empty: true, valid: false, changed: false, value: null };

    var rawText = String(element.value == null ? '' : element.value).trim();
    if (rawText === '') return { empty: true, valid: true, changed: false, value: null };

    var value = decimalNumber(rawText);
    var commit = options.phase !== 'input';
    if (!Number.isFinite(value)) {
      if (commit) element.value = '';
      return { empty: false, valid: false, changed: commit, value: null };
    }

    var min = inputBound(element, 'min', options.min);
    var max = inputBound(element, 'max', options.max);
    var normalized = value;
    if (max !== null && normalized > max) normalized = max;
    if (commit && min !== null && normalized < min) normalized = min;

    var changed = normalized !== value;
    if (changed) element.value = String(normalized);
    return { empty: false, valid: true, changed: changed, value: normalized, min: min, max: max };
  }

  function bindBoundedNumberInput(element, options) {
    options = options || {};
    if (!element || !element.addEventListener) return element;
    if (element.dataset && element.dataset.eduBoundedNumberReady === '1') return element;
    if (element.dataset) element.dataset.eduBoundedNumberReady = '1';

    element.addEventListener('input', function () {
      normalizeBoundedInput(element, Object.assign({}, options, { phase: 'input' }));
    });
    element.addEventListener('change', function () {
      normalizeBoundedInput(element, Object.assign({}, options, { phase: 'commit' }));
    });
    element.addEventListener('blur', function () {
      normalizeBoundedInput(element, Object.assign({}, options, { phase: 'commit' }));
    });
    return element;
  }

  function normalizeSpecialtyCode(value) {
    var code = String(value == null ? '' : value)
      .trim()
      .toUpperCase()
      .replace(/\s+/g, '');

    if (!code) return '';

    // Accept Greek, Latin and mixed-script PE/TE/DE prefixes.
    code = code.replace(/^(?:PE|PΕ|ΠE|ΠΕ)/, 'ΠΕ');
    code = code.replace(/^(?:TE|TΕ|ΤE|ΤΕ)/, 'ΤΕ');
    code = code.replace(/^(?:DE|DΕ|ΔE|ΔΕ)/, 'ΔΕ');
    return code;
  }

  function toLatinSpecialtyCode(value) {
    var code = normalizeSpecialtyCode(value);
    if (code.indexOf('ΠΕ') === 0) return 'PE' + code.slice(2);
    if (code.indexOf('ΤΕ') === 0) return 'TE' + code.slice(2);
    if (code.indexOf('ΔΕ') === 0) return 'DE' + code.slice(2);
    return code;
  }

  function createScoreResult(rawPoints, points, extra, aliases) {
    var result = Object.assign({}, extra || {});
    result.rawPoints = finiteNumber(rawPoints);
    result.points = finiteNumber(points);
    result.details = Array.isArray(result.details) ? result.details : [];
    result.warnings = Array.isArray(result.warnings) ? result.warnings : [];

    aliases = aliases || {};
    if (aliases.raw) result.raw = result.rawPoints;
    if (aliases.total) result.total = result.points;
    return result;
  }

  global.EducationCore = Object.freeze({
    MAX_SERVICE_YEARS: MAX_SERVICE_YEARS,
    MAX_SERVICE_MONTHS: MAX_SERVICE_MONTHS,
    clampServiceYears: clampServiceYears,
    clampServiceMonths: clampServiceMonths,
    finiteNumber: finiteNumber,
    normalizeBoundedInput: normalizeBoundedInput,
    bindBoundedNumberInput: bindBoundedNumberInput,
    normalizeSpecialtyCode: normalizeSpecialtyCode,
    toLatinSpecialtyCode: toLatinSpecialtyCode,
    createScoreResult: createScoreResult
  });
})(typeof window !== 'undefined' ? window : globalThis);
