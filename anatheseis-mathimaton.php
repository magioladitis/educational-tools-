<?php require_once __DIR__ . '/includes/config.php'; ?>
<?php require_once __DIR__ . '/includes/teaching-assignments-data.php'; ?>
<?php require_once __DIR__ . '/includes/legal-sources.php'; ?>
<?php require_once __DIR__ . '/includes/reference-sources.php'; ?>
<?php require_once __DIR__ . '/includes/teaching-assignments-legal.php'; ?>
<!doctype html>
<html lang="el">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Αναθέσεις Μαθημάτων ανά Ειδικότητα</title>
  <link rel="stylesheet" href="<?php echo htmlspecialchars(edu_asset_url('assets/common.css'), ENT_QUOTES, 'UTF-8'); ?>">
  <style>
    /* Page-specific only: the shared calculator UI comes from assets/common.css. */
    .edu-page-teaching-assignments .school-type-group{
      margin-top:12px;
    }

    .edu-page-teaching-assignments .school-type-group__title{
      margin:0 0 7px;
      color:var(--edu-muted);
      font-size:13px;
      font-weight:800;
      text-transform:uppercase;
      letter-spacing:.03em;
    }

    .edu-page-teaching-assignments .school-type-options{
      display:grid;
      grid-template-columns:repeat(2,minmax(0,1fr));
      gap:10px;
      margin-bottom:4px;
    }

    .edu-page-teaching-assignments .school-type-group--all .school-type-options{
      grid-template-columns:1fr;
    }

    .edu-page-teaching-assignments .school-type-group--all .checkrow{
      background:var(--edu-primary-soft);
      border-color:var(--edu-primary);
    }

    .edu-page-teaching-assignments .school-type-options .checkrow{
      margin:0;
      padding:12px 14px;
      border:1px solid var(--edu-border);
      border-radius:12px;
      background:var(--edu-surface-soft);
    }

    .edu-page-teaching-assignments #assignmentResults{
      margin-top:14px;
    }

    .edu-page-teaching-assignments #assignmentResults > section{
      margin-top:18px;
    }

    .edu-page-teaching-assignments #assignmentResults > section:first-child{
      margin-top:8px;
    }

    .edu-page-teaching-assignments #assignmentResults h3{
      display:flex;
      align-items:center;
      justify-content:space-between;
      gap:10px;
      margin:0 0 8px;
      padding-bottom:8px;
      border-bottom:1px solid var(--edu-result-row-separator);
      font-size:1.04rem;
    }

    .edu-page-teaching-assignments #assignmentResults .result-row{
      align-items:flex-start;
    }

    .edu-page-teaching-assignments #assignmentResults .result-row > span{
      min-width:0;
      line-height:1.35;
    }

    .edu-page-teaching-assignments #assignmentResults .result-row > span > strong{
      display:block;
      margin-bottom:3px;
      text-align:left;
    }

    .edu-page-teaching-assignments #assignmentResults .result-row small{
      display:block;
      color:var(--edu-muted);
      line-height:1.35;
    }

    .edu-page-teaching-assignments .assignment-badge{
      flex:0 0 auto;
      min-width:34px;
      padding:4px 8px;
      border-radius:999px;
      text-align:center !important;
      font-size:.82rem;
      line-height:1.2;
    }

    .edu-page-teaching-assignments .assignment-a .assignment-badge{
      background:var(--edu-success-soft);
      color:var(--edu-success);
    }

    .edu-page-teaching-assignments .assignment-b .assignment-badge{
      background:var(--edu-primary-soft);
      color:var(--edu-primary-dark);
    }

    .edu-page-teaching-assignments .assignment-c .assignment-badge{
      background:var(--edu-warning-soft);
      color:var(--edu-warning);
    }

    .edu-page-teaching-assignments .assignment-s .assignment-badge{
      background:var(--edu-neutral-soft);
      color:var(--edu-muted);
      min-width:64px;
    }

    @media (max-width:700px){
      .edu-page-teaching-assignments .school-type-options{
        grid-template-columns:1fr;
      }
    }
  </style>
