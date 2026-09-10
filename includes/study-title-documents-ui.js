/*
 * Browser UI controller for dikaiologitika-titlon-spoudon.php.
 * The PHP page owns content/markup; this file owns conditional questions,
 * document-result rendering and interaction wiring.
 */
(function(global){
  "use strict";
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

  function init() {
    const titleType = document.getElementById("titleType");
    const foreignRecognition = document.getElementById("foreignRecognition");
    const showButton = document.getElementById("showDocumentsBtn");
    if (!titleType || !foreignRecognition || !showButton) return;

    titleType.addEventListener("change", updateQuestions);
    foreignRecognition.addEventListener("change", updateForeignExemptionQuestion);
    showButton.addEventListener("click", showDocuments);
    updateQuestions();
    updateForeignExemptionQuestion();
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", init);
  } else {
    init();
  }

  global.StudyTitleDocumentsUI = Object.freeze({
    init: init,
    updateQuestions: updateQuestions,
    updateForeignExemptionQuestion: updateForeignExemptionQuestion,
    showDocuments: showDocuments
  });
})(window);
