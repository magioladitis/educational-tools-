/* Standalone Primary Education intercultural transfers precheck. Circular 138023/E2/2026, part III ch. D. */
(function(root,factory){
  'use strict';
  var api=factory();
  if(typeof module==='object'&&module.exports)module.exports=api;
  root.EducationTransferPrimaryIntercultural=api;
}(typeof globalThis!=='undefined'?globalThis:this,function(){
  'use strict';
  var names={
    'origin-language':'Γνώση γλώσσας προέλευσης μαθητών',
    'greek-second':'Διδασκαλία Ελληνικής ως δεύτερης/ξένης γλώσσας',
    'inter-studies':'Σπουδές Διαπολιτισμικής Αγωγής',
    'education-studies':'Σπουδές επιστημών της αγωγής',
    'subject-studies':'Σπουδές διδακτικού αντικειμένου',
    'inter-experience':'Διδακτική εμπειρία σε Διαπολιτισμικά Σχολεία',
    'reception-experience':'Διδακτική εμπειρία σε Τάξεις Υποδοχής/Φροντιστηριακά Τμήματα',
    'international-language':'Γνώση διεθνούς γλώσσας',
    'seminars':'Πιστοποιήσεις επιμορφωτικών σεμιναρίων Διαπολιτισμικής Εκπαίδευσης'
  };
  function years(value){
    if(value===null || typeof value==='undefined'||String(value).trim()==='')return null;
    var number=Number(String(value).replace(',','.'));
    return Number.isFinite(number)&&number>=0&&number<=50?number:null;
  }
  function evaluate(input){
    var problems=[],checks=[],positives=[];
    if(input.organic==='no') problems.push('Δεν δηλώθηκε το απαιτούμενο έτος υπηρεσίας στην κατεχόμενη οργανική έως 31/08/2027. Ελέγξτε τυχόν ειδική νόμιμη περίπτωση με τη ΔΠΕ.');
    else if(input.organic!=='yes')checks.push('Επιβεβαιώστε το έτος υπηρεσίας στην οργανική έως 31/08/2027.');
    else positives.push('Δηλώθηκε συμπλήρωση του έτους οργανικής.');
    if(input.current==='yes'){
      var n=years(input.currentYears);
      if(n===null)checks.push('Συμπληρώστε την υπηρεσία στο Διαπολιτισμικό Σχολείο (έως 50 έτη).');
      else if(n<2)problems.push('Για μετάθεση ήδη υπηρετούντος σε άλλο Διαπολιτισμικό απαιτείται διετία σε αυτό.');
      else positives.push('Δηλώθηκε η απαιτούμενη διετία στο Διαπολιτισμικό.');
    }
    if(input.inclusion==='yes'){
      if(input.eaeQualification==='no')problems.push('Η θέση Τμήματος Ένταξης απαιτεί και τα προβλεπόμενα προσόντα ΕΑΕ.');
      else checks.push('Για Τμήμα Ένταξης πρέπει να επιβεβαιωθούν οι προϋποθέσεις ΕΑΕ, ακόμη και με δηλωμένο προσόν.');
    } else if(input.inclusion==='unknown')checks.push('Ελέγξτε αν η προκηρυχθείσα θέση αφορά Τμήμα Ένταξης.');
    if(!Array.isArray(input.qualifications)||!input.qualifications.length)checks.push('Δεν έχουν δηλωθεί πρόσθετα προσόντα προτεραιότητας· δεν αποτελούν αυτοτελές υποχρεωτικό κριτήριο.');
    checks.push('Η σειρά προτεραιότητας, η συνάφεια προσόντων και τα διαθέσιμα οργανικά κενά ελέγχονται από το ΚΥΣΠΕ.');
    return {status:problems.length?'problem':checks.length?'review':'positive',problems:problems,checks:checks,positive:positives};
  }
  return {evaluate:evaluate,qualificationNames:Object.freeze(names),years:years};
}));
