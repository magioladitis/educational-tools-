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

    .calc-actions {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 10px;
      margin-top: 16px;
    }

    .calc-actions .secondary {
      background: #e8edf4;
      color: #263445;
    }

    @media (max-width: 620px) {
      .calc-actions { grid-template-columns: 1fr; }
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

    /* Layout aligned with the 1ΓΤ/2024 reference calculator. */
    body.edu-ui .app-box.edu-modernized {
      max-width: 1060px;
      display: grid;
      grid-template-columns: minmax(0, 1fr) 330px;
      column-gap: 18px;
      align-items: start;
    }

    body.edu-ui .app-box.edu-modernized > .edu-legacy-hero {
      grid-column: 1 / -1;
    }

    body.edu-ui .app-box.edu-modernized > .section,
    body.edu-ui .app-box.edu-modernized > #result,
    body.edu-ui .app-box.edu-modernized > .small-note {
      grid-column: 1;
    }

    .hero .meta {
      display: flex;
      flex-wrap: wrap;
      gap: 8px;
      margin-top: 15px;
    }

    .hero .meta span {
      background: rgba(255,255,255,.14);
      color: #fff;
      padding: 6px 10px;
      border-radius: 999px;
      font-size: 13px;
      font-weight: 700;
    }

    .calc-sidebar {
      grid-column: 2;
      grid-row: 2 / span 12;
      position: sticky;
      top: 16px;
      background: #fff;
      border: 1px solid #dbe3ec;
      border-radius: 16px;
      padding: 20px;
      box-shadow: 0 4px 16px rgba(28,39,55,.05);
    }

    .calc-sidebar .total-summary {
      text-align: center;
      padding: 6px 0 16px;
    }

    .calc-sidebar .total-summary .num {
      font-size: 54px;
      line-height: 1;
      font-weight: 850;
      color: #1f6feb;
      letter-spacing: -.04em;
      font-variant-numeric: tabular-nums;
    }

    .calc-sidebar .total-summary .label {
      margin-top: 6px;
      color: #64748b;
      font-size: 14px;
    }

    .calc-sidebar .result-row {
      display: flex;
      justify-content: space-between;
      gap: 12px;
      padding: 9px 0;
      border-top: 1px solid #edf1f5;
      font-size: 14px;
    }

    .calc-sidebar .result-row strong {
      text-align: right;
      font-variant-numeric: tabular-nums;
    }

    .calc-sidebar .sidebar-status {
      margin: 14px 0 0;
      padding: 10px 11px;
      border-radius: 10px;
      background: #f8fafc;
      border: 1px solid #dbe3ec;
      color: #64748b;
      font-size: 13px;
      line-height: 1.45;
    }

    .calc-sidebar .calculate-btn {
      width: 100%;
      margin-top: 14px;
    }

    .sidebar-actions {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 8px;
      margin-top: 8px;
    }

    .sidebar-actions button {
      width: 100%;
      margin-top: 0;
      padding: 11px;
      font-size: 14px;
      border-radius: 10px;
    }

    .sidebar-actions button:disabled {
      opacity: .55;
      cursor: not-allowed;
    }

    @media (max-width: 900px) {
      body.edu-ui .app-box.edu-modernized {
        grid-template-columns: 1fr;
      }

      body.edu-ui .app-box.edu-modernized > .section,
      body.edu-ui .app-box.edu-modernized > #result,
      body.edu-ui .app-box.edu-modernized > .small-note,
      .calc-sidebar {
        grid-column: 1;
        grid-row: auto;
      }

      .calc-sidebar {
        position: static;
        margin: 0 0 16px;
      }
    }

    @media (max-width: 620px) {
      .sidebar-actions { grid-template-columns: 1fr; }
      .calc-sidebar .total-summary .num { font-size: 46px; }
    }
  </style>
  <link rel="stylesheet" href="assets/common.css">
</head>

<body class="edu-ui">
<?php require_once __DIR__ . '/includes/header.php'; ?>

<div class="app-box edu-modernized">
<section class="hero edu-legacy-hero">
<h1>Υπολογισμός μορίων 1ΓΕ/2026 &amp; 2ΓΕ/2026</h1>
<p class="intro">
    Συμπλήρωσε τα στοιχεία σου για έναν <strong>ενδεικτικό</strong> υπολογισμό μορίων
    στους αξιολογικούς πίνακες Γενικής Εκπαίδευσης.
  </p>
  <div class="meta">
    <span>1ΓΕ/2026</span>
    <span>2ΓΕ/2026</span>
    <span>Ακαδημαϊκά έως 120</span>
    <span>Προϋπηρεσία έως 120</span>
  </div>
</section>

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
        <input type="number" id="degreeGrade" min="5" max="10" step="0.01" placeholder="π.χ. 7,50">
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
        <input type="number" id="difficultMonths" min="0" max="60" step="1" value="0" inputmode="numeric" oninput="limitIntegerMonth(this)">
      </div>

      <div class="question">
        <label for="threeMonthMonths2020">Μήνες τρίμηνων συμβάσεων 2020-2021 <small>(έως 8 μήνες)</small></label>
        <input type="number" id="threeMonthMonths2020" min="0" max="8" step="1" value="0" inputmode="numeric" oninput="limitIntegerMonth(this)">
      </div>

      <div class="question">
        <label for="threeMonthMonths2021">Μήνες τρίμηνων συμβάσεων 2021-2022 <small>(έως 7 μήνες)</small></label>
        <input type="number" id="threeMonthMonths2021" min="0" max="7" step="1" value="0" inputmode="numeric" oninput="limitIntegerMonth(this)">
      </div>

      <div class="question">
        <label for="threeMonthDifficultMonths2020">Μήνες τρίμηνων συμβάσεων σε δυσπρόσιτα 2020-2021 <small>(έως 8 μήνες)</small></label>
        <input type="number" id="threeMonthDifficultMonths2020" min="0" max="8" step="1" value="0" inputmode="numeric" oninput="limitIntegerMonth(this)">
      </div>

      <div class="question">
        <label for="threeMonthDifficultMonths2021">Μήνες τρίμηνων συμβάσεων σε δυσπρόσιτα 2021-2022 <small>(έως 7 μήνες)</small></label>
        <input type="number" id="threeMonthDifficultMonths2021" min="0" max="7" step="1" value="0" inputmode="numeric" oninput="limitIntegerMonth(this)">
      </div>

      <div class="question">
        <label for="privateMonths">Μήνες προϋπηρεσίας στην ιδιωτική εκπαίδευση</label>
        <input type="number" id="privateMonths" min="0" step="1" value="0" inputmode="numeric" oninput="limitIntegerMonth(this)">
      </div>

      <div class="question">
        <label for="digitalMonths">Μήνες στο Ψηφιακό Φροντιστήριο</label>
        <input type="number" id="digitalMonths" min="0" max="10" step="1" value="0" inputmode="numeric" oninput="limitIntegerMonth(this)">
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
        <label for="children">Αριθμός επιλέξιμων τέκνων</label>
        <input type="number" id="children" min="0" step="1" value="0">
      </div>
      <div class="question">
        <label for="candidateDisability">Αναπηρία υποψηφίου/ας (%)</label>
        <input type="number" id="candidateDisability" min="0" max="100" step="1" value="0">
      </div>
      <div class="question">
        <label for="spouseDisability">Αναπηρία συζύγου (%)</label>
        <input type="number" id="spouseDisability" min="0" max="100" step="1" value="0">
      </div>
      <div class="question">
        <label for="childDisability">Υψηλότερο ποσοστό αναπηρίας τέκνου (%)</label>
        <input type="number" id="childDisability" min="0" max="100" step="1" value="0">
      </div>
    </div>

    <div class="question">
      <label><input type="checkbox" id="marriageYears4Plus"> Ο έγγαμος βίος έχει διαρκέσει τουλάχιστον 4 έτη</label>
    </div>

    <div class="question">
      <label><input type="checkbox" id="candidateMentalCondition"> Η αναπηρία του/της υποψηφίου οφείλεται, έστω και κατά ποσοστό, σε ψυχική πάθηση</label>
    </div>

    <p class="note">
      Τέκνα: 3 μόρια ανά επιλέξιμο τέκνο.
      Αναπηρία: από 50% και άνω, ποσοστό × 0,4. Αν υπάρχουν περισσότερα επιλέξιμα πρόσωπα,
      λαμβάνεται μόνο το υψηλότερο έγκυρο ποσοστό. Για σύζυγο απαιτείται έγγαμος βίος τουλάχιστον 4 ετών.
      Η αναπηρία του/της υποψηφίου δεν μοριοδοτείται όταν οφείλεται κατά οποιοδήποτε ποσοστό σε ψυχική πάθηση.
      Η αναπηρία τέκνου μοριοδοτείται ανεξαρτήτως ηλικίας.
    </p>
  </div>

  <aside class="calc-sidebar" aria-live="polite">
    <div class="total-summary">
      <div class="num" id="grandTotal">0,00</div>
      <div class="label">συνολικά μόρια</div>
    </div>

    <div class="result-row"><span>Ακαδημαϊκά</span><strong id="resAcademic">0,00 / 120</strong></div>
    <div class="result-row"><span>Προϋπηρεσία</span><strong id="resService">0,00 / 120</strong></div>
    <div class="result-row"><span>Κοινωνικά</span><strong id="resSocial">0,00</strong></div>
    <div class="result-row"><span>Βαθμός τίτλου</span><strong id="resDegree">0,00</strong></div>

    <div class="sidebar-status" id="sidebarStatus">
      Συμπλήρωσε τα στοιχεία και πάτησε «Υπολογισμός μορίων».
    </div>

    <button type="button" class="calculate-btn" onclick="calculatePoints()">Υπολογισμός μορίων</button>
    <div class="sidebar-actions">
      <button type="button" id="copyResultBtn" onclick="copyResult()" disabled>Αντιγραφή αποτελέσματος</button>
      <button type="button" class="secondary" onclick="resetCalculator()">Μηδενισμός</button>
    </div>
  </aside>

  <div id="result" class="result" role="status" aria-live="polite"></div>

  <p class="small-note">
    Το αποτέλεσμα είναι ενδεικτικό και δεν αντικαθιστά την επίσημη προκήρυξη,
    τον έλεγχο του Α.Σ.Ε.Π., τον Ο.Π.ΣΥ.Δ. ή τον επίσημο πίνακα κατάταξης.
  </p>
</div>

<script src="includes/academic-calculations.js"></script>
<script src="includes/service-calculations.js"></script>
<script src="includes/social-calculations.js"></script>
<script>
  let lastResultText = "";

  function valueOf(id) {
    return document.getElementById(id).value;
  }

  function updateSidebarSummary({ total = 0, academic = 0, service = 0, social = 0, degree = 0, specialty = "" } = {}) {
    document.getElementById("grandTotal").textContent = formatPointsFixed(total);
    document.getElementById("resAcademic").textContent = formatPointsFixed(academic) + " / 120";
    document.getElementById("resService").textContent = formatPointsFixed(service) + " / 120";
    document.getElementById("resSocial").textContent = formatPointsFixed(social);
    document.getElementById("resDegree").textContent = formatPointsFixed(degree);

    const status = document.getElementById("sidebarStatus");
    status.textContent = specialty
      ? "Τελευταίος υπολογισμός για " + specialty + ". Δες την αναλυτική κατανομή κάτω από τη φόρμα."
      : "Συμπλήρωσε τα στοιχεία και πάτησε «Υπολογισμός μορίων».";
  }

  async function copyResult() {
    if (!lastResultText) return;
    const btn = document.getElementById("copyResultBtn");
    try {
      await navigator.clipboard.writeText(lastResultText);
    } catch (error) {
      const textarea = document.createElement("textarea");
      textarea.value = lastResultText;
      textarea.style.position = "fixed";
      textarea.style.opacity = "0";
      document.body.appendChild(textarea);
      textarea.select();
      document.execCommand("copy");
      textarea.remove();
    }
    const old = btn.textContent;
    btn.textContent = "Αντιγράφηκε";
    setTimeout(() => { btn.textContent = old; }, 1400);
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

  function formatPointsFixed(value) {
    return (Math.round((Number(value) + Number.EPSILON) * 100) / 100)
      .toLocaleString("el-GR", { minimumFractionDigits: 2, maximumFractionDigits: 2 });
  }

  function showError(message) {
    const result = document.getElementById("result");
    result.style.display = "block";
    result.className = "result error";
    result.innerHTML = message;

    const status = document.getElementById("sidebarStatus");
    if (status) status.textContent = "Δεν έγινε νέος υπολογισμός: " + message;
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

  const childrenInput = document.getElementById("children");
  if (childrenInput) {
    childrenInput.addEventListener("input", function () {
      if (this.value === "") return;
      this.value = Math.max(0, Math.floor(Number(this.value) || 0));
    });
  }

  function resetCalculator() {
    document.getElementById("specialty").value = "";
    document.getElementById("degreeGrade").value = "";

    ["secondDegree","phd","computer","training"].forEach(id => {
      document.getElementById(id).selectedIndex = 0;
    });
    document.getElementById("mscCount").selectedIndex = 0;
    document.getElementById("language1").value = "";
    document.getElementById("level1").value = "none";
    document.getElementById("language2").value = "";
    document.getElementById("level2").value = "none";

    document.querySelectorAll('input[type="number"]').forEach(el => {
      if (el.id !== "degreeGrade") el.value = 0;
    });
    document.querySelectorAll('input[type="checkbox"]').forEach(el => el.checked = false);

    const result = document.getElementById("result");
    result.style.display = "none";
    result.innerHTML = "";
    result.className = "result";

    lastResultText = "";
    document.getElementById("copyResultBtn").disabled = true;
    updateSidebarSummary();
    document.getElementById("specialty").focus();
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

    let academic;
    try {
      academic = EducationAcademic.calculate({
        specialty: specialty,
        degreeGrade: degreeGrade,
        secondDegree: yes("secondDegree"),
        phd: yes("phd"),
        mscCount: parseInt(valueOf("mscCount"), 10) || 0,
        languages: [
          { language: valueOf("language1"), level: valueOf("level1") },
          { language: valueOf("language2"), level: valueOf("level2") }
        ],
        computer: yes("computer"),
        training: yes("training")
      });
    } catch (error) {
      showError(error.message);
      return;
    }

    warnings.push(...academic.warnings);

    const normalMonths = EducationService.regularPublic(numberOf("normalMonths"));
    const difficultMonths = EducationService.difficult(numberOf("difficultMonths"));
    const threeMonthMonths2020 = EducationService.threeMonthRegular2020(numberOf("threeMonthMonths2020"));
    const threeMonthMonths2021 = EducationService.threeMonthRegular2021(numberOf("threeMonthMonths2021"));
    const threeMonthDifficultMonths2020 = EducationService.threeMonthDifficult2020(numberOf("threeMonthDifficultMonths2020"));
    const threeMonthDifficultMonths2021 = EducationService.threeMonthDifficult2021(numberOf("threeMonthDifficultMonths2021"));
    const privateMonths = EducationService.privateSchool(numberOf("privateMonths"));
    const digitalMonths = EducationService.digitalPerSchoolYear(numberOf("digitalMonths"));

    const serviceDetails = [];
    const serviceParts = [];

    if (normalMonths.months > 0) {
      serviceParts.push(normalMonths.points);
      serviceDetails.push("Δημόσια εκπαιδευτική προϋπηρεσία: " + formatPoints(normalMonths.points) + " μόρια");
    }

    if (difficultMonths.months > 0) {
      serviceParts.push(difficultMonths.points);
      serviceDetails.push("Δυσπρόσιτα / καταστήματα κράτησης: " + formatPoints(difficultMonths.points) + " μόρια");
    }

    if (threeMonthMonths2020.months > 0) {
      serviceParts.push(threeMonthMonths2020.points);
      serviceDetails.push("Τρίμηνες συμβάσεις 2020-2021: " + formatPoints(threeMonthMonths2020.points) + " μόρια");
    }

    if (threeMonthMonths2021.months > 0) {
      serviceParts.push(threeMonthMonths2021.points);
      serviceDetails.push("Τρίμηνες συμβάσεις 2021-2022: " + formatPoints(threeMonthMonths2021.points) + " μόρια");
    }

    if (threeMonthDifficultMonths2020.months > 0) {
      serviceParts.push(threeMonthDifficultMonths2020.points);
      serviceDetails.push("Τρίμηνες συμβάσεις σε δυσπρόσιτα 2020-2021: " + formatPoints(threeMonthDifficultMonths2020.points) + " μόρια");
    }

    if (threeMonthDifficultMonths2021.months > 0) {
      serviceParts.push(threeMonthDifficultMonths2021.points);
      serviceDetails.push("Τρίμηνες συμβάσεις σε δυσπρόσιτα 2021-2022: " + formatPoints(threeMonthDifficultMonths2021.points) + " μόρια");
    }

    if (privateMonths.months > 0) {
      serviceParts.push(privateMonths.points);
      serviceDetails.push("Ιδιωτική εκπαίδευση: " + formatPoints(privateMonths.points) + " μόρια");
    }

    if (digitalMonths.months > 0) {
      serviceParts.push(digitalMonths.points);
      serviceDetails.push("Ψηφιακό Φροντιστήριο: " + formatPoints(digitalMonths.points) + " μόρια");
    }

    const serviceResult = EducationService.cappedTotal(serviceParts);
    const serviceRaw = serviceResult.raw;
    const serviceTotal = serviceResult.points;

    if (serviceRaw > EducationService.RULES.totalMaxPoints) {
      warnings.push("Στην εκπαιδευτική προϋπηρεσία εφαρμόστηκε ανώτατο όριο 120 μορίων.");
    }

    const socialResult = EducationSocial.calculate({
      children: numberOf("children"),
      candidateDisability: numberOf("candidateDisability"),
      spouseDisability: numberOf("spouseDisability"),
      childDisability: numberOf("childDisability"),
      marriageYears4Plus: document.getElementById("marriageYears4Plus").checked,
      candidateMentalCondition: document.getElementById("candidateMentalCondition").checked
    });

    const socialTotal = socialResult.total;
    const socialDetails = [];

    if (socialResult.childrenPoints > 0) {
      socialDetails.push("Επιλέξιμα τέκνα: " + formatPoints(socialResult.childrenPoints) + " μόρια");
    }

    if (socialResult.disabilityPoints > 0) {
      socialDetails.push(
        "Αναπηρία (" + socialResult.highestLabel + " " +
        formatPoints(socialResult.highestDisabilityPercent) + "%): " +
        formatPoints(socialResult.disabilityPoints) + " μόρια"
      );
    }

    warnings.push(...socialResult.warnings);

    const total =
      academic.points +
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
          <td>Τίτλοι σπουδών</td>
          <td>${formatPoints(academic.corePoints)}</td>
          <td>${detailText(academic.coreDetails)}</td>
        </tr>

        <tr>
          <td>Ξένες γλώσσες</td>
          <td>${formatPoints(academic.languagePoints)}</td>
          <td>${detailText(academic.languageDetails)}</td>
        </tr>

        <tr>
          <td>Γνώση Η/Υ</td>
          <td>${formatPoints(academic.computerPoints)}</td>
          <td>${detailText(academic.computerDetails)}</td>
        </tr>

        <tr>
          <td>Επιμόρφωση</td>
          <td>${formatPoints(academic.trainingPoints)}</td>
          <td>${detailText(academic.trainingDetails)}</td>
        </tr>

        <tr>
          <td><strong>Σύνολο ακαδημαϊκών κριτηρίων</strong></td>
          <td><strong>${formatPoints(academic.points)}</strong></td>
          <td>${academic.rawPoints > academic.points ? "Εφαρμόστηκε το ανώτατο όριο των 120 μορίων." : "—"}</td>
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

    updateSidebarSummary({
      total: total,
      academic: academic.points,
      service: serviceTotal,
      social: socialTotal,
      degree: academic.degreePoints || (degreeGrade * 2.5),
      specialty: specialty
    });

    lastResultText = [
      "Υπολογισμός μορίων 1ΓΕ/2026 & 2ΓΕ/2026",
      "Κλάδος / ειδικότητα: " + specialty,
      "Συνολικά μόρια: " + formatPoints(total),
      "Ακαδημαϊκά: " + formatPoints(academic.points) + " / 120",
      "Προϋπηρεσία: " + formatPoints(serviceTotal) + " / 120",
      "Κοινωνικά: " + formatPoints(socialTotal),
      "Βαθμός βασικού τίτλου: " + formatPoints(degreeGrade)
    ].join("\n");
    document.getElementById("copyResultBtn").disabled = false;

    showResult(html);
  }
</script>


<section class="edu-source-card" aria-labelledby="sourcesTitle">
  <h2 id="sourcesTitle">Πηγές / Νομική βάση</h2>
  <p>Προκηρύξεις Α.Σ.Ε.Π. <strong>1ΓΕ/2026</strong> (ΦΕΚ 21/29.04.2026) και <strong>2ΓΕ/2026</strong> (ΦΕΚ 22/29.04.2026), ιδίως το Κεφάλαιο Γ΄ «Κριτήρια Κατάταξης». Η τελική μοριοδότηση προκύπτει από τον επίσημο έλεγχο της αίτησης και των δικαιολογητικών.</p>
  <p class="source-links"><a href="https://info.asep.gr/node/78700" target="_blank" rel="noopener noreferrer">1ΓΕ/2026 — ΑΣΕΠ ↗</a> · <a href="https://info.asep.gr/node/78701" target="_blank" rel="noopener noreferrer">2ΓΕ/2026 — ΑΣΕΠ ↗</a></p>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
  <script src="assets/common.js"></script>
</body>
</html>