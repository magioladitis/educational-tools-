<?php require_once __DIR__ . '/includes/config.php'; ?>
<!DOCTYPE html>
<html lang="el">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Υπολογισμός μορίων αναπληρωτών στα Δημόσια Ωνάσεια Σχολεία 2026-2027</title>
<link rel="stylesheet" href="<?php echo htmlspecialchars(edu_asset_url('assets/common.css'), ENT_QUOTES, 'UTF-8'); ?>">
</head>
<body class="edu-ui edu-page-onaseia">
<?php require_once __DIR__ . '/includes/header.php'; ?>
<?php require_once __DIR__ . '/includes/components/calculator-layout.php'; ?>
<?php require_once __DIR__ . '/includes/components/deadline-card.php'; ?>
<?php require_once __DIR__ . '/includes/components/asep-pe-academic.php'; ?>

<?php calculatorContainerStart(array('class' => 'app-box edu-modernized')); ?>
  <?php calculatorHeroStart(array('class' => 'hero edu-legacy-hero')); ?>
<h1>Υπολογισμός μορίων αναπληρωτών στα Δημόσια Ωνάσεια Σχολεία</h1>
<p class="intro">
    Σχολικό έτος <strong>2026-2027</strong>. Ο υπολογισμός βασίζεται στα ακαδημαϊκά προσόντα
    του πίνακα Α.Σ.Ε.Π. και στην αναγνωρισμένη προϋπηρεσία σε Πρότυπα ή Πειραματικά Σχολεία.
  </p>
<?php calculatorHeroEnd(); ?>

  <?php
renderDeadlineCard(array(
    'title' => '📅 Προθεσμίες αιτήσεων ΔΗΜ.Ω.Σ. 2026–2027',
    'intro' => 'Οι δύο φετινές προσκλήσεις έχουν διαφορετική καταληκτική ημερομηνία. Η αντίστροφη μέτρηση γίνεται σε ώρα Ελλάδας.',
    'items' => array(
        array(
            'title' => 'Γενική πρόσκληση εκπαιδευτικών',
            'meta_html' => '1ΓΕ/2026, 2ΓΕ/2026 και 1ΓΤ/2024<br>Αιτήσεις έως <strong>Δευτέρα 24 Αυγούστου 2026, 15:00</strong>.',
            'end' => '2026-08-24T15:00:00+03:00',
            'source_url' => 'https://diavgeia.gov.gr/doc/%CE%957%CE%98%CE%9146%CE%9D%CE%9A%CE%A0%CE%94-%CE%A1%CE%9C%CE%98?inline=true',
            'source_label' => 'Πρόσκληση — ΑΔΑ Ε7ΘΑ46ΝΚΠΔ-ΡΜΘ ↗'
        ),
        array(
            'title' => 'Ειδική πρόσκληση ΕΑΕ — Τμήματα Ένταξης',
            'meta_html' => '3ΕΑ/2025 — ΠΕ02, ΠΕ03 και ΠΕ04 με εξειδίκευση στην ΕΑΕ<br>Αιτήσεις έως <strong>Δευτέρα 31 Αυγούστου 2026, 15:00</strong>.<br><strong>Για την ΕΑΕ:</strong> χρησιμοποίησε τη χειροκίνητη καταχώριση των ακαδημαϊκών μορίων όπως εμφανίζονται στον πίνακα 3ΕΑ/2025.',
            'end' => '2026-08-31T15:00:00+03:00',
            'source_url' => 'https://diavgeia.gov.gr/doc/%CE%A1%CE%A4%CE%91%CE%A746%CE%9D%CE%9A%CE%A0%CE%94-%CE%932%CE%97?inline=true',
            'source_label' => 'Πρόσκληση ΕΑΕ — ΑΔΑ ΡΤΑΧ46ΝΚΠΔ-Γ2Η ↗'
        )
    ),
    'note_html' => 'Το countdown είναι ενημερωτικό. Για την ακριβή ισχύ της προθεσμίας υπερισχύει πάντοτε το κείμενο της αντίστοιχης επίσημης πρόσκλησης.'
));
?>

  <div class="important">
    <strong>Τύπος υπολογισμού:</strong><br>
    Μόρια ΔΗΜ.Ω.Σ. = Ακαδημαϊκά Προσόντα Α.Σ.Ε.Π. + μόρια προϋπηρεσίας σε Πρότυπα/Πειραματικά.<br>
    Η ειδική προϋπηρεσία μοριοδοτείται με <strong>1,5 μόριο ανά αναγνωρισμένο μήνα</strong>,
    με ανώτατο όριο <strong>15 μόρια ανά σχολικό έτος</strong>.
  </div>

  <?php calculatorCardStart(array('tag' => 'div', 'class' => 'section')); ?>
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
  <?php calculatorCardEnd(); ?>

  <?php calculatorCardStart(array('tag' => 'div', 'class' => 'section', 'id' => 'academicSection')); ?>
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
<?php
renderAsepPeAcademic(array(
    'id' => 'asepPeAcademic',
    'specialty_id' => 'specialty',
    'field_class' => 'question',
    'degree_input_type' => 'text',
    'show_subtotal' => false
));
?>
    </div>
  <?php calculatorCardEnd(); ?>

  <?php calculatorCardStart(array('tag' => 'div', 'class' => 'section')); ?>
    <h2>3. Προϋπηρεσία σε Πρότυπα ή Πειραματικά Σχολεία</h2>

    <p class="note">
      Καταχώρισε τους <strong>αναγνωρισμένους μήνες ανά σχολικό έτος</strong>.
      Κάθε μήνας δίνει 1,5 μόριο, με μέγιστο 15 μόρια ανά σχολικό έτος.
      Οι μήνες Ιουλίου και Αυγούστου δεν προσμετρώνται.
    </p>

    <div id="serviceRows"></div>

    <button type="button" class="add-row" onclick="addServiceRow()">+ Προσθήκη σχολικού έτους</button>
  <?php calculatorCardEnd(); ?>

  <?php calculatorActions(array(
    array('label' => 'Υπολόγισε τα μόρια ΔΗΜ.Ω.Σ.', 'attrs' => array('type' => 'button', 'onclick' => 'calculatePoints()')),
    array('label' => 'Καθαρισμός', 'class' => 'reset-btn', 'attrs' => array('type' => 'button', 'onclick' => 'resetForm()'))
  )); ?>

  <?php calculatorInlineResult(array('id' => 'result', 'class' => 'result', 'attrs' => array('role' => 'status', 'aria-live' => 'polite'))); ?>
