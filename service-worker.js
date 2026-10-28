/* Educational Tools — public offline snapshots, no authenticated/server data. */
'use strict';
importScripts('assets/offline-registry.js');

const REVISION = '3.22.207';
const STATIC_PREFIX = 'edu-tools-static-';
const PAGE_PREFIX = 'edu-tools-pages-';
const CACHE_NAME = STATIC_PREFIX + REVISION;
const PAGE_CACHE = PAGE_PREFIX + REVISION;
const scopeURL = new URL(self.registration.scope);
const publicPages = new Set(self.EDU_OFFLINE_REGISTRY.pages);
const transferPair = [
  'ypologismos-morion-metathesis.php',
  'ypologismos-morion-metathesis-protovathmia.php'
];
const staticExt = /\.(?:css|js|png|svg|ico|webp|jpg|jpeg|gif|woff2?|webmanifest)$/i;

function scopedName(url) {
  const path = url.pathname;
  if (url.origin !== scopeURL.origin || !path.startsWith(scopeURL.pathname)) return null;
  return path.slice(scopeURL.pathname.length);
}
function allowedAsset(url) {
  const rel = scopedName(url);
  return !!rel && staticExt.test(url.pathname) &&
    (rel.startsWith('assets/') || rel.startsWith('includes/') ||
      rel === 'favicon.ico' || rel === 'manifest.webmanifest');
}
function allowedResponse(response) {
  return !!response && response.ok && response.type === 'basic' &&
    !response.headers.has('set-cookie') &&
    !/(?:private|no-store)/i.test(response.headers.get('cache-control') || '') &&
    !(response.headers.get('content-type') || '').match(/(?:application\/json)/i);
}
function fetchAnonymous(url) {
  // Header prevents silent prefetches from counting as separate tool visits.
  return fetch(new Request(url, {
    method: 'GET', credentials: 'omit', redirect: 'follow',
    headers: {'X-Edu-Offline-Prefetch': '1'}, cache: 'no-store'
  }));
}
function pageURL(name) { return new URL(name, scopeURL).href; }

// Only these non-personal, presentation-only query strings can reuse a public
// offline snapshot. Leave comparisons are selected client-side from the
// existing public comparison data; query-bearing responses are never cached.
function allowedPageQuery(rel, url) {
  if (!url.search) return true;
  if (rel === 'ergaleia.php') {
    return /^\?group=(?:asep-anaplirotes|eidiki-agogi|metakiniseis|ypiresiaka|eidikes-domes)$/.test(url.search);
  }
  if (rel === 'adeies-sygkrisi.php') {
    // The UI's public comparison IDs are ASCII slugs, e.g. ?leave=kanoniki.
    // Reject extra parameters, duplicates and encoded/unexpected values.
    return /^\?leave=[a-z0-9][a-z0-9-]{0,63}$/.test(url.search);
  }
  return false;
}

