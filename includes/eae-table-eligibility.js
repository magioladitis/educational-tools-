(function(global){
  'use strict';

  const RULES = Object.freeze({
    auxiliarySeminarHours: 400,
    auxiliarySeminarMonths: 7,
    auxiliaryEaeMonths: 10,
    auxiliaryChildDisabilityPercent: 67,
    mainFiveYearYears: 5,
    peAutoMainSpecialties: Object.freeze(['ΠΕ61','ΠΕ71']),
    pe11Specialty: 'ΠΕ11'
  });

  const MAIN_REASONS = Object.freeze({
    phd: 'διδακτορικό στην Ε.Α.Ε./Σχολική Ψυχολογία',
    msc: 'μεταπτυχιακό στην Ε.Α.Ε./Σχολική Ψυχολογία',
    retraining: 'διετής μετεκπαίδευση στην Ε.Α.Ε.',
    fiveYear: 'πενταετής αποδεδειγμένη προϋπηρεσία στην Ε.Α.Ε.',
    pe11: 'προβλεπόμενη κύρια ειδικότητα ΠΕ11 στην Ε.Α.Ε.'
  });

  function bool(value){ return value === true || value === 1 || value === '1' || value === 'true' || value === 'yes'; }
  function number(value){ const n = Number(value); return Number.isFinite(n) ? Math.max(0, n) : 0; }
  function text(value){ return String(value == null ? '' : value).trim(); }

  function labels(profile){
    if(profile === 'te'){
      return {
        main: 'Αξιολογικός Πίνακας Β΄ (Κύριος)',
        aux: 'Επικουρικός Πίνακας',
        none: 'Δεν προκύπτει ένταξη',
        missingSpecialty: 'Δεν προκύπτει ένταξη'
      };
    }
    return {
      main: 'ΚΥΡΙΟΣ – Αξιολογικός Πίνακας Β΄',
      aux: 'ΕΠΙΚΟΥΡΙΚΟΣ Πίνακας Ε.Α.Ε.',
      none: 'Δεν προκύπτει ένταξη',
      missingSpecialty: 'Επίλεξε κλάδο'
    };
  }

  function calculate(config){
    config = config || {};
    const profile = config.profile === 'te' ? 'te' : 'pe';
    const specialty = text(config.specialty);
    const main = config.main || {};
    const aux = config.aux || {};
    const ui = labels(profile);

    if(!specialty){
      return {
        profile,
        code: 'none',
        type: 'none',
        label: ui.missingSpecialty,
        why: profile === 'te'
          ? 'Επίλεξε κλάδο/ειδικότητα για να ολοκληρωθεί ο έλεγχος ένταξης.'
          : 'Δεν έχει επιλεγεί κλάδος / ειδικότητα.',
        mainReasons: [],
        auxReasons: [],
        eligible: false
      };
    }

    const mainReasons = [];
    if(profile === 'pe' && RULES.peAutoMainSpecialties.indexOf(specialty) !== -1){
      mainReasons.push(specialty + ': βασικός κλάδος Ε.Α.Ε.');
    }
    if(bool(main.phd)) mainReasons.push(MAIN_REASONS.phd);
    if(bool(main.msc)) mainReasons.push(MAIN_REASONS.msc);
    if(bool(main.retraining)) mainReasons.push(MAIN_REASONS.retraining);
    if(bool(main.fiveYear)) mainReasons.push(MAIN_REASONS.fiveYear);
    if(profile === 'pe' && specialty === RULES.pe11Specialty && bool(main.pe11)) mainReasons.push(MAIN_REASONS.pe11);

    const auxReasons = [];
    if(bool(aux.seminar400)) auxReasons.push('σεμινάριο Ε.Α.Ε. ≥' + RULES.auxiliarySeminarHours + ' ωρών / ≥' + RULES.auxiliarySeminarMonths + ' μηνών');
    if(number(aux.eaeMonths) >= RULES.auxiliaryEaeMonths) auxReasons.push('τουλάχιστον ' + RULES.auxiliaryEaeMonths + ' μήνες προϋπηρεσίας στην Ε.Α.Ε.');
    if(bool(aux.childDisability67)) auxReasons.push('γονέας παιδιού με αναπηρία ≥' + RULES.auxiliaryChildDisabilityPercent + '%');

    if(mainReasons.length){
      return {
        profile,
        code: 'main',
        type: 'main',
        label: ui.main,
        why: profile === 'te'
          ? 'Δηλώθηκε προσόν που θεμελιώνει ένταξη στον Αξιολογικό Πίνακα Β΄.'
          : 'Κριτήριο/α ένταξης: ' + mainReasons.join(', ') + '.',
        mainReasons,
        auxReasons,
        eligible: true
      };
    }

    if(auxReasons.length){
      return {
        profile,
        code: 'aux',
        type: 'aux',
        label: ui.aux,
        why: profile === 'te'
          ? 'Κριτήριο/α Επικουρικού: ' + auxReasons.join(' · ') + '.'
          : 'Κριτήριο/α ένταξης: ' + auxReasons.join(', ') + '.',
        mainReasons,
        auxReasons,
        eligible: true
      };
    }

    return {
      profile,
      code: 'none',
      type: 'none',
      label: ui.none,
      why: profile === 'te'
        ? 'Δεν έχει δηλωθεί προσόν Κύριου Πίνακα ούτε ένα από τα τρία κριτήρια Επικουρικού.'
        : 'Με τα στοιχεία που δηλώθηκαν δεν προκύπτει προσόν ένταξης ούτε στον Αξιολογικό Πίνακα Β΄ ούτε στον Επικουρικό.',
      mainReasons,
      auxReasons,
      eligible: false
    };
  }

  global.EaeTableEligibility = Object.freeze({ RULES, calculate });
})(window);
