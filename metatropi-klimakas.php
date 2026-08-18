<!DOCTYPE html>
<html lang="el">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="Μετατροπή βαθμού πτυχίου από 10βάθμια σε 20βάθμια κλίμακα και από δεκαδική ή λεκτική μορφή σε ακέραιο μέρος, αριθμητή και παρονομαστή για 1ΓΕ/2026 και 1ΓΤ/2024.">
<title>Μετατροπή κλίμακας βαθμού πτυχίου</title>
<style>
:root{
  --blue:#1f6feb;--blue-dark:#174ea6;--bg:#f5f7fb;--card:#fff;--text:#202124;
  --muted:#5f6368;--border:#d9e0e8;--soft:#eef4ff;--green:#137333;--green-soft:#e6f4ea;
  --orange:#8a5300;--orange-soft:#fff4e5;--shadow:0 5px 18px rgba(0,0,0,.09)
}
*{box-sizing:border-box}
body{font-family:Arial,Helvetica,sans-serif;background:var(--bg);margin:0;padding:30px;color:var(--text);line-height:1.5}
.app-box{max-width:920px;margin:auto;background:var(--card);padding:26px;border-radius:16px;box-shadow:var(--shadow)}
h1{text-align:center;font-size:28px;line-height:1.2;margin:0 0 8px}
.intro{text-align:center;color:var(--muted);max-width:760px;margin:0 auto 22px}
.notice{padding:15px 16px;border-radius:10px;background:var(--orange-soft);color:var(--orange);margin-bottom:22px;border:1px solid #f0d7aa}
.notice strong{color:#6f4300}
.mode-tabs{display:grid;grid-template-columns:1fr 1fr;gap:10px;margin:0 0 18px}
.mode-tabs button{border:1px solid #cfd8e3;background:#fff;color:#344054;border-radius:10px;padding:12px 14px;font-size:15px;font-weight:700;cursor:pointer}
.mode-tabs button.active{background:var(--blue);border-color:var(--blue);color:#fff}
.panel{padding:17px;border:1px solid var(--border);border-radius:12px;background:#fafbfc;margin-bottom:18px}
.panel h2{font-size:18px;margin:0 0 13px}
label{display:block;font-weight:700;margin:0 0 8px}
input,select{width:100%;padding:12px 13px;border:1px solid #c7d0dc;border-radius:9px;background:#fff;color:var(--text);font-size:16px}
input:focus,select:focus{border-color:var(--blue)}
.hint{font-size:13px;color:var(--muted);margin:7px 0 0}
.hidden{display:none!important}
.result{margin-top:20px}
.result-title{text-align:center;margin:0 0 14px;font-size:21px}
.source-summary{padding:13px 14px;background:var(--soft);border:1px solid #cbdcf8;border-radius:10px;color:var(--blue-dark);margin-bottom:15px;text-align:center;font-weight:700}
.result-grid{display:grid;grid-template-columns:1fr 1fr;gap:15px}
.result-card{border:1px solid var(--border);border-radius:13px;padding:17px;background:#fff}
.result-card.recommended{border:2px solid var(--blue);box-shadow:0 4px 14px rgba(31,111,235,.09)}
.result-card h3{margin:0 0 5px;font-size:19px}
.result-card .subtitle{font-size:13px;color:var(--muted);margin-bottom:13px}
.big-grade{font-size:36px;font-weight:700;line-height:1;margin:9px 0 16px;color:var(--blue-dark)}
.fraction-title{font-weight:700;margin:14px 0 8px}
.fraction-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:9px}
.field-box{background:#f7f8fa;border:1px solid #e0e4ea;border-radius:9px;padding:10px;text-align:center}
.field-box span{display:block;font-size:12px;color:var(--muted);margin-bottom:5px}
.field-box strong{font-size:24px}
.points{margin-top:13px;padding:11px;background:var(--green-soft);color:var(--green);border-radius:9px;font-weight:700}
.action-row{display:flex;gap:10px;flex-wrap:wrap;margin-top:16px}
.action-row button{border:0;border-radius:9px;padding:11px 14px;font-size:14px;font-weight:700;cursor:pointer}
.copy-btn{background:var(--blue);color:#fff}
.reset-btn{background:#eef0f3;color:#333}
.lexical-map{margin-top:14px;padding:12px 14px;background:#fff;border:1px dashed #c9d2dd;border-radius:9px;font-size:14px;color:#4b5563}
.lexical-map strong{color:#222}
.error{display:none;margin-top:10px;padding:10px 12px;border-radius:8px;background:#fdecea;color:#b3261e;font-weight:700}
.small-note{margin-top:20px;font-size:13px;color:#666;text-align:justify}
.copy-status{font-size:13px;color:var(--green);font-weight:700;align-self:center}
@media(max-width:720px){body{padding:16px}.app-box{padding:18px}.result-grid{grid-template-columns:1fr}.fraction-grid{grid-template-columns:1fr 1fr 1fr}.mode-tabs{grid-template-columns:1fr}h1{font-size:24px}}
@media print{body{background:#fff;padding:0}.app-box{box-shadow:none;max-width:none}.mode-tabs,.panel,.action-row,.notice,.small-note{display:none}.result{margin-top:0}}
</style>
<link rel="stylesheet" href="assets/common.css">
</head>
<body>
<?php require_once __DIR__ . '/includes/header.php'; ?>
<div class="app-box">
  <h1>Μετατροπή κλίμακας βαθμού πτυχίου</h1>
  <p class="intro">Μετατροπή από 10βάθμια σε 20βάθμια κλίμακα και από δεκαδική ή λεκτική μορφή στα πεδία <strong>Ακέραιο μέρος – Αριθμητής – Παρονομαστής</strong>.</p>

  <div class="notice">
    <strong>⚠️ Προσοχή στη διαφορετική κλίμακα:</strong><br>
    Στην <strong>1ΓΕ/2026</strong> ο βαθμός πτυχίου δηλώνεται σε <strong>10βάθμια κλίμακα</strong>, ενώ στην <strong>1ΓΤ/2024</strong> σε <strong>20βάθμια κλίμακα</strong>. Για την 1ΓΤ/2024 γίνεται πρώτα η αναγωγή στην 20βάθμια και μετά η μετατροπή της δεκαδικής μορφής σε κλασματική.
  </div>

  <div class="mode-tabs" role="group" aria-label="Τρόπος καταχώρισης βαθμού">
    <button type="button" id="decimalTab" class="active" aria-pressed="true">🔢 Δεκαδικός βαθμός</button>
    <button type="button" id="lexicalTab" aria-pressed="false">🔤 Λεκτική κλίμακα</button>
  </div>

  <div class="panel">
    <h2>1. Καταχώρισε τον βαθμό</h2>

    <div id="decimalPanel">
      <label for="decimalGrade">Βαθμός σε 10βάθμια κλίμακα</label>
      <input id="decimalGrade" type="text" inputmode="decimal" autocomplete="off" placeholder="π.χ. 7,34" aria-describedby="decimalHint decimalError">
      <p id="decimalHint" class="hint">Δέχεται κόμμα ή τελεία. Επιτρεπτές τιμές: 1 έως 10.</p>
      <div id="decimalError" class="error" role="alert"></div>
    </div>

    <div id="lexicalPanel" class="hidden">
      <label for="lexicalGrade">Λεκτικός βαθμός</label>
      <select id="lexicalGrade">
        <option value="">-- Επιλογή --</option>
        <option value="5">ΚΑΛΩΣ (5)</option>
        <option value="6.5">ΛΙΑΝ ΚΑΛΩΣ (6,5)</option>
        <option value="8.5">ΑΡΙΣΤΑ (8,5)</option>
      </select>
      <div class="lexical-map">
        <strong>Αντιστοίχιση λεκτικής κλίμακας:</strong>
        ΚΑΛΩΣ → 5 &nbsp;•&nbsp; ΛΙΑΝ ΚΑΛΩΣ → 6,5 &nbsp;•&nbsp; ΑΡΙΣΤΑ → 8,5
      </div>
    </div>
  </div>

  <div id="result" class="result hidden" aria-live="polite">
    <h2 class="result-title">Αποτέλεσμα μετατροπής</h2>
    <div id="sourceSummary" class="source-summary"></div>

    <div class="result-grid">
      <section class="result-card">
        <h3>1ΓΕ/2026</h3>
        <div class="subtitle">Δήλωση σε 10βάθμια κλίμακα</div>
        <div id="grade10" class="big-grade">—</div>
        <div class="fraction-title">Σε κλασματική μορφή</div>
        <div class="fraction-grid">
          <div class="field-box"><span>Ακέραιο μέρος</span><strong id="int10">—</strong></div>
          <div class="field-box"><span>Αριθμητής</span><strong id="num10">—</strong></div>
          <div class="field-box"><span>Παρονομαστής</span><strong id="den10">100</strong></div>
        </div>
        <div id="points10" class="points">Μόρια: —</div>
      </section>

      <section class="result-card recommended">
        <h3>1ΓΤ/2024</h3>
        <div class="subtitle">Αναγωγή σε 20βάθμια κλίμακα</div>
        <div id="grade20" class="big-grade">—</div>
        <div class="fraction-title">Πεδία αίτησης</div>
        <div class="fraction-grid">
          <div class="field-box"><span>Ακέραιο μέρος</span><strong id="int20">—</strong></div>
          <div class="field-box"><span>Αριθμητής</span><strong id="num20">—</strong></div>
          <div class="field-box"><span>Παρονομαστής</span><strong id="den20">100</strong></div>
        </div>
        <div id="points20" class="points">Μόρια: —</div>
        <div class="action-row">
          <button type="button" id="copy20" class="copy-btn">📋 Αντιγραφή πεδίων 1ΓΤ/2024</button>
          <span id="copyStatus" class="copy-status" aria-live="polite"></span>
        </div>
      </section>
    </div>

    <div class="action-row">
      <button type="button" id="resetBtn" class="reset-btn">↺ Μηδενισμός</button>
      <button type="button" onclick="window.print()" class="reset-btn">🖨 Εκτύπωση</button>
    </div>
  </div>

  <p class="small-note">Το εργαλείο κάνει αριθμητική μετατροπή με βάση τους κανόνες που έχουν ενσωματωθεί στη σελίδα. Πριν από οριστική καταχώριση, έλεγξε πάντα την αντίστοιχη προκήρυξη και τα πεδία της ηλεκτρονικής αίτησης.</p>
</div>

<script>
(function(){
  const decimalTab = document.getElementById('decimalTab');
  const lexicalTab = document.getElementById('lexicalTab');
  const decimalPanel = document.getElementById('decimalPanel');
  const lexicalPanel = document.getElementById('lexicalPanel');
  const decimalGrade = document.getElementById('decimalGrade');
  const lexicalGrade = document.getElementById('lexicalGrade');
  const decimalError = document.getElementById('decimalError');
  const result = document.getElementById('result');
  const sourceSummary = document.getElementById('sourceSummary');
  const copyStatus = document.getElementById('copyStatus');
  let mode = 'decimal';
  let current = null;

  function round2(value){
    return Math.round((value + Number.EPSILON) * 100) / 100;
  }

  function formatEl(value){
    return new Intl.NumberFormat('el-GR', {maximumFractionDigits:2, minimumFractionDigits:0}).format(value);
  }

  function mixedFraction(value){
    let integer = Math.floor(value + 1e-9);
    let numerator = Math.round((value - integer) * 100);
    if(numerator >= 100){
      integer += 1;
      numerator = 0;
    }
    return {integer, numerator, denominator:100};
  }

  function parseDecimal(){
    const raw = decimalGrade.value.trim().replace(',', '.');
    if(raw === '') return null;
    if(!/^\d+(?:\.\d+)?$/.test(raw)) return NaN;
    return Number(raw);
  }

  function setMode(newMode){
    mode = newMode;
    const isDecimal = mode === 'decimal';
    decimalTab.classList.toggle('active', isDecimal);
    lexicalTab.classList.toggle('active', !isDecimal);
    decimalTab.setAttribute('aria-pressed', isDecimal ? 'true' : 'false');
    lexicalTab.setAttribute('aria-pressed', isDecimal ? 'false' : 'true');
    decimalPanel.classList.toggle('hidden', !isDecimal);
    lexicalPanel.classList.toggle('hidden', isDecimal);
    decimalError.style.display = 'none';
    copyStatus.textContent = '';
    update();
  }

  function update(){
    let grade;
    let sourceLabel;
    decimalError.style.display = 'none';
    copyStatus.textContent = '';

    if(mode === 'decimal'){
      grade = parseDecimal();
      if(grade === null){
        result.classList.add('hidden');
        current = null;
        return;
      }
      if(Number.isNaN(grade) || grade < 1 || grade > 10){
        result.classList.add('hidden');
        current = null;
        decimalError.textContent = 'Καταχώρισε έγκυρο βαθμό από 1 έως 10.';
        decimalError.style.display = 'block';
        return;
      }
      sourceLabel = 'Δεκαδικός βαθμός: ' + formatEl(grade) + '/10';
    } else {
      if(!lexicalGrade.value){
        result.classList.add('hidden');
        current = null;
        return;
      }
      grade = Number(lexicalGrade.value);
      sourceLabel = 'Λεκτικός βαθμός: ' + lexicalGrade.options[lexicalGrade.selectedIndex].text;
    }

    const grade10 = round2(grade);
    const grade20 = round2(grade10 * 2);
    const frac10 = mixedFraction(grade10);
    const frac20 = mixedFraction(grade20);
    const points10 = Math.min(25, round2(grade10 * 2.5));
    const points20 = Math.min(60, round2(grade20 * 3));

    current = {grade10, grade20, frac10, frac20, points10, points20};

    sourceSummary.textContent = sourceLabel + ' → ' + formatEl(grade10) + '/10 → ' + formatEl(grade20) + '/20';
    document.getElementById('grade10').textContent = formatEl(grade10) + ' / 10';
    document.getElementById('grade20').textContent = formatEl(grade20) + ' / 20';
    document.getElementById('int10').textContent = frac10.integer;
    document.getElementById('num10').textContent = frac10.numerator;
    document.getElementById('int20').textContent = frac20.integer;
    document.getElementById('num20').textContent = frac20.numerator;
    document.getElementById('points10').textContent = 'Μόρια 1ΓΕ/2026: ' + formatEl(points10);
    document.getElementById('points20').textContent = 'Μόρια 1ΓΤ/2024: ' + formatEl(points20);
    result.classList.remove('hidden');
  }

  async function copyFields(){
    if(!current) return;
    const text = [
      '1ΓΤ/2024 – Βαθμός σε 20βάθμια κλίμακα: ' + formatEl(current.grade20),
      'Ακέραιο μέρος: ' + current.frac20.integer,
      'Αριθμητής: ' + current.frac20.numerator,
      'Παρονομαστής: ' + current.frac20.denominator
    ].join('\n');

    try{
      await navigator.clipboard.writeText(text);
      copyStatus.textContent = '✓ Αντιγράφηκε';
    }catch(e){
      const area = document.createElement('textarea');
      area.value = text;
      document.body.appendChild(area);
      area.select();
      document.execCommand('copy');
      area.remove();
      copyStatus.textContent = '✓ Αντιγράφηκε';
    }
  }

  function resetAll(){
    decimalGrade.value = '';
    lexicalGrade.value = '';
    decimalError.style.display = 'none';
    result.classList.add('hidden');
    copyStatus.textContent = '';
    current = null;
    setMode('decimal');
    decimalGrade.focus();
  }

  decimalTab.addEventListener('click', () => setMode('decimal'));
  lexicalTab.addEventListener('click', () => setMode('lexical'));
  decimalGrade.addEventListener('input', update);
  lexicalGrade.addEventListener('change', update);
  document.getElementById('copy20').addEventListener('click', copyFields);
  document.getElementById('resetBtn').addEventListener('click', resetAll);
})();
</script>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
</body>
</html>
