<?php require_once __DIR__ . '/includes/config.php'; ?>
<!DOCTYPE html>
<html lang="el">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Οδηγός ένστασης 1ΓΕ/2026 &amp; 2ΓΕ/2026</title>
<link rel="stylesheet" href="<?php echo htmlspecialchars(edu_asset_url('assets/common.css'), ENT_QUOTES, 'UTF-8'); ?>">
</head>
<body class="edu-ui edu-guide-standard edu-guide-objection">
<?php require_once __DIR__ . '/includes/header.php'; ?>
<div class="app-box edu-modernized">
  <section class="hero edu-legacy-hero">
<h1>Οδηγός ένστασης 1ΓΕ/2026 &amp; 2ΓΕ/2026</h1>
<p class="intro">Γρήγορος, διαδραστικός οδηγός για την υποβολή ένστασης κατά των προσωρινών πινάκων εκπαιδευτικών.</p>
</section>

  <div class="deadline-card">
    <strong>📅 Προθεσμία ενστάσεων</strong>
    <div class="deadline-line">Από <b>Τετάρτη 12 Αυγούστου 2026, ώρα 08:00</b> έως και <b>Παρασκευή 21 Αυγούστου 2026, ώρα 14:00</b> (ώρα Ελλάδας).</div>
    <div id="deadlineStatus" class="status" role="status" aria-live="polite"></div>
  </div>

  <div class="quick-card">
    <strong>📘 Επίσημες οδηγίες ΑΣΕΠ</strong>
    Για λεπτομέρειες μπορείς να ανοίξεις απευθείας το επίσημο εγχειρίδιο ηλεκτρονικής ένστασης εκπαιδευτικών.
    <br><a class="inline-link" href="https://info.asep.gr/sites/default/files/2023-10/manual_enstasi_ekpaideutikon.pdf" target="_blank" rel="noopener">Άνοιγμα επίσημου εγχειριδίου ΑΣΕΠ</a>
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

  <div id="pointsTools" class="quick-card hidden">
    <strong>🧮 Έλεγξε ξανά τα μόριά σου</strong>
    Αν η ένσταση αφορά λανθασμένο υπολογισμό μορίων, μπορείς πρώτα να κάνεις ανεξάρτητο επανυπολογισμό με το εργαλείο 1ΓΕ/2026 &amp; 2ΓΕ/2026.
    <br><a class="inline-link" href="ypologismos-morion.php" target="_blank" rel="noopener">Άνοιγμα υπολογιστή μορίων</a>
  </div>

  <div id="foreignQuestions" class="hidden">
    <div class="question">
      <label for="recognitionStatus">Έχει εκδοθεί πιστοποιητικό / πράξη αναγνώρισης του τίτλου αλλοδαπής;</label>
      <select id="recognitionStatus" onchange="updateRecognitionDate()">
        <option value="">-- Επιλογή --</option>
        <option value="issued">Ναι, έχει εκδοθεί</option>
        <option value="pending">Όχι, η αναγνώριση εκκρεμεί</option>
        <option value="none">Δεν έχει γίνει αίτηση αναγνώρισης</option>
      </select>
    </div>

    <div id="recognitionDateQuestion" class="question hidden">
      <label for="recognitionDate">Η πράξη αναγνώρισης έχει εκδοθεί έως τη λήξη της προθεσμίας ενστάσεων;</label>
      <select id="recognitionDate">
        <option value="">-- Επιλογή --</option>
        <option value="yes">Ναι</option>
        <option value="no">Όχι</option>
      </select>
    </div>
  </div>

  <div class="question">
    <label for="submissionMode"><span class="question-number">2</span>Ποια είναι η κατάσταση της ένστασής σου;</label>
    <select id="submissionMode" onchange="updateResubmissionInfo()">
      <option value="">-- Επιλογή --</option>
      <option value="first">Δεν έχω υποβάλει ακόμη ένσταση</option>
      <option value="resubmit">Έχω ήδη υποβάλει και θέλω να τη διορθώσω / επανυποβάλω</option>
      <option value="recalled">Έχω ήδη κάνει Ανάκληση και θα την υποβάλω ξανά</option>
    </select>
  </div>

  <div id="resubmissionInfo" class="quick-card hidden">
    <strong>🔁 Επανυποβολή / Ανάκληση ένστασης</strong>
    <span id="resubmissionText"></span>
    <br><a class="inline-link" href="https://info.asep.gr/sites/default/files/2023-10/e-paravolo_enstasi.pdf" target="_blank" rel="noopener">Επίσημες οδηγίες e-Παραβόλου ΑΣΕΠ</a>
  </div>

  <div class="question">
    <label for="paravoloStatus"><span class="question-number">3</span>Έχεις εκδώσει και πληρώσει το παράβολο ένστασης των 50€;</label>
    <select id="paravoloStatus" onchange="updateParavoloUI()">
      <option value="">-- Επιλογή --</option>
      <option value="paid">Ναι, είναι πληρωμένο</option>
      <option value="issued_not_paid">Έχει εκδοθεί, αλλά δεν έχει πληρωθεί</option>
      <option value="no">Όχι, δεν το έχω εκδώσει</option>
    </select>
  </div>

  <div id="paravoloValidator" class="question hidden">
    <label for="paravoloCode"><span class="question-number">4</span>Έλεγχος μορφής κωδικού e-Παραβόλου</label>
    <input id="paravoloCode" type="text" inputmode="numeric" autocomplete="off" maxlength="32" placeholder="Π.χ. 12345678901234567890" oninput="validateParavoloCode()">
    <div class="field-hint">Επικόλλησε τον κωδικό που σκοπεύεις να καταχωρίσεις στην ένσταση. Το εργαλείο ελέγχει μόνο τη <strong>μορφή</strong> του κωδικού — όχι αν είναι έγκυρος ή πληρωμένος στο σύστημα του ΑΣΕΠ.</div>
    <div id="paravoloValidation" class="validation-message neutral" role="status" aria-live="polite">Ο σωστός κωδικός e-Παραβόλου έχει ακριβώς 20 ψηφία, χωρίς κενά.</div>
  </div>

  <div class="paravolo-summary">
    <strong>Στοιχεία e-Παραβόλου ένστασης</strong>
    <ul>
      <li><strong>Φορέας Δημοσίου:</strong> Ανώτατο Συμβούλιο Επιλογής Προσωπικού (ΑΣΕΠ)</li>
      <li><strong>Κατηγορία Παραβόλου:</strong> Υποβολή ένστασης</li>
      <li><strong>Τύπος Παραβόλου:</strong> <b>[1236]</b> Υποβολή ένστασης για όλες τις διαδικασίες πλήρωσης θέσεων των φορέων της παρ. 1</li>
      <li><strong>Ποσό:</strong> 50,00 €</li>
      <li><strong>Κωδικός e-Παραβόλου:</strong> <b>20 ψηφία, χωρίς κενά</b> — αυτός καταχωρίζεται στην ηλεκτρονική ένσταση του ΑΣΕΠ.</li>
      <li><strong>Προσοχή:</strong> μην χρησιμοποιήσεις τον κωδικό πληρωμής <b>RF</b> στη θέση του 20ψήφιου κωδικού e-Παραβόλου.</li>
    </ul>
  </div>

  <button class="guide-submit edu-mt-4" type="button" id="guidanceBtn" onclick="showGuidance()">Εμφάνιση οδηγιών</button>
  <div id="result" class="result" role="status" aria-live="polite"></div>

  <p class="small-note">Το εργαλείο παρέχει ενδεικτική καθοδήγηση για τις ενστάσεις των προσωρινών πινάκων 1ΓΕ/2026 και 2ΓΕ/2026. Για την υποβολή ισχύουν η επίσημη ανακοίνωση, οι προκηρύξεις και οι οδηγίες της ηλεκτρονικής πλατφόρμας του ΑΣΕΠ.</p>
