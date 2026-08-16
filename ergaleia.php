<!DOCTYPE html>
<html lang="el">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="Εργαλειοθήκη Εκπαιδευτικού: δωρεάν εργαλεία για ΑΣΕΠ, αναπληρωτές, αποσπάσεις και Δημόσια Ωνάσεια Σχολεία.">
  <title>Εργαλειοθήκη Εκπαιδευτικού</title>

  <style>
    :root {
      --bg: #f4f7fb;
      --card: #ffffff;
      --text: #18202b;
      --muted: #5f6b7a;
      --border: #dfe5ec;
      --blue: #1f6feb;
      --blue-dark: #174ea6;
      --blue-soft: #eef4ff;
      --green: #18794e;
      --green-soft: #eaf7f0;
      --purple: #6941c6;
      --purple-soft: #f1edff;
      --orange: #b45309;
      --orange-soft: #fff4e5;
      --shadow: 0 10px 30px rgba(28, 39, 55, 0.09);
    }

    * {
      box-sizing: border-box;
    }

    body {
      font-family: Arial, Helvetica, sans-serif;
      background: var(--bg);
      margin: 0;
      color: var(--text);
      line-height: 1.55;
    }

    .page-shell {
      max-width: 1180px;
      margin: 0 auto;
      padding: 34px 22px 50px;
    }

    .hero {
      position: relative;
      overflow: hidden;
      background: linear-gradient(135deg, #174ea6 0%, #1f6feb 58%, #3b82f6 100%);
      color: #fff;
      border-radius: 22px;
      padding: 42px 36px;
      box-shadow: var(--shadow);
    }

    .hero::after {
      content: "";
      position: absolute;
      width: 260px;
      height: 260px;
      border-radius: 50%;
      background: rgba(255,255,255,0.08);
      right: -70px;
      top: -100px;
    }

    .hero-kicker {
      display: inline-block;
      padding: 6px 11px;
      margin-bottom: 13px;
      border-radius: 999px;
      background: rgba(255,255,255,0.14);
      border: 1px solid rgba(255,255,255,0.22);
      font-size: 13px;
      font-weight: bold;
      letter-spacing: .02em;
    }

    .hero h1 {
      position: relative;
      z-index: 1;
      margin: 0 0 12px;
      font-size: clamp(30px, 5vw, 44px);
      line-height: 1.12;
    }

    .hero p {
      position: relative;
      z-index: 1;
      margin: 0;
      max-width: 800px;
      color: rgba(255,255,255,0.92);
      font-size: 17px;
    }

    .hero-meta {
      position: relative;
      z-index: 1;
      display: flex;
      gap: 10px;
      flex-wrap: wrap;
      margin-top: 22px;
    }

    .hero-meta span {
      padding: 7px 11px;
      border-radius: 9px;
      background: rgba(0,0,0,0.12);
      font-size: 13px;
      font-weight: bold;
    }

    .toolbar {
      margin-top: 24px;
      background: var(--card);
      border: 1px solid var(--border);
      border-radius: 18px;
      padding: 18px;
      box-shadow: 0 4px 18px rgba(28,39,55,0.05);
    }

    .search-wrap {
      position: relative;
    }

    .search-wrap::before {
      content: "🔎";
      position: absolute;
      left: 15px;
      top: 50%;
      transform: translateY(-50%);
      font-size: 16px;
      pointer-events: none;
    }

    #toolSearch {
      width: 100%;
      border: 1px solid #cfd7e2;
      border-radius: 12px;
      padding: 14px 14px 14px 45px;
      font-size: 16px;
      outline: none;
      background: #fbfcfe;
      color: var(--text);
      transition: border-color .15s ease, box-shadow .15s ease;
    }

    #toolSearch:focus {
      border-color: var(--blue);
      box-shadow: 0 0 0 3px rgba(31,111,235,0.12);
    }

    .filters {
      display: flex;
      gap: 9px;
      flex-wrap: wrap;
      margin-top: 14px;
    }

    .filter-btn {
      appearance: none;
      border: 1px solid #d4dbe5;
      background: #fff;
      color: #465262;
      border-radius: 999px;
      padding: 8px 13px;
      font-size: 14px;
      font-weight: bold;
      cursor: pointer;
      transition: all .15s ease;
    }

    .filter-btn:hover,
    .filter-btn.active {
      color: #fff;
      background: var(--blue);
      border-color: var(--blue);
    }

    .results-line {
      margin: 17px 2px 0;
      color: var(--muted);
      font-size: 14px;
    }

    .tools-grid {
      display: grid;
      grid-template-columns: repeat(3, minmax(0, 1fr));
      gap: 18px;
      margin-top: 18px;
    }

    .tool-card {
      position: relative;
      display: flex;
      flex-direction: column;
      min-height: 265px;
      text-decoration: none;
      color: inherit;
      background: var(--card);
      border: 1px solid var(--border);
      border-radius: 17px;
      padding: 20px;
      box-shadow: 0 4px 16px rgba(28,39,55,0.05);
      transition: transform .16s ease, box-shadow .16s ease, border-color .16s ease;
    }

    .tool-card:hover {
      transform: translateY(-4px);
      box-shadow: 0 12px 28px rgba(28,39,55,0.12);
      border-color: #a8c7fa;
    }

    .card-top {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 10px;
      margin-bottom: 15px;
    }

    .tool-number {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      min-width: 38px;
      height: 32px;
      padding: 0 10px;
      background: var(--blue);
      color: #fff;
      font-weight: bold;
      border-radius: 999px;
      font-size: 13px;
    }

    .category-tag {
      display: inline-block;
      padding: 5px 9px;
      border-radius: 999px;
      font-size: 12px;
      font-weight: bold;
      background: var(--blue-soft);
      color: var(--blue-dark);
    }

    .category-tag.green {
      background: var(--green-soft);
      color: var(--green);
    }

    .category-tag.purple {
      background: var(--purple-soft);
      color: var(--purple);
    }

    .category-tag.orange {
      background: var(--orange-soft);
      color: var(--orange);
    }

    .new-badge {
      position: absolute;
      right: 18px;
      top: -10px;
      padding: 5px 9px;
      border-radius: 999px;
      background: #137333;
      color: #fff;
      font-size: 11px;
      font-weight: bold;
      box-shadow: 0 3px 8px rgba(0,0,0,.12);
    }

    .tool-card h2 {
      font-size: 20px;
      line-height: 1.3;
      margin: 0 0 10px;
    }

    .tool-card p {
      color: var(--muted);
      font-size: 14.5px;
      margin: 0 0 18px;
    }

    .tool-card .button-like {
      margin-top: auto;
      color: var(--blue);
      font-size: 14.5px;
      font-weight: bold;
    }

    .no-results {
      display: none;
      margin-top: 18px;
      padding: 24px;
      text-align: center;
      color: var(--muted);
      background: #fff;
      border: 1px dashed #cfd7e2;
      border-radius: 14px;
    }

    .info-grid {
      display: grid;
      grid-template-columns: 1.15fr .85fr;
      gap: 18px;
      margin-top: 26px;
    }

    .notice,
    .side-box {
      padding: 18px;
      border-radius: 14px;
      line-height: 1.6;
      font-size: 14.5px;
    }

    .notice {
      background: var(--orange-soft);
      color: #7b4900;
      border: 1px solid #f0d4a8;
    }

    .notice strong {
      color: #633b00;
    }

    .side-box {
      background: var(--blue-soft);
      color: var(--blue-dark);
      border: 1px solid #d6e4ff;
    }

    .side-box strong {
      display: block;
      margin-bottom: 5px;
    }

    .side-box a {
      color: var(--blue);
      font-weight: bold;
    }

    .credits {
      text-align: center;
      margin-top: 27px;
      color: #7a8490;
      font-size: 13px;
    }

    .hidden-card {
      display: none !important;
    }

    @media (max-width: 900px) {
      .tools-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
      }

      .info-grid {
        grid-template-columns: 1fr;
      }
    }

    @media (max-width: 620px) {
      .page-shell {
        padding: 18px 13px 35px;
      }

      .hero {
        padding: 30px 22px;
        border-radius: 17px;
      }

      .hero p {
        font-size: 15px;
      }

      .tools-grid {
        grid-template-columns: 1fr;
      }

      .tool-card {
        min-height: 230px;
      }

      .toolbar {
        padding: 14px;
      }
    }
  </style>
  <link rel="stylesheet" href="assets/common.css">
