<!DOCTYPE html>
<html lang="el">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="Ενδεικτικός υπολογισμός μορίων απόσπασης εκπαιδευτικών από ΠΥΣΠΕ/ΠΥΣΔΕ σε ΠΥΣΠΕ/ΠΥΣΔΕ για το διδακτικό έτος 2026-2027.">
  <title>Υπολογισμός μορίων απόσπασης εκπαιδευτικών</title>

  <style>
    :root {
      --blue: #1f6feb;
      --blue-dark: #174ea6;
      --bg: #f5f5f5;
      --card: #ffffff;
      --section: #fafafa;
      --border: #d9d9d9;
      --text: #222;
      --muted: #666;
      --green-bg: #e6f4ea;
      --green: #137333;
      --warn-bg: #fff4e5;
      --warn: #8a5700;
      --info-bg: #eef4ff;
      --error-bg: #fdecea;
      --error: #b3261e;
    }

    * {
      box-sizing: border-box;
    }

    body {
      font-family: Arial, sans-serif;
      background: var(--bg);
      margin: 0;
      padding: 30px;
      color: var(--text);
    }

    .app-box {
      max-width: 960px;
      margin: auto;
      background: var(--card);
      padding: 26px;
      border-radius: 14px;
      box-shadow: 0 4px 14px rgba(0,0,0,0.12);
    }

    .back-tools {
      display: inline-block;
      margin-bottom: 18px;
      color: var(--blue);
      font-weight: bold;
      text-decoration: none;
    }

    .back-tools:hover {
      text-decoration: underline;
    }

    h1 {
      text-align: center;
      font-size: 28px;
      margin: 0 0 10px;
    }

    .intro {
      text-align: center;
      color: #555;
      line-height: 1.55;
      margin: 0 auto 22px;
      max-width: 820px;
    }

    .official-note {
      margin: 18px 0 24px;
      padding: 14px 16px;
      background: var(--info-bg);
      border-left: 5px solid var(--blue);
      border-radius: 8px;
      line-height: 1.55;
      font-size: 14px;
    }

    .official-note a {
      color: var(--blue);
      font-weight: bold;
    }

    .section {
      margin-top: 22px;
      padding: 17px;
      border-radius: 12px;
      background: var(--section);
      border: 1px solid var(--border);
    }

    .section h2 {
      margin: 0 0 14px;
      font-size: 20px;
      color: var(--blue-dark);
    }

    .section h3 {
      margin: 18px 0 10px;
      font-size: 16px;
    }

    .field-grid {
      display: grid;
      grid-template-columns: repeat(2, minmax(0, 1fr));
      gap: 14px;
    }

    .field-grid.three {
      grid-template-columns: repeat(3, minmax(0, 1fr));
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
      background: #fff;
    }

    input:focus,
    select:focus {
      outline: 2px solid rgba(31,111,235,0.18);
      border-color: var(--blue);
    }

    .check-row {
      display: flex;
      gap: 10px;
      align-items: flex-start;
      padding: 10px 0;
    }

    .check-row input[type="checkbox"] {
      width: auto;
      margin-top: 3px;
      transform: scale(1.15);
    }

    .check-row label {
      margin: 0;
      font-weight: normal;
    }

    .check-row strong {
      color: var(--blue-dark);
    }

    .note {
      margin: 10px 0 0;
      font-size: 13px;
      color: var(--muted);
      line-height: 1.5;
    }

    .mini-table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 12px;
      background: #fff;
      font-size: 14px;
    }

    .mini-table th,
    .mini-table td {
      border: 1px solid #d0d7de;
      padding: 9px;
      text-align: left;
      vertical-align: top;
    }

    .mini-table th {
      background: #f1f3f4;
    }

    .actions {
      display: grid;
      grid-template-columns: 2fr 1fr;
      gap: 12px;
      margin-top: 24px;
    }

    button {
      padding: 15px;
      border: none;
      border-radius: 8px;
      font-size: 17px;
      font-weight: bold;
      cursor: pointer;
    }

    .primary-btn {
      background: var(--blue);
      color: #fff;
    }

    .primary-btn:hover {
      background: #1558c0;
    }

    .secondary-btn {
      background: #e9ecef;
      color: #333;
    }

    .secondary-btn:hover {
      background: #dde1e5;
    }

    .result {
      display: none;
      margin-top: 24px;
      padding: 18px;
      border-radius: 10px;
      background: var(--info-bg);
      color: var(--blue-dark);
      line-height: 1.6;
    }

    .result.error {
      background: var(--error-bg);
      color: var(--error);
      font-weight: bold;
    }

    .result h2 {
      margin-top: 0;
    }

    .score-big {
      text-align: center;
      background: #fff;
      border: 2px solid #c9dcff;
      border-radius: 12px;
      padding: 18px;
      margin-bottom: 16px;
    }

    .score-big .number {
      display: block;
      font-size: 38px;
      font-weight: bold;
      color: var(--blue-dark);
      line-height: 1.1;
    }

    .score-big .caption {
      color: var(--muted);
      font-size: 14px;
      margin-top: 5px;
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

    .breakdown td.points {
      text-align: center;
      font-weight: bold;
      white-space: nowrap;
    }

    .total-row {
      font-weight: bold;
      background: var(--green-bg);
      color: var(--green);
    }

    .warning {
      margin-top: 16px;
      padding: 12px;
      border-radius: 8px;
      background: var(--warn-bg);
      color: var(--warn);
      font-weight: bold;
    }

    .small-note {
      margin-top: 18px;
      font-size: 13px;
      color: var(--muted);
      line-height: 1.55;
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
        padding: 16px;
      }

      .app-box {
        padding: 18px;
      }

      .field-grid,
      .field-grid.three,
      .compact-checks,
      .actions {
        grid-template-columns: 1fr;
      }

      h1 {
        font-size: 24px;
      }

      .breakdown {
        font-size: 13px;
      }
    }

    .status-box {
      margin: 0 0 16px;
      padding: 14px 16px;
      border-radius: 10px;
      line-height: 1.55;
      font-weight: bold;
    }

    .status-box.ok {
      background: var(--green-bg);
      color: var(--green);
      border: 1px solid #b7dfc1;
    }

    .status-box.warn {
      background: var(--warn-bg);
      color: var(--warn);
      border: 1px solid #f0d39a;
    }

    .status-box.stop {
      background: var(--error-bg);
      color: var(--error);
      border: 1px solid #f1b9b5;
    }

    .subtle-box {
      margin-top: 12px;
      padding: 12px;
      border-radius: 8px;
      background: #fff;
      border: 1px dashed #c9c9c9;
      font-size: 14px;
      line-height: 1.5;
    }

    .compact-checks {
      display: grid;
      grid-template-columns: repeat(2, minmax(0, 1fr));
      gap: 0 16px;
    }

    @media print {
      body {
        background: #fff;
        padding: 0;
      }

      .app-box {
        box-shadow: none;
        max-width: none;
      }

      .back-tools,
      .actions {
        display: none;
      }
    }
  </style>
  <link rel="stylesheet" href="assets/common.css">
