<!doctype html>
<html lang="el">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Υπολογισμός Μορίων ΔΗΜ.Ω.Σ. 2026-2027</title>
  <style>
    :root{
      --bg:#f6f7fb;
      --card:#ffffff;
      --text:#1f2937;
      --muted:#6b7280;
      --border:#d9dee8;
      --accent:#1d4ed8;
      --accent2:#0f766e;
      --warn:#92400e;
      --warnbg:#fff7ed;
      --okbg:#ecfdf5;
      --radius:16px;
    }
    *{box-sizing:border-box}
    body{
      margin:0;
      font-family:system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Arial,sans-serif;
      background:var(--bg);
      color:var(--text);
      line-height:1.45;
    }
    .dimos-calc{
      max-width:1180px;
      margin:0 auto;
      padding:22px;
    }
    .hero{
      background:linear-gradient(135deg,#172554,#1d4ed8);
      color:#fff;
      border-radius:var(--radius);
      padding:26px;
      margin-bottom:18px;
      box-shadow:0 12px 28px rgba(15,23,42,.16);
    }
    .hero h1{
      margin:0 0 8px;
      font-size:clamp(1.55rem,3vw,2.25rem);
      line-height:1.15;
    }
    .hero p{margin:6px 0;color:#e5edff}
    .grid{
      display:grid;
      grid-template-columns:minmax(0,1fr) 330px;
      gap:18px;
      align-items:start;
    }
    @media (max-width: 900px){
      .grid{grid-template-columns:1fr}
      .results{position:static!important}
    }
    .card{
      background:var(--card);
      border:1px solid var(--border);
      border-radius:var(--radius);
      padding:18px;
      margin-bottom:14px;
      box-shadow:0 6px 16px rgba(15,23,42,.05);
    }
    .card h2{
      margin:0 0 5px;
      font-size:1.18rem;
    }
    .card .cap{
      color:var(--muted);
      margin:0 0 14px;
      font-size:.95rem;
    }
    .note{
      background:var(--warnbg);
      border:1px solid #fed7aa;
      color:var(--warn);
      padding:11px 12px;
      border-radius:12px;
      margin:10px 0 14px;
      font-size:.94rem;
    }
    .oknote{
      background:var(--okbg);
      border:1px solid #a7f3d0;
      color:#065f46;
      padding:11px 12px;
      border-radius:12px;
      margin:10px 0 14px;
      font-size:.94rem;
    }
    .field{
      display:grid;
      grid-template-columns:minmax(0,1fr) 150px;
      gap:12px;
      align-items:center;
      padding:10px 0;
      border-top:1px solid #eef1f6;
    }
    .field:first-of-type{border-top:0}
    .field label{
      font-weight:600;
    }
    .field small{
      display:block;
      color:var(--muted);
      font-weight:400;
      margin-top:2px;
    }
    input[type="number"], select{
      width:100%;
      padding:10px 11px;
      border:1px solid var(--border);
      border-radius:11px;
      font-size:1rem;
      background:#fff;
      color:var(--text);
    }
    input[type="checkbox"]{
      width:20px;
      height:20px;
      accent-color:var(--accent);
    }
    .checkrow{
      display:grid;
      grid-template-columns:28px minmax(0,1fr);
      gap:10px;
      padding:9px 0;
      border-top:1px solid #eef1f6;
      align-items:start;
    }
    .checkrow:first-of-type{border-top:0}
    .checkrow label{font-weight:600}
    .checkrow small{display:block;color:var(--muted);font-weight:400}
    .subtot{
      display:flex;
      justify-content:space-between;
      align-items:center;
      border-top:1px dashed var(--border);
      margin-top:14px;
      padding-top:12px;
      font-weight:700;
      color:#111827;
    }
    .pill{
      display:inline-flex;
      align-items:center;
      justify-content:center;
      min-width:72px;
      padding:7px 10px;
      border-radius:999px;
      background:#eff6ff;
      color:#1d4ed8;
      font-variant-numeric:tabular-nums;
    }
    .results{
      position:sticky;
      top:16px;
    }
    .total{
      text-align:center;
      padding:22px 18px;
    }
    .total .num{
      display:block;
      font-size:3.2rem;
      line-height:1;
      font-weight:850;
      color:var(--accent);
      letter-spacing:-.04em;
      font-variant-numeric:tabular-nums;
    }
    .total .outof{
      color:var(--muted);
      margin-top:4px;
    }
    .bar{
      height:12px;
      background:#e5e7eb;
      border-radius:999px;
      overflow:hidden;
      margin:13px 0 6px;
    }
    .bar > div{
      height:100%;
      width:0%;
      background:linear-gradient(90deg,var(--accent),var(--accent2));
      border-radius:999px;
      transition:width .2s ease;
    }
    .result-row{
      display:flex;
      justify-content:space-between;
      gap:10px;
      padding:9px 0;
      border-top:1px solid #eef1f6;
      font-size:.96rem;
    }
    .result-row strong{font-variant-numeric:tabular-nums}
    .actions{
      display:grid;
      grid-template-columns:1fr 1fr;
      gap:8px;
      margin-top:12px;
    }
    button{
      border:0;
      border-radius:12px;
      padding:11px 12px;
      font-weight:700;
      cursor:pointer;
      background:var(--accent);
      color:#fff;
      font-size:.95rem;
    }
    button.secondary{
      background:#e5e7eb;
      color:#111827;
    }
    .two-col{
      display:grid;
      grid-template-columns:1fr 1fr;
      gap:12px;
    }
    @media (max-width: 620px){
      .field{grid-template-columns:1fr}
      .two-col{grid-template-columns:1fr}
    }
    table{
      width:100%;
      border-collapse:collapse;
      margin-top:8px;
      overflow:hidden;
      border-radius:12px;
      font-size:.94rem;
    }
    th,td{
      border:1px solid #e5e7eb;
      padding:9px;
      vertical-align:middle;
    }
    th{
      background:#f8fafc;
      text-align:left;
      color:#334155;
    }
    td.numcell{width:125px}
    .footer{
      color:var(--muted);
      text-align:center;
      font-size:.9rem;
      margin:22px 0 6px;
    }
    @media print{
      body{background:#fff}
      .actions{display:none}
      .results{position:static}
      .card,.hero{box-shadow:none}
    }
  </style>
  <link rel="stylesheet" href="assets/common.css">
</head>
<body>
<?php require_once __DIR__ . '/includes/header.php'; ?>
  <main class="dimos-calc" id="dimosCalc">
    <section class="hero">
      <h1>Υπολογισμός Μορίων ΔΗΜ.Ω.Σ. 2026–2027</h1>
      <p>Ενημερωτικός υπολογιστής μοριοδότησης για απόσπαση μόνιμων εκπαιδευτικών σε Δημόσια Ωνάσεια Σχολεία.</p>
      <p><strong>Μέγιστο σύνολο: 53 μόρια</strong> · Α: 29 · Β: 13 · Γ: 11</p>
    </section>

    <div class="grid">
      <div>
        <section class="card">
          <h2>Α. Επιστημονική, παιδαγωγική συγκρότηση και κατάρτιση</h2>
          <p class="cap">Μέγιστο κατηγορίας: 29 μόρια</p>

          <div class="note">
            Δεν μοριοδοτείται προσόν που αποτέλεσε προϋπόθεση διορισμού/μετάταξης/κατάταξης. Για αλλοδαπούς τίτλους απαιτείται η προβλεπόμενη αναγνώριση.
          </div>

          <h3>Α1. Τίτλοι σπουδών <span class="pill" id="titlesSubtotal">0.00 / 19</span></h3>
          <div class="checkrow">
            <input type="checkbox" id="phdRelated" data-title-points="8">
            <label for="phdRelated">Διδακτορικό συναφές <small>8 μόρια</small></label>
          </div>
          <div class="checkrow">
            <input type="checkbox" id="secondPhd" data-title-points="5">
            <label for="secondPhd">Δεύτερο διδακτορικό <small>5 μόρια</small></label>
          </div>
          <div class="checkrow">
            <input type="checkbox" id="masterRelated" data-title-points="4">
            <label for="masterRelated">Μεταπτυχιακό συναφές <small>4 μόρια</small></label>
          </div>
          <div class="checkrow">
            <input type="checkbox" id="masterOtherOrSecond" data-title-points="2">
            <label for="masterOtherOrSecond">Μεταπτυχιακό μη συναφές ή δεύτερο μεταπτυχιακό <small>2 μόρια</small></label>
          </div>
          <div class="checkrow">
            <input type="checkbox" id="secondDegree4y" data-title-points="3">
            <label for="secondDegree4y">Δεύτερο πτυχίο ΠΕ/ΤΕ τετραετούς φοίτησης <small>3 μόρια</small></label>
          </div>
          <div class="checkrow">
            <input type="checkbox" id="secondDegreeShort" data-title-points="2">
            <label for="secondDegreeShort">Δεύτερο πτυχίο ΤΕ διάρκειας μικρότερης των 4 ετών <small>2 μόρια</small></label>
          </div>
          <div class="checkrow">
            <input type="checkbox" id="esdda" data-title-points="2">
            <label for="esdda">Αποφοίτηση από Ε.Σ.Δ.Δ.Α. <small>2 μόρια</small></label>
          </div>
          <div class="checkrow">
            <input type="checkbox" id="thirdDegree" data-title-points="1">
            <label for="thirdDegree">Τρίτο πτυχίο <small>1 μόριο</small></label>
          </div>

          <h3>Α2. Επιμορφώσεις <span class="pill" id="trainingSubtotal">0.00 / 5</span></h3>
          <div class="checkrow">
            <input type="checkbox" id="selme" data-training-points="1">
            <label for="selme">Σ.Ε.Λ.Μ.Ε. / Α.Σ.ΠΑΙ.Τ.Ε. / Σ.Ε.Λ.Ε.Τ.Ε. <small>1 μόριο, εφόσον δεν αποτέλεσε προσόν διορισμού</small></label>
          </div>
          <div class="field">
            <label for="aeiPrograms">Προγράμματα Α.Ε.Ι. ≥300 ωρών ή ≥9 μηνών <small>1 μόριο ανά πρόγραμμα, έως 2</small></label>
            <input type="number" id="aeiPrograms" min="0" step="1" value="0">
          </div>
          <div class="field">
            <label for="pekHours">Π.Ε.Κ. ώρες επιμόρφωσης <small>0,1 ανά πλήρες 10ωρο, έως 1. Η εισαγωγική επιμόρφωση νεοδιόριστων δεν μοριοδοτείται.</small></label>
            <input type="number" id="pekHours" min="0" step="1" value="0">
          </div>
          <div class="field">
            <label for="iepHours">Ι.Ε.Π. / Π.Ι. / φορείς ΥΠΑΙΘΑ ώρες <small>0,1 ανά πλήρες 10ωρο, έως 1</small></label>
            <input type="number" id="iepHours" min="0" step="1" value="0">
          </div>
          <div class="checkrow">
            <input type="checkbox" id="meizon" data-training-points="1">
            <label for="meizon">Μείζον Πρόγραμμα Επιμόρφωσης Εκπαιδευτικών <small>1 μόριο</small></label>
          </div>
          <div class="two-col">
            <div class="field">
              <label for="eapAnnual">Ε.Α.Π. ετήσιες θεματικές ενότητες <small>0,80 ανά ενότητα, υποκριτήριο έως 0,80</small></label>
              <input type="number" id="eapAnnual" min="0" step="1" value="0">
            </div>
            <div class="field">
              <label for="eapSemester">Ε.Α.Π. εξαμηνιαίες θεματικές ενότητες <small>0,40 ανά ενότητα, υποκριτήριο έως 0,80</small></label>
              <input type="number" id="eapSemester" min="0" step="1" value="0">
            </div>
          </div>
          <div class="field">
            <label for="ekddaHours">Ε.Κ.Δ.Δ.Α. ώρες επιμόρφωσης <small>0,1 ανά πλήρες 10ωρο, έως 1</small></label>
            <input type="number" id="ekddaHours" min="0" step="1" value="0">
          </div>

          <h3>Α3. Ξένες γλώσσες <span class="pill" id="languageSubtotal">0.00 / 5</span></h3>
          <div class="note">
            Για κλάδους ξένων γλωσσών δεν μοριοδοτείται η γλώσσα που αποτέλεσε προσόν διορισμού. Για την ίδια γλώσσα λαμβάνεται μόνο το ανώτερο επίπεδο.
          </div>
          <div class="two-col">
            <div class="field">
              <label for="language1">Πρώτη ξένη γλώσσα</label>
              <select id="language1">
                <option value="0">Καμία / Δεν μοριοδοτείται</option>
                <option value="1">Β2 — 1 μόριο</option>
                <option value="2">Γ1 — 2 μόρια</option>
                <option value="3">Γ2 — 3 μόρια</option>
              </select>
            </div>
            <div class="field">
              <label for="language2">Δεύτερη ξένη γλώσσα</label>
              <select id="language2">
                <option value="0">Καμία / Δεν μοριοδοτείται</option>
                <option value="1">Β2 — 1 μόριο</option>
                <option value="2">Γ1 — 2 μόρια</option>
                <option value="3">Γ2 — 3 μόρια</option>
              </select>
            </div>
          </div>

          <div class="subtot">
            <span>Σύνολο Α</span>
            <span class="pill" id="categoryA">0.00 / 29</span>
          </div>
        </section>

        <section class="card">
          <h2>Β. Επιστημονικό – συγγραφικό έργο</h2>
          <p class="cap">Μέγιστο κατηγορίας: 13 μόρια</p>

          <h3>Β1. Ερευνητικά προγράμματα και διακρίσεις <span class="pill" id="researchSubtotal">0.00 / 4</span></h3>
          <div class="two-col">
            <div class="field">
              <label for="researchPrograms">Συμμετοχές σε ερευνητικά προγράμματα / ομάδες έργου <small>1 μόριο ανά συμμετοχή</small></label>
              <input type="number" id="researchPrograms" min="0" step="1" value="0">
            </div>
            <div class="field">
              <label for="awards">Συναφείς διακρίσεις <small>1 μόριο ανά διάκριση</small></label>
              <input type="number" id="awards" min="0" step="1" value="0">
            </div>
          </div>

          <h3>Β2. Συγγραφικό και ερευνητικό έργο <span class="pill" id="writingSubtotal">0.00 / 5</span></h3>
          <div class="oknote">Στην ομαδική συγγραφή λαμβάνεται το ήμισυ της μοριοδότησης. Βάλε χωριστά ατομικά και ομαδικά έργα.</div>
          <table>
            <thead>
              <tr>
                <th>Είδος έργου</th>
                <th>Μόρια</th>
                <th>Ατομικά</th>
                <th>Ομαδικά</th>
              </tr>
            </thead>
            <tbody id="writingRows"></tbody>
          </table>

          <h3>Β3. Άρθρα σε επιστημονικά περιοδικά <span class="pill" id="articlesSubtotal">0.00 / 4</span></h3>
          <table>
            <thead>
              <tr>
                <th>Είδος άρθρου</th>
                <th>Μόρια</th>
                <th>Ατομικά</th>
                <th>Ομαδικά</th>
              </tr>
            </thead>
            <tbody id="articleRows"></tbody>
          </table>

          <div class="subtot">
            <span>Σύνολο Β</span>
            <span class="pill" id="categoryB">0.00 / 13</span>
          </div>
        </section>

        <section class="card">
          <h2>Γ. Καινοτόμο εκπαιδευτικό έργο και συμβολή στην ανάπτυξη του σχολείου</h2>
          <p class="cap">Μέγιστο κατηγορίας: 11 μόρια</p>

          <div class="field">
            <label for="selfAssessmentActions">Δράσεις κοινού ενδιαφέροντος στο πλαίσιο αυτοαξιολόγησης σχολικής μονάδας <small>0,50 ανά δράση, έως 2</small></label>
            <input type="number" id="selfAssessmentActions" min="0" step="1" value="0">
          </div>
          <div class="field">
            <label for="innovativePublished">Καινοτόμα διδακτική πρακτική / έρευνα δράσης / ενδοσχολική επιμόρφωση με δημοσίευση σε περιοδικό ή πρακτικά με κριτές <small>1 ανά δράση, έως 2</small></label>
            <input type="number" id="innovativePublished" min="0" step="1" value="0">
          </div>
          <div class="field">
            <label for="innovativePrograms">Καινοτόμα εκπαιδευτικά προγράμματα ή δράσεις <small>π.χ. Erasmus+, eTwinning, MUN, περιβαλλοντικά, πολιτιστικά, αγωγής υγείας · 0,50 ανά πρόγραμμα/δράση, έως 4</small></label>
            <input type="number" id="innovativePrograms" min="0" step="1" value="0">
          </div>
          <div class="field">
            <label for="clubs">Όμιλοι, Σύνολα ή εξωδιδακτικές δραστηριότητες <small>0,50 ανά δράση, έως 3</small></label>
            <input type="number" id="clubs" min="0" step="1" value="0">
          </div>

          <div class="subtot">
            <span>Σύνολο Γ</span>
            <span class="pill" id="categoryC">0.00 / 11</span>
          </div>
        </section>
      </div>

      <aside class="card results">
        <div class="total">
          <span class="num" id="grandTotal">0.00</span>
          <div class="outof">από 53 μόρια</div>
          <div class="bar"><div id="totalBar"></div></div>
        </div>

        <div class="result-row"><span>Α. Συγκρότηση & κατάρτιση</span><strong id="resA">0.00 / 29</strong></div>
        <div class="result-row"><span>Β. Επιστημονικό έργο</span><strong id="resB">0.00 / 13</strong></div>
        <div class="result-row"><span>Γ. Καινοτόμο έργο</span><strong id="resC">0.00 / 11</strong></div>

        <div class="actions">
          <button type="button" id="copyBtn">Αντιγραφή</button>
          <button type="button" class="secondary" id="resetBtn">Μηδενισμός</button>
        </div>
        <div class="actions">
          <button type="button" class="secondary" id="printBtn">Εκτύπωση</button>
          <button type="button" class="secondary" id="sampleBtn">Δοκιμή</button>
        </div>

        <div class="note" style="margin-top:14px">
          Το εργαλείο είναι βοηθητικό. Η τελική αποτίμηση γίνεται από τα αρμόδια όργανα και προϋποθέτει τα σωστά δικαιολογητικά στα αντίστοιχα πεδία.
        </div>
      </aside>
    </div>
</main>

  <script>
    (function(){
      const MAX = {
        titles: 19,
        training: 5,
        language: 5,
        A: 29,
        research: 4,
        writing: 5,
        articles: 4,
        B: 13,
        C: 11,
        total: 53
      };

      const writingItems = [
        { id:"intlBooks", label:"Βιβλία διεθνών εκδοτικών οίκων με ISBN", pts:2 },
        { id:"greekBooks", label:"Βιβλία ελληνικών εκδοτικών οίκων με ISBN", pts:1.5 },
        { id:"intlChapters", label:"Κεφάλαια σε συλλογικούς τόμους διεθνών εκδοτικών οίκων με ISBN", pts:1 },
        { id:"greekChapters", label:"Κεφάλαια σε συλλογικούς τόμους ελληνικών εκδοτικών οίκων με ISBN", pts:0.5 },
        { id:"intlProceedings", label:"Εισηγήσεις σε πρακτικά διεθνών συνεδρίων με ISBN/ISSN", pts:0.5 },
        { id:"greekProceedings", label:"Εισηγήσεις σε πρακτικά ελληνικών συνεδρίων με ISBN/ISSN", pts:0.25 },
        { id:"schoolBook", label:"Σχολικό εγχειρίδιο / διδακτικό βιβλίο / ΑΠΣ-ΔΕΠΠΣ / προγράμματα σπουδών", pts:0.5 },
        { id:"software", label:"Εκπαιδευτικό λογισμικό πιστοποιημένο ή με σφραγίδα ποιότητας", pts:0.25 },
        { id:"trainingMaterial", label:"Επιμορφωτικό υλικό ΥΠΑΙΘΑ / ΙΕΠ / ΠΙ / εποπτευόμενων φορέων", pts:0.25 }
      ];

      const articleItems = [
        { id:"intlArticles", label:"Άρθρα σε διεθνή επιστημονικά περιοδικά με ISSN και κριτές", pts:1 },
        { id:"greekArticles", label:"Άρθρα σε ελληνικά επιστημονικά περιοδικά με ISSN και κριτές", pts:0.5 }
      ];

      const $ = (id) => document.getElementById(id);
      const qsa = (sel) => Array.from(document.querySelectorAll(sel));
      const n = (id) => Math.max(0, Number($(id)?.value || 0));
      const cap = (value, max) => Math.min(value, max);
      const f = (value) => (Math.round((value + Number.EPSILON) * 100) / 100).toFixed(2);
      const fullTenHours = (hours) => Math.floor(Math.max(0, hours) / 10) * 0.1;

      function makeRows(targetId, items){
        const tbody = $(targetId);
        tbody.innerHTML = items.map(item => `
          <tr>
            <td>${item.label}</td>
            <td>${String(item.pts).replace(".", ",")}</td>
            <td class="numcell"><input type="number" min="0" step="1" value="0" id="${item.id}_solo" aria-label="${item.label} ατομικά"></td>
            <td class="numcell"><input type="number" min="0" step="1" value="0" id="${item.id}_group" aria-label="${item.label} ομαδικά"></td>
          </tr>
        `).join("");
      }

      makeRows("writingRows", writingItems);
      makeRows("articleRows", articleItems);

      function scoreItems(items){
        return items.reduce((sum, item) => {
          const solo = n(item.id + "_solo");
          const group = n(item.id + "_group");
          return sum + item.pts * solo + item.pts * 0.5 * group;
        }, 0);
      }

      function calc(){
        const titlesRaw = qsa("[data-title-points]").reduce((sum, el) => sum + (el.checked ? Number(el.dataset.titlePoints) : 0), 0);
        const titles = cap(titlesRaw, MAX.titles);

        const trainingRaw =
          ( $("selme").checked ? 1 : 0 ) +
          cap(n("aeiPrograms"), 2) +
          cap(fullTenHours(n("pekHours")), 1) +
          cap(fullTenHours(n("iepHours")), 1) +
          ( $("meizon").checked ? 1 : 0 ) +
          cap(n("eapAnnual") * 0.8 + n("eapSemester") * 0.4, 0.8) +
          cap(fullTenHours(n("ekddaHours")), 1);
        const training = cap(trainingRaw, MAX.training);

        const language = cap(Number($("language1").value) + Number($("language2").value), MAX.language);
        const A = cap(titles + training + language, MAX.A);

        const research = cap(n("researchPrograms") + n("awards"), MAX.research);
        const writing = cap(scoreItems(writingItems), MAX.writing);
        const articles = cap(scoreItems(articleItems), MAX.articles);
        const B = cap(research + writing + articles, MAX.B);

        const C = cap(
          cap(n("selfAssessmentActions") * 0.5, 2) +
          cap(n("innovativePublished") * 1, 2) +
          cap(n("innovativePrograms") * 0.5, 4) +
          cap(n("clubs") * 0.5, 3),
          MAX.C
        );

        const total = cap(A + B + C, MAX.total);

        const values = {titles, training, language, A, research, writing, articles, B, C, total};
        render(values);
        return values;
      }

      function render(v){
        $("titlesSubtotal").textContent = `${f(v.titles)} / 19`;
        $("trainingSubtotal").textContent = `${f(v.training)} / 5`;
        $("languageSubtotal").textContent = `${f(v.language)} / 5`;
        $("categoryA").textContent = `${f(v.A)} / 29`;

        $("researchSubtotal").textContent = `${f(v.research)} / 4`;
        $("writingSubtotal").textContent = `${f(v.writing)} / 5`;
        $("articlesSubtotal").textContent = `${f(v.articles)} / 4`;
        $("categoryB").textContent = `${f(v.B)} / 13`;

        $("categoryC").textContent = `${f(v.C)} / 11`;

        $("grandTotal").textContent = f(v.total);
        $("resA").textContent = `${f(v.A)} / 29`;
        $("resB").textContent = `${f(v.B)} / 13`;
        $("resC").textContent = `${f(v.C)} / 11`;
        $("totalBar").style.width = `${Math.min(100, (v.total / MAX.total) * 100)}%`;
      }

      function summaryText(v){
        return [
          "Υπολογισμός μορίων ΔΗΜ.Ω.Σ. 2026–2027",
          `Σύνολο: ${f(v.total)} / 53`,
          `Α. Επιστημονική, παιδαγωγική συγκρότηση και κατάρτιση: ${f(v.A)} / 29`,
          `  - Τίτλοι σπουδών: ${f(v.titles)} / 19`,
          `  - Επιμορφώσεις: ${f(v.training)} / 5`,
          `  - Ξένες γλώσσες: ${f(v.language)} / 5`,
          `Β. Επιστημονικό – συγγραφικό έργο: ${f(v.B)} / 13`,
          `  - Ερευνητικά προγράμματα και διακρίσεις: ${f(v.research)} / 4`,
          `  - Συγγραφικό έργο: ${f(v.writing)} / 5`,
          `  - Άρθρα: ${f(v.articles)} / 4`,
          `Γ. Καινοτόμο εκπαιδευτικό έργο: ${f(v.C)} / 11`,
          "",
          "Σημείωση: Ενημερωτικός υπολογισμός. Η τελική αποτίμηση γίνεται από τα αρμόδια όργανα."
        ].join("\n");
      }

      document.addEventListener("input", calc);
      document.addEventListener("change", calc);

      $("resetBtn").addEventListener("click", () => {
        qsa("input[type='number']").forEach(i => i.value = 0);
        qsa("input[type='checkbox']").forEach(i => i.checked = false);
        qsa("select").forEach(s => s.value = 0);
        calc();
      });

      $("printBtn").addEventListener("click", () => window.print());

      $("copyBtn").addEventListener("click", async () => {
        const text = summaryText(calc());
        try{
          await navigator.clipboard.writeText(text);
          $("copyBtn").textContent = "Αντιγράφηκε";
          setTimeout(() => $("copyBtn").textContent = "Αντιγραφή", 1400);
        }catch(e){
          alert(text);
        }
      });

      $("sampleBtn").addEventListener("click", () => {
        $("masterRelated").checked = true;
        $("secondDegree4y").checked = true;
        $("aeiPrograms").value = 2;
        $("iepHours").value = 40;
        $("language1").value = 3;
        $("language2").value = 2;
        $("researchPrograms").value = 1;
        $("intlProceedings_solo").value = 2;
        $("greekArticles_group").value = 1;
        $("innovativePrograms").value = 3;
        $("clubs").value = 2;
        calc();
      });

      calc();
    })();
  </script>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
</body>
</html>
