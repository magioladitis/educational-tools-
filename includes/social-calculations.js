/*
 * Κοινή μοριοδότηση κοινωνικών κριτηρίων για:
 * 1ΓΕ/2026, 2ΓΕ/2026, 1ΓΤ/2024 και 1ΕΑ/2025–4ΕΑ/2025.
 */
(function (global) {
  "use strict";

  const RULES = Object.freeze({
    childPoints: 3,
    minDisabilityPercent: 50,
    disabilityRate: 0.4,
    spouseMinMarriageYears: 4,
    auxiliaryChildDisabilityPercent: 67
  });

  function number(value) {
    const n = Number(value);
    return Number.isFinite(n) ? n : 0;
  }

  function clampPercent(value) {
    return Math.min(100, Math.max(0, number(value)));
  }

  function nonNegativeInteger(value) {
    return Math.max(0, Math.floor(number(value)));
  }

  function calculate(options = {}) {
    const children = nonNegativeInteger(options.children);
    const childrenPoints = children * RULES.childPoints;

    const candidatePercent = clampPercent(options.candidateDisability);
    const spousePercent = clampPercent(options.spouseDisability);
    const childPercent = clampPercent(options.childDisability);

    const candidateMentalCondition = Boolean(options.candidateMentalCondition);
    const marriageYears4Plus = Boolean(options.marriageYears4Plus);

    const warnings = [];
    const eligibleDisabilities = [];

    const candidateEligible =
      candidatePercent >= RULES.minDisabilityPercent &&
      !candidateMentalCondition;

    if (candidateEligible) {
      eligibleDisabilities.push({ person: "candidate", label: "Υποψήφιος/α", percent: candidatePercent });
    } else if (candidatePercent >= RULES.minDisabilityPercent && candidateMentalCondition) {
      warnings.push("Η αναπηρία του/της υποψηφίου δεν μοριοδοτείται όταν οφείλεται, έστω και κατά ποσοστό, σε ψυχική πάθηση.");
    } else if (candidatePercent > 0) {
      warnings.push("Η αναπηρία του/της υποψηφίου μοριοδοτείται από 50% και άνω.");
    }

    const spouseEligible =
      spousePercent >= RULES.minDisabilityPercent &&
      marriageYears4Plus;

    if (spouseEligible) {
      eligibleDisabilities.push({ person: "spouse", label: "Σύζυγος", percent: spousePercent });
    } else if (spousePercent >= RULES.minDisabilityPercent && !marriageYears4Plus) {
      warnings.push("Η αναπηρία συζύγου δεν μοριοδοτείται επειδή δεν δηλώθηκε έγγαμος βίος τουλάχιστον 4 ετών.");
    } else if (spousePercent > 0) {
      warnings.push("Η αναπηρία συζύγου μοριοδοτείται από 50% και άνω.");
    }

    const childEligible = childPercent >= RULES.minDisabilityPercent;
    if (childEligible) {
      eligibleDisabilities.push({ person: "child", label: "Τέκνο", percent: childPercent });
    } else if (childPercent > 0) {
      warnings.push("Η αναπηρία τέκνου μοριοδοτείται από 50% και άνω.");
    }

    let highest = null;
    for (const item of eligibleDisabilities) {
      if (!highest || item.percent > highest.percent) highest = item;
    }

    const highestDisabilityPercent = highest ? highest.percent : 0;
    const disabilityPoints = highestDisabilityPercent * RULES.disabilityRate;

    return {
      children,
      childrenPoints,
      candidatePercent,
      spousePercent,
      childPercent,
      candidateEligible,
      spouseEligible,
      childEligible,
      highestPerson: highest ? highest.person : null,
      highestLabel: highest ? highest.label : "",
      highestDisabilityPercent,
      disabilityPoints,
      total: childrenPoints + disabilityPoints,
      childDisability67: childPercent >= RULES.auxiliaryChildDisabilityPercent,
      warnings
    };
  }

  global.EducationSocial = Object.freeze({
    RULES,
    calculate,
    clampPercent,
    nonNegativeInteger
  });
})(window);
