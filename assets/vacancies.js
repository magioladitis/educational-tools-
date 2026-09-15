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
