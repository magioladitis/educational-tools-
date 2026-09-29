(function () {
  'use strict';

  var list = document.getElementById('timelineList');
  if (!list) return;

  var chips = Array.prototype.slice.call(document.querySelectorAll('[data-timeline-filter]'));
  var levelChips = Array.prototype.slice.call(document.querySelectorAll('[data-timeline-level]'));
  var events = Array.prototype.slice.call(document.querySelectorAll('[data-timeline-event]'));
  var verifiedOnly = document.getElementById('timelineVerifiedOnly');
  var status = document.getElementById('timelineStatusLine');
  var empty = document.getElementById('timelineEmpty');
  var activeFilter = 'all';
  var activeLevel = 'all';

  function refresh() {
    var shown = 0;
    events.forEach(function (event) {
      var groupMatch = activeFilter === 'all' || event.getAttribute('data-group') === activeFilter;
      var rawLevels = event.getAttribute('data-levels') || 'all';
      var levels = rawLevels.split(',').map(function (value) { return value.trim(); }).filter(Boolean);
      var levelMatch = activeLevel === 'all' || levels.indexOf('all') !== -1 || levels.indexOf(activeLevel) !== -1;
      var verifiedMatch = !verifiedOnly || !verifiedOnly.checked || event.getAttribute('data-verified') === '1';
      var visible = groupMatch && levelMatch && verifiedMatch;
      event.hidden = !visible;
      if (visible) shown += 1;
    });

    if (status) {
      var defaultView = activeFilter === 'all' && activeLevel === 'all' && (!verifiedOnly || verifiedOnly.checked);
      status.textContent = defaultView ? '' : (shown === 1 ? '1 διαδικασία' : shown + ' διαδικασίες');
      status.hidden = defaultView;
    }
    if (empty) empty.hidden = shown !== 0;
  }

  chips.forEach(function (chip) {
    chip.addEventListener('click', function () {
      activeFilter = chip.getAttribute('data-timeline-filter') || 'all';
      chips.forEach(function (item) { item.classList.toggle('is-active', item === chip); });
      refresh();
    });
  });


  levelChips.forEach(function (chip) {
    chip.addEventListener('click', function () {
      activeLevel = chip.getAttribute('data-timeline-level') || 'all';
      levelChips.forEach(function (item) { item.classList.toggle('is-active', item === chip); });
      refresh();
    });
  });

  if (verifiedOnly) verifiedOnly.addEventListener('change', refresh);
  refresh();
}());