</head>
<body class="edu-ui edu-calc-standard edu-page-teaching-assignments">
<?php require_once __DIR__ . '/includes/header.php'; ?>
<?php require_once __DIR__ . '/includes/components/calculator-layout.php'; ?>

<main id="teachingAssignmentsTool" class="app">
  <?php calculatorHero(array(
    'title_html' => 'Αναθέσεις Μαθημάτων ανά Ειδικότητα',
    'intro' => 'Επίλεξε τον κλάδο / την ειδικότητά σου και δες ποια μαθήματα έχεις σε Α΄, Β΄ ή Γ΄ ανάθεση.',
    'meta_class' => 'meta',
    'badges' => array('2026–2027', 'Ημερήσια & Εσπερινά', 'ΕΠΑ.Λ. / Π.ΕΠΑ.Λ.', 'Ε.Α.Ε.', 'ΕΝ.Ε.Ε.ΓΥ.-Λ.', 'Ε.Ε.Ε.ΕΚ.', 'Καλλιτεχνικά', 'Μουσικά', 'Πρότυπα Εκκλησιαστικά', 'Α΄ · Β΄ · Γ΄ ανάθεση')
  )); ?>

  <?php calculatorColumnsStart(); ?>
    <?php calculatorMainStart(); ?>
      <?php calculatorCardStart(); ?>
        <h2>Κλάδος / ειδικότητα</h2>
        <p class="cap">Επίλεξε τον κλάδο σου για να εμφανιστούν αυτόματα οι αναθέσεις μαθημάτων που αντιστοιχούν στην ειδικότητά σου.</p>

        <div class="field-grid">
          <div class="field">
            <label for="specialty">Κλάδος / ειδικότητα</label>
            <select id="specialty">
              <option value="">— Επιλογή κλάδου —</option>
              <?php foreach (teachingAssignmentKnownSpecialties() as $code): ?>
                <?php if ($code === 'ΤΕ') continue; // τεχνικός γενικός δείκτης, όχι πραγματικός κλάδος ?>
                <option value="<?php echo htmlspecialchars($code, ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($code, ENT_QUOTES, 'UTF-8'); ?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>

        <div class="field">
          <label>Τύπος σχολείου</label>
          <div class="school-type-group school-type-group--all">
            <div class="school-type-options">
              <div class="checkrow">
                <input type="checkbox" id="schoolAll">
                <label for="schoolAll"><strong>Όλα</strong> <small>(επιλογή / αποεπιλογή όλων των δομών)</small></label>
              </div>
            </div>
          </div>
          <div class="school-type-group">
            <div class="school-type-group__title">Γενική Εκπαίδευση</div>
            <div class="school-type-options">
              <div class="checkrow">
                <input type="checkbox" id="schoolGymnasio" checked>
                <label for="schoolGymnasio">Γυμνάσιο</label>
              </div>
              <div class="checkrow">
                <input type="checkbox" id="schoolEveningGym">
                <label for="schoolEveningGym">Εσπερινό Γυμνάσιο</label>
              </div>
              <div class="checkrow">
                <input type="checkbox" id="schoolGel" checked>
                <label for="schoolGel">Γενικό Λύκειο (ΓΕΛ)</label>
              </div>
              <div class="checkrow">
                <input type="checkbox" id="schoolEveningGel">
                <label for="schoolEveningGel">Εσπερινό ΓΕΛ</label>
              </div>
            </div>
          </div>
          <div class="school-type-group">
            <div class="school-type-group__title">Πρότυπα Εκκλησιαστικά Σχολεία</div>
            <div class="school-type-options">
              <div class="checkrow">
                <input type="checkbox" id="schoolEcclesiasticalGym">
                <label for="schoolEcclesiasticalGym">Πρότυπο Εκκλησιαστικό Γυμνάσιο</label>
              </div>
              <div class="checkrow">
                <input type="checkbox" id="schoolEcclesiasticalLykeio">
                <label for="schoolEcclesiasticalLykeio">Πρότυπο Εκκλησιαστικό Λύκειο</label>
              </div>
            </div>
          </div>
          <div class="school-type-group">
            <div class="school-type-group__title">Επαγγελματική Εκπαίδευση</div>
            <div class="school-type-options">
              <div class="checkrow">
                <input type="checkbox" id="schoolEpal">
                <label for="schoolEpal">ΕΠΑ.Λ.</label>
              </div>
              <div class="checkrow">
                <input type="checkbox" id="schoolEveningEpal">
                <label for="schoolEveningEpal">Εσπερινό ΕΠΑ.Λ.</label>
              </div>
              <div class="checkrow">
                <input type="checkbox" id="schoolPepal">
                <label for="schoolPepal">Πρότυπο ΕΠΑ.Λ. (Π.ΕΠΑ.Λ.)</label>
              </div>
            </div>
          </div>
          <div class="school-type-group">
            <div class="school-type-group__title">Ειδική Αγωγή και Εκπαίδευση</div>
            <div class="school-type-options">
              <div class="checkrow">
                <input type="checkbox" id="schoolEaeGym">
                <label for="schoolEaeGym">Γυμνάσιο Ε.Α.Ε.</label>
              </div>
              <div class="checkrow">
                <input type="checkbox" id="schoolEaeLykeio">
                <label for="schoolEaeLykeio">Λύκειο Ε.Α.Ε.</label>
              </div>
              <div class="checkrow">
                <input type="checkbox" id="schoolEneegylGym">
                <label for="schoolEneegylGym">Γυμνάσιο ΕΝ.Ε.Ε.ΓΥ.-Λ.</label>
              </div>
              <div class="checkrow">
                <input type="checkbox" id="schoolEneegylLykeio">
                <label for="schoolEneegylLykeio">Λύκειο ΕΝ.Ε.Ε.ΓΥ.-Λ.</label>
              </div>
              <div class="checkrow">
                <input type="checkbox" id="schoolEeeek">
                <label for="schoolEeeek">Ε.Ε.Ε.ΕΚ.</label>
              </div>
            </div>
          </div>
          <div class="school-type-group">
            <div class="school-type-group__title">Καλλιτεχνικά Σχολεία</div>
            <div class="school-type-options">
              <div class="checkrow">
                <input type="checkbox" id="schoolKallitexnikoGym">
                <label for="schoolKallitexnikoGym">Καλλιτεχνικό Γυμνάσιο</label>
              </div>
              <div class="checkrow">
                <input type="checkbox" id="schoolKallitexnikoLykeio">
                <label for="schoolKallitexnikoLykeio">Καλλιτεχνικό Λύκειο</label>
              </div>
            </div>
          </div>
          <div class="school-type-group">
            <div class="school-type-group__title">Μουσικά Σχολεία</div>
            <div class="school-type-options">
              <div class="checkrow">
                <input type="checkbox" id="schoolMousikoGym">
                <label for="schoolMousikoGym">Μουσικό Γυμνάσιο</label>
              </div>
              <div class="checkrow">
                <input type="checkbox" id="schoolMousikoLykeio">
                <label for="schoolMousikoLykeio">Γενικό Μουσικό Λύκειο</label>
              </div>
            </div>
          </div>
          <p class="help"><strong>ΕΠΑ.Λ.:</strong> περιλαμβάνονται Γενική Παιδεία, μαθήματα Προσανατολισμού/Επιλογής, οι 9 τομείς της Β΄ και οι 35 ειδικότητες της Γ΄, σύμφωνα με τις ισχύουσες αναθέσεις.</p>
          <p class="help"><strong>Π.ΕΠΑ.Λ.:</strong> περιλαμβάνονται Γενική Παιδεία, διαθεματικές ενότητες της Α΄, οι 9 τομείς της Β΄ και οι ειδικότητες της Γ΄.</p>
          <p class="help"><strong>Καλλιτεχνικά Σχολεία:</strong> οι ειδικοί πίνακες Κίνηση–Χορός, Κινηματογράφος, Κλασικός Χορός και Σύγχρονος Χορός εμφανίζονται ως ξεχωριστές επιλογές ειδικότητας.</p>
          <p class="help"><strong>Μουσικά Σχολεία:</strong> όπου η ανάθεση εξαρτάται από μουσική ειδίκευση, εμφανίζονται τα αντίστοιχα πεδία. Μαθήματα του ισχύοντος ωρολογίου χωρίς τεκμηριωμένη αντιστοίχιση στον πίνακα αναθέσεων δεν εμφανίζουν αυθαίρετη ανάθεση.</p>
          <p class="help"><strong>ΕΝ.Ε.Ε.ΓΥ.-Λ.:</strong> περιλαμβάνονται Γυμνάσιο και Λύκειο Α΄–Δ΄, οι 8 κοινοί τομείς Β΄–Γ΄ και οι 33 ειδικότητες της Δ΄, με τις ειδικές προϋποθέσεις και τις κατά προτεραιότητα αναθέσεις.</p>
        </div>

        <div class="field hidden" id="musicSpecializationWrap">
          <label for="musicSpecialization">Μουσική ειδίκευση</label>
          <select id="musicSpecialization">
            <option value="">— Χωρίς συγκεκριμένη μουσική ειδίκευση —</option>
            <option value="piano">Πιάνο</option>
            <option value="tambouras">Ταμπουράς / παραδοσιακό όργανο αναφοράς</option>
            <option value="european_theory">Θεωρητικά Ευρωπαϊκής Μουσικής</option>
            <option value="music_technology">Μουσική Τεχνολογία</option>
            <option value="music_production">Μουσική Παραγωγή</option>
            <option value="other_instrument">Άλλο μουσικό όργανο</option>
          </select>
          <p class="help">Χρησιμοποιείται μόνο όπου η ανάθεση εξαρτάται από συγκεκριμένη μουσική ειδίκευση. Για Πιάνο, Ταμπουρά/όργανο αναφοράς και Ατομικό Όργανο Επιλογής συνδυάζεται και με το επόμενο πεδίο. Στα τεχνολογικά μαθήματα της Α΄ η ΠΕ79.02 εξαιρείται από τον περιορισμό ειδίκευσης.</p>
        </div>

        <div class="field hidden" id="musicSpecializationRelationWrap">
          <label for="musicSpecializationRelation">Σχέση με τη μουσική ειδίκευση</label>
          <select id="musicSpecializationRelation">
            <option value="">— Επίλεξε —</option>
            <option value="primary">Κύρια ειδίκευση / τοποθέτηση στην αντίστοιχη ειδίκευση</option>
            <option value="additional">Πρόσθετη ειδίκευση / τοποθέτηση σε διαφορετική ειδίκευση</option>
          </select>
          <p class="help">Η επιλογή καθορίζει αν τα συγκεκριμένα οργανικά μαθήματα εμφανίζονται ως Α΄ ή Β΄ ανάθεση.</p>
        </div>

        <div class="field" id="gradeWrap">
          <label for="gradeFilter">Τάξη</label>
          <select id="gradeFilter">
            <option value="all">Όλες οι τάξεις</option>
            <option value="Α΄">Α΄ τάξη</option>
            <option value="Β΄">Β΄ τάξη</option>
            <option value="Γ΄">Γ΄ τάξη</option>
            <option value="Δ΄">Δ΄ τάξη</option>
          </select>
        </div>

        <div class="note">
          Στα <strong>Εργαστήρια Δεξιοτήτων</strong> η Β΄ ανάθεση μπορεί να αφορά «όλες τις άλλες ειδικότητες», σύμφωνα με τον αντίστοιχο πίνακα αναθέσεων. Η γενική αυτή ένδειξη αφορά κανονικούς κλάδους/ειδικότητες και <strong>όχι τους ειδικούς πίνακες ωρομίσθιου προσωπικού Καλλιτεχνικών Σχολείων</strong>.
        </div>
      <?php calculatorCardEnd(); ?>

      <?php calculatorCardStart(array('id' => 'fullAssignmentsCard')); ?>
        <h2>Αναλυτικές αναθέσεις</h2>
        <p class="cap" id="fullResultsStatus">Επίλεξε κλάδο / ειδικότητα για να εμφανιστεί η πλήρης λίστα μαθημάτων.</p>
        <div id="assignmentResults" aria-live="polite"></div>
      <?php calculatorCardEnd(); ?>

      <?php calculatorDisclosureStart(array(
        'summary' => 'Τι σημαίνουν οι αναθέσεις;',
        'open' => true,
        'attrs' => array('data-mobile-collapsed' => 'true')
      )); ?>
        <p><strong>Α΄ ανάθεση:</strong> μαθήματα της βασικής ειδικότητας με τα οποία καλύπτεται κατά προτεραιότητα το υποχρεωτικό ωράριο.</p>
        <p><strong>Β΄ ανάθεση:</strong> χρησιμοποιείται για συμπλήρωση του υποχρεωτικού ωραρίου ή για κάλυψη εκπαιδευτικών αναγκών. Ισχύουν οι προβλεπόμενοι περιορισμοί ωρών και δεύτερης ειδικότητας.</p>
        <p><strong>Γ΄ ανάθεση:</strong> αφορά τη βασική ειδικότητα και μπορεί να χρησιμοποιηθεί <strong>μετά τις 30 Σεπτεμβρίου</strong>, με απόφαση ΠΥΣΔΕ, για κενά που παραμένουν ακάλυπτα.</p>
      <?php calculatorDisclosureEnd(); ?>
    <?php calculatorMainEnd(); ?>

    <?php calculatorResultsStart(array('attrs' => array('aria-live' => 'polite'))); ?>
      <?php calculatorScoreHeader(array(
        'variant' => 'capped',
        'class' => 'total',
        'value_id' => 'resultCount',
        'value_html' => '—',
        'value_class' => 'num',
        'cap_html' => 'μαθήματα / αναθέσεις',
        'cap_class' => 'outof'
      )); ?>

      <?php calculatorResultRow(array('label_html' => 'Α΄ ανάθεση', 'value_html' => '0', 'value_id' => 'countA')); ?>
      <?php calculatorResultRow(array('label_html' => 'Β΄ ανάθεση', 'value_html' => '0', 'value_id' => 'countB')); ?>
      <?php calculatorResultRow(array('label_html' => 'Γ΄ ανάθεση', 'value_html' => '0', 'value_id' => 'countC')); ?>
      <?php calculatorResultRow(array('label_html' => 'Ειδικές προβλέψεις', 'value_html' => '0', 'value_id' => 'countSpecial')); ?>

      <?php calculatorResultMessage(array('variant' => 'status', 'id' => 'statusMessage', 'text' => 'Επίλεξε ειδικότητα για συνοπτικά αποτελέσματα.')); ?>
    <?php calculatorResultsEnd(); ?>
  <?php calculatorColumnsEnd(); ?>
