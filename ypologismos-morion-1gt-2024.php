<?php require_once __DIR__ . '/includes/config.php'; ?>
<!DOCTYPE html>
<html lang="el">
<head>
<!-- UI consolidation v3.20: shared design system in assets/common.css -->
<meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="Υπολογισμός μορίων για την προκήρυξη ΑΣΕΠ 1ΓΤ/2024 για τους κλάδους ΤΕ01, ΤΕ02 και ΤΕ16.">
  <title>Υπολογισμός μορίων 1ΓΤ/2024</title>
<link rel="stylesheet" href="<?php echo htmlspecialchars(edu_asset_url('assets/common.css'), ENT_QUOTES, 'UTF-8'); ?>">
</head>
<body class="edu-ui edu-calc-standard edu-page-gt1">
<?php require_once __DIR__ . '/includes/header.php'; ?>
<?php require_once __DIR__ . '/includes/components/calculator-layout.php'; ?>
<?php require_once __DIR__ . '/includes/components/deadline-card.php'; ?>
<?php require_once __DIR__ . '/includes/components/asep-te-academic.php'; ?>
<?php require_once __DIR__ . '/includes/components/asep-social-criteria.php'; ?>
<?php require_once __DIR__ . '/includes/components/asep-three-month-service.php'; ?>
<?php require_once __DIR__ . '/includes/components/asep-pedagogical-proof.php'; ?>
<div class="app">
<?php calculatorHeroStart(); ?>
    <h1>Υπολογισμός μορίων 1ΓΤ/2024</h1>
    <p>Ενδεικτικός υπολογισμός για τους αξιολογικούς πίνακες Γενικής Εκπαίδευσης κατηγορίας Τ.Ε.</p>
    <div class="meta">
      <span>ΤΕ01</span><span>ΤΕ02</span><span>ΤΕ16</span>
      <span>Ακαδημαϊκά έως 120</span><span>Προϋπηρεσία έως 120</span>
    </div>
  <?php calculatorHeroEnd(); ?>

  <?php
renderDeadlineCard(array(
    'title' => '📅 Δήλωση περιοχών αναπληρωτών 2026–2027',
    'intro' => 'Η φετινή πρόσκληση του ΥΠΑΙΘΑ για πρόσληψη αναπληρωτών/ωρομισθίων είναι σε εξέλιξη.',
    'items' => array(array(
        'title' => '1ΓΤ/2024 — ΤΕ01, ΤΕ02, ΤΕ16',
        'meta_html' => 'Δήλωση περιοχών στο <strong>ΟΠΣΥΔ</strong> από <strong>Παρασκευή 14</strong> έως και <strong>Δευτέρα 24 Αυγούστου 2026</strong>.',
        'start' => '2026-08-14T00:00:00+03:00',
        'end_exclusive' => '2026-08-25T00:00:00+03:00',
        'source_url' => 'https://diavgeia.gov.gr/doc/9%CE%96%CE%A5%CE%A146%CE%9D%CE%9A%CE%A0%CE%94-%CE%93%CE%A8%CE%A9?inline=true',
        'source_label' => 'Επίσημη πρόσκληση — ΑΔΑ 9ΖΥΡ46ΝΚΠΔ-ΓΨΩ ↗'
    )),
    'note_html' => '<strong>Σημείωση ώρας:</strong> η επίσημη πρόσκληση αναφέρει την καταληκτική ημερομηνία 24/08/2026 χωρίς συγκεκριμένη ώρα. Το countdown χρησιμοποιεί τεχνικά το τέλος της ημέρας σε ώρα Ελλάδας· υπερισχύει πάντοτε η επίσημη πρόσκληση και το ΟΠΣΥΔ.'
));
?>

  <?php calculatorColumnsStart(); ?>
    <?php calculatorMainStart(); ?>
      <?php calculatorCardStart(); ?>
        <h2>Κλάδος και βασικός τίτλος</h2>
        <p class="cap">Ο κλάδος επηρεάζει κυρίως τον τρόπο περιγραφής του δεύτερου τίτλου και τη βοήθεια για τον βαθμό ΤΕ16.</p>

        <div class="field-grid">
          <div class="field">
            <label for="branch">Κλάδος</label>
            <select id="branch">
              <option value="te01">ΤΕ01</option>
              <option value="te02">ΤΕ02</option>
              <option value="te16">ΤΕ16 — Μουσικής μη Ανώτατων Ιδρυμάτων</option>
            </select>
          </div>
          <?php renderAsepTeAcademic(array('part' => 'grade-scale')); ?>
        </div>

        <?php renderAsepTeAcademic(array(
            'part' => 'degree-details',
            'id' => 'asepTeAcademic',
            'branch_id' => 'branch'
        )); ?>
      <?php calculatorCardEnd(); ?>

      <?php