</head>

<body class="edu-ui">
<?php require_once __DIR__ . '/includes/header.php'; ?>

<div class="app-box edu-modernized">
<section class="hero edu-legacy-hero">
<h1>Υπολογισμός μορίων απόσπασης εκπαιδευτικών</h1>
<p class="intro">
    Υπολόγισε <strong>ενδεικτικά</strong> τα μόριά σου για απόσπαση
    <strong>από ΠΥΣΠΕ/ΠΥΣΔΕ σε ΠΥΣΠΕ/ΠΥΣΔΕ</strong>.
    Ορισμένα κριτήρια εξαρτώνται από τη συγκεκριμένη περιοχή που ζητάς,
    γι’ αυτό ο υπολογισμός πρέπει να γίνεται χωριστά για κάθε περιοχή ενδιαφέροντος.
  </p>
</section>

  <div class="official-note">
    <strong>Πηγές / Νομική βάση:</strong><br>
    <strong>Βάση υπολογισμού:</strong> Εγκύκλιος Υ.ΠΑΙ.Θ.Α. 41297/Ε2/02-04-2026
    για τις αποσπάσεις εκπαιδευτικών του διδακτικού έτους 2026-2027.
    Ο υπολογιστής αφορά την <strong>Ενότητα Α – Αποσπάσεις με κριτήρια μοριοδότησης</strong>
    και δεν υποκαθιστά τον επίσημο έλεγχο της αίτησης.
    <br>
    <a href="https://www.minedu.gov.gr/site/64683-02-04-26-prosklisi-ekpaideftikon-protovathmias-kai-defterovathmias-ekpaidefsis-gia-ypovoli-aitiseon-apospaseon-apo-pyspe-pysde-se-pyspe-pysde-se-domes-e-a-e-ke-d-a-s-y-mousika-kai-kallitexnika-sxoleia-gia-to-didaktiko-etos-2026-2027"
       target="_blank" rel="noopener noreferrer">Επίσημη πρόσκληση Υ.ΠΑΙ.Θ.Α.</a>
  </div>

  <div class="section">
    <h2>Α. Προκαταρκτικός έλεγχος δικαιώματος / κωλύματος</h2>

    <div class="question">
      <label for="appointmentStatus">Κατάσταση ως προς τον διορισμό</label>
      <select id="appointmentStatus">
        <option value="">— Επιλογή —</option>
        <option value="not_new">Δεν είμαι νεοδιόριστος/η που επηρεάζεται από τον ειδικό κανόνα</option>
        <option value="before_2024_09_01">Νεοδιόριστος/η πριν από 01-09-2024</option>
        <option value="sep_2024">Διορισμός από 01-09-2024 έως 30-09-2024 / δεν είμαι βέβαιος/η</option>
        <option value="after_2024_09_30">Νεοδιόριστος/η μετά τις 30-09-2024</option>
      </select>
    </div>

    <p class="note">
      Η εγκύκλιος αναφέρει ρητά ότι όσοι διορίστηκαν πριν από 01-09-2024 μπορούν να υποβάλουν αίτηση
      ανεξάρτητα από τη διετία, ενώ όσοι διορίστηκαν μετά τις 30-09-2024 μπορούν να υποβάλουν αίτηση
      ΠΥΣΠΕ/ΠΥΣΔΕ → ΠΥΣΠΕ/ΠΥΣΔΕ μόνο εφόσον υπάγονται στις προβλεπόμενες κατ’ εξαίρεση περιπτώσεις.
    </p>

    <h3>Βασικά κωλύματα που μπορεί να εμποδίζουν την εξέταση της αίτησης</h3>
    <div class="compact-checks">
      <div class="check-row">
        <input type="checkbox" id="obstacleMusicExclusive">
        <label for="obstacleMusicExclusive">ΠΕ79.01 / ΤΕ16 με αποκλειστικό διορισμό και τοποθέτηση σε Μουσικό Σχολείο από το 2006 και μετά</label>
      </div>
      <div class="check-row">
        <input type="checkbox" id="obstacleLeader">
        <label for="obstacleLeader">Στέλεχος εκπαίδευσης με θητεία που λήγει μετά τις 31-08-2026</label>
      </div>
      <div class="check-row">
        <input type="checkbox" id="obstacleTermDetachment">
        <label for="obstacleTermDetachment">Απόσπαση με θητεία που δεν λήγει έως 31-08-2026</label>
      </div>
      <div class="check-row">
        <input type="checkbox" id="obstacleActiveDetachment">
        <label for="obstacleActiveDetachment">Άλλη απόσπαση που δεν λήγει έως 31-08-2026</label>
      </div>
      <div class="check-row">
        <input type="checkbox" id="obstacleESK">
        <label for="obstacleESK">Κώλυμα διετίας από μη ανάληψη / ανάκληση απόσπασης ή μετάταξης μέσω ΕΣΚ (χωρίς την εξαίρεση λόγων υγείας)</label>
      </div>
      <div class="check-row">
        <input type="checkbox" id="obstacleSuspension">
        <label for="obstacleSuspension">Κατάσταση αργίας ή αναστολή άσκησης καθηκόντων</label>
      </div>
      <div class="check-row">
        <input type="checkbox" id="obstacleEaeGeneral">
        <label for="obstacleEaeGeneral">Διορισμός στην ΕΑΕ χωρίς συμπλήρωση 5 ετών και αίτημα απόσπασης στη Γενική Εκπαίδευση</label>
      </div>
    </div>

    <p class="note">
      Ο έλεγχος είναι βοηθητικός. Για ΠΕ61/ΠΕ71 η εγκύκλιος επισημαίνει ειδικά ότι δεν είναι δυνατή απόσπαση στη Γενική Εκπαίδευση.
    </p>
  </div>

  <div class="section">
    <h2>Β. Έλεγχος πιθανής απόσπασης κατά προτεραιότητα</h2>

    <div class="check-row">
      <input type="checkbox" id="prioritySpecialCategory">
      <label for="prioritySpecialCategory">
        Ανήκω σε <strong>ειδική κατηγορία μετάθεσης</strong> που προβλέπεται από τις διατάξεις στις οποίες παραπέμπει η εγκύκλιος.
      </label>
    </div>

    <div class="check-row">
      <input type="checkbox" id="priorityNewSelfSpouse75">
      <label for="priorityNewSelfSpouse75">
        Είμαι νεοδιόριστος/η και εγώ ή ο/η σύζυγος έχουμε αναπηρία <strong>75% και άνω</strong>.
      </label>
    </div>

    <div class="check-row">
      <input type="checkbox" id="priorityNewChild67">
      <label for="priorityNewChild67">
        Είμαι νεοδιόριστος/η και έχω τέκνο με αναπηρία <strong>67% και άνω</strong>.
      </label>
    </div>

    <div class="question">
      <label for="priorityCoServiceCategory">Κατηγορία κατά προτεραιότητα που συνδέεται με σύζυγο / συμβιούντα</label>
      <select id="priorityCoServiceCategory">
        <option value="none">Καμία από τις παρακάτω</option>
        <option value="uniformed">Σύζυγος/συμβιών στρατιωτικού, ΕΛ.ΑΣ., Πυροσβεστικού, Λιμενικού, προσωπικού Καταστημάτων Κράτησης, πληρώματος ασθενοφόρου ΕΚΑΒ ή άλλης περίπτωσης της κατηγορίας γ</option>
        <option value="judicial">Σύζυγος δικαστικού λειτουργού ή κύριου προσωπικού ΝΣΚ</option>
        <option value="university">Σύζυγος μέλους ΔΕΠ, ΕΔΙΠ, ΕΕΠ ή ΕΤΕΠ</option>
      </select>
    </div>

    <div class="check-row">
      <input type="checkbox" id="priorityElected">
      <label for="priorityElected">
        Είμαι αιρετός/ή ΟΤΑ σε μία από τις προβλεπόμενες ιδιότητες (π.χ. περιφερειακός/δημοτικός σύμβουλος, δήμαρχος κ.λπ.).
      </label>
    </div>

    <div class="check-row">
      <input type="checkbox" id="priorityFirstPreference">
      <label for="priorityFirstPreference">
        Για την κατά προτεραιότητα περίπτωση που βασίζεται σε συνυπηρέτηση, έχω δηλώσει ως <strong>1η προτίμηση</strong> το συγκεκριμένο ΠΥΣΠΕ/ΠΥΣΔΕ.
      </label>
    </div>

    <p class="note">
      Οι κατά προτεραιότητα αιτήσεις εξετάζονται συγκρινόμενες μόνο μεταξύ τους. Για τις περιπτώσεις συνυπηρέτησης,
      αν δεν δηλωθεί ως πρώτη προτίμηση το ΠΥΣΠΕ/ΠΥΣΔΕ όπου υπάρχει η συνυπηρέτηση, δεν ισχύει η κατά προτεραιότητα απόσπαση.
      Στην κατά προτεραιότητα διαδικασία Αθήνα και Θεσσαλονίκη <strong>δεν</strong> αντιμετωπίζονται ενιαία.
    </p>
  </div>

  <div class="section">
    <h2>Περιοχή για την οποία γίνεται ο υπολογισμός</h2>

    <div class="question">
      <label for="requestedArea">ΠΥΣΠΕ / ΠΥΣΔΕ ενδιαφέροντος (προαιρετικό)</label>
      <input type="text" id="requestedArea" placeholder="π.χ. ΠΥΣΔΕ Κέρκυρας">
    </div>

    <p class="note">
      Η συνυπηρέτηση, η εντοπιότητα, ορισμένοι λόγοι υγείας γονέων και οι σπουδές
      μπορεί να δίνουν μόρια μόνο για συγκεκριμένο ΠΥΣΠΕ/ΠΥΣΔΕ.
    </p>
  </div>

  <div class="section">
    <h2>1. Συνολική υπηρεσία</h2>

    <div class="field-grid three">
      <div class="question">
        <label for="serviceYears">Έτη</label>
        <input type="number" id="serviceYears" min="0" step="1" value="0">
      </div>
      <div class="question">
        <label for="serviceMonths">Μήνες</label>
        <input type="number" id="serviceMonths" min="0" max="11" step="1" value="0">
      </div>
      <div class="question">
        <label for="serviceDays">Υπόλοιπο ημερών</label>
        <input type="number" id="serviceDays" min="0" max="30" step="1" value="0">
      </div>
    </div>

    <table class="mini-table">
      <tr><th>Χρόνος υπηρεσίας</th><th>Μοριοδότηση</th></tr>
      <tr><td>1 έως και 10 έτη</td><td>1 μονάδα ανά έτος</td></tr>
      <tr><td>Πάνω από 10 έως και 20 έτη</td><td>1,5 μονάδα ανά έτος</td></tr>
      <tr><td>Πάνω από 20 έτη</td><td>2 μονάδες ανά έτος</td></tr>
    </table>

    <p class="note">
      Ο χρόνος συνολικής υπηρεσίας ταυτίζεται με τον χρόνο που υπολογίζεται στις μεταθέσεις.
      Υπόλοιπο <strong>15 ημερών και άνω</strong> υπολογίζεται ως ένας πλήρης μήνας.
    </p>
  </div>

  <div class="section">
    <h2>2. Κριτήρια που συνδέονται με την περιοχή απόσπασης</h2>

    <h3>Συνυπηρέτηση — 10 μόρια</h3>
    <div class="question">
      <label for="coServiceType">Ποια περίπτωση περιγράφει τον/τη σύζυγο;</label>
      <select id="coServiceType">
        <option value="none">Δεν ζητώ / δεν δικαιούμαι συνυπηρέτηση</option>
        <option value="public_organic">Υπηρετεί οργανικά (όχι με απόσπαση) σε υπηρεσία του δημόσιου τομέα στο συγκεκριμένο ΠΥΣΠΕ/ΠΥΣΔΕ</option>
        <option value="teacher_term">Είναι εκπαιδευτικός που υπηρετεί με θητεία στο συγκεκριμένο ΠΥΣΠΕ/ΠΥΣΔΕ</option>
        <option value="public_contract">Εργάζεται στο δημόσιο με σύμβαση ορισμένου/αορίστου χρόνου ή ως αναπληρωτής/ωρομίσθιος</option>
        <option value="private">Εργάζεται στον ιδιωτικό τομέα</option>
        <option value="unemployed_all_year">Ήταν άνεργος/η σε όλο το τελευταίο έτος</option>
      </select>
    </div>

    <div class="compact-checks">
      <div class="check-row">
        <input type="checkbox" id="coServiceOneYearSameArea">
        <label for="coServiceOneYearSameArea">Για τις περιπτώσεις σύμβασης/ιδιωτικού τομέα, το τελευταίο έτος εργασίας ή ανεργίας αφορά το ίδιο ΠΥΣΠΕ/ΠΥΣΔΕ.</label>
      </div>
      <div class="check-row">
        <input type="checkbox" id="coServiceWorkedDay">
        <label for="coServiceWorkedDay">Υπήρξε τουλάχιστον <strong>1 ημέρα εργασίας</strong> στην περιοχή μέσα στο τελευταίο έτος.</label>
      </div>
    </div>

    <div class="check-row">
      <input type="checkbox" id="locality">
      <label for="locality">
        <strong>Εντοπιότητα</strong> στο συγκεκριμένο ΠΥΣΠΕ/ΠΥΣΔΕ — <strong>4 μόρια</strong>
      </label>
    </div>

    <p class="note">
      Στην απλή μοριοδότηση συνυπηρέτησης, Α΄/Β΄/Γ΄/Δ΄ Αθήνας αντιμετωπίζονται ενιαία και αντίστοιχα
      Α΄/Β΄ Θεσσαλονίκης αντιμετωπίζονται ενιαία. Η εντοπιότητα ισχύει σε επίπεδο ΠΥΣΠΕ/ΠΥΣΔΕ.
    </p>
  </div>

  <div class="section">
    <h2>3. Οικογενειακοί λόγοι</h2>

    <div class="field-grid">
      <div class="question">
        <label for="familyStatus">Οικογενειακή κατάσταση</label>
        <select id="familyStatus">
          <option value="none">Καμία μοριοδοτούμενη περίπτωση</option>
          <option value="married">Έγγαμος/η ή σύμφωνο συμβίωσης — 4 μόρια</option>
          <option value="divorced_custody">Διαζευγμένος/η ή σε διάσταση με νόμιμη επιμέλεια — 4 μόρια</option>
          <option value="widowed_child">Σε χηρεία με άγαμο ανήλικο ή σπουδάζον παιδί — 12 μόρια</option>
          <option value="widowed_nochild">Σε χηρεία χωρίς μοριοδοτούμενο παιδί — 4 μόρια</option>
          <option value="single_parent">Άγαμος/η με άγαμο ανήλικο ή σπουδάζον παιδί — 6 μόρια</option>
        </select>
      </div>
      <div class="question">
        <label for="eligibleChildren">Αριθμός τέκνων που μοριοδοτούνται</label>
        <input type="number" id="eligibleChildren" min="0" step="1" value="0">
      </div>
    </div>

    <p class="note">
      Τέκνα: 5 μόρια για το πρώτο, 6 για το δεύτερο, 8 για το τρίτο και 10 για κάθε επόμενο,
      εφόσον είναι άγαμα ανήλικα ή σπουδάζουν. <strong>Ειδικά για το 2025-2026</strong>, τέκνο που φοιτά
      στη Γ΄ Λυκείου δικαιούται μόρια ακόμη και αν έχει συμπληρώσει το 18ο έτος και δεν έχει ακόμη
      εγγραφεί σε ανώτερη/ανώτατη δημόσια σχολή.
    </p>
  </div>

  <div class="section">
    <h2>4. Σοβαροί λόγοι υγείας</h2>

    <h3>Α. Εκπαιδευτικός, τέκνο ή/και σύζυγος</h3>
    <div class="field-grid">
      <div class="question">
        <label for="healthPerson">Σε ποιο πρόσωπο αφορά η υψηλότερη μοριοδοτούμενη περίπτωση;</label>
        <select id="healthPerson">
          <option value="none">Κανένα</option>
          <option value="self">Τον/την εκπαιδευτικό</option>
          <option value="spouse">Τον/τη σύζυγο</option>
          <option value="child">Τέκνο</option>
        </select>
      </div>
      <div class="question">
        <label for="healthSelfFamily">Ποσοστό αναπηρίας</label>
        <select id="healthSelfFamily">
          <option value="0">Καμία</option>
          <option value="5">50%–66% — 5 μόρια</option>
          <option value="20">67%–79% — 20 μόρια</option>
          <option value="30">80% και άνω — 30 μόρια</option>
        </select>
      </div>
    </div>

    <div class="check-row">
      <input type="checkbox" id="healthChildProtected">
      <label for="healthChildProtected">Αν αφορά τέκνο: είναι προστατευόμενο μέλος ή διαμένει με τον/την εκπαιδευτικό.</label>
    </div>

    <h3>Β. Γονείς του/της εκπαιδευτικού</h3>
    <div class="field-grid">
      <div class="question">
        <label for="healthParents">Υψηλότερη περίπτωση γονέα</label>
        <select id="healthParents">
          <option value="0">Καμία</option>
          <option value="1">Αναπηρία 50%–66% — 1 μόριο</option>
          <option value="3">Αναπηρία 67% και άνω — 3 μόρια</option>
        </select>
      </div>
      <div class="check-row">
        <input type="checkbox" id="parentLocationEligible">
        <label for="parentLocationEligible">Ο γονέας είναι δημότης από διετίας και διαμένει σε δήμο της περιοχής όπου ζητείται η απόσπαση.</label>
      </div>
    </div>

    <div class="check-row">
      <input type="checkbox" id="siblingHealth">
      <label for="siblingHealth">Αδελφός/ή με αναπηρία 67% και άνω και δικαστική απόφαση επιμέλειας / δικαστικής συμπαράστασης — <strong>5 μόρια</strong></label>
    </div>

    <div class="check-row">
      <input type="checkbox" id="ivf">
      <label for="ivf">Θεραπεία εξωσωματικής γονιμοποίησης του/της εκπαιδευτικού ή του/της συζύγου — <strong>3 μόρια</strong></label>
    </div>

    <p class="note">
      Η μοριοδότηση σοβαρών λόγων υγείας δεν γίνεται προσθετικά <strong>εντός της ίδιας κατηγορίας</strong>
      όταν ο λόγος συντρέχει σε περισσότερα του ενός συγγενικά πρόσωπα. Απαιτείται εν ισχύ γνωμάτευση
      των αρμόδιων υγειονομικών επιτροπών ή ΚΕΠΑ. Για γονέα απαιτούνται επιπλέον τα προβλεπόμενα
      δικαιολογητικά εντοπιότητας και μόνιμης κατοικίας.
    </p>
  </div>

  <div class="section">
    <h2>5. Λοιποί λόγοι — σπουδές</h2>

    <div class="question">
      <label for="studyType">Τύπος σπουδών</label>
      <select id="studyType">
        <option value="none">Δεν ζητώ μόρια σπουδών</option>
        <option value="eligible">Μεταπτυχιακές σπουδές ή απόκτηση άλλου τίτλου ΑΕΙ</option>
        <option value="eap">Σπουδές στο Ελληνικό Ανοικτό Πανεπιστήμιο (δεν μοριοδοτούνται)</option>
        <option value="phd">Διδακτορικό (δεν μοριοδοτείται με αυτό το κριτήριο)</option>
      </select>
    </div>

    <div class="compact-checks">
      <div class="check-row">
        <input type="checkbox" id="studyDifferentArea">
        <label for="studyDifferentArea">Η σχολή εδρεύει σε διαφορετική περιοχή από την περιοχή οργανικής μου.</label>
      </div>
      <div class="check-row">
        <input type="checkbox" id="studyRequestedArea">
        <label for="studyRequestedArea">Το ΠΥΣΠΕ/ΠΥΣΔΕ που υπολογίζω είναι εκείνο στο οποίο βρίσκεται η σχολή.</label>
      </div>
      <div class="check-row">
        <input type="checkbox" id="studyWithinDuration">
        <label for="studyWithinDuration">Βρίσκομαι μέσα στον προβλεπόμενο χρόνο φοίτησης.</label>
      </div>
    </div>

    <p class="note">
      Εφόσον πληρούνται όλες οι προϋποθέσεις: <strong>2 μόρια</strong>. Τα ΠΥΣΠΕ/ΠΥΣΔΕ Αττικής και Θεσσαλονίκης
      αντιμετωπίζονται ενιαία για το κριτήριο των σπουδών. Δεν δίνονται οι 2 μονάδες για ΕΑΠ ή διδακτορικό.
    </p>
  </div>

  <div class="actions">
    <button class="primary-btn" type="button" onclick="calculatePoints()">Υπολογισμός μορίων</button>
    <button class="secondary-btn" type="button" onclick="resetCalculator()">Καθαρισμός</button>
  </div>

  <div id="result" class="result" role="status" aria-live="polite"></div>

  <p class="small-note">
    <strong>Σημαντικό:</strong> Το αποτέλεσμα είναι ενδεικτικό. Η εφαρμογή κάνει βοηθητικό έλεγχο ορισμένων
    βασικών κωλυμάτων και περιπτώσεων κατά προτεραιότητα, αλλά δεν αποτελεί πλήρη διοικητικό ή νομικό
    έλεγχο ούτε πιστοποιεί την επάρκεια των δικαιολογητικών. Σε περίπτωση διαφοράς ισχύουν αποκλειστικά
    η επίσημη εγκύκλιος, η εφαρμογή ΟΠΣΥΔ και ο έλεγχος των αρμόδιων υπηρεσιών.
  </p>
