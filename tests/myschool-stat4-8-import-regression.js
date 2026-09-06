const path=require('path');
const zlib=require('zlib');
const imp=require(path.join(__dirname,'..','includes','myschool-staff-import.js'));
let pass=0,fail=0;
function check(name,cond){if(cond){console.log('PASS: '+name);pass++;}else{console.log('FAIL: '+name);fail++;}}

const headers=[
 'Κωδικός Σχολείου','Ονομασία Σχολείου','Α.Μ.','Επώνυμο','Όνομα','Κωδικός Κύριας Ειδικότητας','Κωδικός 2ης Ειδικότητας',
 'Διευθυντής Σχολείου','Υποδιευθυντής Σχολείου','Υποχρεωτικό Διδακτικό Ωράριο Υπηρέτησης',
 'Ώρες Υποχ. Διδακτικού Ωραρίου Υπηρέτησης στο Φορέα','Μείωση Ωραρίου'
];
const csvText=headers.join(';')+'\n'
 +'="2401020";2ο ΗΜΕΡΗΣΙΟ ΓΥΜΝΑΣΙΟ ΚΕΡΚΥΡΑΣ;100001;ΔΟΚΙΜΗ;ΜΑΡΙΑ;ΠΕ86;ΠΕ03;Όχι;Όχι;20;8;0\n'
 +'="2401020";2ο ΗΜΕΡΗΣΙΟ ΓΥΜΝΑΣΙΟ ΚΕΡΚΥΡΑΣ;100002;ΔΙΕΥΘΥΝΤΗΣ;ΝΙΚΟΣ;ΠΕ02;;Ναι;Όχι;18;18;13';

const registry=imp.parseText(csvText,{filename:'stat4_8-test.csv'});
check('registry schema',registry.schema_version==='dde_staff_registry_v1');
check('two placements one school',registry.placement_count===2&&registry.school_count===1);
check('transient source AM only used for aggregate unique count',registry.unique_people_count===2&&!Object.prototype.hasOwnProperty.call(registry.people[0],'source_am'));
const people=imp.forSchool(registry,'2401020');
check('school formula code normalized',people.length===2&&people[0].school_code==='2401020');
check('secondary specialty preserved',people[0].specialty_code==='ΠΕ86'&&people[0].secondary_specialty_code==='ΠΕ03');
check('partial placement converts to external hours',people[0].required_teaching_hours===20&&people[0].available_here_hours===8&&people[0].assigned_external_hours===12);
check('director source reduction becomes effective obligation',people[1].role==='director'&&people[1].source_base_required_hours===18&&people[1].source_reduction_hours===13&&people[1].required_teaching_hours===5&&people[1].available_here_hours===5);
check('normalized registry omits contact and tax data',!Object.prototype.hasOwnProperty.call(people[0],'email')&&!Object.prototype.hasOwnProperty.call(people[0],'afm')&&!Object.prototype.hasOwnProperty.call(people[0],'phone'));
const clean=imp.cleanedCsv(registry);
check('clean export keeps safe staffing fields',clean.includes('dde_staff_registry_v1;2401020')&&clean.includes('ΠΕ86;ΠΕ03;ΔΟΚΙΜΗ ΜΑΡΙΑ'));
check('clean export omits source AM/tax/contact headers',!clean.includes('Α.Μ.')&&!clean.includes('Α.Φ.Μ.')&&!clean.includes('Τηλέφωνο')&&!clean.includes('Email'));

function crc32(buf){
 let c=0xffffffff;
 for(const b of buf){c^=b;for(let k=0;k<8;k++)c=(c>>>1)^((c&1)?0xedb88320:0);}
 return (c^0xffffffff)>>>0;
}
function makeZip(name,text){
 const data=Buffer.from(text,'utf8'), compressed=zlib.deflateRawSync(data), nameBuf=Buffer.from(name,'utf8'), crc=crc32(data);
 const local=Buffer.alloc(30); local.writeUInt32LE(0x04034b50,0); local.writeUInt16LE(20,4); local.writeUInt16LE(0x0800,6); local.writeUInt16LE(8,8); local.writeUInt32LE(crc,14); local.writeUInt32LE(compressed.length,18); local.writeUInt32LE(data.length,22); local.writeUInt16LE(nameBuf.length,26);
 const central=Buffer.alloc(46); central.writeUInt32LE(0x02014b50,0); central.writeUInt16LE(20,4); central.writeUInt16LE(20,6); central.writeUInt16LE(0x0800,8); central.writeUInt16LE(8,10); central.writeUInt32LE(crc,16); central.writeUInt32LE(compressed.length,20); central.writeUInt32LE(data.length,24); central.writeUInt16LE(nameBuf.length,28); central.writeUInt32LE(0,42);
 const eocd=Buffer.alloc(22); eocd.writeUInt32LE(0x06054b50,0); eocd.writeUInt16LE(1,8); eocd.writeUInt16LE(1,10); eocd.writeUInt32LE(central.length+nameBuf.length,12); eocd.writeUInt32LE(local.length+nameBuf.length+compressed.length,16);
 return Buffer.concat([local,nameBuf,compressed,central,nameBuf,eocd]);
}
(async()=>{
 try{
   const zip=makeZip('stat4_8-test.csv',csvText); const ab=zip.buffer.slice(zip.byteOffset,zip.byteOffset+zip.byteLength);
   const fromZip=await imp.parseArrayBuffer(ab,'stat4_8-test.zip');
   check('direct ZIP import',fromZip.placement_count===2&&fromZip.source_inner_file==='stat4_8-test.csv');
   const fake={data:{},setItem(k,v){this.data[k]=v;},getItem(k){return this.data[k]||null;},removeItem(k){delete this.data[k];}};
   check('session save',imp.saveSession(fromZip,fake)===true);
   check('session load',imp.loadSession(fake).placement_count===2);
   imp.clearSession(fake); check('session clear',imp.loadSession(fake)===null);
 }catch(e){console.error(e);check('direct ZIP import',false);}
 console.log(`RESULT ${pass} PASS / ${fail} FAIL`); process.exit(fail?1:0);
})();
