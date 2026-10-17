/* Primary EAE/KEDASY transfer precheck, 2026-27. No dependency on Secondary rules. */
(function (root, factory) {
  'use strict';
  var api = factory();
  if (typeof module === 'object' && module.exports) module.exports = api;
  root.EducationTransferPrimaryEae = api;
}(typeof globalThis !== 'undefined' ? globalThis : this, function () {
  'use strict';
  var permittedKedasy = ['ΠΕ60','ΠΕ61','ΠΕ70','ΠΕ71'];
  var names = {
    phd:'Διδακτορικό στην ΕΑΕ ή στη Σχολική Ψυχολογία',
    masters:'Μεταπτυχιακό στην ΕΑΕ ή στη Σχολική Ψυχολογία',
    retraining:'Διετής μετεκπαίδευση ΕΑΕ των Διδασκαλείων',
    experience:'Πτυχίο ΑΕΙ και τουλάχιστον 5 έτη αποδεδειγμένης υπηρεσίας στην ΕΑΕ',
    pe11:'Πτυχίο ΤΕΦΑΑ με προβλεπόμενη κύρια ειδικότητα στην ΕΑΕ (αποκλειστικά ΠΕ11)'
  };
  function finiteYears(v){
    if (v === '' || v === null || typeof v === 'undefined') return null;
    var n = Number(String(v).replace(',','.'));
    return Number.isFinite(n) && n >= 0 && n <= 50 ? n : null;
  }
  function evaluate(data){
    var mode = data.mode || 'region';
    if (mode === 'region') return {status:'not-applicable',problems:[],checks:[],positive:[],qualificationValid:[]};
    var problems=[],checks=[],positive=[];
    var branch=String(data.branch||'').trim().toUpperCase();
    var origin=data.origin||'unknown';
    var quals=data.qualifications||{};
    var claimed=Object.keys(names).filter(function(k){return !!quals[k];});
    var valid=claimed.filter(function(k){
      if(k==='experience') return finiteYears(data.eaeYears)!==null && finiteYears(data.eaeYears)>=5;
      if(k==='pe11') return branch==='ΠΕ11';
      return true;
    });
    if(!branch) checks.push('Συμπληρώστε τον κλάδο του εκπαιδευτικού.');
    if(branch==='ΆΛΛΟΣ') checks.push('Απαιτείται επαλήθευση του ακριβούς κλάδου και της προβλεπόμενης οργανικής θέσης.');
    if(mode==='kedasy' && branch && permittedKedasy.indexOf(branch)===-1){
      problems.push('Ο κλάδος '+branch+' δεν περιλαμβάνεται στους ΠΕ60, ΠΕ61, ΠΕ70, ΠΕ71 που προβλέπονται για ΚΕΔΑΣΥ Πρωτοβάθμιας.');
    }
    if(origin==='unknown') checks.push('Επιλέξτε αν ο διορισμός έγινε σε Γενική ή ΕΑΕ ή αν έχει προηγηθεί μετάθεση σε ΕΑΕ.');
    if(origin==='general') {
      var generalYears=finiteYears(data.generalYears);
      if(generalYears===null) checks.push('Συμπληρώστε τα διδακτικά έτη από τον διορισμό στη Γενική Εκπαίδευση.');
      else if(generalYears<3) problems.push('Για τη μετάβαση από Γενική στην ΕΑΕ απαιτούνται τουλάχιστον 3 διδακτικά έτη από τον διορισμό.');
      else positive.push('Δηλώθηκε συμπλήρωση της τριετίας από τον διορισμό.');
      if(data.organic==='no') problems.push('Για μετάβαση από Γενική σε ΕΑΕ απαιτείται και υπηρέτηση της οργανικής θέσης.');
      else if(data.organic!=='yes') checks.push('Επιβεβαιώστε ότι έχει υπηρετηθεί η οργανική θέση.');
      if(!claimed.length) checks.push('Δεν έχει δηλωθεί ακόμη προσόν εξειδίκευσης στην ΕΑΕ.');
      else if(!valid.length) problems.push('Κανένα από τα δηλωμένα προσόντα εξειδίκευσης ΕΑΕ δεν καλύπτει ακόμη την ελεγχόμενη προϋπόθεση.');
      else positive.push('Δηλώθηκε τουλάχιστον μία εναλλακτική διαδρομή εξειδίκευσης ΕΑΕ.');
    } else if(origin==='appointed-eae') {
      var areaYears=finiteYears(data.appointmentAreaYears);
      if(areaYears===null) checks.push('Συμπληρώστε την υπηρεσία στην περιοχή διορισμού στην ΕΑΕ.');
      else if(areaYears<2) problems.push('Για διορισμό στην ΕΑΕ απαιτείται η νόμιμη διετία στην περιοχή διορισμού.');
      else positive.push('Δηλώθηκε συμπλήρωση της διετίας στην περιοχή διορισμού ΕΑΕ.');
      if(data.organic==='no') checks.push('Η υπηρεσία στην οργανική και τυχόν εξαιρέσεις χρειάζονται έλεγχο της ΔΠΕ.');
      else if(data.organic==='unknown') checks.push('Επιβεβαιώστε την υπηρεσία στην οργανική, όπου απαιτείται.');
    } else if(origin==='transferred-eae') {
      checks.push('Για ήδη μετατεθειμένο εκπαιδευτικό σε δομή ΕΑΕ χρειάζεται έλεγχος της προηγούμενης μετάθεσης και της υπηρεσιακής κατάστασης.');
      if(data.organic==='no') checks.push('Ελέγξτε με τη ΔΠΕ τη μη συμπλήρωση υπηρεσίας οργανικής και τυχόν νόμιμη εξαίρεση.');
      if(!valid.length) checks.push('Επαληθεύστε τα ήδη καταχωρισμένα προσόντα εξειδίκευσης ΕΑΕ στον υπηρεσιακό φάκελο.');
    }
    if(quals.experience && finiteYears(data.eaeYears)===null) checks.push('Συμπληρώστε την αποδεδειγμένη διδακτική εμπειρία ΕΑΕ για τη διαδρομή πενταετίας.');
    if(quals.experience && finiteYears(data.eaeYears)!==null && finiteYears(data.eaeYears)<5){
      checks.push('Η δηλωμένη πενταετής διαδρομή δεν συμπληρώνεται· ελέγξτε τα στοιχεία της, ακόμη κι αν υπάρχει άλλο αποδεκτό προσόν.');
    }
    if(quals.pe11 && branch && branch!=='ΠΕ11') checks.push('Η εναλλακτική διαδρομή τίτλου ΤΕΦΑΑ αφορά μόνο τον κλάδο ΠΕ11.');
    if(origin==='general' && valid.length) checks.push('Η νομιμότητα, αναγνώριση και συνάφεια των τίτλων επιβεβαιώνεται από την αρμόδια υπηρεσία.');
    if(mode==='kedasy') checks.push('Η οργανική θέση, ο κλάδος και η πλήρωση των προϋποθέσεων ΕΑΕ ελέγχονται από τα αρμόδια υπηρεσιακά όργανα.');
    return {status:problems.length?'problem':checks.length?'review':'positive', problems:problems,checks:checks,positive:positive,qualificationValid:valid,claimed:claimed};
  }
  return {evaluate:evaluate, permittedKedasy:permittedKedasy.slice(), qualificationNames:names, finiteYears:finiteYears};
}));
