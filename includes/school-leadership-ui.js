(function() {
  'use strict';
  var form = document.getElementById('schoolLeadershipForm');
  if (!form || !window.SchoolLeadership) return;
  var result = document.getElementById('sl-result');
  var calculated = false;
  var titleNames = ['phdRelated','phdOther','masterRelated','masterOther'];
  function normalizeTitleInput(input) {
    if (!input || titleNames.indexOf(input.name) < 0) return;
    var normalized = window.SchoolLeadership.normalizeTitleCount(input.value);
    if (input.value !== String(normalized)) input.value = normalized;
  }
  function normalizeTitles() {
    titleNames.forEach(function(name) {normalizeTitleInput(form.elements.namedItem(name));});
  }
  function value(name) {return form.elements.namedItem(name).value;}
  function showEvaluation() {
    var general = value('mode') === 'general';
    var type = value('evalType');
    document.getElementById('sl-evaluation').hidden = !general;
    document.getElementById('sl-evalDescriptions').hidden = type === 'leader' || !type;
    document.getElementById('sl-evalNumeric').hidden = type !== 'leader';
    document.getElementById('sl-evalA2').closest('.field').hidden = type !== 'three';
    form.querySelectorAll('[name^="eval"]').forEach(function(el) {
      el.disabled = !general || (el.name === 'evalScore' && type !== 'leader') ||
        (['evalA1','evalA2','evalB'].indexOf(el.name) >= 0 && (type === 'leader' || !type)) ||
        (el.name === 'evalA2' && type !== 'three');
    });
    var primary = value('primary') === 'yes';
    form.elements.namedItem('didaskaleio').disabled = !primary;
  }
  function data() {
    var d = {};
    new FormData(form).forEach(function(v,k) {d[k] = v;});
    d.inputsConfirmed = form.elements.namedItem('inputsConfirmed').checked;
    return d;
  }
  function element(tag,text,className) {
    var el = document.createElement(tag); el.textContent = text;
    if (className) el.className = className;
    return el;
  }
  function format(n) {return n.toLocaleString('el-GR',{maximumFractionDigits:4});}
  function render() {
    if (!form.checkValidity()) {
      result.replaceChildren(element('h3','Χρειάζεται διόρθωση πεδίου'),element('p','Συμπλήρωσε αριθμούς εντός των ορίων που αναγράφονται στα πεδία.'));
      return;
    }
    var d = data(), r = window.SchoolLeadership.calculate(d);
    var frag = document.createDocumentFragment();
    frag.appendChild(element('h3',r.total === null ? 'Μερικό αποτέλεσμα — εκκρεμούν στοιχεία' : 'Συνολικό ενδεικτικό αποτέλεσμα'));
    frag.appendChild(element('p',r.total === null ? 'Προσόντα και εμπειρία: '+format(r.base)+' / 55' : format(r.total)+' / '+r.max+' μόρια','sl-total'));
    if (r.total === null) frag.appendChild(element('p','Σύνολο καταχωρισμένων κατηγοριών: '+format(r.known)+' μόρια. Δεν αποτελεί τελικό σύνολο.'));
    var rows = [['Επιστημονική – παιδαγωγική συγκρότηση',r.academic,28],['Διδακτική, συμβουλευτική και διοικητική εμπειρία',r.experience,27],
      ['Αξιολόγηση',r.evaluation,d.mode === 'first' ? 0 : 25],['Συνέντευξη',r.interview,20]];
    var table = document.createElement('table');table.className='sl-table';
    var caption = element('caption','Ανάλυση κατηγοριών');table.appendChild(caption);
    var head=document.createElement('thead'),hr=document.createElement('tr');
    ['Κατηγορία','Μόρια','Όριο'].forEach(function(t) {var th=element('th',t);th.scope='col';hr.appendChild(th);});head.appendChild(hr);table.appendChild(head);
    var body=document.createElement('tbody');
    rows.forEach(function(row) {
      var tr=document.createElement('tr'),th=element('th',row[0]);th.scope='row';tr.appendChild(th);
      tr.appendChild(element('td',row[1] === null ? 'Εκκρεμεί' : format(row[1])));tr.appendChild(element('td',String(row[2])));body.appendChild(tr);
    });table.appendChild(body);frag.appendChild(table);
    var detail = document.createElement('dl');detail.className='sl-breakdown';
    var labels={titles:'Τίτλοι',training:'Επιμόρφωση',languages:'Γλώσσες',books:'Βιβλία / υλικό',articles:'Άρθρα',higher:'Διδασκαλία ΑΕΙ',teaching:'Διδακτική εμπειρία',trainer:'Επιμορφωτικό έργο',research:'Έρευνα / δράσεις',teachingGroup:'Διδακτική + επιμορφωτικό + έρευνα (κοινό όριο 13)',guidance:'Συμβουλευτικό έργο',admin:'Διοικητική εμπειρία',school:'Από τη διοικητική: σχολικές θέσεις (κοινό όριο 6)'};
    Object.keys(labels).forEach(function(k) {detail.appendChild(element('dt',labels[k]));detail.appendChild(element('dd',format(r.details[k])));});frag.appendChild(detail);
    if (r.pending.length) {var list=document.createElement('ul');r.pending.forEach(function(p) {list.appendChild(element('li',p));});frag.appendChild(list);}
    if (d.interview !== undefined && d.interview !== '') frag.appendChild(element('p','Η συνέντευξη είναι καταχωρισμένη τιμή. Αν πρόκειται για υποθετικό βαθμό, το αποτέλεσμα είναι προσομοίωση.'));
    frag.appendChild(element('p','Βάση: ν. 4823/2021, άρθρα 33 και 35· για την πρώτη επιλογή άρθρο 232 §5. Ο έλεγχος προϋποθέσεων υποψηφιότητας γίνεται χωριστά.'));
    result.replaceChildren(frag);
  }
  form.addEventListener('submit',function(e) {e.preventDefault();normalizeTitles();calculated=true;showEvaluation();render();});
  form.addEventListener('input',function(e) {normalizeTitleInput(e.target);if(calculated)render();});
  form.addEventListener('change',function(e) {normalizeTitleInput(e.target);showEvaluation();if(calculated)render();});
  form.addEventListener('focusout',function(e) {normalizeTitleInput(e.target);if(calculated)render();});
  form.addEventListener('reset',function() {
    calculated=false;
    setTimeout(function() {showEvaluation();result.replaceChildren(element('h3','Αποτέλεσμα μοριοδότησης'),element('p','Συμπλήρωσε τα προσόντα και επίλεξε «Υπολογισμός μορίων».'));},0);
  });
  document.getElementById('sl-print').addEventListener('click',function() {
    form.querySelectorAll('details').forEach(function(el) {el.open=true;});window.print();
  });
  normalizeTitles();
  showEvaluation();
}());
