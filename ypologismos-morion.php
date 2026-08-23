<!DOCTYPE html>
<html lang="el">
<head>
<!-- UI consolidation v3.20: shared design system in assets/common.css -->
<meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Υπολογισμός μορίων 1ΓΕ/2026 &amp; 2ΓΕ/2026</title>
  <link rel="stylesheet" href="assets/common.css?v=3.20.15-rc1">
</head>

<body class="edu-ui edu-calc-standard edu-calc-asep-main">
<?php require_once __DIR__ . '/includes/header.php'; ?>
<?php require_once __DIR__ . '/includes/components/asep-pe-academic.php'; ?>
<?php require_once __DIR__ . '/includes/components/asep-social-criteria.php'; ?>
<?php require_once __DIR__ . '/includes/components/asep-three-month-service.php'; ?>
<?php require_once __DIR__ . '/includes/components/asep-digital-tutoring-service.php'; ?>

<div class="app">
<section class="hero">
    <h1>Υπολογισμός μορίων 1ΓΕ/2026 &amp; 2ΓΕ/2026</h1>
    <p>Ενδεικτικός υπολογισμός για τους αξιολογικούς πίνακες Γενικής Εκπαίδευσης κατηγορίας Π.Ε.</p>
    <div class="meta">
      <span>1ΓΕ/2026</span><span>2ΓΕ/2026</span>
      <span>Ακαδημαϊκά έως 120</span><span>Προϋπηρεσία έως 120</span>
    </div>
  </section>

  <div class="layout">
    <div>
      <section class="card">
        <h2>Κλάδος / ειδικότητα</h2>
        <p class="cap">Ο κλάδος εφαρμόζει τους ειδικούς κανόνες της προκήρυξης, όπως τις εξαιρέσεις ξένων γλωσσών και τη μη μοριοδότηση Η/Υ στον ΠΕ86.</p>

        <div class="field-grid">
          <div class="field">
            <label for="specialty">Κλάδος / ειδικότητα</label>
            <select id="specialty">
              <option value="">— Επιλογή κλάδου —</option>
              <option value="ΠΕ01">ΠΕ01</option><option value="ΠΕ02">ΠΕ02</option><option value="ΠΕ03">ΠΕ03</option>
              <option value="ΠΕ04">ΠΕ04</option><option value="ΠΕ05">ΠΕ05</option><option value="ΠΕ06">ΠΕ06</option>
              <option value="ΠΕ07">ΠΕ07</option><option value="ΠΕ08">ΠΕ08</option><option value="ΠΕ11">ΠΕ11</option>
              <option value="ΠΕ33">ΠΕ33</option><option value="ΠΕ34">ΠΕ34</option><option value="ΠΕ40">ΠΕ40</option>
              <option value="ΠΕ41">ΠΕ41</option><option value="ΠΕ60">ΠΕ60</option><option value="ΠΕ70">ΠΕ70</option>
              <option value="ΠΕ73">ΠΕ73</option><option value="ΠΕ78">ΠΕ78</option><option value="ΠΕ79.01">ΠΕ79.01</option>
              <option value="ΠΕ79.02">ΠΕ79.02</option><option value="ΠΕ80">ΠΕ80</option><option value="ΠΕ81">ΠΕ81</option>
              <option value="ΠΕ82">ΠΕ82</option><option value="ΠΕ83">ΠΕ83</option><option value="ΠΕ84">ΠΕ84</option>
              <option value="ΠΕ85">ΠΕ85</option><option value="ΠΕ86">ΠΕ86</option><option value="ΠΕ87">ΠΕ87</option>
              <option value="ΠΕ88">ΠΕ88</option><option value="ΠΕ89">ΠΕ89</option><option value="ΠΕ90">ΠΕ90</option>
              <option value="ΠΕ91">ΠΕ91</option>
            </select>
          </div>
        </div>
      </section>

      <section class="card">
        <h2>Α. Ακαδημαϊκά προσόντα</h2>
        <p class="cap">Μέγιστο κατηγορίας: 120 μόρια</p>
