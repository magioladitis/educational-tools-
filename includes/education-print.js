/*
 * Educational Tools — shared print report helper.
 *
 * Creates an A4-friendly snapshot of the user's current inputs and results.
 * Mature tools with their own dedicated print reports are intentionally NOT
 * registered here (salary, staffing simulator, grade converter).
 */
(function (global) {
  'use strict';

  var PAGE_CONFIG = {
    'posa-paravola.php': { results: ['#result'], actionAnchor: '#result' },
    'ypologismos-morion.php': {},
    'ypologismos-morion-1gt-2024.php': {},
    'ypologismos-morion-onaseia.php': {},
    'ypologismos-morion-apospasis-dimos.php': {},
    'ypologismos-morion-apospasis.php': {},
    'ypologismos-morion-1ea-2025.php': {},
    'ypologismos-morion-2ea-2025.php': {},
    'ypologismos-morion-3ea-2025.php': {},
    'ypologismos-morion-4ea-2025.php': {},
    'ypologismos-morion-5ea-2022.php': {},
    'ypologismos-morion-apospasis-psifiako-frontistirio.php': {},
    'ypologismos-morion-apospasis-sde.php': {},
    'ypologismos-morion-apospasis-exoteriko.php': {},
    'ypologismos-morion-apospasis-evropaika-scholeia.php': {},
    'ypologismos-morion-diefthynton-ypodiefthynton-sde.php': {},
    'ypologismos-morion-mitroo-sde.php': {},
    'ypologismos-morion-sivitanidios-saek.php': {},
    'ypologismos-didaktikou-orariou.php': {},
    'ypologismos-morion-metathesis.php': {},
    'ypologismos-morion-topothetisis-neodioriston.php': {},
    'anatheseis-mathimaton.php': {
      results: ['#fullResultsStatus', '#assignmentResults'],
      actionAnchor: '#fullAssignmentsCard'
    },
    'orologio-programma-mathimaton.php': {
      results: ['#programSummary', '#timetableResults'],
      actionAnchor: '#timetableResults'
    }
  };

  function basename() {
    var path = String(global.location && global.location.pathname || '');
    var name = path.slice(path.lastIndexOf('/') + 1);
    return name || 'ergaleia.php';
  }

  function text(value) {
    return String(value == null ? '' : value).replace(/\s+/g, ' ').trim();
  }

  function visible(el) {
    if (!el || el.hidden || el.getAttribute('aria-hidden') === 'true') return false;
    var p = el;
    while (p && p.nodeType === 1) {
      if (p.hidden) return false;
      if (p.style && (p.style.display === 'none' || p.style.visibility === 'hidden')) return false;
      p = p.parentElement;
    }
    return true;
  }

  function labelFor(control) {
    if (!control) return '';
    var label = '';
    if (control.id) {
      var explicit = document.querySelector('label[for="' + cssEscape(control.id) + '"]');
      if (explicit) label = text(explicit.textContent);
    }
    if (!label) {
      var parentLabel = control.closest ? control.closest('label') : null;
      if (parentLabel) label = text(parentLabel.textContent);
    }
    // Dynamic/shared components often render <label> and <select/input> as
    // siblings inside the same .field instead of linking them with for/id.
    if (!label && control.closest) {
      var field = control.closest('.field, .form-field, .input-group');
      if (field) {
        var nearbyLabel = field.querySelector('label');
        if (nearbyLabel) label = text(nearbyLabel.textContent);
      }
    }
    if (!label && control.getAttribute) label = text(control.getAttribute('aria-label'));
    if (!label) label = text(control.name || control.id || '');
    return label.replace(/\s*\([^)]*προαιρετικ[^)]*\)\s*/i, ' ').trim();
  }

  function cssEscape(value) {
    if (global.CSS && typeof global.CSS.escape === 'function') return global.CSS.escape(value);
    return String(value).replace(/([ #;?%&,.+*~\\':"!^$\[\]()=>|\/@])/g, '\\$1');
  }

  function valueFor(control) {
    var type = String(control.type || '').toLowerCase();
    if (type === 'checkbox') return control.checked ? 'Ναι' : '';
    if (type === 'radio') {
      if (!control.checked) return '';
      var radioLabel = labelFor(control);
      return radioLabel || text(control.value) || 'Ναι';
    }
    if (control.tagName === 'SELECT') {
      if (!control.value) return '';
      var option = control.options && control.selectedIndex >= 0 ? control.options[control.selectedIndex] : null;
      return option ? text(option.textContent) : text(control.value);
    }
    var raw = text(control.value);
    if (!raw) return '';
    if (type === 'number' && Number(raw.replace(',', '.')) === 0) return '';
    return raw;
  }

  function selectedOptionText(select) {
    if (!select || !select.options || select.selectedIndex < 0) return '';
    return text(select.options[select.selectedIndex].textContent);
  }

  function collectLanguageFields(root, rows, seen) {
    var components = root.querySelectorAll('[data-component="asep-language-selector"]');
    Array.prototype.forEach.call(components, function (component) {
      if (!visible(component)) return;
      var languageRows = component.querySelectorAll('[data-language-row]');
      Array.prototype.forEach.call(languageRows, function (languageRow, index) {
        var language = languageRow.querySelector('[data-language-name]');
        var level = languageRow.querySelector('[data-language-level]');
        if (!language || !language.value || !visible(language)) return;

        var languageText = selectedOptionText(language);
        if (language.value === 'other') {
          var other = languageRow.querySelector('[data-language-other]');
          var otherText = other ? text(other.value) : '';
          if (otherText) languageText = otherText;
        }
        if (!languageText) return;

        var languageLabelNode = language.closest('.field') ? language.closest('.field').querySelector('label') : null;
        var rowLabel = languageLabelNode ? text(languageLabelNode.textContent) : '';
        if (!rowLabel) {
          rowLabel = languageRows.length > 1 ? ((index + 1) + 'η ξένη γλώσσα') : 'Ξένη γλώσσα';
        }

        var levelText = level && level.value ? selectedOptionText(level) : '';
        var combined = languageText;
        if (levelText && levelText !== 'Καμία / χωρίς μόρια') combined += ' · ' + levelText;
        else if (levelText) combined += ' · ' + levelText;

        var key = rowLabel + '\u0000' + combined;
        if (seen[key]) return;
        seen[key] = true;
        rows.push({ label: rowLabel, value: combined });
      });
    });
  }

  function collectFields() {
    var root = document.querySelector('main, .page-shell, .app-box, .container') || document.body;
    var controls = root.querySelectorAll('input, select, textarea');
    var rows = [];
    var seen = {};

    // Treat a foreign-language choice as one semantic datum instead of two
    // unrelated select boxes (language + proficiency level).
    collectLanguageFields(root, rows, seen);

    Array.prototype.forEach.call(controls, function (control) {
      var type = String(control.type || '').toLowerCase();
      if (['hidden', 'button', 'submit', 'reset', 'file'].indexOf(type) !== -1) return;
      if (control.disabled || !visible(control)) return;
      if (control.closest && control.closest('[data-component="asep-language-selector"]')) return;
      var value = valueFor(control);
      if (!value) return;
      var label = labelFor(control);
      if (!label) return;
      var key = label + '\u0000' + value;
      if (seen[key]) return;
      seen[key] = true;
      rows.push({ label: label, value: value });
    });
    return rows;
  }

  function cleanClone(node) {
    var clone = node.cloneNode(true);
    var remove = clone.querySelectorAll('button, input, select, textarea, .actions, .button-row, .edu-print-actions, script, style, [hidden], [aria-hidden="true"]');
    Array.prototype.forEach.call(remove, function (el) { el.remove(); });
    clone.removeAttribute('aria-live');
    clone.removeAttribute('role');
    clone.style.display = '';
    return clone;
  }

  function resultNodes(config) {
    var selectors = config.results && config.results.length ? config.results : ['.results'];
    var out = [];
    var seen = [];
    selectors.forEach(function (selector) {
      var matches = document.querySelectorAll(selector);
      Array.prototype.forEach.call(matches, function (el) {
        if (!visible(el)) return;
        if (!text(el.textContent)) return;
        if (seen.indexOf(el) !== -1) return;
        seen.push(el);
        out.push(el);
      });
    });
    if (!out.length) {
      ['#result', '#results'].some(function (selector) {
        var el = document.querySelector(selector);
        if (el && visible(el) && text(el.textContent)) {
          out.push(el);
          return true;
        }
        return false;
      });
    }
    return out;
  }

  function buildSheet(config) {
    var old = document.getElementById('eduGenericPrintSheet');
    if (old) old.remove();

    var sheet = document.createElement('section');
    sheet.id = 'eduGenericPrintSheet';
    sheet.className = 'edu-generic-print-sheet';
    sheet.setAttribute('aria-hidden', 'true');

    var h1 = document.querySelector('h1');
    var title = text(config.title || (h1 && h1.textContent) || document.title || 'Αποτέλεσμα εργαλείου');
    var now = new Date();

    var head = document.createElement('div');
    head.className = 'edu-print-report-head';
    head.innerHTML = '<div><div class="edu-print-report-brand">Εργαλειοθήκη Εκπαιδευτικού</div>' +
      '<h1></h1></div><div class="edu-print-report-meta"></div>';
    head.querySelector('h1').textContent = title;
    head.querySelector('.edu-print-report-meta').textContent = 'Εκτύπωση: ' +
      now.toLocaleDateString('el-GR') + ' ' + now.toLocaleTimeString('el-GR', { hour: '2-digit', minute: '2-digit' });
    sheet.appendChild(head);

    var fields = collectFields();
    if (fields.length) {
      var section = document.createElement('section');
      section.className = 'edu-print-report-section';
      var heading = document.createElement('h2');
      heading.textContent = 'Δηλωμένα στοιχεία';
      section.appendChild(heading);
      var table = document.createElement('table');
      table.className = 'edu-print-report-table edu-print-report-fields';
      var tbody = document.createElement('tbody');
      fields.forEach(function (row) {
        var tr = document.createElement('tr');
        var th = document.createElement('th');
        var td = document.createElement('td');
        th.textContent = row.label;
        td.textContent = row.value;
        tr.appendChild(th); tr.appendChild(td); tbody.appendChild(tr);
      });
      table.appendChild(tbody); section.appendChild(table); sheet.appendChild(section);
    }

    var results = resultNodes(config);
    if (results.length) {
      var resultSection = document.createElement('section');
      resultSection.className = 'edu-print-report-section edu-print-report-results';
      var resultHeading = document.createElement('h2');
      resultHeading.textContent = 'Αποτέλεσμα';
      resultSection.appendChild(resultHeading);
      results.forEach(function (node) {
        var cloned = cleanClone(node);
        cloned.classList.add('edu-print-report-result-block');
        resultSection.appendChild(cloned);
      });
      sheet.appendChild(resultSection);
    }

    var source = document.querySelector('.edu-source-card');
    if (source && visible(source)) {
      var sourceSection = document.createElement('section');
      sourceSection.className = 'edu-print-report-section edu-print-report-sources';
      var sourceClone = cleanClone(source);
      sourceClone.querySelectorAll('details').forEach(function (details) { details.setAttribute('open', ''); });
      sourceSection.appendChild(sourceClone);
      sheet.appendChild(sourceSection);
    }

    var foot = document.createElement('p');
    foot.className = 'edu-print-report-note';
    foot.textContent = 'Η εκτύπωση αποτυπώνει τα στοιχεία και το αποτέλεσμα που εμφανίζονται στο εργαλείο τη συγκεκριμένη στιγμή. Είναι ενημερωτικό βοήθημα και δεν αντικαθιστά επίσημη διοικητική, μισθολογική ή υπηρεσιακή πράξη.';
    sheet.appendChild(foot);

    document.body.appendChild(sheet);
    return sheet;
  }

  function print(config) {
    buildSheet(config || {});
    document.body.classList.add('edu-generic-printing');
    var cleaned = false;
    function cleanup() {
      if (cleaned) return;
      cleaned = true;
      document.body.classList.remove('edu-generic-printing');
      var sheet = document.getElementById('eduGenericPrintSheet');
      if (sheet) sheet.remove();
    }
    global.addEventListener('afterprint', cleanup, { once: true });
    setTimeout(function () { global.print(); }, 0);
    setTimeout(function () {
      if (!document.body.classList.contains('edu-generic-printing')) return;
      if (document.hasFocus && document.hasFocus()) cleanup();
    }, 3000);
  }

  function findActionRow(config) {
    var rows = document.querySelectorAll('.actions, .button-row, .action-row');
    for (var i = 0; i < rows.length; i += 1) {
      if (!visible(rows[i])) continue;
      var txt = text(rows[i].textContent);
      if (/Καθαρισμός|Μηδενισμός|Αντιγραφή|Υπολογισμός|Έλεγχος/.test(txt)) return rows[i];
    }
    if (config.actionAnchor) {
      var anchor = document.querySelector(config.actionAnchor);
      if (anchor) {
        var row = document.createElement('div');
        row.className = 'actions edu-print-actions';
        if (anchor.id === 'result' || anchor.id === 'timetableResults') anchor.insertAdjacentElement('afterend', row);
        else anchor.insertAdjacentElement('afterend', row);
        return row;
      }
    }
    return null;
  }

  function addPrintButton(config) {
    // Dedicated reports remain authoritative; never add a second print control.
    if (document.getElementById('printBtn') || document.getElementById('staffingPrintButton') || document.querySelector('[data-edu-print-button]')) return;
    var existingButtons = document.querySelectorAll('button');
    for (var i = 0; i < existingButtons.length; i += 1) {
      if (/Εκτύπωση/.test(text(existingButtons[i].textContent))) return;
    }

    var row = findActionRow(config);
    if (!row) return;
    row.classList.add('edu-actions-with-print');
    var button = document.createElement('button');
    button.type = 'button';
    button.className = 'secondary edu-btn edu-btn--secondary edu-print-button';
    button.setAttribute('data-edu-print-button', '1');
    button.textContent = 'Εκτύπωση';
    button.addEventListener('click', function () { print(config); });

    var reset = null;
    var buttons = row.querySelectorAll('button');
    for (var j = 0; j < buttons.length; j += 1) {
      if (/Καθαρισμός|Μηδενισμός/.test(text(buttons[j].textContent))) { reset = buttons[j]; break; }
    }
    if (reset) row.insertBefore(button, reset);
    else row.appendChild(button);
  }

  function init() {
    var config = PAGE_CONFIG[basename()];
    if (!config) return;
    addPrintButton(config);
  }

  global.EducationPrint = Object.freeze({ print: print, buildSheet: buildSheet, init: init });
  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init);
  else init();
})(window);
