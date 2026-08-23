<!DOCTYPE html>
<html lang="el">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Υπολογισμός μορίων αναπληρωτών στα Δημόσια Ωνάσεια Σχολεία 2026-2027</title>
<link rel="stylesheet" href="assets/common.css?v=3.20.9-b2">
</head>
<body class="edu-ui edu-page-onaseia">
<?php require_once __DIR__ . '/includes/header.php'; ?>

<div class="app-box edu-modernized">
  <section class="hero edu-legacy-hero">
<h1>Υπολογισμός μορίων αναπληρωτών στα Δημόσια Ωνάσεια Σχολεία</h1>
<p class="intro">
    Σχολικό έτος <strong>2026-2027</strong>. Ο υπολογισμός βασίζεται στα ακαδημαϊκά προσόντα
    του πίνακα Α.Σ.Ε.Π. και στην αναγνωρισμένη προϋπηρεσία σε Πρότυπα ή Πειραματικά Σχολεία.
  </p>
</section>

  <section class="deadline-card onaseia-deadline-card" aria-labelledby="onaseiaDeadlinesTitle">
    <h2 id="onaseiaDeadlinesTitle">📅 Προθεσμίες αιτήσεων ΔΗΜ.Ω.Σ. 2026–2027</h2>
    <p class="onaseia-deadline-intro">Οι δύο φετινές προσκλήσεις έχουν διαφορετική καταληκτική ημερομηνία. Η αντίστροφη μέτρηση γίνεται σε ώρα Ελλάδας.</p>
    <div class="onaseia-deadline-grid">
      <article class="onaseia-deadline-item">
        <h3>Γενική πρόσκληση εκπαιδευτικών</h3>
        <p class="onaseia-deadline-meta">1ΓΕ/2026, 2ΓΕ/2026 και 1ΓΤ/2024<br>Αιτήσεις έως <strong>Δευτέρα 24 Αυγούστου 2026, 15:00</strong>.</p>
        <div id="onaseiaGeneralDeadlineStatus" class="onaseia-deadline-status" role="status" aria-live="polite"></div>
        <a class="onaseia-deadline-link" href="https://diavgeia.gov.gr/doc/%CE%957%CE%98%CE%9146%CE%9D%CE%9A%CE%A0%CE%94-%CE%A1%CE%9C%CE%98?inline=true" target="_blank" rel="noopener noreferrer">Πρόσκληση στη Διαύγεια — ΑΔΑ Ε7ΘΑ46ΝΚΠΔ-ΡΜΘ ↗</a>
      </article>
      <article class="onaseia-deadline-item">
        <h3>Ειδική πρόσκληση ΕΑΕ — Τμήματα Ένταξης</h3>
        <p class="onaseia-deadline-meta">3ΕΑ/2025 — ΠΕ02, ΠΕ03 και ΠΕ04 με εξειδίκευση στην ΕΑΕ<br>Αιτήσεις έως <strong>Δευτέρα 31 Αυγούστου 2026, 15:00</strong>.<br><strong>Για την ΕΑΕ:</strong> χρησιμοποίησε τη χειροκίνητη καταχώριση των ακαδημαϊκών μορίων όπως εμφανίζονται στον πίνακα 3ΕΑ/2025· ο αναλυτικός υπολογισμός της σελίδας αφορά 1ΓΕ/2026–2ΓΕ/2026.</p>
        <div id="onaseiaEaeDeadlineStatus" class="onaseia-deadline-status" role="status" aria-live="polite"></div>
        <a class="onaseia-deadline-link" href="https://diavgeia.gov.gr/doc/%CE%A1%CE%A4%CE%91%CE%A746%CE%9D%CE%9A%CE%A0%CE%94-%CE%932%CE%97?inline=true" target="_blank" rel="noopener noreferrer">Πρόσκληση ΕΑΕ στη Διαύγεια — ΑΔΑ ΡΤΑΧ46ΝΚΠΔ-Γ2Η ↗</a>
      </article>
    </div>
    <p class="onaseia-deadline-note">Το countdown είναι ενημερωτικό. Για την ακριβή ισχύ της προθεσμίας υπερισχύει πάντοτε το κείμενο της αντίστοιχης επίσημης πρόσκλησης.</p>
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
        <label for="manualAcademicPoints">Ακαδημαϊκά μόρια Α.Σ.Ε.Π. (<span id="manualAcademicRange">12,50–120</span>)</label>
        <input type="text" inputmode="decimal" id="manualAcademicPoints" placeholder="π.χ. 82,50" aria-describedby="manualAcademicHelp">
      </div>
      <p class="note" id="manualAcademicHelp">
        Συμπλήρωσε μόνο τη μοριοδότηση της κατηγορίας «Ακαδημαϊκά Προσόντα» και όχι το συνολικό σκορ κατάταξης.
        Για τους κλάδους ΠΕ το ελάχιστο είναι <strong>12,50 μόρια</strong> (βαθμός βασικού τίτλου 5,00 × 2,5).
        Για τον ΤΕ16, που χρησιμοποιεί τα μόρια της 1ΓΤ/2024, το ελάχιστο είναι <strong>30 μόρια</strong>.
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
        Η ίδια κατονομασμένη γλώσσα δεν μπορεί να επιλεγεί και στα δύο πεδία.
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

    <p class="note">
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

