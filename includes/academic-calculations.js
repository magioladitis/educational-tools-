/*
 * Κοινή λογική υπολογισμού ακαδημαϊκών κριτηρίων Α.Σ.Ε.Π.
 * για 1ΓΕ/2026, 2ΓΕ/2026 και ΔΗΜ.Ω.Σ. αναπληρωτών (κλάδοι ΠΕ).
 *
 * Το κοινό ανώτατο όριο των Ακαδημαϊκών Προσόντων είναι 120 μόρια
 * και εφαρμόζεται μετά την πρόσθεση όλων των επιμέρους ακαδημαϊκών κριτηρίων.
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
    maxLanguages: 2
  });

  const LANGUAGE_NAMES = Object.freeze({
    en: "Αγγλική",
    fr: "Γαλλική",
    de: "Γερμανική",
    it: "Ιταλική",
    es: "Ισπανική",
    other: "Άλλη ξένη γλώσσα",
    other2: "Άλλη ξένη γλώσσα"
  });

  const LEVEL_POINTS = Object.freeze({
    none: 0,
    good: 3,
    very_good: 5,
    excellent: 7
  });

  const LEVEL_NAMES = Object.freeze({
    none: "Καμία",
    good: "Καλή",
    very_good: "Πολύ καλή",
    excellent: "Άριστη"
  });

  const EXCLUDED_LANGUAGE_BY_SPECIALTY = Object.freeze({
    "ΠΕ05": "fr",
    "ΠΕ06": "en",
    "ΠΕ07": "de",
    "ΠΕ34": "it",
    "ΠΕ40": "es"
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

  function calculateLanguages(specialty, entries) {
    const warnings = [];
    const details = [];
    const bestByLanguage = {};

    (entries || []).forEach(entry => {
      const language = entry && entry.language ? entry.language : "";
      const level = entry && entry.level ? entry.level : "none";

      if (!language || level === "none") return;

      const points = LEVEL_POINTS[level] || 0;
      if (!points) return;

      if (EXCLUDED_LANGUAGE_BY_SPECIALTY[specialty] === language) {
        warnings.push(
          (LANGUAGE_NAMES[language] || "Η ξένη γλώσσα") +
          ": δεν μοριοδοτείται για τον κλάδο " + specialty + "."
        );
        return;
      }

      // Τα πεδία other και other2 αντιπροσωπεύουν διαφορετικές μη κατονομαζόμενες γλώσσες.
      const key = language;
      const existing = bestByLanguage[key];

      if (!existing || points > existing.points) {
        bestByLanguage[key] = {
          points,
          label: (LANGUAGE_NAMES[language] || language) + " - " + (LEVEL_NAMES[level] || level)
        };
      }
    });

    const counted = Object.values(bestByLanguage)
      .sort((a, b) => b.points - a.points)
      .slice(0, RULES.maxLanguages);

    counted.forEach(item => {
      details.push(item.label + ": " + formatNumber(item.points) + " μόρια");
    });

    return {
      points: counted.reduce((sum, item) => sum + item.points, 0),
      details,
      warnings
    };
  }

  function calculate(config) {
    config = config || {};

    const specialty = config.specialty || "";
    const degreeGrade = number(config.degreeGrade);

    if (!degreeGrade || degreeGrade < 5 || degreeGrade > 10) {
      throw new Error("Παρακαλώ συμπλήρωσε έγκυρο βαθμό βασικού τίτλου σπουδών από 5 έως 10.");
    }

    const warnings = [];
    const coreDetails = [];
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

    const languages = calculateLanguages(specialty, config.languages || []);
    warnings.push(...languages.warnings);

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

    const rawPoints = corePoints + languages.points + computerPoints + trainingPoints;
    const points = Math.min(rawPoints, RULES.maxAcademicPoints);

    if (rawPoints > RULES.maxAcademicPoints) {
      warnings.push("Στα Ακαδημαϊκά Προσόντα εφαρμόστηκε το ανώτατο όριο των 120 μορίων.");
    }

    return {
      points,
      rawPoints,
      corePoints,
      languagePoints: languages.points,
      computerPoints,
      trainingPoints,
      coreDetails,
      languageDetails: languages.details,
      computerDetails,
      trainingDetails,
      details: [
        ...coreDetails,
        ...languages.details,
        ...computerDetails,
        ...trainingDetails
      ],
      warnings
    };
  }

  global.EducationAcademic = Object.freeze({
    RULES,
    LANGUAGE_NAMES,
    LEVEL_POINTS,
    LEVEL_NAMES,
    EXCLUDED_LANGUAGE_BY_SPECIALTY,
    calculateLanguages,
    calculate
  });
})(window);
