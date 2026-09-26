<?php require_once __DIR__ . '/includes/config.php'; ?>
<?php require_once __DIR__ . '/includes/weekly-timetable-data.php'; ?>
<?php require_once __DIR__ . '/includes/legal-sources.php'; ?>
<?php require_once __DIR__ . '/includes/reference-sources.php'; ?>
<?php require_once __DIR__ . '/includes/weekly-timetable-legal.php'; ?>
<?php require_once __DIR__ . '/includes/school-profile-legal.php'; ?>
<?php require_once __DIR__ . '/includes/ethics-class-formation.php'; ?>
<!doctype html>
<html lang="el">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <?php require __DIR__ . '/includes/head-pwa.php'; ?>
  <title>Ώρες Μαθημάτων στο Εβδομαδιαίο Ωρολόγιο Πρόγραμμα</title>
  <link rel="stylesheet" href="<?php echo htmlspecialchars(edu_asset_url('assets/common.css'), ENT_QUOTES, 'UTF-8'); ?>">
  <link rel="stylesheet" href="<?php echo htmlspecialchars(edu_asset_url('assets/weekly-timetable.css'), ENT_QUOTES, 'UTF-8'); ?>">

</head>
<body class="edu-ui edu-calc-standard edu-page-weekly-timetable">
<?php require_once __DIR__ . '/includes/header.php'; ?>
<?php require_once __DIR__ . '/includes/components/calculator-layout.php'; ?>