</head>

<body>
  <main class="page-shell">

    <section class="hero">
      <span class="hero-kicker">ΔΩΡΕΑΝ ΕΡΓΑΛΕΙΑ ΓΙΑ ΕΚΠΑΙΔΕΥΤΙΚΟΥΣ</span>
      <h1>Εργαλειοθήκη Εκπαιδευτικού</h1>
      <p> Συγκεντρωμένα εργαλεία υπολογισμού και ελέγχου για προκηρύξεις ΑΣΕΠ,
αναπληρωτές, αποσπάσεις και ειδικές διαδικασίες, όπως τα Δημόσια Ωνάσεια Σχολεία.
<br>Σχεδιασμός &amp; υλοποίηση: Μ. Μαγιολαδίτης (ΠΕ03, ΠΕ86)     </p>
      <div class="hero-meta">
        <span>14 διαθέσιμα εργαλεία</span>
        <span>ΑΣΕΠ 1ΓΕ/2026 &amp; 2ΓΕ/2026</span>
        <span>Αναπληρωτές</span>
        <span>Αποσπάσεις</span>
        <span>Ειδική Αγωγή</span>
        <span>Ωνάσεια</span>
        <span>Ψηφιακό Φροντιστήριο</span>
        <span>ΣΔΕ</span>
      </div>
    </section>

    <section class="toolbar" aria-label="Αναζήτηση και φίλτρα εργαλείων">
      <div class="search-wrap">
        <input
          type="search"
          id="toolSearch"
          placeholder="Αναζήτηση εργαλείου π.χ. μόρια, παράβολο, Ωνάσεια..."
          autocomplete="off"
          aria-label="Αναζήτηση εργαλείου"
        >
      </div>

      <div class="filters" role="group" aria-label="Κατηγορίες εργαλείων">
        <button class="filter-btn active" type="button" data-filter="all">Όλα</button>
        <button class="filter-btn" type="button" data-filter="asep">ΑΣΕΠ</button>
        <button class="filter-btn" type="button" data-filter="eidiki-agogi">Ειδική Αγωγή</button>
        <button class="filter-btn" type="button" data-filter="anaplirotes">Αναπληρωτές</button>
        <button class="filter-btn" type="button" data-filter="apospaseis">Αποσπάσεις</button>
        <button class="filter-btn" type="button" data-filter="sde">ΣΔΕ</button>
        <button class="filter-btn" type="button" data-filter="onaseia">Ωνάσεια</button>
      </div>

      <div class="results-line" id="resultsLine">Εμφανίζονται 14 εργαλεία.</div>
    </section>

    <section class="tools-grid" id="toolsGrid">

      <a class="tool-card"
         href="dikaioma-symmetoxis.php"
         data-category="asep"
         data-search="δικαίωμα συμμετοχής προκήρυξη ΑΣΕΠ προϋποθέσεις υποψήφιος">
        <div class="card-top">
          <span class="tool-number">01</span>
          <span class="category-tag">ΑΣΕΠ</span>
        </div>
        <h2>Έχω δικαίωμα συμμετοχής;</h2>
        <p>
          Απάντησε σε απλές ερωτήσεις για έναν ενδεικτικό έλεγχο των γενικών
          προϋποθέσεων συμμετοχής στις προκηρύξεις εκπαιδευτικών.
        </p>
        <span class="button-like">Άνοιγμα εργαλείου →</span>
      </a>

      <a class="tool-card"
         href="posa-paravola.php"
         data-category="asep"
         data-search="παράβολα παράβολο κόστος ειδικότητα 1ΓΕ 2ΓΕ ΑΣΕΠ">
        <div class="card-top">
          <span class="tool-number">02</span>
          <span class="category-tag">ΑΣΕΠ</span>
        </div>
        <h2>Πόσα παράβολα χρειάζομαι;</h2>
        <p>
          Επίλεξε την ειδικότητα ή τις ειδικότητές σου και δες πόσα παράβολα
          χρειάζεσαι, το συνολικό κόστος και σε ποια προκήρυξη αντιστοιχείς.
        </p>
        <span class="button-like">Άνοιγμα εργαλείου →</span>
      </a>

      <a class="tool-card"
         href="dikaiologitika-titlon-spoudon.php"
         data-category="asep"
         data-search="δικαιολογητικά τίτλοι σπουδών μεταπτυχιακό διδακτορικό integrated master αλλοδαπή ΔΟΑΤΑΠ">
        <div class="card-top">
          <span class="tool-number">03</span>
          <span class="category-tag">ΑΣΕΠ</span>
        </div>
        <h2>Τι δικαιολογητικά χρειάζομαι;</h2>
        <p>
          Οδηγός για δικαιολογητικά μεταπτυχιακών, διδακτορικών, integrated master
          και τίτλων σπουδών της αλλοδαπής.
        </p>
        <span class="button-like">Άνοιγμα εργαλείου →</span>
      </a>

      <a class="tool-card"
         href="ypologismos-morion.php"
         data-category="asep anaplirotes"
         data-search="υπολογισμός μορίων ΑΣΕΠ αναπληρωτές 1ΓΕ 2ΓΕ ακαδημαϊκά ξένες γλώσσες προϋπηρεσία κοινωνικά κριτήρια">
        <div class="card-top">
          <span class="tool-number">04</span>
          <span class="category-tag green">ΑΣΕΠ / Αναπληρωτές</span>
        </div>
        <h2>Υπολογισμός μορίων 1ΓΕ/2026 &amp; 2ΓΕ/2026</h2>
        <p>
          Υπολόγισε τα μόρια για τις προκηρύξεις 1ΓΕ/2026 και 2ΓΕ/2026 με βάση
          ακαδημαϊκά προσόντα, ξένες γλώσσες, προϋπηρεσία και κοινωνικά κριτήρια.
        </p>
        <span class="button-like">Άνοιγμα εργαλείου →</span>
      </a>

      <a class="tool-card"
         href="ypologismos-morion-1gt-2024.php"
         data-category="asep anaplirotes"
         data-search="1ΓΤ 2024 1GT ΤΕ01 ΤΕ02 ΤΕ16 τεχνική εκπαίδευση μουσικής μη ανώτατων ιδρυμάτων υπολογισμός μόρια ΑΣΕΠ προϋπηρεσία κοινωνικά ακαδημαϊκά">
        <span class="new-badge">ΝΕΟ</span>
        <div class="card-top">
          <span class="tool-number">05</span>
          <span class="category-tag green">ΑΣΕΠ / Τ.Ε.</span>
        </div>
        <h2>Υπολογισμός μορίων 1ΓΤ/2024</h2>
        <p>
          Υπολόγισε τα μόρια για τους κλάδους ΤΕ01, ΤΕ02 και ΤΕ16 με βάση
          ακαδημαϊκά προσόντα, προϋπηρεσία και κοινωνικά κριτήρια της 1ΓΤ/2024.
        </p>
        <span class="button-like">Άνοιγμα εργαλείου →</span>
      </a>

      <a class="tool-card"
         href="paidagogiki-eparkeia.php"
         data-category="asep"
         data-search="παιδαγωγική διδακτική επάρκεια ΠΔΕ πρόταξη ΑΣΕΠ">
        <div class="card-top">
          <span class="tool-number">06</span>
          <span class="category-tag">ΑΣΕΠ</span>
        </div>
        <h2>Έχω Παιδαγωγική και Διδακτική Επάρκεια;</h2>
        <p>
          Έλεγξε ενδεικτικά αν διαθέτεις Π.Δ.Ε. και ποιο αποδεικτικό μπορεί να
          χρειάζεται για την πρόταξη στους αξιολογικούς πίνακες.
        </p>
        <span class="button-like">Άνοιγμα εργαλείου →</span>
      </a>

      <a class="tool-card"
         href="ypologismos-morion-onaseia.php"
         data-category="anaplirotes onaseia"
         data-search="Ωνάσεια ΔΗΜΩΣ Δημόσια Ωνάσεια Σχολεία μόρια αναπληρωτή Πρότυπα Πειραματικά ακαδημαϊκά προσόντα">
        <span class="new-badge">ΝΕΟ</span>
        <div class="card-top">
          <span class="tool-number">07</span>
          <span class="category-tag purple">Ωνάσεια</span>
        </div>
        <h2>Μόρια Αναπληρωτή στα Ωνάσεια</h2>
        <p>
          Υπολόγισε τα μόριά σου για τα Δημόσια Ωνάσεια Σχολεία με βάση τα
          ακαδημαϊκά προσόντα ΑΣΕΠ και την προϋπηρεσία σε Πρότυπα/Πειραματικά.
        </p>
        <span class="button-like">Άνοιγμα εργαλείου →</span>
      </a>

      <a class="tool-card"
         href="ypologismos-morion-apospasis-dimos.php"
         data-category="apospaseis onaseia"
         data-search="ΔΗΜΩΣ Ωνάσεια Δημόσια Ωνάσεια Σχολεία απόσπαση μόνιμων εκπαιδευτικών 53 μόρια επιστημονική παιδαγωγική συγγραφικό καινοτόμο έργο">
        <span class="new-badge">ΝΕΟ</span>
        <div class="card-top">
          <span class="tool-number">08</span>
          <span class="category-tag purple">Ωνάσεια / Αποσπάσεις</span>
        </div>
        <h2>Μόρια Απόσπασης στα ΔΗΜ.Ω.Σ.</h2>
        <p>
          Υπολόγισε τα μόρια για απόσπαση μόνιμων εκπαιδευτικών στα Δημόσια
          Ωνάσεια Σχολεία. Μέγιστο σύνολο 53 μόρια στις κατηγορίες Α, Β και Γ.
        </p>
        <span class="button-like">Άνοιγμα εργαλείου →</span>
      </a>

      <a class="tool-card"
         href="ypologismos-morion-apospasis.php"
         data-category="apospaseis"
         data-search="υπολογισμός μόρια απόσπασης εκπαιδευτικών συνυπηρέτηση εντοπιότητα οικογενειακοί λόγοι υπηρεσία">
        <div class="card-top">
          <span class="tool-number">09</span>
          <span class="category-tag orange">Αποσπάσεις</span>
        </div>
        <h2>Υπολογισμός μορίων απόσπασης</h2>
        <p>
          Υπολόγισε ενδεικτικά τα μόρια απόσπασης εκπαιδευτικών με βάση τα
          αντίστοιχα κριτήρια της διαδικασίας.
        </p>
        <span class="button-like">Άνοιγμα εργαλείου →</span>
      </a>

      <a class="tool-card"
         href="ypologismos-morion-3ea-2025.php"
         data-category="asep anaplirotes eidiki-agogi"
         data-search="3ΕΑ 2025 ειδική αγωγή ΕΑΕ κύριος κύριο πίνακας Β επικουρικός πίνακας μόρια ΑΣΕΠ αναπληρωτές ειδική εκπαίδευση">
        <span class="new-badge">ΝΕΟ</span>
        <div class="card-top">
          <span class="tool-number">10</span>
          <span class="category-tag green">ΑΣΕΠ / Ειδική Αγωγή</span>
        </div>
        <h2>Υπολογισμός μορίων 3ΕΑ/2025</h2>
        <p>
          Υπολόγισε τα μόριά σου στην Ειδική Αγωγή και έλεγξε ενδεικτικά αν
          εντάσσεσαι στον Κύριο (Πίνακα Β΄) ή στον Επικουρικό Πίνακα.
        </p>
        <span class="button-like">Άνοιγμα εργαλείου →</span>
      </a>

      <a class="tool-card"
         href="odigos-enstasis.php"
         data-category="asep"
         data-search="ένσταση ενστάσεις ΑΣΕΠ προσωρινοί πίνακες προσωρινών πινάκων παράβολο 50 ευρώ δικαιολογητικά προθεσμία">
        <span class="new-badge">ΝΕΟ</span>
        <div class="card-top">
          <span class="tool-number">11</span>
          <span class="category-tag">ΑΣΕΠ</span>
        </div>
        <h2>Οδηγός ένστασης ΑΣΕΠ</h2>
        <p>
          Γρήγορος οδηγός για ένσταση κατά προσωρινών πινάκων: λόγος ένστασης,
          δικαιολογητικά, παράβολο 50€ και προθεσμίες.
        </p>
        <span class="button-like">Άνοιγμα εργαλείου →</span>
      </a>

      <a class="tool-card"
         href="dikaiologitika-tekna-anapiria.php"
         data-category="asep anaplirotes"
         data-search="δικαιολογητικά τέκνα αναπηρία κοινωνικά κριτήρια ΑΣΕΠ 1ΓΕ 2ΓΕ μοριοδοτούμενα τέκνα αναπηρία ιδίου συζύγου τέκνου">
        <span class="new-badge">ΝΕΟ</span>
        <div class="card-top">
          <span class="tool-number">12</span>
          <span class="category-tag green">ΑΣΕΠ / Αναπληρωτές</span>
        </div>
        <h2>Δικαιολογητικά τέκνων &amp; αναπηρίας</h2>
        <p>
          Ενδεικτικός οδηγός για τα κοινωνικά κριτήρια: μοριοδοτούμενα τέκνα,
          αναπηρία ιδίου, τέκνου ή συζύγου και τα σχετικά δικαιολογητικά.
        </p>
        <span class="button-like">Άνοιγμα εργαλείου →</span>
      </a>

      <a class="tool-card"
         href="ypologismos-morion-apospasis-psifiako-frontistirio.php"
         data-category="apospaseis"
         data-search="ψηφιακό φροντιστήριο απόσπαση αποσπάσεις μόρια μοριοδότηση μόνιμοι εκπαιδευτικοί βιντεοσκοπημένο μάθημα συνέντευξη πανελλαδικές ΤΠΕ">
        <span class="new-badge">ΝΕΟ</span>
        <div class="card-top">
          <span class="tool-number">13</span>
          <span class="category-tag orange">Αποσπάσεις</span>
        </div>
        <h2>Μόρια Απόσπασης στο Ψηφιακό Φροντιστήριο</h2>
        <p>
          Υπολόγισε τη μοριοδότηση έως 100 μονάδες για απόσπαση στο Ψηφιακό Φροντιστήριο:
          γενική παρουσία, επιστημονική κατάρτιση–εμπειρία και βιντεοσκοπημένο μάθημα.
        </p>
        <span class="button-like">Άνοιγμα εργαλείου →</span>
      </a>

      <a class="tool-card"
         href="ypologismos-morion-apospasis-sde.php"
         data-category="apospaseis sde"
         data-search="ΣΔΕ Σχολεία Δεύτερης Ευκαιρίας απόσπαση αποσπάσεις μόρια μοριοδότηση μόνιμοι εκπαιδευτικοί γραμματισμοί ειδικότητες εκπαίδευση ενηλίκων">
        <span class="new-badge">ΝΕΟ</span>
        <div class="card-top">
          <span class="tool-number">14</span>
          <span class="category-tag orange">ΣΔΕ / Αποσπάσεις</span>
        </div>
        <h2>Μόρια Απόσπασης στα ΣΔΕ</h2>
        <p>
          Έλεγξε αν η ειδικότητά σου είναι αποδεκτή και υπολόγισε τη μοριοδότηση έως 40 μόρια
          για απόσπαση στα Σχολεία Δεύτερης Ευκαιρίας.
        </p>
        <span class="button-like">Άνοιγμα εργαλείου →</span>
      </a>

    </section>

    <div class="no-results" id="noResults">
      Δεν βρέθηκε εργαλείο που να ταιριάζει στην αναζήτησή σου.
    </div>

    <section class="info-grid">
      <div class="notice">
        <strong>Σημαντική σημείωση:</strong>
        Τα εργαλεία παρέχουν ενδεικτική πληροφόρηση και δεν αντικαθιστούν τις
        επίσημες προκηρύξεις, εγκυκλίους και οδηγίες των αρμόδιων φορέων.
        Πριν από την οριστική υποβολή αίτησης, ελέγχετε πάντοτε τα επίσημα έγγραφα
        και τα στοιχεία που εμφανίζονται στο ΑΣΕΠ ή/και στο ΟΠΣΥΔ.
      </div>

      <div class="side-box">
        <strong>Ειδικά για 1ΓΕ/2026 &amp; 2ΓΕ/2026</strong>
        Τα εργαλεία ΑΣΕΠ παραμένουν συγκεντρωμένα και στην ειδική σελίδα
        <a href="asep-tools.php">Χρήσιμα εργαλεία 1ΓΕ/2026 &amp; 2ΓΕ/2026</a>.
      </div>
    </section>
