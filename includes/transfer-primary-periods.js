/* Primary transfer period editors. Calculations remain in shared transfer-calculations.js. */
(function(global){
  'use strict';
  var root=document;
  var $=function(id){return root.getElementById(id);};
  var calculation=global.EducationTransfer;
  if(!calculation || !$('primaryServicePeriodsRows'))return;
  var counters={service:0,msd:0};
  var types={school:'Σχολική μονάδα / οργανική / προσωρινή τοποθέτηση',
    prison:'Σχολική μονάδα σε κατάστημα κράτησης (+5/έτος)',
    digital_tutoring:'Ψηφιακό Φροντιστήριο (+6/έτος)',
    listed_service_bonus2:'ΥΠΑΙΘΑ / ΠΔΕ / ΔΠΕ / ΙΕΠ κ.ά. (+2/έτος)',
    other_secondment:'Λοιπή απόσπαση / φορέας — πλησιέστερο σχολείο Π.Ε.',
    recognized_prior:'Αναγνωρισμένη προϋπηρεσία επιλέξιμη για Μ.Σ.Δ.',
    abroad_europe:'Απόσπαση εξωτερικού — Ευρώπη (Α΄)',
    abroad_america:'Απόσπαση εξωτερικού — Αμερική (Β΄)',
    abroad_other:'Απόσπαση εξωτερικού — λοιπές χώρες (Γ΄)',
    study_leave:'Άδεια υπηρεσιακής εκπαίδευσης με αποδοχές (Α΄)'};
  var categories=[['A','Α΄ — 1'],['B','Β΄ — 2'],['G','Γ΄ — 3'],['D','Δ΄ — 4'],['E','Ε΄ — 5'],['ST','ΣΤ΄ — 6'],['Z','Ζ΄ — 7'],['H','Η΄ — 8'],['TH','Θ΄ — 9'],['I','Ι΄ — 10'],['IA','ΙΑ΄ — 11'],['IB','ΙΒ΄ — 12'],['IG','ΙΓ΄ — 14']];
  function esc(s){return String(s).replace(/[&<>"']/g,function(c){return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c];});}
  function options(items){return items.map(function(pair){return '<option value="'+esc(pair[0])+'">'+esc(pair[1])+'</option>';}).join('');}
  function durationFields(prefix,service){return '<div class="field-grid transfer-service-grid">'+
     [['years','Έτη',50],['months','Μήνες',11],['days','Ημέρες (15+ → μήνας)',29]].map(function(x){return '<div class="field"><label for="'+prefix+'-'+x[0]+'">'+x[1]+'</label><input id="'+prefix+'-'+x[0]+'" type="number" inputmode="numeric" min="0" max="'+x[2]+'" step="1" value="0" class="'+(service?'primary-service-':'primary-msd-')+x[0]+'"></div>';}).join('')+'</div>';}
  function addService(){
    var n=++counters.service,prefix='primary-service-period-'+n;
    var row=root.createElement('section');row.className='transfer-service-period primary-service-period';
    row.innerHTML='<div class="transfer-service-row-head"><strong>Περίοδος υπηρεσίας #'+n+'</strong><button type="button" class="remove-row" data-primary-remove="service">Αφαίρεση</button></div>'+
      '<div class="field"><label for="'+prefix+'-description">Περιγραφή (προαιρετική)</label><input id="'+prefix+'-description" maxlength="120" type="text" placeholder="π.χ. Διδασκαλία σε σχολείο ή αναγνωρισμένη προϋπηρεσία"></div>'+durationFields(prefix,true);
    $('primaryServicePeriodsRows').appendChild(row);
  }
  function addMsd(){
    var n=++counters.msd,prefix='primary-msd-period-'+n;
    var row=root.createElement('section');row.className='transfer-service-row primary-msd-period';
    row.innerHTML='<div class="transfer-service-row-head"><strong>Υπηρέτηση Μ.Σ.Δ. #'+n+'</strong><span class="transfer-row-score">0 μόρια Μ.Σ.Δ.</span></div>'+
      '<div class="field-grid transfer-service-grid"><div class="field transfer-type-field"><label for="'+prefix+'-type">Είδος υπηρέτησης</label><select id="'+prefix+'-type" class="primary-msd-type">'+options(Object.keys(types).map(function(k){return [k,types[k]];}))+'</select></div>'+
      '<div class="field primary-category-field"><label for="'+prefix+'-category">Κατηγορία / πλησιέστερο σχολείο Π.Ε.</label><select class="primary-msd-category" id="'+prefix+'-category">'+options(categories)+'</select></div></div>'+durationFields(prefix,false)+
      '<div class="field"><label for="'+prefix+'-weekdays">Ημέρες ανά εβδομάδα (έως 5/5)</label><input type="number" class="primary-msd-weekdays" id="'+prefix+'-weekdays" min="0.01" max="5" step="0.01" value="5" inputmode="decimal"></div>'+
      '<label class="check-row primary-msd-double-field"><input type="checkbox" class="primary-msd-remote-double"> Επιβεβαιωμένος διπλασιασμός Ι΄–ΙΓ΄ (δύο συνεχόμενα διδακτικά έτη)</label>'+
      '<p class="field-hint primary-msd-row-note"></p><div class="transfer-service-actions"><button type="button" class="remove-row" data-primary-remove="msd">Αφαίρεση</button></div>';
    $('primaryMsdPeriodsRows').appendChild(row);syncRow(row);
  }
  function syncRow(row){
    var type=row.querySelector('.primary-msd-type').value,category=row.querySelector('.primary-msd-category').value;
    var fixed=calculation.isFixedFullWeekType(type);
    row.querySelector('.primary-category-field').hidden=fixed;
    var weekdays=row.querySelector('.primary-msd-weekdays');
    if(fixed){weekdays.value='5';weekdays.disabled=true;}else weekdays.disabled=false;
    var canDouble=type==='school'&&['I','IA','IB','IG'].indexOf(category)!==-1;
    row.querySelector('.primary-msd-double-field').hidden=!canDouble;
    if(!canDouble)row.querySelector('.primary-msd-remote-double').checked=false;
    var messages={prison:'Πλησιέστερη σχολική μονάδα +5 μονάδες/έτος.',digital_tutoring:'Πλησιέστερη σχολική μονάδα +6 μονάδες/έτος.',listed_service_bonus2:'Η προσαύξηση +2 ισχύει μόνο για προβλεπόμενες υπηρεσίες.',recognized_prior:'Ορισμένες αναγνωρισμένες προϋπηρεσίες δεν προσμετρώνται στις Μ.Σ.Δ.· ελέγξτε §4.1.',other_secondment:'Μονάδες της πλησιέστερης σχολικής μονάδας Π.Ε.'};
    row.querySelector('.primary-msd-row-note').textContent=messages[type]||(canDouble?'Διπλασιασμός μόνο εφόσον έχει θεμελιωθεί και καταχωριστεί επίσημα.':'');
  }
  function rows(kind){return Array.prototype.slice.call($(kind==='service'?'primaryServicePeriodsRows':'primaryMsdPeriodsRows').children);}
  function rawNumber(row,klass,max,zeroAllowed){
    var el=row.querySelector('.'+klass),str=el.value.trim().replace(',','.');
    var n=Number(str),valid=str!==''&&Number.isFinite(n)&&n>=0&&n<=max&&(zeroAllowed?true:Number.isInteger(n));
    el.setAttribute('aria-invalid',String(!valid));return {value:n,valid:valid};
  }
  function readService(){
    var list=[],good=true,nonzero=0;
    rows('service').forEach(function(row){
      var vals=['years','months','days'].map(function(k,i){return rawNumber(row,'primary-service-'+k,[50,11,29][i],false);});
      if(vals.some(function(v){return !v.valid;}))good=false;
      var p={years:vals[0].value,months:vals[1].value,days:vals[2].value};
      if(p.years||p.months||p.days)nonzero++;list.push(p);
    });
    var sum=calculation.serviceFromPeriods(list);
    good=good&&sum.valid&&nonzero>0;
    $('primaryServicePeriodStatus').textContent=good?'Συνολική υπηρεσία: '+sum.years+' έτη, '+sum.months+' μήνες (έλεγχος μη επικάλυψης από τον εκπαιδευτικό).':'Δώστε τουλάχιστον μία πραγματική περίοδο, έγκυρα έτη/μήνες/ημέρες και συνολικά έως 50 έτη. Οι επικαλύψεις πρέπει να ελεγχθούν στο μητρώο.';
    return {periods:list,valid:good};
  }
  function readMsd(){
    var list=[],good=true,nonzero=0, totalMonths=0;
    rows('msd').forEach(function(row){
      var vals=['years','months','days'].map(function(k,i){return rawNumber(row,'primary-msd-'+k,[50,11,29][i],false);});
      var weekdays=rawNumber(row,'primary-msd-weekdays',5,true);
      var validRow=weekdays.valid&&weekdays.value>0&&!vals.some(function(v){return !v.valid;});
      if(!validRow)good=false;
      var p={type:row.querySelector('.primary-msd-type').value,category:row.querySelector('.primary-msd-category').value,
        years:vals[0].value,months:vals[1].value,days:vals[2].value,
        daysPerWeek:weekdays.value,remoteDouble:row.querySelector('.primary-msd-remote-double').checked};
      var result=calculation.calculateMsdPeriod(p);
      row.querySelector('.transfer-row-score').textContent=validRow?result.points.toLocaleString('el-GR',{maximumFractionDigits:2})+' μόρια Μ.Σ.Δ.':'— μη έγκυρη καταχώριση';
      if(p.years||p.months||p.days)nonzero++;
      totalMonths+=p.years*12+p.months+(p.days>=15?1:0);
      list.push(p);
    });
    // Multiple same-duration school placements are legitimate when the teacher splits a 5-day week.
    // Require the user to verify their day-per-week allocation rather than guessing overlaps.
    // Concurrent part-week allocations can legitimately share the same calendar months.
    // Do not add their durations as if they were sequential service.
    good=good&&nonzero>0;
    $('primaryMsdPeriodStatus').textContent=good?'Υπολογισμός από '+list.length+' υπηρετήσεις. Επαληθεύστε ότι δεν διπλομετράται ο χρόνος και οι παράλληλες ημέρες ανά εβδομάδα δεν ξεπερνούν συνολικά τις 5.':'Συμπληρώστε τουλάχιστον μία υπηρέτηση, έγκυρη διάρκεια (έως 50 έτη ανά υπηρεσία) και 1–5 ημέρες ανά εβδομάδα.';
    return {periods:list,valid:good};
  }
  function bind(notify){
    function changed(){if(typeof notify==='function')notify();}
    $('primaryAddServicePeriod').addEventListener('click',function(){addService();changed();});
    $('primaryAddMsdPeriod').addEventListener('click',function(){addMsd();changed();});
    ['primaryServicePeriodsRows','primaryMsdPeriodsRows'].forEach(function(id){
      $(id).addEventListener('input',changed);
      $(id).addEventListener('change',function(e){var row=e.target.closest('.primary-msd-period');if(row)syncRow(row);changed();});
      $(id).addEventListener('click',function(e){var btn=e.target.closest('[data-primary-remove]');if(!btn)return;var wrap=btn.closest('.primary-service-period,.primary-msd-period');if(wrap)wrap.remove();changed();});
    });
  }
  function sync(){
    var service=$('primaryServiceMode').value,msd=$('primaryMsdMode').value;
    $('primaryServicePointsPanel').hidden=service!=='points';
    $('primaryServiceDurationPanel').hidden=service!=='direct';
    $('primaryServicePeriodsPanel').hidden=service!=='periods';
    $('primaryMsdDirectPanel').hidden=msd!=='direct';
    $('primaryMsdPeriodsPanel').hidden=msd!=='periods';
  }
  addService();addMsd();
  global.EducationTransferPrimaryPeriods={readService:readService,readMsd:readMsd,sync:sync,bind:bind};
}(typeof window!=='undefined'?window:this));