<main id="weeklyTimetableTool" class="app">
  <?php
  // Εσωτερική σημείωση / roadmap — να μην εμφανίζεται στο public UI:
  // Το εργαλείο έχει σχεδιαστεί ώστε σε επόμενη φάση να συνδεθεί με τις
  // Α΄/Β΄/Γ΄ αναθέσεις και με το υποχρεωτικό ωράριο κάθε εκπαιδευτικού.
  ?>
  <?php calculatorHero(array(
    'title_html' => 'Ώρες Μαθημάτων στο Εβδομαδιαίο Ωρολόγιο Πρόγραμμα',
    'intro' => 'Επίλεξε τύπο σχολείου και τάξη για να δεις τις εβδομαδιαίες ώρες κάθε μαθήματος, όπως ισχύουν για το σχολικό έτος 2026–2027.',
    'meta_class' => 'meta',
    'badges' => array('2026–2027', 'Γυμνάσιο & ΓΕΛ', 'Πρότυπα Εκκλησιαστικά', 'ΕΠΑ.Λ. & Π.ΕΠΑ.Λ.', 'ΕΝ.Ε.Ε.ΓΥ.-Λ.', 'Ε.Ε.Ε.ΕΚ.', 'Ημερήσιο & Εσπερινό', 'Καλλιτεχνικά', 'Μουσικά')
  )); ?>

  <?php calculatorColumnsStart(); ?>
    <?php calculatorMainStart(); ?>
      <?php calculatorCardStart(); ?>
        <h2>Ωρολόγιο πρόγραμμα</h2>
        <p class="cap">Το εργαλείο εμφανίζει το επίσημο εβδομαδιαίο πρόγραμμα ανά τάξη. Στα ΓΕΛ οι Ομάδες Προσανατολισμού εμφανίζονται χωριστά από τη Γενική Παιδεία. Στα Πρότυπα Εκκλησιαστικά Σχολεία εμφανίζεται χωριστά η Θρησκευτική Εξειδίκευση. Στα Καλλιτεχνικά Σχολεία επιλέγεται κατεύθυνση. Στη Β΄–Γ΄ τάξη ΕΠΑ.Λ./Π.ΕΠΑ.Λ. επιλέγεται τομέας και στη Γ΄ τάξη επιλέγεται επιπλέον ειδικότητα. Στο Λύκειο ΕΝ.Ε.Ε.ΓΥ.-Λ. επιλέγεται τομέας στη Β΄–Δ΄ τάξη και ειδικότητα στη Δ΄. Στα Ε.Ε.Ε.ΕΚ. εμφανίζεται το σταθερό πρόγραμμα Α΄–Ε΄ και η ειδική, εξατομικευμένη λειτουργία της ΣΤ΄ τάξης. Στον Τομέα Υγείας της Β΄/Γ΄ τάξης εμφανίζεται επιπλέον η προβλεπόμενη από το ΦΕΚ περίπτωση διδασκαλίας.</p>

        <div class="field-grid">
          <div class="field">
            <label for="schoolType">Τύπος σχολείου</label>
            <select id="schoolType">
              <?php foreach (weeklyTimetableSchoolTypes() as $code => $info): ?>
                <option value="<?php echo htmlspecialchars($code, ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($info['label'], ENT_QUOTES, 'UTF-8'); ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="field">
            <label for="grade">Τάξη</label>
            <select id="grade"></select>
          </div>
          <div id="trackField" class="field" hidden>
            <label id="trackLabel" for="track">Κατεύθυνση</label>
            <select id="track"></select>
          </div>
          <div id="variantField" class="field" hidden>
            <label id="variantLabel" for="variant">Περίπτωση</label>
            <select id="variant"></select>
          </div>
          <div id="specialtyField" class="field" hidden>
            <label id="specialtyLabel" for="specialty">Ειδικότητα</label>
            <select id="specialty"></select>
          </div>
        </div>

        <div id="programSummary" class="timetable-summary" aria-live="polite"></div>
        <div id="timetableResults" aria-live="polite"></div>

      <?php calculatorCardEnd(); ?>
    <?php calculatorMainEnd(); ?>

    <?php calculatorResultsStart(array('class' => 'card results')); ?>
      <?php calculatorDisclosureStart(array(
        'class' => 'edu-result-disclosure',
        'summary' => 'Τι σημαίνει «ώρες»',
        'open' => true,
        'attrs' => array('data-mobile-collapsed' => 'true')
      )); ?>
        <p class="cap">Οι ώρες αφορούν το εβδομαδιαίο ωρολόγιο πρόγραμμα του συγκεκριμένου τμήματος/ομάδας και όχι το ατομικό υποχρεωτικό ωράριο ενός εκπαιδευτικού.</p>
        <p class="help">Στη Γ΄ ΓΕΛ ορισμένα μαθήματα Γενικής Παιδείας εξαρτώνται από την Ομάδα Προσανατολισμού. Δεν πρέπει να αθροίζονται σαν να τα παρακολουθούν όλοι οι μαθητές.</p>
        <p class="help">Στο Β΄ Εσπερινού ΓΕΛ οι ενδείξεις <strong>1/2</strong> και <strong>2/1</strong> διατηρούνται όπως ακριβώς στο ΦΕΚ και δεν μετατρέπονται σε τεχνητό μέσο όρο.</p>
        <p class="help">Στα επαγγελματικά μαθήματα: <strong>Θ</strong> = θεωρία, <strong>Ε</strong> = εργαστήριο, <strong>Σ</strong> = σχέδιο και <strong>ΠΑ</strong> = πρακτική άσκηση.</p>
        <p class="help"><strong>Ε.Ε.Ε.ΕΚ.:</strong> στις Α΄–Ε΄ τάξεις το βασικό ωρολόγιο είναι 30 ώρες. Η κύρια/Β΄/Γ΄ εξειδίκευση αντιστοιχεί στα εργαστήρια που λειτουργούν στη συγκεκριμένη σχολική μονάδα. Όπου δεν υπάρχει Γ΄ εξειδίκευση, οι ώρες της κατανέμονται στην κύρια ή στη Β΄. Στη ΣΤ΄ τάξη δεν υπάρχει ενιαίος σταθερός αριθμητικός πίνακας: η πρακτική άσκηση και η συμπλήρωση με μαθήματα προηγούμενων τάξεων καθορίζονται από τον Σύλλογο Διδασκόντων και το ΕΠΕ.</p>
        <p class="help"><strong>Ηθική:</strong> από το 2026–2027, στα Γυμνάσια και Γενικά Λύκεια απαιτούνται τουλάχιστον <strong>10 απαλλασσόμενοι/ες ανά τάξη</strong>, με το όριο να έχει συμπληρωθεί έως την πέμπτη ημέρα από την έναρξη των μαθημάτων. Με ένα τμήμα, Θρησκευτικά και Ηθική διδάσκονται την ίδια ώρα σε διακριτές αίθουσες. Σε περισσότερα τμήματα, αν μπορούν να συγκροτηθούν ισοδύναμα τμήματα Ηθικής, αυτά δημιουργούνται χωρίς αύξηση του συνολικού αριθμού τμημάτων· διαφορετικά οι απαλλασσόμενοι/ες συγκεντρώνονται στο τμήμα με τους/τις περισσότερους/ες απαλλασσόμενους/ες και τα δύο μαθήματα διδάσκονται παράλληλα σε διακριτές αίθουσες. Αν το όριο των 10 δεν συμπληρωθεί εμπρόθεσμα, εφαρμόζεται η προβλεπόμενη διαδικασία της Κ.Υ.Α. Β΄ 5130/2024. Σε Πρότυπα Εκκλησιαστικά Σχολεία, ΕΠΑ.Λ., Π.ΕΠΑ.Λ. και ΕΝ.Ε.Ε.ΓΥ.-Λ. δεν επεκτείνουμε αυτόματα τον κανόνα χωρίς ειδική κανονιστική βάση.</p>
        <p class="help"><strong>Τεχνολογία / Πληροφορική — Ημερήσιο Γυμνάσιο:</strong> όταν ένα τμήμα έχει πάνω από <strong>21 μαθητές/ήτριες</strong>, χωρίζεται σε δύο ομάδες. Στην Α΄ τάξη ο χωρισμός αφορά την Πληροφορική, την Τεχνολογία και την Οικιακή Οικονομία· στη Β΄ και Γ΄ αφορά την Πληροφορική και την Τεχνολογία. Οι ώρες του βασικού πίνακα παραμένουν ώρες ανά μαθητή/τμήμα, αλλά για τον υπολογισμό αναγκών εκπαιδευτικών δημιουργούνται πρόσθετες ώρες ομάδων.</p>
      <?php calculatorDisclosureEnd(); ?>