</div>

<script>
const objectionStart = new Date('2026-08-12T08:00:00+03:00');
const objectionEnd   = new Date('2026-08-21T14:00:00+03:00');

function valueOf(id){ return document.getElementById(id).value; }

function pad2(n){ return String(n).padStart(2,'0'); }

function formatRemaining(ms){
  const totalSeconds = Math.max(0, Math.floor(ms / 1000));
  const days = Math.floor(totalSeconds / 86400);
  const hours = Math.floor((totalSeconds % 86400) / 3600);
  const minutes = Math.floor((totalSeconds % 3600) / 60);
  const seconds = totalSeconds % 60;
  const parts = [];
  if(days > 0) parts.push(days + (days === 1 ? ' ημέρα' : ' ημέρες'));
  parts.push(pad2(hours) + ' ώρες');
  parts.push(pad2(minutes) + ' λεπτά');
  parts.push(pad2(seconds) + ' δευτ.');
  return parts.join(', ');
}

function updateDeadlineStatus(){
  const now = new Date();
  const box = document.getElementById('deadlineStatus');
  const btn = document.getElementById('guidanceBtn');

  box.className = 'status';
  btn.disabled = false;

  if(now < objectionStart){
    box.classList.add('before');
    box.innerHTML = '🟠 Η προθεσμία δεν έχει ανοίξει ακόμη.' +
      '<span class="countdown">Ανοίγει σε: <strong>' + formatRemaining(objectionStart - now) + '</strong></span>';
  } else if(now <= objectionEnd){
    box.classList.add('open');
    box.innerHTML = '🟢 Η προθεσμία ενστάσεων είναι ανοικτή.' +
      '<span class="countdown">Απομένουν: <strong>' + formatRemaining(objectionEnd - now) + '</strong></span>';
  } else {
    box.classList.add('closed');
    box.textContent = '🔴 Η προθεσμία ενστάσεων έχει λήξει.';
  }
}

