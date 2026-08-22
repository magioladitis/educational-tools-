/*
 * Κοινή λογική μοριοδότησης ξένων γλωσσών.
 * - Η ίδια γλώσσα μοριοδοτείται μόνο μία φορά, στο ανώτερο δηλωμένο επίπεδο.
 * - Μπορούν να εξαιρεθούν γλώσσες που αποτέλεσαν προσόν διορισμού.
 * - Η επιλογή «Άλλη» υποστηρίζει ελεύθερο όνομα γλώσσας και ασφαλή σύγκριση διπλοεγγραφών.
 */
(function (global) {
  'use strict';

  const LABELS = Object.freeze({
    en: 'Αγγλική',
    fr: 'Γαλλική',
    de: 'Γερμανική',
    it: 'Ιταλική',
    es: 'Ισπανική',
    other: 'Άλλη ξένη γλώσσα'
  });

  function normalizeText(value) {
    return String(value || '')
      .trim()
      .toLocaleLowerCase('el-GR')
      .normalize('NFD')
      .replace(/[\u0300-\u036f]/g, '')
      .replace(/\s+/g, ' ');
  }

  function resolveLanguage(language, otherText) {
    const code = String(language || '').trim();
    if (!code) return { key: '', label: '' };
    if (code === 'other') {
      const label = String(otherText || '').trim();
      return label
        ? { key: 'other:' + normalizeText(label), label }
        : { key: '', label: '' };
    }
    return { key: code, label: LABELS[code] || code };
  }

  function calculatePair(entries, options) {
    const opts = options || {};
    const excluded = new Set((opts.excluded || []).filter(Boolean));
    const cap = Number.isFinite(Number(opts.cap)) ? Number(opts.cap) : Infinity;
    const byLanguage = new Map();
    const warnings = [];
    const duplicates = [];
    const excludedEntries = [];
    const missingLanguage = [];

    (entries || []).forEach((entry, index) => {
      const points = Math.max(0, Number(entry && entry.points) || 0);
      const resolved = resolveLanguage(entry && entry.language, entry && entry.otherText);

      if (points > 0 && !resolved.key) {
        missingLanguage.push(index);
        warnings.push(`Επίλεξε ποια είναι η ${index + 1}η ξένη γλώσσα για να μοριοδοτηθεί.`);
        return;
      }
      if (!resolved.key || points <= 0) return;

      if (excluded.has(resolved.key)) {
        excludedEntries.push(index);
        warnings.push(`${resolved.label}: δεν μοριοδοτείται επειδή αποτέλεσε προσόν διορισμού.`);
        return;
      }

      const previous = byLanguage.get(resolved.key);
      if (previous) {
        duplicates.push(index);
        if (points > previous.points) {
          previous.points = points;
          previous.index = index;
        }
        warnings.push(`${resolved.label}: δηλώθηκε περισσότερες από μία φορές· υπολογίζεται μόνο το ανώτερο επίπεδο.`);
      } else {
        byLanguage.set(resolved.key, {
          key: resolved.key,
          label: resolved.label,
          points,
          index
        });
      }
    });

    const accepted = Array.from(byLanguage.values());
    const raw = accepted.reduce((sum, item) => sum + item.points, 0);
    return {
      raw,
      points: Math.min(raw, cap),
      accepted,
      warnings,
      duplicates,
      excludedEntries,
      missingLanguage
    };
  }

  global.EducationLanguages = Object.freeze({
    LABELS,
    normalizeText,
    resolveLanguage,
    calculatePair
  });
})(typeof window !== 'undefined' ? window : globalThis);