<?php calculatorResultsEnd(); ?>
  <?php calculatorColumnsEnd(); ?>
</main>

<?php sourceCardStart(); ?>
  <?php sourceCardDisclaimerStart(); ?>
    Η τρέχουσα έκδοση καλύπτει Ημερήσιο και Εσπερινό Γυμνάσιο/ΓΕΛ, Πρότυπο Εκκλησιαστικό Γυμνάσιο και Πρότυπο Εκκλησιαστικό Λύκειο, Καλλιτεχνικό και Μουσικό Γυμνάσιο/Γενικό Λύκειο, Γυμνάσιο και Λύκειο ΕΝ.Ε.Ε.ΓΥ.-Λ., Ε.Ε.Ε.ΕΚ. Α΄–ΣΤ΄, Α΄–Γ΄ τάξη Ημερήσιου και τριετούς Εσπερινού ΕΠΑ.Λ., καθώς και Α΄–Γ΄ τάξη Π.ΕΠΑ.Λ., σύμφωνα με τα ισχύοντα ωρολόγια προγράμματα για το σχολικό έτος 2026–2027. Στην Α΄ Π.ΕΠΑ.Λ. τα έξι μαθήματα Επαγγελματικής Κατεύθυνσης είναι ενιαία εργαστηριακά blocks 2Ε/3Ε· οι αναθέσεις γίνονται ανά θεματική ενότητα και δεν προκύπτει σταθερή κατανομή ωρών ανά ενότητα από το ΦΕΚ αναθέσεων. Για τη Γ΄ Π.ΕΠΑ.Λ. οι τίτλοι μαθημάτων έχουν διασταυρωθεί και με το αυτοτελές ΦΕΚ αναθέσεων Β΄ 5510/2023. Για τα Καλλιτεχνικά Σχολεία η αντιπαραβολή γίνεται με το ΦΕΚ Β΄ 2583/2026, όπως τροποποιήθηκε με το ΦΕΚ Β΄ 5555/2026, για τη γενική παιδεία και με το ΦΕΚ Β΄ 3418/2024 για την καλλιτεχνική παιδεία. Για τα Μουσικά Σχολεία η αντιπαραβολή τίτλων γίνεται και με το ΦΕΚ αναθέσεων μουσικής παιδείας Β΄ 4202/2018.
  <?php sourceCardDisclaimerEnd(); ?>
  <?php sourceCardLinksStart(); ?>
    <?php sourceCardLink(ethicsClassFormationPolicy()['source_url'], 'Υ.Α. 108070/Δ2/2026 — ΦΕΚ Β΄ 5231/2026 · Διδασκαλία Ηθικής ↗'); ?>
    <?php $referenceLink = referenceSourceLinkByKey('pes_regulatory_framework_official_page'); if ($referenceLink) sourceCardLink($referenceLink['url'], $referenceLink['label']); ?>
    <?php $ecclesiasticalLegalLink = weeklyTimetableEcclesiasticalLegalLink('ecclesiastical_gymnasio_timetable_2025', 'overview'); ?>
    <?php if ($ecclesiasticalLegalLink): sourceCardLink($ecclesiasticalLegalLink['url'], $ecclesiasticalLegalLink['label']); endif; ?>
    <?php $ecclesiasticalLegalLink = weeklyTimetableEcclesiasticalLegalLink('ecclesiastical_timetable_2021', 'overview'); ?>
    <?php if ($ecclesiasticalLegalLink): sourceCardLink($ecclesiasticalLegalLink['url'], $ecclesiasticalLegalLink['label']); endif; ?>
    <?php $ecclesiasticalLegalLink = weeklyTimetableEcclesiasticalLegalLink('ecclesiastical_lykeio_timetable_2022', 'overview'); ?>
    <?php if ($ecclesiasticalLegalLink): sourceCardLink($ecclesiasticalLegalLink['url'], $ecclesiasticalLegalLink['label']); endif; ?>
    <?php foreach (schoolProfileGeneralEducationLegalLinks() as $legalLink): ?>
      <?php sourceCardLink($legalLink['url'], $legalLink['label']); ?>
    <?php endforeach; ?>
    <?php foreach (weeklyTimetableLegalOverviewLinks() as $legalLink): ?>
      <?php sourceCardLink($legalLink['url'], $legalLink['label']); ?>
    <?php endforeach; ?>
    <?php foreach (weeklyTimetableSpecialSchoolOverviewLinks('arts_music') as $legalLink): ?>
      <?php sourceCardLink($legalLink['url'], $legalLink['label']); ?>
    <?php endforeach; ?>
    <?php foreach (legalSourceLinksForKeys(weeklyTimetableLegalSourceKeysForSchools(array('eneegyl_gymnasio', 'eneegyl_lykeio'))) as $legalLink): ?>
      <?php sourceCardLink($legalLink['url'], $legalLink['label']); ?>
    <?php endforeach; ?>
    <?php foreach (weeklyTimetableSpecialSchoolOverviewLinks('eeeek') as $legalLink): ?>
      <?php sourceCardLink($legalLink['url'], $legalLink['label']); ?>
    <?php endforeach; ?>
    <?php $vocationalLegalLinks = weeklyTimetableVocationalOverviewLinks(); ?>
    <?php foreach (array_slice($vocationalLegalLinks, 0, 2) as $legalLink): ?>
      <?php sourceCardLink($legalLink['url'], $legalLink['label']); ?>
    <?php endforeach; ?>
    <?php $referenceLink = referenceSourceLinkByKey('vocational_grade_c_timetables_official_index'); if ($referenceLink) sourceCardLink($referenceLink['url'], $referenceLink['label']); ?>
    <?php foreach (array_slice($vocationalLegalLinks, 2) as $legalLink): ?>
      <?php sourceCardLink($legalLink['url'], $legalLink['label']); ?>
    <?php endforeach; ?>
  <?php sourceCardLinksEnd(); ?>
<?php sourceCardEnd(); ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
<script src="<?php echo htmlspecialchars(edu_asset_url('assets/common.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
<template id="weeklyTimetableData"><?php echo htmlspecialchars(json_encode(array(
  'schools' => weeklyTimetableSchoolTypes(),
  'rows' => weeklyTimetablePublicRows(),
  'ethicsPolicy' => ethicsClassFormationPublicPolicy(),
), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), ENT_NOQUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?></template>
<script src="<?php echo htmlspecialchars(edu_asset_url('includes/weekly-timetable-ui.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
</body>
</html>
