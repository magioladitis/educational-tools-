<?php require_once __DIR__ . '/includes/config.php'; ?>
<!DOCTYPE html>
<html lang="el">
<head>
<!-- UI consolidation v3.20: simplified flow + shared design system -->
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Οδηγός δικαιολογητικών τέκνων και αναπηρίας</title>
  <link rel="stylesheet" href="<?php echo htmlspecialchars(edu_asset_url('assets/common.css'), ENT_QUOTES, 'UTF-8'); ?>">
</head>
<body class="edu-ui edu-guide-standard edu-guide-children-disability">
<?php require_once __DIR__ . '/includes/header.php'; ?>
<div class="app-box edu-modernized">
<section class="hero edu-legacy-hero">
<h1>Οδηγός δικαιολογητικών τέκνων και αναπηρίας</h1>
<p class="intro">Ενδεικτικός οδηγός για τα κοινωνικά κριτήρια: <strong>αριθμός τέκνων</strong> και <strong>αναπηρία 50% και άνω</strong>, χωρίς περιττές διπλές ερωτήσεις.</p>
</section>

<div class="question">
<label for="criterion">Ποιο κοινωνικό κριτήριο θέλεις να ελέγξεις;</label>
<select id="criterion">
<option value="">-- Επιλογή --</option>
<option value="children">Αριθμός τέκνων</option>
<option value="disability">Αναπηρία 50% και άνω</option>
<option value="both">Τέκνα και αναπηρία</option>
</select>
</div>

<div id="childrenQuestions" class="hidden">
<div class="question">
<label for="childCase">Ποια περίπτωση τέκνου/τέκνων δηλώνεις;</label>
<select id="childCase">
<option value="">-- Επιλογή --</option>
<option value="under23">Άγαμο τέκνο που δεν έχει συμπληρώσει το 23ο έτος</option>
<option value="student25">Άγαμο τέκνο που σπουδάζει σε Α.Ε.Ι. ή ομοταγές ίδρυμα αλλοδαπής και δεν έχει συμπληρώσει το 25ο έτος</option>
<option value="military25">Άγαμο τέκνο που εκπληρώνει στρατιωτικές υποχρεώσεις και δεν έχει συμπληρώσει το 25ο έτος</option>
<option value="mixed">Περισσότερες από μία περιπτώσεις</option>
<option value="unknown">Δεν είμαι σίγουρος/η</option>
</select>
</div>

<div class="question">
<label for="familyCertificateStatus">Τα στοιχεία των τέκνων φαίνονται σωστά στο πιστοποιητικό οικογενειακής κατάστασης / Ο.Π.ΣΥ.Δ.;</label>
<select id="familyCertificateStatus">
<option value="">-- Επιλογή --</option>
<option value="yes">Ναι</option>
<option value="no">Όχι / χρειάζεται διόρθωση</option>
<option value="unknown">Δεν είμαι σίγουρος/η</option>
</select>
</div>

<div class="question">
<label>Υπάρχει ειδική οικογενειακή κατάσταση;</label>
<div class="checkbox-group">
<label><input type="checkbox" name="familySpecialCase" value="divorce"> Διαζύγιο / λύση γάμου / λύση συμφώνου συμβίωσης</label>
<label><input type="checkbox" name="familySpecialCase" value="separation"> Διάσταση ή χωριστή διαβίωση χωρίς κοινή επιμέλεια</label>
<label><input type="checkbox" name="familySpecialCase" value="unmarried"> Τέκνο χωρίς γάμο ή χωρίς σύμφωνο συμβίωσης των γονέων</label>
<label><input type="checkbox" name="familySpecialCase" value="adopted"> Υιοθετημένο ή νομίμως αναγνωρισμένο τέκνο</label>
<label><input type="checkbox" name="familySpecialCase" value="none"> Όχι, δεν υπάρχει ειδική περίπτωση</label>
</div>
</div>
</div>

