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

  const INSURED_STATUSES = Object.freeze({
    new: Object.freeze({
      label: "Νέος ασφαλισμένος — πρώτη ασφάλιση από 01/01/1993",
      shortLabel: "Νέος ασφαλισμένος (από 01/01/1993)"
    }),
    old: Object.freeze({
      label: "Παλαιός ασφαλισμένος — πρώτη ασφάλιση έως 31/12/1992",
      shortLabel: "Παλαιός ασφαλισμένος (έως 31/12/1992)"
    })
  });

  const REMOTE_AREA_ALLOWANCE_MONTHLY = 100;
  // e-EFKA: eligible salaried mothers pay 50% of the employee main-pension contribution.
  // The ordinary employee main-pension rate is 6.67%, so the reduction is 3.335%
  // of the applicable main-pension contribution base (which differs for old/new insured).
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

  const DISABILITY_TAX_TREATMENTS = Object.freeze({
    none: Object.freeze({
      label: "Χωρίς ειδική φορολογική ρύθμιση αναπηρίας",
      annualReduction: 0,
      salaryTaxExempt: false
    }),
    disability67_79: Object.freeze({
      label: "Αναπηρία 67%–79,99% — μείωση φόρου έως 200 € / έτος",
      annualReduction: 200,
      salaryTaxExempt: false
    }),
    disability80plus: Object.freeze({
      label: "Αναπηρία ≥80% — απαλλαγή φόρου μισθωτής εργασίας",
      annualReduction: 0,
      salaryTaxExempt: true
    })
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

  function hasOwn(obj, key) {
    return Object.prototype.hasOwnProperty.call(obj || {}, key);
  }

  function earningsFromOptions(options) {
    const detailed = ["basicMonthly", "familyAllowanceMonthly", "positionAllowanceMonthly", "remoteAllowanceMonthly"]
      .some(function (key) { return hasOwn(options, key); });

    if (!detailed) {
      const legacyGross = nonNegativeNumber(options.grossMonthly);
      return {
        detailed: false,
        basic: legacyGross,
        family: 0,
        position: 0,
        remote: 0,
        gross: legacyGross
      };
    }

    const basic = nonNegativeNumber(options.basicMonthly);
    const family = nonNegativeNumber(options.familyAllowanceMonthly);
    const position = nonNegativeNumber(options.positionAllowanceMonthly);
    const remote = nonNegativeNumber(options.remoteAllowanceMonthly);
    const supportedGross = basic + family + position + remote;
    const gross = hasOwn(options, "grossMonthly")
      ? nonNegativeNumber(options.grossMonthly)
      : supportedGross;

    // The salary page supplies exactly the supported components. If a caller also
    // supplies a higher grossMonthly, keep the extra amount only in the universal
    // gross bases (health/unemployment) instead of guessing its pension/MTPY status.
    return {
      detailed: true,
      basic: basic,
      family: family,
      position: position,
      remote: remote,
      gross: Math.max(gross, supportedGross),
      unsupportedRegular: Math.max(0, gross - supportedGross)
    };
  }

  function component(key, label, rate, base, note) {
    const exact = nonNegativeNumber(base) * nonNegativeNumber(rate);
    return {
      key: key,
      label: label,
      rate: rate,
      base: nonNegativeNumber(base),
      amountExact: exact,
      amount: roundMoney(exact),
      note: note || ""
    };
  }

  function customComponent(key, label, exactAmount, note) {
    return {
      key: key,
      label: label,
      rate: null,
      base: null,
      amountExact: nonNegativeNumber(exactAmount),
      amount: roundMoney(exactAmount),
      note: note || ""
    };
  }

  function buildDeductionComponents(earnings, profileKey, insuredStatus) {
    const gross = earnings.gross;
    if (profileKey === "substitute") {
      return {
        components: [component("efkaKpk101", "ΕΦΚΑ — ΚΠΚ 101", 0.1337, gross, "13,37% επί των ασφαλιστέων αποδοχών")],
        pensionBase: gross,
        lumpSumBase: 0,
        mtpyPrimaryBase: 0,
        mtpyReducedBase: 0,
        healthBase: gross,
        unemploymentBase: gross,
        deductionBreakdown: "ΚΠΚ 101 · συνολική εισφορά ασφαλισμένου 13,37%",
        basesLabel: "ΚΠΚ 101: " + roundMoney(gross).toFixed(2).replace(".", ",") + " €"
      };
    }

    const oldInsured = insuredStatus === "old";
    const basicAndPosition = earnings.basic + earnings.position;
    const familyAndRemote = earnings.family + earnings.remote;
    const pensionBase = oldInsured ? basicAndPosition : gross;
    const lumpSumBase = oldInsured ? earnings.basic : gross;
    const mtpyPrimaryBase = oldInsured ? basicAndPosition : gross;
    const mtpyReducedBase = oldInsured ? familyAndRemote : 0;
    const components = [
      component(
        "efkaPensionSupplementary",
        "ΕΦΚΑ — κύρια σύνταξη + επικουρική",
        0.0967,
        pensionBase,
        "9,67% επί της βάσης κύριας/επικουρικής σύνταξης"
      ),
      component("healthInKind", "ΕΦΚΑ υγεία — παροχές σε είδος", 0.0165, gross, "1,65% επί των πάσης φύσεως τακτικών αποδοχών"),
      component("healthCash", "ΕΦΚΑ υγεία — παροχές σε χρήμα", 0.0040, gross, "0,40% επί των πάσης φύσεως τακτικών αποδοχών"),
      component(
        "lumpSum",
        "Τ.Π.Δ.Υ. / εφάπαξ",
        0.0400,
        lumpSumBase,
        oldInsured ? "4% επί του βασικού μισθού (παλαιός ασφαλισμένος)" : "4% επί των ασφαλιστέων αποδοχών κύριας σύνταξης (νέος ασφαλισμένος)"
      )
    ];

    if (oldInsured) {
      const mtpyExact = mtpyPrimaryBase * 0.045 + mtpyReducedBase * 0.01;
      components.push(customComponent(
        "mtpy",
        "Μ.Τ.Π.Υ.",
        mtpyExact,
        "4,5% σε βασικό μισθό + θέση ευθύνης · 1% σε οικογενειακή παροχή + παραμεθόριο"
      ));
    } else {
      components.push(component("mtpy", "Μ.Τ.Π.Υ.", 0.0450, mtpyPrimaryBase, "4,5% επί των συντάξιμων αποδοχών"));
    }

    components.push(component(
      "unemployment",
      "Εισφορά για την καταπολέμηση της ανεργίας",
      0.0200,
      gross,
      "2% επί τακτικών αποδοχών / πρόσθετων αμοιβών που περιλαμβάνονται στην εκτίμηση"
    ));

    const deductionBreakdown = oldInsured
      ? "ΕΦΚΑ κύρια+επικουρική 9,67%: βασικός+θέση · Υγεία 2,05%: μικτά · ΤΠΔΥ 4%: βασικός · ΜΤΠΥ 4,5%: βασικός+θέση και 1%: οικογενειακή/παραμεθόριο · Ανεργία 2%: μικτά"
      : "ΕΦΚΑ κύρια+επικουρική 9,67% · Υγεία 2,05% · ΤΠΔΥ 4% · ΜΤΠΥ 4,5% · Ανεργία 2% επί των αντίστοιχων ασφαλιστέων αποδοχών";

    const euro = function (value) { return roundMoney(value).toFixed(2).replace(".", ",") + " €"; };
    const basesLabel = oldInsured
      ? "Κύρια/επικουρική: " + euro(pensionBase) + " · ΤΠΔΥ: " + euro(lumpSumBase) + " · ΜΤΠΥ 4,5%: " + euro(mtpyPrimaryBase) + " · ΜΤΠΥ 1%: " + euro(mtpyReducedBase) + " · Υγεία/ανεργία: " + euro(gross)
      : "Κύρια/επικουρική/ΤΠΔΥ/ΜΤΠΥ: " + euro(pensionBase) + " · Υγεία/ανεργία: " + euro(gross);

    return {
      components: components,
      pensionBase: pensionBase,
      lumpSumBase: lumpSumBase,
      mtpyPrimaryBase: mtpyPrimaryBase,
      mtpyReducedBase: mtpyReducedBase,
      healthBase: gross,
      unemploymentBase: gross,
      deductionBreakdown: deductionBreakdown,
      basesLabel: basesLabel
    };
  }

  function roundedDeductionComponents(gross, profile) {
    // Legacy helper retained for compatibility with older tests/callers.
    const source = profile && Array.isArray(profile.deductionComponents)
      ? profile.deductionComponents
      : [];
    return source.map(function (entry) {
      return component(entry.key, entry.label, entry.rate, gross, "");
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
    const profileKey = PROFILES[options.profile] ? options.profile : "permanent";
    const profile = PROFILES[profileKey];
    const earnings = earningsFromOptions(options);
    const gross = earnings.gross;
    const ageGroup = AGE_GROUPS[options.ageGroup] ? options.ageGroup : "over30";
    const children = Math.min(20, nonNegativeInteger(options.children));
    const disabilityTaxTreatmentKey = DISABILITY_TAX_TREATMENTS[options.disabilityTaxTreatment]
      ? options.disabilityTaxTreatment
      : "none";
    const disabilityTaxTreatment = DISABILITY_TAX_TREATMENTS[disabilityTaxTreatmentKey];
    const insuredStatus = INSURED_STATUSES[options.insuredStatus] ? options.insuredStatus : "new";
    const insuredStatusApplies = profileKey !== "substitute";
    const effectiveInsuredStatus = insuredStatusApplies ? insuredStatus : "new";
    const otherDeductions = nonNegativeNumber(options.otherDeductions);
    const maternityPensionReduction = options.maternityPensionReduction === true;

    const deductionModel = buildDeductionComponents(earnings, profileKey, effectiveInsuredStatus);
    const deductionComponents = deductionModel.components;
    const baseStandardDeductionsExact = deductionComponents.reduce(function (sum, entry) {
      return sum + entry.amountExact;
    }, 0);
    const baseStandardDeductions = roundMoney(deductionComponents.reduce(function (sum, entry) {
      return sum + entry.amount;
    }, 0));

    // The maternity relief is 50% of the employee main-pension contribution only.
    // Its base is therefore the old/new main-pension base, not necessarily total gross pay.
    const maternityReduction = maternityPensionReduction
      ? roundMoney(deductionModel.pensionBase * MATERNITY_MAIN_PENSION_REDUCTION_RATE)
      : 0;
    const maternityReductionExact = maternityPensionReduction
      ? deductionModel.pensionBase * MATERNITY_MAIN_PENSION_REDUCTION_RATE
      : 0;
    const standardDeductions = roundMoney(Math.max(0, baseStandardDeductions - maternityReduction));
    const standardDeductionsExact = Math.max(0, baseStandardDeductionsExact - maternityReductionExact);
    const registrationDeduction = roundMoney(gross * profile.extraCashRate);

    // Payroll lines are rounded individually for the paid amount. The withholding-tax
    // basis uses the statutory contribution amounts before cent-level line rounding.
    const taxableMonthlyExact = Math.max(0, gross - standardDeductionsExact);
    const taxableMonthly = roundMoney(taxableMonthlyExact);
    const taxableAnnualExact = taxableMonthlyExact * 12;
    const taxableAnnual = roundMoney(taxableAnnualExact);
    const taxBeforeCredit = grossAnnualTax(taxableAnnualExact, ageGroup, children);
    const credit = taxCredit(taxableAnnualExact, children, taxBeforeCredit);
    const taxAfterArticle16 = Math.max(0, taxBeforeCredit - credit);
    const disabilityTaxRelief = disabilityTaxTreatment.salaryTaxExempt
      ? taxAfterArticle16
      : Math.min(taxAfterArticle16, disabilityTaxTreatment.annualReduction);
    const annualTax = Math.max(0, taxAfterArticle16 - disabilityTaxRelief);
    const monthlyTax = roundMoney(annualTax / 12);
    const roundedOtherDeductions = roundMoney(otherDeductions);
    const netBeforeOtherDeductions = roundMoney(Math.max(0, gross - standardDeductions - registrationDeduction - monthlyTax));
    const estimatedNet = roundMoney(Math.max(0, netBeforeOtherDeductions - roundedOtherDeductions));
    const standardDeductionRate = gross > 0 ? standardDeductionsExact / gross : 0;
    const baseStandardDeductionRate = gross > 0 ? baseStandardDeductionsExact / gross : profile.deductibleRate;

    return {
      grossMonthly: gross,
      earnings: earnings,
      profile: profileKey,
      profileLabel: profile.label,
      insuredStatus: insuredStatus,
      insuredStatusApplies: insuredStatusApplies,
      insuredStatusLabel: insuredStatusApplies ? INSURED_STATUSES[insuredStatus].label : "Δεν εφαρμόζεται στο ΚΠΚ 101",
      deductionBreakdown: deductionModel.deductionBreakdown + (maternityPensionReduction ? " · Μειωμένη κύρια σύνταξη μητρότητας 50%" : ""),
      insuranceBasesLabel: deductionModel.basesLabel,
      pensionContributionBase: deductionModel.pensionBase,
      lumpSumContributionBase: deductionModel.lumpSumBase,
      mtpyPrimaryContributionBase: deductionModel.mtpyPrimaryBase,
      mtpyReducedContributionBase: deductionModel.mtpyReducedBase,
      healthContributionBase: deductionModel.healthBase,
      unemploymentContributionBase: deductionModel.unemploymentBase,
      ageGroup: ageGroup,
      ageGroupLabel: AGE_GROUPS[ageGroup],
      children: children,
      disabilityTaxTreatment: disabilityTaxTreatmentKey,
      disabilityTaxTreatmentLabel: disabilityTaxTreatment.label,
      disabilityTaxRelief: disabilityTaxRelief,
      salaryTaxExemptDueToDisability: disabilityTaxTreatment.salaryTaxExempt,
      standardDeductionRate: standardDeductionRate,
      baseStandardDeductionRate: baseStandardDeductionRate,
      baseStandardDeductions: baseStandardDeductions,
      deductionComponents: deductionComponents,
      maternityPensionReduction: maternityPensionReduction,
      maternityPensionReductionRate: maternityPensionReduction ? MATERNITY_MAIN_PENSION_REDUCTION_RATE : 0,
      maternityPensionContributionBase: deductionModel.pensionBase,
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
    INSURED_STATUSES: INSURED_STATUSES,
    PERMANENT_DEDUCTION_COMPONENTS: PERMANENT_DEDUCTION_COMPONENTS,
    SUBSTITUTE_DEDUCTION_COMPONENTS: SUBSTITUTE_DEDUCTION_COMPONENTS,
    roundMoney: roundMoney,
    roundedDeductionComponents: roundedDeductionComponents,
    earningsFromOptions: earningsFromOptions,
    buildDeductionComponents: buildDeductionComponents,
    REMOTE_AREA_ALLOWANCE_MONTHLY: REMOTE_AREA_ALLOWANCE_MONTHLY,
    MATERNITY_MAIN_PENSION_REDUCTION_RATE: MATERNITY_MAIN_PENSION_REDUCTION_RATE,
    POSITION_ALLOWANCES: POSITION_ALLOWANCES,
    positionAllowanceMonthly: positionAllowanceMonthly,
    positionAllowanceLabel: positionAllowanceLabel,
    familyAllowanceMonthly: familyAllowanceMonthly,
    AGE_GROUPS: AGE_GROUPS,
    DISABILITY_TAX_TREATMENTS: DISABILITY_TAX_TREATMENTS,
    TAX_BRACKETS: TAX_BRACKETS,
    taxRateForBracket: taxRateForBracket,
    grossAnnualTax: grossAnnualTax,
    baseTaxCredit: baseTaxCredit,
    taxCredit: taxCredit,
    calculate: calculate
  });
})(window);
