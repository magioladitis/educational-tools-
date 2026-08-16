<!doctype html>
<html lang="el">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="Υπολογισμός μορίων και βασικός έλεγχος δικαιώματος για απόσπαση εκπαιδευτικών σε ελληνόγλωσσες εκπαιδευτικές μονάδες του εξωτερικού.">
  <title>Μόρια Απόσπασης στο Εξωτερικό</title>
  <style>
    :root{
      --bg:#f4f7fb;--card:#fff;--text:#18202b;--muted:#5f6b7a;--border:#dfe5ec;
      --blue:#1f6feb;--blue-dark:#174ea6;--green:#18794e;--green-soft:#eaf7f0;
      --orange:#9a4d00;--orange-soft:#fff4e5;--red:#b42318;--red-soft:#fff0ee;
      --purple:#6941c6;--purple-soft:#f1edff;--shadow:0 10px 30px rgba(28,39,55,.09)
    }
    *{box-sizing:border-box}
    body{margin:0;font-family:Arial,Helvetica,sans-serif;background:var(--bg);color:var(--text);line-height:1.55}
    .page-shell{max-width:1180px;margin:0 auto;padding:28px 22px 50px}
    .hero{background:linear-gradient(135deg,#123c69 0%,#1f6feb 58%,#4b83d8 100%);color:#fff;border-radius:20px;padding:30px;box-shadow:var(--shadow);margin-bottom:20px}
    .hero h1{margin:0 0 9px;font-size:clamp(28px,4vw,40px);line-height:1.15}
    .hero p{margin:5px 0;color:rgba(255,255,255,.94);max-width:940px}
    .hero-meta{display:flex;gap:9px;flex-wrap:wrap;margin-top:17px}
    .hero-meta span{background:rgba(0,0,0,.16);padding:6px 10px;border-radius:999px;font-size:13px;font-weight:bold}
    .layout{display:grid;grid-template-columns:minmax(0,1fr) 350px;gap:18px;align-items:start}
    .card{background:var(--card);border:1px solid var(--border);border-radius:16px;padding:18px;margin-bottom:16px;box-shadow:0 5px 18px rgba(28,39,55,.05)}
    .card h2{margin:0 0 5px;font-size:20px}.card h3{margin:18px 0 8px;font-size:16px}
    .subtitle{margin:0 0 15px;color:var(--muted);font-size:14px}
    .field-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:13px}
    .field{border:1px solid #e5e9ef;border-radius:12px;padding:13px;background:#fbfcfe}
    .field.full{grid-column:1/-1}
    label{display:block;font-weight:700;margin-bottom:7px}
    label small{display:block;font-weight:400;color:var(--muted);margin-top:3px;line-height:1.4}
    input[type="number"],select{width:100%;padding:10px 11px;border:1px solid #cfd7e2;border-radius:9px;font-size:15px;background:#fff;color:var(--text)}
    select{cursor:pointer}.hidden{display:none!important}
    .check{display:flex;gap:10px;align-items:flex-start;border:1px solid #e5e9ef;border-radius:12px;padding:13px;background:#fbfcfe;margin-top:10px}
    .check input{margin-top:4px}.check label{margin:0;cursor:pointer}
    .note,.info,.warning,.danger,.success{margin-top:13px;padding:12px 13px;border-radius:11px;font-size:14px;line-height:1.5}
    .note,.info{background:#eef4ff;border:1px solid #d6e4ff;color:#174ea6}
    .warning{background:var(--orange-soft);border:1px solid #f0d4a8;color:#7b4900}
    .danger{background:var(--red-soft);border:1px solid #f3c1bc;color:#8f1f17}
    .success{background:var(--green-soft);border:1px solid #b7e3c9;color:#12633f}
    .score-row{display:flex;justify-content:space-between;gap:18px;align-items:center;padding:11px 0;border-bottom:1px solid #edf0f4}
    .score-row:last-child{border-bottom:0}.score-row strong{font-variant-numeric:tabular-nums;color:var(--blue-dark)}
    .results{position:sticky;top:14px}
    .big-total{text-align:center;padding:7px 0 15px}
    .big-total .number{font-size:56px;font-weight:800;line-height:1;color:var(--blue);font-variant-numeric:tabular-nums}
    .big-total .outof{color:var(--muted);margin-top:5px}
    .bar{height:11px;background:#e5e7eb;border-radius:999px;overflow:hidden;margin:12px 0}
    .bar div{height:100%;width:0;background:linear-gradient(90deg,#1f6feb,#6941c6);transition:width .2s ease}
    .result-row{display:flex;justify-content:space-between;gap:12px;padding:9px 0;border-top:1px solid #edf0f4;font-size:14px}
    .result-row strong{font-variant-numeric:tabular-nums}
    .actions{display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-top:14px}
    button{border:0;border-radius:10px;padding:11px 12px;font-weight:bold;cursor:pointer;font-size:14px}
    .primary{background:var(--blue);color:#fff}.secondary{background:#e8edf4;color:#253247}
    details{margin-top:12px}summary{cursor:pointer;font-weight:bold;color:var(--blue-dark)}
    .criteria-list{margin:8px 0 0;padding-left:20px;color:#4f5967;font-size:14px}.criteria-list li{margin:5px 0}
    .source-note{font-size:13px;color:#667085;margin-top:18px;text-align:justify}
    @media(max-width:920px){.layout{grid-template-columns:1fr}.results{position:static}}
    @media(max-width:650px){.page-shell{padding:16px 12px 34px}.hero{padding:24px 19px}.field-grid{grid-template-columns:1fr}.field.full{grid-column:auto}.actions{grid-template-columns:1fr}}
  </style>
  <link rel="stylesheet" href="assets/common.css">
</head>
<body>
<main class="page-shell">
  <?php require_once __DIR__ . '/includes/header.php'; ?>

  <section class="hero">
    <h1>Μόρια Απόσπασης στο Εξωτερικό</h1>
    <p>Ενδεικτικός υπολογισμός μορίων και βασικός έλεγχος δικαιώματος για απόσπαση σε ελληνόγλωσσες εκπαιδευτικές μονάδες του εξωτερικού.</p>
    <div class="hero-meta">
      <span>Βασικός Πίνακας: έως 185*</span>
      <span>Εναλλακτικός: έως 165*</span>
      <span>Β2: προαπαιτούμενο · 0 μόρια</span>
      <span>Υ.Α. 83046/Η2/2020</span>
    </div>
  </section>

  <div class="layout">
    <div>

      <section class="card">
        <h2>1. Βασικός έλεγχος δικαιώματος</h2>
        <p class="subtitle">Τα πεδία ξεκινούν ουδέτερα ώστε το εργαλείο να μη θεωρεί καμία κρίσιμη προϋπόθεση ως δεδομένη.</p>

        <div class="field-grid">
          <div class="field">
            <label for="branchAllowed">Ειδικότητα για τη χώρα / περιοχή
              <small>Η ειδικότητά μου περιλαμβάνεται στο Παράρτημα ΙΙΙ της τρέχουσας πρόσκλησης για τη χώρα ή περιοχή που με ενδιαφέρει.</small>
            </label>
            <select id="branchAllowed">
              <option value="">— Επίλεξε —</option>
              <option value="yes">Ναι</option>
              <option value="no">Όχι / δεν εντοπίζεται</option>
            </select>
          </div>

          <div class="field">
            <label for="educationYears">Συνολικά έτη εκπαιδευτικής υπηρεσίας
              <small>Απαιτούνται τουλάχιστον 5 έτη.</small>
            </label>
            <input type="number" id="educationYears" min="0" step="1" inputmode="numeric" value="" placeholder="π.χ. 12">
          </div>

          <div class="field">
            <label for="teachingYears">Έτη διδακτικής υπηρεσίας μετά το ΦΕΚ διορισμού
              <small>Απαιτούνται τουλάχιστον 3 έτη σε σχολεία Πρωτοβάθμιας ή Δευτεροβάθμιας Εκπαίδευσης.</small>
            </label>
            <input type="number" id="teachingYears" min="0" step="1" inputmode="numeric" value="" placeholder="π.χ. 8">
          </div>

          <div class="field">
            <label for="blockingIssue">Κώλυμα απόσπασης
              <small>Υπάρχει κάποιο από τα κωλύματα της τρέχουσας πρόσκλησης;</small>
            </label>
            <select id="blockingIssue">
              <option value="">— Επίλεξε —</option>
              <option value="no">Όχι</option>
              <option value="yes">Ναι / πιθανόν</option>
            </select>
          </div>

          <div class="field">
            <label for="tableType">Αξιολογικός πίνακας</label>
            <select id="tableType">
              <option value="">— Επίλεξε —</option>
              <option value="main">Βασικός Πίνακας — γλώσσα χώρας υποδοχής</option>
              <option value="alternative">Εναλλακτικός Πίνακας — Αγγλικά / Γαλλικά / Γερμανικά</option>
            </select>
          </div>

          <div class="field">
            <label for="bilingualPosition">Η θέση απαιτεί διδασκαλία σε δύο γλώσσες;
              <small>Σε αυτή την περίπτωση απαιτείται τουλάχιστον Γ1/C1 στη γλώσσα της χώρας υποδοχής.</small>
            </label>
            <select id="bilingualPosition">
              <option value="">— Επίλεξε —</option>
              <option value="no">Όχι</option>
              <option value="yes">Ναι</option>
            </select>
          </div>
        </div>

        <details>
          <summary>Τι θεωρείται κώλυμα στην τρέχουσα πρόσκληση;</summary>
          <ul class="criteria-list">
            <li>Προηγούμενη διακοπή απόσπασης για πλημμελή άσκηση καθηκόντων, ανεπάρκεια ή αδικαιολόγητες/μακρές απουσίες.</li>
            <li>Ορισμένες ποινικές ή πειθαρχικές περιπτώσεις ή εκκρεμής Ε.Δ.Ε.</li>
            <li>Ολοκλήρωση του προβλεπόμενου χρόνου στα Ευρωπαϊκά Σχολεία.</li>
            <li>Οικειοθελής διακοπή ή ανάκληση απόσπασης όταν δεν έχει παρέλθει η προβλεπόμενη διετία.</li>
            <li>Υπηρεσία με απόσπαση στο εξωτερικό κατά το έτος που ορίζει η πρόσκληση ή συμπλήρωση τριετίας με επιμίσθιο.</li>
            <li>Ειδικοί περιορισμοί για επιχορηγούμενα σχολεία της Βαυαρίας.</li>
          </ul>
          <div class="note">Ο παραπάνω κατάλογος είναι συνοπτικός. Για οριακή περίπτωση απαιτείται έλεγχος του πλήρους κειμένου της εκάστοτε πρόσκλησης.</div>
        </details>
      </section>

      <section class="card">
        <h2>2. Τίτλοι σπουδών</h2>
        <p class="subtitle">Οι τίτλοι μοριοδοτούνται αθροιστικά σύμφωνα με τον πίνακα του άρθρου 3.</p>

        <div class="check">
          <input type="checkbox" id="phd">
          <label for="phd">Διδακτορική διατριβή / διδακτορικό δίπλωμα <small>50 μόρια</small></label>
        </div>
        <div class="check">
          <input type="checkbox" id="master">
          <label for="master">Μεταπτυχιακός τίτλος σπουδών <small>25 μόρια</small></label>
        </div>
        <div class="check">
          <input type="checkbox" id="secondMaster">
          <label for="secondMaster">Δεύτερο μεταπτυχιακό <small>15 μόρια</small></label>
        </div>
        <div class="check">
          <input type="checkbox" id="secondDegree">
          <label for="secondDegree">Δεύτερο πτυχίο Α.Ε.Ι. <small>15 μόρια</small></label>
        </div>
      </section>

      <section class="card">
        <h2>3. Γλωσσομάθεια αξιολογικού πίνακα</h2>
        <p class="subtitle">Το Β2 είναι το ελάχιστο επίπεδο συμμετοχής και δεν μοριοδοτείται.</p>

        <div id="mainLanguageWrap" class="field">
          <label for="primaryLevel" id="primaryLevelLabel">Επίπεδο γλώσσας χώρας υποδοχής</label>
          <select id="primaryLevel">
            <option value="">— Επίλεξε —</option>
            <option value="b2">Β2 — Καλή γνώση · 0 μόρια</option>
            <option value="c1">Γ1 / C1 — Πολύ καλή γνώση</option>
            <option value="c2">Γ2 / C2 — Άριστη γνώση</option>
          </select>
        </div>

        <div id="alternativeFields" class="field-grid hidden" style="margin-top:13px">
          <div class="field">
            <label for="alternativeLanguage">Εναλλακτική γλώσσα</label>
            <select id="alternativeLanguage">
              <option value="">— Επίλεξε —</option>
              <option value="english">Αγγλικά</option>
              <option value="french">Γαλλικά</option>
              <option value="german">Γερμανικά</option>
            </select>
          </div>
          <div class="field">
            <label for="alternativeDifferentFromCountry">Η παραπάνω γλώσσα είναι διαφορετική από τη γλώσσα της χώρας απόσπασης;</label>
            <select id="alternativeDifferentFromCountry">
              <option value="">— Επίλεξε —</option>
              <option value="yes">Ναι</option>
              <option value="no">Όχι</option>
            </select>
          </div>
        </div>

        <div id="hostBilingualWrap" class="field hidden" style="margin-top:13px">
          <label for="hostBilingualLevel">Γλώσσα χώρας υποδοχής για δίγλωσση διδασκαλία
            <small>Στον Εναλλακτικό Πίνακα, αν η θέση απαιτεί διδασκαλία σε δύο γλώσσες, δηλώνεται ξεχωριστά το επίπεδο της γλώσσας της χώρας.</small>
          </label>
          <select id="hostBilingualLevel">
            <option value="">— Επίλεξε —</option>
            <option value="b2">Β2</option>
            <option value="c1">Γ1 / C1</option>
            <option value="c2">Γ2 / C2</option>
          </select>
        </div>

        <div id="primaryLanguageHelp" class="note">Επίλεξε πρώτα Βασικό ή Εναλλακτικό Πίνακα.</div>
      </section>

      <section class="card">
        <h2>4. Δεύτερη ξένη γλώσσα</h2>
        <p class="subtitle">Πρόκειται για πρόσθετη, διαφορετική ξένη γλώσσα από εκείνη που χρησιμοποιείται για την κατάταξη στον πίνακα.</p>

        <div class="field-grid">
          <div class="field">
            <label for="secondLanguageLevel">Επίπεδο δεύτερης ξένης γλώσσας</label>
            <select id="secondLanguageLevel">
              <option value="none">Δεν δηλώνω δεύτερη ξένη γλώσσα</option>
              <option value="b2">Β2 — 10 μόρια</option>
              <option value="c1">Γ1 / C1 — 20 μόρια</option>
              <option value="c2">Γ2 / C2 — 30 μόρια</option>
            </select>
          </div>

          <div class="field" id="secondLanguageDistinctWrap">
            <label for="secondLanguageDistinct">Είναι διαφορετική γλώσσα από εκείνη του αξιολογικού πίνακα;</label>
            <select id="secondLanguageDistinct">
              <option value="">— Επίλεξε —</option>
              <option value="yes">Ναι</option>
              <option value="no">Όχι</option>
            </select>
          </div>
        </div>
      </section>

      <section class="card">
        <h2>5. Κριτήρια ισοβαθμίας</h2>
        <p class="subtitle">Δεν προσθέτουν μόρια. Εφαρμόζονται με την ακόλουθη σειρά όταν υπάρχει ισοβαθμία στον ίδιο πίνακα.</p>
        <ol class="criteria-list">
          <li>Σειρά προτίμησης της συγκεκριμένης χώρας.</li>
          <li>Επίπεδο γλωσσομάθειας.</li>
          <li>Χηρεία με παιδιά.</li>
          <li>Μονογονεϊκή οικογένεια.</li>
          <li>Διάζευξη με παιδιά υπό την επιμέλεια του/της εκπαιδευτικού.</li>
          <li>Αριθμός τέκνων.</li>
          <li>Γνώση τρίτης ξένης γλώσσας: Γ2, έπειτα Γ1, έπειτα Β2.</li>
          <li>Συνολική υπηρεσία από το ΦΕΚ διορισμού.</li>
        </ol>
        <div class="note">Αν εξακολουθεί η ισοβαθμία, λαμβάνονται υπόψη η ημερομηνία και η σειρά δημοσίευσης του διορισμού στο ΦΕΚ.</div>
      </section>

    </div>

    <aside class="card results" aria-live="polite">
      <h2>Αποτέλεσμα</h2>

      <div class="big-total">
        <div class="number" id="grandTotal">0</div>
        <div class="outof" id="totalOutOf">Επίλεξε αξιολογικό πίνακα</div>
      </div>

      <div class="bar"><div id="scoreBar"></div></div>

      <div class="result-row"><span>Τίτλοι σπουδών</span><strong id="academicResult">0</strong></div>
      <div class="result-row"><span id="primaryResultLabel">Γλώσσα πίνακα</span><strong id="primaryResult">0</strong></div>
      <div class="result-row"><span>Δεύτερη ξένη γλώσσα</span><strong id="secondLanguageResult">0</strong></div>
      <div class="result-row"><span>Πίνακας</span><strong id="tableResult">—</strong></div>

      <div id="eligibilityStatus" role="status" aria-live="polite"></div>

      <div class="actions">
        <button type="button" class="primary" id="copyBtn">Αντιγραφή σύνοψης</button>
        <button type="button" class="secondary" id="resetBtn">Μηδενισμός</button>
      </div>

      <div class="note">
        *Τα 185/165 είναι το θεωρητικό άθροισμα των επιμέρους μοριοδοτούμενων κριτηρίων του ΦΕΚ και όχι ξεχωριστό συνολικό πλαφόν που αναγράφεται στην απόφαση.
      </div>
    </aside>
  </div>

  <div class="source-note">
    <strong>Πηγή:</strong> Υ.Α. 83046/Η2/30-06-2020, ΦΕΚ Β΄ 2687/01.07.2020, ιδίως άρθρα 2–3.
    Η πρόσκληση 11771/Η2/30-01-2026 για το σχολικό έτος 2026-2027 και το 2027 Νοτίου Ημισφαιρίου εξακολουθεί να χρησιμοποιεί την παραπάνω Υ.Α. για την κατάρτιση των πινάκων και τη μοριοδότηση.
    Το εργαλείο αφορά τις ελληνόγλωσσες εκπαιδευτικές μονάδες εξωτερικού και δεν υποκαθιστά τον έλεγχο της εκάστοτε πρόσκλησης, των χωρών/ειδικοτήτων και των ειδικών τοπικών προϋποθέσεων.
  </div>

  <?php require_once __DIR__ . '/includes/footer.php'; ?>
</main>

<script src="includes/abroad-calculations.js"></script>
<script>
(function(){
  "use strict";
  const $ = id => document.getElementById(id);
  const fmt = n => Number(n || 0).toLocaleString('el-GR', {maximumFractionDigits: 1});

  const allIds = [
    'branchAllowed','educationYears','teachingYears','blockingIssue',
    'tableType','bilingualPosition','phd','master','secondMaster','secondDegree',
    'primaryLevel','alternativeLanguage','alternativeDifferentFromCountry',
    'hostBilingualLevel','secondLanguageLevel','secondLanguageDistinct'
  ];

  function normalizeYears(id){
    const el = $(id);
    if(!el || el.value === '') return;
    el.value = String(Math.max(0, Math.floor(Number(el.value) || 0)));
  }

  function updateUI(){
    const tableType = $('tableType').value;
    const bilingual = $('bilingualPosition').value;

    $('alternativeFields').classList.toggle('hidden', tableType !== 'alternative');

    if(tableType === 'main'){
      $('primaryLevelLabel').textContent = 'Επίπεδο γλώσσας χώρας υποδοχής';
      $('primaryLanguageHelp').textContent =
        'Βασικός Πίνακας: Β2 = 0 μόρια, Γ1/C1 = 30 μόρια, Γ2/C2 = 50 μόρια.';
    } else if(tableType === 'alternative'){
      $('primaryLevelLabel').textContent = 'Επίπεδο εναλλακτικής γλώσσας (Αγγλικά / Γαλλικά / Γερμανικά)';
      $('primaryLanguageHelp').textContent =
        'Εναλλακτικός Πίνακας: Β2 = 0 μόρια, Γ1/C1 = 20 μόρια, Γ2/C2 = 30 μόρια.';
    } else {
      $('primaryLevelLabel').textContent = 'Επίπεδο γλώσσας αξιολογικού πίνακα';
      $('primaryLanguageHelp').textContent = 'Επίλεξε πρώτα Βασικό ή Εναλλακτικό Πίνακα.';
    }

    $('hostBilingualWrap').classList.toggle(
      'hidden',
      !(tableType === 'alternative' && bilingual === 'yes')
    );

    const secondUsed = $('secondLanguageLevel').value !== 'none';
    $('secondLanguageDistinctWrap').classList.toggle('hidden', !secondUsed);
    if(!secondUsed) $('secondLanguageDistinct').value = '';
  }

  function values(){
    normalizeYears('educationYears');
    normalizeYears('teachingYears');

    return {
      branchAllowed: $('branchAllowed').value,
      educationYears: $('educationYears').value,
      educationYearsAnswered: $('educationYears').value !== '',
      teachingYears: $('teachingYears').value,
      teachingYearsAnswered: $('teachingYears').value !== '',
      blockingIssue: $('blockingIssue').value,
      tableType: $('tableType').value,
      bilingualPosition: $('bilingualPosition').value,

      phd: $('phd').checked,
      master: $('master').checked,
      secondMaster: $('secondMaster').checked,
      secondDegree: $('secondDegree').checked,

      primaryLevel: $('primaryLevel').value,
      alternativeLanguage: $('alternativeLanguage').value,
      alternativeDifferentFromCountry: $('alternativeDifferentFromCountry').value,
      hostBilingualLevel: $('hostBilingualLevel').value,

      secondLanguageLevel: $('secondLanguageLevel').value,
      secondLanguageDistinct: $('secondLanguageDistinct').value
    };
  }

  function calculate(){
    updateUI();
    const r = AbroadSecondment.calculate(values());

    $('grandTotal').textContent = fmt(r.total);
    $('academicResult').textContent = fmt(r.academic);
    $('primaryResult').textContent = fmt(r.primaryLanguagePoints);
    $('secondLanguageResult').textContent = fmt(r.secondLanguagePoints);

    if(r.tableType === 'main'){
      $('tableResult').textContent = 'Βασικός';
      $('primaryResultLabel').textContent = 'Γλώσσα χώρας';
      $('totalOutOf').textContent = 'θεωρητικά έως 185';
    } else if(r.tableType === 'alternative'){
      $('tableResult').textContent = 'Εναλλακτικός';
      $('primaryResultLabel').textContent = 'Εναλλακτική γλώσσα';
      $('totalOutOf').textContent = 'θεωρητικά έως 165';
    } else {
      $('tableResult').textContent = '—';
      $('primaryResultLabel').textContent = 'Γλώσσα πίνακα';
      $('totalOutOf').textContent = 'Επίλεξε αξιολογικό πίνακα';
    }

    const pct = r.theoreticalMax ? Math.min(100, (r.total / r.theoreticalMax) * 100) : 0;
    $('scoreBar').style.width = pct + '%';

    const boxes = [];
    if(r.unanswered.length){
      boxes.push(
        '<div class="info"><strong>Χρειάζονται ακόμη στοιχεία:</strong><ul style="margin:7px 0 0;padding-left:20px"><li>'
        + r.unanswered.join('</li><li>') + '</li></ul></div>'
      );
    }

    if(r.issues.length){
      boxes.push(
        '<div class="danger"><strong>Ο βασικός έλεγχος δεν είναι θετικός:</strong><ul style="margin:7px 0 0;padding-left:20px"><li>'
        + r.issues.join('</li><li>') + '</li></ul></div>'
      );
    } else if(r.eligible){
      boxes.push(
        '<div class="success"><strong>Ο βασικός έλεγχος είναι θετικός.</strong> Με τα δηλωμένα στοιχεία καλύπτονται οι βασικές προϋποθέσεις για τον επιλεγμένο πίνακα. Απαιτείται πάντως έλεγχος της χώρας, της ειδικότητας και όλων των δικαιολογητικών.</div>'
      );
    }

    if(r.warnings.length){
      boxes.push(
        '<div class="warning"><strong>Παρατηρήσεις:</strong><ul style="margin:7px 0 0;padding-left:20px"><li>'
        + r.warnings.join('</li><li>') + '</li></ul></div>'
      );
    }

    $('eligibilityStatus').innerHTML = boxes.join('');
    return r;
  }

  async function copySummary(){
    const r = calculate();
    const table = r.tableType === 'main'
      ? 'Βασικός Πίνακας'
      : (r.tableType === 'alternative' ? 'Εναλλακτικός Πίνακας' : 'Δεν επιλέχθηκε');

    const lines = [
      'Μόρια Απόσπασης στο Εξωτερικό',
      `Πίνακας: ${table}`,
      `Τίτλοι σπουδών: ${fmt(r.academic)}`,
      `Γλώσσα πίνακα: ${fmt(r.primaryLanguagePoints)}`,
      `Δεύτερη ξένη γλώσσα: ${fmt(r.secondLanguagePoints)}`,
      `Σύνολο: ${fmt(r.total)}`,
      `Βασικός έλεγχος: ${r.eligible ? 'ΘΕΤΙΚΟΣ' : 'ΜΗ ΟΛΟΚΛΗΡΩΜΕΝΟΣ / ΜΗ ΘΕΤΙΚΟΣ'}`,
      'Ενδεικτικός υπολογισμός βάσει της Υ.Α. 83046/Η2/30-06-2020 (Β΄ 2687).'
    ];

    try{
      await navigator.clipboard.writeText(lines.join('\n'));
      $('copyBtn').textContent = 'Αντιγράφηκε ✓';
      setTimeout(() => $('copyBtn').textContent = 'Αντιγραφή σύνοψης', 1400);
    }catch(e){
      alert(lines.join('\n'));
    }
  }

  function reset(){
    document.querySelectorAll('input[type="checkbox"]').forEach(el => el.checked = false);
    document.querySelectorAll('input[type="number"]').forEach(el => el.value = '');
    document.querySelectorAll('select').forEach(el => el.selectedIndex = 0);
    $('secondLanguageLevel').value = 'none';
    updateUI();
    calculate();
    $('branchAllowed').focus();
  }

  allIds.forEach(id => {
    const el = $(id);
    if(!el) return;
    el.addEventListener('input', calculate);
    el.addEventListener('change', calculate);
  });

  $('copyBtn').addEventListener('click', copySummary);
  $('resetBtn').addEventListener('click', reset);

  updateUI();
  calculate();
})();
</script>
</body>
</html>
