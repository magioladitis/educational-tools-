(function (global) {
  'use strict';

  var initialized = false;
  var byId = function (id) { return document.getElementById(id); };
  var calc = global.NewlyAppointedPlacementCalculations;

  function formatPoints(value) {
    return Number(value || 0).toLocaleString('el-GR', { maximumFractionDigits: 2 });
  }

  function getInput() {
    return {
      familyStatus: byId('familyStatus').value,
      eligibleChildren: byId('eligibleChildren').value,
      coService: byId('coService').checked,
      locality: byId('locality').checked
    };
  }

  function render() {
    var result = calc.calculate(getInput());
    byId('totalResult').textContent = formatPoints(result.total);
    byId('familyStatusResult').textContent = formatPoints(result.familyStatusPoints);
    byId('childrenResult').textContent = formatPoints(result.childPoints);
    byId('familyResult').textContent = formatPoints(result.familyPoints);
    byId('coServiceResult').textContent = formatPoints(result.coServicePoints);
    byId('localityResult').textContent = formatPoints(result.localityPoints);
    if (result.familyStatusRequiresChild && result.eligibleChildren === 0) {
      byId('statusResult').textContent = 'Η επιλεγμένη οικογενειακή κατάσταση δίνει 4 μόρια μόνο όταν υπάρχει μοριοδοτούμενο τέκνο και πληρούνται οι προϋποθέσεις επιμέλειας.';
    } else {
      byId('statusResult').textContent = result.total > 0
        ? 'Το σύνολο αφορά το συγκεκριμένο σχολείο/Δήμο. Έλεγξε ξανά συνυπηρέτηση και εντοπιότητα για κάθε διαφορετικό Δήμο.'
        : 'Δεν έχουν επιλεγεί μοριοδοτούμενα κριτήρια για τον συγκεκριμένο Δήμο.';
    }
  }

  function reset() {
    byId('familyStatus').value = 'none';
    byId('eligibleChildren').value = '0';
    byId('coService').checked = false;
    byId('locality').checked = false;
    render();
  }

  function init() {
    if (initialized) return;
    initialized = true;

    byId('eligibleChildren').addEventListener('input', function () {
      var n = Math.floor(Number(this.value || 0));
      if (!Number.isFinite(n)) n = 0;
      this.value = String(Math.max(0, Math.min(20, n)));
      render();
    });

    ['familyStatus', 'coService', 'locality'].forEach(function (id) {
      byId(id).addEventListener('change', render);
    });

    byId('resetBtn').addEventListener('click', reset);
    render();
  }

  global.NewlyAppointedPlacementUI = Object.freeze({
    init: init,
    render: render,
    reset: reset
  });

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init, { once: true });
  } else {
    init();
  }
})(window);
