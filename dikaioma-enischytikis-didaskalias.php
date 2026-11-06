<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/components/source-card.php';
require_once __DIR__ . '/includes/components/page-hero.php';
$reinforcementSources = array(
    'gym' => 'https://www.minedu.gov.gr/site/71122-06-11-26-ypobole-aiteseon-ypopsephion-didaskonton-sten-enischytike-didaskalia-gymnasiou-2026-2027',
    'edem' => 'https://www.minedu.gov.gr/site/71123-ypobole-aiteseon-ypopsephion-didaskonton-sten-enischytike-didaskalia-eidikon-mathematon-2026-2027'
);
$reinforcementDeadlines = array();
$reinforcementDeadlineConfig = require __DIR__ . '/includes/deadlines.php';
foreach ($reinforcementDeadlineConfig['items'] as $reinforcementItem) {
    foreach ($reinforcementSources as $reinforcementProgram => $reinforcementURL) {
        if (isset($reinforcementItem['source_url']) && $reinforcementItem['source_url'] === $reinforcementURL) {
            $reinforcementDeadlines[$reinforcementProgram] = $reinforcementItem;
        }
    }
}
function reinforcementSelect($name, $label, $options, $note = '', $details = '') {
    echo '<div class="reinforcement-field"><label for="reinforcement-' . htmlspecialchars($name, ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars($label, ENT_QUOTES, 'UTF-8') . '</label>';
    echo '<select id="reinforcement-' . htmlspecialchars($name, ENT_QUOTES, 'UTF-8') . '" name="' . htmlspecialchars($name, ENT_QUOTES, 'UTF-8') . '"' . (in_array($name, array('branch', 'board', 'subject'), true) ? ' disabled' : '') . ($note !== '' ? ' aria-describedby="reinforcement-note-' . htmlspecialchars($name, ENT_QUOTES, 'UTF-8') . '"' : '') . '>';
    foreach ($options as $key => $value) {
        echo '<option value="' . htmlspecialchars($key, ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars($value, ENT_QUOTES, 'UTF-8') . '</option>';
    }
    echo '</select>';
    if ($note !== '') echo '<small id="reinforcement-note-' . htmlspecialchars($name, ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars($note, ENT_QUOTES, 'UTF-8') . '</small>';
    if ($details !== '') echo '<details><summary>Τι προβλέπει η πρόσκληση;</summary>' . $details . '</details>';
    echo '</div>';
}
?>
<!DOCTYPE html><html lang="el"><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<?php require __DIR__ . '/includes/head-pwa.php'; ?>
<title>Μπορώ να κάνω αίτηση στην Ενισχυτική Διδασκαλία; | Εργαλειοθήκη Εκπαιδευτικού</title>
<meta name="description" content="Έλεγχος κλάδου και πινάκων για την Ενισχυτική Γυμνασίου και Ειδικών Μαθημάτων 2026–2027, με οδηγίες αίτησης στο ΟΠΣΥΔ.">
<link rel="stylesheet" href="<?php echo htmlspecialchars(edu_asset_url('assets/common.css'), ENT_QUOTES, 'UTF-8'); ?>">
<link rel="stylesheet" href="<?php echo htmlspecialchars(edu_asset_url('assets/reinforcement.css'), ENT_QUOTES, 'UTF-8'); ?>">
</head><body class="edu-ui edu-guide-standard edu-calc-standard">
<?php require __DIR__ . '/includes/header.php'; ?>
<main id="main-content" class="reinforcement-shell">
<?php eduPageHero(array(
    'title' => 'Μπορώ να κάνω αίτηση στην Ενισχυτική Διδασκαλία;',
    'intro' => 'Έλεγξε κλάδο, πίνακα ΑΣΕΠ και βασικές προϋποθέσεις για Γυμνάσιο ή Ειδικά Μαθήματα (ΕΔΕΜ), για το σχολικό έτος 2026–2027. Στη συνέχεια δες τα βήματα για την αίτηση στο ΟΠΣΥΔ.',
    'meta' => array('Γυμνάσιο & ΕΔΕΜ', 'Ηλεκτρονική αίτηση στο ΟΠΣΥΔ', 'Μία ΔΔΕ ανά αίτηση')
)); ?>
<section class="reinforcement-panel" aria-labelledby="reinforcement-form-title">
<h2 id="reinforcement-form-title">Έλεγχος δυνατότητας αίτησης</h2>
<p id="reinforcement-period" class="reinforcement-date" role="status">Επίλεξε πρόγραμμα για να δεις την προθεσμία.</p>
<?php foreach ($reinforcementSources as $reinforcementProgram => $reinforcementURL):
    $reinforcementItem = isset($reinforcementDeadlines[$reinforcementProgram]) ? $reinforcementDeadlines[$reinforcementProgram] : array(); ?>
<div hidden data-reinforcement-program="<?php echo $reinforcementProgram; ?>" data-start="<?php echo htmlspecialchars(isset($reinforcementItem['start']) ? $reinforcementItem['start'] : '', ENT_QUOTES, 'UTF-8'); ?>" data-end="<?php echo htmlspecialchars(isset($reinforcementItem['end_exclusive']) ? $reinforcementItem['end_exclusive'] : '', ENT_QUOTES, 'UTF-8'); ?>">
<p>Η προθεσμία προέρχεται από την κοινή λίστα της Εργαλειοθήκης. Η πρόσκληση δεν ορίζει συγκεκριμένη ώρα λήξης.</p>
<a href="<?php echo htmlspecialchars($reinforcementURL, ENT_QUOTES, 'UTF-8'); ?>" target="_blank" rel="noopener noreferrer">Επίσημη πρόσκληση <?php echo $reinforcementProgram === 'gym' ? 'Γυμνασίου' : 'ΕΔΕΜ'; ?> ↗</a>
</div><?php endforeach; ?>
<noscript><p>Ενεργοποίησε JavaScript για τον διαδραστικό έλεγχο. Οι επίσημες πηγές και οι οδηγίες αίτησης παραμένουν διαθέσιμες παρακάτω.</p></noscript>
<form id="reinforcement-form"><div class="reinforcement-grid">
<?php
reinforcementSelect('program', 'Σε ποιο πρόγραμμα θέλεις να κάνεις αίτηση;', array(''=>'Επίλεξε πρόγραμμα','gym'=>'Ενισχυτική Διδασκαλία Γυμνασίου','edem'=>'Ενισχυτική Διδασκαλία Ειδικών Μαθημάτων (ΕΔΕΜ)'));
reinforcementSelect('branch', 'Κλάδος / ειδικότητα', array(''=>'Επίλεξε κλάδο','PE02'=>'ΠΕ02 — Φιλόλογοι','PE03'=>'ΠΕ03 — Μαθηματικοί','PE04.01'=>'ΠΕ04.01 — Φυσικοί','PE04.02'=>'ΠΕ04.02 — Χημικοί','PE85'=>'ΠΕ85 — Χημικοί Μηχανικοί','PE06'=>'ΠΕ06 — Αγγλικής','PE05'=>'ΠΕ05 — Γαλλικής','PE07'=>'ΠΕ07 — Γερμανικής','PE34'=>'ΠΕ34 — Ιταλικής','PE40'=>'ΠΕ40 — Ισπανικής','PE79.01'=>'ΠΕ79.01 — Μουσικής','TE16'=>'ΤΕ16 — Μουσικής','PE08'=>'ΠΕ08 — Καλλιτεχνικών','PE89.01'=>'ΠΕ89.01','PE81'=>'ΠΕ81','other'=>'Άλλος κλάδος'), 'Επίλεξε πρώτα πρόγραμμα. Εμφανίζονται μόνο οι κλάδοι που καλεί η συγκεκριμένη πρόσκληση. Αν ο κλάδος σου δεν εμφανίζεται, δεν περιλαμβάνεται σε αυτή.');
reinforcementSelect('board', 'Σε ποιον από τους παρακάτω πίνακες περιλαμβάνεσαι;', array(''=>'Επίλεξε πίνακα','2GE'=>'2ΓΕ/2026 — προσωρινός πίνακας Α΄','1GE'=>'1ΓΕ/2026 — προσωρινός πίνακας Α΄','3EA-main'=>'3ΕΑ/2025 — τελικός κύριος πίνακας Β΄','3EA-aux'=>'3ΕΑ/2025 — τελικός επικουρικός','1GT'=>'1ΓΤ/2024 — τελικός πίνακας Α΄','4EA-main'=>'4ΕΑ/2025 — τελικός κύριος πίνακας Β΄','4EA-aux'=>'4ΕΑ/2025 — τελικός επικουρικός','none'=>'Δεν περιλαμβάνομαι σε κανέναν από αυτούς','unknown'=>'Δεν είμαι σίγουρος/η'), 'Έλεγξε τον πίνακα για τον κλάδο που επέλεξες. Αν περιλαμβάνεσαι σε περισσότερους, επανάλαβε τον έλεγχο για κάθε πίνακα.');
?>
<div id="reinforcement-edem" hidden><?php reinforcementSelect('subject','Ειδικό μάθημα',array(''=>'Επίλεξε μάθημα','french'=>'Γαλλικά','german'=>'Γερμανικά','italian'=>'Ιταλικά','spanish'=>'Ισπανικά','music'=>'Μουσική Αντίληψη, Θεωρία και Αρμονία','free'=>'Ελεύθερο Σχέδιο','linear'=>'Γραμμικό Σχέδιο')); ?></div>
<div id="reinforcement-chemical" hidden><?php reinforcementSelect('chemical','Διαθέτεις πτυχίο Χημικών Μηχανικών;',array(''=>'Δεν έχει επιβεβαιωθεί','yes'=>'Ναι','no'=>'Όχι')); ?></div>
<div id="reinforcement-music" hidden><?php reinforcementSelect('musicSpecialty','ΤΕ16: κατέχεις τη μουσική ειδίκευση «Θεωρητικά Ευρωπαϊκής Μουσικής»;',array(''=>'Δεν έχει επιβεβαιωθεί','yes'=>'Ναι','no'=>'Όχι')); ?></div>
<?php
reinforcementSelect('sanction','Υπάγεσαι σε ενεργή κύρωση αποκλεισμού λόγω προηγούμενης μη ανάληψης υπηρεσίας ή παραίτησης;',array(''=>'Δεν έχει ελεγχθεί','no'=>'Όχι, το έχω ελέγξει','yes'=>'Ναι, υπάρχει ενεργή κύρωση','unknown'=>'Δεν είμαι σίγουρος/η'), 'Αφορά τις κυρώσεις που αναφέρουν οι προσκλήσεις (άρθρο 63 §5Α ν.4589/2019 και άρθρο 86 §4 ν.4547/2018). Η προηγούμενη παραίτηση από μόνη της δεν αρκεί για να υποθέσουμε ενεργό αποκλεισμό.');
$reinforcementYesNo = array(''=>'Επίλεξε απάντηση','yes'=>'Ναι','no'=>'Όχι','unknown'=>'Δεν γνωρίζω');
reinforcementSelect('ageEligible','Καλύπτεις τα ηλικιακά όρια της πρόσκλησης;', $reinforcementYesNo,
    'Κατώτατο όριο: 21ο έτος. Ανώτατο: 67ο έτος. Δες τον ειδικό τρόπο υπολογισμού παρακάτω.',
    '<p>Για τον υπολογισμό του κατώτατου ορίου θεωρείται ημερομηνία γέννησης η <strong>1η Ιανουαρίου</strong> του έτους γέννησης. Για το ανώτατο όριο θεωρείται η <strong>31η Δεκεμβρίου</strong> του έτους γέννησης. Το ανώτατο όριο πρέπει να συντρέχει κατά τη λήξη της προθεσμίας αιτήσεων. Μην απαντήσεις μόνο με βάση τα πραγματικά γενέθλιά σου· αν είσαι κοντά στα όρια και δεν είσαι βέβαιος/η, επίλεξε «Δεν γνωρίζω» και ζήτησε επιβεβαίωση από τη ΔΔΕ.</p>');
reinforcementSelect('citizenshipEligible','Ανήκεις σε κάποια από τις προβλεπόμενες κατηγορίες δικαιώματος συμμετοχής;', $reinforcementYesNo,
    'Έλληνες πολίτες, πολίτες ΕΕ ή οι συγκεκριμένες κατηγορίες ομογενών που αναφέρονται παρακάτω.',
    '<ul><li>Έλληνες πολίτες.</li><li>Πολίτες κρατών-μελών της Ευρωπαϊκής Ένωσης, σύμφωνα με τον ν.2431/1996 και τους περιορισμούς του άρθρου 1 §1.</li><li>Βορειοηπειρώτες, Κύπριοι ομογενείς και ομογενείς αλλοδαποί από Κωνσταντινούπολη, Ίμβρο, Τένεδο και Αίγυπτο, με την προβλεπόμενη απόδειξη της ιδιότητας κατά το ν.δ.3832/1958.</li></ul><p>Για ειδική περίπτωση ή εκκρεμή απόδειξη δικαιώματος, επίλεξε «Δεν γνωρίζω».</p>');
reinforcementSelect('dutiesEligible','Πληροίς τη νόμιμη προϋπόθεση υγείας που επιτρέπει την εκτέλεση των καθηκόντων της θέσης;', $reinforcementYesNo,
    'Η πρόσκληση παραπέμπει στο άρθρο 7 ν.3528/2007. Δεν καταχωρίζεις ιατρικά στοιχεία ή διάγνωση.');
reinforcementSelect('applicationImpediment','Συντρέχει κάποιο από τα κωλύματα υποβολής αίτησης της παρακάτω λίστας;', $reinforcementYesNo,
    'Ναι = υπάρχει κώλυμα. Όχι = δεν συντρέχει καμία από τις αναφερόμενες περιπτώσεις. Αν δεν είσαι βέβαιος/η, επίλεξε «Δεν γνωρίζω».',
    '<ul><li>Απόλυση από δημόσια υπηρεσία λόγω πειθαρχικής οριστικής παύσης ή καταγγελία σύμβασης για σπουδαίο λόγο που οφείλεται σε υπαιτιότητα του εργαζομένου, χωρίς να έχει παρέλθει ο νόμιμος χρόνος αποκλεισμού.</li><li>Ποινική δίωξη ή καταδίκη για έγκλημα κατά της γενετήσιας ελευθερίας ή οικονομικής εκμετάλλευσης της γενετήσιας ζωής.</li><li>Άλλο κώλυμα διορισμού του άρθρου 8 ν.3528/2007, όπως ισχύει (οι προβλεπόμενες καταδίκες/παραπομπές, αποστέρηση δημόσιας θέσης ή στέρηση πολιτικών δικαιωμάτων, δικαστική συμπαράσταση και ανάκληση διορισμού που προκλήθηκε με δόλο ή παρανομία του ίδιου του προσώπου, στις περιπτώσεις και για τον χρόνο που ορίζει ο νόμος).</li></ul><p>Τα κωλύματα πρέπει να ελέγχονται τόσο στη λήξη της προθεσμίας αιτήσεων όσο και στην πρόσληψη. Για αμφιβολία ως προς συγκεκριμένη περίπτωση ή τη λήξη αποκλεισμού, ζήτησε υπηρεσιακή επιβεβαίωση. Δεν χρειάζεται να δηλώσεις εδώ ποια περίπτωση σε αφορά.</p><p>Οι κυρώσεις λόγω μη ανάληψης/παραίτησης και οι στρατιωτικές υποχρεώσεις ελέγχονται στις δικές τους ερωτήσεις. Εργασία σε ιδιωτικό σχολείο/φροντιστήριο, εμπορική ιδιότητα και άλλη σύμβαση εξετάζονται χωριστά ως ζητήματα ανάληψης υπηρεσίας.</p>');
reinforcementSelect('military','Στρατιωτικές υποχρεώσεις στο ΟΠΣΥΔ',array(''=>'Δεν έχει ελεγχθεί','yes'=>'Έχει επιβεβαιωθεί το απαιτούμενο πεδίο «ΝΑΙ»','na'=>'Δεν με αφορά','no'=>'Το απαιτούμενο πεδίο δεν έχει ενημερωθεί','unknown'=>'Εκκρεμεί έλεγχος / ειδική περίπτωση'), 'Για τους υποψηφίους που αφορά, η πρόσκληση ζητά έλεγχο του πεδίου «Εκπληρωμένες Στρατ. Υποχρεώσεις». Για ειδικές περιπτώσεις πολιτών ΕΕ επικοινώνησε με την αρμόδια υπηρεσία.');
reinforcementSelect('service','Έλεγξες τυχόν κώλυμα ανάληψης υπηρεσίας ή άλλη ενεργή εκπαιδευτική σύμβαση;',array(''=>'Δεν έχει ελεγχθεί','clear'=>'Ναι, δεν διαπιστώνω σχετικό ζήτημα','issue'=>'Υπάρχει ή ενδέχεται να υπάρχει ζήτημα','unknown'=>'Χρειάζομαι υπηρεσιακή διευκρίνιση'), 'Το δικαίωμα αίτησης δεν ταυτίζεται με τη δυνατότητα ανάληψης υπηρεσίας. Δες τις διευκρινίσεις παρακάτω.');
?>
</div><div class="reinforcement-actions"><button type="submit">Έλεγχος δυνατότητας αίτησης</button><button type="reset">Καθαρισμός</button></div></form>
<section id="reinforcement-result" class="reinforcement-result" role="status" aria-live="polite" aria-atomic="true"><p>Συμπλήρωσε τις επιλογές σου και πάτησε «Έλεγχος δυνατότητας αίτησης».</p></section>
</section>
<section class="reinforcement-panel" aria-labelledby="reinforcement-steps"><h2 id="reinforcement-steps">Πώς κάνω την αίτηση;</h2>
<ol><li>Επιβεβαίωσε τον κλάδο και την ένταξή σου στον προβλεπόμενο πίνακα ΑΣΕΠ.</li><li>Διάβασε την αντίστοιχη πρόσκληση και τον συνημμένο πίνακα κατανομής εκπαιδευτικών ανά ΔΔΕ και κλάδο.</li><li>Συνδέσου στο ΟΠΣΥΔ μέσα στην προθεσμία και επίλεξε τη χωριστή αίτηση για το πρόγραμμα που σε ενδιαφέρει.</li><li>Επίλεξε <strong>μία (1) Διεύθυνση Δευτεροβάθμιας Εκπαίδευσης</strong>, στην οποία επιθυμείς να εργαστείς.</li><li>Έλεγξε τα δηλωμένα στοιχεία και ολοκλήρωσε την υποβολή. Κράτησε αντίγραφο ή αποδεικτικό της καταχωρισμένης αίτησης.</li><li>Παρακολούθησε τις ανακοινώσεις της επιλεγμένης ΔΔΕ για τις επόμενες ενέργειες, τις προτιμήσεις ΣΚΑΕ και τις τοποθετήσεις.</li></ol>
<div class="reinforcement-links"><a class="reinforcement-link" href="https://opsyd.sch.gr/" target="_blank" rel="noopener noreferrer">Μετάβαση στο ΟΠΣΥΔ ↗</a><a href="prothesmies.php">Όλες οι προθεσμίες →</a></div>
<p class="reinforcement-note">Οι προσκλήσεις αφορούν πρόσληψη <strong>αναπληρωτών μειωμένου ωραρίου ή ωρομισθίων</strong>, με σχέση εργασίας ιδιωτικού δικαίου ορισμένου χρόνου. Η αίτηση υπέχει θέση υπεύθυνης δήλωσης.</p></section>
<section class="reinforcement-panel"><h2>Συχνές απορίες</h2>
<details><summary>Αρκεί να έχω κάνει αίτηση σε προκήρυξη ΑΣΕΠ;</summary><p>Χρειάζεται να περιλαμβάνεσαι στον συγκεκριμένο αξιολογικό πίνακα που καλεί η πρόσκληση, για τον κατάλληλο κλάδο. Η υποβολή αίτησης στο ΑΣΕΠ δεν αποδεικνύει από μόνη της ένταξη στον πίνακα.</p></details>
<details><summary>Μπορώ να δηλώσω πολλές Διευθύνσεις;</summary><p>Κάθε πρόσκληση προβλέπει επιλογή μίας (1) ΔΔΕ στην αντίστοιχη ηλεκτρονική αίτηση. Οι τοποθετήσεις γίνονται από τη ΔΔΕ με βάση την κατάταξη και τις δηλώσεις προτίμησης ΣΚΑΕ.</p></details>
<details><summary>Χρειάζεται ξεχωριστή αίτηση για Γυμνάσιο και ΕΔΕΜ;</summary><p>Πρόκειται για δύο χωριστές προσκλήσεις με διαφορετικούς κλάδους και πίνακες. Έλεγξε χωριστά κάθε πρόγραμμα και συμπλήρωσε την αντίστοιχη αίτηση στο ΟΠΣΥΔ, εφόσον καλύπτεσαι.</p></details>
<details><summary>Είμαι ΠΕ85 ή ΤΕ16. Τι επιπλέον απαιτείται;</summary><p>Για το Γυμνάσιο καλείται ο ΠΕ85 με πτυχίο Χημικών Μηχανικών. Στην ΕΔΕΜ ο ΤΕ16 πρέπει να κατέχει τη μουσική ειδίκευση «Θεωρητικά Ευρωπαϊκής Μουσικής» και προσλαμβάνεται μετά την εξάντληση των υποψηφίων ΠΕ79.01.</p></details>
<details><summary>Μπορώ να συμμετέχω στην ΕΔΕΜ από πίνακα ΕΑΕ;</summary><p>Η πρόσκληση περιλαμβάνει τους προβλεπόμενους κύριους και επικουρικούς πίνακες 3ΕΑ/2025 για τους κλάδους ΠΕ και 4ΕΑ/2025 για ΤΕ16. Η προτεραιότητα πρόσληψης εξαρτάται από την προέλευση της πλειοψηφίας των μαθητών του τμήματος και την εξάντληση των σχετικών πινάκων. Η ένταξη σε ΕΑΕ δεν αρκεί χωρίς τον καλούμενο κλάδο/ειδίκευση.</p></details>
<details><summary>Αν προσληφθώ, παραμένει ενεργή η υποψηφιότητά μου για Γενική ή ΕΑΕ;</summary><p>Οι προσκλήσεις ορίζουν ότι παραμένεις υποψήφιος για πρόσληψη στη Γενική Εκπαίδευση ή στις δομές ΕΑΕ, εφόσον έχεις υποβάλει τη σχετική αίτηση και παραμένει ενεργή. Αυτό δεν επιτρέπει αυτομάτως παράλληλη απασχόληση.</p></details>
<details><summary>Έχω άλλη εκπαιδευτική σύμβαση ή εργάζομαι σε ιδιωτικό σχολείο/φροντιστήριο.</summary><p>Έλεγξε τα κωλύματα ανάληψης υπηρεσίας στην πρόσκληση πριν αποδεχθείς πρόσληψη. Για ιδιωτικό σχολείο/φροντιστήριο, εμπορική ιδιότητα, άλλη δημόσια θέση ή εκπαιδευτική θέση στο εξωτερικό υπάρχουν ειδικές προβλέψεις. Η ΕΔΕΜ αναφέρει ρητά ότι δεν επιτρέπονται δύο εκπαιδευτικές συμβάσεις στο ΥΠΑΙΘΑ, με εξαίρεση αποδεδειγμένη έλλειψη υποψηφίων και εφόσον συντρέχουν οι νόμιμες προϋποθέσεις. Η υπηρεσία πρέπει να εξετάσει την περίπτωσή σου· δεν προκύπτει αυτόματη άδεια δεύτερης σύμβασης από τον παρόντα έλεγχο.</p></details>
<details><summary>Τι συμβαίνει αν δεν αναλάβω ή αν παραιτηθώ από την Ενισχυτική;</summary><p>Οι συγκεκριμένες προσκλήσεις προβλέπουν μετακίνηση στο τέλος του πίνακα της οικείας ΔΔΕ για το αντίστοιχο πρόγραμμα. Αυτό είναι διαφορετικό από τον έλεγχο ήδη ενεργών κυρώσεων λόγω προηγούμενης μη ανάληψης ή παραίτησης, οι οποίες αποκλείουν συμμετοχή στην παρούσα διαδικασία.</p></details>
<details><summary>Πώς υπολογίζονται τα μόρια για την πρόσληψη;</summary><p>Δεν προβλέπεται εδώ νέος ανεξάρτητος υπολογισμός μορίων. Η επιλογή γίνεται με βάση τη σειρά κατάταξης στον οικείο πίνακα ΑΣΕΠ και τους κανόνες της πρόσκλησης.</p></details>
</section>
<?php sourceCardStart(array('title'=>'Πηγές / Προσκλήσεις 2026–2027')); ?>
<p>Έλεγχος πηγών: 06/11/2026. Προσκλήσεις 151710/Δ2/06-11-2026 (Γυμνάσιο, ΑΔΑ 911Β46ΝΚΠΔ-0ΒΝ) και 151737/ΓΔ4/06-11-2026 (ΕΔΕΜ, ΑΔΑ 9Φ8846ΝΚΠΔ-ΙΟ0). Οι συνημμένοι πίνακες κατανομής διατίθενται στις ανακοινώσεις.</p>
<?php sourceCardLinksStart(); foreach ($reinforcementSources as $reinforcementProgram => $reinforcementURL) sourceCardLink($reinforcementURL, $reinforcementProgram === 'gym' ? 'ΥΠΑΙΘΑ — Γυμνάσιο: πρόσκληση και πίνακας κατανομής ↗' : 'ΥΠΑΙΘΑ — ΕΔΕΜ: πρόσκληση και πίνακας κατανομής ↗'); sourceCardLinksEnd(); ?>
<p>Για τα κωλύματα διορισμού λαμβάνονται υπόψη και τα άρθρα 3–4 του ν.5225/2025: <a href="https://www.ypes.gr/wp-content/uploads/2025/09/n5225-FEKA152-20250100152.pdf" target="_blank" rel="noopener noreferrer">Υπουργείο Εσωτερικών — ΦΕΚ Α΄152/2025 ↗</a>.</p>
<?php sourceCardDisclaimerStart(); ?>Ενδεικτικός έλεγχος των δηλωμένων στοιχείων. Η υπηρεσία ελέγχει την πραγματική ένταξη στους πίνακες, τα δικαιολογητικά, τις γενικές προϋποθέσεις και τα κωλύματα. Δεν αποθηκεύονται ούτε αποστέλλονται οι απαντήσεις σου.<?php sourceCardDisclaimerEnd(); sourceCardEnd(); ?>
</main>
<?php require __DIR__ . '/includes/footer.php'; ?>
<script src="<?php echo htmlspecialchars(edu_asset_url('includes/reinforcement-eligibility.js'), ENT_QUOTES, 'UTF-8'); ?>" defer></script>
<script src="<?php echo htmlspecialchars(edu_asset_url('includes/reinforcement-ui.js'), ENT_QUOTES, 'UTF-8'); ?>" defer></script>
<script src="<?php echo htmlspecialchars(edu_asset_url('assets/common.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
</body></html>
