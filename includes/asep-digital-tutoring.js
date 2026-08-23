(function (global) {
  "use strict";

  function rootOf(ref) {
    if (!ref) return null;
    if (typeof ref === "string") return document.getElementById(ref);
    return ref;
  }

  function rules() {
    if (!global.EducationService || !global.EducationService.RULES) return {};
    return global.EducationService.RULES.digitalSchoolYears || {};
  }

  function schoolYears() {
    return Object.keys(rules());
  }

  function integer(value) {
    var n = Math.floor(Number(value) || 0);
    return Math.max(0, n);
  }

  function formatDefault(value) {
    var rounded = Math.round((Number(value) || 0) * 100) / 100;
    return Number.isInteger(rounded) ? String(rounded) : rounded.toFixed(2).replace(".", ",");
  }

  function selectedYears(root, exceptSelect) {
    var used = {};
    root.querySelectorAll("[data-digital-year]").forEach(function (select) {
      if (select !== exceptSelect && select.value) used[select.value] = true;
    });
    return used;
  }

  function syncYearOptions(root) {
    var selects = root.querySelectorAll("[data-digital-year]");
    selects.forEach(function (select) {
      var used = selectedYears(root, select);
      Array.prototype.forEach.call(select.options, function (option) {
        if (!option.value) return;
        option.disabled = !!used[option.value];
      });
    });

    var add = root.querySelector("[data-digital-tutoring-add]");
    if (add) add.disabled = selects.length >= schoolYears().length;
  }

  function rowLimit(year) {
    return rules()[year] || null;
  }

  function syncRow(row) {
    var select = row.querySelector("[data-digital-year]");
    var monthsInput = row.querySelector("[data-digital-months]");
    var daysInput = row.querySelector("[data-digital-days]");
    var monthsNote = row.querySelector("[data-digital-months-note]");
    var daysNote = row.querySelector("[data-digital-days-note]");
    if (!select || !monthsInput || !daysInput) return;

    var limit = rowLimit(select.value);
    if (!limit) {
      monthsInput.max = "0";
      daysInput.max = "0";
      monthsInput.value = "0";
      daysInput.value = "0";
      if (monthsNote) monthsNote.textContent = "Επίλεξε σχολικό έτος";
      if (daysNote) daysNote.textContent = "Επίλεξε σχολικό έτος";
      return;
    }

    var months = Math.min(integer(monthsInput.value), limit.maxMonths);
    monthsInput.max = String(limit.maxMonths);
    monthsInput.value = String(months);

    var maxDays = months >= limit.maxMonths ? limit.maxDaysAtMaxMonths : 29;
    var days = Math.min(integer(daysInput.value), maxDays);
    daysInput.max = String(maxDays);
    daysInput.value = String(days);

    if (monthsNote) {
      monthsNote.textContent = "Μέγιστη διάρκεια έτους: " + limit.maxMonths + " μήνες και " + limit.maxDaysAtMaxMonths + " ημέρες";
    }
    if (daysNote) {
      daysNote.textContent = months >= limit.maxMonths
        ? "Έως " + limit.maxDaysAtMaxMonths + " ημέρες όταν δηλωθούν " + limit.maxMonths + " μήνες"
        : "Έως 29 ημέρες πριν συμπληρωθεί επόμενος πλήρης μήνας";
    }
  }

  function ordinal(index) {
    return index === 0 ? "1ο" : (index === 1 ? "2ο" : String(index + 1) + "ο");
  }

  function updateTitles(root) {
    root.querySelectorAll("[data-digital-tutoring-row]").forEach(function (row, index) {
      var title = row.querySelector("[data-digital-row-title]");
      if (title) title.textContent = ordinal(index) + " σχολικό έτος";
      var remove = row.querySelector("[data-digital-remove]");
      if (remove) remove.classList.toggle("hidden", index === 0 && root.querySelectorAll("[data-digital-tutoring-row]").length === 1);
    });
  }

  function makeRow(root, preferredYear) {
    var row = document.createElement("div");
    row.className = "asep-digital-tutoring-row";
    row.setAttribute("data-digital-tutoring-row", "");

    var inputClass = root.getAttribute("data-input-class") || "service-months";
    var years = schoolYears();
    var used = selectedYears(root, null);
    var firstAvailable = preferredYear || years.find(function (year) { return !used[year]; }) || "";

    var options = '<option value="">— Επιλογή σχολικού έτους —</option>';
    years.forEach(function (year) {
      var label = year.replace("-", "–");
      options += '<option value="' + year + '"' + (year === firstAvailable ? ' selected' : '') + '>' + label + '</option>';
    });

    row.innerHTML =
      '<h4 data-digital-row-title></h4>' +
      '<div class="field-grid">' +
        '<div class="field">' +
          '<label>Σχολικό έτος</label>' +
          '<select data-digital-year>' + options + '</select>' +
        '</div>' +
        '<div class="field">' +
          '<label>Πλήρεις μήνες<small data-digital-months-note></small></label>' +
          '<input class="' + inputClass + '" data-digital-months type="number" min="0" step="1" inputmode="numeric" value="0">' +
        '</div>' +
        '<div class="field">' +
          '<label>Υπόλοιπο ημερών<small data-digital-days-note></small></label>' +
          '<input data-digital-days type="number" min="0" step="1" inputmode="numeric" value="0">' +
        '</div>' +
      '</div>' +
      '<div class="actions"><button type="button" class="secondary" data-digital-remove>Αφαίρεση σχολικού έτους</button></div>';

    root.querySelector("[data-digital-tutoring-rows]").appendChild(row);
    syncRow(row);
    updateTitles(root);
    syncYearOptions(root);
    return row;
  }

  function emit(root) {
    var event;
    try {
      event = new CustomEvent("asep-digital-tutoring-change", { bubbles: true });
    } catch (e) {
      event = document.createEvent("CustomEvent");
      event.initCustomEvent("asep-digital-tutoring-change", true, false, null);
    }
    root.dispatchEvent(event);
  }

  function readEntries(ref) {
    var root = rootOf(ref);
    if (!root) return [];
    var entries = [];
    root.querySelectorAll("[data-digital-tutoring-row]").forEach(function (row) {
      var year = row.querySelector("[data-digital-year]");
      var months = row.querySelector("[data-digital-months]");
      var days = row.querySelector("[data-digital-days]");
      if (!year || !year.value) return;
      entries.push({
        schoolYear: year.value,
        months: integer(months ? months.value : 0),
        days: integer(days ? days.value : 0)
      });
    });
    return entries;
  }

  function calculate(ref) {
    if (!global.EducationService || typeof global.EducationService.digitalTutoring !== "function") {
      return {
        entries: [], activeYears: [], fullMonths: 0, totalDays: 0,
        convertedMonths: 0, extraMonths: 0, remainingDays: 0,
        basePoints: 0, convertedRawPoints: 0, convertedPoints: 0,
        rawPoints: 0, points: 0, warnings: []
      };
    }
    return global.EducationService.digitalTutoring(readEntries(ref));
  }

  function details(ref, formatter) {
    var result = calculate(ref);
    var format = typeof formatter === "function" ? formatter : formatDefault;
    var lines = [];

    result.activeYears.forEach(function (year) {
      lines.push(
        "Ψηφιακό Φροντιστήριο — " + year.label + ": " +
        year.months + " " + (year.months === 1 ? "μήνας" : "μήνες") +
        " και " + year.days + " " + (year.days === 1 ? "ημέρα" : "ημέρες") +
        " · " + format(year.basePoints) + " μόρια από τους πλήρεις μήνες"
      );
    });

    if (result.totalDays > 0) {
      var daysLine = "Ψηφιακό Φροντιστήριο — άθροισμα υπολοίπων ημερών: " +
        result.totalDays + " " + (result.totalDays === 1 ? "ημέρα" : "ημέρες") +
        " → " + result.convertedMonths + " " +
        (result.convertedMonths === 1 ? "επιπλέον μήνας" : "επιπλέον μήνες") +
        " = " + format(result.convertedPoints) + " μόρια";
      if (result.remainingDays > 0) {
        daysLine += " · τελικό υπόλοιπο " + result.remainingDays + " " +
          (result.remainingDays === 1 ? "ημέρα" : "ημέρες") + " χωρίς μοριοδότηση";
      }
      lines.push(daysLine);
    }

    return lines;
  }

  function summary(ref, formatter) {
    var result = calculate(ref);
    if (!result.activeYears.length) return "";
    var format = typeof formatter === "function" ? formatter : formatDefault;
    var parts = result.activeYears.map(function (year) {
      return year.label + ": " + year.months + "μ " + year.days + "η";
    });
    var text = "Ψηφιακό Φροντιστήριο: " + parts.join(" · ");
    if (result.totalDays > 0) {
      text += " · ημέρες " + result.totalDays + " → " + result.convertedMonths + " επιπλέον μήνας/μήνες";
      if (result.remainingDays > 0) text += " + " + result.remainingDays + " ημέρες υπόλοιπο";
    }
    text += " · σύνολο " + format(result.points) + " μόρια";
    return text;
  }

  function updateStatus(root) {
    var status = root.querySelector("[data-digital-tutoring-status]");
    if (!status) return;
    var result = calculate(root);
    var parts = [];
    if (result.convertedMonths > 0 || result.remainingDays > 0) {
      parts.push(
        "Υπόλοιπα ημερών: " + result.totalDays + " → " + result.convertedMonths +
        " επιπλέον μήνας/μήνες" +
        (result.remainingDays ? " και " + result.remainingDays + " ημέρες υπόλοιπο" : "") + "."
      );
    }
    if (result.warnings && result.warnings.length) parts = parts.concat(result.warnings);
    status.textContent = parts.join(" ");
    status.classList.toggle("hidden", parts.length === 0);
  }

  function reset(ref, options) {
    var root = rootOf(ref);
    if (!root) return;
    var rows = root.querySelector("[data-digital-tutoring-rows]");
    if (!rows) return;
    rows.innerHTML = "";
    makeRow(root, schoolYears()[0] || "");
    updateStatus(root);
    if (!options || !options.silent) emit(root);
  }

  function initRoot(root) {
    if (!root || root.getAttribute("data-digital-tutoring-ready") === "1") return;
    root.setAttribute("data-digital-tutoring-ready", "1");

    var add = root.querySelector("[data-digital-tutoring-add]");
    if (add) {
      add.addEventListener("click", function () {
        if (root.querySelectorAll("[data-digital-tutoring-row]").length >= schoolYears().length) return;
        makeRow(root, "");
        updateStatus(root);
        emit(root);
      });
    }

    root.addEventListener("change", function (event) {
      var row = event.target.closest ? event.target.closest("[data-digital-tutoring-row]") : null;
      if (row && event.target.matches("[data-digital-year]")) {
        syncRow(row);
        syncYearOptions(root);
      }
      updateStatus(root);
      emit(root);
    });

    root.addEventListener("input", function (event) {
      var row = event.target.closest ? event.target.closest("[data-digital-tutoring-row]") : null;
      if (row && (event.target.matches("[data-digital-months]") || event.target.matches("[data-digital-days]"))) {
        syncRow(row);
      }
      updateStatus(root);
      emit(root);
    });

    root.addEventListener("click", function (event) {
      if (!event.target.matches("[data-digital-remove]")) return;
      var row = event.target.closest("[data-digital-tutoring-row]");
      if (row) row.remove();
      if (!root.querySelector("[data-digital-tutoring-row]")) makeRow(root, schoolYears()[0] || "");
      updateTitles(root);
      syncYearOptions(root);
      updateStatus(root);
      emit(root);
    });

    makeRow(root, schoolYears()[0] || "");
    updateStatus(root);
  }

  function initAll() {
    document.querySelectorAll('[data-component="asep-digital-tutoring-service"]').forEach(initRoot);
  }

  global.AsepDigitalTutoring = Object.freeze({
    initAll: initAll,
    readEntries: readEntries,
    calculate: calculate,
    getState: calculate,
    details: details,
    summary: summary,
    reset: reset
  });

  if (document.readyState === "loading") document.addEventListener("DOMContentLoaded", initAll);
  else initAll();
})(window);
