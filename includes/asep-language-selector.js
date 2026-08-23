/*
 * Shared foreign-language UI/controller.
 * Business rules are read exclusively from EducationLanguages.PROFILES.
 */
(function (global) {
  'use strict';

  const LANGUAGE_ORDER = ['en', 'fr', 'de', 'it', 'es', 'other'];
  const LEVEL_ORDER = ['none', 'good', 'very_good', 'excellent'];

  function byId(id) {
    return id ? document.getElementById(id) : null;
  }

  function formatPoints(value) {
    const n = Math.round((Number(value) || 0) * 100) / 100;
    return Number.isInteger(n) ? String(n) : n.toFixed(2).replace('.', ',');
  }

  function getContainer(ref) {
    return typeof ref === 'string' ? byId(ref) : ref;
  }

  function profileOf(container) {
    return EducationLanguages.getProfile(container.dataset.profile || 'pe');
  }

  function languageOptions() {
    return '<option value="">— Επιλογή γλώσσας —</option>' +
      LANGUAGE_ORDER.map(function (key) {
        return '<option value="' + key + '">' + EducationLanguages.LABELS[key] + '</option>';
      }).join('');
  }

  function levelOptions(profile) {
    return LEVEL_ORDER.map(function (key) {
      if (key === 'none') {
        return '<option value="none">Καμία / χωρίς μόρια</option>';
      }
      if (profile.scoringMode === 'ranked') {
        return '<option value="' + key + '">' + EducationLanguages.LEVEL_LABELS[key] + '</option>';
      }
      const points = profile.levelPoints[key] || 0;
      return '<option value="' + key + '">' +
        EducationLanguages.LEVEL_LABELS[key] + ' — ' + formatPoints(points) +
        (points === 1 ? ' μόριο' : ' μόρια') + '</option>';
    }).join('');
  }

  function ruleIntro(profile) {
    if (profile.scoringMode === 'ranked') {
      const first = profile.positionLevelPoints[0];
      const second = profile.positionLevelPoints[1] || null;
      let text = '<strong>Μοριοδοτούνται έως δύο ξένες γλώσσες με κατάταξη.</strong> ' +
        'Η ισχυρότερη δηλωμένη γλώσσα υπολογίζεται ως 1η: ' +
        'Άριστη ' + formatPoints(first.excellent) + ' · Πολύ καλή ' + formatPoints(first.very_good) +
        ' · Καλή ' + formatPoints(first.good) + '.';
      if (second) {
        text += ' Η επόμενη υπολογίζεται ως 2η: Άριστη ' + formatPoints(second.excellent) +
          ' · Πολύ καλή ' + formatPoints(second.very_good) + ' · Καλή ' + formatPoints(second.good) + '.';
      }
      if (profile.excludeOwnSpecialtyLanguage) {
        text += ' Η γλώσσα του κλάδου δεν μοριοδοτείται.';
      }
      return text;
    }

    const scoreText = [
      'Άριστη: ' + formatPoints(profile.levelPoints.excellent),
      'Πολύ καλή: ' + formatPoints(profile.levelPoints.very_good),
      'Καλή: ' + formatPoints(profile.levelPoints.good)
    ].join(' · ');

    if (profile.maxLanguages === 1) {
      return '<strong>Μοριοδοτείται μία μόνο ξένη γλώσσα.</strong> ' + scoreText +
        '. Αν υπάρχουν περισσότεροι τίτλοι της ίδιας γλώσσας, λαμβάνεται μόνο ο ανώτερος.';
    }

    let text = '<strong>Μοριοδοτούνται έως δύο ξένες γλώσσες.</strong> ' + scoreText +
      '. Η ίδια γλώσσα δεν μπορεί να επιλεγεί δύο φορές.';
    if (profile.excludeOwnSpecialtyLanguage) {
      text += ' Στους ΠΕ05, ΠΕ06, ΠΕ07, ΠΕ34 και ΠΕ40 η γλώσσα του κλάδου δεν είναι διαθέσιμη για μοριοδότηση.';
    }
    return text;
  }

  function render(container) {
    if (!container || container.dataset.languageReady === '1') return;
    const profile = profileOf(container);
    const title = container.querySelector('[data-language-title]');
    const intro = container.querySelector('[data-language-intro]');
    const fields = container.querySelector('[data-language-fields]');

    if (title) title.textContent = profile.maxLanguages === 1 ? 'Ξένη γλώσσα' : 'Ξένες γλώσσες';
    if (intro) intro.innerHTML = ruleIntro(profile);

    const fieldClass = container.dataset.fieldClass || 'field';
    const rows = [];
    for (let i = 0; i < profile.maxLanguages; i += 1) {
      const n = i + 1;
      const languageLabel = profile.maxLanguages === 1 ? 'Ξένη γλώσσα' : n + 'η ξένη γλώσσα';
      const levelLabel = profile.maxLanguages === 1 ? 'Επίπεδο' : 'Επίπεδο ' + n + 'ης γλώσσας';
      rows.push(
        '<div class="field-grid asep-language-row" data-language-row="' + i + '">' +
          '<div class="' + fieldClass + '">' +
            '<label>' + languageLabel + '</label>' +
            '<select data-language-name>' + languageOptions() + '</select>' +
          '</div>' +
          '<div class="' + fieldClass + '">' +
            '<label>' + levelLabel + '</label>' +
            '<select data-language-level>' + levelOptions(profile) + '</select>' +
          '</div>' +
          '<div class="' + fieldClass + ' hidden" data-language-other-wrap>' +
            '<label>Ονομασία άλλης ξένης γλώσσας</label>' +
            '<input type="text" data-language-other placeholder="π.χ. Πορτογαλική">' +
          '</div>' +
        '</div>'
      );
    }
    fields.innerHTML = rows.join('');
    container.dataset.languageReady = '1';

    container.addEventListener('change', function (event) {
      const target = event.target;
      let keepStatus = false;
      if (target && target.matches('[data-language-other]')) {
        keepStatus = preventDuplicateOther(container, target);
      }
      sync(container, { keepStatus: keepStatus });
      emitChange(container);
    });
    container.addEventListener('input', function () {
      sync(container, { keepStatus: true });
      emitChange(container);
    });

    const specialty = byId(container.dataset.specialtyId || '');
    if (specialty) {
      specialty.addEventListener('change', function () {
        sync(container);
        emitChange(container);
      });
    }

    sync(container, { silent: true });
  }

  function rows(container) {
    return Array.from(container.querySelectorAll('[data-language-row]'));
  }

  function selectedEntries(containerRef) {
    const container = getContainer(containerRef);
    if (!container) return [];
    return rows(container).map(function (row) {
      return {
        language: row.querySelector('[data-language-name]').value,
        level: row.querySelector('[data-language-level]').value,
        otherText: row.querySelector('[data-language-other]').value
      };
    });
  }

  function specialtyValue(container) {
    const specialty = byId(container.dataset.specialtyId || '');
    return specialty ? specialty.value : '';
  }

  function ownExcludedLanguage(container) {
    const profile = profileOf(container);
    if (!profile.excludeOwnSpecialtyLanguage) return '';
    return EducationLanguages.OWN_LANGUAGE_BY_SPECIALTY[specialtyValue(container)] || '';
  }

  function preventDuplicateOther(container, changedInput) {
    const normalized = EducationLanguages.normalizeText(changedInput.value);
    if (!normalized) return false;

    // "Άλλη" is only for a language not already present in the standard list.
    // This also prevents bypassing a PE branch-language exclusion by typing
    // e.g. "Αγγλικά" instead of selecting Αγγλική.
    const resolved = EducationLanguages.resolveLanguage('other', changedInput.value);
    if (resolved.key && !resolved.key.startsWith('other:')) {
      changedInput.value = '';
      setStatus(
        container,
        resolved.label + ' υπάρχει ήδη στις βασικές επιλογές. Επίλεξέ την από τη λίστα.',
        true
      );
      return true;
    }

    const duplicates = rows(container).filter(function (row) {
      const select = row.querySelector('[data-language-name]');
      const other = row.querySelector('[data-language-other]');
      return select.value === 'other' && other !== changedInput &&
        EducationLanguages.normalizeText(other.value) === normalized;
    });

    if (duplicates.length) {
      changedInput.value = '';
      setStatus(container, 'Η ίδια ξένη γλώσσα έχει ήδη δηλωθεί. Επίλεξε διαφορετική γλώσσα.', true);
      return true;
    }
    return false;
  }

  function setStatus(container, text, forceShow) {
    const status = container.querySelector('[data-language-status]');
    if (!status) return;
    status.textContent = text || '';
    status.classList.toggle('hidden', !(forceShow || text));
  }

  function setContext(container, excludedLanguage) {
    const context = container.querySelector('[data-language-context]');
    if (!context) return;
    const specialty = specialtyValue(container);
    if (excludedLanguage && specialty) {
      context.textContent = 'Στον ' + specialty + ' η ' + EducationLanguages.LABELS[excludedLanguage] +
        ' δεν μοριοδοτείται, επειδή αποτελεί τη γλώσσα του κλάδου, και έχει αφαιρεθεί από τις διαθέσιμες επιλογές.';
      context.classList.remove('hidden');
    } else {
      context.textContent = '';
      context.classList.add('hidden');
    }
  }

  function sync(containerRef, options) {
    const container = getContainer(containerRef);
    if (!container) return null;
    if (container.dataset.languageReady !== '1') render(container);

    const opts = options || {};
    const excludedLanguage = ownExcludedLanguage(container);
    const allRows = rows(container);

    // If specialty changes after the user selected its own language, remove it.
    allRows.forEach(function (row) {
      const select = row.querySelector('[data-language-name]');
      const level = row.querySelector('[data-language-level]');
      const otherWrap = row.querySelector('[data-language-other-wrap]');
      const other = row.querySelector('[data-language-other]');

      if (excludedLanguage && select.value === excludedLanguage) {
        select.value = '';
        level.value = 'none';
        other.value = '';
      }
      otherWrap.classList.toggle('hidden', select.value !== 'other');
      if (select.value !== 'other') other.value = '';
    });

    // Named languages cannot be selected in more than one row.
    allRows.forEach(function (row, rowIndex) {
      const select = row.querySelector('[data-language-name]');
      const selectedElsewhere = new Set();
      allRows.forEach(function (otherRow, otherIndex) {
        if (otherIndex === rowIndex) return;
        const value = otherRow.querySelector('[data-language-name]').value;
        if (value && value !== 'other') selectedElsewhere.add(value);
      });

      Array.from(select.options).forEach(function (option) {
        if (!option.value || option.value === 'other') {
          option.disabled = false;
          return;
        }
        option.disabled = option.value === excludedLanguage || selectedElsewhere.has(option.value);
      });
    });

    setContext(container, excludedLanguage);
    const result = calculate(container);
    if (!opts.keepStatus) {
      setStatus(container, result.warnings.join(' '), false);
    }
    return result;
  }

  function calculate(containerRef) {
    const container = getContainer(containerRef);
    if (!container) {
      return { points: 0, raw: 0, accepted: [], details: [], warnings: [] };
    }
    return EducationLanguages.calculate(
      container.dataset.profile || 'pe',
      selectedEntries(container),
      { specialty: specialtyValue(container) }
    );
  }

  function reset(containerRef, options) {
    const container = getContainer(containerRef);
    if (!container) return;
    rows(container).forEach(function (row) {
      row.querySelector('[data-language-name]').value = '';
      row.querySelector('[data-language-level]').value = 'none';
      row.querySelector('[data-language-other]').value = '';
    });
    sync(container, { silent: true });
    if (!(options && options.silent)) emitChange(container);
  }

  function summary(containerRef, formatter) {
    const result = calculate(containerRef);
    if (!result.accepted.length) return '';
    const fmt = typeof formatter === 'function' ? formatter : formatPoints;
    const prefix = result.maxLanguages === 1 ? 'Ξένη γλώσσα: ' : 'Ξένες γλώσσες: ';
    return prefix + result.accepted.map(function (item) {
      return item.label + ' (' + item.levelLabel + ') ' + fmt(item.points) + ' μόρια';
    }).join(' · ');
  }

  function emitChange(container) {
    container.dispatchEvent(new CustomEvent('asep-language-change', {
      bubbles: true,
      detail: calculate(container)
    }));
  }

  function initAll() {
    document.querySelectorAll('[data-component="asep-language-selector"]').forEach(render);
  }

  global.AsepLanguageSelector = Object.freeze({
    initAll: initAll,
    sync: sync,
    calculate: calculate,
    getState: calculate,
    reset: reset,
    summary: summary,
    readEntries: selectedEntries
  });

  // Components are above the script on all calculator pages, so initialize now.
  initAll();
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initAll);
  }
})(typeof window !== 'undefined' ? window : globalThis);
