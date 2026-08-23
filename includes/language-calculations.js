/*
 * Ενιαία λογική μοριοδότησης ξένων γλωσσών για εργαλεία εκπαιδευτικών.
 *
 * Single source of truth:
 * - πλήθος γλωσσών που μοριοδοτούνται ανά προφίλ,
 * - μόρια ανά επίπεδο ή/και θέση κατάταξης,
 * - εξαίρεση της γλώσσας του κλάδου ΠΕ05/ΠΕ06/ΠΕ07/ΠΕ34/ΠΕ40,
 * - διπλοεγγραφές ίδιας γλώσσας.
 *
 * Το UI/controller βρίσκεται στο includes/asep-language-selector.js.
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

  const LEVEL_LABELS = Object.freeze({
    none: 'Καμία / χωρίς μόρια',
    good: 'Καλή γνώση',
    very_good: 'Πολύ καλή γνώση',
    excellent: 'Άριστη γνώση'
  });

  const OWN_LANGUAGE_BY_SPECIALTY = Object.freeze({
    'ΠΕ05': 'fr', 'PE05': 'fr',
    'ΠΕ06': 'en', 'PE06': 'en',
    'ΠΕ07': 'de', 'PE07': 'de',
    'ΠΕ34': 'it', 'PE34': 'it',
    'ΠΕ40': 'es', 'PE40': 'es'
  });

  // Prevent bypassing duplicate / branch-language rules through "Άλλη γλώσσα".
  // These aliases are normalized by normalizeText() before lookup.
  const KNOWN_LANGUAGE_ALIASES = Object.freeze({
    'αγγλικη': 'en', 'αγγλικα': 'en', 'αγγλικη γλωσσα': 'en', 'english': 'en', 'english language': 'en',
    'γαλλικη': 'fr', 'γαλλικα': 'fr', 'γαλλικη γλωσσα': 'fr', 'french': 'fr', 'francais': 'fr', 'français': 'fr',
    'γερμανικη': 'de', 'γερμανικα': 'de', 'γερμανικη γλωσσα': 'de', 'german': 'de', 'deutsch': 'de',
    'ιταλικη': 'it', 'ιταλικα': 'it', 'ιταλικη γλωσσα': 'it', 'italian': 'it', 'italiano': 'it',
    'ισπανικη': 'es', 'ισπανικα': 'es', 'ισπανικη γλωσσα': 'es', 'spanish': 'es', 'espanol': 'es', 'español': 'es'
  });

  function freezeProfile(maxLanguages, points, excludeOwnSpecialtyLanguage) {
    return Object.freeze({
      maxLanguages: maxLanguages,
      levelPoints: Object.freeze({
        none: 0,
        good: points.good,
        very_good: points.very_good,
        excellent: points.excellent
      }),
      excludeOwnSpecialtyLanguage: Boolean(excludeOwnSpecialtyLanguage),
      scoringMode: 'uniform'
    });
  }

  function freezeRankedProfile(positionPoints, excludeOwnSpecialtyLanguage) {
    const positions = (positionPoints || []).map(function (points) {
      return Object.freeze({
        none: 0,
        good: Number(points.good) || 0,
        very_good: Number(points.very_good) || 0,
        excellent: Number(points.excellent) || 0
      });
    });
    if (!positions.length) throw new Error('Απαιτείται τουλάχιστον μία θέση μοριοδότησης ξένης γλώσσας.');
    return Object.freeze({
      maxLanguages: positions.length,
      // levelPoints παραμένει διαθέσιμο για συμβατότητα/UI fallback.
      levelPoints: positions[0],
      positionLevelPoints: Object.freeze(positions),
      excludeOwnSpecialtyLanguage: Boolean(excludeOwnSpecialtyLanguage),
      scoringMode: 'ranked'
    });
  }

  const PROFILES = Object.freeze({
    // 1ΓΕ/2026, 2ΓΕ/2026 και 3ΕΑ/2025 (κλάδοι ΠΕ).
    pe: freezeProfile(2, { good: 3, very_good: 5, excellent: 7 }, true),

    // 2ΕΑ/2025 (ΕΕΠ).
    eep: freezeProfile(2, { good: 3, very_good: 5, excellent: 7 }, false),

    // 1ΕΑ/2025 (ΕΒΠ).
    ebp: freezeProfile(2, { good: 4, very_good: 6, excellent: 8 }, false),

    // 1ΓΤ/2024 και 4ΕΑ/2025 (κατηγορία ΤΕ): μοριοδοτείται μία μόνο γλώσσα.
    te: freezeProfile(1, { good: 10, very_good: 15, excellent: 20 }, false),

    // Απόσπαση εκπαιδευτικών στα ΣΔΕ 2026-2027: η ισχυρότερη γλώσσα
    // μοριοδοτείται ως 1η (1 / 1,5 / 2) και η επόμενη ως 2η
    // (0,5 / 0,75 / 1). Η γλώσσα του κλάδου δεν μοριοδοτείται.
    sde_secondment: freezeRankedProfile([
      { good: 1, very_good: 1.5, excellent: 2 },
      { good: 0.5, very_good: 0.75, excellent: 1 }
    ], true)
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
      if (!label) return { key: '', label: '' };
      const normalized = normalizeText(label);
      const knownCode = KNOWN_LANGUAGE_ALIASES[normalized] || '';
      if (knownCode) {
        return { key: knownCode, label: LABELS[knownCode], knownAlias: true };
      }
      return { key: 'other:' + normalized, label: label };
    }
    return { key: code, label: LABELS[code] || code };
  }

  /*
   * Compatibility API for existing non-profile calculators.
   * entries: [{ language, otherText, points }]
   */
  function calculatePair(entries, options) {
    const opts = options || {};
    const excluded = new Set((opts.excluded || []).filter(Boolean));
    const cap = Number.isFinite(Number(opts.cap)) ? Number(opts.cap) : Infinity;
    const byLanguage = new Map();
    const warnings = [];
    const duplicates = [];
    const excludedEntries = [];
    const missingLanguage = [];

    (entries || []).forEach(function (entry, index) {
      const points = Math.max(0, Number(entry && entry.points) || 0);
      const resolved = resolveLanguage(entry && entry.language, entry && entry.otherText);

      if (points > 0 && !resolved.key) {
        missingLanguage.push(index);
        warnings.push('Επίλεξε ποια είναι η ' + (index + 1) + 'η ξένη γλώσσα για να μοριοδοτηθεί.');
        return;
      }
      if (!resolved.key || points <= 0) return;

      if (excluded.has(resolved.key)) {
        excludedEntries.push(index);
        warnings.push(resolved.label + ': δεν μοριοδοτείται επειδή αποτελεί προσόν διορισμού για τον κλάδο.');
        return;
      }

      const previous = byLanguage.get(resolved.key);
      if (previous) {
        duplicates.push(index);
        if (points > previous.points) {
          previous.points = points;
          previous.index = index;
        }
        warnings.push(resolved.label + ': δηλώθηκε περισσότερες από μία φορές· υπολογίζεται μόνο το ανώτερο επίπεδο.');
      } else {
        byLanguage.set(resolved.key, {
          key: resolved.key,
          label: resolved.label,
          points: points,
          index: index
        });
      }
    });

    const accepted = Array.from(byLanguage.values());
    const raw = accepted.reduce(function (sum, item) { return sum + item.points; }, 0);
    return {
      raw: raw,
      points: Math.min(raw, cap),
      accepted: accepted,
      warnings: warnings,
      duplicates: duplicates,
      excludedEntries: excludedEntries,
      missingLanguage: missingLanguage
    };
  }

  function getProfile(profileName) {
    const profile = PROFILES[String(profileName || '')];
    if (!profile) {
      throw new Error('Άγνωστο προφίλ ξένων γλωσσών: ' + String(profileName || ''));
    }
    return profile;
  }

  function pointsToLevel(profile, points) {
    const keys = ['excellent', 'very_good', 'good'];
    for (let i = 0; i < keys.length; i += 1) {
      if (profile.levelPoints[keys[i]] === points) return keys[i];
    }
    return 'none';
  }

  /*
   * Canonical profile API for ASEP calculators.
   * entries: [{ language, otherText, level }]
   * options: { specialty }
   */
  function calculate(profileName, entries, options) {
    const profile = getProfile(profileName);
    const opts = options || {};
    const specialty = String(opts.specialty || '');
    const ownLanguage = profile.excludeOwnSpecialtyLanguage
      ? (OWN_LANGUAGE_BY_SPECIALTY[specialty] || '')
      : '';

    const rankedMode = profile.scoringMode === 'ranked';
    const LEVEL_RANK = { none: 0, good: 1, very_good: 2, excellent: 3 };
    const RANK_LEVEL = { 1: 'good', 2: 'very_good', 3: 'excellent' };

    const pointEntries = (entries || []).map(function (entry) {
      const level = String((entry && entry.level) || 'none');
      return {
        language: entry && entry.language,
        otherText: entry && entry.otherText,
        // Στα ranked profiles το προσωρινό points είναι μόνο βαθμίδα ταξινόμησης.
        points: rankedMode ? (LEVEL_RANK[level] || 0) : (profile.levelPoints[level] || 0)
      };
    });

    const base = calculatePair(pointEntries, {
      excluded: ownLanguage ? [ownLanguage] : []
    });

    const sorted = base.accepted.slice().sort(function (a, b) {
      if (b.points !== a.points) return b.points - a.points;
      return a.index - b.index;
    });

    const accepted = sorted.slice(0, profile.maxLanguages).map(function (item, positionIndex) {
      const level = rankedMode ? (RANK_LEVEL[item.points] || 'none') : pointsToLevel(profile, item.points);
      const actualPoints = rankedMode
        ? ((profile.positionLevelPoints[positionIndex] || {})[level] || 0)
        : item.points;
      const extra = {
        points: actualPoints,
        level: level,
        levelLabel: LEVEL_LABELS[level] || level
      };
      if (rankedMode) extra.position = positionIndex + 1;
      return Object.assign({}, item, extra);
    });

    const ignoredByLimit = sorted.slice(profile.maxLanguages);
    const warnings = base.warnings.slice();

    if (ignoredByLimit.length > 0) {
      warnings.push(
        profile.maxLanguages === 1
          ? 'Η συγκεκριμένη προκήρυξη μοριοδοτεί μία μόνο ξένη γλώσσα.'
          : 'Η συγκεκριμένη προκήρυξη μοριοδοτεί έως ' + profile.maxLanguages + ' ξένες γλώσσες.'
      );
    }

    const raw = rankedMode
      ? accepted.reduce(function (sum, item) { return sum + item.points; }, 0)
      : sorted.reduce(function (sum, item) { return sum + item.points; }, 0);
    const points = accepted.reduce(function (sum, item) { return sum + item.points; }, 0);
    const details = accepted.map(function (item) {
      const positionPrefix = rankedMode ? (item.position + 'η ξένη γλώσσα — ') : '';
      return positionPrefix + item.label + ' - ' + item.levelLabel + ': ' + item.points + ' μόρια';
    });

    return {
      profile: String(profileName),
      scoringMode: profile.scoringMode,
      maxLanguages: profile.maxLanguages,
      raw: raw,
      points: points,
      accepted: accepted,
      details: details,
      warnings: warnings,
      duplicates: base.duplicates,
      excludedEntries: base.excludedEntries,
      missingLanguage: base.missingLanguage,
      ignoredByLimit: ignoredByLimit,
      excludedLanguage: ownLanguage
    };
  }

  global.EducationLanguages = Object.freeze({
    LABELS: LABELS,
    LEVEL_LABELS: LEVEL_LABELS,
    OWN_LANGUAGE_BY_SPECIALTY: OWN_LANGUAGE_BY_SPECIALTY,
    KNOWN_LANGUAGE_ALIASES: KNOWN_LANGUAGE_ALIASES,
    PROFILES: PROFILES,
    normalizeText: normalizeText,
    resolveLanguage: resolveLanguage,
    getProfile: getProfile,
    calculatePair: calculatePair,
    calculate: calculate
  });
})(typeof window !== 'undefined' ? window : globalThis);
