<!DOCTYPE html>
<html lang="el">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Υπολογισμός μορίων αναπληρωτών στα Δημόσια Ωνάσεια Σχολεία 2026-2027</title>

  <style>
    :root {
      --blue: #174ea6;
      --blue2: #1f6feb;
      --green: #137333;
      --bg: #f5f5f5;
      --card: #ffffff;
      --line: #d0d7de;
      --muted: #666;
      --warning-bg: #fff4e5;
      --warning-text: #8a5200;
    }

    * { box-sizing: border-box; }

    body {
      font-family: Arial, sans-serif;
      background: var(--bg);
      margin: 0;
      padding: 30px;
      color: #222;
    }

    .app-box {
      max-width: 980px;
      margin: auto;
      background: var(--card);
      padding: 26px;
      border-radius: 14px;
      box-shadow: 0 4px 14px rgba(0,0,0,0.12);
    }

    h1 {
      text-align: center;
      font-size: 28px;
      line-height: 1.25;
      margin: 0 0 10px;
    }

    .intro {
      text-align: center;
      color: #555;
      line-height: 1.55;
      margin: 0 auto 24px;
      max-width: 820px;
    }

    .section {
      margin-top: 22px;
      padding: 17px;
      border-radius: 12px;
      background: #fafafa;
      border: 1px solid #ddd;
    }

    .section h2 {
      margin: 0 0 14px;
      font-size: 20px;
      color: var(--blue);
    }

    .field-grid {
      display: grid;
      grid-template-columns: repeat(2, minmax(0, 1fr));
      gap: 14px;
    }

    .question { margin-bottom: 12px; }

    label {
      display: block;
      font-weight: bold;
      margin-bottom: 6px;
      line-height: 1.35;
    }

    input, select {
      width: 100%;
      padding: 11px;
      border-radius: 8px;
      border: 1px solid #ccc;
      font-size: 15px;
      background: #fff;
    }

    .note {
      margin: 10px 0 0;
      font-size: 13px;
      color: var(--muted);
      line-height: 1.5;
    }

    .important {
      margin: 14px 0 0;
      padding: 12px 14px;
      background: #eef4ff;
      border-left: 4px solid var(--blue2);
      border-radius: 8px;
      line-height: 1.5;
    }

    .mode-row {
      display: flex;
      gap: 18px;
      flex-wrap: wrap;
      margin-bottom: 14px;
    }

    .mode-row label {
      display: flex;
      align-items: center;
      gap: 7px;
      font-weight: normal;
      cursor: pointer;
    }

    .mode-row input[type="radio"] {
      width: auto;
      margin: 0;
    }

    .service-row {
      display: grid;
      grid-template-columns: 1.1fr 1fr auto;
      gap: 10px;
      align-items: end;
      margin-bottom: 10px;
      padding: 12px;
      background: #fff;
      border: 1px solid #e1e4e8;
      border-radius: 9px;
    }

    .service-row .question { margin: 0; }

    .remove-row {
      width: auto;
      margin: 0;
      padding: 11px 13px;
      background: #b3261e;
      font-size: 14px;
    }

    .remove-row:hover { background: #8f1e18; }

    .add-row {
      width: auto;
      margin-top: 6px;
      padding: 10px 14px;
      font-size: 14px;
      background: #5f6368;
    }

    .add-row:hover { background: #44474b; }

    .actions {
      display: grid;
      grid-template-columns: 1fr auto;
      gap: 10px;
      margin-top: 24px;
    }

    button {
      border: none;
      border-radius: 8px;
      font-size: 17px;
      font-weight: bold;
      cursor: pointer;
      background: var(--blue2);
      color: white;
      padding: 14px 18px;
    }

    button:hover { background: #1558c0; }

    .reset-btn {
      background: #5f6368;
      font-size: 15px;
    }

    .reset-btn:hover { background: #44474b; }

    .result {
      display: none;
      margin-top: 24px;
      padding: 18px;
      border-radius: 10px;
      background: #eef4ff;
      color: var(--blue);
      line-height: 1.55;
    }

    .result.error {
      background: #fdecea;
      color: #b3261e;
      font-weight: bold;
    }

    .result h2 { margin-top: 0; }

    .big-total {
      font-size: 30px;
      font-weight: bold;
      color: var(--green);
      margin: 4px 0 14px;
    }

    .breakdown {
      width: 100%;
      border-collapse: collapse;
      margin-top: 12px;
      background: #fff;
      color: #222;
    }

    .breakdown th, .breakdown td {
      border: 1px solid var(--line);
      padding: 9px;
      text-align: left;
      vertical-align: top;
    }

    .breakdown th { background: #f1f3f4; }

    .total-row {
      font-weight: bold;
      background: #e6f4ea;
      color: var(--green);
    }

    .warning {
      margin-top: 16px;
      padding: 12px;
      border-radius: 8px;
      background: var(--warning-bg);
      color: var(--warning-text);
      font-weight: bold;
    }

    .credits {
      margin-top: 24px;
      text-align: center;
      font-size: 13px;
      color: #777;
    }

    .hidden { display: none !important; }

    @media (max-width: 760px) {
      body { padding: 16px; }
      .app-box { padding: 18px; }
      .field-grid { grid-template-columns: 1fr; }
      .service-row { grid-template-columns: 1fr; }
      .remove-row { width: 100%; }
      .actions { grid-template-columns: 1fr; }
      h1 { font-size: 24px; }
    }
  </style>
  <link rel="stylesheet" href="assets/common.css">
</head>
<body class="edu-ui">
<?php require_once __DIR__ . '/includes/header.php'; ?>

<div class="app-box edu-modernized">
  <section class="hero edu-legacy-hero">
<h1>Υπολογισμός μορίων αναπληρωτών στα Δημόσια Ωνάσεια Σχολεία</h1>
<p class="intro">
    Σχολικό έτος <strong>2026-2027</strong>. Ο υπολογισμός βασίζεται στα ακαδημαϊκά προσόντα
    του πίνακα Α.Σ.Ε.Π. και στην αναγνωρισμένη προϋπηρεσία σε Πρότυπα ή Πειραματικά Σχολεία.
  </p>
</section>

  <div class="important">
    <strong>Τύπος υπολογισμού:</strong><br>
    Μόρια ΔΗΜ.Ω.Σ. = Ακαδημαϊκά Προσόντα Α.Σ.Ε.Π. + μόρια προϋπηρεσίας σε Πρότυπα/Πειραματικά.<br>
    Η ειδική προϋπηρεσία μοριοδοτείται με <strong>1,5 μόριο ανά αναγνωρισμένο μήνα</strong>,
    με ανώτατο όριο <strong>15 μόρια ανά σχολικό έτος</strong>.
  </div>

  <div class="section">
    <h2>1. Κλάδος / ειδικότητα</h2>

    <div class="question">
      <label for="specialty">Επίλεξε κλάδο</label>
      <select id="specialty">
        <option value="">-- Επιλογή --</option>
        <option value="ΠΕ01">ΠΕ01 Θεολόγοι</option>
        <option value="ΠΕ02">ΠΕ02 Φιλόλογοι</option>
        <option value="ΠΕ03">ΠΕ03 Μαθηματικοί</option>
        <option value="ΠΕ04">ΠΕ04 Φυσικών Επιστημών</option>
        <option value="ΠΕ05">ΠΕ05 Γαλλικής</option>
        <option value="ΠΕ06">ΠΕ06 Αγγλικής</option>
        <option value="ΠΕ07">ΠΕ07 Γερμανικής</option>
        <option value="ΠΕ08">ΠΕ08 Καλλιτεχνικών</option>
        <option value="ΠΕ11">ΠΕ11 Φυσικής Αγωγής</option>
        <option value="ΠΕ78">ΠΕ78 Κοινωνικών Επιστημών</option>
        <option value="ΠΕ79.01">ΠΕ79.01 Μουσικής Επιστήμης</option>
        <option value="ΠΕ80">ΠΕ80 Οικονομίας</option>
        <option value="ΠΕ81">ΠΕ81 Πολιτικών Μηχανικών - Αρχιτεκτόνων</option>
        <option value="ΠΕ82">ΠΕ82 Μηχανολόγων</option>
        <option value="ΠΕ83">ΠΕ83 Ηλεκτρολόγων</option>
        <option value="ΠΕ84">ΠΕ84 Ηλεκτρονικών</option>
        <option value="ΠΕ85">ΠΕ85 Χημικών Μηχανικών</option>
        <option value="ΠΕ86">ΠΕ86 Πληροφορικής</option>
        <option value="ΠΕ88">ΠΕ88 Γεωπονίας / Διατροφής / Περιβάλλοντος</option>
        <option value="ΤΕ16">ΤΕ16 Μουσικής</option>
      </select>
    </div>

    <p class="note">
      Για τους κλάδους ΠΕ γίνεται αναλυτικός υπολογισμός των ακαδημαϊκών προσόντων των 1ΓΕ/2026 και 2ΓΕ/2026.
      Για τον ΤΕ16 χρησιμοποιείται η καταχώριση των ακαδημαϊκών μορίων όπως εμφανίζονται στον πίνακα της 1ΓΤ/2024.
    </p>
  </div>

  <div class="section" id="academicSection">
    <h2>2. Ακαδημαϊκά Προσόντα Α.Σ.Ε.Π.</h2>

    <div class="mode-row" id="modeRow">
      <label>
        <input type="radio" name="academicMode" value="detailed" checked>
        Αναλυτικός υπολογισμός
      </label>
      <label>
        <input type="radio" name="academicMode" value="manual">
        Γνωρίζω ήδη τα ακαδημαϊκά μόρια από τον πίνακα Α.Σ.Ε.Π.
      </label>
    </div>

    <div id="manualAcademic" class="hidden">
      <div class="question">
        <label for="manualAcademicPoints">Ακαδημαϊκά μόρια Α.Σ.Ε.Π. (0-120)</label>
        <input type="text" inputmode="decimal" id="manualAcademicPoints" placeholder="π.χ. 82,50">
      </div>
      <p class="note">
        Συμπλήρωσε μόνο τη μοριοδότηση της κατηγορίας «Ακαδημαϊκά Προσόντα» και όχι το συνολικό σκορ κατάταξης.
      </p>
    </div>

    <div id="detailedAcademic">
      <h3>Τίτλοι σπουδών</h3>
      <div class="field-grid">
        <div class="question">
          <label for="degreeGrade">Βαθμός βασικού τίτλου σπουδών <small>Έγκυρος βαθμός: 5,00–10,00.</small></label>
          <input type="text" inputmode="decimal" id="degreeGrade" placeholder="π.χ. 7,50">
        </div>

        <div class="question">
          <label for="secondDegree">Δεύτερο πτυχίο Α.Ε.Ι.</label>
          <select id="secondDegree">
            <option value="no">Όχι</option>
            <option value="yes">Ναι</option>
          </select>
        </div>

        <div class="question">
          <label for="phd">Διδακτορικό δίπλωμα</label>
          <select id="phd">
            <option value="no">Όχι</option>
            <option value="yes">Ναι</option>
          </select>
        </div>

        <div class="question">
          <label for="mscCount">Μεταπτυχιακός τίτλος / integrated master</label>
          <select id="mscCount">
            <option value="0">Κανένας</option>
            <option value="1">Ένας τίτλος</option>
            <option value="2">Δύο τίτλοι</option>
          </select>
        </div>
      </div>

      <p class="note">
        Βασικός τίτλος: βαθμός × 2,5 · Δεύτερο πτυχίο: 7 · Διδακτορικό: 40 ·
        1ος μεταπτυχιακός / integrated master: 20 · 2ος: 8 μόρια.
      </p>

      <h3>Ξένες γλώσσες</h3>
      <div class="field-grid">
        <div class="question">
          <label for="language1">1η ξένη γλώσσα</label>
          <select id="language1">
            <option value="">-- Καμία --</option>
            <option value="en">Αγγλική</option>
            <option value="fr">Γαλλική</option>
            <option value="de">Γερμανική</option>
            <option value="it">Ιταλική</option>
            <option value="es">Ισπανική</option>
            <option value="other">Άλλη ξένη γλώσσα</option>
          </select>
        </div>

        <div class="question">
          <label for="level1">Επίπεδο 1ης γλώσσας</label>
          <select id="level1">
            <option value="none">-- Κανένα --</option>
            <option value="excellent">Άριστη γνώση</option>
            <option value="very_good">Πολύ καλή γνώση</option>
            <option value="good">Καλή γνώση</option>
          </select>
        </div>

        <div class="question">
          <label for="language2">2η ξένη γλώσσα</label>
          <select id="language2">
            <option value="">-- Καμία --</option>
            <option value="en">Αγγλική</option>
            <option value="fr">Γαλλική</option>
            <option value="de">Γερμανική</option>
            <option value="it">Ιταλική</option>
            <option value="es">Ισπανική</option>
            <option value="other2">Άλλη ξένη γλώσσα</option>
          </select>
        </div>

        <div class="question">
          <label for="level2">Επίπεδο 2ης γλώσσας</label>
          <select id="level2">
            <option value="none">-- Κανένα --</option>
            <option value="excellent">Άριστη γνώση</option>
            <option value="very_good">Πολύ καλή γνώση</option>
            <option value="good">Καλή γνώση</option>
          </select>
        </div>
      </div>

      <p class="note">
        Μοριοδοτούνται έως δύο ξένες γλώσσες: άριστη 7, πολύ καλή 5, καλή 3 μόρια.
        Η Γαλλική δεν μοριοδοτείται στον ΠΕ05, η Αγγλική στον ΠΕ06 και η Γερμανική στον ΠΕ07.
      </p>

      <h3>Λοιπά ακαδημαϊκά προσόντα</h3>
      <div class="field-grid">
        <div class="question">
          <label for="computer">Πιστοποιημένη γνώση Η/Υ / ΤΠΕ Α' επιπέδου</label>
          <select id="computer">
            <option value="no">Όχι</option>
            <option value="yes">Ναι</option>
          </select>
        </div>

        <div class="question">
          <label for="training">Επιμόρφωση τουλάχιστον 300 ωρών και 7 μηνών</label>
          <select id="training">
            <option value="no">Όχι</option>
            <option value="yes">Ναι</option>
          </select>
        </div>
      </div>

      <p class="note">
        Γνώση Η/Υ: 4 μόρια (δεν μοριοδοτείται στον ΠΕ86) · Επιμόρφωση: 2 μόρια.
        Το σύνολο των Ακαδημαϊκών Προσόντων δεν μπορεί να υπερβεί τις 120 μονάδες.
      </p>
    </div>
  </div>

  <div class="section">
    <h2>3. Προϋπηρεσία σε Πρότυπα ή Πειραματικά Σχολεία</h2>

    <p class="note" style="margin-bottom:14px;">
      Καταχώρισε τους <strong>αναγνωρισμένους μήνες ανά σχολικό έτος</strong>.
      Κάθε μήνας δίνει 1,5 μόριο, με μέγιστο 15 μόρια ανά σχολικό έτος.
      Οι μήνες Ιουλίου και Αυγούστου δεν προσμετρώνται.
    </p>

    <div id="serviceRows"></div>

    <button type="button" class="add-row" onclick="addServiceRow()">+ Προσθήκη σχολικού έτους</button>
  </div>

  <div class="actions">
    <button type="button" onclick="calculatePoints()">Υπολόγισε τα μόρια ΔΗΜ.Ω.Σ.</button>
    <button type="button" class="reset-btn" onclick="resetForm()">Καθαρισμός</button>
  </div>

  <div id="result" class="result" role="status" aria-live="polite"></div>
</div>

<script src="includes/academic-calculations.js"></script>
<script>
  const schoolYears = [];
  for (let y = 2025; y >= 1990; y--) {
    schoolYears.push(`${y}-${y + 1}`);
  }

  function valueOf(id) {
    return document.getElementById(id).value;
  }

  function yes(id) {
    return valueOf(id) === "yes";
  }

  function greekNumber(value) {
    if (typeof value !== "string") return 0;
    const normalized = value.trim().replace(/\s/g, "").replace(",", ".");
    const n = parseFloat(normalized);
    return Number.isFinite(n) ? n : 0;
  }

  function formatPoints(value) {
    const rounded = Math.round((value + Number.EPSILON) * 100) / 100;
    return rounded.toLocaleString("el-GR", {
      minimumFractionDigits: Number.isInteger(rounded) ? 0 : 2,
      maximumFractionDigits: 2
    });
  }

  function showError(message) {
    const result = document.getElementById("result");
    result.style.display = "block";
    result.className = "result error";
    result.innerHTML = message;
    result.scrollIntoView({ behavior: "smooth", block: "nearest" });
  }

  function showResult(html) {
    const result = document.getElementById("result");
    result.style.display = "block";
    result.className = "result";
    result.innerHTML = html;
    result.scrollIntoView({ behavior: "smooth", block: "nearest" });
  }

  function currentAcademicMode() {
    return document.querySelector('input[name="academicMode"]:checked').value;
  }

  function updateAcademicMode() {
    const specialty = valueOf("specialty");
    const detailedRadio = document.querySelector('input[name="academicMode"][value="detailed"]');
    const manualRadio = document.querySelector('input[name="academicMode"][value="manual"]');

    if (specialty === "ΤΕ16") {
      manualRadio.checked = true;
      detailedRadio.disabled = true;
    } else {
      detailedRadio.disabled = false;
    }

    const manual = currentAcademicMode() === "manual";
    document.getElementById("manualAcademic").classList.toggle("hidden", !manual);
    document.getElementById("detailedAcademic").classList.toggle("hidden", manual);
  }

  document.querySelectorAll('input[name="academicMode"]').forEach(el => {
    el.addEventListener("change", updateAcademicMode);
  });

  document.getElementById("specialty").addEventListener("change", updateAcademicMode);

  function calculateDetailedAcademic(specialty, warnings) {
    const academic = EducationAcademic.calculate({
      specialty: specialty,
      degreeGrade: valueOf("degreeGrade"),
      secondDegree: yes("secondDegree"),
      phd: yes("phd"),
      mscCount: parseInt(valueOf("mscCount"), 10) || 0,
      languages: [
        { language: valueOf("language1"), level: valueOf("level1") },
        { language: valueOf("language2"), level: valueOf("level2") }
      ],
      computer: yes("computer"),
      training: yes("training")
    });

    warnings.push(...academic.warnings);
    return academic;
  }

  function serviceYearOptions(selectedYear = "") {
    return schoolYears.map(y =>
      `<option value="${y}" ${y === selectedYear ? "selected" : ""}>${y}</option>`
    ).join("");
  }

  let serviceRowCounter = 0;

  function addServiceRow(selectedYear = "", months = "") {
    serviceRowCounter += 1;
    const wrap = document.getElementById("serviceRows");
    const row = document.createElement("div");
    row.className = "service-row";
    row.innerHTML = `
      <div class="question">
        <label for="serviceYear_${serviceRowCounter}">Σχολικό έτος</label>
        <select class="service-year" id="serviceYear_${serviceRowCounter}">
          <option value="">-- Επιλογή --</option>
          ${serviceYearOptions(selectedYear)}
        </select>
      </div>
      <div class="question">
        <label for="serviceMonths_${serviceRowCounter}">Αναγνωρισμένοι μήνες</label>
        <input class="service-months" id="serviceMonths_${serviceRowCounter}" type="text" inputmode="decimal" value="${months}" placeholder="0 έως 10">
      </div>
      <button type="button" class="remove-row" aria-label="Αφαίρεση γραμμής προϋπηρεσίας" onclick="this.parentElement.remove()">Αφαίρεση</button>
    `;
    wrap.appendChild(row);
  }

  function calculateService(warnings) {
    const totalsByYear = {};

    document.querySelectorAll(".service-row").forEach(row => {
      const year = row.querySelector(".service-year").value;
      const months = greekNumber(row.querySelector(".service-months").value);

      if (!year && months > 0) {
        throw new Error("Υπάρχει γραμμή προϋπηρεσίας με μήνες αλλά χωρίς επιλεγμένο σχολικό έτος.");
      }

      if (months < 0) {
        throw new Error("Οι μήνες προϋπηρεσίας δεν μπορούν να είναι αρνητικοί.");
      }

      if (!year || months === 0) return;

      totalsByYear[year] = (totalsByYear[year] || 0) + months;
    });

    let totalPoints = 0;
    const details = [];

    Object.keys(totalsByYear).sort().reverse().forEach(year => {
      const months = totalsByYear[year];
      const rawPoints = months * 1.5;
      const points = Math.min(rawPoints, 15);
      totalPoints += points;

      details.push(`${year}: ${formatPoints(months)} μήνες → ${formatPoints(points)} μόρια`);

      if (months > 10) {
        warnings.push(`${year}: δηλώθηκαν ${formatPoints(months)} μήνες. Εφαρμόστηκε το ετήσιο πλαφόν των 15 μορίων.`);
      }
    });

    return { points: totalPoints, details };
  }

  function calculatePoints() {
    const specialty = valueOf("specialty");
    if (!specialty) {
      showError("Παρακαλώ επίλεξε κλάδο / ειδικότητα.");
      return;
    }

    const warnings = [];

    try {
      let academicPoints = 0;
      let academicDetails = [];

      if (currentAcademicMode() === "manual") {
        const manualRaw = valueOf("manualAcademicPoints").trim();
        if (manualRaw === "") {
          throw new Error("Παρακαλώ συμπλήρωσε τα ακαδημαϊκά μόρια Α.Σ.Ε.Π.");
        }
        academicPoints = greekNumber(manualRaw);
        if (academicPoints < 0 || academicPoints > 120) {
          throw new Error("Τα ακαδημαϊκά μόρια Α.Σ.Ε.Π. πρέπει να είναι από 0 έως 120.");
        }
        academicDetails.push("Καταχώριση από τον πίνακα Α.Σ.Ε.Π.");
      } else {
        const academic = calculateDetailedAcademic(specialty, warnings);
        academicPoints = academic.points;
        academicDetails = academic.details;
      }

      const service = calculateService(warnings);
      const total = academicPoints + service.points;

      const detailText = items => items.length ? items.join("<br>") : "—";

      let html = `
        <h2>Αποτέλεσμα</h2>
        <div class="big-total">${formatPoints(total)} μόρια ΔΗΜ.Ω.Σ.</div>

        <table class="breakdown">
          <tr>
            <th>Κατηγορία</th>
            <th>Μόρια</th>
            <th>Ανάλυση</th>
          </tr>
          <tr>
            <td>Ακαδημαϊκά Προσόντα Α.Σ.Ε.Π.</td>
            <td>${formatPoints(academicPoints)}</td>
            <td>${detailText(academicDetails)}</td>
          </tr>
          <tr>
            <td>Πρότυπα / Πειραματικά Σχολεία</td>
            <td>${formatPoints(service.points)}</td>
            <td>${detailText(service.details)}</td>
          </tr>
          <tr class="total-row">
            <td>Συνολικά μόρια ΔΗΜ.Ω.Σ.</td>
            <td>${formatPoints(total)}</td>
            <td>Ακαδημαϊκά + ειδική προϋπηρεσία</td>
          </tr>
        </table>

        <p class="note" style="margin-top:14px;">
          Σε περίπτωση ισοβαθμίας, η πρόσκληση προβλέπει πρόταξη του υποψηφίου με περισσότερη προϋπηρεσία στον πίνακα Α.Σ.Ε.Π.
        </p>
      `;

      if (warnings.length) {
        html += `<div class="warning">Προσοχή:<br>${warnings.map(w => "• " + w).join("<br>")}</div>`;
      }

      showResult(html);
    } catch (err) {
      showError(err.message || "Παρουσιάστηκε σφάλμα στον υπολογισμό.");
    }
  }

  function resetForm() {
    document.getElementById("specialty").value = "";
    document.querySelector('input[name="academicMode"][value="detailed"]').checked = true;
    document.querySelector('input[name="academicMode"][value="detailed"]').disabled = false;
    document.getElementById("manualAcademicPoints").value = "";
    document.getElementById("degreeGrade").value = "";
    document.getElementById("secondDegree").value = "no";
    document.getElementById("phd").value = "no";
    document.getElementById("mscCount").value = "0";
    document.getElementById("language1").value = "";
    document.getElementById("level1").value = "none";
    document.getElementById("language2").value = "";
    document.getElementById("level2").value = "none";
    document.getElementById("computer").value = "no";
    document.getElementById("training").value = "no";
    document.getElementById("serviceRows").innerHTML = "";
    addServiceRow("2025-2026");
    addServiceRow("2024-2025");
    addServiceRow("2023-2024");
    document.getElementById("result").style.display = "none";
    updateAcademicMode();
  }

  addServiceRow("2025-2026");
  addServiceRow("2024-2025");
  addServiceRow("2023-2024");
  updateAcademicMode();
</script>


<section class="edu-source-card" aria-labelledby="sourcesTitle">
  <h2 id="sourcesTitle">Πηγές / Νομική βάση</h2>
  <p>Προκήρυξη της Διοικούσας Επιτροπής Δημοσίων Ωνασείων Σχολείων για τη διαδικασία κατάταξης εκπαιδευτικών Δευτεροβάθμιας Εκπαίδευσης και την πλήρωση κενών θέσεων θητείας στα ΔΗΜ.Ω.Σ. για το 2026. Για τα ακαδημαϊκά κριτήρια που αντλούνται από τους πίνακες Α.Σ.Ε.Π. χρησιμοποιούνται και οι κανόνες των 1ΓΕ/2026–2ΓΕ/2026.</p>
  <p class="source-links"><a href="https://www.minedu.gov.gr/news?catid=1183&id=63940%3A30-01-26-prokiryksi-diadikasias-katataksis-ekpaideftikon-vvathmias-ekpaidefsis-me-seira-proteraiotitas-kata-klado-kai-eidikotita-ypopsifion-gia-tin-plirosi-kenon-theseon-thiteias-sta-dimosia-onaseia-sxoleia&view=article" target="_blank" rel="noopener noreferrer">Επίσημη προκήρυξη ΔΗΜ.Ω.Σ. — ΥΠΑΙΘΑ ↗</a> · <a href="https://info.asep.gr/node/78737" target="_blank" rel="noopener noreferrer">1ΓΕ/2026 &amp; 2ΓΕ/2026 — ΑΣΕΠ ↗</a></p>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
  <script src="assets/common.js"></script>
</body>
</html>
