<?php require_once __DIR__ . '/includes/config.php'; ?>
<!DOCTYPE html>
<html lang="el">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Έχω δικαίωμα συμμετοχής στις προκηρύξεις 1ΓΕ/2026, 2ΓΕ/2026;</title>
  <link rel="stylesheet" href="<?php echo htmlspecialchars(edu_asset_url('assets/common.css'), ENT_QUOTES, 'UTF-8'); ?>">
</head>

<body class="edu-ui edu-guide-standard edu-guide-eligibility">
<?php require_once __DIR__ . '/includes/header.php'; ?>

<div class="app-box edu-modernized">
<section class="hero edu-legacy-hero">
<h1>Έχω δικαίωμα συμμετοχής στις προκηρύξεις 1ΓΕ/2026 &amp; 2ΓΕ/2026;</h1>
<p class="intro">
  Το παρόν εργαλείο παρέχει έναν ενδεικτικό έλεγχο των γενικών προϋποθέσεων συμμετοχής
  στις <a href="https://search.et.gr/el/fek/?fekId=798251">1ΓΕ/2026</a> και <a href="https://search.et.gr/el/fek/?fekId=798194">2ΓΕ/2026</a> και δεν αντικαθιστά την επίσημη προκήρυξη του Α.Σ.Ε.Π. ούτε την προσωπική ευθύνη
  του/της υποψηφίου/ας για την ορθή υποβολή της αίτησης.
</p>
</section>
  <p class="intro">
    Απάντησε στις παρακάτω ερωτήσεις για έναν ενδεικτικό έλεγχο των γενικών προϋποθέσεων συμμετοχής.
  </p>

  <div class="progress-panel" aria-live="polite">
    <div class="progress-head">
      <span>Πρόοδος συμπλήρωσης</span>
      <span id="progressText">0/14 απαντήσεις</span>
    </div>
    <div class="progress-track" aria-hidden="true">
      <div id="progressFill" class="progress-fill"></div>
    </div>
  </div>

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

  <div class="button-row">
    <button type="button" id="eligibilityCheckBtn">Έλεγχος δικαιώματος συμμετοχής</button>
    <button type="button" id="eligibilityResetBtn" class="reset-button">Καθαρισμός</button>
  </div>

  <div id="result" class="result" role="status" aria-live="polite"></div>

  <p class="small-note">
    Το αποτέλεσμα είναι ενδεικτικό και βασίζεται στις γενικές προϋποθέσεις συμμετοχής του Παραρτήματος Α΄.
    Δεν αντικαθιστά την προσεκτική ανάγνωση της προκήρυξης και των επίσημων οδηγιών του Α.Σ.Ε.Π.
  </p>
</div>

<script src="<?php echo htmlspecialchars(edu_asset_url('includes/eligibility-guide-ui.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>


<?php sourceCardStart(); ?>
  <p>Προκηρύξεις Α.Σ.Ε.Π. <strong>1ΓΕ/2026</strong> (ΦΕΚ 21/τ. Α.Σ.Ε.Π./29.04.2026) και <strong>2ΓΕ/2026</strong> (ΦΕΚ 22/τ. Α.Σ.Ε.Π./29.04.2026), ιδίως οι γενικές προϋποθέσεις συμμετοχής και το Παράρτημα Α΄.</p>
  <?php sourceCardLinksStart(); ?><?php sourceCardLink('https://info.asep.gr/node/78700', '1ΓΕ/2026 — ΑΣΕΠ ↗'); ?><?php sourceCardLink('https://info.asep.gr/node/78701', '2ΓΕ/2026 — ΑΣΕΠ ↗'); ?><?php sourceCardLinksEnd(); ?>
<?php sourceCardEnd(); ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
  <script src="<?php echo htmlspecialchars(edu_asset_url('assets/common.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
</body>
</html>