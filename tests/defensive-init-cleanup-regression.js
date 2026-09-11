'use strict';
const fs = require('fs');
const path = require('path');
const vm = require('vm');
const ROOT = path.resolve(__dirname, '..');
let failed = false;
function ok(label, condition) {
  if (condition) console.log('PASS | ' + label);
  else { console.error('FAIL | ' + label); failed = true; }
}
function load(rel, ctx) {
  vm.runInContext(fs.readFileSync(path.join(ROOT, rel), 'utf8'), ctx, { filename: rel });
}

// 1) Children/disability helpers must tolerate optional DOM pieces being absent.
{
  const elements = {
    criterion: { value: 'disability', addEventListener() {} },
    disabilityPerson: { value: 'spouse', addEventListener() {} },
    disabilityPercent: { value: '', addEventListener() {} },
    showDocumentsBtn: { addEventListener() {} }
  };
  const ctx = {
    console,
    document: {
      getElementById(id) { return elements[id] || null; },
      querySelectorAll() { return []; }
    }
  };
  vm.createContext(ctx);
  let loaded = true;
  try { load('includes/children-disability-documents-ui.js', ctx); } catch (e) { loaded = false; }
  ok('children/disability module loads with optional panels missing', loaded);
  let visibilitySafe = true;
  try { ctx.updateVisibility(); } catch (e) { visibilitySafe = false; }
  ok('updateVisibility tolerates missing optional panels/result', visibilitySafe);
  let personSafe = true;
  try { ctx.updateDisabilityPersonUI(); } catch (e) { personSafe = false; }
  ok('updateDisabilityPersonUI tolerates missing spouse/candidate panels', personSafe);
  delete elements.criterion;
  let documentsSafe = true;
  try { ctx.showDocuments(); } catch (e) { documentsSafe = false; }
  ok('showDocuments tolerates missing criterion/result nodes', documentsSafe);
  ok('missing value resolves to empty string', ctx.valueOf('doesNotExist') === '');
}

function checkReadyStateModule(rel, apiName, componentSelector) {
  // loading: no eager scan; exactly one once-only DOMContentLoaded listener.
  let queryCount = 0;
  let domHandler = null;
  let domOptions = null;
  const loadingCtx = {
    console,
    CustomEvent: function () {},
    window: null,
    document: {
      readyState: 'loading',
      getElementById() { return null; },
      querySelectorAll(selector) { if (selector === componentSelector) queryCount++; return []; },
      addEventListener(name, fn, options) { if (name === 'DOMContentLoaded') { domHandler = fn; domOptions = options; } }
    }
  };
  loadingCtx.window = loadingCtx;
  vm.createContext(loadingCtx);
  load(rel, loadingCtx);
  ok(apiName + ' does not eagerly scan while document is loading', queryCount === 0);
  ok(apiName + ' registers DOMContentLoaded initializer', typeof domHandler === 'function');
  ok(apiName + ' DOMContentLoaded listener is once-only', !!(domOptions && domOptions.once === true));
  domHandler();
  ok(apiName + ' scans once when DOMContentLoaded fires', queryCount === 1);

  // complete: initialize immediately, no DOMContentLoaded listener needed.
  queryCount = 0;
  let listenerCount = 0;
  const completeCtx = {
    console,
    CustomEvent: function () {},
    window: null,
    document: {
      readyState: 'complete',
      getElementById() { return null; },
      querySelectorAll(selector) { if (selector === componentSelector) queryCount++; return []; },
      addEventListener(name) { if (name === 'DOMContentLoaded') listenerCount++; }
    }
  };
  completeCtx.window = completeCtx;
  vm.createContext(completeCtx);
  load(rel, completeCtx);
  ok(apiName + ' initializes immediately after DOM is ready', queryCount === 1);
  ok(apiName + ' does not register late DOMContentLoaded listener', listenerCount === 0);
}

// 2) Avoid the previous eager-init + DOMContentLoaded double scan.
checkReadyStateModule('includes/asep-te-academic.js', 'TE academic', '[data-component="asep-te-academic"]');
checkReadyStateModule('includes/asep-language-selector.js', 'Language selector', '[data-component="asep-language-selector"]');

// 3) Detachment initial diagnostic must read one coherent input snapshot.
{
  let appointmentReads = 0;
  const studyBox = { innerHTML: '', className: '' };
  const ctx = {
    console,
    Intl,
    Number,
    Math,
    window: null,
    scrollTo() {},
    document: {
      readyState: 'complete',
      querySelector(selector) { return selector === '.edu-page-detachment' ? {} : null; },
      querySelectorAll() { return []; },
      addEventListener() {},
      getElementById(id) {
        if (id === 'appointmentStatus') appointmentReads++;
        if (id === 'studyPointsStatus') return studyBox;
        return null;
      }
    },
    EducationDetachment: {
      calculate(input) { return { studiesPoints: 0 }; }
    }
  };
  ctx.window = ctx;
  vm.createContext(ctx);
  load('includes/detachment-ui.js', ctx);
  ok('detachment init reads appointmentStatus exactly once', appointmentReads === 1);
  ok('detachment UI API remains available', !!(ctx.EducationDetachmentUI && typeof ctx.EducationDetachmentUI.calculate === 'function'));
}

if (failed) process.exit(1);
