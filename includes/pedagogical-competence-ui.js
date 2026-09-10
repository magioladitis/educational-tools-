/*
 * Browser UI controller for paidagogiki-eparkeia.php.
 * The PHP page owns content/markup; this file owns conditional visibility,
 * result rendering and interaction wiring.
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

  function updateVisibility() {
    const proofType = valueOf("proofType");

    hide("pedagogicalDepartmentQuestions");
    hide("epathQuestions");
    hide("professorSchoolQuestions");

    document.getElementById("result").style.display = "none";

    if (proofType === "pedagogical_department") {
      show("pedagogicalDepartmentQuestions");
    }

    if (proofType === "epath") {
      show("epathQuestions");
    }

    if (proofType === "professor_school") {
      show("professorSchoolQuestions");
    }
  }

  function showResult(type, html) {
    const result = document.getElementById("result");
    result.style.display = "block";
    result.className = "result " + type;
    result.innerHTML = html;
  }

  function makeList(items) {
    if (!items.length) return "";
    return "<ul>" + items.map(item => "<li>" + item + "</li>").join("") + "</ul>";
  }

  function opsydNote(opsyd) {
    if (opsyd === "yes") {
      return "Έχεις δηλώσει ότι το σχετικό αποδεικτικό εμφανίζεται ή έχει καταχωριστεί στον Ο.Π.ΣΥ.Δ.";
    }

    if (opsyd === "no") {
      return "Χρειάζεται να ελέγξεις αν πρέπει να υποβάλεις αίτημα επικαιροποίησης στοιχείων στον Ο.Π.ΣΥ.Δ. και να μεταφορτώσεις το σχετικό δικαιολογητικό.";
    }

    return "Χρειάζεται έλεγχος στον Ο.Π.ΣΥ.Δ. για το αν το σχετικό αποδεικτικό έχει καταχωριστεί σωστά.";
  }

  function checkEparkeia() {
    const specialty = valueOf("specialty");
    const proofType = valueOf("proofType");
    const opsyd = valueOf("opsyd");

    if (!specialty || !proofType || !opsyd) {
      showResult(
        "unknown",
        "<h2>Λείπουν στοιχεία</h2><p>Παρακαλώ απάντησε στις βασικές ερωτήσεις.</p>"
      );
      return;
    }

    const documents = [];
    const notes = [];
    notes.push(opsydNote(opsyd));

    if (proofType === "aei_certificate") {
      documents.push("Βεβαίωση Παιδαγωγικής και Διδακτικής Επάρκειας από Τμήμα Α.Ε.Ι. ή από συνεργαζόμενα Τμήματα Α.Ε.Ι.");
      documents.push("Η βεβαίωση πρέπει να προκύπτει μετά από παρακολούθηση ομάδας μαθημάτων ή ειδικού προγράμματος σπουδών.");
      notes.push("Στις 1ΓΕ/2026 και 2ΓΕ/2026 η σχετική μεταβατική δυνατότητα αφορά αποφοίτους που είχαν εισαχθεί έως και το ακαδημαϊκό έτος 2026-2027 σε Τμήμα/Σχολή που χορηγούσε την πιστοποίηση κατά τον χρόνο εισαγωγής.");

      showResult(
        "positive",
        `
          <h2>Φαίνεται ότι διαθέτεις Παιδαγωγική και Διδακτική Επάρκεια</h2>
          <p>
            Η βεβαίωση Α.Ε.Ι. είναι μία από τις ρητές περιπτώσεις αποδεικτικών Π.Δ.Ε.
          </p>

          <div class="note-box">
            <strong>Ενδεικτικά δικαιολογητικά:</strong>
            ${makeList(documents)}
          </div>

          <div class="note-box">
            <strong>Προσοχή:</strong>
            ${makeList(notes)}
          </div>
        `
      );
      return;
    }

    if (proofType === "education_msc_phd") {
      documents.push("Μεταπτυχιακός τίτλος σπουδών στις επιστήμες της αγωγής ή διδακτορικό δίπλωμα στις επιστήμες της αγωγής.");
      documents.push("Αν πρόκειται για τίτλο αλλοδαπής, ελέγχεται και το ζήτημα της αναγνώρισης, όπου απαιτείται.");

      showResult(
        "positive",
        `
          <h2>Φαίνεται ότι διαθέτεις Παιδαγωγική και Διδακτική Επάρκεια</h2>
          <p>
            Δήλωσες μεταπτυχιακό ή διδακτορικό στις επιστήμες της αγωγής, που προβλέπεται ως αποδεικτικό Π.Δ.Ε.
          </p>

          <div class="note-box">
            <strong>Ενδεικτικά δικαιολογητικά:</strong>
            ${makeList(documents)}
          </div>

          <div class="note-box">
            <strong>Προσοχή:</strong>
            ${makeList(notes)}
          </div>
        `
      );
      return;
    }

    if (proofType === "old_certificate") {
      documents.push("Πιστοποιητικό παιδαγωγικής επάρκειας της παρ. 5 του άρθρου 4 του ν. 3027/2002.");

      showResult(
        "positive",
        `
          <h2>Φαίνεται ότι διαθέτεις Παιδαγωγική και Διδακτική Επάρκεια</h2>
          <p>
            Δήλωσες παλαιό πιστοποιητικό παιδαγωγικής επάρκειας που προβλέπεται στο Παράρτημα Ε΄.
          </p>

          <div class="note-box">
            <strong>Ενδεικτικά δικαιολογητικά:</strong>
            ${makeList(documents)}
          </div>

          <div class="note-box">
            <strong>Προσοχή:</strong>
            ${makeList(notes)}
          </div>
        `
      );
      return;
    }

    if (proofType === "pedagogical_department") {
      const departmentType = valueOf("pedagogicalDepartmentType");

      if (!departmentType) {
        showResult(
          "unknown",
          "<h2>Λείπει στοιχείο</h2><p>Παρακαλώ επίλεξε την κατηγορία Παιδαγωγικού Τμήματος.</p>"
        );
        return;
      }

      if (departmentType === "unknown") {
        showResult(
          "unknown",
          `
            <h2>Χρειάζεται περαιτέρω έλεγχος</h2>
            <p>
              Δεν είναι σαφές αν ο τίτλος σου ανήκει στις περιπτώσεις Παιδαγωγικών Τμημάτων
              που πιστοποιούν εξ ορισμού Π.Δ.Ε.
            </p>

            <div class="note-box">
              <strong>Τι να ελέγξεις:</strong>
              <ul>
                <li>Την ακριβή ονομασία του Τμήματος στο πτυχίο σου.</li>
                <li>Αν το Τμήμα περιλαμβάνεται στις περιπτώσεις του Παραρτήματος Ε΄.</li>
                <li>Την καταχώριση στον Ο.Π.ΣΥ.Δ.</li>
              </ul>
            </div>
          `
        );
        return;
      }

      documents.push("Πτυχίο Παιδαγωγικού Τμήματος ή αντίστοιχου Τμήματος που αναφέρεται στο Παράρτημα Ε΄.");

      showResult(
        "positive",
        `
          <h2>Φαίνεται ότι διαθέτεις Παιδαγωγική και Διδακτική Επάρκεια</h2>
          <p>
            Οι περιπτώσεις Παιδαγωγικών Τμημάτων του Παραρτήματος Ε΄ πιστοποιούν την Π.Δ.Ε.
            εξ ορισμού με την αποφοίτηση από το αντίστοιχο Τμήμα.
          </p>

          <div class="note-box">
            <strong>Ενδεικτικά δικαιολογητικά:</strong>
            ${makeList(documents)}
          </div>

          <div class="note-box">
            <strong>Προσοχή:</strong>
            ${makeList(notes)}
          </div>
        `
      );
      return;
    }

    if (proofType === "aspaite") {
      documents.push("Πτυχίο Α.Σ.ΠΑΙ.Τ.Ε.");

      showResult(
        "positive",
        `
          <h2>Φαίνεται ότι διαθέτεις Παιδαγωγική και Διδακτική Επάρκεια</h2>
          <p>
            Το πτυχίο Α.Σ.ΠΑΙ.Τ.Ε. περιλαμβάνεται στις περιπτώσεις που πιστοποιούν Π.Δ.Ε.
            εξ ορισμού.
          </p>

          <div class="note-box">
            <strong>Ενδεικτικά δικαιολογητικά:</strong>
            ${makeList(documents)}
          </div>

          <div class="note-box">
            <strong>Προσοχή:</strong>
            ${makeList(notes)}
          </div>
        `
      );
      return;
    }

    if (proofType === "aspaite_eppaik") {
      documents.push("Πιστοποιητικό Παιδαγωγικής και Διδακτικής Επάρκειας του Ετήσιου Προγράμματος Παιδαγωγικής Κατάρτισης (ΕΠΠΑΙΚ) της Α.Σ.ΠΑΙ.Τ.Ε. / πρώην ΠΑΤΕΣ–ΣΕΛΕΤΕ.");

      showResult(
        "positive",
        `
          <h2>Φαίνεται ότι διαθέτεις Παιδαγωγική και Διδακτική Επάρκεια</h2>
          <p>Δήλωσες πιστοποιητικό ΕΠΠΑΙΚ της Α.Σ.ΠΑΙ.Τ.Ε., το οποίο αποτελεί αποδεικτικό Π.Δ.Ε.</p>
          <div class="note-box"><strong>Ενδεικτικό δικαιολογητικό:</strong>${makeList(documents)}</div>
          <div class="note-box"><strong>Προσοχή:</strong>${makeList(notes)}</div>
        `
      );
      return;
    }

    if (proofType === "article99") {
      documents.push("Πιστοποιητικό Παιδαγωγικής και Διδακτικής Επάρκειας του άρθρου 99 του ν. 4957/2022.");
      documents.push("Το πιστοποιητικό πρέπει να έχει χορηγηθεί βάσει ειδικού προγράμματος σπουδών από Α.Ε.Ι.");

      showResult(
        "positive",
        `
          <h2>Φαίνεται ότι διαθέτεις Παιδαγωγική και Διδακτική Επάρκεια</h2>
          <p>
            Δήλωσες πιστοποιητικό Π.Δ.Ε. του άρθρου 99 του ν. 4957/2022, που προβλέπεται στο Παράρτημα Ε΄.
          </p>

          <div class="note-box">
            <strong>Ενδεικτικά δικαιολογητικά:</strong>
            ${makeList(documents)}
          </div>

          <div class="note-box">
            <strong>Προσοχή:</strong>
            ${makeList(notes)}
          </div>
        `
      );
      return;
    }

    if (proofType === "epath") {
      const epathDate = valueOf("epathDate");

      if (!epathDate) {
        showResult(
          "unknown",
          "<h2>Λείπει στοιχείο</h2><p>Παρακαλώ δήλωσε την ημερομηνία κτήσης του πτυχίου Ε.Π.Α.Θ.</p>"
        );
        return;
      }

      if (epathDate === "before") {
        documents.push("Πτυχίο Ειδικής Παιδαγωγικής Ακαδημίας Θεσσαλονίκης με ημερομηνία κτήσης πριν από 12/6/2018.");

        showResult(
          "positive",
          `
            <h2>Φαίνεται ότι διαθέτεις Παιδαγωγική και Διδακτική Επάρκεια</h2>
            <p>
              Το πτυχίο Ε.Π.Α.Θ. με ημερομηνία κτήσης προγενέστερη της 12ης Ιουνίου 2018
              περιλαμβάνεται στις περιπτώσεις εξ ορισμού Π.Δ.Ε.
            </p>

            <div class="note-box">
              <strong>Ενδεικτικά δικαιολογητικά:</strong>
              ${makeList(documents)}
            </div>

            <div class="note-box">
              <strong>Προσοχή:</strong>
              ${makeList(notes)}
            </div>
          `
        );
        return;
      }

      if (epathDate === "after") {
        showResult(
          "warning",
          `
            <h2>Δεν φαίνεται να καλύπτεται η περίπτωση Ε.Π.Α.Θ.</h2>
            <p>
              Για την περίπτωση Ε.Π.Α.Θ. το Παράρτημα Ε΄ αναφέρεται σε πτυχίο με ημερομηνία
              κτήσης προγενέστερη της 12ης Ιουνίου 2018.
            </p>

            <div class="note-box">
              <strong>Τι να ελέγξεις:</strong>
              <ul>
                <li>Αν διαθέτεις άλλο αποδεικτικό Π.Δ.Ε.</li>
                <li>Αν υπάρχει σχετική καταχώριση ή δυνατότητα επικαιροποίησης στον Ο.Π.ΣΥ.Δ.</li>
              </ul>
            </div>
          `
        );
        return;
      }

      showResult(
        "unknown",
        `
          <h2>Χρειάζεται περαιτέρω έλεγχος</h2>
          <p>
            Δεν είναι σαφές αν το πτυχίο Ε.Π.Α.Θ. πληροί τη χρονική προϋπόθεση του Παραρτήματος Ε΄.
          </p>
        `
      );
      return;
    }

    if (proofType === "professor_school") {
      const entryYear = valueOf("entryYear");
      const graduationYear = valueOf("graduationYear");

      if (!entryYear || !graduationYear) {
        showResult(
          "unknown",
          "<h2>Λείπουν στοιχεία</h2><p>Παρακαλώ απάντησε στις ερωτήσεις για το έτος εισαγωγής και το έτος κτήσης πτυχίου.</p>"
        );
        return;
      }

      if (entryYear === "up_to_2014") {
        documents.push("Πτυχίο καθηγητικής σχολής.");
        documents.push("Βεβαίωση εγγραφής στο Τμήμα, για την απόδειξη ότι η εισαγωγή έγινε έως και το ακαδημαϊκό έτος 2014-2015.");

        showResult(
          "positive",
          `
            <h2>Φαίνεται ότι διαθέτεις Παιδαγωγική και Διδακτική Επάρκεια</h2>
            <p>
              Για πτυχίο καθηγητικής σχολής με έτος εισαγωγής μέχρι και το ακαδημαϊκό έτος 2014-2015,
              η Π.Δ.Ε. μπορεί να πιστοποιείται εξ ορισμού.
            </p>

            <div class="note-box">
              <strong>Ενδεικτικά δικαιολογητικά:</strong>
              ${makeList(documents)}
            </div>

            <div class="note-box">
              <strong>Προσοχή:</strong>
              ${makeList(notes)}
            </div>
          `
        );
        return;
      }

      if (graduationYear === "up_to_2017") {
        documents.push("Πτυχίο καθηγητικής σχολής με κτήση έως και το ακαδημαϊκό έτος 2017-2018.");

        showResult(
          "positive",
          `
            <h2>Φαίνεται ότι διαθέτεις Παιδαγωγική και Διδακτική Επάρκεια</h2>
            <p>
              Για πτυχίο καθηγητικής σχολής με κτήση έως και το ακαδημαϊκό έτος 2017-2018,
              η Π.Δ.Ε. μπορεί να πιστοποιείται εξ ορισμού.
            </p>

            <div class="note-box">
              <strong>Ενδεικτικά δικαιολογητικά:</strong>
              ${makeList(documents)}
            </div>

            <div class="note-box">
              <strong>Προσοχή:</strong>
              ${makeList(notes)}
            </div>
          `
        );
        return;
      }

      if (entryYear === "from_2015" && graduationYear === "from_2018") {
        showResult(
          "warning",
          `
            <h2>Δεν φαίνεται να καλύπτεσαι αυτομάτως μόνο από το πτυχίο</h2>
            <p>
              Για όσους/ες εισήχθησαν από το ακαδημαϊκό έτος 2015-2016 και μετά
              και είναι πτυχιούχοι από το ακαδημαϊκό έτος 2018-2019 και εφεξής,
              δεν προκύπτει αυτομάτως Π.Δ.Ε. μόνο από το πτυχίο καθηγητικής σχολής.
            </p>

            <div class="note-box">
              <strong>Τι χρειάζεται να ελέγξεις:</strong>
              <ul>
                <li>Αν διαθέτεις βεβαίωση Π.Δ.Ε. από Α.Ε.Ι.</li>
                <li>Αν έχεις μεταπτυχιακό ή διδακτορικό στις επιστήμες της αγωγής.</li>
                <li>Αν διαθέτεις πιστοποιητικό Π.Δ.Ε. του άρθρου 99 του ν. 4957/2022.</li>
                <li>Αν υπάρχει άλλο αποδεικτικό Π.Δ.Ε. που προβλέπεται στο Παράρτημα Ε΄.</li>
                <li>Αν το σχετικό αποδεικτικό είναι καταχωρισμένο στον Ο.Π.ΣΥ.Δ.</li>
              </ul>
            </div>
          `
        );
        return;
      }

      showResult(
        "unknown",
        `
          <h2>Χρειάζεται περαιτέρω έλεγχος</h2>
          <p>
            Οι απαντήσεις για το έτος εισαγωγής ή το έτος κτήσης πτυχίου δεν αρκούν
            για ασφαλές συμπέρασμα.
          </p>

          <div class="note-box">
            Έλεγξε το Παράρτημα Ε΄, το Τμήμα αποφοίτησης και την καταχώριση στον Ο.Π.ΣΥ.Δ.
          </div>
        `
      );
      return;
    }

    if (proofType === "none") {
      showResult(
        "negative",
        `
          <h2>Δεν φαίνεται να προκύπτει Παιδαγωγική και Διδακτική Επάρκεια</h2>
          <p>
            Με βάση τις απαντήσεις σου, δεν δήλωσες κάποιο από τα αποδεικτικά Π.Δ.Ε.
            που προβλέπονται στο Παράρτημα Ε΄.
          </p>

          <div class="note-box">
            <strong>Τι να ελέγξεις:</strong>
            <ul>
              <li>Αν το Τμήμα σου χορηγεί σχετική βεβαίωση Π.Δ.Ε.</li>
              <li>Αν έχεις μεταπτυχιακό ή διδακτορικό στις επιστήμες της αγωγής.</li>
              <li>Αν έχεις πιστοποιητικό Π.Δ.Ε. από Α.Ε.Ι. ή άλλο προβλεπόμενο αποδεικτικό.</li>
              <li>Αν υπάρχει σχετική καταχώριση στον Ο.Π.ΣΥ.Δ.</li>
            </ul>
          </div>
        `
      );
      return;
    }

    showResult(
      "unknown",
      `
        <h2>Χρειάζεται περαιτέρω έλεγχος</h2>
        <p>
          Δεν είναι σαφές με ποιο αποδεικτικό πιστοποιείται η Παιδαγωγική και Διδακτική Επάρκεια.
        </p>

        <div class="note-box">
          Καλό είναι να ελέγξεις το Παράρτημα Ε΄ της προκήρυξης και την καταχώριση στον Ο.Π.ΣΥ.Δ.
        </div>
      `
    );
  }

  function init() {
    const proofType = document.getElementById("proofType");
    const checkButton = document.getElementById("checkEparkeiaBtn");
    if (!proofType || !checkButton) return;

    proofType.addEventListener("change", updateVisibility);
    checkButton.addEventListener("click", checkEparkeia);
    updateVisibility();
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", init);
  } else {
    init();
  }

  global.PedagogicalCompetenceUI = Object.freeze({
    init: init,
    updateVisibility: updateVisibility,
    checkEparkeia: checkEparkeia
  });
})(window);
