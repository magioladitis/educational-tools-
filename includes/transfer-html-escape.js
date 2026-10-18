/* Shared HTML escaping for browser transfer controllers (both education grades).
 * Evidence/source modules that are used in standalone Node VM contexts retain
 * their independent escape until their harnesses are migrated.
 */
(function (global) {
  'use strict';
  function escapeHtml(value) {
    return String(value).replace(/[&<>"']/g, function (c) {
      return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c];
    });
  }
  global.EducationTransferHtmlEscape = Object.freeze({escapeHtml:escapeHtml});
})(typeof window !== 'undefined' ? window : globalThis);
