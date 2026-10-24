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
    var warnings = [];
    if (values.teaching !== null && values.education !== null && values.teaching > values.education) warnings.push('Η διδακτική υπηρεσία υπερβαίνει τη συνολική εκπαιδευτική: έλεγξε τις τιμές.');
    if (values.sameLevel !== null && values.teaching !== null && values.sameLevel > values.teaching) warnings.push('Η υπηρεσία στην οικεία βαθμίδα υπερβαίνει τη συνολική διδακτική: έλεγξε τις τιμές.');
    if (data.school !== 'general') warnings.push('Ο ειδικός τύπος σχολείου απαιτεί επιπλέον προϋποθέσεις· δεν παρέχεται θετική βεβαίωση.');
    if (data.administrative === 'issue') warnings.push('Πιθανό διοικητικό κώλυμα: απαιτείται υπηρεσιακός έλεγχος.');
    var anyFail = checks.some(function (c) { return c.status === 'fail'; });
    var anyUnknown = checks.some(function (c) { return c.status === 'unknown'; });
    return { status: anyFail ? 'not_met' : (anyUnknown || warnings.length ? 'review' : 'basic_pass'), checks: checks, warnings: warnings };
  }
  if (typeof module !== 'undefined' && module.exports) module.exports = { evaluate: evaluate, numberOrNull: numberOrNull };
  if (!root.document) return;
  var doc = root.document;
  var ids = ['level', 'school', 'education', 'teaching', 'sameLevel', 'ict', 'levelMatch', 'administrative'];
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
