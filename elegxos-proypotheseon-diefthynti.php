<?php require_once __DIR__ . '/includes/config.php'; ?>
<!DOCTYPE html>
<html lang="el">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<?php require __DIR__ . '/includes/head-pwa.php'; ?>
<title>Έλεγχος προϋποθέσεων Διευθυντή Σχολικής Μονάδας | Εργαλειοθήκη Εκπαιδευτικού</title>
<meta name="description" content="Ενδεικτικός έλεγχος γενικών προϋποθέσεων επιλογής Διευθυντών Σχολικών Μονάδων.">
<link rel="stylesheet" href="<?php echo htmlspecialchars(edu_asset_url('assets/common.css'), ENT_QUOTES, 'UTF-8'); ?>">
<link rel="stylesheet" href="<?php echo htmlspecialchars(edu_asset_url('assets/leadership-eligibility.css'), ENT_QUOTES, 'UTF-8'); ?>">
</head>
<body class="edu-ui edu-calc-standard edu-leadership">
<?php require_once __DIR__ . '/includes/header.php'; ?>
<main class="page-shell leadership-shell">
<section class="hero"><span class="hero-kicker">ΣΧΟΛΙΚΗ ΜΟΝΑΔΑ · ΘΕΣΕΙΣ ΕΥΘΥΝΗΣ</span><h1>Έλεγχος προϋποθέσεων Διευθυντή Σχολικής Μονάδας</h1><p>Προκαταρκτικός έλεγχος βασικών τυπικών προϋποθέσεων. Δεν υπολογίζει μόρια επιλογής και δεν υποκαθιστά την προκήρυξη ή την κρίση του αρμόδιου Συμβουλίου.</p><div class="hero-meta"><span>Ν. 4823/2021, άρθρο 31</span><span>Γενικές σχολικές μονάδες</span><span>Χωρίς αποθήκευση στοιχείων</span></div></section>
<section class="leadership-panel" aria-labelledby="formTitle"><h2 id="formTitle">Α. Στοιχεία υποψηφιότητας</h2>
<div class="leadership-fields">
<div class="field"><label for="level">Βαθμίδα της θέσης</label><select id="level"><option value="secondary">Δευτεροβάθμια</option><option value="primary">Πρωτοβάθμια</option></select></div>
<div class="field"><label for="school">Τύπος σχολείου</label><select id="school"><option value="general">Συνήθης σχολική μονάδα</option><option value="special">ΣΜΕΑΕ</option><option value="model">Πρότυπο / Πειραματικό</option><option value="other">Άλλος ειδικός τύπος</option></select></div>
<div class="field"><label for="education">Συνολικά έτη εκπαιδευτικής υπηρεσίας</label><input type="number" min="0" max="60" step="0.01" id="education" placeholder="π.χ. 15"></div>
<div class="field"><label for="teaching">Έτη διδακτικής υπηρεσίας συνολικά</label><input type="number" min="0" max="60" step="0.01" id="teaching" placeholder="π.χ. 10"></div>
<div class="field"><label for="sameLevel">Έτη διδακτικής υπηρεσίας στην οικεία βαθμίδα</label><input type="number" min="0" max="60" step="0.01" id="sameLevel" placeholder="π.χ. 8"></div>
<div class="field"><label for="ict">ΤΠΕ Α΄ επιπέδου</label><select id="ict"><option value="unknown">Δεν έχω ελέγξει το αποδεικτικό</option><option value="yes">Διαθέτω πιστοποίηση ή επιβεβαιωμένη νόμιμη ισοδυναμία</option><option value="no">Δεν διαθέτω</option></select></div>
<div class="field"><label for="levelMatch">Υπηρετώ στην οικεία βαθμίδα</label><select id="levelMatch"><option value="unknown">Χρειάζεται επιβεβαίωση</option><option value="yes">Ναι</option><option value="no">Όχι</option></select></div>
<div class="field"><label for="administrative">Διοικητικά κωλύματα / περιορισμοί</label><select id="administrative"><option value="unknown">Δεν έχουν ελεγχθεί πλήρως</option><option value="clear">Έχω ελέγξει την τρέχουσα προκήρυξη, δεν υφίσταται κώλυμα</option><option value="issue">Υπάρχει πιθανό κώλυμα ή εκκρεμότητα</option></select></div>
</div>
<div class="leadership-actions"><button type="button" id="checkEligibility">Έλεγχος γενικών προϋποθέσεων</button><button type="button" id="resetEligibility" class="leadership-secondary">Καθαρισμός</button><button type="button" id="printEligibility" class="leadership-secondary">Εκτύπωση αποτελέσματος</button></div>
</section>
<section id="eligibilityResult" class="leadership-panel" aria-live="polite" aria-labelledby="resultTitle"><h2 id="resultTitle">Β. Αποτέλεσμα προελέγχου</h2><p>Συμπλήρωσε τα στοιχεία και επίλεξε «Έλεγχος γενικών προϋποθέσεων».</p></section>
<section class="leadership-panel"><h2>Γ. Νομική τεκμηρίωση και όρια</h2>
<p>Ο αρχικός έλεγχος εξετάζει τον γενικό κανόνα των 12 ετών εκπαιδευτικής υπηρεσίας, 8 ετών διδακτικής (4 στην οικεία βαθμίδα) και ΤΠΕ Α΄. Δεν κρίνει ειδικούς κλάδους, καταλληλότητα για συγκεκριμένη σχολική μονάδα, εναλλακτικά καθεστώτα επαναπροκήρυξης, εκκρεμή κωλύματα ή πρόσθετα προσόντα.</p>
<ul><li><a href="https://api.et.gr/apiFEK/1/2021/136/pdf" target="_blank" rel="noopener noreferrer">Ν. 4823/2021, ΦΕΚ Α΄ 136 — άρθρο 31</a></li><li><a href="https://api.et.gr/apiFEK/2/2022/6141/pdf" target="_blank" rel="noopener noreferrer">ΥΑ Φ.361.22/6/149788/Ε3/2022, ΦΕΚ Β΄ 6141</a></li><li><a href="https://mitos.gov.gr/index.php/ΔΔ:Επιλογή_και_Τοποθέτηση_Διευθυντών_Σχολικών_Μονάδων" target="_blank" rel="noopener noreferrer">Εθνικό Μητρώο Διοικητικών Διαδικασιών — Μίτος</a></li></ul>
<p class="leadership-note">Ειδικοί τύποι σχολείων, κλάδοι, όρια συνταξιοδότησης, πειθαρχικά ή υπηρεσιακά κωλύματα και η περίπτωση επαναπροκήρυξης απαιτούν ξεχωριστό έλεγχο με την ισχύουσα πρόσκληση. Θετικό αποτέλεσμα σημαίνει μόνο ότι δεν εντοπίστηκε έλλειψη στους βασικούς αριθμητικούς κανόνες.</p>
</section></main>
<script src="<?php echo htmlspecialchars(edu_asset_url('includes/leadership-eligibility.js'), ENT_QUOTES, 'UTF-8'); ?>" defer></script>
</body></html>
