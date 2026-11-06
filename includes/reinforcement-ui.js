(function(){
  'use strict';
  var form=document.getElementById('reinforcement-form'),api=window.ReinforcementEligibility;
  if(!form||!api)return;
  var result=document.getElementById('reinforcement-result'),checked=false;
  var optionCache={};
  ['branch','board','subject'].forEach(function(name){
    optionCache[name]=Array.from(form.elements.namedItem(name).options).map(function(o){return{value:o.value,text:o.textContent};});
  });
  function val(k){return form.elements.namedItem(k).value;}
  function el(tag,text){var n=document.createElement(tag);n.textContent=text;return n;}
  function filterOptions(name,allowed,placeholder,enabled){
    var select=form.elements.namedItem(name),previous=select.value;
    var signature=JSON.stringify([allowed,placeholder,enabled]);
    if(select.dataset.filterSignature===signature)return;
    var fragment=document.createDocumentFragment(),empty=el('option',placeholder);empty.value='';fragment.appendChild(empty);
    optionCache[name].forEach(function(o){if(o.value&&allowed.indexOf(o.value)>=0){var item=el('option',o.text);item.value=o.value;fragment.appendChild(item);}});
    select.replaceChildren(fragment);select.value=allowed.indexOf(previous)>=0?previous:'';
    select.disabled=!enabled;select.dataset.filterSignature=signature;
  }
  function toggle(id,show){var group=document.getElementById(id);group.hidden=!show;group.querySelectorAll('select').forEach(function(e){e.disabled=!show;});}
  function sync(){
    var programmeSelected=['gym','edem'].indexOf(val('program'))>=0;
    var choices=api.options(val('program'),val('branch'));
    filterOptions('branch',choices.branches,programmeSelected?'Επίλεξε κλάδο':'Επίλεξε πρώτα πρόγραμμα',programmeSelected);
    choices=api.options(val('program'),val('branch'));
    var branchSelected=choices.boards.length>0;
    filterOptions('board',branchSelected?choices.boards.concat(['none','unknown']):[],branchSelected?'Επίλεξε πίνακα':'Επίλεξε πρώτα κλάδο',branchSelected);
    filterOptions('subject',choices.subjects,branchSelected?'Επίλεξε μάθημα':'Επίλεξε πρώτα κλάδο',val('program')==='edem'&&branchSelected);
    toggle('reinforcement-edem',val('program')==='edem');
    form.elements.namedItem('subject').disabled=val('program')!=='edem'||!branchSelected;
    toggle('reinforcement-chemical',val('program')==='gym'&&val('branch')==='PE85');
    toggle('reinforcement-music',val('program')==='edem'&&val('branch')==='TE16');
    document.querySelectorAll('[data-reinforcement-program]').forEach(function(e){e.hidden=e.dataset.reinforcementProgram!==val('program');});
    period();
  }
  function period(){
    var box=document.getElementById('reinforcement-period'),row=document.querySelector('[data-reinforcement-program="'+val('program')+'"]');
    var state=row?api.period(row.dataset.start,row.dataset.end):'unknown';
    var fmt=new Intl.DateTimeFormat('el-GR',{timeZone:'Europe/Athens',day:'2-digit',month:'2-digit',year:'numeric'});
    var first=state==='unknown'?'':fmt.format(new Date(row.dataset.start));
    var last=state==='unknown'?'':fmt.format(new Date(Date.parse(row.dataset.end)-1));
    box.textContent={before:'Αιτήσεις από '+first+' έως και '+last+'. Δεν έχουν ξεκινήσει ακόμη.',open:'Οι αιτήσεις είναι ανοικτές έως και '+last+'.',closed:'Η προθεσμία αυτής της πρόσκλησης έληξε στις '+last+'. Ο έλεγχος αφορά το πλαίσιο 2026–2027.',unknown:val('program')?'Δεν εντοπίστηκε καταχώριση προθεσμίας. Έλεγξε την επίσημη πρόσκληση.':'Επίλεξε πρόγραμμα για να δεις την προθεσμία.'}[state];
  }
  function render(){
    var data={};new FormData(form).forEach(function(v,k){data[k]=v;});
    var r=api.evaluate(data),frag=document.createDocumentFragment();
    result.className='reinforcement-result '+r.status;
    frag.appendChild(el('h2',{basic_pass:'Με βάση τις απαντήσεις σου, καλύπτεις τις ελεγχόμενες προϋποθέσεις αίτησης',review:'Χρειάζεται επιβεβαίωση στοιχείων',not_met:'Δεν καλύπτεται κάποια από τις ελεγχόμενες προϋποθέσεις'}[r.status]));
    if(r.reasons.length){var list=document.createElement('ul');r.reasons.forEach(function(x){list.appendChild(el('li',x));});frag.appendChild(list);}
    r.notes.forEach(function(x){frag.appendChild(el('p',x));});
    frag.appendChild(el('p','Η δυνατότητα συμμετοχής ελέγχεται χωριστά από την προθεσμία και από τη δυνατότητα ανάληψης υπηρεσίας. Ο έλεγχος δεν εγγυάται πρόσληψη.'));
    result.replaceChildren(frag);
  }
  form.addEventListener('submit',function(e){e.preventDefault();checked=true;sync();render();});
  form.addEventListener('change',function(){sync();if(checked)render();});
  form.addEventListener('reset',function(){checked=false;setTimeout(function(){sync();result.className='reinforcement-result';result.replaceChildren(el('p','Συμπλήρωσε τις επιλογές σου και πάτησε «Έλεγχος δυνατότητας αίτησης».'));},0);});
  sync();setInterval(period,60000);
}());
