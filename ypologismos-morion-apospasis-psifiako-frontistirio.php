<!doctype html>
<html lang="el">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="Υπολογισμός μοριοδότησης για απόσπαση μονίμων εκπαιδευτικών στο Ψηφιακό Φροντιστήριο 2026-2027.">
  <title>Υπολογισμός μορίων απόσπασης στο Ψηφιακό Φροντιστήριο</title>
  <link rel="stylesheet" href="assets/common.css?v=3.20.12-rc1">
</head>
<body class="edu-ui edu-page-digital-tutoring">
  <main class="page-shell">
    <?php require_once __DIR__ . '/includes/header.php'; ?>

    <section class="hero">
      <h1>Υπολογισμός μορίων απόσπασης στο Ψηφιακό Φροντιστήριο</h1>
      <p>Ενδεικτικός υπολογισμός για την πρόσκληση αποσπάσεων μονίμων εκπαιδευτικών για το σχολικό έτος 2026–2027.</p>
      <div class="hero-meta">
        <span>Σύνολο: 100 μονάδες</span>
        <span>Β + Γ πριν από τη συνέντευξη: έως 65</span>
        <span>Βιντεοσκοπημένο μάθημα: βάση 20/35</span>
      </div>
    </section>

    <div class="layout">
      <div>
        <section class="card">
          <h2>Προϋποθέσεις που επηρεάζουν τη διαδικασία</h2>
          <p class="subtitle">Δεν μοριοδοτούνται, αλλά μπορεί να επηρεάσουν το αν η αίτηση είναι παραδεκτή ή αν συνεχίζεις στη διαδικασία.</p>
          <div class="field-grid">
            <div class="field">
              <label for="requiredExperience">Έχεις τουλάχιστον 2 έτη διδακτικής προϋπηρεσίας στο σχετικό μάθημα της Γ΄ Λυκείου;</label>
              <select id="requiredExperience">
                <option value="">— Επίλεξε —</option>
                <option value="yes">Ναι</option>
                <option value="no">Όχι</option>
              </select>
            </div>
            <div class="field">
              <label for="videoFace">Εμφανίζεται το πρόσωπό σου στο βιντεοσκοπημένο μάθημα;</label>
              <select id="videoFace">
                <option value="">— Επίλεξε —</option>
                <option value="yes">Ναι</option>
                <option value="no">Όχι</option>
              </select>
            </div>
            <div class="field">
              <label for="videoDuration">Η διάρκεια του βίντεο είναι 4–7 λεπτά;</label>
              <select id="videoDuration">
                <option value="">— Επίλεξε —</option>
                <option value="yes">Ναι</option>
                <option value="no">Όχι</option>
              </select>
            </div>
            <div class="field">
              <label for="eaePosition">Υποβάλλεις αίτηση για θέση Ειδικής Αγωγής (ΕΑΕ);</label>
              <select id="eaePosition" onchange="toggleEae()">
                <option value="no">Όχι</option>
                <option value="yes">Ναι</option>
              </select>
            </div>
            <div class="field full hidden" id="eaeSpecializationWrap">
              <label for="eaeSpecialization">Διαθέτεις την απαιτούμενη εξειδίκευση στην ΕΑΕ;</label>
              <select id="eaeSpecialization">
                <option value="">— Επίλεξε —</option>
                <option value="yes">Ναι</option>
                <option value="no">Όχι</option>
              </select>
            </div>
          </div>
          <div class="warning">Η πρόσκληση προβλέπει και επιπλέον κωλύματα/προϋποθέσεις απόσπασης. Το παρόν εργαλείο δεν αντικαθιστά τον πλήρη έλεγχο της πρόσκλησης και του ΠΥΜ.</div>
        </section>

        <section class="card">
          <div class="criterion-head">
            <div>
              <h2>Α. Γενική παρουσία</h2>
              <p class="subtitle">Αποτιμάται στη διά ζώσης συνέντευξη.</p>
            </div>
            <div class="max">έως 35</div>
          </div>
          <div class="score-row">
            <label for="a1"><strong>Α1. Συγκρότηση σκέψης – λόγου</strong><small>Μέγιστο 20 · βάση επιλογής 12</small></label>
            <input type="number" id="a1" min="0" max="20" step="0.1" value="0" oninput="calculate()">
          </div>
          <div class="score-row">
            <label for="a2"><strong>Α2. Επικοινωνιακές δεξιότητες</strong><small>Μέγιστο 15 · βάση επιλογής 8</small></label>
            <input type="number" id="a2" min="0" max="15" step="0.1" value="0" oninput="calculate()">
          </div>
          <div class="info">Αν δεν έχει πραγματοποιηθεί ακόμη η συνέντευξη, άφησε τα Α1 και Α2 στο 0. Το εργαλείο θα σου δείξει ξεχωριστά τη βαθμολογία Β + Γ που χρησιμοποιείται πριν από τη συνέντευξη.</div>
        </section>

        <section class="card">
          <div class="criterion-head">
            <div>
              <h2>Β. Επιστημονική κατάρτιση – εμπειρία</h2>
              <p class="subtitle">Τα επιμέρους κριτήρια αθροίζουν έως 30 μονάδες.</p>
            </div>
            <div class="max">έως 30</div>
          </div>

          <div class="field-grid">
            <div class="field">
              <label for="phd">Συναφές διδακτορικό δίπλωμα <small>+12 μονάδες</small></label>
              <select id="phd" onchange="calculate()"><option value="0">Όχι</option><option value="12">Ναι</option></select>
            </div>
            <div class="field">
              <label for="master">Συναφής μεταπτυχιακός τίτλος <small>+8 μονάδες</small></label>
              <select id="master" onchange="calculate()"><option value="0">Όχι</option><option value="8">Ναι</option></select>
            </div>
            <div class="field">
              <label for="examExperience">Εμπειρία σε Πανελλαδικές Εξετάσεις <small>Θεματοδότης ή βαθμολογητής ή υπεύθυνος μαθήματος σε Βαθμολογικό Κέντρο · +2</small></label>
              <select id="examExperience" onchange="calculate()"><option value="0">Όχι</option><option value="2">Ναι</option></select>
            </div>
            <div class="field">
              <label for="branchPe86">Κλάδος ΠΕ86; <small>Για τον ΠΕ86 τεκμαίρεται η γνώση ΤΠΕ Α΄ επιπέδου, αν δεν δηλωθεί ανώτερη πιστοποίηση.</small></label>
              <select id="branchPe86" onchange="calculate()"><option value="no">Όχι</option><option value="yes">Ναι</option></select>
            </div>
          </div>

          <div class="score-row">
            <div>
              <strong>Β4. Πρόσθετη διδακτική προϋπηρεσία στο πανελλαδικώς εξεταζόμενο μάθημα</strong>
              <small>Μόνο πέραν των απαιτούμενων 2 ετών και σε πανελλαδικώς εξεταζόμενο μάθημα του ίδιου κλάδου με την προκηρυσσόμενη θέση, σε δημόσιο ή ιδιωτικό σχολείο ή στο Ψηφιακό Φροντιστήριο. 2 μονάδες ανά πλήρες διδακτικό έτος, 1 μονάδα ανά τετράμηνο, έως 6. Χρόνος μικρότερος του τετραμήνου δεν υπολογίζεται.</small>
            </div>
            <div>
              <label for="extraYears" class="edu-font-13">Πλήρη επιπλέον έτη</label>
              <input type="number" id="extraYears" min="0" step="1" value="0" oninput="normalizeInteger(this);calculate()">
              <label for="extraMonths" class="edu-font-13 edu-mt-8">Υπόλοιπο μηνών (0–11)</label>
              <input type="number" id="extraMonths" min="0" max="11" step="1" value="0" oninput="normalizeInteger(this,11);calculate()">
            </div>
          </div>

          <div class="score-row">
            <label for="ict"><strong>Β5. Πιστοποιημένη γνώση Τ.Π.Ε.</strong><small>Αν υπάρχουν περισσότερες από μία πιστοποιήσεις, βαθμολογείται μόνο η ανώτερη.</small></label>
            <select id="ict" onchange="calculate()">
              <option value="0">Καμία</option>
              <option value="1">Α΄ επίπεδο / άρθρο 9 π.δ. 85/2022 — 1</option>
              <option value="1.5">Β1 — 1,5</option>
              <option value="2">Β ή Β2 — 2</option>
            </select>
          </div>
        </section>

        <section class="card">
          <div class="criterion-head">
            <div>
              <h2>Γ. Βιντεοσκοπημένο μάθημα</h2>
              <p class="subtitle">Ολοκληρωμένη εξ αποστάσεως διδασκαλία διάρκειας 4–7 λεπτών.</p>
            </div>
            <div class="max">έως 35</div>
          </div>
          <div class="score-row">
            <label for="videoScore"><strong>Γ1. Βαθμολογία βιντεοσκοπημένου μαθήματος</strong><small>Μέγιστο 35 · βάση επιλογής 20</small></label>
            <input type="number" id="videoScore" min="0" max="35" step="0.1" value="" placeholder="0–35" oninput="calculate()">
          </div>
          <div class="note">Για την κλήση σε συνέντευξη λαμβάνονται υπόψη οι περισσότεροι βαθμοί στις κατηγορίες <strong>Β + Γ</strong>, με απαραίτητη βάση τουλάχιστον <strong>20 μονάδων στη Γ</strong>. Η πρόσκληση προβλέπει κλήση κατά ανώτατο όριο του τριπλάσιου αριθμού υποψηφίων σε σχέση με τις θέσεις.</div>
        </section>
      </div>

      <aside class="results" aria-live="polite">
        <section class="card">
          <div class="big-total">
            <div class="number" id="totalScore">0</div>
            <div class="outof">/ 100 μονάδες</div>
          </div>
          <div class="bar"><div id="totalBar"></div></div>

          <div class="result-row"><span>Α. Γενική παρουσία</span><strong id="aTotal">0 / 35</strong></div>
          <div class="result-row"><span>Β. Κατάρτιση – εμπειρία</span><strong id="bTotal">0 / 30</strong></div>
          <div class="result-row"><span>Γ. Βιντεοσκοπημένο μάθημα</span><strong id="cTotal">0 / 35</strong></div>

          <div class="pre-interview">
            <div>Β + Γ πριν από τη συνέντευξη</div>
            <strong id="preInterview">0 / 65</strong>
          </div>

          <div id="statusBox" class="status-box" role="status" aria-live="polite"></div>

          <div class="actions">
            <button class="primary" type="button" onclick="calculate()">Υπολογισμός</button>
            <button class="secondary" type="button" onclick="resetForm()">Καθαρισμός</button>
          </div>
        </section>

        <section class="card">
          <h2>Ανάλυση Β</h2>
          <div class="result-row"><span>Β1. Διδακτορικό</span><strong id="b1Result">0</strong></div>
          <div class="result-row"><span>Β2. Μεταπτυχιακό</span><strong id="b2Result">0</strong></div>
          <div class="result-row"><span>Β3. Πανελλαδικές</span><strong id="b3Result">0</strong></div>
          <div class="result-row"><span>Β4. Πρόσθετη προϋπηρεσία</span><strong id="b4Result">0</strong></div>
          <div class="result-row"><span>Β5. Τ.Π.Ε.</span><strong id="b5Result">0</strong></div>
        </section>
      </aside>
    </div>

    <section class="edu-source-card" aria-labelledby="sourcesTitle"><h2 id="sourcesTitle">Πηγές / Νομική βάση</h2><p><strong>Πηγή:</strong> Πρόσκληση 86300/Δ7/29.06.2026 για αποσπάσεις μονίμων εκπαιδευτικών στο Ψηφιακό Φροντιστήριο για το σχολικό έτος 2026–2027.</p><div class="source-links"><a href="https://diavgeia.gov.gr/doc/934%CE%9946%CE%9D%CE%9A%CE%A0%CE%94-80%CE%9C?inline=true" target="_blank" rel="noopener noreferrer">Πρόσκληση Ψηφιακού Φροντιστηρίου — Διαύγεια (ΑΔΑ 934Ι46ΝΚΠΔ-80Μ) ↗</a></div><p class="source-disclaimer">Το εργαλείο παρέχει ενδεικτικό υπολογισμό και δεν αντικαθιστά την επίσημη πρόσκληση, τα δικαιολογητικά και την κρίση της αρμόδιας επιτροπής.</p></section>
