/* Educational Tools — conservative static-asset service worker. */
'use strict';

const CACHE_PREFIX = 'edu-tools-static-';
const CACHE_NAME = CACHE_PREFIX + '3.22.12';
const STATIC_ASSET_RE = /\.(?:css|js|png|svg|ico|webp|jpg|jpeg|gif|woff2?|webmanifest)$/i;

self.addEventListener('install', function () {
  self.skipWaiting();
});

self.addEventListener('activate', function (event) {
  event.waitUntil(
    caches.keys()
      .then(function (keys) {
        return Promise.all(keys.map(function (key) {
          if (key.indexOf(CACHE_PREFIX) === 0 && key !== CACHE_NAME) {
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
