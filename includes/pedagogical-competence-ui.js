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

  function appendixProgram(row) {
    const programs = REFERENCE.appendix_named_programs || {};
    return row && Object.prototype.hasOwnProperty.call(programs, String(row)) ? programs[String(row)] : null;
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

  function result(status, title, detail, extra) {
    return Object.assign({ status, title, detail, documents: [], notes: [] }, extra || {});
  }

  function evaluateCredentialValues(v) {
    const proofType = v.proofType || "";
    if (!proofType) return result(UNKNOWN, "Δεν έχει επιλεγεί αποδεικτικό", "Επίλεξε κατηγορία ή αφαίρεσε την κενή καταχώριση.");

    if (proofType === "aei_certificate") {
      if (v.aeiCertificateEligibility === "yes") return result(POSITIVE, "Βεβαίωση Α.Ε.Ι.", "Η διαδρομή αντιστοιχεί σε προβλεπόμενη βεβαίωση Α.Ε.Ι. και δηλώθηκε ότι πληρούται η μεταβατική προϋπόθεση της 2ΓΕ/2026.", {documents:["Βεβαίωση Παιδαγωγικής και Διδακτικής Επάρκειας Α.Ε.Ι."], notes:["Εισαγωγή έως και 2026–2027 και πιστοποίηση από το Τμήμα/Σχολή κατά τον χρόνο εισαγωγής."]});
      if (v.aeiCertificateEligibility === "no") return result(WARNING, "Βεβαίωση Α.Ε.Ι. — χρειάζεται έλεγχος", "Η δηλωμένη περίπτωση δεν φαίνεται να πληροί τη μεταβατική προϋπόθεση της 2ΓΕ/2026. Μην απορρίψεις όμως άλλα πτυχία ή αποδεικτικά που διαθέτεις.");
      return result(UNKNOWN, "Βεβαίωση Α.Ε.Ι. — λείπει κρίσιμο στοιχείο", "Χρειάζεται να επιβεβαιωθεί η μεταβατική προϋπόθεση της συγκεκριμένης βεβαίωσης.");
    }

    if (proofType === "education_msc_phd") {
      if (v.educationDegreeOrigin === "domestic") return result(POSITIVE, "Μεταπτυχιακός / διδακτορικός τίτλος στις επιστήμες της αγωγής", "Ο τίτλος ημεδαπής δηλώθηκε ως τίτλος στις επιστήμες της αγωγής, κατηγορία που προβλέπεται ως αποδεικτικό Π.Δ.Ε.");
      if (v.educationDegreeOrigin === "foreign") {
        if (v.foreignEducationEvidence === "yes") return result(POSITIVE, "Τίτλος αλλοδαπής στις επιστήμες της αγωγής", "Δηλώθηκε ότι υπάρχει το απαιτούμενο αποδεικτικό/αναγνώριση για την ένταξη του τίτλου στις επιστήμες της αγωγής.");
        if (v.foreignEducationEvidence === "no") return result(WARNING, "Τίτλος αλλοδαπής — δεν αρκεί μόνο ο τίτλος", "Χρειάζεται το προβλεπόμενο αποδεικτικό/αναγνώριση ότι ο τίτλος εμπίπτει στις επιστήμες της αγωγής.");
        return result(UNKNOWN, "Τίτλος αλλοδαπής — χρειάζεται τεκμηρίωση", "Δεν έχει επιβεβαιωθεί το απαιτούμενο αποδεικτικό/αναγνώριση για τις επιστήμες της αγωγής.");
      }
      return result(UNKNOWN, "Μεταπτυχιακός / διδακτορικός τίτλος — λείπει στοιχείο", "Δήλωσε αν ο τίτλος είναι ημεδαπής ή αλλοδαπής.");
    }

    if (proofType === "old_certificate") return result(POSITIVE, "Πιστοποιητικό ν. 3027/2002", "Η κατηγορία περιλαμβάνεται ρητά στις διαδρομές απόδειξης Π.Δ.Ε.");

    if (proofType === "pedagogical_department") {
      if (!v.pedagogicalDepartmentType || v.pedagogicalDepartmentType === "unknown") return result(UNKNOWN, "Παιδαγωγικό Τμήμα — χρειάζεται ακριβής ταυτοποίηση", "Χρειάζεται η ακριβής ονομασία του Τμήματος για να διαπιστωθεί αν ανήκει στις ρητά προβλεπόμενες περιπτώσεις της σχετικής προκήρυξης.");
      return result(POSITIVE, "Πτυχίο προβλεπόμενου Παιδαγωγικού Τμήματος", "Η επιλεγμένη κατηγορία ανήκει στις περιπτώσεις όπου η Π.Δ.Ε. πιστοποιείται εξ ορισμού με την αποφοίτηση.");
    }

    if (proofType === "aspaite") return result(POSITIVE, "Πτυχίο Α.Σ.ΠΑΙ.Τ.Ε.", "Το πτυχίο Α.Σ.ΠΑΙ.Τ.Ε. περιλαμβάνεται στις εξ ορισμού περιπτώσεις Π.Δ.Ε.");
    if (proofType === "aspaite_eppaik") return result(POSITIVE, "Πιστοποιητικό ΕΠΠΑΙΚ Α.Σ.ΠΑΙ.Τ.Ε.", "Το πιστοποιητικό ΕΠΠΑΙΚ / πρώην ΠΑΤΕΣ–ΣΕΛΕΤΕ αποτελεί ρητή διαδρομή απόδειξης Π.Δ.Ε.");
    if (proofType === "article99") return result(POSITIVE, "Πιστοποιητικό άρθρου 99 ν. 4957/2022", "Το πιστοποιητικό ειδικού προγράμματος σπουδών Α.Ε.Ι. του άρθρου 99 προβλέπεται ρητά.");

    if (proofType === "epath") {
      if (v.epathDate === "before") return result(POSITIVE, "Πτυχίο Ε.Π.Α.Θ.", "Η δηλωμένη ημερομηνία κτήσης είναι πριν από 12/06/2018, όπως απαιτεί η σχετική περίπτωση.");
      if (v.epathDate === "after") return result(WARNING, "Πτυχίο Ε.Π.Α.Θ. — δεν καλύπτεται από τη συγκεκριμένη χρονική περίπτωση", "Η ειδική χρονική περίπτωση της διαθέσιμης βάσης αναφοράς αφορά τίτλο με ημερομηνία κτήσης προγενέστερη της 12/06/2018.");
      return result(UNKNOWN, "Πτυχίο Ε.Π.Α.Θ. — χρειάζεται ημερομηνία", "Δεν έχει επιβεβαιωθεί η κρίσιμη ημερομηνία κτήσης.");
    }

    if (proofType === "professor_school") {
      if (v.professorSchoolMatch === "no") return result(WARNING, "Το συγκεκριμένο πτυχίο δεν δηλώθηκε ως πτυχίο καθηγητικής σχολής", "Ο κλάδος υποψηφιότητας από μόνος του δεν αρκεί. Έλεγξε τα υπόλοιπα πτυχία ή αποδεικτικά σου.");
      if (!v.professorSchoolMatch || v.professorSchoolMatch === "unknown") return result(UNKNOWN, "Καθηγητική σχολή — χρειάζεται ταυτοποίηση του συγκεκριμένου πτυχίου", "Δεν αρκεί ο κλάδος. Πρέπει να επιβεβαιωθεί ότι το συγκεκριμένο πτυχίο ανήκει στην προβλεπόμενη κατηγορία.");
      if (v.entryYear === "up_to_2014" || v.graduationYear === "up_to_2017") return result(POSITIVE, "Πτυχίο καθηγητικής σχολής — μεταβατική περίπτωση", "Για το συγκεκριμένο πτυχίο δηλώθηκε ότι συντρέχει μία από τις χρονικές προϋποθέσεις: εισαγωγή έως 2014–2015 ή κτήση έως 2017–2018.");
      if (v.entryYear === "from_2015" && v.graduationYear === "from_2018") return result(WARNING, "Το συγκεκριμένο πτυχίο δεν θεμελιώνει a priori Π.Δ.Ε.", "Για εισαγωγή από 2015–2016 και κτήση από 2018–2019 και μετά απαιτείται άλλο αποδεικτικό. Άλλο πτυχίο του ίδιου προσώπου μπορεί όμως να θεμελιώνει Π.Δ.Ε.");
      return result(UNKNOWN, "Καθηγητική σχολή — λείπουν χρονικά στοιχεία", "Χρειάζονται το έτος εισαγωγής και το έτος κτήσης του συγκεκριμένου πτυχίου.");
    }

    if (proofType === "appendix_named_program") {
      if (!v.appendixRow || v.appendixRow === "unknown") return result(UNKNOWN, "Χρειάζεται αντιστοίχιση με τον επίσημο πίνακα", "Επίλεξε τον ακριβή τίτλο και φορέα από την διαθέσιμη ονομαστική βάση της 2ΓΕ/2026.");
      const program = appendixProgram(v.appendixRow);
      if (!program) return result(UNKNOWN, "Η εγγραφή δεν βρέθηκε στο canonical dataset", "Δεν δίνεται αποτέλεσμα μέχρι να διασταυρωθεί ξανά η συγκεκριμένη εγγραφή με την επίσημη πηγή.");
      const referenceDetail = "Εγγραφή " + v.appendixRow + ": " + program.title + ". Φορέας: " + program.provider + ". Σχετική διάταξη/απόφαση: " + program.legal_basis + ". ΦΕΚ: " + program.fek + ".";
      if (v.appendixExactMatch === "yes") return result(POSITIVE, "Ρητά κατονομαζόμενος τίτλος / πρόγραμμα στην 2ΓΕ/2026", referenceDetail + " Δήλωσες ότι ο τίτλος και ο φορέας του δικού σου δικαιολογητικού ταυτίζονται ακριβώς με την εγγραφή.");
      if (v.appendixExactMatch === "no") return result(WARNING, "Δεν υπάρχει ακριβής αντιστοίχιση με την επιλεγμένη εγγραφή", referenceDetail + " Αφού υπάρχει διαφορά στον τίτλο ή στον φορέα, η συγκεκριμένη επιλογή δεν χρησιμοποιείται ως θετική διαδρομή. Έλεγξε άλλη εγγραφή ή άλλο αποδεικτικό.");
      return result(UNKNOWN, "Χρειάζεται επιβεβαίωση ακριβούς τίτλου και φορέα", referenceDetail + " Επιβεβαίωσε ότι τα δύο στοιχεία ταυτίζονται με το δικαιολογητικό σου πριν δοθεί θετικό αποτέλεσμα.");
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
    else status = NEGATIVE;
    return {status, positives:grouped.positive, warnings:grouped.warning, unknowns:grouped.unknown, negatives:grouped.negative};
  }

  function valuesFromCard(card) {
    return {
      proofType: roleValue(card, "proofType", "proofType"),
      aeiCertificateEligibility: roleValue(card, "aeiCertificateEligibility", "aeiCertificateEligibility"),
      educationDegreeOrigin: roleValue(card, "educationDegreeOrigin", "educationDegreeOrigin"),
      foreignEducationEvidence: roleValue(card, "foreignEducationEvidence", "foreignEducationEvidence"),
      pedagogicalDepartmentType: roleValue(card, "pedagogicalDepartmentType", "pedagogicalDepartmentType"),
      epathDate: roleValue(card, "epathDate", "epathDate"),
      professorSchoolMatch: roleValue(card, "professorSchoolMatch", "professorSchoolMatch"),
      entryYear: roleValue(card, "entryYear", "entryYear"),
      graduationYear: roleValue(card, "graduationYear", "graduationYear"),
      appendixRow: roleValue(card, "appendixRow", "appendixRow"),
      appendixExactMatch: roleValue(card, "appendixExactMatch", "appendixExactMatch")
    };
  }

  function credentialCards() {
    const list = byId("credentialsList");
    const cards = safeQsa(list, ".ped-credential");
    return cards.length ? cards : [null]; // backward-compatible test/runtime fallback
  }

  function syncCard(card) {
    const proofType = roleValue(card, "proofType", "proofType");
    safeQsa(card || document, "[data-section]").forEach(section => toggleHidden(section, section.getAttribute("data-section") !== proofType));
    const origin = roleValue(card, "educationDegreeOrigin", "educationDegreeOrigin");
    const foreign = safeQs(card || document, '[data-subsection="foreignEducation"]') || byId("foreignEducationQuestions");
    if (foreign) toggleHidden(foreign, !(proofType === "education_msc_phd" && origin === "foreign"));

    const appendixRow = roleValue(card, "appendixRow", "appendixRow");
    const appendixMatch = safeQs(card || document, '[data-subsection="appendixExactMatch"]');
    const appendixDetails = safeQs(card || document, '[data-role="appendixProgramDetails"]');
    const program = proofType === "appendix_named_program" ? appendixProgram(appendixRow) : null;
    if (appendixMatch) toggleHidden(appendixMatch, !program);
    if (appendixDetails) {
      if (!program) {
        appendixDetails.innerHTML = "";
        toggleHidden(appendixDetails, true);
      } else {
        appendixDetails.innerHTML = '<strong>Εγγραφή ' + escapeHtml(appendixRow) + '</strong>' +
          '<div><strong>Τίτλος:</strong> ' + escapeHtml(program.title) + '</div>' +
          '<div><strong>Φορέας:</strong> ' + escapeHtml(program.provider) + '</div>' +
          '<div><strong>Διάταξη / απόφαση:</strong> ' + escapeHtml(program.legal_basis) + '</div>' +
          '<div><strong>ΦΕΚ:</strong> ' + escapeHtml(program.fek) + '</div>';
        toggleHidden(appendixDetails, false);
      }
    }
  }

  function updateVisibility() {
    const cards = credentialCards();
    cards.forEach(syncCard);
    const resultEl = byId("result");
    if (resultEl) resultEl.style.display = "none";

    // Legacy fixed-section fallback for regression harnesses without querySelectorAll.
    if (cards.length === 1 && cards[0] === null) {
      const proofType = valueOf("proofType");
      [["pedagogicalDepartmentQuestions","pedagogical_department"],["epathQuestions","epath"],["professorSchoolQuestions","professor_school"],["aeiCertificateQuestions","aei_certificate"],["educationDegreeQuestions","education_msc_phd"],["appendixNamedProgramQuestions","appendix_named_program"]].forEach(pair => {
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
      html += '<p>Βρέθηκαν <strong>' + aggregate.positives.length + '</strong> ανεξάρτητες θετικές διαδρομές. Αρκεί μία έγκυρη διαδρομή· τυχόν αρνητική ή αβέβαιη δεύτερη καταχώριση δεν αναιρεί την θετική.</p>';
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
      if (card && e.target && typeof e.target.getAttribute === "function" && e.target.getAttribute("data-role") === "appendixRow") {
        const exact = safeQs(card, '[data-role="appendixExactMatch"]');
        if (exact) exact.value = "";
      }
      if (card) syncCard(card);
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
