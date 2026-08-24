/* ASEP pedagogical-proof quick check — presentation only. No scoring logic. */
(function (global) {
  'use strict';

  var labels = {
    'aei-certificate': 'Βεβαίωση Α.Ε.Ι. μετά από ομάδα μαθημάτων / ειδικό πρόγραμμα',
    'education-degree': 'Μεταπτυχιακός ή διδακτορικός τίτλος στις επιστήμες της αγωγής',
    'old-certificate': 'Πιστοποιητικό παιδαγωγικής επάρκειας ν. 3027/2002',
    'pedagogical-department': 'Πτυχίο Παιδαγωγικού / προβλεπόμενου Τμήματος',
    'aspaite-degree': 'Πτυχίο Α.Σ.ΠΑΙ.Τ.Ε.',
    'aspaite-eppaik': 'Πιστοποιητικό ΕΠΠΑΙΚ Α.Σ.ΠΑΙ.Τ.Ε.',
    'aspaite-three-month': 'Βεβαίωση τρίμηνης Παιδαγωγικής Επιμόρφωσης Α.Σ.ΠΑΙ.Τ.Ε.',
    'article99': 'Πιστοποιητικό Π.Δ.Ε. άρθρου 99 ν. 4957/2022',
    'epath': 'Πτυχίο Ε.Π.Α.Θ.',
    'professor-school': 'Πτυχίο πρώην καθηγητικής σχολής',
    'other': 'Άλλο / αβέβαιο αποδεικτικό'
  };

  function componentForInput(inputId) {
    var nodes = document.querySelectorAll('[data-component="asep-pedagogical-proof"]');
    for (var i = 0; i < nodes.length; i += 1) {
      if ((nodes[i].getAttribute('data-input-id') || 'pedagogical') === inputId) return nodes[i];
    }
    return null;
  }

  function setStatus(status, type, text) {
    status.className = 'asep-pedagogical-proof-status ' + type;
    status.textContent = text;
  }

  function sync(component) {
    if (!component) return;
    var inputId = component.getAttribute('data-input-id') || 'pedagogical';
    var cutoff = component.getAttribute('data-aei-cutoff') || '';
    var aeiWarning = component.getAttribute('data-aei-warning') || '';
    var input = document.getElementById(inputId);
    var panel = component.querySelector('[data-pedagogical-proof-panel]');
    var method = component.querySelector('[data-pedagogical-proof-method]');
    var status = component.querySelector('[data-pedagogical-proof-status]');
    if (!input || !panel || !method || !status) return;

    var active = !!input.checked;
    panel.classList.toggle('hidden', !active);
    panel.setAttribute('aria-hidden', active ? 'false' : 'true');
    if (!active) {
      method.value = '';
      setStatus(status, 'neutral', 'Επίλεξε την κατηγορία αποδεικτικού.');
      return;
    }

    var value = method.value;
    if (!value) {
      setStatus(status, 'neutral', 'Δήλωσες Π.Δ.Ε. — επίλεξε τώρα το αποδεικτικό που τη θεμελιώνει.');
    } else if (value === 'aei-certificate') {
      setStatus(status, 'warning', aeiWarning || ('Η κατηγορία προβλέπεται. Έλεγξε ειδικά ότι πληρούται η μεταβατική προϋπόθεση εισαγωγής έως και ' + cutoff + ' και ότι το Τμήμα/Σχολή χορηγούσε την πιστοποίηση κατά τον χρόνο εισαγωγής.'));
    } else if (value === 'epath') {
      setStatus(status, 'warning', 'Η κατηγορία προβλέπεται μόνο με πτυχίο Ε.Π.Α.Θ. που αποκτήθηκε πριν από 12/06/2018.');
    } else if (value === 'professor-school') {
      setStatus(status, 'warning', 'Έλεγξε τη μεταβατική προϋπόθεση: εισαγωγή έως 2014–2015 ή κτήση πτυχίου έως 2017–2018. Για μεταγενέστερη περίπτωση απαιτείται άλλο αποδεικτικό Π.Δ.Ε.');
    } else if (value === 'other') {
      setStatus(status, 'warning', 'Το αποδεικτικό δεν αντιστοιχίστηκε αυτόματα σε ρητή κατηγορία. Χρειάζεται έλεγχος στο Παράρτημα Ε΄ της συγκεκριμένης προκήρυξης.');
    } else {
      setStatus(status, 'success', '✓ Η επιλεγμένη κατηγορία περιλαμβάνεται στις προβλεπόμενες διαδρομές απόδειξης Παιδαγωγικής και Διδακτικής Επάρκειας για αυτό το πλαίσιο.');
    }
  }

  function syncAll() {
    document.querySelectorAll('[data-component="asep-pedagogical-proof"]').forEach(sync);
  }

  function summary(inputId) {
    inputId = inputId || 'pedagogical';
    var component = componentForInput(inputId);
    var input = document.getElementById(inputId);
    if (!component || !input || !input.checked) return 'Παιδαγωγική & Διδακτική Επάρκεια: ΟΧΙ / ΔΕΝ ΔΗΛΩΘΗΚΕ';
    var method = component.querySelector('[data-pedagogical-proof-method]');
    var value = method ? method.value : '';
    return 'Παιδαγωγική & Διδακτική Επάρκεια: ΝΑΙ — ΠΡΟΤΑΞΗ' + (value ? ' · Αποδεικτικό: ' + (labels[value] || value) : ' · Αποδεικτικό: δεν επιλέχθηκε');
  }

  function reset(inputId) {
    var component = componentForInput(inputId || 'pedagogical');
    if (!component) return;
    var method = component.querySelector('[data-pedagogical-proof-method]');
    if (method) method.value = '';
    sync(component);
  }

  function init() {
    syncAll();
    document.addEventListener('input', function () { window.setTimeout(syncAll, 0); });
    document.addEventListener('change', function () { window.setTimeout(syncAll, 0); });
  }

  global.AsepPedagogicalProof = { syncAll: syncAll, summary: summary, reset: reset };
  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init);
  else init();
})(window);
