/* Transfer document preparation guide. Advisory only; no personal data transmitted. */
(function(global){
'use strict';
var $=function(id){return document.getElementById(id);};
var base=[
 ['submission','Οριστικοποίηση ηλεκτρονικής αίτησης','Η ηλεκτρονική αίτηση υποβάλλεται και οριστικοποιείται στο https://teachers.minedu.gov.gr/. Ελέγξτε την οριστική υποβολή, το αποδεικτικό και τις προθεσμίες της τρέχουσας εγκυκλίου.','Έλεγχος διαδικασίας'],
 ['record','Υπηρεσιακό μητρώο και χρόνος υπηρεσίας','Δείτε υπηρετήσεις και μόρια στο https://teachers.minedu.gov.gr/. Αντιπαραβάλετε τις καταχωρίσεις και την αναγνωρισμένη προϋπηρεσία· ζητήστε διόρθωση μέσω ΔΔΕ όπου χρειάζεται.','Έλεγχος φακέλου']
];
var special={};
var EAE_SOURCE='Εγκύκλιος 129787/Ε2/15-10-2025, κεφάλαιο Δ΄, σελ. 34–37.';
var MUSIC_SOURCE='Εγκύκλιος 129787/Ε2/15-10-2025, κεφάλαιο Ε΄, σελ. 38–39.';
var ART_SOURCE='Εγκύκλιος 129787/Ε2/15-10-2025, κεφάλαιο ΣΤ΄, σελ. 40–42.';
function dossierRow(kind,year,newElements){
 var calc=global.EducationTransferEligibility;
 var state=calc&&calc.dossier?calc.dossier({kind:kind,year:year,newElements:newElements}):null;
 return entry(kind+'-dossier','Έλεγχος προηγούμενου ηλεκτρονικού φακέλου',
   state?state.status+' '+state.limitation:'Ελέγξτε τον φάκελο με την εγκύκλιο του έτους υποβολής.',
   state&&state.skipResubmission?'Μη επανυποβολή φακέλου (βάση 2025–2026)':'Έλεγχος υποβολής');
}
var checked={};
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
 rows.push(entry('eae-service','Υπηρεσιακή προϋπόθεση μετάθεσης','Ελέγξτε την τριετή διδακτική υπηρεσία (Γενική → ΕΑΕ), τη διετία στην περιοχή διορισμού όπου απαιτείται ή τις ειδικές υποχρεώσεις ήδη υπηρετούντων στην ΕΑΕ.','Έλεγχος μητρώου'));
 if(inp.destination==='deaf')rows.push(entry('eae-sign','Σχολείο Κωφών — πιστοποίηση ΕΝΓ','Απαιτείται πιστοποιημένη επάρκεια στην Ελληνική Νοηματική Γλώσσα από αποδεκτό φορέα κατά την εγκύκλιο· απλή παρακολούθηση δεν αρκεί.','Ειδικό αποδεικτικό'));
 if(inp.destination==='blind')rows.push(entry('eae-braille','Σχολείο Τυφλών — πιστοποίηση Braille','Απαιτείται πιστοποιημένη γνώση γραφής Braille από αποδεκτό φορέα κατά την εγκύκλιο· απλή παρακολούθηση δεν αρκεί.','Ειδικό αποδεικτικό'));
 if(result&&result.advice.length)rows.push(entry('eae-warnings','Σημεία που απαιτούν προσοχή',result.advice.join(' '),'Προέλεγχος — όχι απόφαση'));
 rows.push(dossierRow('eae',$('transferEaePreviousYear').value,$('transferEaeNewDocuments').checked));
 rows.push(entry('eae-routing','Διαχωρίστε δικαιολογητικά μοριοδότησης από δικαιολογητικά προσόντων','Στον κύκλο 2025–2026: δικαιολογητικά μοριοδότησης στη ΔΔΕ οργανικής· προσόντα/κριτήρια ΕΑΕ μέσω mydocs.minedu.gov.gr. Η νέα αίτηση υποβάλλεται κάθε χρόνο.','Δύο διαδρομές υποβολής'));
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
  entry('art-service','Πενταετής εμπειρία και υπηρέτηση οργανικής','Για τον κύκλο 2025–2026: συμπλήρωση υπηρεσιακών προϋποθέσεων έως 31-08-2026. Ελέγξτε τυχόν νόμιμη εξαίρεση από την πενταετία και την υπηρεσιακή καταχώριση μέσω ΔΔΕ.','Προέλεγχος δικαιώματος'),
  entry('art-cv','Αναλυτικό βιογραφικό','Προετοιμάστε πλήρες βιογραφικό που τεκμηριώνει επιμόρφωση, μετεκπαίδευση, διδακτική εμπειρία σε Καλλιτεχνικά, μεταπτυχιακές σπουδές, επιστημονικό έργο και συναφή προσόντα. '+ART_SOURCE,'Φάκελος κρίσης'),
  entry('art-inventory','Πίνακας δικαιολογητικών','Καταγράψτε σε πίνακα τους τίτλους, βεβαιώσεις και λοιπά αποδεικτικά που τεκμηριώνουν τα συνεκτιμώμενα προσόντα· μην περιλάβετε άσχετα έγγραφα.','Σύμφωνα με εγκύκλιο'),
  entry('art-degrees','Τίτλοι σπουδών, επιμόρφωση και διδακτική εμπειρία','Συγκεντρώστε μόνο τα σχετικά τεκμήρια. Για αλλοδαπούς τίτλους σπουδών ελέγξτε αναγνώριση ΔΟΑΤΑΠ/ΔΙΚΑΤΣΑ και, όπου απαιτείται, επίσημη μετάφραση. Η ΔΔΕ ελέγχει τα υπηρεσιακά στοιχεία.','Έλεγχος τίτλων'),
  entry('art-publications','Δημοσιεύσεις, βιβλία και επιστημονικό έργο','Εφόσον δηλώνονται δημοσιεύσεις ή βιβλία, η εγκύκλιος 2025–2026 δέχεται εξώφυλλο και πρώτη σελίδα ή σύνδεσμο της ηλεκτρονικής έκδοσης.','Εφόσον υπάρχουν'),
  entry('art-vacancies','Οργανικό κενό στον κλάδο/ειδικότητα','Ελέγξτε στο teachers.minedu.gov.gr ότι έχει προκηρυχθεί κενή οργανική θέση στον συγκεκριμένο κλάδο/ειδικότητα στο επιλεγμένο Καλλιτεχνικό.','Υποχρεωτικός έλεγχος προτίμησης'),
  dossierRow('art',$('transferArtPreviousYear').value,$('transferArtNewDocuments').checked),
  entry('art-submit','Υποβολή νέου φακέλου όταν απαιτείται','Για τον κύκλο 2025–2026: mydocs.minedu.gov.gr, μία αποστολή για βιογραφικό/δικαιολογητικά, αρχεία PDF ασπρόμαυρα 200/300 dpi, εντός προθεσμίας. Δεν γίνονται δεκτά έντυπα, εκπρόθεσμα ή διαφορετική διαδρομή υποβολής.','Διαδικασία 2025–2026')
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
function build(){
 var mode=$('mode').value, qual=mode==='music'||mode==='art';
 var a=base.map(function(x){return entry.apply(null,x);});
 if(!qual){
  if((+$('serviceYears').value)|| (+$('serviceMonths').value)||(+ $('serviceDays').value)) a.push(entry('service','Πράξεις αναγνώρισης και βεβαιώσεις υπηρετήσεων','Ελέγξτε αν οι υπηρεσίες και τυχόν αναγνωρίσεις έχουν ήδη καταχωριστεί.','Έλεγχος φακέλου'));
  if($('familyStatusEligible').checked || +$('eligibleChildren').value>0) a.push(entry('family','Οικογενειακή κατάσταση / τέκνα','Ελέγξτε πιστοποιητικό οικογενειακής κατάστασης, ηλικία/ιδιότητα τέκνων και τυχόν ειδικά αποδεικτικά. Διαπιστώστε αν αναζητούνται αυτεπάγγελτα.','Έλεγχος αναζήτησης'));
  if($('coService').checked) a.push(entry('coservice','Συνυπηρέτηση','Ελέγξτε βεβαίωση εργασίας ή υπηρεσίας συζύγου/συμβίου, τόπο απασχόλησης και τυχόν διάρκεια ή πρόσθετα έγγραφα ανά καθεστώς.','Πιθανό δικαιολογητικό'));
  if($('locality').checked) a.push(entry('locality','Εντοπιότητα','Ελέγξτε βεβαίωση δημοτικότητας, χρόνο κτήσης της και αντιστοίχιση με τη ζητούμενη περιοχή ή τον δήμο.','Πιθανό δικαιολογητικό'));
  if($('msdMode').value==='direct') a.push(entry('msd-direct','Μ.Σ.Δ. — απευθείας μεταφορά μορίων από το Μητρώο','Αντιγράψτε τον ακριβή συνολικό αριθμό Μ.Σ.Δ. από το https://teachers.minedu.gov.gr/. Δεν αθροίζονται ξανά οι αναλυτικές υπηρετήσεις.','Διασταύρωση με Μητρώο'));
  else if(document.querySelectorAll('.transfer-service-row').length) a.push(entry('msd','Μονάδες Συνθηκών Διαβίωσης','Ελέγξτε υπηρετήσεις ανά σχολείο/κατηγορία, αποσπάσεις, παράλληλη υπηρεσία και προσαυξήσεις με το Μητρώο στο https://teachers.minedu.gov.gr/.','Έλεγχος φακέλου'));
 }
 if(mode==='region' && global.EducationTransferEligibility && global.EducationTransferEligibility.returnToGeneral){
  var rr=global.EducationTransferEligibility.returnToGeneral({
   origin:$('transferGeneralOrigin').value,eaeYears:$('transferGeneralEaeYears').value,
   areaYears:$('transferGeneralEaeAreaYears').value,sinceTransferYears:$('transferGeneralEaeSinceTransfer').value,
   organicServed:$('transferGeneralEaeOrganic').checked});
  if(rr.applicable) a.push(entry('eae-to-general','Ειδική Αγωγή → Γενική: θεμελίωση δικαιώματος',
    rr.status+' '+(rr.advice.length?rr.advice.join(' '):'Ελέγξτε τις υπηρετήσεις και την οργανική με τη ΔΔΕ.')+' Βάση: εγκύκλιος 129787/Ε2/15-10-2025, σελ. 11 §7. Ο έλεγχος της εγκυκλίου 2026–2027 εκκρεμεί.',
    'Προέλεγχος δικαιώματος'));
 }
 if(mode==='smeae') a=a.concat(eaeDocuments());
 if(mode==='music') a=a.concat(musicDocuments());
 if(mode==='art') a=a.concat(artDocuments());
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
function portalLink(id){return ['submission','record','msd','msd-direct'].indexOf(id)!==-1?' <a href="https://teachers.minedu.gov.gr/" target="_blank" rel="noopener noreferrer">Άνοιγμα teachers.minedu.gov.gr ↗</a>':'';}
function render(){
 var root=$('transferDocumentChecklist');if(!root)return;
 var rows=build();
 root.innerHTML=rows.map(function(d){return '<div style="margin-bottom:12px;padding:10px 0;border-bottom:1px solid #d9dfe6"><label style="display:flex;gap:8px;align-items:flex-start"><input type="checkbox" data-doc-id="'+escape(d.id)+'" '+(checked[d.id]?'checked':'')+' style="margin-top:5px"><span><strong>'+escape(d.title)+'</strong><small style="display:block;opacity:.8">'+escape(d.tag)+'</small><span style="display:block;margin-top:3px">'+escape(d.detail)+portalLink(d.id)+'</span></span></label>'+(d.id==='eae-basis'?eaeChoices(false):'')+'</div>';}).join('');
 root.querySelectorAll('[data-doc-id]').forEach(function(input){input.addEventListener('change',function(){checked[input.dataset.docId]=input.checked;});});
 root.querySelectorAll('input[name="eaeDocQualification"]').forEach(function(input){input.addEventListener('change',function(){if(!input.checked)return;var select=$('transferEaeQualification');select.value=input.value;select.dispatchEvent(new Event('change',{bubbles:true}));});});
}
function print(){var rows=build(),mode=$('mode').selectedOptions[0].textContent,html='<!doctype html><html lang="el"><meta charset="utf-8"><title>Λίστα δικαιολογητικών μετάθεσης</title><style>body{font:15px Arial;max-width:780px;margin:36px auto;line-height:1.5}li{margin-bottom:16px}small{color:#555}</style><h1>Έλεγχος δικαιολογητικών μετάθεσης</h1><p>'+escape(mode)+'</p><p><strong>Ενδεικτική λίστα — όχι βεβαίωση υποχρεωτικών εγγράφων.</strong> Επαληθεύστε τα πάντα με την εγκύκλιο της περιόδου και τη ΔΔΕ.</p><ul>'+rows.map(function(d){return '<li>'+(checked[d.id]?'☑':'☐')+' <strong>'+escape(d.title)+'</strong><br>'+escape(d.detail)+' <small>('+escape(d.tag)+')</small>'+(d.id==='eae-basis'?eaeChoices(true):'')+'</li>';}).join('')+'</ul></html>';var win=global.open('','_blank');if(win){win.document.write(html);win.document.close();win.focus();win.print();}}
function init(){var b=$('transferPrintChecklist');if(b)b.addEventListener('click',print);document.addEventListener('input',function(e){if(e.target.closest && e.target.closest('#transferDocumentChecklist'))return;if(e.target.closest && e.target.closest('.edu-page-transfer'))render();});document.addEventListener('change',function(e){if(e.target.closest && e.target.closest('#transferDocumentChecklist'))return;if(e.target.closest && e.target.closest('.edu-page-transfer'))render();});render();}
global.EducationTransferDocuments=Object.freeze({init:init,render:render,build:build});
})(window);
