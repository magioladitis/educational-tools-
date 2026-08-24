/*
 * Μοριοδότηση Διευθυντών / Υποδιευθυντών Σχολείων Δεύτερης Ευκαιρίας.
 * Βάση: Υ.Α. 70621/Κ1, ΦΕΚ Β' 3037/19.06.2025, άρθρο 4.
 */
(function (global) {
  'use strict';

  const ROLE = Object.freeze({
    director: Object.freeze({
      label: 'Διευθυντής Σ.Δ.Ε.',
      minServiceYears: 8,
      requiredTeachingHours: 3,
      max: Object.freeze({ formal: 25, teaching: 20, admin: 25, training: 5, interview: 25, criteria: 75, total: 100 }),
      teaching: Object.freeze({ sde: 10, adult: 6, school: 4 }),
      admin: Object.freeze({ sdeDirector: 12, sdeDeputy: 8, other: 5 })
    }),
    deputy: Object.freeze({
      label: 'Υποδιευθυντής Σ.Δ.Ε.',
      minServiceYears: 6,
      requiredTeachingHours: 9,
      max: Object.freeze({ formal: 25, teaching: 25, admin: 20, training: 5, interview: 0, criteria: 75, total: 75 }),
      teaching: Object.freeze({ sde: 12, adult: 8, school: 5 }),
      admin: Object.freeze({ sdeDirector: 10, sdeDeputy: 6, other: 4 })
    })
  });

  const MAX_EXPERIENCE_YEARS = 40;

  function n(value) {
    const x = Number(value);
    return Number.isFinite(x) && x > 0 ? x : 0;
  }

  function cap(value, max) {
    return Math.min(Math.max(n(value), 0), max);
  }

  function experienceYears(value) {
    return Math.min(MAX_EXPERIENCE_YEARS, n(value));
  }

  function round2(value) {
    return Math.round((Number(value) + Number.EPSILON) * 100) / 100;
  }

  function roleConfig(role) {
    return ROLE[role] || null;
  }

  function qualificationPoints(value, relevant, other) {
    if (value === 'relevant') return relevant;
    if (value === 'other') return other;
    return 0;
  }

  function calculateLanguages(data) {
    if (!global.EducationLanguages || typeof global.EducationLanguages.calculate !== 'function') {
      throw new Error('Απαιτείται το κοινό EducationLanguages πριν από το SDELeadershipCalc.');
    }

    const shared = global.EducationLanguages.calculate('sde_leadership', [
      {
        language: data.language1 || '',
        level: data.languageLevel1 || 'none',
        excluded: !!data.languageAppointment1,
        exclusionWarning: 'Μία δηλωμένη ξένη γλώσσα δεν μοριοδοτήθηκε επειδή αποτέλεσε προσόν διορισμού.'
      },
      {
        language: data.language2 || '',
        level: data.languageLevel2 || 'none',
        excluded: !!data.languageAppointment2,
        exclusionWarning: 'Μία δηλωμένη ξένη γλώσσα δεν μοριοδοτήθηκε επειδή αποτέλεσε προσόν διορισμού.'
      }
    ]);

    const levelCode = { good: 'B2', very_good: 'C1', excellent: 'C2' };
    const details = (shared.detailItems || []).map(function (item) {
      return {
        label: String(item.position || '') + 'η ξένη γλώσσα (' + (levelCode[item.level] || item.levelLabel || '') + ')',
        points: item.points
      };
    });

    return {
      points: round2(shared.points),
      details: details,
      warnings: (shared.warnings || []).slice(),
      shared: shared
    };
  }

  function calculateFormal(data) {
    const details = [];
    const warnings = [];

    const phd = qualificationPoints(data.phd, 8, 6);
    const master = qualificationPoints(data.master, 4, 2);
    const esdda = data.esdda ? 5 : 0;
    const secondDegree = data.secondDegree ? 3 : 0;
    const languages = calculateLanguages(data);

    if (phd) details.push({ label: 'Διδακτορικό', points: phd });
    if (esdda) details.push({ label: 'Απόφοιτος ΕΣΔΔΑ', points: esdda });
    if (master) details.push({ label: 'Μεταπτυχιακό / integrated master', points: master });
    if (secondDegree) details.push({ label: 'Δεύτερο πτυχίο τριτοβάθμιας', points: secondDegree });
    languages.details.forEach((x) => details.push(x));
    languages.warnings.forEach((x) => warnings.push(x));

    const raw = phd + esdda + master + secondDegree + languages.points;
    const total = Math.min(raw, 25);
    if (raw > 25) warnings.push('Τα τυπικά προσόντα περιορίστηκαν στο ανώτατο όριο των 25 μορίων.');

    return { total: round2(total), raw: round2(raw), details, warnings };
  }

  function calculateAdmin(data, role) {
    const cfg = roleConfig(role);
    if (!cfg) return { total: 0, details: [], overflow: { sdeDirector: 0, sdeDeputy: 0, other: 0 } };

    const yearsDirector = experienceYears(data.sdeDirectorYears);
    const yearsDeputy = experienceYears(data.sdeDeputyYears);
    const yearsOther = experienceYears(data.otherAdminYears);

    const pDirector = Math.min(yearsDirector * 2, cfg.admin.sdeDirector);
    const pDeputy = Math.min(yearsDeputy, cfg.admin.sdeDeputy);
    const pOther = Math.min(yearsOther, cfg.admin.other);

    const details = [];
    if (pDirector) details.push({ label: 'Διοικητική εμπειρία: Διευθυντής ΣΔΕ', points: pDirector });
    if (pDeputy) details.push({ label: 'Διοικητική εμπειρία: Υποδιευθυντής ΣΔΕ', points: pDeputy });
    if (pOther) details.push({ label: 'Διοικητική εμπειρία: σχολικές μονάδες / ΣΑΕΚ / ΕΣΚ', points: pOther });

    const creditedDirectorYears = cfg.admin.sdeDirector / 2;
    const creditedDeputyYears = cfg.admin.sdeDeputy;
    const creditedOtherYears = cfg.admin.other;

    return {
      total: round2(Math.min(pDirector + pDeputy + pOther, cfg.max.admin)),
      details,
      overflow: {
        sdeDirector: round2(Math.max(0, yearsDirector - creditedDirectorYears)),
        sdeDeputy: round2(Math.max(0, yearsDeputy - creditedDeputyYears)),
        other: round2(Math.max(0, yearsOther - creditedOtherYears))
      }
    };
  }

  function calculateTeaching(data, role) {
    const cfg = roleConfig(role);
    if (!cfg) return { total: 0, details: [], sde: 0, adult: 0, school: 0 };

    const sdeYears = experienceYears(data.sdeTeachingYears) + experienceYears(data.sdeTransferredYears);
    const sdeHours = n(data.sdeTeachingHours);
    const sde = Math.min(sdeYears + sdeHours / 650, cfg.teaching.sde);

    const adult = Math.min(n(data.adultNonformalHours) / 100 * 0.5, cfg.teaching.adult);

    const schoolYears = experienceYears(data.schoolTeachingYears) + experienceYears(data.schoolTransferredYears);
    const schoolHours = n(data.schoolTeachingHours);
    const school = Math.min(schoolYears + schoolHours / 650, cfg.teaching.school);

    const details = [];
    if (sde) details.push({ label: 'Διδακτικό έργο στα ΣΔΕ', points: sde });
    if (adult) details.push({ label: 'Μη τυπική εκπαίδευση ενηλίκων', points: adult });
    if (school) details.push({ label: 'Π/θμια – Δ/θμια – ΣΑΕΚ – ΕΣΚ', points: school });

    return {
      total: round2(Math.min(sde + adult + school, cfg.max.teaching)),
      details,
      sde: round2(sde), adult: round2(adult), school: round2(school)
    };
  }

  function calculateTraining(data) {
    const hours = n(data.trainingHours);
    const points = hours > 0 && hours < 15 ? 0 : Math.min(hours / 100 * 0.5, 5);
    const warnings = [];
    if (hours > 0 && hours < 15) warnings.push('Η δηλωμένη επιμόρφωση είναι μικρότερη των 15 ωρών και δεν μοριοδοτείται.');
    return {
      total: round2(points),
      details: points ? [{ label: 'Επιμόρφωση Εκπαίδευσης Ενηλίκων / ΣΔΕ / Διοίκησης', points: round2(points) }] : [],
      warnings
    };
  }

  function evaluateEligibility(data, role) {
    const cfg = roleConfig(role);
    if (!cfg) return { status: 'incomplete', issues: ['Επίλεξε θέση υποψηφιότητας.'], missing: ['Θέση υποψηφιότητας'] };

    const issues = [];
    const missing = [];
    const requireValue = (value, label) => {
      if (value === '' || value == null) missing.push(label);
    };

    requireValue(data.permanentTeacher, 'Ιδιότητα εν ενεργεία μόνιμου εκπαιδευτικού');
    requireValue(data.assignmentEligible, 'Δυνατότητα κάλυψης διδακτικού έργου ' + cfg.requiredTeachingHours + ' ωρών');
    requireValue(data.computerKnowledge, 'Γνώση Η/Υ');
    requireValue(data.adultEducationExperience, 'Γνώση / εμπειρία στην εκπαίδευση ενηλίκων');
    requireValue(data.blockingIssue, 'Κωλύματα συμμετοχής');
    if (role === 'director') {
      requireValue(data.tertiaryDegree, 'Πτυχίο τριτοβάθμιας εκπαίδευσης');
      requireValue(data.adminQualifications, 'Διοικητικά προσόντα');
    }

    const serviceRaw = String(data.educationalServiceYears ?? '');
    const serviceYears = experienceYears(data.educationalServiceYears);
    if (serviceRaw === '') missing.push('Συνολική εκπαιδευτική υπηρεσία');

    if (data.permanentTeacher === 'no') issues.push('Απαιτείται να είσαι εν ενεργεία μόνιμος/η εκπαιδευτικός Πρωτοβάθμιας ή Δευτεροβάθμιας Εκπαίδευσης.');
    if (serviceRaw !== '' && serviceYears < cfg.minServiceYears) issues.push('Απαιτούνται τουλάχιστον ' + cfg.minServiceYears + ' έτη εκπαιδευτικής υπηρεσίας.');
    if (role === 'director' && data.tertiaryDegree === 'no') issues.push('Για θέση Διευθυντή απαιτείται πτυχίο τριτοβάθμιας εκπαίδευσης ή ισότιμος τίτλος.');
    if (data.assignmentEligible === 'no') issues.push('Πρέπει να μπορείς να καλύπτεις ανάγκες διδακτικού έργου ' + cfg.requiredTeachingHours + ' ωρών στα γνωστικά αντικείμενα των ΣΔΕ.');
    if (data.computerKnowledge === 'no') issues.push('Απαιτείται γνώση πληροφορικής / χειρισμού Η/Υ.');
    if (data.adultEducationExperience === 'no') issues.push('Απαιτείται γνώση και εμπειρία στην εκπαίδευση ενηλίκων.');
    if (role === 'director' && data.adminQualifications === 'no') issues.push('Για θέση Διευθυντή απαιτούνται σημαντικά διοικητικά προσόντα.');
    if (data.blockingIssue === 'yes') issues.push('Δήλωσες ότι υπάρχει ή ενδέχεται να υπάρχει κώλυμα συμμετοχής του άρθρου 3.');

    return {
      status: issues.length ? 'not-eligible' : (missing.length ? 'incomplete' : 'eligible'),
      issues, missing
    };
  }

  function calculate(data) {
    const role = data.role || '';
    const cfg = roleConfig(role);
    const formal = calculateFormal(data);
    const teaching = calculateTeaching(data, role);
    const admin = calculateAdmin(data, role);
    const training = calculateTraining(data);
    const criteria = round2(formal.total + teaching.total + admin.total + training.total);
    const eligibility = evaluateEligibility(data, role);

    const interviewEntered = role === 'director' && data.interviewScore !== '' && data.interviewScore != null;
    const interview = role === 'director' ? cap(data.interviewScore, 25) : 0;
    const final = cfg ? (role === 'director' ? (interviewEntered ? round2(criteria + interview) : null) : criteria) : 0;

    return {
      role,
      config: cfg,
      formal,
      teaching,
      admin,
      training,
      criteria,
      interview: round2(interview),
      interviewEntered,
      final,
      eligibility,
      warnings:[].concat(formal.warnings || [], training.warnings || [])
    };
  }

  global.SDELeadership = Object.freeze({
    ROLE,
    calculate,
    calculateFormal,
    calculateTeaching,
    calculateAdmin,
    calculateTraining,
    calculateLanguages,
    evaluateEligibility,
    round2,
    MAX_EXPERIENCE_YEARS,
    experienceYears
  });
})(typeof window !== 'undefined' ? window : globalThis);
