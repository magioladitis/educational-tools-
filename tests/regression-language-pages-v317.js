'use strict';
const assert=require('assert');
const path=require('path');
const {runPage}=require('./browserless-page-harness');
const root=path.join(__dirname,'..');
const svc=path.join(root,'includes/service-calculations.js');
const soc=path.join(root,'includes/social-calculations.js');
const lang=path.join(root,'includes/language-calculations.js');
let passed=0;
function approxText(actual, expected, label){const n=Number(String(actual).replace(',','.').split(' ')[0]);assert.ok(Math.abs(n-expected)<1e-9,`${label}: expected ${expected}, got ${actual}`);passed++;}

let p=runPage(path.join(root,'tests/rendered-ypologismos-morion-1ea-2025-v317.html'),[svc,soc,lang]);
p.change('langName1','en');p.change('lang1','4');p.change('langName2','en');p.change('lang2','8');approxText(p.text('academicSubtotal'),8,'1EA duplicate language highest only');

p=runPage(path.join(root,'tests/rendered-ypologismos-morion-2ea-2025-v317.html'),[svc,soc,lang]);
p.change('langName1','fr');p.change('lang1','3');p.change('langName2','fr');p.change('lang2','7');approxText(p.text('academicSubtotal'),7,'2EA duplicate language highest only');

p=runPage(path.join(root,'tests/rendered-ypologismos-morion-3ea-2025-v317.html'),[svc,soc,lang]);
p.change('specialty','ΠΕ06');p.change('langName1','en');p.change('lang1','7');p.change('langName2','fr');p.change('lang2','5');approxText(p.text('resAcademic'),5,'3EA PE06 English excluded, French counted');

p=runPage(path.join(root,'tests/rendered-ypologismos-morion-apospasis-dimos-v317.html'),[lang]);
p.change('appointmentLanguage','en');p.change('languageName1','en');p.change('language1','3');p.change('languageName2','fr');p.change('language2','2');approxText(p.text('languageSubtotal'),2,'DIMOS appointment English excluded');

console.log(`PASS ${passed} page-level language v3.17 regression checks`);
