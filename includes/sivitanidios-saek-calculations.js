(function (global) {
  'use strict';

  const MAX = {
    education: 23,
    formalTitles: 14,
    adultEducation: 3,
    training: 6,
    teaching: 21,
    saekTeaching: 12,
    formalTeaching: 4,
    nonFormalTeaching: 5,
    work: 10,
    other: 6,
    baseGraduate: 60,
    baseCraft: 16
  };

  const TITLE_POINTS = { aei: 10, higher_state: 7, saek: 6, secondary: 5, none: 0 };
  const RELATED_POSTGRAD = { phd: 4, master: 3, none: 0 };
  const ADULT_ED_POSTGRAD = { phd: 3, master: 2, none: 0 };
  const LANGUAGE_POINTS = { C2: 2, C1: 1.5, B2: 1, none: 0 };
  const UNEMPLOYMENT_PERCENT = { none: 0, m0_6: 2, m6_12: 4, m12_18: 6, m18_24: 8, m24plus: 10 };

  function num(v) {
    const n = Number(String(v == null ? '' : v).replace(',', '.'));
    return Number.isFinite(n) && n > 0 ? n : 0;
  }
  function round(v) { return Math.round((Number(v) + Number.EPSILON) * 100) / 100; }
  function cap(v, max) { return Math.min(Math.max(0, v), max); }

  function calculateEducation(data) {
    if (data.candidateType !== 'graduate') {
      return { titlePoints: 0, adultEducationPoints: 0, trainingPoints: 0, points: 0, details: [] };
    }
    const titleBase = TITLE_POINTS[data.qualifyingTitle] || 0;
    const relatedPostgrad = RELATED_POSTGRAD[data.relatedPostgrad] || 0;
    const titlePoints = cap(titleBase + relatedPostgrad, MAX.formalTitles);
    const adultEducationPoints = ADULT_ED_POSTGRAD[data.adultEducationPostgrad] || 0;

    const trainingSubject = cap(Math.floor(num(data.trainingSubjectHours) / 25) * 0.25, 2);
    const trainingVet = cap(Math.floor(num(data.trainingVetHours) / 25) * 0.25, 2);
    const trainingAdult = cap(Math.floor(num(data.trainingAdultHours) / 25) * 0.25, 2);
    const trainingPoints = cap(trainingSubject + trainingVet + trainingAdult, MAX.training);
    const points = cap(titlePoints + adultEducationPoints + trainingPoints, MAX.education);
    const details = [];
    if (titleBase) details.push({ label: 'Ανώτερος σχετικός βασικός τίτλος', points: titleBase });
    if (relatedPostgrad) details.push({ label: data.relatedPostgrad === 'phd' ? 'Σχετικό διδακτορικό' : 'Σχετικό μεταπτυχιακό', points: relatedPostgrad });
    if (adultEducationPoints) details.push({ label: data.adultEducationPostgrad === 'phd' ? 'Διδακτορικό στην Εκπαίδευση Ενηλίκων' : 'Μεταπτυχιακό στην Εκπαίδευση Ενηλίκων', points: adultEducationPoints });
    if (trainingSubject) details.push({ label: 'Επιμόρφωση στο διδακτικό αντικείμενο', points: trainingSubject });
    if (trainingVet) details.push({ label: 'Επιμόρφωση στην επαγγελματική εκπαίδευση/κατάρτιση', points: trainingVet });
    if (trainingAdult) details.push({ label: 'Επιμόρφωση στις αρχές Εκπαίδευσης Ενηλίκων', points: trainingAdult });
    return { titlePoints: round(titlePoints), adultEducationPoints: round(adultEducationPoints), trainingPoints: round(trainingPoints), points: round(points), details };
  }

  function calculateTeaching(data) {
    if (data.candidateType !== 'graduate') {
      return { saekPoints: 0, formalPoints: 0, nonFormalPoints: 0, points: 0, details: [] };
    }
    const saekOutside = cap(num(data.saekOutsideHours) / 150, 8);
    const saekSivitanidios = cap(num(data.saekSivitanidiosHours) / 150 * 1.2, 10);
    const saekPoints = cap(saekOutside + saekSivitanidios, MAX.saekTeaching);
    const tertiaryTeachingYears = Math.min(50, num(data.tertiaryTeachingYears));
    const primarySecondaryTeachingYears = Math.min(50, num(data.primarySecondaryTeachingYears));
    const formalPoints = cap(tertiaryTeachingYears * 0.5 + primarySecondaryTeachingYears, MAX.formalTeaching);
    const nonFormalPoints = cap(num(data.otherNonFormalHours) / 200, MAX.nonFormalTeaching);
    const points = cap(saekPoints + formalPoints + nonFormalPoints, MAX.teaching);
    const details = [];
    if (saekOutside) details.push({ label: 'Διδασκαλία σε ΣΑΕΚ/ΣΕΚ εκτός ΣΔΣΤΕ', points: round(saekOutside) });
    if (saekSivitanidios) details.push({ label: 'Διδασκαλία σε ΣΑΕΚ/ΣΕΚ Σιβιτανιδείου', points: round(saekSivitanidios) });
    if (formalPoints) details.push({ label: 'Τυπική εκπαίδευση', points: round(formalPoints) });
    if (nonFormalPoints) details.push({ label: 'Άλλα προγράμματα μη τυπικής εκπαίδευσης', points: round(nonFormalPoints) });
    return { saekPoints: round(saekPoints), formalPoints: round(formalPoints), nonFormalPoints: round(nonFormalPoints), points: round(points), details };
  }

  function calculateWork(data) {
    const months = num(data.workMonths);
    const prerequisiteMonths = data.candidateType === 'craft' ? 36 : 0;
    const scoredMonths = Math.max(0, months - prerequisiteMonths);
    const points = cap(scoredMonths / 12, MAX.work);
    return { totalMonths: round(months), prerequisiteMonths, scoredMonths: round(scoredMonths), points: round(points) };
  }

  function calculateOther(data) {
    const languagePoints = data.languageTeachingExcluded ? 0 : (LANGUAGE_POINTS[data.languageLevel] || 0);
    const computerPoints = data.computer && !data.pe86 ? 2 : 0;
    const adultTrainerPoints = data.adultTrainer ? 2 : 0;
    const points = cap(languagePoints + computerPoints + adultTrainerPoints, MAX.other);
    const warnings = [];
    const hasLanguageLevel = data.languageLevel && data.languageLevel !== 'none';
    const namedLanguage = ['english', 'french', 'german', 'italian', 'spanish'].includes(data.languageName);
    const article28Status = data.languageArticle28Status || (data.languageArticle28 === true ? 'yes' : '');
    if (data.languageTeachingExcluded && hasLanguageLevel) warnings.push('Η γλώσσα δεν μοριοδοτείται επειδή δηλώθηκε ως γλώσσα που διδάσκει ο υποψήφιος.');
    if (hasLanguageLevel && (!data.languageName || data.languageName === 'none')) warnings.push('Επίλεξε τη γλώσσα για να ελεγχθεί το απαιτούμενο δικαιολογητικό γλωσσομάθειας.');
    if (hasLanguageLevel && data.languageName && data.languageName !== 'none' && !namedLanguage && !(data.languageTitleRegistered && data.languageOfficialTranslation)) warnings.push('Για τον δηλωμένο τίτλο γλωσσομάθειας απαιτούνται καταχώρηση του ξενόγλωσσου τίτλου και επίσημη μετάφραση.');
    if (hasLanguageLevel && namedLanguage && article28Status === 'no' && !(data.languageTitleRegistered && data.languageOfficialTranslation)) warnings.push('Για τον δηλωμένο τίτλο γλωσσομάθειας απαιτούνται καταχώρηση του ξενόγλωσσου τίτλου και επίσημη μετάφραση.');
    if (data.computer && data.pe86) warnings.push('Ο κλάδος ΠΕ86 δεν μοριοδοτείται στη γνώση χειρισμού Η/Υ.');
    return { languagePoints: round(languagePoints), computerPoints, adultTrainerPoints, points: round(points), warnings };
  }

  function calculateSocial(data, base) {
    const unemploymentPercent = UNEMPLOYMENT_PERCENT[data.unemploymentBand] || 0;
    const specialKeys = ['threeChildren', 'manyChildren', 'singleParent', 'disabilityCategory'];
    const specialCount = specialKeys.reduce((sum, key) => sum + (data[key] ? 1 : 0), 0);
    const specialPercent = specialCount * 10;
    const totalPercent = unemploymentPercent + specialPercent;
    const increase = base * totalPercent / 100;
    return { unemploymentPercent, specialCount, specialPercent, totalPercent, increase: round(increase) };
  }

  function eligibility(data) {
    const blockers = [], pending = [];
    if (!data.candidateType) pending.push('Επίλεξε αν συμμετέχεις ως πτυχιούχος ή εμπειροτέχνης.');
    if (data.candidateType === 'graduate' && (!data.qualifyingTitle || data.qualifyingTitle === 'none')) pending.push('Επίλεξε τον ανώτερο σχετικό βασικό τίτλο που δηλώνεις.');
    if (data.candidateType === 'craft' && num(data.workMonths) < 36) blockers.push('Για εμπειροτέχνη απαιτούνται τουλάχιστον 3 έτη (36 μήνες) συναφούς επαγγελματικής εμπειρίας.');
    return { eligible: blockers.length === 0 && pending.length === 0, blockers, pending };
  }

  function calculateAll(data) {
    const education = calculateEducation(data);
    const teaching = calculateTeaching(data);
    const work = calculateWork(data);
    const other = calculateOther(data);
    const base = round(education.points + teaching.points + work.points + other.points);
    const social = calculateSocial(data, base);
    const final = round(base + social.increase);
    return { education, teaching, work, other, base, social, final, eligibility: eligibility(data), max: MAX };
  }

  global.SivitanidiosSaekCalc = { MAX, calculateEducation, calculateTeaching, calculateWork, calculateOther, calculateSocial, eligibility, calculateAll };
})(typeof window !== 'undefined' ? window : globalThis);
