'use strict';
// Regression tests for query-bearing public leave comparison links and
// client-side restoration of the selected leave from a canonical cached HTML.
const assert = require('node:assert/strict');
const fs = require('node:fs');
const vm = require('node:vm');
const path = require('node:path');
const root = path.resolve(__dirname, '..');
const prefix = 'https://example.org/subdir/';

async function testServiceWorker() {
  const handlers = {};
  const saved = new Map();
  let networkOnline = false;
  const fakeCache = {match: async (key) => saved.get(String(key))?.clone(),
    put: async (key, value) => saved.set(String(key), value)};
  const context = {
    URL, Set, Promise, Array, Request, Response,
    caches: {open: async () => fakeCache, keys: async () => []},
    self: {registration: {scope: prefix},
      addEventListener: (type, fn) => {handlers[type] = fn;},
      skipWaiting: () => {}, clients: {claim: async () => {}}},
    fetch: async (req) => {
      if (!networkOnline) throw new Error('offline');
      const target = typeof req === 'string' ? req : req.url;
      const resp = new Response('<html>public leave snapshot</html>', {
        status:200,headers:{'content-type':'text/html'}});
      // Model the same-origin, browser-fetched response fields.
      Object.defineProperties(resp, {type:{value:'basic'},url:{value:target}});
      return resp;
    },
    importScripts: (file) => vm.runInContext(fs.readFileSync(path.join(root, file), 'utf8'), sandbox)
  };
  const sandbox = vm.createContext(context);
  vm.runInContext(fs.readFileSync(path.join(root, 'service-worker.js'), 'utf8'), sandbox);

  const leavePages = ['adeies-ekpaideutikon.php','adeies-monimon.php','adeies-anapliroton.php','adeies-sygkrisi.php'];
  for (const name of leavePages) {
    saved.set(prefix + name, new Response('<html>public leave snapshot</html>', {
      status: 200, headers: {'content-type': 'text/html'}}));
  }
  async function go(relative, expected) {
    let resultPromise;
    handlers.fetch({request: {method: 'GET',mode: 'navigate', url: prefix + relative},
      respondWith: (promise) => {resultPromise=promise;}, waitUntil: () => {}});
    if (!expected) {
      assert.equal(resultPromise,undefined, 'Unsafe query/path intercepted: '+relative);
      return;
    }
    assert(resultPromise, 'Missing offline fallback for ' + relative);
    const resp = await resultPromise;
    assert.equal(resp.status,200, 'Missing offline response for '+relative);
    assert.match(await resp.text(), /public leave snapshot/);
  }
  for (const name of leavePages) await go(name,true);
  await go('adeies-sygkrisi.php?leave=kanoniki',true);
  await go('adeies-sygkrisi.php?leave=meiwmeno-anatrofis',true);
  await go('adeies-sygkrisi.php?leave=anatrofis',true);
  await go('adeies-sygkrisi.php?leave=x&token=private',false);
  await go('adeies-sygkrisi.php?leave=kanoniki&leave=gamos',false);
  await go('adeies-sygkrisi.php?leave=kanoniki%26private',false);
  await go('adeies-sygkrisi.php?session=abc',false);
  await go('adeies-monimon.php?private=1',false);
  await go('adeies-anapliroton.php?filter=foo',false);
  await go('kena-sxoleion-login.php',false);
  await go('ypologismos-didaktikon-anagkon.php',false);
  // Never create a cache entry keyed by a query string.
  assert.equal(saved.size,4);

  // Visiting a specific comparison ONLINE should also save the anonymous
  // canonical comparison so the subsequent offline deep link is available.
  saved.delete(prefix + 'adeies-sygkrisi.php');
  networkOnline = true;
  const pending = [];
  let onlineResponse;
  handlers.fetch({request: {method:'GET',mode:'navigate',url:prefix+'adeies-sygkrisi.php?leave=patrotitas'},
    respondWith: (value) => {onlineResponse=value;},
    waitUntil: (promise) => {pending.push(promise);}});
  assert.equal((await onlineResponse).status,200);
  await Promise.all(pending);
  assert(saved.has(prefix + 'adeies-sygkrisi.php'), 'Online deep link must cache query-free comparison');
  assert(!saved.has(prefix + 'adeies-sygkrisi.php?leave=patrotitas'), 'Do not cache query response');
  networkOnline = false;
  await go('adeies-sygkrisi.php?leave=patrotitas',true);
}

function testUi() {
  const source = fs.readFileSync(path.join(root, 'includes/leave-comparison-ui.js'), 'utf8');
  for (const [query, expected] of [
    ['?leave=anatrofis','anatrofis'],
    ['?leave=kanoniki','kanoniki'],
    ['?leave=unknown','kanoniki'],
    ['','kanoniki']
  ]) {
    const idList = ['kanoniki','anatrofis'];
    const panels = idList.map((id) => ({
      id,hidden:id !== 'kanoniki',
      getAttribute: (name) => name === 'data-leave-comparison' ? id : null,
      setAttribute: () => {}, querySelector: () => null
    }));
    const payload = Object.fromEntries(idList.map((id) => [id, {
      id,title:id,permanent:{pay:'yes',service:'yes', comparison:{duration:{kind:'fixed',base:3,unit:'days'}}},
      substitute:{pay:'yes',service:'yes',comparison:{duration:{kind:'fixed',base:3,unit:'days'}}}
    }]));
    const select = {value:'kanoniki',innerHTML:'',disabled:false,
      appendChild: () => {},addEventListener: () => {}};
    const viewSelect = {value:'all',addEventListener:()=>{}};
    const document = {
      getElementById: (id) => ({leaveCompareSelect:select,leaveCompareView:viewSelect,
        leaveComparisonData:{textContent:JSON.stringify(payload)}}[id] || null),
      querySelectorAll: (selector) => selector==='[data-leave-comparison]' ? panels : [],
      querySelector: (selector) => panels.find((p) => selector === '[data-leave-comparison="'+p.id+'"]') || null,
      createElement: () => ({value:'',textContent:''})
    };
    const window = {location:{href:prefix+'adeies-sygkrisi.php'+query},history:{replaceState:()=>{}},
      matchMedia:()=>({matches:true})};
    vm.runInNewContext(source,{document,window,URL,Array,JSON,Object,String,console});
    assert.equal(select.value,expected,'Incorrect active leave on '+query);
    assert.equal(panels.find(p=>!p.hidden).id,expected,'Incorrect visible comparison on '+query);
  }
}

(async () => {
  await testServiceWorker();
  testUi();
  console.log('PASS: four leave pages and query-specific comparison work offline; arbitrary queries stay excluded.');
})().catch((error) => {console.error(error);process.exitCode=1;});