</main>

<template id="teachingAssignmentsData"><?php echo htmlspecialchars(json_encode(teachingAssignmentsData(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), ENT_NOQUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?></template>
<script src="<?php echo htmlspecialchars(edu_asset_url('includes/teaching-assignments-ui.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>

<?php sourceCardStart(); ?>
  <p><strong>Γυμνάσιο / Εσπερινό Γυμνάσιο / ΓΕΛ / Εσπερινό ΓΕΛ:</strong> Υ.Α. 54058/Δ2/05-05-2026, ΦΕΚ Β΄ 2583/07-05-2026, όπως τροποποιήθηκε με την Υ.Α. 112867/Δ2/31-08-2026, ΦΕΚ Β΄ 5555/11-09-2026. Η τροποποίηση ισχύει από το σχολικό έτος 2026-2027 και αλλάζει δύο σημεία: <strong>ΤΕ16 σε Α΄ ανάθεση στη Μουσική του «Πολιτισμός και Δραστηριότητες» στο Γυμνάσιο</strong> και <strong>ΠΕ33 σε Α΄ ανάθεση στην Ιστορία Γενικής Παιδείας της Γ΄ ΓΕΛ</strong>. Η απόφαση έχει ενιαίο πίνακα αναθέσεων για Γυμνάσιο και ΓΕΛ. Στα εσπερινά εμφανίζονται μόνο τα μαθήματα που περιλαμβάνονται στο ισχύον ωρολόγιο του αντίστοιχου σχολείου: Υ.Α. 43751/Δ2/2026, ΦΕΚ Β΄ 2106/09-04-2026 για το Εσπερινό Γυμνάσιο και Υ.Α. 43706/Δ2/2026, ΦΕΚ Β΄ 2102/09-04-2026 για το Εσπερινό ΓΕΛ.</p>
  <p><strong>Πρότυπα Εκκλησιαστικά Σχολεία:</strong> για τα μαθήματα του κοινού προγράμματος εφαρμόζονται οι ισχύουσες διατάξεις αναθέσεων Γυμνασίου/ΓΕΛ (ΦΕΚ Β΄ 2583/2026, όπως τροποποιήθηκε με το ΦΕΚ Β΄ 5555/2026), περιορισμένες στο ειδικό ωρολόγιο των Π.Ε.Σ. (118380/Θ2/2021, ΦΕΚ Β΄ 4438/2021, όπως ισχύει). Η Βυζαντινή Μουσική ανατίθεται σε ΠΕ79.01 και, ελλείψει αυτών, ΤΕ16, πάντοτε με Δίπλωμα Βυζαντινής Μουσικής. Η Εικονογραφία εμφανίζεται μόνο ως ειδική πρόβλεψη ΠΕ01/ΠΕ08 και απαιτεί τις ειδικές προϋποθέσεις της 71346/Θ2/2020 (Β΄ 2466), όπως τροποποιήθηκε με την 4404/Θ2/2023 (Β΄ 253). Τα έξι θεολογικά μαθήματα εξειδίκευσης εμφανίζονται στον ΠΕ01 ως ειδικές προβλέψεις, όχι ως βαθμίδες Α΄/Β΄/Γ΄ του γενικού πίνακα.</p>
  <p><strong>Ε.Α.Ε.:</strong> Υ.Α. 72559/Δ3, ΦΕΚ Β΄ 3275/11-06-2026. <strong>ΕΝ.Ε.Ε.ΓΥ.-Λ.:</strong> Υ.Α. 69785/Δ3/29-05-2026, ΦΕΚ Β΄ 3216/05-06-2026. Οι αποφάσεις αφορούν το σχολικό έτος 2026-2027 και περιλαμβάνουν και το μάθημα <strong>Ηθική</strong>.</p>
  <p><strong>ΕΠΑ.Λ. / Εσπερινά ΕΠΑ.Λ.:</strong> Υ.Α. Φ22/75401/Δ4/10-05-2018, ΦΕΚ Β΄ 1664/15-05-2018, όπως τροποποιήθηκε και ισχύει με τα ΦΕΚ Β΄ 2637/2018, 3520/2018, 2779/2019, 453/2020, 3609/2020, 418/2023, 5206/2023, 1975/2025 και 2625/2026. Για τη Γ΄ τάξη του τριετούς Εσπερινού ΕΠΑ.Λ. οι αναθέσεις περιορίζονται στα μαθήματα του ωρολογίου του ΦΕΚ Β΄ 2636/2018, όπως τροποποιήθηκε με το ΦΕΚ Β΄ 4373/2018, διορθώθηκε με το ΦΕΚ Β΄ 4815/2018 και συμπληρώνεται για τα Ναυτιλιακά από το ΦΕΚ Β΄ 3224/2018. Για την ειδικότητα «Υπάλληλος Τουριστικών Επιχειρήσεων» η επιλογή Γαλλικών/Γερμανικών/Ισπανικών/Ιταλικών προβλέπεται στο ημερήσιο από το ΦΕΚ Β΄ 2122/2018 και διατηρείται στο τριετές εσπερινό από το ΦΕΚ Β΄ 2636/2018.</p>
  <p><strong>Π.ΕΠΑ.Λ.:</strong> Α΄ τάξη: Υ.Α. Φ9/116550/Δ4/17-09-2021, ΦΕΚ Β΄ 4367/22-09-2021, όπως τροποποιήθηκε με τα ΦΕΚ Β΄ 5188/2023, 7403/2023, 1832/2025 και 2687/2026. Στα έξι μαθήματα Επαγγελματικής Κατεύθυνσης Προσανατολιστικού Χαρακτήρα οι αναθέσεις γίνονται με βάση τις επιμέρους ενότητες, τη συνάφεια με το βασικό πτυχίο και τα εξειδικευμένα προσόντα· το ΦΕΚ Β΄ 7403/2023 ορίζει ότι η ανάθεση γίνεται από τον Σύλλογο Διδασκόντων του Π.ΕΠΑ.Λ. ύστερα από εισήγηση του/της Διευθυντή/τριας. Β΄ τάξη: Υ.Α. Φ9/114791/Δ4/21-09-2022, ΦΕΚ Β΄ 4983/26-09-2022, όπως τροποποιήθηκε με τα ΦΕΚ Β΄ 418/2023, 5206/2023 και 2624/2026. Γ΄ τάξη: Υ.Α. Φ9/101003/Δ4/13-09-2023, ΦΕΚ Β΄ 5510/18-09-2023. Οι τίτλοι των μαθημάτων της Γ΄ τάξης έχουν διασταυρωθεί και με το αντίστοιχο ωρολόγιο πρόγραμμα, Υ.Α. Φ9/93929/Δ4, ΦΕΚ Β΄ 5251/30-08-2023.</p>
  <p><strong>Καλλιτεχνικά Σχολεία:</strong> για τα μαθήματα καλλιτεχνικής παιδείας εφαρμόζεται η Υ.Α. 65409/Δ2/12-06-2024, ΦΕΚ Β΄ 3418/13-06-2024, η οποία τροποποιεί την Υ.Α. 148262/Δ2/10-09-2018 (ΦΕΚ Β΄ 4077). Για τα μαθήματα γενικής παιδείας εφαρμόζεται το ΦΕΚ Β΄ 2583/2026, όπως τροποποιήθηκε με το ΦΕΚ Β΄ 5555/2026. Τα εμφανιζόμενα μαθήματα περιορίζονται στο ισχύον ωρολόγιο 43820/Δ2/07-04-2026, ΦΕΚ Β΄ 2104/09-04-2026.</p>
  <p><strong>Μουσικά Σχολεία:</strong> για τα μαθήματα μουσικής παιδείας εφαρμόζεται η Υ.Α. 144236/Δ2/05-09-2018, ΦΕΚ Β΄ 4202/25-09-2018, και για τα μαθήματα γενικής παιδείας το ΦΕΚ Β΄ 2583/2026, όπως τροποποιήθηκε με το ΦΕΚ Β΄ 5555/2026, σε αντιπαραβολή με το ισχύον ωρολόγιο πρόγραμμα 43787/Δ2/07-04-2026, ΦΕΚ Β΄ 2107/09-04-2026. Όπου η ανάθεση εξαρτάται από μουσική ειδίκευση εφαρμόζονται οι ειδικοί κανόνες του πίνακα αναθέσεων. Για μαθήματα του ωρολογίου χωρίς ρητή ή ασφαλώς ισοδύναμη αντιστοίχιση στον πίνακα του 2018 δεν εμφανίζεται ανάθεση.</p>
  <p><strong>Ε.Ε.Ε.ΕΚ.:</strong> οι αναθέσεις βασίζονται στην Υ.Α. 71105/Δ3/04-05-2018 (ΦΕΚ Β΄ 1761/17-05-2018). Τα μαθήματα γενικής παιδείας εμφανίζονται χωριστά από τις 42 ονομασίες εργαστηρίων του επίσημου πίνακα. Η Α΄/Β΄ ανάθεση και οι ειδικές προτεραιότητες παλαιών κλάδων διατηρούνται όπως δημοσιεύθηκαν.</p>
  <?php sourceCardLinksStart(); ?>
    <?php foreach (legalSourceLinksForKeys(teachingAssignmentsLegalSourceKeysForSchools(array('gymnasio', 'esperino_gymnasio', 'gel', 'esperino_gel'))) as $legalLink): ?>
      <?php sourceCardLink($legalLink['url'], $legalLink['label']); ?>
    <?php endforeach; ?>
    <?php foreach (teachingAssignmentsGeneralTimetableCrosscheckLinks() as $legalLink): ?>
      <?php sourceCardLink($legalLink['url'], $legalLink['label']); ?>
    <?php endforeach; ?>
    <?php $referenceLink = referenceSourceLinkByKey('pes_staff_official_page'); if ($referenceLink) sourceCardLink($referenceLink['url'], $referenceLink['label']); ?>
    <?php $pesTimetableLegalSource = legalSourceByKey('ecclesiastical_timetable_2021'); ?>
    <?php if ($pesTimetableLegalSource): sourceCardLink(legalSourceUrl($pesTimetableLegalSource, 'legal_text'), 'ΦΕΚ Β΄ 4438/2021 — Ωρολόγιο Εκκλησιαστικών Σχολείων ↗'); endif; ?>
    <?php $referenceLink = referenceSourceLinkByKey('eae_assignments_official_index'); if ($referenceLink) sourceCardLink($referenceLink['url'], $referenceLink['label']); ?>
    <?php foreach (legalSourceLinksForKeys(teachingAssignmentsLegalSourceKeysForSchools(array('eae_gymnasio', 'eae_lykeio', 'eneegyl_gymnasio', 'eneegyl_lykeio'))) as $legalLink): ?>
      <?php sourceCardLink($legalLink['url'], $legalLink['label']); ?>
    <?php endforeach; ?>
    <?php $referenceLink = referenceSourceLinkByKey('epal_assignments_official_framework'); if ($referenceLink) sourceCardLink($referenceLink['url'], $referenceLink['label']); ?>
    <?php $referenceLink = referenceSourceLinkByKey('pepal_assignments_official_framework_2025'); if ($referenceLink) sourceCardLink($referenceLink['url'], $referenceLink['label']); ?>
    <?php foreach (teachingAssignmentsVocationalOverviewLinks() as $legalLink): ?>
      <?php sourceCardLink($legalLink['url'], $legalLink['label']); ?>
    <?php endforeach; ?>
    <?php $referenceLink = referenceSourceLinkByKey('art_schools_official_page'); if ($referenceLink) sourceCardLink($referenceLink['url'], $referenceLink['label']); ?>
    <?php foreach (teachingAssignmentsSpecialSchoolOverviewLinks() as $legalLink): ?>
      <?php sourceCardLink($legalLink['url'], $legalLink['label']); ?>
    <?php endforeach; ?>
  <?php sourceCardLinksEnd(); ?>
<?php sourceCardEnd(); ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
<script src="<?php echo htmlspecialchars(edu_asset_url('assets/common.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
</body>
</html>
