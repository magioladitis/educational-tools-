<?php require_once __DIR__ . '/includes/config.php'; ?>
<?php require_once __DIR__ . '/includes/components/source-card.php'; ?>
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
<section class="hero"><span class="hero-kicker">ΣΧΟΛΙΚΗ ΜΟΝΑΔΑ · ΘΕΣΕΙΣ ΕΥΘΥΝΗΣ · BETA</span><h1>Έλεγχος προϋποθέσεων Διευθυντή Σχολικής Μονάδας</h1><p>Προκαταρκτικός έλεγχος βασικών τυπικών προϋποθέσεων. Δεν υπολογίζει μόρια επιλογής και δεν υποκαθιστά την προκήρυξη ή την κρίση του αρμόδιου Συμβουλίου.</p><div class="hero-meta"><span>Ν. 4823/2021, άρθρο 31</span><span>Ανά σχολικό τύπο και κλάδο</span><span>Χωρίς αποθήκευση στοιχείων</span></div></section>
<section class="leadership-panel" aria-labelledby="formTitle"><h2 id="formTitle">Α. Στοιχεία υποψηφιότητας</h2>
<div class="leadership-fields">

<div class="field"><label for="school">Τύπος σχολικής μονάδας / θέσης</label><select id="school"><option value="gym">Γυμνάσιο</option><option value="gel">Γενικό Λύκειο (ΓΕΛ)</option><option value="epal">Επαγγελματικό Λύκειο (ΕΠΑΛ)</option><option value="kindergarten">Νηπιαγωγείο</option><option value="primary_school">Δημοτικό Σχολείο</option><option value="ek">Εργαστηριακό Κέντρο (Ε.Κ.)</option><option value="special_kindergarten">Ειδικό Νηπιαγωγείο</option><option value="special_primary">Ειδικό Δημοτικό</option><option value="special_secondary">ΣΜΕΑΕ Δευτεροβάθμιας (εκτός ΕΕΕΕΚ)</option><option value="eeeeek">ΕΕΕΕΚ</option><option value="deaf_blind">ΣΜΕΑΕ κωφών / βαρηκόων / τυφλών (επιλέξτε και βαθμίδα)</option><option value="model">Πρότυπο / Πειραματικό</option><option value="other">Άλλος ειδικός τύπος</option></select></div>
<div class="field"><label for="branch">Κλάδος / ειδικότητα</label><select id="branch"><option value="">Επιλέξτε κλάδο</option><option value="PE60">ΠΕ60 — Νηπιαγωγοί</option><option value="PE61">ΠΕ61 — Νηπιαγωγοί ΕΑΕ</option><option value="PE70">ΠΕ70 — Δάσκαλοι</option><option value="PE71">ΠΕ71 — Δάσκαλοι ΕΑΕ</option><?php foreach (array_merge(range(1, 8), array(11, 33, 34, 40), range(78, 91)) as $n): ?><option value="PE<?php echo str_pad((string)$n, 2, '0', STR_PAD_LEFT); ?>">ΠΕ<?php echo str_pad((string)$n, 2, '0', STR_PAD_LEFT); ?></option><?php endforeach; ?><option value="OTHER_PE">Άλλος κλάδος ΠΕ</option><option value="EEP">Ειδικό Εκπαιδευτικό Προσωπικό (ΕΕΠ)</option><option value="OTHER">Άλλο / δεν γνωρίζω</option></select></div>
<div class="field"><label for="branchLevel">Βαθμίδα στην οποία υπηρετεί ο/η υποψήφιος/α</label><select id="branchLevel"><option value="unknown">Δεν έχει επιβεβαιωθεί</option><option value="primary">Πρωτοβάθμια</option><option value="secondary">Δευτεροβάθμια</option><option value="eep">ΕΕΠ</option></select></div>
<div class="field"><label for="specialQualification">Ειδικά προσόντα / απαιτήσεις θέσης</label><select id="specialQualification"><option value="unknown">Δεν έχουν επαληθευτεί</option><option value="confirmed">Έχουν ελεγχθεί με τα δικαιολογητικά και την προκήρυξη</option><option value="not_confirmed">Δεν πληρούνται ή υπάρχει εκκρεμότητα</option></select></div>
<div class="field"><label for="specialLevel">Βαθμίδα ειδικής σχολικής μονάδας (μόνο για κωφών/τυφλών)</label><select id="specialLevel"><option value="unknown">Επιλέξτε βαθμίδα</option><option value="primary_kindergarten">Νηπιαγωγείο ΕΑΕ</option><option value="primary">Δημοτικό ΕΑΕ</option><option value="secondary">Δευτεροβάθμια ΣΜΕΑΕ</option></select></div>
<div class="field"><label for="eaeEligible">Εξειδίκευση ΕΑΕ / προσόντα τοποθέτησης σε ΣΜΕΑΕ</label><select id="eaeEligible"><option value="unknown">Δεν έχουν ελεγχθεί</option><option value="yes">Ναι, έχουν επιβεβαιωθεί</option><option value="no">Όχι</option></select></div>
<div class="field"><label for="specialYears">Έτη υπηρεσίας στον απαιτούμενο τύπο ΣΜΕΑΕ</label><input type="number" min="0" max="60" step="0.01" id="specialYears" placeholder="π.χ. 4"></div>
<div class="field"><label for="supportYears">Έτη υποστηρικτικής υπηρεσίας (μόνο ΕΕΠ)</label><input type="number" min="0" max="60" step="0.01" id="supportYears" placeholder="π.χ. 8"></div>
<div class="field"><label for="sensoryType">Τύπος σχολείου αισθητηριακής αναπηρίας</label><select id="sensoryType"><option value="unknown">Δεν αφορά ή δεν έχει επιλεγεί</option><option value="deaf">Κωφών / βαρηκόων</option><option value="blind">Τυφλών</option></select></div>
<div class="field"><label for="sensoryProof">ΕΝΓ / Braille ή υπηρεσία με οργανική θέση</label><select id="sensoryProof"><option value="unknown">Χρειάζεται έλεγχος</option><option value="certified">Αντίστοιχη πιστοποίηση ΕΝΓ ή Braille</option><option value="organic4">Οργανική θέση και τουλάχιστον 4 έτη υπηρεσίας στην ίδια σχολική μονάδα</option><option value="none">Καμία από τις δύο περιπτώσεις</option></select></div>
<div class="field"><label for="reannouncement">Επαναπροκήρυξη / ειδική εξαίρεση</label><select id="reannouncement"><option value="normal">Κανονική προκήρυξη</option><option value="repeat">Επαναπροκήρυξη για κενή θέση</option><option value="no_candidates">Ειδική περίπτωση έλλειψης υποψηφίων (απαιτείται υπηρεσιακή επιβεβαίωση)</option></select></div>
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
<?php sourceCardStart(array('title' => 'Πηγές / Νομική βάση', 'title_id' => 'leadershipSourcesTitle')); ?>
<p><strong>Γενικός προέλεγχος:</strong> Ελέγχονται μόνο οι βασικές προϋποθέσεις εκπαιδευτικής και διδακτικής υπηρεσίας, υπηρεσίας στην οικεία βαθμίδα και γνώσης ΤΠΕ. Ο έλεγχος κλάδου αφορά μόνο τις ρητά καλυπτόμενες περιπτώσεις. Για ΣΜΕΑΕ εξετάζονται χωριστά ο τύπος σχολείου, τα προσόντα ΕΑΕ, η ειδική υπηρεσία και οι προϋποθέσεις ΕΕΠ. Τα Πρότυπα/Πειραματικά και οι επαναπροκηρύξεις απαιτούν πρόσθετη εξέταση.</p>
<?php sourceCardLinksStart(); ?>
<?php sourceCardLink('https://api.et.gr/apiFEK/1/2021/136/pdf', 'Ν. 4823/2021 — ΦΕΚ Α΄ 136, άρθρο 31 ↗'); ?>
<?php sourceCardLink('https://api.et.gr/apiFEK/2/2022/6141/pdf', 'Υ.Α. Φ.361.22/6/149788/Ε3/2022 — ΦΕΚ Β΄ 6141 ↗'); ?>
<?php sourceCardLink('https://api.et.gr/apiFEK/2/2023/4843/pdf', 'Υ.Α. τροποποίησης διαδικασίας επαναπροκήρυξης — ΦΕΚ Β΄ 4843/2023 ↗'); ?>
<?php sourceCardLink('https://www.e-nomothesia.gr/index.php/nomos-4823-2021-phek-136a-3-8-2021.html', 'Ν. 4823/2021 — άρθρο 31, ειδικότερες προϋποθέσεις ανά τύπο σχολείου ↗'); ?>
<?php sourceCardLink('https://mitos.gov.gr/index.php/ΔΔ:Επιλογή_και_Τοποθέτηση_Διευθυντών_Σχολικών_Μονάδων', 'Μίτος — Επιλογή και τοποθέτηση Διευθυντών Σχολικών Μονάδων ↗'); ?>
<?php sourceCardLinksEnd(); ?>
<?php sourceCardDisclaimerStart(); ?>
Το αποτέλεσμα είναι ενδεικτικό και δεν πιστοποιεί δικαίωμα υποψηφιότητας. Ο κλάδος, η σχολική μονάδα, τα κωλύματα, οι ειδικές εξαιρέσεις και οι όροι της τρέχουσας προκήρυξης ελέγχονται από την αρμόδια υπηρεσία.
<?php sourceCardDisclaimerEnd(); ?>
<?php sourceCardEnd(); ?></main>
<script src="<?php echo htmlspecialchars(edu_asset_url('includes/leadership-eligibility.js'), ENT_QUOTES, 'UTF-8'); ?>" defer></script>
</body></html>
