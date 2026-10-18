/* Shared input guard for the 2026–27 transfer social criteria.
 * Both P.E. and D.E. use the same physical integer child-count restriction.
 * An empty field stays empty, while pasted/typed out-of-range values are clamped.
 */
(function (global) {
  'use strict';
  function clampChildren(input) {
    if (!input || input.value === '') return;
    var n = Number(input.value);
    if (!Number.isFinite(n)) { input.value = ''; return; }
    input.value = String(Math.max(0, Math.min(20, Math.floor(n))));
  }
  // A single visibility rule for the spouse-employment dropdown on both grades.
  // Grade-specific eligibility rules remain in their respective controllers.
  function syncCoServiceField(checkbox, field, choice) {
    if (!checkbox || !field || !choice) return false;
    var visible = checkbox.checked || choice.value !== 'unspecified';
    field.style.display = visible ? 'block' : 'none';
    if (typeof field.setAttribute === 'function') field.setAttribute('aria-hidden', visible ? 'false' : 'true');
    return visible;
  }
  function bindCoServiceField(checkbox, field, choice) {
    if (!checkbox || !field || !choice) return;
    // Direct input handling keeps the dropdown responsive even when another
    // unrelated numeric field prevents a complete recalculation.
    ['input', 'change'].forEach(function (eventName) {
      checkbox.addEventListener(eventName, function () {
        syncCoServiceField(checkbox, field, choice);
      });
      choice.addEventListener(eventName, function () {
        syncCoServiceField(checkbox, field, choice);
      });
    });
    syncCoServiceField(checkbox, field, choice);
  }
  // Bind the shared spouse dropdown independently of the grade controller.
  // A missing optional Primary vacancy/eligibility asset must not disable this UI.
  function initializeSocialFields() {
    [['coService','transferCoServiceCaseField','transferCoServiceCase'],
     ['primaryCoservice','primaryCoServiceCaseField','primaryCoServiceCase']].forEach(function (ids) {
      var checkbox=document.getElementById(ids[0]);
      var field=document.getElementById(ids[1]);
      var choice=document.getElementById(ids[2]);
      if (!checkbox || !field || !choice || field.getAttribute('data-social-bound')==='yes') return;
      bindCoServiceField(checkbox, field, choice);
      field.setAttribute('data-social-bound','yes');
    });
  }
  if (typeof document !== 'undefined' && document.getElementById) {
    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', initializeSocialFields);
    else initializeSocialFields();
  }
  global.EducationTransferSocialInput = Object.freeze({
    clampChildren: clampChildren,
    syncCoServiceField: syncCoServiceField,
    bindCoServiceField: bindCoServiceField
  });
})(typeof window !== 'undefined' ? window : globalThis);
