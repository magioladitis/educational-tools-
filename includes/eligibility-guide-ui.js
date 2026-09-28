/*
 * Browser UI controller for dikaioma-symmetoxis.php.
 * The PHP page owns the questions/content; this file owns validation,
 * result rendering and interaction wiring.
 */
(function(global){
  "use strict";
function valueOf(id) {
    return document.getElementById(id).value;
  }

  function showResult(message, cssClass) {
    const result = document.getElementById("result");
    result.style.display = "block";
    result.className = "result " + cssClass;
    result.innerHTML = message;
  }

  const fieldIds = [
    "birthYear",
    "citizenship",
    "health",
    "qualifications",
    "dismissed",
    "criminal",
    "convictionImpediment",
    "indictmentImpediment",
    "civilRightsOrSupport",
    "commercial",
    "politicalOffice",
    "publicFullTime",
    "privateEducation",
    "military"
  ];

  let validationAttempted = false;

  function setMissingState(field, isMissing) {
    const question = field.closest(".question");
    if (!question) return;
    question.classList.toggle("has-missing", Boolean(isMissing));
    field.classList.toggle("edu-field-invalid", Boolean(isMissing));
    if (isMissing) {
      field.setAttribute("aria-invalid", "true");
      field.setAttribute("data-edu-validation-owned", "true");
    } else if (field.getAttribute("data-edu-validation-owned") === "true") {
      field.removeAttribute("aria-invalid");
      field.removeAttribute("data-edu-validation-owned");
    }
  }

  const immediateImpediments = {
    citizenship: {
      value: "not_eligible",
      message: "⚠️ Η συγκεκριμένη απάντηση δεν πληροί την προϋπόθεση ιθαγένειας / ειδικής κατηγορίας υποψηφίου/ας."
    },
    health: {
      value: "no",
      message: "⚠️ Η συγκεκριμένη απάντηση αποτελεί κώλυμα, επειδή δηλώθηκε ότι δεν υπάρχει η απαιτούμενη υγεία για την εκτέλεση των καθηκόντων."
    },
    qualifications: {
      value: "no",
      message: "⚠️ Η συγκεκριμένη απάντηση αποτελεί κώλυμα, επειδή δηλώθηκε ότι δεν κατέχονται τα απαιτούμενα προσόντα της ειδικότητας."
    },
    dismissed: {
      value: "yes",
      message: "⚠️ Δηλώθηκε απόλυση που, σύμφωνα με τα κριτήρια του εργαλείου, δημιουργεί κώλυμα συμμετοχής."
    },
    criminal: {
      value: "yes",
      message: "⚠️ Δηλώθηκε ποινική δίωξη ή καταδίκη για αδικήματα που αποτελούν κώλυμα."
    },
    convictionImpediment: {
      value: "yes",
      message: "⚠️ Δηλώθηκε καταδίκη που, σύμφωνα με τα κριτήρια του εργαλείου, αποτελεί κώλυμα διορισμού."
    },
    indictmentImpediment: {
      value: "yes",
      message: "⚠️ Δηλώθηκε παραπομπή με τελεσίδικο βούλευμα που, σύμφωνα με τα κριτήρια του εργαλείου, αποτελεί κώλυμα διορισμού."
    },
    civilRightsOrSupport: {
      value: "yes",
      message: "⚠️ Δηλώθηκε στέρηση πολιτικών δικαιωμάτων ή δικαστική συμπαράσταση, που αποτελεί κώλυμα σύμφωνα με τα κριτήρια του εργαλείου."
    }
  };

  function setInlineWarning(field, message) {
    const question = field.closest(".question");
    if (!question) return;

    let warning = question.querySelector(".inline-impediment");
    if (!warning) {
      warning = document.createElement("div");
      warning.className = "inline-impediment";
      warning.setAttribute("role", "alert");
      question.appendChild(warning);
    }

    if (message) {
      question.classList.add("has-impediment");
      warning.textContent = message;
      warning.style.display = "block";
    } else {
      question.classList.remove("has-impediment");
      warning.textContent = "";
      warning.style.display = "none";
    }
  }

  function updateImmediateWarning(field) {
    const id = field.id;

    if (id === "birthYear") {
      const raw = field.value.trim();
      if (!raw) {
        setInlineWarning(field, "");
        return;
      }

      const birthYear = parseInt(raw, 10);
      if (birthYear < 1900 || birthYear > 2026) {
        setInlineWarning(field, "⚠️ Συμπλήρωσε έγκυρο έτος γέννησης.");
      } else if (birthYear < 1959 || birthYear > 2005) {
        setInlineWarning(field, "⚠️ Με βάση το ηλικιακό κριτήριο του εργαλείου για το 2026, το αποδεκτό εύρος γέννησης είναι 1959–2005.");
      } else {
        setInlineWarning(field, "");
      }
      return;
    }

    const rule = immediateImpediments[id];
    if (rule && field.value === rule.value) {
      setInlineWarning(field, rule.message);
    } else {
      setInlineWarning(field, "");
    }
  }

  function updateFormState(event) {
    const field = event.target;
    if (validationAttempted) {
      setMissingState(field, String(field.value).trim() === "");
      const stillMissing = fieldIds.some(id => String(valueOf(id)).trim() === "");
      if (!stillMissing && window.EduToolsUI && typeof window.EduToolsUI.clearValidationSummary === "function") {
        window.EduToolsUI.clearValidationSummary(document.querySelector(".app-box"));
      }
    }
    updateImmediateWarning(field);

    // Αποφεύγουμε να μένει στην οθόνη παλιό αποτέλεσμα μετά από αλλαγή απάντησης.
    const result = document.getElementById("result");
    result.style.display = "none";
    result.innerHTML = "";
  }

  function resetForm() {
    validationAttempted = false;
    fieldIds.forEach(id => {
      const field = document.getElementById(id);
      field.value = "";
      setInlineWarning(field, "");
      setMissingState(field, false);
    });

    const result = document.getElementById("result");
    result.style.display = "none";
    result.innerHTML = "";
    if (window.EduToolsUI && typeof window.EduToolsUI.clearValidationSummary === "function") {
      window.EduToolsUI.clearValidationSummary(document.querySelector(".app-box"));
    }
    document.getElementById("birthYear").focus();
  }


  function checkEligibility() {
    const birthYear = parseInt(valueOf("birthYear"), 10);
	const referenceYear = 2026;
	const minBirthYear = referenceYear - 67; // 1959
	const maxBirthYear = referenceYear - 21; // 2005

    const citizenship = valueOf("citizenship");
    const health = valueOf("health");
    const qualifications = valueOf("qualifications");

    const dismissed = valueOf("dismissed");
    const criminal = valueOf("criminal");
    const convictionImpediment = valueOf("convictionImpediment");
	const indictmentImpediment = valueOf("indictmentImpediment");
	const civilRightsOrSupport = valueOf("civilRightsOrSupport");

    const commercial = valueOf("commercial");
    const politicalOffice = valueOf("politicalOffice");
    const publicFullTime = valueOf("publicFullTime");
    const privateEducation = valueOf("privateEducation");
    const military = valueOf("military");

    validationAttempted = true;
    const missingIds = fieldIds.filter(id => String(valueOf(id)).trim() === "");
    fieldIds.forEach(id => {
      const field = document.getElementById(id);
      setMissingState(field, missingIds.includes(id));
    });

    if (missingIds.length > 0) {
      const noun = missingIds.length === 1 ? "ερώτηση" : "ερωτήσεις";
      const message = `Απομένουν ${missingIds.length} ${noun} χωρίς απάντηση. Συμπλήρωσέ ${missingIds.length === 1 ? "την" : "τις"} και ξαναπάτησε «Έλεγχος δικαιώματος συμμετοχής».`;
      showResult(message, "unknown");
      if (window.EduToolsUI && typeof window.EduToolsUI.reportMissingFields === "function") {
        window.EduToolsUI.reportMissingFields(missingIds, {
          scope: document.querySelector(".app-box"),
          message: message
        });
      } else {
        const firstMissing = document.getElementById(missingIds[0]);
        if (firstMissing) firstMissing.focus();
      }
      return;
    }

	if (birthYear < 1900 || birthYear > referenceYear) {
	  showResult(
		"Παρακαλώ συμπλήρωσε έγκυρο έτος γέννησης.",
		"unknown"
	  );
	  return;
	}

	if (birthYear < minBirthYear || birthYear > maxBirthYear) {
	  showResult(
		"Δεν προκύπτει δικαίωμα συμμετοχής, επειδή δεν πληρούται το ηλικιακό όριο των γενικών προϋποθέσεων. Για έτος αναφοράς το 2026, το αποδεκτό εύρος γέννησης είναι από το 1959 έως και το 2005.",
		"not-eligible"
	  );
	  return;
	}

    if (citizenship === "not_eligible") {
      showResult(
        "Δεν προκύπτει δικαίωμα συμμετοχής, επειδή δεν πληρούται η προϋπόθεση ιθαγένειας ή ειδικής κατηγορίας υποψηφίου/ας.",
        "not-eligible"
      );
      return;
    }

    if (health === "no") {
      showResult(
        "Δεν προκύπτει δικαίωμα συμμετοχής, επειδή δηλώθηκε ότι δεν υπάρχει η απαιτούμενη υγεία για την εκτέλεση των καθηκόντων της θέσης.",
        "not-eligible"
      );
      return;
    }

    if (qualifications === "no") {
      showResult(
        "Δεν προκύπτει δικαίωμα συμμετοχής, επειδή δηλώθηκε ότι δεν κατέχονται τα απαιτούμενα προσόντα της ειδικότητας.",
        "not-eligible"
      );
      return;
    }

    if (dismissed === "yes") {
      showResult(
        "Δεν υπάρχει δικαίωμα υποβολής αίτησης, επειδή δηλώθηκε απόλυση από φορέα του δημόσιου τομέα με σχετικό κώλυμα.",
        "not-eligible"
      );
      return;
    }

    if (criminal === "yes") {
      showResult(
        "Δεν υπάρχει δικαίωμα υποβολής αίτησης, επειδή δηλώθηκε ποινική δίωξη ή καταδίκη για αδικήματα που αποτελούν κώλυμα.",
        "not-eligible"
      );
      return;
    }

    if (convictionImpediment === "yes") {
	  showResult(
		"Δεν υπάρχει δικαίωμα υποβολής αίτησης, επειδή δηλώθηκε ποινική καταδίκη που μπορεί να αποτελεί κώλυμα διορισμού.",
		"not-eligible"
	  );
	  return;
	}

	if (indictmentImpediment === "yes") {
	  showResult(
		"Δεν υπάρχει δικαίωμα υποβολής αίτησης, επειδή δηλώθηκε παραπομπή με τελεσίδικο βούλευμα για αδίκημα που μπορεί να αποτελεί κώλυμα διορισμού.",
		"not-eligible"
	  );
	  return;
	}

	if (civilRightsOrSupport === "yes") {
	  showResult(
		"Δεν υπάρχει δικαίωμα υποβολής αίτησης, επειδή δηλώθηκε στέρηση πολιτικών δικαιωμάτων ή δικαστική συμπαράσταση.",
		"not-eligible"
	  );
	  return;
	}

    const hasUnknown = [
      citizenship,
      health,
      qualifications,
      dismissed,
      criminal,
      convictionImpediment,
	indictmentImpediment,
	civilRightsOrSupport,
      commercial,
      politicalOffice,
      publicFullTime,
      privateEducation,
      military
    ].includes("unknown");

    if (hasUnknown) {
      showResult(
        "Χρειάζεται περαιτέρω έλεγχος, επειδή σε μία ή περισσότερες ερωτήσεις επιλέχθηκε «Δεν είμαι σίγουρος/η».",
        "unknown"
      );
      return;
    }

    const conditionalIssues = [];

    if (commercial === "yes") {
      conditionalIssues.push("συμμετοχή ή ιδιότητα σε εμπορική εταιρεία");
    }

    if (politicalOffice === "yes") {
      conditionalIssues.push("κατοχή βουλευτικού αξιώματος");
    }

    if (publicFullTime === "yes") {
      conditionalIssues.push("υπηρεσία πλήρους ωραρίου στο Δημόσιο ή σε φορέα του δημόσιου τομέα");
    }

    if (privateEducation === "yes") {
      conditionalIssues.push("ιδιοκτησία φροντιστηρίου ή διδασκαλία σε ιδιωτικό σχολείο");
    }

    if (military === "no") {
      conditionalIssues.push("μη εκπλήρωση στρατιωτικών υποχρεώσεων ή μη ύπαρξη νόμιμης απαλλαγής");
    }

    if (conditionalIssues.length > 0) {
      showResult(
        "Έχεις δικαίωμα υποβολής αίτησης, αλλά υπάρχει πιθανό κώλυμα ανάληψης υπηρεσίας σε περίπτωση διορισμού.<br><br>" +
        "Σημεία που χρειάζονται τακτοποίηση/έλεγχο: <br>" +
        conditionalIssues.map(item => "• " + item).join("<br>"),
        "conditional"
      );
      return;
    }

    showResult(
      "Με βάση τις απαντήσεις σου, προκύπτει ότι έχεις δικαίωμα συμμετοχής στην προκήρυξη.",
      "eligible"
    );
  }

  function init() {
    fieldIds.forEach(function(id) {
      const field = document.getElementById(id);
      if (!field) return;
      field.addEventListener(field.tagName === "SELECT" ? "change" : "input", updateFormState);
    });

    const checkButton = document.getElementById("eligibilityCheckBtn");
    const resetButton = document.getElementById("eligibilityResetBtn");
    if (checkButton) checkButton.addEventListener("click", checkEligibility);
    if (resetButton) resetButton.addEventListener("click", resetForm);

  }

  global.EligibilityGuideUI = Object.freeze({
    checkEligibility: checkEligibility,
    resetForm: resetForm
  });

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", init);
  } else {
    init();
  }
}(typeof window !== "undefined" ? window : globalThis));
