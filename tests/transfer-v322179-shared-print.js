/* Explicit shared printing contract (v3.22.179): secure HTML and grade-specific inputs. */
'use strict';
const fs=require('node:fs'),path=require('node:path'),vm=require('node:vm'),assert=require('node:assert/strict');
const dir=path.resolve(__dirname,'..'),read=f=>fs.readFileSync(path.join(dir,f),'utf8');
let output='',printed=0;
const win={open:()=>({document:{write:s=>{output=s;},close:()=>{}},focus:()=>{},print:()=>{printed++;}})};
vm.runInNewContext(read('includes/transfer-checklist-print.js'),{window:win});
const printer=win.EducationTransferChecklistPrint;
assert.equal(typeof printer.print,'function');
const params={grade:'Πρωτοβάθμια',mode:'ΣΜΕΑΕ',sections:[{title:'mydocs',lines:['Ειδική Αγωγή'] }],
 rows:[{title:'Ενέργεια <script>alert(1)</script>',action:true,done:true,detail:'2 γραμμές\nκαι τρίτη',channel:'Email'},
 {title:'Πιστοποιητικό οικογενειακής κατάστασης',action:false,done:true,detail:'Αυτεπάγγελτα'},
 {title:'Μη διαθέσιμη μοριοδότηση',action:true,disabled:true,done:true}],progress:'1 από 1',notice:'Όχι επίσημο'};
let html=printer.render(params);
assert.match(html,/<meta charset="utf-8">/);
assert.match(html,/Πρωτοβάθμια/);
assert.match(html,/Ειδική Αγωγή/);
assert.ok(html.includes('☑')&&html.includes('ⓘ'));
assert.ok(html.includes('&lt;script&gt;alert(1)&lt;/script&gt;'));
assert.ok(!html.includes('<script>'));
assert.ok(html.includes('2 γραμμές<br>και τρίτη'));
assert.ok(html.includes('Πιστοποιητικό οικογενειακής κατάστασης'));
assert.ok(printer.print(params)); assert.equal(printed,1);assert.equal(output,html);
const pageP=read('ypologismos-morion-metathesis-protovathmia.php');
const pageD=read('ypologismos-morion-metathesis.php');
for(const [page,controller] of [[pageP,'transfer-primary.js'],[pageD,'transfer-documents.js']]){
 assert.ok(page.indexOf('transfer-checklist-print.js')>page.indexOf('transfer-checklist.js'));
 assert.ok(page.indexOf('transfer-checklist-print.js')<page.indexOf(controller));
}
assert.match(pageP,/id="primaryPrintChecklist"/);
assert.match(read('includes/transfer-primary.js'),/function printPrimaryChecklist\(\)/);
assert.match(read('includes/transfer-primary.js'),/EducationTransferChildrenDocuments\.detail\('primary'\)/);
assert.match(read('includes/transfer-documents.js'),/EducationTransferChecklistPrint\.print/);
console.log('PASS v3.22.179 shared safe print: markup, status, routes, A4, load order');
