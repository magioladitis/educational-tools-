/* Educational Tools — conservative static-asset service worker. */
'use strict';

const CACHE_PREFIX = 'edu-tools-static-';
const CACHE_NAME = CACHE_PREFIX + '3.22.199';
const PAGE_CACHE = 'edu-tools-pages-3.22.199';
const PAGE_CACHE_PREFIX = 'edu-tools-pages-';
const OFFLINE_PAGES = new Set([
  '/ergaleia.php',
  '/ypologismos-morion-metathesis.php',
  '/ypologismos-morion-metathesis-protovathmia.php'
]);
const STATIC_ASSET_RE = /\.(?:css|js|png|svg|ico|webp|jpg|jpeg|gif|woff2?|webmanifest)$/i;

self.addEventListener('install', function () {
  self.skipWaiting();
});

self.addEventListener('activate', function (event) {
  event.waitUntil(
    caches.keys()
      .then(function (keys) {
        return Promise.all(keys.map(function (key) {
          if ((key.indexOf(CACHE_PREFIX) === 0 && key !== CACHE_NAME) ||
              (key.indexOf(PAGE_CACHE_PREFIX) === 0 && key !== PAGE_CACHE)) {
            return caches.delete(key);
          }
          return null;
        }));
      })
      .then(function () {
        return self.clients.claim();
      })
  );
});

self.addEventListener('fetch', function (event) {
  const request = event.request;
  if (request.method !== 'GET') return;

  const url = new URL(request.url);
  if (url.origin !== self.location.origin) return;

  // Only these three public, anonymous pages may be used as offline snapshots.
  // No query-string URLs, POSTs, private content or other PHP endpoints are cached.
  const pagePath = url.pathname.replace(/^\/[^/]+(?=\/)/, '');
  if (request.mode === 'navigate' && !url.search && OFFLINE_PAGES.has(pagePath)) {
    event.respondWith(
      fetch(request).then(function (response) {
        if (response && response.ok && response.type === 'basic' &&
            !response.headers.has('set-cookie') &&
            !(response.headers.get('cache-control') || '').match(/private|no-store/i)) {
          const snapshot = response.clone();
          event.waitUntil(caches.open(PAGE_CACHE).then(function (cache) {
            return cache.put(request, snapshot);
          }));
        }
        return response;
      }).catch(function () {
        return caches.open(PAGE_CACHE).then(function (cache) {
          return cache.match(request).then(function (saved) {
            return saved || Response.error();
          });
        });
      })
    );
    return;
  }
  // All other PHP and HTML navigations stay network-only.
  if (request.mode === 'navigate') return;

  // Never cache navigations/PHP/HTML/dynamic requests. Only static assets get
  // an offline fallback, using network-first so releases are seen immediately.
  if (!STATIC_ASSET_RE.test(url.pathname)) return;

  event.respondWith(
    caches.open(CACHE_NAME).then(function (cache) {
      return fetch(request)
        .then(function (response) {
          if (response && response.ok && response.type === 'basic') {
            cache.put(request, response.clone());
          }
          return response;
        })
        .catch(function () {
          return cache.match(request).then(function (cached) {
            return cached || Response.error();
          });
        });
    })
  );
});
