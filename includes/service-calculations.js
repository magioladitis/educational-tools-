/*
 * Κοινή λογική υπολογισμού εκπαιδευτικής προϋπηρεσίας
 * για 1ΓΕ/2026, 2ΓΕ/2026, 1ΓΤ/2024 και 3ΕΑ/2025.
 *
 * Τρίμηνες συμβάσεις:
 * 2020-2021: έως 8 μήνες
 * 2021-2022: έως 7 μήνες
 *
 * Ψηφιακό Φροντιστήριο:
 * Όλα τα όρια ανά σχολικό έτος και η μετατροπή υπολοίπων ημερών
 * βρίσκονται αποκλειστικά σε αυτό το module.
 */
(function (global) {
  "use strict";

  const RULES = Object.freeze({
    difficultMaxMonths: 60,
    totalMaxPoints: 120,
    threeMonth2020MaxMonths: 8,
    threeMonth2021MaxMonths: 7,
    threeMonthRegularRate: 1.5,
    threeMonthRegularMaxPoints: 10,
    threeMonthDifficultRate: 3,
    threeMonthDifficultMaxPoints: 20,
    publicRate: 1,
    difficultRate: 2,
    privateRate: 0.9,
    digitalRate: 1.5,
    digitalMaxMonthsPerSchoolYear: 10,
    digitalMaxPointsPerSchoolYear: 15,
    digitalSchoolYears: Object.freeze({
      "2024-2025": Object.freeze({ maxMonths: 9, maxDaysAtMaxMonths: 16 }),
      "2025-2026": Object.freeze({ maxMonths: 8, maxDaysAtMaxMonths: 2 })
    })
  });

  function nonNegativeInteger(value) {
    const n = Math.floor(Number(value) || 0);
    return Math.max(0, n);
  }

  function months(value, maxMonths = null) {
    const n = nonNegativeInteger(value);
    return maxMonths === null ? n : Math.min(n, maxMonths);
  }

  function regularPublic(value) {
    const m = months(value);
    return { months: m, points: m * RULES.publicRate };
  }

  function difficult(value) {
    const m = months(value, RULES.difficultMaxMonths);
    return { months: m, points: m * RULES.difficultRate };
  }

  function threeMonthRegular2020(value) {
    const m = months(value, RULES.threeMonth2020MaxMonths);
    return { months: m, points: Math.min(m * RULES.threeMonthRegularRate, RULES.threeMonthRegularMaxPoints) };
  }

  function threeMonthRegular2021(value) {
    const m = months(value, RULES.threeMonth2021MaxMonths);
    return { months: m, points: Math.min(m * RULES.threeMonthRegularRate, RULES.threeMonthRegularMaxPoints) };
  }

  function threeMonthDifficult2020(value) {
    const m = months(value, RULES.threeMonth2020MaxMonths);
    return { months: m, points: Math.min(m * RULES.threeMonthDifficultRate, RULES.threeMonthDifficultMaxPoints) };
  }

  function threeMonthDifficult2021(value) {
    const m = months(value, RULES.threeMonth2021MaxMonths);
    return { months: m, points: Math.min(m * RULES.threeMonthDifficultRate, RULES.threeMonthDifficultMaxPoints) };
  }

  function privateSchool(value) {
    const m = months(value);
    return { months: m, points: m * RULES.privateRate };
  }

  // Legacy API kept for older calculators that still pass one school year as months only.
  function digitalPerSchoolYear(value) {
    const m = months(value, RULES.digitalMaxMonthsPerSchoolYear);
    return { months: m, points: Math.min(m * RULES.digitalRate, RULES.digitalMaxPointsPerSchoolYear) };
  }

  function digitalTutoring(entries) {
    const source = Array.isArray(entries) ? entries : [];
    const activeYears = [];
    const warnings = [];
    const usedYears = Object.create(null);

    source.forEach(function (entry) {
      if (!entry || !entry.schoolYear) return;

      const schoolYear = String(entry.schoolYear);
      const limit = RULES.digitalSchoolYears[schoolYear];
      if (!limit) {
        warnings.push("Το σχολικό έτος " + schoolYear + " δεν υποστηρίζεται στον υπολογισμό του Ψηφιακού Φροντιστηρίου.");
        return;
      }
      if (usedYears[schoolYear]) {
        warnings.push("Το σχολικό έτος " + schoolYear + " δηλώθηκε περισσότερες από μία φορές και υπολογίστηκε μόνο η πρώτη εγγραφή.");
        return;
      }
      usedYears[schoolYear] = true;

      const enteredMonths = nonNegativeInteger(entry.months);
      const m = Math.min(enteredMonths, limit.maxMonths);
      const monthsCapped = enteredMonths > limit.maxMonths;

      const enteredDays = nonNegativeInteger(entry.days);
      const maxDays = m >= limit.maxMonths ? limit.maxDaysAtMaxMonths : 29;
      const d = Math.min(enteredDays, maxDays);
      const daysCapped = enteredDays > maxDays;
      const durationCapped = monthsCapped || daysCapped;

      if (monthsCapped) {
        warnings.push("Στο Ψηφιακό Φροντιστήριο — " + schoolYear.replace("-", "–") + " εφαρμόστηκε το μέγιστο των " + limit.maxMonths + " μηνών.");
      }
      if (daysCapped) {
        warnings.push("Στο Ψηφιακό Φροντιστήριο — " + schoolYear.replace("-", "–") + " εφαρμόστηκε το μέγιστο των " + maxDays + " υπολοίπων ημερών για τους δηλωμένους μήνες.");
      }

      if (m === 0 && d === 0) return;

      const baseRawPoints = m * RULES.digitalRate;
      const basePoints = Math.min(baseRawPoints, RULES.digitalMaxPointsPerSchoolYear);
      const capped = baseRawPoints > basePoints;

      activeYears.push({
        schoolYear: schoolYear,
        label: schoolYear.replace("-", "–"),
        months: m,
        days: d,
        maxMonths: limit.maxMonths,
        maxDaysAtMaxMonths: limit.maxDaysAtMaxMonths,
        baseRawPoints: baseRawPoints,
        basePoints: basePoints,
        durationCapped: durationCapped,
        capped: capped
      });
    });

    const fullMonths = activeYears.reduce(function (sum, entry) { return sum + entry.months; }, 0);
    const totalDays = activeYears.reduce(function (sum, entry) { return sum + entry.days; }, 0);
    const convertedMonths = Math.floor(totalDays / 30);
    const remainingDays = totalDays % 30;
    const basePoints = activeYears.reduce(function (sum, entry) { return sum + entry.basePoints; }, 0);
    const convertedRawPoints = convertedMonths * RULES.digitalRate;
    const maxPoints = activeYears.length * RULES.digitalMaxPointsPerSchoolYear;
    const remainingPointCapacity = Math.max(0, maxPoints - basePoints);
    const convertedPoints = Math.min(convertedRawPoints, remainingPointCapacity);
    const rawPoints = basePoints + convertedRawPoints;
    const points = basePoints + convertedPoints;

    if (convertedRawPoints > convertedPoints && activeYears.length > 0) {
      warnings.push("Στη μετατροπή των υπολοίπων ημερών του Ψηφιακού Φροντιστηρίου εφαρμόστηκε το ανώτατο όριο των 15 μορίων ανά σχολικό έτος.");
    }

    return {
      entries: activeYears,
      activeYears: activeYears,
      fullMonths: fullMonths,
      totalDays: totalDays,
      remainderDays: totalDays,
      convertedMonths: convertedMonths,
      extraMonths: convertedMonths,
      remainingDays: remainingDays,
      countedMonths: fullMonths + convertedMonths,
      basePoints: basePoints,
      convertedRawPoints: convertedRawPoints,
      convertedPoints: convertedPoints,
      rawPoints: rawPoints,
      maxPoints: maxPoints,
      points: points,
      warnings: warnings
    };
  }

  function calculateAsepService(options) {
    options = options || {};

    const regular = regularPublic(options.regularMonths);
    const hard = difficult(options.difficultMonths);
    const regular2020 = threeMonthRegular2020(options.threeMonthRegular2020);
    const regular2021 = threeMonthRegular2021(options.threeMonthRegular2021);
    const difficult2020 = threeMonthDifficult2020(options.threeMonthDifficult2020);
    const difficult2021 = threeMonthDifficult2021(options.threeMonthDifficult2021);
    const privateResult = privateSchool(options.privateMonths);
    const digital = options.digitalTutoring && typeof options.digitalTutoring === "object"
      ? options.digitalTutoring
      : { points: 0, countedMonths: 0, activeYears: [], warnings: [] };

    const parts = {
      regular: regular,
      difficult: hard,
      threeMonthRegular2020: regular2020,
      threeMonthRegular2021: regular2021,
      threeMonthDifficult2020: difficult2020,
      threeMonthDifficult2021: difficult2021,
      privateSchool: privateResult,
      digitalTutoring: digital
    };

    const pointValues = [
      regular.points, hard.points,
      regular2020.points, regular2021.points,
      difficult2020.points, difficult2021.points,
      privateResult.points, Number(digital.points) || 0
    ];
    const total = cappedTotal(pointValues);
    const monthsTotal =
      regular.months + hard.months +
      regular2020.months + regular2021.months +
      difficult2020.months + difficult2021.months +
      privateResult.months + (Number(digital.countedMonths) || 0);

    const warnings = Array.isArray(digital.warnings) ? digital.warnings.slice() : [];
    if (total.raw > RULES.totalMaxPoints) {
      warnings.push("Η μοριοδότηση προϋπηρεσίας περιορίστηκε στο ανώτατο όριο των 120 μορίων.");
    }

    return global.EducationCore.createScoreResult(total.rawPoints, total.points, {
      parts: parts,
      months: monthsTotal,
      capped: total.rawPoints > RULES.totalMaxPoints,
      warnings: warnings
    }, { raw: true });
  }

  function cappedTotal(values) {
    const raw = values.reduce((sum, value) => sum + (Number(value) || 0), 0);
    return global.EducationCore.createScoreResult(raw, Math.min(raw, RULES.totalMaxPoints), {}, { raw: true });
  }

  global.EducationService = Object.freeze({
    RULES,
    nonNegativeInteger,
    months,
    regularPublic,
    difficult,
    threeMonthRegular2020,
    threeMonthRegular2021,
    threeMonthDifficult2020,
    threeMonthDifficult2021,
    privateSchool,
    digitalPerSchoolYear,
    digitalTutoring,
    calculateAsepService,
    cappedTotal
  });
})(window);
