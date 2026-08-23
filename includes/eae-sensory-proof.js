/* EAE sensory priority proof checks — presentation only, no scoring. */
(function (global) {
  'use strict';

  function emptyMessage(panel) {
    return panel.getAttribute('data-kind') === 'braille'
      ? 'Επίλεξε τον φορέα ή τον τρόπο απόδειξης που διαθέτεις.'
      : 'Επίλεξε τον φορέα έκδοσης του πιστοποιητικού ή της βεβαίωσης.';
  }

  function sync(panel) {
    var input = document.getElementById(panel.getAttribute('data-input-id') || '');
    var method = panel.querySelector('[data-eae-sensory-method]');
    var status = panel.querySelector('[data-eae-sensory-status]');
    if (!input || !method || !status) return;

    var active = !!input.checked && !input.disabled;
    panel.classList.toggle('hidden', !active);
    panel.setAttribute('aria-hidden', active ? 'false' : 'true');
    if (!active) {
      method.value = '';
      status.className = 'eae-sensory-status neutral';
      status.textContent = emptyMessage(panel);
      return;
    }

    if (!method.value) {
      status.className = 'eae-sensory-status neutral';
      status.textContent = emptyMessage(panel);
    } else if (method.value === 'other') {
      status.className = 'eae-sensory-status warning';
      status.textContent = '⚠ Ο φορέας δεν περιλαμβάνεται ρητά στους αποδεκτούς τρόπους απόδειξης της προκήρυξης. Απαιτείται επίσημος έλεγχος.';
    } else {
      status.className = 'eae-sensory-status success';
      status.textContent = '✓ Ο επιλεγμένος φορέας ή τρόπος απόδειξης αναφέρεται ρητά στην προκήρυξη.';
    }
  }

  function syncAll() {
    document.querySelectorAll('[data-eae-sensory-panel]').forEach(sync);
  }

  function init() {
    syncAll();
    document.addEventListener('input', function () { window.setTimeout(syncAll, 0); });
    document.addEventListener('change', function () { window.setTimeout(syncAll, 0); });
    document.addEventListener('click', function (event) {
      if (event.target && (event.target.id === 'resetBtn' || event.target.classList.contains('reset-btn') || event.target.classList.contains('reset-button'))) {
        window.setTimeout(syncAll, 0);
      }
    });
  }

  global.EaeSensoryProof = { syncAll: syncAll };
  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init);
  else init();
})(window);
