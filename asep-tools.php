<!DOCTYPE html>
<html lang="el">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Χρήσιμα εργαλεία για τις προκηρύξεις 1ΓΕ/2026 και 2ΓΕ/2026</title>

  <style>
    body {
      font-family: Arial, sans-serif;
      background: #f5f5f5;
      margin: 0;
      padding: 30px;
      color: #222;
    }

    .page-box {
      max-width: 980px;
      margin: auto;
      background: #ffffff;
      padding: 28px;
      border-radius: 16px;
      box-shadow: 0 4px 14px rgba(0,0,0,0.12);
    }

    h1 {
      text-align: center;
      font-size: 28px;
      margin-bottom: 10px;
      line-height: 1.25;
    }

    .intro {
      text-align: center;
      color: #555;
      font-size: 16px;
      line-height: 1.55;
      max-width: 760px;
      margin: 0 auto 28px auto;
    }

	.tools-grid {
	  display: grid;
	  grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
	  gap: 18px;
	  margin-top: 24px;
	}
	
    .tool-card {
      display: block;
      text-decoration: none;
      color: inherit;
      background: #fafafa;
      border: 1px solid #ddd;
      border-radius: 14px;
      padding: 20px;
      transition: transform 0.15s ease, box-shadow 0.15s ease, border-color 0.15s ease;
      min-height: 210px;
    }

    .tool-card:hover {
      transform: translateY(-3px);
      box-shadow: 0 6px 18px rgba(0,0,0,0.14);
      border-color: #1f6feb;
    }

    .tool-number {
      display: inline-block;
      background: #1f6feb;
      color: #fff;
      font-weight: bold;
      padding: 6px 10px;
      border-radius: 999px;
      font-size: 14px;
      margin-bottom: 12px;
    }

    .tool-card h2 {
      font-size: 20px;
      margin: 0 0 10px 0;
      line-height: 1.3;
    }

    .tool-card p {
      color: #555;
      font-size: 15px;
      line-height: 1.5;
      margin: 0 0 14px 0;
    }

    .tool-card .button-like {
      display: inline-block;
      margin-top: 8px;
      color: #1f6feb;
      font-weight: bold;
      font-size: 15px;
    }

    .notice {
      margin-top: 28px;
      padding: 16px;
      border-radius: 10px;
      background: #fff4e5;
      color: #8a5300;
      line-height: 1.55;
      font-size: 15px;
    }

    .notice strong {
      color: #6f4300;
    }

    .quick-links {
      margin-top: 24px;
      padding: 16px;
      border-radius: 10px;
      background: #eef4ff;
      color: #174ea6;
      line-height: 1.6;
    }

    .quick-links h2 {
      margin-top: 0;
      font-size: 18px;
    }

    .quick-links a {
      color: #1f6feb;
      font-weight: bold;
      word-break: break-word;
    }

    .credits {
      margin-top: 26px;
      text-align: center;
      font-size: 13px;
      color: #777;
    }

    @media (max-width: 850px) {
      .tools-grid {
        grid-template-columns: 1fr;
      }

      body {
        padding: 18px;
      }

      .page-box {
        padding: 22px;
      }

      h1 {
        font-size: 24px;
      }
    }
  </style>
  <link rel="stylesheet" href="assets/common.css">
</head>

