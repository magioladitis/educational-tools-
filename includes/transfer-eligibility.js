/* Transfer-specific advice. Rule baseline: Circular 138014/E2/15-10-2026, pp.35–42.
   Advisory only: NOT a legal decision or final eligibility verification. */
(function(global){
  'use strict';
  var EAE_TITLES={
    doctorate:'Διδακτορικό στην ΕΑΕ / Σχολική Ψυχολογία',
    masters:'Μεταπτυχιακό στην ΕΑΕ / Σχολική Ψυχολογία',
    two_year:'Πτυχίο διετούς μετεκπαίδευσης στην ΕΑΕ',
    experience:'Βασικό πτυχίο ΑΕΙ + πενταετής αποδεδειγμένη υπηρεσία στην ΕΑΕ',
    pe11:'ΠΕ11: κύρια ειδικότητα ΤΕΦΑΑ στην προβλεπόμενη Ειδική/Προσαρμοσμένη Φυσική Αγωγή'
  };
  var MUSIC_SUBJECTS={
    theory:{name:'Θεωρητικά της μουσικής',document:'Πτυχίο αντίστιξης ή φούγκας ή δίπλωμα σύνθεσης και τεκμηριωμένη ενασχόληση τουλάχιστον πέντε ετών στο αντίστοιχο μάθημα.'},
    traditional:{name:'Ελληνική παραδοσιακή μουσική',document:'Δίπλωμα Βυζαντινής Εκκλησιαστικής Μουσικής, γνώση δημοτικού τραγουδιού και τεκμηριωμένη ενασχόληση τουλάχιστον πέντε ετών.'},
    ensemble:{name:'Διεύθυνση χορωδιακών / ορχηστρικών συνόλων',document:'Αντίστοιχος τίτλος σπουδών και τεκμηριωμένη πενταετής ενασχόληση· η εγκύκλιος προβλέπει ειδική κρίση όταν δεν υπάρχουν τίτλοι.'},
    instrument:{name:'Ευρωπαϊκό μουσικό όργανο',document:'Δίπλωμα ή πτυχίο μουσικού οργάνου και τεκμηριωμένη πενταετής ενασχόληση με τη διδασκαλία του.'},
    other:{name:'Άλλο αντικείμενο',document:'Ελέγξτε την ακριβή ειδικότητα και τα προβλεπόμενα τεκμήρια βάσει θέσης και ετήσιας εγκυκλίου.'}
  };
  /* Retained-dossier rules explicitly verified in circular 138014/E2/15-10-2026. */
  var BASELINE_CIRCULAR=Object.freeze({schoolYear:'2026–2027',reference:'138014/Ε2/15-10-2026',
    eaePriorYears:Object.freeze(['2025','2026']),
    artsPriorYears:Object.freeze(['2021','2022','2023','2024','2025','2026'])});
  var ART_BRANCHES=Object.freeze({pe08:'ΠΕ08 — Εικαστικών',pe11:'ΠΕ11 — Φυσικής Αγωγής',
    pe79:'ΠΕ79.01 — Μουσικής Επιστήμης',pe9101:'ΠΕ91.01 — Θεατρικών Σπουδών',
    pe9102:'ΠΕ91.02 — Δραματικής Τέχνης'});
  function dossier(a) {
    a=a||{};
    var years=a.kind==='eae'?BASELINE_CIRCULAR.eaePriorYears:BASELINE_CIRCULAR.artsPriorYears;
    var matched=years.indexOf(String(a.year||''))!==-1;
    var changed=!!a.newElements;
    return {matched:matched,needsNewDocuments:changed,
      skipResubmission:matched&&!changed,
      status: matched&&!changed?'Προηγούμενος φάκελος — δεν απαιτείται εκ νέου αποστολή σύμφωνα με την εγκύκλιο 2026–2027.':
        matched?'Υπάρχει προηγούμενος φάκελος, αλλά τα νέα στοιχεία/δικαιολογητικά πρέπει να σταλούν με την προβλεπόμενη διαδικασία.':
        'Ελέγξτε την αρχική υποβολή του φακέλου προσόντων σύμφωνα με την εγκύκλιο 2026–2027.',
      limitation:'Αφορά τον φάκελο προσόντων/βιογραφικού, όχι την ετήσια αίτηση, ούτε τυχόν νέα δικαιολογητικά μοριοδότησης. Ισχύει μόνο για ήδη αποσταλμένο φάκελο στη συγκεκριμένη κατηγορία σχολείου ή στην ΕΑΕ, όχι για διαφορετικό τύπο αίτησης.'};
  }
  function art(a) {
    a=a||{};
    var unmet=[],review=[],branch=a.branch||'general',ex=a.exceptionType||'none';
    var specialized=Object.prototype.hasOwnProperty.call(ART_BRANCHES,branch);
    var register=global.EducationArtisticVacancies;
    if(register&&Object.prototype.hasOwnProperty.call(a,'branchCode')){
      if(!a.branchCode)review.push('Επιλέξτε κλάδο με προκηρυγμένο οργανικό κενό 2026–2027.');
      else if(!register.hasBranch(a.branchCode))unmet.push('Δεν έχει προκηρυχθεί οργανικό κενό για τον επιλεγμένο κλάδο.');
      else if(!a.school)review.push('Επιλέξτε Καλλιτεχνικό σχολείο με προκηρυγμένο οργανικό κενό στον κλάδο.');
      else {
        var options=register.specializations(a.branchCode,a.school);
        if(!options.length)unmet.push('Το επιλεγμένο σχολείο δεν διαθέτει προκηρυγμένο κενό στον κλάδο.');
        else if(options.length>1&&!a.specialization)review.push('Επιλέξτε την προκηρυγμένη ειδίκευση της θέσης.');
        else if(a.specialization!==null&&!register.count(a.branchCode,a.school,a.specialization))unmet.push('Η συγκεκριμένη ειδίκευση δεν περιλαμβάνεται στα προκηρυγμένα κενά του σχολείου.');
      }
      if(a.branchCode===register.musicGroup)review.push('Προσοχή: η προκήρυξη αναγράφει κοινή κατηγορία Μουσικής ΠΕ79–ΤΕ16. Δεν είναι δύο ανεξάρτητα σύνολα κενών· ελέγξτε την ακριβή επιλεξιμότητα στο επίσημο σύστημα.');
    }
    if(!a.organic) review.push('Ελέγξτε την υπηρέτηση οργανικής θέσης ή τυχόν ειδική νόμιμη εξαίρεση με τη ΔΔΕ.');
    if(year(a.teachingYears)<5 && ex==='none') unmet.push('Η εγκύκλιος 2026–2027 απαιτεί τουλάχιστον 5 έτη διδακτικής εμπειρίας σε σχολεία Δ.Ε. έως 31-08-2027.');
    if(ex==='direct_music'&&branch!=='pe79') unmet.push('Η εξαίρεση απευθείας διορισμού σε Μουσικό αφορά αποκλειστικά εκπαιδευτικούς μουσικής παιδείας.');
    if(ex==='direct_music'||ex==='primary_transfer') review.push('Ελέγξτε υπηρεσιακά τη συνδρομή της δηλωμένης εξαίρεσης από την πενταετία.');
    if(branch==='other') review.push('Επαληθεύστε τον κλάδο/ειδικότητα και αν περιλαμβάνεται στην ομάδα καλλιτεχνικής παιδείας.');
    if(specialized&&!a.orientation) review.push('Επιλέξτε κατεύθυνση/αντικείμενο για πιο συγκεκριμένες οδηγίες τεκμηρίωσης.');
    var notes=unmet.concat(review);
    var verdict=unmet.length?'not_established':review.length?'needs_verification':'preliminary_met';
    return {specialized:specialized,branchTitle:ART_BRANCHES[branch]||'', advice:notes,
      unmetCriteria:unmet,review:review,verdict:verdict,
      status:verdict==='not_established'?'Δεν πληρούνται όλες οι ελεγχόμενες βασικές προϋποθέσεις με τα δηλωμένα στοιχεία':
        verdict==='needs_verification'?'Χρειάζονται υπηρεσιακοί έλεγχοι / πρόσθετα στοιχεία':'Ο αρχικός προέλεγχος συμπληρώθηκε — απαιτείται κρίση αρμοδίων οργάνων'};
  }
  function year(v){var n=Number(v);return Number.isFinite(n)?Math.max(0,n):0;}
  // Ανεξάρτητες επιλογές όπως στο e-Datacenter. Μία έγκυρη διαδρομή αρκεί.
  // Το legacy `qualification` γίνεται δεκτό για παλιούς callers/tests.
  function eae(a){
    a=a||{};
    var branch=a.branch||'other',selected=Array.isArray(a.qualifications)?a.qualifications.slice():[a.qualification||'none'];
    var allowed=Object.keys(EAE_TITLES);
    selected=selected.filter(function(q,i){return allowed.indexOf(q)!==-1&&selected.indexOf(q)===i;});
    var unmet=[],review=[],valid=[],invalid=[];
    function eaeYears(){var raw=a.eaeYears;if(raw==null||String(raw).trim()==='')return null;
      var n=Number(String(raw).replace(',','.'));return Number.isFinite(n)&&n>=0&&n<=50?n:null;}
    selected.forEach(function(q){
      if(q==='experience'){
        var y=eaeYears();
        if(y===null)review.push('Στην πενταετή διαδρομή συμπληρώστε έγκυρη προϋπηρεσία ΕΑΕ (0–50 έτη).');
        else if(y<5)invalid.push('Η διαδρομή πτυχίου ΑΕΙ + υπηρεσίας απαιτεί τουλάχιστον πέντε αποδεδειγμένα έτη στην ΕΑΕ.');
        else valid.push(q);
      }else if(q==='pe11'){
        if(branch!=='pe11')invalid.push('Η διαδρομή κύριας ειδικότητας ΤΕΦΑΑ αφορά αποκλειστικά τον κλάδο ΠΕ11.');
        else valid.push(q);
      }else valid.push(q);
    });
    if(!selected.length)review.push('Επιλέξτε τουλάχιστον ένα από τα πέντε αποδεκτά προσόντα εξειδίκευσης ΕΑΕ.');
    // An ineligible *extra* qualification is not fatal if another route is valid.
    if(!valid.length && invalid.length)unmet=unmet.concat(invalid);
    else if(invalid.length)review=review.concat(invalid.map(function(t){return 'Πρόσθετο δηλωμένο προσόν προς διόρθωση: '+t;}));
    if(a.origin!=='eae'){
      var rawTeachingYears=Number(a.teachingYears);
      if(a.teachingYears==null||String(a.teachingYears).trim()===''||!Number.isFinite(rawTeachingYears)||rawTeachingYears<0||rawTeachingYears>50)
        review.push('Συμπληρώστε έγκυρα διδακτικά έτη από διορισμό, από 0 έως 50.');
      else if(rawTeachingYears<3)unmet.push('Για μετάβαση από Γενική/Επαγγελματική σε ΕΑΕ απαιτείται τριετής διδακτική υπηρεσία από τον διορισμό.');
      if(a.newly&&(!Number.isFinite(Number(a.areaYears))||Number(a.areaYears)<0||Number(a.areaYears)>45))review.push('Τα έτη υπηρεσίας στην περιοχή διορισμού πρέπει να είναι από 0 έως 45.');
      else if(a.newly&&year(a.areaYears)<2)unmet.push('Για νεοδιόριστους ελέγχονται δύο έτη στην περιοχή διορισμού, εντός της απαιτούμενης τριετίας.');
    }else review.push('Για μετακίνηση ΕΑΕ → ΕΑΕ ελέγξτε χωριστά υπηρέτηση οργανικής, περιοχή και τυχόν ειδικές υπηρεσιακές υποχρεώσεις.');
    if(a.destination==='kedasy' && ['pe02','pe03','pe04'].indexOf(branch)===-1)unmet.push('Για ΚΕΔΑΣΥ ελέγχεται κλάδος ΠΕ02, ΠΕ03 ή ΠΕ04 και ύπαρξη αντίστοιχης κενής οργανικής θέσης.');
    if(a.destination==='kedasy' && ['pe02','pe03','pe04'].indexOf(branch)!==-1)review.push('Για ΚΕΔΑΣΥ επιβεβαιώστε την προβλεπόμενη οργανική θέση του κλάδου στο οικείο οργανόγραμμα.');
    if(a.destination==='deaf'||a.destination==='blind'){
      var certificate=a.destination==='deaf'?'Ελληνικής Νοηματικής Γλώσσας':'γραφής Braille';
      if(a.sensoryCertification==='no')unmet.push('Για τη συγκεκριμένη σχολική μονάδα απαιτείται πιστοποιημένη γνώση '+certificate+'. Δηλώθηκε ότι δεν υπάρχει.');
      else if(a.sensoryCertification==='yes')review.push('Επαληθεύστε τη βεβαίωση πιστοποιημένης γνώσης '+certificate+' και τον αποδεκτό φορέα έκδοσης.');
      else review.push('Για τη συγκεκριμένη σχολική μονάδα απαιτείται επιπλέον πιστοποιημένη γνώση '+certificate+'.');
    }
    var verdict=unmet.length?'not_established':review.length?'needs_verification':'preliminary_met';
    return {selectedQualification:selected[0]||'none',selectedQualifications:selected,
      validQualifications:valid,qualificationTitle:selected.map(function(q){return EAE_TITLES[q];}).join(' · '),
      advice:unmet.concat(review),unmet:unmet,review:review,verdict:verdict,
      status:verdict==='not_established'?'Δεν πληρούται μία ή περισσότερες βασικές προϋποθέσεις με τα δηλωμένα στοιχεία':
        verdict==='needs_verification'?'Χρειάζονται έλεγχοι/στοιχεία':
          'Δεν εντοπίστηκε προφανές κώλυμα στα δηλωμένα βασικά πεδία — απαιτείται έλεγχος ΔΔΕ'};
  }
  // Μετάβαση από ΕΑΕ στη Γενική: διαφορετικός κανόνας για διορισθέντες και μετατεθέντες.
  // Κανόνες κύκλου 2026–2027, εγκύκλιος 138014/Ε2/15-10-2026, σελ. 35, §2.
  function returnToGeneral(a){
    a=a||{};
    var origin=a.origin||'general';
    var unmet=[], review=[];
    function asYears(value){
      if(value===undefined||value===null||String(value).trim()==='') return null;
      var n=Number(String(value).trim().replace(',','.'));
      return Number.isFinite(n)&&n>=0&&n<=50?n:null;
    }
    function yearsLeft(required,provided){
      return String(Math.round((required-provided)*100)/100).replace('.',',');
    }
    if(origin==='appointed_eae'){
      var eaeYears=asYears(a.eaeYears),areaYears=asYears(a.areaYears);
      if(areaYears!==null&&areaYears>45)areaYears=null;
      if(eaeYears===null||areaYears===null){
        review.push('Συμπληρώστε έγκυρα έτη υπηρεσίας στην ΕΑΕ και στην περιοχή διορισμού.');
      }else{
        if(eaeYears<5) unmet.push('Δεν έχει συμπληρωθεί η απαιτούμενη πενταετής υπηρεσία (5 έτη) σε σχολικές μονάδες ΕΑΕ. Υπολείπονται '+yearsLeft(5,eaeYears)+' έτη.');
        if(areaYears<2) unmet.push('Δεν έχουν συμπληρωθεί τα 2 σχολικά έτη στην περιοχή διορισμού. Υπολείπονται '+yearsLeft(2,areaYears)+' έτη.');
      }
    }else if(origin==='transferred_eae'){
      var sinceTransferYears=asYears(a.sinceTransferYears);
      if(sinceTransferYears===null) review.push('Συμπληρώστε έγκυρο χρόνο διδακτικής υπηρεσίας στην ΕΑΕ μετά τη μετάθεση.');
      else if(sinceTransferYears<2) unmet.push('Δεν έχουν συμπληρωθεί τα 2 έτη διδακτικής υπηρεσίας στην ΕΑΕ μετά τη μετάθεση. Υπολείπονται '+yearsLeft(2,sinceTransferYears)+' έτη.');
      if(!a.organicServed) unmet.push('Δεν έχει δηλωθεί η απαιτούμενη υπηρέτηση της οργανικής θέσης στην ΕΑΕ για 1 έτος.');
    }else if(origin==='other'){
      review.push('Επιβεβαιώστε με τη ΔΔΕ την υπηρεσιακή προέλευση και την εφαρμοστέα διάταξη.');
    }
    var verdict=unmet.length?'not_established':review.length?'needs_verification':origin==='general'?'not_applicable':'preliminary_met';
    var status=verdict==='not_established'?'Δεν θεμελιώνεται δικαίωμα μετάθεσης στη Γενική Εκπαίδευση με τα δηλωμένα στοιχεία.':
      verdict==='needs_verification'?'Χρειάζονται πρόσθετα στοιχεία ή υπηρεσιακός έλεγχος για τη θεμελίωση δικαιώματος.':
      verdict==='not_applicable'?'Δεν εφαρμόζεται ο ειδικός κανόνας επιστροφής από ΕΑΕ με βάση την επιλογή σας. Ισχύουν οι γενικοί όροι μετάθεσης.':
      'Οι δηλωμένες προϋποθέσεις επιστροφής από ΕΑΕ στη Γενική φαίνεται να καλύπτονται. Απαιτείται επιβεβαίωση από τη ΔΔΕ και έλεγχος των λοιπών όρων μετάθεσης.';
    return {origin:origin, applicable:origin!=='general', verdict:verdict,
      meetsDeclaredConditions:verdict==='preliminary_met',
      needsReview:verdict==='needs_verification'||verdict==='not_established',
      unmetCriteria:unmet, advice:unmet.concat(review), status:status};
  }
  /* Πειραματικά Σχολεία Διαπολιτισμικής Εκπαίδευσης — Κεφ. Ζ΄ σελ. 43–45. */
  var INTERCULTURAL_QUALIFICATIONS=Object.freeze([
    {id:'origin_language',title:'Επαρκής γνώση της γλώσσας προέλευσης της πλειονότητας των μαθητών',proof:'Τίτλος γλωσσομάθειας της συγκεκριμένης γλώσσας, σύμφωνα με τους κανόνες υποβολής/μετάφρασης της εγκυκλίου.'},
    {id:'greek_second',title:'Διδασκαλία της Ελληνικής ως δεύτερης / ξένης γλώσσας',proof:'Τίτλος/πιστοποιητικό εξειδίκευσης που τεκμηριώνει το συγκεκριμένο γνωστικό αντικείμενο.'},
    {id:'intercultural_studies',title:'Σπουδές Διαπολιτισμικής Αγωγής',proof:'Πτυχίο μετεκπαίδευσης ή μεταπτυχιακός τίτλος στο σχετικό πεδίο.'},
    {id:'pedagogy_studies',title:'Σπουδές επιστημών της αγωγής',proof:'Τεκμήριο μετεκπαίδευσης ή μεταπτυχιακών σπουδών στις επιστήμες της αγωγής.'},
    {id:'subject_studies',title:'Σπουδές στο διδακτικό αντικείμενο',proof:'Πτυχίο μετεκπαίδευσης ή μεταπτυχιακός τίτλος στο διδασκόμενο αντικείμενο.'},
    {id:'school_experience',title:'Διδακτική εμπειρία σε Διαπολιτισμικό Σχολείο',proof:'Βεβαίωση υπηρεσίας/διδακτικής εμπειρίας στη συγκεκριμένη κατηγορία σχολείου.'},
    {id:'reception_experience',title:'Διδακτική εμπειρία σε Τάξεις Υποδοχής / Φροντιστηριακά Τμήματα',proof:'Αναλυτική βεβαίωση διδακτικής υπηρεσίας στις αντίστοιχες δομές.'},
    {id:'international_language',title:'Γνώση διεθνούς γλώσσας',proof:'Αποδεκτό πιστοποιητικό γνώσης Αγγλικών, Γαλλικών ή Γερμανικών.'},
    {id:'seminars',title:'Επιμόρφωση σε Διαπολιτισμική Εκπαίδευση',proof:'Βεβαίωση παρακολούθησης σεμιναρίου ΥΠΑΙΘΑ ή συνεργαζόμενου φορέα.'}
  ]);
  function intercultural(a){
    a=a||{};
    var unmet=[], review=[];
    var organic=a.organic||'unknown',current=a.current||'no',vacancy=a.vacancy||'unknown';
    if(organic==='no') unmet.push('Δεν έχει δηλωθεί υπηρέτηση της κατεχόμενης οργανικής θέσης έως 31/08/2027.');
    else if(organic!=='yes') review.push('Επιβεβαιώστε την υπηρέτηση οργανικής έως 31/08/2027.');
    if(current==='yes'){
      var y=Number(String(a.years==null?'':a.years).replace(',','.'));
      if(String(a.years==null?'':a.years).trim()===''||!Number.isFinite(y)||y<0||y>50) review.push('Δηλώστε έγκυρα έτη υπηρεσίας στην οργανική του Διαπολιτισμικού Σχολείου.');
      else if(y<2) unmet.push('Για ήδη οργανικά τοποθετημένους στα Διαπολιτισμικά απαιτούνται 2 έτη υπηρεσίας· με τα δηλωμένα στοιχεία υπολείπονται '+(Math.round((2-y)*100)/100)+' έτη.');
    }else if(current==='unknown')review.push('Επιβεβαιώστε αν ισχύει ο ειδικός κανόνας διετίας για ήδη οργανικά τοποθετημένους.');
    var vacancyRegister=global.EducationInterculturalVacancies;
    if(vacancyRegister && (Object.prototype.hasOwnProperty.call(a,'branch')||Object.prototype.hasOwnProperty.call(a,'school'))){
      if(!a.branch)review.push('Επιλέξτε κλάδο Δευτεροβάθμιας με προκηρυγμένο οργανικό κενό.');
      else if(!vacancyRegister.hasBranch(a.branch))unmet.push('Ο δηλωμένος κλάδος δεν περιλαμβάνεται στους κλάδους με προκηρυγμένο οργανικό κενό Δ.Ε. 2026–2027.');
      if(a.branch&&!a.school)review.push('Επιλέξτε σχολείο στο οποίο προκηρύσσεται οργανικό κενό του συγκεκριμένου κλάδου.');
      else if(a.branch&&a.school&&!vacancyRegister.count(a.branch,a.school))unmet.push('Ο επιλεγμένος συνδυασμός κλάδου και σχολείου δεν εμφανίζεται στα προκηρυγμένα κενά.');
    }
    if(vacancy==='no')unmet.push('Δεν μπορεί να επιλεγεί σχολείο χωρίς προκηρυγμένο οργανικό κενό στον κλάδο/ειδικότητα.');
    else if(vacancy!=='yes')review.push('Ελέγξτε την ύπαρξη οργανικού κενού στο συγκεκριμένο σχολείο/κλάδο στο teachers.minedu.gov.gr.');
    if(a.languageCase==='unknown')review.push('Ελέγξτε τη γλώσσα προέλευσης της πλειονότητας των μαθητών και την ειδική εξαίρεση για σχολεία με πλειοψηφία Ρομά.');
    var qualified=Array.isArray(a.qualifications)?a.qualifications.filter(function(x){return INTERCULTURAL_QUALIFICATIONS.some(function(q){return q.id===x;});}):[];
    var verdict=unmet.length?'not_established':review.length?'needs_verification':'preliminary_met';
    var status=verdict==='not_established'?'Με τα δηλωμένα στοιχεία δεν πληρούνται οι ελεγχόμενες προϋποθέσεις ή δεν επιτρέπεται η συγκεκριμένη προτίμηση.':verdict==='needs_verification'?'Χρειάζονται επιπλέον στοιχεία/υπηρεσιακή επαλήθευση.':'Οι βασικοί δηλωμένοι όροι φαίνεται να πληρούνται — απαιτείται τελικός έλεγχος ΔΔΕ.';
    return {verdict:verdict,status:status,unmetCriteria:unmet,advice:unmet.concat(review),selectedQualifications:qualified,languageException:a.languageCase==='roma'};
  }
  function music(a){
    a=a||{};var type=a.educationType||'general';var exception=a.exceptionType||'none';
    var unmet=[],review=[];
    if(!a.organic)review.push('Επιβεβαιώστε την υποχρέωση υπηρέτησης οργανικής θέσης και τυχόν ειδική εξαίρεση.');
    if(year(a.teachingYears)<5&&exception==='none')unmet.push('Απαιτείται πενταετής διδακτική εμπειρία στη Δευτεροβάθμια έως την προβλεπόμενη ημερομηνία αναφοράς της εγκυκλίου.');
    if(exception==='direct_music'&&type!=='specialized')unmet.push('Η εξαίρεση απευθείας διορισμού σε Μουσικό αφορά εκπαιδευτικούς μουσικής παιδείας.');
    if(exception==='direct_music'||exception==='primary_transfer')review.push('Η δηλωμένη εξαίρεση από την πενταετία χρειάζεται υπηρεσιακή επιβεβαίωση.');
    if(type==='specialized'&&!MUSIC_SUBJECTS[a.subject])review.push('Επιλέξτε μουσικό αντικείμενο ώστε να εμφανιστούν τα ειδικά αποδεικτικά.');
    var notices=unmet.concat(review);
    var verdict=unmet.length?'not_established':review.length?'needs_verification':'preliminary_met';
    return {advice:notices,unmetCriteria:unmet,review:review,verdict:verdict,
      subjectInfo:MUSIC_SUBJECTS[a.subject]||null,type:type,
      status:verdict==='not_established'?'Δεν πληρούνται όλες οι ελεγχόμενες βασικές προϋποθέσεις με τα δηλωμένα στοιχεία':
        verdict==='needs_verification'?'Χρειάζονται έλεγχοι/στοιχεία':'Αρχικός έλεγχος συμπληρώθηκε — η κρίση προσόντων γίνεται από τα αρμόδια όργανα'};
  }
  global.EducationTransferEligibility=Object.freeze({eae:eae,music:music,art:art,intercultural:intercultural,returnToGeneral:returnToGeneral,dossier:dossier,
    BASELINE_CIRCULAR:BASELINE_CIRCULAR,ART_BRANCHES:ART_BRANCHES,
    EAE_TITLES:Object.freeze(EAE_TITLES),MUSIC_SUBJECTS:Object.freeze(MUSIC_SUBJECTS),INTERCULTURAL_QUALIFICATIONS:INTERCULTURAL_QUALIFICATIONS});
})(window);