renderAsepTeAcademic(array(
    'part' => 'qualifications',
    'id' => 'asepTeAcademic',
    'training_context' => '1gt-2024-300h-7m'
));
?>

      <section id="asepService" class="card" data-component="asep-service-criteria" data-subtotal-id="serviceSubtotal" data-subtotal-with-cap="true">
        <h2>Β. Εκπαιδευτική προϋπηρεσία</h2>
        <p class="cap">Μέγιστο κατηγορίας: 120 μόρια</p>

        <div class="note">
          Βάλε τους μήνες σε <strong>ένα μόνο</strong> από τα αντίστοιχα πεδία. Μήνας που δηλώνεται ως δυσπρόσιτος ή ως τρίμηνη σύμβαση δεν πρέπει να ξαναμπεί στους απλούς μήνες, ώστε να μη γίνει διπλή μέτρηση.
        </div>

        <div class="note">
          <strong>Σημείωση 1ΓΤ/2024:</strong> Λαμβάνεται υπόψη η εκπαιδευτική προϋπηρεσία σε μήνες χωρίς να υπολογίζονται τα υπόλοιπα ημερών. Για τον λόγο αυτό, όλα τα πεδία προϋπηρεσίας δέχονται μόνο ακέραιους μήνες.
        </div>

        <div class="field">
          <label for="regularMonths">Λοιπή αναγνωρισμένη εκπαιδευτική προϋπηρεσία
            <small>1 μόριο ανά μήνα · έως 120 μήνες. Εδώ μπορεί να συμπεριληφθεί και αναγνωρισμένη ιδιωτική προϋπηρεσία που πληροί τις προϋποθέσεις της προκήρυξης.</small>
          </label>
          <input type="number" id="regularMonths" data-service-role="regular" min="0" max="120" step="1" value="0">
        </div>

        <div class="field">
          <label for="difficultMonths">Δυσπρόσιτα / σχολικές μονάδες σε καταστήματα κράτησης
            <small>Από το σχολικό έτος 2020–2021 και μετά · 2 μόρια ανά μήνα · έως 60 μήνες.</small>
          </label>
          <input type="number" id="difficultMonths" data-service-role="difficult" min="0" max="60" step="1" value="0">
        </div>

<?php
renderAsepThreeMonthService(array(
    'regular_2020_id' => 'threeMonthRegular2020',
    'difficult_2020_id' => 'threeMonthDifficult2020',
    'regular_2021_id' => 'threeMonthRegular2021',
    'difficult_2021_id' => 'threeMonthDifficult2021'
));
?>

        <div class="subtot"><span>Σύνολο Προϋπηρεσίας</span><span class="pill" id="serviceSubtotal">0,00 / 120</span></div>
      </section>

<?php
renderAsepSocialCriteria(array(
    'title' => 'Γ. Κοινωνικά κριτήρια',
    'children_id' => 'children',
    'candidate_id' => 'candidateDisability',
    'spouse_id' => 'spouseDisability',
    'child_id' => 'childDisability',
    'marriage_id' => 'marriageYears4Plus',
    'mental_id' => 'candidateMentalCondition',
    'input_step' => '0.01',
    'child_points' => 3,
    'min_disability_percent' => 50,
    'disability_rate' => '0,4',
    'spouse_min_marriage_years' => 4,
    'child_extra_note' => 'Ανεξαρτήτως ηλικίας.',
    'child_auxiliary_note' => '',
    'warning_id' => 'socialWarning',
    'subtotal_id' => 'socialSubtotal',
    'subtotal_label' => 'Σύνολο Κοινωνικών'
));
?>

      <?php calculatorCardStart(); ?>
        <h2>Παιδαγωγική και Διδακτική Επάρκεια</h2>
