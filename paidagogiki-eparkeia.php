<!DOCTYPE html>
<html lang="el">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Έχω Παιδαγωγική και Διδακτική Επάρκεια;</title>

  <style>
    body {
      font-family: Arial, sans-serif;
      background: #f5f5f5;
      margin: 0;
      padding: 30px;
      color: #222;
    }

    .app-box {
      max-width: 860px;
      margin: auto;
      background: #ffffff;
      padding: 25px;
      border-radius: 14px;
      box-shadow: 0 4px 14px rgba(0,0,0,0.12);
    }

    .back-tools {
      display: inline-block;
      margin-bottom: 18px;
      color: #1f6feb;
      font-weight: bold;
      text-decoration: none;
    }

    .back-tools:hover {
      text-decoration: underline;
    }

    h1 {
      text-align: center;
      font-size: 26px;
      margin-bottom: 10px;
      line-height: 1.25;
    }

    .intro {
      text-align: center;
      color: #555;
      line-height: 1.5;
      margin-bottom: 24px;
    }

    .question {
      margin-bottom: 18px;
      padding: 14px;
      background: #fafafa;
      border: 1px solid #ddd;
      border-radius: 10px;
    }

    label {
      display: block;
      font-weight: bold;
      margin-bottom: 8px;
      line-height: 1.4;
    }

    select {
      width: 100%;
      padding: 11px;
      border-radius: 8px;
      border: 1px solid #ccc;
      font-size: 15px;
      box-sizing: border-box;
      background: #fff;
    }

    button {
      width: 100%;
      margin-top: 20px;
      padding: 14px;
      border: none;
      border-radius: 8px;
      font-size: 17px;
      font-weight: bold;
      cursor: pointer;
      background: #1f6feb;
      color: white;
    }

    button:hover {
      background: #1558c0;
    }

    .result {
      display: none;
      margin-top: 24px;
      padding: 18px;
      border-radius: 10px;
      line-height: 1.6;
    }

    .result h2 {
      margin-top: 0;
      font-size: 20px;
    }

    .result ul {
      margin-top: 8px;
      padding-left: 22px;
    }

    .positive {
      background: #e6f4ea;
      color: #137333;
    }

    .negative {
      background: #fdecea;
      color: #b3261e;
    }

    .warning {
      background: #fff4e5;
      color: #9a5b00;
    }

    .unknown {
      background: #eef4ff;
      color: #174ea6;
    }

    .note-box {
      margin-top: 14px;
      padding: 12px;
      border-radius: 8px;
      background: rgba(255,255,255,0.65);
      border: 1px solid rgba(0,0,0,0.08);
    }

    .small-note {
      margin-top: 18px;
      font-size: 13px;
      color: #666;
      line-height: 1.5;
      text-align: justify;
    }

    .credits {
      margin-top: 24px;
      text-align: center;
      font-size: 13px;
      color: #777;
    }

    .hidden {
      display: none;
    }

    @media (max-width: 760px) {
      body {
        padding: 18px;
      }

      h1 {
        font-size: 23px;
      }
    }
  </style>
  <link rel="stylesheet" href="assets/common.css">
</head>

<body class="edu-ui">
<?php require_once __DIR__ . '/includes/header.php'; ?>

<div class="app-box edu-modernized">
<section class="hero edu-legacy-hero">
<h1>Έχω Παιδαγωγική και Διδακτική Επάρκεια;</h1>
<p class="intro">
    Το εργαλείο παρέχει <strong>ενδεικτικό</strong> έλεγχο με βάση τις κατηγορίες
    αποδεικτικών της Παιδαγωγικής και Διδακτικής Επάρκειας του Παραρτήματος Ε΄.
  </p>
