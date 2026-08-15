<!DOCTYPE html>
<html lang="el">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Οδηγός ένστασης ΑΣΕΠ</title>
<style>
body{font-family:Arial,sans-serif;background:#f5f5f5;margin:0;padding:30px;color:#222}
.app-box{max-width:900px;margin:auto;background:#fff;padding:25px;border-radius:14px;box-shadow:0 4px 14px rgba(0,0,0,.12)}
.back-tools{display:inline-block;margin-bottom:18px;color:#1f6feb;font-weight:bold;text-decoration:none}
.back-tools:hover{text-decoration:underline}
h1{text-align:center;font-size:26px;margin:0 0 10px;line-height:1.25}
.intro{text-align:center;color:#555;line-height:1.5;margin-bottom:20px}
.deadline-card{margin:0 0 22px;padding:16px;border-radius:12px;border:1px solid #d8e2f2;background:#f7faff}
.deadline-card strong{display:block;font-size:17px;margin-bottom:6px}
.deadline-line{line-height:1.55}
.status{margin-top:10px;padding:10px 12px;border-radius:8px;font-weight:bold}
.status.open{background:#e6f4ea;color:#137333}
.status.before{background:#fff4e5;color:#8a5300}
.status.closed{background:#fdecea;color:#b3261e}
.question{margin-bottom:18px;padding:14px;background:#fafafa;border:1px solid #ddd;border-radius:10px}
.question-number{display:inline-flex;align-items:center;justify-content:center;width:26px;height:26px;border-radius:50%;background:#1f6feb;color:#fff;font-size:14px;margin-right:7px}
label{display:block;font-weight:bold;margin-bottom:9px;line-height:1.4}
select{width:100%;padding:11px;border-radius:8px;border:1px solid #ccc;font-size:15px;box-sizing:border-box;background:#fff}
button{width:100%;margin-top:4px;padding:14px;border:none;border-radius:8px;font-size:17px;font-weight:bold;cursor:pointer;background:#1f6feb;color:white}
button:hover{background:#1558c0}
button:disabled{background:#aaa;cursor:not-allowed}
.hidden{display:none}
.result{display:none;margin-top:24px;padding:18px;border-radius:10px;background:#eef4ff;color:#174ea6;line-height:1.6}
.result h2{margin-top:0;font-size:21px}
.result h3{margin:18px 0 7px;font-size:17px}
.result ul,.result ol{margin:7px 0;padding-left:23px}
.note-box,.warning-box,.success-box,.danger-box{margin-top:14px;padding:12px;border-radius:8px}
.note-box{background:rgba(255,255,255,.78);border:1px solid rgba(0,0,0,.08);color:#333}
.warning-box{background:#fff4e5;color:#8a5300}
.success-box{background:#e6f4ea;color:#137333}
.danger-box{background:#fdecea;color:#b3261e}
.paravolo-summary{margin:14px 0 18px;padding:14px;border-radius:10px;background:#f8f9fa;border:1px solid #ddd;line-height:1.55}
.paravolo-summary ul{margin:8px 0 0;padding-left:22px}
.action-links{display:flex;gap:10px;flex-wrap:wrap;margin-top:12px}
.action-links a{display:inline-block;padding:9px 12px;border-radius:7px;background:#fff;border:1px solid #cbd5e1;color:#174ea6;text-decoration:none;font-weight:bold}
.action-links a:hover{text-decoration:underline}
.small-note{margin-top:18px;font-size:13px;color:#666;line-height:1.5;text-align:justify}
.credits{margin-top:24px;text-align:center;font-size:13px;color:#777}
@media(max-width:760px){body{padding:16px}.app-box{padding:18px}h1{font-size:23px}.action-links{display:block}.action-links a{display:block;margin-top:8px;text-align:center}}
</style>
</head>
<body>
<?php require_once __DIR__ . '/includes/header.php'; ?>
<div class="app-box">
<h1>Οδηγός ένστασης ΑΣΕΠ</h1>
  <p class="intro">Γρήγορος οδηγός για την υποβολή ένστασης κατά των προσωρινών πινάκων.</p>

  <div class="deadline-card">
    <strong>📅 Προθεσμία ενστάσεων</strong>
    <div class="deadline-line">Από <b>Τετάρτη 12 Αυγούστου 2026, ώρα 08:00</b> έως και <b>Παρασκευή 21 Αυγούστου 2026, ώρα 14:00</b>.</div>
    <div id="deadlineStatus" class="status"></div>
  </div>

  <div class="question">
    <label for="objectionReason"><span class="question-number">1</span>Ποιος είναι ο βασικός λόγος της ένστασής σου;</label>
    <select id="objectionReason" onchange="updateExtraQuestions()">
      <option value="">-- Επιλογή --</option>
      <option value="points">Λανθασμένος υπολογισμός μορίων</option>
      <option value="rejection">Απόρριψη / μη ένταξη στον πίνακα</option>
      <option value="missing">Δεν λήφθηκε υπόψη προσόν ή δικαιολογητικό</option>
      <option value="foreign">Αναγνώριση τίτλου αλλοδαπής</option>
      <option value="personal">Λάθος προσωπικά ή υπηρεσιακά στοιχεία</option>
      <option value="other">Άλλος λόγος</option>
    </select>
  </div>

  <div id="foreignQuestions" class="hidden">
    <div class="question">
      <label for="recognitionStatus">Έχει εκδοθεί πιστοποιητικό / πράξη αναγνώρισης του τίτλου αλλοδαπής;</label>
      <select id="recognitionStatus">
        <option value="">-- Επιλογή --</option>
        <option value="issued">Ναι, έχει εκδοθεί</option>
        <option value="pending">Όχι, η αναγνώριση εκκρεμεί</option>
        <option value="none">Δεν έχει γίνει αίτηση αναγνώρισης</option>
      </select>
    </div>

    <div class="question">
      <label for="recognitionDate">Αν έχει εκδοθεί, η πράξη αναγνώρισης έχει εκδοθεί έως τη λήξη της προθεσμίας ενστάσεων;</label>
      <select id="recognitionDate">
        <option value="">-- Επιλογή --</option>
        <option value="yes">Ναι</option>
        <option value="no">Όχι</option>
        <option value="na">Δεν εφαρμόζεται / δεν έχει εκδοθεί ακόμη</option>
      </select>
    </div>
  </div>

  <div class="question">
    <label for="paravoloStatus"><span class="question-number">2</span>Έχεις εκδώσει και πληρώσει το παράβολο ένστασης των 50€;</label>
    <select id="paravoloStatus">
      <option value="">-- Επιλογή --</option>
      <option value="paid">Ναι, είναι πληρωμένο</option>
      <option value="issued_not_paid">Έχει εκδοθεί, αλλά δεν έχει πληρωθεί</option>
      <option value="no">Όχι, δεν το έχω εκδώσει</option>
    </select>
  </div>

  <div class="paravolo-summary">
    <strong>Στοιχεία e-Παραβόλου ένστασης</strong>
    <ul>
      <li><strong>Φορέας Δημοσίου:</strong> Ανώτατο Συμβούλιο Επιλογής Προσωπικού (ΑΣΕΠ)</li>
      <li><strong>Κατηγορία Παραβόλου:</strong> Υποβολή ένστασης</li>
      <li><strong>Τύπος Παραβόλου:</strong> <b>[1236]</b> Υποβολή ένστασης για όλες τις διαδικασίες πλήρωσης θέσεων των φορέων της παρ. 1</li>
      <li><strong>Ποσό:</strong> 50,00 €</li>
    </ul>
  </div>

  <button id="guidanceBtn" onclick="showGuidance()">Εμφάνιση οδηγιών</button>
  <div id="result" class="result"></div>

  <p class="small-note">Το εργαλείο παρέχει ενδεικτική καθοδήγηση. Για την υποβολή ισχύουν η επίσημη ανακοίνωση, η προκήρυξη και οι οδηγίες της ηλεκτρονικής πλατφόρμας του ΑΣΕΠ.</p>
  <div class="credits">Υλοποίηση/σχεδιασμός: Μάριος Μαγιολαδίτης, 2026</div>
</div>

<script>
const objectionStart = new Date('2026-08-12T08:00:00+03:00');
const objectionEnd   = new Date('2026-08-21T14:00:00+03:00');

function valueOf(id){ return document.getElementById(id).value; }

function updateDeadlineStatus(){
  const now = new Date();
  const box = document.getElementById('deadlineStatus');
  const btn = document.getElementById('guidanceBtn');

  box.className = 'status';
  btn.disabled = false;

  if(now < objectionStart){
    box.classList.add('before');
    box.textContent = '🟠 Η προθεσμία δεν έχει ανοίξει ακόμη.';
  } else if(now <= objectionEnd){
    box.classList.add('open');
    box.textContent = '🟢 Η προθεσμία ενστάσεων είναι ανοικτή.';
  } else {
    box.classList.add('closed');
    box.textContent = '🔴 Η προθεσμία ενστάσεων έχει λήξει.';
  }
}

function updateExtraQuestions(){
  const reason = valueOf('objectionReason');
  const foreign = document.getElementById('foreignQuestions');
  const result = document.getElementById('result');

  if(reason === 'foreign'){
    foreign.classList.remove('hidden');
  } else {
    foreign.classList.add('hidden');
    document.getElementById('recognitionStatus').value = '';
    document.getElementById('recognitionDate').value = '';
  }

  result.style.display = 'none';
}

function reasonGuidance(reason){
  const guidance = {
    points: {
      title: 'Λανθασμένος υπολογισμός μορίων',
      doc: 'Ετοίμασε σαφή τεκμηρίωση για τον σωστό υπολογισμό των μορίων σου (π.χ. τίτλοι, προϋπηρεσία, ξένες γλώσσες, κοινωνικά κριτήρια ή άλλο σχετικό στοιχείο).'
    },
    rejection: {
      title: 'Απόρριψη / μη ένταξη στον πίνακα',
      doc: 'Εντόπισε τον ακριβή λόγο απόρριψης και συγκέντρωσε τα δικαιολογητικά που αποδεικνύουν ότι πληρούνται οι σχετικές προϋποθέσεις.'
    },
    missing: {
      title: 'Δεν λήφθηκε υπόψη προσόν ή δικαιολογητικό',
      doc: 'Κατονόμασε συγκεκριμένα το προσόν ή το δικαιολογητικό που θεωρείς ότι δεν λήφθηκε υπόψη και τεκμηρίωσε γιατί έπρεπε να προσμετρηθεί.'
    },
    foreign: {
      title: 'Αναγνώριση τίτλου αλλοδαπής',
      doc: 'Συγκέντρωσε την πράξη / το πιστοποιητικό αναγνώρισης και τα σχετικά στοιχεία που τεκμηριώνουν το αίτημά σου.'
    },
    personal: {
      title: 'Λάθος προσωπικά ή υπηρεσιακά στοιχεία',
      doc: 'Συγκέντρωσε τα στοιχεία ή δικαιολογητικά που αποδεικνύουν ποια δεδομένα πρέπει να διορθωθούν.'
    },
    other: {
      title: 'Άλλος λόγος',
      doc: 'Περιέγραψε με σαφήνεια το συγκεκριμένο σφάλμα που ζητάς να διορθωθεί και επισύναψε κάθε σχετικό αποδεικτικό στοιχείο.'
    }
  };
  return guidance[reason];
}

function showGuidance(){
  const now = new Date();
  const reason = valueOf('objectionReason');
  const paravolo = valueOf('paravoloStatus');
  const result = document.getElementById('result');

  if(!reason || !paravolo){
    result.style.display = 'block';
    result.innerHTML = '<h2>Λείπουν στοιχεία</h2><div class="warning-box">Επίλεξε τον λόγο της ένστασης και την κατάσταση του παραβόλου.</div>';
    return;
  }

  if(reason === 'foreign'){
    const recognition = valueOf('recognitionStatus');
    const recognitionDate = valueOf('recognitionDate');
    if(!recognition || !recognitionDate){
      result.style.display = 'block';
      result.innerHTML = '<h2>Λείπουν στοιχεία</h2><div class="warning-box">Συμπλήρωσε και τις ερωτήσεις που αφορούν την αναγνώριση του τίτλου αλλοδαπής.</div>';
      return;
    }
  }

  const rg = reasonGuidance(reason);
  let html = '<h2>Τι πρέπει να κάνεις</h2>';

  if(now < objectionStart){
    html += '<div class="warning-box"><strong>Η προθεσμία δεν έχει ανοίξει ακόμη.</strong> Η ηλεκτρονική υποβολή ξεκινά στις 12/08/2026 και ώρα 08:00.</div>';
  } else if(now > objectionEnd){
    html += '<div class="danger-box"><strong>Η προθεσμία έχει λήξει.</strong> Η καταληκτική ημερομηνία ήταν 21/08/2026 και ώρα 14:00.</div>';
  } else {
    html += '<div class="success-box"><strong>Η προθεσμία είναι ανοικτή.</strong> Ολοκλήρωσε την υποβολή έως 21/08/2026 και ώρα 14:00.</div>';
  }

  html += '<h3>1. Τεκμηρίωσε τον λόγο της ένστασης</h3>';
  html += '<div class="note-box"><strong>' + rg.title + '</strong><br>' + rg.doc + '<br><br>Η ένσταση καλό είναι να είναι συγκεκριμένη και να εξηγεί με σαφήνεια ποιο στοιχείο θεωρείς λανθασμένο και ποια διόρθωση ζητάς.</div>';

  if(reason === 'foreign'){
    const recognition = valueOf('recognitionStatus');
    const recognitionDate = valueOf('recognitionDate');

    if(recognition === 'issued' && recognitionDate === 'yes'){
      html += '<div class="success-box">Η πράξη αναγνώρισης έχει εκδοθεί έως τη λήξη της προθεσμίας ενστάσεων. Συμπερίλαβέ την στην τεκμηρίωσή σου.</div>';
    }
    if(recognition === 'issued' && recognitionDate === 'no'){
      html += '<div class="warning-box">Δήλωσες ότι η πράξη αναγνώρισης εκδόθηκε μετά τη λήξη της προθεσμίας ενστάσεων. Χρειάζεται ιδιαίτερος έλεγχος αν μπορεί να ληφθεί υπόψη.</div>';
    }
    if(recognition === 'pending'){
      html += '<div class="warning-box">Η αναγνώριση εκκρεμεί. Έλεγξε αν η πράξη θα έχει εκδοθεί έως τη λήξη της προθεσμίας ενστάσεων.</div>';
    }
    if(recognition === 'none'){
      html += '<div class="warning-box">Δεν έχει γίνει αίτηση αναγνώρισης, επομένως δεν υπάρχει αυτή τη στιγμή πράξη αναγνώρισης για να χρησιμοποιηθεί στην ένσταση.</div>';
    }
  }

  html += '<h3>2. Έλεγξε το e-Παράβολο</h3>';
  html += '<div class="note-box"><ul>' +
          '<li><strong>Φορέας:</strong> Ανώτατο Συμβούλιο Επιλογής Προσωπικού (ΑΣΕΠ)</li>' +
          '<li><strong>Κατηγορία:</strong> Υποβολή ένστασης</li>' +
          '<li><strong>Τύπος:</strong> [1236] Υποβολή ένστασης για όλες τις διαδικασίες πλήρωσης θέσεων των φορέων της παρ. 1</li>' +
          '<li><strong>Ποσό:</strong> 50,00 €</li>' +
          '</ul></div>';

  if(paravolo === 'paid'){
    html += '<div class="success-box">Το παράβολο είναι πληρωμένο. Έλεγξε προσεκτικά ότι ο αριθμός του έχει καταχωριστεί σωστά στην ένσταση.</div>';
  }
  if(paravolo === 'issued_not_paid'){
    html += '<div class="warning-box"><strong>Προσοχή:</strong> το παράβολο έχει εκδοθεί αλλά δεν έχει πληρωθεί. Το αντίτιμο πρέπει να καταβληθεί πριν από την υποβολή της ένστασης.</div>';
  }
  if(paravolo === 'no'){
    html += '<div class="warning-box"><strong>Χρειάζεται πρώτα έκδοση και πληρωμή του παραβόλου των 50€.</strong> Στη συνέχεια αναγράφεται ο αριθμός του παραβόλου στην ηλεκτρονική ένσταση.</div>';
  }

  html += '<h3>3. Υπέβαλε ηλεκτρονικά την ένσταση</h3>';
  html += '<div class="note-box"><ol>' +
          '<li>Μπες στον διαδικτυακό τόπο του ΑΣΕΠ.</li>' +
          '<li>Ακολούθησε τη διαδρομή <strong>Ηλεκτρονικές Υπηρεσίες → Ένσταση</strong>.</li>' +
          '<li>Συμπλήρωσε με σαφήνεια τον λόγο της ένστασης και επισύναψε τα σχετικά δικαιολογητικά όπου απαιτείται.</li>' +
          '<li>Ανάγραψε τον αριθμό του e-Παραβόλου στην ένσταση.</li>' +
          '<li>Βεβαιώσου ότι το παράβολο έχει πληρωθεί <strong>πριν από την υποβολή</strong>.</li>' +
          '<li>Ολοκλήρωσε την υποβολή το αργότερο έως <strong>Παρασκευή 21/08/2026, ώρα 14:00</strong>.</li>' +
          '</ol>' +
          '<div class="action-links">' +
          '<a href="https://www.asep.gr" target="_blank" rel="noopener">Άνοιγμα ΑΣΕΠ</a>' +
          '<a href="https://www.gsis.gr" target="_blank" rel="noopener">e-Παράβολο / ΓΓΠΣΨΔ</a>' +
          '</div></div>';

  html += '<div class="warning-box"><strong>Τελικός έλεγχος πριν την υποβολή:</strong><ul>' +
          '<li>σωστός λόγος ένστασης,</li>' +
          '<li>σαφής τεκμηρίωση και σχετικά δικαιολογητικά,</li>' +
          '<li>σωστός αριθμός e-Παραβόλου,</li>' +
          '<li>παράβολο πληρωμένο,</li>' +
          '<li>οριστική υποβολή εντός της προθεσμίας.</li>' +
          '</ul></div>';

  result.innerHTML = html;
  result.style.display = 'block';
  result.scrollIntoView({behavior:'smooth',block:'start'});
}

updateDeadlineStatus();
setInterval(updateDeadlineStatus, 60000);
</script>
</body>
</html>
