/* Primary Education — PE73 minority programme: advisory precheck only.
 * Source: Circular 138023/E2/15-10-2026, Part III, Chapter E.
 * No unverified official electronic application code or vacancies in this module.
 */
(function(root){
  'use strict';
  var areas={xanthi:'Ξάνθη',rodopi:'Ροδόπη',evros:'Έβρος'};
  function evaluate(input){
    input=input||{};
    var issues=[],notes=[];
    var route=input.route==='within'?'within':'between';
    var samePrefecture=route==='between' && input.origin && input.destination===input.origin;
    var body=route==='within'?'Οικείο ΠΥΣΠΕ':samePrefecture?'Αρμόδια ΔΠΕ — υπηρεσιακή επαλήθευση':'ΑΠΥΣΠΕ Ανατολικής Μακεδονίας–Θράκης';
    if(input.branch==='other') issues.push('Αυτή η ειδική διαδρομή προορίζεται αποκλειστικά για τον κλάδο ΠΕ73 του μειονοτικού προγράμματος.');
    else if(input.branch!=='pe73')notes.push('Επιβεβαιώστε ότι ανήκετε στον κλάδο ΠΕ73 του μειονοτικού προγράμματος.');
    if(!areas[input.origin])notes.push('Επιλέξτε την Περιφερειακή Ενότητα οργανικής θέσης.');
    if(route==='between'){
      if(!areas[input.destination])notes.push('Επιλέξτε την Περιφερειακή Ενότητα προορισμού.');
      else if(samePrefecture)notes.push('Ίδια Περιφερειακή Ενότητα: η εγκύκλιος διακρίνει μετάθεση μεταξύ περιοχών και βελτίωση εντός νομού. Επιβεβαιώστε εάν πρόκειται για διαφορετικές περιοχές μετάθεσης ή για βελτίωση και ποιο συμβούλιο είναι αρμόδιο.');
    }else notes.push('Η βελτίωση εξετάζεται εντός του νομού της οργανικής θέσης. Οι συγκεκριμένες σχολικές προτιμήσεις και τα κενά καθορίζονται από την αρμόδια υπηρεσία.');
    if(input.organic==='no') notes.push('Η μη συμπλήρωση υπηρεσίας στην οργανική απαιτεί εξατομικευμένο υπηρεσιακό έλεγχο και πιθανή εφαρμογή εξαιρέσεων.');
    else if(input.organic!=='yes')notes.push('Επιβεβαιώστε τις προϋποθέσεις υπηρέτησης της οργανικής θέσης.');
    notes.push('Αρμόδιο όργανο: '+body+'. Η θετική ένδειξη δεν αποτελεί απόφαση μετάθεσης ούτε βεβαίωση οργανικού κενού.');
    return {route:route,body:body,status:issues.length?'problem':input.branch==='pe73'&&areas[input.origin]&&(route==='within'||areas[input.destination]&&!samePrefecture)&&input.organic==='yes'?'positive':'review',issues:issues,notes:notes};
  }
  root.EducationTransferPrimaryMinority=Object.freeze({evaluate:evaluate});
}(typeof window!=='undefined'?window:this));