<script src="includes/academic-calculations.js?v=3.20.4"></script>
<script src="includes/onaseia-calculations.js?v=3.20.4"></script>
<script>
  let isLiveCalculation = false;
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
    if (!isLiveCalculation) result.scrollIntoView({ behavior: "smooth", block: "nearest" });
  }

  function showResult(html) {
    const result = document.getElementById("result");
    result.style.display = "block";
    result.className = "result";
    result.innerHTML = html;
    if (!isLiveCalculation) result.scrollIntoView({ behavior: "smooth", block: "nearest" });
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

    const manualMin = OnaseiaAcademic.manualAcademicMin(specialty);
    document.getElementById("manualAcademicRange").textContent = `${formatPoints(manualMin)}–120`;
    document.getElementById("manualAcademicPoints").setAttribute("data-min", String(manualMin));
    document.getElementById("manualAcademicPoints").setAttribute("data-max", "120");
  }

  document.querySelectorAll('input[name="academicMode"]').forEach(el => {
    el.addEventListener("change", updateAcademicMode);
  });

  document.getElementById("specialty").addEventListener("change", updateAcademicMode);

  const onaseiaNamedLanguages = new Set(["en", "fr", "de", "it", "es"]);

  function syncOnaseiaLanguageOptions(changedId = "") {
    const language1 = document.getElementById("language1");
    const language2 = document.getElementById("language2");
    const level1 = document.getElementById("level1");
    const level2 = document.getElementById("level2");

    if (onaseiaNamedLanguages.has(language1.value) && language1.value === language2.value) {
      if (changedId === "language2") {
        language1.value = "";
        level1.value = "none";
      } else {
        language2.value = "";
        level2.value = "none";
      }
    }

    Array.from(language1.options).forEach(option => {
      option.disabled = onaseiaNamedLanguages.has(option.value) && option.value === language2.value;
    });
    Array.from(language2.options).forEach(option => {
      option.disabled = onaseiaNamedLanguages.has(option.value) && option.value === language1.value;
    });
  }

  document.getElementById("language1").addEventListener("change", () => syncOnaseiaLanguageOptions("language1"));
  document.getElementById("language2").addEventListener("change", () => syncOnaseiaLanguageOptions("language2"));
  syncOnaseiaLanguageOptions();

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
        const manualCheck = OnaseiaAcademic.validateManualAcademicPoints(manualRaw, specialty);
        if (manualCheck.reason === "empty") {
          throw new Error("Παρακαλώ συμπλήρωσε τα ακαδημαϊκά μόρια Α.Σ.Ε.Π.");
        }
        if (!manualCheck.valid) {
          throw new Error(`Τα ακαδημαϊκά μόρια Α.Σ.Ε.Π. πρέπει να είναι από ${formatPoints(manualCheck.min)} έως ${formatPoints(manualCheck.max)}.`);
        }
        academicPoints = manualCheck.points;
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

        <p class="note">
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

  function clearLiveResult() {
    const result = document.getElementById("result");
    result.style.display = "none";
    result.innerHTML = "";
    result.className = "result";
  }

  function liveCalculatePoints() {
    const specialty = valueOf("specialty");
    if (!specialty) { clearLiveResult(); return; }

    if (currentAcademicMode() === "manual") {
      const check = OnaseiaAcademic.validateManualAcademicPoints(valueOf("manualAcademicPoints"), specialty);
      if (!check.valid) { clearLiveResult(); return; }
    } else {
      const grade = greekNumber(valueOf("degreeGrade"));
      if (!grade || grade < 5 || grade > 10) { clearLiveResult(); return; }
    }

    isLiveCalculation = true;
    try { calculatePoints(); } finally { isLiveCalculation = false; }
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
    syncOnaseiaLanguageOptions();
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
  const onaseiaDeadlines = [
    { id: "onaseiaGeneralDeadlineStatus", end: new Date("2026-08-24T15:00:00+03:00") },
    { id: "onaseiaEaeDeadlineStatus", end: new Date("2026-08-31T15:00:00+03:00") }
  ];

  function formatOnaseiaRemaining(ms) {
    const totalSeconds = Math.max(0, Math.floor(ms / 1000));
    const days = Math.floor(totalSeconds / 86400);
    const hours = Math.floor((totalSeconds % 86400) / 3600);
    const minutes = Math.floor((totalSeconds % 3600) / 60);
    const seconds = totalSeconds % 60;
    const parts = [];
    if (days) parts.push(days + (days === 1 ? " ημέρα" : " ημέρες"));
    parts.push(String(hours).padStart(2, "0") + " ώρες");
    parts.push(String(minutes).padStart(2, "0") + " λεπτά");
    parts.push(String(seconds).padStart(2, "0") + " δευτ.");
    return parts.join(", ");
  }

  function updateOnaseiaDeadlines() {
    const now = new Date();
    onaseiaDeadlines.forEach(item => {
      const box = document.getElementById(item.id);
      if (!box) return;
      box.className = "onaseia-deadline-status";
      if (now <= item.end) {
        box.classList.add("open");
        box.innerHTML = "🟢 Η προθεσμία είναι ανοικτή." +
          '<span class="onaseia-countdown">Απομένουν: <strong>' + formatOnaseiaRemaining(item.end - now) + "</strong></span>";
      } else {
        box.classList.add("closed");
        box.textContent = "🔴 Η προθεσμία έχει λήξει.";
      }
    });
  }

  updateOnaseiaDeadlines();
  setInterval(updateOnaseiaDeadlines, 1000);

  document.addEventListener("input", event => {
    if (event.target && event.target.matches("input, select")) liveCalculatePoints();
  });
  document.addEventListener("change", event => {
    if (event.target && event.target.matches("input, select")) liveCalculatePoints();
  });
</script>


<section class="edu-source-card" aria-labelledby="sourcesTitle">
  <h2 id="sourcesTitle">Πηγές / Νομική βάση</h2>
  <p>Η κατάταξη στα ΔΗΜ.Ω.Σ. συνδυάζει τη μοριοδότηση των ακαδημαϊκών προσόντων όπως έχει διαμορφωθεί στον αντίστοιχο πίνακα Α.Σ.Ε.Π. με την αναγνωρισμένη προϋπηρεσία σε Πρότυπα ή Πειραματικά Σχολεία.</p>
  <p><strong>Φετινές προσκλήσεις πρόσληψης 2026–2027:</strong><br>
    <a href="https://diavgeia.gov.gr/doc/%CE%957%CE%98%CE%9146%CE%9D%CE%9A%CE%A0%CE%94-%CE%A1%CE%9C%CE%98?inline=true" target="_blank" rel="noopener noreferrer">14/08/2026 — Γενική πρόσκληση ΔΗΜ.Ω.Σ. για αναπληρωτές — ΑΔΑ Ε7ΘΑ46ΝΚΠΔ-ΡΜΘ ↗</a><br>
    <a href="https://diavgeia.gov.gr/doc/%CE%A1%CE%A4%CE%91%CE%A746%CE%9D%CE%9A%CE%A0%CE%94-%CE%932%CE%97?inline=true" target="_blank" rel="noopener noreferrer">20/08/2026 — Ειδική πρόσκληση ΕΑΕ για Τμήματα Ένταξης ΔΗΜ.Ω.Σ. — ΑΔΑ ΡΤΑΧ46ΝΚΠΔ-Γ2Η ↗</a>
  </p>
  <p class="source-links"><a href="https://www.minedu.gov.gr/news?catid=1183&id=63940%3A30-01-26-prokiryksi-diadikasias-katataksis-ekpaideftikon-vvathmias-ekpaidefsis-me-seira-proteraiotitas-kata-klado-kai-eidikotita-ypopsifion-gia-tin-plirosi-kenon-theseon-thiteias-sta-dimosia-onaseia-sxoleia&view=article" target="_blank" rel="noopener noreferrer">Προκήρυξη διαδικασίας κατάταξης ΔΗΜ.Ω.Σ. — ΥΠΑΙΘΑ ↗</a> · <a href="https://info.asep.gr/node/78737" target="_blank" rel="noopener noreferrer">1ΓΕ/2026 &amp; 2ΓΕ/2026 — ΑΣΕΠ ↗</a></p>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
  <script src="assets/common.js?v=3.20.9-b2"></script>
</body>
</html>
