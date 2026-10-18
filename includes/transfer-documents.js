/* Transfer document preparation guide. Advisory only; no personal data transmitted. */
(function(global){
'use strict';
var $=function(id){return document.getElementById(id);};
// Tolerate legacy test stubs that expose only the original form fields.
function optionalElement(id){try{return document.getElementById(id);}catch(err){return null;}}
var base=[
 ['record','Υπηρεσιακό μητρώο και χρόνος υπηρεσίας','Δείτε υπηρετήσεις και μόρια στο https://teachers.minedu.gov.gr/. Πίνακες Δ.Ε.: 23/11/2026· αιτήματα διορθώσεων 23–27/11/2026 (μεταθέσεις από περιοχή σε περιοχή). Αντιπαραβάλετε το μητρώο και επικοινωνήστε με τη ΔΔΕ για σφάλματα.','']
];
var special={};
var EAE_SOURCE='Εγκύκλιος 138014/Ε2/15-10-2026, κεφάλαιο Δ΄, σελ. 34–38.';
var MUSIC_SOURCE='Εγκύκλιος 138014/Ε2/15-10-2026, κεφάλαιο Ε΄, σελ. 39–41.';
var ART_SOURCE='Εγκύκλιος 138014/Ε2/15-10-2026, κεφάλαιο ΣΤ΄, σελ. 41–43.';
var IC_SOURCE='Εγκύκλιος 138014/Ε2/15-10-2026, κεφάλαιο Ζ΄, σελ. 43–45.';
var checked={};
var FAMILY_SOURCE='Πηγή: εγκύκλιος 138014/Ε2/15-10-2026, Μέρος Δεύτερο, κεφ. Β΄, ενότητες Β. Συνυπηρέτηση (σελ. 18–20) και Γ. Οικογενειακοί λόγοι (σελ. 20–21).';
/* The categories below are the 2026-27 secondary-education circular, not a blanket list of rights. */
var COSERVICE_CASES={
 unspecified:{summary:'Διαλέξτε καθεστώς εργασίας: τα δικαιολογητικά, η απαιτούμενη διετία και οι εξαιρέσεις διαφέρουν.',detail:'Για έγκυρο φάκελο καθορίστε αν πρόκειται για Δημόσιο με οργανική, ιδιωτικό τομέα, ελεύθερο επάγγελμα, δικηγόρο ή άλλη ειδική περίπτωση.'},
 public_organic:{summary:'Δημόσιος υπάλληλος/λειτουργός με οργανική: δεν απαιτείται διετία.',detail:'Βεβαίωση της υπηρεσίας που πιστοποιεί την οργανική θέση και τον ακριβή τόπο υπηρεσίας — όχι τον τόπο προσωρινής απόσπασης. Δεν απαιτείται συμπλήρωση διετίας.'},
 teacher_organic:{summary:'Μόνιμος εκπαιδευτικός: μετρά η οργανική θέση, όχι η απόσπαση/θητεία.',detail:'Βεβαίωση υπηρεσίας από την αρμόδια ΔΠΕ/ΔΔΕ με την οργανική θέση/περιοχή μετάθεσης. Για στελέχη εκπαίδευσης και θητεία σε Πρότυπα/Πειραματικά, μετρά η οργανική και όχι ο τόπος άσκησης καθηκόντων. Δεν απαιτείται διετία.'},
 teacher_disposal:{summary:'Εκπαιδευτικός στη διάθεση ΠΥΣΠΕ/ΠΥΣΔΕ: μόνο για μεταθέσεις από περιοχή σε περιοχή.',detail:'Βεβαίωση ΔΠΕ/ΔΔΕ ότι ο/η σύζυγος βρίσκεται στη διάθεση συγκεκριμένου ΠΥΣΠΕ/ΠΥΣΔΕ και σε ποια περιοχή μετάθεσης ανήκει. Δεν χορηγούνται οι μονάδες στη βελτίωση/οριστική τοποθέτηση.',localBlocked:true},
 private_employee:{summary:'Ιδιωτικός υπάλληλος: απαιτείται συμπληρωμένη διετία έως τη λήξη αιτήσεων.',detail:'Βεβαίωση ασφαλιστικού φορέα για τόπο εργασίας, ασφάλιση και τουλάχιστον δύο τελευταία χρόνια εργασίας έως 02/11/2026. Αν ο φορέας δεν εκδίδει βεβαίωση: βεβαίωση εργοδότη/-ών ΜΑΖΙ με μηχανογραφικά δελτία ασφάλισης· αν οι εισφορές δεν οριστικοποιήθηκαν, αποδεκτές οι υποβληθείσες ΑΠΔ.'},
 self_employed:{summary:'Ελεύθερος επαγγελματίας: διετία και βεβαίωση ασφαλιστικού φορέα.',detail:'Βεβαίωση ασφαλιστικού φορέα που τεκμηριώνει ασφάλιση, τόπο επαγγελματικής δραστηριότητας και τη διετία έως 02/11/2026. Στοιχεία myAADE μπορούν να τεκμηριώνουν την ενεργή έδρα αλλά ΔΕΝ αντικαθιστούν τη βεβαίωση ασφαλιστικού φορέα. Ειδικές προβλέψεις για μη χορήγηση βεβαιώσεων λόγω οφειλών.'},
 remote_employee:{summary:'Τηλεργασία: μόνο αποκλειστική, με έδρα τη ζητούμενη περιοχή και δήλωση στην ΕΡΓΑΝΗ.',detail:'Σύμβαση εργασίας ή τροποποίηση που τεκμηριώνει αποκλειστική τηλεργασία (όλο το ωράριο/όλες τις ημέρες) με έδρα τη ζητούμενη περιοχή και αντίστοιχη δήλωση στο Π.Σ. ΕΡΓΑΝΗ. Για ιδιωτικό υπάλληλο εξακολουθεί να ελέγχεται η διετία και η ασφαλιστική τεκμηρίωση.'},
 lawyer:{summary:'Δικηγόρος: δεν απαιτείται διετία.',detail:'Τεκμηρίωση της ιδιότητας του δικηγόρου και του τόπου νόμιμης επαγγελματικής εγκατάστασης (π.χ. στοιχεία Δικηγορικού Συλλόγου). Εφόσον διατηρούνται νόμιμα γραφεία σε διαφορετικές περιοχές μετάθεσης, μοριοδοτείται μόνο μία περιοχή που επιλέγει ο ενδιαφερόμενος. Ως άμισθος δημόσιος λειτουργός δεν απαιτείται διετία.'},
 private_doctor:{summary:'Ιατρός εκτός Δημοσίου: απαιτείται τεκμηρίωση ιδιωτικού τομέα/ελεύθερου επαγγέλματος.',detail:'Βεβαίωση ασφαλιστικού φορέα, τόπος άσκησης επαγγέλματος και απαιτούμενη διετία, κατά την παρ. 6β άρθρου 16 π.δ. 50/1996.'},
 substitute_teacher:{summary:'Δεν αναγνωρίζονται μονάδες συνυπηρέτησης για σύζυγο αναπληρωτή εκπαιδευτικό.',detail:'Η εγκύκλιος 2026–2027 αποκλείει ρητά τη μοριοδότηση συνυπηρέτησης για σύζυγο αναπληρωτή (διευκρίνιση 6). Μην υπολογίζετε τα +4 μόρια μόνο από τη σύμβαση αναπληρωτή.',blocked:true},
 public_contract:{summary:'Σύμβαση Δημοσίου χωρίς οργανική θέση: δεν αναγνωρίζονται μονάδες συνυπηρέτησης.',detail:'Η εγκύκλιος αποκλείει ρητά εργαζομένους του δημόσιου τομέα χωρίς οργανική θέση, με σύμβαση οποιουδήποτε είδους (διευκρίνιση 10). Δεν αρκεί βεβαίωση συμβασιούχου.',blocked:true},
 other:{summary:'Άλλη περίπτωση: απαιτείται εξατομικευμένη επιβεβαίωση από τη ΔΔΕ.',detail:'Μην θεωρήσετε ότι η βεβαίωση εργασίας μόνη της τεκμηριώνει τη συνυπηρέτηση. Ισχύουν ειδικές ρυθμίσεις (π.χ. επιδοτούμενη ανεργία/κοινωφελής εργασία με ενεργή σύμβαση κατά την αίτηση), με την εγκύκλιο και την αρμόδια ΔΔΕ.'}
};
function coServiceInfo(value,mode){
 var info=COSERVICE_CASES[value]||COSERVICE_CASES.unspecified;
 if(info.localBlocked && mode==='local')return {summary:'Σύζυγος στη διάθεση ΠΥΣΠΕ/ΠΥΣΔΕ: δεν μοριοδοτείται για βελτίωση ή οριστική τοποθέτηση.',detail:info.detail,blocked:true};
 return {summary:info.summary,detail:info.detail,blocked:!!info.blocked};
}

function entry(id,title,detail,tag){return {id:id,title:title,detail:detail,tag:tag};}
function eaeDocuments(){
 var inp={origin:$('transferEaeOrigin').value,area:$('transferEaeArea').value,destination:$('transferEaeDestination').value,
  branch:$('transferEaeBranch').value,qualification:$('transferEaeQualification').value,
  qualifications:(function(){var controls=document.querySelectorAll('[data-eae-qualification]');
    return controls.length?Array.prototype.map.call(document.querySelectorAll('[data-eae-qualification]:checked'),function(el){return el.getAttribute('data-eae-qualification');}):[$('transferEaeQualification').value];})(),
  sensoryCertification:(optionalElement('transferEaeSensoryCertification')||{value:'unknown'}).value,
  teachingYears:$('transferEaeTeachingYears').value,newly:$('transferEaeNewly').checked,
  areaYears:$('transferEaeAreaYears').value,eaeYears:$('transferEaeExperienceYears').value};
 var k=global.EducationTransferEligibility, result=k?k.eae(inp):null;
 var chosen=result?result.selectedQualifications:inp.qualifications;
 // One document card per selected qualification; no second selection form inside the checklist.
 var rows=[];
 if(chosen.indexOf('doctorate')!==-1)rows.push(entry('eae-doctorate','Διδακτορικό — τεκμηρίωση συνάφειας','Αν δεν καλύπτεται από απόφαση διαπίστωσης συνάφειας, ελέγξτε την απαιτούμενη περίληψη διατριβής. Για τίτλο αλλοδαπής απαιτούνται μετάφραση/αναγνώριση σύμφωνα με την εγκύκλιο.','Δικαιολογητικά τίτλου'));
 if(chosen.indexOf('masters')!==-1)rows.push(entry('eae-masters','Μεταπτυχιακό — τεκμηρίωση συνάφειας','Ελέγξτε αν ο τίτλος περιλαμβάνεται σε σχετική ΥΑ συνάφειας· διαφορετικά απαιτείται τεκμηρίωση προγράμματος με τουλάχιστον 50% μαθημάτων ΕΑΕ και περίληψη εργασίας, αν εκπονήθηκε. Ξένοι τίτλοι: μετάφραση/αναγνώριση.','Δικαιολογητικά τίτλου'));
 if(chosen.indexOf('two_year')!==-1)rows.push(entry('eae-two-year','Πτυχίο διετούς μετεκπαίδευσης στην ΕΑΕ','Ελέγξτε τον τίτλο διετούς μετεκπαίδευσης των Διδασκαλείων ή τη νόμιμη αναγνώριση ισότιμου τίτλου αλλοδαπής.','Αποδεικτικό εξειδίκευσης'));
 if(chosen.indexOf('experience')!==-1)rows.push(entry('eae-5year','Πενταετής αποδεδειγμένη προϋπηρεσία ΕΑΕ','Ελέγξτε υπηρετήσεις αναλυτικά ανά φορέα, χρονικό διάστημα και δομή ΕΑΕ: ΣΜΕΑΕ, ΕΕΕΕΚ, Τμήμα Ένταξης, παράλληλη στήριξη, ΚΕΔΑΣΥ κ.λπ. Η ΔΔΕ/ΥΠΑΙΘΑ επιβεβαιώνει τη συνδρομή της προϋπόθεσης.','Βεβαιώσεις και μητρώο'));
 if(chosen.indexOf('pe11')!==-1)rows.push(entry('eae-pe11','ΠΕ11 — κύρια ειδικότητα ΤΕΦΑΑ','Ελέγξτε ότι το βασικό πτυχίο ΤΕΦΑΑ αποτυπώνει μία από τις κύριες ειδικότητες που απαριθμεί η εγκύκλιος. Δεν αρκεί μεμονωμένη επιμόρφωση.','Τίτλος και ειδικότητα'));
 if(inp.destination==='deaf')rows.push(entry('eae-sign','Σχολείο Κωφών — πιστοποίηση ΕΝΓ',(inp.sensoryCertification==='yes'?'Επαληθεύστε τον φορέα έκδοσης και την καταχώριση της δηλωμένης πιστοποίησης. ':'Δεν έχει επαληθευτεί η απαιτούμενη πιστοποίηση. ' )+'Απαιτείται πιστοποιημένη επάρκεια στην Ελληνική Νοηματική Γλώσσα από αποδεκτό φορέα κατά την εγκύκλιο· απλή παρακολούθηση δεν αρκεί.','Ειδικό αποδεικτικό'));
 if(inp.destination==='blind')rows.push(entry('eae-braille','Σχολείο Τυφλών — πιστοποίηση Braille',(inp.sensoryCertification==='yes'?'Επαληθεύστε τον φορέα έκδοσης και την καταχώριση της δηλωμένης πιστοποίησης. ':'Δεν έχει επαληθευτεί η απαιτούμενη πιστοποίηση. ' )+'Απαιτείται πιστοποιημένη γνώση γραφής Braille από αποδεκτό φορέα κατά την εγκύκλιο· απλή παρακολούθηση δεν αρκεί.','Ειδικό αποδεικτικό'));
 return rows;
}
function musicDocuments(){
 var type=$('transferEducationType').value, exception=$('transferMusicException').value,subject=$('transferMusicSubject').value;
 var m=global.EducationTransferEligibility?global.EducationTransferEligibility.music({educationType:type,organic:$('transferMusicOrganic').checked,teachingYears:$('transferMusicTeachingYears').value,exceptionType:exception,subject:subject}):null;
 var rows=[
 entry('music-common','Γενικός φάκελος (αφορά και τη Γενική παιδεία)','Αναλυτικό βιογραφικό, πίνακας δικαιολογητικών και διαθέσιμα πιστοποιητικά που τεκμηριώνουν επιμόρφωση, μετεκπαίδευση, διδακτική εμπειρία σε Μουσικά, μεταπτυχιακούς τίτλους και επιστημονικό έργο. Δεν απαιτείται μουσική εξειδίκευση για κλάδους Γενικής παιδείας. '+MUSIC_SOURCE,'Φάκελος κρίσης')
 ];
 if(type==='specialized'){
  rows.push(entry('music-specialist','Μουσική παιδεία — πρόσθετη εξειδίκευση','Ελέγξτε τα αποδεικτικά απόλυτης εξειδίκευσης για την ειδικότητα ΠΕ79.01/ΤΕ16. Τίτλοι μουσικών ιδρυμάτων, κατάλληλη αναγνώριση και τεκμηριωμένη ενασχόληση.','Μόνο μουσική παιδεία'));
  if(m&&m.subjectInfo)rows.push(entry('music-subject','Αντικείμενο: '+m.subjectInfo.name,m.subjectInfo.document,'Εξειδίκευση ανά μάθημα'));
  if(subject==='unspecified')rows.push(entry('music-subject-missing','Επιλογή συγκεκριμένου μουσικού αντικειμένου','Επιλέξτε Θεωρητικά, Παραδοσιακή Μουσική, Σύνολα, Όργανο ή Άλλο ώστε να εμφανιστούν τα ειδικά τεκμήρια.','Συμπληρώστε στοιχεία'));
  rows.push(entry('music-appointment','Περιορισμός διορισμού σε Μουσικά','Ελέγξτε την αρχική πράξη διορισμού: για ορισμένες παλαιές μουσικές κατηγορίες επιτρέπονται μεταθέσεις μόνο σε Μουσικά Σχολεία.','Έλεγχος κλάδου'));
 }
 // Eligibility findings are already presented above in the dedicated Musical School precheck.
 return rows;
}
function artDocuments(){
 var branchCode=$('transferArtBranch').value,orientation=$('transferArtOrientation').value;
 var registry=global.EducationArtisticVacancies;
 var legacyBranch=registry?({'ΠΕ08':'pe08','ΠΕ11':'pe11','ΠΕ79–ΤΕ16':'pe79','ΠΕ91.01':'pe9101','ΠΕ91.02':'pe9102'}[branchCode]||(branchCode?'general':'other')):branchCode;
 var input={branch:legacyBranch,orientation:orientation,organic:$('transferArtOrganic').checked,
   teachingYears:$('transferArtTeachingYears').value,exceptionType:$('transferArtException').value};
 if(registry){
  input.branchCode=branchCode;
  input.school=$('transferArtSchool').value;
  input.specialization=$('transferArtSpecializationRow').style.display==='none'?null:($('transferArtSpecialization').selectedIndex>0?$('transferArtSpecialization').value:null);
 }
 var engine=global.EducationTransferEligibility,result=engine?engine.art(input):null;
 var rows=[
  entry('art-cv','Αναλυτικό βιογραφικό','Προετοιμάστε πλήρες βιογραφικό που τεκμηριώνει επιμόρφωση, μετεκπαίδευση, διδακτική εμπειρία σε Καλλιτεχνικά, μεταπτυχιακές σπουδές, επιστημονικό έργο και συναφή προσόντα. '+ART_SOURCE,'Φάκελος κρίσης'),
  entry('art-inventory','Πίνακας δικαιολογητικών','Καταγράψτε σε πίνακα τους τίτλους, βεβαιώσεις και λοιπά αποδεικτικά που τεκμηριώνουν τα συνεκτιμώμενα προσόντα· μην περιλάβετε άσχετα έγγραφα.','Σύμφωνα με εγκύκλιο'),
  entry('art-degrees','Τίτλοι σπουδών, επιμόρφωση και διδακτική εμπειρία','Συγκεντρώστε μόνο τα σχετικά τεκμήρια. Για αλλοδαπούς τίτλους σπουδών ελέγξτε αναγνώριση ΔΟΑΤΑΠ/ΔΙΚΑΤΣΑ και, όπου απαιτείται, επίσημη μετάφραση. Η ΔΔΕ ελέγχει τα υπηρεσιακά στοιχεία.','Έλεγχος τίτλων'),
  entry('art-publications','Δημοσιεύσεις, βιβλία και επιστημονικό έργο','Εφόσον δηλώνονται δημοσιεύσεις ή βιβλία, η εγκύκλιος 2026–2027 δέχεται εξώφυλλο και πρώτη σελίδα ή σύνδεσμο της ηλεκτρονικής έκδοσης.','Εφόσον υπάρχουν')
 ];
 if(result&&result.specialized){
  rows.push(entry('art-committee','Ειδική κρίση για κλάδο καλλιτεχνικής παιδείας',
    'Κλάδος '+result.branchTitle+'. Προβλέπεται εισήγηση της Επιστημονικής Επιτροπής Καλλιτεχνικής Παιδείας. Οργανώστε τα συναφή με το πραγματικό αντικείμενο τίτλους, διδασκαλία και καλλιτεχνική/επιστημονική δραστηριότητα· δεν αποτελεί γενική απαίτηση «portfolio» για κάθε κλάδο.',
    'ΠΕ08/ΠΕ11/ΠΕ79.01/ΠΕ91.01/ΠΕ91.02'));
  if(orientation)rows.push(entry('art-orientation','Συναφή τεκμήρια κατεύθυνσης',
    'Δηλώθηκε '+$('transferArtOrientation').selectedOptions[0].textContent+'. Τεκμηριώστε μόνο τη συνάφεια τίτλων, διδακτικού ή καλλιτεχνικού έργου με την προκηρυγμένη ειδικότητα, σύμφωνα με όσα γίνονται δεκτά. Η κατεύθυνση από μόνη της δεν αποδεικνύει δικαίωμα.',
    'Εξατομικευμένη υπενθύμιση'));
 }
 return rows;
}
function interculturalDocuments(){
 var elig=global.EducationTransferEligibility, qs=elig&&elig.INTERCULTURAL_QUALIFICATIONS||[];
 var chosen=Array.prototype.map.call(document.querySelectorAll('[data-intercultural-qual]:checked'),function(el){return el.getAttribute('data-intercultural-qual');});
 var state=elig&&elig.intercultural({organic:$('transferInterculturalOrganic').value,current:$('transferInterculturalCurrent').value,
   years:$('transferInterculturalYears').value,vacancy:$('transferInterculturalVacancy').value,
   languageCase:$('transferInterculturalLanguageCase').value,qualifications:chosen});
 var rows=[
   entry('ic-cv','Αναλυτικό βιογραφικό σημείωμα','Πλήρες βιογραφικό, πίνακας δικαιολογητικών και πιστοποιητικά/τίτλοι που αποδεικνύουν τα πραγματικά δηλωμένα προσόντα. Αποφύγετε άσχετα έγγραφα.','Φάκελος προσόντων')
 ];
 chosen.forEach(function(id){
   var q=qs.find(function(x){return x.id===id;});
   if(q)rows.push(entry('ic-qual-'+id,'Τεκμηρίωση: '+q.title,q.proof,'Επιλεγμένο πρόσθετο προσόν'));
 });
 rows.push(entry('ic-foreign','Ξενόγλωσσοι τίτλοι και δημοσιεύσεις','Για τίτλους αλλοδαπής ελέγξτε αναγνώριση ΔΟΑΤΑΠ/ΔΙΚΑΤΣΑ και επίσημη μετάφραση όπου απαιτείται. Για δημοσιεύσεις/βιβλία αρκεί αντίγραφο εξωφύλλου και πρώτης σελίδας. Η εγκύκλιος εξαιρεί τίτλους γλωσσομάθειας συγκεκριμένων γλωσσών από την απαίτηση μετάφρασης.','Ανά περίπτωση'));
 return rows;
}
function build(){
 var mode=$('mode').value, qual=mode==='music'||mode==='art'||mode==='intercultural', eae=['smeae','eae_sensory','kedasy'].indexOf(mode)!==-1;
 var a=base.filter(function(x){return !qual || (mode!=='intercultural'||x[0]!=='record');}).map(function(x){return entry.apply(null,x);});
 if(!qual){
  // v3.22.118: the service-record checkbox also covers M.S.D.; never create a duplicate task.
  var record=a.find(function(x){return x.id==='record';});
  if(record){
   record.title='Υπηρεσιακό μητρώο, χρόνος υπηρεσίας και Μ.Σ.Δ.';
   // Recognition acts are checked here, never as an extra checkbox when service is entered.
   record.detail+='\nΕλέγξτε ότι τυχόν πράξεις αναγνώρισης υπηρεσίας και σχετικές βεβαιώσεις έχουν καταχωριστεί σωστά στο Μητρώο.';
   record.detail+='\nΜονάδες Συνθηκών Διαβίωσης (Μ.Σ.Δ.): ';
   record.detail+='\nΣυνολική υπηρεσία: ';
   var serviceMode=optionalElement('serviceMode')?optionalElement('serviceMode').value:'direct';
   if(serviceMode==='periods')record.detail+='Διασταυρώστε τις διακριτές, μη επικαλυπτόμενες περιόδους προϋπηρεσίας με το επίσημο Μητρώο.';
   else if(serviceMode==='points')record.detail+='Αντιγράψτε τα επίσημα μόρια συνολικής υπηρεσίας/προϋπηρεσίας από το Μητρώο, χωρίς να τα προσθέσετε ξανά από τις επιμέρους περιόδους.';
   else record.detail+='Ελέγξτε τον συνολικό χρόνο σε έτη, μήνες και ημέρες όπως εμφανίζεται στο επίσημο Μητρώο.';
   if($('msdMode').value==='direct'){
    record.detail+='Αντιγράψτε τον ακριβή συνολικό αριθμό μορίων Μ.Σ.Δ. από το επίσημο Μητρώο. Οι αναλυτικές υπηρετήσεις δεν προστίθενται δεύτερη φορά.';
   }else{
    record.detail+='Διασταυρώστε τις υπηρετήσεις ανά σχολείο και κατηγορία, τις αποσπάσεις, την παράλληλη υπηρεσία και τις προσαυξήσεις με το επίσημο Μητρώο.';
   }
  }
  if($('familyStatusEligible').checked || +$('eligibleChildren').value>0){
   a.push(entry('family','Πιστοποιητικό οικογενειακής κατάστασης',
    'Το πιστοποιητικό από ελληνικό δημοτολόγιο αναζητείται ΥΠΟΧΡΕΩΤΙΚΑ αυτεπαγγέλτως από την αρμόδια δημόσια υπηρεσία (ΔΔΕ). Δεν χρειάζεται να το εκδώσετε ή να το προσκομίσετε για την αίτηση μετάθεσης. Ελέγξτε την ορθότητα των οικογενειακών/δημοτολογικών στοιχείων σας και ενημερώστε τη ΔΔΕ για τυχόν ασυμφωνίες. Σε τεχνική αδυναμία άντλησης, η υπηρεσία οφείλει να το αναζητήσει με άλλον πρόσφορο τρόπο. Νομική βάση: άρθρα 32 και 50 ν. 5099/2024, ΚΥΑ 19173/2025 (Β΄ 1783), εγκύκλιος ΥΠΕΣ ΔΙΔΔΔΗ/Φ.16/οικ.2923/18-02-2026.',
    'Αυτεπάγγελτα από ΔΔΕ — δεν προσκομίζεται'));
   if(+$('eligibleChildren').value>0) a.push(entry('children-specific','Τέκνα — πότε απαιτούνται επιπλέον αποδεικτικά',
    'ΑΝΗΛΙΚΑ (γέννηση από 01/01/2009): για την ηλικία/συγγένεια αρκούν τα στοιχεία οικογενειακής κατάστασης που αναζητεί αυτεπάγγελτα η ΔΔΕ· δεν απαιτείται βεβαίωση σπουδών.\n'+
    'ΕΝΗΛΙΚΑ ΠΟΥ ΣΠΟΥΔΑΖΟΥΝ (γέννηση από 01/01/2002, μέχρι 25 ετών): απαιτείται πιστοποιητικό/βεβαίωση σπουδών της ανώτερης/ανώτατης δημόσιας σχολής εσωτερικού ή ισότιμης εξωτερικού, με στοιχεία φοίτησης ώστε να ελεγχθεί ότι δεν βρίσκεται στο τελευταίο εξάμηνο/έτος και δεν πρόκειται για δεύτερο πτυχίο ή μεταπτυχιακό. Αναστολή/διακοπή φοίτησης και ΣΑΕΚ/Κολλέγια δεν μοριοδοτούνται. Ξενόγλωσσοι τίτλοι: επίσημη ελληνική μετάφραση.\n'+
    'ΑΓΑΜΟΣ/ΔΙΑΖΕΥΓΜΕΝΟΣ ΓΟΝΕΑΣ: εφόσον συντρέχει περίπτωση μορίων οικογενειακής κατάστασης λόγω επιμέλειας, απαιτούνται τα ειδικά αποδεικτικά/δηλώσεις που εξειδικεύει η εγκύκλιος. Για διαζευγμένους: δημόσιο έγγραφο ανάθεσης επιμέλειας· για άγαμους γονείς διαφοροποιούνται τα αποδεικτικά σύμφωνα με τη διευκρίνιση 1.\n'+
    'ΑΝΑΠΗΡΙΑ ΤΕΚΝΟΥ: οι ιατρικές γνωματεύσεις αφορούν ενδεχόμενη ξεχωριστή ειδική κατηγορία/προτεραιότητα, όχι αυτομάτως τις συνήθεις μονάδες τέκνων.\n'+
    'ΠΟΛΥΤΕΚΝΙΑ (άρθρο 6 ν. 3454/2006): Για την απόκτηση της ιδιότητας συνυπολογίζονται άγαμα τέκνα που δεν έχουν συμπληρώσει τα 23 έτη· έως πριν από τα 25 έτη συνυπολογίζονται επίσης εφόσον σπουδάζουν σε αναγνωρισμένη τριτοβάθμια σχολή ή υπηρετούν τη στρατιωτική τους θητεία. Τέκνα με ισόβια αναπηρία 67% και άνω συνυπολογίζονται ανεξάρτητα από ηλικία και οικογενειακή κατάσταση.\n'+
    'Κατά κανόνα απαιτούνται τέσσερα τέκνα. Ο νόμος προβλέπει ειδικές περιπτώσεις με τρία τέκνα (γονέας χωρίς σύζυγο με την προβλεπόμενη επιμέλεια και αποκλειστική υποχρέωση διατροφής ή γονέας με ισόβια αναπηρία 67% και άνω). Η πολυτεκνική ιδιότητα, αφού αποκτηθεί νόμιμα, διατηρείται ισοβίως.\n'+
    'ΠΡΟΣΟΧΗ: Η ιδιότητα πολυτέκνου/ειδική κατηγορία δεν ταυτίζεται με τις μονάδες μετάθεσης λόγω τέκνων· τα τελευταία ελέγχονται με τους αυτοτελείς όρους της εγκυκλίου. Για απόδειξη σπουδών ή στρατιωτικής θητείας απαιτούνται κατά περίπτωση οι σχετικές βεβαιώσεις, σύμφωνα με τις οδηγίες αποστολής της εγκυκλίου.\n'+
    'ΝΕΟΓΕΝΝΗΤΟ: κατ’ εξαίρεση η ληξιαρχική πράξη γέννησης μπορεί να υποβληθεί έως το τέλος Δεκεμβρίου 2026· για γέννηση στις 31/12 προβλέπεται ειδική άμεση δήλωση στη ΔΔΕ και μεταγενέστερη προσκόμιση ληξιαρχικής πράξης.\n'+
    'Τα δικαιολογητικά οικογενειακών λόγων εκδίδονται κατά κανόνα στο τελευταίο τρίμηνο. Το πιστοποιητικό οικογενειακής κατάστασης ελληνικού δημοτολογίου αναζητείται αυτεπάγγελτα. Για έγγραφα αλλοδαπών αρχών ισχύουν οι ειδικές οδηγίες της εγκυκλίου. '+FAMILY_SOURCE,
    ''));
  }
  var caseSelect=$('coService').checked ? $('transferCoServiceCase') : (document.querySelector?document.querySelector('#transferCoServiceCase'):null);
  var caseValue=caseSelect?caseSelect.value:'unspecified';
  var areaMode=eae && $('transferEaeArea').value==='same'?'local':mode;
  var info=coServiceInfo(caseValue,areaMode);
  // Show the documentation for a blocked status even though the scoring box is unchecked/disabled.
  if($('coService').checked || (caseValue!=='unspecified' && info.blocked)){
   var spouseDoc=entry('coservice','Δικαιολογητικά συνυπηρέτησης',
    info.summary+'\n'+info.detail+'\nΤα απαιτούμενα αποδεικτικά συνυπηρέτησης πρέπει να έχουν εκδοθεί στο τελευταίο τρίμηνο (με εξαίρεση βεβαιώσεις εργοδοτών για προγενέστερη εργασία που έπαυσε: τελευταία διετία). Η συνυπηρέτηση αφορά μία περιοχή μετάθεσης — όχι ολόκληρο τον νομό — ή τον δήμο σχολείου για βελτίωση/οριστική τοποθέτηση. '+FAMILY_SOURCE,
    info.blocked?'Δεν θεμελιώνεται συνυπηρέτηση (+0)':(caseValue==='unspecified'||caseValue==='other'?'Υπό επιβεβαίωση — δεν προστίθενται +4':''));
   spouseDoc.disabled=info.blocked;
   a.push(spouseDoc);
  }
  if($('locality').checked) a.push(entry('locality','Εντοπιότητα','Ελέγξτε βεβαίωση δημοτικότητας, χρόνο κτήσης της και αντιστοίχιση με τη ζητούμενη περιοχή ή τον δήμο.','Πιθανό δικαιολογητικό'));
  // No separate MSD row: the record item above includes this check for either entry mode.
 }
 // The EAE-to-General eligibility verdict is already displayed above the checklist.
 if(eae) a=a.concat(eaeDocuments());
 if(mode==='music') a=a.concat(musicDocuments());
 if(mode==='art') a=a.concat(artDocuments());
 if(mode==='intercultural') a=a.concat(interculturalDocuments());
 if(!qual && optionalElement('transferSpecialCase') && optionalElement('transferSpecialCase').value!=='none'){
  var specialCase=optionalElement('transferSpecialCase').value;
  if(specialCase==='health_committee')a.push(entry('health-committee','Ειδική κατηγορία υγείας — γνωμάτευση Υγειονομικής Επιτροπής',
   'Για γνωμάτευση Β/θμιας Υγειονομικής Επιτροπής ή ΑΣΥΕ/ΑΝΥΕ/ΑΑΥΕ (αφορά υγεία ιδίου, συζύγου ή τέκνου, όχι γονέων/αδελφών, όπου συντρέχουν οι όροι): αποστολή σε ΚΛΕΙΣΤΟ φάκελο με ένδειξη «εμπιστευτικό πρωτόκολλο» στο ΥΠΑΙΘΑ, Ανδρέα Παπανδρέου 37, 15180 Μαρούσι. Συνυποβάλλεται απλή αίτηση με ονοματεπώνυμο, Α.Μ. και αριθμό πρωτοκόλλου αίτησης μετάθεσης. Δεν αντικαθίσταται από απλό email/mydocs. Η βεβαίωση και το δικαίωμα κρίνονται υπηρεσιακά. Εγκύκλιος 138014/Ε2, Μέρος Πρώτο, Κεφ. Α, §2.2.2.',
   'Κλειστός φυσικός φάκελος — ειδική διαδρομή'));
  if(specialCase==='health_kepa')a.push(entry('health-kepa','Ειδική κατηγορία υγείας — στοιχεία ΚΕΠΑ',
   'Στην ηλεκτρονική αίτηση επιλέξτε «Ειδική κατηγορία» και καταχωρίστε «Αριθμ. Επιτροπής» και «Αριθμ. Μητρώου ΚΕΠΑ» χωρίς καθέτους, παύλες, κενά ή ημερομηνία. Η γνωμάτευση ΚΕΠΑ ΔΕΝ αποστέλλεται. Αν δεν έχει εκδοθεί εμπρόθεσμα, ισχύουν ειδικές οδηγίες υπεύθυνης δήλωσης και ενημέρωσης πριν από την εξέταση από ΚΥΣΔΕ. Δεν προκύπτει αυτόματη αποδοχή προτεραιότητας. Εγκύκλιος 138014/Ε2, Μέρος Πρώτο, Κεφ. Α, §2.2.2, Μέρος Τρίτο, κεφ. Γ.',
   'Ηλεκτρονικά στοιχεία — όχι αποστολή γνωμάτευσης'));
  if(specialCase==='judicial_spouse')a.push(entry('judicial-spouse','Σύζυγος δικαστικού λειτουργού — ειδικός φάκελος',
   'Για κατά προτεραιότητα μετάθεση συζύγου δικαστικού λειτουργού: υποβάλλονται στο mydocs.minedu.gov.gr πιστοποιητικό οικογενειακής κατάστασης, βεβαίωση εργασίας και έγγραφο από το οποίο προκύπτει η περιφέρεια αρμοδιότητας του δικαστηρίου του/της συζύγου. Διαδικασία διακριτή από τα συνήθη δικαιολογητικά μοριοδότησης. Η υπηρεσία ελέγχει τις προϋποθέσεις. Εγκύκλιος 138014/Ε2, Μέρος Πρώτο, Κεφ. Α, §2.2.3.',
   'mydocs — ειδική διαδρομή'));
 }
 return a;
}
function escape(s){return String(s).replace(/[&<>"']/g,function(c){return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c];});}
/* v3.22.146 — lightweight, explicit submission channel per checklist entry.
 * Section 2.2 of circular 138014/E2/15-10-2026: keep scoring evidence,
 * qualification portfolios, official records and exceptional medical routes distinct.
 * These labels describe the channel, not an unconditional duty to re-submit.
 */
function submissionChannel(d){
 var id=d.id;
 if(id==='family')return {kind:'automatic',text:'Αυτεπάγγελτα από ΔΔΕ — δεν το προσκομίζετε'};
 if(id==='record')return {kind:'check',text:'Έλεγχος στο επίσημο μητρώο — χωρίς αποστολή'};
 if(id==='music-appointment'||id==='music-subject-missing'){
  return {kind:'check',text:'Έλεγχος στοιχείων / θεμελίωσης — όχι αυτοτελής αποστολή'};
 }
 if(id==='health-committee')return {kind:'special',text:'Κλειστός φυσικός φάκελος στο ΥΠΑΙΘΑ — όχι email/mydocs'};
 if(id==='health-kepa')return {kind:'special',text:'Καταχώριση στοιχείων στην αίτηση — δεν αποστέλλεται η γνωμάτευση ΚΕΠΑ'};
 if(id==='judicial-spouse')return {kind:'mydocs',text:'Υποβολή μέσω mydocs — ειδική διαδικασία'};
 if(id==='coservice' && d.disabled)return {kind:'check',text:'Δεν θεμελιώνεται μοριοδότηση — χωρίς αποστολή για το κριτήριο'};
 if(id==='coservice'||id==='locality'||id==='children-specific'){
  return {kind:'email',text:'Αποστολή με email στη ΔΔΕ οργανικής — όπου απαιτούνται αποδεικτικά'};
 }
 if(id==='eae-5year')return {kind:'check',text:'Διασταύρωση υπηρεσίας από ΔΔΕ · mydocs μόνο για τυχόν πρόσθετα αποδεικτικά'};
 if(/^eae-/.test(id)||/^music-/.test(id)||/^art-/.test(id)||/^ic-/.test(id)){
  return {kind:'mydocs',text:'Υποβολή μέσω mydocs — μόνο αν απαιτείται νέος/συμπληρωματικός φάκελος'};
 }
 return {kind:'check',text:'Ελέγξτε τον τρόπο υποβολής στις πληροφορίες'};
}

function portalLink(id){return id==='record'?' <a class="edu-transfer-doc__portal" href="https://teachers.minedu.gov.gr/" target="_blank" rel="noopener noreferrer">Άνοιγμα teachers.minedu.gov.gr ↗</a>':'';}

/* v3.22.121: merged record recognition checks, compact EAE qualification documentation; details remain printable. */
var openRows={};
var SUMMARIES={
 record:'Ελέγξτε υπηρετήσεις και μόρια. Πίνακες Δ.Ε.: 23/11/2026 · Διορθώσεις: 23–27/11/2026.',
 'children-specific':'Ανήλικα: χωρίς βεβαίωση σπουδών. Ενήλικα που σπουδάζουν: βεβαίωση φοίτησης όπου απαιτείται. Νεογέννητα: ειδικές οδηγίες έως 31/12/2026.',
 'ic-submit':'Αν απαιτείται νέος φάκελος, η αποστολή γίνεται αποκλειστικά μέσω mydocs.minedu.gov.gr, σε μία αποστολή.'
};
function legalSeparation(detail){
 var body=String(detail),source='';
 [FAMILY_SOURCE,EAE_SOURCE,MUSIC_SOURCE,ART_SOURCE,IC_SOURCE].forEach(function(ref){
  if(body.slice(-ref.length)===ref){body=body.slice(0,-ref.length).trim();source=ref;}
 });
 if(!source){
  var at=body.lastIndexOf(' Νομική βάση:');
  if(at!==-1){source=body.slice(at+1);body=body.slice(0,at).trim();}
 }
 return {body:body,source:source};
}
function shortText(d){
 if(d.id==='coservice')return d.detail.split('\n')[0];
 if(SUMMARIES[d.id])return SUMMARIES[d.id];
 var text=legalSeparation(d.detail).body.split('\n')[0].trim();
 if(text.length<=145)return text;
 var cut=text.lastIndexOf(' ',145);
 return text.slice(0,cut>80?cut:145).trim()+'…';
}
function isCritical(d){
 return (d.id==='coservice' && (d.tag.indexOf('Δεν θεμελιώνεται')!==-1 || d.tag.indexOf('Υπό επιβεβαίωση')!==-1));
}
function documentTaskType(doc,channel){
 if(doc.disabled || channel.kind==='automatic')return 'info';
 if(channel.kind==='check')return 'verify';
 // Email/mydocs/special routes require some preparation or application input.
 return 'action';
}
function progress(root){
 var rows=root.querySelectorAll('[data-doc-id]');
 var complete=0,active=0;
 rows.forEach(function(input){if(input.disabled)return;active++;if(input.checked)complete++;});
 var target=$('transferDocumentProgress');
 if(target)target.textContent=active?'Δικαιολογητικά/ενέργειες που ετοιμάστηκαν: '+complete+' από '+active+'.':'Δεν προκύπτουν δικαιολογητικά προς προετοιμασία από τις επιλογές σας.';
}
/* v3.22.123 — one disclosure, not a checklist task, for all submission routes. */
var SUBMISSION_GUIDANCE={
 region:{
  title:'Γενική μετάθεση από περιοχή σε περιοχή',
  lines:[
   'Τα απαιτούμενα δικαιολογητικά μοριοδότησης (όπως συνυπηρέτηση) αποστέλλονται ψηφιοποιημένα με email στη ΔΔΕ οργανικής έως 02/11/2026.',
   'Κατά κανόνα έχουν εκδοθεί το τελευταίο τρίμηνο. Για τυχόν ειδική υποβολή προς κεντρική υπηρεσία, χρησιμοποιείται το mydocs.minedu.gov.gr μόνο όταν προβλέπεται από την εγκύκλιο.'
  ]
 },
 local:{
  title:'Οριστική τοποθέτηση / βελτίωση',
  lines:[
   'Τα τυχόν απαιτούμενα δικαιολογητικά μοριοδότησης αποστέλλονται ψηφιοποιημένα με email στη ΔΔΕ οργανικής έως 02/11/2026, σύμφωνα με τις οδηγίες της αρμόδιας ΔΔΕ.',
   'Ελέγξτε την έκδοση των δικαιολογητικών εντός του προβλεπόμενου διαστήματος. Το mydocs.minedu.gov.gr αφορά μόνο περιπτώσεις για τις οποίες προβλέπεται υποβολή στην κεντρική υπηρεσία.'
  ]
 },
 smeae:{
  title:'Μετάθεση σε ΣΜΕΑΕ — δύο διαδρομές',
  lines:[
   'Δικαιολογητικά μοριοδότησης (π.χ. οικογενειακοί λόγοι, συνυπηρέτηση): στη ΔΔΕ οργανικής, με τις προθεσμίες της εγκυκλίου.',
   'Δικαιολογητικά προσόντων/κριτηρίων ΕΑΕ και βιογραφικό: μέσω mydocs.minedu.gov.gr, όταν απαιτείται νέος ή συμπληρωματικός φάκελος.',
   'Προηγούμενος ηλεκτρονικός φάκελος μπορεί να απαλλάσσει από εκ νέου αποστολή των ίδιων στοιχείων· η νέα ηλεκτρονική αίτηση μετάθεσης υποβάλλεται κάθε χρόνο.'
  ]
 },
 eae_sensory:{
  title:'Σχολεία Κωφών/Τυφλών — δύο διαδρομές',
  lines:[
   'Δικαιολογητικά μοριοδότησης στη ΔΔΕ οργανικής, σύμφωνα με την εγκύκλιο.',
   'Προσόντα ΕΑΕ και αποδεικτικό πιστοποιημένης ΕΝΓ ή Braille, ανάλογα με το σχολείο: μέσω mydocs.minedu.gov.gr όταν απαιτείται νέος/συμπληρωματικός φάκελος.',
   'Η αίτηση με κωδικό 001.ΔΕΚΤ υποβάλλεται χωριστά στο teachers.minedu.gov.gr. Ελέγξτε προηγούμενο ηλεκτρονικό φάκελο.'
  ]
 },
 kedasy:{
  title:'Μετάθεση σε ΚΕΔΑΣΥ — δύο διαδρομές',
  lines:[
   'Δικαιολογητικά μοριοδότησης στη ΔΔΕ οργανικής, κατά την εγκύκλιο.',
   'Προσόντα ΕΑΕ και στοιχεία κλάδου ΠΕ02/ΠΕ03/ΠΕ04: μέσω mydocs.minedu.gov.gr όταν απαιτείται νέος/συμπληρωματικός φάκελος.',
   'Η αίτηση 001.ΔΕΣΚΕ.ΚΔΣ υποβάλλεται χωριστά στο teachers.minedu.gov.gr. Η οργανική θέση και ο κλάδος επιβεβαιώνονται από τη ΔΔΕ.'
  ]
 },
 music:{
  title:'Μετάθεση σε Μουσικό Σχολείο',
  lines:[
   'Αν απαιτείται νέος ή συμπληρωματικός φάκελος προσόντων/βιογραφικού, υποβάλετέ τον μέσω mydocs.minedu.gov.gr σε μία αποστολή. Ελέγξτε πρώτα την ισχύ προηγούμενου ηλεκτρονικού φακέλου.',
   'Για τυχόν νέα δικαιολογητικά μοριοδότησης ακολουθούνται οι οδηγίες της ΔΔΕ οργανικής.'
  ]
 },
 art:{
  title:'Μετάθεση σε Καλλιτεχνικό Σχολείο',
  lines:[
   'Νέος ή συμπληρωματικός φάκελος βιογραφικού/προσόντων: μέσω mydocs.minedu.gov.gr σε μία αποστολή, με PDF έως 50 MB, ασπρόμαυρα 200–300 dpi, έως 02/11/2026.',
   'Ελέγξτε πρώτα τον προηγούμενο φάκελο· η υποβολή έντυπων ή εκπρόθεσμων δικαιολογητικών δεν υποκαθιστά την προβλεπόμενη διαδικασία.'
  ]
 },
 intercultural:{
  title:'Μετάθεση σε Πειραματικό Διαπολιτισμικής Εκπαίδευσης',
  lines:[
   'Εφόσον απαιτείται νέος φάκελος, βιογραφικό και αποδεικτικά προσόντων αποστέλλονται αποκλειστικά μέσω mydocs.minedu.gov.gr, σε μία αποστολή PDF (ασπρόμαυρα 200–300 dpi) και εντός προθεσμίας.',
   'Ελέγξτε αν ο παλαιός ηλεκτρονικός φάκελος καλύπτει τα στοιχεία που δηλώνετε. Δεν αντικαθίσταται η προβλεπόμενη διαδρομή με έντυπα ή email.'
  ]
 }
};
function submissionGuideData(){
 var mode=$('mode')?$('mode').value:'region';
 return SUBMISSION_GUIDANCE[mode]||SUBMISSION_GUIDANCE.region;
}
/* 3.22.151 — prominent, route-aware entry to the existing message composer.
 * Only genuinely actionable documents determine the visible email/mydocs callout.
 * An official check or exceptional physical route must never be called email.
 */
function submissionUiText(key,defaultText){return global.EducationTransferEmailComponent&&global.EducationTransferEmailComponent.label(key)||defaultText;}
function submissionFeatureState(rows,mode){
 mode=mode||($('mode')?$('mode').value:'region');
 rows=rows||build();
 var channels=rows.filter(function(d){return documentTaskType(d,submissionChannel(d))==='action';})
  .map(function(d){return submissionChannel(d).kind;});
 var email=channels.indexOf('email')!==-1, mydocs=channels.indexOf('mydocs')!==-1;
 var special=channels.indexOf('special')!==-1;
 if(special&&!email&&!mydocs)return {
  title:'Ειδικές οδηγίες υποβολής δικαιολογητικών',
  lead:'Η επιλογή σας απαιτεί ειδική διαδικασία. Ελέγξτε πού υποβάλλονται τα στοιχεία πριν χρησιμοποιήσετε οποιοδήποτε υπόδειγμα.',
  button:'Προβολή ειδικών οδηγιών υποβολής',email:false,specialOnly:true};
 if(email&&mydocs)return {
  title:submissionUiText('mixedTitle','Προετοιμασία email και υποβολής μέσω mydocs'),
  lead:'Για τη μοριοδότηση ετοιμάζετε email στη ΔΔΕ οργανικής· ο φάκελος προσόντων υποβάλλεται χωριστά στο mydocs μόνο όπου απαιτείται.',
  button:submissionUiText('mixedButton','Προετοιμασία μηνύματος υποβολής'),email:true};
 if(email)return {
  title:submissionUiText('emailTitle','Προετοιμασία μηνύματος υποβολής δικαιολογητικών'),
  lead:'Δημιουργήστε θέμα και σώμα email για τα αποδεικτικά που πρέπει να αποστείλετε στη ΔΔΕ οργανικής.',
  button:submissionUiText('emailButton','Δημιουργία email υποβολής'),email:true};
 if(mydocs)return {
  title:submissionUiText('mydocsTitle','Έτοιμο υπόδειγμα υποβολής μέσω mydocs'),
  lead:'Η συγκεκριμένη διαδρομή αφορά mydocs, μόνο εφόσον απαιτείται νέος ή συμπληρωματικός φάκελος· δεν αντικαθίσταται με email.',
  button:submissionUiText('mydocsButton','Προετοιμασία κειμένου για mydocs'),email:false};
 return {
  title:submissionUiText('optionalTitle','Υπόδειγμα μηνύματος — αν χρειαστεί'),
  lead:'Δεν προκύπτει αυτή τη στιγμή δικαιολογητικό προς αποστολή από τις επιλογές σας. Το υπόδειγμα παραμένει διαθέσιμο για ενδεχόμενη μεταβολή.',
  button:submissionUiText('optionalButton','Προβολή προαιρετικού υποδείγματος'),email:false};
}
function updateSubmissionFeature(state){
 var card=document.querySelector?document.querySelector('#transferEmailFeature'):null;
 if(!card)return;
 var title=card.querySelector('#transferEmailFeatureTitle'),lead=card.querySelector('#transferEmailFeatureLead'),button=card.querySelector('#transferEmailFeatureOpen');
 if(title)title.textContent=state.title;
 if(lead)lead.textContent=state.lead;
 if(button)button.textContent=state.button;
 card.classList.toggle('is-optional',!state.email&&state.title.indexOf('αν χρειαστεί')!==-1);
}
function submissionMailto(tpl){
 // No guessed recipient and no automatic sending or attachments.
 return global.EducationTransferEmailComponent?global.EducationTransferEmailComponent.mailto(tpl):'mailto:?subject='+encodeURIComponent(tpl.subject)+'&body='+encodeURIComponent(tpl.body);
}
/* v3.22.131 — optional, entirely client-side personalization of the official
   message templates (circular §2.2.1 / §2.2.4 / §2.2.5). All values remain
   only in JS memory until reset/navigation: no cookies, storage or requests. */
var SUBMISSION_MESSAGE_TYPES={
 region:'Μετάθεση από περιοχή σε περιοχή',
 local:'Οριστική τοποθέτηση / βελτίωση',
 smeae:'μετάθεση σε ΣΜΕΑΕ / ΕΕΕΕΚ',
 eae_sensory:'μετάθεση σε Σχολεία Κωφών / Τυφλών',
 kedasy:'μετάθεση σε ΚΕΔΑΣΥ',
 music:'μετάθεση σε Μουσικό Σχολείο',
 art:'μετάθεση σε Καλλιτεχνικό Σχολείο',
 intercultural:'μετάθεση σε Σχολείο Διαπολιτισμικής Εκπαίδευσης'
};
var submissionPersonal={name:'',branch:'',protocol:'',registry:'',home:''};
function submissionText(p,key,fallback){
 var value=String(p&&p[key]||'').trim();
 return value||fallback;
}
function submissionMessageTemplate(mode,personal){
 personal=personal||submissionPersonal;
 if(personal.registry)personal=Object.assign({},personal,{registry:String(personal.registry).slice(0,15)});
 mode=mode||($('mode')?$('mode').value:'region');
 var typ=SUBMISSION_MESSAGE_TYPES[mode]||SUBMISSION_MESSAGE_TYPES.region;
 var general=(mode==='region'||mode==='local');
 var protocol=submissionText(personal,'protocol','[Αριθμός πρωτοκόλλου οριστικοποιημένης αίτησης]');
 var registry=submissionText(personal,'registry','[Αριθμός μητρώου]');
 var labels=global.EducationTransferTeacherSpecialtyLabels||{};
 var candidate=submissionText(personal,'branch','');
 var code=global.EducationSpecialtyCodes&&global.EducationSpecialtyCodes.normalize ? global.EducationSpecialtyCodes.normalize(candidate) : candidate;
 var branch=code&&(Object.prototype.hasOwnProperty.call(labels,code)||(!Object.keys(labels).length&&/^(ΠΕ|ΤΕ|ΔΕ)\d{2}(?:\.\d{2})?$/.test(code)))?code:'[Κλάδος/ειδικότητα]';
 var name=submissionText(personal,'name','[Ονοματεπώνυμο]');
 var home=submissionText(personal,'home','[ΔΔΕ οργανικής]');
 return {
  mode:mode, type:typ, general:general,
  destination:general?'Email στη ΔΔΕ οργανικής':'Στη ΔΔΕ οργανικής για μοριοδότηση και στο mydocs.minedu.gov.gr όπου προβλέπεται για προσόντα/κριτήρια',
  circular:general?'2.2.1':(mode==='music'||mode==='art'||mode==='intercultural'?'2.2.5':'2.2.4'),
  subject:general?[typ,protocol,registry].join(', '):['ΔΕ',typ,protocol,registry,branch].join(', '),
  body:general?[name,branch,typ,protocol,registry].join(', '):[name,branch,typ,protocol,registry,home].join(', ')
 };
}
function personalField(key,label,placeholder,wide){
 var max=key==='protocol'?150:key==='registry'?15:120;
 if(key==='branch'){
  var labels=global.EducationTransferTeacherSpecialtyLabels||{};
  var shared=global.EducationTransferEmailComponent;
  var options=shared&&shared.specialtyOptions?shared.specialtyOptions(labels,submissionPersonal.branch):
   Object.keys(labels).sort(function(a,b){return a.localeCompare(b,'el');}).map(function(code){
    return '<option value="'+escape(code)+'"'+(submissionPersonal.branch===code?' selected':'')+'>'+escape(code+' — '+labels[code])+'</option>';
   }).join('');
  return '<label class="transfer-submission-template__field'+(wide?' is-wide':'')+'" for="transferPersonal-branch">'+
   '<span>'+escape(label)+'</span><select id="transferPersonal-branch" name="transfer-personal-branch" data-transfer-personal-field="branch">'+
   '<option value="">— Επιλέξτε ειδικότητα —</option>'+options+'</select></label>';
 }
 return '<label class="transfer-submission-template__field'+(wide?' is-wide':'')+'" for="transferPersonal-'+key+'">'+
  '<span>'+escape(label)+'</span><input type="text" id="transferPersonal-'+key+'" data-transfer-personal-field="'+key+'" name="transfer-personal-'+key+'" '+
  'value="'+escape(submissionPersonal[key])+'" placeholder="'+escape(placeholder)+'" maxlength="'+max+'" autocomplete="off" spellcheck="false"></label>';
}
function submissionMessageHtml(data,expanded,allowEmail){
 var tpl=submissionMessageTemplate(data.mode,submissionPersonal);
 return '<section class="transfer-submission-template" aria-label="Υπόδειγμα θέματος και σώματος αποστολής">'+
  '<h4>'+escape(submissionUiText('templateHeading','Θέμα και σώμα μηνύματος — υπόδειγμα'))+'</h4>'+
  '<p class="transfer-submission-template__note">Το υπόδειγμα ακολουθεί τον επιλεγμένο τύπο αίτησης. '+
  (tpl.general?'Χρησιμοποιείται για email στη ΔΔΕ οργανικής.':'Για τα σχετικά δικαιολογητικά της ΔΔΕ ή την υποβολή στο mydocs, <strong>μόνο όπου προβλέπεται</strong>. Στο mydocs το σώμα μπορεί να εμφανίζεται ως πεδίο «Αιτούμαι».')+
  (['smeae','eae_sensory','kedasy'].indexOf(tpl.mode)!==-1?' Η §2.2.4 ορίζει τη σειρά των πεδίων και δίνει ενδεικτικό τύπο αίτησης. Ελέγξτε ότι η αναλυτική περιγραφή του τύπου που συμπληρώνεται εδώ συμφωνεί με την οριστικοποιημένη αίτησή σας.':'')+'</p>'+
  '<details class="transfer-submission-template__personalize" data-transfer-personalize'+(expanded?' open':'')+'>'+
  '<summary>'+escape(submissionUiText('personalize','Δημιουργία έτοιμου μηνύματος (προαιρετικά)'))+'</summary>'+
  '<div class="transfer-submission-template__personal-body">'+
  '<p class="transfer-submission-template__private-note">Συμπληρώστε όσα στοιχεία θέλετε από την <strong>οριστικοποιημένη</strong> αίτηση. Τα κενά παραμένουν ως υποδείξεις σε αγκύλες. Τα στοιχεία χρησιμοποιούνται μόνο σε αυτή τη σελίδα, χωρίς αποστολή ή μόνιμη αποθήκευση.</p>'+
  '<div class="transfer-submission-template__fields">'+
  personalField('name','Ονοματεπώνυμο','π.χ. Παπαδόπουλος Νικόλαος',true)+
  personalField('branch','Κλάδος / ειδικότητα','π.χ. ΠΕ03',false)+
  personalField('registry','Αριθμός μητρώου','Α.Μ. εκπαιδευτικού',false)+
  personalField('protocol','Αριθμός πρωτοκόλλου οριστικοποιημένης αίτησης',''+escape(submissionUiText('copy','Αντιγραφή'))+' από teachers.minedu.gov.gr',true)+
  (tpl.general?'':personalField('home','ΔΔΕ οργανικής','π.χ. ΔΔΕ Κέρκυρας',true))+
  '</div><button type="button" class="secondary transfer-submission-template__clear" data-transfer-personal-clear>'+escape(submissionUiText('clear','Καθαρισμός πεδίων'))+'</button></div></details>'+
  '<div class="transfer-submission-template__item"><div class="transfer-submission-template__bar"><strong>'+escape(submissionUiText('subject','Θέμα:'))+'</strong><button type="button" class="secondary" data-transfer-copy-template="subject" aria-label="'+escape(submissionUiText('copy','Αντιγραφή'))+' θέματος">'+escape(submissionUiText('copy','Αντιγραφή'))+'</button></div><p class="transfer-submission-template__value" data-transfer-template-value="subject">'+escape(tpl.subject)+'</p></div>'+
  '<div class="transfer-submission-template__item"><div class="transfer-submission-template__bar"><strong>'+escape(submissionUiText('body','Σώμα κειμένου:'))+'</strong><button type="button" class="secondary" data-transfer-copy-template="body" aria-label="'+escape(submissionUiText('copy','Αντιγραφή'))+' σώματος κειμένου">'+escape(submissionUiText('copy','Αντιγραφή'))+'</button></div><p class="transfer-submission-template__value" data-transfer-template-value="body">'+escape(tpl.body)+'</p></div>'+
  (allowEmail?'<p class="transfer-submission-template__email-action"><a class="transfer-email-feature__mail-link" data-transfer-mailto href="'+escape(submissionMailto(tpl))+'">'+escape(submissionUiText('openMail','Άνοιγμα στο email μου ↗'))+'</a> <span>'+escape(submissionUiText('recipientHint','Ο παραλήπτης δεν συμπληρώνεται αυτόματα· προσθέστε εσείς τα συνημμένα. Δεν πραγματοποιείται αποστολή.'))+'</span></p>':'')+
  '<p class="transfer-submission-template__source">Πηγή: εγκύκλιος μεταθέσεων Δ.Ε. 138014/Ε2/15-10-2026, §'+escape(tpl.circular)+'. Το υπόδειγμα αφορά ηλεκτρονική υποβολή όπου προβλέπεται· ειδικά δικαιολογητικά (π.χ. ορισμένες υγειονομικές γνωματεύσεις) μπορεί να έχουν διαφορετική διαδικασία.</p>'+
  '<p class="transfer-submission-template__feedback" id="transferSubmissionCopyFeedback" role="status" aria-live="polite"></p></section>';
}
function refreshSubmissionTemplate(){
 var root=$('transferSubmissionGuidanceBody');
 if(!root)return;
 var tpl=submissionMessageTemplate(null,submissionPersonal);
 ['subject','body'].forEach(function(key){
  var el=root.querySelector('[data-transfer-template-value="'+key+'"]');
  if(el)el.textContent=tpl[key];
 });
 var mailLink=root.querySelector('[data-transfer-mailto]');
 if(mailLink)mailLink.setAttribute('href',submissionMailto(tpl));
 var feedback=root.querySelector('#transferSubmissionCopyFeedback');
 if(feedback)feedback.textContent='';
}
function clearSubmissionPersonal(){
 Object.keys(submissionPersonal).forEach(function(key){submissionPersonal[key]='';});
 var root=$('transferSubmissionGuidanceBody');
 if(root)root.querySelectorAll('[data-transfer-personal-field]').forEach(function(el){el.value='';});
 refreshSubmissionTemplate();
}
function onSubmissionPersonalInput(e){
 var el=e.target;
 if(!el||!el.getAttribute)return false;
 var key=el.getAttribute('data-transfer-personal-field');
 if(!Object.prototype.hasOwnProperty.call(submissionPersonal,key))return false;
 var entered=el.value;
 if(key==='registry'&&entered.length>15){entered=entered.slice(0,15);el.value=entered;}
 if(key==='branch'){
  var common=global.EducationSpecialtyCodes;
  var normalized=common&&common.normalize?common.normalize(entered):entered;
  var labels=global.EducationTransferTeacherSpecialtyLabels||{};
  entered=Object.prototype.hasOwnProperty.call(labels,normalized)?normalized:'';
 }
 submissionPersonal[key]=entered;
 refreshSubmissionTemplate();
 return true;
}
function legacyCopySubmission(text){
 // Fallback for browsers without the secure Clipboard API.
 var input=document.createElement('textarea');
 input.value=text;
 input.setAttribute('readonly','');
 input.style.position='fixed';
 input.style.left='-9999px';
 document.body.appendChild(input);
 input.select();
 var ok=false;
 try { ok=!!document.execCommand('copy'); } catch(err) { ok=false; }
 document.body.removeChild(input);
 return ok;
}
function copySubmissionTemplate(event){
 var clear=event.target.closest&&event.target.closest('[data-transfer-personal-clear]');
 if(clear){clearSubmissionPersonal();return;}
 if(event.target.id==='resetBtn'||(event.target.closest&&event.target.closest('#resetBtn'))){clearSubmissionPersonal();return;}
 var button=event.target.closest&&event.target.closest('[data-transfer-copy-template]');
 if(!button)return;
 var container=button.closest('.transfer-submission-template');
 if(!container)return;
 var key=button.getAttribute('data-transfer-copy-template');
 var value=container.querySelector('[data-transfer-template-value="'+key+'"]');
 var feedback=container.querySelector('#transferSubmissionCopyFeedback');
 if(!value)return;
 var copied=value.textContent;
 if(global.EducationTransferEmailComponent){
  global.EducationTransferEmailComponent.copy(copied,{
    nativeSuccess:submissionUiText('copySuccess','Το κείμενο αντιγράφηκε.'),
    nativeBrackets:submissionUiText('copyBrackets','Αντιγράφηκε. Συμπληρώστε τα πεδία που απομένουν στις αγκύλες.'),
    fallbackSuccess:submissionUiText('copySuccess','Το κείμενο αντιγράφηκε.'),
    fallbackFailure:submissionUiText('copyFallbackFailure','Επιλέξτε το κείμενο και αντιγράψτε το χειροκίνητα.')
  },feedback);
  return;
 }
 if(typeof navigator!=='undefined'&&navigator.clipboard&&navigator.clipboard.writeText){
  navigator.clipboard.writeText(copied).then(function(){
   if(feedback)feedback.textContent=/\[[^\]]+\]/.test(copied)?submissionUiText('copyBrackets','Αντιγράφηκε. Συμπληρώστε τα πεδία που απομένουν στις αγκύλες.'):submissionUiText('copySuccess','Το κείμενο αντιγράφηκε.');
  },function(){if(feedback)feedback.textContent=legacyCopySubmission(copied)?submissionUiText('copySuccess','Το κείμενο αντιγράφηκε.'):submissionUiText('copyNativeFailure','Δεν ήταν δυνατή η αντιγραφή. Επιλέξτε το κείμενο χειροκίνητα.');});
 }else if(feedback){
  feedback.textContent=legacyCopySubmission(copied)?submissionUiText('copySuccess','Το κείμενο αντιγράφηκε.'):submissionUiText('copyFallbackFailure','Επιλέξτε το κείμενο και αντιγράψτε το χειροκίνητα.');
 }
}
function renderSubmissionGuidance(rows){
 var target=$('transferSubmissionGuidanceBody');
 if(!target)return;
 var details=target.querySelector&&target.querySelector('[data-transfer-personalize]');
 var expanded=!!(details&&details.open);
 var data=submissionGuideData();
 data.mode=$('mode')?$('mode').value:'region';
 var feature=submissionFeatureState(rows,data.mode);
 var specialEntries=(rows||build()).filter(function(d){return submissionChannel(d).kind==='special';});
 var specialNote=specialEntries.length?'<p class="transfer-submission-special-note"><strong>Προσοχή — ειδική διαδρομή:</strong> '+specialEntries.map(function(d){return escape(d.title)+': '+escape(submissionChannel(d).text);}).join(' · ')+' Δείτε την αντίστοιχη εγγραφή δικαιολογητικών για λεπτομέρειες.</p>':'';
 updateSubmissionFeature(feature);
 target.innerHTML='<p><strong>'+escape(data.title)+'</strong></p>'+data.lines.map(function(line){
  return '<p>'+escape(line).replace(/(teachers\.minedu\.gov\.gr|mydocs\.minedu\.gov\.gr)/g,function(host){
   return '<a href="https://'+host+'/" target="_blank" rel="noopener noreferrer">'+host+' ↗</a>';
  })+'</p>';
 }).join('')+specialNote+(feature.specialOnly?'':submissionMessageHtml(data,expanded,feature.email));
}
function openSubmissionComposer(){
 var disclosure=$('transferSubmissionGuidance');
 if(!disclosure)return;
 if(global.EducationTransferEmailComponent){
  global.EducationTransferEmailComponent.open(disclosure,$('transferEmailFeatureOpen'),'[data-transfer-personalize]');
  return;
 }
 disclosure.open=true;
 var personalize=disclosure.querySelector('[data-transfer-personalize]');
 if(personalize)personalize.open=true;
 var button=$('transferEmailFeatureOpen');if(button)button.setAttribute('aria-expanded','true');
 if(disclosure.scrollIntoView)disclosure.scrollIntoView({block:'start',behavior:'smooth'});
}
function render(){
 var root=$('transferDocumentChecklist');if(!root)return;
 root.querySelectorAll('details[data-doc-details]').forEach(function(details){
  openRows[details.getAttribute('data-doc-details')]=details.open;
 });
 var rows=build();
 renderSubmissionGuidance(rows);
 var summary=optionalElement('transferDocumentActionSummary');
 if(summary){
  // General deadlines and submission channels are explained once, above this panel.
  // Only truly exceptional procedures warrant an additional inline warning.
  var exceptional=rows.filter(function(d){return ['health-committee','health-kepa','judicial-spouse'].indexOf(d.id)!==-1;});
  summary.hidden=exceptional.length===0;
  summary.innerHTML=exceptional.length?'<p class="transfer-docs-warning"><strong>Ειδική διαδικασία:</strong> Η επιλεγμένη περίπτωση ('+exceptional.map(function(d){return escape(d.title);}).join(' · ')+') χρειάζεται ιδιαίτερο έλεγχο υποβολής. Διαβάστε τη σχετική εγγραφή παρακάτω· δεν αρκούν πάντα οι γενικές οδηγίες email/mydocs.</p>':'';
 }
 root.innerHTML=rows.map(function(d){
  var id=escape(d.id),full=legalSeparation(d.detail),open=Object.prototype.hasOwnProperty.call(openRows,d.id)?openRows[d.id]:isCritical(d);
  var warning=isCritical(d),heading=escape(d.title),detail=escape(full.body).replace(/\n/g,'<br>');
  var channel=submissionChannel(d);
  if(d.id==='record')detail=detail.replace('Μονάδες Συνθηκών Διαβίωσης (Μ.Σ.Δ.):','<strong>Μονάδες Συνθηκών Διαβίωσης (Μ.Σ.Δ.):</strong>');
  // Avoid repeating the same advice outside and inside its disclosure, including EAE qualification evidence.
  // v3.22.124: avoid duplicate previews for all five standard Artistic School supporting documents.
  var compact=d.id==='locality' || ['family','record','music-common','eae-doctorate','eae-masters','eae-two-year','eae-5year','eae-pe11','art-cv','art-inventory','art-degrees','art-publications','ic-foreign'].indexOf(d.id)!==-1;
  var taskType=documentTaskType(d,channel),actionable=taskType==='action';
  if(!actionable)delete checked[d.id];
  return '<article class="edu-transfer-doc'+(actionable&&checked[d.id]?' is-complete':'')+(warning?' has-warning':'')+(compact?' is-compact':'')+(d.disabled?' is-unavailable':'')+(!actionable?' is-informational':'')+'" data-doc-card="'+id+'">'+
   '<div class="edu-transfer-doc__head">'+
    (actionable?'<input class="edu-transfer-doc__check" type="checkbox" id="transferDocCheck-'+id+'" data-doc-id="'+id+'" '+(checked[d.id]?'checked':'')+' aria-label="Ετοιμάστηκε: '+heading+'">':'<span class="edu-transfer-doc__info-icon" aria-hidden="true">ⓘ</span>')+
    '<div class="edu-transfer-doc__main">'+
     (actionable?'<label class="edu-transfer-doc__title" for="transferDocCheck-'+id+'">'+heading+'</label>':'<div class="edu-transfer-doc__title">'+heading+'</div>')+
     '<div class="edu-transfer-doc__channel edu-transfer-doc__channel--'+escape(channel.kind)+'">'+escape(channel.text)+'</div>'+
     (compact||!d.tag?'':'<div class="edu-transfer-doc__category">'+escape(d.tag)+'</div>')+
     (compact?'':'<p class="edu-transfer-doc__summary">'+escape(shortText(d))+'</p>')+
     '<div class="edu-transfer-doc__actions">'+portalLink(d.id)+'</div>'+
     '<details class="edu-transfer-doc__disclosure" data-doc-details="'+id+'" '+(open?'open':'')+'>'+
      '<summary>Περισσότερες πληροφορίες</summary>'+
      '<div class="edu-transfer-doc__body"><div class="edu-transfer-doc__detail">'+detail+'</div>'+
       (full.source?'<p class="edu-transfer-doc__source">'+escape(full.source)+'</p>':'')+
      '</div>'+
     '</details>'+
    '</div>'+
   '</div></article>';
 }).join('');
 root.querySelectorAll('[data-doc-id]').forEach(function(input){input.addEventListener('change',function(){
  checked[input.dataset.docId]=input.checked;
  var card=input.closest('[data-doc-card]');if(card)card.classList.toggle('is-complete',input.checked);
  progress(root);
 });});
 root.querySelectorAll('details[data-doc-details]').forEach(function(details){details.addEventListener('toggle',function(){
  openRows[details.getAttribute('data-doc-details')]=details.open;
 });});
 progress(root);
}

function print(){var rows=build(),mode=$('mode').selectedOptions[0].textContent,submission=submissionGuideData(),html='<!doctype html><html lang="el"><meta charset="utf-8"><title>Λίστα δικαιολογητικών μετάθεσης</title><style>body{font:14px Arial;max-width:780px;margin:36px auto;line-height:1.5}li{margin-bottom:14px}small{color:#555;font-size:11px}</style><h1>Έλεγχος δικαιολογητικών μετάθεσης</h1><p>'+escape(mode)+'</p>'+(($('mode').value==='intercultural')?'<h2>Σημειώσεις Διαπολιτισμικών</h2><p><strong>Αλληλεπίδραση με άλλες αιτήσεις.</strong> Σε περίπτωση ικανοποίησης μετάθεσης σε ΕΑΕ/ΚΕΔΑΣΥ, Μουσικό ή Καλλιτεχνικό, δεν εξετάζεται η αίτηση για Διαπολιτισμικό. Αν ικανοποιηθεί σε Διαπολιτισμικό, δεν λαμβάνονται υπόψη άλλες αιτήσεις.</p><p><strong>Μετάθεση προς Γενική μέσα στην ίδια περιοχή.</strong> Εκπαιδευτικός με οργανική σε Διαπολιτισμικό που ζητά σχολείο Γενικής στην ίδια περιοχή υποβάλλει αίτηση από περιοχή σε περιοχή, όχι βελτίωσης.</p>':'')+'<h2>Πού αποστέλλονται τα δικαιολογητικά;</h2><p><strong>'+escape(submission.title)+'</strong></p>'+submission.lines.map(function(t){return '<p>'+escape(t)+'</p>';}).join('')+'<h3>Θέμα και σώμα μηνύματος — υπόδειγμα</h3><p>Αντικαταστήστε τα πεδία στις αγκύλες με τα στοιχεία της οριστικοποιημένης αίτησης.</p><p><strong>Θέμα:</strong> '+escape(submissionMessageTemplate().subject)+'</p><p><strong>Σώμα κειμένου:</strong> '+escape(submissionMessageTemplate().body)+'</p><p><small>Εγκύκλιος 138014/Ε2/15-10-2026, §'+escape(submissionMessageTemplate().circular)+'</small></p>'+'<p><strong>Ενδεικτική λίστα — όχι βεβαίωση υποχρεωτικών εγγράφων.</strong> Επαληθεύστε τα πάντα με την εγκύκλιο της περιόδου και τη ΔΔΕ.</p><ul>'+rows.map(function(d){return '<li>'+(d.disabled?'— Δεν μοριοδοτείται':(documentTaskType(d,submissionChannel(d))==='action'?(checked[d.id]?'☑':'☐'):'ⓘ'))+' <strong>'+escape(d.title)+'</strong><br>'+escape(d.detail).replace(/\n/g,'<br>')+(d.tag && ['family','music-common'].indexOf(d.id)===-1?' <small>('+escape(d.tag)+')</small>':'')+'<br><small>'+escape(submissionChannel(d).text)+'</small>'+'</li>';}).join('')+'</ul></html>';var win=global.open('','_blank');if(win){win.document.write(html);win.document.close();win.focus();win.print();}}
function init(){var b=$('transferPrintChecklist');if(b)b.addEventListener('click',print);
 var launch=$('transferEmailFeatureOpen');if(launch)launch.addEventListener('click',openSubmissionComposer);
 var disclosure=$('transferSubmissionGuidance');if(disclosure){if(global.EducationTransferEmailComponent)global.EducationTransferEmailComponent.bindState(disclosure,$('transferEmailFeatureOpen'));else disclosure.addEventListener('toggle',function(){var btn=$('transferEmailFeatureOpen');if(btn)btn.setAttribute('aria-expanded',disclosure.open?'true':'false');});}
 document.addEventListener('click',copySubmissionTemplate);document.addEventListener('input',function(e){if(onSubmissionPersonalInput(e))return;if(e.target.closest && e.target.closest('#transferDocumentChecklist'))return;if(e.target.closest && e.target.closest('.edu-page-transfer'))render();});document.addEventListener('change',function(e){if(onSubmissionPersonalInput(e))return;if(e.target.closest && e.target.closest('#transferDocumentChecklist'))return;if(e.target.closest && e.target.closest('.edu-page-transfer'))render();});render();}
global.EducationTransferDocuments=Object.freeze({init:init,render:render,build:build,submissionChannel:submissionChannel,coServiceInfo:coServiceInfo,submissionGuideData:submissionGuideData,renderSubmissionGuidance:renderSubmissionGuidance,submissionMessageTemplate:submissionMessageTemplate,submissionFeatureState:submissionFeatureState,submissionMailto:submissionMailto});
})(window);
