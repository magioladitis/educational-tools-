/* User-visible warning for cached offline transfer guidance. */
(function () {
  'use strict';
  function update() {
    var notice = document.getElementById('edu-offline-notice');
    if (!notice) return;
    notice.hidden = navigator.onLine;
  }
  function init() {
    if (!document.body) return;
    var notice = document.createElement('div');
    notice.id = 'edu-offline-notice';
    notice.setAttribute('role', 'status');
    notice.style.cssText = 'position:sticky;top:0;z-index:9999;padding:12px 18px;background:#fff0c2;color:#493400;border-bottom:2px solid #b26c00;font:600 15px/1.5 system-ui,sans-serif;text-align:center';
    notice.textContent = 'Εκτός σύνδεσης: βλέπετε αποθηκευμένη έκδοση. Οι εγκύκλιοι, προθεσμίες και προϋποθέσεις μπορεί να έχουν αλλάξει. Ελέγξτε τις επίσημες πηγές όταν συνδεθείτε.';
    notice.hidden = true;
    document.body.insertBefore(notice, document.body.firstChild);
    update();
    window.addEventListener('online', update);
    window.addEventListener('offline', update);
  }
  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init);
  else init();
}());
