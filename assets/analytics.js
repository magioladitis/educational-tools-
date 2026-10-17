/* Aggregate active time; no cookies, identifiers, IPs, URLs, form fields or local/session storage. */
(function () {
  'use strict';
  var script = document.currentScript;
  var tool = script && script.getAttribute('data-edu-analytics-tool');
  if (!tool || !/^[a-z0-9-]+\.php$/.test(tool)) return;
  if ((navigator.doNotTrack || '') === '1' || navigator.globalPrivacyControl === true) return;
  var target = 'analytics-collect.php';
  var tickMs = 5000;
  var lastTick = performance.now();
  var lastActivity = performance.now();
  var collected = 0;
  var lastSent = performance.now();
  var active = document.visibilityState === 'visible' && document.hasFocus();

  function interact() { lastActivity = performance.now(); }
  ['pointerdown','keydown','scroll','touchstart'].forEach(function (event) {
    window.addEventListener(event, interact, { passive: true });
  });
  window.addEventListener('focus', function () { active = true; lastTick = performance.now(); interact(); });
  window.addEventListener('blur', function () { active = false; flush(); });
  document.addEventListener('visibilitychange', function () {
    if (document.visibilityState !== 'visible') { active = false; flush(); }
    else { active = document.hasFocus(); lastTick = performance.now(); interact(); }
  });

  function flush() {
    var seconds = Math.min(30, Math.floor(collected));
    if (seconds < 1) return;
    collected -= seconds;
    var body = JSON.stringify({tool: tool, seconds: seconds});
    if (navigator.sendBeacon) {
      var blob = new Blob([body], { type: 'application/json' });
      if (navigator.sendBeacon(target, blob)) return;
    }
    try { fetch(target, {method:'POST',headers:{'Content-Type':'application/json'},body:body,credentials:'omit',keepalive:true}); }
    catch (e) { /* best effort */ }
  }
  setInterval(function () {
    var now = performance.now();
    var diff = Math.min(tickMs / 1000, Math.max(0, (now - lastTick) / 1000));
    // Counts visible/focused time only while the visitor interacted in the past 60s.
    if (active && document.visibilityState === 'visible' && document.hasFocus() && now - lastActivity < 60000) {
      collected += diff;
    }
    lastTick = now;
    if (collected >= 25 || now - lastSent >= 30000) { flush(); lastSent = now; }
  }, tickMs);
  window.addEventListener('pagehide', flush);
}());
