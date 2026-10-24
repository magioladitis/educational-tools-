(function (root) {
  'use strict';
  function numberOrNull(v) {
    if (v === '' || v === null || typeof v === 'undefined') return null;
    var n = Number(String(v).replace(',', '.'));
    return Number.isFinite(n) && n >= 0 && n <= 60 ? n : null;
  }
  function evaluate(data) {
    var fields = ['education', 'teaching', 'sameLevel'];
    var values = {};
    fields.forEach(function (k) { values[k] = numberOrNull(data[k]); });
    var checks = [
      { label: '12 έτη εκπαιδευτικής υπηρεσίας', status: values.education === null ? 'unknown' : (values.education >= 12 ? 'pass' : 'fail') },
      { label: '8 έτη διδακτικής υπηρεσίας', status: values.teaching === null ? 'unknown' : (values.teaching >= 8 ? 'pass' : 'fail') },
      { label: '4 έτη διδακτικής στην οικεία βαθμίδα', status: values.sameLevel === null ? 'unknown' : (values.sameLevel >= 4 ? 'pass' : 'fail') },
      { label: 'ΤΠΕ Α΄ επιπέδου ή νόμιμη ισοδυναμία', status: data.ict === 'yes' ? 'pass' : (data.ict === 'no' ? 'fail' : 'unknown') },
      { label: 'Υπηρέτηση στην οικεία βαθμίδα', status: data.levelMatch === 'yes' ? 'pass' : (data.levelMatch === 'no' ? 'fail' : 'unknown') },
      { label: 'Υπηρεσιακά / διοικητικά κωλύματα', status: data.administrative === 'clear' ? 'pass' : (data.administrative === 'issue' ? 'unknown' : 'unknown') }
    ];
    var school = data.school || 'gym';
    var branch = data.branch || '';
    var branchLevel = data.branchLevel || 'unknown';
    var secondaryAllowed = /^(PE0[1-8]|PE11|PE33|PE34|PE40|PE(7[8-9]|8[0-9]|9[01]))$/.test(branch);
    var ekAllowed = /^PE(8[0-9]|90)$/.test(branch);
    var special = /^(special_|model|other)/.test(school) || school === 'eeeeek' || school === 'deaf_blind';
    var primarySchool = school === 'kindergarten' || school === 'primary_school' || school === 'special_kindergarten' || school === 'special_primary';
    var positionLevel = primarySchool ? 'primary' : (school === 'deaf_blind' ? (data.specialLevel === 'secondary' ? 'secondary' : 'primary') : 'secondary');
    var branchStatus = 'unknown';
    if (branch) {
      if (school === 'kindergarten') branchStatus = branch === 'PE60' ? 'pass' : 'fail';
      else if (school === 'primary_school') branchStatus = branchLevel === 'primary' && branch !== 'EEP' && branch !== 'OTHER' ? 'pass' : (branchLevel === 'secondary' || branch === 'EEP' ? 'fail' : 'unknown');
      else if (school === 'gym' || school === 'gel' || school === 'epal') branchStatus = secondaryAllowed && branchLevel === 'secondary' ? 'pass' : (!secondaryAllowed || branchLevel === 'primary' ? 'fail' : 'unknown');
      else if (school === 'ek') branchStatus = ekAllowed && branchLevel === 'secondary' ? 'pass' : (!ekAllowed || branchLevel === 'primary' ? 'fail' : 'unknown');
    }
    checks.push({ label: 'Κλάδος για τον επιλεγμένο τύπο σχολείου (άρθρο 31 παρ. 6)', status: branchStatus });
    if (school === 'special_kindergarten' || school === 'special_primary' || school === 'special_secondary' || school === 'eeeeek' || school === 'deaf_blind') {
      var eaeSchool = school === 'deaf_blind' ? data.specialLevel : school;
      var requiredLevel = eaeSchool === 'primary_kindergarten' || eaeSchool === 'special_kindergarten' || eaeSchool === 'special_primary' || eaeSchool === 'primary' ? 'primary' : 'secondary';
      var eligibleBranch = 'unknown';
      if (eaeSchool === 'primary_kindergarten' || eaeSchool === 'special_kindergarten') eligibleBranch = branch === 'PE60' || branch === 'PE61' ? 'pass' : (branch ? 'fail' : 'unknown');
      else if (eaeSchool === 'primary' || eaeSchool === 'special_primary') eligibleBranch = branchLevel === 'primary' && branch !== 'EEP' ? 'pass' : (branch === 'EEP' ? 'unknown' : branch ? 'unknown' : 'unknown');
      else if (eaeSchool === 'secondary' || eaeSchool === 'special_secondary') eligibleBranch = branch === 'EEP' ? 'unknown' : (secondaryAllowed && branchLevel === 'secondary' ? 'pass' : 'unknown');
      else if (eaeSchool === 'eeeeek') eligibleBranch = branch === 'PE70' || branch === 'PE71' ? 'unknown' : (branch === 'EEP' ? 'unknown' : (secondaryAllowed && branchLevel === 'secondary' ? 'pass' : 'unknown'));
      checks.push({label:'Κλάδος ειδικής σχολικής μονάδας (άρθρο 31 §6δ)',status:eligibleBranch});
      checks.push({label:'Εξειδίκευση ΕΑΕ / προσόντα τοποθέτησης σε ΣΜΕΑΕ',status:data.eaeEligible==='yes'?'pass':(data.eaeEligible==='no'?'fail':'unknown')});
      var years = numberOrNull(data.specialYears);
      checks.push({label:'4 έτη στον προβλεπόμενο τύπο ΣΜΕΑΕ',status:years === null?'unknown':(years >= 4?'pass':'fail')});
      if (branch === 'EEP') {
        var support = numberOrNull(data.supportYears);
        checks.push({label:'8 έτη υποστηρικτικής υπηρεσίας ΕΕΠ',status:support===null?'unknown':(support>=8?'pass':'fail')});
        // Το άρθρο 31 επιτρέπει ΕΕΠ με ιδιαίτερο υπηρεσιακό καθεστώς. Οι γενικές διδακτικές προϋποθέσεις απαιτούν ειδική κρίση.
        checks.slice(1,3).forEach(function(c){c.status='unknown';});
      }
      if (school === 'deaf_blind') {
        var sensory = data.sensoryType;
        var proof = data.sensoryProof;
        checks.push({label:'Πιστοποιημένη ΕΝΓ/Braille ή οργανική θέση και 4 έτη στην ίδια ΣΜΕΑΕ',status:sensory==='unknown'?'unknown':(proof==='certified'||proof==='organic4'?'pass':proof==='none'?'unknown':'unknown')});
      }
    }
    checks.push({ label: 'Ειδικές προϋποθέσεις θέσης / προκήρυξης', status: special ? 'unknown' : (data.specialQualification === 'confirmed' ? 'pass' : 'unknown') });
    var warnings = [];
    if (values.teaching !== null && values.education !== null && values.teaching > values.education) warnings.push('Η διδακτική υπηρεσία υπερβαίνει τη συνολική εκπαιδευτική: έλεγξε τις τιμές.');
    if (values.sameLevel !== null && values.teaching !== null && values.sameLevel > values.teaching) warnings.push('Η υπηρεσία στην οικεία βαθμίδα υπερβαίνει τη συνολική διδακτική: έλεγξε τις τιμές.');
    if (data.reannouncement && data.reannouncement !== 'normal') warnings.push('Επαναπροκήρυξη ή εξαίρεση: οι όροι της συγκεκριμένης πρόσκλησης απαιτούν εξατομικευμένη υπηρεσιακή κρίση.');
    if (school === 'deaf_blind' && data.sensoryProof === 'none') warnings.push('Εναλλακτική εξαίρεση μόνο με τις ειδικές προϋποθέσεις του άρθρου 31 §6δδ και επιβεβαίωση έλλειψης υποψηφίων.');
    if (special) warnings.push('Ο ειδικός τύπος σχολείου απαιτεί εξειδικευμένο υπηρεσιακό έλεγχο (π.χ. ΕΑΕ/ΕΕΠ, τύπος ΣΜΕΑΕ, Πρότυπα ή Πειραματικά).');
    if (branchLevel !== 'unknown' && branchLevel !== positionLevel && !(special && branchLevel === 'eep')) warnings.push('Η βαθμίδα υπηρεσίας δεν αντιστοιχεί στη θέση. Έλεγξε τις νόμιμες εξαιρέσεις.');
    if (data.specialQualification === 'not_confirmed') warnings.push('Δεν έχουν επιβεβαιωθεί οι ειδικές προϋποθέσεις της θέσης.');
    if (data.administrative === 'issue') warnings.push('Πιθανό διοικητικό κώλυμα: απαιτείται υπηρεσιακός έλεγχος.');
    var anyFail = checks.some(function (c) { return c.status === 'fail'; });
    var anyUnknown = checks.some(function (c) { return c.status === 'unknown'; });
    return { status: anyFail ? 'not_met' : (anyUnknown || warnings.length ? 'review' : 'basic_pass'), checks: checks, warnings: warnings };
  }
  if (typeof module !== 'undefined' && module.exports) module.exports = { evaluate: evaluate, numberOrNull: numberOrNull };
  if (!root.document) return;
  var doc = root.document;
  var ids = ['school', 'branch', 'branchLevel', 'specialQualification', 'specialLevel', 'eaeEligible', 'specialYears', 'supportYears', 'sensoryType', 'sensoryProof', 'reannouncement', 'education', 'teaching', 'sameLevel', 'ict', 'levelMatch', 'administrative'];
  function getData() { var d = {}; ids.forEach(function (id) { d[id] = doc.getElementById(id).value; }); return d; }
  function el(tag, text) { var e = doc.createElement(tag); e.textContent = text; return e; }
  doc.getElementById('checkEligibility').addEventListener('click', function () {
    var result = evaluate(getData()); var host = doc.getElementById('eligibilityResult');
    while (host.children.length > 1) host.removeChild(host.lastChild);
    var message = result.status === 'not_met' ? 'Δεν πληρούται τουλάχιστον μία από τις εξεταζόμενες γενικές προϋποθέσεις.' : result.status === 'basic_pass' ? 'Οι εξεταζόμενες γενικές προϋποθέσεις φαίνεται να καλύπτονται. Απαιτείται τελικός έλεγχος της προκήρυξης, των ειδικών απαιτήσεων και των κωλυμάτων.' : 'Δεν μπορεί να εξαχθεί οριστικό συμπέρασμα. Απαιτείται συμπλήρωση ή επιβεβαίωση στοιχείων.';
    var head = el('p', message); head.className = 'leadership-result ' + result.status; host.appendChild(head);
    var ul = doc.createElement('ul'); result.checks.forEach(function (c) { var names = { pass: '✓', fail: '✕', unknown: '○' }; var li = el('li', names[c.status] + ' ' + c.label + (c.status === 'unknown' ? ' — χρειάζεται έλεγχος' : '')); ul.appendChild(li); }); host.appendChild(ul);
    if (result.warnings.length) { var w = doc.createElement('ul'); result.warnings.forEach(function (s) { w.appendChild(el('li', s)); }); w.className = 'leadership-warnings'; host.appendChild(w); }
  });
  doc.getElementById('resetEligibility').addEventListener('click', function () { ids.forEach(function (id) { var e = doc.getElementById(id); if (e.tagName === 'INPUT') e.value = ''; else e.selectedIndex = 0; }); doc.getElementById('eligibilityResult').innerHTML = '<h2 id="resultTitle">Β. Αποτέλεσμα προελέγχου</h2><p>Συμπλήρωσε τα στοιχεία και επίλεξε «Έλεγχος γενικών προϋποθέσεων».</p>'; });
  doc.getElementById('printEligibility').addEventListener('click', function () { root.print(); });
})(typeof window !== 'undefined' ? window : globalThis);