<?php renderAsepPedagogicalProof(array(
    'context' => '1gt-2024',
    'input_id' => 'pedagogical'
)); ?>
      <?php calculatorCardEnd(); ?>
    <?php calculatorMainEnd(); ?>

    <?php calculatorResultsStart(); ?>
      <div class="total">
        <div class="num" id="grandTotal">0,00</div>
        <div class="label">συνολικά μόρια</div>
      </div>

      <?php calculatorResultRow(array('label_html' => 'Ακαδημαϊκά', 'value_html' => '0,00 / 120', 'value_id' => 'resAcademic')); ?>
      <?php calculatorResultRow(array('label_html' => 'Προϋπηρεσία', 'value_html' => '0,00 / 120', 'value_id' => 'resService')); ?>
      <?php calculatorResultRow(array('label_html' => 'Κοινωνικά', 'value_html' => '0,00', 'value_id' => 'resSocial')); ?>
      <?php calculatorResultRow(array('label_html' => 'Βαθμός τίτλου', 'value_html' => '0,00', 'value_id' => 'resDegree')); ?>
      <?php calculatorResultRow(array('label_html' => 'Ξένη γλώσσα', 'value_html' => '0,00', 'value_id' => 'resLanguage')); ?>
      <?php calculatorResultRow(array('label_html' => 'Τέκνα', 'value_html' => '0,00', 'value_id' => 'resChildren')); ?>
      <?php calculatorResultRow(array('label_html' => 'Αναπηρία', 'value_html' => '0,00', 'value_id' => 'resDisability')); ?>

      <div class="priority" id="priorityBox">Χωρίς δηλωμένη πρόταξη Π.Δ.Ε.</div>

      <?php calculatorActions(array(array('attrs' => array('type' => 'button', 'id' => 'copyBtn'), 'html' => 'Αντιγραφή αποτελέσματος'), array('attrs' => array('type' => 'button', 'class' => 'secondary', 'id' => 'resetBtn'), 'html' => 'Μηδενισμός'))); ?>

      <div class="info-note edu-mt-14">
        Σε ισοβαθμία προηγούνται κατά σειρά: περισσότερα κοινωνικά μόρια (και ειδικότερα αναπηρία), έπειτα περισσότερα ακαδημαϊκά / μεγαλύτερος βαθμός πτυχίου και τέλος περισσότερη προϋπηρεσία.
      </div>
    <?php calculatorResultsEnd(); ?>
  <?php calculatorColumnsEnd(); ?>

  <?php sourceCardStart(); ?>
    <p><strong>Πηγή:</strong> Προκήρυξη ΑΣΕΠ 1ΓΤ/2024, ΦΕΚ Α.Σ.Ε.Π. 25/10.07.2024, Κεφάλαιο Γ΄ «Κριτήρια Κατάταξης».</p>
    <?php sourceCardLinksStart(); ?><?php sourceCardLink('https://info.asep.gr/node/73068', '1ΓΤ/2024 — ΑΣΕΠ ↗'); ?><?php sourceCardLinksEnd(); ?>
    <?php sourceCardDisclaimerStart(); ?>Το εργαλείο είναι ενημερωτικό. Η τελική μοριοδότηση προκύπτει από τον έλεγχο της αίτησης και των δικαιολογητικών από τα αρμόδια όργανα.<?php sourceCardDisclaimerEnd(); ?>
  <?php sourceCardEnd(); ?>

  <div class="credits">Υλοποίηση / επεξεργασία: Μάριος Μαγιολαδίτης</div>
</div>

