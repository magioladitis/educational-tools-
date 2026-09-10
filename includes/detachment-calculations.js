/*
 * Pure calculation engine for ypologismos-morion-apospasis.php.
 * No DOM access: the same engine can be reused by a future mobile client.
 */
(function (global) {
  'use strict';

  function bool(value) { return value === true; }
  function num(value) {
    var n = Number(value);
    return Number.isFinite(n) ? n : 0;
  }

  function calculateServicePoints(years, months, days) {
    years = Math.min(50, Math.max(0, Math.floor(num(years))));
    months = num(months);
    days = num(days);

    var totalMonths = (years * 12) + months;
    if (days >= 15) totalMonths += 1;

    var firstBandMonths = Math.min(totalMonths, 120);
    var secondBandMonths = Math.min(Math.max(totalMonths - 120, 0), 120);
    var thirdBandMonths = Math.max(totalMonths - 240, 0);

    var firstBandPoints = firstBandMonths / 12;
    var secondBandPoints = (secondBandMonths / 12) * 1.5;
    var thirdBandPoints = (thirdBandMonths / 12) * 2;

    return {
      totalMonths: totalMonths,
      countedYears: Math.floor(totalMonths / 12),
      countedMonths: totalMonths % 12,
      firstBandPoints: firstBandPoints,
      secondBandPoints: secondBandPoints,
      thirdBandPoints: thirdBandPoints,
      total: firstBandPoints + secondBandPoints + thirdBandPoints
    };
  }

  function calculateChildrenPoints(children) {
    var points = 0;
    if (children >= 1) points += 5;
    if (children >= 2) points += 6;
    if (children >= 3) points += 8;
    if (children >= 4) points += (children - 3) * 10;
    return points;
  }

  function familyStatusPoints(status) {
    return ({
      none: 0,
      married: 4,
      divorced_custody: 4,
      widowed_child: 12,
      widowed_nochild: 4,
      single_parent: 6
    })[status] || 0;
  }

  function familyStatusLabel(status) {
    return ({
      none: 'Καμία μοριοδοτούμενη οικογενειακή κατάσταση',
      married: 'Έγγαμος/η ή σύμφωνο συμβίωσης',
      divorced_custody: 'Διαζευγμένος/η ή σε διάσταση με νόμιμη επιμέλεια',
      widowed_child: 'Χηρεία με μοριοδοτούμενο παιδί',
      widowed_nochild: 'Χηρεία χωρίς μοριοδοτούμενο παιδί',
      single_parent: 'Άγαμος/η γονέας με μοριοδοτούμενο παιδί'
    })[status] || 'Οικογενειακή κατάσταση';
  }

  function calculateCoService(input, warnings, info) {
    var type = input.coServiceType || 'none';
    if (type === 'none') return 0;

    if (type === 'unemployed_all_year') {
      warnings.push('Δεν αποδόθηκαν μόρια συνυπηρέτησης: ο/η σύζυγος ήταν άνεργος/η σε όλο το τελευταίο έτος.');
      return 0;
    }

    if (type === 'public_organic') {
      info.push('Συνυπηρέτηση: οργανική υπηρεσία συζύγου στον δημόσιο τομέα στην περιοχή.');
      return 10;
    }

    if (type === 'teacher_term') {
      info.push('Συνυπηρέτηση: σύζυγος εκπαιδευτικός με θητεία — λαμβάνεται η περιοχή όπου υπηρετεί.');
      return 10;
    }

    if (type === 'public_contract' || type === 'private') {
      if (!bool(input.coServiceOneYearSameArea)) {
        warnings.push('Δεν αποδόθηκαν τα 10 μόρια συνυπηρέτησης: δεν επιβεβαιώθηκε ότι το απαιτούμενο διάστημα εργασίας/ανεργίας αφορά το ίδιο ΠΥΣΠΕ/ΠΥΣΔΕ.');
        return 0;
      }
      if (!bool(input.coServiceWorkedDay)) {
        warnings.push('Δεν αποδόθηκαν τα 10 μόρια συνυπηρέτησης: απαιτείται τουλάχιστον μία ημέρα εργασίας στην περιοχή μέσα στο τελευταίο έτος.');
        return 0;
      }
      info.push('Συνυπηρέτηση: πληρούνται οι δηλωμένες προϋποθέσεις τελευταίου έτους και τουλάχιστον μίας ημέρας εργασίας.');
      return 10;
    }

    return 0;
  }

  function evaluatePriorityAndObstacles(input, warnings) {
    var appointmentStatus = input.appointmentStatus || '';
    var obstacleReasons = [];
    var obstacleMap = [
      ['obstacleMusicExclusive', 'ΠΕ79.01/ΤΕ16 με αποκλειστικό διορισμό σε Μουσικό Σχολείο'],
      ['obstacleLeader', 'θητεία στελέχους εκπαίδευσης που λήγει μετά τις 31-08-2026'],
      ['obstacleTermDetachment', 'απόσπαση με θητεία που δεν λήγει έως 31-08-2026'],
      ['obstacleActiveDetachment', 'άλλη απόσπαση που δεν λήγει έως 31-08-2026'],
      ['obstacleESK', 'κώλυμα διετίας από διαδικασία ΕΣΚ'],
      ['obstacleSuspension', 'αργία ή αναστολή άσκησης καθηκόντων'],
      ['obstacleEaeGeneral', 'διορισμός στην ΕΑΕ χωρίς 5ετία με αίτημα προς Γενική Εκπαίδευση']
    ];

    obstacleMap.forEach(function (item) {
      if (bool(input[item[0]])) obstacleReasons.push(item[1]);
    });

    var specialCategory = bool(input.prioritySpecialCategory);
    var newSelfSpouse75 = bool(input.priorityNewSelfSpouse75);
    var newChild67 = bool(input.priorityNewChild67);
    if (!appointmentStatus) {
      warnings.push('Δεν έχει δηλωθεί η περίοδος μόνιμου διορισμού. Ο έλεγχος του ειδικού κανόνα νεοδιορίστων δεν μπορεί να ολοκληρωθεί.');
    }

    var isNewAppointee = ['before_2024_09_01', 'sep_2024', 'after_2024_09_30'].indexOf(appointmentStatus) !== -1;
    var newAppointeeException = specialCategory || (isNewAppointee && (newSelfSpouse75 || newChild67));

    if (appointmentStatus === 'after_2024_09_30' && !newAppointeeException) {
      obstacleReasons.push('νεοδιόριστος/η μετά τις 30-09-2024 χωρίς δηλωμένη κατ’ εξαίρεση περίπτωση της παρ. 5α');
    }

    if (appointmentStatus === 'sep_2024') {
      warnings.push('Για διορισμό από 01-09 έως 30-09-2024 η συγκεκριμένη διατύπωση της εγκυκλίου δεν επιτρέπει ασφαλή αυτόματο συμπέρασμα από το εργαλείο. Απαιτείται έλεγχος από τη Διεύθυνση Εκπαίδευσης.');
    }

    var priorityReasons = [];
    if (specialCategory) priorityReasons.push('δηλώθηκε ειδική κατηγορία μετάθεσης');
    if (isNewAppointee && newSelfSpouse75) priorityReasons.push('νεοδιόριστος/η με αναπηρία ιδίου/συζύγου 75%+');
    if (isNewAppointee && newChild67) priorityReasons.push('νεοδιόριστος/η με τέκνο με αναπηρία 67%+');
    if (bool(input.priorityElected)) priorityReasons.push('αιρετός/ή ΟΤΑ');

    var coPriority = input.priorityCoServiceCategory || 'none';
    var coPriorityLabels = {
      uniformed: 'κατηγορία συζύγου/συμβιούντος ένστολου ή άλλης περίπτωσης γ',
      judicial: 'σύζυγος δικαστικού λειτουργού / κύριου προσωπικού ΝΣΚ',
      university: 'σύζυγος μέλους ΔΕΠ/ΕΔΙΠ/ΕΕΠ/ΕΤΕΠ'
    };

    var priorityBlockedByFirstChoice = false;
    if (coPriority !== 'none') {
      if (bool(input.priorityFirstPreference)) {
        priorityReasons.push(coPriorityLabels[coPriority]);
      } else {
        priorityBlockedByFirstChoice = true;
        warnings.push('Η δηλωμένη κατά προτεραιότητα περίπτωση συνυπηρέτησης δεν ενεργοποιήθηκε, επειδή δεν επιβεβαιώθηκε ότι το σχετικό ΠΥΣΠΕ/ΠΥΣΔΕ είναι η 1η προτίμηση.');
      }
    }

    return {
      obstacleReasons: obstacleReasons,
      priorityReasons: priorityReasons,
      priorityBlockedByFirstChoice: priorityBlockedByFirstChoice
    };
  }

  function calculate(input) {
    input = input || {};
    var warnings = [];
    var info = [];

    var years = num(input.serviceYears);
    var months = num(input.serviceMonths);
    var days = num(input.serviceDays);

    if (!Number.isInteger(years) || years < 0) return { error: 'Τα έτη συνολικής υπηρεσίας πρέπει να είναι μη αρνητικός ακέραιος αριθμός.' };
    if (!Number.isInteger(months) || months < 0 || months > 11) return { error: 'Οι μήνες συνολικής υπηρεσίας πρέπει να είναι από 0 έως 11.' };
    if (!Number.isInteger(days) || days < 0 || days > 30) return { error: 'Οι ημέρες πρέπει να είναι από 0 έως 30.' };

    var children = Math.min(20, num(input.eligibleChildren));
    if (!Number.isInteger(children) || children < 0) return { error: 'Ο αριθμός τέκνων πρέπει να είναι ακέραιος από 0 έως 20.' };

    var requestedArea = String(input.requestedArea || '').trim();
    var eligibility = evaluatePriorityAndObstacles(input, warnings);

    var service = calculateServicePoints(years, months, days);
    if (days > 0 && days < 15) warnings.push('Οι ' + days + ' ημέρες δεν προσμετρήθηκαν, επειδή είναι λιγότερες από 15.');
    if (days >= 15) warnings.push('Οι ' + days + ' ημέρες υπολογίστηκαν ως ένας επιπλέον πλήρης μήνας.');

    var coServicePoints = calculateCoService(input, warnings, info);
    var localityPoints = bool(input.locality) ? 4 : 0;

    var normalizedArea = requestedArea.toLocaleLowerCase('el-GR');
    if (coServicePoints > 0 && (normalizedArea.indexOf('αθην') !== -1 || normalizedArea.indexOf('θεσσαλον') !== -1)) {
      info.push('Στην απλή μοριοδότηση συνυπηρέτησης, Αθήνα/Θεσσαλονίκη εφαρμόζουν τον ειδικό ενιαίο κανόνα της εγκυκλίου.');
    }

    var familyStatus = input.familyStatus || 'none';
    var familyBasePoints = familyStatusPoints(familyStatus);
    var childrenPoints = calculateChildrenPoints(children);
    var familyTotal = familyBasePoints + childrenPoints;

    if ((familyStatus === 'widowed_child' || familyStatus === 'single_parent') && children === 0) {
      warnings.push('Η επιλεγμένη οικογενειακή κατάσταση προϋποθέτει μοριοδοτούμενο παιδί, αλλά ο αριθμός τέκνων είναι 0.');
    }

    var healthPerson = input.healthPerson || 'none';
    var selfFamilyHealthPoints = parseInt(input.healthSelfFamily, 10) || 0;
    if (selfFamilyHealthPoints > 0 && healthPerson === 'none') {
      warnings.push('Δεν αποδόθηκαν μόρια στην κατηγορία εκπαιδευτικού/συζύγου/τέκνου επειδή δεν επιλέχθηκε το πρόσωπο στο οποίο αφορά η αναπηρία.');
      selfFamilyHealthPoints = 0;
    }
    if (selfFamilyHealthPoints > 0 && healthPerson === 'child' && !bool(input.healthChildProtected)) {
      warnings.push('Δεν αποδόθηκαν μόρια υγείας τέκνου επειδή δεν επιβεβαιώθηκε ότι είναι προστατευόμενο μέλος ή διαμένει με τον/την εκπαιδευτικό.');
      selfFamilyHealthPoints = 0;
    }

    var parentHealthPoints = parseInt(input.healthParents, 10) || 0;
    if (parentHealthPoints > 0 && !bool(input.parentLocationEligible)) {
      warnings.push('Δεν αποδόθηκαν μόρια υγείας γονέα επειδή δεν επιβεβαιώθηκαν οι τοπικές προϋποθέσεις (δημότης από διετίας και διαμονή στην περιοχή). ');
      parentHealthPoints = 0;
    }

    var siblingHealthPoints = bool(input.siblingHealth) ? 5 : 0;
    var ivfPoints = bool(input.ivf) ? 3 : 0;
    var healthTotal = selfFamilyHealthPoints + parentHealthPoints + siblingHealthPoints + ivfPoints;

    var studyType = input.studyType || 'none';
    var studiesPoints = 0;
    if (studyType === 'eligible') {
      if (bool(input.studyDifferentArea) && bool(input.studyRequestedArea) && bool(input.studyWithinDuration)) {
        studiesPoints = 2;
      } else {
        warnings.push('Δεν αποδόθηκαν τα 2 μόρια σπουδών επειδή δεν επιβεβαιώθηκαν όλες οι απαιτούμενες προϋποθέσεις.');
      }
    } else if (studyType === 'eap') {
      warnings.push('Οι σπουδές στο ΕΑΠ δεν μοριοδοτούνται με το κριτήριο των 2 μονάδων.');
    } else if (studyType === 'phd') {
      warnings.push('Η απόκτηση διδακτορικού τίτλου δεν μοριοδοτείται με το κριτήριο των 2 μονάδων σπουδών.');
    }

    var total = service.total + coServicePoints + localityPoints + familyTotal + healthTotal + studiesPoints;
    var sidebarStatus = requestedArea ? 'Υπολογισμός για ' + requestedArea + '.' : 'Ενδεικτικός υπολογισμός μορίων απόσπασης.';
    var sidebarVariant = 'status';
    if (eligibility.obstacleReasons.length > 0) {
      sidebarStatus = 'Πιθανό κώλυμα εξέτασης της αίτησης — έλεγξε το αναλυτικό αποτέλεσμα.';
      sidebarVariant = 'warning';
    } else if (eligibility.priorityReasons.length > 0) {
      sidebarStatus = 'Πιθανή υπαγωγή σε απόσπαση κατά προτεραιότητα.';
      sidebarVariant = 'success';
    } else if (eligibility.priorityBlockedByFirstChoice) {
      sidebarStatus = 'Η πιθανή κατά προτεραιότητα περίπτωση χρειάζεται έλεγχο της 1ης προτίμησης.';
      sidebarVariant = 'warning';
    }

    return {
      error: null,
      warnings: warnings,
      info: info,
      requestedArea: requestedArea,
      service: service,
      coServicePoints: coServicePoints,
      localityPoints: localityPoints,
      familyStatus: familyStatus,
      familyBasePoints: familyBasePoints,
      children: children,
      childrenPoints: childrenPoints,
      familyTotal: familyTotal,
      selfFamilyHealthPoints: selfFamilyHealthPoints,
      parentHealthPoints: parentHealthPoints,
      siblingHealthPoints: siblingHealthPoints,
      ivfPoints: ivfPoints,
      healthTotal: healthTotal,
      studiesPoints: studiesPoints,
      total: total,
      eligibility: eligibility,
      sidebarStatus: sidebarStatus,
      sidebarVariant: sidebarVariant
    };
  }

  global.EducationDetachment = {
    calculate: calculate,
    calculateServicePoints: calculateServicePoints,
    calculateChildrenPoints: calculateChildrenPoints,
    familyStatusPoints: familyStatusPoints,
    familyStatusLabel: familyStatusLabel
  };
})(window);
