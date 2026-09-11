'use strict';
const fs=require('fs'),vm=require('vm'),path=require('path');
const ROOT=path.resolve(__dirname,'..');
let pass=0,fail=0;
function ok(cond,msg,extra=''){if(cond){pass++;console.log('PASS',msg)}else{fail++;console.error('FAIL',msg,extra)}}
function load(ctx,rel){vm.runInContext(fs.readFileSync(path.join(ROOT,rel),'utf8'),ctx,{filename:rel});}
class CL{constructor(){this.s=new Set()} add(x){this.s.add(x)} remove(x){this.s.delete(x)} toggle(x,on){if(arguments.length===1){this.s.has(x)?this.s.delete(x):this.s.add(x)}else on?this.s.add(x):this.s.delete(x)} contains(x){return this.s.has(x)}}
function el(attrs={}){return {attrs:{...attrs},value:'',textContent:'',innerHTML:'',style:{display:''},className:'',classList:new CL(),listeners:{},setAttribute(k,v){this.attrs[k]=String(v)},getAttribute(k){return this.attrs[k]??null},addEventListener(t,f){(this.listeners[t]??=[]).push(f)},scrollIntoView(){this.scrolled=true}}}

// 1) Tools directory: search and category filter still operate after extraction.
const toolbar=el({'data-initial-filter':'all'}), search=el(), results=el(), noResults=el();
const cards=[el({'data-group':'appointments','data-search':'ΑΣΕΠ μόρια'}),el({'data-group':'transfers','data-search':'μετάθεση αποσπάσεις'}),el({'data-group':'school','data-search':'ωρολόγιο πρόγραμμα'})];
cards[0].textContent='Υπολογισμός μορίων';cards[1].textContent='Μεταθέσεις';cards[2].textContent='Ωρολόγιο';
const groups=[el(),el(),el()]; groups.forEach((g,i)=>g.querySelector=()=>cards[i].classList.contains('hidden-card')?null:cards[i]);
const buttons=[el({'data-filter':'all'}),el({'data-filter':'transfers'})];
const links=[el({'data-directory-filter':'school'})];
const dctx={console,document:{getElementById:id=>({"tools-directory":toolbar,toolSearch:search,resultsLine:results,noResults:noResults}[id]||null),querySelectorAll(sel){return sel==='.tool-card'?cards:sel==='.tool-group'?groups:sel==='.filter-btn'?buttons:sel==='[data-directory-filter]'?links:[]}},Array,Set};dctx.window=dctx;vm.createContext(dctx);load(dctx,'assets/tools-directory.js');
ok(results.textContent.includes('3 εργαλεία'),'directory initial count preserved',results.textContent);
search.value='μεταθεση';search.listeners.input[0]();ok(!cards[1].classList.contains('hidden-card')&&cards[0].classList.contains('hidden-card'),'directory Greek search still filters cards');
search.value='';buttons[1].listeners.click[0]();ok(!cards[1].classList.contains('hidden-card')&&cards[2].classList.contains('hidden-card'),'directory category button still filters');
let prevented=false;links[0].listeners.click[0]({preventDefault(){prevented=true}});ok(prevented&&!cards[2].classList.contains('hidden-card')&&toolbar.scrolled===true,'directory category link filters and scrolls');

// 2) SAEK deputy eligibility: external buttons and decision flow.
const ids=['saek','status','requiredDegree','experience','evaluationRefusal','unsuitable','retirement','result','progressText','progressFill','checkEligibilityBtn','resetBtn'];
const E={};ids.forEach(id=>E[id]=el());
const sctx={console,document:{getElementById:id=>E[id]},Array};sctx.window=sctx;vm.createContext(sctx);load(sctx,'includes/saek-deputy-eligibility-ui.js');
ok((E.checkEligibilityBtn.listeners.click||[]).length===1&&(E.resetBtn.listeners.click||[]).length===1,'SAEK actions externally bound once');
Object.assign(E.saek,{value:'eligible'});Object.assign(E.status,{value:'yes'});Object.assign(E.requiredDegree,{value:'yes'});Object.assign(E.experience,{value:'yes'});Object.assign(E.evaluationRefusal,{value:'no'});Object.assign(E.unsuitable,{value:'no'});Object.assign(E.retirement,{value:'no'});
E.checkEligibilityBtn.listeners.click[0]();ok(E.result.className.includes('eligible')&&E.result.innerHTML.includes('πληροίς'),'SAEK eligible path preserved');
E.saek.value='other';E.checkEligibilityBtn.listeners.click[0]();ok(E.result.className.includes('not-eligible')&&E.result.innerHTML.includes('26 Σ.Α.Ε.Κ.'),'SAEK excluded-school path preserved');
E.resetBtn.listeners.click[0]();ok(E.progressText.textContent==='0/7 απαντήσεις'&&E.result.style.display==='none','SAEK reset/progress preserved');

// 3) European Schools canonical engine sanity: the UI still targets an unchanged engine.
const ectx={console};ectx.window=ectx;vm.createContext(ectx);load(ectx,'includes/education-core.js');load(ectx,'includes/european-schools-calculations.js');
let r=ectx.EuropeanSchools.calculate({position:'pe06_bru1',teachingYears:4,teachingMonths:0,teachingDays:0,ictLevel:'a',blockingIssue:'no'});
ok(r.eligibleBeforeInterview===true,'European Schools minimum eligibility remains valid',JSON.stringify(r));
r=ectx.EuropeanSchools.calculate({position:'pe06_bru1',teachingYears:3,ictLevel:'a',blockingIssue:'no'});
ok(r.eligibleBeforeInterview===false&&r.issues.some(x=>x.includes('4 πλήρη έτη')),'European Schools four-year minimum preserved');
const langs=ectx.EuropeanSchools.languagePoints({hostLanguageKey:'french',hostLanguageLevel:'c2',secondWorkingLanguage:'french',secondWorkingLevel:'c2'});
ok(langs.total===5&&langs.duplicateLanguages.includes('french'),'European Schools duplicate language is not double-counted',JSON.stringify(langs));

console.log(`RESULT ${pass} PASS / ${fail} FAIL`);process.exit(fail?1:0);