function assetUrlsFromHTML(html, baseUrl) {
  const urls = new Set();
  const tags = html.match(/<(?:script|link|img)\b[^>]*>/gi) || [];
  tags.forEach(function (tag) {
    const match = tag.match(/\b(?:src|href)\s*=\s*(["'])(.*?)\1/i);
    if (!match) return;
    try {
      const url = new URL(match[2].replace(/&amp;/g, '&'), baseUrl);
      if (allowedAsset(url)) urls.add(url.href);
    } catch (_) { /* Ignore unsupported or invalid references. */ }
  });
  return Array.from(urls);
}
async function cacheStatic(url, cache) {
  if (await cache.match(url)) return;
  const response = await fetchAnonymous(url);
  if (!allowedResponse(response)) throw new Error('Μη διαθέσιμο στατικό αρχείο');
  await cache.put(url, response);
}
async function savePublicPage(name) {
  if (!publicPages.has(name)) throw new Error('Η σελίδα δεν επιτρέπεται offline');
  const url = pageURL(name);
  const response = await fetchAnonymous(url);
  if (!allowedResponse(response) || !/text\/html/i.test(response.headers.get('content-type') || '')) {
    throw new Error('Μη αποθηκεύσιμη απάντηση');
  }
  const html = await response.clone().text();
  // Avoid caching an error/login/redirect destination in place of a tool page.
  if (new URL(response.url).pathname !== new URL(url).pathname) {
    throw new Error('Ανακατεύθυνση σε άλλη σελίδα');
  }
  const assetCache = await caches.open(CACHE_NAME);
  const deps = assetUrlsFromHTML(html, url);
  const missing = [];
  // Failures must be surfaced instead of claiming the offline bundle is complete.
  for (const dep of deps) {
    try { await cacheStatic(dep, assetCache); }
    catch (_) {
      if (!(await assetCache.match(dep))) missing.push(dep);
    }
  }
  if (missing.length) throw new Error('Λείπουν ' + missing.length + ' εξαρτήσεις');
  // Commit page only when its immediate same-origin JS/CSS dependencies exist.
  const pageCache = await caches.open(PAGE_CACHE);
  await pageCache.put(url, response);
}

async function status() {
  const cache = await caches.open(PAGE_CACHE);
  const saved = [];
  for (const name of publicPages) {
    if (await cache.match(pageURL(name))) saved.push(name);
  }
  return {type: 'EDU_OFFLINE_STATUS', revision: REVISION,
    saved: saved, total: publicPages.size,
    excluded: self.EDU_OFFLINE_REGISTRY.excluded};
}
async function notify(client, data) {
  if (client && typeof client.postMessage === 'function') client.postMessage(data);
}
async function prefetchAll(client) {
  let completed = 0, failed = 0;
  const errors = [];
  const names = Array.from(publicPages);
  await notify(client, {type:'EDU_OFFLINE_PROGRESS', done:0, total:names.length, failed:0});
  for (const name of names) {
    try { await savePublicPage(name); }
    catch (error) { failed++; errors.push(name + ': ' + String(error && error.message || error)); }
    completed++;
    await notify(client, {type:'EDU_OFFLINE_PROGRESS', done:completed,
      total:names.length, failed:failed, current:name});
  }
  await notify(client, {type:'EDU_OFFLINE_FINISHED', done:completed,
    total:names.length, failed:failed, errors:errors.slice(0,10)});
  await notify(client, await status());
}

self.addEventListener('install', function (event) {
  self.skipWaiting();
});
self.addEventListener('activate', function (event) {
  event.waitUntil(caches.keys().then(function (keys) {
    return Promise.all(keys.map(function (key) {
      if ((key.startsWith(STATIC_PREFIX) && key !== CACHE_NAME) ||
          (key.startsWith(PAGE_PREFIX) && key !== PAGE_CACHE)) return caches.delete(key);
      return Promise.resolve();
    }));
  }).then(function () { return self.clients.claim(); }));
});

self.addEventListener('message', function (event) {
  if (!event.data || !event.source) return;
  if (event.data.type === 'EDU_OFFLINE_GET_STATUS') {
    event.waitUntil(status().then(function (value) { return notify(event.source, value); }));
  }
  if (event.data.type === 'EDU_OFFLINE_SAVE_ALL') {
    event.waitUntil(prefetchAll(event.source));
  }
});

self.addEventListener('fetch', function (event) {
  const request = event.request;
  if (request.method !== 'GET') return;
  const url = new URL(request.url);
  const rel = scopedName(url);
  if (rel === null) return;

  if (request.mode === 'navigate') {
    // Only explicitly whitelisted public pages and presentation-only query
    // strings. Offline responses always come from the canonical base URL.
    if (!publicPages.has(rel) || !allowedPageQuery(rel, url)) return;

    event.respondWith(fetch(request).then(function (response) {
      // A comparison deep link is still a public read-only page: snapshot
      // its canonical URL so that a later offline reload works with ?leave=.
      // Other query-bearing responses remain network-only by default.
      if (response && response.ok && (!url.search || (rel === 'adeies-sygkrisi.php' && allowedPageQuery(rel, url)))) {
        event.waitUntil(savePublicPage(rel).then(function () {
          if (transferPair.indexOf(rel) !== -1) {
            return savePublicPage(transferPair[rel === transferPair[0] ? 1 : 0]);
          }
        }).catch(function () { /* Normal online navigation remains usable. */ }));
      }
      return response;
    }).catch(function () {
      return caches.open(PAGE_CACHE).then(function (cache) {
        return cache.match(pageURL(rel)).then(function (saved) {
          return saved || Response.error();
        });
      });
    }));
    return;
  }

  // Only public static assets are retained. No API, admin, CSV, JSON, PHP,
  // POST or user-specific endpoint responses are ever stored by this worker.
  if (!allowedAsset(url)) return;
  event.respondWith(caches.open(CACHE_NAME).then(function (cache) {
    return fetch(request).then(function (response) {
      if (allowedResponse(response)) {
        event.waitUntil(cache.put(request, response.clone()));
      }
      return response;
    }).catch(function () {
      return cache.match(request).then(function (saved) { return saved || Response.error(); });
    });
  }));
});
