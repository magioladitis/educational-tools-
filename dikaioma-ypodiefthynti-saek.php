<?php require_once __DIR__ . '/includes/config.php'; ?>
<!DOCTYPE html>
<html lang="el">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="Ενδεικτικός έλεγχος δικαιώματος υποψηφιότητας για θέσεις Υποδιευθυντών δημόσιων ΣΑΕΚ 2026.">
<title>Έχω δικαίωμα υποψηφιότητας για Υποδιευθυντής ΣΑΕΚ;</title>
<link rel="stylesheet" href="<?php echo htmlspecialchars(edu_asset_url('assets/common.css'), ENT_QUOTES, 'UTF-8'); ?>">
</head>
<body class="edu-ui edu-guide-standard edu-guide-eligibility">
<?php require_once __DIR__ . '/includes/header.php'; ?>
<div class="app-box edu-modernized">
<section class="hero edu-legacy-hero">
<h1>Έχω δικαίωμα υποψηφιότητας για Υποδιευθυντής Σ.Α.Ε.Κ.;</h1>
<p class="intro">Ενδεικτικός έλεγχος των προϋποθέσεων της Πρόσκλησης <strong>Κ5/113585/01-09-2026</strong> για τις κενές θέσεις Υποδιευθυντών δημόσιων Σ.Α.Ε.Κ. Η αίτηση υποβάλλεται μόνο στη Σ.Α.Ε.Κ. στην οποία υπηρετεί ο/η υποψήφιος/α.</p>
</section>
<p class="intro">Απάντησε στις ερωτήσεις. Το εργαλείο ελέγχει τις ρητές προϋποθέσεις και τα κωλύματα της πρόσκλησης· δεν πραγματοποιεί μοριοδότηση.</p>

<div class="progress-panel" aria-live="polite"><div class="progress-head"><span>Πρόοδος συμπλήρωσης</span><span id="progressText">0/7 απαντήσεις</span></div><div class="progress-track" aria-hidden="true"><div id="progressFill" class="progress-fill"></div></div></div>

<div class="question"><label for="saek">Σ.Α.Ε.Κ. στην οποία υπηρετείς</label><select id="saek"><option value="">-- Επιλογή --</option>
<option>Σ.Α.Ε.Κ. Δράμας</option><option>Θεματική Σ.Α.Ε.Κ. Αιγάλεω</option><option>Θεματική Σ.Α.Ε.Κ. Αχαρνών</option><option>Σ.Α.Ε.Κ. Γαλατσίου</option><option>Σ.Α.Ε.Κ. Δάφνης-Υμηττού</option><option>Σ.Α.Ε.Κ. Κηφισιάς</option><option>Σ.Α.Ε.Κ. Σαλαμίνας</option><option>Σ.Α.Ε.Κ. Χαλανδρίου</option><option>Σ.Α.Ε.Κ. Χίου</option><option>Σ.Α.Ε.Κ. Καστοριάς</option><option>Σ.Α.Ε.Κ. Άρτας</option><option>Σ.Α.Ε.Κ. Ηγουμενίτσας</option><option>Σ.Α.Ε.Κ. Ιωαννίνων</option><option>Σ.Α.Ε.Κ. Κόνιτσας</option><option>Πειραματική Σ.Α.Ε.Κ. Καρδίτσας</option><option>Σ.Α.Ε.Κ. Κεφαλληνίας</option><option>Σ.Α.Ε.Κ. Λευκάδας</option><option>Πειραματική Σ.Α.Ε.Κ. Βέροιας</option><option>Πειραματική Σ.Α.Ε.Κ. Θέρμης</option><option>Σ.Α.Ε.Κ. Χανίων</option><option>Σ.Α.Ε.Κ. Θήρας</option><option>Σ.Α.Ε.Κ. Νάξου</option><option>Σ.Α.Ε.Κ. Μεγαλόπολης</option><option>Σ.Α.Ε.Κ. Σπάρτης</option><option>Σ.Α.Ε.Κ. Στεμνίτσας</option><option>Σ.Α.Ε.Κ. Λιβαδειάς</option><option value="other">Άλλη Σ.Α.Ε.Κ.</option></select></div>

