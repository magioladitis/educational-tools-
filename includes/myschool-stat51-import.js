(function(root){
  'use strict';

  var csv=root.EducationPersonnelCsv || null;
  if(!csv && typeof require!=='undefined'){
    try{ csv=require('./personnel-csv-import.js'); }catch(e){}
  }

  var REGISTRY_SCHEMA='myschool_stat5_1_v1';
  var SOURCE_KIND='myschool_stat5_1';
  var SESSION_KEY='education_myschool_stat5_1_v1';
  var MAX_ARCHIVE_BYTES=25*1024*1024;
  var REQUIRED_HEADERS=[
    'Κωδικός Μονάδας','Ονομασία Σχολείου','Τάξη','Μάθημα',
    'Συνολικές Ώρες Τμημάτων','Κάλυψη Αναθέσεων','Εκτίμηση Κενών από myschool'
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
  function stripDiacritics(value){
    return String(value==null?'':value).normalize('NFD').replace(/[\u0300-\u036f]/g,'');
  }
  function normalizeSchoolCode(value){
    return cleanFormulaText(value).replace(/\s+/g,'').toUpperCase();
  }
  function numberValue(value){
    var raw=cleanFormulaText(value).replace(/\s+/g,'').replace(',','.');
    if(raw==='') return 0;
    var n=Number(raw.replace(/[^0-9.+-]/g,''));
    return isFinite(n)?Math.max(0,n):0;
  }
  function normalizeGrade(value){
    var t=stripDiacritics(cleanFormulaText(value)).toUpperCase().replace(/[΄’'`.\s]/g,'');
    if(/^Α(?:ΤΑΞΗ)?$/.test(t)) return 'Α';
    if(/^Β(?:ΤΑΞΗ)?$/.test(t)) return 'Β';
    if(/^Γ(?:ΤΑΞΗ)?$/.test(t)) return 'Γ';
    if(/^Δ(?:ΤΑΞΗ)?$/.test(t)) return 'Δ';
    return t;
  }
  function normalizeSubject(value){
    var t=stripDiacritics(cleanFormulaText(value)).toLocaleLowerCase('el-GR');
    t=t.replace(/[‐‑‒–—-]+/g,' ')
      .replace(/[()\[\]{}«»“”"'΄’.,:;!?/\\|]+/g,' ')
      .replace(/\s+/g,' ').trim();
    // Το stat5_1 συχνά προσθέτει επίπεδο ξένης γλώσσας (π.χ. «Αγγλικά μέσοι»),
    // ενώ το ωρολόγιο πρόγραμμα κρατά μόνο τη γλώσσα.
    t=t.replace(/\s+(αρχαριοι|μεσοι|προχωρημενοι|αρχαριο|μεσο|προχωρημενο)$/u,'').trim();
    return t;
  }
  function normalizeStructure(value){
    var t=stripDiacritics(value).toLocaleLowerCase('el-GR');
    if(t.indexOf('ενεεγυ')>=0) return 'eneegyl';
    if(t.indexOf('γυμνασ')>=0) return 'gymnasio';
    if(t.indexOf('γενικ')>=0 && t.indexOf('λυκει')>=0) return 'gel';
    if(t.indexOf('λυκει')>=0 && t.indexOf('επαγγελμα')<0) return 'gel';
    if(t.indexOf('επαλ')>=0 || t.indexOf('επαγγελματικ')>=0) return 'epal';
    return '';
  }
  function localStructure(value){
    var t=String(value==null?'':value).toLowerCase();
    if(t.indexOf('eneegyl')>=0) return 'eneegyl';
    if(t.indexOf('gymnasio')>=0) return 'gymnasio';
    if(t==='gel' || t.indexOf('_gel')>=0 || t.indexOf('lykeio')>=0) return 'gel';
    if(t.indexOf('epal')>=0) return 'epal';
    return '';
  }
  function looseKey(grade,subject){
    return normalizeGrade(grade)+'|'+normalizeSubject(subject);
  }
  function strictKey(structure,grade,subject){
    return String(structure||'')+'|'+looseKey(grade,subject);
  }
  function comparisonKey(grade,subject,structure){
    var s=String(structure||'');
    return s?strictKey(s,grade,subject):looseKey(grade,subject);
  }
  function hasHeaders(headers){
    var set={}; (headers||[]).forEach(function(h){set[String(h).trim()]=true;});
    var missing=REQUIRED_HEADERS.filter(function(h){return !set[h];});
    return {valid:missing.length===0,missing:missing};
  }
  function normalizeRow(row){
    var schoolCode=normalizeSchoolCode(row['Κωδικός Μονάδας']);
    var schoolName=cleanFormulaText(row['Ονομασία Σχολείου']);
    var grade=normalizeGrade(row['Τάξη']);
    var subject=cleanFormulaText(row['Μάθημα']);
    if(!schoolCode || !grade || !subject) return null;
    var total=numberValue(row['Συνολικές Ώρες Τμημάτων']);
    var covered=numberValue(row['Κάλυψη Αναθέσεων']);
    var gap=numberValue(row['Εκτίμηση Κενών από myschool']);
    var computedGap=Math.max(0,total-covered);
    var studyArea=cleanFormulaText(row['Τομέας Σπουδών']);
    var structure=normalizeStructure(studyArea+' '+cleanFormulaText(row['Τύπος Σχολείου'])+' '+cleanFormulaText(row['Είδος Σχολείου']));
    return {
      school_code:schoolCode,
      school_name:schoolName,
      grade:grade,
      study_area:studyArea,
      structure:structure,
      subject:subject,
      subject_key:normalizeSubject(subject),
      strict_key:strictKey(structure,grade,subject),
      loose_key:looseKey(grade,subject),
      sections:numberValue(row['Αριθμός Τμημάτων']),
      max_weekly_hours:numberValue(row['Μέγιστες Εβδομαδιαίες Ώρες Μαθήματος']),
      total_hours:total,
      covered_hours:covered,
      myschool_gap_hours:gap,
      computed_gap_hours:computedGap,
      unit_gap_hours:numberValue(row['Εκτίμηση Κενών από μονάδα']),
      comments:cleanFormulaText(row['Σχόλια']),
      assignment_a:cleanFormulaText(row['Ειδικότητες που έχουν το μάθημα ως Α ανάθεση']),
      assignment_b:cleanFormulaText(row['Ειδικότητες που έχουν το μάθημα ως Β ανάθεση'])
    };
  }
  function buildRegistry(parsed,meta){
    if(!parsed || !Array.isArray(parsed.headers) || !Array.isArray(parsed.rows)) throw new Error('Μη έγκυρο CSV.');
    var headerCheck=hasHeaders(parsed.headers);
    if(!headerCheck.valid) throw new Error('Το αρχείο δεν αναγνωρίζεται ως stat5_1. Λείπουν: '+headerCheck.missing.join(', '));
    var rows=[],schoolSet={},formulaMismatches=0;
    parsed.rows.forEach(function(raw){
      var row=normalizeRow(raw);
      if(!row) return;
      rows.push(row); schoolSet[row.school_code]=true;
      if(Math.abs(row.computed_gap_hours-row.myschool_gap_hours)>0.001) formulaMismatches++;
    });
    if(!rows.length) throw new Error('Δεν βρέθηκαν έγκυρες γραμμές stat5_1.');
    return {
      schema_version:REGISTRY_SCHEMA,
      source_kind:SOURCE_KIND,
      source_file:meta&&meta.filename?String(meta.filename):'',
      source_inner_file:meta&&meta.innerFilename?String(meta.innerFilename):'',
      imported_at:new Date().toISOString(),
      row_count:rows.length,
      school_count:Object.keys(schoolSet).length,
      formula_mismatch_count:formulaMismatches,
      rows:rows
    };
  }
  function parseText(text,meta){
    if(!csv || !csv.parse) throw new Error('Δεν φορτώθηκε ο κοινός CSV parser.');
    return buildRegistry(csv.parse(String(text||'')),meta||{});
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
    for(var i=view.byteLength-22;i>=min;i--){if(u32(view,i)===0x06054b50) return i;}
    return -1;
  }
  function decodeZipName(bytes,utf8){
    try{return new TextDecoder(utf8?'utf-8':'windows-1253').decode(bytes);}catch(e){return new TextDecoder('utf-8').decode(bytes);}
  }
  function zipEntries(buffer){
    var view=new DataView(buffer),eocd=findEocd(view);
    if(eocd<0) throw new Error('Το ZIP δεν έχει έγκυρο κεντρικό κατάλογο.');
    var count=u16(view,eocd+10),cursor=u32(view,eocd+16),entries=[];
    for(var i=0;i<count;i++){
      if(cursor+46>view.byteLength || u32(view,cursor)!==0x02014b50) throw new Error('Μη έγκυρη εγγραφή ZIP.');
      var flags=u16(view,cursor+8),method=u16(view,cursor+10),compressedSize=u32(view,cursor+20),uncompressedSize=u32(view,cursor+24);
      var nameLen=u16(view,cursor+28),extraLen=u16(view,cursor+30),commentLen=u16(view,cursor+32),localOffset=u32(view,cursor+42);
      if(compressedSize===0xffffffff || uncompressedSize===0xffffffff || localOffset===0xffffffff) throw new Error('ZIP64 δεν υποστηρίζεται από τον τοπικό importer.');
      var nameBytes=new Uint8Array(buffer,cursor+46,nameLen);
      entries.push({name:decodeZipName(nameBytes,(flags&0x0800)!==0),flags:flags,method:method,compressedSize:compressedSize,uncompressedSize:uncompressedSize,localOffset:localOffset});
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
    var entry=entries.find(function(e){return /stat5[_-]?1/i.test(e.name);}) || entries[0];
    if(entry.flags&1) throw new Error('Κρυπτογραφημένο ZIP δεν υποστηρίζεται.');
    if(entry.uncompressedSize>MAX_ARCHIVE_BYTES) throw new Error('Το CSV μέσα στο ZIP είναι υπερβολικά μεγάλο για ασφαλή τοπική εισαγωγή.');
    var view=new DataView(buffer),off=entry.localOffset;
    if(off+30>view.byteLength || u32(view,off)!==0x04034b50) throw new Error('Μη έγκυρη τοπική εγγραφή ZIP.');
    var nameLen=u16(view,off+26),extraLen=u16(view,off+28),dataStart=off+30+nameLen+extraLen;
    if(dataStart+entry.compressedSize>view.byteLength) throw new Error('Το ZIP είναι ελλιπές.');
    var compressed=new Uint8Array(buffer,dataStart,entry.compressedSize),out;
    if(entry.method===0) out=new Uint8Array(compressed);
    else if(entry.method===8) out=await inflateRaw(compressed);
    else throw new Error('Μη υποστηριζόμενη μέθοδος συμπίεσης ZIP ('+entry.method+').');
    if(out.byteLength>MAX_ARCHIVE_BYTES) throw new Error('Το αποσυμπιεσμένο CSV υπερβαίνει το όριο ασφαλείας.');
    return {bytes:out,filename:entry.name};
  }
  async function parseArrayBuffer(buffer,filename){
    if(!buffer || buffer.byteLength>MAX_ARCHIVE_BYTES) throw new Error('Το αρχείο είναι υπερβολικά μεγάλο για ασφαλή τοπική εισαγωγή.');
    var bytes=new Uint8Array(buffer),inner='';
    if(bytes.length>=4 && bytes[0]===0x50 && bytes[1]===0x4b && bytes[2]===0x03 && bytes[3]===0x04){
      var extracted=await extractCsvFromZip(buffer); bytes=extracted.bytes; inner=extracted.filename;
    }
    return parseText(decodeBytes(bytes),{filename:filename||'',innerFilename:inner});
  }
  function forSchool(registry,schoolCode){
    var code=normalizeSchoolCode(schoolCode);
    if(!registry || !Array.isArray(registry.rows) || !code) return [];
    return registry.rows.filter(function(row){return normalizeSchoolCode(row.school_code)===code;});
  }
  function aggregateRows(rows){
    var groups={};
    (rows||[]).forEach(function(row){
      var key=row.strict_key||strictKey(row.structure,row.grade,row.subject);
      if(!groups[key]) groups[key]={key:key,loose_key:row.loose_key||looseKey(row.grade,row.subject),structure:row.structure||'',grade:row.grade,subject:row.subject,myschool_gap_hours:0,total_hours:0,covered_hours:0,sections:0,assignment_a:[],assignment_b:[],source_rows:0};
      var g=groups[key];
      g.myschool_gap_hours+=numberValue(row.myschool_gap_hours);
      g.total_hours+=numberValue(row.total_hours);
      g.covered_hours+=numberValue(row.covered_hours);
      g.sections+=numberValue(row.sections);
      g.source_rows++;
      if(row.assignment_a && g.assignment_a.indexOf(row.assignment_a)<0) g.assignment_a.push(row.assignment_a);
      if(row.assignment_b && g.assignment_b.indexOf(row.assignment_b)<0) g.assignment_b.push(row.assignment_b);
    });
    return groups;
  }
  function saveSession(registry,storage){
    storage=storage || (typeof sessionStorage!=='undefined'?sessionStorage:null);
    if(!storage) return false;
    try{storage.setItem(SESSION_KEY,JSON.stringify(registry));return true;}catch(e){return false;}
  }
  function loadSession(storage){
    storage=storage || (typeof sessionStorage!=='undefined'?sessionStorage:null);
    if(!storage) return null;
    try{
      var obj=JSON.parse(storage.getItem(SESSION_KEY)||'null');
      return obj && obj.schema_version===REGISTRY_SCHEMA && Array.isArray(obj.rows)?obj:null;
    }catch(e){return null;}
  }
  function clearSession(storage){
    storage=storage || (typeof sessionStorage!=='undefined'?sessionStorage:null);
    if(storage) storage.removeItem(SESSION_KEY);
  }

  var api={
    registrySchemaVersion:REGISTRY_SCHEMA,
    sourceKind:SOURCE_KIND,
    sessionKey:SESSION_KEY,
    requiredHeaders:REQUIRED_HEADERS.slice(),
    cleanFormulaText:cleanFormulaText,
    normalizeSchoolCode:normalizeSchoolCode,
    normalizeGrade:normalizeGrade,
    normalizeSubject:normalizeSubject,
    normalizeStructure:normalizeStructure,
    localStructure:localStructure,
    looseKey:looseKey,
    strictKey:strictKey,
    comparisonKey:comparisonKey,
    hasHeaders:hasHeaders,
    normalizeRow:normalizeRow,
    buildRegistry:buildRegistry,
    parseText:parseText,
    decodeBytes:decodeBytes,
    zipEntries:zipEntries,
    extractCsvFromZip:extractCsvFromZip,
    parseArrayBuffer:parseArrayBuffer,
    forSchool:forSchool,
    aggregateRows:aggregateRows,
    saveSession:saveSession,
    loadSession:loadSession,
    clearSession:clearSession
  };
  root.EducationMySchoolStat51=api;
  if(typeof module!=='undefined' && module.exports) module.exports=api;
})(typeof window!=='undefined'?window:globalThis);
