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

  var MAX_SERVICE_YEARS = 40;
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
    normalizeSpecialtyCode: normalizeSpecialtyCode,
    toLatinSpecialtyCode: toLatinSpecialtyCode,
    createScoreResult: createScoreResult
  });
})(typeof window !== 'undefined' ? window : globalThis);
