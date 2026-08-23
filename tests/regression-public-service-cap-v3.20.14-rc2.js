#!/usr/bin/env node
'use strict';
const fs=require('fs'), path=require('path'), vm=require('vm');
const ROOT=process.env.ASEP_TARGET_DIR||process.argv[2]||'/mnt/data/asep-toolkit-v3.20.14-rc2-production';
let p=0,f=0;
function test(n,fn){try{fn();console.log('✓ '+n);p++;}catch(e){console.error('✗ '+n+': '+e.message);f++;}}
function eq(a,b){if(a!==b)throw new Error(`expected ${b}, got ${a}`)}
function contains(s,x){if(!s.includes(x))throw new Error('missing '+x)}
const c={console};c.window=c;c.globalThis=c;vm.createContext(c);vm.runInContext(fs.readFileSync(path.join(ROOT,'includes','service-calculations.js'),'utf8'),c);
const S=c.EducationService;

test('RULES.publicMaxMonths = 120',()=>eq(S.RULES.publicMaxMonths,120));
test('regularPublic 119 => 119 months / 119 points',()=>{const r=S.regularPublic(119);eq(r.months,119);eq(r.points,119)});
test('regularPublic 120 => 120 months / 120 points',()=>{const r=S.regularPublic(120);eq(r.months,120);eq(r.points,120)});
test('regularPublic 121 => capped to 120',()=>{const r=S.regularPublic(121);eq(r.months,120);eq(r.points,120)});
test('regularPublic 999 => capped to 120',()=>{const r=S.regularPublic(999);eq(r.months,120);eq(r.points,120)});
test('regularPublic negative => 0',()=>{const r=S.regularPublic(-1);eq(r.months,0);eq(r.points,0)});

const pages={
 'ypologismos-morion.php':'normalMonths',
 'ypologismos-morion-1gt-2024.php':'regularMonths',
 'ypologismos-morion-1ea-2025.php':'publicMonths',
 'ypologismos-morion-2ea-2025.php':'publicMonths',
 'ypologismos-morion-3ea-2025.php':'publicMonths',
 'ypologismos-morion-4ea-2025.php':'regularMonths'
};
for(const [file,id] of Object.entries(pages)){
 const s=fs.readFileSync(path.join(ROOT,file),'utf8');
 test(`${file}: public/regular input has max=120`,()=>{
   const re=new RegExp(`<input[^>]*id=["']${id}["'][^>]*>`,`i`); const m=s.match(re); if(!m)throw new Error('input not found'); contains(m[0],'max="120"');
 });
 test(`${file}: explicitly says up to 120 months`,()=>contains(s,'έως 120 μήνες'));
 test(`${file}: loads cache-busted shared service module`,()=>contains(s,'includes/service-calculations.js?v=3.20.14-rc2'));
}
console.log(`\nPublic service cap: PASS ${p} / FAIL ${f}`); if(f)process.exit(1);
