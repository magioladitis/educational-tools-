(function () {
  'use strict';

  var select = document.getElementById('leaveCompareSelect');
  var viewSelect = document.getElementById('leaveCompareView');
  var dataNode = document.getElementById('leaveComparisonData');
  var panels = Array.prototype.slice.call(document.querySelectorAll('[data-leave-comparison]'));
  if (!select || !dataNode || !panels.length) return;

  var payload = {};
  try {
    payload = JSON.parse(dataNode.textContent || '{}');
  } catch (error) {
    return;
  }

  var initialId = select.value;
  var orderedIds = Object.keys(payload);
  var results = {};

  function sameValue(a, b) {
    if (a === b) return true;
    if (a === null || b === null || typeof a !== typeof b) return false;
    if (Array.isArray(a)) {
      if (!Array.isArray(b) || a.length !== b.length) return false;
      for (var i = 0; i < a.length; i += 1) {
        if (!sameValue(a[i], b[i])) return false;
      }
      return true;
    }
    if (typeof a === 'object') {
      var aKeys = Object.keys(a).sort();
      var bKeys = Object.keys(b).sort();
      if (!sameValue(aKeys, bKeys)) return false;
      for (var j = 0; j < aKeys.length; j += 1) {
        var key = aKeys[j];
        if (!sameValue(a[key], b[key])) return false;
      }
      return true;
    }
    return false;
  }

  function durationMeta(side) {
    return side && side.comparison && side.comparison.duration ? side.comparison.duration : {};
  }

  function baseDurationSignature(side) {
    var duration = durationMeta(side);
    var kind = duration.kind || 'text';
    var signature = {kind: kind};

    if (kind === 'fixed') {
      signature.unit = duration.unit || null;
      signature.base = duration.base !== undefined ? duration.base : null;
      signature.period = duration.period || null;
    } else if (kind === 'maximum') {
      signature.unit = duration.unit || null;
      signature.max = duration.max !== undefined ? duration.max : null;
      signature.period = duration.period || null;
    } else if (kind === 'range') {
      signature.unit = duration.unit || null;
      signature.min = duration.min !== undefined ? duration.min : null;
      signature.max = duration.max !== undefined ? duration.max : null;
      signature.period = duration.period || null;
    } else {
      signature.semantic_key = duration.semantic_key || side.duration_text || '';
    }
    return signature;
  }

  function extraDurationSignature(side) {
    var duration = durationMeta(side);
    return {
      extended_max: duration.extended_max !== undefined ? duration.extended_max : null,
      variant_key: duration.variant_key || null,
      variants: duration.variants || null
    };
  }

  function frequencySignature(side) {
    var duration = durationMeta(side);
    return {
      limit: duration.frequency_limit !== undefined ? duration.frequency_limit : null,
      period: duration.frequency_period || null
    };
  }

  function scopeSignature(side) {
    var scope = side && side.comparison ? side.comparison.scope : null;
    return scope && scope.key ? scope.key : null;
  }

  function hasExtras(side) {
    var duration = durationMeta(side);
    return duration.extended_max !== undefined || duration.variant_key !== undefined || duration.variants !== undefined;
  }

  function hasFrequency(side) {
    var duration = durationMeta(side);
    return duration.frequency_limit !== undefined || duration.frequency_period !== undefined;
  }

  function hasScope(side) {
    return !!(side && side.comparison && side.comparison.scope && side.comparison.scope.key);
  }

  function comparePair(pair) {
    var permanent = pair.permanent || {};
    var substitute = pair.substitute || {};
    var sameBase = sameValue(baseDurationSignature(permanent), baseDurationSignature(substitute));
    var extrasRelevant = hasExtras(permanent) || hasExtras(substitute);
    var frequencyRelevant = hasFrequency(permanent) || hasFrequency(substitute);
    var scopeRelevant = hasScope(permanent) || hasScope(substitute);
    var sameExtras = !extrasRelevant || sameValue(extraDurationSignature(permanent), extraDurationSignature(substitute));
    var sameFrequency = !frequencyRelevant || sameValue(frequencySignature(permanent), frequencySignature(substitute));
    var sameScope = !scopeRelevant || scopeSignature(permanent) === scopeSignature(substitute);
    var samePay = permanent.pay === substitute.pay;
    var sameService = permanent.service === substitute.service;
    var proportionalRelevant = !!permanent.proportional || !!substitute.proportional;
    var sameProportional = !!permanent.proportional === !!substitute.proportional;
    var allSame = sameBase && sameExtras && sameFrequency && sameScope && samePay && sameService && sameProportional;
    var sameCore = sameBase && sameExtras && sameFrequency && sameScope && samePay && sameService;

    return {
      sameBase: sameBase,
      extrasRelevant: extrasRelevant,
      sameExtras: sameExtras,
      frequencyRelevant: frequencyRelevant,
      sameFrequency: sameFrequency,
      scopeRelevant: scopeRelevant,
      sameScope: sameScope,
      samePay: samePay,
      sameService: sameService,
      proportionalRelevant: proportionalRelevant,
      sameProportional: sameProportional,
      allSame: allSame,
      sameCore: sameCore,
      hasDifference: !allSame
    };
  }

  function summaryItem(label, same, sameText, differentText) {
    var span = document.createElement('span');
    span.className = 'leave-compare-summary__item ' + (same ? 'is-same' : 'is-different');
    span.textContent = label + ': ' + (same ? sameText : differentText);
    return span;
  }

  function escapeHtml(value) {
    return String(value)
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#039;');
  }

  function renderPanelSummary(id) {
    var panel = document.querySelector('[data-leave-comparison="' + id.replace(/"/g, '\\"') + '"]');
    var result = results[id];
    if (!panel || !result) return;

    var summary = panel.querySelector('[data-leave-compare-summary]');
    var overall = panel.querySelector('[data-leave-compare-overall]');
    if (summary) {
      summary.innerHTML = '';
      summary.appendChild(summaryItem('Βασική διάρκεια', result.sameBase, 'ίδια', 'διαφέρει'));
      if (result.extrasRelevant) summary.appendChild(summaryItem('Ειδικές περιπτώσεις', result.sameExtras, 'ίδιες', 'διαφέρουν'));
      if (result.frequencyRelevant) summary.appendChild(summaryItem('Συχνότητα', result.sameFrequency, 'ίδια', 'διαφέρει'));
      if (result.scopeRelevant) summary.appendChild(summaryItem('Πεδίο δικαιώματος', result.sameScope, 'ίδιο', 'διαφέρει'));
      summary.appendChild(summaryItem('Αποδοχές', result.samePay, 'ίδιες', 'διαφέρουν'));
      summary.appendChild(summaryItem('Υπηρεσία', result.sameService, 'ίδια', 'διαφέρει'));
      if (result.proportionalRelevant) summary.appendChild(summaryItem('Αναλογικότητα', result.sameProportional, 'ίδια', 'διαφέρει'));
    }

    ['permanent', 'substitute'].forEach(function (audience) {
      var side = payload[id] && payload[id][audience] ? payload[id][audience] : {};
      var scope = side.comparison && side.comparison.scope ? side.comparison.scope : null;
      var note = panel.querySelector('[data-leave-compare-scope="' + audience + '"]');
      if (!note) return;
      if (scope && scope.label) {
        note.hidden = false;
        note.innerHTML = '<strong>Πεδίο δικαιώματος:</strong> ' + escapeHtml(scope.label);
      } else {
        note.hidden = true;
        note.textContent = '';
      }
    });

    if (overall) {
      overall.className = 'leave-compare-overall ' + (result.allSame ? 'is-same' : (result.sameBase ? 'is-partial' : 'is-different'));
      if (result.allSame) {
        overall.textContent = 'Ουσιαστικά ίδιο δικαίωμα';
      } else if (result.sameCore && !result.sameProportional) {
        overall.textContent = 'Ίδιο βασικό δικαίωμα · διαφορετική αναλογικότητα εφαρμογής';
      } else if (result.sameBase) {
        overall.textContent = 'Ίδια βασική διάρκεια · υπάρχουν διαφορές στους όρους εφαρμογής';
      } else {
        overall.textContent = 'Υπάρχουν ουσιαστικές διαφορές';
      }
    }

    panel.setAttribute('data-has-difference', result.hasDifference ? '1' : '0');
    panel.setAttribute('data-same-base-duration', result.sameBase ? '1' : '0');
  }

  orderedIds.forEach(function (id) {
    results[id] = comparePair(payload[id] || {});
    renderPanelSummary(id);
  });

  function show(id, updateUrl) {
    var found = false;
    panels.forEach(function (panel) {
      var active = panel.getAttribute('data-leave-comparison') === id;
      panel.hidden = !active;
      if (active) found = true;
    });
    if (!found) return;
    select.value = id;
    if (updateUrl && window.history && window.history.replaceState) {
      var url = new URL(window.location.href);
      url.searchParams.set('leave', id);
      window.history.replaceState({}, '', url.toString());
    }
  }

  function idsForView(view) {
    return orderedIds.filter(function (id) {
      if (view === 'differences') return results[id] && results[id].hasDifference;
      if (view === 'same-base') return results[id] && results[id].sameBase;
      return true;
    });
  }

  function rebuildLeaveOptions(view, updateUrl) {
    var allowed = idsForView(view);
    var current = select.value;
    select.innerHTML = '';

    allowed.forEach(function (id) {
      var option = document.createElement('option');
      option.value = id;
      option.textContent = payload[id].title || id;
      select.appendChild(option);
    });

    if (!allowed.length) {
      select.disabled = true;
      panels.forEach(function (panel) { panel.hidden = true; });
      return;
    }

    select.disabled = false;
    var next = allowed.indexOf(current) !== -1 ? current : allowed[0];
    select.value = next;
    show(next, updateUrl && next !== current);
  }

  select.addEventListener('change', function () {
    show(select.value, true);
    var active = document.querySelector('[data-leave-comparison="' + select.value.replace(/"/g, '\\"') + '"]');
    if (active) {
      var reduceMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
      active.scrollIntoView({behavior: reduceMotion ? 'auto' : 'smooth', block: 'start'});
    }
  });

  if (viewSelect) {
    viewSelect.addEventListener('change', function () {
      rebuildLeaveOptions(viewSelect.value, true);
    });
  }

  rebuildLeaveOptions('all', false);
  if (orderedIds.indexOf(initialId) !== -1) {
    select.value = initialId;
    show(initialId, false);
  }
})();
