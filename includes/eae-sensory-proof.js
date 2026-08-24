/* EAE sensory priority proof checks — presentation/state only, no scoring. */
(function (global) {
  'use strict';

  function resolveRoot(target) {
    if (target && target.nodeType === 1) return target;
    if (typeof target === 'string' && target) {
      var byId = document.getElementById(target);
      if (byId && byId.matches('[data-component="eae-sensory-priority"]')) return byId;
    }
    return document.querySelector('[data-component="eae-sensory-priority"]');
  }

  function emptyMessage(panel) {
    return panel.getAttribute('data-kind') === 'braille'
      ? 'Επίλεξε τον φορέα ή τον τρόπο απόδειξης που διαθέτεις.'
      : 'Επίλεξε τον φορέα έκδοσης του πιστοποιητικού ή της βεβαίωσης.';
  }

  function labelFor(kind) {
    return kind === 'braille'
      ? 'Braille — προτεραιότητα για μαθητές με προβλήματα όρασης'
      : 'ΕΝΓ — προτεραιότητα για κωφούς/βαρήκοους μαθητές';
  }

  function shortName(kind) {
    return kind === 'braille' ? 'Braille' : 'ΕΝΓ';
  }

  function sync(panel) {
    var input = document.getElementById(panel.getAttribute('data-input-id') || '');
    var method = panel.querySelector('[data-eae-sensory-method]');
    var status = panel.querySelector('[data-eae-sensory-status]');
    if (!input || !method || !status) return;

    var active = !!input.checked && !input.disabled;
    panel.classList.toggle('hidden', !active);
    panel.setAttribute('aria-hidden', active ? 'false' : 'true');
    if (!active) {
      method.value = '';
      status.className = 'eae-sensory-status neutral';
      status.textContent = emptyMessage(panel);
      return;
    }

    if (!method.value) {
      status.className = 'eae-sensory-status neutral';
      status.textContent = emptyMessage(panel);
    } else if (method.value === 'other') {
      status.className = 'eae-sensory-status warning';
      status.textContent = '⚠ Ο φορέας δεν περιλαμβάνεται ρητά στους αποδεκτούς τρόπους απόδειξης της προκήρυξης. Απαιτείται επίσημος έλεγχος.';
    } else {
      status.className = 'eae-sensory-status success';
      status.textContent = '✓ Ο επιλεγμένος φορέας ή τρόπος απόδειξης αναφέρεται ρητά στην προκήρυξη.';
    }
  }

  function syncAll(target) {
    var root = resolveRoot(target);
    var scope = root || document;
    scope.querySelectorAll('[data-eae-sensory-panel]').forEach(sync);
  }

  function stateForPanel(panel) {
    var kind = panel.getAttribute('data-kind') || '';
    var input = document.getElementById(panel.getAttribute('data-input-id') || '');
    var method = panel.querySelector('[data-eae-sensory-method]');
    var selected = !!(input && input.checked && !input.disabled);
    var methodValue = method ? method.value : '';
    var methodText = '';
    if (method && method.selectedIndex >= 0 && methodValue) {
      methodText = method.options[method.selectedIndex].textContent.trim();
    }
    return {
      kind: kind,
      available: !!input,
      selected: selected,
      method: methodValue,
      methodText: methodText,
      proofSelected: selected && !!methodValue,
      proofRecognized: selected && !!methodValue && methodValue !== 'other',
      label: labelFor(kind),
      shortName: shortName(kind)
    };
  }

  function getState(target) {
    var root = resolveRoot(target);
    var result = { eng: null, braille: null, active: [], labels: [] };
    if (!root) return result;
    root.querySelectorAll('[data-eae-sensory-panel]').forEach(function (panel) {
      var state = stateForPanel(panel);
      if (state.kind === 'braille') result.braille = state;
      else if (state.kind === 'eng') result.eng = state;
      if (state.selected) {
        result.active.push(state);
        result.labels.push(state.label);
      }
    });
    return result;
  }

  function priorityLabels(target) {
    return getState(target).labels.slice();
  }

  function priorityText(target, separator) {
    return priorityLabels(target).join(separator || ' · ');
  }

  function summary(target) {
    var state = getState(target);
    var available = [state.eng, state.braille].filter(Boolean);
    if (!available.length) return '';
    return available.map(function (item) {
      var text = item.shortName + ': ' + (item.selected ? 'δηλώθηκε ειδική προτεραιότητα' : 'δεν δηλώθηκε');
      if (item.selected) {
        if (item.methodText) text += ' — αποδεικτικό: ' + item.methodText;
        else text += ' — αποδεικτικό: δεν επιλέχθηκε';
      }
      return text;
    }).join('\n');
  }

  function reset(target) {
    var root = resolveRoot(target);
    if (!root) return;
    root.querySelectorAll('[data-eae-sensory-panel]').forEach(function (panel) {
      var input = document.getElementById(panel.getAttribute('data-input-id') || '');
      var method = panel.querySelector('[data-eae-sensory-method]');
      if (input) input.checked = false;
      if (method) method.value = '';
      sync(panel);
    });
  }

  function init() {
    syncAll();
    document.addEventListener('input', function () { window.setTimeout(syncAll, 0); });
    document.addEventListener('change', function () { window.setTimeout(syncAll, 0); });
    document.addEventListener('click', function (event) {
      if (event.target && (event.target.id === 'resetBtn' || event.target.classList.contains('reset-btn') || event.target.classList.contains('reset-button'))) {
        window.setTimeout(syncAll, 0);
      }
    });
  }

  global.EaeSensoryProof = {
    syncAll: syncAll,
    getState: getState,
    priorityLabels: priorityLabels,
    priorityText: priorityText,
    summary: summary,
    reset: reset
  };
  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init);
  else init();
})(window);
