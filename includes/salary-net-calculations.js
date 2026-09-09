/*
 * Indicative net-pay calculation for the salary-scale calculator.
 * Tax year: 2026. The engine works on the monthly gross amount supplied by the caller (basic salary plus any supported optional allowance).
 * It does not replace an official payroll statement or annual tax assessment.
 */
(function (global) {
  "use strict";

  const PROFILES = Object.freeze({
    permanent: Object.freeze({
      label: "Μόνιμος δημόσιος υπάλληλος",
      deductibleRate: 0.2222,
      deductionBreakdown: "Σύνταξη 6,67% · Υγεία 2,05% · Επικουρική 3% · Εφάπαξ 4% · ΜΤΠΥ 4,5% · Ανεργία 2%",
      extraCashRate: 0
    }),
    newly_appointed: Object.freeze({
      label: "Νεοδιόριστος — 1ο έτος ΜΤΠΥ",
      deductibleRate: 0.2222,
      deductionBreakdown: "Σύνταξη 6,67% · Υγεία 2,05% · Επικουρική 3% · Εφάπαξ 4% · ΜΤΠΥ 4,5% · Ανεργία 2%",
      extraCashRate: 1 / 12
    }),
    substitute: Object.freeze({
      label: "Αναπληρωτής / ΙΔΟΧ — ΚΠΚ 101",
      deductibleRate: 0.1337,
      deductionBreakdown: "ΚΠΚ 101 · συνολική εισφορά ασφαλισμένου 13,37%",
      extraCashRate: 0
    })
  });

  const REMOTE_AREA_ALLOWANCE_MONTHLY = 100;

  function familyAllowanceMonthly(children) {
    const c = Math.min(20, nonNegativeInteger(children));
    if (c <= 0) return 0;
    if (c === 1) return 70;
    if (c === 2) return 120;
    if (c === 3) return 170;
    if (c === 4) return 220;
    return 220 + (c - 4) * 70;
  }

  const AGE_GROUPS = Object.freeze({
    over30: "Άνω των 30 ετών",
    age26to30: "26–30 ετών",
    upTo25: "Έως 25 ετών"
  });

  const TAX_BRACKETS = Object.freeze([
    Object.freeze({ from: 0, to: 10000 }),
    Object.freeze({ from: 10000, to: 20000 }),
    Object.freeze({ from: 20000, to: 30000 }),
    Object.freeze({ from: 30000, to: 40000 }),
    Object.freeze({ from: 40000, to: 60000 }),
    Object.freeze({ from: 60000, to: Infinity })
  ]);

  function nonNegativeNumber(value) {
    const n = Number(value);
    return Number.isFinite(n) ? Math.max(0, n) : 0;
  }

  function nonNegativeInteger(value) {
    return Math.max(0, Math.floor(nonNegativeNumber(value)));
  }

  function taxRateForBracket(index, ageGroup, children) {
    const c = nonNegativeInteger(children);
    const age = AGE_GROUPS[ageGroup] ? ageGroup : "over30";

    if (index === 0) {
      if (age === "upTo25" || c >= 4) return 0;
      return 0.09;
    }

    if (index === 1) {
      if (age === "upTo25") return 0;
      if (c >= 4) return 0;
      if (age === "age26to30") return 0.09;
      if (c === 3) return 0.09;
      if (c === 2) return 0.16;
      if (c === 1) return 0.18;
      return 0.20;
    }

    if (index === 2) {
      if (c === 0) return 0.26;
      if (c === 1) return 0.24;
      if (c === 2) return 0.22;
      if (c === 3) return 0.20;
      return Math.max(0, 0.18 - (c - 4) * 0.02);
    }

    if (index === 3) return 0.34;
    if (index === 4) return 0.39;
    return 0.44;
  }

  function grossAnnualTax(taxableAnnualIncome, ageGroup, children) {
    const income = nonNegativeNumber(taxableAnnualIncome);
    let tax = 0;
    for (let i = 0; i < TAX_BRACKETS.length; i += 1) {
      const bracket = TAX_BRACKETS[i];
      if (income <= bracket.from) break;
      const taxablePart = Math.min(income, bracket.to) - bracket.from;
      if (taxablePart > 0) tax += taxablePart * taxRateForBracket(i, ageGroup, children);
    }
    return Math.max(0, tax);
  }

  function baseTaxCredit(children) {
    const c = nonNegativeInteger(children);
    if (c === 0) return 777;
    if (c === 1) return 900;
    if (c === 2) return 1120;
    if (c === 3) return 1340;
    if (c === 4) return 1580;
    if (c === 5) return 1780;
    return 1780 + (c - 5) * 220;
  }

  function taxCredit(taxableAnnualIncome, children, grossTax) {
    const income = nonNegativeNumber(taxableAnnualIncome);
    const c = nonNegativeInteger(children);
    const taxBeforeCredit = nonNegativeNumber(grossTax);
    let credit = baseTaxCredit(c);

    // Article 16 KFE: for fewer than five dependent children the reduction
    // decreases by EUR 20 per EUR 1,000 of taxable salary income above EUR 12,000.
    if (c < 5 && income > 12000) {
      credit -= (income - 12000) * 0.02;
    }

    return Math.min(taxBeforeCredit, Math.max(0, credit));
  }

  function calculate(options) {
    options = options || {};
    const gross = nonNegativeNumber(options.grossMonthly);
    const profileKey = PROFILES[options.profile] ? options.profile : "permanent";
    const profile = PROFILES[profileKey];
    const ageGroup = AGE_GROUPS[options.ageGroup] ? options.ageGroup : "over30";
    const children = Math.min(20, nonNegativeInteger(options.children));

    const standardDeductions = gross * profile.deductibleRate;
    const registrationDeduction = gross * profile.extraCashRate;
    const taxableMonthly = Math.max(0, gross - standardDeductions);
    const taxableAnnual = taxableMonthly * 12;
    const taxBeforeCredit = grossAnnualTax(taxableAnnual, ageGroup, children);
    const credit = taxCredit(taxableAnnual, children, taxBeforeCredit);
    const annualTax = Math.max(0, taxBeforeCredit - credit);
    const monthlyTax = annualTax / 12;
    const estimatedNet = Math.max(0, gross - standardDeductions - registrationDeduction - monthlyTax);

    return {
      grossMonthly: gross,
      profile: profileKey,
      profileLabel: profile.label,
      deductionBreakdown: profile.deductionBreakdown,
      ageGroup: ageGroup,
      ageGroupLabel: AGE_GROUPS[ageGroup],
      children: children,
      standardDeductionRate: profile.deductibleRate,
      standardDeductions: standardDeductions,
      registrationDeductionRate: profile.extraCashRate,
      registrationDeduction: registrationDeduction,
      taxableMonthly: taxableMonthly,
      taxableAnnual: taxableAnnual,
      taxBeforeCredit: taxBeforeCredit,
      baseTaxCredit: baseTaxCredit(children),
      taxCredit: credit,
      annualTax: annualTax,
      monthlyTax: monthlyTax,
      estimatedNet: estimatedNet
    };
  }

  global.EducationSalaryNet = Object.freeze({
    PROFILES: PROFILES,
    REMOTE_AREA_ALLOWANCE_MONTHLY: REMOTE_AREA_ALLOWANCE_MONTHLY,
    familyAllowanceMonthly: familyAllowanceMonthly,
    AGE_GROUPS: AGE_GROUPS,
    TAX_BRACKETS: TAX_BRACKETS,
    taxRateForBracket: taxRateForBracket,
    grossAnnualTax: grossAnnualTax,
    baseTaxCredit: baseTaxCredit,
    taxCredit: taxCredit,
    calculate: calculate
  });
})(window);