function updateExtraQuestions(){
  const reason = valueOf('objectionReason');
  const foreign = document.getElementById('foreignQuestions');
  const pointsTools = document.getElementById('pointsTools');
  const result = document.getElementById('result');

  pointsTools.classList.toggle('hidden', reason !== 'points');

  if(reason === 'foreign'){
    foreign.classList.remove('hidden');
  } else {
    foreign.classList.add('hidden');
    document.getElementById('recognitionStatus').value = '';
    document.getElementById('recognitionDate').value = '';
    document.getElementById('recognitionDateQuestion').classList.add('hidden');
  }

  result.style.display = 'none';
}

function updateRecognitionDate(){
  const status = valueOf('recognitionStatus');
  const box = document.getElementById('recognitionDateQuestion');
  const date = document.getElementById('recognitionDate');

  if(status === 'issued'){
    box.classList.remove('hidden');
  } else {
    box.classList.add('hidden');
    date.value = '';
  }
}

function updateResubmissionInfo(){
  const mode = valueOf('submissionMode');
  const box = document.getElementById('resubmissionInfo');
  const text = document.getElementById('resubmissionText');

  if(mode === 'resubmit'){
    box.classList.remove('hidden');
    text.innerHTML = 'Χρησιμοποίησε την επιλογή <strong>«Επανυποβολή»</strong> για να τροποποιήσεις/συμπληρώσεις την ήδη υποβληθείσα ένσταση. Η αρχική υποβολή ακυρώνεται και πρέπει να γίνει νέα οριστική υποβολή <strong>εντός της προθεσμίας</strong>. Για ακύρωση και εκ νέου υποβολή στην ίδια κατηγορία εκπαίδευσης της ίδιας προκήρυξης, οι επίσημες οδηγίες αναφέρουν ότι <strong>δεν απαιτείται νέο παράβολο</strong>.';
  } else if(mode === 'recalled'){
    box.classList.remove('hidden');
    text.innerHTML = 'Αφού έχει γίνει <strong>Ανάκληση</strong>, η προηγούμενη υποβολή δεν παραμένει ενεργή. Πρέπει να ολοκληρώσεις νέα υποβολή <strong>πριν από τη λήξη της προθεσμίας</strong>. Για ακύρωση και εκ νέου υποβολή στην ίδια κατηγορία εκπαίδευσης της ίδιας προκήρυξης, <strong>δεν απαιτείται νέο παράβολο</strong>.';
  } else {
    box.classList.add('hidden');
    text.textContent = '';
  }
}

function updateParavoloUI(){
  const status = valueOf('paravoloStatus');
  const validator = document.getElementById('paravoloValidator');
  const code = document.getElementById('paravoloCode');

  if(status === 'paid' || status === 'issued_not_paid'){
    validator.classList.remove('hidden');
  } else {
    validator.classList.add('hidden');
    code.value = '';
    validateParavoloCode();
  }
}

