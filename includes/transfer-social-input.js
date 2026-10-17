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
  global.EducationTransferSocialInput = Object.freeze({clampChildren: clampChildren});
})(typeof window !== 'undefined' ? window : globalThis);