<script src="<?php echo htmlspecialchars(edu_asset_url('includes/service-calculations.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
<script src="<?php echo htmlspecialchars(edu_asset_url('includes/asep-service-controller.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
<script src="<?php echo htmlspecialchars(edu_asset_url('includes/social-calculations.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
<script src="<?php echo htmlspecialchars(edu_asset_url('includes/asep-social-criteria.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
<script src="<?php echo htmlspecialchars(edu_asset_url('includes/language-calculations.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
<script src="<?php echo htmlspecialchars(edu_asset_url('includes/asep-language-selector.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
<script src="<?php echo htmlspecialchars(edu_asset_url('includes/te-academic-calculations.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
<script src="<?php echo htmlspecialchars(edu_asset_url('includes/training-proof.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
<script src="<?php echo htmlspecialchars(edu_asset_url('includes/asep-computer-proof.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
<script src="<?php echo htmlspecialchars(edu_asset_url('includes/asep-te-academic.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
<script src="<?php echo htmlspecialchars(edu_asset_url('includes/asep-pedagogical-proof.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
<script>
(function(){
  "use strict";
  const $=id=>document.getElementById(id);
  const num=id=>Math.max(0,Number($(id)?.value||0));
  const intNum=id=>Math.max(0,Math.floor(Number($(id)?.value||0)));
  const fmt=v=>(Math.round((Number(v)+Number.EPSILON)*100)/100).toLocaleString('el-GR',{minimumFractionDigits:2,maximumFractionDigits:2});


  function socialResult(){return AsepSocialCriteria.getState('socialCriteria',fmt);}
  function serviceResult(){return AsepServiceController.getState('asepService',fmt);}

  function calc(){
    const academic=AsepTeAcademic.getState('asepTeAcademic',fmt);
    const academicResult=academic.result, languages=academic.languages;
    const service=serviceResult(),social=socialResult();
    const total=academicResult.points+service.points+social.points;
    $('grandTotal').textContent=fmt(total);$('resAcademic').textContent=`${fmt(academicResult.points)} / 120`;$('resService').textContent=`${fmt(service.points)} / 120`;$('resSocial').textContent=fmt(social.points);
    $('resDegree').textContent=fmt(academicResult.degreePoints);$('resLanguage').textContent=fmt(languages.points);$('resChildren').textContent=fmt(social.childrenPoints);$('resDisability').textContent=fmt(social.disabilityPoints);


    const ped=$('pedagogical').checked;$('priorityBox').classList.toggle('yes',ped);$('priorityBox').textContent=ped?'ΠΡΟΤΑΞΗ λόγω Παιδαγωγικής & Διδακτικής Επάρκειας':'Χωρίς δηλωμένη πρόταξη Π.Δ.Ε.';
    return{academic:academicResult,service,social,languages,total,ped};
  }

  function languageSummary(v){const item=v.languages.accepted[0];return item?`${item.label} — ${fmt(v.languages.points)} μόρια`:'δεν δηλώθηκε';}
  function summary(v){return[
    'Υπολογισμός μορίων 1ΓΤ/2024',`Σύνολο: ${fmt(v.total)}`,`Ακαδημαϊκά: ${fmt(v.academic.points)} / 120`,`Προϋπηρεσία: ${fmt(v.service.points)} / 120`,
    `Κοινωνικά: ${fmt(v.social.points)}`,`Ξένη γλώσσα: ${languageSummary(v)}`,AsepPedagogicalProof.summary('pedagogical'),
    AsepTeAcademic.trainingSummary('asepTeAcademic'),'','Ενδεικτικός υπολογισμός βάσει της Προκήρυξης ΑΣΕΠ 1ΓΤ/2024.'
  ].filter((x,i,a)=>x!==''||a[i-1]!=='').join('\n');}

  document.addEventListener('input',calc);document.addEventListener('change',calc);
  $('resetBtn').addEventListener('click',()=>{
    document.querySelectorAll('input[type="number"]').forEach(el=>el.value='0');$('degreeGrade').value='';
    document.querySelectorAll('input[type="text"]').forEach(el=>el.value='');document.querySelectorAll('input[type="checkbox"],input[type="radio"]').forEach(el=>el.checked=false);
    $('branch').value='te01';AsepServiceController.reset('asepService',{silent:true});AsepTeAcademic.reset('asepTeAcademic',{silent:true});AsepPedagogicalProof.reset('pedagogical');calc();
  });
  $('copyBtn').addEventListener('click',async()=>{const text=summary(calc());try{await navigator.clipboard.writeText(text);const old=$('copyBtn').textContent;$('copyBtn').textContent='Αντιγράφηκε';setTimeout(()=>$('copyBtn').textContent=old,1400);}catch(e){alert(text);}});
  AsepTeAcademic.sync('asepTeAcademic');calc();
})();
</script>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
  <script src="<?php echo htmlspecialchars(edu_asset_url('assets/common.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
</body>
</html>
