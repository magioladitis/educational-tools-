const assert = require('node:assert/strict');
const fs = require('node:fs');
const vm = require('node:vm');
const path = require('node:path');
const root = path.resolve(__dirname, '..');
const handlers = {};
const entries = new Map();
const fakeCache = {match: async (key) => entries.get(String(key)) || undefined,
  put: async (key, value) => entries.set(String(key),value)};
const context = {
  URL, Set, Promise, Array, Request, Response, console,
  caches:{open: async () => fakeCache,keys:async()=>[]},
  self:{registration:{scope:'https://example.org/educational-tools/'},
    addEventListener:(type,fn)=>handlers[type]=fn,
    skipWaiting:()=>{},clients:{claim:async()=>{}}},
  fetch: async()=>{throw new Error('offline')},
  importScripts:(file)=>vm.runInContext(fs.readFileSync(path.join(root,file),'utf8'), sandbox),
};
const sandbox = vm.createContext(context);
vm.runInContext(fs.readFileSync(path.join(root,'service-worker.js'),'utf8'),sandbox);
assert.equal(vm.runInContext("scopedName(new URL('https://example.org/educational-tools/ergaleia.php'))",sandbox),'ergaleia.php');
assert.equal(vm.runInContext("allowedAsset(new URL('https://example.org/educational-tools/assets/common.js?v=2'))",sandbox),true);
assert.equal(vm.runInContext("allowedAsset(new URL('https://example.org/educational-tools/data/private.csv'))",sandbox),false);
assert.equal(vm.runInContext("allowedAsset(new URL('https://example.org/educational-tools/kena-sxoleion-password.php'))",sandbox),false);
async function offlineNavigation(filename, expectedResponse) {
  let resp;
  const event={request:{method:'GET',mode:'navigate',url:'https://example.org/educational-tools/'+filename},
    respondWith:(value)=>{resp=value},waitUntil:()=>{}};
  handlers.fetch(event);
  if (!expectedResponse) {assert.equal(resp,undefined,'must not intercept '+filename);return}
  assert(resp,'must intercept '+filename);
  const result=await resp;
  assert.equal(result.status,200);
}
(async()=>{
  const cached=new Response('<html>cached</html>',{status:200});
  entries.set('https://example.org/educational-tools/ergaleia.php',cached);
  await offlineNavigation('ergaleia.php?group=eidiki-agogi',true);
  await offlineNavigation('kena-sxoleion-login.php',false);
  await offlineNavigation('kena-sxoleion-admin.php',false);
  await offlineNavigation('ergaleia.php?secret=1',false);
  await offlineNavigation('ypologismos-didaktikon-anagkon.php',false);
  console.log('PASS: nested scope, safe assets, catalog filtered navigation, excluded endpoints.');
})().catch(e=>{console.error(e);process.exitCode=1});