<div class="question"><label for="status">Με ποια ιδιότητα υπηρετείς στην παραπάνω Σ.Α.Ε.Κ.;</label><select id="status"><option value="">-- Επιλογή --</option><option value="eligible">Μόνιμος/η εκπαιδευτικός Σ.Α.Ε.Κ.</option><option value="eligible">Διοικητικός/ή υπάλληλος με οποιαδήποτε σχέση εργασίας</option><option value="eligible">Αποσπασμένος/η εκπαιδευτικός για διοικητικό έργο στη Σ.Α.Ε.Κ.</option><option value="no">Δεν ανήκω σε κάποια από τις παραπάνω κατηγορίες</option></select></div>

<div class="question"><label for="requiredDegree">Διαθέτεις τον απαιτούμενο τίτλο ανώτατης εκπαίδευσης (ή αναγνωρισμένο ακαδημαϊκά ισοδύναμο/ισότιμο τίτλο αλλοδαπής);</label><select id="requiredDegree"><option value="">-- Επιλογή --</option><option value="yes">Ναι</option><option value="no">Όχι</option><option value="unknown">Δεν είμαι σίγουρος/η</option></select></div>

<div class="question"><label for="experience">Έχεις τουλάχιστον δύο (2) έτη διοικητικής εμπειρίας ή εκπαιδευτικής υπηρεσίας στην επαγγελματική εκπαίδευση ή κατάρτιση;</label><select id="experience"><option value="">-- Επιλογή --</option><option value="yes">Ναι</option><option value="no">Όχι</option><option value="unknown">Δεν είμαι σίγουρος/η</option></select></div>

<div class="question"><label for="evaluationRefusal">Έχεις ασκήσει καθήκοντα Διευθυντή/Υποδιευθυντή δημόσιας Σ.Α.Ε.Κ. και εμπίπτεις στον εξαετή αποκλεισμό λόγω άρνησης ή παρακώλυσης της προβλεπόμενης αξιολόγησης;</label><select id="evaluationRefusal"><option value="">-- Επιλογή --</option><option value="no">Όχι</option><option value="yes">Ναι</option><option value="unknown">Δεν είμαι σίγουρος/η</option></select></div>

<div class="question"><label for="unsuitable">Έχει αξιολογηθεί το έργο σου ως «ακατάλληλο» και βρίσκεσαι ακόμη μέσα στον προβλεπόμενο τριετή αποκλεισμό;</label><select id="unsuitable"><option value="">-- Επιλογή --</option><option value="no">Όχι</option><option value="yes">Ναι</option><option value="unknown">Δεν είμαι σίγουρος/η</option></select></div>

<div class="question"><label for="retirement">Αποχωρείς υποχρεωτικά από την υπηρεσία λόγω συνταξιοδότησης έως και τις 10/09/2027;</label><select id="retirement"><option value="">-- Επιλογή --</option><option value="no">Όχι</option><option value="yes">Ναι</option><option value="unknown">Δεν είμαι σίγουρος/η</option></select></div>