function validateParavoloCode(){
  const input = document.getElementById('paravoloCode');
  const message = document.getElementById('paravoloValidation');
  let raw = input.value;

  // Αφαιρούνται μόνο κενά ώστε να μη μετατραπεί κατά λάθος ένας RF κωδικός σε αριθμητικό κωδικό.
  const compact = raw.replace(/\s+/g,'');
  if(raw !== compact){
    input.value = compact;
    raw = compact;
  }

  input.classList.remove('valid','invalid');
  message.className = 'validation-message neutral';

  if(raw === ''){
    message.textContent = 'Ο σωστός κωδικός e-Παραβόλου έχει ακριβώς 20 ψηφία, χωρίς κενά.';
    return 'empty';
  }

  if(/^RF/i.test(raw)){
    input.classList.add('invalid');
    message.className = 'validation-message bad';
    message.innerHTML = '❌ Αυτό μοιάζει με <strong>κωδικό πληρωμής RF</strong>. Στην ένσταση χρειάζεται ο 20ψήφιος αριθμητικός κωδικός e-Παραβόλου.';
    return 'rf';
  }

  if(!/^\d+$/.test(raw)){
    input.classList.add('invalid');
    message.className = 'validation-message bad';
    message.innerHTML = '❌ Ο κωδικός πρέπει να αποτελείται <strong>μόνο από ψηφία</strong>.';
    return 'invalid';
  }

  if(raw.length === 20){
    input.classList.add('valid');
    message.className = 'validation-message good';
    message.innerHTML = '✅ <strong>20 ψηφία — σωστή μορφή.</strong> Στην πλατφόρμα του ΑΣΕΠ πάτησε και «Έλεγχος Παραβόλου» ώστε να επιβεβαιώσεις ότι εμφανίζεται ως ΠΛΗΡΩΜΕΝΟ.';
    return 'valid';
  }

  input.classList.add('invalid');
  message.className = 'validation-message bad';
  if(raw.length === 25){
    message.innerHTML = '❌ Βρέθηκαν <strong>25 ψηφία</strong>. Έλεγξε μήπως έχεις αντιγράψει κωδικό πληρωμής αντί για τον 20ψήφιο κωδικό e-Παραβόλου.';
  } else {
    message.innerHTML = '❌ Βρέθηκαν <strong>' + raw.length + ' ψηφία</strong>. Ο κωδικός e-Παραβόλου που καταχωρίζεται στην ένσταση πρέπει να έχει ακριβώς 20.';
  }
  return 'invalid';
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
  const submissionMode = valueOf('submissionMode');
  const paravolo = valueOf('paravoloStatus');
  const paravoloCheck = validateParavoloCode();
  const result = document.getElementById('result');

  if(!reason || !submissionMode || !paravolo){
    result.style.display = 'block';
    result.innerHTML = '<h2>Λείπουν στοιχεία</h2><div class="warning-box">Επίλεξε τον λόγο της ένστασης, την κατάσταση της υποβολής και την κατάσταση του παραβόλου.</div>';
    return;
  }

  if(reason === 'foreign'){
    const recognition = valueOf('recognitionStatus');
    const recognitionDate = valueOf('recognitionDate');
    if(!recognition || (recognition === 'issued' && !recognitionDate)){
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
    html += '<div class="success-box"><strong>Η προθεσμία είναι ανοικτή.</strong> Απομένουν <strong>' + formatRemaining(objectionEnd - now) + '</strong>.</div>';
  }

  html += '<h3>1. Τεκμηρίωσε τον λόγο της ένστασης</h3>';
  html += '<div class="note-box"><strong>' + rg.title + '</strong><br>' + rg.doc + '<br><br>Η ένσταση καλό είναι να είναι συγκεκριμένη και να εξηγεί με σαφήνεια ποιο στοιχείο θεωρείς λανθασμένο και ποια διόρθωση ζητάς.</div>';

  if(reason === 'points'){
    html += '<div class="note-box"><strong>🧮 Χρήσιμος έλεγχος:</strong> πριν οριστικοποιήσεις το κείμενο της ένστασης, μπορείς να επανυπολογίσεις τα μόρια στο εργαλείο <a href="ypologismos-morion.php" target="_blank" rel="noopener">Υπολογισμός μορίων 1ΓΕ/2026 &amp; 2ΓΕ/2026</a>.</div>';
  }

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

  html += '<h3>2. Έλεγξε αν πρόκειται για πρώτη υποβολή ή επανυποβολή</h3>';
  if(submissionMode === 'first'){
    html += '<div class="note-box">Δεν έχεις υποβάλει ακόμη ένσταση. Προχώρησε σε <strong>Νέα Ένσταση</strong> και ολοκλήρωσε την οριστική υποβολή μέσα στην προθεσμία.</div>';
  } else if(submissionMode === 'resubmit'){
    html += '<div class="warning-box"><strong>Επανυποβολή:</strong> χρησιμοποίησε την αντίστοιχη επιλογή για να διορθώσεις την ήδη υποβληθείσα ένσταση και φρόντισε να την υποβάλεις ξανά οριστικά εντός προθεσμίας. Η αρχική υποβολή ακυρώνεται. <strong>Δεν απαιτείται νέο παράβολο</strong> όταν η ακύρωση και νέα υποβολή αφορά την ίδια κατηγορία εκπαίδευσης της ίδιας προκήρυξης.</div>';
  } else if(submissionMode === 'recalled'){
    html += '<div class="warning-box"><strong>Έχει προηγηθεί Ανάκληση:</strong> η προηγούμενη υποβολή δεν είναι πλέον ενεργή. Κάνε νέα οριστική υποβολή πριν τη λήξη της προθεσμίας. <strong>Δεν απαιτείται νέο παράβολο</strong> όταν η ακύρωση και νέα υποβολή αφορά την ίδια κατηγορία εκπαίδευσης της ίδιας προκήρυξης.</div>';
  }

  html += '<h3>3. Έλεγξε το e-Παράβολο</h3>';
  html += '<div class="note-box"><ul>' +
          '<li><strong>Φορέας:</strong> Ανώτατο Συμβούλιο Επιλογής Προσωπικού (ΑΣΕΠ)</li>' +
          '<li><strong>Κατηγορία:</strong> Υποβολή ένστασης</li>' +
          '<li><strong>Τύπος:</strong> [1236] Υποβολή ένστασης για όλες τις διαδικασίες πλήρωσης θέσεων των φορέων της παρ. 1</li>' +
          '<li><strong>Ποσό:</strong> 50,00 €</li>' +
          '<li><strong>Κωδικός e-Παραβόλου:</strong> 20 ψηφία, χωρίς κενά</li>' +
          '<li><strong>Στην πλατφόρμα ΑΣΕΠ:</strong> πάτησε «Έλεγχος Παραβόλου» και βεβαιώσου ότι εμφανίζεται ως <strong>ΠΛΗΡΩΜΕΝΟ</strong>.</li>' +
          '</ul></div>';

  if(paravolo === 'paid'){
    html += '<div class="success-box">Δήλωσες ότι το παράβολο είναι πληρωμένο.</div>';
  }
  if(paravolo === 'issued_not_paid'){
    html += '<div class="warning-box"><strong>Προσοχή:</strong> το παράβολο έχει εκδοθεί αλλά δεν έχει πληρωθεί. Το αντίτιμο πρέπει να καταβληθεί πριν από την υποβολή της ένστασης.</div>';
  }
  if(paravolo === 'no'){
    html += '<div class="warning-box"><strong>Χρειάζεται πρώτα έκδοση και πληρωμή του παραβόλου των 50€.</strong> Στη συνέχεια καταχωρίζεται ο 20ψήφιος αριθμός του στην ηλεκτρονική ένσταση.</div>';
  }

  if(paravolo === 'paid' || paravolo === 'issued_not_paid'){
    if(paravoloCheck === 'valid'){
      html += '<div class="success-box">✅ Ο κωδικός που πληκτρολόγησες έχει <strong>σωστή μορφή 20 ψηφίων</strong>. Αυτό δεν υποκαθιστά τον επίσημο «Έλεγχο Παραβόλου» μέσα στην πλατφόρμα του ΑΣΕΠ.</div>';
    } else if(paravoloCheck === 'empty'){
      html += '<div class="warning-box">Δεν έκανες έλεγχο μορφής του κωδικού. Προαιρετικά, επικόλλησε τον 20ψήφιο κωδικό στο σχετικό πεδίο του εργαλείου για να αποφύγεις λάθος αντιγραφή.</div>';
    } else {
      html += '<div class="danger-box"><strong>Ο κωδικός που πληκτρολόγησες δεν έχει τη σωστή μορφή 20ψήφιου e-Παραβόλου.</strong> Διόρθωσέ τον πριν από την υποβολή.</div>';
    }
  }

  html += '<h3>4. Υπέβαλε ηλεκτρονικά την ένσταση</h3>';
  html += '<div class="note-box"><ol>' +
          '<li>Μπες στον διαδικτυακό τόπο του ΑΣΕΠ.</li>' +
          '<li>Ακολούθησε τη διαδρομή <strong>Ηλεκτρονικές Υπηρεσίες → Ένσταση</strong>.</li>' +
          '<li>Συμπλήρωσε με σαφήνεια τον λόγο της ένστασης και επισύναψε τα σχετικά δικαιολογητικά όπου απαιτείται.</li>' +
          '<li>Καταχώρισε τον <strong>20ψήφιο κωδικό e-Παραβόλου</strong>.</li>' +
          '<li>Πάτησε <strong>«Έλεγχος Παραβόλου»</strong> και βεβαιώσου ότι εμφανίζεται ως <strong>ΠΛΗΡΩΜΕΝΟ</strong>.</li>' +
          '<li>Ολοκλήρωσε την οριστική υποβολή το αργότερο έως <strong>Παρασκευή 21/08/2026, ώρα 14:00</strong>.</li>' +
          '</ol>' +
          '<div class="action-links">' +
          '<a href="https://www.asep.gr" target="_blank" rel="noopener">Άνοιγμα ΑΣΕΠ</a>' +
          '<a href="https://info.asep.gr/sites/default/files/2023-10/manual_enstasi_ekpaideutikon.pdf" target="_blank" rel="noopener">Εγχειρίδιο ένστασης εκπαιδευτικών</a>' +
          '<a href="https://info.asep.gr/sites/default/files/2023-10/e-paravolo_enstasi.pdf" target="_blank" rel="noopener">Οδηγίες e-Παραβόλου</a>' +
          '<a href="https://info.asep.gr/node/79576" target="_blank" rel="noopener">Ανακοίνωση 1ΓΕ/2026 &amp; 2ΓΕ/2026</a>' +
          '</div></div>';

  html += '<div class="warning-box"><strong>Τελικός έλεγχος πριν την υποβολή:</strong><ul>' +
          '<li>σωστός λόγος ένστασης,</li>' +
          '<li>σαφής τεκμηρίωση και σχετικά δικαιολογητικά,</li>' +
          '<li>σωστός <strong>20ψήφιος κωδικός e-Παραβόλου</strong>,</li>' +
          '<li>ένδειξη <strong>ΠΛΗΡΩΜΕΝΟ</strong> μετά τον επίσημο έλεγχο παραβόλου,</li>' +
          (submissionMode === 'first' ? '' : '<li>αν έγινε Επανυποβολή/Ανάκληση, να έχει ολοκληρωθεί <strong>νέα οριστική υποβολή</strong>,</li>') +
          '<li>οριστική υποβολή εντός της προθεσμίας.</li>' +
          '</ul></div>';

  result.innerHTML = html;
  result.style.display = 'block';
  result.scrollIntoView({behavior:'smooth',block:'start'});
}

updateDeadlineStatus();
setInterval(updateDeadlineStatus, 1000);
updateExtraQuestions();
updateRecognitionDate();
updateResubmissionInfo();
updateParavoloUI();
</script>

<?php sourceCardStart(); ?>
  <p>Προκηρύξεις Α.Σ.Ε.Π. 1ΓΕ/2026 και 2ΓΕ/2026, η επίσημη ανακοίνωση για τις ενστάσεις των προσωρινών πινάκων, καθώς και τα επίσημα εγχειρίδια Α.Σ.Ε.Π. για ηλεκτρονική ένσταση και e-Παράβολο.</p>
  <?php sourceCardLinksStart(); ?><?php sourceCardLink('https://info.asep.gr/node/79576', 'Ανακοίνωση ενστάσεων — ΑΣΕΠ ↗'); ?><?php sourceCardLink('https://info.asep.gr/sites/default/files/2023-10/manual_enstasi_ekpaideutikon.pdf', 'Εγχειρίδιο ένστασης ↗'); ?><?php sourceCardLink('https://info.asep.gr/sites/default/files/2023-10/e-paravolo_enstasi.pdf', 'Οδηγίες e-Παραβόλου ↗'); ?><?php sourceCardLinksEnd(); ?>
<?php sourceCardEnd(); ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
  <script src="<?php echo htmlspecialchars(edu_asset_url('assets/common.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
</body>
</html>
