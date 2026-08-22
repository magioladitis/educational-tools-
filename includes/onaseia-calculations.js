/*
 * Δημόσια Ωνάσεια Σχολεία — βοηθητικοί κανόνες manual ακαδημαϊκών μορίων.
 * v3.20.4
 *
 * Κλάδοι ΠΕ: ελάχιστο 12,5 = βαθμός βασικού τίτλου 5,00 × 2,5.
 * ΤΕ16: το εργαλείο χρησιμοποιεί τα ακαδημαϊκά μόρια 1ΓΤ/2024·
 *       ελάχιστο 30 = βαθμός 5/10 -> 10/20 × 3.
 */
(function (global) {
  'use strict';

  const MANUAL_ACADEMIC_MAX = 120;
  const MANUAL_ACADEMIC_MIN_PE = 12.5;
  const MANUAL_ACADEMIC_MIN_TE16 = 30;

  function greekNumber(value) {
    const normalized = String(value == null ? '' : value).trim().replace(',', '.');
    if (normalized === '') return NaN;
    const n = Number(normalized);
    return Number.isFinite(n) ? n : NaN;
  }

  function manualAcademicMin(specialty) {
    return specialty === 'ΤΕ16' ? MANUAL_ACADEMIC_MIN_TE16 : MANUAL_ACADEMIC_MIN_PE;
  }

  function validateManualAcademicPoints(value, specialty) {
    const raw = String(value == null ? '' : value).trim();
    const min = manualAcademicMin(specialty);
    const max = MANUAL_ACADEMIC_MAX;
    if (raw === '') return { valid: false, reason: 'empty', points: NaN, min, max };
    const points = greekNumber(raw);
    if (!Number.isFinite(points)) return { valid: false, reason: 'nan', points, min, max };
    if (points < min) return { valid: false, reason: 'below-min', points, min, max };
    if (points > max) return { valid: false, reason: 'above-max', points, min, max };
    return { valid: true, reason: 'ok', points, min, max };
  }

  global.OnaseiaAcademic = Object.freeze({
    MANUAL_ACADEMIC_MAX,
    MANUAL_ACADEMIC_MIN_PE,
    MANUAL_ACADEMIC_MIN_TE16,
    greekNumber,
    manualAcademicMin,
    validateManualAcademicPoints
  });
})(typeof window !== 'undefined' ? window : globalThis);
