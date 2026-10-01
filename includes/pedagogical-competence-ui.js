/*
 * Browser UI controller for paidagogiki-eparkeia.php.
 * Core rule: target specialty and credentials are separate concepts. Every credential is
 * evaluated independently; one valid route is enough for an overall positive result.
 */
(function(global){
  "use strict";

  const POSITIVE = "positive";
  const WARNING = "warning";
  const NEGATIVE = "negative";
  const UNKNOWN = "unknown";
  const MAX_CREDENTIALS = 6;
  const ADD_CREDENTIAL_LABEL = "+ Προσθήκη άλλου τίτλου / αποδεικτικού";

  function readReferenceData() {
    if (global.PedagogicalCompetenceReference && global.PedagogicalCompetenceReference.appendix_named_programs) {
      return global.PedagogicalCompetenceReference;
    }
    if (typeof document === "undefined" || typeof document.getElementById !== "function") return {};
    const el = document.getElementById("pedagogicalCompetenceReference");
    if (!el || typeof el.getAttribute !== "function") return {};
    const raw = el.getAttribute("data-reference-json") || "";
    if (!raw) return {};
    try {
      const parsed = JSON.parse(raw);
      return parsed && typeof parsed === "object" ? parsed : {};
    } catch (error) {
      return {};
    }
  }

  const REFERENCE = readReferenceData();

  function referenceProgram(row, expectedSection) {
    const programs = REFERENCE.appendix_named_programs || {};
    const program = row && Object.prototype.hasOwnProperty.call(programs, String(row)) ? programs[String(row)] : null;
    if (!program) return null;
    if (expectedSection && program.section !== expectedSection) return null;
    return program;
  }

  function byId(id) { return document.getElementById(id); }
  function valueOf(id) { const el = byId(id); return el ? el.value : ""; }
  function safeQsa(root, selector) { return root && typeof root.querySelectorAll === "function" ? Array.from(root.querySelectorAll(selector)) : []; }
  function safeQs(root, selector) { return root && typeof root.querySelector === "function" ? root.querySelector(selector) : null; }
  function roleValue(card, role, legacyId) {
    const el = safeQs(card, '[data-role="' + role + '"]') || (legacyId ? byId(legacyId) : null);
    return el ? el.value : "";
  }

  function toggleHidden(el, hidden) {
    if (!el || !el.classList) return;
    if (typeof el.classList.toggle === "function") el.classList.toggle("hidden", !!hidden);
    else if (hidden && typeof el.classList.add === "function") el.classList.add("hidden");
    else if (!hidden && typeof el.classList.remove === "function") el.classList.remove("hidden");
  }

  function setGuidance(el, tone, message) {
    if (!el) return;
    el.className = "ped-flow-guidance" + (tone ? " is-" + tone : "");
    el.textContent = message || "";
    toggleHidden(el, !message);
  }

  function normalizeSearchText(value) {
    let text = String(value || "").toLowerCase();
    if (typeof text.normalize === "function") text = text.normalize("NFD").replace(/[\u0300-\u036f]/g, "");
    return text;
  }

  function filteredPrograms(section, query) {
    const programs = REFERENCE.appendix_named_programs || {};
    const normalizedQuery = normalizeSearchText(query);
    return Object.keys(programs).filter(function(row){
      const program = programs[row];
      if (!program || program.section !== section) return false;
      if (!normalizedQuery) return true;
      return normalizeSearchText(program.title + " " + program.provider).indexOf(normalizedQuery) !== -1;
    }).map(function(row){ return { row: row, program: programs[row] }; });
  }

  function repopulateProgramSelect(card, section, searchRole, selectRole) {
    const search = safeQs(card, '[data-role="' + searchRole + '"]');
    const select = safeQs(card, '[data-role="' + selectRole + '"]');
    if (!search || !select || typeof document === "undefined") return;
    const current = select.value || "";
    const matches = filteredPrograms(section, search.value || "");
    while (select.firstChild) select.removeChild(select.firstChild);
    const placeholder = document.createElement("option");
    placeholder.value = ""; placeholder.textContent = "-- Επιλογή τίτλου --"; select.appendChild(placeholder);
    matches.forEach(function(item){
      const option = document.createElement("option");
      option.value = String(item.row);
      option.textContent = item.program.title + " — " + item.program.provider;
      select.appendChild(option);
    });
    const unknown = document.createElement("option");
    unknown.value = "unknown"; unknown.textContent = "Δεν βρίσκω / δεν γνωρίζω τον ακριβή τίτλο"; select.appendChild(unknown);
    const stillVisible = matches.some(function(item){ return String(item.row) === current; });
    if (current === "unknown" || stillVisible) select.value = current;
    else select.value = "";
  }

  function result(status, title, detail, extra) {
    return Object.assign({ status, title, detail, documents: [], notes: [] }, extra || {});
  }

  function programReferenceText(row, program) {
    return "Εγγραφή " + row + ": " + program.title + ". Φορέας: " + program.provider + ". Σχετική διάταξη/απόφαση: " + program.legal_basis + ". ΦΕΚ: " + program.fek + ".";
  }

  function evaluateCredentialValues(v) {
    const proofType = v.proofType || "";
    if (!proofType) return result(UNKNOWN, "Δεν έχει επιλεγεί αποδεικτικό", "Επίλεξε κατηγορία ή αφαίρεσε την κενή καταχώριση.");

    if (proofType === "aei_certificate") {
      const subtype = v.aeiCertificateSubtype || "";
      if (!subtype) return result(UNKNOWN, "Βεβαίωση / πιστοποιητικό Α.Ε.Ι. — λείπει τύπος", "Επίλεξε ποια μορφή βεβαίωσης ή πιστοποιητικού διαθέτεις.");

      if (subtype === "article99") {
        return result(POSITIVE, "Πιστοποιητικό άρθρου 99 ν. 4957/2022", "Το πιστοποιητικό ειδικού προγράμματος σπουδών Α.Ε.Ι. του άρθρου 99 αποτελεί προβλεπόμενο αποδεικτικό Π.Δ.Ε.");
      }

      if (subtype === "named_special_program") {
        if (!v.namedSpecialProgramRow || v.namedSpecialProgramRow === "unknown") {
          return result(UNKNOWN, "Παλαιότερο εγκεκριμένο πρόγραμμα Π.Δ.Ε. — χρειάζεται ταυτοποίηση", "Επίλεξε τον ακριβή τίτλο και φορέα από την ονομαστική βάση αναφοράς.");
        }
        const program = referenceProgram(v.namedSpecialProgramRow, "special_program");
        if (!program) return result(UNKNOWN, "Η εγγραφή δεν βρέθηκε στο canonical dataset", "Δεν δίνεται αποτέλεσμα μέχρι να διασταυρωθεί ξανά η συγκεκριμένη εγγραφή με την επίσημη πηγή.");
        const referenceDetail = programReferenceText(v.namedSpecialProgramRow, program);
        if (v.namedSpecialProgramExactMatch === "yes") return result(POSITIVE, "Βεβαίωση / πιστοποιητικό από εγκεκριμένο πρόγραμμα Π.Δ.Ε.", referenceDetail + " Δήλωσες ότι τίτλος και φορέας ταυτίζονται ακριβώς.");
        if (v.namedSpecialProgramExactMatch === "no") return result(WARNING, "Δεν υπάρχει ακριβής αντιστοίχιση με την επιλεγμένη εγγραφή", referenceDetail + " Αφού υπάρχει διαφορά στον τίτλο ή στον φορέα, η συγκεκριμένη επιλογή δεν χρησιμοποιείται ως θετική διαδρομή.");
        return result(UNKNOWN, "Χρειάζεται επιβεβαίωση ακριβούς τίτλου και φορέα", referenceDetail + " Επιβεβαίωσε την ακριβή αντιστοίχιση πριν δοθεί θετικό αποτέλεσμα.");
      }

      if (subtype === "standard") {
        /* New guided flow; aeiCertificateEligibility is kept as a backward-compatible fallback. */
        if (v.aeiEntryPeriod === "up_to_2026") {
          if (v.aeiCertifiedAtEntry === "yes") return result(POSITIVE, "Βεβαίωση Π.Δ.Ε. από Α.Ε.Ι.", "Δηλώθηκε εισαγωγή έως και το 2026–2027 και ότι το Τμήμα / η Σχολή χορηγούσε την πιστοποίηση Π.Δ.Ε. κατά τον χρόνο εισαγωγής.", {documents:["Βεβαίωση Παιδαγωγικής και Διδακτικής Επάρκειας Α.Ε.Ι."], notes:["Η χρονική προϋπόθεση προέρχεται από τη βάση 1ΓΕ/2026–2ΓΕ/2026. Σε άλλη διαδικασία ελέγχεται η αντίστοιχη προκήρυξη."]});
          if (v.aeiCertifiedAtEntry === "no") return result(WARNING, "Βεβαίωση Α.Ε.Ι. — δεν πληρούται η δεύτερη μεταβατική προϋπόθεση", "Παρότι η εισαγωγή είναι έως και το 2026–2027, δήλωσες ότι το Τμήμα / η Σχολή δεν χορηγούσε την πιστοποίηση κατά τον χρόνο εισαγωγής. Έλεγξε άλλη διαδρομή ή τους ειδικούς όρους της διαδικασίας.");
          return result(UNKNOWN, "Βεβαίωση Α.Ε.Ι. — χρειάζεται ακόμη μία επιβεβαίωση", "Η χρονική προϋπόθεση φαίνεται να καλύπτεται, αλλά πρέπει να επιβεβαιωθεί ότι το Τμήμα / η Σχολή χορηγούσε την πιστοποίηση κατά τον χρόνο εισαγωγής.");
        }
        if (v.aeiEntryPeriod === "from_2027") return result(WARNING, "Βεβαίωση Α.Ε.Ι. — η συγκεκριμένη μεταβατική περίπτωση δεν καλύπτει την εισαγωγή", "Η διαθέσιμη βάση 1ΓΕ/2026–2ΓΕ/2026 θέτει ως χρονικό όριο την εισαγωγή έως και το 2026–2027. Έλεγξε αν διαθέτεις πιστοποιητικό άρθρου 99 ή άλλη προβλεπόμενη διαδρομή.");
        if (v.aeiEntryPeriod === "unknown") return result(UNKNOWN, "Βεβαίωση Α.Ε.Ι. — χρειάζεται το έτος εισαγωγής", "Χωρίς το χρονικό στοιχείο δεν μπορεί να ελεγχθεί η μεταβατική περίπτωση της διαθέσιμης βάσης αναφοράς.");
        if (v.aeiCertificateEligibility === "yes") return result(POSITIVE, "Βεβαίωση Π.Δ.Ε. από Α.Ε.Ι.", "Δηλώθηκε ότι πληρούνται οι μεταβατικές προϋποθέσεις της διαθέσιμης βάσης αναφοράς.");
        if (v.aeiCertificateEligibility === "no") return result(WARNING, "Βεβαίωση Α.Ε.Ι. — χρειάζεται έλεγχος της διαδικασίας", "Η δηλωμένη περίπτωση δεν φαίνεται να πληροί τη μεταβατική προϋπόθεση της διαθέσιμης βάσης αναφοράς.");
        return result(UNKNOWN, "Βεβαίωση Α.Ε.Ι. — λείπει κρίσιμο χρονικό στοιχείο", "Δήλωσε πρώτα πότε έγινε η εισαγωγή στο συγκεκριμένο Τμήμα / Σχολή.");
      }

      return result(UNKNOWN, "Βεβαίωση / πιστοποιητικό Α.Ε.Ι. — άγνωστος τύπος", "Χρειάζεται περαιτέρω έλεγχος του συγκεκριμένου αποδεικτικού.");
    }

    if (proofType === "education_msc_phd") {
      const namedProgram = referenceProgram(v.namedPostgraduateRow, "postgraduate_prior");
      if (v.educationDegreeOrigin === "domestic") {
        if (v.domesticEducationEvidence === "yes") return result(POSITIVE, "Μεταπτυχιακός / διδακτορικός τίτλος στις επιστήμες της αγωγής", "Δηλώθηκε ότι μπορείς ήδη να τεκμηριώσεις ότι ο τίτλος ημεδαπής εμπίπτει στις επιστήμες της αγωγής. Δεν χρειάζεται αναζήτηση στην παλαιότερη ονομαστική λίστα.");
        if (namedProgram) {
          const referenceDetail = programReferenceText(v.namedPostgraduateRow, namedProgram);
          if (v.namedPostgraduateExactMatch === "yes") return result(POSITIVE, "Παλαιότερο ονομαστικά εγκεκριμένο Π.Μ.Σ.", referenceDetail + " Δήλωσες ότι τίτλος και φορέας ταυτίζονται ακριβώς.");
          if (v.namedPostgraduateExactMatch === "no") return result(WARNING, "Το επιλεγμένο Π.Μ.Σ. δεν ταυτίζεται ακριβώς", referenceDetail + " Η συγκεκριμένη εγγραφή δεν χρησιμοποιείται ως θετική διαδρομή επειδή υπάρχει διαφορά στον τίτλο ή στον φορέα.");
          return result(UNKNOWN, "Χρειάζεται επιβεβαίωση της ονομαστικής εγγραφής Π.Μ.Σ.", referenceDetail + " Επιβεβαίωσε ακριβή τίτλο και φορέα.");
        }
        if (v.domesticEducationEvidence === "no") return result(WARNING, "Μεταπτυχιακός / διδακτορικός τίτλος ημεδαπής — δεν τεκμηριώνεται από αυτή τη διαδρομή", "Δήλωσες ότι ο τίτλος δεν ανήκει στις επιστήμες της αγωγής. Αν πρόκειται για παλαιότερο εγκεκριμένο Π.Μ.Σ. με διαφορετική ονομασία, μπορείς να χρησιμοποιήσεις τον προαιρετικό ονομαστικό έλεγχο.");
        return result(UNKNOWN, "Τίτλος ημεδαπής — χρειάζεται πρώτα να ξεκαθαριστεί η κατηγορία", "Επιβεβαίωσε αν ο τίτλος είναι στις επιστήμες της αγωγής. Μόνο αν δεν είσαι βέβαιος/η χρειάζεται να ψάξεις την παλαιότερη ονομαστική λίστα.");
      }
      if (v.educationDegreeOrigin === "foreign") {
        if (v.foreignEducationEvidence === "yes") return result(POSITIVE, "Τίτλος αλλοδαπής στις επιστήμες της αγωγής", "Δηλώθηκε ότι υπάρχει το απαιτούμενο αποδεικτικό/αναγνώριση για την ένταξη του τίτλου στις επιστήμες της αγωγής.");
        if (v.foreignEducationEvidence === "no") return result(WARNING, "Τίτλος αλλοδαπής — δεν αρκεί μόνο ο τίτλος", "Χρειάζεται το προβλεπόμενο αποδεικτικό/αναγνώριση ότι ο τίτλος εμπίπτει στις επιστήμες της αγωγής.");
        return result(UNKNOWN, "Τίτλος αλλοδαπής — χρειάζεται τεκμηρίωση", "Δεν έχει επιβεβαιωθεί το απαιτούμενο αποδεικτικό/αναγνώριση για τις επιστήμες της αγωγής.");
      }
      return result(UNKNOWN, "Μεταπτυχιακός / διδακτορικός τίτλος — λείπει στοιχείο", "Δήλωσε αν ο τίτλος είναι ημεδαπής ή αλλοδαπής.");
    }

    if (proofType === "old_certificate") return result(POSITIVE, "Πιστοποιητικό ν. 3027/2002", "Η κατηγορία περιλαμβάνεται στις προβλεπόμενες διαδρομές απόδειξης Π.Δ.Ε.");

    if (proofType === "pedagogical_department") {
      if (!v.pedagogicalDepartmentType || v.pedagogicalDepartmentType === "unknown") return result(UNKNOWN, "Παιδαγωγικό Τμήμα — χρειάζεται ακριβής ταυτοποίηση", "Χρειάζεται η ακριβής ονομασία του Τμήματος για να διαπιστωθεί αν ανήκει στις προβλεπόμενες περιπτώσεις.");
      return result(POSITIVE, "Πτυχίο προβλεπόμενου Παιδαγωγικού Τμήματος", "Η επιλεγμένη κατηγορία ανήκει στις περιπτώσεις όπου η Π.Δ.Ε. πιστοποιείται εξ ορισμού με την αποφοίτηση.");
    }

    if (proofType === "aspaite") return result(POSITIVE, "Πτυχίο Α.Σ.ΠΑΙ.Τ.Ε.", "Το πτυχίο Α.Σ.ΠΑΙ.Τ.Ε. περιλαμβάνεται στις εξ ορισμού περιπτώσεις Π.Δ.Ε.");
    if (proofType === "aspaite_eppaik") return result(POSITIVE, "Πιστοποιητικό ΕΠΠΑΙΚ Α.Σ.ΠΑΙ.Τ.Ε.", "Το πιστοποιητικό ΕΠΠΑΙΚ / πρώην ΠΑΤΕΣ–ΣΕΛΕΤΕ αποτελεί προβλεπόμενη διαδρομή απόδειξης Π.Δ.Ε.");

    /* Backward-compatible legacy value: no longer exposed as a separate top-level UI option. */
    if (proofType === "article99") return result(POSITIVE, "Πιστοποιητικό άρθρου 99 ν. 4957/2022", "Το πιστοποιητικό ειδικού προγράμματος σπουδών Α.Ε.Ι. του άρθρου 99 προβλέπεται ρητά.");

    if (proofType === "epath") {
      if (v.epathDate === "before") return result(POSITIVE, "Πτυχίο Ε.Π.Α.Θ.", "Η δηλωμένη ημερομηνία κτήσης είναι πριν από 12/06/2018, όπως απαιτεί η σχετική περίπτωση.");
      if (v.epathDate === "after") return result(WARNING, "Πτυχίο Ε.Π.Α.Θ. — δεν καλύπτεται από τη συγκεκριμένη χρονική περίπτωση", "Η ειδική χρονική περίπτωση της διαθέσιμης βάσης αναφοράς αφορά τίτλο με ημερομηνία κτήσης προγενέστερη της 12/06/2018.");
      return result(UNKNOWN, "Πτυχίο Ε.Π.Α.Θ. — χρειάζεται ημερομηνία", "Δεν έχει επιβεβαιωθεί η κρίσιμη ημερομηνία κτήσης.");
    }

    if (proofType === "professor_school") {
      if (v.entryYear === "up_to_2014") return result(POSITIVE, "Πτυχίο καθηγητικής σχολής — μεταβατική περίπτωση", "Η εισαγωγή έως και το 2014–2015 καλύπτει τη συγκεκριμένη χρονική περίπτωση, οπότε δεν απαιτείται δεύτερο χρονικό στοιχείο για αυτόν τον έλεγχο.");
      if (!v.entryYear) return result(UNKNOWN, "Καθηγητική σχολή — χρειάζεται πρώτα το έτος εισαγωγής", "Δήλωσε πρώτα το έτος εισαγωγής στο συγκεκριμένο Τμήμα. Μόνο αν χρειάζεται θα εμφανιστεί στη συνέχεια και το έτος κτήσης.");
      if (v.graduationYear === "up_to_2017") return result(POSITIVE, "Πτυχίο καθηγητικής σχολής — μεταβατική περίπτωση", "Η κτήση του πτυχίου έως και το 2017–2018 καλύπτει τη δεύτερη χρονική περίπτωση.");
      if (v.entryYear === "from_2015" && v.graduationYear === "from_2018") return result(WARNING, "Το συγκεκριμένο πτυχίο καθηγητικής σχολής δεν θεμελιώνει a priori Π.Δ.Ε.", "Για εισαγωγή από 2015–2016 και κτήση από 2018–2019 και μετά απαιτείται άλλο αποδεικτικό. Άλλο πτυχίο του ίδιου προσώπου μπορεί όμως να θεμελιώνει Π.Δ.Ε.");
      if (v.entryYear === "from_2015" && !v.graduationYear) return result(UNKNOWN, "Καθηγητική σχολή — χρειάζεται τώρα το έτος κτήσης", "Επειδή η εισαγωγή είναι από 2015–2016 και μετά, χρειάζεται να δηλώσεις αν το πτυχίο αποκτήθηκε έως και το 2017–2018 ή από το 2018–2019 και μετά.");
      return result(UNKNOWN, "Καθηγητική σχολή — χρειάζεται ακόμη ένα χρονικό στοιχείο", "Με τα διαθέσιμα στοιχεία δεν προκύπτει ακόμη ασφαλές συμπέρασμα. Αν δεν γνωρίζεις το έτος εισαγωγής, το έτος κτήσης μπορεί να επιλύσει τη διαδρομή μόνο όταν είναι έως και το 2017–2018.");
    }

    /* Backward-compatible legacy route. New UI nests rows 39–52 under the A.E.I. certificate route. */
    if (proofType === "appendix_named_program") {
      const program = referenceProgram(v.appendixRow, "");
      if (!program) return result(UNKNOWN, "Χρειάζεται αντιστοίχιση με την ονομαστική βάση", "Η παλιά αυτόνομη διαδρομή δεν εμφανίζεται πλέον στο UI. Επίλεξε τη βασική κατηγορία του τίτλου σου.");
      const referenceDetail = programReferenceText(v.appendixRow, program);
      if (v.appendixExactMatch === "yes") return result(POSITIVE, "Ονομαστικά καταγεγραμμένος τίτλος / πρόγραμμα", referenceDetail);
      return result(UNKNOWN, "Χρειάζεται επιβεβαίωση ακριβούς τίτλου και φορέα", referenceDetail);
    }

    if (proofType === "none") return result(NEGATIVE, "Δεν δηλώθηκε αποδεικτικό σε αυτή την καταχώριση", "Η συγκεκριμένη καταχώριση δεν θεμελιώνει Π.Δ.Ε. Εξετάζονται όμως ανεξάρτητα όλες οι υπόλοιπες καταχωρίσεις.");
    return result(UNKNOWN, "Χρειάζεται περαιτέρω έλεγχος", "Το αποδεικτικό δεν έχει ταυτοποιηθεί με ασφαλή τρόπο.");
  }

  function aggregateEvaluations(items) {
    const active = items.filter(Boolean);
    if (!active.length) return {status:UNKNOWN, positives:[], warnings:[], unknowns:[], negatives:[]};
    const grouped = {positive:[], warning:[], unknown:[], negative:[]};
    active.forEach(item => grouped[item.status].push(item));
    let status = UNKNOWN;
    if (grouped.positive.length) status = POSITIVE;
    else if (grouped.warning.length) status = WARNING;
    else if (grouped.unknown.length) status = UNKNOWN;
    else if (grouped.negative.length) status = NEGATIVE;
    return {status, positives:grouped.positive, warnings:grouped.warning, unknowns:grouped.unknown, negatives:grouped.negative};
  }

  function valuesFromCard(card) {
    return {
      proofType: roleValue(card, "proofType", "proofType"),
      aeiCertificateSubtype: roleValue(card, "aeiCertificateSubtype", "aeiCertificateSubtype"),
      aeiCertificateEligibility: roleValue(card, "aeiCertificateEligibility", "aeiCertificateEligibility"),
      aeiEntryPeriod: roleValue(card, "aeiEntryPeriod", "aeiEntryPeriod"),
      aeiCertifiedAtEntry: roleValue(card, "aeiCertifiedAtEntry", "aeiCertifiedAtEntry"),
      namedSpecialProgramRow: roleValue(card, "namedSpecialProgramRow", "namedSpecialProgramRow"),
      namedSpecialProgramExactMatch: roleValue(card, "namedSpecialProgramExactMatch", "namedSpecialProgramExactMatch"),
      educationDegreeOrigin: roleValue(card, "educationDegreeOrigin", "educationDegreeOrigin"),
      domesticEducationEvidence: roleValue(card, "domesticEducationEvidence", "domesticEducationEvidence"),
      foreignEducationEvidence: roleValue(card, "foreignEducationEvidence", "foreignEducationEvidence"),
      namedPostgraduateRow: roleValue(card, "namedPostgraduateRow", "namedPostgraduateRow"),
      namedPostgraduateExactMatch: roleValue(card, "namedPostgraduateExactMatch", "namedPostgraduateExactMatch"),
      pedagogicalDepartmentType: roleValue(card, "pedagogicalDepartmentType", "pedagogicalDepartmentType"),
      epathDate: roleValue(card, "epathDate", "epathDate"),
      entryYear: roleValue(card, "entryYear", "entryYear"),
      graduationYear: roleValue(card, "graduationYear", "graduationYear"),
      appendixRow: roleValue(card, "appendixRow", "appendixRow"),
      appendixExactMatch: roleValue(card, "appendixExactMatch", "appendixExactMatch")
    };
  }

  function credentialCards() {
    const list = byId("credentialsList");
    const cards = safeQsa(list, ".ped-credential");
    return cards.length ? cards : [null];
  }

  function renderProgramDetails(detailsEl, row, program) {
    if (!detailsEl) return;
    if (!program) {
      detailsEl.innerHTML = "";
      toggleHidden(detailsEl, true);
      return;
    }
    detailsEl.innerHTML = '<strong>Εγγραφή ' + escapeHtml(row) + '</strong>' +
      '<div><strong>Τίτλος:</strong> ' + escapeHtml(program.title) + '</div>' +
      '<div><strong>Φορέας:</strong> ' + escapeHtml(program.provider) + '</div>' +
      '<div><strong>Διάταξη / απόφαση:</strong> ' + escapeHtml(program.legal_basis) + '</div>' +
      '<div><strong>ΦΕΚ:</strong> ' + escapeHtml(program.fek) + '</div>';
    toggleHidden(detailsEl, false);
  }

  function syncCard(card) {
    const root = card || document;
    const proofType = roleValue(card, "proofType", "proofType");
    safeQsa(root, "[data-section]").forEach(section => toggleHidden(section, section.getAttribute("data-section") !== proofType));

    /* MSc/PhD: answer the broad eligibility question before exposing the old named list. */
    const origin = roleValue(card, "educationDegreeOrigin", "educationDegreeOrigin");
    const domestic = safeQs(root, '[data-subsection="domesticEducation"]');
    const foreign = safeQs(root, '[data-subsection="foreignEducation"]') || byId("foreignEducationQuestions");
    if (domestic) toggleHidden(domestic, !(proofType === "education_msc_phd" && origin === "domestic"));
    if (foreign) toggleHidden(foreign, !(proofType === "education_msc_phd" && origin === "foreign"));

    const domesticEvidence = roleValue(card, "domesticEducationEvidence", "domesticEducationEvidence");
    const namedPostgraduateReference = safeQs(root, '[data-subsection="namedPostgraduateReference"]');
    const shouldOfferNamedPostgraduate = proofType === "education_msc_phd" && origin === "domestic" && (domesticEvidence === "no" || domesticEvidence === "unknown");
    if (namedPostgraduateReference) toggleHidden(namedPostgraduateReference, !shouldOfferNamedPostgraduate);

    const educationGuidance = safeQs(root, '[data-role="educationDegreeGuidance"]');
    if (proofType === "education_msc_phd" && origin === "domestic" && domesticEvidence === "yes") {
      setGuidance(educationGuidance, "positive", "Δεν χρειάζεται να ψάξεις στην αναλυτική λίστα: δήλωσες ότι μπορείς ήδη να τεκμηριώσεις ότι ο τίτλος είναι στις επιστήμες της αγωγής.");
    } else if (proofType === "education_msc_phd" && origin === "domestic" && domesticEvidence === "no") {
      setGuidance(educationGuidance, "warning", "Η γενική διαδρομή δεν αρκεί. Μόνο αν πρόκειται για παλαιότερο εγκεκριμένο Π.Μ.Σ. με διαφορετική ονομασία έχει νόημα να ανοίξεις τον ονομαστικό έλεγχο που ακολουθεί.");
    } else if (proofType === "education_msc_phd" && origin === "domestic" && domesticEvidence === "unknown") {
      setGuidance(educationGuidance, "info", "Πριν ψάξεις όλη τη λίστα, έλεγξε το δίπλωμα ή σχετική βεβαίωση. Αν παραμένει αβέβαιο, χρησιμοποίησε τον ονομαστικό έλεγχο παλαιότερων Π.Μ.Σ. που εμφανίζεται παρακάτω.");
    } else if (proofType === "education_msc_phd" && origin === "foreign") {
      setGuidance(educationGuidance, "info", "Για τίτλο αλλοδαπής δεν χρειάζεται να ψάξεις την παλαιότερη ελληνική ονομαστική λίστα· κρίσιμο είναι το απαιτούμενο αποδεικτικό/η αναγνώριση για τις επιστήμες της αγωγής.");
    } else {
      setGuidance(educationGuidance, "", "");
    }

    /* AEI certificate: split the transition rule into two sequential checks. */
    const certificateSubtype = roleValue(card, "aeiCertificateSubtype", "aeiCertificateSubtype");
    const standardCertificate = safeQs(root, '[data-subsection="aeiStandardCertificate"]');
    const namedSpecialProgram = safeQs(root, '[data-subsection="aeiNamedSpecialProgram"]');
    if (standardCertificate) toggleHidden(standardCertificate, !(proofType === "aei_certificate" && certificateSubtype === "standard"));
    if (namedSpecialProgram) toggleHidden(namedSpecialProgram, !(proofType === "aei_certificate" && certificateSubtype === "named_special_program"));

    const aeiEntryPeriod = roleValue(card, "aeiEntryPeriod", "aeiEntryPeriod");
    const aeiCertifiedAtEntry = roleValue(card, "aeiCertifiedAtEntry", "aeiCertifiedAtEntry");
    const certifiedAtEntryQuestion = safeQs(root, '[data-subsection="aeiCertifiedAtEntry"]');
    if (certifiedAtEntryQuestion) toggleHidden(certifiedAtEntryQuestion, !(proofType === "aei_certificate" && certificateSubtype === "standard" && aeiEntryPeriod === "up_to_2026"));
    const aeiGuidance = safeQs(root, '[data-role="aeiStandardGuidance"]');
    if (proofType === "aei_certificate" && certificateSubtype === "standard" && aeiEntryPeriod === "from_2027") {
      setGuidance(aeiGuidance, "warning", "Η μεταβατική περίπτωση της βάσης 1ΓΕ/2026–2ΓΕ/2026 αφορά εισαγωγή έως και το 2026-2027. Δεν χρειάζεται να συνεχίσεις σε δεύτερη ερώτηση γι’ αυτή τη διαδρομή.");
    } else if (proofType === "aei_certificate" && certificateSubtype === "standard" && aeiEntryPeriod === "up_to_2026" && aeiCertifiedAtEntry === "yes") {
      setGuidance(aeiGuidance, "positive", "Καλύπτονται τα δύο χρονικά/μεταβατικά στοιχεία που ζητά η συγκεκριμένη βάση αναφοράς.");
    } else if (proofType === "aei_certificate" && certificateSubtype === "standard" && aeiEntryPeriod === "up_to_2026" && aeiCertifiedAtEntry === "no") {
      setGuidance(aeiGuidance, "warning", "Η εισαγωγή είναι εντός του χρονικού ορίου, αλλά δήλωσες ότι το Τμήμα / η Σχολή δεν χορηγούσε την πιστοποίηση κατά τον χρόνο εισαγωγής.");
    } else if (proofType === "aei_certificate" && certificateSubtype === "standard" && aeiEntryPeriod === "up_to_2026") {
      setGuidance(aeiGuidance, "info", "Το έτος εισαγωγής είναι εντός του ορίου. Χρειάζεται μόνο να απαντήσεις τη δεύτερη προϋπόθεση που εμφανίστηκε.");
    } else if (proofType === "aei_certificate" && certificateSubtype === "standard" && aeiEntryPeriod === "unknown") {
      setGuidance(aeiGuidance, "info", "Χρειάζεται πρώτα να εντοπίσεις το ακαδημαϊκό έτος εισαγωγής, επειδή αυτό καθορίζει αν εφαρμόζεται η μεταβατική περίπτωση.");
    } else {
      setGuidance(aeiGuidance, "", "");
    }

    const namedSpecialProgramRow = roleValue(card, "namedSpecialProgramRow", "namedSpecialProgramRow");
    const specialProgram = certificateSubtype === "named_special_program" ? referenceProgram(namedSpecialProgramRow, "special_program") : null;
    const specialProgramMatch = safeQs(root, '[data-subsection="namedSpecialProgramExactMatch"]');
    if (specialProgramMatch) toggleHidden(specialProgramMatch, !specialProgram);
    renderProgramDetails(safeQs(root, '[data-role="namedSpecialProgramDetails"]'), namedSpecialProgramRow, specialProgram);

    const namedPostgraduateRow = roleValue(card, "namedPostgraduateRow", "namedPostgraduateRow");
    const postgraduateProgram = shouldOfferNamedPostgraduate ? referenceProgram(namedPostgraduateRow, "postgraduate_prior") : null;
    const postgraduateMatch = safeQs(root, '[data-subsection="namedPostgraduateExactMatch"]');
    if (postgraduateMatch) toggleHidden(postgraduateMatch, !postgraduateProgram);
    renderProgramDetails(safeQs(root, '[data-role="namedPostgraduateDetails"]'), namedPostgraduateRow, postgraduateProgram);

    /* EPATH: surface the cutoff immediately after the answer. */
    const epathDate = roleValue(card, "epathDate", "epathDate");
    const epathGuidance = safeQs(root, '[data-role="epathGuidance"]');
    if (proofType === "epath" && epathDate === "before") setGuidance(epathGuidance, "positive", "Η ημερομηνία είναι πριν από 12/06/2018 και καλύπτει το χρονικό όριο της συγκεκριμένης περίπτωσης.");
    else if (proofType === "epath" && epathDate === "after") setGuidance(epathGuidance, "warning", "Η συγκεκριμένη περίπτωση αφορά πτυχίο με ημερομηνία κτήσης πριν από 12/06/2018. Πρόσθεσε άλλο αποδεικτικό αν διαθέτεις.");
    else if (proofType === "epath" && epathDate === "unknown") setGuidance(epathGuidance, "info", "Χρειάζεται η ακριβής ημερομηνία κτήσης του πτυχίου για να εφαρμοστεί το όριο 12/06/2018.");
    else setGuidance(epathGuidance, "", "");

    /* Professor school: do not ask the second date if the first date already resolves the route. */
    const entryYear = roleValue(card, "entryYear", "entryYear");
    const graduationYear = roleValue(card, "graduationYear", "graduationYear");
    const graduationQuestion = safeQs(root, '[data-subsection="professorGraduation"]');
    if (graduationQuestion) toggleHidden(graduationQuestion, !(proofType === "professor_school" && (entryYear === "from_2015" || entryYear === "unknown")));
    const professorGuidance = safeQs(root, '[data-role="professorSchoolGuidance"]');
    if (proofType === "professor_school" && entryYear === "up_to_2014") {
      setGuidance(professorGuidance, "positive", "Η εισαγωγή έως και το 2014-2015 καλύπτει ήδη τη συγκεκριμένη μεταβατική περίπτωση. Δεν χρειάζεται να δηλώσεις και έτος κτήσης για αυτόν τον έλεγχο.");
    } else if (proofType === "professor_school" && entryYear === "from_2015" && graduationYear === "up_to_2017") {
      setGuidance(professorGuidance, "positive", "Παρότι η εισαγωγή είναι από 2015-2016 και μετά, η κτήση έως και το 2017-2018 καλύπτει τη δεύτερη χρονική περίπτωση.");
    } else if (proofType === "professor_school" && entryYear === "from_2015" && graduationYear === "from_2018") {
      setGuidance(professorGuidance, "warning", "Δεν καλύπτεται καμία από τις δύο χρονικές μεταβατικές περιπτώσεις. Αν έχεις άλλο πτυχίο ή αποδεικτικό Π.Δ.Ε., πρόσθεσέ το ξεχωριστά.");
    } else if (proofType === "professor_school" && entryYear === "from_2015") {
      setGuidance(professorGuidance, "info", "Επειδή η εισαγωγή είναι από 2015-2016 και μετά, χρειάζεται τώρα μόνο το έτος κτήσης του πτυχίου.");
    } else if (proofType === "professor_school" && entryYear === "unknown" && graduationYear === "up_to_2017") {
      setGuidance(professorGuidance, "positive", "Η κτήση έως και το 2017-2018 καλύπτει τη δεύτερη χρονική περίπτωση, ακόμη κι αν δεν έχεις εντοπίσει το έτος εισαγωγής.");
    } else if (proofType === "professor_school" && entryYear === "unknown") {
      setGuidance(professorGuidance, "info", "Αν δεν γνωρίζεις το έτος εισαγωγής, το έτος κτήσης μπορεί να είναι αρκετό μόνο αν είναι έως και το 2017-2018.");
    } else {
      setGuidance(professorGuidance, "", "");
    }
  }

  function updateVisibility() {
    const cards = credentialCards();
    cards.forEach(syncCard);
    const resultEl = byId("result");
    if (resultEl) resultEl.style.display = "none";

    if (cards.length === 1 && cards[0] === null) {
      const proofType = valueOf("proofType");
      [["pedagogicalDepartmentQuestions","pedagogical_department"],["epathQuestions","epath"],["professorSchoolQuestions","professor_school"],["aeiCertificateQuestions","aei_certificate"],["educationDegreeQuestions","education_msc_phd"]].forEach(pair => {
        const el = byId(pair[0]); if (el) toggleHidden(el, proofType !== pair[1]);
      });
    }
  }

  function opsydBlock(value) {
    if (value === "yes") return '<div class="note-box"><strong>Ο.Π.ΣΥ.Δ.:</strong> Δήλωσες ότι το σχετικό αποδεικτικό εμφανίζεται ή έχει καταχωριστεί.</div>';
    if (value === "no") return '<div class="note-box"><strong>Ο.Π.ΣΥ.Δ.:</strong> Η Π.Δ.Ε. μπορεί να προκύπτει από τον τίτλο σου, αλλά για να ληφθεί υπόψη στη διαδικασία χρειάζεται ο προβλεπόμενος έλεγχος/επικαιροποίηση του ηλεκτρονικού φακέλου.</div>';
    return '<div class="note-box"><strong>Ο.Π.ΣΥ.Δ.:</strong> Δεν επηρεάζει το παραπάνω συμπέρασμα για τον τίτλο, αλλά χρειάζεται ξεχωριστός έλεγχος της καταχώρισης για τη χρήση του προσόντος στη διαδικασία.</div>';
  }

  function escapeHtml(s) { return String(s).replace(/[&<>"']/g, ch => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[ch])); }
  function renderItem(item, idx) {
    return '<li><strong>Αποδεικτικό ' + (idx + 1) + ': ' + escapeHtml(item.title) + '</strong><br>' + escapeHtml(item.detail) + '</li>';
  }

  function showResult(type, html) {
    const el = byId("result"); if (!el) return;
    el.style.display = "block"; el.className = "result " + type; el.innerHTML = html;
  }

  function checkEparkeia() {
    const cards = credentialCards();
    const evaluations = cards.map(card => evaluateCredentialValues(valuesFromCard(card)));
    const aggregate = aggregateEvaluations(evaluations);
    const specialty = valueOf("specialty");
    const opsyd = valueOf("opsyd");
    const specialtyLine = specialty ? '<p><strong>Κλάδος ελέγχου:</strong> ' + escapeHtml(specialty) + '. Ο κλάδος λειτουργεί ως πλαίσιο και δεν ακυρώνει άλλο ανεξάρτητο πτυχίο/αποδεικτικό.</p>' : '<p><strong>Κλάδος ελέγχου:</strong> δεν επιλέχθηκε. Το συμπέρασμα αφορά μόνο τα δηλωμένα αποδεικτικά.</p>';

    let html = '';
    if (aggregate.status === POSITIVE) {
      html += '<h2>Φαίνεται ότι διαθέτεις Παιδαγωγική και Διδακτική Επάρκεια</h2>';
      html += '<p>Βρέθηκαν <strong>' + aggregate.positives.length + '</strong> ανεξάρτητες θετικές διαδρομές. Αρκεί μία έγκυρη διαδρομή· τυχόν αρνητική ή αβέβαιη δεύτερη καταχώριση δεν αναιρεί τη θετική.</p>';
    } else if (aggregate.status === WARNING) {
      html += '<h2>Δεν προκύπτει ακόμη ασφαλές θετικό συμπέρασμα</h2><p>Υπάρχει τουλάχιστον μία διαδρομή που απαιτεί πρόσθετο έλεγχο ή άλλο αποδεικτικό.</p>';
    } else if (aggregate.status === NEGATIVE) {
      html += '<h2>Δεν προκύπτει Π.Δ.Ε. από τα αποδεικτικά που δήλωσες</h2><p>Το συμπέρασμα αφορά αποκλειστικά τις καταχωρίσεις που έδωσες.</p>';
    } else {
      html += '<h2>Χρειάζονται ακόμη στοιχεία</h2><p>Δεν υπάρχει αρκετή πληροφορία για ασφαλές συμπέρασμα.</p>';
    }
    html += specialtyLine;
    if (aggregate.positives.length) html += '<div class="note-box"><strong>Θετικές διαδρομές:</strong><ul>' + aggregate.positives.map(renderItem).join('') + '</ul></div>';
    const pending = aggregate.warnings.concat(aggregate.unknowns);
    if (pending.length) html += '<div class="note-box"><strong>Διαδρομές που χρειάζονται έλεγχο:</strong><ul>' + pending.map(renderItem).join('') + '</ul></div>';
    html += opsydBlock(opsyd);
    showResult(aggregate.status, html);
  }

  function reindexCredentialCards() {
    const list = byId("credentialsList");
    safeQsa(list, ".ped-credential").forEach(function(card, index){
      if (typeof card.setAttribute === "function") card.setAttribute("data-credential-index", String(index));
      const heading = safeQs(card, ".ped-credential-heading h3");
      if (heading) heading.textContent = "Αποδεικτικό " + String(index + 1);
    });
  }

  function updateCredentialControls() {
    const list = byId("credentialsList");
    const addButton = byId("addCredentialBtn");
    if (!list || !addButton) return;
    const count = safeQsa(list, ".ped-credential").length;
    const atLimit = count >= MAX_CREDENTIALS;
    addButton.disabled = atLimit;
    addButton.setAttribute("aria-disabled", atLimit ? "true" : "false");
    addButton.textContent = ADD_CREDENTIAL_LABEL;
    if (atLimit) addButton.setAttribute("title", "Μέγιστος αριθμός: " + MAX_CREDENTIALS + " αποδεικτικά");
    else addButton.removeAttribute("title");
  }

  function addCredential() {
    const template = byId("credentialTemplate"), list = byId("credentialsList");
    if (!template || !list) return;
    const count = safeQsa(list, ".ped-credential").length;
    if (count >= MAX_CREDENTIALS) { updateCredentialControls(); return; }
    const html = template.innerHTML.replace(/__INDEX__/g, String(count)).replace(/__NUMBER__/g, String(count + 1));
    const holder = document.createElement("div"); holder.innerHTML = html.trim();
    const card = holder.firstElementChild; if (!card) return;
    list.appendChild(card);
    reindexCredentialCards();
    syncCard(card);
    updateCredentialControls();
  }

  function init() {
    const checkButton = document.getElementById("checkEparkeiaBtn");
    if (!checkButton) return;
    const addButton = byId("addCredentialBtn");
    if (addButton) addButton.addEventListener("click", addCredential);
    checkButton.addEventListener("click", checkEparkeia);
    const proofType = byId("proofType"); if (proofType) proofType.addEventListener("change", updateVisibility);
    document.addEventListener("change", function(e){
      const card = e.target && typeof e.target.closest === "function" ? e.target.closest(".ped-credential") : null;
      const role = card && e.target && typeof e.target.getAttribute === "function" ? e.target.getAttribute("data-role") : "";
      if (card && role === "namedSpecialProgramRow") {
        const exact = safeQs(card, '[data-role="namedSpecialProgramExactMatch"]');
        if (exact) exact.value = "";
      }
      if (card && role === "namedPostgraduateRow") {
        const exactPostgraduate = safeQs(card, '[data-role="namedPostgraduateExactMatch"]');
        if (exactPostgraduate) exactPostgraduate.value = "";
      }
      if (card) syncCard(card);
    });
    document.addEventListener("input", function(e){
      const card = e.target && typeof e.target.closest === "function" ? e.target.closest(".ped-credential") : null;
      if (!card || !e.target || typeof e.target.getAttribute !== "function") return;
      const role = e.target.getAttribute("data-role");
      if (role === "namedSpecialProgramSearch") {
        repopulateProgramSelect(card, "special_program", "namedSpecialProgramSearch", "namedSpecialProgramRow");
        const exact = safeQs(card, '[data-role="namedSpecialProgramExactMatch"]');
        if (exact) exact.value = "";
        syncCard(card);
      } else if (role === "namedPostgraduateSearch") {
        repopulateProgramSelect(card, "postgraduate_prior", "namedPostgraduateSearch", "namedPostgraduateRow");
        const exactPostgraduate = safeQs(card, '[data-role="namedPostgraduateExactMatch"]');
        if (exactPostgraduate) exactPostgraduate.value = "";
        syncCard(card);
      }
    });
    document.addEventListener("click", function(e){
      const btn = e.target && typeof e.target.closest === "function" ? e.target.closest('[data-action="removeCredential"]') : null;
      if (!btn) return;
      const card = btn.closest(".ped-credential");
      if (card) {
        card.remove();
        reindexCredentialCards();
        updateCredentialControls();
      }
    });
    reindexCredentialCards();
    updateCredentialControls();
    updateVisibility();
  }

  if (document.readyState === "loading") document.addEventListener("DOMContentLoaded", init); else init();

  global.PedagogicalCompetenceUI = Object.freeze({
    init, updateVisibility, checkEparkeia, addCredential,
    evaluateCredentialValues, aggregateEvaluations,
    maxCredentials: MAX_CREDENTIALS
  });
})(window);