<?php
renderAsepPeAcademic(array(
    'id' => 'asepPeAcademic',
    'specialty_id' => 'specialty',
    'field_class' => 'field',
    'degree_input_type' => 'number',
    'show_subtotal' => true,
    'subtotal_id' => 'academicSubtotal'
));
?>
      </section>

      <section id="asepService" class="card" data-component="asep-service-criteria" data-subtotal-id="serviceSubtotal" data-subtotal-with-cap="true">
        <h2>Β. Εκπαιδευτική προϋπηρεσία</h2>
        <p class="cap">Μέγιστο κατηγορίας: 120 μόρια</p>

        <div class="note">Βάλε κάθε χρονικό διάστημα σε <strong>ένα μόνο</strong> αντίστοιχο πεδίο, ώστε να μη γίνει διπλή μέτρηση.</div>

        <div class="field-grid">
          <div class="field"><label for="normalMonths">Δημόσια εκπαιδευτική προϋπηρεσία<small>1 μόριο/μήνα · έως 120 μήνες</small></label><input type="number" id="normalMonths" data-service-role="regular" min="0" max="120" step="1" value="0" inputmode="numeric" oninput="limitIntegerMonth(this)"></div>
          <div class="field"><label for="difficultMonths">Δυσπρόσιτα / καταστήματα κράτησης από 2020–2021<small>2 μόρια/μήνα · έως 60 μήνες</small></label><input type="number" id="difficultMonths" data-service-role="difficult" min="0" max="60" step="1" value="0" inputmode="numeric" oninput="limitIntegerMonth(this)"></div>
        </div>

<?php
renderAsepThreeMonthService(array(
    'regular_2020_id' => 'threeMonthMonths2020',
    'difficult_2020_id' => 'threeMonthDifficultMonths2020',
    'regular_2021_id' => 'threeMonthMonths2021',
    'difficult_2021_id' => 'threeMonthDifficultMonths2021'
));
?>

        <div class="field-grid">
          <div class="field"><label for="privateMonths">Ιδιωτική εκπαίδευση<small>0,9 μόρια/μήνα</small></label><input type="number" id="privateMonths" data-service-role="private" min="0" step="1" value="0" inputmode="numeric" oninput="limitIntegerMonth(this)"></div>
        </div>

<?php renderAsepDigitalTutoringService(array('container_id' => 'digitalTutoring', 'input_class' => 'service-months')); ?>
        <div class="subtot"><span>Σύνολο Προϋπηρεσίας</span><span class="pill" id="serviceSubtotal">0,00 / 120</span></div>
      </section>

<?php
renderAsepSocialCriteria(array(
    'title' => 'Γ. Κοινωνικά κριτήρια',
    'children_id' => 'children',
    'candidate_id' => 'candidateDisability',
    'spouse_id' => 'spouseDisability',
    'child_id' => 'childDisability',
    'marriage_id' => 'marriageYears4Plus',
    'mental_id' => 'candidateMentalCondition',
    'input_step' => '1',
    'child_points' => 3,
    'min_disability_percent' => 50,
    'disability_rate' => '0,4',
    'spouse_min_marriage_years' => 4,
    'child_extra_note' => 'Ανεξαρτήτως ηλικίας.',
    'child_auxiliary_note' => '',
    'warning_id' => '',
    'subtotal_id' => 'socialSubtotal',
    'subtotal_label' => 'Σύνολο Κοινωνικών'
));
?>

      <p class="small-note">Το αποτέλεσμα είναι ενδεικτικό και δεν αντικαθιστά την επίσημη προκήρυξη, τον έλεγχο του Α.Σ.Ε.Π., τον Ο.Π.ΣΥ.Δ. ή τον επίσημο πίνακα κατάταξης.</p>
    </div>

    <aside class="card results" aria-live="polite">
      <div class="total">
        <div class="num" id="grandTotal">0,00</div>
        <div class="label">συνολικά μόρια</div>
      </div>

      <div class="result-row"><span>Ακαδημαϊκά</span><strong id="resAcademic">0,00 / 120</strong></div>
      <div class="result-row"><span>Προϋπηρεσία</span><strong id="resService">0,00 / 120</strong></div>
      <div class="result-row"><span>Κοινωνικά</span><strong id="resSocial">0,00</strong></div>
      <div class="result-row"><span>Βαθμός τίτλου</span><strong id="resDegree">0,00</strong></div>

      <div class="priority" id="sidebarStatus">Συμπλήρωσε κλάδο και βαθμό τίτλου· στη συνέχεια τα μόρια ενημερώνονται αυτόματα.</div>

      <button type="button" class="calculate-primary" onclick="calculatePoints()">Υπολογισμός μορίων</button>
      <div class="actions">
        <button type="button" id="copyResultBtn" onclick="copyResult()" disabled>Αντιγραφή αποτελέσματος</button>
        <button type="button" class="secondary" onclick="resetCalculator()">Μηδενισμός</button>
      </div>

      <div class="info-note edu-mt-14">Η τελική σειρά κατάταξης εξαρτάται από τους κανόνες της αντίστοιχης προκήρυξης και τον επίσημο έλεγχο των δικαιολογητικών.</div>
    </aside>
  </div>

  <div id="result" class="result" role="status" aria-live="polite"></div>

  <section class="edu-source-card" aria-labelledby="sourcesTitle">
    <h2 id="sourcesTitle">Πηγές / Νομική βάση</h2>
    <p>Προκηρύξεις Α.Σ.Ε.Π. <strong>1ΓΕ/2026</strong> (ΦΕΚ 21/29.04.2026) και <strong>2ΓΕ/2026</strong> (ΦΕΚ 22/29.04.2026), ιδίως το Κεφάλαιο Γ΄ «Κριτήρια Κατάταξης».</p>
    <div class="source-links"><a href="https://info.asep.gr/node/78700" target="_blank" rel="noopener noreferrer">1ΓΕ/2026 — ΑΣΕΠ ↗</a> <a href="https://info.asep.gr/node/78701" target="_blank" rel="noopener noreferrer">2ΓΕ/2026 — ΑΣΕΠ ↗</a></div>
    <p class="source-disclaimer">Το εργαλείο είναι ενημερωτικό. Η τελική μοριοδότηση προκύπτει από τον επίσημο έλεγχο της αίτησης και των δικαιολογητικών.</p>
  </section>
