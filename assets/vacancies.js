(function () {
  'use strict';
  document.addEventListener('DOMContentLoaded', function () {
    var role = document.getElementById('role');
    var schoolPicker = document.querySelector('[data-school-picker]');
    function syncRole() {
      if (!role || !schoolPicker) return;
      schoolPicker.hidden = role.value === 'admin';
    }
    if (role) { role.addEventListener('change', syncRole); syncRole(); }

    var autoSubmitControls = document.querySelectorAll('[data-auto-submit="true"]');
    Array.prototype.forEach.call(autoSubmitControls, function (control) {
      control.addEventListener('change', function () {
        if (control.form) control.form.submit();
      });
    });

    var confirmForms = document.querySelectorAll('form[data-confirm]');
    Array.prototype.forEach.call(confirmForms, function (confirmForm) {
      confirmForm.addEventListener('submit', function (event) {
        var message = confirmForm.getAttribute('data-confirm') || '';
        if (message && !window.confirm(message)) event.preventDefault();
      });
    });

    var accountRoleSelects = document.querySelectorAll('.vacancy-role-select[data-school-target]');
    Array.prototype.forEach.call(accountRoleSelects, function (select) {
      var targetId = select.getAttribute('data-school-target');
      var target = targetId ? document.getElementById(targetId) : null;
      if (!target) return;
      function syncAccountSchool() {
        var isAdmin = select.value === 'admin';
        target.disabled = isAdmin;
        if (isAdmin) target.value = '0';
      }
      select.addEventListener('change', syncAccountSchool);
      syncAccountSchool();
    });

    var list = document.getElementById('vacancyEntries');
    var picker = document.getElementById('specialtyPicker');
    var template = document.getElementById('vacancyEntryTemplate');
    var empty = document.getElementById('vacancyEmptyState');
    var addButton = document.querySelector('[data-add-specialty]');
    function refreshEmpty() {
      if (!list || !empty) return;
      empty.hidden = list.querySelectorAll('.vacancy-entry').length > 0;
    }
    function bindRemove(scope) {
      var buttons = (scope || document).querySelectorAll('[data-remove-entry]');
      Array.prototype.forEach.call(buttons, function (button) {
        if (button.getAttribute('data-bound') === '1') return;
        button.setAttribute('data-bound', '1');
        button.addEventListener('click', function () {
          var entry = button.closest ? button.closest('.vacancy-entry') : button.parentNode.parentNode;
          if (entry && entry.parentNode) entry.parentNode.removeChild(entry);
          refreshEmpty();
        });
      });
    }
    if (addButton && picker && list && template) {
      addButton.addEventListener('click', function () {
        var option = picker.options[picker.selectedIndex];
        if (!option || !option.value) return;
        if (list.querySelector('[data-specialty-id="' + option.value + '"]')) {
          alert('Η ειδικότητα έχει ήδη προστεθεί.');
          return;
        }
        var index = parseInt(list.getAttribute('data-next-index') || '0', 10);
        var display = (option.getAttribute('data-code') || '') + ' — ' + (option.getAttribute('data-label') || '');
        var html = template.innerHTML
          .replace(/__IDX__/g, String(index))
          .replace(/__SID__/g, String(option.value))
          .replace(/__DISPLAY__/g, display.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;'));
        var holder = document.createElement('div');
        holder.innerHTML = html;
        var node = holder.firstElementChild;
        list.appendChild(node);
        list.setAttribute('data-next-index', String(index + 1));
        picker.value = '';
        bindRemove(node);
        refreshEmpty();
      });
    }
    bindRemove(document);
    refreshEmpty();



    var drillToggles = document.querySelectorAll('[data-vacancy-drill-toggle]');
    Array.prototype.forEach.call(drillToggles, function (toggle) {
      toggle.addEventListener('click', function () {
        var targetId = toggle.getAttribute('data-vacancy-drill-toggle');
        if (!targetId) return;
        var target = document.getElementById(targetId);
        if (!target) return;
        var isOpen = toggle.getAttribute('aria-expanded') === 'true';
        toggle.setAttribute('aria-expanded', isOpen ? 'false' : 'true');
        target.hidden = isOpen;
        var row = toggle.closest ? toggle.closest('tr') : null;
        if (row) row.classList.toggle('is-expanded', !isOpen);
      });
    });

    var finalButton = document.querySelector('[data-final-submit]');
    if (finalButton) {
      finalButton.addEventListener('click', function (event) {
        if (!window.confirm('Οριστική υποβολή; Οι ειδικότητες που δεν έχουν προστεθεί θα θεωρηθούν 0 ώρες για αυτόν τον γύρο. Το ιστορικό της προηγούμενης έκδοσης θα διατηρηθεί.')) {
          event.preventDefault();
        }
      });
    }
  });
}());


/* v1.5.4 — protect school vacancy edits that have not been saved yet. */
(function () {
  'use strict';

  var form = document.querySelector('[data-vacancies-form]');
  if (!form) return;

  var dirty = false;
  var submitting = false;

  function markDirty(event) {
    if (!event || !event.target) return;
    if (!form.contains(event.target)) return;
    if (event.target.matches('button[type="submit"]')) return;
    dirty = true;
  }

  form.addEventListener('input', markDirty);
  form.addEventListener('change', markDirty);

  /* Add/remove specialty buttons change the effective form even when no input event fires. */
  form.addEventListener('click', function (event) {
    var target = event.target.closest('[data-add-specialty], [data-remove-entry]');
    if (target) dirty = true;
  });

  form.addEventListener('submit', function () {
    submitting = true;
    dirty = false;
  });

  window.addEventListener('beforeunload', function (event) {
    if (!dirty || submitting) return;
    event.preventDefault();
    event.returnValue = '';
  });
})();
