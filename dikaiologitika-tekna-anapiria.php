<!DOCTYPE html>
<html lang="el">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Οδηγός δικαιολογητικών τέκνων και αναπηρίας</title>
<style>
body{font-family:Arial,sans-serif;background:#f5f5f5;margin:0;padding:30px;color:#222}
.app-box{max-width:880px;margin:auto;background:#fff;padding:25px;border-radius:14px;box-shadow:0 4px 14px rgba(0,0,0,.12)}
.back-tools{display:inline-block;margin-bottom:18px;color:#1f6feb;font-weight:bold;text-decoration:none}
.back-tools:hover{text-decoration:underline}
h1{text-align:center;font-size:26px;margin-bottom:10px;line-height:1.25}
.intro{text-align:center;color:#555;line-height:1.5;margin-bottom:24px}
.question{margin-bottom:18px;padding:14px;background:#fafafa;border:1px solid #ddd;border-radius:10px}
label{display:block;font-weight:bold;margin-bottom:8px;line-height:1.4}
select{width:100%;padding:11px;border-radius:8px;border:1px solid #ccc;font-size:15px;box-sizing:border-box;background:#fff}
.checkbox-group label{display:flex;gap:8px;align-items:flex-start;font-weight:normal;margin:8px 0;line-height:1.4}
button{width:100%;margin-top:20px;padding:14px;border:none;border-radius:8px;font-size:17px;font-weight:bold;cursor:pointer;background:#1f6feb;color:white}
button:hover{background:#1558c0}
.result{display:none;margin-top:24px;padding:18px;border-radius:10px;background:#eef4ff;color:#174ea6;line-height:1.6}
.result h2{margin-top:0;font-size:20px}
.result ul{margin-top:8px;padding-left:22px}
.note-box{margin-top:14px;padding:12px;border-radius:8px;background:rgba(255,255,255,.7);border:1px solid rgba(0,0,0,.08)}
.warning-box{margin-top:14px;padding:12px;border-radius:8px;background:#fff4e5;color:#8a5300;font-weight:bold}
.success-box{margin-top:14px;padding:12px;border-radius:8px;background:#e6f4ea;color:#137333;font-weight:bold}
.small-note{margin-top:18px;font-size:13px;color:#666;line-height:1.5;text-align:justify}
.credits{margin-top:24px;text-align:center;font-size:13px;color:#777}
.hidden{display:none}
@media(max-width:760px){body{padding:18px}h1{font-size:23px}}
</style>
  <link rel="stylesheet" href="assets/common.css">
</head>
<body class="edu-ui">
<?php require_once __DIR__ . '/includes/header.php'; ?>
<div class="app-box edu-modernized">
<section class="hero edu-legacy-hero">
<h1>Οδηγός δικαιολογητικών τέκνων και αναπηρίας</h1>
<p class="intro">Ενδεικτικός οδηγός για τα κοινωνικά κριτήρια, διαμορφωμένος σύμφωνα με τα τέσσερα σχετικά πεδία της πλατφόρμας: <strong>Μοριοδοτούμενα Τέκνα</strong>, <strong>Αναπηρία Ιδίου</strong>, <strong>Αναπηρία Τέκνου</strong> και <strong>Αναπηρία Συζύγου</strong>.</p>
</section>

<div class="question">
<label for="criterion">Ποιο πεδίο της πλατφόρμας θέλεις να ελέγξεις;</label>
<select id="criterion" onchange="updateVisibility()">
<option value="">-- Επιλογή --</option>
<option value="children">Μοριοδοτούμενα Τέκνα</option>
<option value="candidate_disability">Ποσοστό Αναπηρίας Ιδίου ≥ 50%</option>
<option value="child_disability">Ποσοστό Αναπηρίας Τέκνου ≥ 50%</option>
<option value="spouse_disability">Ποσοστό Αναπηρίας Συζύγου ≥ 50%</option>
<option value="multiple">Περισσότερα από ένα πεδία</option>
</select>
<p class="small-note" style="margin-top:10px;margin-bottom:0;">
  Η επιλογή είναι διαμορφωμένη με βάση τα πεδία που εμφανίζονται στην πλατφόρμα:
  Μοριοδοτούμενα Τέκνα, Αναπηρία Ιδίου, Αναπηρία Τέκνου και Αναπηρία Συζύγου.
</p>
</div>

<div id="childrenQuestions" class="hidden">
<div class="question">
<label for="under23Child">Υπάρχει άγαμο τέκνο που δεν έχει συμπληρώσει το 23ο έτος;</label>
<select id="under23Child">
<option value="">-- Επιλογή --</option>
<option value="no">Όχι</option>
<option value="yes">Ναι</option>
<option value="unknown">Δεν είμαι σίγουρος/η</option>
</select>
<p class="small-note" style="margin-top:10px;margin-bottom:0;">
  Επίλεξε «Ναι» για τέκνο που καλύπτεται από το ηλικιακό όριο έως το 23ο έτος, χωρίς να χρειάζεται βεβαίωση σπουδών ή στρατιωτικής υπηρεσίας.
</p>
</div>

<div class="question">
<label for="studentChild">Υπάρχει σπουδάζον τέκνο που δεν έχει συμπληρώσει το 25ο έτος;</label>
<select id="studentChild">
<option value="">-- Επιλογή --</option>
<option value="no">Όχι</option>
<option value="yes">Ναι</option>
<option value="unknown">Δεν είμαι σίγουρος/η</option>
</select>
<p class="small-note" style="margin-top:10px;margin-bottom:0;">
  Επίλεξε «Ναι» μόνο όταν χρειάζεται να αποδειχθεί η ιδιότητα του/της φοιτητή/τριας, ώστε να υπολογιστεί τέκνο έως το 25ο έτος.
</p>
</div>

<div class="question">
<label for="militaryChild">Υπάρχει τέκνο που εκπληρώνει στρατιωτικές υποχρεώσεις και δεν έχει συμπληρώσει το 25ο έτος;</label>
<select id="militaryChild">
<option value="">-- Επιλογή --</option>
<option value="no">Όχι</option>
<option value="yes">Ναι</option>
<option value="unknown">Δεν είμαι σίγουρος/η</option>
</select>
<p class="small-note" style="margin-top:10px;margin-bottom:0;">
  Επίλεξε «Ναι» μόνο όταν χρειάζεται να αποδειχθεί η στρατιωτική θητεία τέκνου, ώστε να υπολογιστεί τέκνο έως το 25ο έτος.
</p>
</div>

<div class="question">
<label>Υπάρχει ειδική οικογενειακή κατάσταση; Αν δεν υπάρχει, άφησε τα παρακάτω κενά.</label>
<div class="checkbox-group">
<label><input type="checkbox" name="familySpecialCase" value="divorce"> Διαζύγιο / λύση γάμου / λύση συμφώνου συμβίωσης</label>
<label><input type="checkbox" name="familySpecialCase" value="separation"> Διάσταση ή χωριστή διαβίωση χωρίς κοινή επιμέλεια</label>
<label><input type="checkbox" name="familySpecialCase" value="unmarried"> Τέκνο χωρίς γάμο ή χωρίς σύμφωνο συμβίωσης των γονέων</label>
<label><input type="checkbox" name="familySpecialCase" value="adopted"> Υιοθετημένο ή νομίμως αναγνωρισμένο τέκνο</label>
</div>
</div>
</div>

<div id="disabilityQuestions" class="hidden">
<div id="disabilityPersonQuestion" class="question hidden">
<label for="disabilityPerson">Η αναπηρία αφορά:</label>
<select id="disabilityPerson">
<option value="">-- Επιλογή --</option>
<option value="candidate">Υποψήφιος/α</option>
<option value="spouse">Σύζυγος</option>
<option value="child">Τέκνο</option>
</select>
</div>



<div class="question">
<label for="disabilityPercent">Το ποσοστό αναπηρίας είναι 50% και άνω;</label>
<select id="disabilityPercent" onchange="updateDisabilityCertificateVisibility()">
<option value="">-- Επιλογή --</option>
<option value="yes">Ναι</option>
<option value="no">Όχι</option>
<option value="unknown">Δεν είμαι σίγουρος/η</option>
</select>
</div>

<div id="disabilityCertificateQuestion" class="question hidden">
<label for="disabilityCertificate">Τι πιστοποιητικό αναπηρίας υπάρχει;</label>
<select id="disabilityCertificate">
<option value="">-- Επιλογή --</option>
<option value="kepa">Πιστοποιητικό ΚΕ.Π.Α. σε ισχύ</option>
<option value="military_committees">Πιστοποιητικό σε ισχύ από Α.Σ.Υ.Ε., Α.Ν.Υ.Ε., Α.Α.Υ.Ε., Ελληνική Αστυνομία ή Πυροσβεστικό Σώμα</option>
<option value="old_valid">Παλαιότερη γνωμάτευση που εξακολουθεί να γίνεται δεκτή σύμφωνα με την προκήρυξη</option>
<option value="expired">Έχει λήξει ή δεν είναι σαφές αν είναι σε ισχύ</option>
<option value="none">Δεν υπάρχει πιστοποιητικό</option>
<option value="unknown">Δεν είμαι σίγουρος/η</option>
</select>
</div>
</div>

<button type="button" onclick="showDocuments()">Εμφάνιση ενδεικτικών δικαιολογητικών</button>
<div id="result" class="result" role="status" aria-live="polite"></div>

<p class="small-note">Το εργαλείο παρέχει ενδεικτική καθοδήγηση και δεν αντικαθιστά την επίσημη προκήρυξη, τις οδηγίες του Α.Σ.Ε.Π., τον έλεγχο του Ο.Π.ΣΥ.Δ. ή τον έλεγχο των αρμόδιων υπηρεσιών.</p>
</div>

<script>
function valueOf(id){return document.getElementById(id).value}
function show(id){document.getElementById(id).classList.remove("hidden")}
function hide(id){document.getElementById(id).classList.add("hidden")}
function updateVisibility(){
  const c=valueOf("criterion");
  hide("childrenQuestions");
  hide("disabilityQuestions");
  hide("disabilityCertificateQuestion");
  hide("disabilityPersonQuestion");
  hide("disabilityCertificateQuestion");
  document.getElementById("result").style.display="none";

  const disabilityPerson = document.getElementById("disabilityPerson");

  if(c==="children"){
    show("childrenQuestions");
    disabilityPerson.value="";
  }

  if(c==="candidate_disability"){
    show("disabilityQuestions");
    disabilityPerson.value="candidate";
  }

  if(c==="child_disability"){
    show("disabilityQuestions");
    disabilityPerson.value="child";
  }

  if(c==="spouse_disability"){
    show("disabilityQuestions");
    disabilityPerson.value="spouse";
  }

  if(c==="multiple"){
    show("childrenQuestions");
    show("disabilityQuestions");
    show("disabilityPersonQuestion");
    disabilityPerson.value="";
  }
  updateDisabilityCertificateVisibility();
}
function updateDisabilityCertificateVisibility(){
  const percent = valueOf("disabilityPercent");
  const certificate = document.getElementById("disabilityCertificate");

  if(percent === "yes" || percent === "unknown"){
    show("disabilityCertificateQuestion");
  } else {
    hide("disabilityCertificateQuestion");
    if(certificate){
      certificate.value = "";
    }
  }

  document.getElementById("result").style.display = "none";
}
function selectedFamilySpecialCases(){
  return Array.from(document.querySelectorAll('input[name="familySpecialCase"]:checked')).map(i=>i.value)
}
function makeList(items){
  if(!items.length) return "";
  return "<ul>"+items.map(item=>"<li>"+item+"</li>").join("")+"</ul>"
}
function showResult(html){
  const r=document.getElementById("result");
  r.style.display="block"; r.innerHTML=html
}
function addChildrenDocuments(documents,warnings,info){
  const under23Child = valueOf("under23Child");
  const studentChild = valueOf("studentChild");
  const militaryChild = valueOf("militaryChild");
  const specialCases = selectedFamilySpecialCases();

  if(!under23Child || !studentChild || !militaryChild) {
    return false;
  }

  const hasQualifyingChild =
    under23Child === "yes" ||
    studentChild === "yes" ||
    militaryChild === "yes";

  const hasUnknownChildCase =
    under23Child === "unknown" ||
    studentChild === "unknown" ||
    militaryChild === "unknown";

  if(!hasQualifyingChild && !hasUnknownChildCase) {
    warnings.push("Δεν προκύπτει μοριοδοτούμενο τέκνο με βάση τις απαντήσεις σου. Επομένως δεν εμφανίζονται ενδεικτικά δικαιολογητικά για το πεδίο «Μοριοδοτούμενα Τέκνα».");
    return true;
  }

  if(!hasQualifyingChild && hasUnknownChildCase) {
    warnings.push("Δεν είναι σαφές αν υπάρχει τέκνο που πληροί τις προϋποθέσεις μοριοδότησης. Έλεγξε ηλικία, οικογενειακή κατάσταση, σπουδές ή στρατιωτική θητεία.");
    return true;
  }

  info.push("Σύμφωνα με το ΦΕΚ, ο αριθμός των τέκνων αποδεικνύεται με πιστοποιητικό οικογενειακής κατάστασης από το οποίο προκύπτει ο αριθμός των τέκνων. Το πιστοποιητικό αναζητείται αυτεπάγγελτα, εφόσον ο/η υποψήφιος/α υποβάλει σχετικό αίτημα στον Ο.Π.ΣΥ.Δ.");

  if(under23Child === "yes") {
    info.push("Για άγαμο τέκνο που δεν έχει συμπληρώσει το 23ο έτος, συνήθως δεν χρειάζεται πρόσθετη βεβαίωση σπουδών ή στρατιωτικής υπηρεσίας. Αρκεί να προκύπτουν σωστά τα στοιχεία από την αυτεπάγγελτη αναζήτηση.");
  }

  if(under23Child === "unknown") {
    warnings.push("Δεν είναι σαφές αν υπάρχει άγαμο τέκνο που δεν έχει συμπληρώσει το 23ο έτος. Έλεγξε την ηλικία και την οικογενειακή κατάσταση του τέκνου.");
  }
if(studentChild==="yes") {
    documents.push("Βεβαίωση σπουδών από το οικείο Α.Ε.Ι. ή το ομοταγές ίδρυμα της αλλοδαπής, για τέκνο που σπουδάζει και δεν έχει συμπληρώσει το 25ο έτος.");
  }

  if(studentChild==="unknown") {
    warnings.push("Δεν είναι σαφές αν υπάρχει σπουδάζον τέκνο που χρειάζεται να αποδειχθεί με βεβαίωση σπουδών. Έλεγξε ηλικία και φοιτητική ιδιότητα.");
  }

  if(militaryChild==="yes") {
    documents.push("Βεβαίωση υπηρεσίας από τη στρατιωτική μονάδα, για τέκνο που εκπληρώνει στρατιωτικές υποχρεώσεις και δεν έχει συμπληρώσει το 25ο έτος.");
  }

  if(militaryChild==="unknown") {
    warnings.push("Δεν είναι σαφές αν υπάρχει τέκνο που εκπληρώνει στρατιωτικές υποχρεώσεις και χρειάζεται βεβαίωση υπηρεσίας. Έλεγξε ηλικία και κατάσταση θητείας.");
  }

  if(specialCases.includes("divorce") || specialCases.includes("separation") || specialCases.includes("unmarried")){
    documents.push("Κατάλληλα δικαιολογητικά από τα οποία αποδεικνύεται ότι ο/η υποψήφιος/α έχει τη γονική μέριμνα και επιμέλεια του τέκνου.");
    warnings.push("Σε περιπτώσεις διαζυγίου, λύσης γάμου/συμφώνου συμβίωσης, διάστασης ή τέκνου χωρίς γάμο, χρειάζεται ιδιαίτερος έλεγχος των δικαιολογητικών γονικής μέριμνας και επιμέλειας.");
  }

  if(specialCases.includes("adopted")) {
    info.push("Τα υιοθετημένα, νομίμως αναγνωρισμένα ή εκτός γάμου γεννηθέντα τέκνα λαμβάνονται υπόψη, εφόσον πληρούνται οι προϋποθέσεις γονικής μέριμνας, επιμέλειας, ηλικίας και οικογενειακής κατάστασης.");
  }

  info.push("Όπου απαιτείται πρόσφατη έκδοση, το πιστοποιητικό ή τα σχετικά δικαιολογητικά πρέπει να έχουν εκδοθεί εντός του τελευταίου τριμήνου πριν από τη λήξη της προθεσμίας υποβολής των ηλεκτρονικών αιτήσεων.");

  return true;
}
function addDisabilityDocuments(documents,warnings,info){
  const p = valueOf("disabilityPerson");
  const percent = valueOf("disabilityPercent");

  if(!p || !percent) {
    return false;
  }

  if(percent === "no") {
    warnings.push("Δεν πληρούνται οι προϋποθέσεις μοριοδότησης για την αναπηρία, επειδή το ποσοστό είναι μικρότερο από 50%.");
    return true;
  }

  if(percent === "unknown") {
    warnings.push("Χρειάζεται πρώτα να ελεγχθεί αν το ποσοστό αναπηρίας είναι 50% και άνω. Αν είναι μικρότερο από 50%, δεν προκύπτει μοριοδότηση για την αναπηρία.");
  }

  const cert = valueOf("disabilityCertificate");

  if(!cert) {
    return false;
  }

  if(cert === "kepa") {
    documents.push("Πιστοποιητικό ΚΕ.Π.Α. σε ισχύ, με το οποίο προσδιορίζεται το ποσοστό αναπηρίας.");
  }

  if(cert === "military_committees") {
    documents.push("Πιστοποιητικό σε ισχύ από την αρμόδια Ανώτατη Υγειονομική Επιτροπή, όπου αυτό προβλέπεται.");
  }

  if(cert === "old_valid") {
    documents.push("Παλαιότερη γνωμάτευση / πιστοποιητικό αναπηρίας που εξακολουθεί να γίνεται δεκτό σύμφωνα με την προκήρυξη.");
    warnings.push("Έλεγξε προσεκτικά αν η παλαιότερη γνωμάτευση εξακολουθεί να ισχύει και γίνεται δεκτή.");
  }

  if(cert === "expired") {
    warnings.push("Αν το πιστοποιητικό έχει λήξει ή δεν είναι σαφές αν είναι σε ισχύ, χρειάζεται έλεγχος πριν χρησιμοποιηθεί για μοριοδότηση.");
  }

  if(cert === "none") {
    warnings.push("Δεν προκύπτει δικαιολογητικό αναπηρίας, επειδή δηλώθηκε ότι δεν υπάρχει πιστοποιητικό.");
  }

  if(cert === "unknown") {
    warnings.push("Χρειάζεται έλεγχος του πιστοποιητικού αναπηρίας και της ισχύος του.");
  }

  if(p === "spouse") {
    documents.push("Αυτεπάγγελτη αναζήτηση πιστοποιητικού οικογενειακής κατάστασης μέσω Ο.Π.ΣΥ.Δ. ή άλλο κατάλληλο δικαιολογητικό από το οποίο αποδεικνύεται η σχέση με τον/τη σύζυγο, όπου απαιτείται.");
    info.push("Όπου απαιτείται πρόσφατο πιστοποιητικό οικογενειακής κατάστασης, ισχύει ο χρονικός περιορισμός του τελευταίου τριμήνου πριν από τη λήξη της προθεσμίας.");
  }

  if(p === "child") {
    documents.push("Αυτεπάγγελτη αναζήτηση πιστοποιητικού οικογενειακής κατάστασης μέσω Ο.Π.ΣΥ.Δ., από το οποίο προκύπτει η σχέση με το τέκνο, όπου απαιτείται.");
    documents.push("Όπου απαιτείται, δικαιολογητικά που αποδεικνύουν γονική μέριμνα και επιμέλεια.");
    info.push("Όπου απαιτείται πρόσφατο πιστοποιητικό οικογενειακής κατάστασης, ισχύει ο χρονικός περιορισμός του τελευταίου τριμήνου πριν από τη λήξη της προθεσμίας.");
  }

  if(p === "candidate") {
    info.push("Για αναπηρία του/της ίδιου/ας του/της υποψηφίου/ας, το βασικό δικαιολογητικό είναι το πιστοποιητικό αναπηρίας σε ισχύ.");
  }

  return true;
}
function showDocuments(){
  const c=valueOf("criterion");
  if(!c){showResult("<h2>Λείπει επιλογή</h2><p>Παρακαλώ επίλεξε ποιο κοινωνικό κριτήριο θέλεις να ελέγξεις.</p>");return}
  const documents=[], warnings=[], info=[];
  if(c==="children"||c==="multiple"){
    if(!addChildrenDocuments(documents,warnings,info)){showResult("<h2>Λείπουν στοιχεία</h2><p>Παρακαλώ απάντησε στις ερωτήσεις για τα τέκνα, το όριο των 23 ετών και τις σπουδές/στρατιωτική θητεία όπου υπάρχουν.</p>");return}
  }
  if(["candidate_disability","child_disability","spouse_disability","multiple"].includes(c)){
    if(!addDisabilityDocuments(documents,warnings,info)){showResult("<h2>Λείπουν στοιχεία</h2><p>Παρακαλώ απάντησε στις ερωτήσεις για την αναπηρία. Αν το ποσοστό είναι 50% και άνω, συμπλήρωσε και το πεδίο για το πιστοποιητικό αναπηρίας.</p>");return}
  }

  const uniqueDocuments = [...new Set(documents)];
  let html = '<h2>Ενδεικτικά δικαιολογητικά</h2>';

  if(uniqueDocuments.length > 0) {
    html += '<div class="note-box">' + makeList(uniqueDocuments) + '</div>';
  } else if(info.length > 0) {
    html += '<div class="note-box">Δεν εμφανίζεται δικαιολογητικό για μεταφόρτωση στο συγκεκριμένο πεδίο με βάση τις απαντήσεις σου. Δες τις χρήσιμες επισημάνσεις παρακάτω.</div>';
  } else {
    html += '<div class="warning-box">Δεν εμφανίζονται ενδεικτικά δικαιολογητικά, επειδή με βάση τις απαντήσεις δεν προκύπτει ότι πληρούνται οι προϋποθέσεις μοριοδότησης για το συγκεκριμένο κοινωνικό κριτήριο.</div>';
  }
  if(info.length) html+='<div class="success-box">Χρήσιμες επισημάνσεις:'+makeList([...new Set(info)])+'</div>';
  if(warnings.length) html+='<div class="warning-box">Προσοχή:'+makeList([...new Set(warnings)])+'</div>';
  html+='<div class="note-box"><strong>Σημείωση για πρόσφατη έκδοση:</strong> Όπου απαιτείται πρόσφατη έκδοση, το πιστοποιητικό ή τα σχετικά δικαιολογητικά πρέπει να έχουν εκδοθεί εντός του τελευταίου τριμήνου πριν από τη λήξη της προθεσμίας υποβολής των ηλεκτρονικών αιτήσεων.</div>';
  html+='<div class="note-box"><strong>Υπενθύμιση:</strong> Για τη μοριοδότηση τέκνων απαιτείται γονική μέριμνα και επιμέλεια, καθώς και να πληρούνται οι ηλικιακές και λοιπές προϋποθέσεις. Για την αναπηρία απαιτείται ποσοστό 50% και άνω και κατάλληλο πιστοποιητικό σε ισχύ.</div>';
  showResult(html);
}
</script>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
  <script src="assets/common.js"></script>
</body>
</html>
