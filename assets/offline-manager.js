/* Opt-in, public page offline library. No personal files or server data. */
(function () {
  'use strict';
  var button = document.getElementById('eduOfflineSaveAll');
  var status = document.getElementById('eduOfflineManagerStatus');
  if (!button || !status) return;
  if (!('serviceWorker' in navigator) || (location.protocol !== 'https:' &&
      location.hostname !== 'localhost' && location.hostname !== '127.0.0.1')) {
    button.disabled = true;
    status.textContent = 'Η offline αποθήκευση απαιτεί ασφαλή σύνδεση HTTPS και συμβατό πρόγραμμα περιήγησης.';
    return;
  }
  var running = false;
  function report(message) { status.textContent = message; }
  function targetWorker() {
    return navigator.serviceWorker.ready.then(function (registration) {
      return registration.active || navigator.serviceWorker.controller;
    });
  }
  function send(type) {
    return targetWorker().then(function (worker) {
      if (!worker) throw new Error('Το offline εργαλείο δεν είναι ενεργό');
      worker.postMessage({type:type});
    });
  }
  function refresh() {
    send('EDU_OFFLINE_GET_STATUS').catch(function () {
      report('Αδυναμία ανάγνωσης offline διαθεσιμότητας.');
    });
  }
  navigator.serviceWorker.addEventListener('message', function (event) {
    var data = event.data || {};
    if (data.type === 'EDU_OFFLINE_STATUS' && !running) {
      report('Αποθηκευμένες σελίδες: ' + data.saved.length + ' / ' + data.total +
        '. Τα εργαλεία με σύνδεση χρήστη παραμένουν online.');
    } else if (data.type === 'EDU_OFFLINE_PROGRESS') {
      report('Αποθήκευση: ' + data.done + ' / ' + data.total +
        (data.failed ? ' · Αποτυχίες: ' + data.failed : '') + '.');
    } else if (data.type === 'EDU_OFFLINE_FINISHED') {
      running = false;
      button.disabled = !navigator.onLine;
      button.textContent = 'Ενημέρωση offline εργαλείων';
      report(data.failed ?
        'Ολοκληρώθηκε με ' + data.failed + ' προβλήματα. Μερικές σελίδες δεν είναι διαθέσιμες offline. Συνδεθείτε ξανά και δοκιμάστε ενημέρωση.' :
        'Οι ' + data.done + ' δημόσιες σελίδες αποθηκεύτηκαν. Μπορείτε να τις ανοίγετε χωρίς σύνδεση (εξωτερικοί σύνδεσμοι απαιτούν Internet).');
    }
  });
  button.addEventListener('click', function () {
    if (running || !navigator.onLine) return;
    running = true;
    button.disabled = true;
    report('Ετοιμασία αποθήκευσης δημόσιων σελίδων…');
    send('EDU_OFFLINE_SAVE_ALL').catch(function () {
      running = false;
      button.disabled = false;
      report('Δεν ξεκίνησε η αποθήκευση. Δοκιμάστε ξανά με σύνδεση.');
    });
  });
  window.addEventListener('online', function () {
    if (!running) button.disabled = false;
    refresh();
  });
  window.addEventListener('offline', function () {
    button.disabled = true;
    if (!running) refresh();
  });
  if (!navigator.onLine) button.disabled = true;
  refresh();
}());