</main>

<script>
  function n(id){
    const value = Number(document.getElementById(id).value || 0);
    return Number.isFinite(value) ? value : 0;
  }

  function clamp(value,min,max){ return Math.min(Math.max(value,min),max); }

  function format(value){
    const rounded = Math.round(value * 10) / 10;
    return Number.isInteger(rounded) ? String(rounded) : rounded.toFixed(1).replace('.', ',');
  }

  function normalizeInteger(input,max=null){
    let value = Math.floor(Number(input.value || 0));
    value = Math.max(0,value);
    if(max !== null) value = Math.min(max,value);
    input.value = value;
  }

  function toggleEae(){
    const show = document.getElementById('eaePosition').value === 'yes';
    document.getElementById('eaeSpecializationWrap').classList.toggle('hidden', !show);
    calculate();
  }

  function calculate(){
    const a1 = clamp(n('a1'),0,20);
    const a2 = clamp(n('a2'),0,15);
    const a = a1 + a2;

    const b1 = n('phd');
    const b2 = n('master');
    const b3 = n('examExperience');

    const years = Math.max(0,Math.floor(n('extraYears')));
    const months = clamp(Math.floor(n('extraMonths')),0,11);
    const b4 = Math.min(6, years * 2 + Math.floor(months / 4));

    let b5 = n('ict');
    const pe86 = document.getElementById('branchPe86').value === 'yes';
    if(pe86 && b5 < 1) b5 = 1;

    const b = Math.min(30,b1+b2+b3+b4+b5);
    const c = clamp(n('videoScore'),0,35);
    const pre = b + c;
    const total = a + b + c;

    document.getElementById('aTotal').textContent = format(a) + ' / 35';
    document.getElementById('bTotal').textContent = format(b) + ' / 30';
    document.getElementById('cTotal').textContent = format(c) + ' / 35';
    document.getElementById('preInterview').textContent = format(pre) + ' / 65';
    document.getElementById('totalScore').textContent = format(total);
    document.getElementById('totalBar').style.width = clamp(total,0,100) + '%';

    document.getElementById('b1Result').textContent = format(b1);
    document.getElementById('b2Result').textContent = format(b2);
    document.getElementById('b3Result').textContent = format(b3);
    document.getElementById('b4Result').textContent = format(b4);
    document.getElementById('b5Result').textContent = format(b5);

    const requiredExperienceValue = document.getElementById('requiredExperience').value;
    const videoFaceValue = document.getElementById('videoFace').value;
    const videoDurationValue = document.getElementById('videoDuration').value;
    const eaePosition = document.getElementById('eaePosition').value === 'yes';
    const eaeSpecializationValue = document.getElementById('eaeSpecialization').value;

    const issues = [];
    const unanswered = [];
    if(requiredExperienceValue === '') unanswered.push('διετής διδακτική προϋπηρεσία');
    else if(requiredExperienceValue === 'no') issues.push('Δεν δηλώνεται η απαιτούμενη διετής διδακτική προϋπηρεσία στο σχετικό μάθημα.');

    if(videoFaceValue === '') unanswered.push('εμφάνιση προσώπου στο βίντεο');
    else if(videoFaceValue === 'no') issues.push('Η πρόσκληση ορίζει ότι αν δεν εμφανίζεται το πρόσωπο του/της εκπαιδευτικού στο βίντεο, ο/η υποψήφιος/α αποκλείεται.');

    if(videoDurationValue === '') unanswered.push('διάρκεια βίντεο');
    else if(videoDurationValue === 'no') issues.push('Το βιντεοσκοπημένο μάθημα πρέπει να έχει διάρκεια 4–7 λεπτά.');

    if(eaePosition){
      if(eaeSpecializationValue === '') unanswered.push('εξειδίκευση ΕΑΕ');
      else if(eaeSpecializationValue === 'no') issues.push('Για θέση ΕΑΕ απαιτείται η προβλεπόμενη εξειδίκευση στην ΕΑΕ.');
    }

    const status = [];
    const videoScoreFilled = document.getElementById('videoScore').value !== '';
    if(!videoScoreFilled){
      status.push('<div class="info"><strong>Βαθμολογία βίντεο:</strong> συμπλήρωσε τη βαθμολογία Γ όταν είναι διαθέσιμη.</div>');
    } else if(c < 20){
      status.push('<div class="danger"><strong>Δεν καλύπτεται η βάση του βιντεοσκοπημένου μαθήματος:</strong> απαιτούνται τουλάχιστον 20/35.</div>');
    } else {
      status.push('<div class="success"><strong>Καλύπτεται η βάση Γ:</strong> ' + format(c) + '/35. Η κλήση σε συνέντευξη εξαρτάται και από τη σχετική κατάταξη Β+Γ έναντι των άλλων υποψηφίων.</div>');
    }

    if((a1 > 0 || a2 > 0) && (a1 < 12 || a2 < 8)){
      const missing=[];
      if(a1 < 12) missing.push('Α1 κάτω από 12');
      if(a2 < 8) missing.push('Α2 κάτω από 8');
      status.push('<div class="danger"><strong>Βάσεις συνέντευξης:</strong> ' + missing.join(' · ') + '.</div>');
    } else if(a1 >= 12 && a2 >= 8){
      status.push('<div class="success"><strong>Καλύπτονται και οι δύο βάσεις της συνέντευξης.</strong></div>');
    }

    if(unanswered.length){
      status.push('<div class="warning"><strong>Συμπλήρωσε τις προϋποθέσεις:</strong> ' + unanswered.join(' · ') + '.</div>');
    }
    if(issues.length){
      status.push('<div class="danger"><strong>Έλεγχος προϋποθέσεων:</strong><ul class="edu-list-compact"><li>' + issues.join('</li><li>') + '</li></ul></div>');
    }

    document.getElementById('statusBox').innerHTML = status.join('');
  }

  function resetForm(){
    document.querySelectorAll('input[type="number"]').forEach(el => el.value = 0);
    document.getElementById('videoScore').value = '';
    document.querySelectorAll('select').forEach(el => {
      if(el.id === 'requiredExperience' || el.id === 'videoFace' || el.id === 'videoDuration' || el.id === 'eaeSpecialization') el.value='';
      else el.selectedIndex=0;
    });
    toggleEae();
    window.scrollTo({top:0,behavior:'smooth'});
  }

  toggleEae();
  calculate();
</script>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
  <script src="assets/common.js?v=3.20.10"></script>
</body>
</html>
