/* Educational Tools — shared progressive UI helpers. */
(function () {
  'use strict';

  function normaliseText(value) {
    return (value || '').replace(/\s+/g, ' ').trim().toLocaleLowerCase('el-GR');
  }

  function enhanceButtons(root) {
    (root || document).querySelectorAll('button').forEach(function (button) {
      if (button.classList.contains('edu-back-to-top')) return;
      if (button.classList.contains('edu-btn--primary') || button.classList.contains('edu-btn--secondary') ||
          button.classList.contains('edu-btn-primary') || button.classList.contains('edu-btn-secondary')) return;

      /* Stateful / component buttons keep their page-specific appearance. */
      if (button.matches('.filter-btn, .add-row, .remove-row, .tab, .tab-btn, .mode-tab, .edu-tools-menu-action, .edu-install-help__close, .edu-mobile-hero-info-button, .edu-mobile-sticky-action__button, .edu-mobile-edit-inputs')) return;
      if (button.closest('.filters, .mode-tabs, [role="tablist"], .segmented-choice')) return;

      var text = normaliseText(button.textContent);
      var isSecondary =
        text.indexOf('μηδεν') !== -1 ||
        text.indexOf('καθαρισ') !== -1 ||
        text.indexOf('αντιγραφ') !== -1 ||
        text.indexOf('εκτύπ') !== -1 ||
        text.indexOf('κλείσ') !== -1 ||
        text.indexOf('παράδειγμα') !== -1;

      if (button.classList.contains('secondary') || button.classList.contains('reset-button') || button.classList.contains('reset-btn')) {
        isSecondary = true;
      }

      /* Explicit primary/secondary classes are respected; otherwise enhance
         ordinary action buttons progressively. */
      if (button.classList.contains('primary') || button.classList.contains('secondary') ||
          button.classList.contains('reset-button') || button.classList.contains('reset-btn')) return;
      button.classList.add('edu-btn', isSecondary ? 'edu-btn--secondary' : 'edu-btn--primary');
    });
  }

  function enhanceResults(root) {
    (root || document).querySelectorAll('.result, .results').forEach(function (result) {
      if (!result.hasAttribute('aria-live')) result.setAttribute('aria-live', 'polite');
    });
  }

  function embedSourceCards() {
    document.querySelectorAll('.edu-source-card').forEach(function (card) {
      var existingHost = card.closest('.app-box, .edu-tool-shell, main');
      if (existingHost) {
        card.classList.add('is-embedded');
        return;
      }

      var host = document.querySelector(
        '.app-box.edu-modernized, .app-box, main.dimos-calc, main.edu-tool-shell, main'
      );

      if (!host) return;
      host.appendChild(card);
      card.classList.add('is-embedded');
    });
  }


  function initialiseResponsiveSourceCards() {
    var collapseForTouch = !!(window.matchMedia && window.matchMedia(
      '(max-width: 650px), (hover: none) and (pointer: coarse)'
    ).matches);

    /* Generic mobile-first disclosures stay collapsed on phones/touch devices. */
    document.querySelectorAll('.edu-disclosure[data-mobile-collapsed="true"]').forEach(function (details) {
      if (collapseForTouch) details.removeAttribute('open');
    });

    /* Source cards render closed to avoid a flash-open state on iOS/PWA.
       Desktop progressively expands them after capability/viewport detection. */
    document.querySelectorAll('.edu-source-card[data-mobile-collapsed="true"] > .edu-source-card__details').forEach(function (details) {
      var card = details.parentElement;
      var desktopExpanded = card && card.getAttribute('data-desktop-expanded') === 'true';
      if (collapseForTouch || !desktopExpanded) details.removeAttribute('open');
      else details.setAttribute('open', '');
    });
  }

  function formatDeadlineRemaining(ms) {
    var totalSeconds = Math.max(0, Math.floor(ms / 1000));
    var days = Math.floor(totalSeconds / 86400);
    var hours = Math.floor((totalSeconds % 86400) / 3600);
    var minutes = Math.floor((totalSeconds % 3600) / 60);
    var seconds = totalSeconds % 60;
    var parts = [];
    if (days) parts.push(days + (days === 1 ? ' ημέρα' : ' ημέρες'));
    parts.push(String(hours).padStart(2, '0') + ' ώρες');
    parts.push(String(minutes).padStart(2, '0') + ' λεπτά');
    parts.push(String(seconds).padStart(2, '0') + ' δευτ.');
    return parts.join(', ');
  }

  function updateDeadlineStatus(box, now) {
    var startText = box.getAttribute('data-deadline-start') || '';
    var endText = box.getAttribute('data-deadline-end') || '';
    var endExclusiveText = box.getAttribute('data-deadline-end-exclusive') || '';
    var start = startText ? new Date(startText) : null;
    var end = endText ? new Date(endText) : (endExclusiveText ? new Date(endExclusiveText) : null);
    var openText = box.getAttribute('data-deadline-open-text') || 'Η προθεσμία είναι ανοικτή.';
    var beforeText = box.getAttribute('data-deadline-before-text') || 'Η προθεσμία δεν έχει ανοίξει ακόμη.';
    var closedText = box.getAttribute('data-deadline-closed-text') || 'Η προθεσμία έχει λήξει.';

    if ((start && isNaN(start.getTime())) || (end && isNaN(end.getTime()))) {
      box.classList.remove('before', 'open', 'closed');
      box.classList.add('closed');
      box.textContent = 'Δεν ήταν δυνατός ο έλεγχος της προθεσμίας.';
      return;
    }

    box.classList.remove('before', 'open', 'closed');

    if (start && now < start) {
      box.classList.add('before');
      box.innerHTML = '🟠 ' + beforeText + (start ? '<span class="edu-deadline-countdown">Ανοίγει σε: <strong>' + formatDeadlineRemaining(start - now) + '</strong></span>' : '');
      return;
    }

    if (!end || now < end || (!endExclusiveText && now.getTime() === end.getTime())) {
      box.classList.add('open');
      box.innerHTML = '🟢 ' + openText + (end ? '<span class="edu-deadline-countdown">Απομένουν: <strong>' + formatDeadlineRemaining(end - now) + '</strong></span>' : '');
      return;
    }

    box.classList.add('closed');
    box.textContent = '🔴 ' + closedText;
  }

  function installDeadlineCountdowns(root) {
    var boxes = Array.prototype.slice.call((root || document).querySelectorAll('[data-edu-deadline]'));
    if (!boxes.length) return;

    function updateAll() {
      var now = new Date();
      boxes.forEach(function (box) {
        updateDeadlineStatus(box, now);
      });
    }

    updateAll();
    window.setInterval(updateAll, 1000);
  }

  function installAccessibilityLandmarks() {
    var main = document.querySelector('main, [role="main"], .app, .page-shell');
    if (main && !main.id) main.id = 'main-content';
    else if (main && main.id !== 'main-content' && !document.getElementById('main-content')) main.id = 'main-content';
  }

  function prefersReducedMotion() {
    return !!(window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches);
  }

  function installBackToTop() {
    if (document.querySelector('.edu-back-to-top')) return;

    var button = document.createElement('button');
    button.type = 'button';
    button.className = 'edu-back-to-top';
    button.setAttribute('aria-label', 'Επιστροφή στην αρχή της σελίδας');
    button.setAttribute('title', 'Επιστροφή στην αρχή');
    button.innerHTML = '↑';
    document.body.appendChild(button);

    function update() {
      button.classList.toggle('is-visible', window.scrollY > 650);
    }

    button.addEventListener('click', function () {
      window.scrollTo({ top: 0, behavior: prefersReducedMotion() ? 'auto' : 'smooth' });
    });

    window.addEventListener('scroll', update, { passive: true });
    update();
  }


  function isMobileUxViewport() {
    return !!(window.matchMedia && window.matchMedia('(max-width: 700px)').matches);
  }

  function scrollElementIntoView(element, block) {
    if (!element || typeof element.scrollIntoView !== 'function') return;
    element.scrollIntoView({
      behavior: prefersReducedMotion() ? 'auto' : 'smooth',
      block: block || 'start'
    });
  }

  function visibleElement(element) {
    if (!element || !element.isConnected) return false;
    var style = window.getComputedStyle(element);
    if (style.display === 'none' || style.visibility === 'hidden') return false;
    var rect = element.getBoundingClientRect();
    return rect.width > 0 && rect.height > 0;
  }

  function fieldFromReference(ref) {
    if (!ref) return null;
    if (ref instanceof Element) return ref;
    if (typeof ref !== 'string') return null;
    return document.getElementById(ref) || document.querySelector(ref);
  }

  function fieldContainer(field) {
    if (!field) return null;
    return field.closest('.question, .field, .form-group, .edu-field, .check-row, .checkrow') || field.parentElement;
  }

  function markFieldInvalid(field, invalid) {
    if (!field) return;
    var container = fieldContainer(field);
    field.classList.toggle('edu-field-invalid', !!invalid);
    if (invalid) {
      field.setAttribute('aria-invalid', 'true');
      field.setAttribute('data-edu-validation-owned', 'true');
    } else if (field.getAttribute('data-edu-validation-owned') === 'true') {
      field.removeAttribute('aria-invalid');
      field.removeAttribute('data-edu-validation-owned');
    }
    if (container) container.classList.toggle('edu-has-invalid-field', !!invalid);
  }

  function clearValidationSummary(scope) {
    (scope || document).querySelectorAll('.edu-validation-summary').forEach(function (summary) {
      summary.remove();
    });
  }

  function reportMissingFields(fieldRefs, options) {
    options = options || {};
    var fields = (fieldRefs || []).map(fieldFromReference).filter(Boolean);
    var scope = options.scope || (fields[0] && fields[0].closest('.app-box, .app, main, .edu-tool-shell')) || document;

    clearValidationSummary(scope);
    fields.forEach(function (field) { markFieldInvalid(field, true); });
    if (!fields.length) return null;

    var noun = fields.length === 1 ? 'πεδίο χρειάζεται' : 'πεδία χρειάζονται';
    var summary = document.createElement('div');
    summary.className = 'edu-validation-summary';
    summary.setAttribute('role', 'alert');
    summary.textContent = options.message || ('Υπάρχουν ' + fields.length + ' ' + noun + ' συμπλήρωση.');

    var first = fields[0];
    var firstContainer = fieldContainer(first) || first;
    if (firstContainer.parentNode) firstContainer.parentNode.insertBefore(summary, firstContainer);

    window.setTimeout(function () {
      try { first.focus({ preventScroll: true }); } catch (error) { first.focus(); }
      scrollElementIntoView(firstContainer, 'center');
    }, 0);
    return summary;
  }

  function installNativeValidationAssist(root) {
    (root || document).querySelectorAll('form').forEach(function (form) {
      if (form.getAttribute('data-edu-validation') === 'off') return;
      var constrained = Array.prototype.slice.call(form.querySelectorAll('input[required], select[required], textarea[required]'));
      if (!constrained.length) return;

      form.addEventListener('submit', function (event) {
        var invalid = constrained.filter(function (field) { return !field.checkValidity(); });
        constrained.forEach(function (field) { markFieldInvalid(field, invalid.indexOf(field) !== -1); });
        if (!invalid.length) {
          clearValidationSummary(form);
          return;
        }
        event.preventDefault();
        reportMissingFields(invalid, { scope: form });
      });

      constrained.forEach(function (field) {
        field.addEventListener('input', function () {
          if (field.checkValidity()) markFieldInvalid(field, false);
        });
        field.addEventListener('change', function () {
          if (field.checkValidity()) markFieldInvalid(field, false);
        });
      });
    });
  }

  function installMobileHeroInfoDisclosures(root) {
    /* On phones, a page/tool hero should answer one thing first: what is this?
       Supporting copy and score/source badges remain available behind a small
       accessible information button instead of occupying the first viewport. */
    var heroes = Array.prototype.slice.call((root || document).querySelectorAll('.hero, .edu-legacy-hero'));
    heroes.forEach(function (hero) {
      if (hero.getAttribute('data-edu-mobile-info') === 'off') return;
      if (hero.querySelector('.edu-mobile-hero-info-button')) return;

      var infoNodes = Array.prototype.slice.call(hero.children).filter(function (child) {
        return child.matches && child.matches('.hero-kicker, p, .intro, .subtitle, .meta, .hero-meta, .hero-tags');
      });
      if (!infoNodes.length) return;

      infoNodes.forEach(function (node) {
        node.setAttribute('data-edu-mobile-hero-info-content', 'true');
      });

      var button = document.createElement('button');
      button.type = 'button';
      button.className = 'edu-mobile-hero-info-button';
      button.setAttribute('aria-expanded', 'false');
      button.setAttribute('aria-label', 'Πληροφορίες εργαλείου');
      button.setAttribute('title', 'Πληροφορίες εργαλείου');
      button.textContent = 'i';

      var title = hero.querySelector('h1');
      if (title && title.nextSibling) title.parentNode.insertBefore(button, title.nextSibling);
      else hero.appendChild(button);

      function sync() {
        var mobile = isMobileUxViewport();
        var open = hero.classList.contains('edu-mobile-hero-info-open');
        infoNodes.forEach(function (node) {
          if (mobile && !open) node.setAttribute('hidden', '');
          else node.removeAttribute('hidden');
        });
        button.setAttribute('aria-expanded', mobile && open ? 'true' : 'false');
        button.setAttribute('aria-label', mobile && open ? 'Απόκρυψη πληροφοριών εργαλείου' : 'Πληροφορίες εργαλείου');
        button.setAttribute('title', mobile && open ? 'Απόκρυψη πληροφοριών εργαλείου' : 'Πληροφορίες εργαλείου');
      }

      button.addEventListener('click', function () {
        hero.classList.toggle('edu-mobile-hero-info-open');
        sync();
      });
      window.addEventListener('resize', sync);
      sync();
    });
  }

  function firstEditableField(scope) {
    return (scope || document).querySelector('input:not([type="hidden"]):not([disabled]), select:not([disabled]), textarea:not([disabled])');
  }

  function ensureEditInputsButton(result, scope, getLastField) {
    if (!isMobileUxViewport() || !result || result.querySelector('.edu-mobile-edit-inputs')) return;
    var fallback = firstEditableField(scope);
    if (!fallback) return;

    var button = document.createElement('button');
    button.type = 'button';
    button.className = 'edu-mobile-edit-inputs';
    button.textContent = 'Επεξεργασία στοιχείων ↑';
    button.addEventListener('click', function () {
      var field = getLastField() || fallback;
      var container = fieldContainer(field) || field;
      scrollElementIntoView(container, 'center');
      window.setTimeout(function () {
        try { field.focus({ preventScroll: true }); } catch (error) { field.focus(); }
      }, prefersReducedMotion() ? 0 : 220);
    });
    result.insertBefore(button, result.firstChild);
  }

  function installMobilePrimaryActions(root) {
    var actions = Array.prototype.slice.call((root || document).querySelectorAll('[data-edu-primary-action="true"]'));
    if (!actions.length) return;

    actions.forEach(function (action) {
      if (action.getAttribute('data-edu-mobile-action') === 'off') return;
      var scope = action.closest('.app-box, .app, main, .edu-tool-shell') || document.body;
      var resultSelector = action.getAttribute('data-edu-result-target') || '#result';
      var result = null;
      try { result = scope.querySelector(resultSelector) || document.querySelector(resultSelector); } catch (error) { result = null; }
      var lastField = null;
      var userEngaged = false;
      var originalVisible = false;
      var revealSerial = 0;

      scope.addEventListener('focusin', function (event) {
        if (event.target && event.target.matches && event.target.matches('input, select, textarea')) {
          lastField = event.target;
          userEngaged = true;
        }
        updateSticky();
      });
      scope.addEventListener('focusout', function () { window.setTimeout(updateSticky, 50); });
      scope.addEventListener('input', function (event) {
        if (event.target && event.target.matches && event.target.matches('input, select, textarea')) lastField = event.target;
        userEngaged = true;
      });
      scope.addEventListener('change', function (event) {
        if (event.target && event.target.matches && event.target.matches('input, select, textarea')) lastField = event.target;
        userEngaged = true;
      });

      var sticky = document.createElement('div');
      sticky.className = 'edu-mobile-sticky-action';
      sticky.hidden = true;
      var stickyButton = document.createElement('button');
      stickyButton.type = 'button';
      stickyButton.className = 'edu-mobile-sticky-action__button';
      stickyButton.textContent = action.getAttribute('data-edu-mobile-label') || normaliseMobileActionLabel(action.textContent);
      stickyButton.setAttribute('aria-label', normaliseText(action.textContent) ? action.textContent.trim() : 'Κύρια ενέργεια');
      sticky.appendChild(stickyButton);
      document.body.appendChild(sticky);

      function editingControlActive() {
        var active = document.activeElement;
        return !!(active && active.matches && active.matches('input, select, textarea'));
      }

      function normaliseMobileActionLabel(label) {
        var clean = (label || '').replace(/\s+/g, ' ').trim();
        if (/^Έλεγχος/i.test(clean)) return 'Έλεγχος';
        if (/^Υπολογ/i.test(clean)) return 'Υπολογισμός';
        return clean || 'Συνέχεια';
      }

      function updateSticky() {
        var shouldShow = isMobileUxViewport() && userEngaged && !originalVisible && !editingControlActive() && !action.disabled && visibleElement(action);
        sticky.hidden = !shouldShow;
        sticky.classList.toggle('is-visible', shouldShow);
        document.body.classList.toggle('edu-mobile-sticky-action-visible', shouldShow);
      }

      function handleActionResult() {
        var serial = ++revealSerial;
        [0, 80, 220, 520].forEach(function (delay) {
          window.setTimeout(function () {
            if (serial !== revealSerial || !isMobileUxViewport()) return;
            var invalid = scope.querySelector('.edu-field-invalid, .edu-has-invalid-field [aria-invalid="true"], .question.has-missing select, .question.has-missing input, [aria-invalid="true"]');
            if (invalid && visibleElement(invalid)) {
              var invalidContainer = fieldContainer(invalid) || invalid;
              scrollElementIntoView(invalidContainer, 'center');
              revealSerial += 1;
              return;
            }
            if (!result || !visibleElement(result) || !normaliseText(result.textContent)) return;
            ensureEditInputsButton(result, scope, function () { return lastField; });
            result.classList.add('edu-mobile-result-target');
            scrollElementIntoView(result, 'start');
            revealSerial += 1;
          }, delay);
        });
      }

      action.addEventListener('click', function () {
        userEngaged = true;
        handleActionResult();
        updateSticky();
      });
      stickyButton.addEventListener('click', function () {
        userEngaged = true;
        action.click();
      });

      if ('IntersectionObserver' in window) {
        var observer = new IntersectionObserver(function (entries) {
          entries.forEach(function (entry) {
            if (entry.target === action) originalVisible = entry.isIntersecting && entry.intersectionRatio > 0.15;
          });
          updateSticky();
        }, { threshold: [0, .15, .5, 1] });
        observer.observe(action);
      } else {
        function updateOriginalVisibility() {
          var rect = action.getBoundingClientRect();
          originalVisible = rect.bottom > 0 && rect.top < window.innerHeight;
          updateSticky();
        }
        window.addEventListener('scroll', updateOriginalVisibility, { passive: true });
        window.addEventListener('resize', updateOriginalVisibility);
        updateOriginalVisibility();
      }

      window.addEventListener('scroll', function () {
        if (window.scrollY > 180) userEngaged = true;
        updateSticky();
      }, { passive: true });
      window.addEventListener('resize', updateSticky);
      updateSticky();
    });
  }


  /* Mobile UX phase 2 -------------------------------------------------------
     - table discoverability / compact stacking for opted-in small tables
     - mobile keyboard hints and decimal-comma assistance
     - short-lived local draft recovery for ordinary calculator/guide fields
     ---------------------------------------------------------------------- */
  function showSharedToast(message) {
    var toast = document.querySelector('.edu-app-toast');
    if (!toast) {
      toast = document.createElement('div');
      toast.className = 'edu-app-toast';
      toast.setAttribute('role', 'status');
      toast.setAttribute('aria-live', 'polite');
      document.body.appendChild(toast);
    }
    toast.textContent = message;
    toast.classList.add('is-visible');
    window.clearTimeout(showSharedToast.timer);
    showSharedToast.timer = window.setTimeout(function () { toast.classList.remove('is-visible'); }, 2800);
  }

  function inputUsesDecimalKeyboard(input) {
    if (!input || input.tagName !== 'INPUT') return false;
    if ((input.getAttribute('inputmode') || '').toLowerCase() === 'decimal') return true;
    if ((input.type || '').toLowerCase() !== 'number') return false;
    var step = (input.getAttribute('step') || '').trim().toLowerCase();
    if (!step || step === '1') return false;
    if (step === 'any') return true;
    var parsed = Number(step);
    return Number.isFinite(parsed) && Math.floor(parsed) !== parsed;
  }

  function installDecimalCommaAssist(input) {
    if (!input || input.getAttribute('data-edu-decimal-comma-ready') === 'true') return;
    if ((input.type || '').toLowerCase() !== 'number' || !inputUsesDecimalKeyboard(input)) return;
    input.setAttribute('data-edu-decimal-comma-ready', 'true');

    function handleDecimalInput(event) {
      if (!event) return;
      var comma = event.data === ',' || event.key === ',';
      var current = String(input.value || '');
      if (comma) {
        if (current.indexOf('.') !== -1) {
          event.preventDefault();
          input.removeAttribute('data-edu-pending-decimal');
          return;
        }
        /* A number input may reject an intermediate value such as "12.".
           Remember the Greek comma briefly and commit a valid "12.5" when the
           next digit arrives. */
        if (current !== '' && /^-?\d+$/.test(current)) {
          event.preventDefault();
          input.setAttribute('data-edu-pending-decimal', 'true');
        }
        return;
      }
      if (event.type === 'beforeinput' && input.getAttribute('data-edu-pending-decimal') === 'true') {
        if (/^\d$/.test(event.data || '')) {
          event.preventDefault();
          input.value = current + '.' + event.data;
          input.removeAttribute('data-edu-pending-decimal');
          input.dispatchEvent(new Event('input', { bubbles: true }));
        } else if (event.inputType && event.inputType.indexOf('delete') === 0) {
          input.removeAttribute('data-edu-pending-decimal');
        }
      }
      if (event.type === 'keydown' && (event.key === 'Escape' || event.key === 'Backspace' || event.key === 'Delete')) {
        input.removeAttribute('data-edu-pending-decimal');
      }
    }

    input.addEventListener('beforeinput', handleDecimalInput);
    input.addEventListener('keydown', handleDecimalInput);
    input.addEventListener('blur', function () { input.removeAttribute('data-edu-pending-decimal'); });
  }

  function installMobileInputHints(root) {
    var scope = root || document;
    var inputs = Array.prototype.slice.call(scope.querySelectorAll ? scope.querySelectorAll('input, textarea') : []);
    if (scope.matches && scope.matches('input, textarea')) inputs.unshift(scope);

    inputs.forEach(function (input) {
      if (input.getAttribute('data-edu-mobile-input') === 'off') return;
      var type = (input.type || '').toLowerCase();
      if (type === 'number' && !input.hasAttribute('inputmode')) {
        input.setAttribute('inputmode', inputUsesDecimalKeyboard(input) ? 'decimal' : 'numeric');
      }
      if (inputUsesDecimalKeyboard(input)) installDecimalCommaAssist(input);
    });

    /* enterkeyhint changes only the soft-keyboard label; it does not change
       form submission or calculator logic. */
    var enterScope = scope === document ? document : scope;
    var editable = Array.prototype.slice.call(enterScope.querySelectorAll ? enterScope.querySelectorAll(
      'input:not([type="hidden"]):not([type="checkbox"]):not([type="radio"]):not([type="file"]):not([type="button"]):not([type="submit"]):not([type="reset"]):not([disabled]), textarea:not([disabled])'
    ) : []);
    if (scope.matches && scope.matches('input:not([type="hidden"]):not([type="checkbox"]):not([type="radio"]):not([type="file"]):not([type="button"]):not([type="submit"]):not([type="reset"]):not([disabled]), textarea:not([disabled])')) editable.unshift(scope);
    editable = editable.filter(visibleElement);
    editable.forEach(function (input, index) {
      if (input.hasAttribute('enterkeyhint') || input.getAttribute('data-edu-mobile-input') === 'off') return;
      if ((input.type || '').toLowerCase() === 'search') input.setAttribute('enterkeyhint', 'search');
      else input.setAttribute('enterkeyhint', index === editable.length - 1 ? 'done' : 'next');
    });
  }

  function mobileTableHeading(table) {
    var caption = table.querySelector('caption');
    if (caption && normaliseText(caption.textContent)) return caption.textContent.trim();
    var current = table.previousElementSibling;
    while (current) {
      if (current.matches && current.matches('h2, h3, h4, summary')) return current.textContent.trim();
      current = current.previousElementSibling;
    }
    return 'Πίνακας δεδομένων';
  }

  function applyStackedTableLabels(table) {
    if (table.getAttribute('data-edu-mobile-stack-ready') === 'true') return;
    table.setAttribute('data-edu-mobile-stack-ready', 'true');
    table.classList.add('edu-mobile-table-stack');

    var headerCells = Array.prototype.slice.call(table.querySelectorAll('thead th'));
    var headerRow = null;
    if (!headerCells.length) {
      var firstRow = table.querySelector('tr');
      if (firstRow) {
        headerCells = Array.prototype.slice.call(firstRow.querySelectorAll('th'));
        if (headerCells.length) headerRow = firstRow;
      }
    }
    if (!headerCells.length) return;
    if (headerRow) headerRow.classList.add('edu-mobile-table-header-row');

    var labels = headerCells.map(function (cell) { return cell.textContent.trim(); });
    table.querySelectorAll('tbody tr, tr').forEach(function (row) {
      if (row === headerRow || row.closest('thead')) return;
      Array.prototype.slice.call(row.children).forEach(function (cell, index) {
        if (cell.tagName !== 'TD' || cell.hasAttribute('data-edu-label')) return;
        cell.setAttribute('data-edu-label', labels[index] || 'Τιμή');
      });
    });
  }

  function installMobileTableAssist(root) {
    var scope = root || document;
    var tables = Array.prototype.slice.call(scope.querySelectorAll ? scope.querySelectorAll('table') : []);
    if (scope.matches && scope.matches('table')) tables.unshift(scope);

    tables.forEach(function (table) {
      if (table.getAttribute('data-edu-mobile-table') === 'off') return;
      if (/\bprint[-_]/.test(table.className || '') || table.closest('.print-only, [data-print-only="true"]')) return;
      if (table.getAttribute('data-edu-mobile-table') === 'stack') {
        applyStackedTableLabels(table);
        return;
      }
      if (table.getAttribute('data-edu-mobile-table-ready') === 'true') return;

      var wrapper = table.closest('.table-wrap, .mapping-wrap, .matrix-wrap, .edu-overflow-x-auto, .edu-mobile-table-scroll');
      if (!wrapper && isMobileUxViewport() && table.parentNode) {
        wrapper = document.createElement('div');
        wrapper.className = 'edu-mobile-table-scroll';
        table.parentNode.insertBefore(wrapper, table);
        wrapper.appendChild(table);
      }
      if (!wrapper) return;
      table.setAttribute('data-edu-mobile-table-ready', 'true');
      wrapper.classList.add('edu-mobile-table-scroll-region');
      if (!wrapper.hasAttribute('tabindex')) wrapper.setAttribute('tabindex', '0');
      if (!wrapper.hasAttribute('role')) wrapper.setAttribute('role', 'region');
      if (!wrapper.hasAttribute('aria-label')) wrapper.setAttribute('aria-label', mobileTableHeading(table));

      var hint = document.createElement('div');
      hint.className = 'edu-mobile-table-hint';
      hint.setAttribute('aria-hidden', 'true');
      hint.textContent = 'Σύρε οριζόντια για περισσότερες στήλες →';
      wrapper.parentNode.insertBefore(hint, wrapper);

      function updateHint() {
        var overflow = isMobileUxViewport() && wrapper.scrollWidth > wrapper.clientWidth + 3;
        hint.hidden = !overflow || wrapper.scrollLeft > 10;
        wrapper.classList.toggle('has-horizontal-overflow', overflow);
      }
      wrapper.addEventListener('scroll', updateHint, { passive: true });
      window.addEventListener('resize', updateHint);
      var details = wrapper.closest('details');
      if (details) details.addEventListener('toggle', function () { window.setTimeout(updateHint, 0); });
      window.requestAnimationFrame(updateHint);
    });
  }

  var DRAFT_TTL_MS = 12 * 60 * 60 * 1000;
  var DRAFT_PREFIX = 'eduToolsDraftV1:';

  function draftKey() {
    return DRAFT_PREFIX + window.location.pathname;
  }

  function draftPersistenceEnabled() {
    if (!document.querySelector('.edu-tools-global-header[data-edu-current-tool-href]')) return false;
    if (document.body.getAttribute('data-edu-draft-persist') === 'off') return false;
    if (document.body.matches('.edu-vacancies, .edu-page-staffing-simulator')) return false;
    return true;
  }

  function draftFieldKey(field) {
    if (field.id) return 'id:' + field.id;
    if (!field.name) return '';
    if ((field.type || '').toLowerCase() === 'radio') return 'radio:' + field.name + ':' + field.value;
    return 'name:' + field.name;
  }

  function isSafeDraftField(field) {
    if (!field || field.disabled || field.readOnly || !draftFieldKey(field)) return false;
    if (field.getAttribute('data-edu-draft') === 'off' || field.closest('[data-edu-draft-persist="off"]')) return false;
    var type = (field.type || '').toLowerCase();
    if (['hidden', 'password', 'file', 'submit', 'button', 'reset', 'image', 'range'].indexOf(type) !== -1) return false;
    var autocomplete = (field.getAttribute('autocomplete') || '').toLowerCase();
    if (/name|email|tel|address|password|cc-|one-time-code/.test(autocomplete)) return false;
    var identity = ((field.id || '') + ' ' + (field.name || '')).toLowerCase();
    if (/(password|passwd|email|e-mail|phone|telephone|mobile|address|amka|afm|username|user_name|contact)/.test(identity)) return false;
    return field.matches('input, select, textarea');
  }

  function readDraftState(field) {
    var type = (field.type || '').toLowerCase();
    if (type === 'checkbox' || type === 'radio') return { checked: !!field.checked };
    var value = String(field.value == null ? '' : field.value);
    if (value.length > 300) value = value.slice(0, 300);
    return { value: value };
  }

  function installDraftPersistence(root) {
    if (!draftPersistenceEnabled()) return;
    var storage = null;
    try { storage = window.localStorage; } catch (error) { storage = null; }
    if (!storage) return;
    var key = draftKey();
    var restoring = false;
    var ignoreSavesUntil = 0;
    var saveTimer = null;

    function fields() {
      return Array.prototype.slice.call((root || document).querySelectorAll('input, select, textarea')).filter(isSafeDraftField);
    }

    function clearDraft() {
      window.clearTimeout(saveTimer);
      ignoreSavesUntil = Date.now() + 900;
      try { storage.removeItem(key); } catch (error) {}
    }

    function saveDraft() {
      if (restoring || Date.now() < ignoreSavesUntil) return;
      var state = {};
      fields().forEach(function (field) {
        state[draftFieldKey(field)] = readDraftState(field);
      });
      try {
        storage.setItem(key, JSON.stringify({ savedAt: Date.now(), state: state }));
      } catch (error) {}
    }

    function scheduleSave() {
      if (restoring || Date.now() < ignoreSavesUntil) return;
      window.clearTimeout(saveTimer);
      saveTimer = window.setTimeout(saveDraft, 450);
    }

    function restoreDraft() {
      var payload = null;
      try { payload = JSON.parse(storage.getItem(key) || 'null'); } catch (error) { payload = null; }
      if (!payload || !payload.state || !payload.savedAt || Date.now() - payload.savedAt > DRAFT_TTL_MS) {
        if (payload) clearDraft();
        return;
      }

      restoring = true;
      var restored = [];
      fields().forEach(function (field) {
        var saved = payload.state[draftFieldKey(field)];
        if (!saved) return;
        var type = (field.type || '').toLowerCase();
        if (type === 'checkbox' || type === 'radio') {
          if (field.checked !== !!saved.checked) {
            field.checked = !!saved.checked;
            restored.push(field);
          }
        } else if (Object.prototype.hasOwnProperty.call(saved, 'value') && field.value !== saved.value) {
          field.value = saved.value;
          restored.push(field);
        }
      });
      restored.forEach(function (field) {
        var type = (field.type || '').toLowerCase();
        field.dispatchEvent(new Event(type === 'checkbox' || type === 'radio' || field.tagName === 'SELECT' ? 'change' : 'input', { bubbles: true }));
      });
      restoring = false;
      if (restored.length) showSharedToast('Επαναφέρθηκαν προσωρινά τα στοιχεία που είχες συμπληρώσει.');
    }

    document.addEventListener('input', function (event) {
      if (isSafeDraftField(event.target)) scheduleSave();
    });
    document.addEventListener('change', function (event) {
      if (isSafeDraftField(event.target)) scheduleSave();
    });
    document.addEventListener('click', function (event) {
      var button = event.target && event.target.closest ? event.target.closest('button, input[type="reset"]') : null;
      if (!button) return;
      var label = normaliseText(button.textContent || button.value || '');
      if (button.type === 'reset' || label.indexOf('καθαρισ') !== -1 || label.indexOf('μηδεν') !== -1) {
        window.setTimeout(clearDraft, 0);
      }
    });
    document.addEventListener('reset', function () { window.setTimeout(clearDraft, 0); }, true);
    window.setTimeout(restoreDraft, 20);
  }

  window.EduToolsUI = Object.freeze(Object.assign({}, window.EduToolsUI || {}, {
    markFieldInvalid: markFieldInvalid,
    clearValidationSummary: clearValidationSummary,
    reportMissingFields: reportMissingFields,
    revealResult: function (result) {
      if (!result) return;
      result.classList.add('edu-mobile-result-target');
      if (isMobileUxViewport()) scrollElementIntoView(result, 'start');
    }
  }));

  function init() {
    document.body.classList.add('edu-ui', 'edu-mobile-ux-enabled');
    installAccessibilityLandmarks();
    enhanceButtons(document);
    enhanceResults(document);
    embedSourceCards();
    initialiseResponsiveSourceCards();
    installMobileHeroInfoDisclosures(document);
    installNativeValidationAssist(document);
    installMobilePrimaryActions(document);
    installMobileInputHints(document);
    installMobileTableAssist(document);
    window.addEventListener('resize', function () { installMobileTableAssist(document); });
    installDraftPersistence(document);
    installBackToTop();
    installDeadlineCountdowns(document);

    /* Dynamic result content may create buttons/messages after page load. */
    var observer = new MutationObserver(function (records) {
      records.forEach(function (record) {
        record.addedNodes.forEach(function (node) {
          if (!(node instanceof Element)) return;
          enhanceButtons(node);
          enhanceResults(node);
          installMobileInputHints(node);
          installMobileTableAssist(node);
        });
      });
    });
    observer.observe(document.body, { childList: true, subtree: true });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
