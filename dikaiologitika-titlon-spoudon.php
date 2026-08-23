<!DOCTYPE html>
<html lang="el">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Τι δικαιολογητικά χρειάζομαι για τίτλους σπουδών;</title>
  <link rel="stylesheet" href="assets/common.css?v=3.20.12-rc1">
</head>

<body class="edu-ui edu-guide-standard">
<?php require_once __DIR__ . '/includes/header.php'; ?>

<div class="app-box edu-modernized">
<section class="hero edu-legacy-hero">
<h1>Τι δικαιολογητικά χρειάζομαι για τίτλους σπουδών;</h1>
<p class="intro">
    Το εργαλείο παρέχει <strong>ενδεικτική</strong> καθοδήγηση για μεταπτυχιακούς τίτλους,
    διδακτορικά διπλώματα, integrated master και τίτλους σπουδών της αλλοδαπής.
  </p>
</section>

  <div class="question">
    <label for="titleType">Τι θέλεις να δηλώσεις;</label>
    <select id="titleType" onchange="updateQuestions()">
      <option value="">-- Επιλογή --</option>
      <option value="none">Δεν δηλώνω μεταπτυχιακό, διδακτορικό ή integrated master</option>
      <option value="msc_gr">Μεταπτυχιακός τίτλος σπουδών ημεδαπής</option>
      <option value="phd_gr">Διδακτορικό δίπλωμα ημεδαπής</option>
      <option value="integrated_master">Ενιαίος και αδιάσπαστος τίτλος σπουδών μεταπτυχιακού επιπέδου / integrated master</option>
	  <option value="joint_msc">Μεταπτυχιακός τίτλος Ειδίκευσης κοινού Π.Μ.Σ. μεταξύ Πανεπιστημίων ημεδαπής και αλλοδαπής</option>
      <option value="msc_foreign">Μεταπτυχιακός τίτλος σπουδών αλλοδαπής</option>
      <option value="phd_foreign">Διδακτορικό δίπλωμα αλλοδαπής</option>
    </select>
  </div>

  <div id="greekTitleQuestions" class="hidden">
    <div class="question">
      <label for="greekTitleStatus">Έχει εκδοθεί ο τίτλος ή εκκρεμεί ορκωμοσία;</label>
      <select id="greekTitleStatus">
        <option value="">-- Επιλογή --</option>
        <option value="issued">Έχει εκδοθεί ο τίτλος</option>
        <option value="pending_oath">Εκκρεμεί η ορκωμοσία / δεν έχει ακόμη απονεμηθεί επίσημα ο τίτλος</option>
      </select>
    </div>
  </div>

<div id="jointMscQuestions" class="hidden">
  <div class="question">
    <label for="jointMscStatus">
      Έχει εκδοθεί ο μεταπτυχιακός τίτλος ή εκκρεμεί η απονομή / ορκωμοσία;
    </label>
    <select id="jointMscStatus">
      <option value="">-- Επιλογή --</option>
      <option value="issued">Έχει εκδοθεί ο τίτλος</option>
      <option value="pending_oath">Εκκρεμεί η απονομή / ορκωμοσία</option>
    </select>
  </div>

  <div class="question">
    <label for="jointMscProof">
      Προκύπτει από τον τίτλο ή από βεβαίωση ότι πρόκειται για κοινό Πρόγραμμα Μεταπτυχιακών Σπουδών μεταξύ Πανεπιστημίου της ημεδαπής και Πανεπιστημίου της αλλοδαπής;
    </label>
    <select id="jointMscProof">
      <option value="">-- Επιλογή --</option>
      <option value="yes">Ναι</option>
      <option value="no">Όχι</option>
      <option value="unknown">Δεν είμαι σίγουρος/η</option>
    </select>
  </div>
