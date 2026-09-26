/* Educational Tools — shared PWA bootstrap. */
(function () {
  'use strict';

  if (!('serviceWorker' in navigator)) return;

  var isLocalhost = window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1';
  if (window.location.protocol !== 'https:' && !isLocalhost) return;

  window.addEventListener('load', function () {
    navigator.serviceWorker.register('service-worker.js', { scope: './', updateViaCache: 'none' }).catch(function () {
      /* Progressive enhancement: the tools remain fully usable without SW. */
    });
  });
}());
