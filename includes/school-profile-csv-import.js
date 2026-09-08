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
    school_id:['αναγνωριστικο σχολειου','school id','school_id','unit id','registry id'],
    school_code:['κωδικος υπουργειου','κωδικος σχολειου','κωδικος σχολικης μοναδας','myschool code','ministry code','school code','registry no'],
    school_address:['διευθυνση σχολειου','διευθυνση','ταχυδρομικη διευθυνση','school address','address'],
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
    ethics_c_equivalent:['γ τμηματα ηθικης','γ ισοδυναμα τμηματα ηθικης','c ethics sections'],
    lt_general_a:['λτ α γενικης','λυκειακες ταξεις α γενικης','lt general a'],
    lt_general_b:['λτ β γενικης','λυκειακες ταξεις β γενικης','lt general b'],
    lt_general_c:['λτ γ γενικης','λυκειακες ταξεις γ γενικης','lt general c'],
    lt_b_hum:['λτ β ανθρωπιστικων','lt b humanities'],
    lt_b_sci:['λτ β θετικων','lt b science'],
    lt_c_hum:['λτ γ ανθρωπιστικων','lt c humanities'],
    lt_c_scihealth:['λτ γ θετικων υγειας','lt c science health'],
    lt_c_econit:['λτ γ οικονομιας πληροφορικης','lt c economics it'],
    lt_lang_a_fr:['λτ α γαλλικα','λτ α γαλλικα ομαδες','lt a french groups'],
    lt_lang_a_de:['λτ α γερμανικα','λτ α γερμανικα ομαδες','lt a german groups'],
    lt_lang_b_fr:['λτ β γαλλικα','λτ β γαλλικα ομαδες','lt b french groups'],
    lt_lang_b_de:['λτ β γερμανικα','λτ β γερμανικα ομαδες','lt b german groups'],
    lt_c_field_math:['λτ γ μαθηματικα 2ου πεδιου','lt c field math'],
    lt_c_field_bio:['λτ γ βιολογια 3ου πεδιου','lt c field biology'],
    lt_c_cond_math:['λτ γ μαθηματικα γενικης παιδειας','lt c conditional math'],
    lt_c_cond_history:['λτ γ ιστορια γενικης παιδειας','lt c conditional history'],
    lt_ethics_a_exempt:['λτ α απαλλασσομενοι','lt a ethics exempt'],
    lt_ethics_a_timely:['λτ α ηθικη εντος 5ης','lt a ethics timely'],
    lt_ethics_a_equivalent:['λτ α τμηματα ηθικης','lt a ethics sections'],
    lt_ethics_b_exempt:['λτ β απαλλασσομενοι','lt b ethics exempt'],
    lt_ethics_b_timely:['λτ β ηθικη εντος 5ης','lt b ethics timely'],
    lt_ethics_b_equivalent:['λτ β τμηματα ηθικης','lt b ethics sections'],
    lt_ethics_c_exempt:['λτ γ απαλλασσομενοι','lt c ethics exempt'],
    lt_ethics_c_timely:['λτ γ ηθικη εντος 5ης','lt c ethics timely'],
    lt_ethics_c_equivalent:['λτ γ τμηματα ηθικης','lt c ethics sections']
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
  var MAX_BASIC_SECTIONS=120;
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
    var canonical=raw.toLowerCase();
    if(['gymnasio','gel','gymnasio_lt','esperino_gymnasio','esperino_gel','epal','esperino_epal','pepal','eneegyl','eeeek','mousiko','kallitexniko','sek'].indexOf(canonical)>=0) return canonical;
    var t=normalizeHeader(raw);
    var compact=t.replace(/\s+/g,'');
    /* Specific structures must be detected before generic words such as «Γυμνάσιο» or «Λύκειο». */
    if(t.indexOf('μουσικ')>=0) return 'mousiko';
    if(t.indexOf('καλλιτεχνικ')>=0) return 'kallitexniko';
    if(compact.indexOf('ενεεγυλ')>=0 || compact.indexOf('ενεεγυ')>=0 || t.indexOf('ενιαιο ειδικο επαγγελματικο γυμνασιο λυκειο')>=0) return 'eneegyl';
    if(compact.indexOf('εεεεκ')>=0) return 'eeeek';
    if(t.indexOf('προτυπο επαγγελματικο λυκειο')>=0 || t.indexOf('προτυπο επαλ')>=0 || compact.indexOf('πεπαλ')>=0) return 'pepal';
    if(t.indexOf('εσπερινο')>=0 && (compact.indexOf('επαλ')>=0 || t.indexOf('επαγγελματικο λυκειο')>=0)) return 'esperino_epal';
    if(compact.indexOf('επαλ')>=0 || t.indexOf('επαγγελματικο λυκειο')>=0) return 'epal';
    if(t.indexOf('γυμνασιο')>=0 && (t.indexOf('λυκειακ')>=0 || t.indexOf('με λ τ')>=0)) return 'gymnasio_lt';
    if(t.indexOf('εσπερινο γυμνασιο')>=0) return 'esperino_gymnasio';
    if(t==='gymnasio' || t.indexOf('γυμνασιο')>=0) return 'gymnasio';
    if(t.indexOf('εσπερινο')>=0 && (t.indexOf('γελ')>=0 || t.indexOf('γενικο λυκειο')>=0)) return 'esperino_gel';
    if(t==='gel' || t==='γενικο λυκειο' || t.indexOf('γελ')>=0 || t.indexOf('γενικο λυκειο')>=0) return 'gel';
    if(t.indexOf('εργαστηριακο κεντρο')>=0 || t.indexOf('σχολικο εργαστηριακο κεντρο')>=0 || t==='εκ' || t==='σεκ') return 'sek';
    return raw;
  }
  var TYPE_LABELS={
    gymnasio:'Ημερήσιο Γυμνάσιο',
    gel:'Ημερήσιο Γενικό Λύκειο',
    gymnasio_lt:'Γυμνάσιο με Λ.Τ.',
    esperino_gymnasio:'Εσπερινό Γυμνάσιο',
    esperino_gel:'Εσπερινό ΓΕΛ',
    epal:'ΕΠΑΛ',
    esperino_epal:'Εσπερινό ΕΠΑΛ',
    pepal:'Πρότυπο ΕΠΑΛ',
    eneegyl:'ΕΝ.Ε.Ε.ΓΥ.-Λ.',
    eeeek:'Ε.Ε.Ε.ΕΚ.',
    mousiko:'Μουσικό Σχολείο',
    kallitexniko:'Καλλιτεχνικό Σχολείο',
    sek:'Εργαστηριακό Κέντρο'
  };
  function isSupportedType(type){ return type==='gymnasio' || type==='gel' || type==='esperino_gymnasio' || type==='esperino_gel' || type==='gymnasio_lt'; }
  function typeLabel(type){ return TYPE_LABELS[type] || String(type || 'Άγνωστος τύπος'); }

  function rowToSchool(row,mapping,index){
    var type=normalizeSchoolType(get(row,mapping,'school_type'));
    var school={
      schema_version:String(get(row,mapping,'schema_version')||'').trim(),
      school_id:String(get(row,mapping,'school_id')||'').trim(),
      school_code:String(get(row,mapping,'school_code')||'').trim(),
      school_address:String(get(row,mapping,'school_address')||'').trim(),
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
      ethics_c_equivalent:nullableInt(get(row,mapping,'ethics_c_equivalent')),
      lt_general_a:nonNegativeInt(get(row,mapping,'lt_general_a')),
      lt_general_b:nonNegativeInt(get(row,mapping,'lt_general_b')),
      lt_general_c:nonNegativeInt(get(row,mapping,'lt_general_c')),
      lt_b_hum:nonNegativeInt(get(row,mapping,'lt_b_hum')),
      lt_b_sci:nonNegativeInt(get(row,mapping,'lt_b_sci')),
      lt_c_hum:nonNegativeInt(get(row,mapping,'lt_c_hum')),
      lt_c_scihealth:nonNegativeInt(get(row,mapping,'lt_c_scihealth')),
      lt_c_econit:nonNegativeInt(get(row,mapping,'lt_c_econit')),
      lt_lang_a_fr:nonNegativeInt(get(row,mapping,'lt_lang_a_fr')),
      lt_lang_a_de:nonNegativeInt(get(row,mapping,'lt_lang_a_de')),
      lt_lang_b_fr:nonNegativeInt(get(row,mapping,'lt_lang_b_fr')),
      lt_lang_b_de:nonNegativeInt(get(row,mapping,'lt_lang_b_de')),
      lt_c_field_math:nonNegativeInt(get(row,mapping,'lt_c_field_math')),
      lt_c_field_bio:nonNegativeInt(get(row,mapping,'lt_c_field_bio')),
      lt_c_cond_math:nonNegativeInt(get(row,mapping,'lt_c_cond_math')),
      lt_c_cond_history:nonNegativeInt(get(row,mapping,'lt_c_cond_history')),
      lt_ethics_a_exempt:nullableInt(get(row,mapping,'lt_ethics_a_exempt')),
      lt_ethics_a_timely:normalizeBool(get(row,mapping,'lt_ethics_a_timely')),
      lt_ethics_a_equivalent:nullableInt(get(row,mapping,'lt_ethics_a_equivalent')),
      lt_ethics_b_exempt:nullableInt(get(row,mapping,'lt_ethics_b_exempt')),
      lt_ethics_b_timely:normalizeBool(get(row,mapping,'lt_ethics_b_timely')),
      lt_ethics_b_equivalent:nullableInt(get(row,mapping,'lt_ethics_b_equivalent')),
      lt_ethics_c_exempt:nullableInt(get(row,mapping,'lt_ethics_c_exempt')),
      lt_ethics_c_timely:normalizeBool(get(row,mapping,'lt_ethics_c_timely')),
      lt_ethics_c_equivalent:nullableInt(get(row,mapping,'lt_ethics_c_equivalent'))
    };
    if(!school.school_id) school.school_id=school.school_code || 'school-'+String((index || 0)+1);
    return school;
  }
  function basicSectionTotal(school){
    var total=['general_a','general_b','general_c'].reduce(function(sum,key){return sum+nonNegativeInt(school&&school[key]);},0);
    if(school && school.school_type==='gymnasio_lt') total+=['lt_general_a','lt_general_b','lt_general_c'].reduce(function(sum,key){return sum+nonNegativeInt(school&&school[key]);},0);
    return total;
  }
  function validateRegistry(records){
    var seenIds={},seenCodes={},duplicateIds=[],duplicateCodes=[],oversized=[];
    (records||[]).forEach(function(school,index){
      var id=String(school&&school.school_id||'').trim(), code=String(school&&school.school_code||'').trim();
      if(id){ if(Object.prototype.hasOwnProperty.call(seenIds,id)) duplicateIds.push(id); else seenIds[id]=index; }
      if(code){ if(Object.prototype.hasOwnProperty.call(seenCodes,code)) duplicateCodes.push(code); else seenCodes[code]=index; }
      var total=basicSectionTotal(school);
      if(total>MAX_BASIC_SECTIONS) oversized.push({index:index,school_id:id,school_code:code,school_name:String(school&&school.school_name||''),total:total});
    });
    return {valid:duplicateIds.length===0&&duplicateCodes.length===0&&oversized.length===0,duplicate_ids:Array.from(new Set(duplicateIds)),duplicate_codes:Array.from(new Set(duplicateCodes)),oversized:oversized};
  }

  var FORM_FIELDS=[
    'gym_general_a','gym_general_b','gym_general_c',
    'gym_lang_a_fr','gym_lang_a_de','gym_lang_a_it','gym_lang_b_fr','gym_lang_b_de','gym_lang_b_it','gym_lang_c_fr','gym_lang_c_de','gym_lang_c_it',
    'gym_tech_split_a','gym_tech_split_b','gym_tech_split_c',
    'gel_general_a','gel_general_b','gel_general_c',
    'gel_lang_a_fr','gel_lang_a_de','gel_lang_b_fr','gel_lang_b_de',
    'gel_b_hum','gel_b_sci','gel_c_hum','gel_c_scihealth','gel_c_econit','gel_c_field_math','gel_c_field_bio','gel_c_cond_math','gel_c_cond_history','egel_b_period',
    'ethics_a_exempt','ethics_a_timely','ethics_a_equivalent','ethics_b_exempt','ethics_b_timely','ethics_b_equivalent','ethics_c_exempt','ethics_c_timely','ethics_c_equivalent',
    'lt_ethics_a_exempt','lt_ethics_a_timely','lt_ethics_a_equivalent','lt_ethics_b_exempt','lt_ethics_b_timely','lt_ethics_b_equivalent','lt_ethics_c_exempt','lt_ethics_c_timely','lt_ethics_c_equivalent'
  ];
  function schoolToFormValues(school){
    var out={school_registry_id:school.school_id || '',school_code:school.school_code || '',school_name:school.school_name || '',school_type:school.school_type || ''};
    FORM_FIELDS.forEach(function(field){out[field]='';});
    var isGelFamily=school.school_type==='gel' || school.school_type==='esperino_gel';
    var isComposite=school.school_type==='gymnasio_lt';
    ['a','b','c'].forEach(function(g){
      if(isComposite){
        out['gym_general_'+g]=school['general_'+g];
        out['gel_general_'+g]=school['lt_general_'+g];
      }else{
        out[(isGelFamily?'gel':'gym')+'_general_'+g]=school['general_'+g];
      }
      out['ethics_'+g+'_exempt']=school['ethics_'+g+'_exempt'];
      out['ethics_'+g+'_timely']=school['ethics_'+g+'_timely'];
      out['ethics_'+g+'_equivalent']=school['ethics_'+g+'_equivalent'];
    });
    if(school.school_type==='gymnasio' || isComposite){
      ['a','b','c'].forEach(function(g){
        out['gym_lang_'+g+'_fr']=school['lang_'+g+'_fr'];
        out['gym_lang_'+g+'_de']=school['lang_'+g+'_de'];
        out['gym_lang_'+g+'_it']=school['lang_'+g+'_it'];
        out['gym_tech_split_'+g]=school['tech_split_'+g];
      });
    }
    if(isGelFamily || isComposite){
      if(school.school_type==='gel'){
        ['a','b'].forEach(function(g){
          out['gel_lang_'+g+'_fr']=school['lang_'+g+'_fr'];
          out['gel_lang_'+g+'_de']=school['lang_'+g+'_de'];
        });
      }
      if(isComposite){
        out['gel_b_hum']=school.lt_b_hum; out['gel_b_sci']=school.lt_b_sci;
        out['gel_c_hum']=school.lt_c_hum; out['gel_c_scihealth']=school.lt_c_scihealth; out['gel_c_econit']=school.lt_c_econit;
        out['gel_lang_a_fr']=school.lt_lang_a_fr; out['gel_lang_a_de']=school.lt_lang_a_de;
        out['gel_lang_b_fr']=school.lt_lang_b_fr; out['gel_lang_b_de']=school.lt_lang_b_de;
        out['gel_c_field_math']=school.lt_c_field_math; out['gel_c_field_bio']=school.lt_c_field_bio;
        out['gel_c_cond_math']=school.lt_c_cond_math; out['gel_c_cond_history']=school.lt_c_cond_history;
        ['a','b','c'].forEach(function(g){
          out['lt_ethics_'+g+'_exempt']=school['lt_ethics_'+g+'_exempt'];
          out['lt_ethics_'+g+'_timely']=school['lt_ethics_'+g+'_timely'];
          out['lt_ethics_'+g+'_equivalent']=school['lt_ethics_'+g+'_equivalent'];
        });
      }else{
        ['gel_b_hum','gel_b_sci','gel_c_hum','gel_c_scihealth','gel_c_econit','gel_c_field_math','gel_c_field_bio','gel_c_cond_math','gel_c_cond_history'].forEach(function(field){out[field]=school[field];});
      }
      if(school.school_type==='esperino_gel') out['egel_b_period']='Α΄ τετράμηνο';
    }
    return out;
  }

  var BUILTIN_CORFU_REGISTRY_CSV="Έκδοση μητρώου;Αναγνωριστικό σχολείου;Κωδικός Υπουργείου;Ονομασία σχολείου;Είδος σχολείου;Διεύθυνση σχολείου;Α τμήματα;Β τμήματα;Γ τμήματα;Α Γαλλικά ομάδες;Α Γερμανικά ομάδες;Α Ιταλικά ομάδες;Β Γαλλικά ομάδες;Β Γερμανικά ομάδες;Β Ιταλικά ομάδες;Γ Γαλλικά ομάδες;Γ Γερμανικά ομάδες;Γ Ιταλικά ομάδες;Α τμήματα άνω 21;Β τμήματα άνω 21;Γ τμήματα άνω 21;Β ομάδες Ανθρωπιστικών;Β ομάδες Θετικών;Γ ομάδες Ανθρωπιστικών;Γ ομάδες Θετικών Υγείας;Γ ομάδες Οικονομίας Πληροφορικής;Γ Μαθηματικά 2ου πεδίου;Γ Βιολογία 3ου πεδίου;Γ Μαθηματικά Γενικής Παιδείας;Γ Ιστορία Γενικής Παιδείας;Α απαλλασσόμενοι;Α Ηθική εντός 5ης;Α τμήματα Ηθικής;Β απαλλασσόμενοι;Β Ηθική εντός 5ης;Β τμήματα Ηθικής;Γ απαλλασσόμενοι;Γ Ηθική εντός 5ης;Γ τμήματα Ηθικής;Σχολικό έτος;Χαρακτηρισμός 2026-27;Γυμνάσιο Α μαθητές;Γυμνάσιο Β μαθητές;Γυμνάσιο Γ μαθητές;Γυμνάσιο Δ μαθητές;Γυμνάσιο Δ τμήματα;Λύκειο Α μαθητές;Λύκειο Β μαθητές;Λύκειο Γ μαθητές;ΛΤ Α Γενικής;ΛΤ Β Γενικής;ΛΤ Γ Γενικής;ΛΤ Β Ανθρωπιστικών;ΛΤ Β Θετικών;ΛΤ Γ Ανθρωπιστικών;ΛΤ Γ Θετικών Υγείας;ΛΤ Γ Οικονομίας Πληροφορικής;Ολιγομελή εγκεκριμένα 2026-27;Πηγή τμημάτων;Κατάσταση πληρότητας;Εκκρεμή πεδία;Σημειώσεις\nschool_registry_v1;2401010;2401010;1ο ΗΜΕΡΗΣΙΟ ΓΥΜΝΑΣΙΟ ΚΕΡΚΥΡΑΣ;Ημερήσιο Γυμνάσιο;ΡΙΖΟΣΠΑΣΤΩΝ ΒΟΥΛΕΥΤΩΝ ΙΟΝΙΟΥ ΒΟΥΛΗΣ 6, ΤΚ 49100;3;3;4;2;1;0;2;1;0;2;2;0;3;3;3;;;;;;;;;;;;;;;;;;;2026-2027;;81;72;89;;;;;;;;;;;;;;;Απόφαση ΔΔΕ Κέρκυρας 3346/14-07-2026 (Γυμνάσια) + myschool stat3_1 05-09-2026 (τρέχοντα βασικά τμήματα/μαθητές) + myschool stat3_10 06-09-2026 (ομάδες ξένων γλωσσών);Πλήρες για αυτόματο υπολογισμό βασικών διδακτικών αναγκών;;\nschool_registry_v1;2401020;2401020;2ο ΗΜΕΡΗΣΙΟ ΓΥΜΝΑΣΙΟ ΚΕΡΚΥΡΑΣ;Ημερήσιο Γυμνάσιο;ΖΑΜΠΕΛΗ 1, ΤΚ 49100;4;4;3;2;2;0;1;3;0;1;2;0;4;1;1;;;;;;;;;;;;;;;;;;;2026-2027;;88;75;52;;;;;;;;;;;;;;;Απόφαση ΔΔΕ Κέρκυρας 3346/14-07-2026 (Γυμνάσια) + myschool stat3_1 05-09-2026 (τρέχοντα βασικά τμήματα/μαθητές) + myschool stat3_10 06-09-2026 (ομάδες ξένων γλωσσών);Πλήρες για αυτόματο υπολογισμό βασικών διδακτικών αναγκών;;\nschool_registry_v1;2401030;2401030;3ο ΗΜΕΡΗΣΙΟ ΓΥΜΝΑΣΙΟ ΚΕΡΚΥΡΑΣ;Ημερήσιο Γυμνάσιο;ΤΕΡΜΑ ΚΟΛΟΚΟΤΡΩΝΗ, ΤΚ 49100;3;3;3;2;2;0;1;2;0;2;2;0;3;2;1;;;;;;;;;;;;;;;;;;;2026-2027;;69;69;63;;;;;;;;;;;;;;;Απόφαση ΔΔΕ Κέρκυρας 3346/14-07-2026 (Γυμνάσια) + myschool stat3_1 05-09-2026 (τρέχοντα βασικά τμήματα/μαθητές) + myschool stat3_10 06-09-2026 (ομάδες ξένων γλωσσών);Πλήρες για αυτόματο υπολογισμό βασικών διδακτικών αναγκών;;\nschool_registry_v1;2401040;2401040;4ο ΗΜΕΡΗΣΙΟ ΓΥΜΝΑΣΙΟ ΚΕΡΚΥΡΑΣ;Ημερήσιο Γυμνάσιο;ΚΟΛΟΚΟΤΡΩΝΗ ΤΕΡΜΑ, ΤΚ 49100;3;3;3;2;1;0;1;2;0;1;2;0;2;0;0;;;;;;;;;;;;;;;;;;;2026-2027;;61;55;51;;;;;;;;;;;;;;;Απόφαση ΔΔΕ Κέρκυρας 3346/14-07-2026 (Γυμνάσια) + myschool stat3_1 05-09-2026 (τρέχοντα βασικά τμήματα/μαθητές) + myschool stat3_10 06-09-2026 (ομάδες ξένων γλωσσών);Πλήρες για αυτόματο υπολογισμό βασικών διδακτικών αναγκών;;\nschool_registry_v1;2401050;2401050;5ο ΗΜΕΡΗΣΙΟ ΓΥΜΝΑΣΙΟ ΚΕΡΚΥΡΑΣ;Ημερήσιο Γυμνάσιο;ΡΙΖΟΣΠΑΣΤΩΝ ΒΟΥΛΕΥΤΩΝ ΙΟΝΙΟΥ ΒΟΥΛΗΣ 6, ΤΚ 49100;3;4;4;2;1;0;3;3;0;3;2;0;3;1;2;;;;;;;;;;;;;;;;;;;2026-2027;;71;81;84;;;;;;;;;;;;;;;Απόφαση ΔΔΕ Κέρκυρας 3346/14-07-2026 (Γυμνάσια) + myschool stat3_1 05-09-2026 (τρέχοντα βασικά τμήματα/μαθητές) + myschool stat3_10 06-09-2026 (ομάδες ξένων γλωσσών);Πλήρες για αυτόματο υπολογισμό βασικών διδακτικών αναγκών;;\nschool_registry_v1;2401070;2401070;6ο ΗΜΕΡΗΣΙΟ ΓΥΜΝΑΣΙΟ ΚΕΡΚΥΡΑΣ;Ημερήσιο Γυμνάσιο;ΕΛΕΥΘ.ΒΕΝΙΖΕΛΟΥ 42, ΤΚ 49100;3;3;4;3;1;0;2;2;0;2;2;0;2;3;0;;;;;;;;;;;;;;;;;;;2026-2027;;70;71;76;;;;;;;;;;;;;;;Απόφαση ΔΔΕ Κέρκυρας 3346/14-07-2026 (Γυμνάσια) + myschool stat3_1 05-09-2026 (τρέχοντα βασικά τμήματα/μαθητές) + myschool stat3_10 06-09-2026 (ομάδες ξένων γλωσσών);Πλήρες για αυτόματο υπολογισμό βασικών διδακτικών αναγκών;;\nschool_registry_v1;2401055;2401055;7ο ΠΕΙΡΑΜΑΤΙΚΟ ΓΥΜΝΑΣΙΟ ΚΕΡΚΥΡΑΣ;Ημερήσιο Γυμνάσιο;ΑΓΙΟΙ ΘΕΟΔΩΡΟΙ - ΚΕΡΚΥΡΑ, ΤΚ 49100;4;4;4;3;2;0;3;1;0;2;3;0;3;4;1;;;;;;;;;;;;;;;;;;;2026-2027;Πειραματικό από το σχολικό έτος 2026-2027;91;94;86;;;;;;;;;;;;;;;Απόφαση ΔΔΕ Κέρκυρας 3346/14-07-2026 (Γυμνάσια) + myschool stat3_1 05-09-2026 (τρέχοντα βασικά τμήματα/μαθητές) + myschool stat3_10 06-09-2026 (ομάδες ξένων γλωσσών);Πλήρες για αυτόματο υπολογισμό βασικών διδακτικών αναγκών;;Ο κωδικός 2401055 είναι το 7ο Γυμνάσιο Κέρκυρας, που λειτουργεί ως Πειραματικό από 2026-2027.\nschool_registry_v1;2402090;2402090;ΓΥΜΝΑΣΙΟ ΑΓΙΟΥ ΙΩΑΝΝΗ;Ημερήσιο Γυμνάσιο;ΑΓΙΟΣ ΙΩΑΝΝΗΣ ΚΕΡΚΥΡΑΣ, ΤΚ 49150;3;2;2;1;2;0;1;1;0;0;2;0;3;0;0;;;;;;;;;;;;;;;;;;;2026-2027;;69;31;29;;;;;;;;;;;;;;;Απόφαση ΔΔΕ Κέρκυρας 3346/14-07-2026 (Γυμνάσια) + myschool stat3_1 05-09-2026 (τρέχοντα βασικά τμήματα/μαθητές) + myschool stat3_10 06-09-2026 (ομάδες ξένων γλωσσών);Πλήρες για αυτόματο υπολογισμό βασικών διδακτικών αναγκών;;\nschool_registry_v1;2406020;2406020;ΓΥΜΝΑΣΙΟ ΑΜΦΙΠΑΓΙΤΩΝ «ΑΝΔΡΕΑΣ ΚΑΛΒΟΣ»;Ημερήσιο Γυμνάσιο;ΠΕΡΟΥΛΑΔΕΣ, ΤΚ 49081;2;2;2;2;0;0;2;0;0;2;0;0;0;0;0;;;;;;;;;;;;;;;;;;;2026-2027;;32;25;23;;;;;;;;;;;;;;;Απόφαση ΔΔΕ Κέρκυρας 3346/14-07-2026 (Γυμνάσια) + myschool stat3_1 05-09-2026 (τρέχοντα βασικά τμήματα/μαθητές) + myschool stat3_10 06-09-2026 (ομάδες ξένων γλωσσών);Πλήρες για αυτόματο υπολογισμό βασικών διδακτικών αναγκών;;\nschool_registry_v1;2405010;2405010;ΓΥΜΝΑΣΙΟ ΚΑΙ ΛΥΚΕΙΑΚΕΣ ΤΑΞΕΙΣ ΑΡΓΥΡΑΔΩΝ ΚΕΡΚΥΡΑΣ;Γυμνάσιο με Λυκειακές Τάξεις;ΑΡΓΥΡΑΔΕΣ, ΤΚ 49080;2;1;1;0;2;0;0;1;0;0;1;0;0;1;1;1;1;1;1;1;;;;;;;;;;;;;;2026-2027;;32;22;24;;;34;25;30;2;1;2;1;1;1;1;1;Γ Ανθρωπιστικών Σπουδών | Γ Θετικών Σπουδών και Σπουδών Υγείας;Απόφαση ΔΔΕ Κέρκυρας 3346/14-07-2026 (Γυμνάσια) + Απόφαση ΔΔΕ Κέρκυρας 3446/24-07-2026 (ΓΕΛ/ΛΤ) + μεταγενέστερες εγκρίσεις ολιγομελών + myschool stat3_1 05-09-2026 (τρέχοντα βασικά τμήματα/μαθητές) + myschool stat3_10 06-09-2026 (ομάδες ξένων γλωσσών);Δομικά στοιχεία καταχωρισμένα · τύπος προσωρινά ανενεργός στο εργαλείο;;Τα γενικά Α/Β/Γ του βασικού schema κρατούν το γυμνασιακό σκέλος· τα στοιχεία Λυκειακών Τάξεων διατηρούνται στις πρόσθετες στήλες ΛΤ.\nschool_registry_v1;2406010;2406010;ΓΥΜΝΑΣΙΟ ΚΑΡΟΥΣΑΔΩΝ;Ημερήσιο Γυμνάσιο;ΚΑΡΟΥΣΑΔΕΣ, ΤΚ 49081;1;1;1;0;1;0;0;1;0;0;1;0;1;0;0;;;;;;;;;;;;;;;;;;;2026-2027;;23;18;17;;;;;;;;;;;;;;;Απόφαση ΔΔΕ Κέρκυρας 3346/14-07-2026 (Γυμνάσια) + myschool stat3_1 05-09-2026 (τρέχοντα βασικά τμήματα/μαθητές) + myschool stat3_10 06-09-2026 (ομάδες ξένων γλωσσών);Πλήρες για αυτόματο υπολογισμό βασικών διδακτικών αναγκών;;\nschool_registry_v1;2402050;2402050;ΓΥΜΝΑΣΙΟ ΦΑΙΑΚΩΝ ΚΕΡΚΥΡΑΣ;Ημερήσιο Γυμνάσιο;ΔΑΣΙΑ - ΚΕΡΚΥΡΑ, ΤΚ 49083;3;2;3;1;2;0;1;1;0;1;2;0;1;1;1;;;;;;;;;;;;;;;;;;;2026-2027;;61;40;54;;;;;;;;;;;;;;;Απόφαση ΔΔΕ Κέρκυρας 3346/14-07-2026 (Γυμνάσια) + myschool stat3_1 05-09-2026 (τρέχοντα βασικά τμήματα/μαθητές) + myschool stat3_10 06-09-2026 (ομάδες ξένων γλωσσών);Πλήρες για αυτόματο υπολογισμό βασικών διδακτικών αναγκών;;Η Α΄ τάξη εμφανίζεται 3 τμήματα / 61 μαθητές στο myschool 05-09-2026, έναντι 2 τμημάτων / 46 μαθητών στην απόφαση 14-07-2026· χρησιμοποιήθηκε η νεότερη πραγματική εικόνα.\nschool_registry_v1;2411001;2411001;ΕΝΙΑΙΟ ΕΙΔΙΚΟ ΕΠΑΓΓΕΛΜΑΤΙΚΟ ΓΥΜΝΑΣΙΟ-ΛΥΚΕΙΟ ΚΕΡΚΥΡΑΣ;ΕΝ.Ε.Ε.ΓΥ.-Λ.;10η πάροδος, Σπύρου Ραθ 1, Κέρκυρα, ΤΚ 49132;2;2;2;0;0;0;0;0;0;0;0;0;0;0;0;;;;;;;;;;;;;;;;;;;2026-2027;;11;13;14;6;1;13;9;4;;;;;;;;;;Απόφαση ΔΔΕ Κέρκυρας 3346/14-07-2026 (Γυμνάσια) + myschool stat3_1 05-09-2026 (τρέχοντα βασικά τμήματα/μαθητές) + myschool stat3_10 06-09-2026 (ομάδες ξένων γλωσσών);Δομικά στοιχεία καταχωρισμένα · τύπος προσωρινά ανενεργός στο εργαλείο;;\nschool_registry_v1;2401060;2401060;ΕΣΠΕΡΙΝΟ ΓΥΜΝΑΣΙΟ ΚΕΡΚΥΡΑΣ;Εσπερινό Γυμνάσιο;ΣΠΥΡΟΥ ΞΥΝΔΑ 2, ΤΚ 49100;1;1;1;;;;;;;;;;0;0;0;;;;;;;;;;;;;;;;;;;2026-2027;;2;3;5;;;;;;;;;;;;;;;Απόφαση ΔΔΕ Κέρκυρας 3346/14-07-2026 (Γυμνάσια) + myschool stat3_1 05-09-2026 (τρέχοντα βασικά τμήματα/μαθητές);Δομικά στοιχεία καταχωρισμένα · τύπος προσωρινά ανενεργός στο εργαλείο;;\nschool_registry_v1;2404010;2404010;ΗΜΕΡΗΣΙΟ ΓΥΜΝΑΣΙΟ ΑΓΡΟΥ;Ημερήσιο Γυμνάσιο;ΑΓΡΟΣ, ΤΚ 49083;2;2;2;1;1;0;1;1;0;1;1;0;0;0;0;;;;;;;;;;;;;;;;;;;2026-2027;;34;31;29;;;;;;;;;;;;;;;Απόφαση ΔΔΕ Κέρκυρας 3346/14-07-2026 (Γυμνάσια) + myschool stat3_1 05-09-2026 (τρέχοντα βασικά τμήματα/μαθητές) + myschool stat3_10 06-09-2026 (ομάδες ξένων γλωσσών);Πλήρες για αυτόματο υπολογισμό βασικών διδακτικών αναγκών;;\nschool_registry_v1;2410010;2410010;ΗΜΕΡΗΣΙΟ ΓΥΜΝΑΣΙΟ ΘΙΝΑΛΙΟΥ ΚΕΡΚΥΡΑΣ - ΓΥΜΝΑΣΙΟ ΘΙΝΑΛΙΟΥ;Ημερήσιο Γυμνάσιο;ΑΧΑΡΑΒΗ ΚΕΡΚΥΡΑ, ΤΚ 49081;2;3;2;1;1;0;1;2;0;0;2;0;2;0;2;;;;;;;;;;;;;;;;;;;2026-2027;;50;60;47;;;;;;;;;;;;;;;Απόφαση ΔΔΕ Κέρκυρας 3346/14-07-2026 (Γυμνάσια) + myschool stat3_1 05-09-2026 (τρέχοντα βασικά τμήματα/μαθητές) + myschool stat3_10 06-09-2026 (ομάδες ξένων γλωσσών);Πλήρες για αυτόματο υπολογισμό βασικών διδακτικών αναγκών;;\nschool_registry_v1;2407010;2407010;ΗΜΕΡΗΣΙΟ ΓΥΜΝΑΣΙΟ ΚΑΣΤΕΛΛΑΝΩΝ ΜΕΣΗΣ ΚΕΡΚΥΡΑΣ;Ημερήσιο Γυμνάσιο;ΚΑΣΤΕΛΛΑΝΟΙ ΜΕΣΗΣ, ΤΚ 49084;4;4;3;2;2;0;2;2;0;2;2;0;3;0;1;;;;;;;;;;;;;;;;;;;2026-2027;;87;69;56;;;;;;;;;;;;;;;Απόφαση ΔΔΕ Κέρκυρας 3346/14-07-2026 (Γυμνάσια) + myschool stat3_1 05-09-2026 (τρέχοντα βασικά τμήματα/μαθητές) + myschool stat3_10 06-09-2026 (ομάδες ξένων γλωσσών);Πλήρες για αυτόματο υπολογισμό βασικών διδακτικών αναγκών;;\nschool_registry_v1;2402010;2402010;ΗΜΕΡΗΣΙΟ ΓΥΜΝΑΣΙΟ ΛΕΥΚΙΜΜΗΣ ΚΕΡΚΥΡΑΣ;Ημερήσιο Γυμνάσιο;ΠΟΛΥΤΕΧΝΕΙΟΥ 2, ΤΚ 49080;3;3;3;0;3;0;0;3;0;0;3;0;3;0;0;;;;;;;;;;;;;;;;;;;2026-2027;;73;56;54;;;;;;;;;;;;;;;Απόφαση ΔΔΕ Κέρκυρας 3346/14-07-2026 (Γυμνάσια) + myschool stat3_1 05-09-2026 (τρέχοντα βασικά τμήματα/μαθητές) + myschool stat3_10 06-09-2026 (ομάδες ξένων γλωσσών);Πλήρες για αυτόματο υπολογισμό βασικών διδακτικών αναγκών;;\nschool_registry_v1;2408050;2408050;ΗΜΕΡΗΣΙΟ ΓΥΜΝΑΣΙΟ ΛΙΑΠΑΔΩΝ ΚΕΡΚΥΡΑΣ;Ημερήσιο Γυμνάσιο;ΛΙΑΠΑΔΕΣ, ΤΚ 49083;1;1;1;0;1;0;0;1;0;0;1;0;0;0;0;;;;;;;;;;;;;;;;;;;2026-2027;;14;14;6;;;;;;;;;;;;;;;Απόφαση ΔΔΕ Κέρκυρας 3346/14-07-2026 (Γυμνάσια) + myschool stat3_1 05-09-2026 (τρέχοντα βασικά τμήματα/μαθητές) + myschool stat3_10 06-09-2026 (ομάδες ξένων γλωσσών);Πλήρες για αυτόματο υπολογισμό βασικών διδακτικών αναγκών;;\nschool_registry_v1;2409010;2409010;ΗΜΕΡΗΣΙΟ ΓΥΜΝΑΣΙΟ με Λ.Τ. ΚΑΣΣΙΟΠΗΣ ΚΕΡΚΥΡΑΣ;Γυμνάσιο με Λυκειακές Τάξεις;ΚΑΣΣΙΟΠΗ, ΤΚ 49081;1;1;1;1;0;0;1;0;0;1;0;0;0;0;0;1;1;1;1;1;;;;;;;;;;;;;;2026-2027;;16;18;16;;;36;12;13;2;2;2;1;1;1;1;1;Β Ανθρωπιστικών Σπουδών | Β Θετικών Σπουδών | Γ Ανθρωπιστικών Σπουδών | Γ Θετικών Σπουδών και Σπουδών Υγείας | Γ Σπουδών Οικονομίας και Πληροφορικής;Απόφαση ΔΔΕ Κέρκυρας 3346/14-07-2026 (Γυμνάσια) + Απόφαση ΔΔΕ Κέρκυρας 3446/24-07-2026 (ΓΕΛ/ΛΤ) + μεταγενέστερες εγκρίσεις ολιγομελών + myschool stat3_1 05-09-2026 (τρέχοντα βασικά τμήματα/μαθητές) + myschool stat3_10 06-09-2026 (ομάδες ξένων γλωσσών);Δομικά στοιχεία καταχωρισμένα · τύπος προσωρινά ανενεργός στο εργαλείο;;Τα γενικά Α/Β/Γ του βασικού schema κρατούν το γυμνασιακό σκέλος· τα στοιχεία Λυκειακών Τάξεων διατηρούνται στις πρόσθετες στήλες ΛΤ.\nschool_registry_v1;2403010;2403010;ΗΜΕΡΗΣΙΟ ΓΥΜΝΑΣΙΟ ΠΑΞΩΝ;Γυμνάσιο με Λυκειακές Τάξεις;ΠΑΞΟΙ ΚΕΡΚΥΡΑΣ, ΤΚ 49082;2;2;2;2;0;0;1;0;0;2;0;0;0;0;0;1;1;1;1;1;;;;;;;;;;;;;;2026-2027;;31;27;22;;;34;0;0;2;1;1;1;1;1;1;1;Β Ανθρωπιστικών Σπουδών | Γ Θετικών Σπουδών και Σπουδών Υγείας | Γ Σπουδών Οικονομίας και Πληροφορικής;Απόφαση ΔΔΕ Κέρκυρας 3346/14-07-2026 (Γυμνάσια) + Απόφαση ΔΔΕ Κέρκυρας 3446/24-07-2026 (ΓΕΛ/ΛΤ) + μεταγενέστερες εγκρίσεις ολιγομελών + myschool stat3_1 05-09-2026 (τρέχοντα βασικά τμήματα/μαθητές) + myschool stat3_10 06-09-2026 (ομάδες ξένων γλωσσών);Δομικά στοιχεία καταχωρισμένα · τύπος προσωρινά ανενεργός στο εργαλείο;;Τα γενικά Α/Β/Γ του βασικού schema κρατούν το γυμνασιακό σκέλος· τα στοιχεία Λυκειακών Τάξεων διατηρούνται στις πρόσθετες στήλες ΛΤ.\nschool_registry_v1;2408010;2408010;ΗΜΕΡΗΣΙΟ ΓΥΜΝΑΣΙΟ ΣΚΡΙΠΕΡΟΥ ΚΕΡΚΥΡΑΣ ΜΕ ΛΥΚΕΙΑΚΕΣ ΤΑΞΕΙΣ;Γυμνάσιο με Λυκειακές Τάξεις;ΣΚΡΙΠΕΡΟ, ΤΚ 49083;1;1;1;0;1;0;1;0;0;1;0;0;0;0;0;1;1;1;1;1;;;;;;;;;;;;;;2026-2027;;13;21;11;;;21;20;15;2;2;2;1;1;1;1;1;Β Ανθρωπιστικών Σπουδών | Β Θετικών Σπουδών | Γ Ανθρωπιστικών Σπουδών | Γ Θετικών Σπουδών και Σπουδών Υγείας | Γ Σπουδών Οικονομίας και Πληροφορικής;Απόφαση ΔΔΕ Κέρκυρας 3346/14-07-2026 (Γυμνάσια) + Απόφαση ΔΔΕ Κέρκυρας 3446/24-07-2026 (ΓΕΛ/ΛΤ) + μεταγενέστερες εγκρίσεις ολιγομελών + myschool stat3_1 05-09-2026 (τρέχοντα βασικά τμήματα/μαθητές) + myschool stat3_10 06-09-2026 (ομάδες ξένων γλωσσών);Δομικά στοιχεία καταχωρισμένα · τύπος προσωρινά ανενεργός στο εργαλείο;;Τα γενικά Α/Β/Γ του βασικού schema κρατούν το γυμνασιακό σκέλος· τα στοιχεία Λυκειακών Τάξεων διατηρούνται στις πρόσθετες στήλες ΛΤ.\nschool_registry_v1;2401065;2401065;ΜΟΥΣΙΚΟ ΓΥΜΝΑΣΙΟ ΚΕΡΚΥΡΑΣ;Μουσικό Σχολείο;ΤΖΑΒΡΟΥ-ΚΑΤΩ ΚΟΡΑΚΙΑΝΑ, ΤΚ 49083;3;4;3;1;2;0;3;3;0;2;1;0;0;0;3;1;2;1;1;1;;;;;;;;;;;;;;2026-2027;;37;78;75;;;50;34;23;2;2;2;1;2;1;1;1;Β Ανθρωπιστικών Σπουδών | Γ Ανθρωπιστικών Σπουδών | Γ Θετικών Σπουδών και Σπουδών Υγείας | Γ Σπουδών Οικονομίας και Πληροφορικής;Απόφαση ΔΔΕ Κέρκυρας 3346/14-07-2026 (Γυμνάσια) + Απόφαση ΔΔΕ Κέρκυρας 3446/24-07-2026 (ΓΕΛ/ΛΤ) + μεταγενέστερες εγκρίσεις ολιγομελών + myschool stat3_1 05-09-2026 (τρέχοντα βασικά τμήματα/μαθητές) + myschool stat3_10 06-09-2026 (ομάδες ξένων γλωσσών);Δομικά στοιχεία καταχωρισμένα · τύπος προσωρινά ανενεργός στο εργαλείο;;Τα γενικά Α/Β/Γ του βασικού schema κρατούν το γυμνασιακό σκέλος· τα στοιχεία Λυκειακών Τάξεων διατηρούνται στις πρόσθετες στήλες ΛΤ.\nschool_registry_v1;2441001;2441001;ΕΕΕΕΚ ΚΕΡΚΥΡΑ - ΕΕΕΕΚ ΚΕΡΚΥΡΑΣ;Ε.Ε.Ε.ΕΚ.;4ο χλμ ΕΘΝΙΚΗΣ ΛΕΥΚΙΜΜΗΣ, ΤΚ 49100;2;2;1;;;;;;;;;;0;0;0;;;;;;;;;;;;;;;;;;;2026-2027;;8;9;5;3;1;;;;;;;;;;;;;Ενσωματωμένος κατάλογος ΔΔΕ Κέρκυρας / κωδικοί myschool + myschool stat3_1 05-09-2026 (τρέχοντα βασικά τμήματα/μαθητές);Μόνο μητρώο · δεν υπάρχουν στοιχεία τμημάτων στις δύο αποφάσεις;;\nschool_registry_v1;2440030;2440030;1ο ΕΠΑΛ ΚΕΡΚΥΡΑΣ;ΕΠΑΛ;ΕΥΑΓΓΕΛΟΥ ΝΑΠΟΛΕΟΝΤΟΣ 12, ΤΚ 49100;5;8;7;0;0;0;0;0;0;0;0;0;3;0;0;;;;;;;;;;;;;;;;;;;2026-2027;;107;0;0;;;;;;;;;;;;;;;Ενσωματωμένος κατάλογος ΔΔΕ Κέρκυρας / κωδικοί myschool + myschool stat3_1 05-09-2026 (τρέχοντα βασικά τμήματα/μαθητές) + myschool stat3_10 06-09-2026 (ομάδες ξένων γλωσσών);Μόνο μητρώο · δεν υπάρχουν στοιχεία τμημάτων στις δύο αποφάσεις;;\nschool_registry_v1;2440050;2440050;1ο ΗΜΕΡΗΣΙΟ ΕΠΑΛ ΚΑΤΩ ΚΟΡΑΚΙΑΝΑΣ;ΕΠΑΛ;ΚΑΤΩ ΚΟΡΑΚΙΑΝΑ - ΚΕΡΚΥΡΑ, ΤΚ 49083;1;1;1;0;0;0;0;0;0;0;0;0;0;0;0;;;;;;;;;;;;;;;;;;;2026-2027;;16;12;8;;;;;;;;;;;;;;;Ενσωματωμένος κατάλογος ΔΔΕ Κέρκυρας / κωδικοί myschool + myschool stat3_1 05-09-2026 (τρέχοντα βασικά τμήματα/μαθητές) + myschool stat3_10 06-09-2026 (ομάδες ξένων γλωσσών);Μόνο μητρώο · δεν υπάρχουν στοιχεία τμημάτων στις δύο αποφάσεις;;\nschool_registry_v1;2440045;2440045;ΕΣΠΕΡΙΝΟ ΕΠΑ.Λ ΚΕΡΚΥΡΑΣ;Εσπερινό ΕΠΑΛ;ΕΥΑΓΓΕΛΟΥ ΝΑΠΟΛΕΟΝΤΟΣ 12, ΤΚ 49100;2;2;2;0;0;0;0;0;0;0;0;0;0;0;0;;;;;;;;;;;;;;;;;;;2026-2027;;14;27;24;;;;;;;;;;;;;;;Ενσωματωμένος κατάλογος ΔΔΕ Κέρκυρας / κωδικοί myschool + myschool stat3_1 05-09-2026 (τρέχοντα βασικά τμήματα/μαθητές) + myschool stat3_10 06-09-2026 (ομάδες ξένων γλωσσών);Μόνο μητρώο · δεν υπάρχουν στοιχεία τμημάτων στις δύο αποφάσεις;;\nschool_registry_v1;2448000;2448000;Πρότυπο Επαγγελματικό Λύκειο (Π.ΕΠΑ.Λ.) Κέρκυρας;Πρότυπο ΕΠΑΛ;ΠΑΓΚΡΑΤΕΪΚΑ, ΤΚ 49100;3;5;1;0;0;0;0;0;0;0;0;0;0;0;0;;;;;;;;;;;;;;;;;;;2026-2027;;61;83;14;;;;;;;;;;;;;;;Ενσωματωμένος κατάλογος ΔΔΕ Κέρκυρας / κωδικοί myschool + myschool stat3_1 05-09-2026 (τρέχοντα βασικά τμήματα/μαθητές) + myschool stat3_10 06-09-2026 (ομάδες ξένων γλωσσών);Μόνο μητρώο · δεν υπάρχουν στοιχεία τμημάτων στις δύο αποφάσεις;;\nschool_registry_v1;2451010;2451010;1ο ΗΜΕΡΗΣΙΟ ΓΕΝΙΚΟ ΛΥΚΕΙΟ ΚΕΡΚΥΡΑΣ;Ημερήσιο Γενικό Λύκειο (ΓΕΛ);ΣΠΥΡΟΥ ΞΥΝΔΑ 4, ΤΚ 49100;3;4;5;2;2;0;2;3;0;0;0;0;;;;2;3;2;2;2;;;;;;;;;;;;;;2026-2027;;;;;;;74;78;102;;;;;;;;;;Απόφαση ΔΔΕ Κέρκυρας 3446/24-07-2026 (ΓΕΛ/ΛΤ) + μεταγενέστερες εγκρίσεις ολιγομελών + myschool stat3_1 05-09-2026 (τρέχοντα βασικά τμήματα/μαθητές) + myschool stat3_10 06-09-2026 (ομάδες ξένων γλωσσών);Τμήματα/προσανατολισμοί/2η ξένη πλήρη · απαιτεί ειδικές ομάδες Γ΄ για πλήρη υπολογισμό;\"Γ Μαθηματικά 2ου πεδίου; Γ Βιολογία 3ου πεδίου; Γ Μαθηματικά Γενικής Παιδείας; Γ Ιστορία Γενικής Παιδείας\";\nschool_registry_v1;2451020;2451020;2ο ΗΜΕΡΗΣΙΟ ΓΕΝΙΚΟ ΛΥΚΕΙΟ ΚΕΡΚΥΡΑΣ;Ημερήσιο Γενικό Λύκειο (ΓΕΛ);ΣΠΥΡΟΥ ΞΥΝΔΑ 4, ΤΚ 49100;4;4;5;2;2;0;2;3;0;0;0;0;;;;1;3;1;2;2;;;;;;;;;;;;;;2026-2027;;;;;;;97;56;83;;;;;;;;;Β Ανθρωπιστικών Σπουδών | Γ Ανθρωπιστικών Σπουδών;Απόφαση ΔΔΕ Κέρκυρας 3446/24-07-2026 (ΓΕΛ/ΛΤ) + μεταγενέστερες εγκρίσεις ολιγομελών + myschool stat3_1 05-09-2026 (τρέχοντα βασικά τμήματα/μαθητές) + myschool stat3_10 06-09-2026 (ομάδες ξένων γλωσσών);Τμήματα/προσανατολισμοί/2η ξένη πλήρη · απαιτεί ειδικές ομάδες Γ΄ για πλήρη υπολογισμό;\"Γ Μαθηματικά 2ου πεδίου; Γ Βιολογία 3ου πεδίου; Γ Μαθηματικά Γενικής Παιδείας; Γ Ιστορία Γενικής Παιδείας\";\nschool_registry_v1;2451030;2451030;3ο ΗΜΕΡΗΣΙΟ ΓΕΝΙΚΟ ΛΥΚΕΙΟ ΚΕΡΚΥΡΑΣ;Ημερήσιο Γενικό Λύκειο (ΓΕΛ);ΣΠΥΡΟΥ ΞΥΝΔΑ 2, ΤΚ 49100;4;4;5;4;4;0;3;3;0;0;0;0;;;;2;3;2;2;2;;;;;;;;;;;;;;2026-2027;;;;;;;108;93;119;;;;;;;;;;Απόφαση ΔΔΕ Κέρκυρας 3446/24-07-2026 (ΓΕΛ/ΛΤ) + μεταγενέστερες εγκρίσεις ολιγομελών + myschool stat3_1 05-09-2026 (τρέχοντα βασικά τμήματα/μαθητές) + myschool stat3_10 06-09-2026 (ομάδες ξένων γλωσσών);Τμήματα/προσανατολισμοί/2η ξένη πλήρη · απαιτεί ειδικές ομάδες Γ΄ για πλήρη υπολογισμό;\"Γ Μαθηματικά 2ου πεδίου; Γ Βιολογία 3ου πεδίου; Γ Μαθηματικά Γενικής Παιδείας; Γ Ιστορία Γενικής Παιδείας\";\nschool_registry_v1;2451040;2451040;4ο ΓΕΝΙΚΟ ΛΥΚΕΙΟ ΚΕΡΚΥΡΑΣ;Ημερήσιο Γενικό Λύκειο (ΓΕΛ);ΠΑΡΟΔΟΣ ΑΓΡΟΚΗΠΙΩΝ 1, ΤΚ 49100;4;5;5;1;3;0;1;4;0;0;0;0;;;;1;4;2;2;2;;;;;;;;;;;;;;2026-2027;;;;;;;85;109;115;;;;;;;;;;Απόφαση ΔΔΕ Κέρκυρας 3446/24-07-2026 (ΓΕΛ/ΛΤ) + μεταγενέστερες εγκρίσεις ολιγομελών + myschool stat3_1 05-09-2026 (τρέχοντα βασικά τμήματα/μαθητές) + myschool stat3_10 06-09-2026 (ομάδες ξένων γλωσσών);Τμήματα/προσανατολισμοί/2η ξένη πλήρη · απαιτεί ειδικές ομάδες Γ΄ για πλήρη υπολογισμό;\"Γ Μαθηματικά 2ου πεδίου; Γ Βιολογία 3ου πεδίου; Γ Μαθηματικά Γενικής Παιδείας; Γ Ιστορία Γενικής Παιδείας\";\nschool_registry_v1;2490030;2490030;5ο ΗΜΕΡΗΣΙΟ ΓΕΝΙΚΟ ΛΥΚΕΙΟ ΚΕΡΚΥΡΑΣ;Ημερήσιο Γενικό Λύκειο (ΓΕΛ);Ε. ΝΑΠΟΛΕΟΝΤΟΣ 12, ΤΚ 49132;3;3;3;2;2;0;1;2;0;0;0;0;;;;1;2;1;1;2;;;;;;;;;;;;;;2026-2027;;;;;;;51;53;43;;;;;;;;;Γ Θετικών Σπουδών και Σπουδών Υγείας;Απόφαση ΔΔΕ Κέρκυρας 3446/24-07-2026 (ΓΕΛ/ΛΤ) + μεταγενέστερες εγκρίσεις ολιγομελών + myschool stat3_1 05-09-2026 (τρέχοντα βασικά τμήματα/μαθητές) + myschool stat3_10 06-09-2026 (ομάδες ξένων γλωσσών);Τμήματα/προσανατολισμοί/2η ξένη πλήρη · απαιτεί ειδικές ομάδες Γ΄ για πλήρη υπολογισμό;\"Γ Μαθηματικά 2ου πεδίου; Γ Βιολογία 3ου πεδίου; Γ Μαθηματικά Γενικής Παιδείας; Γ Ιστορία Γενικής Παιδείας\";\nschool_registry_v1;2451060;2451060;ΕΣΠΕΡΙΝΟ ΓΕΝΙΚΟ ΛΥΚΕΙΟ ΚΕΡΚΥΡΑΣ;Εσπερινό ΓΕΛ;Σ. ΞΥΝΔΑ 2, ΤΚ 49100;1;1;1;0;0;0;0;0;0;;;;;;;1;1;1;1;1;;;;;;;;;;;;;;2026-2027;;;;;;;0;0;0;;;;;;;;;Β Ανθρωπιστικών Σπουδών | Β Θετικών Σπουδών | Γ Ανθρωπιστικών Σπουδών | Γ Θετικών Σπουδών και Σπουδών Υγείας | Γ Σπουδών Οικονομίας και Πληροφορικής;Απόφαση ΔΔΕ Κέρκυρας 3446/24-07-2026 (ΓΕΛ/ΛΤ) + μεταγενέστερες εγκρίσεις ολιγομελών + myschool stat3_1 05-09-2026 (τρέχοντα βασικά τμήματα/μαθητές) + myschool stat3_10 06-09-2026 (ομάδες ξένων γλωσσών);Δομικά στοιχεία καταχωρισμένα · τύπος προσωρινά ανενεργός στο εργαλείο;;\nschool_registry_v1;2454010;2454010;ΗΜΕΡΗΣΙΟ ΓΕΝΙΚΟ ΛΥΚΕΙΟ ΑΓΡΟΥ ΚΕΡΚΥΡΑΣ;Ημερήσιο Γενικό Λύκειο (ΓΕΛ);ΑΓΡΟΣ, ΤΚ 49083;3;3;3;3;3;0;1;2;0;0;0;0;;;;1;2;1;1;2;;;;;;;;;;;;;;2026-2027;;;;;;;62;56;52;;;;;;;;;Β Ανθρωπιστικών Σπουδών | Γ Ανθρωπιστικών Σπουδών | Γ Θετικών Σπουδών και Σπουδών Υγείας;Απόφαση ΔΔΕ Κέρκυρας 3446/24-07-2026 (ΓΕΛ/ΛΤ) + μεταγενέστερες εγκρίσεις ολιγομελών + myschool stat3_1 05-09-2026 (τρέχοντα βασικά τμήματα/μαθητές) + myschool stat3_10 06-09-2026 (ομάδες ξένων γλωσσών);Τμήματα/προσανατολισμοί/2η ξένη πλήρη · απαιτεί ειδικές ομάδες Γ΄ για πλήρη υπολογισμό;\"Γ Μαθηματικά 2ου πεδίου; Γ Βιολογία 3ου πεδίου; Γ Μαθηματικά Γενικής Παιδείας; Γ Ιστορία Γενικής Παιδείας\";\nschool_registry_v1;2457010;2457010;ΗΜΕΡΗΣΙΟ ΓΕΝΙΚΟ ΛΥΚΕΙΟ ΚΑΣΤΕΛΛΑΝΩΝ ΜΕΣΗΣ - ΚΕΡΚΥΡΑΣ;Ημερήσιο Γενικό Λύκειο (ΓΕΛ);ΚΑΣΤΕΛΛΑΝΟΙ ΜΕΣΗΣ, ΤΚ 49084;2;2;2;1;2;0;1;2;0;0;0;0;;;;1;2;1;1;1;;;;;;;;;;;;;;2026-2027;;;;;;;44;36;40;;;;;;;;;Β Ανθρωπιστικών Σπουδών | Γ Ανθρωπιστικών Σπουδών | Γ Θετικών Σπουδών και Σπουδών Υγείας;Απόφαση ΔΔΕ Κέρκυρας 3446/24-07-2026 (ΓΕΛ/ΛΤ) + μεταγενέστερες εγκρίσεις ολιγομελών + myschool stat3_1 05-09-2026 (τρέχοντα βασικά τμήματα/μαθητές) + myschool stat3_10 06-09-2026 (ομάδες ξένων γλωσσών);Τμήματα/προσανατολισμοί/2η ξένη πλήρη · απαιτεί ειδικές ομάδες Γ΄ για πλήρη υπολογισμό;\"Γ Μαθηματικά 2ου πεδίου; Γ Βιολογία 3ου πεδίου; Γ Μαθηματικά Γενικής Παιδείας; Γ Ιστορία Γενικής Παιδείας\";\nschool_registry_v1;2452010;2452010;ΗΜΕΡΗΣΙΟ ΓΕΝΙΚΟ ΛΥΚΕΙΟ ΛΕΥΚΙΜΜΗΣ ΚΕΡΚΥΡΑΣ;Ημερήσιο Γενικό Λύκειο (ΓΕΛ);ΠΟΛΥΤΕΧΝΕΙΟΥ 2, ΤΚ 49080;2;2;2;0;2;0;0;2;0;0;0;0;;;;1;1;1;1;1;;;;;;;;;;;;;;2026-2027;;;;;;;42;38;30;;;;;;;;;Β Ανθρωπιστικών Σπουδών | Γ Ανθρωπιστικών Σπουδών | Γ Θετικών Σπουδών και Σπουδών Υγείας | Γ Σπουδών Οικονομίας και Πληροφορικής;Απόφαση ΔΔΕ Κέρκυρας 3446/24-07-2026 (ΓΕΛ/ΛΤ) + μεταγενέστερες εγκρίσεις ολιγομελών + myschool stat3_1 05-09-2026 (τρέχοντα βασικά τμήματα/μαθητές) + myschool stat3_10 06-09-2026 (ομάδες ξένων γλωσσών);Τμήματα/προσανατολισμοί/2η ξένη πλήρη · απαιτεί ειδικές ομάδες Γ΄ για πλήρη υπολογισμό;\"Γ Μαθηματικά 2ου πεδίου; Γ Βιολογία 3ου πεδίου; Γ Μαθηματικά Γενικής Παιδείας; Γ Ιστορία Γενικής Παιδείας\";\nschool_registry_v1;SEK087;SEK087;1ο ΕΡΓΑΣΤΗΡΙΑΚΟ ΚΕΝΤΡΟ ΚΕΡΚΥΡΑΣ;Εργαστηριακό Κέντρο (Ε.Κ.);ΕΥΑΓΓΕΛΟΥ ΝΑΠΟΛΕΟΝΤΟΣ 12, ΤΚ 49100;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;2026-2027;;;;;;;;;;;;;;;;;;;Ενσωματωμένος κατάλογος ΔΔΕ Κέρκυρας / κωδικοί myschool;Μόνο μητρώο · δεν υπάρχουν στοιχεία τμημάτων στις δύο αποφάσεις;;\n";

  var BUILTIN_DIRECTORY_DEFINITIONS={
    dde_corfu_2026:{
      id:'dde_corfu_2026',
      dataset_version:'2026-2027-full-v2-stat3-10',
      label:'ΔΔΕ Κέρκυρας 2026-2027',
      registry_schema:'school_registry_v1',
      retrieved_on:'2026-09-06',
      source_label:'ΔΔΕ Κέρκυρας · αποφάσεις τμημάτων 2026-2027 · myschool',
      source_url:'',
      filename:'school_registry_v1-dde-kerkyras-2026-2027-full.csv',
      csv_text:BUILTIN_CORFU_REGISTRY_CSV
    }
  };
  function getBuiltinDirectory(id){
    var def=BUILTIN_DIRECTORY_DEFINITIONS[id];
    if(!def) return null;
    var parsed=parse(def.csv_text || '');
    var mapping=autoMap(parsed.headers);
    var schools=parsed.rows.map(function(row,index){
      var school=rowToSchool(row,mapping,index);
      school.directory_only=basicSectionTotal(school)===0;
      school.directory_id=id;
      school.registry_dataset_version=def.dataset_version || '';
      school.school_year=String(row['Σχολικό έτος'] || '').trim();
      school.completeness_status=String(row['Κατάσταση πληρότητας'] || '').trim();
      school.pending_fields=String(row['Εκκρεμή πεδία'] || '').trim();
      school.sections_source=String(row['Πηγή τμημάτων'] || '').trim();
      school.notes=String(row['Σημειώσεις'] || '').trim();
      school.approved_small_groups=String(row['Ολιγομελή εγκεκριμένα 2026-27'] || '').trim();
      if(school.school_type==='gymnasio_lt' && school.completeness_status.indexOf('προσωρινά ανενεργός')>=0){
        school.completeness_status='Τμήματα Γυμνασίου/Λυκειακών Τάξεων και ομάδες προσανατολισμού καταχωρισμένα · συμπλήρωσε όπου χρειάζεται 2η ξένη γλώσσα ΛΤ και ειδικές ομάδες Γ΄.';
      }
      school.registry_source_row=row;
      return school;
    });
    return {
      id:def.id,label:def.label,registry_schema:def.registry_schema,retrieved_on:def.retrieved_on,
      source_label:def.source_label,source_url:def.source_url,filename:def.filename,dataset_version:def.dataset_version,
      raw_csv:def.csv_text,headers:parsed.headers.slice(),schools:schools
    };
  }

  var api={
    schemaVersion:'2026-09-06-school-registry-v1-full-corfu-stat3-10',
    registrySchemaVersion:'school_registry_v1',
    supportsMultipleSchools:true,
    maxBasicSections:MAX_BASIC_SECTIONS,
    basicSectionTotal:basicSectionTotal,
    validateRegistry:validateRegistry,
    supportedTypes:['gymnasio','gel','esperino_gymnasio','esperino_gel','gymnasio_lt'],
    placeholderTypes:['epal','esperino_epal','pepal','eneegyl','eeeek','mousiko','kallitexniko','sek'],
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
    getBuiltinDirectory:getBuiltinDirectory,
    formFields:FORM_FIELDS.slice()
  };
  root.EducationSchoolCsv=api;
  if(typeof module!=='undefined' && module.exports) module.exports=api;
})(typeof window!=='undefined'?window:globalThis);
