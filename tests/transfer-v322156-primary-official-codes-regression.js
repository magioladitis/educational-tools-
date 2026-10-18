'use strict';
const assert=require('assert');
const fs=require('fs'),path=require('path');
const php=fs.readFileSync(path.join(__dirname,'../ypologismos-morion-metathesis-protovathmia.php'),'utf8');
const js=fs.readFileSync(path.join(__dirname,'../includes/transfer-primary.js'),'utf8');
const expected={region:'001.ΠΕ001',eae:'001.ΣΜΚΔ001',sensory:'001.ΣΜΕΑ003',kedasy:'001.ΣΜΚΔ001.ΚΔΣ',intercultural:'002.ΔΙΑΠ001'};
for(const [mode,code] of Object.entries(expected)){
  assert(php.includes(`value="${mode}" data-official-code="${code}"`),`${mode} official application mapping`);
}
assert(!php.includes('Δευτεροβάθμια (πλήρης 3.22.151)'), 'No hardcoded version in level selector');
assert(php.includes('>Δευτεροβάθμια</a>'), 'User-friendly level selector');
for(const [mode,code] of Object.entries(expected)){
  const line=php.split('\n').find(s=>s.includes(`value="${mode}" data-official-code="${code}"`));
  assert(line && line.includes(`[${code}]`), `${mode} code appears inside dropdown option`);
}
assert(!php.includes('id="primaryOfficialApplication"'), 'No redundant separate code hint');
assert(php.includes('id="primaryDestination"'),'Destination selector exists for grouped codes');
for(const raw of ['eeeek','deaf','blind'])assert(!php.includes('value="'+raw+'" data-official-code'),'No duplicate official type for '+raw);
assert(!fs.readFileSync(path.join(__dirname,'../ypologismos-morion-metathesis.php'),'utf8').includes('3.22.156'), 'Secondary transfer body unchanged');
console.log('OK: official code mappings, dynamic hint, version-free level selector');
