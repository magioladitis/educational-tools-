/* One compact warning for ALL cached public pages, not only transfers. */
(function () {
  'use strict';
  function update() {
    var notice = document.getElementById('edu-offline-notice');
    if (notice) notice.hidden = navigator.onLine;
  }
  function init() {
    if (!document.body || document.getElementById('edu-offline-notice')) return;
    var notice = document.createElement('div');
    notice.id = 'edu-offline-notice';
    notice.className = 'edu-offline-notice';
    notice.setAttribute('role', 'status');
    var details = document.createElement('details');
    var summary = document.createElement('summary');
    summary.textContent = 'Εκτός σύνδεσης · Αποθηκευμένη έκδοση';
    var body = document.createElement('p');
    body.textContent = 'Οι εγκύκλιοι, οι προθεσμίες και οι προϋποθέσεις μπορεί να έχουν αλλάξει. Ελέγξτε τις επίσημες πηγές όταν συνδεθείτε. Ορισμένες λειτουργίες απαιτούν Internet.';
    details.appendChild(summary);
    details.appendChild(body);
    notice.appendChild(details);
    notice.hidden = true;
    document.body.insertBefore(notice, document.body.firstChild);
    update();
    window.addEventListener('online', update);
    window.addEventListener('offline', update);
  }
  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init);
  else init();
}());
