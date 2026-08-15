<!DOCTYPE html>
<html lang="el">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="Υπολογισμός μορίων για την προκήρυξη ΑΣΕΠ 1ΓΤ/2024 για τους κλάδους ΤΕ01, ΤΕ02 και ΤΕ16.">
  <title>Υπολογισμός μορίων 1ΓΤ/2024</title>

  <style>
    :root {
      --bg:#f4f7fb;
      --card:#fff;
      --text:#18202b;
      --muted:#64748b;
      --border:#dbe3ec;
      --blue:#1f6feb;
      --blue-dark:#174ea6;
      --blue-soft:#eef4ff;
      --green:#137333;
      --green-soft:#eaf7ef;
      --orange:#9a5b00;
      --orange-soft:#fff4e5;
      --purple:#6941c6;
      --purple-soft:#f1edff;
      --shadow:0 8px 24px rgba(28,39,55,.08);
    }

    *{box-sizing:border-box}

    body{
      font-family:Arial,Helvetica,sans-serif;
      background:var(--bg);
      margin:0;
      padding:30px 18px;
      color:var(--text);
      line-height:1.55;
    }

    .app{
      max-width:1060px;
      margin:auto;
    }

    .back-tools{
      display:inline-block;
      margin:0 0 16px;
      color:var(--blue);
      font-weight:700;
      text-decoration:none;
    }
    .back-tools:hover{text-decoration:underline}

    .hero{
      background:linear-gradient(135deg,#173b7a,#1f6feb);
      color:#fff;
      border-radius:18px;
      padding:30px;
      box-shadow:var(--shadow);
      margin-bottom:18px;
    }
    .hero h1{margin:0 0 8px;font-size:clamp(26px,4vw,38px);line-height:1.15}
    .hero p{margin:5px 0;color:#e9f1ff}
    .hero .meta{display:flex;gap:8px;flex-wrap:wrap;margin-top:15px}
    .hero .meta span{background:rgba(255,255,255,.14);padding:6px 10px;border-radius:999px;font-size:13px;font-weight:700}

    .layout{
      display:grid;
      grid-template-columns:minmax(0,1fr) 330px;
      gap:18px;
      align-items:start;
    }

    .card{
      background:var(--card);
      border:1px solid var(--border);
      border-radius:16px;
      padding:20px;
      margin-bottom:16px;
      box-shadow:0 4px 16px rgba(28,39,55,.05);
    }
    .card h2{margin:0 0 5px;color:var(--blue-dark);font-size:21px}
    .card h3{margin:20px 0 7px;font-size:17px}
    .cap{color:var(--muted);font-size:14px;margin:0 0 15px}

    .field-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:14px}
    .field{margin-bottom:13px}
    label{display:block;font-weight:700;margin-bottom:6px;line-height:1.35}
    label small,.help{display:block;color:var(--muted);font-weight:400;font-size:13px;margin-top:3px}

    input,select{
      width:100%;
      padding:11px;
      border:1px solid #cbd5e1;
      border-radius:9px;
      background:#fff;
      color:var(--text);
      font-size:15px;
    }
    input:focus,select:focus{outline:0;border-color:var(--blue);box-shadow:0 0 0 3px rgba(31,111,235,.12)}

    .checkrow{display:flex;gap:10px;align-items:flex-start;padding:9px 0}
    .checkrow input{width:19px;height:19px;margin-top:2px;flex:0 0 auto}
    .checkrow label{margin:0}

    .note{
      margin:12px 0;
      padding:11px 12px;
      border-radius:10px;
      background:var(--orange-soft);
      color:#7b4900;
      border:1px solid #f0d4a8;
      font-size:13.5px;
    }
    .info-note{
      margin:12px 0;
      padding:11px 12px;
      border-radius:10px;
      background:var(--blue-soft);
      color:var(--blue-dark);
      border:1px solid #d5e4ff;
      font-size:13.5px;
    }

    .subtot{
      display:flex;
      align-items:center;
      justify-content:space-between;
      gap:12px;
      margin-top:15px;
      padding-top:12px;
      border-top:1px dashed #cbd5e1;
      font-weight:700;
    }
    .pill{
      display:inline-flex;
      padding:6px 10px;
      border-radius:999px;
      background:var(--blue-soft);
      color:var(--blue-dark);
      font-variant-numeric:tabular-nums;
      white-space:nowrap;
    }

    .results{position:sticky;top:16px}
    .total{text-align:center;padding:10px 0 18px}
    .total .num{font-size:54px;line-height:1;font-weight:850;color:var(--blue);letter-spacing:-.04em;font-variant-numeric:tabular-nums}
    .total .label{color:var(--muted);margin-top:5px}
    .result-row{display:flex;justify-content:space-between;gap:12px;padding:9px 0;border-top:1px solid #edf1f5;font-size:14px}
    .result-row strong{font-variant-numeric:tabular-nums;text-align:right}

    .priority{
      margin:14px 0 4px;
      padding:11px;
      border-radius:10px;
      background:#f8fafc;
      border:1px solid var(--border);
      text-align:center;
      font-weight:700;
      color:var(--muted);
    }
    .priority.yes{background:var(--green-soft);border-color:#b7e0c7;color:var(--green)}

    .actions{display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-top:14px}
    button{border:0;border-radius:10px;padding:11px;font-weight:700;cursor:pointer;font-size:14px;background:var(--blue);color:#fff}
    button.secondary{background:#e7ecf2;color:#243244}

    .source{
      background:#fff;
      border:1px solid var(--border);
      border-radius:14px;
      padding:16px;
      margin-top:18px;
      color:var(--muted);
      font-size:13.5px;
    }
    .source strong{color:var(--text)}
    .credits{text-align:center;margin-top:20px;color:#7a8490;font-size:13px}

    .hidden{display:none!important}

    @media(max-width:900px){
      .layout{grid-template-columns:1fr}
      .results{position:static}
    }
    @media(max-width:650px){
      body{padding:16px 10px}
      .hero{padding:24px 19px}
      .field-grid{grid-template-columns:1fr}
      .card{padding:16px}
      .actions{grid-template-columns:1fr}
    }
  </style>
</head>
<body>
<?php require_once __DIR__ . '/includes/header.php'; ?>
<div class="app">
<section class="hero">
    <h1>Υπολογισμός μορίων 1ΓΤ/2024</h1>
    <p>Ενδεικτικός υπολογισμός για τους αξιολογικούς πίνακες Γενικής Εκπαίδευσης κατηγορίας Τ.Ε.</p>
    <div class="meta">
      <span>ΤΕ01</span><span>ΤΕ02</span><span>ΤΕ16</span>
      <span>Ακαδημαϊκά έως 120</span><span>Προϋπηρεσία έως 120</span>
    </div>
  </section>

  <div class="layout">
    <div>
      <section class="card">
        <h2>Κλάδος και βασικός τίτλος</h2>
        <p class="cap">Ο κλάδος επηρεάζει κυρίως τον τρόπο περιγραφής του δεύτερου τίτλου και τη βοήθεια για τον βαθμό ΤΕ16.</p>

        <div class="field-grid">
          <div class="field">
            <label for="branch">Κλάδος</label>
            <select id="branch">
              <option value="te01">ΤΕ01</option>
              <option value="te02">ΤΕ02</option>
              <option value="te16">ΤΕ16 — Μουσικής μη Ανώτατων Ιδρυμάτων</option>
            </select>
          </div>
          <div class="field">
            <label for="gradeScale">Κλίμακα βαθμού τίτλου</label>
            <select id="gradeScale">
              <option value="20">Κλίμακα 0–20</option>
              <option value="10">Κλίμακα 0–10</option>
              <option value="te16text">ΤΕ16 — περιγραφικός βαθμός</option>
            </select>
          </div>
        </div>

        <div id="numericGradeWrap" class="field">
          <label for="degreeGrade">Βαθμός βασικού τίτλου
            <small>Ο βαθμός ανάγεται σε κλίμακα 20 και πολλαπλασιάζεται ×3. Μέγιστο: 60 μόρια.</small>
          </label>
          <input type="number" id="degreeGrade" min="0" max="20" step="0.01" value="0">
        </div>

        <div id="te16TextWrap" class="field hidden">
          <label for="te16TextGrade">Χαρακτηρισμός βαθμού ΤΕ16</label>
          <select id="te16TextGrade">
            <option value="0">Δεν αναγράφεται βαθμολογία → 5,00</option>
            <option value="5">ΚΑΛΩΣ → 5,00</option>
            <option value="6.5">ΛΙΑΝ ΚΑΛΩΣ → 6,50</option>
            <option value="8.5">ΑΡΙΣΤΑ → 8,50</option>
          </select>
          <div class="help">Οι τιμές 5,00 / 6,50 / 8,50 αναφέρονται στην κλίμακα 10 και ο υπολογιστής τις ανάγει αυτόματα σε κλίμακα 20.</div>
        </div>

        <div class="info-note" id="normalizedGradeInfo">Αναγμένος βαθμός: 0,00 / 20 · Μόρια βαθμού: 0,00 / 60</div>
      </section>

      <section class="card">
        <h2>Α. Ακαδημαϊκά προσόντα</h2>
        <p class="cap">Μέγιστο κατηγορίας: 120 μόρια</p>

        <div class="checkrow">
          <input type="checkbox" id="secondTitle">
          <label for="secondTitle"><span id="secondTitleLabel">Πτυχίο επιπέδου 5 / Ι.Ε.Κ. ίδιας ειδικότητας</span><small>10 μόρια</small></label>
        </div>

        <div class="field">
          <label for="language">Καλύτερη ξένη γλώσσα
            <small>Μοριοδοτείται μόνο μία ξένη γλώσσα.</small>
          </label>
          <select id="language">
            <option value="0">Καμία / δεν μοριοδοτείται</option>
            <option value="10">Καλή γνώση — 10 μόρια</option>
            <option value="15">Πολύ καλή γνώση — 15 μόρια</option>
            <option value="20">Άριστη γνώση — 20 μόρια</option>
          </select>
        </div>

        <div class="checkrow">
          <input type="checkbox" id="computer">
          <label for="computer">Πιστοποιημένη γνώση χειρισμού Η/Υ Α΄ επιπέδου<small>Επεξεργασία κειμένου, υπολογιστικά φύλλα και υπηρεσίες διαδικτύου — 20 μόρια</small></label>
        </div>

        <div class="checkrow">
          <input type="checkbox" id="training">
          <label for="training">Επιμόρφωση ≥300 ωρών και διάρκειας ≥7 μηνών<small>Α.Ε.Ι. ή εποπτευόμενος δημόσιος φορέας — μοριοδοτείται μία επιμόρφωση — 10 μόρια</small></label>
        </div>

        <div class="subtot"><span>Σύνολο Ακαδημαϊκών</span><span class="pill" id="academicSubtotal">0,00 / 120</span></div>
      </section>

      <section class="card">
        <h2>Β. Εκπαιδευτική προϋπηρεσία</h2>
        <p class="cap">Μέγιστο κατηγορίας: 120 μόρια</p>

        <div class="note">
          Βάλε τους μήνες σε <strong>ένα μόνο</strong> από τα αντίστοιχα πεδία. Μήνας που δηλώνεται ως δυσπρόσιτος ή ως τρίμηνη σύμβαση δεν πρέπει να ξαναμπεί στους απλούς μήνες, ώστε να μη γίνει διπλή μέτρηση.
        </div>

        <div class="note">
          <strong>Σημείωση 1ΓΤ/2024:</strong> Λαμβάνεται υπόψη η εκπαιδευτική προϋπηρεσία σε μήνες χωρίς να υπολογίζονται τα υπόλοιπα ημερών. Για τον λόγο αυτό, όλα τα πεδία προϋπηρεσίας δέχονται μόνο ακέραιους μήνες.
        </div>

        <div class="field">
          <label for="regularMonths">Λοιπή αναγνωρισμένη εκπαιδευτική προϋπηρεσία
            <small>1 μόριο ανά μήνα. Εδώ μπορεί να συμπεριληφθεί και αναγνωρισμένη ιδιωτική προϋπηρεσία που πληροί τις προϋποθέσεις της προκήρυξης.</small>
          </label>
          <input type="number" id="regularMonths" min="0" step="1" value="0">
        </div>

        <div class="field">
          <label for="difficultMonths">Δυσπρόσιτα / σχολικές μονάδες σε καταστήματα κράτησης
            <small>Από το σχολικό έτος 2020–2021 και μετά · 2 μόρια ανά μήνα · έως 60 μήνες.</small>
          </label>
          <input type="number" id="difficultMonths" min="0" max="60" step="1" value="0">
        </div>

        <h3>Τρίμηνες συμβάσεις 2020–2021</h3>
        <div class="field-grid">
          <div class="field">
            <label for="covid20Regular">Λοιπές τρίμηνες — μήνες<small>1,5 μόριο/μήνα · έως 10 μόρια στο έτος</small></label>
            <input type="number" id="covid20Regular" min="0" step="1" value="0">
          </div>
          <div class="field">
            <label for="covid20Difficult">Δυσπρόσιτες τρίμηνες — μήνες<small>3 μόρια/μήνα · έως 20 μόρια στο έτος</small></label>
            <input type="number" id="covid20Difficult" min="0" step="1" value="0">
          </div>
        </div>

        <h3>Τρίμηνες συμβάσεις 2021–2022</h3>
        <div class="field-grid">
          <div class="field">
            <label for="covid21Regular">Λοιπές τρίμηνες — μήνες<small>1,5 μόριο/μήνα · έως 10 μόρια στο έτος</small></label>
            <input type="number" id="covid21Regular" min="0" step="1" value="0">
          </div>
          <div class="field">
            <label for="covid21Difficult">Δυσπρόσιτες τρίμηνες — μήνες<small>3 μόρια/μήνα · έως 20 μόρια στο έτος</small></label>
            <input type="number" id="covid21Difficult" min="0" step="1" value="0">
          </div>
        </div>

        <div class="subtot"><span>Σύνολο Προϋπηρεσίας</span><span class="pill" id="serviceSubtotal">0,00 / 120</span></div>
      </section>

      <section class="card">
        <h2>Γ. Κοινωνικά κριτήρια</h2>

        <div class="field-grid">
          <div class="field">
            <label for="children">Αριθμός επιλέξιμων τέκνων
              <small>3 μόρια ανά τέκνο. Περιλαμβάνονται οι περιπτώσεις ηλικίας/σπουδών/στρατιωτικής θητείας που ορίζει η προκήρυξη.</small>
            </label>
            <input type="number" id="children" min="0" step="1" value="0">
          </div>
          <div class="field">
            <label for="disability">Υψηλότερο επιλέξιμο ποσοστό αναπηρίας
              <small>Μόνο αν είναι ≥50%. Λαμβάνεται ένα μόνο, το υψηλότερο, ποσοστό υποψηφίου / συζύγου / τέκνου.</small>
            </label>
            <input type="number" id="disability" min="0" max="100" step="1" value="0">
          </div>
        </div>

        <div class="note">
          Για αναπηρία συζύγου απαιτείται έγγαμος βίος τουλάχιστον 4 ετών. Η αναπηρία του υποψηφίου μοριοδοτείται εφόσον δεν οφείλεται κατά κανένα ποσοστό σε ψυχικές παθήσεις. Η αναπηρία τέκνου μοριοδοτείται ανεξαρτήτως ηλικίας.
        </div>

        <div class="subtot"><span>Σύνολο Κοινωνικών</span><span class="pill" id="socialSubtotal">0,00</span></div>
      </section>

      <section class="card">
        <h2>Παιδαγωγική και Διδακτική Επάρκεια</h2>
        <div class="checkrow">
          <input type="checkbox" id="pedagogical">
          <label for="pedagogical">Διαθέτω πιστοποιημένη Παιδαγωγική και Διδακτική Επάρκεια ή την προβλεπόμενη βεβαίωση τρίμηνης παιδαγωγικής επιμόρφωσης Α.Σ.ΠΑΙ.Τ.Ε.<small>Δεν προσθέτει μόρια. Δίνει πρόταξη έναντι υποψηφίων που δεν τη διαθέτουν.</small></label>
        </div>
      </section>
    </div>

    <aside class="card results">
      <div class="total">
        <div class="num" id="grandTotal">0,00</div>
        <div class="label">συνολικά μόρια</div>
      </div>

      <div class="result-row"><span>Ακαδημαϊκά</span><strong id="resAcademic">0,00 / 120</strong></div>
      <div class="result-row"><span>Προϋπηρεσία</span><strong id="resService">0,00 / 120</strong></div>
      <div class="result-row"><span>Κοινωνικά</span><strong id="resSocial">0,00</strong></div>
      <div class="result-row"><span>Βαθμός τίτλου</span><strong id="resDegree">0,00</strong></div>
      <div class="result-row"><span>Τέκνα</span><strong id="resChildren">0,00</strong></div>
      <div class="result-row"><span>Αναπηρία</span><strong id="resDisability">0,00</strong></div>

      <div class="priority" id="priorityBox">Χωρίς δηλωμένη πρόταξη Π.Δ.Ε.</div>

      <div class="actions">
        <button type="button" id="copyBtn">Αντιγραφή αποτελέσματος</button>
        <button type="button" class="secondary" id="resetBtn">Μηδενισμός</button>
      </div>

      <div class="info-note" style="margin-top:14px">
        Σε ισοβαθμία προηγούνται κατά σειρά: περισσότερα κοινωνικά μόρια (και ειδικότερα αναπηρία), έπειτα περισσότερα ακαδημαϊκά / μεγαλύτερος βαθμός πτυχίου και τέλος περισσότερη προϋπηρεσία.
      </div>
    </aside>
  </div>

  <section class="source">
    <strong>Πηγή:</strong> Προκήρυξη ΑΣΕΠ 1ΓΤ/2024, ΦΕΚ Α.Σ.Ε.Π. 25/10.07.2024, Κεφάλαιο Γ΄ «Κριτήρια Κατάταξης».<br>
    Το εργαλείο είναι ενημερωτικό. Η τελική μοριοδότηση προκύπτει από τον έλεγχο της αίτησης και των δικαιολογητικών από τα αρμόδια όργανα.
  </section>

  <div class="credits">Υλοποίηση / επεξεργασία: Μάριος Μαγιολαδίτης</div>
</div>

<script>
(function(){
  const $ = id => document.getElementById(id);
  const num = id => Math.max(0, Number($(id)?.value || 0));
  const intNum = id => Math.max(0, Math.floor(Number($(id)?.value || 0)));
  const cap = (v,max) => Math.min(Math.max(0,v),max);
  const fmt = v => (Math.round((Number(v)+Number.EPSILON)*100)/100).toLocaleString('el-GR',{minimumFractionDigits:2,maximumFractionDigits:2});

  function updateBranchUI(){
    const branch = $('branch').value;
    if(branch === 'te16'){
      $('secondTitleLabel').textContent = 'Δεύτερο πτυχίο από το οποίο προκύπτει μουσική ειδίκευση, αναγνωρισμένου μη Ανώτατου Εκπαιδευτικού Ιδρύματος';
      if($('gradeScale').dataset.auto !== 'off') $('gradeScale').value = '10';
    } else {
      $('secondTitleLabel').textContent = 'Πτυχίο επιπέδου 5 / Ι.Ε.Κ. ίδιας ειδικότητας';
      if($('gradeScale').dataset.auto !== 'off') $('gradeScale').value = '20';
    }
    updateGradeUI();
  }

  function updateGradeUI(){
    const scale = $('gradeScale').value;
    const textual = scale === 'te16text';
    $('numericGradeWrap').classList.toggle('hidden', textual);
    $('te16TextWrap').classList.toggle('hidden', !textual);
    if(!textual){
      $('degreeGrade').max = scale === '10' ? '10' : '20';
    }
  }

  function grade20(){
    const scale = $('gradeScale').value;
    if(scale === 'te16text'){
      let g = Number($('te16TextGrade').value || 0);
      if(g === 0) g = 5;
      return cap(g * 2, 20);
    }
    const g = num('degreeGrade');
    return cap(scale === '10' ? g * 2 : g, 20);
  }

  function calc(){
    const normalizedGrade = grade20();
    const degreePoints = cap(normalizedGrade * 3, 60);
    const secondTitle = $('secondTitle').checked ? 10 : 0;
    const language = Number($('language').value || 0);
    const computer = $('computer').checked ? 20 : 0;
    const training = $('training').checked ? 10 : 0;
    const academic = cap(degreePoints + secondTitle + language + computer + training, 120);

    const regular = intNum('regularMonths') * 1;
    const difficult = cap(intNum('difficultMonths'),60) * 2;
    const c20reg = cap(intNum('covid20Regular') * 1.5, 10);
    const c20dif = cap(intNum('covid20Difficult') * 3, 20);
    const c21reg = cap(intNum('covid21Regular') * 1.5, 10);
    const c21dif = cap(intNum('covid21Difficult') * 3, 20);
    const service = cap(regular + difficult + c20reg + c20dif + c21reg + c21dif, 120);

    const childrenPts = Math.floor(num('children')) * 3;
    const disabilityPct = cap(num('disability'),100);
    const disabilityPts = disabilityPct >= 50 ? disabilityPct * 0.4 : 0;
    const social = childrenPts + disabilityPts;

    const total = academic + service + social;

    $('normalizedGradeInfo').textContent = `Αναγμένος βαθμός: ${fmt(normalizedGrade)} / 20 · Μόρια βαθμού: ${fmt(degreePoints)} / 60`;
    $('academicSubtotal').textContent = `${fmt(academic)} / 120`;
    $('serviceSubtotal').textContent = `${fmt(service)} / 120`;
    $('socialSubtotal').textContent = fmt(social);
    $('grandTotal').textContent = fmt(total);
    $('resAcademic').textContent = `${fmt(academic)} / 120`;
    $('resService').textContent = `${fmt(service)} / 120`;
    $('resSocial').textContent = fmt(social);
    $('resDegree').textContent = fmt(degreePoints);
    $('resChildren').textContent = fmt(childrenPts);
    $('resDisability').textContent = fmt(disabilityPts);

    const ped = $('pedagogical').checked;
    $('priorityBox').classList.toggle('yes',ped);
    $('priorityBox').textContent = ped ? 'ΠΡΟΤΑΞΗ λόγω Παιδαγωγικής & Διδακτικής Επάρκειας' : 'Χωρίς δηλωμένη πρόταξη Π.Δ.Ε.';

    return {normalizedGrade,degreePoints,academic,service,childrenPts,disabilityPts,social,total,ped};
  }

  function summary(v){
    return [
      'Υπολογισμός μορίων 1ΓΤ/2024',
      `Σύνολο: ${fmt(v.total)}`,
      `Ακαδημαϊκά: ${fmt(v.academic)} / 120`,
      `Προϋπηρεσία: ${fmt(v.service)} / 120`,
      `Κοινωνικά: ${fmt(v.social)}`,
      `Παιδαγωγική επάρκεια: ${v.ped ? 'ΝΑΙ — ΠΡΟΤΑΞΗ' : 'ΟΧΙ / ΔΕΝ ΔΗΛΩΘΗΚΕ'}`,
      '',
      'Ενδεικτικός υπολογισμός βάσει της Προκήρυξης ΑΣΕΠ 1ΓΤ/2024.'
    ].join('\n');
  }

  const serviceMonthIds = ['regularMonths','difficultMonths','covid20Regular','covid20Difficult','covid21Regular','covid21Difficult'];
  serviceMonthIds.forEach(id => {
    const el = $(id);
    el.addEventListener('input', () => {
      if (el.value === '') return;
      const value = Math.max(0, Math.floor(Number(el.value) || 0));
      el.value = value;
    });
  });

  document.addEventListener('input',calc);
  document.addEventListener('change',calc);

  $('branch').addEventListener('change',()=>{
    $('gradeScale').dataset.auto = 'on';
    updateBranchUI();
    calc();
  });
  $('gradeScale').addEventListener('change',()=>{
    $('gradeScale').dataset.auto = 'off';
    updateGradeUI();
    calc();
  });

  $('resetBtn').addEventListener('click',()=>{
    document.querySelectorAll('input[type="number"]').forEach(el=>el.value=0);
    document.querySelectorAll('input[type="checkbox"]').forEach(el=>el.checked=false);
    $('branch').value='te01';
    $('gradeScale').dataset.auto='on';
    $('gradeScale').value='20';
    $('language').value='0';
    $('te16TextGrade').value='0';
    updateBranchUI();
    calc();
  });

  $('copyBtn').addEventListener('click',async()=>{
    const text=summary(calc());
    try{
      await navigator.clipboard.writeText(text);
      const old=$('copyBtn').textContent;
      $('copyBtn').textContent='Αντιγράφηκε';
      setTimeout(()=>$('copyBtn').textContent=old,1400);
    }catch(e){ alert(text); }
  });

  $('gradeScale').dataset.auto='on';
  updateBranchUI();
  calc();
})();
</script>
</body>
</html>
