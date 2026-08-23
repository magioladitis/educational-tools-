/*
 * Κοινή λογική υπολογισμού ακαδημαϊκών κριτηρίων Α.Σ.Ε.Π. για κλάδους ΠΕ.
 *
 * Η μοριοδότηση ξένων γλωσσών ΔΕΝ υλοποιείται εδώ. Το μοναδικό source of truth
 * είναι το includes/language-calculations.js και το αποτέλεσμα περνά εδώ ως
 * languagePoints / languageDetails / languageWarnings.
 */
(function (global) {
  "use strict";

  const RULES = Object.freeze({
    maxAcademicPoints: 120,
    degreeMultiplier: 2.5,
    secondDegreePoints: 7,
    phdPoints: 40,
    firstMscPoints: 20,
    secondMscPoints: 8,
    computerPoints: 4,
    trainingPoints: 2
  });

  function number(value) {
    if (typeof value === "string") {
      value = value.trim().replace(",", ".");
    }
    const n = Number(value);
    return Number.isFinite(n) ? n : 0;
  }

  function formatNumber(value) {
    const rounded = Math.round(Number(value) * 100) / 100;
    return Number.isInteger(rounded)
      ? String(rounded)
      : rounded.toFixed(2).replace(".", ",");
  }

  function normalizeBoolean(value) {
    return value === true || value === "yes" || value === 1 || value === "1";
  }

  function calculate(config) {
    config = config || {};

    const specialty = config.specialty || "";
    const degreeGrade = number(config.degreeGrade);

    if (!degreeGrade || degreeGrade < 5 || degreeGrade > 10) {
      throw new Error("Παρακαλώ συμπλήρωσε έγκυρο βαθμό βασικού τίτλου σπουδών από 5 έως 10.");
    }

    const warnings = Array.isArray(config.languageWarnings) ? config.languageWarnings.slice() : [];
    const coreDetails = [];
    const languageDetails = Array.isArray(config.languageDetails) ? config.languageDetails.slice() : [];
    const computerDetails = [];
    const trainingDetails = [];

    let corePoints = 0;

    const degreePoints = degreeGrade * RULES.degreeMultiplier;
    corePoints += degreePoints;
    coreDetails.push(
      "Βασικός τίτλος (" + formatNumber(degreeGrade) + " × 2,5): " +
      formatNumber(degreePoints) + " μόρια"
    );

    if (normalizeBoolean(config.secondDegree)) {
      corePoints += RULES.secondDegreePoints;
      coreDetails.push("Δεύτερο πτυχίο Α.Ε.Ι.: 7 μόρια");
    }

    if (normalizeBoolean(config.phd)) {
      corePoints += RULES.phdPoints;
      coreDetails.push("Διδακτορικό δίπλωμα: 40 μόρια");
    }

    const mscCount = Math.max(0, Math.min(2, Math.floor(number(config.mscCount))));
    if (mscCount >= 1) {
      corePoints += RULES.firstMscPoints;
      coreDetails.push("1ος μεταπτυχιακός / integrated master: 20 μόρια");
    }
    if (mscCount >= 2) {
      corePoints += RULES.secondMscPoints;
      coreDetails.push("2ος μεταπτυχιακός / integrated master: 8 μόρια");
    }

    const languagePoints = Math.max(0, number(config.languagePoints));

    let computerPoints = 0;
    if (normalizeBoolean(config.computer)) {
      if (specialty === "ΠΕ86") {
        warnings.push("Η γνώση Η/Υ δεν μοριοδοτείται για τον κλάδο ΠΕ86.");
      } else {
        computerPoints = RULES.computerPoints;
        computerDetails.push("Πιστοποιημένη γνώση Η/Υ / ΤΠΕ Α’ επιπέδου: 4 μόρια");
      }
    }

    let trainingPoints = 0;
    if (normalizeBoolean(config.training)) {
      trainingPoints = RULES.trainingPoints;
      trainingDetails.push("Επιμόρφωση τουλάχιστον 300 ωρών και 7 μηνών: 2 μόρια");
    }

    const rawPoints = corePoints + languagePoints + computerPoints + trainingPoints;
    const points = Math.min(rawPoints, RULES.maxAcademicPoints);

    if (rawPoints > RULES.maxAcademicPoints) {
      warnings.push("Στα Ακαδημαϊκά Προσόντα εφαρμόστηκε το ανώτατο όριο των 120 μορίων.");
    }

    return {
      points: points,
      rawPoints: rawPoints,
      degreePoints: degreePoints,
      corePoints: corePoints,
      languagePoints: languagePoints,
      computerPoints: computerPoints,
      trainingPoints: trainingPoints,
      coreDetails: coreDetails,
      languageDetails: languageDetails,
      computerDetails: computerDetails,
      trainingDetails: trainingDetails,
      details: coreDetails.concat(languageDetails, computerDetails, trainingDetails),
      warnings: warnings
    };
  }

  global.EducationAcademic = Object.freeze({
    RULES: RULES,
    calculate: calculate
  });
})(typeof window !== "undefined" ? window : globalThis);
