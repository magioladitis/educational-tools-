(function(global){
  'use strict';

  var MAX = Object.freeze({academic:45, service:25, interview:30, total:100});

  function bool(v){ return v === true || v === 'true' || v === 1 || v === '1'; }
  function number(v){ var n = Number(v); return Number.isFinite(n) ? n : 0; }
  function integer(v){ return Math.floor(number(v)); }
  function clamp(v,min,max){ return Math.min(max, Math.max(min, number(v))); }
  function isAnswered(v){ return v !== '' && v !== null && v !== undefined; }

  function academicPoints(o){
    o = o || {};
    var warnings = [];
    var hasPhd = bool(o.relevantPhd) || bool(o.otherPhd);
    var relevantMasterSameSubject = bool(o.relevantMaster) && bool(o.relevantMasterSameSubjectAsPhd);
    var otherMasterSameSubject = bool(o.otherMaster) && bool(o.otherMasterSameSubjectAsPhd);
    var relevantMasterSuppressed = relevantMasterSameSubject && hasPhd;
    var otherMasterSuppressed = otherMasterSameSubject && hasPhd;

    if (relevantMasterSameSubject && !hasPhd) {
      warnings.push('Δηλώθηκε ότι το συναφές master είναι στο ίδιο αντικείμενο με διδακτορικό, χωρίς να έχει δηλωθεί διδακτορικό.');
    }
    if (otherMasterSameSubject && !hasPhd) {
      warnings.push('Δηλώθηκε ότι το μη συναφές master είναι στο ίδιο αντικείμενο με διδακτορικό, χωρίς να έχει δηλωθεί διδακτορικό.');
    }

    var greek = o.greekLevel === 'very_good' ? 10 : (o.greekLevel === 'good' ? 5 : 0);
    var otherLanguagesCount = Math.min(3, Math.max(0, integer(o.otherLanguagesCount)));
    var breakdown = {
      relevantPhd: bool(o.relevantPhd) ? 8 : 0,
      otherPhd: bool(o.otherPhd) ? 4 : 0,
      relevantMaster: bool(o.relevantMaster) && !relevantMasterSuppressed ? 4 : 0,
      otherMaster: bool(o.otherMaster) && !otherMasterSuppressed ? 2 : 0,
      greek: greek,
      publication: bool(o.publication) ? 3 : 0,
      secondDegree: bool(o.secondDegree) ? 2 : 0,
      otherLanguages: otherLanguagesCount * 4
    };
    var total = Object.keys(breakdown).reduce(function(sum,key){ return sum + breakdown[key]; },0);

    return {
      total: total,
      max: MAX.academic,
      breakdown: breakdown,
      otherLanguagesCount: otherLanguagesCount,
      suppressed: {
        relevantMaster: relevantMasterSuppressed,
        otherMaster: otherMasterSuppressed
      },
      warnings: warnings
    };
  }

  function servicePoints(o){
    o = o || {};
    var years = Math.min(5, Math.max(0, integer(o.europeanSchoolYears)));
    return {
      years: years,
      points: years * 5,
      max: MAX.service
    };
  }

  function interviewPoints(o){
    o = o || {};
    var greekAnswered = isAnswered(o.interviewGreek);
    var personalityAnswered = isAnswered(o.interviewPersonality);
    var greek = greekAnswered ? clamp(o.interviewGreek,0,20) : 0;
    var personality = personalityAnswered ? clamp(o.interviewPersonality,0,10) : 0;
    return {
      greek: greek,
      personality: personality,
      total: greek + personality,
      max: MAX.interview,
      complete: greekAnswered && personalityAnswered,
      anyEntered: greekAnswered || personalityAnswered
    };
  }

  function eligibility(o){
    o = o || {};
    var unanswered = [], issues = [], notes = [];
    var route = o.candidateRoute || '';

    if (!route) unanswered.push('κατηγορία υποψηφίου');
    if (!o.teachingQualification) unanswered.push('τυπικό προσόν διδασκαλίας για τη θέση');
    else if (o.teachingQualification === 'no') issues.push('Δεν δηλώθηκε το απαιτούμενο τυπικό προσόν διδασκαλίας για τη θέση.');

    if (!o.appointmentObstacle) unanswered.push('κώλυμα διορισμού');
    else if (o.appointmentObstacle === 'yes') issues.push('Δηλώθηκε πιθανό κώλυμα διορισμού κατά το άρθρο 8 του ν. 3528/2007.');

    if (!o.healthFitness) unanswered.push('υγεία και φυσική καταλληλότητα');
    else if (o.healthFitness === 'no') issues.push('Δεν δηλώθηκε η απαιτούμενη υγεία και φυσική καταλληλότητα για τα καθήκοντα της θέσης.');

    if (route === 'non_native') {
      notes.push('Η εξαιρετική διαδρομή μη φυσικού ομιλητή κατατάσσεται σε χωριστό πίνακα, ο οποίος ενεργοποιείται μετά την εξάντληση του πίνακα φυσικών ομιλητών.');
      if (!o.excellentRequiredLanguage) unanswered.push('άριστη γνώση της απαιτούμενης γλώσσας');
      else if (o.excellentRequiredLanguage === 'no') issues.push('Για τη διαδρομή μη φυσικού ομιλητή απαιτείται άριστη γνώση της γλώσσας που αφορά τη θέση.');
      if (!o.inspectorAgreement) unanswered.push('σύμφωνη γνώμη αρμόδιου Εθνικού Επιθεωρητή Ευρωπαϊκών Σχολείων');
      else if (o.inspectorAgreement === 'no') issues.push('Για τη διαδρομή μη φυσικού ομιλητή απαιτείται η σύμφωνη γνώμη του αρμόδιου Εθνικού Επιθεωρητή Ευρωπαϊκών Σχολείων.');
    } else if (route === 'native') {
      notes.push('Στην πρόσκληση 2026–2027 δίνεται προτεραιότητα στους φυσικούς ομιλητές της γλώσσας που αφορά τη θέση.');
    }

    return {
      answered: unanswered.length === 0,
      eligible: unanswered.length === 0 && issues.length === 0,
      unanswered: unanswered,
      issues: issues,
      notes: notes
    };
  }

  function documentChecklist(o){
    o = o || {};
    var docs = [
      'Βασικός τίτλος σπουδών με ημερομηνία κτήσης και βαθμό ή συμπληρωματική βεβαίωση του ιδρύματος.',
      'Ταυτότητα, διαβατήριο ή άλλο επίσημο έγγραφο που αποδεικνύει τη χώρα προέλευσης.',
      'Υπεύθυνη δήλωση για τα κωλύματα/προϋποθέσεις της πρόσκλησης και βιογραφικό σημείωμα.'
    ];
    if (bool(o.relevantPhd) || bool(o.otherPhd) || bool(o.relevantMaster) || bool(o.otherMaster) || bool(o.secondDegree)) {
      docs.push('Τίτλοι μεταπτυχιακών/διδακτορικών/δεύτερου πτυχίου που δηλώνονται, με τις απαιτούμενες μεταφράσεις και αναγνωρίσεις όπου εφαρμόζεται.');
    }
    if (o.greekLevel === 'good' || o.greekLevel === 'very_good') {
      docs.push('Δικαιολογητικό που αποδεικνύει το δηλωμένο επίπεδο γνώσης της ελληνικής γλώσσας.');
    }
    if (bool(o.publication)) docs.push('Δημοσιευμένη συγγραφική εργασία· η πρόσκληση ζητά δημοσιευμένες εργασίες με ISBN ή ISSN.');
    if (integer(o.otherLanguagesCount) > 0) docs.push('Πιστοποιητικά για τις πρόσθετες ξένες γλώσσες, τουλάχιστον επιπέδου Β2.');
    if (integer(o.europeanSchoolYears) > 0) docs.push('Βεβαιώσεις διδακτικής υπηρεσίας σε Ευρωπαϊκό Σχολείο ή στο Σχολείο Ευρωπαϊκής Παιδείας Ηρακλείου.');
    if (o.candidateRoute === 'non_native') docs.push('Για τη διαδρομή μη φυσικού ομιλητή: απόδειξη άριστης γνώσης της απαιτούμενης γλώσσας και σύμφωνη γνώμη του αρμόδιου Εθνικού Επιθεωρητή.');
    return docs;
  }

  function calculate(o){
    o = o || {};
    var academic = academicPoints(o);
    var service = servicePoints(o);
    var interview = interviewPoints(o);
    var eligibilityResult = eligibility(o);
    var preInterview = academic.total + service.points;
    var finalTotal = interview.complete ? preInterview + interview.total : null;
    var warnings = academic.warnings.slice();

    if (integer(o.otherLanguagesCount) > 3) warnings.push('Μοριοδοτούνται έως τρεις πρόσθετες γλώσσες. Το πλήθος περιορίστηκε αυτόματα στις 3.');
    if (integer(o.europeanSchoolYears) > 5) warnings.push('Μοριοδοτούνται έως πέντε έτη υπηρεσίας. Η προϋπηρεσία περιορίστηκε αυτόματα στα 5 έτη.');

    return {
      academic: academic,
      service: service,
      interview: interview,
      eligibility: eligibilityResult,
      preInterview: preInterview,
      preInterviewMax: MAX.academic + MAX.service,
      finalTotal: finalTotal,
      max: MAX,
      warnings: warnings,
      documents: documentChecklist(o)
    };
  }

  global.HeraklionEuropeanEducation = Object.freeze({
    MAX: MAX,
    academicPoints: academicPoints,
    servicePoints: servicePoints,
    interviewPoints: interviewPoints,
    eligibility: eligibility,
    documentChecklist: documentChecklist,
    calculate: calculate
  });
})(window);
