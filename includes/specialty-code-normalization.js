/*
 * Canonical teacher-specialty code normalization.
 *
 * One browser-side source of truth for ΠΕ / ΤΕ / ΔΕ codes. Callers that need
 * to extract a code from a free-text CSV cell should use extract(), which in
 * turn delegates the final normalization to normalize().
 */
(function (global) {
  'use strict';

  function prefixKind(text) {
    if (/^(?:PE|PΕ|ΠE|ΠΕ)/i.test(text)) return 'ΠΕ';
    if (/^(?:TE|TΕ|ΤE|ΤΕ)/i.test(text)) return 'ΤΕ';
    if (/^(?:DE|DΕ|ΔE|ΔΕ)/i.test(text)) return 'ΔΕ';
    return '';
  }

  function normalize(value) {
    var text = String(value == null ? '' : value).trim().toUpperCase().replace(/\s+/g, '');
    if (!text) return '';

    var prefix = prefixKind(text);
    if (!prefix) return '';

    var rest = text.slice(2);
    var match = rest.match(/^([0-9]{1,2})(?:[.\-_/]([0-9]{1,2}))?$/);
    if (!match) return '';

    var main = String(parseInt(match[1], 10));
    if (main.length < 2) main = '0' + main;
    var result = prefix + main;

    if (match[2] !== undefined) {
      var sub = String(parseInt(match[2], 10));
      if (sub.length < 2) sub = '0' + sub;
      result += '.' + sub;
    }
    return result;
  }

  function extract(value) {
    var text = String(value == null ? '' : value).toUpperCase();
    if (!text) return '';
    var match = text.match(/(?:PE|PΕ|ΠE|ΠΕ|TE|TΕ|ΤE|ΤΕ|DE|DΕ|ΔE|ΔΕ)\s*[0-9]{1,2}(?:\s*[.\-_/]\s*[0-9]{1,2})?/i);
    return match ? normalize(match[0]) : '';
  }

  var api = Object.freeze({
    schema: 'teacher_specialty_code_normalization_v1',
    normalize: normalize,
    extract: extract
  });

  global.EducationSpecialtyCodes = api;
  if (typeof module !== 'undefined' && module.exports) module.exports = api;
})(typeof window !== 'undefined' ? window : globalThis);
