(function(root){
  'use strict';

  function stripDiacritics(value){
    return String(value == null ? '' : value).normalize('NFD').replace(/[\u0300-\u036f]/g,'');
  }
  function normalizeHeader(value){
    return stripDiacritics(value).toLowerCase().replace(/[“”"'`´]/g,'').replace(/[^a-z0-9α-ω]+/g,' ').trim().replace(/\s+/g,' ');
  }
  function normalizeSpecialtyCode(value){
    var text=stripDiacritics(value).toUpperCase().replace(/PE/g,'ΠΕ').replace(/TE/g,'ΤΕ').replace(/DE/g,'ΔΕ');
    var m=text.match(/(ΠΕ|ΤΕ|ΔΕ)\s*([0-9]{1,2})(?:\s*[.\-_/]\s*([0-9]{1,2}))?/);
    if(!m) return '';
    var main=String(parseInt(m[2],10));
    if(main.length<2) main='0'+main;
    var out=m[1]+main;
    if(m[3]){
      var sub=String(parseInt(m[3],10));
      if(sub.length<2) sub='0'+sub;
      out+='.'+sub;
    }
    return out;
  }
  function parseDelimited(text, delimiter){
    text=String(text == null ? '' : text).replace(/^\uFEFF/,'');
    var rows=[], row=[], field='', quoted=false;
    for(var i=0;i<text.length;i++){
      var ch=text[i];
      if(quoted){
        if(ch==='"'){
          if(text[i+1]==='"'){ field+='"'; i++; }
          else quoted=false;
        } else field+=ch;
      } else {
        if(ch==='"') quoted=true;
        else if(ch===delimiter){ row.push(field); field=''; }
        else if(ch==='\n'){ row.push(field); rows.push(row); row=[]; field=''; }
        else if(ch==='\r'){
          if(text[i+1]==='\n') i++;
          row.push(field); rows.push(row); row=[]; field='';
        } else field+=ch;
      }
    }
    if(field!=='' || row.length){ row.push(field); rows.push(row); }
    while(rows.length && rows[rows.length-1].every(function(v){return String(v).trim()==='';})) rows.pop();
    return rows;
  }
  function detectDelimiter(text){
    var candidates=[';','\t',','];
    var best=';', bestScore=-1;
    candidates.forEach(function(delim){
      var rows=parseDelimited(String(text).slice(0,20000),delim).slice(0,12).filter(function(r){return r.some(function(v){return String(v).trim()!=='';});});
      if(!rows.length) return;
      var counts=rows.map(function(r){return r.length;});
      var freq={}; counts.forEach(function(c){freq[c]=(freq[c]||0)+1;});
      var modal=1, modalFreq=0;
      Object.keys(freq).forEach(function(k){ if(freq[k]>modalFreq){modal=parseInt(k,10);modalFreq=freq[k];} });
      var score=(modal>1?100:0)+(modalFreq*10)+modal;
      if(score>bestScore){bestScore=score;best=delim;}
    });
    return best;
  }
  function parse(text, delimiter){
    delimiter=delimiter || detectDelimiter(text);
    var rows=parseDelimited(text,delimiter);
    if(!rows.length) return {delimiter:delimiter,headers:[],rows:[]};
    var headers=rows.shift().map(function(h,index){var s=String(h).trim();return s!==''?s:'Στήλη '+(index+1);});
    var objects=rows.filter(function(r){return r.some(function(v){return String(v).trim()!=='';});}).map(function(r){
      var obj={}; headers.forEach(function(h,i){obj[h]=r[i] == null ? '' : String(r[i]).trim();}); return obj;
    });
    return {delimiter:delimiter,headers:headers,rows:objects};
  }

  var FIELD_SYNONYMS={
    schema_version:['εκδοση μητρωου','εκδοση μητρωου προσωπικου','schema version','registry version'],
    person_id:['αναγνωριστικο','αναγνωριστικο εκπαιδευτικου','person id','person_id'],
    specialty_code:['κλαδος','κλαδος ειδικοτητα','ειδικοτητα','κωδικος ειδικοτητας','specialty','specialty code','branch','branch code','code'],
    secondary_specialty_code:['2η ειδικοτητα','δευτερη ειδικοτητα','κωδικος 2ης ειδικοτητας','κωδικος δευτερης ειδικοτητας','second specialty','secondary specialty','secondary specialty code'],
    display_name:['ονοματεπωνυμο','ονομα εκπαιδευτικου','εκπαιδευτικος','full name','fullname','name'],
    surname:['επωνυμο','surname','last name','lastname'],
    given_name:['ονομα','first name','firstname','given name'],
    service_years:['ετη υπηρεσιας','ετη προυπηρεσιας','χρονια υπηρεσιας','ετη','years','service years'],
    service_months:['μηνες υπηρεσιας','μηνες προυπηρεσιας','μηνες','months','service months'],
    service_days:['ημερες υπηρεσιας','ημερες προυπηρεσιας','ημερες','days','service days'],
    service_combined:['προυπηρεσια','υπηρεσια','χρονος υπηρεσιας','συνολικη υπηρεσια','service','service time'],
    role:['ρολος','ιδιοτητα','θεση','role','position'],
    required_teaching_hours:['υποχρεωτικο ωραριο','υποχρεωτικες ωρες','διδακτικο ωραριο','ωραριο','required teaching hours','required hours','teaching hours'],
    assigned_external_hours:['ωρες αλλου','ωρες σε αλλη μοναδα','ωρες αλλης μοναδας','διαθεση ωρες','external hours','hours elsewhere'],
    director_sections_band:['τμηματα διευθυντη','κλιμακα τμηματων','αριθμος τμηματων','director sections','sections band'],
    hours_branch:['κλιμακα ωραριου δε','κλιμακα δε','ωραριο δε','de scale','hours branch'],
    obligation_source:['πηγη ωραριου','πηγη υποχρεωτικου ωραριου','obligation source','hours source'],
    source_base_required_hours:['υω πηγης','βασικο υω πηγης','source base required hours','source required hours'],
    source_reduction_hours:['μειωση πηγης','μειωση ωραριου πηγης','source reduction hours'],
    source_hours_at_unit:['ωρες υω στον φορεα','ωρες στον φορεα πηγης','source hours at unit','hours at unit']
  };
  var FIELD_FUZZY_SYNONYMS={
    schema_version:['εκδοση μητρωου','schema version','registry version'],
    person_id:['αναγνωριστικο εκπαιδευτικου','person id'],
    specialty_code:['κλαδος ειδικοτητα','κωδικος ειδικοτητας','specialty code','branch code'],
    secondary_specialty_code:['2η ειδικοτητα','δευτερη ειδικοτητα','κωδικος 2ης ειδικοτητας','secondary specialty'],
    display_name:['ονοματεπωνυμο','ονομα εκπαιδευτικου','full name'],
    service_years:['ετη υπηρεσιας','ετη προυπηρεσιας','service years'],
    service_months:['μηνες υπηρεσιας','μηνες προυπηρεσιας','service months'],
    service_days:['ημερες υπηρεσιας','ημερες προυπηρεσιας','service days'],
    service_combined:['συνολικη προυπηρεσια','χρονος υπηρεσιας','συνολικη υπηρεσια','service time'],
    required_teaching_hours:['υποχρεωτικο ωραριο','υποχρεωτικες ωρες','διδακτικο ωραριο','required teaching hours','required hours'],
    assigned_external_hours:['ωρες σε αλλη μοναδα','ωρες αλλης μοναδας','external hours','hours elsewhere'],
    director_sections_band:['κλιμακα τμηματων','αριθμος τμηματων','director sections','sections band'],
    hours_branch:['κλιμακα ωραριου δε','de scale','hours branch'],
    obligation_source:['πηγη ωραριου','obligation source','hours source'],
    source_base_required_hours:['υω πηγης','source required hours'],
    source_reduction_hours:['μειωση πηγης','source reduction hours'],
    source_hours_at_unit:['ωρες υω στον φορεα','source hours at unit']
  };
  function autoMap(headers){
    var normalized=headers.map(normalizeHeader), map={};
    Object.keys(FIELD_SYNONYMS).forEach(function(field){
      var syns=FIELD_SYNONYMS[field].map(normalizeHeader), idx=-1;
      for(var i=0;i<normalized.length && idx<0;i++) if(syns.indexOf(normalized[i])>=0) idx=i;
      if(idx<0 && FIELD_FUZZY_SYNONYMS[field]){
        var fuzzy=FIELD_FUZZY_SYNONYMS[field].map(normalizeHeader);
        for(var j=0;j<normalized.length && idx<0;j++){
          for(var k=0;k<fuzzy.length;k++){
            var f=fuzzy[k];
            var suffixIndex=normalized[j].lastIndexOf(' '+f);
            if(normalized[j]===f || normalized[j].indexOf(f+' ')===0 || (suffixIndex>=0 && suffixIndex===normalized[j].length-f.length-1)){ idx=j; break; }
          }
        }
      }
      if(idx>=0) map[field]=headers[idx];
    });
    return map;
  }
  function nonNegativeInt(value,max){
    var n=parseInt(String(value == null ? '' : value).replace(/[^0-9-]/g,''),10);
    if(!isFinite(n) || n<0) n=0;
    if(typeof max==='number') n=Math.min(max,n);
    return n;
  }
  function optionalPositiveInt(value,max){
    var raw=String(value == null ? '' : value).trim();
    if(raw==='') return '';
    if(!/^\d+$/.test(raw)) return raw;
    var n=parseInt(raw,10);
    if(!isFinite(n)) return raw;
    if(typeof max==='number' && n>max) return raw;
    return n;
  }
  function parseCombinedService(value){
    var text=stripDiacritics(value).toLowerCase();
    var y=0,m=0,d=0,match;
    match=text.match(/(\d+)\s*(?:ετ|χρον|year)/); if(match) y=parseInt(match[1],10)||0;
    match=text.match(/(\d+)\s*(?:μην|month)/); if(match) m=parseInt(match[1],10)||0;
    match=text.match(/(\d+)\s*(?:ημερ|day)/); if(match) d=parseInt(match[1],10)||0;
    if(!y&&!m&&!d){
      var parts=text.match(/\d+/g)||[];
      if(parts.length){y=parseInt(parts[0],10)||0;m=parseInt(parts[1]||0,10)||0;d=parseInt(parts[2]||0,10)||0;}
    }
    return {years:Math.max(0,y),months:Math.max(0,Math.min(11,m)),days:Math.max(0,Math.min(29,d))};
  }
  function normalizeRole(value){
    var t=normalizeHeader(value);
    if(t.indexOf('υποδιευθυν')>=0 || t.indexOf('τομεαρχ')>=0 || t.indexOf('vice')>=0 || t.indexOf('sector')>=0) return 'vice_or_sector';
    if(t.indexOf('διευθυν')>=0 || t.indexOf('director')>=0) return 'director';
    return 'teacher';
  }
  function normalizeDirectorBand(value){
    var t=String(value == null ? '' : value).trim();
    if(/^\d+$/.test(t)){
      var n=parseInt(t,10);
      if(n>=13) return '13+';
      if(n>=10) return '10-12';
      if(n>=6) return '6-9';
      if(n>=3) return '3-5';
    }
    if(/13\s*\+|πανω\s*απο\s*12|>\s*12/i.test(stripDiacritics(t))) return '13+';
    if(/10\s*[-–]\s*12/.test(t)) return '10-12';
    if(/6\s*[-–]\s*9/.test(t)) return '6-9';
    if(/3\s*[-–]\s*5/.test(t)) return '3-5';
    return ['3-5','6-9','10-12','13+'].indexOf(t)>=0?t:'';
  }
  function normalizeHoursBranch(value){
    var t=normalizeHeader(value);
    if(t.indexOf('αρχιτεχν')>=0 || t.indexOf('arch')>=0) return 'DE01_ARCH';
    if(t.indexOf('τεχνιτ')>=0 || t.indexOf('tech')>=0) return 'DE01_TECH';
    return ['DE01_ARCH','DE01_TECH'].indexOf(String(value).trim())>=0?String(value).trim():'';
  }
  function normalizeObligationSource(value){
    var t=normalizeHeader(value).replace(/\s+/g,' ');
    if(t.indexOf('myschool')>=0 && t.indexOf('stat4')>=0) return 'myschool_stat4_8';
    if(String(value||'').trim()==='myschool_stat4_8') return 'myschool_stat4_8';
    return '';
  }
  function get(row,mapping,field){
    var h=mapping[field]; return h && Object.prototype.hasOwnProperty.call(row,h) ? row[h] : '';
  }
  function rowToPersonnel(row,mapping){
    var combined=parseCombinedService(get(row,mapping,'service_combined'));
    var display=String(get(row,mapping,'display_name')||'').trim();
    if(!display){
      var surname=String(get(row,mapping,'surname')||'').trim();
      var given=String(get(row,mapping,'given_name')||'').trim();
      display=(surname+' '+given).trim();
    }
    return {
      schema_version:String(get(row,mapping,'schema_version')||'').trim(),
      person_id:String(get(row,mapping,'person_id')||'').trim(),
      specialty_code:normalizeSpecialtyCode(get(row,mapping,'specialty_code')),
      secondary_specialty_code:normalizeSpecialtyCode(get(row,mapping,'secondary_specialty_code')),
      display_name:display,
      service_years:mapping.service_years ? nonNegativeInt(get(row,mapping,'service_years'),50) : Math.min(50,combined.years),
      service_months:mapping.service_months ? nonNegativeInt(get(row,mapping,'service_months'),11) : combined.months,
      service_days:mapping.service_days ? nonNegativeInt(get(row,mapping,'service_days'),29) : combined.days,
      role:normalizeRole(get(row,mapping,'role')),
      required_teaching_hours:optionalPositiveInt(get(row,mapping,'required_teaching_hours'),35),
      assigned_external_hours:nonNegativeInt(get(row,mapping,'assigned_external_hours'),35),
      director_sections_band:normalizeDirectorBand(get(row,mapping,'director_sections_band')),
      hours_branch:normalizeHoursBranch(get(row,mapping,'hours_branch')),
      obligation_source:normalizeObligationSource(get(row,mapping,'obligation_source')),
      source_base_required_hours:optionalPositiveInt(get(row,mapping,'source_base_required_hours'),35),
      source_reduction_hours:optionalPositiveInt(get(row,mapping,'source_reduction_hours'),35),
      source_hours_at_unit:optionalPositiveInt(get(row,mapping,'source_hours_at_unit'),35)
    };
  }

  var api={
    schemaVersion:'2026-09-05-staff-registry-v1',
    registrySchemaVersion:'staff_registry_v1',
    supportsManualRequiredTeachingHours:true,
    supportsSecondarySpecialty:true,
    supportsSourceObligation:true,
    normalizeHeader:normalizeHeader,
    normalizeObligationSource:normalizeObligationSource,
    normalizeSpecialtyCode:normalizeSpecialtyCode,
    parseDelimited:parseDelimited,
    detectDelimiter:detectDelimiter,
    parse:parse,
    autoMap:autoMap,
    parseCombinedService:parseCombinedService,
    rowToPersonnel:rowToPersonnel
  };
  root.EducationPersonnelCsv=api;
  if(typeof module!=='undefined' && module.exports) module.exports=api;
})(typeof window!=='undefined'?window:globalThis);
