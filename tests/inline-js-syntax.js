'use strict';
const fs=require('fs');const path=require('path');const vm=require('vm');
const root=path.resolve(__dirname,'..');
const pages=['ypologismos-morion.php','ypologismos-morion-1gt-2024.php','ypologismos-morion-1ea-2025.php','ypologismos-morion-2ea-2025.php','ypologismos-morion-3ea-2025.php','ypologismos-morion-4ea-2025.php','ypologismos-morion-onaseia.php'];
let pass=0,fail=0;
for(const file of pages){
 const s=fs.readFileSync(path.join(root,file),'utf8');
 const re=/<script(?![^>]*\bsrc\s*=)[^>]*>([\s\S]*?)<\/script>/gi; let m,i=0;
 while((m=re.exec(s))){i++;try{new vm.Script(m[1],{filename:file+'#inline'+i});console.log('✓ '+file+' inline '+i);pass++;}catch(e){console.error('✗ '+file+' inline '+i+': '+e.message);fail++;}}
 if(i===0){console.log('• '+file+': no inline scripts');}
}
console.log(`\nPASS: ${pass}`);console.log(`FAIL: ${fail}`);if(fail)process.exit(1);
