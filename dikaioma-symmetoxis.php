<!DOCTYPE html>
<html lang="el">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Έχω δικαίωμα συμμετοχής στις προκηρύξεις 1ΓΕ/2026, 2ΓΕ/2026;</title>
  <link rel="stylesheet" href="assets/common.css?v=3.20.9-rc1">
</head>

<body class="edu-ui edu-guide-standard edu-guide-eligibility">
<?php require_once __DIR__ . '/includes/header.php'; ?>

<div class="app-box edu-modernized">
<section class="hero edu-legacy-hero">
<h1>Έχω δικαίωμα συμμετοχής στις προκηρύξεις 1ΓΕ/2026 &amp; 2ΓΕ/2026;</h1>
<p class="intro">
  Το παρόν εργαλείο παρέχει έναν ενδεικτικό έλεγχο των γενικών προϋποθέσεων συμμετοχής
  στις <a href="https://search.et.gr/el/fek/?fekId=798251">1ΓΕ/2026</a> και <a href="https://search.et.gr/el/fek/?fekId=798194">2ΓΕ/2026</a> και δεν αντικαθιστά την επίσημη προκήρυξη του Α.Σ.Ε.Π. ούτε την προσωπική ευθύνη
  του/της υποψηφίου/ας για την ορθή υποβολή της αίτησης.