<?php calculatorContainerEnd(); ?>

<script src="<?php echo htmlspecialchars(edu_asset_url('includes/language-calculations.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
<script src="<?php echo htmlspecialchars(edu_asset_url('includes/asep-language-selector.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
<script src="<?php echo htmlspecialchars(edu_asset_url('includes/academic-calculations.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
<script src="<?php echo htmlspecialchars(edu_asset_url('includes/asep-computer-proof.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
<script src="<?php echo htmlspecialchars(edu_asset_url('includes/training-proof.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
<script src="<?php echo htmlspecialchars(edu_asset_url('includes/asep-pe-academic.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
<script src="<?php echo htmlspecialchars(edu_asset_url('includes/onaseia-calculations.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
<script>
  let isLiveCalculation = false;
  const schoolYears = [];
  for (let y = 2025; y >= 1990; y--) {
    schoolYears.push(`${y}-${y + 1}`);
  }

  function valueOf(id) {
    return document.getElementById(id).value;
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
    AsepPeAcademic.sync("asepPeAcademic");
  }

  document.querySelectorAll('input[name="academicMode"]').forEach(el => {
    el.addEventListener("change", updateAcademicMode);
  });

  document.getElementById("specialty").addEventListener("change", updateAcademicMode);


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
        <label for="serviceYear${serviceRowCounter}">Σχολικό έτος</label>
        <select class="service-year" id="serviceYear${serviceRowCounter}">
          <option value="">-- Επιλογή --</option>
          ${serviceYearOptions(selectedYear)}
        </select>
      </div>
      <div class="question">
        <label for="serviceMonths${serviceRowCounter}">Αναγνωρισμένοι μήνες</label>
        <input class="service-months" id="serviceMonths${serviceRowCounter}" type="text" inputmode="decimal" data-min="0" data-max="10" value="${months}" placeholder="0 έως 10">
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
      const enteredMonths = totalsByYear[year];
      const months = Math.min(10, enteredMonths);
      const points = months * 1.5;
      totalPoints += points;

      details.push(`${year}: ${formatPoints(months)} μήνες → ${formatPoints(points)} μόρια`);

      if (enteredMonths > 10) {
        warnings.push(`${year}: δηλώθηκαν ${formatPoints(enteredMonths)} μήνες. Υπολογίστηκαν έως 10 μήνες / 15 μόρια για το σχολικό έτος.`);
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
        const academic = AsepPeAcademic.calculate("asepPeAcademic");
        warnings.push(...academic.warnings);
        const academicProofWarning = AsepPeAcademic.trainingWarning("asepPeAcademic");
        if (academicProofWarning) warnings.push(academicProofWarning);
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
      const academicCheck = AsepPeAcademic.validate("asepPeAcademic");
      if (!academicCheck.valid) { clearLiveResult(); return; }
    }

    isLiveCalculation = true;
    try { calculatePoints(); } finally { isLiveCalculation = false; }
  }

  function resetForm() {
    document.getElementById("specialty").value = "";
    document.querySelector('input[name="academicMode"][value="detailed"]').checked = true;
    document.querySelector('input[name="academicMode"][value="detailed"]').disabled = false;
    document.getElementById("manualAcademicPoints").value = "";
    AsepPeAcademic.reset("asepPeAcademic", { silent: true });
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
  AsepPeAcademic.sync("asepPeAcademic");

  document.addEventListener("input", event => {
    if (event.target && event.target.classList && event.target.classList.contains("service-months") && event.target.value !== "") {
      const parsed = greekNumber(event.target.value);
      if (Number.isFinite(parsed)) {
        if (parsed < 0) event.target.value = "0";
        if (parsed > 10) event.target.value = "10";
      }
    }
    if (event.target && event.target.matches("input, select")) liveCalculatePoints();
  });
  document.addEventListener("change", event => {
    if (event.target && event.target.matches("input, select")) liveCalculatePoints();
  });
</script>


<?php sourceCardStart(); ?>
  <p>Η κατάταξη στα ΔΗΜ.Ω.Σ. συνδυάζει τη μοριοδότηση των ακαδημαϊκών προσόντων όπως έχει διαμορφωθεί στον αντίστοιχο πίνακα Α.Σ.Ε.Π. με την αναγνωρισμένη προϋπηρεσία σε Πρότυπα ή Πειραματικά Σχολεία.</p>
  <p><strong>Φετινές προσκλήσεις πρόσληψης 2026–2027:</strong></p>
  <?php sourceCardLinksStart(); ?><?php sourceCardLink('https://diavgeia.gov.gr/doc/%CE%957%CE%98%CE%9146%CE%9D%CE%9A%CE%A0%CE%94-%CE%A1%CE%9C%CE%98?inline=true', '14/08/2026 — Γενική πρόσκληση ΔΗΜ.Ω.Σ. για αναπληρωτές — ΑΔΑ Ε7ΘΑ46ΝΚΠΔ-ΡΜΘ ↗'); ?><?php sourceCardLink('https://diavgeia.gov.gr/doc/%CE%A1%CE%A4%CE%91%CE%A746%CE%9D%CE%9A%CE%A0%CE%94-%CE%932%CE%97?inline=true', '20/08/2026 — Ειδική πρόσκληση ΕΑΕ για Τμήματα Ένταξης ΔΗΜ.Ω.Σ. — ΑΔΑ ΡΤΑΧ46ΝΚΠΔ-Γ2Η ↗'); ?><?php sourceCardLinksEnd(); ?>
  <?php sourceCardLinksStart(); ?><?php sourceCardLink('https://www.minedu.gov.gr/news?catid=1183&id=63940%3A30-01-26-prokiryksi-diadikasias-katataksis-ekpaideftikon-vvathmias-ekpaidefsis-me-seira-proteraiotitas-kata-klado-kai-eidikotita-ypopsifion-gia-tin-plirosi-kenon-theseon-thiteias-sta-dimosia-onaseia-sxoleia&view=article', 'Προκήρυξη διαδικασίας κατάταξης ΔΗΜ.Ω.Σ. — ΥΠΑΙΘΑ ↗'); ?><?php sourceCardLink('https://info.asep.gr/node/78737', '1ΓΕ/2026 & 2ΓΕ/2026 — ΑΣΕΠ ↗'); ?><?php sourceCardLinksEnd(); ?>
<?php sourceCardEnd(); ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
  <script src="<?php echo htmlspecialchars(edu_asset_url('assets/common.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
</body>
</html>
