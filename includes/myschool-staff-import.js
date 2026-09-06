(function(root){
  'use strict';

  var personnelCsv=root.EducationPersonnelCsv || null;
  if(!personnelCsv && typeof require!=='undefined'){
    try{ personnelCsv=require('./personnel-csv-import.js'); }catch(e){}
  }

  var REGISTRY_SCHEMA='dde_staff_registry_v1';
  var SOURCE_KIND='myschool_stat4_8';
  var SESSION_KEY='education_dde_staff_registry_v1';
  var MAX_ARCHIVE_BYTES=25*1024*1024;
  var REQUIRED_HEADERS=[
    'Κωδικός Σχολείου','Ονομασία Σχολείου','Επώνυμο','Όνομα',
    'Κωδικός Κύριας Ειδικότητας','Κωδικός 2ης Ειδικότητας',
    'Διευθυντής Σχολείου','Υποδιευθυντής Σχολείου',
    'Υποχρεωτικό Διδακτικό Ωράριο Υπηρέτησης',
    'Ώρες Υποχ. Διδακτικού Ωραρίου Υπηρέτησης στο Φορέα','Μείωση Ωραρίου'
  ];

  function cleanFormulaText(value){
    var text=String(value==null?'':value).trim();
    var m=text.match(/^=\s*"([^"]*)"$/);
    if(m) return m[1].trim();
    m=text.match(/^=\s*([^=].*)$/);
    if(m) text=m[1].trim();
    if(text.length>=2 && text[0]==='"' && text[text.length-1]==='"') return text.slice(1,-1).trim();
    return text;
  }
  function normalizeSchoolCode(value){
    return cleanFormulaText(value).replace(/\s+/g,'').toUpperCase();
  }
  function intValue(value){
    var raw=String(value==null?'':value).trim();
    if(raw==='') return 0;
    var n=parseInt(raw.replace(/[^0-9-]/g,''),10);
    return isFinite(n)?Math.max(0,n):0;
  }
  function yes(value){
    var t=String(value==null?'':value).trim().toLocaleLowerCase('el-GR');
    return t==='ναι'||t==='nai'||t==='yes'||t==='1'||t==='true';
  }
  function roleFromRow(row){
    if(yes(row['Διευθυντής Σχολείου'])) return 'director';
    if(yes(row['Υποδιευθυντής Σχολείου'])) return 'vice_or_sector';
    return 'teacher';
  }
  function roleLabel(role){
    if(role==='director') return 'Διευθυντής/ντρια';
    if(role==='vice_or_sector') return 'Υποδιευθυντής/ντρια';
    return 'Εκπαιδευτικός';
  }
  function normalizeName(row){
    return (String(row['Επώνυμο']||'').trim()+' '+String(row['Όνομα']||'').trim()).replace(/\s+/g,' ').trim();
  }
  function hash32(text){
    var h=2166136261>>>0;
    text=String(text||'');
    for(var i=0;i<text.length;i++){
      h^=text.charCodeAt(i);
      h=Math.imul(h,16777619)>>>0;
    }
    return ('00000000'+h.toString(16)).slice(-8);
  }
  function normalizeSpecialty(value){
    if(personnelCsv && personnelCsv.normalizeSpecialtyCode) return personnelCsv.normalizeSpecialtyCode(value);
    return String(value==null?'':value).trim();
  }
  function hasHeaders(headers){
    var set={}; (headers||[]).forEach(function(h){set[String(h).trim()]=true;});
    var missing=REQUIRED_HEADERS.filter(function(h){return !set[h];});
    return {valid:missing.length===0,missing:missing};
  }
  function normalizeRow(row,index,duplicateTracker){
    var schoolCode=normalizeSchoolCode(row['Κωδικός Σχολείου']);
    var schoolName=String(row['Ονομασία Σχολείου']||'').trim();
    var displayName=normalizeName(row);
    var specialty=normalizeSpecialty(row['Κωδικός Κύριας Ειδικότητας']);
    var secondary=normalizeSpecialty(row['Κωδικός 2ης Ειδικότητας']);
    if(!schoolCode || !displayName || !specialty) return null;

    var sourceRequired=intValue(row['Υποχρεωτικό Διδακτικό Ωράριο Υπηρέτησης']);
    var sourceAtUnit=intValue(row['Ώρες Υποχ. Διδακτικού Ωραρίου Υπηρέτησης στο Φορέα']);
    var sourceReduction=intValue(row['Μείωση Ωραρίου']);
    var effectiveRequired=Math.max(0,sourceRequired-sourceReduction);
    if(effectiveRequired<1) return null;
    var availableHere=Math.min(effectiveRequired,sourceAtUnit);
    var external=Math.max(0,effectiveRequired-availableHere);
    var role=roleFromRow(row);

    var identitySeed=[schoolCode,displayName,specialty,secondary,role].join('|').toLocaleLowerCase('el-GR');
    var baseId='myschool-'+schoolCode+'-'+hash32(identitySeed);
    var count=(duplicateTracker[baseId]||0)+1; duplicateTracker[baseId]=count;
    var personId=count===1?baseId:baseId+'-'+count;

    return {
      schema_version:'staff_registry_v1',
      registry_schema_version:REGISTRY_SCHEMA,
      source_kind:SOURCE_KIND,
      person_id:personId,
      school_code:schoolCode,
      school_name:schoolName,
      display_name:displayName,
      specialty_code:specialty,
      secondary_specialty_code:secondary,
      role:role,
      required_teaching_hours:effectiveRequired,
      assigned_external_hours:external,
      available_here_hours:availableHere,
      source_base_required_hours:sourceRequired,
      source_reduction_hours:sourceReduction,
      source_hours_at_unit:sourceAtUnit
    };
  }
  function buildRegistry(parsed,meta){
    if(!parsed || !Array.isArray(parsed.headers) || !Array.isArray(parsed.rows)) throw new Error('Μη έγκυρο CSV.');
    var headerCheck=hasHeaders(parsed.headers);
    if(!headerCheck.valid) throw new Error('Το αρχείο δεν αναγνωρίζεται ως stat4_8. Λείπουν: '+headerCheck.missing.join(', '));
    var duplicateTracker={}, people=[], uniqueRaw={};
    parsed.rows.forEach(function(row,index){
      var p=normalizeRow(row,index,duplicateTracker);
      if(p) people.push(p);
      var rawId=cleanFormulaText(row['Α.Μ.']||'');
      if(rawId) uniqueRaw[rawId]=true;
    });
    var schoolSet={}; people.forEach(function(p){schoolSet[p.school_code]=true;});
    return {
      schema_version:REGISTRY_SCHEMA,
      source_kind:SOURCE_KIND,
      source_file:meta&&meta.filename?String(meta.filename):'',
      source_inner_file:meta&&meta.innerFilename?String(meta.innerFilename):'',
      imported_at:new Date().toISOString(),
      placement_count:people.length,
      unique_people_count:Object.keys(uniqueRaw).length || null,
      school_count:Object.keys(schoolSet).length,
      people:people
    };
  }
  function parseText(text,meta){
    if(!personnelCsv || !personnelCsv.parse) throw new Error('Δεν φορτώθηκε ο κοινός CSV parser.');
    return buildRegistry(personnelCsv.parse(String(text||'')),meta||{});
  }
  function decodeBytes(bytes){
    var view=bytes instanceof Uint8Array?bytes:new Uint8Array(bytes);
    try{return new TextDecoder('utf-8',{fatal:true}).decode(view).replace(/^\uFEFF/,'');}catch(e){}
    try{return new TextDecoder('windows-1253').decode(view).replace(/^\uFEFF/,'');}catch(e2){}
    return new TextDecoder('utf-8').decode(view).replace(/^\uFEFF/,'');
  }
  function u16(view,offset){return view.getUint16(offset,true);}
  function u32(view,offset){return view.getUint32(offset,true);}
  function findEocd(view){
    var min=Math.max(0,view.byteLength-65557);
    for(var i=view.byteLength-22;i>=min;i--){ if(u32(view,i)===0x06054b50) return i; }
    return -1;
  }
  function decodeZipName(bytes,utf8){
    try{return new TextDecoder(utf8?'utf-8':'windows-1253').decode(bytes);}catch(e){return new TextDecoder('utf-8').decode(bytes);}
  }
  function zipEntries(buffer){
    var view=new DataView(buffer), eocd=findEocd(view);
    if(eocd<0) throw new Error('Το ZIP δεν έχει έγκυρο κεντρικό κατάλογο.');
    var count=u16(view,eocd+10), centralOffset=u32(view,eocd+16), cursor=centralOffset, entries=[];
    for(var i=0;i<count;i++){
      if(cursor+46>view.byteLength || u32(view,cursor)!==0x02014b50) throw new Error('Μη έγκυρη εγγραφή ZIP.');
      var flags=u16(view,cursor+8), method=u16(view,cursor+10), compressedSize=u32(view,cursor+20), uncompressedSize=u32(view,cursor+24);
      var nameLen=u16(view,cursor+28), extraLen=u16(view,cursor+30), commentLen=u16(view,cursor+32), localOffset=u32(view,cursor+42);
      if(compressedSize===0xffffffff || uncompressedSize===0xffffffff || localOffset===0xffffffff) throw new Error('ZIP64 δεν υποστηρίζεται από τον τοπικό importer.');
      var nameBytes=new Uint8Array(buffer,cursor+46,nameLen);
      var name=decodeZipName(nameBytes,(flags&0x0800)!==0);
      entries.push({name:name,flags:flags,method:method,compressedSize:compressedSize,uncompressedSize:uncompressedSize,localOffset:localOffset});
      cursor+=46+nameLen+extraLen+commentLen;
    }
    return entries;
  }
  async function inflateRaw(bytes){
    if(typeof DecompressionStream==='undefined') throw new Error('Ο browser δεν υποστηρίζει τοπική αποσυμπίεση ZIP. Χρησιμοποίησε το CSV μέσα στο ZIP.');
    var stream=new Blob([bytes]).stream().pipeThrough(new DecompressionStream('deflate-raw'));
    return new Uint8Array(await new Response(stream).arrayBuffer());
  }
  async function extractCsvFromZip(buffer){
    var entries=zipEntries(buffer).filter(function(e){return /\.csv$/i.test(e.name);});
    if(!entries.length) throw new Error('Δεν βρέθηκε CSV μέσα στο ZIP.');
    var entry=entries.find(function(e){return /stat4[_-]?8/i.test(e.name);}) || entries[0];
    if(entry.flags&1) throw new Error('Κρυπτογραφημένο ZIP δεν υποστηρίζεται.');
    if(entry.uncompressedSize>MAX_ARCHIVE_BYTES) throw new Error('Το CSV μέσα στο ZIP είναι υπερβολικά μεγάλο για ασφαλή τοπική εισαγωγή.');
    var view=new DataView(buffer), off=entry.localOffset;
    if(off+30>view.byteLength || u32(view,off)!==0x04034b50) throw new Error('Μη έγκυρη τοπική εγγραφή ZIP.');
    var nameLen=u16(view,off+26), extraLen=u16(view,off+28), dataStart=off+30+nameLen+extraLen;
    if(dataStart+entry.compressedSize>view.byteLength) throw new Error('Το ZIP είναι ελλιπές.');
    var compressed=new Uint8Array(buffer,dataStart,entry.compressedSize);
    var out;
    if(entry.method===0) out=new Uint8Array(compressed);
    else if(entry.method===8) out=await inflateRaw(compressed);
    else throw new Error('Μη υποστηριζόμενη μέθοδος συμπίεσης ZIP ('+entry.method+').');
    if(out.byteLength>MAX_ARCHIVE_BYTES) throw new Error('Το αποσυμπιεσμένο CSV υπερβαίνει το όριο ασφαλείας.');
    return {bytes:out,filename:entry.name};
  }
  async function parseArrayBuffer(buffer,filename){
    var bytes=new Uint8Array(buffer), inner='';
    if(bytes.length>=4 && bytes[0]===0x50 && bytes[1]===0x4b && bytes[2]===0x03 && bytes[3]===0x04){
      var extracted=await extractCsvFromZip(buffer); bytes=extracted.bytes; inner=extracted.filename;
    }
    var text=decodeBytes(bytes);
    return parseText(text,{filename:filename||'',innerFilename:inner});
  }
  function forSchool(registry,schoolCode){
    var code=normalizeSchoolCode(schoolCode);
    if(!registry || !Array.isArray(registry.people) || !code) return [];
    return registry.people.filter(function(p){return normalizeSchoolCode(p.school_code)===code;});
  }
  function saveSession(registry,storage){
    storage=storage || (typeof sessionStorage!=='undefined'?sessionStorage:null);
    if(!storage) return false;
    storage.setItem(SESSION_KEY,JSON.stringify(registry)); return true;
  }
  function loadSession(storage){
    storage=storage || (typeof sessionStorage!=='undefined'?sessionStorage:null);
    if(!storage) return null;
    try{
      var obj=JSON.parse(storage.getItem(SESSION_KEY)||'null');
      return obj && obj.schema_version===REGISTRY_SCHEMA && Array.isArray(obj.people)?obj:null;
    }catch(e){return null;}
  }
  function clearSession(storage){
    storage=storage || (typeof sessionStorage!=='undefined'?sessionStorage:null);
    if(storage) storage.removeItem(SESSION_KEY);
  }
  function csvEscape(value){
    var text=String(value==null?'':value);
    return /[;"\r\n]/.test(text)?'"'+text.replace(/"/g,'""')+'"':text;
  }
  function cleanedCsv(registry){
    var headers=['schema_version','school_code','school_name','person_id','specialty_code','secondary_specialty_code','display_name','role','required_teaching_hours','assigned_external_hours','available_here_hours','source_base_required_hours','source_reduction_hours','source_hours_at_unit'];
    var lines=[headers];
    (registry&&Array.isArray(registry.people)?registry.people:[]).forEach(function(p){
      lines.push([REGISTRY_SCHEMA,p.school_code,p.school_name,p.person_id,p.specialty_code,p.secondary_specialty_code,p.display_name,roleLabel(p.role),p.required_teaching_hours,p.assigned_external_hours,p.available_here_hours,p.source_base_required_hours,p.source_reduction_hours,p.source_hours_at_unit]);
    });
    return '\uFEFF'+lines.map(function(row){return row.map(csvEscape).join(';');}).join('\r\n')+'\r\n';
  }

  var api={
    registrySchemaVersion:REGISTRY_SCHEMA,
    sourceKind:SOURCE_KIND,
    sessionKey:SESSION_KEY,
    requiredHeaders:REQUIRED_HEADERS.slice(),
    normalizeSchoolCode:normalizeSchoolCode,
    roleFromRow:roleFromRow,
    hasHeaders:hasHeaders,
    normalizeRow:normalizeRow,
    buildRegistry:buildRegistry,
    parseText:parseText,
    decodeBytes:decodeBytes,
    zipEntries:zipEntries,
    extractCsvFromZip:extractCsvFromZip,
    parseArrayBuffer:parseArrayBuffer,
    forSchool:forSchool,
    saveSession:saveSession,
    loadSession:loadSession,
    clearSession:clearSession,
    cleanedCsv:cleanedCsv
  };
  root.EducationMySchoolStaff=api;
  if(typeof module!=='undefined' && module.exports) module.exports=api;
})(typeof window!=='undefined'?window:globalThis);
