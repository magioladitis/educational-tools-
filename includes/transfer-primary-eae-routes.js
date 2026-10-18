/* Advisory mapping of Primary EAE application routes — circular 138023/E2/2026, Part III F.
 * A route mismatch advises the user to select the proper application type; it
 * never changes application fields, confers special-category status or awards points.
 */
(function (root, factory) {
  'use strict';
  var api = factory();
  if (typeof module === 'object' && module.exports) module.exports = api;
  root.EducationTransferPrimaryEaeRoutes = api;
}(typeof globalThis !== 'undefined' ? globalThis : this, function () {
  'use strict';
  function check(input) {
    input=input||{};
    var mode=String(input.mode||'');
    var route=String(input.route||'unknown');
    var disability=String(input.disability||'none');
    var eaeMode=['eae','eeeek','deaf','blind','kedasy','placement-eae'].indexOf(mode)!==-1;
    if (!eaeMode) return {visible:false,status:'review',messages:[]};
    var messages=[],status='review';
    if (route==='unknown') {
      messages.push('Επιλέξτε υπηρεσιακή διαδρομή για να ελέγξετε τον σωστό τύπο ηλεκτρονικής αίτησης.');
    } else if (route==='general-same'||route==='general-other') {
      if (mode==='placement-eae') {
        status='problem';
        messages.push('Η μετάβαση από σχολείο Γενικής σε δομή ΕΑΕ, ακόμη και στην ίδια περιοχή, εξετάζεται με αίτηση μετάθεσης προς ΕΑΕ — όχι ως απλή βελτίωση ΕΑΕ.');
      } else {
        status='positive';
        messages.push('Η μετάβαση από Γενική σε ΕΑΕ '+(route==='general-same'?'στην ίδια':'σε άλλη')+' περιοχή εξετάζεται στη διαδικασία μεταθέσεων ΕΑΕ, με τις ειδικές προϋποθέσεις ένταξης.');
      }
    } else if (route==='special-same') {
      if (mode!=='placement-eae') {
        status='problem';
        messages.push('Για μετάθεση μεταξύ ΣΜΕΑΕ/ΕΕΕΕΚ/Τμημάτων Ένταξης της ίδιας περιοχής υποβάλλεται αίτηση βελτίωσης ΕΑΕ, όχι αίτηση από περιοχή σε περιοχή.');
      } else {
        status='positive';
        messages.push('Για μετακίνηση μεταξύ ΣΜΕΑΕ/ΕΕΕΕΚ/Τμημάτων Ένταξης της ίδιας περιοχής προβλέπεται αίτηση βελτίωσης και εξέταση από το οικείο υπηρεσιακό συμβούλιο.');
      }
    } else if (route==='special-other') {
      if(mode==='placement-eae'){
        status='problem';
        messages.push('Από δομή ΕΑΕ προς δομή ΕΑΕ άλλης περιοχής απαιτείται αίτηση μετάθεσης ΕΑΕ και όχι αίτηση τοπικής βελτίωσης.');
      }else{
        status='positive';
        messages.push('Η μετάθεση από δομή ΕΑΕ σε άλλη περιοχή εξετάζεται στη διαδικασία μεταθέσεων ΕΑΕ· επιβεβαιώστε τον ακριβή προορισμό και τα οργανικά κενά.');
      }
    } else if (route==='kedasy-to-kedasy') {
      if(mode!=='kedasy'){
        status='problem';
        messages.push('Για μετακίνηση ΚΕΔΑΣΥ → ΚΕΔΑΣΥ χρησιμοποιήστε τον ειδικό τύπο αίτησης ΚΕΔΑΣΥ, εντός ίδιας ή άλλης Περιφερειακής Διεύθυνσης.');
      }else{
        status='positive';
        messages.push('Η διαδικασία ΚΕΔΑΣΥ → ΚΕΔΑΣΥ προβλέπεται τόσο εντός όσο και μεταξύ Περιφερειακών Διευθύνσεων· ο τελικός έλεγχος είναι υπηρεσιακός.');
      }
    } else if (route==='deaf-exchange') {
      if(mode!=='eae' && mode!=='deaf' && mode!=='blind'){
        status='problem';
        messages.push('Η μετακίνηση ΣΜΕΑΕ ↔ Σχολείου Κωφών/Τυφλών εντάσσεται στη διαδικασία μεταθέσεων ΕΑΕ/Κωφών-Τυφλών και όχι στη βελτίωση ή στο ΚΕΔΑΣΥ.');
      }else{
        status='positive';
        messages.push('Η μετακίνηση μεταξύ ΣΜΕΑΕ και Σχολείου Κωφών/Τυφλών προβλέπεται εντός ή εκτός περιοχής· για Κωφών/Τυφλών ισχύει επιπλέον έλεγχος ΕΝΓ/Braille.');
      }
    } else {
      messages.push('Η συγκεκριμένη διαδρομή χρειάζεται επιβεβαίωση από την οικεία ΔΠΕ.');
    }
    if(disability==='lifetime80') {
      if(mode==='kedasy') {
        messages.push('Η κατ’ εξαίρεση μετάθεση λόγω αναπηρίας ιδίου ≥80% εφ’ όρου ζωής ΔΕΝ εφαρμόζεται στις μεταθέσεις σε ΚΕΔΑΣΥ.');
        if(status==='positive')status='review';
      } else if (mode==='placement-eae') {
        messages.push('Στην οριστική τοποθέτηση/βελτίωση η αναπηρία ≥80% εξετάζεται με τα κριτήρια της ειδικής κατηγορίας, για τις υφιστάμενες κενές θέσεις· δεν εφαρμόζεται αυτόματα εξαίρεση από οργανικά κενά.');
        if(status==='positive')status='review';
      } else {
        messages.push('Η κατ’ εξαίρεση μετάθεση ισχύει για περιοχές μετάθεσης ΕΑΕ (όχι ΚΕΔΑΣΥ), εφόσον πιστοποιείται αναπηρία ιδίου ≥80% εφ’ όρου ζωής με το προβλεπόμενο έγγραφο. Δεν παρέχεται αυτόματη έγκριση από την εφαρμογή.');
        if(status==='positive')status='review';
      }
      messages.push('Ελέγξτε τη συμπλήρωση των ειδικών πεδίων της ηλεκτρονικής αίτησης και τα δικαιολογητικά των §§2.2.2–2.2.3.');
    } else if (disability==='review') {
      messages.push('Αν εξετάζετε ειδική κατηγορία υγείας, επιβεβαιώστε πρώτα τα κατάλληλα αποδεικτικά και τη διαδικασία με τη ΔΠΕ.');
      if(status==='positive')status='review';
    }
    return {visible:true,status:status,messages:messages};
  }
  return {check:check};
}));
