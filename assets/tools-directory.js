
(function () {
  var toolbar = document.getElementById('tools-directory');
  var searchInput = document.getElementById('toolSearch');
  var cards = Array.prototype.slice.call(document.querySelectorAll('.tool-card'));
  var groupSections = Array.prototype.slice.call(document.querySelectorAll('.tool-group'));
  var filterButtons = Array.prototype.slice.call(document.querySelectorAll('.filter-btn'));
  var categoryLinks = Array.prototype.slice.call(document.querySelectorAll('[data-directory-filter]'));
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

  function updateButtons() {
    filterButtons.forEach(function (button) {
      var isActive = button.getAttribute('data-filter') === activeFilter;
      button.classList.toggle('active', isActive);
      button.setAttribute('aria-pressed', isActive ? 'true' : 'false');
    });
  }

  function updateCards() {
    var query = normalizeGreek(searchInput.value.trim());
    var visible = 0;

    cards.forEach(function (card) {
      var group = card.getAttribute('data-group') || '';
      var haystack = normalizeGreek((card.getAttribute('data-search') || '') + ' ' + card.textContent);
      var matchesFilter = activeFilter === 'all' || group === activeFilter;
      var matchesSearch = !query || haystack.indexOf(query) !== -1;
      var show = matchesFilter && matchesSearch;

      card.classList.toggle('hidden-card', !show);
      if (show) {
        visible++;
      }
    });

    groupSections.forEach(function (section) {
      var hasVisibleCard = !!section.querySelector('.tool-card:not(.hidden-card)');
      section.classList.toggle('hidden-group', !hasVisibleCard);
    });

    resultsLine.textContent = visible === 1
      ? 'Εμφανίζεται 1 εργαλείο.'
      : 'Εμφανίζονται ' + visible + ' εργαλεία.';

    noResults.style.display = visible === 0 ? 'block' : 'none';
    noResults.setAttribute('aria-hidden', visible === 0 ? 'false' : 'true');
    updateButtons();
  }

  function setFilter(filter, shouldScroll) {
    activeFilter = filter || 'all';
    updateCards();
    if (shouldScroll && toolbar) {
      toolbar.scrollIntoView({behavior: 'smooth', block: 'start'});
    }
  }

  filterButtons.forEach(function (button) {
    button.addEventListener('click', function () {
      setFilter(button.getAttribute('data-filter') || 'all', false);
    });
  });

  categoryLinks.forEach(function (link) {
    link.addEventListener('click', function (event) {
      event.preventDefault();
      setFilter(link.getAttribute('data-directory-filter') || 'all', true);
    });
  });

  searchInput.addEventListener('input', updateCards);
  updateCards();
})();
