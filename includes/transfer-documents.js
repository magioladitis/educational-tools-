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
 var branch=$('transferArtBranch').value, orientation=$('transferArtOrientation').value;
 var input={branch:branch,orientation:orientation,organic:$('transferArtOrganic').checked,
   teachingYears:$('transferArtTeachingYears').value,exceptionType:$('transferArtException').value};
 var engine=global.EducationTransferEligibility,result=engine?engine.art(input):null;
 var rows=[
  entry('art-service','Πενταετής εμπειρία και υπηρέτηση οργανικής','Συμπλήρωση υπηρεσιακών προϋποθέσεων έως 31-08-2027. Ελέγξτε τυχόν νόμιμη εξαίρεση από την πενταετία και την υπηρεσιακή καταχώριση μέσω ΔΔΕ.','Προέλεγχος δικαιώματος'),
  entry('art-cv','Αναλυτικό βιογραφικό','Προετοιμάστε πλήρες βιογραφικό που τεκμηριώνει επιμόρφωση, μετεκπαίδευση, διδακτική εμπειρία σε Καλλιτεχνικά, μεταπτυχιακές σπουδές, επιστημονικό έργο και συναφή προσόντα. '+ART_SOURCE,'Φάκελος κρίσης'),
  entry('art-inventory','Πίνακας δικαιολογητικών','Καταγράψτε σε πίνακα τους τίτλους, βεβαιώσεις και λοιπά αποδεικτικά που τεκμηριώνουν τα συνεκτιμώμενα προσόντα· μην περιλάβετε άσχετα έγγραφα.','Σύμφωνα με εγκύκλιο'),
  entry('art-degrees','Τίτλοι σπουδών, επιμόρφωση και διδακτική εμπειρία','Συγκεντρώστε μόνο τα σχετικά τεκμήρια. Για αλλοδαπούς τίτλους σπουδών ελέγξτε αναγνώριση ΔΟΑΤΑΠ/ΔΙΚΑΤΣΑ και, όπου απαιτείται, επίσημη μετάφραση. Η ΔΔΕ ελέγχει τα υπηρεσιακά στοιχεία.','Έλεγχος τίτλων'),
  entry('art-publications','Δημοσιεύσεις, βιβλία και επιστημονικό έργο','Εφόσον δηλώνονται δημοσιεύσεις ή βιβλία, η εγκύκλιος 2026–2027 δέχεται εξώφυλλο και πρώτη σελίδα ή σύνδεσμο της ηλεκτρονικής έκδοσης.','Εφόσον υπάρχουν'),
  entry('art-vacancies','Οργανικό κενό στον κλάδο/ειδικότητα','Ελέγξτε στο teachers.minedu.gov.gr ότι έχει προκηρυχθεί κενή οργανική θέση στον συγκεκριμένο κλάδο/ειδικότητα στο επιλεγμένο Καλλιτεχνικό.','Υποχρεωτικός έλεγχος προτίμησης'),
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
 rows.push(entry('art-no-scoring','Αξιολόγηση προσόντων χωρίς μόρια','Δεν προβλέπεται μοριοδότηση των συνεκτιμώμενων προσόντων ούτε κατάταξη όλων των αιτούντων σε ενιαίο αξιολογικό πίνακα.','Σημαντική επισήμανση'));
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
   entry('ic-vacancies','Έλεγχος οργανικού κενού στο συγκεκριμένο σχολείο','Στο teachers.minedu.gov.gr επιλέγονται μόνο σχολεία με προκηρυγμένες κενές οργανικές θέσεις για τον κλάδο/ειδικότητα.','Έλεγχος προτίμησης'),
   entry('ic-cv','Αναλυτικό βιογραφικό σημείωμα','Πλήρες βιογραφικό, πίνακας δικαιολογητικών και πιστοποιητικά/τίτλοι που αποδεικνύουν τα πραγματικά δηλωμένα προσόντα. Αποφύγετε άσχετα έγγραφα.','Φάκελος προσόντων'),
   entry('ic-qual-order','Προτεραιότητα προσόντων χωρίς αριθμητική κατάταξη','Η εγκύκλιος απαριθμεί εννέα πρόσθετα προσόντα με σειρά προτεραιότητας. Η επιλογή προσόντος δεν εξασφαλίζει μετάθεση και δεν αποδίδει αριθμητικά μόρια.','Κανόνας κρίσης')
 ];
 if(state&&state.languageException)rows.push(entry('ic-roma','Εξαίρεση από τη γνώση γλώσσας προέλευσης','Για σχολεία στα οποία φοιτούν κατά πλειοψηφία μαθητές Ρομά δεν απαιτείται η γνώση της ξένης γλώσσας προέλευσης που αναφέρει η πρώτη προτίμηση προσόντων.','Ειδική περίπτωση'));
 chosen.forEach(function(id){
   var q=qs.find(function(x){return x.id===id;});
   if(q)rows.push(entry('ic-qual-'+id,'Τεκμηρίωση: '+q.title,q.proof,'Επιλεγμένο πρόσθετο προσόν'));
 });
 if(!chosen.length)rows.push(entry('ic-none','Δεν επιλέχθηκαν πρόσθετα προσόντα','Ελέγξτε αν διαθέτετε κάποιο από τα εννέα προτιμώμενα προσόντα· δεν αποτελούν εννέα υποχρεωτικά δικαιολογητικά για όλους.','Προσωπικός έλεγχος'));
 rows.push(entry('ic-foreign','Ξενόγλωσσοι τίτλοι και δημοσιεύσεις','Για τίτλους αλλοδαπής ελέγξτε αναγνώριση ΔΟΑΤΑΠ/ΔΙΚΑΤΣΑ και επίσημη μετάφραση όπου απαιτείται. Για δημοσιεύσεις/βιβλία αρκεί αντίγραφο εξωφύλλου και πρώτης σελίδας. Η εγκύκλιος εξαιρεί τίτλους γλωσσομάθειας συγκεκριμένων γλωσσών από την απαίτηση μετάφρασης.','Ανά περίπτωση'));
 rows.push(dossierRow('intercultural',$('transferInterculturalPreviousYear').value,$('transferInterculturalNewDocuments').checked));
 rows.push(entry('ic-order','Αλληλεπίδραση με άλλες αιτήσεις','Σε περίπτωση ικανοποίησης της μετάθεσης σε ΕΑΕ/ΚΕΔΑΣΥ, Μουσικό ή Καλλιτεχνικό, δεν εξετάζεται αίτηση για Διαπολιτισμικό. Αν ικανοποιηθεί σε Διαπολιτισμικό, δεν λαμβάνονται υπόψη άλλες αιτήσεις.','Σειρά εξέτασης'));
 rows.push(entry('ic-same-area','Μετάθεση προς Γενική μέσα στην ίδια περιοχή','Όποιος έχει οργανική σε Διαπολιτισμικό και ζητά σχολείο Γενικής στην ίδια περιοχή υποβάλλει αίτηση μετάθεσης «από περιοχή σε περιοχή», όχι βελτίωσης.','Προσοχή στον τύπο αίτησης'));
 return rows;
}
function build(){
 var mode=$('mode').value, qual=mode==='music'||mode==='art'||mode==='intercultural';
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
    'ΑΝΑΠΗΡΙΑ ΤΕΚΝΟΥ: οι ιατρικές γνωματεύσεις αφορούν ενδεχόμενη ξεχωριστή ειδική κατηγορία/προτεραιότητα, όχι αυτομάτως τις συνήθεις μονάδες τέκνων. ΠΟΛΥΤΕΚΝΟΙ: ελέγξτε ειδικά ηλικιακά όρια του άρθρου 6 ν. 3454/2006.\n'+
    'ΝΕΟΓΕΝΝΗΤΟ: κατ’ εξαίρεση η ληξιαρχική πράξη γέννησης μπορεί να υποβληθεί έως το τέλος Δεκεμβρίου 2026· για γέννηση στις 31/12 προβλέπεται ειδική άμεση δήλωση στη ΔΔΕ και μεταγενέστερη προσκόμιση ληξιαρχικής πράξης.\n'+
    'Τα δικαιολογητικά οικογενειακών λόγων εκδίδονται κατά κανόνα στο τελευταίο τρίμηνο. Το πιστοποιητικό οικογενειακής κατάστασης ελληνικού δημοτολογίου αναζητείται αυτεπάγγελτα. Για έγγραφα αλλοδαπών αρχών ισχύουν οι ειδικές οδηγίες της εγκυκλίου. '+FAMILY_SOURCE,
    'Πρόσθετα μόνο εφόσον απαιτούνται'));
  }
  if($('coService').checked){
   var caseSelect=$('transferCoServiceCase');
   var areaMode=mode==='smeae' && $('transferEaeArea').value==='same'?'local':mode;
   var info=coServiceInfo(caseSelect?caseSelect.value:'unspecified',areaMode);
   a.push(entry('coservice','Συνυπηρέτηση — δικαιολογητικά ανά καθεστώς',
    info.summary+'\n'+info.detail+'\nΤα απαιτούμενα αποδεικτικά συνυπηρέτησης πρέπει να έχουν εκδοθεί στο τελευταίο τρίμηνο (με εξαίρεση βεβαιώσεις εργοδοτών για προγενέστερη εργασία που έπαυσε: τελευταία διετία). Η συνυπηρέτηση αφορά μία περιοχή μετάθεσης — όχι ολόκληρο τον νομό — ή τον δήμο σχολείου για βελτίωση/οριστική τοποθέτηση. '+FAMILY_SOURCE,
    info.blocked?'Δεν θεμελιώνεται συνυπηρέτηση (+0)':'Δικαιολογητικά βάσει επιλογής'));
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
 if(mode==='smeae') a=a.concat(eaeDocuments());
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
 return d.id==='eae-warnings' || d.id==='ic-same-area' ||
   (d.id==='coservice' && d.tag.indexOf('Δεν θεμελιώνεται')!==-1);
}
function progress(root){
 var rows=root.querySelectorAll('[data-doc-id]');
 var complete=0;
 rows.forEach(function(input){if(input.checked)complete++;});
 var target=$('transferDocumentProgress');
 if(target)target.textContent='Έλεγχοι που σημειώθηκαν: '+complete+' από '+rows.length+'.';
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
  title:'Μετάθεση στην ΕΑΕ — δύο διαδρομές',
  lines:[
   'Δικαιολογητικά μοριοδότησης (π.χ. οικογενειακοί λόγοι, συνυπηρέτηση): στη ΔΔΕ οργανικής, με τις προθεσμίες της εγκυκλίου.',
   'Δικαιολογητικά προσόντων/κριτηρίων ΕΑΕ και βιογραφικό: μέσω mydocs.minedu.gov.gr, όταν απαιτείται νέος ή συμπληρωματικός φάκελος.',
   'Προηγούμενος ηλεκτρονικός φάκελος μπορεί να απαλλάσσει από εκ νέου αποστολή των ίδιων στοιχείων· η νέα ηλεκτρονική αίτηση μετάθεσης υποβάλλεται κάθε χρόνο.'
  ]
 },
 music:{
  title:'Μετάθεση σε Μουσικό Σχολείο',
  lines:[
   'Η αίτηση μετάθεσης υποβάλλεται στο teachers.minedu.gov.gr.',
   'Αν απαιτείται νέος ή συμπληρωματικός φάκελος προσόντων/βιογραφικού, ακολουθήστε την προβλεπόμενη υποβολή μέσω mydocs.minedu.gov.gr. Ελέγξτε πρώτα την ισχύ προηγούμενου ηλεκτρονικού φακέλου.',
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
function renderSubmissionGuidance(){
 var target=$('transferSubmissionGuidanceBody');
 if(!target)return;
 var data=submissionGuideData();
 target.innerHTML='<p><strong>'+escape(data.title)+'</strong></p>'+data.lines.map(function(line){
  return '<p>'+escape(line).replace(/(teachers\.minedu\.gov\.gr|mydocs\.minedu\.gov\.gr)/g,function(host){
   return '<a href="https://'+host+'/" target="_blank" rel="noopener noreferrer">'+host+' ↗</a>';
  })+'</p>';
 }).join('');
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
  var compact=d.id==='locality' || /-dossier$/.test(d.id) || ['eae-doctorate','eae-masters','eae-two-year','eae-5year','eae-pe11','eae-service'].indexOf(d.id)!==-1;
  return '<article class="edu-transfer-doc'+(checked[d.id]?' is-complete':'')+(warning?' has-warning':'')+(compact?' is-compact':'')+'" data-doc-card="'+id+'">'+
   '<div class="edu-transfer-doc__head">'+
    '<input class="edu-transfer-doc__check" type="checkbox" id="transferDocCheck-'+id+'" data-doc-id="'+id+'" '+(checked[d.id]?'checked':'')+' aria-label="Ολοκληρώθηκε: '+heading+'">'+
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

function print(){var rows=build(),mode=$('mode').selectedOptions[0].textContent,submission=submissionGuideData(),html='<!doctype html><html lang="el"><meta charset="utf-8"><title>Λίστα δικαιολογητικών μετάθεσης</title><style>body{font:14px Arial;max-width:780px;margin:36px auto;line-height:1.5}li{margin-bottom:14px}small{color:#555;font-size:11px}</style><h1>Έλεγχος δικαιολογητικών μετάθεσης</h1><p>'+escape(mode)+'</p><h2>Πού αποστέλλονται τα δικαιολογητικά;</h2><p><strong>'+escape(submission.title)+'</strong></p>'+submission.lines.map(function(t){return '<p>'+escape(t)+'</p>';}).join('')+'<p><strong>Ενδεικτική λίστα — όχι βεβαίωση υποχρεωτικών εγγράφων.</strong> Επαληθεύστε τα πάντα με την εγκύκλιο της περιόδου και τη ΔΔΕ.</p><ul>'+rows.map(function(d){return '<li>'+(checked[d.id]?'☑':'☐')+' <strong>'+escape(d.title)+'</strong><br>'+escape(d.detail).replace(/\n/g,'<br>')+' <small>('+escape(d.tag)+')</small>'+(d.id==='eae-basis'?eaeChoices(true):'')+'</li>';}).join('')+'</ul></html>';var win=global.open('','_blank');if(win){win.document.write(html);win.document.close();win.focus();win.print();}}
function init(){var b=$('transferPrintChecklist');if(b)b.addEventListener('click',print);document.addEventListener('input',function(e){if(e.target.closest && e.target.closest('#transferDocumentChecklist'))return;if(e.target.closest && e.target.closest('.edu-page-transfer'))render();});document.addEventListener('change',function(e){if(e.target.closest && e.target.closest('#transferDocumentChecklist'))return;if(e.target.closest && e.target.closest('.edu-page-transfer'))render();});render();}
global.EducationTransferDocuments=Object.freeze({init:init,render:render,build:build,coServiceInfo:coServiceInfo,submissionGuideData:submissionGuideData,renderSubmissionGuidance:renderSubmissionGuidance});
})(window);
