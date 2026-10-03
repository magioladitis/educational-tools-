(function () {
  'use strict';
  var select = document.getElementById('leaveCompareSelect');
  var panels = Array.prototype.slice.call(document.querySelectorAll('[data-leave-comparison]'));
  if (!select || !panels.length) return;

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

  select.addEventListener('change', function () {
    show(select.value, true);
    var active = document.querySelector('[data-leave-comparison="' + select.value.replace(/"/g, '\\"') + '"]');
    if (active) {
      var reduceMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
      active.scrollIntoView({behavior: reduceMotion ? 'auto' : 'smooth', block: 'start'});
    }
  });

  show(select.value, false);
})();
