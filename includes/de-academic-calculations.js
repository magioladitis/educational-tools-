/*
 * Academic scoring for ASEP 5EA/2022 (DE EAE).
 * Source: Chapter C, FEK ASEP 19/18.04.2022.
 */
(function (global) {
  'use strict';

  function number(value) {
    var n = Number(String(value == null ? '' : value).trim().replace(',', '.'));
    return Number.isFinite(n) ? n : 0;
  }

  function cap(value, max) {
    return Math.min(Math.max(0, number(value)), max);
  }

  function integer(value, max) {
    return Math.min(Math.max(0, Math.floor(number(value))), max);
  }

  function round2(value) {
    return Math.round((number(value) + Number.EPSILON) * 100) / 100;
  }

  function calculate(options) {
    options = options || {};
    var grade = number(options.degreeGrade);
    var validGrade = grade >= 10 && grade <= 20;
    var degreePoints = validGrade ? Math.min(50, round2(grade * 2.5)) : 0;
    var workExperienceYears = integer(options.workExperienceYears, 5);
    var workExperiencePoints = workExperienceYears * 4;
    var languagePoints = cap(options.languagePoints, 20);
    var computerPoints = options.computer ? 20 : 0;
    var trainingPoints = options.training ? 10 : 0;
    var raw = degreePoints + workExperiencePoints + languagePoints + computerPoints + trainingPoints;
    var points = Math.min(raw, 120);
    var warnings = [];

    if (options.degreePresent && !validGrade) {
      warnings.push('Ο βαθμός βασικού τίτλου πρέπει να βρίσκεται στην 20βάθμια κλίμακα 10–20.');
    }
    if (number(options.workExperienceYears) > 5) {
      warnings.push('Η μοριοδοτούμενη πρόσθετη εργασιακή εμπειρία περιορίστηκε στα 5 έτη.');
    }
    if (raw > 120) {
      warnings.push('Στα Ακαδημαϊκά Προσόντα εφαρμόστηκε το ανώτατο όριο των 120 μορίων.');
    }

    return global.EducationCore.createScoreResult(raw, points, {
      degreeGrade: validGrade ? grade : 0,
      degreeValid: validGrade,
      degreePoints: degreePoints,
      workExperienceYears: workExperienceYears,
      workExperiencePoints: workExperiencePoints,
      languagePoints: languagePoints,
      computerPoints: computerPoints,
      trainingPoints: trainingPoints,
      warnings: warnings
    }, { raw: true });
  }

  global.DEAcademic = Object.freeze({ calculate: calculate });
})(window);
