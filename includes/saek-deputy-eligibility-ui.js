(function (global) {
  'use strict';

  const fieldIds = ['saek', 'status', 'requiredDegree', 'experience', 'evaluationRefusal', 'unsuitable', 'retirement'];
  let validationAttempted = false;

  function byId(id) {
    return document.getElementById(id);
  }

  function valueOf(id) {
    const field = byId(id);
    return field ? field.value : '';
  }

  function showResult(message, cssClass) {
    const result = byId('result');
    if (!result) return;
    result.style.display = 'block';
    result.className = 'result ' + cssClass;
    result.innerHTML = message;
  }

  function setMissingState(field, isMissing) {
    if (!field) return;
    const question = typeof field.closest === 'function' ? field.closest('.question') : null;
    if (question) question.classList.toggle('has-missing', Boolean(isMissing));
    field.classList.toggle('edu-field-invalid', Boolean(isMissing));
    if (isMissing) {
      field.setAttribute('aria-invalid', 'true');
      field.setAttribute('data-edu-validation-owned', 'true');
    } else if (field.getAttribute('data-edu-validation-owned') === 'true') {
      field.removeAttribute('aria-invalid');
      field.removeAttribute('data-edu-validation-owned');
    }
  }

  function clearResult() {
    const result = byId('result');
    if (!result) return;
    result.style.display = 'none';
    result.innerHTML = '';
  }

  function updateFieldState(event) {
    const field = event.target;
    if (validationAttempted) {
      setMissingState(field, String(field.value).trim() === '');
      const stillMissing = fieldIds.some(id => String(valueOf(id)).trim() === '');
      if (!stillMissing && global.EduToolsUI && typeof global.EduToolsUI.clearValidationSummary === 'function') {
        global.EduToolsUI.clearValidationSummary(document.querySelector('.app-box'));
      }
    }
    clearResult();
  }

  function resetForm() {
    validationAttempted = false;
    fieldIds.forEach(function (id) {
      const field = byId(id);
      if (!field) return;
      field.value = '';
      setMissingState(field, false);
    });
    clearResult();
    if (global.EduToolsUI && typeof global.EduToolsUI.clearValidationSummary === 'function') {
      global.EduToolsUI.clearValidationSummary(document.querySelector('.app-box'));
    }
    const first = byId(fieldIds[0]);
    if (first) first.focus();
  }

  function reportMissing(missingIds) {
    const noun = missingIds.length === 1 ? 'ερώτηση' : 'ερωτήσεις';
    const message = `Απομένουν ${missingIds.length} ${noun} χωρίς απάντηση. Συμπλήρωσέ ${missingIds.length === 1 ? 'την' : 'τις'} και ξαναπάτησε «Έλεγχος δικαιώματος υποψηφιότητας».`;
    showResult(message, 'unknown');
    if (global.EduToolsUI && typeof global.EduToolsUI.reportMissingFields === 'function') {
      global.EduToolsUI.reportMissingFields(missingIds, {
        scope: document.querySelector('.app-box'),
        message: message
      });
    } else {
      const first = byId(missingIds[0]);
      if (first) first.focus();
    }
  }

  function checkEligibility() {
    validationAttempted = true;
    const missingIds = fieldIds.filter(id => String(valueOf(id)).trim() === '');
    fieldIds.forEach(function (id) {
      setMissingState(byId(id), missingIds.indexOf(id) !== -1);
    });

    if (missingIds.length) {
      reportMissing(missingIds);
      return;
    }

    if (valueOf('saek') === 'other') {
      showResult('Η Σ.Α.Ε.Κ. που δήλωσες δεν περιλαμβάνεται στις 26 Σ.Α.Ε.Κ. με κενές θέσεις της συγκεκριμένης πρόσκλησης. Δεν μπορείς να υποβάλεις αίτηση στο πλαίσιο αυτής της πρόσκλησης.', 'not-eligible');
      return;
    }
    if (valueOf('status') === 'no') {
      showResult('Δεν προκύπτει δικαίωμα υποβολής αίτησης: η πρόσκληση περιορίζει τους υποψηφίους σε όσους υπηρετούν στην οικεία Σ.Α.Ε.Κ. με μία από τις προβλεπόμενες ιδιότητες.', 'not-eligible');
      return;
    }
    if (valueOf('requiredDegree') === 'no') {
      showResult('Δεν προκύπτει δικαίωμα υποβολής αίτησης, επειδή δεν δηλώθηκε ο απαιτούμενος τίτλος ανώτατης εκπαίδευσης.', 'not-eligible');
      return;
    }
    if (valueOf('experience') === 'no') {
      showResult('Δεν προκύπτει δικαίωμα υποβολής αίτησης: απαιτούνται τουλάχιστον δύο (2) έτη διοικητικής εμπειρίας ή εκπαιδευτικής υπηρεσίας στην επαγγελματική εκπαίδευση ή κατάρτιση.', 'not-eligible');
      return;
    }
    if (valueOf('evaluationRefusal') === 'yes') {
      showResult('Δεν προκύπτει δικαίωμα συμμετοχής, επειδή δηλώθηκε ενεργός εξαετής αποκλεισμός που συνδέεται με άρνηση ή παρακώλυση της αξιολόγησης.', 'not-eligible');
      return;
    }
    if (valueOf('unsuitable') === 'yes') {
      showResult('Δεν προκύπτει δικαίωμα συμμετοχής, επειδή δηλώθηκε ενεργός τριετής αποκλεισμός μετά από αξιολόγηση του έργου ως «ακατάλληλο».', 'not-eligible');
      return;
    }
    if (valueOf('retirement') === 'yes') {
      showResult('Δεν προκύπτει δικαίωμα συμμετοχής: η πρόσκληση αποκλείει όσους αποχωρούν υποχρεωτικά λόγω συνταξιοδότησης έως 10/09/2027.', 'not-eligible');
      return;
    }

    const values = fieldIds.map(valueOf);
    if (values.includes('unknown')) {
      showResult('Χρειάζεται περαιτέρω έλεγχος, επειδή σε μία ή περισσότερες προϋποθέσεις επέλεξες «Δεν είμαι σίγουρος/η».', 'unknown');
      return;
    }

    showResult('Με βάση τις απαντήσεις σου, πληροίς τις ρητές βασικές προϋποθέσεις συμμετοχής της πρόσκλησης.<br><br><strong>Προσοχή:</strong> ο τελικός έλεγχος των προϋποθέσεων γίνεται από τον/τη Διευθυντή/ντρια της οικείας Σ.Α.Ε.Κ. και η επιλογή δεν βασίζεται σε αριθμητική μοριοδότηση.', 'eligible');
  }

  function init() {
    fieldIds.forEach(function (id) {
      const field = byId(id);
      if (field) field.addEventListener('change', updateFieldState);
    });
    const checkButton = byId('checkEligibilityBtn');
    const resetButton = byId('resetBtn');
    if (checkButton) checkButton.addEventListener('click', checkEligibility);
    if (resetButton) resetButton.addEventListener('click', resetForm);
  }

  global.SaekDeputyEligibilityUI = Object.freeze({
    checkEligibility: checkEligibility,
    resetForm: resetForm
  });

  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init);
  else init();
}(typeof window !== 'undefined' ? window : globalThis));
