/* Educational Tools — shared progressive UI helpers. */
(function () {
  'use strict';

  function normaliseText(value) {
    return (value || '').replace(/\s+/g, ' ').trim().toLocaleLowerCase('el-GR');
  }

  function enhanceButtons(root) {
    (root || document).querySelectorAll('button').forEach(function (button) {
      if (button.classList.contains('filter-btn') || button.classList.contains('edu-back-to-top')) return;
      if (button.classList.contains('edu-btn-primary') || button.classList.contains('edu-btn-secondary')) return;

      var text = normaliseText(button.textContent);
      var isSecondary =
        text.indexOf('μηδεν') !== -1 ||
        text.indexOf('καθαρισ') !== -1 ||
        text.indexOf('αντιγραφ') !== -1 ||
        text.indexOf('εκτύπ') !== -1 ||
        text.indexOf('κλείσ') !== -1;

      button.classList.add(isSecondary ? 'edu-btn-secondary' : 'edu-btn-primary');
    });
  }

  function enhanceResults(root) {
    (root || document).querySelectorAll('.result, .results').forEach(function (result) {
      if (!result.hasAttribute('aria-live')) result.setAttribute('aria-live', 'polite');
    });
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
    installBackToTop();

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
