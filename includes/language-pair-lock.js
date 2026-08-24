/*
 * Shared UI lock for simple two-language selectors.
 * Keeps named languages distinct across the two selects without touching
 * generic "Άλλη" choices, which may represent two different languages.
 */
(function (global) {
  'use strict';

  function byId(ref) {
    if (!ref) return null;
    if (typeof ref === 'string') return document.getElementById(ref);
    return ref;
  }

  function isGenericOther(value) {
    return /^other\d*$/i.test(String(value || '').trim());
  }

  function keyOf(value) {
    const raw = String(value || '').trim();
    if (!raw || isGenericOther(raw)) return '';
    if (global.EducationLanguages && typeof global.EducationLanguages.resolveLanguage === 'function') {
      const resolved = global.EducationLanguages.resolveLanguage(raw, '');
      return String((resolved && resolved.key) || raw).trim().toLowerCase();
    }
    return raw.toLowerCase();
  }

  function resetRelated(refs) {
    (refs || []).forEach(function (ref) {
      const el = byId(ref);
      if (!el) return;
      if (el.tagName === 'SELECT') el.selectedIndex = 0;
      else if (el.type === 'checkbox' || el.type === 'radio') el.checked = false;
      else el.value = '';
    });
  }

  function releaseOwnLocks(select) {
    if (!select || !select.options) return;
    Array.from(select.options).forEach(function (option) {
      if (option.dataset.languagePairLocked === '1') {
        option.disabled = false;
        delete option.dataset.languagePairLocked;
      }
    });
  }

  function lockKey(select, key) {
    if (!select || !key || !select.options) return;
    Array.from(select.options).forEach(function (option) {
      if (keyOf(option.value) === key) {
        option.disabled = true;
        option.dataset.languagePairLocked = '1';
      }
    });
  }

  function sync(options) {
    const opts = options || {};
    const first = byId(opts.first);
    const second = byId(opts.second);
    if (!first || !second) return { cleared: null, firstKey: '', secondKey: '' };

    releaseOwnLocks(first);
    releaseOwnLocks(second);

    let firstKey = keyOf(first.value);
    let secondKey = keyOf(second.value);
    let cleared = null;

    // Preserve the first-language choice if a stale/programmatic duplicate exists.
    if (firstKey && secondKey && firstKey === secondKey) {
      second.selectedIndex = 0;
      resetRelated(opts.relatedSecond);
      secondKey = '';
      cleared = 'second';
    }

    if (firstKey) lockKey(second, firstKey);
    if (secondKey) lockKey(first, secondKey);

    return { cleared: cleared, firstKey: firstKey, secondKey: secondKey };
  }

  global.LanguagePairLock = Object.freeze({
    keyOf: keyOf,
    sync: sync
  });
})(typeof window !== 'undefined' ? window : globalThis);
