/*
 * Απόσπαση εκπαιδευτικών στο εξωτερικό
 * Βάση: Υ.Α. 83046/Η2/30-06-2020 (Β' 2687), άρθρο 3.
 */
(function (global) {
  "use strict";

  const MAIN_LANGUAGE_POINTS = Object.freeze({
    "": 0,
    b2: 0,
    c1: 30,
    c2: 50
  });

  const ALTERNATIVE_LANGUAGE_POINTS = Object.freeze({
    "": 0,
    b2: 0,
    c1: 20,
    c2: 30
  });

  const SECOND_LANGUAGE_POINTS = Object.freeze({
    none: 0,
    b2: 10,
    c1: 20,
    c2: 30
  });

  const LEVEL_RANK = Object.freeze({
    "": 0,
    b2: 2,
    c1: 3,
    c2: 4
  });

  function number(value) {
    const n = Number(value);
    return Number.isFinite(n) ? n : 0;
  }

  function nonNegativeInteger(value) {
    return Math.max(0, Math.floor(number(value)));
  }

  function academicPoints(options = {}) {
    return (options.phd ? 50 : 0)
      + (options.master ? 25 : 0)
      + (options.secondMaster ? 15 : 0)
      + (options.secondDegree ? 15 : 0);
  }

  function primaryLanguagePoints(tableType, level) {
    if (tableType === "main") return MAIN_LANGUAGE_POINTS[level] || 0;
    if (tableType === "alternative") return ALTERNATIVE_LANGUAGE_POINTS[level] || 0;
    return 0;
  }

  function secondLanguagePoints(level, isDistinct) {
    if (level === "none" || !level) return 0;
    return isDistinct ? (SECOND_LANGUAGE_POINTS[level] || 0) : 0;
  }

  function calculate(options = {}) {
    const tableType = options.tableType || "";
    const educationYears = Math.min(global.EducationCore.MAX_SERVICE_YEARS, nonNegativeInteger(options.educationYears));
    const teachingYears = Math.min(global.EducationCore.MAX_SERVICE_YEARS, nonNegativeInteger(options.teachingYears));
    const branchAllowed = options.branchAllowed || "";
    const blockingIssue = options.blockingIssue || "";
    const bilingualPosition = options.bilingualPosition || "";

    const primaryLevel = options.primaryLevel || "";
    const alternativeLanguage = options.alternativeLanguage || "";
    const alternativeDifferentFromCountry = options.alternativeDifferentFromCountry || "";
    const hostBilingualLevel = options.hostBilingualLevel || "";

    const secondLanguageLevel = options.secondLanguageLevel || "none";
    const secondLanguageDistinct = options.secondLanguageDistinct || "";

    const academic = academicPoints(options);
    const primary = primaryLanguagePoints(tableType, primaryLevel);
    const second = secondLanguagePoints(
      secondLanguageLevel,
      secondLanguageDistinct === "yes"
    );

    const unanswered = [];
    const issues = [];
    const warnings = [];

    if (options.specialtySelected !== true) {
      unanswered.push("ειδικότητα (Παράρτημα ΙΙΙ)");
    } else if (options.preferenceSelected !== true) {
      unanswered.push("1η προτίμηση χώρας / περιοχής (Παράρτημα ΙΙΙ)");
    } else if (branchAllowed === "no") {
      issues.push("Η ειδικότητα δεν περιλαμβάνεται για τη συγκεκριμένη χώρα/περιοχή στο Παράρτημα ΙΙΙ της πρόσκλησης.");
    }

    if (options.educationYearsAnswered !== true) {
      unanswered.push("5ετής εκπαιδευτική υπηρεσία");
    } else if (educationYears < 5) {
      issues.push("Απαιτούνται τουλάχιστον 5 έτη εκπαιδευτικής υπηρεσίας.");
    }

    if (options.teachingYearsAnswered !== true) {
      unanswered.push("3ετής διδακτική υπηρεσία");
    } else if (teachingYears < 3) {
      issues.push("Απαιτούνται τουλάχιστον 3 έτη διδακτικής υπηρεσίας μετά το ΦΕΚ διορισμού.");
    }

    if (!tableType) {
      unanswered.push("τύπος αξιολογικού πίνακα");
    }

    if (tableType === "main") {
      if (!primaryLevel) unanswered.push("επίπεδο γλώσσας χώρας υποδοχής");
      else if (LEVEL_RANK[primaryLevel] < LEVEL_RANK.b2) {
        issues.push("Για τον Βασικό Πίνακα απαιτείται τουλάχιστον Β2 στη γλώσσα της χώρας υποδοχής.");
      }
    }

    if (tableType === "alternative") {
      if (!alternativeLanguage) unanswered.push("εναλλακτική γλώσσα");
      if (!primaryLevel) unanswered.push("επίπεδο εναλλακτικής γλώσσας");
      else if (LEVEL_RANK[primaryLevel] < LEVEL_RANK.b2) {
        issues.push("Για τον Εναλλακτικό Πίνακα απαιτείται τουλάχιστον Β2 στην αγγλική, γαλλική ή γερμανική.");
      }

      if (!alternativeDifferentFromCountry) {
        unanswered.push("αν η εναλλακτική γλώσσα είναι διαφορετική από τη γλώσσα της χώρας");
      } else if (alternativeDifferentFromCountry === "no") {
        issues.push("Ο Εναλλακτικός Πίνακας χρησιμοποιείται όταν η αγγλική/γαλλική/γερμανική δεν είναι η γλώσσα της χώρας απόσπασης.");
      }
    }

    if (!bilingualPosition) {
      unanswered.push("αν η θέση απαιτεί διδασκαλία σε δύο γλώσσες");
    } else if (bilingualPosition === "yes") {
      const bilingualLevel = tableType === "main" ? primaryLevel : hostBilingualLevel;
      if (tableType === "alternative" && !hostBilingualLevel) {
        unanswered.push("επίπεδο γλώσσας χώρας για δίγλωσση διδασκαλία");
      } else if (LEVEL_RANK[bilingualLevel] < LEVEL_RANK.c1) {
        issues.push("Για περίπτωση διδασκαλίας σε δύο γλώσσες απαιτείται τουλάχιστον Γ1/C1 στη γλώσσα της χώρας υποδοχής.");
      }
    }

    if (!blockingIssue) {
      unanswered.push("κωλύματα απόσπασης");
    } else if (blockingIssue === "yes") {
      issues.push("Δηλώθηκε πιθανό κώλυμα απόσπασης. Απαιτείται έλεγχος των όρων της τρέχουσας πρόσκλησης.");
    }

    if (secondLanguageLevel !== "none") {
      if (!secondLanguageDistinct) {
        warnings.push("Για να μοριοδοτηθεί η δεύτερη ξένη γλώσσα, επιβεβαίωσε ότι είναι διαφορετική από τη γλώσσα που χρησιμοποιείται για τον αξιολογικό πίνακα.");
      } else if (secondLanguageDistinct === "no") {
        warnings.push("Η ίδια γλώσσα δεν μπορεί να μοριοδοτηθεί ξανά ως δεύτερη ξένη γλώσσα.");
      }
    }

    const eligible = unanswered.length === 0 && issues.length === 0;

    const total = academic + primary + second;
    const theoreticalMax = tableType === "main"
      ? 185
      : (tableType === "alternative" ? 165 : 0);

    return {
      tableType,
      academic,
      primaryLanguagePoints: primary,
      secondLanguagePoints: second,
      total,
      theoreticalMax,
      educationYears,
      teachingYears,
      eligible,
      unanswered,
      issues,
      warnings
    };
  }

  global.AbroadSecondment = Object.freeze({
    MAIN_LANGUAGE_POINTS,
    ALTERNATIVE_LANGUAGE_POINTS,
    SECOND_LANGUAGE_POINTS,
    academicPoints,
    primaryLanguagePoints,
    secondLanguagePoints,
    calculate
  });
})(window);