</p>
</section>
  <p class="intro">
    Απάντησε στις παρακάτω ερωτήσεις για έναν ενδεικτικό έλεγχο των γενικών προϋποθέσεων συμμετοχής.
  </p>

  <div class="progress-panel" aria-live="polite">
    <div class="progress-head">
      <span>Πρόοδος συμπλήρωσης</span>
      <span id="progressText">0/14 απαντήσεις</span>
    </div>
    <div class="progress-track" aria-hidden="true">
      <div id="progressFill" class="progress-fill"></div>
    </div>
  </div>

	<div class="question">
	  <label for="birthYear">Έτος γέννησης υποψηφίου/ας</label>
	  <input type="number" id="birthYear" min="1900" max="2026" placeholder="π.χ. 1985">
	</div>

  <div class="question">
    <label for="citizenship">Ιθαγένεια / κατηγορία υποψηφίου/ας</label>
    <select id="citizenship">
      <option value="">-- Επιλογή --</option>
      <option value="eligible">Έλληνας/Ελληνίδα πολίτης</option>
      <option value="eligible">Πολίτης κράτους-μέλους της Ευρωπαϊκής Ένωσης</option>
      <option value="eligible">Βορειοηπειρώτης/τισσα, Κύπριος/α Ομογενής ή Ομογενής αλλοδαπός/ή από Κωνσταντινούπολη, Ίμβρο, Τένεδο ή Αίγυπτο με τα απαιτούμενα αποδεικτικά</option>
      <option value="not_eligible">Δεν ανήκω σε καμία από τις παραπάνω κατηγορίες</option>
      <option value="unknown">Δεν είμαι σίγουρος/η</option>
    </select>
  </div>

  <div class="question">
    <label for="health">Έχεις την υγεία που απαιτείται για την εκτέλεση των καθηκόντων της θέσης;</label>
    <select id="health">
      <option value="">-- Επιλογή --</option>
      <option value="yes">Ναι</option>
      <option value="no">Όχι</option>
      <option value="unknown">Δεν είμαι σίγουρος/η</option>
    </select>
  </div>

  <div class="question">
    <label for="qualifications">Κατέχεις τα απαραίτητα προσόντα της ειδικότητας, όπως πτυχίο, τίτλο ξένης γλώσσας όπου απαιτείται, αναγνώριση/ισοτιμία τίτλων κ.λπ.;</label>
    <select id="qualifications">
      <option value="">-- Επιλογή --</option>
      <option value="yes">Ναι</option>
      <option value="no">Όχι</option>
      <option value="unknown">Δεν είμαι σίγουρος/η</option>
    </select>
  </div>

  <div class="question">
    <label for="dismissed">
      Έχεις απολυθεί από θέση δημόσιας υπηρεσίας, Ο.Τ.Α. ή άλλου νομικού προσώπου του δημόσιου τομέα λόγω ποινής οριστικής παύσης ή λόγω καταγγελίας σύμβασης για σπουδαίο λόγο, χωρίς να έχει παρέλθει δεκαετία;
    </label>
    <select id="dismissed">
      <option value="">-- Επιλογή --</option>
      <option value="yes">Ναι</option>
      <option value="no">Όχι</option>
      <option value="unknown">Δεν είμαι σίγουρος/η</option>
    </select>
  </div>

  <div class="question">
    <label for="criminal">
      Διώκεσαι ποινικά ή έχεις καταδικαστεί για οποιοδήποτε έγκλημα κατά της γενετήσιας ελευθερίας ή οικονομικής εκμετάλλευσης της γενετήσιας ζωής;
    </label>
    <select id="criminal">
      <option value="">-- Επιλογή --</option>
      <option value="yes">Ναι</option>
      <option value="no">Όχι</option>
      <option value="unknown">Δεν είμαι σίγουρος/η</option>
    </select>
  </div>

	<div class="question">
	  <label for="convictionImpediment">
		Έχεις καταδικαστεί για κακούργημα ή για αδικήματα όπως κλοπή, υπεξαίρεση, απάτη, εκβίαση, πλαστογραφία, δωροδοκία, απιστία περί την υπηρεσία, παράβαση καθήκοντος ή καθ’ υποτροπή συκοφαντική δυσφήμηση;
	  </label>
	  <select id="convictionImpediment">
		<option value="">-- Επιλογή --</option>
		<option value="yes">Ναι</option>
		<option value="no">Όχι</option>
		<option value="unknown">Δεν είμαι σίγουρος/η</option>
	  </select>
	</div>

	<div class="question">
	  <label for="indictmentImpediment">
		Έχεις παραπεμφθεί με τελεσίδικο βούλευμα για κακούργημα ή για κάποιο από τα παραπάνω αδικήματα;
	  </label>
	  <select id="indictmentImpediment">
		<option value="">-- Επιλογή --</option>
		<option value="yes">Ναι</option>
		<option value="no">Όχι</option>
		<option value="unknown">Δεν είμαι σίγουρος/η</option>
	  </select>
	</div>

	<div class="question">
	  <label for="civilRightsOrSupport">
		Έχεις στερηθεί πολιτικά δικαιώματα λόγω καταδίκης ή τελείς υπό στερητική/επικουρική δικαστική συμπαράσταση;
	  </label>
	  <select id="civilRightsOrSupport">
		<option value="">-- Επιλογή --</option>
		<option value="yes">Ναι</option>
		<option value="no">Όχι</option>
		<option value="unknown">Δεν είμαι σίγουρος/η</option>
	  </select>
	</div>

  <div class="question">
    <label for="commercial">
      Μετέχεις σε εμπορική εταιρεία ή έχεις ιδιότητα που μπορεί να δημιουργεί κώλυμα ανάληψης υπηρεσίας, όπως ομόρρυθμος/η, ετερόρρυθμος/η, διαχειριστής/τρια ή διευθύνων/εντεταλμένος/η σύμβουλος;
    </label>
    <select id="commercial">
      <option value="">-- Επιλογή --</option>
      <option value="yes">Ναι</option>
      <option value="no">Όχι</option>
      <option value="unknown">Δεν είμαι σίγουρος/η</option>
    </select>
  </div>

  <div class="question">
    <label for="politicalOffice">
      Κατέχεις βουλευτικό αξίωμα;
    </label>
    <select id="politicalOffice">
      <option value="">-- Επιλογή --</option>
      <option value="yes">Ναι</option>
      <option value="no">Όχι</option>
      <option value="unknown">Δεν είμαι σίγουρος/η</option>
    </select>
  </div>

  <div class="question">
    <label for="publicFullTime">
      Απασχολείσαι ήδη στο Δημόσιο ή σε κρατικό νομικό πρόσωπο δημοσίου ή ιδιωτικού δικαίου με πλήρες ωράριο εργασίας;
    </label>
    <select id="publicFullTime">
      <option value="">-- Επιλογή --</option>
      <option value="yes">Ναι</option>
      <option value="no">Όχι</option>
      <option value="unknown">Δεν είμαι σίγουρος/η</option>
    </select>
  </div>

  <div class="question">
    <label for="privateEducation">
      Είσαι ιδιοκτήτης/τρια φροντιστηρίου ή διδάσκεις σε ιδιωτικό σχολείο με πλήρες ή μειωμένο ωράριο;
    </label>
    <select id="privateEducation">
      <option value="">-- Επιλογή --</option>
      <option value="yes">Ναι</option>
      <option value="no">Όχι</option>
      <option value="unknown">Δεν είμαι σίγουρος/η</option>
    </select>
  </div>

  <div class="question">
    <label for="military">
      Για άνδρες υποψηφίους: Έχουν εκπληρωθεί οι στρατιωτικές υποχρεώσεις ή υπάρχει νόμιμη απαλλαγή;
    </label>
    <select id="military">
      <option value="">-- Επιλογή --</option>
      <option value="not_applicable">Δεν με αφορά</option>
      <option value="yes">Ναι, έχουν εκπληρωθεί ή υπάρχει νόμιμη απαλλαγή</option>
      <option value="no">Όχι</option>
      <option value="unknown">Δεν είμαι σίγουρος/η</option>
    </select>
  </div>

  <div class="button-row">
    <button type="button" onclick="checkEligibility()">Έλεγχος δικαιώματος συμμετοχής</button>
    <button type="button" class="reset-button" onclick="resetForm()">Μηδενισμός</button>
  </div>

  <div id="result" class="result" role="status" aria-live="polite"></div>

  <p class="small-note">
    Το αποτέλεσμα είναι ενδεικτικό και βασίζεται στις γενικές προϋποθέσεις συμμετοχής του Παραρτήματος Α΄.
    Δεν αντικαθιστά την προσεκτική ανάγνωση της προκήρυξης και των επίσημων οδηγιών του Α.Σ.Ε.Π.
  </p>
