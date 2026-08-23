/*
 * Μοριοδότηση και αντιστοίχιση ειδικοτήτων για αποσπάσεις στα ΣΔΕ.
 * Βάση: Υ.Α. 88422/Κ1, ΦΕΚ Β' 4088/03.07.2026, όπως διορθώθηκε με ΦΕΚ Β' 4199/10.07.2026.
 */
(function (global) {
  'use strict';

  const MAX = Object.freeze({
    formalQualifications: 18,
    training: 4,
    education: 22,
    teachingExperience: 13,
    otherQualifications: 5,
    total: 40
  });

  const SPECIALTIES = Object.freeze({
    'PE01': 'ΠΕ01 Θεολόγων',
    'PE02': 'ΠΕ02 Φιλολόγων',
    'PE03': 'ΠΕ03 Μαθηματικών',
    'PE04.01': 'ΠΕ04.01 Φυσικών',
    'PE04.02': 'ΠΕ04.02 Χημικών',
    'PE04.03': 'ΠΕ04.03 Φυσιογνωστών',
    'PE04.04': 'ΠΕ04.04 Βιολόγων',
    'PE04.05': 'ΠΕ04.05 Γεωλόγων',
    'PE06': 'ΠΕ06 Αγγλικής',
    'PE70': 'ΠΕ70 Δασκάλων',
    'PE78': 'ΠΕ78 Κοινωνικών Επιστημών',
    'PE80': 'ΠΕ80 Οικονομίας',
    'PE85': 'ΠΕ85 Χημικών Μηχανικών',
    'PE86': 'ΠΕ86 Πληροφορικής',
    'PE87.01': 'ΠΕ87.01 Ιατρικής',
    'PE88.01': 'ΠΕ88.01 Γεωπονίας',
    'PE88.05': 'ΠΕ88.05 Φυσικού Περιβάλλοντος',
    'OTHER': 'Άλλη ειδικότητα'
  });

  const LITERACIES = Object.freeze([
    'Ελληνική Γλώσσα',
    'Μαθηματικά',
    'Πληροφορική',
    'Αγγλική Γλώσσα',
    'Κοινωνική Εκπαίδευση',
    'Επιστημονικός Γραμματισμός',
    'Περιβαλλοντική Εκπαίδευση',
    'Τμήματα προετοιμασίας για απολυτήριο Δημοτικού'
  ]);

  function num(value) {
    const n = Number(value);
    return Number.isFinite(n) && n > 0 ? n : 0;
  }

  function cap(value, max) {
    return Math.min(Math.max(num(value), 0), max);
  }

  function phdPoints(value) {
    return value === 'adult' ? 11 : value === 'other' ? 10 : 0;
  }

  function masterPoints(value) {
    return value === 'adult' ? 8 : value === 'other' ? 7 : 0;
  }

  function secondPhdPoints(value) {
    return value === 'adult' ? 2 : value === 'other' ? 1 : 0;
  }

  function secondMasterPoints(value) {
    return value === 'adult' ? 1 : value === 'other' ? 0 : 0;
  }

  function calculateEducation(data) {
    const details = [];
    const warnings = [];

    const phd = phdPoints(data.phd);
    const masterRaw = masterPoints(data.master);
    const master = phd > 0 ? 0 : masterRaw;
    const secondDegree = data.secondDegree ? 4 : 0;
    const secondPhd = secondPhdPoints(data.secondPhd);
    const secondMaster = secondMasterPoints(data.secondMaster);

    if (phd) details.push({ label: 'Διδακτορικό', points: phd });
    if (master) details.push({ label: 'Μεταπτυχιακό', points: master });
    if (phd && masterRaw) warnings.push('Το μεταπτυχιακό δεν προσμετρήθηκε επειδή δηλώθηκε και διδακτορικό.');
    if (secondDegree) details.push({ label: 'Δεύτερο πτυχίο', points: 4 });
    if (secondPhd) details.push({ label: 'Δεύτερο διδακτορικό', points: secondPhd });
    if (secondMaster) details.push({ label: 'Δεύτερο μεταπτυχιακό', points: secondMaster });

    if (secondPhd && !phd) warnings.push('Δήλωσες δεύτερο διδακτορικό χωρίς πρώτο διδακτορικό. Έλεγξε την καταχώριση.');
    if (secondMaster && !masterRaw) warnings.push('Δήλωσες δεύτερο μεταπτυχιακό χωρίς πρώτο μεταπτυχιακό. Έλεγξε την καταχώριση.');

    const formalRaw = phd + master + secondDegree + secondPhd + secondMaster;
    const formal = Math.min(formalRaw, MAX.formalQualifications);

    const sdeTrainingHours = num(data.sdeTrainingHours);
    const adultTrainingHours = num(data.adultTrainingHours);
    const sdeTraining = sdeTrainingHours > 0 && sdeTrainingHours < 15 ? 0 : Math.min(sdeTrainingHours / 100 * 0.25, 2);
    const adultTraining = adultTrainingHours > 0 && adultTrainingHours < 15 ? 0 : Math.min(adultTrainingHours / 100 * 0.25, 2);
    if (sdeTrainingHours > 0 && sdeTrainingHours < 15) warnings.push('Η επιμόρφωση σε θέματα ΣΔΕ είναι μικρότερη των 15 ωρών και δεν μοριοδοτείται.');
    if (adultTrainingHours > 0 && adultTrainingHours < 15) warnings.push('Η επιμόρφωση στις αρχές Εκπαίδευσης Ενηλίκων είναι μικρότερη των 15 ωρών και δεν μοριοδοτείται.');
    if (sdeTraining > 0) details.push({ label: 'Επιμόρφωση σε θέματα ΣΔΕ', points: sdeTraining });
    if (adultTraining > 0) details.push({ label: 'Επιμόρφωση στις αρχές Εκπαίδευσης Ενηλίκων', points: adultTraining });

    const training = Math.min(sdeTraining + adultTraining, MAX.training);
    const total = Math.min(formal + training, MAX.education);

    return { formal, training, total, details, warnings };
  }

  function calculateExperience(data) {
    const details = [];

    const sdeYears = num(data.sdeYears);
    const sdeHourlyHours = num(data.sdeHourlyHours);
    const sdePoints = Math.min(sdeYears + sdeHourlyHours / 650, 5);
    if (sdePoints > 0) details.push({ label: 'Διδακτική εμπειρία σε ΣΔΕ', points: sdePoints });

    const adultHours = num(data.adultEducationHours);
    const adultPoints = Math.min(adultHours / 100 * 0.5, 4);
    if (adultPoints > 0) details.push({ label: 'Εκπαίδευση Ενηλίκων εκτός ΣΔΕ', points: adultPoints });

    const formalYearsTotal = Math.floor(num(data.formalEducationYears));
    const formalPoints = Math.min(formalYearsTotal, 4);
    if (formalPoints > 0) details.push({ label: 'Διδακτική εμπειρία στην τυπική εκπαίδευση', points: formalPoints });

    const raw = sdePoints + adultPoints + formalPoints;
    return {
      sdePoints,
      adultPoints,
      formalPoints,
      formalYearsTotal,
      total: Math.min(raw, MAX.teachingExperience),
      details
    };
  }

  const LEGACY_LANGUAGE_CODES = Object.freeze({
    english: 'en',
    french: 'fr',
    german: 'de',
    italian: 'it',
    spanish: 'es',
    other1: 'other',
    other2: 'other'
  });

  const LEGACY_LANGUAGE_LEVELS = Object.freeze({
    none: 'none',
    B2: 'good',
    C1: 'very_good',
    C2: 'excellent'
  });

  function languageEntries(data) {
    if (Array.isArray(data.languages)) return data.languages;
    // Backwards-compatible adapter for older callers/tests. Scoring remains
    // exclusively in EducationLanguages.
    return [
      {
        language: LEGACY_LANGUAGE_CODES[data.language1] || data.language1 || '',
        otherText: data.languageOther1 || (data.language1 === 'other1' ? 'Άλλη γλώσσα 1' : ''),
        level: LEGACY_LANGUAGE_LEVELS[data.languageLevel1] || data.languageLevel1 || 'none'
      },
      {
        language: LEGACY_LANGUAGE_CODES[data.language2] || data.language2 || '',
        otherText: data.languageOther2 || (data.language2 === 'other2' ? 'Άλλη γλώσσα 2' : ''),
        level: LEGACY_LANGUAGE_LEVELS[data.languageLevel2] || data.languageLevel2 || 'none'
      }
    ];
  }

  function calculateLanguages(data) {
    if (!global.EducationLanguages || typeof global.EducationLanguages.calculate !== 'function') {
      throw new Error('Το κοινό module language-calculations.js πρέπει να φορτωθεί πριν από το sde-calculations.js.');
    }

    const result = global.EducationLanguages.calculate(
      'sde_secondment',
      languageEntries(data || {}),
      { specialty: (data && data.specialty) || '' }
    );
    const LEVEL_CODES = { good: 'B2', very_good: 'C1', excellent: 'C2' };
    return {
      points: result.points,
      details: result.accepted.map(function (item) {
        return {
          label: item.position + 'η ξένη γλώσσα (' + (LEVEL_CODES[item.level] || item.level) + ')',
          points: item.points
        };
      }),
      warnings: result.warnings,
      accepted: result.accepted,
      excludedLanguage: result.excludedLanguage
    };
  }

  function calculateOther(data) {
    const language = calculateLanguages(data);
    const specialty = global.EducationCore.normalizeSpecialtyCode(data.specialty);
    const computer = (data.computer || specialty === 'ΠΕ86') ? 2 : 0;
    const details = language.details.slice();
    if (computer) details.push({ label: specialty === 'ΠΕ86' ? 'Γνώσεις Η/Υ (τεκμαίρονται για ΠΕ86)' : 'Γνώσεις χειρισμού Η/Υ / ΤΠΕ Α΄ επιπέδου', points: 2 });
    return {
      languagePoints: language.points,
      computerPoints: computer,
      total: Math.min(language.points + computer, MAX.otherQualifications),
      details,
      warnings: language.warnings
    };
  }

  function assignmentsFor(specialty, flags) {
    specialty = global.EducationCore.normalizeSpecialtyCode(specialty);
    flags = flags || {};
    const result = [];
    const add = (literacy, assignment, note) => result.push({ literacy, assignment, note: note || '' });

    if (specialty === 'ΠΕ02') {
      add('Ελληνική Γλώσσα', 'Α΄ ανάθεση');
      add('Κοινωνική Εκπαίδευση', 'Β΄ ανάθεση');
    }
    if (specialty === 'ΠΕ03') {
      add('Μαθηματικά', 'Α΄ ανάθεση');
      add('Επιστημονικός Γραμματισμός', 'Β΄ ανάθεση');
    }
    if (['ΠΕ04.01','ΠΕ04.02','ΠΕ04.03','ΠΕ04.04','ΠΕ04.05'].includes(specialty)) {
      if (flags.mathOrInformaticsDegree) {
        add('Μαθηματικά', 'Β΄ ανάθεση', 'Το ΦΕΚ αναφέρει την προϋπόθεση «με πτυχίο Μαθηματικών ή Πληροφορικής».');
      } else {
        add('Μαθηματικά', 'Β΄ ανάθεση υπό προϋπόθεση', 'Απαιτείται η προϋπόθεση πτυχίου που αναγράφεται στο ΦΕΚ.');
      }
      add('Επιστημονικός Γραμματισμός', 'Α΄ ανάθεση');
      if (specialty === 'ΠΕ04.05') add('Περιβαλλοντική Εκπαίδευση', 'Α΄ ανάθεση');
      if (['ΠΕ04.01','ΠΕ04.02','ΠΕ04.03','ΠΕ04.04'].includes(specialty)) add('Περιβαλλοντική Εκπαίδευση', 'Β΄ ανάθεση');
    }
    if (specialty === 'ΠΕ86') {
      add('Πληροφορική', 'Α΄ ανάθεση');
      if (flags.mathOrInformaticsDegree) {
        add('Μαθηματικά', 'Β΄ ανάθεση', 'Το ΦΕΚ αναφέρει την προϋπόθεση «με πτυχίο Μαθηματικών ή Πληροφορικής».');
      } else {
        add('Μαθηματικά', 'Β΄ ανάθεση υπό προϋπόθεση', 'Απαιτείται η προϋπόθεση πτυχίου που αναγράφεται στο ΦΕΚ.');
      }
    }
    if (specialty === 'ΠΕ06') add('Αγγλική Γλώσσα', 'Α΄ ανάθεση');
    if (specialty === 'ΠΕ78') add('Κοινωνική Εκπαίδευση', 'Α΄ ανάθεση');
    if (specialty === 'ΠΕ01') add('Κοινωνική Εκπαίδευση', 'Β΄ ανάθεση');
    if (specialty === 'ΠΕ80') {
      add('Κοινωνική Εκπαίδευση', 'Β΄ ανάθεση', flags.formerPE09or15 ? 'Προτεραιότητα λόγω πτυχίου που αντιστοιχεί σε πρώην ΠΕ09/ΠΕ15.' : 'Προτεραιότητα δίνεται σε πτυχία που αντιστοιχούν σε πρώην ΠΕ09/ΠΕ15.');
    }
    if (specialty === 'ΠΕ85') {
      add('Επιστημονικός Γραμματισμός', 'Α΄ ανάθεση', flags.formerPE1208 ? 'Προτεραιότητα λόγω πτυχίου πρώην ΠΕ12.08.' : 'Προτεραιότητα δίνεται σε πτυχία που αντιστοιχούν σε πρώην ΠΕ12.08.');
      add('Περιβαλλοντική Εκπαίδευση', 'Β΄ ανάθεση', flags.formerPE1208 ? 'Προτεραιότητα λόγω πτυχίου πρώην ΠΕ12.08.' : 'Προτεραιότητα δίνεται σε πτυχία που αντιστοιχούν σε πρώην ΠΕ12.08.');
    }
    if (specialty === 'ΠΕ87.01') add('Επιστημονικός Γραμματισμός', 'Β΄ ανάθεση');
    if (specialty === 'ΠΕ88.01') {
      add('Επιστημονικός Γραμματισμός', 'Β΄ ανάθεση');
      add('Περιβαλλοντική Εκπαίδευση', 'Α΄ ανάθεση');
    }
    if (specialty === 'ΠΕ88.05') add('Περιβαλλοντική Εκπαίδευση', 'Α΄ ανάθεση');
    if (specialty === 'ΠΕ70') add('Τμήματα προετοιμασίας για απολυτήριο Δημοτικού', 'Ειδική πρόβλεψη άρθρου 5');

    return result;
  }

  function calculateAll(data) {
    const education = calculateEducation(data);
    const experience = calculateExperience(data);
    const other = calculateOther(data);
    const raw = education.total + experience.total + other.total;
    return {
      education,
      experience,
      other,
      total: Math.min(raw, MAX.total),
      assignments: assignmentsFor(data.specialty, data.flags || {}),
      eligibilitySchoolYears: num(data.eligibilitySchoolYears),
      eligibleByTwoYears: num(data.eligibilitySchoolYears) >= 2,
      max: MAX
    };
  }

  global.SDECalculator = Object.freeze({
    MAX,
    SPECIALTIES,
    LITERACIES,
    calculateEducation,
    calculateExperience,
    calculateOther,
    calculateLanguages,
    assignmentsFor,
    calculateAll
  });
})(typeof window !== 'undefined' ? window : globalThis);
