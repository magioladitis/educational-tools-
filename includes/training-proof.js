/* Shared ASEP seminar-certificate proof behaviour — no scoring logic. */
(function (global) {
  'use strict';

  function componentByRef(ref) {
    if (!ref) return null;
    if (typeof ref !== 'string') return ref;
    return document.getElementById(ref) || document.querySelector('[data-component="training-proof"][data-input-ids~="' + ref + '"]');
  }

  function triggerIds(component) {
    return (component.getAttribute('data-input-ids') || 'training').split(/\s+/).filter(Boolean);
  }

  function triggerActive(input) {
    if (!input || input.disabled) return false;
    if (input.tagName === 'SELECT') return input.value === 'yes';
    return !!input.checked;
  }

  function isActive(component) {
    return triggerIds(component).some(function (id) {
      return triggerActive(document.getElementById(id));
    });
  }

  function selectedValue(component) {
    var selected = component.querySelector('input[type="radio"]:checked');
    return selected ? selected.value : '';
  }

  function getState(ref) {
    var component = componentByRef(ref);
    return component ? { active: isActive(component), value: selectedValue(component) } : { active: false, value: '' };
  }

  function sync(component) {
    if (!component) return;
    var status = component.querySelector('.training-proof-status');
    if (!status) return;
    var active = isActive(component);
    component.classList.toggle('hidden', !active);
    component.setAttribute('aria-hidden', active ? 'false' : 'true');

    if (!active) {
      component.querySelectorAll('input[type="radio"]').forEach(function (radio) { radio.checked = false; });
      status.className = 'training-proof-status neutral';
      status.textContent = 'Έλεγξε το πιστοποιητικό πριν την υποβολή των δικαιολογητικών.';
      return;
    }

    var value = selectedValue(component);
    status.className = 'training-proof-status ' + (value === 'yes' ? 'success' : value === 'no' ? 'warning' : 'neutral');
    if (value === 'yes') status.textContent = '✓ Οι ημερομηνίες έναρξης και λήξης αναγράφονται στο πιστοποιητικό.';
    else if (value === 'no') status.textContent = '⚠ Απαιτείται πρόσθετη βεβαίωση από τον οικείο φορέα με την ημερομηνία έναρξης και λήξης.';
    else status.textContent = 'Έλεγξε το πιστοποιητικό πριν την υποβολή των δικαιολογητικών.';
  }

  function syncAll() {
    document.querySelectorAll('[data-component="training-proof"]').forEach(sync);
  }

  function summary(ref) {
    var state = getState(ref);
    if (!state.active) return '';
    if (state.value === 'yes') return 'Πιστοποιητικό σεμιναρίου: αναγράφονται ημερομηνία έναρξης και λήξης.';
    if (state.value === 'no') return 'ΔΙΚΑΙΟΛΟΓΗΤΙΚΟ: απαιτείται βεβαίωση φορέα με ημερομηνία έναρξης και λήξης του σεμιναρίου.';
    return 'Έλεγχος πιστοποιητικού σεμιναρίου: εκκρεμεί ο έλεγχος ημερομηνίας έναρξης και λήξης.';
  }

  function warning(ref) {
    var state = getState(ref);
    if (!state.active || state.value === 'yes') return '';
    if (state.value === 'no') return 'Για το σεμινάριο απαιτείται πρόσθετη βεβαίωση από τον οικείο φορέα, επειδή στο πιστοποιητικό δεν αναγράφονται η ημερομηνία έναρξης και λήξης.';
    return 'Εκκρεμεί ο έλεγχος αν στο πιστοποιητικό του σεμιναρίου αναγράφονται η ημερομηνία έναρξης και λήξης.';
  }

  function init() {
    syncAll();
    document.addEventListener('input', function () { window.setTimeout(syncAll, 0); });
    document.addEventListener('change', function () { window.setTimeout(syncAll, 0); });
    document.addEventListener('click', function (event) {
      if (event.target && (event.target.id === 'resetBtn' || event.target.classList.contains('reset-btn') || event.target.classList.contains('reset-button'))) window.setTimeout(syncAll, 0);
    });
  }

  global.TrainingProof = { getState: getState, summary: summary, warning: warning, syncAll: syncAll };
  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init);
  else init();
})(window);
