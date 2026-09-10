/*
 * Browser UI controller for ypologismos-morion.php (1ΓΕ/2026 & 2ΓΕ/2026).
 * Shared scoring components remain separate; this file coordinates live
 * validation, result rendering, copy/reset actions and sidebar updates.
 */
(function(){
  'use strict';
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
    btn.textContent = "Αντιγράφηκε ✓";
    setTimeout(() => { btn.textContent = old; }, 1400);
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

  function resetCalculator() {
    document.getElementById("specialty").value = "";
    AsepPeAcademic.reset("asepPeAcademic", { silent: true });

    document.querySelectorAll('input[type="number"]').forEach(el => {
      if (el.id !== "degreeGrade") el.value = 0;
    });
    AsepServiceController.reset('asepService', { silent: true });
    document.querySelectorAll('input[type="checkbox"]').forEach(el => el.checked = false);
    AsepPedagogicalProof.reset("pedagogical");
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
    const socialTotal = socialResult.points;
    const socialDetails = AsepSocialCriteria.details(socialResult, formatPoints);
    warnings.push(...socialResult.warnings);

    const academicProofWarning = AsepPeAcademic.trainingWarning("asepPeAcademic");
    if (academicProofWarning) warnings.push(academicProofWarning);

    const pedagogical = document.getElementById("pedagogical").checked;
    const pedagogicalBox = document.getElementById("pedagogicalPriorityBox");
    pedagogicalBox.className = "result-message edu-message " + (pedagogical ? "result-message--success edu-message--success" : "result-message--status edu-message--status");
    pedagogicalBox.textContent = pedagogical ? "ΠΡΟΤΑΞΗ λόγω Παιδαγωγικής & Διδακτικής Επάρκειας" : "Χωρίς δηλωμένη πρόταξη Π.Δ.Ε.";

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
      "Βαθμός βασικού τίτλου: " + formatPoints(degreeGrade),
      AsepPedagogicalProof.summary("pedagogical")
    ];
    const digitalTutoringSummary = AsepDigitalTutoring.summary('digitalTutoring', formatPoints);
    if (digitalTutoringSummary) summaryLines.push(digitalTutoringSummary);
    const academicProofSummary = AsepPeAcademic.trainingSummary("asepPeAcademic");
    if (academicProofSummary) summaryLines.push(academicProofSummary);
    lastResultText = summaryLines.join("\n");
    document.getElementById("copyResultBtn").disabled = false;

    showResult(html);
  }

  document.getElementById('copyResultBtn').addEventListener('click', copyResult);
  document.getElementById('resetCalculatorBtn').addEventListener('click', resetCalculator);

  document.querySelectorAll('.layout input, .layout select').forEach(el => {
    el.addEventListener('input', liveCalculatePoints);
    el.addEventListener('change', liveCalculatePoints);
  });
  document.addEventListener('asep-digital-tutoring-change', liveCalculatePoints);
  AsepPeAcademic.sync("asepPeAcademic");
}());