</section>

  <div class="question">
    <label for="specialty">Κλάδος / ειδικότητα</label>
    <select id="specialty">
      <option value="">-- Επιλογή --</option>
      <option value="ΠΕ01">ΠΕ01</option>
      <option value="ΠΕ02">ΠΕ02</option>
      <option value="ΠΕ03">ΠΕ03</option>
      <option value="ΠΕ04">ΠΕ04</option>
      <option value="ΠΕ05">ΠΕ05</option>
      <option value="ΠΕ06">ΠΕ06</option>
      <option value="ΠΕ07">ΠΕ07</option>
      <option value="ΠΕ08">ΠΕ08</option>
      <option value="ΠΕ11">ΠΕ11</option>
      <option value="ΠΕ33">ΠΕ33</option>
      <option value="ΠΕ34">ΠΕ34</option>
      <option value="ΠΕ40">ΠΕ40</option>
      <option value="ΠΕ41">ΠΕ41</option>
      <option value="ΠΕ60">ΠΕ60</option>
      <option value="ΠΕ70">ΠΕ70</option>
      <option value="ΠΕ73">ΠΕ73</option>
      <option value="ΠΕ78">ΠΕ78</option>
      <option value="ΠΕ79.01">ΠΕ79.01</option>
      <option value="ΠΕ79.02">ΠΕ79.02</option>
      <option value="ΠΕ80">ΠΕ80</option>
      <option value="ΠΕ81">ΠΕ81</option>
      <option value="ΠΕ82">ΠΕ82</option>
      <option value="ΠΕ83">ΠΕ83</option>
      <option value="ΠΕ84">ΠΕ84</option>
      <option value="ΠΕ85">ΠΕ85</option>
      <option value="ΠΕ86">ΠΕ86</option>
      <option value="ΠΕ87">ΠΕ87</option>
      <option value="ΠΕ88">ΠΕ88</option>
      <option value="ΠΕ89">ΠΕ89</option>
      <option value="ΠΕ90">ΠΕ90</option>
      <option value="ΠΕ91">ΠΕ91</option>
    </select>
  </div>

  <div class="question">
    <label for="proofType">Με ποιο δικαιολογητικό αποδεικνύεις την Παιδαγωγική και Διδακτική Επάρκεια;</label>
    <select id="proofType" onchange="updateVisibility()">
      <option value="">-- Επιλογή --</option>

      <option value="aei_certificate">
        Βεβαίωση Α.Ε.Ι. μετά από ομάδα μαθημάτων ή ειδικό πρόγραμμα σπουδών
      </option>

      <option value="education_msc_phd">
        Μεταπτυχιακός τίτλος ή διδακτορικό δίπλωμα στις επιστήμες της αγωγής
      </option>

      <option value="old_certificate">
        Πιστοποιητικό παιδαγωγικής επάρκειας της παρ. 5 του άρθρου 4 του ν. 3027/2002
      </option>

      <option value="pedagogical_department">
        Πτυχίο Παιδαγωγικού Τμήματος Α.Ε.Ι. ή αντίστοιχου Τμήματος που αναφέρεται στο Παράρτημα Ε΄
      </option>

      <option value="aspaite">
        Πτυχίο Α.Σ.ΠΑΙ.Τ.Ε.
      </option>

      <option value="article99">
        Πιστοποιητικό Π.Δ.Ε. του άρθρου 99 του ν. 4957/2022
      </option>

      <option value="epath">
        Πτυχίο Ειδικής Παιδαγωγικής Ακαδημίας Θεσσαλονίκης (Ε.Π.Α.Θ.)
      </option>

      <option value="professor_school">
        Πτυχίο καθηγητικής σχολής
      </option>

      <option value="none">
        Δεν διαθέτω κάποιο από τα παραπάνω
      </option>

      <option value="unknown">
        Δεν είμαι σίγουρος/η
      </option>
    </select>
  </div>

  <div id="pedagogicalDepartmentQuestions" class="hidden">
    <div class="question">
      <label for="pedagogicalDepartmentType">Σε ποια κατηγορία Παιδαγωγικού Τμήματος ανήκει ο τίτλος σου;</label>
      <select id="pedagogicalDepartmentType">
        <option value="">-- Επιλογή --</option>
        <option value="pte70">Παιδαγωγικό Τμήμα Δημοτικής Εκπαίδευσης</option>
        <option value="pte60">Τμήμα Εκπαίδευσης και Αγωγής στην Προσχολική Ηλικία</option>
        <option value="fppy">Τμήμα Φιλοσοφίας - Παιδαγωγικής - Ψυχολογίας, εκτός Προγράμματος Ψυχολογίας ΦΠΨ ΕΚΠΑ</option>
        <option value="filosofia_paidagogiki">Τμήμα Φιλοσοφίας - Παιδαγωγικής</option>
        <option value="secondary_pedagogy">Παιδαγωγικό Τμήμα Δευτεροβάθμιας Εκπαίδευσης</option>
        <option value="special_education_teachers">Παιδαγωγικό Τμήμα Ειδικής Αγωγής Πανεπιστημίου Θεσσαλίας, κατεύθυνση Δασκάλων</option>
        <option value="unknown">Δεν είμαι σίγουρος/η</option>
      </select>
    </div>
  </div>

  <div id="epathQuestions" class="hidden">
    <div class="question">
      <label for="epathDate">Η ημερομηνία κτήσης του πτυχίου Ε.Π.Α.Θ. είναι προγενέστερη της 12ης Ιουνίου 2018;</label>
      <select id="epathDate">
        <option value="">-- Επιλογή --</option>
        <option value="before">Ναι, είναι πριν από 12/6/2018</option>
        <option value="after">Όχι, είναι από 12/6/2018 και μετά</option>
        <option value="unknown">Δεν είμαι σίγουρος/η</option>
      </select>
    </div>
  </div>

  <div id="professorSchoolQuestions" class="hidden">
    <div class="question">
      <label for="entryYear">Έτος εισαγωγής στο Τμήμα καθηγητικής σχολής</label>
      <select id="entryYear">
        <option value="">-- Επιλογή --</option>
        <option value="up_to_2014">Μέχρι και το ακαδημαϊκό έτος 2014-2015</option>
        <option value="from_2015">Από το ακαδημαϊκό έτος 2015-2016 και μετά</option>
        <option value="unknown">Δεν είμαι σίγουρος/η</option>
      </select>
    </div>

    <div class="question">
      <label for="graduationYear">Έτος κτήσης πτυχίου</label>
      <select id="graduationYear">
        <option value="">-- Επιλογή --</option>
        <option value="up_to_2017">Έως και το ακαδημαϊκό έτος 2017-2018</option>
        <option value="from_2018">Από το ακαδημαϊκό έτος 2018-2019 και μετά</option>
        <option value="unknown">Δεν είμαι σίγουρος/η</option>
      </select>
    </div>
  </div>

  <div class="question">
    <label for="opsyd">Το σχετικό αποδεικτικό εμφανίζεται ή έχει καταχωριστεί στον Ο.Π.ΣΥ.Δ.;</label>
    <select id="opsyd">
      <option value="">-- Επιλογή --</option>
      <option value="yes">Ναι</option>
      <option value="no">Όχι</option>
      <option value="unknown">Δεν είμαι σίγουρος/η</option>
    </select>
  </div>

  <button type="button" onclick="checkEparkeia()">Έλεγχος Παιδαγωγικής και Διδακτικής Επάρκειας</button>

  <div id="result" class="result" role="status" aria-live="polite"></div>

  <p class="small-note">
    Το αποτέλεσμα είναι ενδεικτικό. Δεν αντικαθιστά την επίσημη προκήρυξη, τις οδηγίες του Α.Σ.Ε.Π.,
    τον έλεγχο του Ο.Π.ΣΥ.Δ. ή τον έλεγχο των αρμόδιων υπηρεσιών. Η αναγνώριση επαγγελματικής
    ισοδυναμίας τίτλου δεν καλύπτει από μόνη της το ζήτημα της Παιδαγωγικής και Διδακτικής Επάρκειας.
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
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
  <script src="assets/common.js"></script>
</body>
</html>