</div>

<script src="includes/academic-calculations.js?v=3.20.24"></script>
<script src="includes/language-calculations.js?v=3.20.24"></script>
<script src="includes/asep-language-selector.js?v=3.20.24"></script>
<script src="includes/asep-computer-proof.js?v=3.20.25"></script>
<script src="includes/training-proof.js?v=3.20.25"></script>
<script src="includes/asep-pe-academic.js?v=3.20.25"></script>
<script src="includes/service-calculations.js?v=3.20.26"></script>
<script src="includes/asep-service-controller.js?v=3.20.26"></script>
<script src="includes/asep-digital-tutoring.js?v=3.20.22"></script>
<script src="includes/social-calculations.js?v=3.20.26"></script>
<script src="includes/asep-social-criteria.js?v=3.20.26"></script>
<script>
  let lastResultText = "";

  function valueOf(id) {
    return document.getElementById(id).value;
  }

  function updateSidebarSummary({ total = 0, academic = 0, service = 0, social = 0, degree = 0, specialty = "" } = {}) {
    document.getElementById("grandTotal").textContent = formatPointsFixed(total);
    document.getElementById("resAcademic").textContent = formatPointsFixed(academic) + " / 120";
    document.getElementById("resService").textContent = formatPointsFixed(service) + " / 120";
    document.getElementById("resSocial").textContent = formatPointsFixed(social);
    document.getElementById("resDegree").textContent = formatPointsFixed(degree);

    const academicSubtotal = document.getElementById("academicSubtotal");
    const serviceSubtotal = document.getElementById("serviceSubtotal");
    const socialSubtotal = document.getElementById("socialSubtotal");
    if (academicSubtotal) academicSubtotal.textContent = formatPointsFixed(academic) + " / 120";
    if (serviceSubtotal) serviceSubtotal.textContent = formatPointsFixed(service) + " / 120";
    if (socialSubtotal) socialSubtotal.textContent = formatPointsFixed(social);

    const status = document.getElementById("sidebarStatus");
    status.textContent = specialty
      ? "Τελευταίος υπολογισμός για " + specialty + ". Δες την αναλυτική κατανομή κάτω από τη φόρμα."
      : "Συμπλήρωσε κλάδο και βαθμό τίτλου· στη συνέχεια τα μόρια ενημερώνονται αυτόματα.";
  }

  async function copyResult() {
    if (!lastResultText) return;
    const btn = document.getElementById("copyResultBtn");
    try {
      await navigator.clipboard.writeText(lastResultText);
    } catch (error) {
      const textarea = document.createElement("textarea");
      textarea.value = lastResultText;
      textarea.style.position = "fixed";
      textarea.style.opacity = "0";
      document.body.appendChild(textarea);
      textarea.select();
      document.execCommand("copy");
      textarea.remove();
    }
    const old = btn.textContent;
    btn.textContent = "Αντιγράφηκε";
    setTimeout(() => { btn.textContent = old; }, 1400);
  }

  function limitIntegerMonth(input) {
    let value = String(input.value).replace(/[^0-9]/g, "");
    if (value === "") {
      input.value = "";
      return;
    }
    let parsed = Math.max(0, parseInt(value, 10));
    const max = input.getAttribute("max");
    if (max !== null && max !== "") parsed = Math.min(parsed, Number(max));
    input.value = parsed;
  }

  function numberOf(id) {
    const value = parseFloat(valueOf(id));
    return isNaN(value) ? 0 : value;
  }

  function formatPoints(value) {
    const rounded = Math.round(value * 100) / 100;
    if (Number.isInteger(rounded)) {
      return rounded.toString();
    }
    return rounded.toFixed(2).replace(".", ",");
  }

  function formatPointsFixed(value) {
    return (Math.round((Number(value) + Number.EPSILON) * 100) / 100)
      .toLocaleString("el-GR", { minimumFractionDigits: 2, maximumFractionDigits: 2 });
  }

  function showError(message) {
    const result = document.getElementById("result");
    result.style.display = "block";
    result.className = "result error";
    result.innerHTML = message;

    const status = document.getElementById("sidebarStatus");
    if (status) status.textContent = "Δεν έγινε νέος υπολογισμός: " + message;
  }

  function showResult(html) {
    const result = document.getElementById("result");
    result.style.display = "block";
    result.className = "result";
    result.innerHTML = html;
  }

  function clearLiveCalculationState(message) {
    const result = document.getElementById("result");
    result.style.display = "none";
    result.innerHTML = "";
    result.className = "result";
    lastResultText = "";
    document.getElementById("copyResultBtn").disabled = true;
    updateSidebarSummary();
    const status = document.getElementById("sidebarStatus");
    if (status && message) status.textContent = message;
  }

  function liveCalculatePoints() {
    AsepPeAcademic.sync("asepPeAcademic");
    const academicCheck = AsepPeAcademic.validate("asepPeAcademic");
    if (!academicCheck.valid) {
      clearLiveCalculationState("Συμπλήρωσε κλάδο και έγκυρο βαθμό τίτλου (5–10) για live υπολογισμό.");
      return;
    }
    calculatePoints();
  }

  const childrenInput = document.getElementById("children");
  if (childrenInput) {
    childrenInput.addEventListener("input", function () {
      if (this.value === "") return;
      this.value = Math.max(0, Math.floor(Number(this.value) || 0));
    });
  }

  function resetCalculator() {
    document.getElementById("specialty").value = "";
    AsepPeAcademic.reset("asepPeAcademic", { silent: true });

    document.querySelectorAll('input[type="number"]').forEach(el => {
      if (el.id !== "degreeGrade") el.value = 0;
    });
    AsepServiceController.reset('asepService', { silent: true });
    document.querySelectorAll('input[type="checkbox"]').forEach(el => el.checked = false);
    AsepPeAcademic.sync("asepPeAcademic");

    const result = document.getElementById("result");
    result.style.display = "none";
    result.innerHTML = "";
    result.className = "result";

    lastResultText = "";
    document.getElementById("copyResultBtn").disabled = true;
    updateSidebarSummary();
    document.getElementById("specialty").focus();
  }

  function calculatePoints() {
    const warnings = [];
    const specialty = valueOf("specialty");

    if (!specialty) {
      showError("Παρακαλώ επίλεξε κλάδο / ειδικότητα.");
      return;
    }

    const degreeGrade = numberOf("degreeGrade");

    if (!degreeGrade || degreeGrade < 5 || degreeGrade > 10) {
      showError("Παρακαλώ συμπλήρωσε έγκυρο βαθμό βασικού τίτλου σπουδών, από 5 έως 10.");
      return;
    }

    let academic;
    try {
      academic = AsepPeAcademic.calculate("asepPeAcademic");
    } catch (error) {
      showError(error.message);
      return;
    }

    warnings.push(...academic.warnings);

    const service = AsepServiceController.getState("asepService", formatPoints);
    const serviceTotal = service.points;
    const serviceDetails = AsepServiceController.details(service, formatPoints);
    warnings.push(...service.warnings);

    const socialResult = AsepSocialCriteria.getState("socialCriteria", formatPoints);
    const socialTotal = socialResult.total;
    const socialDetails = AsepSocialCriteria.details(socialResult, formatPoints);
    warnings.push(...socialResult.warnings);

    const academicProofWarning = AsepPeAcademic.trainingWarning("asepPeAcademic");
    if (academicProofWarning) warnings.push(academicProofWarning);

    const total =
      academic.points +
      serviceTotal +
      socialTotal;

    function detailText(items) {
      return items.length ? items.join("<br>") : "—";
    }

    let html = `
      <h2>Σύνολο μορίων: ${formatPoints(total)}</h2>

      <table class="breakdown">
        <tr>
          <th>Κατηγορία</th>
          <th>Μόρια</th>
          <th>Ανάλυση</th>
        </tr>

        <tr>
          <td>Τίτλοι σπουδών</td>
          <td>${formatPoints(academic.corePoints)}</td>
          <td>${detailText(academic.coreDetails)}</td>
        </tr>

        <tr>
          <td>Ξένες γλώσσες</td>
          <td>${formatPoints(academic.languagePoints)}</td>
          <td>${detailText(academic.languageDetails)}</td>
        </tr>

        <tr>
          <td>Γνώση Η/Υ</td>
          <td>${formatPoints(academic.computerPoints)}</td>
          <td>${detailText(academic.computerDetails)}</td>
        </tr>

        <tr>
          <td>Επιμόρφωση</td>
          <td>${formatPoints(academic.trainingPoints)}</td>
          <td>${detailText(academic.trainingDetails)}</td>
        </tr>

        <tr>
          <td><strong>Σύνολο ακαδημαϊκών κριτηρίων</strong></td>
          <td><strong>${formatPoints(academic.points)}</strong></td>
          <td>${academic.rawPoints > academic.points ? "Εφαρμόστηκε το ανώτατο όριο των 120 μορίων." : "—"}</td>
        </tr>

        <tr>
          <td>Εκπαιδευτική προϋπηρεσία</td>
          <td>${formatPoints(serviceTotal)}</td>
          <td>${detailText(serviceDetails)}</td>
        </tr>

        <tr>
          <td>Κοινωνικά κριτήρια</td>
          <td>${formatPoints(socialTotal)}</td>
          <td>${detailText(socialDetails)}</td>
        </tr>

        <tr class="total-row">
          <td>Σύνολο</td>
          <td>${formatPoints(total)}</td>
          <td>Ενδεικτικός υπολογισμός</td>
        </tr>
      </table>
    `;

    if (warnings.length > 0) {
      html += `
        <div class="warning">
          Προσοχή:<br>
          ${warnings.map(w => "• " + w).join("<br>")}
        </div>
      `;
    }

    updateSidebarSummary({
      total: total,
      academic: academic.points,
      service: serviceTotal,
      social: socialTotal,
      degree: academic.degreePoints || (degreeGrade * 2.5),
      specialty: specialty
    });

    const summaryLines = [
      "Υπολογισμός μορίων 1ΓΕ/2026 & 2ΓΕ/2026",
      "Κλάδος / ειδικότητα: " + specialty,
      "Συνολικά μόρια: " + formatPoints(total),
      "Ακαδημαϊκά: " + formatPoints(academic.points) + " / 120",
      "Προϋπηρεσία: " + formatPoints(serviceTotal) + " / 120",
      "Κοινωνικά: " + formatPoints(socialTotal),
      "Βαθμός βασικού τίτλου: " + formatPoints(degreeGrade)
    ];
    const digitalTutoringSummary = AsepDigitalTutoring.summary('digitalTutoring', formatPoints);
    if (digitalTutoringSummary) summaryLines.push(digitalTutoringSummary);
    const academicProofSummary = AsepPeAcademic.trainingSummary("asepPeAcademic");
    if (academicProofSummary) summaryLines.push(academicProofSummary);
    lastResultText = summaryLines.join("\n");
    document.getElementById("copyResultBtn").disabled = false;

    showResult(html);
  }

  document.querySelectorAll('.layout input, .layout select').forEach(el => {
    el.addEventListener('input', liveCalculatePoints);
    el.addEventListener('change', liveCalculatePoints);
  });
  document.addEventListener('asep-digital-tutoring-change', liveCalculatePoints);
  AsepPeAcademic.sync("asepPeAcademic");
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
  <script src="assets/common.js?v=3.20.13"></script>
</body>
</html>
