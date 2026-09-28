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
      if (button.matches('.filter-btn, .add-row, .remove-row, .tab, .tab-btn, .mode-tab, .edu-tools-menu-action, .edu-install-help__close, .edu-mobile-intro-toggle, .edu-mobile-sticky-action__button, .edu-mobile-edit-inputs')) return;
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

  function installMobileHeroCompaction(root) {
    (root || document).querySelectorAll('.hero, .edu-legacy-hero').forEach(function (hero) {
      if (hero.getAttribute('data-edu-mobile-compact') === 'off') return;
      var paragraphs = Array.prototype.slice.call(hero.children).filter(function (child) {
        return child.matches && child.matches('p, .intro, .subtitle');
      });

      paragraphs.forEach(function (paragraph) {
        if (normaliseText(paragraph.textContent).length < 190) return;
        if (paragraph.classList.contains('edu-mobile-intro-clamp')) return;

        paragraph.classList.add('edu-mobile-intro-clamp');
        var toggle = document.createElement('button');
        toggle.type = 'button';
        toggle.className = 'edu-mobile-intro-toggle';
        toggle.setAttribute('aria-expanded', 'false');
        toggle.textContent = 'Περισσότερα';
        paragraph.insertAdjacentElement('afterend', toggle);

        toggle.addEventListener('click', function () {
          var expanded = paragraph.classList.toggle('is-expanded');
          toggle.setAttribute('aria-expanded', expanded ? 'true' : 'false');
          toggle.textContent = expanded ? 'Λιγότερα' : 'Περισσότερα';
        });
      });
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
    installMobileHeroCompaction(document);
    installNativeValidationAssist(document);
    installMobilePrimaryActions(document);
    installBackToTop();
    installDeadlineCountdowns(document);

    /* Dynamic result content may create buttons/messages after page load. */
    var observer = new MutationObserver(function (records) {
      records.forEach(function (record) {
        record.addedNodes.forEach(function (node) {
          if (!(node instanceof Element)) return;
          enhanceButtons(node);
          enhanceResults(node);
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