<div id="disabilityQuestions" class="hidden">
<div class="question">
<label for="disabilityPerson">Ποιο πρόσωπο αφορά η αναπηρία;</label>
<select id="disabilityPerson">
<option value="">-- Επιλογή --</option>
<option value="candidate">Υποψήφιος/α</option>
<option value="spouse">Σύζυγος</option>
<option value="child">Τέκνο</option>
</select>
</div>

<div class="question">
<label for="disabilityPercent">Ποσοστό αναπηρίας (%)</label>
<input type="number" id="disabilityPercent" min="0" max="100" step="1" placeholder="π.χ. 67">
<p class="small-note edu-mt-7 edu-mb-0">Το εργαλείο ελέγχει αυτόματα αν το ποσοστό είναι τουλάχιστον 50%.</p>
</div>

<div id="spouseMarriageQuestion" class="question hidden">
<label for="marriageYears4Plus">Ο έγγαμος βίος έχει διαρκέσει τουλάχιστον 4 έτη;</label>
<select id="marriageYears4Plus">
<option value="">-- Επιλογή --</option>
<option value="yes">Ναι</option>
<option value="no">Όχι</option>
<option value="unknown">Δεν είμαι σίγουρος/η</option>
</select>
</div>

<div id="candidateMentalQuestion" class="question hidden">
<label for="candidateMentalCondition">Η αναπηρία του/της υποψηφίου οφείλεται, έστω και κατά ποσοστό, σε ψυχική πάθηση;</label>
<select id="candidateMentalCondition">
<option value="">-- Επιλογή --</option>
<option value="no">Όχι</option>
<option value="yes">Ναι</option>
<option value="unknown">Δεν είμαι σίγουρος/η</option>
</select>
</div>

<div class="question">
<label for="disabilityCertificate">Τι πιστοποιητικό αναπηρίας υπάρχει;</label>
<select id="disabilityCertificate">
<option value="">-- Επιλογή --</option>
<option value="kepa">Πιστοποιητικό ΚΕ.Π.Α. σε ισχύ</option>
<option value="military_committees">Πιστοποιητικό σε ισχύ από Α.Σ.Υ.Ε., Α.Ν.Υ.Ε., Α.Α.Υ.Ε., Ελληνική Αστυνομία ή Πυροσβεστικό Σώμα</option>
<option value="old_valid">Παλαιότερη γνωμάτευση που εξακολουθεί να γίνεται δεκτή σύμφωνα με την προκήρυξη</option>
<option value="expired">Έχει λήξει ή δεν είναι σαφές αν είναι σε ισχύ</option>
<option value="none">Δεν υπάρχει πιστοποιητικό</option>
<option value="unknown">Δεν είμαι σίγουρος/η</option>
</select>
</div>
</div>

<button type="button" class="guide-submit" id="showDocumentsBtn">Εμφάνιση ενδεικτικών δικαιολογητικών</button>
<div id="result" class="result"></div>

<p class="small-note">Το εργαλείο παρέχει ενδεικτική καθοδήγηση και δεν αντικαθιστά την επίσημη προκήρυξη, τις οδηγίες του Α.Σ.Ε.Π., τον έλεγχο του Ο.Π.ΣΥ.Δ. ή τον έλεγχο των αρμόδιων υπηρεσιών.</p>
<?php sourceCardStart(); ?>
  <p>Προκηρύξεις Α.Σ.Ε.Π. <strong>1ΓΕ/2026</strong> και <strong>2ΓΕ/2026</strong>, κοινωνικά κριτήρια και δικαιολογητικά για τέκνα και αναπηρία.</p>
  <?php sourceCardLinksStart(); ?><?php sourceCardLink('https://info.asep.gr/node/78700', '1ΓΕ/2026 — ΑΣΕΠ ↗'); ?><?php sourceCardLink('https://info.asep.gr/node/78701', '2ΓΕ/2026 — ΑΣΕΠ ↗'); ?><?php sourceCardLinksEnd(); ?>
<?php sourceCardEnd(); ?>
</div>

<script src="<?php echo htmlspecialchars(edu_asset_url('includes/children-disability-documents-ui.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
<script src="<?php echo htmlspecialchars(edu_asset_url('assets/common.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
</body>
</html>
