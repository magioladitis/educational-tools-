/* Educational Tools — shared progressive UI helpers. */
(function () {
  'use strict';

  function normaliseText(value) {
    return (value || '').replace(/\s+/g, ' ').trim().toLocaleLowerCase('el-GR');
  }

  function enhanceButtons(root) {
    (root || document).querySelectorAll('button').forEach(function (button) {
      if (button.classList.contains('edu-back-to-top')) return;
      if (button.classList.contains('edu-btn-primary') || button.classList.contains('edu-btn-secondary')) return;

      /* Stateful / component buttons keep their page-specific appearance. */
      if (button.matches('.filter-btn, .add-row, .remove-row, .tab, .tab-btn, .mode-tab')) return;
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
      button.classList.add(isSecondary ? 'edu-btn-secondary' : 'edu-btn-primary');
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
      window.scrollTo({ top: 0, behavior: 'smooth' });
    });

    window.addEventListener('scroll', update, { passive: true });
    update();
  }

  function init() {
    document.body.classList.add('edu-ui');
    enhanceButtons(document);
    enhanceResults(document);
    embedSourceCards();
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
