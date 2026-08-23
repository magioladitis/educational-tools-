/*
 * Κοινή λογική υπολογισμού ακαδημαϊκών κριτηρίων Α.Σ.Ε.Π. για κλάδους ΠΕ.
 *
 * Profiles:
 * - general: 1ΓΕ/2ΓΕ/2026 και καταναλωτές που επαναχρησιμοποιούν τα ίδια κριτήρια.
 * - eep: 2ΕΑ/2025.
 * - eae: 3ΕΑ/2025, με τους ειδικούς κανόνες ΠΕ61/ΠΕ71 και ΠΕ11.
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
    trainingPoints: 2,
    eaePe6171BaseSpecializationPoints: 20,
    eaePe6171WithAdditionalMscPoints: 28,
    eaePe11SpecializationPoints: 8
  });

  const PROFILES = Object.freeze({
    general: Object.freeze({ degreeRequired: true }),
    eep: Object.freeze({ degreeRequired: false }),
    eae: Object.freeze({ degreeRequired: false })
  });

  function number(value) {
    if (typeof value === "string") value = value.trim().replace(",", ".");
    const n = Number(value);
    return Number.isFinite(n) ? n : 0;
  }

  function formatNumber(value) {
    const rounded = Math.round(Number(value) * 100) / 100;
    return Number.isInteger(rounded) ? String(rounded) : rounded.toFixed(2).replace(".", ",");
  }

  function normalizeBoolean(value) {
    return value === true || value === "yes" || value === 1 || value === "1";
  }

  function normalizeProfile(profile) {
    profile = String(profile || "general").toLowerCase();
    return Object.prototype.hasOwnProperty.call(PROFILES, profile) ? profile : "general";
  }

  function calculate(config) {
    config = config || {};
    const profile = normalizeProfile(config.profile);
    const specialty = config.specialty || "";
    const degreeGrade = number(config.degreeGrade);
    const degreeValid = degreeGrade >= 5 && degreeGrade <= 10;
    const degreeRequired = config.degreeRequired === undefined ? PROFILES[profile].degreeRequired : normalizeBoolean(config.degreeRequired);

    if (degreeRequired && !degreeValid) {
      throw new Error("Παρακαλώ συμπλήρωσε έγκυρο βαθμό βασικού τίτλου σπουδών από 5 έως 10.");
    }

    const warnings = Array.isArray(config.languageWarnings) ? config.languageWarnings.slice() : [];
    const coreDetails = [];
    const languageDetails = Array.isArray(config.languageDetails) ? config.languageDetails.slice() : [];
    const computerDetails = [];
    const trainingDetails = [];
    let corePoints = 0;

    const degreePoints = degreeValid ? degreeGrade * RULES.degreeMultiplier : 0;
    if (degreeValid) {
      corePoints += degreePoints;
      coreDetails.push("Βασικός τίτλος (" + formatNumber(degreeGrade) + " × 2,5): " + formatNumber(degreePoints) + " μόρια");
    }

    if (normalizeBoolean(config.secondDegree)) {
      corePoints += RULES.secondDegreePoints;
      coreDetails.push("Δεύτερο πτυχίο Α.Ε.Ι.: 7 μόρια");
    }
    if (normalizeBoolean(config.phd)) {
      corePoints += RULES.phdPoints;
      coreDetails.push("Διδακτορικό δίπλωμα: 40 μόρια");
    }

    const mscCount = Math.max(0, Math.min(2, Math.floor(number(config.mscCount))));
    let mscPoints = 0;
    if (profile === "eae" && (specialty === "ΠΕ61" || specialty === "ΠΕ71")) {
      mscPoints = mscCount >= 1 ? RULES.eaePe6171WithAdditionalMscPoints : RULES.eaePe6171BaseSpecializationPoints;
      corePoints += mscPoints;
      coreDetails.push(mscCount >= 1
        ? "ΠΕ61/ΠΕ71: βασικό πτυχίο Ε.Α.Ε. + επιπλέον μεταπτυχιακός τίτλος: 28 μόρια"
        : "ΠΕ61/ΠΕ71: βασικό πτυχίο Ε.Α.Ε.: 20 μόρια");
    } else {
      if (mscCount >= 1) {
        mscPoints += RULES.firstMscPoints;
        coreDetails.push("1ος μεταπτυχιακός / integrated master: 20 μόρια");
      }
      if (mscCount >= 2) {
        mscPoints += RULES.secondMscPoints;
        coreDetails.push("2ος μεταπτυχιακός / integrated master: 8 μόρια");
      }
      corePoints += mscPoints;
    }

    let specialProfilePoints = 0;
    if (profile === "eae" && specialty === "ΠΕ11" && normalizeBoolean(config.eaePe11Specialization)) {
      specialProfilePoints = RULES.eaePe11SpecializationPoints;
      corePoints += specialProfilePoints;
      coreDetails.push("ΠΕ11 — προβλεπόμενη κύρια ειδικότητα Ε.Α.Ε./Ειδικής Φυσικής Αγωγής: 8 μόρια");
    }

    const languagePoints = Math.max(0, number(config.languagePoints));
    let computerPoints = 0;
    if (normalizeBoolean(config.computer)) {
      if (specialty === "ΠΕ86") warnings.push("Η γνώση Η/Υ δεν μοριοδοτείται για τον κλάδο ΠΕ86.");
      else {
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
    if (rawPoints > RULES.maxAcademicPoints) warnings.push("Στα Ακαδημαϊκά Προσόντα εφαρμόστηκε το ανώτατο όριο των 120 μορίων.");

    return {
      profile, points, rawPoints, degreeGrade, degreeValid, degreePoints, corePoints,
      mscCount, mscPoints, specialProfilePoints, languagePoints, computerPoints, trainingPoints,
      coreDetails, languageDetails, computerDetails, trainingDetails,
      details: coreDetails.concat(languageDetails, computerDetails, trainingDetails), warnings
    };
  }

  global.EducationAcademic = Object.freeze({ RULES, PROFILES, calculate });
})(typeof window !== "undefined" ? window : globalThis);
