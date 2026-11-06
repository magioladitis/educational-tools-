/* 2026–2027 invitations: 151710/Δ2 and 151737/ΓΔ4 (06/11/2026). */
(function(root,factory){
  'use strict';
  if(typeof module==='object'&&module.exports)module.exports=factory();
  else root.ReinforcementEligibility=factory();
}(typeof globalThis!=='undefined'?globalThis:this,function(){
  'use strict';
  var boards={gym:['2GE'],edem:['2GE','3EA-main','3EA-aux']};
  var gym={PE02:'Νεοελληνική Γλώσσα και Γραμματεία / Αρχαία Ελληνική Γλώσσα και Γραμματεία',PE03:'Μαθηματικά','PE04.01':'Φυσική','PE04.02':'Χημεία',PE85:'Χημεία',PE06:'Αγγλικά'};
  var edem={PE05:['french'],PE07:['german'],PE34:['italian'],PE40:['spanish'],'PE79.01':['music'],PE08:['free'],'PE89.01':['free','linear'],PE81:['linear'],TE16:['music']};
  function options(program,branch){
    var branches=program==='gym'?Object.keys(gym):program==='edem'?Object.keys(edem):[];
    if(branches.indexOf(branch)<0)return{branches:branches,boards:[],subjects:[]};
    return{branches:branches,boards:program==='gym'?boards.gym.slice():branch==='TE16'?['1GT','4EA-main','4EA-aux']:branch==='PE79.01'?['1GE','3EA-main','3EA-aux']:boards.edem.slice(),subjects:program==='edem'?edem[branch].slice():[]};
  }
  function evaluate(d){
    d=d||{};var no=[],pending=[],notes=[];
    if(['gym','edem'].indexOf(d.program)<0)return{status:'review',reasons:['Επίλεξε Γυμνάσιο ή Ειδικά Μαθήματα (ΕΔΕΜ).'],notes:[]};
    if(!d.branch)pending.push('Επίλεξε κλάδο / ειδικότητα.');
    var allowed;
    if(d.program==='gym'){
      if(d.branch&&!Object.prototype.hasOwnProperty.call(gym,d.branch))no.push('Ο κλάδος δεν περιλαμβάνεται στην πρόσκληση Γυμνασίου.');
      allowed=options(d.program,d.branch).boards;
      if(d.branch==='PE85'){
        if(d.chemical==='no')no.push('Για ΠΕ85 απαιτείται πτυχίο Χημικών Μηχανικών.');
        else if(d.chemical!=='yes')pending.push('Επιβεβαίωσε το πτυχίο Χημικών Μηχανικών για ΠΕ85.');
      }
      if(gym[d.branch])notes.push('Μάθημα: '+gym[d.branch]+'.');
    }else{
      if(d.branch&&!Object.prototype.hasOwnProperty.call(edem,d.branch))no.push('Ο κλάδος δεν περιλαμβάνεται στην πρόσκληση ΕΔΕΜ.');
      if(!d.subject)pending.push('Επίλεξε ειδικό μάθημα.');
      else if(edem[d.branch]&&edem[d.branch].indexOf(d.subject)<0)no.push('Το επιλεγμένο ειδικό μάθημα δεν αντιστοιχεί στον κλάδο της πρόσκλησης.');
      allowed=options(d.program,d.branch).boards;
      if(d.branch==='TE16'){
        if(d.musicSpecialty==='no')no.push('Για ΤΕ16 απαιτείται η μουσική ειδίκευση «Θεωρητικά Ευρωπαϊκής Μουσικής».');
        else if(d.musicSpecialty!=='yes')pending.push('Επιβεβαίωσε τη μουσική ειδίκευση «Θεωρητικά Ευρωπαϊκής Μουσικής».');
        notes.push('Η πρόσληψη ΤΕ16 προβλέπεται μόνο μετά την εξάντληση των υποψηφίων ΠΕ79.01. Αυτό δεν αποκλείει την αίτηση του ΤΕ16 στους προβλεπόμενους πίνακες.');
      }
      if(d.board==='3EA-aux'||d.board==='4EA-aux')notes.push('Η αξιοποίηση επικουρικού πίνακα ΕΑΕ ακολουθεί την εξάντληση του κύριου πίνακα, σύμφωνα με την πρόσκληση.');
      notes.push('Στην ΕΔΕΜ η προτεραιότητα Γενικής / ΕΑΕ εξαρτάται και από την προέλευση της πλειοψηφίας των μαθητών του τμήματος.');
    }
    if(!d.board||d.board==='unknown')pending.push('Επιβεβαίωσε τον πίνακα ΑΣΕΠ στον οποίο περιλαμβάνεσαι.');
    else if(d.branch&&allowed.indexOf(d.board)<0)no.push('Ο επιλεγμένος πίνακας ΑΣΕΠ δεν καλύπτει αυτόν τον συνδυασμό προγράμματος και κλάδου.');
    if(d.sanction==='yes')no.push('Δήλωσες ενεργή κύρωση αποκλεισμού λόγω μη ανάληψης υπηρεσίας ή παραίτησης· οι προσκλήσεις εξαιρούν όσους υπάγονται σε αυτές τις κυρώσεις.');
    else if(d.sanction!=='no')pending.push('Χρειάζεται έλεγχος για τυχόν ενεργή κύρωση αποκλεισμού.');
    if(d.ageEligible==='no')no.push('Δήλωσες ότι δεν καλύπτεις τα ηλικιακά όρια της πρόσκλησης (κατώτατο 21ο, ανώτατο 67ο έτος, με τον ειδικό τρόπο υπολογισμού).');
    else if(d.ageEligible!=='yes')pending.push('Επιβεβαίωσε τα ηλικιακά όρια και τον τρόπο υπολογισμού τους.');
    if(d.citizenshipEligible==='no')no.push('Δήλωσες ότι δεν ανήκεις στις κατηγορίες ιθαγένειας / δικαιώματος συμμετοχής που καλεί η πρόσκληση.');
    else if(d.citizenshipEligible!=='yes')pending.push('Επιβεβαίωσε την ιθαγένεια ή άλλη προβλεπόμενη κατηγορία δικαιώματος συμμετοχής.');
    if(d.dutiesEligible==='no')no.push('Δήλωσες ότι δεν πληρούται η νόμιμη προϋπόθεση για την εκτέλεση των καθηκόντων της θέσης.');
    else if(d.dutiesEligible!=='yes')pending.push('Εκκρεμεί επιβεβαίωση της προϋπόθεσης εκτέλεσης καθηκόντων (άρθρο 7 ν.3528/2007).');
    if(d.applicationImpediment==='yes')no.push('Δήλωσες ότι συντρέχει κάποιο από τα κωλύματα υποβολής αίτησης που αναφέρονται στην αναπτυσσόμενη λίστα.');
    else if(d.applicationImpediment!=='no')pending.push('Εκκρεμεί ο έλεγχος των συγκεκριμένων κωλυμάτων υποβολής αίτησης.');
    if(['yes','na'].indexOf(d.military)<0)pending.push('Χρειάζεται έλεγχος του πεδίου στρατιωτικών υποχρεώσεων στο ΟΠΣΥΔ ή της ειδικής εξαίρεσης που σε αφορά.');
    if(d.service==='issue')notes.push('Αναφέρεις πιθανό κώλυμα ανάληψης υπηρεσίας. Η δυνατότητα αίτησης δεν το αίρει· χρειάζεται έλεγχος και, όπου προβλέπεται, άρση του πριν από την ανάληψη.');
    else if(d.service!=='clear')notes.push('Έλεγξε πριν από την ανάληψη υπηρεσίας τυχόν ασυμβίβαστα ή άλλη ενεργή εκπαιδευτική σύμβαση.');
    return{status:no.length?'not_met':pending.length?'review':'basic_pass',reasons:no.concat(pending),notes:notes};
  }
  function period(start,end,now){
    var a=Date.parse(start),b=Date.parse(end),t=now===undefined?Date.now():Number(now);
    if(!Number.isFinite(a)||!Number.isFinite(b)||!Number.isFinite(t)||b<=a)return'unknown';
    return t<a?'before':t>=b?'closed':'open';
  }
  return{evaluate:evaluate,period:period,options:options};
}));
