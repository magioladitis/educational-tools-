/*
 * Κοινή λογική υπολογισμού εκπαιδευτικής προϋπηρεσίας
 * για 1ΓΕ/2026, 2ΓΕ/2026, 1ΓΤ/2024 και 3ΕΑ/2025.
 *
 * Τρίμηνες συμβάσεις:
 * 2020-2021: έως 8 μήνες
 * 2021-2022: έως 7 μήνες
 *
 * Ανώτατα όρια μορίων:
 * κανονικές τρίμηνες: 10 μόρια/έτος
 * δυσπρόσιτες τρίμηνες: 20 μόρια/έτος
 * Ψηφιακό Φροντιστήριο: 15 μόρια/σχολικό έτος· έως 9 μήνες
 * και 16 ημέρες το 2024-2025, έως 8 μήνες και 2 ημέρες το
 * 2025-2026. Τα υπόλοιπα ημερών μετατρέπονται ανά 30 σε μήνα.
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
    digitalMaxPointsPerSchoolYear: 15
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

  function digitalPerSchoolYear(value) {
    const m = months(value);
    return { months: m, points: Math.min(m * RULES.digitalRate, RULES.digitalMaxPointsPerSchoolYear) };
  }

  function digitalAcrossSchoolYears(entries) {
    const activeYears = (Array.isArray(entries) ? entries : []).map((entry, index) => {
      const submittedMonths = months(entry && entry.months);
      const submittedDays = Math.min(29, nonNegativeInteger(entry && entry.days));
      const maxMonths = entry && entry.maxMonths !== undefined
        ? nonNegativeInteger(entry.maxMonths)
        : null;
      const maxDaysAtMaxMonths = entry && entry.maxDaysAtMaxMonths !== undefined
        ? Math.min(29, nonNegativeInteger(entry.maxDaysAtMaxMonths))
        : 29;
      const submittedDurationDays = submittedMonths * 30 + submittedDays;
      const maxDurationDays = maxMonths === null
        ? submittedDurationDays
        : maxMonths * 30 + maxDaysAtMaxMonths;
      const acceptedDurationDays = Math.min(submittedDurationDays, maxDurationDays);
      const m = Math.floor(acceptedDurationDays / 30);
      const d = acceptedDurationDays % 30;
      const basePoints = Math.min(m * RULES.digitalRate, RULES.digitalMaxPointsPerSchoolYear);
      return {
        label: entry && entry.label ? String(entry.label) : ((index + 1) + "ο σχολικό έτος"),
        months: m,
        days: d,
        submittedMonths,
        submittedDays,
        maxMonths,
        maxDaysAtMaxMonths,
        durationCapped: acceptedDurationDays < submittedDurationDays,
        basePoints,
        capped: m * RULES.digitalRate > RULES.digitalMaxPointsPerSchoolYear
      };
    }).filter(entry => entry.months > 0 || entry.days > 0);

    const totalDays = activeYears.reduce((sum, entry) => sum + entry.days, 0);
    const convertedMonths = Math.floor(totalDays / 30);
    const remainingDays = totalDays % 30;
    const basePoints = activeYears.reduce((sum, entry) => sum + entry.basePoints, 0);
    const maxPoints = activeYears.length * RULES.digitalMaxPointsPerSchoolYear;
    const convertedRawPoints = convertedMonths * RULES.digitalRate;
    const convertedPoints = Math.min(convertedRawPoints, Math.max(0, maxPoints - basePoints));

    return {
      activeYears,
      totalDays,
      convertedMonths,
      remainingDays,
      basePoints,
      convertedRawPoints,
      convertedPoints,
      maxPoints,
      points: basePoints + convertedPoints
    };
  }

  function cappedTotal(values) {
    const raw = values.reduce((sum, value) => sum + (Number(value) || 0), 0);
    return { raw, points: Math.min(raw, RULES.totalMaxPoints) };
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
    digitalAcrossSchoolYears,
    cappedTotal
  });
})(window);
