<!DOCTYPE html>
<html lang="el">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Υπολογισμός μορίων 1ΓΕ/2026 &amp; 2ΓΕ/2026</title>

  <style>
    body {
      font-family: Arial, sans-serif;
      background: #f5f5f5;
      margin: 0;
      padding: 30px;
      color: #222;
    }

    .app-box {
      max-width: 900px;
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
      font-size: 27px;
      margin-bottom: 10px;
    }

    .intro {
      text-align: center;
      color: #555;
      line-height: 1.5;
      margin-bottom: 24px;
    }

    .section {
      margin-top: 22px;
      padding: 16px;
      border-radius: 12px;
      background: #fafafa;
      border: 1px solid #ddd;
    }

    .section h2 {
      margin-top: 0;
      font-size: 20px;
      color: #174ea6;
    }

    .field-grid {
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: 14px;
    }

    .question {
      margin-bottom: 12px;
    }

    label {
      display: block;
      font-weight: bold;
      margin-bottom: 6px;
      line-height: 1.35;
    }

    input,
    select {
      width: 100%;
      padding: 11px;
      border-radius: 8px;
      border: 1px solid #ccc;
      font-size: 15px;
      box-sizing: border-box;
      background: #fff;
    }

    .note {
      margin-top: 10px;
      font-size: 13px;
      color: #666;
      line-height: 1.45;
    }

    button {
      width: 100%;
      margin-top: 24px;
      padding: 15px;
      border: none;
      border-radius: 8px;
      font-size: 18px;
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
      background: #eef4ff;
      color: #174ea6;
      line-height: 1.6;
    }

    .result.error {
      background: #fdecea;
      color: #b3261e;
      font-weight: bold;
    }

    .result h2 {
      margin-top: 0;
    }

    .breakdown {
      width: 100%;
      border-collapse: collapse;
      margin-top: 12px;
      background: #fff;
    }

    .breakdown th,
    .breakdown td {
      border: 1px solid #d0d7de;
      padding: 9px;
      text-align: left;
      vertical-align: top;
    }

    .breakdown th {
      background: #f1f3f4;
    }

    .total-row {
      font-weight: bold;
      background: #e6f4ea;
      color: #137333;
    }

    .warning {
      margin-top: 16px;
      padding: 12px;
      border-radius: 8px;
      background: #fff4e5;
      color: #9a5b00;
      font-weight: bold;
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

    @media (max-width: 760px) {
      body {
        padding: 18px;
      }

      .field-grid {
        grid-template-columns: 1fr;
      }

      h1 {
        font-size: 24px;
      }
    }
  </style>
</head>

<body>

<div class="app-box">

  <a class="back-tools" href="asep-tools.php">← Επιστροφή στα εργαλεία ΑΣΕΠ</a>

  <h1>Υπολογισμός μορίων 1ΓΕ/2026 &amp; 2ΓΕ/2026</h1>

  <p class="intro">
    Συμπλήρωσε τα στοιχεία σου για έναν <strong>ενδεικτικό</strong> υπολογισμό μορίων
    στους αξιολογικούς πίνακες Γενικής Εκπαίδευσης.
  </p>

  <div class="section">
    <h2>Κλάδος / ειδικότητα</h2>

    <div class="question">
      <label for="specialty">Επίλεξε κλάδο / ειδικότητα</label>
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

    <p class="note">
      Ο κλάδος χρειάζεται για ειδικούς κανόνες, όπως οι εξαιρέσεις στις ξένες γλώσσες
      και η μη μοριοδότηση Η/Υ στον ΠΕ86.
    </p>
  </div>

  <div class="section">
    <h2>Α. Ακαδημαϊκά προσόντα</h2>

    <div class="field-grid">
      <div class="question">
        <label for="degreeGrade">Βαθμός βασικού τίτλου σπουδών</label>
        <input type="number" id="degreeGrade" min="5" max="10" step="0.01" placeholder="π.χ. 7.50">
      </div>

      <div class="question">
        <label for="secondDegree">Δεύτερο πτυχίο Α.Ε.Ι.</label>
        <select id="secondDegree">
          <option value="no">Όχι</option>
          <option value="yes">Ναι</option>
        </select>
      </div>

      <div class="question">
        <label for="phd">Διδακτορικό δίπλωμα</label>
        <select id="phd">
          <option value="no">Όχι</option>
          <option value="yes">Ναι</option>
        </select>
      </div>

      <div class="question">
        <label for="mscCount">Μεταπτυχιακός τίτλος / integrated master</label>
        <select id="mscCount">
          <option value="0">Κανένας</option>
          <option value="1">Ένας τίτλος</option>
          <option value="2">Δύο τίτλοι</option>
        </select>
      </div>
    </div>

    <p class="note">
      Βασικός τίτλος: βαθμός × 2,5. Δεύτερο πτυχίο: 7 μόρια.
      Διδακτορικό: 40 μόρια. Πρώτος μεταπτυχιακός / integrated master: 20 μόρια.
      Δεύτερος μεταπτυχιακός / integrated master: 8 μόρια.
    </p>
  </div>

  <div class="section">
    <h2>Β. Γνώση ξένης γλώσσας</h2>

    <div class="field-grid">
      <div class="question">
        <label for="language1">1η ξένη γλώσσα</label>
        <select id="language1">
          <option value="">-- Καμία --</option>
          <option value="en">Αγγλική</option>
          <option value="fr">Γαλλική</option>
          <option value="de">Γερμανική</option>
          <option value="it">Ιταλική</option>
          <option value="es">Ισπανική</option>
          <option value="other">Άλλη ξένη γλώσσα</option>
        </select>
      </div>

      <div class="question">
        <label for="level1">Επίπεδο 1ης γλώσσας</label>
        <select id="level1">
          <option value="none">-- Κανένα --</option>
          <option value="excellent">Άριστη γνώση</option>
          <option value="very_good">Πολύ καλή γνώση</option>
          <option value="good">Καλή γνώση</option>
        </select>
      </div>

      <div class="question">
        <label for="language2">2η ξένη γλώσσα</label>
        <select id="language2">
          <option value="">-- Καμία --</option>
          <option value="en">Αγγλική</option>
          <option value="fr">Γαλλική</option>
          <option value="de">Γερμανική</option>
          <option value="it">Ιταλική</option>
          <option value="es">Ισπανική</option>
          <option value="other2">Άλλη ξένη γλώσσα</option>
        </select>
      </div>

      <div class="question">
        <label for="level2">Επίπεδο 2ης γλώσσας</label>
        <select id="level2">
          <option value="none">-- Κανένα --</option>
          <option value="excellent">Άριστη γνώση</option>
          <option value="very_good">Πολύ καλή γνώση</option>
          <option value="good">Καλή γνώση</option>
        </select>
      </div>
    </div>

    <p class="note">
      Μοριοδοτούνται μέχρι δύο ξένες γλώσσες. Άριστη: 7 μόρια,
      πολύ καλή: 5 μόρια, καλή: 3 μόρια.
      Η αντίστοιχη γλώσσα δεν μοριοδοτείται στους κλάδους ΠΕ05, ΠΕ06, ΠΕ07, ΠΕ34 και ΠΕ40.
    </p>
  </div>

  <div class="section">
    <h2>Γ. Λοιπά ακαδημαϊκά προσόντα</h2>

    <div class="field-grid">
      <div class="question">
        <label for="computer">Πιστοποιημένη γνώση Η/Υ ή ΤΠΕ Α’ επιπέδου</label>
        <select id="computer">
          <option value="no">Όχι</option>
          <option value="yes">Ναι</option>
        </select>
      </div>

      <div class="question">
        <label for="training">Επιμόρφωση τουλάχιστον 300 ωρών και διάρκειας τουλάχιστον 7 μηνών</label>
        <select id="training">
          <option value="no">Όχι</option>
          <option value="yes">Ναι</option>
        </select>
      </div>
    </div>

    <p class="note">
      Η/Υ ή ΤΠΕ Α’ επιπέδου: 4 μόρια, εκτός από τον κλάδο ΠΕ86.
      Επιμόρφωση: 2 μόρια. Μοριοδοτείται μόνο μία επιμόρφωση.
    </p>
  </div>

  <div class="section">
    <h2>Δ. Εκπαιδευτική προϋπηρεσία</h2>

    <div class="field-grid">
      <div class="question">
        <label for="normalMonths">Μήνες δημόσιας εκπαιδευτικής προϋπηρεσίας</label>
        <input type="number" id="normalMonths" min="0" step="1" value="0" inputmode="numeric" oninput="limitIntegerMonth(this)">
      </div>

      <div class="question">
        <label for="difficultMonths">Μήνες σε δυσπρόσιτα / καταστήματα κράτησης από 2020-2021 και μετά</label>
        <input type="number" id="difficultMonths" min="0" step="1" value="0" inputmode="numeric" oninput="limitIntegerMonth(this)">
      </div>

      <div class="question">
        <label for="threeMonthMonths2020">Μήνες τρίμηνων συμβάσεων 2020-2021</label>
        <input type="number" id="threeMonthMonths2020" min="0" step="1" value="0" inputmode="numeric" oninput="limitIntegerMonth(this)">
      </div>

      <div class="question">
        <label for="threeMonthMonths2021">Μήνες τρίμηνων συμβάσεων 2021-2022</label>
        <input type="number" id="threeMonthMonths2021" min="0" step="1" value="0" inputmode="numeric" oninput="limitIntegerMonth(this)">
      </div>

      <div class="question">
        <label for="threeMonthDifficultMonths2020">Μήνες τρίμηνων συμβάσεων σε δυσπρόσιτα 2020-2021</label>
        <input type="number" id="threeMonthDifficultMonths2020" min="0" step="1" value="0" inputmode="numeric" oninput="limitIntegerMonth(this)">
      </div>

      <div class="question">
        <label for="threeMonthDifficultMonths2021">Μήνες τρίμηνων συμβάσεων σε δυσπρόσιτα 2021-2022</label>
        <input type="number" id="threeMonthDifficultMonths2021" min="0" step="1" value="0" inputmode="numeric" oninput="limitIntegerMonth(this)">
      </div>

      <div class="question">
        <label for="privateMonths">Μήνες προϋπηρεσίας στην ιδιωτική εκπαίδευση</label>
        <input type="number" id="privateMonths" min="0" step="1" value="0" inputmode="numeric" oninput="limitIntegerMonth(this)">
      </div>

      <div class="question">
        <label for="digitalMonths">Μήνες στο Ψηφιακό Φροντιστήριο</label>
        <input type="number" id="digitalMonths" min="0" step="1" value="0" inputmode="numeric" oninput="limitIntegerMonth(this)">
      </div>
    </div>

    <p class="note">
      Κανονική δημόσια προϋπηρεσία: 1 μόριο/μήνα.
      Δυσπρόσιτα από 2020-2021 και μετά: 2 μόρια/μήνα.
      Τρίμηνες συμβάσεις: 1,5 μόριο/μήνα, με ανώτατο όριο 10 μόρια ανά σχολικό έτος.
      Τρίμηνες σε δυσπρόσιτα: 3 μόρια/μήνα, με ανώτατο όριο 20 μόρια ανά σχολικό έτος.
      Ιδιωτική εκπαίδευση: 0,9 μόρια/μήνα.
      Ψηφιακό Φροντιστήριο: 1,5 μόριο/μήνα.
    </p>

    <p class="note">
      <strong>Σημείωση:</strong> Λαμβάνεται υπόψη η εκπαιδευτική προϋπηρεσία σε μήνες χωρίς να υπολογίζονται τα υπόλοιπα ημερών.
      Για τον λόγο αυτό, όλα τα πεδία προϋπηρεσίας δέχονται μόνο ακέραιους μήνες.
    </p>

    <p class="note">
      Μην καταχωρίζεις τους ίδιους μήνες σε περισσότερα από ένα πεδία.
      Οι τρίμηνες συμβάσεις 2020-2021 και 2021-2022 δηλώνονται σε ξεχωριστά πεδία, επειδή εφαρμόζονται χωριστά ετήσια ανώτατα όρια.
      Η εφαρμογή εφαρμόζει ανώτατο όριο 120 μορίων στην προϋπηρεσία.
    </p>
  </div>

  <div class="section">
    <h2>Ε. Κοινωνικά κριτήρια</h2>

    <div class="field-grid">
      <div class="question">
        <label for="children">Αριθμός ανήλικων τέκνων</label>
        <input type="number" id="children" min="0" step="1" value="0">
      </div>

      <div class="question">
        <label for="disability">Ποσοστό αναπηρίας υποψηφίου/συζύγου/τέκνου</label>
        <input type="number" id="disability" min="0" max="100" step="1" value="0">
      </div>
    </div>

    <p class="note">
      Τέκνα: 3 μόρια για κάθε ανήλικο τέκνο.
      Αναπηρία 50% και άνω: ποσοστό αναπηρίας × 0,4.
    </p>
  </div>

  <button onclick="calculatePoints()">Υπολογισμός μορίων</button>

  <div id="result" class="result"></div>

  <p class="small-note">
    Το αποτέλεσμα είναι ενδεικτικό και δεν αντικαθιστά την επίσημη προκήρυξη,
    τον έλεγχο του Α.Σ.Ε.Π., τον Ο.Π.ΣΥ.Δ. ή τον επίσημο πίνακα κατάταξης.
  </p>

  <div class="credits">
    Υλοποίηση/σχεδιασμός: Μάριος Μαγιολαδίτης, 2026
  </div>

</div>

<script>
  function valueOf(id) {
    return document.getElementById(id).value;
  }


  function limitIntegerMonth(input) {
    let value = String(input.value).replace(/[^0-9]/g, "");
    if (value === "") {
      input.value = "";
      return;
    }
    input.value = Math.max(0, parseInt(value, 10));
  }

  function numberOf(id) {
    const value = parseFloat(valueOf(id));
    return isNaN(value) ? 0 : value;
  }

  function formatPoints(value) {
    const rounded = Math.round(value * 100) / 100;
    if (Number.isInteger(rounded)) {
      return rounded.toString();
    }
    return rounded.toFixed(2).replace(".", ",");
  }

  function showError(message) {
    const result = document.getElementById("result");
    result.style.display = "block";
    result.className = "result error";
    result.innerHTML = message;
  }

  function showResult(html) {
    const result = document.getElementById("result");
    result.style.display = "block";
    result.className = "result";
    result.innerHTML = html;
  }

  function yes(id) {
    return valueOf(id) === "yes";
  }

  function calculateLanguagePoints(specialty, warnings, details) {
    const languageNames = {
      en: "Αγγλική",
      fr: "Γαλλική",
      de: "Γερμανική",
      it: "Ιταλική",
      es: "Ισπανική",
      other: "Άλλη ξένη γλώσσα",
      other2: "Άλλη ξένη γλώσσα"
    };

    const levelPoints = {
      none: 0,
      good: 3,
      very_good: 5,
      excellent: 7
    };

    const levelNames = {
      none: "Καμία",
      good: "Καλή",
      very_good: "Πολύ καλή",
      excellent: "Άριστη"
    };

    const excludedLanguageBySpecialty = {
      "ΠΕ05": "fr",
      "ΠΕ06": "en",
      "ΠΕ07": "de",
      "ΠΕ34": "it",
      "ΠΕ40": "es"
    };

    const entries = [
      { language: valueOf("language1"), level: valueOf("level1") },
      { language: valueOf("language2"), level: valueOf("level2") }
    ];

    const bestByLanguage = {};

    entries.forEach(entry => {
      if (!entry.language || entry.level === "none") {
        return;
      }

      const points = levelPoints[entry.level] || 0;

      if (excludedLanguageBySpecialty[specialty] === entry.language) {
        warnings.push(
          "Η " + languageNames[entry.language] + " δεν μοριοδοτείται για τον κλάδο " + specialty + "."
        );
        return;
      }

      const existing = bestByLanguage[entry.language];

      if (!existing || points > existing.points) {
        bestByLanguage[entry.language] = {
          points: points,
          label: languageNames[entry.language] + " - " + levelNames[entry.level]
        };
      }
    });

    const countedLanguages = Object.values(bestByLanguage)
      .sort((a, b) => b.points - a.points)
      .slice(0, 2);

    countedLanguages.forEach(item => {
      details.push(item.label + ": " + item.points + " μόρια");
    });

    return countedLanguages.reduce((sum, item) => sum + item.points, 0);
  }

  function calculatePoints() {
    const warnings = [];
    const specialty = valueOf("specialty");

    if (!specialty) {
      showError("Παρακαλώ επίλεξε κλάδο / ειδικότητα.");
      return;
    }

    const degreeGrade = numberOf("degreeGrade");

    if (!degreeGrade || degreeGrade < 5 || degreeGrade > 10) {
      showError("Παρακαλώ συμπλήρωσε έγκυρο βαθμό βασικού τίτλου σπουδών, από 5 έως 10.");
      return;
    }

    const academicDetails = [];
    let academicRaw = 0;

    const degreePoints = degreeGrade * 2.5;
    academicRaw += degreePoints;
    academicDetails.push("Βασικός τίτλος: " + formatPoints(degreePoints) + " μόρια");

    if (yes("secondDegree")) {
      academicRaw += 7;
      academicDetails.push("Δεύτερο πτυχίο Α.Ε.Ι.: 7 μόρια");
    }

    if (yes("phd")) {
      academicRaw += 40;
      academicDetails.push("Διδακτορικό δίπλωμα: 40 μόρια");
    }

    const mscCount = parseInt(valueOf("mscCount"), 10);

    if (mscCount === 1) {
      academicRaw += 20;
      academicDetails.push("Μεταπτυχιακός τίτλος / integrated master: 20 μόρια");
    }

    if (mscCount === 2) {
      academicRaw += 28;
      academicDetails.push("Πρώτος μεταπτυχιακός / integrated master: 20 μόρια");
      academicDetails.push("Δεύτερος μεταπτυχιακός / integrated master: 8 μόρια");
    }

    const academicTotal = Math.min(academicRaw, 120);

    if (academicRaw > 120) {
      warnings.push("Στα ακαδημαϊκά προσόντα εφαρμόστηκε ανώτατο όριο 120 μορίων.");
    }

    const languageDetails = [];
    const languageTotal = calculateLanguagePoints(specialty, warnings, languageDetails);

    let computerPoints = 0;
    const computerDetails = [];

    if (yes("computer")) {
      if (specialty === "ΠΕ86") {
        warnings.push("Η γνώση Η/Υ δεν μοριοδοτείται για τον κλάδο ΠΕ86.");
      } else {
        computerPoints = 4;
        computerDetails.push("Πιστοποιημένη γνώση Η/Υ / ΤΠΕ Α’ επιπέδου: 4 μόρια");
      }
    }

    let trainingPoints = 0;
    const trainingDetails = [];

    if (yes("training")) {
      trainingPoints = 2;
      trainingDetails.push("Επιμόρφωση τουλάχιστον 300 ωρών και 7 μηνών: 2 μόρια");
    }

    const normalMonths = Math.floor(numberOf("normalMonths"));
    const difficultMonths = Math.floor(numberOf("difficultMonths"));
    const threeMonthMonths2020 = Math.floor(numberOf("threeMonthMonths2020"));
    const threeMonthMonths2021 = Math.floor(numberOf("threeMonthMonths2021"));
    const threeMonthDifficultMonths2020 = Math.floor(numberOf("threeMonthDifficultMonths2020"));
    const threeMonthDifficultMonths2021 = Math.floor(numberOf("threeMonthDifficultMonths2021"));
    const privateMonths = Math.floor(numberOf("privateMonths"));
    const digitalMonths = Math.floor(numberOf("digitalMonths"));

    let serviceRaw = 0;
    const serviceDetails = [];

    if (normalMonths > 0) {
      const points = normalMonths * 1;
      serviceRaw += points;
      serviceDetails.push("Δημόσια εκπαιδευτική προϋπηρεσία: " + formatPoints(points) + " μόρια");
    }

    if (difficultMonths > 0) {
      const points = difficultMonths * 2;
      serviceRaw += points;
      serviceDetails.push("Δυσπρόσιτα / καταστήματα κράτησης: " + formatPoints(points) + " μόρια");
    }

    if (threeMonthMonths2020 > 0) {
      const rawPoints = threeMonthMonths2020 * 1.5;
      const points = Math.min(rawPoints, 10);
      serviceRaw += points;
      serviceDetails.push("Τρίμηνες συμβάσεις 2020-2021: " + formatPoints(points) + " μόρια");
      if (rawPoints > 10) {
        warnings.push("Στις τρίμηνες συμβάσεις 2020-2021 εφαρμόστηκε το ανώτατο όριο των 10 μορίων.");
      }
    }

    if (threeMonthMonths2021 > 0) {
      const rawPoints = threeMonthMonths2021 * 1.5;
      const points = Math.min(rawPoints, 10);
      serviceRaw += points;
      serviceDetails.push("Τρίμηνες συμβάσεις 2021-2022: " + formatPoints(points) + " μόρια");
      if (rawPoints > 10) {
        warnings.push("Στις τρίμηνες συμβάσεις 2021-2022 εφαρμόστηκε το ανώτατο όριο των 10 μορίων.");
      }
    }

    if (threeMonthDifficultMonths2020 > 0) {
      const rawPoints = threeMonthDifficultMonths2020 * 3;
      const points = Math.min(rawPoints, 20);
      serviceRaw += points;
      serviceDetails.push("Τρίμηνες συμβάσεις σε δυσπρόσιτα 2020-2021: " + formatPoints(points) + " μόρια");
      if (rawPoints > 20) {
        warnings.push("Στις τρίμηνες συμβάσεις σε δυσπρόσιτα 2020-2021 εφαρμόστηκε το ανώτατο όριο των 20 μορίων.");
      }
    }

    if (threeMonthDifficultMonths2021 > 0) {
      const rawPoints = threeMonthDifficultMonths2021 * 3;
      const points = Math.min(rawPoints, 20);
      serviceRaw += points;
      serviceDetails.push("Τρίμηνες συμβάσεις σε δυσπρόσιτα 2021-2022: " + formatPoints(points) + " μόρια");
      if (rawPoints > 20) {
        warnings.push("Στις τρίμηνες συμβάσεις σε δυσπρόσιτα 2021-2022 εφαρμόστηκε το ανώτατο όριο των 20 μορίων.");
      }
    }

    if (privateMonths > 0) {
      const points = privateMonths * 0.9;
      serviceRaw += points;
      serviceDetails.push("Ιδιωτική εκπαίδευση: " + formatPoints(points) + " μόρια");
    }

    if (digitalMonths > 0) {
      const points = digitalMonths * 1.5;
      serviceRaw += points;
      serviceDetails.push("Ψηφιακό Φροντιστήριο: " + formatPoints(points) + " μόρια");
      warnings.push("Για το Ψηφιακό Φροντιστήριο υπάρχει ανώτατο όριο μοριοδότησης ανά σχολικό έτος. Ο υπολογισμός εδώ είναι ενδεικτικός.");
    }

    const serviceTotal = Math.min(serviceRaw, 120);

    if (serviceRaw > 120) {
      warnings.push("Στην εκπαιδευτική προϋπηρεσία εφαρμόστηκε ανώτατο όριο 120 μορίων.");
    }

    const children = numberOf("children");
    const disability = numberOf("disability");

    let socialTotal = 0;
    const socialDetails = [];

    if (children > 0) {
      const points = children * 3;
      socialTotal += points;
      socialDetails.push("Ανήλικα τέκνα: " + formatPoints(points) + " μόρια");
    }

    if (disability >= 50) {
      const points = disability * 0.4;
      socialTotal += points;
      socialDetails.push("Αναπηρία " + formatPoints(disability) + "%: " + formatPoints(points) + " μόρια");
    } else if (disability > 0) {
      warnings.push("Η αναπηρία μοριοδοτείται όταν το ποσοστό είναι 50% και άνω.");
    }

    const total =
      academicTotal +
      languageTotal +
      computerPoints +
      trainingPoints +
      serviceTotal +
      socialTotal;

    function detailText(items) {
      return items.length ? items.join("<br>") : "—";
    }

    let html = `
      <h2>Σύνολο μορίων: ${formatPoints(total)}</h2>

      <table class="breakdown">
        <tr>
          <th>Κατηγορία</th>
          <th>Μόρια</th>
          <th>Ανάλυση</th>
        </tr>

        <tr>
          <td>Ακαδημαϊκά προσόντα</td>
          <td>${formatPoints(academicTotal)}</td>
          <td>${detailText(academicDetails)}</td>
        </tr>

        <tr>
          <td>Ξένες γλώσσες</td>
          <td>${formatPoints(languageTotal)}</td>
          <td>${detailText(languageDetails)}</td>
        </tr>

        <tr>
          <td>Γνώση Η/Υ</td>
          <td>${formatPoints(computerPoints)}</td>
          <td>${detailText(computerDetails)}</td>
        </tr>

        <tr>
          <td>Επιμόρφωση</td>
          <td>${formatPoints(trainingPoints)}</td>
          <td>${detailText(trainingDetails)}</td>
        </tr>

        <tr>
          <td>Εκπαιδευτική προϋπηρεσία</td>
          <td>${formatPoints(serviceTotal)}</td>
          <td>${detailText(serviceDetails)}</td>
        </tr>

        <tr>
          <td>Κοινωνικά κριτήρια</td>
          <td>${formatPoints(socialTotal)}</td>
          <td>${detailText(socialDetails)}</td>
        </tr>

        <tr class="total-row">
          <td>Σύνολο</td>
          <td>${formatPoints(total)}</td>
          <td>Ενδεικτικός υπολογισμός</td>
        </tr>
      </table>
    `;

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

</body>
</html>