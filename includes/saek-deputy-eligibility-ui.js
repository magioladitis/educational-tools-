
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

document.getElementById('checkEligibilityBtn').addEventListener('click',checkEligibility);
document.getElementById('resetBtn').addEventListener('click',resetForm);
updateProgress();
