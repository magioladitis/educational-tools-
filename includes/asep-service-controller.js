/*
 * DOM/controller layer for shared ASEP educational-service calculation.
 * Fields are discovered by data-service-role, so page-specific IDs do not
 * leak into calculation code.
 */
(function (global) {
  "use strict";

  const ROLE_TO_OPTION = Object.freeze({
    "regular": "regularMonths",
    "difficult": "difficultMonths",
    "three-month-regular-2020": "threeMonthRegular2020",
    "three-month-regular-2021": "threeMonthRegular2021",
    "three-month-difficult-2020": "threeMonthDifficult2020",
    "three-month-difficult-2021": "threeMonthDifficult2021",
    "private": "privateMonths"
  });

  function resolveRoot(ref) {
    if (ref && ref.nodeType === 1) return ref;
    if (typeof ref === "string" && ref) {
      const byId = document.getElementById(ref);
      if (byId) return byId;
    }
    return document.querySelector('[data-component="asep-service-criteria"]');
  }

  const boundRoots = typeof WeakSet === "function" ? new WeakSet() : null;

  function isServiceInput(el) {
    return !!(el && el.nodeType === 1 && el.matches &&
      el.matches('[data-service-role]:not([data-service-role="digital-tutoring"])'));
  }

  function sanitizeInput(el) {
    if (!isServiceInput(el) || !("value" in el) || el.value === "") return false;
    let value = Math.max(0, Math.floor(Number(el.value) || 0));
    const max = el.getAttribute("max");
    if (max !== null && max !== "" && Number.isFinite(Number(max))) {
      value = Math.min(value, Number(max));
    }
    const normalized = String(value);
    const changed = el.value !== normalized;
    if (changed) el.value = normalized;
    return changed;
  }

  function sanitize(ref) {
    const root = resolveRoot(ref);
    if (!root) return null;
    root.querySelectorAll('[data-service-role]:not([data-service-role="digital-tutoring"])').forEach(sanitizeInput);
    return root;
  }

  function bind(ref) {
    const root = resolveRoot(ref);
    if (!root) return null;
    if (boundRoots && boundRoots.has(root)) return root;
    if (!boundRoots && root.getAttribute("data-service-sanitizer-bound") === "true") return root;
    const handler = function (event) { sanitizeInput(event.target); };
    root.addEventListener("input", handler);
    root.addEventListener("change", handler);
    if (boundRoots) boundRoots.add(root);
    else root.setAttribute("data-service-sanitizer-bound", "true");
    return root;
  }

  function bindAll() {
    document.querySelectorAll('[data-component="asep-service-criteria"]').forEach(function (root) { bind(root); });
  }

  function numberValue(el) {
    if (!el) return 0;
    sanitizeInput(el);
    const n = Number(el.value);
    return Number.isFinite(n) ? n : 0;
  }

  function read(ref) {
    const root = resolveRoot(ref);
    if (!root) throw new Error("Δεν βρέθηκε το κοινό τμήμα προϋπηρεσίας ΑΣΕΠ.");
    sanitize(root);
    const options = {};
    Object.keys(ROLE_TO_OPTION).forEach(function (role) {
      const el = root.querySelector('[data-service-role="' + role + '"]');
      options[ROLE_TO_OPTION[role]] = numberValue(el);
    });

    const digital = root.querySelector('[data-service-role="digital-tutoring"]');
    if (digital && global.AsepDigitalTutoring) {
      options.digitalTutoring = global.AsepDigitalTutoring.getState(digital.id);
    }
    return options;
  }

  function calculate(ref) {
    if (!global.EducationService || !global.EducationService.calculateAsepService) {
      throw new Error("Δεν φορτώθηκε η κοινή μηχανή υπολογισμού προϋπηρεσίας.");
    }
    return global.EducationService.calculateAsepService(read(ref));
  }

  function details(resultOrRef, formatter) {
    const result = resultOrRef && resultOrRef.parts ? resultOrRef : calculate(resultOrRef);
    const fmt = typeof formatter === "function" ? formatter : function (value) { return String(value); };
    const p = result.parts;
    const out = [];
    if (p.regular.months > 0) out.push("Δημόσια εκπαιδευτική προϋπηρεσία: " + fmt(p.regular.points) + " μόρια");
    if (p.difficult.months > 0) out.push("Δυσπρόσιτα / καταστήματα κράτησης: " + fmt(p.difficult.points) + " μόρια");
    if (p.threeMonthRegular2020.months > 0) out.push("Τρίμηνες συμβάσεις 2020-2021: " + fmt(p.threeMonthRegular2020.points) + " μόρια");
    if (p.threeMonthRegular2021.months > 0) out.push("Τρίμηνες συμβάσεις 2021-2022: " + fmt(p.threeMonthRegular2021.points) + " μόρια");
    if (p.threeMonthDifficult2020.months > 0) out.push("Τρίμηνες συμβάσεις σε δυσπρόσιτα 2020-2021: " + fmt(p.threeMonthDifficult2020.points) + " μόρια");
    if (p.threeMonthDifficult2021.months > 0) out.push("Τρίμηνες συμβάσεις σε δυσπρόσιτα 2021-2022: " + fmt(p.threeMonthDifficult2021.points) + " μόρια");
    if (p.privateSchool.months > 0) out.push("Ιδιωτική εκπαίδευση: " + fmt(p.privateSchool.points) + " μόρια");
    if (p.digitalTutoring && p.digitalTutoring.activeYears && p.digitalTutoring.activeYears.length && global.AsepDigitalTutoring) {
      const digitalRoot = resolveRoot(resultOrRef);
      const digital = digitalRoot && digitalRoot.querySelector('[data-service-role="digital-tutoring"]');
      if (digital) out.push.apply(out, global.AsepDigitalTutoring.details(digital.id, fmt));
    }
    return out;
  }

  function sync(ref, result, formatter) {
    const root = resolveRoot(ref);
    if (!root) return result;
    const fmt = typeof formatter === "function" ? formatter : function (value) { return String(value); };
    const subtotalId = root.getAttribute("data-subtotal-id") || "";
    const warningId = root.getAttribute("data-warning-id") || "";
    if (subtotalId) {
      const subtotal = document.getElementById(subtotalId);
      if (subtotal) subtotal.textContent = fmt(result.points) + (root.getAttribute("data-subtotal-with-cap") === "true" ? " / 120" : "");
    }
    if (warningId) {
      const warning = document.getElementById(warningId);
      if (warning) {
        const displayWarnings = result.warnings.slice();
        if (root.getAttribute("data-warn-months") === "true" && result.months > 120) {
          displayWarnings.unshift("Έχουν δηλωθεί " + result.months + " μήνες σε ξεχωριστές κατηγορίες. Έλεγξε ότι δεν έχει δηλωθεί ο ίδιος μήνας δύο φορές και ότι η συνολική πραγματική προϋπηρεσία δεν υπερβαίνει τους 120 μήνες που λαμβάνονται υπόψη.");
        }
        warning.textContent = displayWarnings.join(" ");
        warning.classList.toggle("hidden", displayWarnings.length === 0);
      }
    }
    return result;
  }

  function getState(ref, formatter) {
    const result = calculate(ref);
    return sync(ref, result, formatter);
  }

  function reset(ref, options) {
    const root = resolveRoot(ref);
    if (!root) return;
    root.querySelectorAll('[data-service-role]:not([data-service-role="digital-tutoring"])').forEach(function (el) {
      if ("value" in el) el.value = "0";
    });
    const digital = root.querySelector('[data-service-role="digital-tutoring"]');
    if (digital && global.AsepDigitalTutoring) {
      global.AsepDigitalTutoring.reset(digital.id, { silent: true });
    }
    if (!(options && options.silent)) {
      root.dispatchEvent(new CustomEvent("asep-service-change", { bubbles: true }));
    }
  }

  global.AsepServiceController = Object.freeze({
    read: read,
    calculate: calculate,
    getState: getState,
    details: details,
    sync: sync,
    sanitizeInput: sanitizeInput,
    sanitize: sanitize,
    bind: bind,
    bindAll: bindAll,
    reset: reset
  });

  if (document.readyState === "loading") document.addEventListener("DOMContentLoaded", bindAll);
  else bindAll();
})(window);
