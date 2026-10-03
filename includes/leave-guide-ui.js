(function () {
  'use strict';
  var search = document.getElementById('leaveSearch');
  var jump = document.getElementById('leaveJump');
  var count = document.getElementById('leaveCount');
  var noResults = document.getElementById('leaveNoResults');
  var cards = Array.prototype.slice.call(document.querySelectorAll('.leave-card'));
  var filters = Array.prototype.slice.call(document.querySelectorAll('[data-leave-filter]'));
  var activeFilter = 'all';

  function normalize(text) {
    return (text || '')
      .toLocaleLowerCase('el-GR')
      .normalize('NFD')
      .replace(/[\u0300-\u036f]/g, '')
      .replace(/ς/g, 'σ');
  }

  function update() {
    var q = normalize(search ? search.value.trim() : '');
    var visible = 0;
    cards.forEach(function (card) {
      var category = card.getAttribute('data-leave-category') || '';
      var haystack = normalize((card.getAttribute('data-leave-search') || '') + ' ' + card.textContent);
      var show = (activeFilter === 'all' || category === activeFilter) && (!q || haystack.indexOf(q) !== -1);
      card.hidden = !show;
      if (show) visible++;
    });
    if (count) count.textContent = visible === 1 ? '1 άδεια' : visible + ' άδειες';
    if (noResults) noResults.hidden = visible !== 0;
  }

  filters.forEach(function (button) {
    button.addEventListener('click', function () {
      activeFilter = button.getAttribute('data-leave-filter') || 'all';
      filters.forEach(function (item) { item.classList.toggle('is-active', item === button); });
      update();
    });
  });

  if (search) search.addEventListener('input', update);

  document.addEventListener('click', function (event) {
    var link = event.target.closest('[data-related-leave]');
    if (!link) return;
    var id = link.getAttribute('data-related-leave');
    var target = document.querySelector('[data-leave-id="' + (id || '').replace(/"/g, '\\"') + '"]');
    if (!target) return;
    event.preventDefault();
    activeFilter = 'all';
    filters.forEach(function (item) { item.classList.toggle('is-active', item.getAttribute('data-leave-filter') === 'all'); });
    if (search) search.value = '';
    update();
    target.open = true;
    var reduceMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    target.scrollIntoView({behavior: reduceMotion ? 'auto' : 'smooth', block: 'start'});
  });

  if (jump) jump.addEventListener('change', function () {
    if (!jump.value) return;
    var target = document.querySelector('[data-leave-id="' + jump.value.replace(/"/g, '\\"') + '"]');
    if (!target) return;
    activeFilter = 'all';
    filters.forEach(function (item) { item.classList.toggle('is-active', item.getAttribute('data-leave-filter') === 'all'); });
    if (search) search.value = '';
    update();
    target.open = true;
    var reduceMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    target.scrollIntoView({behavior: reduceMotion ? 'auto' : 'smooth', block: 'start'});
    window.setTimeout(function () {
      var summary = target.querySelector('summary');
      if (summary) summary.focus({preventScroll: true});
    }, reduceMotion ? 0 : 350);
  });

  update();
})();