</div>

  <div id="integratedMasterQuestions" class="hidden">
    <div class="question">
      <label for="integratedDecision">
        Έχει εκδοθεί και δημοσιευθεί η διαπιστωτική απόφαση υπαγωγής του Τμήματος στις διατάξεις για integrated master μέχρι τη λήξη της προθεσμίας;
      </label>
      <select id="integratedDecision">
        <option value="">-- Επιλογή --</option>
        <option value="yes">Ναι</option>
        <option value="no">Όχι</option>
        <option value="unknown">Δεν είμαι σίγουρος/η</option>
      </select>
    </div>

    <div class="question">
      <label for="departmentNameDifferent">
        Το Τμήμα σου έχει διαφορετική ονομασία από αυτή που αναφέρεται στη σχετική διαπιστωτική απόφαση;
      </label>
      <select id="departmentNameDifferent">
        <option value="">-- Επιλογή --</option>
        <option value="yes">Ναι</option>
        <option value="no">Όχι</option>
        <option value="unknown">Δεν είμαι σίγουρος/η</option>
      </select>
    </div>
  </div>

  <div id="foreignTitleQuestions" class="hidden">
    <div class="question">
      <label for="foreignRecognition">
        Τι ισχύει για την αναγνώριση του τίτλου σπουδών της αλλοδαπής;
      </label>
      <select id="foreignRecognition" onchange="updateForeignExemptionQuestion()">
        <option value="">-- Επιλογή --</option>
        <option value="academic_recognition">
          Έχω πράξη αναγνώρισης ακαδημαϊκής ισοδυναμίας / ισοτιμίας / αντιστοιχίας από Δ.Ο.Α.Τ.Α.Π.
        </option>
        <option value="exception">
          Εμπίπτω σε εξαίρεση και διαθέτω άλλη πράξη ή απόφαση επαγγελματικής αναγνώρισης
        </option>
        <option value="pending">
          Έχω υποβάλει αίτηση αναγνώρισης, αλλά εκκρεμεί
        </option>
        <option value="none">
          Δεν έχω αναγνώριση και δεν έχω υποβάλει αίτηση
        </option>
        <option value="unknown">
          Δεν είμαι σίγουρος/η
        </option>
      </select>
    </div>

    <div id="foreignExemptionQuestions" class="hidden">
      <div class="question">
        <label for="foreignExemption">
          Σε ποια περίπτωση εξαίρεσης ανήκεις;
        </label>

        <select id="foreignExemption">
          <option value="">-- Επιλογή --</option>

          <option value="saeitte">
            Έχω πτυχίο ή δίπλωμα ανώτατης εκπαίδευσης από χώρα της Ευρωπαϊκής Ένωσης και πράξη αναγνώρισης επαγγελματικής ισοτιμίας από το Σ.Α.Ε.Ι.Τ.Τ.Ε.
          </option>

          <option value="saep_professional_qualifications">
            Έχω απόφαση αναγνώρισης επαγγελματικών προσόντων από το Σ.Α.Ε.Π.
          </option>

          <option value="saep_professional_equivalence">
            Έχω απόφαση αναγνώρισης επαγγελματικής ισοδυναμίας τίτλου τυπικής ανώτατης εκπαίδευσης από το Σ.Α.Ε.Π.
          </option>

          <option value="saetek">
            Έχω τίτλο μεταδευτεροβάθμιας εκπαίδευσης από χώρα της Ευρωπαϊκής Ένωσης και απόφαση αναγνώρισης επαγγελματικής εκπαίδευσης από το Σ.Α.Ε.Τ.Ε.Κ.
          </option>

          <option value="automatic_recognition">
            Έχω άδεια άσκησης επαγγέλματος βάσει αυτόματης αναγνώρισης διπλωμάτων / πιστοποιητικών / τίτλων από αρμόδια εθνική αρχή
          </option>

          <option value="ateen_professional_qualifications">
            Έχω απόφαση αναγνώρισης επαγγελματικών προσόντων από το Α.Τ.Ε.Ε.Ν.
          </option>

          <option value="ateen_professional_equivalence">
            Έχω απόφαση αναγνώρισης επαγγελματικής ισοδυναμίας τίτλου τυπικής ανώτατης εκπαίδευσης από το Α.Τ.Ε.Ε.Ν.
          </option>

          <option value="unknown">
            Δεν είμαι σίγουρος/η ποια περίπτωση με αφορά
          </option>
        </select>
      </div>
    </div>
  </div>

  <button type="button" class="guide-submit" onclick="showDocuments()">Εμφάνιση δικαιολογητικών</button>

  <div id="result" class="result" role="status" aria-live="polite"></div>

  <p class="small-note">
    Το αποτέλεσμα είναι ενδεικτικό και βασίζεται στις οδηγίες για μεταπτυχιακούς,
    διδακτορικούς και integrated master τίτλους. Δεν αντικαθιστά την επίσημη προκήρυξη,
    τις οδηγίες του Α.Σ.Ε.Π., τον έλεγχο του Ο.Π.ΣΥ.Δ. ή τον έλεγχο των αρμόδιων υπηρεσιών.
  </p>
