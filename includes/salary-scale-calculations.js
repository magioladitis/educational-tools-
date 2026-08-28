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
      remainderMonths: remainderMonths,
      monthsToNext: monthsToNext,
      capped: finalMK >= rule.maxMK,
      promotionCapped: baseMK + promotion.mk > rule.maxMK
    };
  }

  global.EducationSalaryScale = Object.freeze({
    RULES: RULES,
    PROMOTIONS: PROMOTIONS,
    nonNegativeInteger: nonNegativeInteger,
    serviceMonths: serviceMonths,
    suspendedServiceMonths: suspendedServiceMonths,
    calculate: calculate
  });
})(window);