<body>
<?php require_once __DIR__ . '/includes/header.php'; ?>

  <div class="page-box">

    <h1>Χρήσιμα εργαλεία για τις προκηρύξεις 1ΓΕ/2026 και 2ΓΕ/2026</h1>

    <p class="intro">
      Συγκεντρωμένα απλά εργαλεία για υποψήφιους/ες στις 1ΓΕ/2026 και 2ΓΕ/2026:
      υπολογισμός μορίων και παραβόλων, έλεγχοι συμμετοχής και παιδαγωγικής επάρκειας,
      οδηγοί δικαιολογητικών και ένστασης.
    </p>

    <div class="tools-grid">

      <a class="tool-card" href="dikaioma-symmetoxis.php">
        <span class="tool-number">1</span>
        <h2>Έχω δικαίωμα συμμετοχής;</h2>
        <p>
          Απάντησε σε απλές ερωτήσεις για έναν ενδεικτικό έλεγχο των γενικών
          προϋποθέσεων συμμετοχής στην προκήρυξη.
        </p>
        <span class="button-like">Άνοιγμα εργαλείου →</span>
      </a>

      <a class="tool-card" href="posa-paravola.php">
        <span class="tool-number">2</span>
        <h2>Πόσα παράβολα χρειάζομαι;</h2>
        <p>
          Επίλεξε την ειδικότητα ή τις ειδικότητές σου και δες αν χρειάζεσαι
          1 ή 2 παράβολα, το συνολικό κόστος και σε ποια προκήρυξη αντιστοιχείς.
        </p>
        <span class="button-like">Άνοιγμα εργαλείου →</span>
      </a>

      <a class="tool-card" href="dikaiologitika-titlon-spoudon.php">
        <span class="tool-number">3</span>
        <h2>Τι δικαιολογητικά χρειάζομαι;</h2>
        <p>
          Δες ενδεικτικά τι μπορεί να χρειάζεται για μεταπτυχιακούς,
          διδακτορικούς, integrated master και τίτλους σπουδών της αλλοδαπής.
        </p>
        <span class="button-like">Άνοιγμα εργαλείου →</span>
      </a>

	<a class="tool-card" href="ypologismos-morion.php">
	  <span class="tool-number">4</span>
	  <h2>Υπολογισμός μορίων 1ΓΕ/2026 &amp; 2ΓΕ/2026</h2>
	  <p>
		Συμπλήρωσε τα ακαδημαϊκά προσόντα, τις ξένες γλώσσες, την προϋπηρεσία
		και τα κοινωνικά κριτήρια για έναν ενδεικτικό υπολογισμό μορίων.
	  </p>
	  <span class="button-like">Άνοιγμα εργαλείου →</span>
	</a>
	
	<a class="tool-card" href="paidagogiki-eparkeia.php">
  <span class="tool-number">5</span>
  <h2>Έχω Παιδαγωγική και Διδακτική Επάρκεια;</h2>
  <p>
    Έλεγξε ενδεικτικά αν διαθέτεις Π.Δ.Ε. και τι αποδεικτικό μπορεί να χρειάζεται
    για την πρόταξη στους αξιολογικούς πίνακες.
  </p>
  <span class="button-like">Άνοιγμα εργαλείου →</span>
</a>

      <a class="tool-card" href="dikaiologitika-tekna-anapiria.php">
        <span class="tool-number">6</span>
        <h2>Δικαιολογητικά τέκνων &amp; αναπηρίας</h2>
        <p>
          Ενδεικτικός οδηγός για τα κοινωνικά κριτήρια: μοριοδοτούμενα τέκνα,
          αναπηρία ιδίου, τέκνου ή συζύγου και τα σχετικά δικαιολογητικά.
        </p>
        <span class="button-like">Άνοιγμα εργαλείου →</span>
      </a>

      <a class="tool-card" href="odigos-enstasis.php">
        <span class="tool-number">7</span>
        <h2>Οδηγός ένστασης 1ΓΕ/2026 &amp; 2ΓΕ/2026</h2>
        <p>
          Γρήγορος οδηγός για ένσταση κατά προσωρινών πινάκων, παράβολο 50€,
          βασική τεκμηρίωση και προθεσμίες.
        </p>
        <span class="button-like">Άνοιγμα εργαλείου →</span>
      </a>


    </div>
	
	

    <div class="notice">
      <strong>Σημαντική σημείωση:</strong>
      Τα εργαλεία παρέχουν ενδεικτική πληροφόρηση και δεν αντικαθιστούν την επίσημη προκήρυξη,
      τις οδηγίες του Α.Σ.Ε.Π., τον έλεγχο του Ο.Π.ΣΥ.Δ. ή την προσωπική ευθύνη
      του/της υποψηφίου/ας για την ορθή υποβολή της αίτησης.<br>
	  Διαβάστε και τις <a href="https://info.asep.gr/node/78799">πρόσφατες ανακοινώσεις του ΑΣΕΠ</a>.
    </div>

    <div class="quick-links">
      <h2>Γρήγορη πρόσβαση</h2>
      <p>
        <a href="posa-paravola.php">Πόσα παράβολα χρειάζομαι;</a><br>
        <a href="dikaioma-symmetoxis.php">Έχω δικαίωμα συμμετοχής στην προκήρυξη;</a><br>
        <a href="dikaiologitika-titlon-spoudon.php">Τι δικαιολογητικά χρειάζομαι για τίτλους σπουδών;</a><br>
        <a href="dikaiologitika-tekna-anapiria.php">Δικαιολογητικά τέκνων και αναπηρίας</a><br>
        <a href="odigos-enstasis.php">Οδηγός ένστασης 1ΓΕ/2026 &amp; 2ΓΕ/2026</a>
      </p>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
</body>
</html>