</main>

  <script>
    (function () {
      const searchInput = document.getElementById('toolSearch');
      const cards = Array.from(document.querySelectorAll('.tool-card'));
      const filterButtons = Array.from(document.querySelectorAll('.filter-btn'));
      const resultsLine = document.getElementById('resultsLine');
      const noResults = document.getElementById('noResults');

      let activeFilter = 'all';

      function normalizeGreek(text) {
        return (text || '')
          .toLocaleLowerCase('el-GR')
          .normalize('NFD')
          .replace(/[\u0300-\u036f]/g, '')
          .replace(/ς/g, 'σ');
      }

      function updateCards() {
        const query = normalizeGreek(searchInput.value.trim());
        let visible = 0;

        cards.forEach(card => {
          const categories = (card.dataset.category || '').split(/\s+/);
          const haystack = normalizeGreek(
            (card.dataset.search || '') + ' ' + card.textContent
          );

          const matchesFilter = activeFilter === 'all' || categories.includes(activeFilter);
          const matchesSearch = !query || haystack.includes(query);
          const show = matchesFilter && matchesSearch;

          card.classList.toggle('hidden-card', !show);
          if (show) visible++;
        });

        resultsLine.textContent = visible === 1
          ? 'Εμφανίζεται 1 εργαλείο.'
          : 'Εμφανίζονται ' + visible + ' εργαλεία.';

        noResults.style.display = visible === 0 ? 'block' : 'none';
      }

      filterButtons.forEach(button => {
        button.addEventListener('click', () => {
          activeFilter = button.dataset.filter || 'all';

          filterButtons.forEach(btn => {
            const isActive = btn === button;
            btn.classList.toggle('active', isActive);
            btn.setAttribute('aria-pressed', isActive ? 'true' : 'false');
          });

          updateCards();
        });
      });

      searchInput.addEventListener('input', updateCards);
      updateCards();
    })();
  </script>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
</body>
</html>
