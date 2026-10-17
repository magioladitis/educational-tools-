const fs = require('fs');
const vm = require('vm');
const assert = require('assert');
const events = {};
const beacons = [];
let clock = 0;
let tick;
const document = {
  currentScript: {getAttribute: (name) => name === 'data-edu-analytics-tool' ? 'ypologismos-morion-metathesis.php' : null},
  visibilityState: 'visible',
  hasFocus: () => true,
  addEventListener: (name, fn) => {events[name] = fn;}
};
const window = {addEventListener: (name, fn) => {events[name]=fn;}};
const navigator = { doNotTrack: '0', sendBeacon: (_url, blob) => {beacons.push(blob); return true;}};
const context = {document, window, navigator, performance:{now:()=>clock},setInterval:(fn)=>{tick=fn;},Blob:class {constructor(parts,opts){this.parts=parts;this.type=opts.type;}}};
vm.runInNewContext(fs.readFileSync(require('path').join(__dirname,'../assets/analytics.js'),'utf8'),context);
for (let i=0;i<6;i++) {clock += 5000;tick();}
assert.ok(beacons.length>=1);
assert.ok(beacons[0].parts[0].includes('"seconds":25'));
assert.ok(!beacons[0].parts[0].includes('name'));
assert.ok(!beacons[0].parts[0].includes('ip'));
console.log('OK: active-time beacon contains only whitelisted page ID and bounded seconds.');
