(function(root){
  'use strict';

  function stripDiacritics(value){
    return String(value == null ? '' : value).normalize('NFD').replace(/[\u0300-\u036f]/g,'');
  }
  function normalizeHeader(value){
    return stripDiacritics(value).toLowerCase().replace(/[“”"'`´]/g,'').replace(/[^a-z0-9α-ω]+/g,' ').trim().replace(/\s+/g,' ');
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
        }else field+=ch;
      }else{
        if(ch==='"') quoted=true;
        else if(ch===delimiter){ row.push(field); field=''; }
        else if(ch==='\n'){ row.push(field); rows.push(row); row=[]; field=''; }
        else if(ch==='\r'){
          if(text[i+1]==='\n') i++;
          row.push(field); rows.push(row); row=[]; field='';
        }else field+=ch;
      }
    }
    if(field!=='' || row.length){ row.push(field); rows.push(row); }
    while(rows.length && rows[rows.length-1].every(function(v){return String(v).trim()==='';})) rows.pop();
    return rows;
  }
  function detectDelimiter(text){
    var candidates=[';','\t',','], best=';', bestScore=-1;
    candidates.forEach(function(delim){
      var rows=parseDelimited(String(text).slice(0,30000),delim).slice(0,12).filter(function(r){return r.some(function(v){return String(v).trim()!=='';});});
      if(!rows.length) return;
      var counts=rows.map(function(r){return r.length;}), freq={};
      counts.forEach(function(c){freq[c]=(freq[c]||0)+1;});
      var modal=1, modalFreq=0;
      Object.keys(freq).forEach(function(k){if(freq[k]>modalFreq){modal=parseInt(k,10);modalFreq=freq[k];}});
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
    schema_version:['εκδοση μητρωου','schema version','registry version'],
    school_id:['αναγνωριστικο σχολειου','κωδικος σχολειου','school id','school_id','unit id'],
    school_name:['ονομασια σχολειου','ονομα σχολειου','σχολειο','school name','name'],
    school_type:['ειδος σχολειου','τυπος σχολειου','δομη','school type','type'],
    general_a:['α τμηματα','α ταξη τμηματα','τμηματα α','αριθμος τμηματων α','general a','sections a'],
    general_b:['β τμηματα','β ταξη τμηματα','τμηματα β','αριθμος τμηματων β','general b','sections b'],
    general_c:['γ τμηματα','γ ταξη τμηματα','τμηματα γ','αριθμος τμηματων γ','general c','sections c'],
    lang_a_fr:['α γαλλικα','α γαλλικα ομαδες','γαλλικα α','a french groups'],
    lang_a_de:['α γερμανικα','α γερμανικα ομαδες','γερμανικα α','a german groups'],
    lang_a_it:['α ιταλικα','α ιταλικα ομαδες','ιταλικα α','a italian groups'],
    lang_b_fr:['β γαλλικα','β γαλλικα ομαδες','γαλλικα β','b french groups'],
    lang_b_de:['β γερμανικα','β γερμανικα ομαδες','γερμανικα β','b german groups'],
    lang_b_it:['β ιταλικα','β ιταλικα ομαδες','ιταλικα β','b italian groups'],
    lang_c_fr:['γ γαλλικα','γ γαλλικα ομαδες','γαλλικα γ','c french groups'],
    lang_c_de:['γ γερμανικα','γ γερμανικα ομαδες','γερμανικα γ','c german groups'],
    lang_c_it:['γ ιταλικα','γ ιταλικα ομαδες','ιταλικα γ','c italian groups'],
    tech_split_a:['α τμηματα ανω 21','α τεχνολογια πληροφορικη χωρισμος','a tech split'],
    tech_split_b:['β τμηματα ανω 21','β τεχνολογια πληροφορικη χωρισμος','b tech split'],
    tech_split_c:['γ τμηματα ανω 21','γ τεχνολογια πληροφορικη χωρισμος','c tech split'],
    gel_b_hum:['β ανθρωπιστικων','β ομαδες ανθρωπιστικων','gel b humanities'],
    gel_b_sci:['β θετικων','β ομαδες θετικων','gel b science'],
    gel_c_hum:['γ ανθρωπιστικων','γ ομαδες ανθρωπιστικων','gel c humanities'],
    gel_c_scihealth:['γ θετικων υγειας','γ ομαδες θετικων υγειας','gel c science health'],
    gel_c_econit:['γ οικονομιας πληροφορικης','γ ομαδες οικονομιας πληροφορικης','gel c economics it'],
    gel_c_field_math:['γ 2ο πεδιο μαθηματικα','γ μαθηματικα 2ου πεδιου','gel c field math'],
    gel_c_field_bio:['γ 3ο πεδιο βιολογια','γ βιολογια 3ου πεδιου','gel c field biology'],
    gel_c_cond_math:['γ μαθηματικα γενικης','γ μαθηματικα γενικης παιδειας','gel c conditional math'],
    gel_c_cond_history:['γ ιστορια γενικης','γ ιστορια γενικης παιδειας','gel c conditional history'],
    ethics_a_exempt:['α απαλλασσομενοι','α ηθικη απαλλασσομενοι','a ethics exempt'],
    ethics_a_timely:['α ηθικη εντος 5ης','α εως 5η ημερα','a ethics timely'],
    ethics_a_equivalent:['α τμηματα ηθικης','α ισοδυναμα τμηματα ηθικης','a ethics sections'],
    ethics_b_exempt:['β απαλλασσομενοι','β ηθικη απαλλασσομενοι','b ethics exempt'],
    ethics_b_timely:['β ηθικη εντος 5ης','β εως 5η ημερα','b ethics timely'],
    ethics_b_equivalent:['β τμηματα ηθικης','β ισοδυναμα τμηματα ηθικης','b ethics sections'],
    ethics_c_exempt:['γ απαλλασσομενοι','γ ηθικη απαλλασσομενοι','c ethics exempt'],
    ethics_c_timely:['γ ηθικη εντος 5ης','γ εως 5η ημερα','c ethics timely'],
    ethics_c_equivalent:['γ τμηματα ηθικης','γ ισοδυναμα τμηματα ηθικης','c ethics sections']
  };

  function autoMap(headers){
    var normalized=headers.map(normalizeHeader), map={};
    Object.keys(FIELD_SYNONYMS).forEach(function(field){
      var syns=FIELD_SYNONYMS[field].map(normalizeHeader), idx=-1;
      for(var i=0;i<normalized.length && idx<0;i++) if(syns.indexOf(normalized[i])>=0) idx=i;
      if(idx>=0) map[field]=headers[idx];
    });
    return map;
  }
  function get(row,mapping,field){
    var h=mapping[field]; return h && Object.prototype.hasOwnProperty.call(row,h) ? row[h] : '';
  }
  function nonNegativeInt(value){
    var raw=String(value == null ? '' : value).trim();
    if(raw==='') return 0;
    var n=parseInt(raw.replace(/[^0-9-]/g,''),10);
    return isFinite(n) && n>=0 ? n : 0;
  }
  function nullableInt(value){
    var raw=String(value == null ? '' : value).trim();
    if(raw==='') return '';
    var n=parseInt(raw.replace(/[^0-9-]/g,''),10);
    return isFinite(n) && n>=0 ? n : '';
  }
  function normalizeBool(value){
    var t=normalizeHeader(value);
    if(t==='') return '';
    if(['1','ναι','yes','true','y'].indexOf(t)>=0) return '1';
    if(['0','οχι','no','false','n'].indexOf(t)>=0) return '0';
    return '';
  }
  function normalizeSchoolType(value){
    var raw=String(value == null ? '' : value).trim();
    var t=normalizeHeader(raw);
    if(t==='gymnasio' || (t.indexOf('γυμνασιο')>=0 && t.indexOf('εσπερινο')<0)) return 'gymnasio';
    if(t==='gel' || t==='γενικο λυκειο' || ((t.indexOf('γελ')>=0 || t.indexOf('γενικο λυκειο')>=0) && t.indexOf('εσπερινο')<0)) return 'gel';
    if(t.indexOf('εσπερινο γυμνασιο')>=0) return 'esperino_gymnasio';
    if(t.indexOf('εσπερινο')>=0 && (t.indexOf('γελ')>=0 || t.indexOf('γενικο λυκειο')>=0)) return 'esperino_gel';
    if(t.indexOf('προτυπο επαλ')>=0 || t.indexOf('π επαλ')>=0 || t==='pepal') return 'pepal';
    if(t.indexOf('επал')>=0) return 'epal';
    if(t.indexOf('επaλ')>=0 || t.indexOf('επαλ')>=0 || t==='epal') return 'epal';
    if(t.indexOf('εν εεγυ')>=0 || t.indexOf('ενεεγυ')>=0 || t==='eneegyl') return 'eneegyl';
    if(t.indexOf('εεεεκ')>=0 || t==='eeeek') return 'eeeek';
    if(t.indexOf('μουσικ')>=0) return 'mousiko';
    if(t.indexOf('καλλιτεχνικ')>=0) return 'kallitexniko';
    return raw;
  }
  var TYPE_LABELS={
    gymnasio:'Ημερήσιο Γυμνάσιο',
    gel:'Ημερήσιο Γενικό Λύκειο (ΓΕΛ)',
    esperino_gymnasio:'Εσπερινό Γυμνάσιο',
    esperino_gel:'Εσπερινό ΓΕΛ',
    epal:'ΕΠΑΛ',
    pepal:'Πρότυπο ΕΠΑΛ',
    eneegyl:'ΕΝ.Ε.Ε.ΓΥ.-Λ.',
    eeeek:'Ε.Ε.Ε.ΕΚ.',
    mousiko:'Μουσικό Σχολείο',
    kallitexniko:'Καλλιτεχνικό Σχολείο'
  };
  function isSupportedType(type){ return type==='gymnasio' || type==='gel'; }
  function typeLabel(type){ return TYPE_LABELS[type] || String(type || 'Άγνωστος τύπος'); }

  function rowToSchool(row,mapping,index){
    var type=normalizeSchoolType(get(row,mapping,'school_type'));
    var school={
      schema_version:String(get(row,mapping,'schema_version')||'').trim(),
      school_id:String(get(row,mapping,'school_id')||'').trim(),
      school_name:String(get(row,mapping,'school_name')||'').trim(),
      school_type:type,
      school_type_label:typeLabel(type),
      supported:isSupportedType(type),
      general_a:nonNegativeInt(get(row,mapping,'general_a')),
      general_b:nonNegativeInt(get(row,mapping,'general_b')),
      general_c:nonNegativeInt(get(row,mapping,'general_c')),
      lang_a_fr:nonNegativeInt(get(row,mapping,'lang_a_fr')),
      lang_a_de:nonNegativeInt(get(row,mapping,'lang_a_de')),
      lang_a_it:nonNegativeInt(get(row,mapping,'lang_a_it')),
      lang_b_fr:nonNegativeInt(get(row,mapping,'lang_b_fr')),
      lang_b_de:nonNegativeInt(get(row,mapping,'lang_b_de')),
      lang_b_it:nonNegativeInt(get(row,mapping,'lang_b_it')),
      lang_c_fr:nonNegativeInt(get(row,mapping,'lang_c_fr')),
      lang_c_de:nonNegativeInt(get(row,mapping,'lang_c_de')),
      lang_c_it:nonNegativeInt(get(row,mapping,'lang_c_it')),
      tech_split_a:nonNegativeInt(get(row,mapping,'tech_split_a')),
      tech_split_b:nonNegativeInt(get(row,mapping,'tech_split_b')),
      tech_split_c:nonNegativeInt(get(row,mapping,'tech_split_c')),
      gel_b_hum:nonNegativeInt(get(row,mapping,'gel_b_hum')),
      gel_b_sci:nonNegativeInt(get(row,mapping,'gel_b_sci')),
      gel_c_hum:nonNegativeInt(get(row,mapping,'gel_c_hum')),
      gel_c_scihealth:nonNegativeInt(get(row,mapping,'gel_c_scihealth')),
      gel_c_econit:nonNegativeInt(get(row,mapping,'gel_c_econit')),
      gel_c_field_math:nonNegativeInt(get(row,mapping,'gel_c_field_math')),
      gel_c_field_bio:nonNegativeInt(get(row,mapping,'gel_c_field_bio')),
      gel_c_cond_math:nonNegativeInt(get(row,mapping,'gel_c_cond_math')),
      gel_c_cond_history:nonNegativeInt(get(row,mapping,'gel_c_cond_history')),
      ethics_a_exempt:nullableInt(get(row,mapping,'ethics_a_exempt')),
      ethics_a_timely:normalizeBool(get(row,mapping,'ethics_a_timely')),
      ethics_a_equivalent:nullableInt(get(row,mapping,'ethics_a_equivalent')),
      ethics_b_exempt:nullableInt(get(row,mapping,'ethics_b_exempt')),
      ethics_b_timely:normalizeBool(get(row,mapping,'ethics_b_timely')),
      ethics_b_equivalent:nullableInt(get(row,mapping,'ethics_b_equivalent')),
      ethics_c_exempt:nullableInt(get(row,mapping,'ethics_c_exempt')),
      ethics_c_timely:normalizeBool(get(row,mapping,'ethics_c_timely')),
      ethics_c_equivalent:nullableInt(get(row,mapping,'ethics_c_equivalent'))
    };
    if(!school.school_id) school.school_id='school-'+String((index || 0)+1);
    return school;
  }

  var FORM_FIELDS=[
    'gym_general_a','gym_general_b','gym_general_c',
    'gym_lang_a_fr','gym_lang_a_de','gym_lang_a_it','gym_lang_b_fr','gym_lang_b_de','gym_lang_b_it','gym_lang_c_fr','gym_lang_c_de','gym_lang_c_it',
    'gym_tech_split_a','gym_tech_split_b','gym_tech_split_c',
    'gel_general_a','gel_general_b','gel_general_c',
    'gel_lang_a_fr','gel_lang_a_de','gel_lang_b_fr','gel_lang_b_de',
    'gel_b_hum','gel_b_sci','gel_c_hum','gel_c_scihealth','gel_c_econit','gel_c_field_math','gel_c_field_bio','gel_c_cond_math','gel_c_cond_history',
    'ethics_a_exempt','ethics_a_timely','ethics_a_equivalent','ethics_b_exempt','ethics_b_timely','ethics_b_equivalent','ethics_c_exempt','ethics_c_timely','ethics_c_equivalent'
  ];
  function schoolToFormValues(school){
    var out={school_registry_id:school.school_id || '',school_name:school.school_name || '',school_type:school.school_type || ''};
    FORM_FIELDS.forEach(function(field){out[field]='';});
    ['a','b','c'].forEach(function(g){
      out[(school.school_type==='gel'?'gel':'gym')+'_general_'+g]=school['general_'+g];
      out['ethics_'+g+'_exempt']=school['ethics_'+g+'_exempt'];
      out['ethics_'+g+'_timely']=school['ethics_'+g+'_timely'];
      out['ethics_'+g+'_equivalent']=school['ethics_'+g+'_equivalent'];
    });
    if(school.school_type==='gymnasio'){
      ['a','b','c'].forEach(function(g){
        out['gym_lang_'+g+'_fr']=school['lang_'+g+'_fr'];
        out['gym_lang_'+g+'_de']=school['lang_'+g+'_de'];
        out['gym_lang_'+g+'_it']=school['lang_'+g+'_it'];
        out['gym_tech_split_'+g]=school['tech_split_'+g];
      });
    }else if(school.school_type==='gel'){
      ['a','b'].forEach(function(g){
        out['gel_lang_'+g+'_fr']=school['lang_'+g+'_fr'];
        out['gel_lang_'+g+'_de']=school['lang_'+g+'_de'];
      });
      ['gel_b_hum','gel_b_sci','gel_c_hum','gel_c_scihealth','gel_c_econit','gel_c_field_math','gel_c_field_bio','gel_c_cond_math','gel_c_cond_history'].forEach(function(field){out[field]=school[field];});
    }
    return out;
  }

  var api={
    schemaVersion:'2026-09-06-school-registry-v1',
    registrySchemaVersion:'school_registry_v1',
    supportsMultipleSchools:true,
    supportedTypes:['gymnasio','gel'],
    placeholderTypes:['esperino_gymnasio','esperino_gel','epal','pepal','eneegyl','eeeek','mousiko','kallitexniko'],
    normalizeHeader:normalizeHeader,
    normalizeSchoolType:normalizeSchoolType,
    typeLabel:typeLabel,
    isSupportedType:isSupportedType,
    parseDelimited:parseDelimited,
    detectDelimiter:detectDelimiter,
    parse:parse,
    autoMap:autoMap,
    rowToSchool:rowToSchool,
    schoolToFormValues:schoolToFormValues,
    formFields:FORM_FIELDS.slice()
  };
  root.EducationSchoolCsv=api;
  if(typeof module!=='undefined' && module.exports) module.exports=api;
})(typeof window!=='undefined'?window:globalThis);
