/*
 * Κοινή ακαδημαϊκή μοριοδότηση κατηγορίας ΤΕ
 * για 1ΓΤ/2024 και 4ΕΑ/2025.
 */
(function (global) {
  "use strict";

  function cap(value, max) {
    return Math.min(Math.max(0, Number(value) || 0), max);
  }

  function normalizeGrade20(scale, numericGrade, te16TextGrade) {
    if (scale === "te16text") {
      let g = Number(te16TextGrade || 0);
      if (!g) g = 5;
      return cap(g * 2, 20);
    }
    const g = Math.max(0, Number(numericGrade) || 0);
    return cap(scale === "10" ? g * 2 : g, 20);
  }

  function calculate(options) {
    const normalizedGrade = normalizeGrade20(
      options.gradeScale,
      options.degreeGrade,
      options.te16TextGrade
    );
    const degreePoints = cap(normalizedGrade * 3, 60);
    const secondTitlePoints = options.secondTitle ? 10 : 0;
    const languagePoints = cap(options.languagePoints || 0, 20);
    const computerPoints = options.computer ? 20 : 0;
    const trainingPoints = options.training ? 10 : 0;
    const raw =
      degreePoints +
      secondTitlePoints +
      languagePoints +
      computerPoints +
      trainingPoints;

    const points = cap(raw, 120);
    const warnings = raw > 120 ? ['Στα Ακαδημαϊκά Προσόντα εφαρμόστηκε το ανώτατο όριο των 120 μορίων.'] : [];
    return global.EducationCore.createScoreResult(raw, points, {
      normalizedGrade,
      degreePoints,
      secondTitlePoints,
      languagePoints,
      computerPoints,
      trainingPoints,
      warnings
    }, { raw: true });
  }

  global.TEAcademic = Object.freeze({
    normalizeGrade20,
    calculate
  });
})(window);