<div class="button-row"><button type="button" onclick="checkEligibility()">Έλεγχος δικαιώματος υποψηφιότητας</button><button type="button" class="reset-button" onclick="resetForm()">Μηδενισμός</button></div>
<div id="result" class="result" role="status" aria-live="polite"></div>
<p class="small-note">Η πρόσκληση αναφέρει επίσης ότι ο υποψήφιος πρέπει να διαθέτει γνώση και εμπειρία σε διδακτικές μεθόδους επαγγελματικής εκπαίδευσης και κατάρτισης και στην επιμόρφωση εκπαιδευτών, καθώς και ικανότητα παρακολούθησης/εποπτείας της εκπαιδευτικής διαδικασίας και συντονισμού της αξιολόγησης. Τα στοιχεία αυτά δεν μετατρέπονται εδώ σε αυτόματο αριθμητικό κριτήριο, επειδή η πρόσκληση δεν ορίζει σχετική μοριοδότηση.</p>
</div>
<script>
const fieldIds=['saek','status','requiredDegree','experience','evaluationRefusal','unsuitable','retirement'];
function valueOf(id){return document.getElementById(id).value;}
function showResult(message,cssClass){const r=document.getElementById('result');r.style.display='block';r.className='result '+cssClass;r.innerHTML=message;}
function updateProgress(){const done=fieldIds.filter(id=>valueOf(id)!=='').length;document.getElementById('progressText').textContent=done+'/7 απαντήσεις';document.getElementById('progressFill').style.width=(done/7*100)+'%';}
fieldIds.forEach(id=>document.getElementById(id).addEventListener('change',updateProgress));
function resetForm(){fieldIds.forEach(id=>document.getElementById(id).value='');const r=document.getElementById('result');r.style.display='none';r.innerHTML='';updateProgress();}
function checkEligibility(){
 const vals=fieldIds.map(valueOf); if(vals.includes('')){showResult('Συμπλήρωσε πρώτα όλες τις ερωτήσεις.','unknown');return;}
 if(valueOf('saek')==='other'){showResult('Η Σ.Α.Ε.Κ. που δήλωσες δεν περιλαμβάνεται στις 26 Σ.Α.Ε.Κ. με κενές θέσεις της συγκεκριμένης πρόσκλησης. Δεν μπορείς να υποβάλεις αίτηση στο πλαίσιο αυτής της πρόσκλησης.','not-eligible');return;}
 if(valueOf('status')==='no'){showResult('Δεν προκύπτει δικαίωμα υποβολής αίτησης: η πρόσκληση περιορίζει τους υποψηφίους σε όσους υπηρετούν στην οικεία Σ.Α.Ε.Κ. με μία από τις προβλεπόμενες ιδιότητες.','not-eligible');return;}
 if(valueOf('requiredDegree')==='no'){showResult('Δεν προκύπτει δικαίωμα υποβολής αίτησης, επειδή δεν δηλώθηκε ο απαιτούμενος τίτλος ανώτατης εκπαίδευσης.','not-eligible');return;}
 if(valueOf('experience')==='no'){showResult('Δεν προκύπτει δικαίωμα υποβολής αίτησης: απαιτούνται τουλάχιστον δύο (2) έτη διοικητικής εμπειρίας ή εκπαιδευτικής υπηρεσίας στην επαγγελματική εκπαίδευση ή κατάρτιση.','not-eligible');return;}
 if(valueOf('evaluationRefusal')==='yes'){showResult('Δεν προκύπτει δικαίωμα συμμετοχής, επειδή δηλώθηκε ενεργός εξαετής αποκλεισμός που συνδέεται με άρνηση ή παρακώλυση της αξιολόγησης.','not-eligible');return;}
 if(valueOf('unsuitable')==='yes'){showResult('Δεν προκύπτει δικαίωμα συμμετοχής, επειδή δηλώθηκε ενεργός τριετής αποκλεισμός μετά από αξιολόγηση του έργου ως «ακατάλληλο».','not-eligible');return;}
 if(valueOf('retirement')==='yes'){showResult('Δεν προκύπτει δικαίωμα συμμετοχής: η πρόσκληση αποκλείει όσους αποχωρούν υποχρεωτικά λόγω συνταξιοδότησης έως 10/09/2027.','not-eligible');return;}
 if(vals.includes('unknown')){showResult('Χρειάζεται περαιτέρω έλεγχος, επειδή σε μία ή περισσότερες προϋποθέσεις επέλεξες «Δεν είμαι σίγουρος/η».','unknown');return;}
 showResult('Με βάση τις απαντήσεις σου, πληροίς τις ρητές βασικές προϋποθέσεις συμμετοχής της πρόσκλησης.<br><br><strong>Προσοχή:</strong> ο τελικός έλεγχος των προϋποθέσεων γίνεται από τον/τη Διευθυντή/ντρια της οικείας Σ.Α.Ε.Κ. και η επιλογή δεν βασίζεται σε αριθμητική μοριοδότηση.','eligible');
}
</script>
<?php sourceCardStart(); ?>
<p><strong>Κ5/113585/01-09-2026</strong> — Πρόσκληση εκδήλωσης ενδιαφέροντος για την πλήρωση με επιλογή θέσεων Υποδιευθυντών δημόσιων Σ.Α.Ε.Κ. Η προθεσμία αιτήσεων είναι <strong>01/09/2026–10/09/2026</strong>.</p>
<?php sourceCardLinksStart(); ?><?php sourceCardLink('https://www.minedu.gov.gr/publications/docs2026/%CE%A0%CF%81%CF%8C%CF%83%CE%BA%CE%BB%CE%B7%CF%83%CE%B7_%CE%A5%CF%80%CE%BF%CE%B4%CE%B9%CE%B5%CF%85%CE%B8%CF%85%CE%BD%CF%84%CF%8E%CE%BD_%CE%A3%CE%91%CE%95%CE%9A_2026.pdf', 'ΥΠΑΙΘΑ — επίσημη πρόσκληση ↗'); ?><?php sourceCardLinksEnd(); ?>
<?php sourceCardEnd(); ?>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
<script src="<?php echo htmlspecialchars(edu_asset_url('assets/common.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
</body></html>
