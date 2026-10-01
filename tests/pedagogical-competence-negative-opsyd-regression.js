const fs = require('fs');
const vm = require('vm');
const source = fs.readFileSync(__dirname + '/../includes/pedagogical-competence-ui.js', 'utf8');
const fakeDocument = {
  readyState: 'loading',
  addEventListener(){},
  getElementById(){ return null; },
  querySelectorAll(){ return []; }
};
const context = { window: {}, document: fakeDocument, console };
context.window.window = context.window;
vm.createContext(context);
vm.runInContext(source, context);
const ui = context.window.PedagogicalCompetenceUI;
function assert(cond, msg){ if(!cond){ console.error('FAIL:', msg); process.exit(1); } console.log('PASS:', msg); }
let e = ui.evaluateCredentialValues({proofType:'epath', epathDate:'after'});
assert(e.status === 'negative', 'EPATH on/after 12/06/2018 is a definitive negative route');
e = ui.evaluateCredentialValues({proofType:'professor_school', entryYear:'from_2015', graduationYear:'from_2018'});
assert(e.status === 'negative', 'professor-school late entry/late graduation is a definitive negative route');
let agg = ui.aggregateEvaluations([ui.evaluateCredentialValues({proofType:'epath', epathDate:'after'})]);
assert(agg.status === 'negative', 'a sole definitive negative route yields a negative aggregate');
agg = ui.aggregateEvaluations([
  ui.evaluateCredentialValues({proofType:'epath', epathDate:'after'}),
  ui.evaluateCredentialValues({proofType:'aspaite_eppaik'})
]);
assert(agg.status === 'positive' && agg.positives.length === 1 && agg.negatives.length === 1, 'a second positive credential still overrides a negative route');
console.log('4/4 negative/OPSYD status regression checks passed');