</div>

<script>
  function valueOf(id) {
    return document.getElementById(id).value;
  }

  function hide(id) {
    document.getElementById(id).classList.add("hidden");
  }

  function show(id) {
    document.getElementById(id).classList.remove("hidden");
  }

  function updateQuestions() {
    const titleType = valueOf("titleType");

    hide("greekTitleQuestions");
    hide("integratedMasterQuestions");
    hide("foreignTitleQuestions");
    hide("foreignExemptionQuestions");
	hide("jointMscQuestions");

    document.getElementById("result").style.display = "none";

    if (titleType === "msc_gr" || titleType === "phd_gr") {
      show("greekTitleQuestions");
    }

    if (titleType === "integrated_master") {
      show("integratedMasterQuestions");
    }

	if (titleType === "joint_msc") {
	  show("jointMscQuestions");
	}

    if (titleType === "msc_foreign" || titleType === "phd_foreign") {
      show("foreignTitleQuestions");
    }
  }

  function updateForeignExemptionQuestion() {
    const foreignRecognition = valueOf("foreignRecognition");

    hide("foreignExemptionQuestions");

    if (foreignRecognition === "exception") {
      show("foreignExemptionQuestions");
    }
  }

  function showResult(html) {
    const result = document.getElementById("result");
    result.style.display = "block";
    result.innerHTML = html;
  }

  function makeList(items) {
    if (items.length === 0) return "";
    return "<ul>" + items.map(item => "<li>" + item + "</li>").join("") + "</ul>";
  }

  function getForeignExemptionDetails(value) {
  const details = {
    saeitte: {
      title: "Πράξη αναγνώρισης επαγγελματικής ισοτιμίας από το Σ.Α.Ε.Ι.Τ.Τ.Ε.",
      documents: [
        "Πράξη αναγνώρισης επαγγελματικής ισοτιμίας από το Σ.Α.Ε.Ι.Τ.Τ.Ε."
      ],
      note: "Η περίπτωση αφορά τίτλο ανώτατης εκπαίδευσης από χώρα μέλος της Ευρωπαϊκής Ένωσης, για τον οποίο έχει εκδοθεί πράξη αναγνώρισης επαγγελματικής ισοτιμίας."
    },

    saep_professional_qualifications: {
      title: "Απόφαση αναγνώρισης επαγγελματικών προσόντων από το Σ.Α.Ε.Π.",
      documents: [
        "Απόφαση αναγνώρισης επαγγελματικών προσόντων από το Σ.Α.Ε.Π."
      ],
      note: "Η περίπτωση αφορά κατόχους απόφασης αναγνώρισης επαγγελματικών προσόντων από το Συμβούλιο Αναγνώρισης Επαγγελματικών Προσόντων."
    },

    saep_professional_equivalence: {
      title: "Απόφαση αναγνώρισης επαγγελματικής ισοδυναμίας από το Σ.Α.Ε.Π.",
      documents: [
        "Απόφαση αναγνώρισης επαγγελματικής ισοδυναμίας τίτλου τυπικής ανώτατης εκπαίδευσης από το Σ.Α.Ε.Π."
      ],
      note: "Η περίπτωση αφορά τίτλο τυπικής ανώτατης εκπαίδευσης για τον οποίο έχει εκδοθεί απόφαση επαγγελματικής ισοδυναμίας."
    },

    saetek: {
      title: "Απόφαση αναγνώρισης επαγγελματικής εκπαίδευσης από το Σ.Α.Ε.Τ.Ε.Κ.",
      documents: [
        "Απόφαση αναγνώρισης επαγγελματικής εκπαίδευσης από το Σ.Α.Ε.Τ.Ε.Κ."
      ],
      note: "Η περίπτωση αφορά τίτλους μεταδευτεροβάθμιας εκπαίδευσης από χώρες της Ευρωπαϊκής Ένωσης, όταν έχει αναγνωριστεί δικαίωμα άσκησης νομοθετικά κατοχυρωμένου επαγγέλματος."
    },

    automatic_recognition: {
      title: "Άδεια άσκησης επαγγέλματος βάσει αυτόματης αναγνώρισης",
      documents: [
        "Άδεια άσκησης επαγγέλματος ή σχετική πράξη της αρμόδιας εθνικής αρχής."
      ],
      note: "Η περίπτωση αφορά τίτλους, διπλώματα ή πιστοποιητικά για τα οποία έχει χορηγηθεί άδεια άσκησης επαγγέλματος βάσει του συστήματος αυτόματης αναγνώρισης."
    },

    ateen_professional_qualifications: {
      title: "Απόφαση αναγνώρισης επαγγελματικών προσόντων από το Α.Τ.Ε.Ε.Ν.",
      documents: [
        "Απόφαση αναγνώρισης επαγγελματικών προσόντων από το Α.Τ.Ε.Ε.Ν."
      ],
      note: "Η περίπτωση αφορά κατόχους απόφασης αναγνώρισης επαγγελματικών προσόντων από το Αυτοτελές Τμήμα Εφαρμογής της Ευρωπαϊκής Νομοθεσίας."
    },

    ateen_professional_equivalence: {
      title: "Απόφαση αναγνώρισης επαγγελματικής ισοδυναμίας από το Α.Τ.Ε.Ε.Ν.",
      documents: [
        "Απόφαση αναγνώρισης επαγγελματικής ισοδυναμίας τίτλου τυπικής ανώτατης εκπαίδευσης από το Α.Τ.Ε.Ε.Ν."
      ],
      note: "Η περίπτωση αφορά κατόχους απόφασης αναγνώρισης επαγγελματικής ισοδυναμίας από το Α.Τ.Ε.Ε.Ν."
    },

    unknown: {
      title: "Δεν είναι σαφής η περίπτωση εξαίρεσης",
      documents: [
        "Όποια πράξη, απόφαση ή βεβαίωση διαθέτεις σχετικά με επαγγελματική αναγνώριση."
      ],
      note: "Χρειάζεται περαιτέρω έλεγχος για να διαπιστωθεί αν εμπίπτεις πράγματι σε εξαίρεση και ποιο δικαιολογητικό πρέπει να προσκομιστεί."
    }
  };

  return details[value] || null;
}

  function showDocuments() {
    const titleType = valueOf("titleType");

    if (!titleType) {
      showResult(`
        <h2>Συμπλήρωσε πρώτα την επιλογή τίτλου.</h2>
        <p>Παρακαλώ επίλεξε τι είδους τίτλο θέλεις να δηλώσεις.</p>
      `);
      return;
    }

    let documents = [];
    let warnings = [];
    let successMessages = [];
    let infoMessages = [];

    if (titleType === "none") {
      showResult(`
        <h2>Δεν απαιτείται πρόσθετο δικαιολογητικό τίτλου.</h2>
        <p>
          Αφού δεν δηλώνεις μεταπτυχιακό, διδακτορικό ή integrated master,
          δεν προκύπτει πρόσθετο δικαιολογητικό για αυτή την κατηγορία.
        </p>
      `);
      return;
    }

    if (titleType === "msc_gr" || titleType === "phd_gr") {
      const greekTitleStatus = valueOf("greekTitleStatus");

      if (!greekTitleStatus) {
        showResult(`
          <h2>Λείπει απάντηση.</h2>
          <p>Παρακαλώ δήλωσε αν έχει εκδοθεί ο τίτλος ή αν εκκρεμεί ορκωμοσία.</p>
        `);
        return;
      }

      if (greekTitleStatus === "issued") {
        if (titleType === "msc_gr") {
          documents.push("Αντίγραφο του μεταπτυχιακού τίτλου σπουδών.");
        }

        if (titleType === "phd_gr") {
          documents.push("Αντίγραφο του διδακτορικού διπλώματος.");
        }
      }

      if (greekTitleStatus === "pending_oath") {
        if (titleType === "msc_gr") {
          documents.push("Βεβαίωση από το Α.Ε.Ι. ότι έχεις αποκτήσει τον μεταπτυχιακό τίτλο.");
          documents.push("Η βεβαίωση πρέπει να αναφέρει την ημερομηνία κτήσης του μεταπτυχιακού τίτλου.");
        }

        if (titleType === "phd_gr") {
          documents.push("Βεβαίωση από τη Γραμματεία του οικείου Α.Ε.Ι. ότι έχεις αποκτήσει το διδακτορικό δίπλωμα.");
          documents.push("Η βεβαίωση πρέπει να αναφέρει την ημερομηνία επιτυχούς υποστήριξης του θέματος της διδακτορικής διατριβής.");
        }

        warnings.push("Η βεβαίωση πρέπει να έχει εκδοθεί σύμφωνα με όσα ορίζει η προκήρυξη και να καλύπτει τον χρόνο κτήσης του τίτλου.");
      }
    }

    if (titleType === "integrated_master") {
      const integratedDecision = valueOf("integratedDecision");
      const departmentNameDifferent = valueOf("departmentNameDifferent");

      if (!integratedDecision || !departmentNameDifferent) {
        showResult(`
          <h2>Λείπουν απαντήσεις.</h2>
          <p>Παρακαλώ απάντησε στις ερωτήσεις για το integrated master.</p>
        `);
        return;
      }

      documents.push("Τίτλος σπουδών / πτυχίο του Τμήματος με ενιαίο και αδιάσπαστο τίτλο σπουδών μεταπτυχιακού επιπέδου, εφόσον τον δηλώνεις για μοριοδότηση.");

      if (integratedDecision === "yes") {
        successMessages.push("Δεν απαιτείται να προσκομίσεις τη διαπιστωτική απόφαση, εφόσον αυτή έχει εκδοθεί και δημοσιευθεί μέχρι τη λήξη της προθεσμίας υποβολής της αίτησης.");
      }

      if (integratedDecision === "no") {
        warnings.push("Αν δεν έχει δημοσιευθεί η σχετική διαπιστωτική απόφαση μέχρι τη λήξη της προθεσμίας, χρειάζεται ιδιαίτερη προσοχή γιατί μπορεί να μη ληφθεί υπόψη ως integrated master.");
      }

      if (integratedDecision === "unknown") {
        warnings.push("Χρειάζεται να ελέγξεις αν υπάρχει δημοσιευμένη διαπιστωτική απόφαση για το Τμήμα σου.");
      }

      if (departmentNameDifferent === "yes") {
        documents.push("Βεβαίωση από το αρμόδιο Τμήμα ότι το πτυχίο σου αντιστοιχεί σε αυτό που αναφέρεται στη σχετική διαπιστωτική απόφαση.");
      }

      if (departmentNameDifferent === "unknown") {
        warnings.push("Χρειάζεται να ελέγξεις αν η ονομασία του Τμήματος στο πτυχίο σου ταυτίζεται με την ονομασία που αναφέρεται στη διαπιστωτική απόφαση.");
      }
    }

	if (titleType === "joint_msc") {
	  const jointMscStatus = valueOf("jointMscStatus");
	  const jointMscProof = valueOf("jointMscProof");

	  if (!jointMscStatus || !jointMscProof) {
		showResult(`
		  <h2>Λείπουν απαντήσεις.</h2>
		  <p>Παρακαλώ απάντησε στις ερωτήσεις για το κοινό Πρόγραμμα Μεταπτυχιακών Σπουδών.</p>
		`);
		return;
	  }

	  if (jointMscStatus === "issued") {
		documents.push("Αντίγραφο του μεταπτυχιακού τίτλου Ειδίκευσης κοινού Προγράμματος Μεταπτυχιακών Σπουδών.");
	  }

	  if (jointMscStatus === "pending_oath") {
		documents.push("Βεβαίωση από το αρμόδιο Α.Ε.Ι. ότι έχεις αποκτήσει τον μεταπτυχιακό τίτλο.");
		documents.push("Η βεβαίωση πρέπει να αναφέρει την ημερομηνία κτήσης του μεταπτυχιακού τίτλου.");
	  }

	  if (jointMscProof === "yes") {
		successMessages.push(
		  "Για μεταπτυχιακό τίτλο Ειδίκευσης κοινού Προγράμματος Μεταπτυχιακών Σπουδών μεταξύ Πανεπιστημίων της ημεδαπής και της αλλοδαπής δεν απαιτείται προσκόμιση ισοτιμίας από Δ.Ο.Α.Τ.Α.Π."
		);
	  }

	  if (jointMscProof === "no") {
		warnings.push(
		  "Αν δεν προκύπτει ότι πρόκειται για κοινό Π.Μ.Σ. μεταξύ Πανεπιστημίου της ημεδαπής και Πανεπιστημίου της αλλοδαπής, χρειάζεται ιδιαίτερη προσοχή. Μπορεί να απαιτηθεί διαφορετικός έλεγχος ή πρόσθετο δικαιολογητικό."
		);
	  }

	  if (jointMscProof === "unknown") {
		warnings.push(
		  "Χρειάζεται να ελέγξεις αν ο τίτλος ή σχετική βεβαίωση αποδεικνύει ότι πρόκειται για κοινό Π.Μ.Σ. μεταξύ Πανεπιστημίου της ημεδαπής και Πανεπιστημίου της αλλοδαπής."
		);
	  }

	  documents.push("Επίσημη μετάφραση του τίτλου ή των σχετικών εγγράφων, όπου απαιτείται.");
	}






		if (titleType === "msc_foreign" || titleType === "phd_foreign") {
	  const foreignRecognition = valueOf("foreignRecognition");

	  if (!foreignRecognition) {
		showResult(`
		  <h2>Λείπει απάντηση.</h2>
		  <p>Παρακαλώ δήλωσε τι ισχύει για την αναγνώριση του τίτλου αλλοδαπής.</p>
		`);
		return;
	  }

	  if (foreignRecognition === "academic_recognition") {
		documents.push(
		  "Πράξη Ακαδημαϊκής Ισοδυναμίας από τον Δ.Ο.Α.Τ.Α.Π. ή Πιστοποιητικό Αναγνώρισης από τον Δ.Ο.Α.Τ.Α.Π. περί ισοτιμίας ή Πράξη Αναγνώρισης του τίτλου από το ΔΙ.Κ.Α.Τ.Σ.Α."
		);

		infoMessages.push(
		  "Για μεταπτυχιακό ή διδακτορικό τίτλο της αλλοδαπής, όταν υπάρχει ήδη αναγνώριση, το κρίσιμο δικαιολογητικό είναι η πράξη ή το πιστοποιητικό αναγνώρισης. Καλό είναι ο αρχικός τίτλος και η μετάφρασή του να υπάρχουν διαθέσιμα στον προσωπικό φάκελο, αλλά δεν τα εμφανίζουμε εδώ ως κύριο απαιτούμενο δικαιολογητικό."
		);
	  }

	  if (foreignRecognition === "exception") {
		const foreignExemption = valueOf("foreignExemption");

		if (!foreignExemption) {
		  showResult(`
			<h2>Λείπει απάντηση.</h2>
			<p>Παρακαλώ επίλεξε σε ποια περίπτωση εξαίρεσης ανήκεις.</p>
		  `);
		  return;
		}

		const exemptionDetails = getForeignExemptionDetails(foreignExemption);

		if (exemptionDetails) {
		  infoMessages.push(
			"<strong>Περίπτωση εξαίρεσης:</strong> " + exemptionDetails.title
		  );

		  exemptionDetails.documents.forEach(item => {
			if (!documents.includes(item)) {
			  documents.push(item);
			}
		  });

		  warnings.push(exemptionDetails.note);
		}

		successMessages.push(
		  "Η εξαίρεση σημαίνει ότι ενδέχεται να μην απαιτείται πράξη ακαδημαϊκής αναγνώρισης / ισοτιμίας / αντιστοιχίας από Δ.Ο.Α.Τ.Α.Π. Δεν σημαίνει όμως ότι δεν απαιτείται κανένα δικαιολογητικό. Πρέπει να προσκομιστεί η αντίστοιχη πράξη ή απόφαση επαγγελματικής αναγνώρισης."
		);
	  }

	  if (foreignRecognition === "pending") {
		documents.push("Νομίμως επικυρωμένο φωτοαντίγραφο του προς αναγνώριση τίτλου σπουδών.");
		documents.push("Επίσημη μετάφραση του προς αναγνώριση τίτλου σπουδών.");
		documents.push("Φωτοαντίγραφο της πρωτοκολλημένης ή καταχωρισμένης αίτησης αναγνώρισης προς Δ.Ο.Α.Τ.Α.Π. ή προς το αρμόδιο όργανο.");
		documents.push("Υποβολή / μεταφόρτωση των παραπάνω μέσω αιτήματος επικαιροποίησης στοιχείου στο Ο.Π.ΣΥ.Δ.");

		warnings.push(
		  "Τα πιστοποιητικά αναγνώρισης των τίτλων αλλοδαπής πρέπει να έχουν εκδοθεί από τον αρμόδιο φορέα εντός των προθεσμιών που ορίζει η προκήρυξη."
		);
	  }

	  if (foreignRecognition === "none") {
		warnings.push(
		  "Δεν προκύπτει πλήρης φάκελος για τίτλο αλλοδαπής, επειδή δεν υπάρχει πράξη αναγνώρισης, δεν δηλώθηκε εξαίρεση και δεν υπάρχει εκκρεμής αίτηση αναγνώρισης."
		);
	  }

	  if (foreignRecognition === "unknown") {
		warnings.push(
		  "Χρειάζεται περαιτέρω έλεγχος για το αν ο τίτλος σου απαιτεί πράξη αναγνώρισης από Δ.Ο.Α.Τ.Α.Π. ή αν εμπίπτει σε κάποια από τις εξαιρέσεις της προκήρυξης."
		);
	  }
	}

    let html = `
      <h2>Ενδεικτικά δικαιολογητικά που χρειάζεσαι</h2>
      ${makeList(documents)}
    `;

    if (infoMessages.length > 0) {
      html += `
        <div class="info">
          ${infoMessages.join("<br><br>")}
        </div>
      `;
    }

    if (successMessages.length > 0) {
      html += `
        <div class="success">
          ${successMessages.join("<br><br>")}
        </div>
      `;
    }

    if (warnings.length > 0) {
      html += `
        <div class="warning">
          Προσοχή:<br>
          ${warnings.map(w => "• " + w).join("<br>")}
        </div>
      `;
    }

    showResult(html);
  }
</script>


<section class="edu-source-card" aria-labelledby="sourcesTitle">
  <h2 id="sourcesTitle">Πηγές / Νομική βάση</h2>
  <p>Προκηρύξεις Α.Σ.Ε.Π. <strong>1ΓΕ/2026</strong> και <strong>2ΓΕ/2026</strong>, ιδίως τα κεφάλαια για τα απαιτούμενα δικαιολογητικά και τις προϋποθέσεις αναγνώρισης τίτλων σπουδών ημεδαπής και αλλοδαπής. Το εργαλείο είναι βοηθητικός οδηγός και δεν υποκαθιστά τον έλεγχο Ο.Π.ΣΥ.Δ./Α.Σ.Ε.Π.</p>
  <div class="source-links"><a href="https://info.asep.gr/node/78700" target="_blank" rel="noopener noreferrer">1ΓΕ/2026 — ΑΣΕΠ ↗</a> <a href="https://info.asep.gr/node/78701" target="_blank" rel="noopener noreferrer">2ΓΕ/2026 — ΑΣΕΠ ↗</a></div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
  <script src="assets/common.js?v=3.20.10"></script>
</body>
</html>