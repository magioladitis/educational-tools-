/* Transfer document preparation guide. Advisory only; no personal data transmitted. */
(function(global){
'use strict';
var $=function(id){return document.getElementById(id);};
var base=[
 ['submission','Οριστικοποίηση ηλεκτρονικής αίτησης','Η αίτηση υποβάλλεται και οριστικοποιείται στο https://teachers.minedu.gov.gr/ από 16/10 έως 02/11/2026. Προσωρινή αποθήκευση ΔΕΝ ισοδυναμεί με υποβολή. Αποθηκεύστε το PDF της οριστικοποιημένης αίτησης με αριθμό πρωτοκόλλου.','Έλεγχος διαδικασίας'],
 ['record','Υπηρεσιακό μητρώο και χρόνος υπηρεσίας','Δείτε υπηρετήσεις και μόρια στο https://teachers.minedu.gov.gr/. Πίνακες Δ.Ε.: 23/11/2026· αιτήματα διορθώσεων 23–27/11/2026 (μεταθέσεις από περιοχή σε περιοχή). Αντιπαραβάλετε το μητρώο και επικοινωνήστε με τη ΔΔΕ για σφάλματα.','Έλεγχος φακέλου']
];
var special={};
var EAE_SOURCE='Εγκύκλιος 138014/Ε2/15-10-2026, κεφάλαιο Δ΄, σελ. 34–38.';
var MUSIC_SOURCE='Εγκύκλιος 138014/Ε2/15-10-2026, κεφάλαιο Ε΄, σελ. 39–41.';
var ART_SOURCE='Εγκύκλιος 138014/Ε2/15-10-2026, κεφάλαιο ΣΤ΄, σελ. 41–43.';
var IC_SOURCE='Εγκύκλιος 138014/Ε2/15-10-2026, κεφάλαιο Ζ΄, σελ. 43–45.';
function dossierRow(kind,year,newElements){
 var calc=global.EducationTransferEligibility;
 var state=calc&&calc.dossier?calc.dossier({kind:kind,year:year,newElements:newElements}):null;
 return entry(kind+'-dossier','Έλεγχος προηγούμενου ηλεκτρονικού φακέλου',
   state?state.status+' '+state.limitation:'Ελέγξτε τον φάκελο με την εγκύκλιο του έτους υποβολής.',
   state&&state.skipResubmission?'Μη επανυποβολή φακέλου (βάση 2026–2027)':'Έλεγχος υποβολής');
}
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
  teachingYears:$('transferEaeTeachingYears').value,newly:$('transferEaeNewly').checked,
  areaYears:$('transferEaeAreaYears').value,eaeYears:$('transferEaeExperienceYears').value};
 var k=global.EducationTransferEligibility, result=k?k.eae(inp):null;
 var rows=[entry('eae-basis','Αποδεικτικά προσόντων Ειδικής Αγωγής — 5 εναλλακτικές',result&&result.qualificationTitle?'Έχετε επιλέξει: '+result.qualificationTitle+'. Αρκεί μία αποδεκτή διαδρομή, όχι και οι πέντε. Ελέγξτε τη συνάφεια/αναγνώριση του τίτλου και την καταχώρισή του. '+EAE_SOURCE:'Επιλέξτε μία από τις 5 εναλλακτικές διαδρομές παρακάτω. Δεν χρειάζονται και οι πέντε. '+EAE_SOURCE,'Έλεγχος ΔΔΕ')];
 if(inp.qualification==='doctorate')rows.push(entry('eae-doctorate','Διδακτορικό — τεκμηρίωση συνάφειας','Αν δεν καλύπτεται από απόφαση διαπίστωσης συνάφειας, ελέγξτε την απαιτούμενη περίληψη διατριβής. Για τίτλο αλλοδαπής απαιτούνται μετάφραση/αναγνώριση σύμφωνα με την εγκύκλιο.','Δικαιολογητικά τίτλου'));
 if(inp.qualification==='masters')rows.push(entry('eae-masters','Μεταπτυχιακό — τεκμηρίωση συνάφειας','Ελέγξτε αν ο τίτλος περιλαμβάνεται σε σχετική ΥΑ συνάφειας· διαφορετικά απαιτείται τεκμηρίωση προγράμματος με τουλάχιστον 50% μαθημάτων ΕΑΕ και περίληψη εργασίας, αν εκπονήθηκε. Ξένοι τίτλοι: μετάφραση/αναγνώριση.','Δικαιολογητικά τίτλου'));
 if(inp.qualification==='two_year')rows.push(entry('eae-two-year','Πτυχίο διετούς μετεκπαίδευσης στην ΕΑΕ','Ελέγξτε τον τίτλο διετούς μετεκπαίδευσης των Διδασκαλείων ή τη νόμιμη αναγνώριση ισότιμου τίτλου αλλοδαπής.','Αποδεικτικό εξειδίκευσης'));
 if(inp.qualification==='experience')rows.push(entry('eae-5year','Πενταετής πραγματική διδακτική προϋπηρεσία ΕΑΕ','Ελέγξτε υπηρετήσεις αναλυτικά ανά φορέα, χρονικό διάστημα και δομή ΕΑΕ: ΣΜΕΑΕ, ΕΕΕΕΚ, Τμήμα Ένταξης, παράλληλη στήριξη, ΚΕΔΑΣΥ κ.λπ. Η ΔΔΕ/ΥΠΑΙΘΑ επιβεβαιώνει τη συνδρομή της προϋπόθεσης.','Βεβαιώσεις και μητρώο'));
 if(inp.qualification==='pe11')rows.push(entry('eae-pe11','ΠΕ11 — κύρια ειδικότητα ΤΕΦΑΑ','Ελέγξτε ότι το βασικό πτυχίο ΤΕΦΑΑ αποτυπώνει μία από τις κύριες ειδικότητες που απαριθμεί η εγκύκλιος. Δεν αρκεί μεμονωμένη επιμόρφωση.','Τίτλος και ειδικότητα'));
 rows.push(entry('eae-service','Υπηρεσιακές προϋποθέσεις δικαιώματος μετάθεσης στην ΕΑΕ','Ελέγξτε την τριετή διδακτική υπηρεσία (Γενική → ΕΑΕ), τη διετία στην περιοχή διορισμού όπου απαιτείται ή τις ειδικές υποχρεώσεις ήδη υπηρετούντων στην ΕΑΕ.','Προέλεγχος δικαιώματος'));
 if(inp.destination==='kedasy')rows.push(entry('eae-kedasy','ΚΕΔΑΣΥ — κλάδος και προβλεπόμενη οργανική θέση','Επιτρέπονται μόνο οι κλάδοι ΠΕ02, ΠΕ03, ΠΕ04 εφόσον υπάρχουν αντίστοιχες κενές οργανικές θέσεις στο οικείο οργανόγραμμα. Απαιτείται μία αναγνωρισμένη διαδρομή προσόντων ΕΑΕ και υπηρεσιακή επιβεβαίωση. '+EAE_SOURCE,'Πρόσθετη προϋπόθεση ΚΕΔΑΣΥ'));
 if(inp.destination==='deaf')rows.push(entry('eae-sign','Σχολείο Κωφών — πιστοποίηση ΕΝΓ','Απαιτείται πιστοποιημένη επάρκεια στην Ελληνική Νοηματική Γλώσσα από αποδεκτό φορέα κατά την εγκύκλιο· απλή παρακολούθηση δεν αρκεί.','Ειδικό αποδεικτικό'));
 if(inp.destination==='blind')rows.push(entry('eae-braille','Σχολείο Τυφλών — πιστοποίηση Braille','Απαιτείται πιστοποιημένη γνώση γραφής Braille από αποδεκτό φορέα κατά την εγκύκλιο· απλή παρακολούθηση δεν αρκεί.','Ειδικό αποδεικτικό'));
 if(result&&result.advice.length)rows.push(entry('eae-warnings','Σημεία που απαιτούν προσοχή',result.advice.join(' '),'Προέλεγχος — όχι απόφαση'));
 rows.push(dossierRow('eae',$('transferEaePreviousYear').value,$('transferEaeNewDocuments').checked));
 return rows;
}
function musicDocuments(){
 var type=$('transferEducationType').value, exception=$('transferMusicException').value,subject=$('transferMusicSubject').value;
 var m=global.EducationTransferEligibility?global.EducationTransferEligibility.music({educationType:type,organic:$('transferMusicOrganic').checked,teachingYears:$('transferMusicTeachingYears').value,exceptionType:exception,subject:subject}):null;
 var rows=[
 entry('music-common','Γενικός φάκελος (αφορά και τη Γενική παιδεία)','Αναλυτικό βιογραφικό, πίνακας δικαιολογητικών και διαθέσιμα πιστοποιητικά που τεκμηριώνουν επιμόρφωση, μετεκπαίδευση, διδακτική εμπειρία σε Μουσικά, μεταπτυχιακούς τίτλους και επιστημονικό έργο. Δεν απαιτείται μουσική εξειδίκευση για κλάδους Γενικής παιδείας. '+MUSIC_SOURCE,'Φάκελος κρίσης'),
 entry('music-service','Προϋποθέσεις υπηρεσίας και οργανικής','Ελέγξτε υπηρέτηση οργανικής, πενταετή διδακτική εμπειρία έως την ημερομηνία αναφοράς και, αν δηλώθηκε, τη συγκεκριμένη εξαίρεση από την πενταετία.','Έλεγχος ΔΔΕ'),
dossierRow('music',$('transferMusicPreviousYear').value,$('transferMusicNewDocuments').checked)
 ];
 if(type==='specialized'){
  rows.push(entry('music-specialist','Μουσική παιδεία — πρόσθετη εξειδίκευση','Ελέγξτε τα αποδεικτικά απόλυτης εξειδίκευσης για την ειδικότητα ΠΕ79.01/ΤΕ16. Τίτλοι μουσικών ιδρυμάτων, κατάλληλη αναγνώριση και τεκμηριωμένη ενασχόληση.','Μόνο μουσική παιδεία'));
  if(m&&m.subjectInfo)rows.push(entry('music-subject','Αντικείμενο: '+m.subjectInfo.name,m.subjectInfo.document,'Εξειδίκευση ανά μάθημα'));
  if(subject==='unspecified')rows.push(entry('music-subject-missing','Επιλογή συγκεκριμένου μουσικού αντικειμένου','Επιλέξτε Θεωρητικά, Παραδοσιακή Μουσική, Σύνολα, Όργανο ή Άλλο ώστε να εμφανιστούν τα ειδικά τεκμήρια.','Συμπληρώστε στοιχεία'));
  rows.push(entry('music-appointment','Περιορισμός διορισμού σε Μουσικά','Ελέγξτε την αρχική πράξη διορισμού: για ορισμένες παλαιές μουσικές κατηγορίες επιτρέπονται μεταθέσεις μόνο σε Μουσικά Σχολεία.','Έλεγχος κλάδου'));
 }
 if(m&&m.advice.length)rows.push(entry('music-warnings','Εκκρεμή σημεία προελέγχου',m.advice.join(' '),'Προέλεγχος — όχι απόφαση'));
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
  entry('art-service','Πενταετής εμπειρία και υπηρέτηση οργανικής','Συμπλήρωση υπηρεσιακών προϋποθέσεων έως 31-08-2027. Ελέγξτε τυχόν νόμιμη εξαίρεση από την πενταετία και την υπηρεσιακή καταχώριση μέσω ΔΔΕ.','Προέλεγχος δικαιώματος'),
  entry('art-cv','Αναλυτικό βιογραφικό','Προετοιμάστε πλήρες βιογραφικό που τεκμηριώνει επιμόρφωση, μετεκπαίδευση, διδακτική εμπειρία σε Καλλιτεχνικά, μεταπτυχιακές σπουδές, επιστημονικό έργο και συναφή προσόντα. '+ART_SOURCE,'Φάκελος κρίσης'),
  entry('art-inventory','Πίνακας δικαιολογητικών','Καταγράψτε σε πίνακα τους τίτλους, βεβαιώσεις και λοιπά αποδεικτικά που τεκμηριώνουν τα συνεκτιμώμενα προσόντα· μην περιλάβετε άσχετα έγγραφα.','Σύμφωνα με εγκύκλιο'),
  entry('art-degrees','Τίτλοι σπουδών, επιμόρφωση και διδακτική εμπειρία','Συγκεντρώστε μόνο τα σχετικά τεκμήρια. Για αλλοδαπούς τίτλους σπουδών ελέγξτε αναγνώριση ΔΟΑΤΑΠ/ΔΙΚΑΤΣΑ και, όπου απαιτείται, επίσημη μετάφραση. Η ΔΔΕ ελέγχει τα υπηρεσιακά στοιχεία.','Έλεγχος τίτλων'),
  entry('art-publications','Δημοσιεύσεις, βιβλία και επιστημονικό έργο','Εφόσον δηλώνονται δημοσιεύσεις ή βιβλία, η εγκύκλιος 2026–2027 δέχεται εξώφυλλο και πρώτη σελίδα ή σύνδεσμο της ηλεκτρονικής έκδοσης.','Εφόσον υπάρχουν'),
  dossierRow('art',$('transferArtPreviousYear').value,$('transferArtNewDocuments').checked)
 ];
 if(result&&result.specialized){
  rows.push(entry('art-committee','Ειδική κρίση για κλάδο καλλιτεχνικής παιδείας',
    'Κλάδος '+result.branchTitle+'. Προβλέπεται εισήγηση της Επιστημονικής Επιτροπής Καλλιτεχνικής Παιδείας. Οργανώστε τα συναφή με το πραγματικό αντικείμενο τίτλους, διδασκαλία και καλλιτεχνική/επιστημονική δραστηριότητα· δεν αποτελεί γενική απαίτηση «portfolio» για κάθε κλάδο.',
    'ΠΕ08/ΠΕ11/ΠΕ79.01/ΠΕ91.01/ΠΕ91.02'));
  if(orientation)rows.push(entry('art-orientation','Συναφή τεκμήρια κατεύθυνσης',
    'Δηλώθηκε '+$('transferArtOrientation').selectedOptions[0].textContent+'. Τεκμηριώστε μόνο τη συνάφεια τίτλων, διδακτικού ή καλλιτεχνικού έργου με την προκηρυγμένη ειδικότητα, σύμφωνα με όσα γίνονται δεκτά. Η κατεύθυνση από μόνη της δεν αποδεικνύει δικαίωμα.',
    'Εξατομικευμένη υπενθύμιση'));
 }
 if(result&&result.advice.length)rows.push(entry('art-warnings','Εκκρεμότητες προελέγχου',result.advice.join(' '),'Υπηρεσιακή επαλήθευση'));
 return rows;
}
function interculturalDocuments(){
 var elig=global.EducationTransferEligibility, qs=elig&&elig.INTERCULTURAL_QUALIFICATIONS||[];
 var chosen=Array.prototype.map.call(document.querySelectorAll('[data-intercultural-qual]:checked'),function(el){return el.getAttribute('data-intercultural-qual');});
 var state=elig&&elig.intercultural({organic:$('transferInterculturalOrganic').value,current:$('transferInterculturalCurrent').value,
   years:$('transferInterculturalYears').value,vacancy:$('transferInterculturalVacancy').value,
   languageCase:$('transferInterculturalLanguageCase').value,qualifications:chosen});
 var rows=[
   entry('ic-basis','Προέλεγχος υπηρεσιακών προϋποθέσεων',state?state.status+' '+state.advice.join(' ')+' '+IC_SOURCE:IC_SOURCE,'Θεμελίωση — έλεγχος ΔΔΕ'),
   entry('ic-service-record','Υπηρέτηση οργανικής θέσης','Ελέγξτε τις πράξεις τοποθέτησης, τις πραγματικές υπηρετήσεις και τον ειδικό κανόνα διετίας όταν ήδη κατέχετε οργανική σε Πειραματικό Διαπολιτισμικής.','Έλεγχος μητρώου'),
   entry('ic-cv','Αναλυτικό βιογραφικό σημείωμα','Πλήρες βιογραφικό, πίνακας δικαιολογητικών και πιστοποιητικά/τίτλοι που αποδεικνύουν τα πραγματικά δηλωμένα προσόντα. Αποφύγετε άσχετα έγγραφα.','Φάκελος προσόντων')
 ];
 if(state&&state.languageException)rows.push(entry('ic-roma','Εξαίρεση από τη γνώση γλώσσας προέλευσης','Για σχολεία στα οποία φοιτούν κατά πλειοψηφία μαθητές Ρομά δεν απαιτείται η γνώση της ξένης γλώσσας προέλευσης που αναφέρει η πρώτη προτίμηση προσόντων.','Ειδική περίπτωση'));
 chosen.forEach(function(id){
   var q=qs.find(function(x){return x.id===id;});
   if(q)rows.push(entry('ic-qual-'+id,'Τεκμηρίωση: '+q.title,q.proof,'Επιλεγμένο πρόσθετο προσόν'));
 });
 if(!chosen.length)rows.push(entry('ic-none','Δεν επιλέχθηκαν πρόσθετα προσόντα','Ελέγξτε αν διαθέτετε κάποιο από τα εννέα προτιμώμενα προσόντα· δεν αποτελούν εννέα υποχρεωτικά δικαιολογητικά για όλους.','Προσωπικός έλεγχος'));
 rows.push(entry('ic-foreign','Ξενόγλωσσοι τίτλοι και δημοσιεύσεις','Για τίτλους αλλοδαπής ελέγξτε αναγνώριση ΔΟΑΤΑΠ/ΔΙΚΑΤΣΑ και επίσημη μετάφραση όπου απαιτείται. Για δημοσιεύσεις/βιβλία αρκεί αντίγραφο εξωφύλλου και πρώτης σελίδας. Η εγκύκλιος εξαιρεί τίτλους γλωσσομάθειας συγκεκριμένων γλωσσών από την απαίτηση μετάφρασης.','Ανά περίπτωση'));
 rows.push(dossierRow('intercultural',$('transferInterculturalPreviousYear').value,$('transferInterculturalNewDocuments').checked));
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
   if($('msdMode').value==='direct'){
    record.detail+='Αντιγράψτε τον ακριβή συνολικό αριθμό μορίων Μ.Σ.Δ. από το επίσημο Μητρώο. Οι αναλυτικές υπηρετήσεις δεν προστίθενται δεύτερη φορά.';
   }else{
    record.detail+='Διασταυρώστε τις υπηρετήσεις ανά σχολείο και κατηγορία, τις αποσπάσεις, την παράλληλη υπηρεσία και τις προσαυξήσεις με το επίσημο Μητρώο.';
   }
  }
  if($('familyStatusEligible').checked || +$('eligibleChildren').value>0){
   a.push(entry('family','Πιστοποιητικό οικογενειακής κατάστασης — αυτεπάγγελτη αναζήτηση',
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
    'Πρόσθετα μόνο εφόσον απαιτούνται'));
  }
  var caseSelect=$('coService').checked ? $('transferCoServiceCase') : (document.querySelector?document.querySelector('#transferCoServiceCase'):null);
  var caseValue=caseSelect?caseSelect.value:'unspecified';
  var areaMode=eae && $('transferEaeArea').value==='same'?'local':mode;
  var info=coServiceInfo(caseValue,areaMode);
  // Show the documentation for a blocked status even though the scoring box is unchecked/disabled.
  if($('coService').checked || (caseValue!=='unspecified' && info.blocked)){
   var spouseDoc=entry('coservice','Δικαιολογητικά συνυπηρέτησης',
    info.summary+'\n'+info.detail+'\nΤα απαιτούμενα αποδεικτικά συνυπηρέτησης πρέπει να έχουν εκδοθεί στο τελευταίο τρίμηνο (με εξαίρεση βεβαιώσεις εργοδοτών για προγενέστερη εργασία που έπαυσε: τελευταία διετία). Η συνυπηρέτηση αφορά μία περιοχή μετάθεσης — όχι ολόκληρο τον νομό — ή τον δήμο σχολείου για βελτίωση/οριστική τοποθέτηση. '+FAMILY_SOURCE,
    info.blocked?'Δεν θεμελιώνεται συνυπηρέτηση (+0)':'Δικαιολογητικά βάσει επιλογής');
   spouseDoc.disabled=info.blocked;
   a.push(spouseDoc);
  }
  if($('locality').checked) a.push(entry('locality','Εντοπιότητα','Ελέγξτε βεβαίωση δημοτικότητας, χρόνο κτήσης της και αντιστοίχιση με τη ζητούμενη περιοχή ή τον δήμο.','Πιθανό δικαιολογητικό'));
  // No separate MSD row: the record item above includes this check for either entry mode.
 }
 if(mode==='region' && global.EducationTransferEligibility && global.EducationTransferEligibility.returnToGeneral){
  var rr=global.EducationTransferEligibility.returnToGeneral({
   origin:$('transferGeneralOrigin').value,eaeYears:$('transferGeneralEaeYears').value,
   areaYears:$('transferGeneralEaeAreaYears').value,sinceTransferYears:$('transferGeneralEaeSinceTransfer').value,
   organicServed:$('transferGeneralEaeOrganic').checked});
  if(rr.applicable) a.push(entry('eae-to-general','Ειδική Αγωγή → Γενική: θεμελίωση δικαιώματος',
    rr.status+' '+(rr.advice.length?rr.advice.join(' '):'Ελέγξτε τις υπηρετήσεις και την οργανική με τη ΔΔΕ.')+' Βάση: εγκύκλιος 138014/Ε2/15-10-2026, σελ. 35, ενότητα ΕΑΕ. Απαιτείται υπηρεσιακή επιβεβαίωση.',
    'Προέλεγχος δικαιώματος'));
 }
 if(eae) a=a.concat(eaeDocuments());
 if(mode==='music') a=a.concat(musicDocuments());
 if(mode==='art') a=a.concat(artDocuments());
 if(mode==='intercultural') a=a.concat(interculturalDocuments());
 return a;
}
function escape(s){return String(s).replace(/[&<>"']/g,function(c){return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c];});}
function eaeChoices(printMode){
 var names=global.EducationTransferEligibility && global.EducationTransferEligibility.EAE_TITLES || {};
 var chosen=$('transferEaeQualification').value;
 var options=['doctorate','masters','two_year','experience','pe11'];
 return '<div class="transfer-eae-options"><p class="small-note"><strong>Πέντε εναλλακτικές οδοί εξειδίκευσης (επιλέγετε μία):</strong></p>' +
  (printMode?'<ol>':'<div role="radiogroup" aria-label="Επιλογή αποδεικτικού προσόντων Ειδικής Αγωγής">') +
  options.map(function(key){var selected=chosen===key, name=escape(names[key]||key);return printMode?'<li>'+name+(selected?' <strong>— επιλεγμένο</strong>':'')+'</li>':'<label class="transfer-eae-option"><input type="radio" name="eaeDocQualification" value="'+key+'" '+(selected?'checked':'')+'> <span>'+name+(key==='pe11'?' (μόνο ΠΕ11)':'')+'</span></label>';}).join('') +
  (printMode?'</ol>':'</div>') + '<p class="small-note">Δεν απαιτείται να διαθέτετε και τα πέντε προσόντα. Η αποδοχή του επιλεγμένου τίτλου ελέγχεται από την αρμόδια υπηρεσία.</p></div>';
}
function portalLink(id){return ['submission','record'].indexOf(id)!==-1?' <a class="edu-transfer-doc__portal" href="https://teachers.minedu.gov.gr/" target="_blank" rel="noopener noreferrer">Άνοιγμα teachers.minedu.gov.gr ↗</a>':'';}

/* v3.22.121: merged record recognition checks, compact EAE service/routing; details remain printable. */
var openRows={};
var SUMMARIES={
 submission:'16/10–02/11/2026: οριστικοποιήστε την αίτηση. Η προσωρινή αποθήκευση ΔΕΝ ισοδυναμεί με υποβολή.',
 record:'Ελέγξτε υπηρετήσεις και μόρια. Πίνακες Δ.Ε.: 23/11/2026 · Διορθώσεις: 23–27/11/2026.',
 family:'Το ελληνικό πιστοποιητικό οικογενειακής κατάστασης αναζητείται αυτεπαγγέλτως από τη ΔΔΕ — δεν το προσκομίζετε.',
 'children-specific':'Ανήλικα: χωρίς βεβαίωση σπουδών. Ενήλικα που σπουδάζουν: βεβαίωση φοίτησης όπου απαιτείται. Νεογέννητα: ειδικές οδηγίες έως 31/12/2026.',
 'eae-basis':'Ελέγξτε ποια από τις πέντε εναλλακτικές διαδρομές εξειδίκευσης ισχύει για εσάς.',
 'eae-to-general':'Ελέγξτε τις ειδικές προϋποθέσεις επιστροφής στη Γενική Εκπαίδευση με τη ΔΔΕ.',
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
 return d.id==='eae-warnings' ||
   (d.id==='coservice' && d.tag.indexOf('Δεν θεμελιώνεται')!==-1);
}
function progress(root){
 var rows=root.querySelectorAll('[data-doc-id]');
 var complete=0,active=0;
 rows.forEach(function(input){if(input.disabled)return;active++;if(input.checked)complete++;});
 var target=$('transferDocumentProgress');
 if(target)target.textContent='Έλεγχοι που σημειώθηκαν: '+complete+' από '+active+'.';
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
   'Η αίτηση μετάθεσης υποβάλλεται στο teachers.minedu.gov.gr.',
   'Αν απαιτείται νέος ή συμπληρωματικός φάκελος προσόντων/βιογραφικού, υποβάλετέ τον μέσω mydocs.minedu.gov.gr σε μία αποστολή. Ελέγξτε πρώτα την ισχύ προηγούμενου ηλεκτρονικού φακέλου.',
   'Για τυχόν νέα δικαιολογητικά μοριοδότησης ακολουθούνται οι οδηγίες της ΔΔΕ οργανικής.'
  ]
 },
 art:{
  title:'Μετάθεση σε Καλλιτεχνικό Σχολείο',
  lines:[
   'Η αίτηση μετάθεσης υποβάλλεται στο teachers.minedu.gov.gr.',
   'Νέος ή συμπληρωματικός φάκελος βιογραφικού/προσόντων: μέσω mydocs.minedu.gov.gr σε μία αποστολή, με PDF έως 50 MB, ασπρόμαυρα 200–300 dpi, έως 02/11/2026.',
   'Ελέγξτε πρώτα τον προηγούμενο φάκελο· η υποβολή έντυπων ή εκπρόθεσμων δικαιολογητικών δεν υποκαθιστά την προβλεπόμενη διαδικασία.'
  ]
 },
 intercultural:{
  title:'Μετάθεση σε Πειραματικό Διαπολιτισμικής Εκπαίδευσης',
  lines:[
   'Η αίτηση μετάθεσης υποβάλλεται στο teachers.minedu.gov.gr.',
   'Εφόσον απαιτείται νέος φάκελος, βιογραφικό και αποδεικτικά προσόντων αποστέλλονται αποκλειστικά μέσω mydocs.minedu.gov.gr, σε μία αποστολή PDF (ασπρόμαυρα 200–300 dpi) και εντός προθεσμίας.',
   'Ελέγξτε αν ο παλαιός ηλεκτρονικός φάκελος καλύπτει τα στοιχεία που δηλώνετε. Δεν αντικαθίσταται η προβλεπόμενη διαδρομή με έντυπα ή email.'
  ]
 }
};
function submissionGuideData(){
 var mode=$('mode')?$('mode').value:'region';
 return SUBMISSION_GUIDANCE[mode]||SUBMISSION_GUIDANCE.region;
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
  var options=Object.keys(labels).sort(function(a,b){return a.localeCompare(b,'el');}).map(function(code){
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
function submissionMessageHtml(data,expanded){
 var tpl=submissionMessageTemplate(data.mode,submissionPersonal);
 return '<section class="transfer-submission-template" aria-label="Υπόδειγμα θέματος και σώματος αποστολής">'+
  '<h4>Θέμα και σώμα μηνύματος — υπόδειγμα</h4>'+
  '<p class="transfer-submission-template__note">Το υπόδειγμα ακολουθεί τον επιλεγμένο τύπο αίτησης. '+
  (tpl.general?'Χρησιμοποιείται για email στη ΔΔΕ οργανικής.':'Για τα σχετικά δικαιολογητικά της ΔΔΕ ή την υποβολή στο mydocs, <strong>μόνο όπου προβλέπεται</strong>. Στο mydocs το σώμα μπορεί να εμφανίζεται ως πεδίο «Αιτούμαι».')+'</p>'+
  '<details class="transfer-submission-template__personalize" data-transfer-personalize'+(expanded?' open':'')+'>'+
  '<summary>Δημιουργία έτοιμου μηνύματος (προαιρετικά)</summary>'+
  '<div class="transfer-submission-template__personal-body">'+
  '<p class="transfer-submission-template__private-note">Συμπληρώστε όσα στοιχεία θέλετε από την <strong>οριστικοποιημένη</strong> αίτηση. Τα κενά παραμένουν ως υποδείξεις σε αγκύλες. Τα στοιχεία χρησιμοποιούνται μόνο σε αυτή τη σελίδα, χωρίς αποστολή ή μόνιμη αποθήκευση.</p>'+
  '<div class="transfer-submission-template__fields">'+
  personalField('name','Ονοματεπώνυμο','π.χ. Παπαδόπουλος Νικόλαος',true)+
  personalField('branch','Κλάδος / ειδικότητα','π.χ. ΠΕ03',false)+
  personalField('registry','Αριθμός μητρώου','Α.Μ. εκπαιδευτικού',false)+
  personalField('protocol','Αριθμός πρωτοκόλλου οριστικοποιημένης αίτησης','Αντιγραφή από teachers.minedu.gov.gr',true)+
  (tpl.general?'':personalField('home','ΔΔΕ οργανικής','π.χ. ΔΔΕ Κέρκυρας',true))+
  '</div><button type="button" class="secondary transfer-submission-template__clear" data-transfer-personal-clear>Καθαρισμός πεδίων</button></div></details>'+
  '<div class="transfer-submission-template__item"><div class="transfer-submission-template__bar"><strong>Θέμα:</strong><button type="button" class="secondary" data-transfer-copy-template="subject" aria-label="Αντιγραφή θέματος">Αντιγραφή</button></div><p class="transfer-submission-template__value" data-transfer-template-value="subject">'+escape(tpl.subject)+'</p></div>'+
  '<div class="transfer-submission-template__item"><div class="transfer-submission-template__bar"><strong>Σώμα κειμένου:</strong><button type="button" class="secondary" data-transfer-copy-template="body" aria-label="Αντιγραφή σώματος κειμένου">Αντιγραφή</button></div><p class="transfer-submission-template__value" data-transfer-template-value="body">'+escape(tpl.body)+'</p></div>'+
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
 if(typeof navigator!=='undefined'&&navigator.clipboard&&navigator.clipboard.writeText){
  navigator.clipboard.writeText(copied).then(function(){
   if(feedback)feedback.textContent=/\[[^\]]+\]/.test(copied)?'Αντιγράφηκε. Συμπληρώστε τα πεδία που απομένουν στις αγκύλες.':'Το κείμενο αντιγράφηκε.';
  },function(){if(feedback)feedback.textContent=legacyCopySubmission(copied)?'Το κείμενο αντιγράφηκε.':'Δεν ήταν δυνατή η αντιγραφή. Επιλέξτε το κείμενο χειροκίνητα.';});
 }else if(feedback){
  feedback.textContent=legacyCopySubmission(copied)?'Το κείμενο αντιγράφηκε.':'Επιλέξτε το κείμενο και αντιγράψτε το χειροκίνητα.';
 }
}
function renderSubmissionGuidance(){
 var target=$('transferSubmissionGuidanceBody');
 if(!target)return;
 var details=target.querySelector&&target.querySelector('[data-transfer-personalize]');
 var expanded=!!(details&&details.open);
 var data=submissionGuideData();
 data.mode=$('mode')?$('mode').value:'region';
 target.innerHTML='<p><strong>'+escape(data.title)+'</strong></p>'+data.lines.map(function(line){
  return '<p>'+escape(line).replace(/(teachers\.minedu\.gov\.gr|mydocs\.minedu\.gov\.gr)/g,function(host){
   return '<a href="https://'+host+'/" target="_blank" rel="noopener noreferrer">'+host+' ↗</a>';
  })+'</p>';
 }).join('')+submissionMessageHtml(data,expanded);
}
function render(){
 renderSubmissionGuidance();
 var root=$('transferDocumentChecklist');if(!root)return;
 root.querySelectorAll('details[data-doc-details]').forEach(function(details){
  openRows[details.getAttribute('data-doc-details')]=details.open;
 });
 var rows=build();
 root.innerHTML=rows.map(function(d){
  var id=escape(d.id),full=legalSeparation(d.detail),open=Object.prototype.hasOwnProperty.call(openRows,d.id)?openRows[d.id]:isCritical(d);
  var warning=isCritical(d),heading=escape(d.title),detail=escape(full.body).replace(/\n/g,'<br>');
  if(d.id==='record')detail=detail.replace('Μονάδες Συνθηκών Διαβίωσης (Μ.Σ.Δ.):','<strong>Μονάδες Συνθηκών Διαβίωσης (Μ.Σ.Δ.):</strong>');
  // Avoid repeating the same advice outside and inside its disclosure, including EAE service/routing.
  // v3.22.124: avoid duplicate previews for all five standard Artistic School supporting documents.
  var compact=d.id==='locality' || /-dossier$/.test(d.id) || ['eae-doctorate','eae-masters','eae-two-year','eae-5year','eae-pe11','eae-service','eae-kedasy','art-service','art-cv','art-inventory','art-degrees','art-publications','ic-foreign'].indexOf(d.id)!==-1;
  if(d.disabled)checked[d.id]=false;
  return '<article class="edu-transfer-doc'+(checked[d.id]?' is-complete':'')+(warning?' has-warning':'')+(compact?' is-compact':'')+(d.disabled?' is-unavailable':'')+'" data-doc-card="'+id+'">'+
   '<div class="edu-transfer-doc__head">'+
    '<input class="edu-transfer-doc__check" type="checkbox" id="transferDocCheck-'+id+'" data-doc-id="'+id+'" '+(checked[d.id]?'checked':'')+(d.disabled?' disabled':'')+' aria-label="Ολοκληρώθηκε: '+heading+'">'+
    '<div class="edu-transfer-doc__main">'+
     '<label class="edu-transfer-doc__title" for="transferDocCheck-'+id+'">'+heading+'</label>'+
     (compact?'':'<div class="edu-transfer-doc__category">'+escape(d.tag)+'</div>')+
     (compact?'':'<p class="edu-transfer-doc__summary">'+escape(shortText(d))+'</p>')+
     '<div class="edu-transfer-doc__actions">'+portalLink(d.id)+'</div>'+
     '<details class="edu-transfer-doc__disclosure" data-doc-details="'+id+'" '+(open?'open':'')+'>'+
      '<summary>Περισσότερες πληροφορίες</summary>'+
      '<div class="edu-transfer-doc__body"><div class="edu-transfer-doc__detail">'+detail+'</div>'+
       (full.source?'<p class="edu-transfer-doc__source">'+escape(full.source)+'</p>':'')+
       (d.id==='eae-basis'?eaeChoices(false):'')+
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
 root.querySelectorAll('input[name="eaeDocQualification"]').forEach(function(input){input.addEventListener('change',function(){if(!input.checked)return;var select=$('transferEaeQualification');select.value=input.value;select.dispatchEvent(new Event('change',{bubbles:true}));});});
 progress(root);
}

function print(){var rows=build(),mode=$('mode').selectedOptions[0].textContent,submission=submissionGuideData(),html='<!doctype html><html lang="el"><meta charset="utf-8"><title>Λίστα δικαιολογητικών μετάθεσης</title><style>body{font:14px Arial;max-width:780px;margin:36px auto;line-height:1.5}li{margin-bottom:14px}small{color:#555;font-size:11px}</style><h1>Έλεγχος δικαιολογητικών μετάθεσης</h1><p>'+escape(mode)+'</p>'+(($('mode').value==='intercultural')?'<h2>Σημειώσεις Διαπολιτισμικών</h2><p><strong>Αλληλεπίδραση με άλλες αιτήσεις.</strong> Σε περίπτωση ικανοποίησης μετάθεσης σε ΕΑΕ/ΚΕΔΑΣΥ, Μουσικό ή Καλλιτεχνικό, δεν εξετάζεται η αίτηση για Διαπολιτισμικό. Αν ικανοποιηθεί σε Διαπολιτισμικό, δεν λαμβάνονται υπόψη άλλες αιτήσεις.</p><p><strong>Μετάθεση προς Γενική μέσα στην ίδια περιοχή.</strong> Εκπαιδευτικός με οργανική σε Διαπολιτισμικό που ζητά σχολείο Γενικής στην ίδια περιοχή υποβάλλει αίτηση από περιοχή σε περιοχή, όχι βελτίωσης.</p>':'')+'<h2>Πού αποστέλλονται τα δικαιολογητικά;</h2><p><strong>'+escape(submission.title)+'</strong></p>'+submission.lines.map(function(t){return '<p>'+escape(t)+'</p>';}).join('')+'<h3>Θέμα και σώμα μηνύματος — υπόδειγμα</h3><p>Αντικαταστήστε τα πεδία στις αγκύλες με τα στοιχεία της οριστικοποιημένης αίτησης.</p><p><strong>Θέμα:</strong> '+escape(submissionMessageTemplate().subject)+'</p><p><strong>Σώμα κειμένου:</strong> '+escape(submissionMessageTemplate().body)+'</p><p><small>Εγκύκλιος 138014/Ε2/15-10-2026, §'+escape(submissionMessageTemplate().circular)+'</small></p>'+'<p><strong>Ενδεικτική λίστα — όχι βεβαίωση υποχρεωτικών εγγράφων.</strong> Επαληθεύστε τα πάντα με την εγκύκλιο της περιόδου και τη ΔΔΕ.</p><ul>'+rows.map(function(d){return '<li>'+(d.disabled?'— Δεν μοριοδοτείται':(checked[d.id]?'☑':'☐'))+' <strong>'+escape(d.title)+'</strong><br>'+escape(d.detail).replace(/\n/g,'<br>')+' <small>('+escape(d.tag)+')</small>'+(d.id==='eae-basis'?eaeChoices(true):'')+'</li>';}).join('')+'</ul></html>';var win=global.open('','_blank');if(win){win.document.write(html);win.document.close();win.focus();win.print();}}
function init(){var b=$('transferPrintChecklist');if(b)b.addEventListener('click',print);document.addEventListener('click',copySubmissionTemplate);document.addEventListener('input',function(e){if(onSubmissionPersonalInput(e))return;if(e.target.closest && e.target.closest('#transferDocumentChecklist'))return;if(e.target.closest && e.target.closest('.edu-page-transfer'))render();});document.addEventListener('change',function(e){if(onSubmissionPersonalInput(e))return;if(e.target.closest && e.target.closest('#transferDocumentChecklist'))return;if(e.target.closest && e.target.closest('.edu-page-transfer'))render();});render();}
global.EducationTransferDocuments=Object.freeze({init:init,render:render,build:build,coServiceInfo:coServiceInfo,submissionGuideData:submissionGuideData,renderSubmissionGuidance:renderSubmissionGuidance,submissionMessageTemplate:submissionMessageTemplate});
})(window);
