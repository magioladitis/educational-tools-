'use strict';
const assert=require('node:assert/strict');
const fs=require('node:fs');
const path=require('node:path');
const root=path.join(__dirname,'..');
const routes=require('../includes/transfer-primary-eae-routes.js');
const evaluator=require('../includes/transfer-primary-eae.js');
function evalRoute(mode,route,disability='none'){return routes.check({mode,route,disability});}
assert.equal(evalRoute('eae','general-same').status,'positive','General to EAE same area is an EAE transfer');
assert.equal(evalRoute('kedasy','general-same').status,'positive','General to KEDASY same area uses EAE route');
assert.equal(evalRoute('placement-eae','general-same').status,'problem','General to EAE same area is NOT an improvement');
assert.equal(evalRoute('eae','special-same').status,'problem','Within-area SMEAE/EEEΕK/TE transfer must be local improvement');
assert.equal(evalRoute('placement-eae','special-same').status,'positive','Within-area SMEAE/EEEΕK/TE transfer can be improvement');
assert.equal(evalRoute('placement-eae','special-other').status,'problem','Between area EAE is not local improvement');
assert.equal(evalRoute('kedasy','kedasy-to-kedasy').status,'positive','KEDASY to KEDASY permitted route');
assert.equal(evalRoute('eae','kedasy-to-kedasy').status,'problem','KEDASY to KEDASY uses distinct type');
assert.equal(evalRoute('sensory','deaf-exchange').visible,false,'Unsupported effective mode should not display route');
assert.equal(evalRoute('blind','deaf-exchange').status,'positive','Special school exchanges supported');
assert.match(evalRoute('kedasy','kedasy-to-kedasy','lifetime80').messages.join(' '),/ΔΕΝ εφαρμόζεται/);
assert.match(evalRoute('placement-eae','special-same','lifetime80').messages.join(' '),/υφιστάμενες κενές/);
assert.match(evalRoute('eae','general-other','lifetime80').messages.join(' '),/όχι ΚΕΔΑΣΥ/);
assert.equal(evalRoute('region','general-same','lifetime80').visible,false);
const basic={mode:'eae',branch:'ΠΕ70',origin:'general',generalYears:3,organic:'no',qualifications:{masters:true}};
assert.equal(evaluator.evaluate(basic).status,'problem','Without exception normal organic rule remains');
assert.notEqual(evaluator.evaluate({...basic,exception80:true}).status,'problem','Lifetime80 exception requires review rather than categorical rejection');
assert.equal(evaluator.evaluate({...basic,mode:'kedasy',exception80:false}).status,'problem','KEDASY not exempt');
const php=fs.readFileSync(path.join(root,'ypologismos-morion-metathesis-protovathmia.php'),'utf8');
const js=fs.readFileSync(path.join(root,'includes/transfer-primary.js'),'utf8');
for(const id of ['primaryEaeRouteFields','primaryEaeMoveRoute','primaryEaeDisability80','primaryEaeRouteStatus']){
 assert(php.includes('id="'+id+'"'),'Missing route field '+id);
 assert(js.includes(id),'Not wired '+id);
}
assert(php.includes('transfer-primary-eae-routes.js'),'Missing JS include');
assert(!php.includes('value="mutual"'),'Do not implement mutual Primary transfers');
assert(js.includes('exception80:') && js.includes('updateEaeRoute();'));
assert(js.includes("mode==='kedasy'?'Η κατ’ εξαίρεση μετάθεση δεν εφαρμόζεται στα ΚΕΔΑΣΥ."));
console.log('PASS EAE same-area routes, ≥80% exception boundaries, docs, isolated UI');
