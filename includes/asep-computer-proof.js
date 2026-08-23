/* ASEP computer-proof quick check — presentation only. No scoring logic. */
(function (global) {
  'use strict';

  function isActive(input) {
    if (!input || input.disabled) return false;
    if (input.tagName === 'SELECT') return input.value === 'yes';
    return !!input.checked;
  }

  function sync(component) {
    if (!component) return;
    var inputId = component.getAttribute('data-input-id') || 'computer';
    var input = document.getElementById(inputId);
    var panel = component.querySelector('[data-computer-proof-panel]');
    var method = component.querySelector('[data-computer-proof-method]');
    var status = component.querySelector('[data-computer-proof-status]');
    if (!input || !panel || !method || !status) return;

    var active = isActive(input);
    panel.classList.toggle('hidden', !active);
    panel.setAttribute('aria-hidden', active ? 'false' : 'true');
    if (!active) {
      method.value = '';
      status.className = 'asep-computer-proof-status neutral';
      status.textContent = input.disabled
        ? 'Η γνώση Η/Υ δεν μοριοδοτείται για τον επιλεγμένο κλάδο.'
        : 'Επίλεξε τον τρόπο απόδειξης που διαθέτεις.';
      return;
    }

    if (method.value) {
      status.className = 'asep-computer-proof-status success';
      status.textContent = '✓ Ο επιλεγμένος τρόπος περιλαμβάνεται στους αποδεκτούς τρόπους απόδειξης του Παραρτήματος Β΄.';
    } else {
      status.className = 'asep-computer-proof-status neutral';
      status.textContent = 'Επίλεξε τον τρόπο απόδειξης που διαθέτεις.';
    }
  }

  function syncAll() {
    document.querySelectorAll('[data-component="asep-computer-proof"]').forEach(sync);
  }

  function init() {
    syncAll();
    document.addEventListener('input', function () {
      window.setTimeout(syncAll, 0);
    });
    document.addEventListener('change', function () {
      window.setTimeout(syncAll, 0);
    });
    document.addEventListener('click', function (event) {
      if (event.target && (event.target.id === 'resetBtn' || event.target.classList.contains('reset-btn') || event.target.classList.contains('reset-button'))) {
        window.setTimeout(syncAll, 0);
      }
    });
  }

  global.AsepComputerProof = { syncAll: syncAll };
  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init);
  else init();
})(window);
