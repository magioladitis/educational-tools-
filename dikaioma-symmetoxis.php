<!DOCTYPE html>
<html lang="el">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Έχω δικαίωμα συμμετοχής στις προκηρύξεις 1ΓΕ/2026, 2ΓΕ/2026;</title>

  <style>
    body {
      font-family: Arial, sans-serif;
      background: #f5f5f5;
      margin: 0;
      padding: 30px;
    }

    .app-box {
      max-width: 760px;
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
    }

    .intro {
      text-align: center;
      color: #555;
      line-height: 1.5;
      margin-bottom: 25px;
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

    select,
    input[type="number"] {
      width: 100%;
      padding: 11px;
      border-radius: 8px;
      border: 1px solid #ccc;
      font-size: 15px;
      box-sizing: border-box;
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
      margin-top: 24px;
      padding: 18px;
      border-radius: 10px;
      font-size: 18px;
      font-weight: bold;
      text-align: center;
      display: none;
      line-height: 1.5;
    }

    .eligible {
      background: #e6f4ea;
      color: #137333;
    }

    .not-eligible {
      background: #fdecea;
      color: #b3261e;
    }

    .conditional {
      background: #fff4e5;
      color: #9a5b00;
    }

    .unknown {
      background: #eef4ff;
      color: #174ea6;
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
  </style>
  <link rel="stylesheet" href="assets/common.css">
</head>

<body>
<?php require_once __DIR__ . '/includes/header.php'; ?>

<div class="app-box">
<h1>Έχω δικαίωμα συμμετοχής στις προκηρύξεις 1ΓΕ/2026 &amp; 2ΓΕ/2026;</h1>

<p class="intro">
  Το παρόν εργαλείο παρέχει έναν ενδεικτικό έλεγχο των γενικών προϋποθέσεων συμμετοχής
  στις <a href="https://search.et.gr/el/fek/?fekId=798251">1ΓΕ/2026</a> και <a href="https://search.et.gr/el/fek/?fekId=798194">2ΓΕ/2026</a> και δεν αντικαθιστά την επίσημη προκήρυξη του Α.Σ.Ε.Π. ούτε την προσωπική ευθύνη
  του/της υποψηφίου/ας για την ορθή υποβολή της αίτησης.
</p>
  <p class="intro">
    Απάντησε στις παρακάτω ερωτήσεις για έναν ενδεικτικό έλεγχο των γενικών προϋποθέσεων συμμετοχής.
  </p>

	<div class="question">
	  <label for="birthYear">Έτος γέννησης υποψηφίου/ας</label>
	  <input type="number" id="birthYear" min="1900" max="2026" placeholder="π.χ. 1985">
	</div>

  <div class="question">
    <label for="citizenship">Ιθαγένεια / κατηγορία υποψηφίου/ας</label>
    <select id="citizenship">
      <option value="">-- Επιλογή --</option>
      <option value="eligible">Έλληνας/Ελληνίδα πολίτης</option>
      <option value="eligible">Πολίτης κράτους-μέλους της Ευρωπαϊκής Ένωσης</option>
      <option value="eligible">Βορειοηπειρώτης/τισσα, Κύπριος/α Ομογενής ή Ομογενής αλλοδαπός/ή από Κωνσταντινούπολη, Ίμβρο, Τένεδο ή Αίγυπτο με τα απαιτούμενα αποδεικτικά</option>
      <option value="not_eligible">Δεν ανήκω σε καμία από τις παραπάνω κατηγορίες</option>
      <option value="unknown">Δεν είμαι σίγουρος/η</option>
    </select>
  </div>

  <div class="question">
    <label for="health">Έχεις την υγεία που απαιτείται για την εκτέλεση των καθηκόντων της θέσης;</label>
    <select id="health">
      <option value="">-- Επιλογή --</option>
      <option value="yes">Ναι</option>
      <option value="no">Όχι</option>
      <option value="unknown">Δεν είμαι σίγουρος/η</option>
    </select>
  </div>

  <div class="question">
    <label for="qualifications">Κατέχεις τα απαραίτητα προσόντα της ειδικότητας, όπως πτυχίο, τίτλο ξένης γλώσσας όπου απαιτείται, αναγνώριση/ισοτιμία τίτλων κ.λπ.;</label>
    <select id="qualifications">
      <option value="">-- Επιλογή --</option>
      <option value="yes">Ναι</option>
      <option value="no">Όχι</option>
      <option value="unknown">Δεν είμαι σίγουρος/η</option>
    </select>
  </div>

  <div class="question">
    <label for="dismissed">
      Έχεις απολυθεί από θέση δημόσιας υπηρεσίας, Ο.Τ.Α. ή άλλου νομικού προσώπου του δημόσιου τομέα λόγω ποινής οριστικής παύσης ή λόγω καταγγελίας σύμβασης για σπουδαίο λόγο, χωρίς να έχει παρέλθει δεκαετία;
    </label>
    <select id="dismissed">
      <option value="">-- Επιλογή --</option>
      <option value="yes">Ναι</option>
      <option value="no">Όχι</option>
      <option value="unknown">Δεν είμαι σίγουρος/η</option>
    </select>
  </div>

  <div class="question">
    <label for="criminal">
      Διώκεσαι ποινικά ή έχεις καταδικαστεί για οποιοδήποτε έγκλημα κατά της γενετήσιας ελευθερίας ή οικονομικής εκμετάλλευσης της γενετήσιας ζωής;
    </label>
    <select id="criminal">
      <option value="">-- Επιλογή --</option>
      <option value="yes">Ναι</option>
      <option value="no">Όχι</option>
      <option value="unknown">Δεν είμαι σίγουρος/η</option>
    </select>
  </div>

	<div class="question">
	  <label for="convictionImpediment">
		Έχεις καταδικαστεί για κακούργημα ή για αδικήματα όπως κλοπή, υπεξαίρεση, απάτη, εκβίαση, πλαστογραφία, δωροδοκία, απιστία περί την υπηρεσία, παράβαση καθήκοντος ή καθ’ υποτροπή συκοφαντική δυσφήμηση;
	  </label>
	  <select id="convictionImpediment">
		<option value="">-- Επιλογή --</option>
		<option value="yes">Ναι</option>
		<option value="no">Όχι</option>
		<option value="unknown">Δεν είμαι σίγουρος/η</option>
	  </select>
	</div>

	<div class="question">
	  <label for="indictmentImpediment">
		Έχεις παραπεμφθεί με τελεσίδικο βούλευμα για κακούργημα ή για κάποιο από τα παραπάνω αδικήματα;
	  </label>
	  <select id="indictmentImpediment">
		<option value="">-- Επιλογή --</option>
		<option value="yes">Ναι</option>
		<option value="no">Όχι</option>
		<option value="unknown">Δεν είμαι σίγουρος/η</option>
	  </select>
	</div>

	<div class="question">
	  <label for="civilRightsOrSupport">
		Έχεις στερηθεί πολιτικά δικαιώματα λόγω καταδίκης ή τελείς υπό στερητική/επικουρική δικαστική συμπαράσταση;
	  </label>
	  <select id="civilRightsOrSupport">
		<option value="">-- Επιλογή --</option>
		<option value="yes">Ναι</option>
		<option value="no">Όχι</option>
		<option value="unknown">Δεν είμαι σίγουρος/η</option>
	  </select>
	</div>

  <div class="question">
    <label for="commercial">
      Μετέχεις σε εμπορική εταιρεία ή έχεις ιδιότητα που μπορεί να δημιουργεί κώλυμα ανάληψης υπηρεσίας, όπως ομόρρυθμος/η, ετερόρρυθμος/η, διαχειριστής/τρια ή διευθύνων/εντεταλμένος/η σύμβουλος;
    </label>
    <select id="commercial">
      <option value="">-- Επιλογή --</option>
      <option value="yes">Ναι</option>
      <option value="no">Όχι</option>
      <option value="unknown">Δεν είμαι σίγουρος/η</option>
    </select>
  </div>

  <div class="question">
    <label for="politicalOffice">
      Κατέχεις βουλευτικό αξίωμα;
    </label>
    <select id="politicalOffice">
      <option value="">-- Επιλογή --</option>
      <option value="yes">Ναι</option>
      <option value="no">Όχι</option>
      <option value="unknown">Δεν είμαι σίγουρος/η</option>
    </select>
  </div>

  <div class="question">
    <label for="publicFullTime">
      Απασχολείσαι ήδη στο Δημόσιο ή σε κρατικό νομικό πρόσωπο δημοσίου ή ιδιωτικού δικαίου με πλήρες ωράριο εργασίας;
    </label>
    <select id="publicFullTime">
      <option value="">-- Επιλογή --</option>
      <option value="yes">Ναι</option>
      <option value="no">Όχι</option>
      <option value="unknown">Δεν είμαι σίγουρος/η</option>
    </select>
  </div>

  <div class="question">
    <label for="privateEducation">
      Είσαι ιδιοκτήτης/τρια φροντιστηρίου ή διδάσκεις σε ιδιωτικό σχολείο με πλήρες ή μειωμένο ωράριο;
    </label>
    <select id="privateEducation">
      <option value="">-- Επιλογή --</option>
      <option value="yes">Ναι</option>
      <option value="no">Όχι</option>
      <option value="unknown">Δεν είμαι σίγουρος/η</option>
    </select>
  </div>

  <div class="question">
    <label for="military">
      Για άνδρες υποψηφίους: Έχουν εκπληρωθεί οι στρατιωτικές υποχρεώσεις ή υπάρχει νόμιμη απαλλαγή;
    </label>
    <select id="military">
      <option value="">-- Επιλογή --</option>
      <option value="not_applicable">Δεν με αφορά</option>
      <option value="yes">Ναι, έχουν εκπληρωθεί ή υπάρχει νόμιμη απαλλαγή</option>
      <option value="no">Όχι</option>
      <option value="unknown">Δεν είμαι σίγουρος/η</option>
    </select>
  </div>

  <button onclick="checkEligibility()">Έλεγχος δικαιώματος συμμετοχής</button>

  <div id="result" class="result"></div>

  <p class="small-note">
    Το αποτέλεσμα είναι ενδεικτικό και βασίζεται στις γενικές προϋποθέσεις συμμετοχής του Παραρτήματος Α΄.
    Δεν αντικαθιστά την προσεκτική ανάγνωση της προκήρυξης και των επίσημων οδηγιών του Α.Σ.Ε.Π.
  </p>
</div>

<script>
  function valueOf(id) {
    return document.getElementById(id).value;
  }

  function showResult(message, cssClass) {
    const result = document.getElementById("result");
    result.style.display = "block";
    result.className = "result " + cssClass;
    result.innerHTML = message;
  }

  function checkEligibility() {
    const birthYear = parseInt(valueOf("birthYear"), 10);
	const referenceYear = 2026;
	const minBirthYear = referenceYear - 67; // 1959
	const maxBirthYear = referenceYear - 21; // 2005

    const citizenship = valueOf("citizenship");
    const health = valueOf("health");
    const qualifications = valueOf("qualifications");

    const dismissed = valueOf("dismissed");
    const criminal = valueOf("criminal");
    const convictionImpediment = valueOf("convictionImpediment");
	const indictmentImpediment = valueOf("indictmentImpediment");
	const civilRightsOrSupport = valueOf("civilRightsOrSupport");

    const commercial = valueOf("commercial");
    const politicalOffice = valueOf("politicalOffice");
    const publicFullTime = valueOf("publicFullTime");
    const privateEducation = valueOf("privateEducation");
    const military = valueOf("military");

    const requiredFields = [
      citizenship,
      health,
      qualifications,
      dismissed,
      criminal,
	  convictionImpediment,
	  indictmentImpediment,
	  civilRightsOrSupport,
      commercial,
      politicalOffice,
      publicFullTime,
      privateEducation,
      military
    ];

	if (!birthYear || requiredFields.includes("")) {
	  showResult("Παρακαλώ απάντησε σε όλες τις ερωτήσεις.", "unknown");
	  return;
	}

	if (birthYear < 1900 || birthYear > referenceYear) {
	  showResult(
		"Παρακαλώ συμπλήρωσε έγκυρο έτος γέννησης.",
		"unknown"
	  );
	  return;
	}

	if (birthYear < minBirthYear || birthYear > maxBirthYear) {
	  showResult(
		"Δεν προκύπτει δικαίωμα συμμετοχής, επειδή δεν πληρούται το ηλικιακό όριο των γενικών προϋποθέσεων. Για έτος αναφοράς το 2026, το αποδεκτό εύρος γέννησης είναι από το 1959 έως και το 2005.",
		"not-eligible"
	  );
	  return;
	}

    if (citizenship === "not_eligible") {
      showResult(
        "Δεν προκύπτει δικαίωμα συμμετοχής, επειδή δεν πληρούται η προϋπόθεση ιθαγένειας ή ειδικής κατηγορίας υποψηφίου/ας.",
        "not-eligible"
      );
      return;
    }

    if (health === "no") {
      showResult(
        "Δεν προκύπτει δικαίωμα συμμετοχής, επειδή δηλώθηκε ότι δεν υπάρχει η απαιτούμενη υγεία για την εκτέλεση των καθηκόντων της θέσης.",
        "not-eligible"
      );
      return;
    }

    if (qualifications === "no") {
      showResult(
        "Δεν προκύπτει δικαίωμα συμμετοχής, επειδή δηλώθηκε ότι δεν κατέχονται τα απαιτούμενα προσόντα της ειδικότητας.",
        "not-eligible"
      );
      return;
    }

    if (dismissed === "yes") {
      showResult(
        "Δεν υπάρχει δικαίωμα υποβολής αίτησης, επειδή δηλώθηκε απόλυση από φορέα του δημόσιου τομέα με σχετικό κώλυμα.",
        "not-eligible"
      );
      return;
    }

    if (criminal === "yes") {
      showResult(
        "Δεν υπάρχει δικαίωμα υποβολής αίτησης, επειδή δηλώθηκε ποινική δίωξη ή καταδίκη για αδικήματα που αποτελούν κώλυμα.",
        "not-eligible"
      );
      return;
    }

    if (convictionImpediment === "yes") {
	  showResult(
		"Δεν υπάρχει δικαίωμα υποβολής αίτησης, επειδή δηλώθηκε ποινική καταδίκη που μπορεί να αποτελεί κώλυμα διορισμού.",
		"not-eligible"
	  );
	  return;
	}

	if (indictmentImpediment === "yes") {
	  showResult(
		"Δεν υπάρχει δικαίωμα υποβολής αίτησης, επειδή δηλώθηκε παραπομπή με τελεσίδικο βούλευμα για αδίκημα που μπορεί να αποτελεί κώλυμα διορισμού.",
		"not-eligible"
	  );
	  return;
	}

	if (civilRightsOrSupport === "yes") {
	  showResult(
		"Δεν υπάρχει δικαίωμα υποβολής αίτησης, επειδή δηλώθηκε στέρηση πολιτικών δικαιωμάτων ή δικαστική συμπαράσταση.",
		"not-eligible"
	  );
	  return;
	}

    const hasUnknown = [
      citizenship,
      health,
      qualifications,
      dismissed,
      criminal,
      convictionImpediment,
	indictmentImpediment,
	civilRightsOrSupport,
      commercial,
      politicalOffice,
      publicFullTime,
      privateEducation,
      military
    ].includes("unknown");

    if (hasUnknown) {
      showResult(
        "Χρειάζεται περαιτέρω έλεγχος, επειδή σε μία ή περισσότερες ερωτήσεις επιλέχθηκε «Δεν είμαι σίγουρος/η».",
        "unknown"
      );
      return;
    }

    const conditionalIssues = [];

    if (commercial === "yes") {
      conditionalIssues.push("συμμετοχή ή ιδιότητα σε εμπορική εταιρεία");
    }

    if (politicalOffice === "yes") {
      conditionalIssues.push("κατοχή βουλευτικού αξιώματος");
    }

    if (publicFullTime === "yes") {
      conditionalIssues.push("υπηρεσία πλήρους ωραρίου στο Δημόσιο ή σε φορέα του δημόσιου τομέα");
    }

    if (privateEducation === "yes") {
      conditionalIssues.push("ιδιοκτησία φροντιστηρίου ή διδασκαλία σε ιδιωτικό σχολείο");
    }

    if (military === "no") {
      conditionalIssues.push("μη εκπλήρωση στρατιωτικών υποχρεώσεων ή μη ύπαρξη νόμιμης απαλλαγής");
    }

    if (conditionalIssues.length > 0) {
      showResult(
        "Έχεις δικαίωμα υποβολής αίτησης, αλλά υπάρχει πιθανό κώλυμα ανάληψης υπηρεσίας σε περίπτωση διορισμού.<br><br>" +
        "Σημεία που χρειάζονται τακτοποίηση/έλεγχο: <br>" +
        conditionalIssues.map(item => "• " + item).join("<br>"),
        "conditional"
      );
      return;
    }

    showResult(
      "Με βάση τις απαντήσεις σου, προκύπτει ότι έχεις δικαίωμα συμμετοχής στην προκήρυξη.",
      "eligible"
    );
  }
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
</body>
</html>