</div>

<script>
  let isLiveCalculation = false;
  function valueOf(id) { return document.getElementById(id).value; }
  function numberOf(id) {
    const value = parseFloat(valueOf(id));
    return Number.isFinite(value) ? value : 0;
  }
  function checked(id) { return document.getElementById(id).checked; }

  function formatPoints(value) {
    const truncated = Math.floor((value + Number.EPSILON) * 100) / 100;
    if (Number.isInteger(truncated)) return truncated.toString();
    return truncated.toFixed(2).replace(".", ",").replace(/0$/, "");
  }

  function escapeHtml(text) {
    return text
      .replaceAll("&", "&amp;")
      .replaceAll("<", "&lt;")
      .replaceAll(">", "&gt;")
      .replaceAll('"', "&quot;")
      .replaceAll("'", "&#039;");
  }

  function showError(message) {
    const result = document.getElementById("result");
    result.style.display = "block";
    result.className = "result error";
    result.innerHTML = message;
    if (!isLiveCalculation) result.scrollIntoView({ behavior: "smooth", block: "start" });
  }

  function showResult(html) {
    const result = document.getElementById("result");
    result.style.display = "block";
    result.className = "result";
    result.innerHTML = html;
    if (!isLiveCalculation) result.scrollIntoView({ behavior: "smooth", block: "start" });
  }

  function calculateServicePoints(years, months, days) {
    let totalMonths = (years * 12) + months;
    if (days >= 15) totalMonths += 1;

    const firstBandMonths = Math.min(totalMonths, 120);
    const secondBandMonths = Math.min(Math.max(totalMonths - 120, 0), 120);
    const thirdBandMonths = Math.max(totalMonths - 240, 0);

    const firstBandPoints = firstBandMonths / 12;
    const secondBandPoints = (secondBandMonths / 12) * 1.5;
    const thirdBandPoints = (thirdBandMonths / 12) * 2;

    return {
      totalMonths,
      countedYears: Math.floor(totalMonths / 12),
      countedMonths: totalMonths % 12,
      firstBandPoints,
      secondBandPoints,
      thirdBandPoints,
      total: firstBandPoints + secondBandPoints + thirdBandPoints
    };
  }

  function calculateChildrenPoints(children) {
    let points = 0;
    if (children >= 1) points += 5;
    if (children >= 2) points += 6;
    if (children >= 3) points += 8;
    if (children >= 4) points += (children - 3) * 10;
    return points;
  }

  function familyStatusPoints(status) {
    return ({
      none: 0,
      married: 4,
      divorced_custody: 4,
      widowed_child: 12,
      widowed_nochild: 4,
      single_parent: 6
    })[status] || 0;
  }

  function familyStatusLabel(status) {
    return ({
      none: "Καμία μοριοδοτούμενη οικογενειακή κατάσταση",
      married: "Έγγαμος/η ή σύμφωνο συμβίωσης",
      divorced_custody: "Διαζευγμένος/η ή σε διάσταση με νόμιμη επιμέλεια",
      widowed_child: "Χηρεία με μοριοδοτούμενο παιδί",
      widowed_nochild: "Χηρεία χωρίς μοριοδοτούμενο παιδί",
      single_parent: "Άγαμος/η γονέας με μοριοδοτούμενο παιδί"
    })[status] || "Οικογενειακή κατάσταση";
  }

  function calculateCoService(warnings, info) {
    const type = valueOf("coServiceType");
    if (type === "none") return 0;

    if (type === "unemployed_all_year") {
      warnings.push("Δεν αποδόθηκαν μόρια συνυπηρέτησης: ο/η σύζυγος ήταν άνεργος/η σε όλο το τελευταίο έτος.");
      return 0;
    }

    if (type === "public_organic") {
      info.push("Συνυπηρέτηση: οργανική υπηρεσία συζύγου στον δημόσιο τομέα στην περιοχή.");
      return 10;
    }

    if (type === "teacher_term") {
      info.push("Συνυπηρέτηση: σύζυγος εκπαιδευτικός με θητεία — λαμβάνεται η περιοχή όπου υπηρετεί.");
      return 10;
    }

    if (type === "public_contract" || type === "private") {
      if (!checked("coServiceOneYearSameArea")) {
        warnings.push("Δεν αποδόθηκαν τα 10 μόρια συνυπηρέτησης: δεν επιβεβαιώθηκε ότι το απαιτούμενο διάστημα εργασίας/ανεργίας αφορά το ίδιο ΠΥΣΠΕ/ΠΥΣΔΕ.");
        return 0;
      }
      if (!checked("coServiceWorkedDay")) {
        warnings.push("Δεν αποδόθηκαν τα 10 μόρια συνυπηρέτησης: απαιτείται τουλάχιστον μία ημέρα εργασίας στην περιοχή μέσα στο τελευταίο έτος.");
        return 0;
      }
      info.push("Συνυπηρέτηση: πληρούνται οι δηλωμένες προϋποθέσεις τελευταίου έτους και τουλάχιστον μίας ημέρας εργασίας.");
      return 10;
    }

    return 0;
  }

  function evaluatePriorityAndObstacles(warnings) {
    const appointmentStatus = valueOf("appointmentStatus");
    const obstacleReasons = [];

    const obstacleMap = [
      ["obstacleMusicExclusive", "ΠΕ79.01/ΤΕ16 με αποκλειστικό διορισμό σε Μουσικό Σχολείο"],
      ["obstacleLeader", "θητεία στελέχους εκπαίδευσης που λήγει μετά τις 31-08-2026"],
      ["obstacleTermDetachment", "απόσπαση με θητεία που δεν λήγει έως 31-08-2026"],
      ["obstacleActiveDetachment", "άλλη απόσπαση που δεν λήγει έως 31-08-2026"],
      ["obstacleESK", "κώλυμα διετίας από διαδικασία ΕΣΚ"],
      ["obstacleSuspension", "αργία ή αναστολή άσκησης καθηκόντων"],
      ["obstacleEaeGeneral", "διορισμός στην ΕΑΕ χωρίς 5ετία με αίτημα προς Γενική Εκπαίδευση"]
    ];

    obstacleMap.forEach(([id, label]) => {
      if (checked(id)) obstacleReasons.push(label);
    });

    const specialCategory = checked("prioritySpecialCategory");
    const newSelfSpouse75 = checked("priorityNewSelfSpouse75");
    const newChild67 = checked("priorityNewChild67");
    if (!appointmentStatus) {
      warnings.push("Δεν έχει δηλωθεί η κατάσταση ως προς τον διορισμό. Ο έλεγχος του ειδικού κανόνα νεοδιορίστων δεν μπορεί να ολοκληρωθεί.");
    }
    const isNewAppointee = ["before_2024_09_01","sep_2024","after_2024_09_30"].includes(appointmentStatus);
    const newAppointeeException = specialCategory || (isNewAppointee && (newSelfSpouse75 || newChild67));

    if (appointmentStatus === "after_2024_09_30" && !newAppointeeException) {
      obstacleReasons.push("νεοδιόριστος/η μετά τις 30-09-2024 χωρίς δηλωμένη κατ’ εξαίρεση περίπτωση της παρ. 5α");
    }

    if (appointmentStatus === "sep_2024") {
      warnings.push("Για διορισμό από 01-09 έως 30-09-2024 η συγκεκριμένη διατύπωση της εγκυκλίου δεν επιτρέπει ασφαλή αυτόματο συμπέρασμα από το εργαλείο. Απαιτείται έλεγχος από τη Διεύθυνση Εκπαίδευσης.");
    }

    const priorityReasons = [];
    if (specialCategory) priorityReasons.push("ειδική κατηγορία μετάθεσης");
    if (isNewAppointee && newSelfSpouse75) priorityReasons.push("νεοδιόριστος/η με αναπηρία ιδίου/συζύγου 75%+");
    if (isNewAppointee && newChild67) priorityReasons.push("νεοδιόριστος/η με τέκνο με αναπηρία 67%+");
    if (checked("priorityElected")) priorityReasons.push("αιρετός/ή ΟΤΑ");

    const coPriority = valueOf("priorityCoServiceCategory");
    const coPriorityLabels = {
      uniformed: "κατηγορία συζύγου/συμβιούντος ένστολου ή άλλης περίπτωσης γ",
      judicial: "σύζυγος δικαστικού λειτουργού / κύριου προσωπικού ΝΣΚ",
      university: "σύζυγος μέλους ΔΕΠ/ΕΔΙΠ/ΕΕΠ/ΕΤΕΠ"
    };

    let priorityBlockedByFirstChoice = false;
    if (coPriority !== "none") {
      if (checked("priorityFirstPreference")) {
        priorityReasons.push(coPriorityLabels[coPriority]);
      } else {
        priorityBlockedByFirstChoice = true;
        warnings.push("Η δηλωμένη κατά προτεραιότητα περίπτωση συνυπηρέτησης δεν ενεργοποιήθηκε, επειδή δεν επιβεβαιώθηκε ότι το σχετικό ΠΥΣΠΕ/ΠΥΣΔΕ είναι η 1η προτίμηση.");
      }
    }

    return {
      obstacleReasons,
      priorityReasons,
      priorityBlockedByFirstChoice
    };
  }

  function calculatePoints() {
    const warnings = [];
    const info = [];

    const years = numberOf("serviceYears");
    const months = numberOf("serviceMonths");
    const days = numberOf("serviceDays");

    if (!Number.isInteger(years) || years < 0) return showError("Τα έτη συνολικής υπηρεσίας πρέπει να είναι μη αρνητικός ακέραιος αριθμός.");
    if (!Number.isInteger(months) || months < 0 || months > 11) return showError("Οι μήνες συνολικής υπηρεσίας πρέπει να είναι από 0 έως 11.");
    if (!Number.isInteger(days) || days < 0 || days > 30) return showError("Οι ημέρες πρέπει να είναι από 0 έως 30.");

    const children = numberOf("eligibleChildren");
    if (!Number.isInteger(children) || children < 0) return showError("Ο αριθμός τέκνων πρέπει να είναι μη αρνητικός ακέραιος αριθμός.");

    const requestedArea = valueOf("requestedArea").trim();
    const eligibility = evaluatePriorityAndObstacles(warnings);

    // 1. Συνολική υπηρεσία
    const service = calculateServicePoints(years, months, days);
    const serviceDetails = [];
    if (service.firstBandPoints > 0) serviceDetails.push("Πρώτη κλίμακα (έως 10 έτη): " + formatPoints(service.firstBandPoints));
    if (service.secondBandPoints > 0) serviceDetails.push("Δεύτερη κλίμακα (10–20 έτη): " + formatPoints(service.secondBandPoints));
    if (service.thirdBandPoints > 0) serviceDetails.push("Τρίτη κλίμακα (πάνω από 20 έτη): " + formatPoints(service.thirdBandPoints));

    const countedServiceText = service.countedYears + " έτη" + (service.countedMonths ? " και " + service.countedMonths + " μήνες" : "");
    if (days > 0 && days < 15) warnings.push("Οι " + days + " ημέρες δεν προσμετρήθηκαν, επειδή είναι λιγότερες από 15.");
    if (days >= 15) warnings.push("Οι " + days + " ημέρες υπολογίστηκαν ως ένας επιπλέον πλήρης μήνας.");

    // 2. Συνυπηρέτηση / εντοπιότητα
    const coServicePoints = calculateCoService(warnings, info);
    const localityPoints = checked("locality") ? 4 : 0;

    const normalizedArea = requestedArea.toLocaleLowerCase("el-GR");
    if (coServicePoints > 0 && (normalizedArea.includes("αθην") || normalizedArea.includes("θεσσαλον"))) {
      info.push("Στην απλή μοριοδότηση συνυπηρέτησης, Αθήνα/Θεσσαλονίκη εφαρμόζουν τον ειδικό ενιαίο κανόνα της εγκυκλίου.");
    }

    // 3. Οικογενειακοί λόγοι
    const familyStatus = valueOf("familyStatus");
    const familyBasePoints = familyStatusPoints(familyStatus);
    const childrenPoints = calculateChildrenPoints(children);
    const familyTotal = familyBasePoints + childrenPoints;

    if ((familyStatus === "widowed_child" || familyStatus === "single_parent") && children === 0) {
      warnings.push("Η επιλεγμένη οικογενειακή κατάσταση προϋποθέτει μοριοδοτούμενο παιδί, αλλά ο αριθμός τέκνων είναι 0.");
    }

    // 4. Υγεία
    const healthPerson = valueOf("healthPerson");
    let selfFamilyHealthPoints = parseInt(valueOf("healthSelfFamily"), 10) || 0;
    if (selfFamilyHealthPoints > 0 && healthPerson === "none") {
      warnings.push("Δεν αποδόθηκαν μόρια στην κατηγορία εκπαιδευτικού/συζύγου/τέκνου επειδή δεν επιλέχθηκε το πρόσωπο στο οποίο αφορά η αναπηρία.");
      selfFamilyHealthPoints = 0;
    }
    if (selfFamilyHealthPoints > 0 && healthPerson === "child" && !checked("healthChildProtected")) {
      warnings.push("Δεν αποδόθηκαν μόρια υγείας τέκνου επειδή δεν επιβεβαιώθηκε ότι είναι προστατευόμενο μέλος ή διαμένει με τον/την εκπαιδευτικό.");
      selfFamilyHealthPoints = 0;
    }

    let parentHealthPoints = parseInt(valueOf("healthParents"), 10) || 0;
    if (parentHealthPoints > 0 && !checked("parentLocationEligible")) {
      warnings.push("Δεν αποδόθηκαν μόρια υγείας γονέα επειδή δεν επιβεβαιώθηκαν οι τοπικές προϋποθέσεις (δημότης από διετίας και διαμονή στην περιοχή). ");
      parentHealthPoints = 0;
    }

    const siblingHealthPoints = checked("siblingHealth") ? 5 : 0;
    const ivfPoints = checked("ivf") ? 3 : 0;
    const healthTotal = selfFamilyHealthPoints + parentHealthPoints + siblingHealthPoints + ivfPoints;

    // 5. Σπουδές
    const studyType = valueOf("studyType");
    let studiesPoints = 0;
    if (studyType === "eligible") {
      if (checked("studyDifferentArea") && checked("studyRequestedArea") && checked("studyWithinDuration")) {
        studiesPoints = 2;
      } else {
        warnings.push("Δεν αποδόθηκαν τα 2 μόρια σπουδών επειδή δεν επιβεβαιώθηκαν όλες οι απαιτούμενες προϋποθέσεις.");
      }
    } else if (studyType === "eap") {
      warnings.push("Οι σπουδές στο ΕΑΠ δεν μοριοδοτούνται με το κριτήριο των 2 μονάδων.");
    } else if (studyType === "phd") {
      warnings.push("Η απόκτηση διδακτορικού τίτλου δεν μοριοδοτείται με το κριτήριο των 2 μονάδων σπουδών.");
    }

    const total = service.total + coServicePoints + localityPoints + familyTotal + healthTotal + studiesPoints;

    const familyAnalysis = [];
    if (familyBasePoints > 0) familyAnalysis.push(familyStatusLabel(familyStatus) + ": " + formatPoints(familyBasePoints) + " μόρια");
    if (childrenPoints > 0) familyAnalysis.push(children + " τέκνο/τέκνα: " + formatPoints(childrenPoints) + " μόρια");

    const healthAnalysis = [];
    if (selfFamilyHealthPoints > 0) healthAnalysis.push("Εκπαιδευτικός / τέκνο / σύζυγος: " + selfFamilyHealthPoints + " μόρια");
    if (parentHealthPoints > 0) healthAnalysis.push("Γονέας εκπαιδευτικού: " + parentHealthPoints + " μόρια");
    if (siblingHealthPoints > 0) healthAnalysis.push("Αδελφός/ή: 5 μόρια");
    if (ivfPoints > 0) healthAnalysis.push("Εξωσωματική γονιμοποίηση: 3 μόρια");

    let statusHtml = "";
    if (eligibility.obstacleReasons.length > 0) {
      statusHtml = `<div class="status-box stop">🔴 Πιθανό κώλυμα εξέτασης της αίτησης:<br>${eligibility.obstacleReasons.map(x => "• " + escapeHtml(x)).join("<br>")}<br><span style="font-weight:normal">Τα μόρια εμφανίζονται μόνο πληροφοριακά· απαιτείται έλεγχος από την αρμόδια Διεύθυνση Εκπαίδευσης.</span></div>`;
    } else if (eligibility.priorityReasons.length > 0) {
      statusHtml = `<div class="status-box ok">🟢 Πιθανή υπαγωγή σε απόσπαση κατά προτεραιότητα:<br>${eligibility.priorityReasons.map(x => "• " + escapeHtml(x)).join("<br>")}<br><span style="font-weight:normal">Η κατά προτεραιότητα διαδικασία δεν προσθέτει μόρια· αλλάζει τον τρόπο εξέτασης της αίτησης.</span></div>`;
    } else if (eligibility.priorityBlockedByFirstChoice) {
      statusHtml = `<div class="status-box warn">🟠 Δηλώθηκε πιθανή κατηγορία κατά προτεραιότητα λόγω συνυπηρέτησης, αλλά δεν επιβεβαιώθηκε η απαιτούμενη 1η προτίμηση.</div>`;
    } else {
      statusHtml = `<div class="status-box warn">ℹ️ Με βάση μόνο τις απαντήσεις που δόθηκαν, δεν εντοπίστηκε από το εργαλείο βασικό κώλυμα ή ενεργή κατηγορία κατά προτεραιότητα. Αυτό δεν αποτελεί επίσημη πιστοποίηση.</div>`;
    }

    const areaText = requestedArea ? " για <strong>" + escapeHtml(requestedArea) + "</strong>" : "";

    let html = statusHtml + `
      <div class="score-big">
        <span class="number">${formatPoints(total)}</span>
        <div class="caption">ενδεικτικά μόρια απόσπασης${areaText}</div>
      </div>

      <h2>Ανάλυση μοριοδότησης</h2>
      <table class="breakdown">
        <tr><th>Κριτήριο</th><th>Μόρια</th><th>Ανάλυση</th></tr>
        <tr><td>Συνολική υπηρεσία</td><td class="points">${formatPoints(service.total)}</td><td>${countedServiceText}${serviceDetails.length ? "<br>" + serviceDetails.join("<br>") : ""}</td></tr>
        <tr><td>Συνυπηρέτηση</td><td class="points">${formatPoints(coServicePoints)}</td><td>${coServicePoints ? "Πληρούνται οι δηλωμένες προϋποθέσεις για τη συγκεκριμένη περιοχή." : "—"}</td></tr>
        <tr><td>Εντοπιότητα</td><td class="points">${formatPoints(localityPoints)}</td><td>${localityPoints ? "Δηλώθηκε ότι πληρούνται οι προϋποθέσεις εντοπιότητας." : "—"}</td></tr>
        <tr><td>Οικογενειακοί λόγοι</td><td class="points">${formatPoints(familyTotal)}</td><td>${familyAnalysis.length ? familyAnalysis.join("<br>") : "—"}</td></tr>
        <tr><td>Σοβαροί λόγοι υγείας</td><td class="points">${formatPoints(healthTotal)}</td><td>${healthAnalysis.length ? healthAnalysis.join("<br>") : "—"}</td></tr>
        <tr><td>Σπουδές</td><td class="points">${formatPoints(studiesPoints)}</td><td>${studiesPoints ? "Πληρούνται οι δηλωμένες προϋποθέσεις: 2 μόρια" : "—"}</td></tr>
        <tr class="total-row"><td>ΣΥΝΟΛΟ</td><td class="points">${formatPoints(total)}</td><td>Ενδεικτικός υπολογισμός</td></tr>
      </table>
    `;

    if (info.length > 0) {
      html += `<div class="subtle-box"><strong>Χρήσιμες επισημάνσεις:</strong><br>${info.map(x => "• " + escapeHtml(x)).join("<br>")}</div>`;
    }

    if (warnings.length > 0) {
      html += `<div class="warning">Προσοχή:<br>${warnings.map(w => "• " + escapeHtml(w)).join("<br>")}</div>`;
    }

    showResult(html);
  }

  function liveCalculatePoints() {
    isLiveCalculation = true;
    try { calculatePoints(); } finally { isLiveCalculation = false; }
  }

  function resetCalculator() {
    document.querySelectorAll('input[type="checkbox"]').forEach(el => el.checked = false);
    document.querySelectorAll('input[type="number"]').forEach(el => el.value = 0);
    document.getElementById("requestedArea").value = "";

    const defaults = {
      appointmentStatus: "",
      priorityCoServiceCategory: "none",
      coServiceType: "none",
      familyStatus: "none",
      healthPerson: "none",
      healthSelfFamily: "0",
      healthParents: "0",
      studyType: "none"
    };
    Object.entries(defaults).forEach(([id, val]) => document.getElementById(id).value = val);

    const result = document.getElementById("result");
    result.style.display = "none";
    result.innerHTML = "";
    result.className = "result";
    window.scrollTo({ top: 0, behavior: "smooth" });
  }

  document.addEventListener("input", event => {
    if (event.target && event.target.matches("input, select")) liveCalculatePoints();
  });
  document.addEventListener("change", event => {
    if (event.target && event.target.matches("input, select")) liveCalculatePoints();
  });
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
  <script src="assets/common.js"></script>
</body>
</html>
