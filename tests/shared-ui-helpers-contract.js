const fs=require('fs'),vm=require('vm'),path=require('path');
let pass=0,fail=0; function check(n,c){if(c){console.log('PASS '+n);pass++;}else{console.log('FAIL '+n);fail++;}}
const ctx={globalThis:{},window:undefined,console}; ctx.globalThis=ctx; vm.createContext(ctx);
vm.runInContext(fs.readFileSync(path.join(__dirname,'../includes/education-core.js'),'utf8'),ctx);
const C=ctx.EducationCore;
check('parse comma decimal',C.parseNumber('7,5',0)===7.5);
check('parse invalid fallback',C.parseNumber('x',4)===4);
check('round two digits',C.roundNumber(1.235,2)===1.24);
check('format points fixed two decimals',C.formatPoints(12.5)==='12,50');
check('summary removes empty lines',C.summaryLines(['A','',null,'B'])==='A\nB');
function el(type,value){return {type,value:value||'',checked:true,selectedIndex:3};}
const nums=[el('number','12')],texts=[el('text','abc')],checks=[el('checkbox'),el('radio')],selects=[el('select')];
const root={querySelectorAll(sel){if(sel==='input[type="number"]')return nums;if(sel==='input[type="text"]')return texts;if(sel==='input[type="checkbox"], input[type="radio"]')return checks;if(sel==='select')return selects;return [];}};
C.resetControls(root,{numberValue:'0',textValue:'',resetSelects:true});
check('reset number inputs',nums[0].value==='0');
check('reset text inputs',texts[0].value==='');
check('reset checks',checks.every(x=>x.checked===false));
check('reset selects',selects[0].selectedIndex===0);
console.log(`RESULT ${pass} PASS / ${fail} FAIL`); process.exitCode=fail?1:0;
