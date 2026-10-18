'use strict';
const assert=require('node:assert/strict');
const path=require('node:path');
const fs=require('node:fs');
const {execFileSync}=require('node:child_process');
const root=path.join(__dirname,'..');
const source=fs.readFileSync(path.join(root,'ypologismos-morion-metathesis.php'),'utf8');
const html=execFileSync('php',[path.join(root,'ypologismos-morion-metathesis.php')],{encoding:'utf8'});
const primary=execFileSync('php',[path.join(root,'ypologismos-morion-metathesis-protovathmia.php')],{encoding:'utf8'});
const positions=['Α. Τύπος διαδικασίας και υπηρεσιακά στοιχεία','Β. Μονάδες μετάθεσης','Γ. Κοινωνικά κριτήρια','Δ. Προσωπική λίστα δικαιολογητικών','Προετοιμασία μηνύματος υποβολής δικαιολογητικών'].map(x=>html.indexOf(x));
assert.ok(positions.every(p=>p>=0) && positions.every((p,i)=>i===0||p>positions[i-1]),'A B C D email order');
const service=html.indexOf('id="serviceMode"'),points=html.indexOf('id="serviceDirectPoints"'),msd=html.indexOf('id="msdMode"'),msdpoints=html.indexOf('id="msdDirectPoints"');
assert.ok(positions[1]<service && service<points && points<msd && msd<msdpoints && msdpoints<positions[2],'Both service and MSD in B before C');
assert.ok(html.includes('<label for="serviceDirectPoints">Μόρια συνολικής υπηρεσίας</label>'),'Service label aligns with Primary');
assert.ok(primary.includes('<label for="primaryServicePoints">Μόρια συνολικής υπηρεσίας</label>'));
for(const id of ['serviceMode','serviceDirectPoints','msdMode','msdDirectPoints','transferMsdCard','transferNumericalInputs','transferSocialCriteriaCard','transferDocumentChecklist','transferEmailFeature']){
  assert.equal(html.split(`id="${id}"`).length-1,1,`${id}: unique`);
}
assert.ok(source.indexOf('id="transferEaePanel"')<source.indexOf('Β. Μονάδες μετάθεσης'),'Eligibility review precedes scoring');
assert.ok(source.indexOf('id="transferQualificationPanel"')<source.indexOf('Β. Μονάδες μετάθεσης'),'Qualifications belong to process review');
assert.ok(source.indexOf('id="transferMsdCard"')<source.indexOf('id="transferNumericalInputs"'),'B contains service input');
assert.ok(source.indexOf('id="transferNumericalInputs"')<source.indexOf('id="msdMode"'),'B includes MSD');
assert.ok(source.includes("calculatorCardStart(array('title' => 'Β. Μονάδες μετάθεσης'))"),'B is one card');
assert.ok(!source.includes('Β.1 Θεμελίωση δικαιώματος'),'no misleading legacy B.1 heading in A');
console.log('PASS unified Secondary A-B-C-D layout, preserved field IDs and precheck grouping');
