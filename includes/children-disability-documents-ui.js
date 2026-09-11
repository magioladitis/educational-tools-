function valueOf(id){return document.getElementById(id).value}
function show(id){document.getElementById(id).classList.remove("hidden")}
function hide(id){document.getElementById(id).classList.add("hidden")}
function updateVisibility(){
  const c=valueOf("criterion");
  hide("childrenQuestions"); hide("disabilityQuestions");
  document.getElementById("result").style.display="none";
  if(c==="children"||c==="both") show("childrenQuestions");
  if(c==="disability"||c==="both") show("disabilityQuestions");
  updateDisabilityPersonUI();
}
const disabilityPercentInput=document.getElementById("disabilityPercent");
if(disabilityPercentInput){
  disabilityPercentInput.addEventListener("input",function(){
    if(this.value==="") return;
    let value=Number(this.value);
    if(!Number.isFinite(value)) value=0;
    this.value=String(Math.min(100,Math.max(0,value)));
  });
}

function updateDisabilityPersonUI(){
  const p=valueOf("disabilityPerson");
  hide("spouseMarriageQuestion");
  hide("candidateMentalQuestion");
  if(p==="spouse") show("spouseMarriageQuestion");
  if(p==="candidate") show("candidateMentalQuestion");
  document.getElementById("result").style.display="none";
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
  const childCase=valueOf("childCase");
  const familyStatus=valueOf("familyCertificateStatus");
  const specialCases=selectedFamilySpecialCases();
  if(!childCase||!familyStatus) return false;

  documents.push("Πιστοποιητικό οικογενειακής κατάστασης από το οποίο προκύπτει ο αριθμός των τέκνων και τα ονομαστικά τους στοιχεία.");
  info.push("Το πιστοποιητικό οικογενειακής κατάστασης αναζητείται αυτεπαγγέλτως, εφόσον ο/η υποψήφιος/α υποβάλει σχετικό αίτημα στον Ο.Π.ΣΥ.Δ. και τα στοιχεία μπορούν να αντληθούν ορθά.");

  if(familyStatus==="no") warnings.push("Αν τα στοιχεία δεν εμφανίζονται σωστά ή δεν μπορεί να γίνει αυτεπάγγελτη αναζήτηση, χρειάζεται έλεγχος/διόρθωση στον Ο.Π.ΣΥ.Δ. ή προσκόμιση πιστοποιητικού οικογενειακής κατάστασης σύμφωνα με τις οδηγίες.");
  if(familyStatus==="unknown") warnings.push("Έλεγξε στον Ο.Π.ΣΥ.Δ. τα ονομαστικά στοιχεία των τέκνων και τις ημερομηνίες γέννησης.");

  if(childCase==="student25"||childCase==="mixed") documents.push("Βεβαίωση σπουδών από το οικείο Α.Ε.Ι. ή το ομοταγές ίδρυμα της αλλοδαπής, για τέκνο που σπουδάζει και δεν έχει συμπληρώσει το 25ο έτος.");
  if(childCase==="military25"||childCase==="mixed") documents.push("Βεβαίωση υπηρεσίας από τη στρατιωτική μονάδα, για τέκνο που εκπληρώνει στρατιωτικές υποχρεώσεις και δεν έχει συμπληρώσει το 25ο έτος.");
  if(childCase==="unknown") warnings.push("Δεν είναι σαφές ποια περίπτωση τέκνου δηλώνεις. Έλεγξε ηλικία, οικογενειακή κατάσταση, σπουδές ή στρατιωτική θητεία.");

  if(specialCases.includes("divorce")||specialCases.includes("separation")||specialCases.includes("unmarried")){
    documents.push("Κατάλληλα δικαιολογητικά από τα οποία αποδεικνύεται ότι ο/η υποψήφιος/α έχει τη γονική μέριμνα και επιμέλεια του τέκνου.");
    warnings.push("Σε περιπτώσεις διαζυγίου, λύσης γάμου/συμφώνου συμβίωσης, διάστασης ή τέκνου χωρίς γάμο, χρειάζεται ιδιαίτερος έλεγχος των δικαιολογητικών γονικής μέριμνας και επιμέλειας.");
  }
  if(specialCases.includes("adopted")) info.push("Τα υιοθετημένα, νομίμως αναγνωρισμένα ή εκτός γάμου γεννηθέντα τέκνα λαμβάνονται υπόψη, εφόσον πληρούνται οι προϋποθέσεις γονικής μέριμνας, επιμέλειας, ηλικίας και οικογενειακής κατάστασης.");

  documents.push("Το πιστοποιητικό ή τα σχετικά δικαιολογητικά πρέπει να έχουν εκδοθεί εντός του τελευταίου τριμήνου πριν από τη λήξη της προθεσμίας υποβολής των ηλεκτρονικών αιτήσεων, όπου αυτό απαιτείται.");
  return true;
}
function addDisabilityDocuments(documents,warnings,info){
  const p=valueOf("disabilityPerson"), cert=valueOf("disabilityCertificate");
  const percentRaw=valueOf("disabilityPercent");
  if(!p||!cert||percentRaw==="") return false;
  const percent=Math.min(100,Math.max(0,Number(percentRaw)||0));

  if(percent<50) warnings.push(`Το δηλωμένο ποσοστό (${percent}%) είναι μικρότερο από 50% και δεν μοριοδοτείται στο συγκεκριμένο κοινωνικό κριτήριο.`);
  else info.push(`Δηλωμένο ποσοστό αναπηρίας: ${percent}% (καλύπτει το όριο του 50%).`);

  if(p==="spouse"){
    const marriage=valueOf("marriageYears4Plus");
    if(!marriage) return false;
    if(marriage==="no") warnings.push("Η αναπηρία συζύγου δεν μοριοδοτείται αν ο έγγαμος βίος δεν έχει διαρκέσει τουλάχιστον 4 έτη.");
    if(marriage==="unknown") warnings.push("Χρειάζεται να επιβεβαιωθεί ότι ο έγγαμος βίος έχει διαρκέσει τουλάχιστον 4 έτη.");
  }
  if(p==="candidate"){
    const mental=valueOf("candidateMentalCondition");
    if(!mental) return false;
    if(mental==="yes") warnings.push("Η αναπηρία του/της υποψηφίου δεν μοριοδοτείται όταν οφείλεται, έστω και κατά ποσοστό, σε ψυχική πάθηση.");
    if(mental==="unknown") warnings.push("Χρειάζεται να ελεγχθεί αν η αναπηρία του/της υποψηφίου οφείλεται κατά οποιοδήποτε ποσοστό σε ψυχική πάθηση.");
  }

  if(cert==="kepa") documents.push("Πιστοποιητικό ΚΕ.Π.Α. σε ισχύ, με το οποίο προσδιορίζεται το ποσοστό αναπηρίας.");
  if(cert==="military_committees") documents.push("Πιστοποιητικό σε ισχύ από την αρμόδια Ανώτατη Υγειονομική Επιτροπή, όπου αυτό προβλέπεται.");
  if(cert==="old_valid"){documents.push("Παλαιότερη γνωμάτευση / πιστοποιητικό αναπηρίας που εξακολουθεί να γίνεται δεκτό σύμφωνα με την προκήρυξη."); warnings.push("Έλεγξε προσεκτικά αν η παλαιότερη γνωμάτευση εξακολουθεί να ισχύει και γίνεται δεκτή.")}
  if(cert==="expired") warnings.push("Αν το πιστοποιητικό έχει λήξει ή δεν είναι σαφές αν είναι σε ισχύ, χρειάζεται έλεγχος πριν χρησιμοποιηθεί για μοριοδότηση.");
  if(cert==="none") warnings.push("Δεν προκύπτει δικαιολογητικό αναπηρίας, επειδή δηλώθηκε ότι δεν υπάρχει πιστοποιητικό.");
  if(cert==="unknown") warnings.push("Χρειάζεται έλεγχος του πιστοποιητικού αναπηρίας και της ισχύος του.");

  if(p==="spouse"){
    documents.push("Πιστοποιητικό οικογενειακής κατάστασης ή άλλο κατάλληλο δικαιολογητικό από το οποίο αποδεικνύεται η σχέση με τον/τη σύζυγο.");
    documents.push("Το πιστοποιητικό οικογενειακής κατάστασης πρέπει να είναι πρόσφατο, σύμφωνα με τις οδηγίες.");
  }
  if(p==="child"){
    documents.push("Πιστοποιητικό οικογενειακής κατάστασης από το οποίο προκύπτει η σχέση με το τέκνο.");
    documents.push("Όπου απαιτείται, δικαιολογητικά που αποδεικνύουν γονική μέριμνα και επιμέλεια.");
    documents.push("Το πιστοποιητικό οικογενειακής κατάστασης πρέπει να είναι πρόσφατο, σύμφωνα με τις οδηγίες.");
    info.push("Η αναπηρία τέκνου μοριοδοτείται ανεξαρτήτως ηλικίας, εφόσον πληρούνται οι λοιπές προϋποθέσεις.");
  }
  if(p==="candidate") info.push("Για αναπηρία του/της ίδιου/ας του/της υποψηφίου/ας, το βασικό δικαιολογητικό είναι το πιστοποιητικό αναπηρίας σε ισχύ.");
  return true;
}
function showDocuments(){
  const c=valueOf("criterion");
  if(!c){showResult("<h2>Λείπει επιλογή</h2><p>Παρακαλώ επίλεξε ποιο κοινωνικό κριτήριο θέλεις να ελέγξεις.</p>");return}
  const documents=[], warnings=[], info=[];
  if(c==="children"||c==="both"){
    if(!addChildrenDocuments(documents,warnings,info)){showResult("<h2>Λείπουν στοιχεία</h2><p>Παρακαλώ απάντησε στις ερωτήσεις για τα τέκνα.</p>");return}
  }
  if(c==="disability"||c==="both"){
    if(!addDisabilityDocuments(documents,warnings,info)){showResult("<h2>Λείπουν στοιχεία</h2><p>Παρακαλώ συμπλήρωσε τα απαραίτητα στοιχεία για την αναπηρία.</p>");return}
  }

  let html='<h2>Ενδεικτικά δικαιολογητικά</h2><div class="note-box">'+makeList([...new Set(documents)])+'</div>';
  if(info.length) html+='<div class="success-box">Χρήσιμες επισημάνσεις:'+makeList([...new Set(info)])+'</div>';
  if(warnings.length) html+='<div class="warning-box">Προσοχή:'+makeList([...new Set(warnings)])+'</div>';
  html+='<div class="note-box"><strong>Υπενθύμιση:</strong> Για τη μοριοδότηση τέκνων απαιτείται γονική μέριμνα και επιμέλεια, καθώς και να πληρούνται οι ηλικιακές και λοιπές προϋποθέσεις. Για την αναπηρία απαιτείται ποσοστό 50% και άνω και κατάλληλο πιστοποιητικό σε ισχύ.</div>';
  showResult(html);
}

// External bindings: keep markup free of inline event handlers.
const criterionSelect=document.getElementById("criterion");
if(criterionSelect) criterionSelect.addEventListener("change",updateVisibility);
const disabilityPersonSelect=document.getElementById("disabilityPerson");
if(disabilityPersonSelect) disabilityPersonSelect.addEventListener("change",updateDisabilityPersonUI);
const showDocumentsBtn=document.getElementById("showDocumentsBtn");
if(showDocumentsBtn) showDocumentsBtn.addEventListener("click",showDocuments);
