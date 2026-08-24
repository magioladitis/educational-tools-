/*
 * Μοριοδότηση Μητρώου ΣΔΕ (ωρομίσθιο εκπαιδευτικό προσωπικό,
 * Σύμβουλοι Ψυχολόγοι, Σύμβουλοι Σταδιοδρομίας).
 * Βάση: Υ.Α. 75975/Κ1, ΦΕΚ Β' 3224/25.06.2025, άρθρα 3, 10, 11, 12, 14.
 */
(function (global) {
  'use strict';

  const MAX = Object.freeze({
    formal: 18,
    training: 4,
    education: 22,
    experience: 13,
    other: 5,
    base: 40,
    unemploymentPercent: 10
  });

  const ROLE = Object.freeze({
    educator: 'Εκπαιδευτικό προσωπικό',
    psychologist: 'Σύμβουλος Ψυχολόγος',
    career: 'Σύμβουλος Σταδιοδρομίας'
  });

  const SPECIALTIES = Object.freeze({
    'PE01':'ΠΕ01 Θεολόγων','PE02':'ΠΕ02 Φιλολόγων','PE03':'ΠΕ03 Μαθηματικών',
    'PE04.01':'ΠΕ04.01 Φυσικών','PE04.02':'ΠΕ04.02 Χημικών','PE04.03':'ΠΕ04.03 Φυσιογνωστών',
    'PE04.04':'ΠΕ04.04 Βιολόγων','PE04.05':'ΠΕ04.05 Γεωλόγων','PE06':'ΠΕ06 Αγγλικής',
    'PE08':'ΠΕ08 Καλλιτεχνικών','PE78':'ΠΕ78 Κοινωνικών Επιστημών','PE79.01':'ΠΕ79.01 Μουσικής Επιστήμης',
    'PE80':'ΠΕ80 Οικονομίας','PE85':'ΠΕ85 Χημικών Μηχανικών','PE86':'ΠΕ86 Πληροφορικής',
    'PE87.01':'ΠΕ87.01 Ιατρικής','PE88.01':'ΠΕ88.01 Γεωπονίας','PE88.05':'ΠΕ88.05 Φυσικού Περιβάλλοντος',
    'PE89.01':'ΠΕ89.01 Καλλιτεχνικών Σπουδών','PE91':'ΠΕ91 Δραματικής Τέχνης','TE16':'ΤΕ16 Μουσικής'
  });

  function num(v) {
    const n = Number(v);
    return Number.isFinite(n) && n > 0 ? n : 0;
  }
  function cap(v, max) { return Math.min(Math.max(num(v), 0), max); }
  function round(v) { return Math.round((Number(v) + Number.EPSILON) * 10000) / 10000; }

  function primaryPostgradPoints(kind, relevance) {
    if (!kind || relevance === 'none') return 0;
    if (kind === 'phd') return relevance === 'target' ? 11 : 9;
    if (kind === 'master') return relevance === 'target' ? 8 : 6;
    return 0;
  }

  function calculateFormal(data) {
    const details = [];
    const warnings = [];
    const phd = primaryPostgradPoints('phd', data.phd || 'none');
    const masterRaw = primaryPostgradPoints('master', data.master || 'none');
    const master = phd > 0 ? 0 : masterRaw;
    const secondDegree = data.secondDegree ? 3 : 0;
    const secondPhd = data.secondPhd ? 2 : 0;
    const secondMaster = data.secondMaster ? 1 : 0;
    const extraCredential = data.extraCredential ? 1 : 0;

    if (phd) details.push({label:'Διδακτορικό', points:phd});
    if (master) details.push({label:'Μεταπτυχιακό', points:master});
    if (phd && masterRaw) warnings.push('Δηλώθηκαν διδακτορικό και μεταπτυχιακό: μοριοδοτήθηκε μόνο το διδακτορικό, όπως ορίζει το ΦΕΚ.');
    if (secondDegree) details.push({label:'Δεύτερο πτυχίο', points:3});
    if (secondPhd) details.push({label:'Δεύτερο διδακτορικό', points:2});
    if (secondMaster) details.push({label:'Δεύτερο μεταπτυχιακό', points:1});
    if (extraCredential) details.push({label:data.extraCredentialLabel || 'Πρόσθετο προσόν', points:1});

    const raw = phd + master + secondDegree + secondPhd + secondMaster + extraCredential;
    const total = Math.min(raw, MAX.formal);
    if (raw > MAX.formal) warnings.push('Τα τυπικά προσόντα περιορίστηκαν στο ανώτατο όριο των 18 μορίων.');
    return { total:round(total), raw:round(raw), details, warnings };
  }

  function trainingCaps(role) {
    return role === 'educator'
      ? {sde:2, adult:2, thematic:0}
      : {sde:1, adult:1, thematic:2};
  }

  function calculateTraining(data) {
    const caps = trainingCaps(data.role);
    const details = [];
    const scoreHours = (value, max) => {
      const hours = num(value);
      if (hours > 0 && hours < 15) return 0;
      return Math.min(hours / 100 * 0.25, max);
    };
    const sde = scoreHours(data.trainingSdeHours, caps.sde);
    const adult = scoreHours(data.trainingAdultHours, caps.adult);
    const thematic = scoreHours(data.trainingThematicHours, caps.thematic);
    if (sde) details.push({label:'Επιμόρφωση σε θέματα ΣΔΕ', points:round(sde)});
    if (adult) details.push({label:'Επιμόρφωση στις αρχές Εκπαίδευσης Ενηλίκων', points:round(adult)});
    if (thematic) details.push({label:data.thematicTrainingLabel || 'Θεματική επιμόρφωση', points:round(thematic)});
    return {total:round(Math.min(sde + adult + thematic, MAX.training)), details};
  }

  function calculateEducation(data) {
    const formal = calculateFormal(data);
    const training = calculateTraining(data);
    return {
      formal, training,
      total:round(Math.min(formal.total + training.total, MAX.education)),
      warnings:formal.warnings.slice()
    };
  }

  function calculateExperience(data) {
    const role = data.role;
    const details = [];
    let a = 0, b = 0, c = 0;
    if (role === 'educator') {
      a = Math.min(num(data.expSdeHours) / 200, 5);
      b = Math.min(num(data.expAdultHours) / 100 * 0.5, 4);
      c = Math.min(num(data.expFormalHours) / 200, 4);
      if (a) details.push({label:'Διδακτική εμπειρία στα ΣΔΕ', points:round(a)});
      if (b) details.push({label:'Εκπαίδευση Ενηλίκων εκτός ΣΔΕ', points:round(b)});
      if (c) details.push({label:'Τυπική εκπαίδευση / επαγγελματική κατάρτιση', points:round(c)});
    } else if (role === 'psychologist') {
      a = Math.min(Math.min(global.EducationCore.MAX_SERVICE_MONTHS, num(data.expSdeMonths)) * 0.25, 7);
      b = Math.min(Math.min(global.EducationCore.MAX_SERVICE_MONTHS, num(data.expAdultCounsellingMonths)) * 0.25, 6);
      if (a) details.push({label:'ΣΔΕ ως Σύμβουλος Ψυχολόγος', points:round(a)});
      if (b) details.push({label:'Συμβουλευτικές υπηρεσίες σε ενήλικες', points:round(b)});
    } else if (role === 'career') {
      // Το κείμενο του άρθρου 12 §2.1 αναφέρει «μέγιστο 12», αλλά η στήλη μορίων δίνει 7
      // και η κατηγορία έχει σύνολο 13 με το §2.2 να δίνει 6. Χρησιμοποιείται το εσωτερικά συνεπές 7.
      a = Math.min(Math.min(global.EducationCore.MAX_SERVICE_MONTHS, num(data.expSdeMonths)) * 0.25, 7);
      b = Math.min(Math.min(global.EducationCore.MAX_SERVICE_MONTHS, num(data.expAdultCounsellingMonths)) * 0.25, 6);
      if (a) details.push({label:'ΣΔΕ ως Σύμβουλος Σταδιοδρομίας', points:round(a)});
      if (b) details.push({label:'Συμβουλευτικές υπηρεσίες απασχόλησης / επιχειρηματικότητας σε ενήλικες', points:round(b)});
    }
    return {total:round(Math.min(a+b+c, MAX.experience)), a:round(a), b:round(b), c:round(c), details};
  }

  function calculateLanguages(data) {
    if (!global.EducationLanguages || typeof global.EducationLanguages.calculate !== 'function') {
      throw new Error('Απαιτείται το κοινό EducationLanguages πριν από το SDERegistryCalc.');
    }

    const shared = global.EducationLanguages.calculate('sde_registry', [
      {language:data.language1 || '', level:data.languageLevel1 || 'none'},
      {language:data.language2 || '', level:data.languageLevel2 || 'none'}
    ], {
      specialty: data.role === 'educator' ? (data.specialty || '') : ''
    });

    const levelCode = { good: 'B2', very_good: 'C1', excellent: 'C2' };
    const details = (shared.detailItems || []).map(function (item) {
      return {
        label: String(item.position || '') + 'η ξένη γλώσσα (' + (levelCode[item.level] || item.levelLabel || '') + ')',
        points: item.points
      };
    });

    return {
      points: round(shared.points),
      details: details,
      warnings: (shared.warnings || []).slice(),
      shared: shared
    };
  }

  function calculateOther(data) {
    const languages = calculateLanguages(data);
    const specialty = global.EducationCore.normalizeSpecialtyCode(data.specialty);
    const pe86BasicDegree = data.role === 'educator' && specialty === 'ΠΕ86';
    const computer = (!pe86BasicDegree && data.computer) ? 2 : 0;
    const details = languages.details.slice();
    const warnings = languages.warnings.slice();
    if (computer) details.push({label:'Γνώσεις χειρισμού Η/Υ / Νέες Τεχνολογίες',points:2});
    if (pe86BasicDegree && data.computer) {
      warnings.push('Για ΠΕ86 Πληροφορικής, όταν το πτυχίο Πληροφορικής είναι το βασικό πτυχίο ένταξης στο Μητρώο ΣΔΕ, το πεδίο «Γνώση Χειρισμού Η/Υ» δεν αξιολογείται.');
    }
    return {languagePoints:languages.points,computerPoints:computer,total:round(Math.min(languages.points+computer,MAX.other)),details,warnings};
  }

  function calculateSocial(data, base) {
    const wholeMonths = Math.floor(num(data.unemploymentMonths));
    const extraDays = Math.floor(num(data.unemploymentExtraDays));
    const countedMonths = Math.min(20, wholeMonths + (extraDays >= 15 ? 1 : 0));
    const unemploymentPercent = Math.min(countedMonths * 0.5, MAX.unemploymentPercent);
    const unemploymentPoints = base * unemploymentPercent / 100;

    const categories = [
      ['threeChildren','Τρίτεκνη οικογένεια'],
      ['singleParent','Μονογονεϊκή οικογένεια'],
      ['manyChildren','Πολύτεκνη οικογένεια'],
      ['disability','ΑμεΑ ≥50% (υποψήφιος/τέκνο/σύζυγος με τις προϋποθέσεις)']
    ];
    const selected = categories.filter(([key]) => !!data[key]).map(([,label])=>label);
    const specialPercent = selected.length * 10;
    const specialPoints = base * specialPercent / 100;
    return {
      countedMonths,
      unemploymentPercent:round(unemploymentPercent),
      unemploymentPoints:round(unemploymentPoints),
      specialPercent,
      specialPoints:round(specialPoints),
      selected,
      totalIncrease:round(unemploymentPoints+specialPoints)
    };
  }

  function educatorAssignments(specialty) {
    specialty = global.EducationCore.normalizeSpecialtyCode(specialty);
    const out=[];
    const add=(literacy,assignment)=>out.push({literacy,assignment});
    if (specialty==='ΠΕ02'){add('Ελληνική Γλώσσα','Α΄ ανάθεση');add('Κοινωνική Εκπαίδευση','Β΄ ανάθεση');add('Πολιτισμική–Αισθητική Αγωγή','Β΄ ανάθεση');}
    if (specialty==='ΠΕ03'){add('Μαθηματικά','Α΄ ανάθεση');add('Φυσικές Επιστήμες','Β΄ ανάθεση');}
    if (['ΠΕ04.01','ΠΕ04.02','ΠΕ04.03','ΠΕ04.04','ΠΕ04.05'].includes(specialty)){add('Μαθηματικά','Β΄ ανάθεση');add('Φυσικές Επιστήμες','Α΄ ανάθεση');}
    if (['ΠΕ04.01','ΠΕ04.02','ΠΕ04.03','ΠΕ04.04'].includes(specialty)){add('Περιβαλλοντική Εκπαίδευση','Β΄ ανάθεση');}
    if (specialty==='ΠΕ04.05'){add('Περιβαλλοντική Εκπαίδευση','Α΄ ανάθεση');}
    if (specialty==='ΠΕ86'){add('Πληροφορική','Α΄ ανάθεση');add('Μαθηματικά','Β΄ ανάθεση');}
    if (specialty==='ΠΕ06') add('Αγγλική Γλώσσα','Α΄ ανάθεση');
    if (specialty==='ΠΕ78') add('Κοινωνική Εκπαίδευση','Α΄ ανάθεση');
    if (['ΠΕ01','ΠΕ80'].includes(specialty)) add('Κοινωνική Εκπαίδευση','Β΄ ανάθεση');
    if (specialty==='ΠΕ85'){add('Φυσικές Επιστήμες','Α΄ ανάθεση');add('Περιβαλλοντική Εκπαίδευση','Β΄ ανάθεση');}
    if (['ΠΕ87.01'].includes(specialty)) add('Φυσικές Επιστήμες','Β΄ ανάθεση');
    if (specialty==='ΠΕ88.01'){add('Φυσικές Επιστήμες','Β΄ ανάθεση');add('Περιβαλλοντική Εκπαίδευση','Α΄ ανάθεση');}
    if (specialty==='ΠΕ88.05') add('Περιβαλλοντική Εκπαίδευση','Α΄ ανάθεση');
    if (['ΠΕ08','ΠΕ79.01','ΠΕ89.01','ΠΕ91'].includes(specialty)) add('Πολιτισμική–Αισθητική Αγωγή','Α΄ ανάθεση');
    if (specialty==='ΤΕ16') add('Πολιτισμική–Αισθητική Αγωγή','Β΄ ανάθεση');
    return out;
  }

  function eligibility(data) {
    const role = data.role;
    const blockers=[];
    const pending=[];
    if (!role) pending.push('Επίλεξε κατηγορία υποψηφίου.');
    if (role==='educator') {
      if (!data.specialty) pending.push('Επίλεξε κλάδο/ειδικότητα.');
      else if (!educatorAssignments(data.specialty).length) blockers.push('Ο επιλεγμένος κλάδος δεν αντιστοιχεί στους κλάδους του πίνακα του άρθρου 3.');
    }
    if (role==='psychologist') {
      if (data.psychDegree === false) blockers.push('Απαιτείται πτυχίο Ψυχολογίας ή ισότιμο/αντίστοιχο.');
      else if (data.psychDegree == null) pending.push('Δήλωσε αν διαθέτεις πτυχίο Ψυχολογίας.');
      if (data.psychLicense === false) blockers.push('Απαιτείται άδεια άσκησης επαγγέλματος Ψυχολόγου.');
      else if (data.psychLicense == null) pending.push('Δήλωσε αν διαθέτεις άδεια άσκησης επαγγέλματος.');
      if (data.fppBefore1993 && data.psychMasterForFpp === false) blockers.push('Για πτυχιούχους Φ.Π.Ψ. πριν το 1993 απαιτείται επιπρόσθετα μεταπτυχιακό στην Ψυχολογία.');
      if (data.fppBefore1993 && data.psychMasterForFpp == null) pending.push('Δήλωσε αν διαθέτεις το απαιτούμενο μεταπτυχιακό στην Ψυχολογία.');
    }
    if (role==='career') {
      if (data.tertiaryDegree === false) blockers.push('Απαιτείται πτυχίο Α.Ε.Ι. ή ισότιμο της αλλοδαπής.');
      else if (data.tertiaryDegree == null) pending.push('Δήλωσε αν διαθέτεις πτυχίο Α.Ε.Ι.');
      if (!data.careerQualification || data.careerQualification==='none') blockers.push('Απαιτείται μία από τις προβλεπόμενες εξειδικεύσεις/πιστοποιήσεις στη Συμβουλευτική και τον Επαγγελματικό Προσανατολισμό.');
    }
    return {eligible:blockers.length===0 && pending.length===0, blockers, pending};
  }

  function calculateAll(data) {
    const education=calculateEducation(data);
    const experience=calculateExperience(data);
    const other=calculateOther(data);
    const base=round(Math.min(education.total+experience.total+other.total,MAX.base));
    const social=calculateSocial(data,base);
    const final=round(base+social.totalIncrease);
    return {
      education,experience,other,base,social,final,
      eligibility:eligibility(data),
      assignments:data.role==='educator'?educatorAssignments(data.specialty):[],
      eoppepPriority:data.role==='educator' && !!data.eoppepAdultTrainer,
      warnings:[].concat(education.warnings||[],other.warnings||[]),
      max:MAX
    };
  }

  global.SDERegistryCalc={MAX,ROLE,SPECIALTIES,calculateFormal,calculateTraining,calculateEducation,calculateExperience,calculateLanguages,calculateOther,calculateSocial,educatorAssignments,eligibility,calculateAll};
})(typeof window!=='undefined'?window:globalThis);
