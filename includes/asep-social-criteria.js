/* Shared DOM/controller layer for ASEP social criteria. */
(function (global) {
  "use strict";

  function resolveRoot(ref) {
    if (ref && ref.nodeType === 1) return ref;
    if (typeof ref === "string" && ref) {
      const byId = document.getElementById(ref);
      if (byId) return byId;
    }
    return document.querySelector('[data-component="asep-social-criteria"]');
  }

  function byConfiguredId(root, key) {
    const id = root.getAttribute("data-" + key + "-id") || "";
    return id ? document.getElementById(id) : null;
  }

  function num(el) {
    if (!el) return 0;
    const value = Number(el.value);
    return Number.isFinite(value) ? value : 0;
  }

  function read(ref) {
    const root = resolveRoot(ref);
    if (!root) throw new Error("Δεν βρέθηκε το κοινό τμήμα κοινωνικών κριτηρίων ΑΣΕΠ.");
    const marriage = byConfiguredId(root, "marriage");
    const mental = byConfiguredId(root, "mental");
    return {
      children: num(byConfiguredId(root, "children")),
      candidateDisability: num(byConfiguredId(root, "candidate")),
      spouseDisability: num(byConfiguredId(root, "spouse")),
      childDisability: num(byConfiguredId(root, "child")),
      marriageYears4Plus: Boolean(marriage && marriage.checked),
      candidateMentalCondition: Boolean(mental && mental.checked)
    };
  }

  function calculate(ref) {
    if (!global.EducationSocial || !global.EducationSocial.calculate) {
      throw new Error("Δεν φορτώθηκε η κοινή μηχανή κοινωνικών κριτηρίων.");
    }
    return global.EducationSocial.calculate(read(ref));
  }

  function details(resultOrRef, formatter) {
    const result = resultOrRef && Object.prototype.hasOwnProperty.call(resultOrRef, "childrenPoints") ? resultOrRef : calculate(resultOrRef);
    const fmt = typeof formatter === "function" ? formatter : function (value) { return String(value); };
    const out = [];
    if (result.childrenPoints > 0) out.push("Επιλέξιμα τέκνα: " + fmt(result.childrenPoints) + " μόρια");
    if (result.disabilityPoints > 0) {
      out.push("Αναπηρία (" + result.highestLabel + " " + fmt(result.highestDisabilityPercent) + "%): " + fmt(result.disabilityPoints) + " μόρια");
    }
    return out;
  }

  function sync(ref, result, formatter) {
    const root = resolveRoot(ref);
    if (!root) return result;
    const fmt = typeof formatter === "function" ? formatter : function (value) { return String(value); };
    const warningId = root.getAttribute("data-warning-id") || "";
    const subtotalId = root.getAttribute("data-subtotal-id") || "";
    if (warningId) {
      const warning = document.getElementById(warningId);
      if (warning) {
        const bulletMode = root.getAttribute("data-warning-mode") === "bullets";
        warning[bulletMode ? "innerHTML" : "textContent"] = bulletMode
          ? result.warnings.map(function (message) { return "• " + message; }).join("<br>")
          : result.warnings.join(" ");
        warning.classList.toggle("hidden", result.warnings.length === 0);
      }
    }
    if (subtotalId) {
      const subtotal = document.getElementById(subtotalId);
      if (subtotal) subtotal.textContent = fmt(result.total);
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
    ["children", "candidate", "spouse", "child"].forEach(function (key) {
      const el = byConfiguredId(root, key);
      if (el) el.value = "0";
    });
    ["marriage", "mental"].forEach(function (key) {
      const el = byConfiguredId(root, key);
      if (el) el.checked = false;
    });
    if (!(options && options.silent)) root.dispatchEvent(new CustomEvent("asep-social-change", { bubbles: true }));
  }

  global.AsepSocialCriteria = Object.freeze({
    read: read,
    calculate: calculate,
    getState: getState,
    details: details,
    sync: sync,
    reset: reset
  });
})(window);
