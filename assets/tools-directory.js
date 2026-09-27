(function () {
  var toolbar = document.getElementById('tools-directory');
  var searchInput = document.getElementById('toolSearch');
  var cards = Array.prototype.slice.call(document.querySelectorAll('.tool-card'));
  var groupSections = Array.prototype.slice.call(document.querySelectorAll('.tool-group'));
  var categoryLinks = Array.prototype.slice.call(document.querySelectorAll('[data-directory-filter]'));
  var activeCategory = document.getElementById('activeCategoryFilter');
  var activeCategoryText = document.getElementById('activeCategoryText');
  var clearCategoryButton = document.getElementById('clearCategoryFilter');
  var resultsLine = document.getElementById('resultsLine');
  var noResults = document.getElementById('noResults');
  var activeFilter = toolbar && toolbar.getAttribute('data-initial-filter') ? toolbar.getAttribute('data-initial-filter') : 'all';

  function normalizeGreek(text) {
    return (text || '')
      .toLocaleLowerCase('el-GR')
      .normalize('NFD')
      .replace(/[\u0300-\u036f]/g, '')
      .replace(/ς/g, 'σ');
  }

  function categoryLabel(filter) {
    var match = categoryLinks.find(function (link) {
      return link.getAttribute('data-directory-filter') === filter;
    });
    return match ? (match.getAttribute('data-directory-label') || match.textContent.trim()) : '';
  }

  function updateCategoryLinks() {
    categoryLinks.forEach(function (link) {
      var isActive = activeFilter !== 'all' && link.getAttribute('data-directory-filter') === activeFilter;
      link.classList.toggle('is-active', isActive);
      if (isActive) link.setAttribute('aria-current', 'true');
      else link.removeAttribute('aria-current');
    });
  }

  function updateCards() {
    if (!searchInput || !resultsLine || !noResults) return;

    var query = normalizeGreek(searchInput.value.trim());
    var visible = 0;

    cards.forEach(function (card) {
      var group = card.getAttribute('data-group') || '';
      var haystack = normalizeGreek((card.getAttribute('data-search') || '') + ' ' + card.textContent);
      var matchesFilter = activeFilter === 'all' || group === activeFilter;
      var matchesSearch = !query || haystack.indexOf(query) !== -1;
      var show = matchesFilter && matchesSearch;

      card.classList.toggle('hidden-card', !show);
      if (show) visible++;
    });

    groupSections.forEach(function (section) {
      var hasVisibleCard = !!section.querySelector('.tool-card:not(.hidden-card)');
      section.classList.toggle('hidden-group', !hasVisibleCard);
    });

    var countText = visible === 1 ? '1 εργαλείο' : visible + ' εργαλεία';
    if (activeFilter !== 'all' && activeCategory && activeCategoryText) {
      activeCategory.hidden = false;
      activeCategoryText.textContent = categoryLabel(activeFilter) + ' · ' + countText;
      resultsLine.hidden = true;
      resultsLine.textContent = 'Εμφανίζονται ' + countText + '.';
    } else {
      if (activeCategory) activeCategory.hidden = true;
      resultsLine.hidden = false;
      resultsLine.textContent = visible === 1 ? 'Εμφανίζεται 1 εργαλείο.' : 'Εμφανίζονται ' + visible + ' εργαλεία.';
    }

    noResults.style.display = visible === 0 ? 'block' : 'none';
    noResults.setAttribute('aria-hidden', visible === 0 ? 'false' : 'true');
    updateCategoryLinks();
  }

  function setFilter(filter, shouldScroll) {
    activeFilter = filter || 'all';
    updateCards();
    if (shouldScroll && toolbar) {
      var reduceMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
      toolbar.scrollIntoView({behavior: reduceMotion ? 'auto' : 'smooth', block: 'start'});
    }
  }

  categoryLinks.forEach(function (link) {
    link.addEventListener('click', function (event) {
      event.preventDefault();
      setFilter(link.getAttribute('data-directory-filter') || 'all', true);
    });
  });

  if (clearCategoryButton) {
    clearCategoryButton.addEventListener('click', function () {
      setFilter('all', false);
      if (searchInput) searchInput.focus();
    });
  }

  if (searchInput) searchInput.addEventListener('input', updateCards);
  updateCards();
})();
