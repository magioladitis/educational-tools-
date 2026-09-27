/* Educational Tools — local app experience: recents, favorites, share, install. */
(function () {
  'use strict';

  function appStorageNamespace() {
    var manifest = document.querySelector('link[rel="manifest"]');
    try {
      var path = manifest ? new URL(manifest.href, window.location.href).pathname : '/';
      path = path.replace(/[^/]*$/, '');
      return 'eduTools:' + path + ':';
    } catch (error) {
      return 'eduTools:/:';
    }
  }

  var STORAGE_NAMESPACE = appStorageNamespace();
  var RECENTS_KEY = STORAGE_NAMESPACE + 'eduToolsRecentV1';
  var FAVORITES_KEY = STORAGE_NAMESPACE + 'eduToolsFavoritesV1';
  var MAX_RECENTS = 5;
  var MAX_FAVORITES = 20;
  var deferredInstallPrompt = null;

  function readList(key) {
    try {
      var value = JSON.parse(window.localStorage.getItem(key) || '[]');
      return Array.isArray(value) ? value.filter(function (item) { return typeof item === 'string' && item !== ''; }) : [];
    } catch (error) {
      return [];
    }
  }

  function writeList(key, list) {
    try {
      window.localStorage.setItem(key, JSON.stringify(list));
      return true;
    } catch (error) {
      return false;
    }
  }

  function uniqueFront(list, value, limit) {
    var next = [value].concat(list.filter(function (item) { return item !== value; }));
    return next.slice(0, limit);
  }

  function isStandalone() {
    return !!((window.matchMedia && window.matchMedia('(display-mode: standalone)').matches) || window.navigator.standalone === true);
  }

  function currentToolHref() {
    var header = document.querySelector('.edu-tools-global-header[data-edu-current-tool-href]');
    return header ? (header.getAttribute('data-edu-current-tool-href') || '') : '';
  }

  function currentToolTitle() {
    var header = document.querySelector('.edu-tools-global-header[data-edu-current-tool-title]');
    return header ? (header.getAttribute('data-edu-current-tool-title') || document.title) : document.title;
  }

  function trackRecentTool() {
    var href = currentToolHref();
    if (!href) return;
    writeList(RECENTS_KEY, uniqueFront(readList(RECENTS_KEY), href, MAX_RECENTS));
  }

  function updateFavoriteButton() {
    var button = document.querySelector('[data-edu-favorite-toggle]');
    if (!button) return;
    var href = currentToolHref();
    var active = href && readList(FAVORITES_KEY).indexOf(href) !== -1;
    var label = button.querySelector('[data-edu-favorite-label]');
    var icon = button.querySelector('[data-edu-favorite-icon]');
    button.setAttribute('aria-pressed', active ? 'true' : 'false');
    button.classList.toggle('is-active', !!active);
    if (label) label.textContent = active ? 'Αφαίρεση από αγαπημένα' : 'Προσθήκη στα αγαπημένα';
    if (icon) icon.textContent = active ? '★' : '☆';
  }

  function toggleFavorite() {
    var href = currentToolHref();
    if (!href) return;
    var list = readList(FAVORITES_KEY);
    var index = list.indexOf(href);
    var added = index === -1;
    if (added) list = uniqueFront(list, href, MAX_FAVORITES);
    else list.splice(index, 1);
    if (!writeList(FAVORITES_KEY, list)) {
      showToast('Δεν ήταν δυνατή η αποθήκευση των αγαπημένων στη συσκευή.');
      return;
    }
    updateFavoriteButton();
    renderPersonalTools();
    showToast(added ? 'Προστέθηκε στα αγαπημένα.' : 'Αφαιρέθηκε από τα αγαπημένα.');
  }

  function showToast(message) {
    var toast = document.querySelector('.edu-app-toast');
    if (!toast) {
      toast = document.createElement('div');
      toast.className = 'edu-app-toast';
      toast.setAttribute('role', 'status');
      toast.setAttribute('aria-live', 'polite');
      document.body.appendChild(toast);
    }
    toast.textContent = message;
    toast.classList.add('is-visible');
    window.clearTimeout(showToast.timer);
    showToast.timer = window.setTimeout(function () { toast.classList.remove('is-visible'); }, 2600);
  }

  function copyCurrentUrl() {
    var url = window.location.href;
    if (navigator.clipboard && navigator.clipboard.writeText) {
      return navigator.clipboard.writeText(url).then(function () { showToast('Ο σύνδεσμος αντιγράφηκε.'); });
    }
    return new Promise(function (resolve, reject) {
      var input = document.createElement('textarea');
      input.value = url;
      input.setAttribute('readonly', 'readonly');
      input.style.position = 'fixed';
      input.style.opacity = '0';
      document.body.appendChild(input);
      input.select();
      try {
        if (!document.execCommand('copy')) throw new Error('copy failed');
        showToast('Ο σύνδεσμος αντιγράφηκε.');
        resolve();
      } catch (error) {
        reject(error);
      } finally {
        document.body.removeChild(input);
      }
    });
  }

  function shareCurrentPage() {
    var payload = { title: currentToolTitle(), url: window.location.href };
    if (navigator.share) {
      navigator.share(payload).catch(function (error) {
        if (error && error.name === 'AbortError') return;
        copyCurrentUrl().catch(function () { showToast('Δεν ήταν δυνατή η κοινοποίηση.'); });
      });
      return;
    }
    copyCurrentUrl().catch(function () { showToast('Δεν ήταν δυνατή η αντιγραφή του συνδέσμου.'); });
  }

  function closeMenu() {
    var menu = document.querySelector('.edu-tools-global-menu[open]');
    if (menu) menu.removeAttribute('open');
  }

  function ensureInstallDialog() {
    var overlay = document.querySelector('.edu-install-help');
    if (overlay) return overlay;

    overlay = document.createElement('div');
    overlay.className = 'edu-install-help';
    overlay.hidden = true;
    overlay.innerHTML = '' +
      '<div class="edu-install-help__backdrop" data-edu-install-close></div>' +
      '<section class="edu-install-help__panel" role="dialog" aria-modal="true" aria-labelledby="eduInstallTitle">' +
        '<button type="button" class="edu-install-help__close" data-edu-install-close aria-label="Κλείσιμο">×</button>' +
        '<h2 id="eduInstallTitle">Εγκατάσταση στο κινητό</h2>' +
        '<div class="edu-install-help__content" data-edu-install-content></div>' +
      '</section>';
    document.body.appendChild(overlay);
    overlay.querySelectorAll('[data-edu-install-close]').forEach(function (button) {
      button.addEventListener('click', function () { overlay.hidden = true; });
    });
    document.addEventListener('keydown', function (event) {
      if (event.key === 'Escape' && !overlay.hidden) overlay.hidden = true;
    });
    return overlay;
  }

  function showInstallHelp() {
    var overlay = ensureInstallDialog();
    var content = overlay.querySelector('[data-edu-install-content]');
    var ua = navigator.userAgent || '';
    var isiOS = /iPad|iPhone|iPod/.test(ua) || (navigator.platform === 'MacIntel' && navigator.maxTouchPoints > 1);
    var isAndroid = /Android/i.test(ua);

    if (isiOS) {
      content.innerHTML = '<ol><li>Άνοιξε τη σελίδα στο <strong>Safari</strong>.</li><li>Πάτησε <strong>Κοινοποίηση</strong>.</li><li>Επίλεξε <strong>«Προσθήκη στην οθόνη Αφετηρίας»</strong>.</li><li>Άφησε ενεργό το <strong>«Άνοιγμα ως εφαρμογή ιστού»</strong> και πάτησε <strong>«Προσθήκη»</strong>.</li></ol>';
    } else if (isAndroid) {
      content.innerHTML = '<ol><li>Άνοιξε τη σελίδα στο <strong>Chrome</strong>.</li><li>Πάτησε το μενού <strong>⋮</strong>.</li><li>Επίλεξε <strong>«Εγκατάσταση εφαρμογής»</strong> ή <strong>«Προσθήκη στην αρχική οθόνη»</strong>.</li><li>Επιβεβαίωσε την εγκατάσταση.</li></ol>';
    } else {
      content.innerHTML = '<p>Αν ο browser σου υποστηρίζει εγκατάσταση εφαρμογών ιστού, αναζήτησε την επιλογή <strong>«Εγκατάσταση εφαρμογής»</strong> ή <strong>«Προσθήκη στην αρχική οθόνη»</strong> στο μενού του.</p>';
    }
    overlay.hidden = false;
    var closeButton = overlay.querySelector('.edu-install-help__close');
    if (closeButton) closeButton.focus();
  }

  function installApp() {
    closeMenu();
    if (isStandalone()) {
      showToast('Η Εργαλειοθήκη είναι ήδη εγκατεστημένη.');
      return;
    }
    if (deferredInstallPrompt) {
      var prompt = deferredInstallPrompt;
      deferredInstallPrompt = null;
      prompt.prompt();
      prompt.userChoice.then(function (choice) {
        if (choice && choice.outcome === 'accepted') showToast('Η εγκατάσταση ξεκίνησε.');
        updateInstallButtons();
      });
      return;
    }
    showInstallHelp();
  }

  function updateInstallButtons() {
    var installed = isStandalone();
    document.querySelectorAll('[data-edu-install]').forEach(function (button) {
      button.hidden = installed;
    });
  }

  function cardIndex() {
    var index = {};
    document.querySelectorAll('.tool-card[href]').forEach(function (card) {
      var href = card.getAttribute('href') || '';
      if (!href) return;
      var titleNode = card.querySelector('h2, h3');
      var tagNode = card.querySelector('.category-tag');
      index[href] = {
        href: href,
        title: titleNode ? titleNode.textContent.trim() : href,
        tag: tagNode ? tagNode.textContent.trim() : ''
      };
    });
    return index;
  }

  function createPersonalToolLink(tool) {
    var link = document.createElement('a');
    link.className = 'edu-personal-tool';
    link.href = tool.href;
    var text = document.createElement('span');
    text.className = 'edu-personal-tool__text';
    var strong = document.createElement('strong');
    strong.textContent = tool.title;
    text.appendChild(strong);
    if (tool.tag) {
      var small = document.createElement('small');
      small.textContent = tool.tag;
      text.appendChild(small);
    }
    var arrow = document.createElement('span');
    arrow.className = 'edu-personal-tool__arrow';
    arrow.setAttribute('aria-hidden', 'true');
    arrow.textContent = '→';
    link.appendChild(text);
    link.appendChild(arrow);
    return link;
  }

  function renderPersonalGroup(name, list, index) {
    var group = document.querySelector('[data-edu-personal-group="' + name + '"]');
    var host = document.querySelector('[data-edu-personal-list="' + name + '"]');
    if (!group || !host) return false;
    host.innerHTML = '';
    var rendered = 0;
    list.forEach(function (href) {
      if (!index[href]) return;
      host.appendChild(createPersonalToolLink(index[href]));
      rendered++;
    });
    group.hidden = rendered === 0;
    return rendered > 0;
  }

  function renderPersonalTools() {
    var section = document.querySelector('[data-edu-personal-tools]');
    if (!section) return;
    var index = cardIndex();
    var favorites = readList(FAVORITES_KEY);
    var favoriteSet = Object.create(null);
    favorites.forEach(function (href) { favoriteSet[href] = true; });
    var recents = readList(RECENTS_KEY).filter(function (href) { return !favoriteSet[href]; });
    var hasRecent = renderPersonalGroup('recent', recents, index);
    var hasFavorites = renderPersonalGroup('favorites', favorites, index);
    section.hidden = !(hasRecent || hasFavorites);
  }

  function bindActions() {
    var favoriteButton = document.querySelector('[data-edu-favorite-toggle]');
    if (favoriteButton) favoriteButton.addEventListener('click', function () { toggleFavorite(); closeMenu(); });

    document.querySelectorAll('[data-edu-share]').forEach(function (button) {
      button.addEventListener('click', function () { closeMenu(); shareCurrentPage(); });
    });
    document.querySelectorAll('[data-edu-install]').forEach(function (button) {
      button.addEventListener('click', installApp);
    });
  }

  window.addEventListener('beforeinstallprompt', function (event) {
    event.preventDefault();
    deferredInstallPrompt = event;
    updateInstallButtons();
  });
  window.addEventListener('appinstalled', function () {
    deferredInstallPrompt = null;
    updateInstallButtons();
    showToast('Η Εργαλειοθήκη εγκαταστάθηκε.');
  });

  function init() {
    trackRecentTool();
    updateFavoriteButton();
    updateInstallButtons();
    renderPersonalTools();
    bindActions();
  }

  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init);
  else init();
}());
