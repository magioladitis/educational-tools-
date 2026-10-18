/* Regression guard: Primary calculations are independent of optional EAE/intercultural assets;
   both levels keep their score visible on desktop. */
'use strict';
const assert=require('node:assert/strict'),fs=require('node:fs'),path=require('node:path');
const root=path.join(__dirname,'..');
const read=p=>fs.readFileSync(path.join(root,p),'utf8');
const primary=read('includes/transfer-primary.js');
const php=read('ypologismos-morion-metathesis-protovathmia.php');
const css=read('assets/transfer-secondary.css');
assert.ok(primary.includes('if (!scoring) return;'));
assert.ok(!primary.includes('if (!scoring || !eae || !intercultural || !interVacancies) return;'));
assert.ok(primary.includes('global.EducationTransferPrimaryReady=true;'));
assert.ok(primary.includes("if(scoreCard && typeof scoreCard.render==='function')"));
assert.ok(php.includes('id="primaryRuntimeWarning"'));
assert.ok(php.includes("if(window.EducationTransferPrimaryReady===true)return;"));
assert.ok(css.includes('position:sticky;')&&css.includes('#transferScoringResults'));
assert.ok(css.includes('#transferScoringResults > .results'));
console.log('PASS Primary startup fallbacks and Secondary sticky sidebar');
