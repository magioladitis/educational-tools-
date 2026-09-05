const path=require('path');
const csv=require(path.join(__dirname,'..','includes','personnel-csv-import.js'));
let pass=0, fail=0;
function check(name,cond){ if(cond){console.log('PASS: '+name);pass++;}else{console.log('FAIL: '+name);fail++;} }

const semicolon='Κλάδος;Επώνυμο;Όνομα;Έτη υπηρεσίας;Μήνες;Ημέρες;Ρόλος;Ώρες αλλού\nΠΕ03;Παπαδοπούλου;Μαρία;7;3;12;Εκπαιδευτικός;2\nΠΕ02;Ιωάννου;Νίκος;20;0;0;Υποδιευθυντής;0';
let parsed=csv.parse(semicolon);
check('detect semicolon', parsed.delimiter===';');
check('semicolon rows', parsed.rows.length===2);
let map=csv.autoMap(parsed.headers);
check('auto map specialty', map.specialty_code==='Κλάδος');
check('auto map surname/name', map.surname==='Επώνυμο' && map.given_name==='Όνομα');
let p=csv.rowToPersonnel(parsed.rows[0],map);
check('Greek full name combined', p.display_name==='Παπαδοπούλου Μαρία');
check('service fields', p.service_years===7 && p.service_months===3 && p.service_days===12);
check('external hours', p.assigned_external_hours===2);
let p2=csv.rowToPersonnel(parsed.rows[1],map);
check('vice director role', p2.role==='vice_or_sector');

const quoted='Κλάδος,Ονοματεπώνυμο,Προϋπηρεσία,Ρόλος\n"ΠΕ04.01","Κόρη, Μαρία","12 έτη 4 μήνες 3 ημέρες","Διευθυντής"';
parsed=csv.parse(quoted);
check('detect comma', parsed.delimiter===',');
map=csv.autoMap(parsed.headers);
p=csv.rowToPersonnel(parsed.rows[0],map);
check('quoted comma preserved', p.display_name==='Κόρη, Μαρία');
check('combined service parsed', p.service_years===12 && p.service_months===4 && p.service_days===3);
check('director role', p.role==='director');
check('specialty canonical', p.specialty_code==='ΠΕ04.01');

const tab='Ειδικότητα\tΟνοματεπώνυμο\tΑριθμός τμημάτων\tΚλίμακα ωραρίου ΔΕ\nDE01\tΔοκιμή ΔΕ\t8\tΤεχνίτης';
parsed=csv.parse(tab);
check('detect tab', parsed.delimiter==='\t');
map=csv.autoMap(parsed.headers);
p=csv.rowToPersonnel(parsed.rows[0],map);
check('latin DE canonicalized', p.specialty_code==='ΔΕ01');
check('numeric director band maps', p.director_sections_band==='6-9');
check('DE technician maps', p.hours_branch==='DE01_TECH');

check('PE latin canonicalized', csv.normalizeSpecialtyCode('PE 3 - Μαθηματικοί')==='ΠΕ03');
check('TE subcode canonicalized', csv.normalizeSpecialtyCode('TE 01.02')==='ΤΕ01.02');
check('blank specialty remains blank', csv.normalizeSpecialtyCode('Μαθηματικοί')==='');
console.log(`RESULT ${pass} PASS / ${fail} FAIL`);
process.exit(fail?1:0);