</div>

<script>
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

  function updateProgress() {
    const answered = fieldIds.filter(id => String(valueOf(id)).trim() !== "").length;
    const total = fieldIds.length;
    document.getElementById("progressText").textContent = `${answered}/${total} απαντήσεις`;
    document.getElementById("progressFill").style.width = `${(answered / total) * 100}%`;
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
    updateProgress();
    updateImmediateWarning(field);

    // Αποφεύγουμε να μένει στην οθόνη παλιό αποτέλεσμα μετά από αλλαγή απάντησης.
    const result = document.getElementById("result");
    result.style.display = "none";
    result.innerHTML = "";
  }

  function resetForm() {
    fieldIds.forEach(id => {
      const field = document.getElementById(id);
      field.value = "";
      setInlineWarning(field, "");
    });

    const result = document.getElementById("result");
    result.style.display = "none";
    result.innerHTML = "";
    updateProgress();
    document.getElementById("birthYear").focus();
  }

  fieldIds.forEach(id => {
    const field = document.getElementById(id);
    field.addEventListener(field.tagName === "SELECT" ? "change" : "input", updateFormState);
  });

  updateProgress();

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

    const requiredFields = [
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
    ];

	if (!birthYear || requiredFields.includes("")) {
	  showResult("Παρακαλώ απάντησε σε όλες τις ερωτήσεις.", "unknown");
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
</script>


<section class="edu-source-card" aria-labelledby="sourcesTitle">
  <h2 id="sourcesTitle">Πηγές / Νομική βάση</h2>
  <p>Προκηρύξεις Α.Σ.Ε.Π. <strong>1ΓΕ/2026</strong> (ΦΕΚ 21/τ. Α.Σ.Ε.Π./29.04.2026) και <strong>2ΓΕ/2026</strong> (ΦΕΚ 22/τ. Α.Σ.Ε.Π./29.04.2026), ιδίως οι γενικές προϋποθέσεις συμμετοχής και το Παράρτημα Α΄.</p>
  <p class="source-links"><a href="https://info.asep.gr/node/78700" target="_blank" rel="noopener noreferrer">1ΓΕ/2026 — ΑΣΕΠ ↗</a> · <a href="https://info.asep.gr/node/78701" target="_blank" rel="noopener noreferrer">2ΓΕ/2026 — ΑΣΕΠ ↗</a></p>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
  <script src="assets/common.js"></script>
</body>
</html>