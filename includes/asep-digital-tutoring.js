/* Κοινή συμπεριφορά Ψηφιακού Φροντιστηρίου για προκηρύξεις ΑΣΕΠ. */
(function (global) {
  "use strict";

  function rootFor(id) {
    return typeof id === "string" ? document.getElementById(id) : id;
  }

  function rulesFor(root) {
    try {
      return JSON.parse(root.dataset.schoolYears || "{}");
    } catch (error) {
      return {};
    }
  }

  function rowsFor(root) {
    return Array.from(root.querySelectorAll("[data-digital-row]"));
  }

  function nonNegativeInteger(value) {
    return Math.max(0, Math.floor(Number(value) || 0));
  }

  function sanitize(input, max) {
    const value = Math.min(nonNegativeInteger(input.value), max);
    if (String(value) !== input.value) input.value = String(value);
    return value;
  }

  function optionMarkup(rules, selectedYear) {
    return Object.keys(rules).map(year =>
      `<option value="${year}"${year === selectedYear ? " selected" : ""}>${year}</option>`
    ).join("");
  }

  function limitFor(rules, year) {
    const value = rules[year];
    if (value && typeof value === "object") {
      return {
        months: nonNegativeInteger(value.months),
        days: Math.min(29, nonNegativeInteger(value.days))
      };
    }
    return { months: nonNegativeInteger(value), days: 29 };
  }

  function renumber(root) {
    rowsFor(root).forEach((row, index) => {
      const number = index + 1;
      const caption = row.querySelector("[data-digital-year-caption]");
      const year = row.querySelector("[data-digital-year]");
      const months = row.querySelector("[data-digital-months]");
      const days = row.querySelector("[data-digital-days]");
      const monthsCaption = row.querySelector("[data-digital-months-caption]");
      const daysCaption = row.querySelector("[data-digital-days-caption]");
      if (caption) caption.textContent = number + "ο σχολικό έτος";
      if (year) {
        year.id = root.id + "Year" + number;
        caption.htmlFor = year.id;
      }
      if (months) {
        months.id = root.id + "Months" + number;
        monthsCaption.htmlFor = months.id;
      }
      if (days) {
        days.id = root.id + "Days" + number;
        daysCaption.htmlFor = days.id;
      }
    });
  }

  function sync(root) {
    const rules = rulesFor(root);
    const rows = rowsFor(root);
    const selects = rows.map(row => row.querySelector("[data-digital-year]"));
    const selectedYears = selects.map(select => select.value).filter(Boolean);

    rows.forEach((row, index) => {
      const select = selects[index];
      const limit = limitFor(rules, select.value);
      const months = row.querySelector("[data-digital-months]");
      const days = row.querySelector("[data-digital-days]");
      const monthsCaption = row.querySelector("[data-digital-months-caption] small");
      const daysCaption = row.querySelector("[data-digital-days-caption] small");

      Array.from(select.options).forEach(option => {
        option.disabled = option.value !== select.value && selectedYears.includes(option.value);
      });

      months.max = String(limit.months);
      const acceptedMonths = sanitize(months, limit.months);
      const maxDays = acceptedMonths === limit.months ? limit.days : 29;
      days.max = String(maxDays);
      sanitize(days, maxDays);
      if (monthsCaption) monthsCaption.textContent = `Μέγιστη διάρκεια έτους: ${limit.months} μήνες και ${limit.days} ημέρες`;
      if (daysCaption) daysCaption.textContent = `Έως ${limit.days} ημέρες όταν δηλωθούν ${limit.months} μήνες`;
    });

    const addButton = root.querySelector("[data-digital-add]");
    if (addButton) addButton.disabled = rows.length >= Object.keys(rules).length;
    renumber(root);
  }

  function notify(root) {
    root.dispatchEvent(new CustomEvent("asep-digital-tutoring-change", {
      bubbles: true,
      detail: { id: root.id }
    }));
  }

  function addYear(root) {
    const rules = rulesFor(root);
    const rows = rowsFor(root);
    if (rows.length >= Object.keys(rules).length) return;

    const selected = rows.map(row => row.querySelector("[data-digital-year]").value);
    const nextYear = Object.keys(rules).find(year => !selected.includes(year));
    if (!nextYear) return;

    const row = document.createElement("div");
    row.className = "digital-school-year edu-mt-14";
    row.setAttribute("data-digital-row", "");
    row.innerHTML = `
      <div class="field-grid">
        <div class="field">
          <label data-digital-year-caption></label>
          <select class="digital-year-label" data-digital-year>${optionMarkup(rules, nextYear)}</select>
        </div>
        <div class="field">
          <label data-digital-months-caption>Πλήρεις μήνες<small></small></label>
          <input class="digital-months service-months" data-digital-months type="number" min="0" step="1" value="0" inputmode="numeric">
        </div>
        <div class="field">
          <label data-digital-days-caption>Υπόλοιπο ημερών<small></small></label>
          <input class="digital-days service-months" data-digital-days type="number" min="0" max="29" step="1" value="0" inputmode="numeric">
        </div>
      </div>
      <div class="actions">
        <button type="button" class="secondary" data-digital-remove>Αφαίρεση σχολικού έτους</button>
      </div>
    `;
    root.querySelector("[data-digital-rows]").appendChild(row);
    sync(root);
    row.querySelector("[data-digital-year]").focus();
    notify(root);
  }

  function removeYear(root, button) {
    if (rowsFor(root).length === 1) return;
    const row = button.closest("[data-digital-row]");
    if (!row) return;
    row.remove();
    sync(root);
    notify(root);
  }

  function getState(id) {
    const root = rootFor(id);
    if (!root || !global.EducationService) {
      return { activeYears: [], totalDays: 0, convertedMonths: 0, remainingDays: 0, basePoints: 0, convertedRawPoints: 0, convertedPoints: 0, maxPoints: 0, points: 0 };
    }

    const rules = rulesFor(root);
    const entries = rowsFor(root).map((row, index) => {
      const year = row.querySelector("[data-digital-year]").value;
      const limit = limitFor(rules, year);
      return {
        label: year || ((index + 1) + "ο σχολικό έτος"),
        months: row.querySelector("[data-digital-months]").value,
        days: row.querySelector("[data-digital-days]").value,
        maxMonths: limit.months,
        maxDaysAtMaxMonths: limit.days
      };
    });
    return global.EducationService.digitalAcrossSchoolYears(entries);
  }

  function reset(id, options) {
    const root = rootFor(id);
    if (!root) return;
    const rows = rowsFor(root);
    rows.slice(1).forEach(row => row.remove());
    const first = rowsFor(root)[0];
    const firstYear = Object.keys(rulesFor(root))[0] || "";
    first.querySelector("[data-digital-year]").value = firstYear;
    first.querySelector("[data-digital-months]").value = "0";
    first.querySelector("[data-digital-days]").value = "0";
    sync(root);
    if (!options || !options.silent) notify(root);
  }

  function summary(id, formatPoints) {
    const state = getState(id);
    if (!state.activeYears.length) return "";
    const format = typeof formatPoints === "function" ? formatPoints : value => String(value);
    return "Ψηφιακό Φροντιστήριο: " + format(state.points) + " μόρια (" +
      state.convertedMonths + " επιπλέον " + (state.convertedMonths === 1 ? "μήνας" : "μήνες") +
      " από το άθροισμα ημερών, υπόλοιπο " + state.remainingDays + " ημέρες)";
  }

  function init(root) {
    if (!root || root.dataset.digitalTutoringReady === "1") return;
    root.dataset.digitalTutoringReady = "1";
    root.addEventListener("input", event => {
      if (event.target.matches("[data-digital-months], [data-digital-days]")) sync(root);
    });
    root.addEventListener("change", event => {
      if (event.target.matches("[data-digital-year]")) sync(root);
    });
    root.addEventListener("click", event => {
      const addButton = event.target.closest("[data-digital-add]");
      if (addButton) {
        addYear(root);
        return;
      }
      const removeButton = event.target.closest("[data-digital-remove]");
      if (removeButton) removeYear(root, removeButton);
    });
    sync(root);
  }

  function initAll() {
    document.querySelectorAll("[data-digital-tutoring]").forEach(init);
  }

  global.AsepDigitalTutoring = Object.freeze({ init, initAll, sync, getState, reset, summary });
  initAll();
})(window);
