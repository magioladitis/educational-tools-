/*
 * Indicative net-pay calculation for the salary-scale calculator.
 * Tax year: 2026. The engine works on the monthly gross amount supplied by the caller (basic salary plus any supported optional allowance).
 * It does not replace an official payroll statement or annual tax assessment.
 */
(function (global) {
  "use strict";

  const PERMANENT_DEDUCTION_COMPONENTS = Object.freeze([
    Object.freeze({ key: "efkaPensionSupplementary", label: "ΕΦΚΑ — κύρια σύνταξη + επικουρική", rate: 0.0967 }),
    Object.freeze({ key: "healthInKind", label: "ΕΦΚΑ υγεία — παροχές σε είδος", rate: 0.0165 }),
    Object.freeze({ key: "healthCash", label: "ΕΦΚΑ υγεία — παροχές σε χρήμα", rate: 0.0040 }),
    Object.freeze({ key: "lumpSum", label: "Τ.Π.Δ.Υ. / εφάπαξ", rate: 0.0400 }),
    Object.freeze({ key: "mtpy", label: "Μ.Τ.Π.Υ.", rate: 0.0450 }),
    Object.freeze({ key: "unemployment", label: "Εισφορά για την καταπολέμηση της ανεργίας", rate: 0.0200 })
  ]);

  const SUBSTITUTE_DEDUCTION_COMPONENTS = Object.freeze([
    Object.freeze({ key: "efkaKpk101", label: "ΕΦΚΑ — ΚΠΚ 101", rate: 0.1337 })
  ]);

  const PROFILES = Object.freeze({
    permanent: Object.freeze({
      label: "Μόνιμος δημόσιος υπάλληλος",
      deductibleRate: 0.2222,
      deductionBreakdown: "ΕΦΚΑ 9,67% · Υγεία 1,65% + 0,40% · Εφάπαξ 4% · ΜΤΠΥ 4,5% · Ανεργία 2%",
      deductionComponents: PERMANENT_DEDUCTION_COMPONENTS,
      extraCashRate: 0
    }),
    newly_appointed: Object.freeze({
      label: "Νεοδιόριστος — 1ο έτος ΜΤΠΥ",
      deductibleRate: 0.2222,
      deductionBreakdown: "ΕΦΚΑ 9,67% · Υγεία 1,65% + 0,40% · Εφάπαξ 4% · ΜΤΠΥ 4,5% · Ανεργία 2%",
      deductionComponents: PERMANENT_DEDUCTION_COMPONENTS,
      extraCashRate: 1 / 12
    }),
    substitute: Object.freeze({
      label: "Αναπληρωτής / ΙΔΟΧ — ΚΠΚ 101",
      deductibleRate: 0.1337,
      deductionBreakdown: "ΚΠΚ 101 · συνολική εισφορά ασφαλισμένου 13,37%",
      deductionComponents: SUBSTITUTE_DEDUCTION_COMPONENTS,
      extraCashRate: 0
    })
  });

  const REMOTE_AREA_ALLOWANCE_MONTHLY = 100;
  // e-EFKA: eligible salaried mothers pay 50% of the employee main-pension contribution.
  // The ordinary employee main-pension rate represented in our payroll profiles is 6.67%,
  // therefore the reduction is 3.335 percentage points of pensionable gross pay.
  const MATERNITY_MAIN_PENSION_REDUCTION_RATE = 0.03335;

  const POSITION_ALLOWANCES = Object.freeze({
    none: Object.freeze({ label: "Χωρίς θέση ευθύνης", amount: 0 }),
    regional_director: Object.freeze({ label: "Περιφερειακός Διευθυντής Εκπαίδευσης", amount: 1170 }),
    regional_quality_supervisor: Object.freeze({ label: "Περιφερειακός Επόπτης Ποιότητας της Εκπαίδευσης", amount: 780 }),
    education_director: Object.freeze({ label: "Διευθυντής Πρωτοβάθμιας / Δευτεροβάθμιας Εκπαίδευσης", amount: 715 }),
    quality_supervisor: Object.freeze({ label: "Επόπτης Ποιότητας της Εκπαίδευσης", amount: 650 }),
    education_counselor: Object.freeze({ label: "Σύμβουλος Εκπαίδευσης", amount: 455 }),
    kedasy_head: Object.freeze({ label: "Προϊστάμενος ΚΕ.Δ.Α.Σ.Υ. / Γραφείου Μειονοτικής Εκπαίδευσης", amount: 455 }),
    lyceum_director: Object.freeze({ label: "Διευθυντής ΓΕΛ / ΕΠΑΛ / ειδικών δομών λυκειακού επιπέδου", amount: 429 }),
    lyceum_director_large: Object.freeze({ label: "Διευθυντής ΓΕΛ / ΕΠΑΛ / αντίστοιχης δομής με ≥120 μαθητές (Σ.Μ.Ε.Α.Ε. ≥30)", amount: 501 }),
    education_matters_head: Object.freeze({ label: "Προϊστάμενος Τμήματος Εκπαιδευτικών Θεμάτων", amount: 390 }),
    gymnasium_director: Object.freeze({ label: "Διευθυντής Γυμνασίου / Ε.Κ. / αντίστοιχης δομής", amount: 358 }),
    gymnasium_director_large: Object.freeze({ label: "Διευθυντής Γυμνασίου / αντίστοιχης δομής με ≥120 μαθητές (Σ.Μ.Ε.Α.Ε. ≥30)", amount: 429 }),
    vice_director: Object.freeze({ label: "Υποδιευθυντής σχολικής μονάδας / Ε.Κ. / Σ.Δ.Ε. / Σ.Α.Ε.Κ. ή Υπεύθυνος Τομέα Ε.Κ.", amount: 195 }),
    small_school_head: Object.freeze({ label: "Προϊστάμενος 1θέσιου–3θέσιου Δημοτικού / Νηπιαγωγείου", amount: 215 })
  });

  function positionAllowanceMonthly(position) {
    return POSITION_ALLOWANCES[position] ? POSITION_ALLOWANCES[position].amount : 0;
  }

  function positionAllowanceLabel(position) {
    return POSITION_ALLOWANCES[position] ? POSITION_ALLOWANCES[position].label : POSITION_ALLOWANCES.none.label;
  }

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

  // Payroll systems round each individual withholding line to euro cents before
  // they sum the lines. Keep the same convention so that the calculator can
  // reconcile with real payroll statements down to the cent.
  function roundMoney(value) {
    const n = Number(value);
    if (!Number.isFinite(n)) return 0;
    return Math.round((n + (n >= 0 ? 1 : -1) * 1e-9) * 100) / 100;
  }

  function roundedDeductionComponents(gross, profile) {
    const source = profile && Array.isArray(profile.deductionComponents)
      ? profile.deductionComponents
      : [];
    return source.map(function (component) {
      return {
        key: component.key,
        label: component.label,
        rate: component.rate,
        amount: roundMoney(gross * component.rate)
      };
    });
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
    const otherDeductions = nonNegativeNumber(options.otherDeductions);
    const maternityPensionReduction = options.maternityPensionReduction === true;
    const maternityReductionRate = maternityPensionReduction
      ? Math.min(profile.deductibleRate, MATERNITY_MAIN_PENSION_REDUCTION_RATE)
      : 0;
    const effectiveDeductionRate = Math.max(0, profile.deductibleRate - maternityReductionRate);
    const deductionComponents = roundedDeductionComponents(gross, profile);
    const baseStandardDeductions = roundMoney(deductionComponents.reduce(function (sum, component) {
      return sum + component.amount;
    }, 0));
    // In real payroll statements the maternity benefit appears as a separate
    // negative withholding line. Round that line independently, then subtract it.
    const maternityReduction = maternityPensionReduction
      ? roundMoney(gross * maternityReductionRate)
      : 0;
    const standardDeductions = roundMoney(Math.max(0, baseStandardDeductions - maternityReduction));
    const registrationDeduction = roundMoney(gross * profile.extraCashRate);
    // Payroll lines are rounded independently for the paid amount, while the
    // withholding-tax basis is calculated from the statutory rates before the
    // cent-level line rounding. This reproduces the observed payroll statements.
    const taxableMonthlyExact = Math.max(0, gross - (gross * effectiveDeductionRate));
    const taxableMonthly = roundMoney(taxableMonthlyExact);
    const taxableAnnualExact = taxableMonthlyExact * 12;
    const taxableAnnual = roundMoney(taxableAnnualExact);
    const taxBeforeCredit = grossAnnualTax(taxableAnnualExact, ageGroup, children);
    const credit = taxCredit(taxableAnnualExact, children, taxBeforeCredit);
    const annualTax = Math.max(0, taxBeforeCredit - credit);
    const monthlyTax = roundMoney(annualTax / 12);
    const roundedOtherDeductions = roundMoney(otherDeductions);
    const netBeforeOtherDeductions = roundMoney(Math.max(0, gross - standardDeductions - registrationDeduction - monthlyTax));
    const estimatedNet = roundMoney(Math.max(0, netBeforeOtherDeductions - roundedOtherDeductions));

    return {
      grossMonthly: gross,
      profile: profileKey,
      profileLabel: profile.label,
      deductionBreakdown: maternityPensionReduction
        ? profile.deductionBreakdown + " · Μειωμένη κύρια σύνταξη μητρότητας 50%"
        : profile.deductionBreakdown,
      ageGroup: ageGroup,
      ageGroupLabel: AGE_GROUPS[ageGroup],
      children: children,
      standardDeductionRate: effectiveDeductionRate,
      baseStandardDeductionRate: profile.deductibleRate,
      baseStandardDeductions: baseStandardDeductions,
      deductionComponents: deductionComponents,
      maternityPensionReduction: maternityPensionReduction,
      maternityPensionReductionRate: maternityReductionRate,
      maternityPensionReductionAmount: maternityReduction,
      standardDeductions: standardDeductions,
      registrationDeductionRate: profile.extraCashRate,
      registrationDeduction: registrationDeduction,
      otherDeductions: roundedOtherDeductions,
      netBeforeOtherDeductions: netBeforeOtherDeductions,
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
    PERMANENT_DEDUCTION_COMPONENTS: PERMANENT_DEDUCTION_COMPONENTS,
    SUBSTITUTE_DEDUCTION_COMPONENTS: SUBSTITUTE_DEDUCTION_COMPONENTS,
    roundMoney: roundMoney,
    roundedDeductionComponents: roundedDeductionComponents,
    REMOTE_AREA_ALLOWANCE_MONTHLY: REMOTE_AREA_ALLOWANCE_MONTHLY,
    MATERNITY_MAIN_PENSION_REDUCTION_RATE: MATERNITY_MAIN_PENSION_REDUCTION_RATE,
    POSITION_ALLOWANCES: POSITION_ALLOWANCES,
    positionAllowanceMonthly: positionAllowanceMonthly,
    positionAllowanceLabel: positionAllowanceLabel,
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
