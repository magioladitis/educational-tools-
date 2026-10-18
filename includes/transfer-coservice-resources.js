/* Co-service practical evidence guide, 2026–27 transfer circulars.
 * Official e-EFKA pathways are discovery aids, not automatic proof of eligibility.
 * No user data is transmitted by this component.
 */
(function(global){
'use strict';
var URL={
 employed:'https://www.e-efka.gov.gr/el/elektronikes-yperesies/bebaiose-proyperesias',
 insurance:'https://www.e-efka.gov.gr/el/elektronikes-yperesies/atomikos-logariasmos-asphalises',
 history:'https://www.e-efka.gov.gr/en/node/4553',
 request:'https://www.e-efka.gov.gr/el/elektronikes-yperesies/e-ephka-kleisimo-ranteboy-elektroniko-aitema-exypereteses',
 aade:'https://www.gov.gr/upourgeia/oloi-foreis/anexartete-arkhe-demosion-esodon-aade/enarxe-atomikes-epikheireses'
};
function a(url,label){return '<a href="'+url+'" target="_blank" rel="noopener noreferrer">'+label+' ↗</a>';}
function forCase(type){
 if(['private_employee','remote_employee'].indexOf(type)!==-1)return {
  title:'Ιδιωτικός τομέας — από πού ξεκινάτε',
  lines:[
   a(URL.employed,'e-ΕΦΚΑ: Βεβαίωση προϋπηρεσίας μισθωτών')+' — ελέγξτε αν αναγράφει τις περιόδους ασφάλισης που χρειάζεστε.',
   a(URL.insurance,'e-ΕΦΚΑ: Ατομικός Λογαριασμός Ασφάλισης')+' — για διασταύρωση εργοδοτών και ασφαλιστικών ημερών. Είναι <strong>πληροφοριακός</strong>, όχι αυτοτελής βεβαίωση ασφάλισης.',
   'Αν τα ψηφιακά έγγραφα δεν τεκμηριώνουν και τον <strong>τόπο εργασίας</strong> και την απαιτούμενη διετία, '+a(URL.request,'υποβάλετε ηλεκτρονικό αίτημα στον e-ΕΦΚΑ')+' για κατάλληλη βεβαίωση. Αν ο φορέας δεν τη χορηγεί, ακολουθήστε τον συνδυασμό βεβαίωσης εργοδότη και ασφαλιστικών στοιχείων της εγκυκλίου.'
  ]
 };
 if(['self_employed','private_doctor'].indexOf(type)!==-1)return {
  title:'Ελεύθερο επάγγελμα — ασφαλιστική βεβαίωση και τόπος δραστηριότητας',
  lines:[
   a(URL.history,'e-ΕΦΚΑ: Συνοπτικό και Αναλυτικό Ιστορικό Ασφάλισης')+' — ελέγξτε τις περιόδους. Το ιστορικό δεν αποδεικνύει από μόνο του την έδρα ή τη συνυπηρέτηση.',
   a(URL.request,'e-ΕΦΚΑ: Ηλεκτρονικό αίτημα εξυπηρέτησης')+' — ζητήστε κατάλληλη βεβαίωση ασφαλιστικού φορέα, διευκρινίζοντας ότι προορίζεται για συνυπηρέτηση σε μετάθεση εκπαιδευτικού.',
   a(URL.aade,'myAADE: Βεβαίωση έναρξης εργασιών')+' — συμπληρωματικό στοιχείο επαγγελματικής έδρας. <strong>Δεν αντικαθιστά</strong> τη βεβαίωση ασφαλιστικού φορέα. Η ηλεκτρονική βεβαίωση προϋπηρεσίας μη μισθωτών <strong>για ΑΣΕΠ</strong> δεν παρουσιάζεται ως γενικό δικαιολογητικό συνυπηρέτησης.'
  ]
 };
 return null;
}
function render(type){
 var c=forCase(type);if(!c)return '';
 return '<section class="transfer-coservice-resources" aria-label="Πρακτικός οδηγός δικαιολογητικών συνυπηρέτησης"><p><strong>'+c.title+'</strong></p><ul>'+c.lines.map(function(v){return '<li>'+v+'</li>';}).join('')+'</ul><p class="transfer-coservice-resources__note"><strong>Πριν την υποβολή:</strong> πρέπει να προκύπτουν ασφαλιστική ιδιότητα, τόπος εργασίας/δραστηριότητας και διετία όπου απαιτείται. Τα έγγραφα συνυπηρέτησης πρέπει κατά κανόνα να έχουν εκδοθεί εντός τελευταίου τριμήνου. Ελέγξτε με τη ΔΠΕ/ΔΔΕ την επάρκεια του φακέλου.</p></section>';
}
function printText(type){
 var c=forCase(type);if(!c)return '';
 return c.title+' — Επίσημες υπηρεσίες: '+(type==='private_employee'||type==='remote_employee'?[URL.employed,URL.insurance,URL.request].join(' · '):[URL.history,URL.request,URL.aade].join(' · '))+' . Οι ψηφιακές πληροφορίες δεν αντικαθιστούν υποχρεωτική βεβαίωση ασφαλιστικού φορέα.';
}
global.EducationTransferCoServiceResources=Object.freeze({render:render,printText:printText});
})(window);
