/*
 * Common salary-scale (M.K.) calculation for staff covered by Chapter B of n. 4354/2015.
 * The caller supplies already-recognised salary service and the highest title that has
 * already been recognised for salary promotion.
 */
(function (global) {
  "use strict";

  const RULES = Object.freeze({
    PE: Object.freeze({ label: "ΠΕ", maxMK: 19, monthsPerMK: 24 }),
    TE: Object.freeze({ label: "ΤΕ", maxMK: 19, monthsPerMK: 24 }),
    DE: Object.freeze({ label: "ΔΕ", maxMK: 13, monthsPerMK: 36 }),
    YE: Object.freeze({ label: "ΥΕ", maxMK: 13, monthsPerMK: 36 })
  });

  const PROMOTIONS = Object.freeze({
    none: Object.freeze({ mk: 0, label: "Χωρίς μισθολογική προώθηση τίτλου" }),
    master: Object.freeze({ mk: 2, label: "Αναγνωρισμένο συναφές μεταπτυχιακό (+2 Μ.Κ.)" }),
    integrated: Object.freeze({ mk: 2, label: "Integrated Master ελληνικού Α.Ε.Ι. που πληροί τις προϋποθέσεις (+2 Μ.Κ. από 01-01-2026)" }),
    phd: Object.freeze({ mk: 6, label: "Αναγνωρισμένο συναφές διδακτορικό (+6 Μ.Κ.)" })
  });

  // Official basic salaries under Chapter B of n. 4354/2015, effective from 01/04/2026.
  // Source: Ministry of National Economy and Finance circular 54692 EX 2026 / 03-04-2026
  // (ADA: ΨΕ7ΨΗ-ΚΧΧ), Annex Tables 1-4. Index 0 is intentionally null so MK can be used directly.
  const BASIC_SALARIES_2026 = Object.freeze({
    PE: Object.freeze([null, 1232, 1291, 1350, 1409, 1468, 1527, 1586, 1645, 1704, 1763, 1822, 1881, 1940, 1999, 2058, 2117, 2176, 2235, 2294]),
    TE: Object.freeze([null, 1177, 1232, 1287, 1342, 1397, 1452, 1507, 1562, 1617, 1672, 1727, 1782, 1837, 1892, 1947, 2002, 2057, 2112, 2167]),
    DE: Object.freeze([null, 998, 1058, 1118, 1178, 1238, 1298, 1358, 1418, 1478, 1538, 1598, 1658, 1718]),
    YE: Object.freeze([null, 920, 963, 1006, 1049, 1092, 1135, 1178, 1221, 1264, 1307, 1350, 1393, 1436])
  });

  const BASIC_SALARY_EFFECTIVE_DATE = "01/04/2026";

  function nonNegativeInteger(value) {
    const n = Math.floor(Number(value) || 0);
    return Math.max(0, n);
  }

  function serviceMonths(years, months) {
    const y = Math.min(50, nonNegativeInteger(years));
    const m = Math.min(11, nonNegativeInteger(months));
    return y * 12 + m;
  }

  function suspendedServiceMonths(years, months) {
    const y = Math.min(2, nonNegativeInteger(years));
    const m = Math.min(11, nonNegativeInteger(months));
    return Math.min(24, y * 12 + m);
  }


  function basicGrossSalary(category, mk) {
    const code = BASIC_SALARIES_2026[category] ? category : "PE";
    const scale = BASIC_SALARIES_2026[code];
    const safeMk = Math.max(1, Math.min(scale.length - 1, nonNegativeInteger(mk)));
    return scale[safeMk];
  }

  function calculate(options) {
    options = options || {};
    const category = RULES[options.category] ? options.category : "PE";
    const rule = RULES[category];
    const promotionKey = PROMOTIONS[options.qualification] ? options.qualification : "none";
    const promotion = PROMOTIONS[promotionKey];
    const totalMonths = serviceMonths(options.years, options.months);
    const requestedSuspendedMonths = suspendedServiceMonths(options.suspendedYears, options.suspendedMonths);
    const excludedMonths = Math.min(totalMonths, requestedSuspendedMonths);
    const countableMonths = Math.max(0, totalMonths - excludedMonths);

    const completedSteps = Math.floor(countableMonths / rule.monthsPerMK);
    const baseMK = Math.min(rule.maxMK, 1 + completedSteps);
    const finalMK = Math.min(rule.maxMK, baseMK + promotion.mk);
    const remainderMonths = baseMK >= rule.maxMK ? 0 : countableMonths % rule.monthsPerMK;
    const monthsToNext = finalMK >= rule.maxMK ? 0 : rule.monthsPerMK - remainderMonths;

    return {
      category: rule.label,
      categoryCode: category,
      maxMK: rule.maxMK,
      monthsPerMK: rule.monthsPerMK,
      serviceMonths: totalMonths,
      requestedSuspendedServiceMonths: requestedSuspendedMonths,
      suspendedServiceMonths: excludedMonths,
      countableServiceMonths: countableMonths,
      suspendedServiceAdjusted: requestedSuspendedMonths > totalMonths,
      baseMK: baseMK,
      promotionMK: Math.max(0, finalMK - baseMK),
      requestedPromotionMK: promotion.mk,
      qualification: promotionKey,
      qualificationLabel: promotion.label,
      finalMK: finalMK,
      basicGrossSalary: basicGrossSalary(category, finalMK),
      basicSalaryEffectiveDate: BASIC_SALARY_EFFECTIVE_DATE,
      remainderMonths: remainderMonths,
      monthsToNext: monthsToNext,
      capped: finalMK >= rule.maxMK,
      promotionCapped: baseMK + promotion.mk > rule.maxMK
    };
  }

  global.EducationSalaryScale = Object.freeze({
    RULES: RULES,
    PROMOTIONS: PROMOTIONS,
    BASIC_SALARIES_2026: BASIC_SALARIES_2026,
    BASIC_SALARY_EFFECTIVE_DATE: BASIC_SALARY_EFFECTIVE_DATE,
    nonNegativeInteger: nonNegativeInteger,
    basicGrossSalary: basicGrossSalary,
    serviceMonths: serviceMonths,
    suspendedServiceMonths: suspendedServiceMonths,
    calculate: calculate
  });
})(window);
