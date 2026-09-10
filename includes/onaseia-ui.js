/*
 * Browser UI controller for ypologismos-morion-onaseia.php.
 * Academic and validation rules stay in the dedicated calculation modules;
 * this file owns DOM rendering, dynamic service rows and event wiring.
 */
(function(global){
  "use strict";
  let initialized = false;
  let isLiveCalculation = false;
  const MIN_SPECIAL_SERVICE_YEAR = 2020;
  const schoolYears = [];
  for (let y = 2025; y >= MIN_SPECIAL_SERVICE_YEAR; y--) {
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

  function formatPointsFixed(value) {
    const rounded = Math.round((Number(value) + Number.EPSILON) * 100) / 100;
    return rounded.toLocaleString("el-GR", {
      minimumFractionDigits: 2,
      maximumFractionDigits: 2
    });
  }

  function updateSidebarSummary(total = 0, academic = 0, service = 0, status = "") {
    document.getElementById("grandTotal").textContent = formatPointsFixed(total);
    document.getElementById("resAcademic").textContent = formatPointsFixed(academic);
    document.getElementById("resService").textContent = formatPointsFixed(service);
    const statusBox = document.getElementById("sidebarStatus");
    if (statusBox) {
      statusBox.className = "result-message edu-message result-message--status edu-message--status";
      statusBox.textContent = status || "Επίλεξε κλάδο και συμπλήρωσε τα απαιτούμενα στοιχεία.";
    }
  }

  function setSidebarError(message) {
    updateSidebarSummary();
    const statusBox = document.getElementById("sidebarStatus");
    if (!statusBox) return;
    statusBox.className = "result-message edu-message result-message--warning edu-message--warning";
    statusBox.textContent = message;
  }

  function showError(message) {
    setSidebarError(message);
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

  function escapeHtml(value) {
    return String(value ?? "")
      .replace(/&/g, "&amp;")
      .replace(/</g, "&lt;")
      .replace(/>/g, "&gt;")
      .replace(/"/g, "&quot;")
      .replace(/'/g, "&#039;");
  }

  function vacancyRows(items) {
    if (!items.length) return '<p class="note">Δεν καταγράφονται θέσεις στην απόφαση.</p>';
    const rows = items.map(item => `
      <tr>
        <td>${escapeHtml(item.label)}<br><span class="note">Κωδ. ${escapeHtml(item.code)}</span></td>
        <td>${item.positions}</td>
      </tr>`).join("");
    return `<table class="breakdown">
      <tr><th>Σχολική μονάδα</th><th>Θέσεις</th></tr>${rows}
    </table>`;
  }

  function updateVacancyPanel() {
    const specialty = valueOf("specialty");
    const pe04Wrap = document.getElementById("pe04VacancySpecialtyWrap");
    const pe04Select = document.getElementById("pe04VacancySpecialty");
    const panel = document.getElementById("vacancyPanel");
    const needsPe04 = specialty === "ΠΕ04";
    pe04Wrap.classList.toggle("hidden", !needsPe04);
    if (!needsPe04) pe04Select.value = "";

    if (!specialty) {
      panel.classList.add("hidden");
      panel.innerHTML = "";
      return;
    }

    const data = OnaseiaVacancies2026.selection(specialty, pe04Select.value);
    const parts = [
      `<strong>Φετινές λειτουργικές ανάγκες ΔΗΜ.Ω.Σ. 2026-2027</strong>`,
      `<p class="note">Αποφάσεις ${escapeHtml(data.generalDecision)} και ${escapeHtml(data.eaeDecision)}, ${escapeHtml(data.sourceDate)} · προσωρινοί αναπληρωτές πλήρους ωραρίου.</p>`
    ];

    if (data.needsPe04Specialty) {
      parts.push('<p><strong>Γενική Εκπαίδευση:</strong> επίλεξε ειδικότητα ΠΕ04 για να εμφανιστούν οι αντίστοιχες θέσεις.</p>');
    } else {
      const generalLabel = data.te16UsesPe79 ? 'Θέσεις μουσικής ΠΕ79.01' : `Γενική Εκπαίδευση — ${escapeHtml(data.generalCode)}`;
      parts.push(`<details open><summary><strong>${generalLabel}: ${data.generalTotal} ${data.generalTotal === 1 ? "θέση" : "θέσεις"}</strong></summary>${vacancyRows(data.general)}</details>`);
    }

    if (data.eaeCode) {
      parts.push(`<details open><summary><strong>ΕΑΕ — ${escapeHtml(data.eaeCode)}: ${data.eaeTotal} ${data.eaeTotal === 1 ? "θέση" : "θέσεις"}</strong></summary>${vacancyRows(data.eae)}</details>`);
      parts.push('<p class="note">Οι θέσεις ΕΑΕ αφορούν υποψηφίους με εξειδίκευση στην Ε.Α.Ε. της 3ΕΑ/2025.</p>');
    }

    if (data.te16UsesPe79) {
      parts.push('<p class="note"><strong>ΤΕ16:</strong> η απόφαση λειτουργικών αναγκών καταγράφει τη θέση στον ΠΕ79.01. Στη διαδικασία πρόσληψης τηρείται απόλυτη πρόταξη των ΠΕ79 έναντι των ΤΕ16.</p>');
    }

    parts.push('<p class="note">Οι αριθμοί αποτυπώνουν τις λειτουργικές ανάγκες που προσδιορίστηκαν στις 26/08/2026 και μπορεί να μεταβληθούν με νεότερη απόφαση ή μετά από τοποθετήσεις.</p>');
    panel.innerHTML = parts.join("");
    panel.classList.remove("hidden");
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
    updateVacancyPanel();
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
      <button type="button" class="remove-row" aria-label="Αφαίρεση γραμμής προϋπηρεσίας">Αφαίρεση</button>
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

      if (year) {
        const startYear = Number(String(year).slice(0, 4));
        if (!Number.isInteger(startYear) || startYear < MIN_SPECIAL_SERVICE_YEAR) {
          throw new Error("Η προϋπηρεσία σε Πρότυπα/Πειραματικά για τον υπολογισμό ΔΗΜ.Ω.Σ. καταχωρίζεται από το σχολικό έτος 2020-2021 και μετά.");
        }
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

      updateSidebarSummary(
        total,
        academicPoints,
        service.points,
        `Κλάδος ${specialty} · ${currentAcademicMode() === "manual" ? "χειροκίνητη" : "αναλυτική"} καταχώριση ακαδημαϊκών`
      );

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
    updateSidebarSummary();
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
    document.getElementById("pe04VacancySpecialty").value = "";
    document.querySelector('input[name="academicMode"][value="detailed"]').checked = true;
    document.querySelector('input[name="academicMode"][value="detailed"]').disabled = false;
    document.getElementById("manualAcademicPoints").value = "";
    AsepPeAcademic.reset("asepPeAcademic", { silent: true });
    document.getElementById("serviceRows").innerHTML = "";
    addServiceRow("2025-2026");
    addServiceRow("2024-2025");
    addServiceRow("2023-2024");
    document.getElementById("result").style.display = "none";
    updateSidebarSummary();
    updateAcademicMode();
  }

  function handleDocumentInput(event) {
    if (event.target && event.target.classList && event.target.classList.contains("service-months") && event.target.value !== "") {
      const parsed = greekNumber(event.target.value);
      if (Number.isFinite(parsed)) {
        if (parsed < 0) event.target.value = "0";
        if (parsed > 10) event.target.value = "10";
      }
    }
    if (event.target && event.target.matches("input, select")) liveCalculatePoints();
  }

  function handleDocumentChange(event) {
    if (event.target && event.target.matches("input, select")) liveCalculatePoints();
  }

  function handleServiceRowClick(event) {
    const button = event.target && event.target.closest ? event.target.closest(".remove-row") : null;
    if (!button) return;
    const row = button.closest(".service-row");
    if (row) row.remove();
    liveCalculatePoints();
  }

  function init() {
    if (initialized) return;
    initialized = true;

    document.querySelectorAll('input[name="academicMode"]').forEach(el => {
      el.addEventListener("change", updateAcademicMode);
    });

    document.getElementById("specialty").addEventListener("change", updateAcademicMode);
    document.getElementById("pe04VacancySpecialty").addEventListener("change", updateVacancyPanel);
    document.getElementById("addServiceRowBtn").addEventListener("click", () => addServiceRow());
    document.getElementById("calculateBtn").addEventListener("click", calculatePoints);
    document.getElementById("resetBtn").addEventListener("click", resetForm);
    document.getElementById("serviceRows").addEventListener("click", handleServiceRowClick);

    addServiceRow("2025-2026");
    addServiceRow("2024-2025");
    addServiceRow("2023-2024");
    updateAcademicMode();
    AsepPeAcademic.sync("asepPeAcademic");

    document.addEventListener("input", handleDocumentInput);
    document.addEventListener("change", handleDocumentChange);
  }

  global.OnaseiaUI = Object.freeze({ init });
  if (document.readyState === "loading") document.addEventListener("DOMContentLoaded", init);
  else init();

})(window);
