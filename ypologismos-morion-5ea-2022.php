<?php require_once __DIR__ . '/includes/config.php'; ?>
<!DOCTYPE html>
<html lang="el">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="Υπολογισμός μορίων για την ιστορική προκήρυξη ΑΣΕΠ 5ΕΑ/2022 για εκπαιδευτικούς Ειδικής Αγωγής κατηγορίας ΔΕ, κλάδων ΔΕ01 και ΔΕ02.">
<title>Υπολογισμός μορίων 5ΕΑ/2022</title>
<link rel="stylesheet" href="<?php echo htmlspecialchars(edu_asset_url('assets/common.css'), ENT_QUOTES, 'UTF-8'); ?>">
</head>
<body class="edu-ui edu-calc-standard edu-page-ea5">
<?php require_once __DIR__ . '/includes/header.php'; ?>
<?php require_once __DIR__ . '/includes/components/calculator-layout.php'; ?>
<?php require_once __DIR__ . '/includes/teacher-specialties.php'; ?>
<?php require_once __DIR__ . '/includes/components/asep-de-academic.php'; ?>
<?php require_once __DIR__ . '/includes/components/asep-social-criteria.php'; ?>
<?php require_once __DIR__ . '/includes/components/asep-three-month-service.php'; ?>
<?php require_once __DIR__ . '/includes/components/eae-sensory-priority.php'; ?>
<?php require_once __DIR__ . '/includes/components/asep-pedagogical-proof.php'; ?>

<div class="app">
<?php calculatorHero(array(
  'title' => 'Υπολογισμός μορίων 5ΕΑ/2022',
  'intro' => 'Ιστορικός υπολογιστής για τους εκπαιδευτικούς Δευτεροβάθμιας Ειδικής Αγωγής κατηγορίας Δ.Ε. των κλάδων ΔΕ01 και ΔΕ02.',
  'badges' => array('5ΕΑ/2022', 'ΔΕ01 / ΔΕ02', '7 ειδικότητες', '123 αιτήσεις', 'Κύριος / Επικουρικός Πίνακας', 'Ακαδημαϊκά έως 120', 'Προϋπηρεσία έως 120')
)); ?>

<div class="info-note edu-mb-16">
  <strong>Ιστορική προκήρυξη:</strong> η 5ΕΑ/2022 (ΦΕΚ Α.Σ.Ε.Π. 19/18.04.2022) είχε μόλις <strong>123 αιτήσεις</strong>. Μέχρι 24/08/2026 δεν εντοπίζεται αντίστοιχη 5ΕΑ/2025 στην επίσημη σειρά του ΑΣΕΠ· το εργαλείο διατηρείται για πληρότητα και έλεγχο των ιστορικών πινάκων.
</div>

<?php calculatorColumnsStart(); ?>
<?php calculatorMainStart(); ?>

<?php calculatorCardStart(); ?>
  <h2>1. Κλάδος και βασικό τυπικό προσόν</h2>
  <p class="cap">Και οι 7 ειδικότητες της 5ΕΑ/2022 απαιτούσαν τίτλο αντίστοιχης ειδικότητας και τριετή επαγγελματική πείρα μετά την κτήση του τίτλου.</p>

  <div class="field">
    <label for="specialty">Κλάδος / ειδικότητα</label>
    <select id="specialty">
      <option value="">— Επιλογή ειδικότητας —</option>
      <?php
      $deSpecialtyGroups = array(
          'ΔΕ01' => array('ΔΕ01.05', 'ΔΕ01.13', 'ΔΕ01.14', 'ΔΕ01.15', 'ΔΕ01.17'),
          'ΔΕ02' => array('ΔΕ02.01', 'ΔΕ02.02')
      );
      foreach ($deSpecialtyGroups as $groupLabel => $codes) {
          echo '<optgroup label="' . htmlspecialchars($groupLabel, ENT_QUOTES, 'UTF-8') . '">';
          foreach ($codes as $code) {
              echo '<option value="' . htmlspecialchars($code, ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars(teacherSpecialtyDisplay($code), ENT_QUOTES, 'UTF-8') . '</option>';
          }
          echo '</optgroup>';
      }
      ?>
    </select>
  </div>

  <div class="checkrow">
    <input type="checkbox" id="requiredThreeYearExperience">
    <label for="requiredThreeYearExperience">Διαθέτω την υποχρεωτική τριετή επαγγελματική πείρα μετά την κτήση του βασικού τίτλου
      <small><strong>Τυπικό προσόν διορισμού — δεν μοριοδοτείται.</strong> Η ίδια τριετία δεν πρέπει να δηλωθεί στο πεδίο πρόσθετης εργασιακής εμπειρίας.</small>
    </label>
  </div>
  <div id="basicEligibilityStatus" class="priority">Δεν έχει επιβεβαιωθεί ακόμη το βασικό τυπικό προσόν.</div>
<?php calculatorCardEnd(); ?>

<?php calculatorCardStart(); ?>
  <h2>2. Ένταξη σε πίνακα Ε.Α.Ε.</h2>
  <p class="cap">Ενδεικτικός έλεγχος των ειδικών κριτηρίων ένταξης στον Αξιολογικό Πίνακα Β΄ ή στον Επικουρικό Πίνακα.</p>

  <div id="eaeEligibility" data-eae-profile="de" data-specialty-id="specialty" data-social-id="socialCriteria">
    <div class="field">
      <label for="mainCriterion">Κριτήριο ένταξης στον Αξιολογικό Πίνακα Β΄ (Κύριος)</label>
      <select id="mainCriterion" data-eae-main-select>
        <option value="none">Δεν διαθέτω κάποιο από τα παρακάτω</option>
        <option value="phd">Διδακτορικό στην Ε.Α.Ε. ή Σχολική Ψυχολογία, με βασικές σπουδές σε Α.Ε.Ι.</option>
        <option value="msc">Μεταπτυχιακό στην Ε.Α.Ε. ή Σχολική Ψυχολογία, με βασικές σπουδές σε Α.Ε.Ι.</option>
        <option value="retraining">Πτυχίο διετούς μετεκπαίδευσης στην Ε.Α.Ε. Διδασκαλείου, με βασικές σπουδές σε Α.Ε.Ι.</option>
        <option value="aei5years">Πτυχίο Α.Ε.Ι. και τουλάχιστον 5 έτη αποδεδειγμένης προϋπηρεσίας στην Ε.Α.Ε.</option>
      </select>
    </div>

    <h3>Κριτήρια Επικουρικού Πίνακα</h3>
    <div class="note">Αρκεί <strong>ένα</strong>: σεμινάριο Ε.Α.Ε. ≥400 ωρών / ≥7 μηνών, τουλάχιστον 10 μήνες προϋπηρεσίας στην Ε.Α.Ε. ή ιδιότητα γονέα παιδιού με αναπηρία ≥67%.</div>

    <div class="checkrow">
      <input type="checkbox" id="seminar400" data-eae-aux="seminar400">
      <label for="seminar400">Σεμινάριο εξειδίκευσης στην Ε.Α.Ε. ≥400 ωρών και διάρκειας ≥7 μηνών
        <small>Α.Ε.Ι. ή άλλος εποπτευόμενος φορέας του δημόσιου τομέα.</small>
      </label>
    </div>

    <div class="field">
      <label for="eaeMonths">Μήνες προϋπηρεσίας στην Ε.Α.Ε.
        <small>Για το κριτήριο Επικουρικού απαιτούνται τουλάχιστον 10 μήνες.</small>
      </label>
      <input type="number" id="eaeMonths" data-eae-aux="months" min="0" max="480" step="1" value="0">
    </div>

    <div class="info-note">Το κριτήριο γονέα παιδιού με αναπηρία ≥67% ελέγχεται από το αντίστοιχο ποσοστό στα Κοινωνικά Κριτήρια.</div>
  </div>

  <?php renderEaeSensoryPriority(array(
      'context' => '5ea-2022',
      'eng_enabled' => true,
      'braille_enabled' => true
  )); ?>

  <div class="priority" id="tableStatus">Δεν έχει δηλωθεί ακόμη κριτήριο ένταξης.</div>
  <div class="info-note" id="eligibilityWhy">Συμπλήρωσε ειδικότητα και προσόντα για τον έλεγχο ένταξης.</div>
<?php calculatorCardEnd(); ?>

<?php renderAsepDeAcademic(array(
    'id' => 'asepDeAcademic',
    'extra_training_ids' => array('seminar400')
)); ?>

<section id="asepService" class="card" data-component="asep-service-criteria" data-subtotal-id="serviceSubtotal" data-subtotal-with-cap="true" data-warning-id="serviceWarning" data-warn-months="true">
  <h2>Β. Εκπαιδευτική προϋπηρεσία</h2>
  <p class="cap">Μέγιστο κατηγορίας: 120 μόρια</p>
  <div class="note">Μην δηλώνεις τον ίδιο μήνα σε περισσότερα από ένα πεδία. Η εκπαιδευτική προϋπηρεσία είναι διαφορετικό κριτήριο από την επαγγελματική εμπειρία ειδικότητας των Ακαδημαϊκών.</div>

  <div class="field">
    <label for="regularMonths">Μήνες δημόσιας εκπαιδευτικής προϋπηρεσίας
      <small>1 μόριο ανά μήνα · έως 120 μήνες.</small>
    </label>
    <input type="number" id="regularMonths" data-service-role="regular" min="0" max="120" step="1" value="0">
  </div>

  <div class="field">
    <label for="difficultMonths">Δυσπρόσιτα / σχολικές μονάδες σε καταστήματα κράτησης
      <small>Από το σχολικό έτος 2020–2021 και μετά · 2 μόρια ανά μήνα · έως 60 μήνες.</small>
    </label>
    <input type="number" id="difficultMonths" data-service-role="difficult" min="0" max="60" step="1" value="0">
  </div>

  <?php renderAsepThreeMonthService(array(
      'regular_2020_id' => 'threeMonthRegular2020',
      'difficult_2020_id' => 'threeMonthDifficult2020',
      'regular_2021_id' => 'threeMonthRegular2021',
      'difficult_2021_id' => 'threeMonthDifficult2021'
  )); ?>

  <div id="serviceWarning" class="note hidden"></div>
  <?php calculatorSubtotalRow(array('label_html' => 'Σύνολο Προϋπηρεσίας', 'value_id' => 'serviceSubtotal', 'value_html' => '0,00 / 120')); ?>
</section>

<?php renderAsepSocialCriteria(array(
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
    'child_auxiliary_note' => 'Από 67% και άνω θεμελιώνει και κριτήριο ένταξης στον Επικουρικό Πίνακα.',
    'warning_id' => 'socialWarning',
    'subtotal_id' => 'socialSubtotal',
    'subtotal_label' => 'Σύνολο Κοινωνικών'
)); ?>

<?php calculatorCardStart(); ?>
  <h2>Παιδαγωγική και Διδακτική Επάρκεια</h2>
  <?php renderAsepPedagogicalProof(array(
      'context' => '5ea-2022',
      'input_id' => 'pedagogical'
  )); ?>
<?php calculatorCardEnd(); ?>

<?php calculatorMainEnd(); ?>

<?php calculatorResultsStart(); ?>
  <?php calculatorScoreHeader(array('value_id' => 'grandTotal', 'value_html' => '0,00', 'label' => 'συνολικά μόρια')); ?>
  <?php calculatorResultRow(array('label_html' => 'Ακαδημαϊκά', 'value_html' => '0,00 / 120', 'value_id' => 'resAcademic')); ?>
  <?php calculatorResultRow(array('label_html' => 'Βαθμός τίτλου', 'value_html' => '0,00 / 50', 'value_id' => 'resDegree')); ?>
  <?php calculatorResultRow(array('label_html' => 'Πρόσθετη επαγγελματική εμπειρία', 'value_html' => '0,00 / 20', 'value_id' => 'resWork')); ?>
  <?php calculatorResultRow(array('label_html' => 'Ξένη γλώσσα', 'value_html' => '0,00 / 20', 'value_id' => 'resLanguage')); ?>
  <?php calculatorResultRow(array('label_html' => 'Προϋπηρεσία', 'value_html' => '0,00 / 120', 'value_id' => 'resService')); ?>
  <?php calculatorResultRow(array('label_html' => 'Κοινωνικά', 'value_html' => '0,00', 'value_id' => 'resSocial')); ?>
  <?php calculatorResultRow(array('label_html' => 'Βασικό τυπικό προσόν', 'value_html' => '—', 'value_id' => 'resBasic')); ?>
  <?php calculatorResultRow(array('label_html' => 'Πίνακας Ε.Α.Ε.', 'value_html' => '—', 'value_id' => 'resTable')); ?>

  <?php calculatorResultMessage(array('id' => 'priorityBox', 'variant' => 'status', 'text' => 'Χωρίς δηλωμένη ειδική πρόταξη / προτεραιότητα')); ?>
  <?php calculatorActions(array(
      array('attrs' => array('type' => 'button', 'class' => 'secondary', 'id' => 'copyBtn'), 'html' => 'Αντιγραφή'),
      array('attrs' => array('type' => 'button', 'class' => 'secondary', 'id' => 'resetBtn'), 'html' => 'Μηδενισμός')
  )); ?>
<?php calculatorResultsEnd(); ?>
<?php calculatorColumnsEnd(); ?>

<?php sourceCardStart(); ?>
  <p><strong>Βάση υπολογισμού:</strong> Προκήρυξη ΑΣΕΠ <strong>5ΕΑ/2022</strong>, ΦΕΚ Α.Σ.Ε.Π. <strong>19/18.04.2022</strong>, ιδίως Κεφάλαια Β΄–Δ΄ και Παραρτήματα Β΄, Γ΄, Ε΄.</p>
  <?php sourceCardLinksStart(); ?>
    <?php sourceCardLink('https://info.asep.gr/node/62770', 'Επίσημη σελίδα 5ΕΑ/2022 — ΑΣΕΠ ↗'); ?>
    <?php sourceCardLink('https://info.asep.gr/sites/default/files/_migration/Enterprise%20Libraries/asep/Competitions/5%CE%95%CE%91_2022/comp5%CE%95%CE%91_2022.pdf', 'ΦΕΚ 19/18.04.2022 — πλήρης προκήρυξη ↗'); ?>
    <?php sourceCardLink('https://info.asep.gr/node/64113', 'Στατιστικά αιτήσεων 1ΕΑ–5ΕΑ/2022 ↗'); ?>
  <?php sourceCardLinksEnd(); ?>
  <?php sourceCardDisclaimerStart(); ?>Το εργαλείο είναι ενημερωτικό και ιστορικό. Δεν υποκαθιστά τον επίσημο έλεγχο των πινάκων, της αίτησης, του ΟΠΣΥΔ ή των δικαιολογητικών από το ΑΣΕΠ και τα αρμόδια όργανα.<?php sourceCardDisclaimerEnd(); ?>
<?php sourceCardEnd(); ?>

<div class="credits">Εργαλείο υπολογισμού μορίων · 5ΕΑ/2022 · ΔΕ Ειδικής Αγωγής</div>
</div>

<script src="<?php echo htmlspecialchars(edu_asset_url('includes/service-calculations.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
<script src="<?php echo htmlspecialchars(edu_asset_url('includes/asep-service-controller.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
<script src="<?php echo htmlspecialchars(edu_asset_url('includes/social-calculations.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
<script src="<?php echo htmlspecialchars(edu_asset_url('includes/asep-social-criteria.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
<script src="<?php echo htmlspecialchars(edu_asset_url('includes/eae-table-eligibility.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
<script src="<?php echo htmlspecialchars(edu_asset_url('includes/asep-eae-eligibility.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
<script src="<?php echo htmlspecialchars(edu_asset_url('includes/language-calculations.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
<script src="<?php echo htmlspecialchars(edu_asset_url('includes/asep-language-selector.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
<script src="<?php echo htmlspecialchars(edu_asset_url('includes/de-academic-calculations.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
<script src="<?php echo htmlspecialchars(edu_asset_url('includes/training-proof.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
<script src="<?php echo htmlspecialchars(edu_asset_url('includes/asep-computer-proof.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
<script src="<?php echo htmlspecialchars(edu_asset_url('includes/asep-de-academic.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
<script src="<?php echo htmlspecialchars(edu_asset_url('includes/asep-pedagogical-proof.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
<script src="<?php echo htmlspecialchars(edu_asset_url('includes/eae-sensory-proof.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
<script>
(function(){
  'use strict';
  const $ = id => document.getElementById(id);
  const fmt = v => (Math.round((Number(v)+Number.EPSILON)*100)/100).toLocaleString('el-GR',{minimumFractionDigits:2,maximumFractionDigits:2});

  function specialtyLabel(){
    const option=$('specialty').selectedOptions[0];
    return option&&option.value?option.textContent.trim():'ειδικότητα μη επιλεγμένη';
  }

  function basicEligible(){return !!$('specialty').value && $('requiredThreeYearExperience').checked;}
  function service(){return AsepServiceController.getState('asepService',fmt);}
  function social(){return AsepSocialCriteria.getState('socialCriteria',fmt);}

  function sanitizeLocalInteger(el){
    if(!el || el.value==='') return;
    if(el.id!=='eaeMonths' && el.id!=='children') return;
    let value=Math.max(0,Math.floor(Number(el.value)||0));
    const max=el.getAttribute('max');
    if(max!==null&&max!=='') value=Math.min(value,Number(max));
    el.value=String(value);
  }

  function calc(){
    TrainingProof.syncAll();
    AsepPedagogicalProof.syncAll();
    EaeSensoryProof.syncAll();
    const academic=AsepDeAcademic.getState('asepDeAcademic',fmt);
    const serviceResult=service();
    const socialResult=social();
    const eligibility=AsepEaeEligibility.getState('eaeEligibility',{socialResult:socialResult});
    const basic=basicEligible();
    const total=academic.result.points+serviceResult.points+socialResult.points;

    $('grandTotal').textContent=fmt(total);
    $('resAcademic').textContent=fmt(academic.result.points)+' / 120';
    $('resDegree').textContent=fmt(academic.result.degreePoints)+' / 50';
    $('resWork').textContent=fmt(academic.result.workExperiencePoints)+' / 20';
    $('resLanguage').textContent=fmt(academic.languages.points)+' / 20';
    $('resService').textContent=fmt(serviceResult.points)+' / 120';
    $('resSocial').textContent=fmt(socialResult.points);
    $('resBasic').textContent=basic?'Επιβεβαιώθηκε':'Δεν επιβεβαιώθηκε';
    $('resTable').textContent=eligibility.label;

    $('basicEligibilityStatus').classList.toggle('yes',basic);
    $('basicEligibilityStatus').textContent=basic
      ?'✓ Δηλώθηκε ειδικότητα και επιβεβαιώθηκε η υποχρεωτική τριετής επαγγελματική πείρα.'
      :'⚠ Για συμμετοχή απαιτούνται επιλεγμένη ειδικότητα και υποχρεωτική τριετής επαγγελματική πείρα μετά την κτήση του τίτλου.';

    $('tableStatus').classList.toggle('yes',eligibility.code==='main'||eligibility.code==='aux');
    $('tableStatus').textContent=eligibility.label;
    $('eligibilityWhy').textContent=eligibility.why;

    const priorities=[];
    if($('pedagogical').checked) priorities.push('ΠΡΟΤΑΞΗ λόγω Παιδαγωγικής και Διδακτικής Επάρκειας');
    priorities.push.apply(priorities,EaeSensoryProof.priorityLabels());
    $('priorityBox').className='result-message edu-message '+(priorities.length?'result-message--success edu-message--success':'result-message--status edu-message--status');
    $('priorityBox').textContent=priorities.length?priorities.join(' · '):'Χωρίς δηλωμένη ειδική πρόταξη / προτεραιότητα';

    return {academic,serviceResult,socialResult,eligibility,basic,total,priorities};
  }

  function summary(v){
    return [
      'Υπολογισμός μορίων 5ΕΑ/2022 — '+specialtyLabel(),
      'Σύνολο: '+fmt(v.total),
      'Ακαδημαϊκά: '+fmt(v.academic.result.points)+' / 120',
      'Βαθμός τίτλου: '+fmt(v.academic.result.degreePoints)+' / 50',
      'Πρόσθετη επαγγελματική εμπειρία: '+fmt(v.academic.result.workExperiencePoints)+' / 20',
      'Ξένη γλώσσα: '+fmt(v.academic.languages.points)+' / 20',
      'Προϋπηρεσία: '+fmt(v.serviceResult.points)+' / 120',
      'Κοινωνικά: '+fmt(v.socialResult.points),
      'Βασικό τυπικό προσόν (υποχρεωτική 3ετία): '+(v.basic?'ΝΑΙ':'ΟΧΙ / ΔΕΝ ΕΠΙΒΕΒΑΙΩΘΗΚΕ'),
      'Πίνακας Ε.Α.Ε.: '+v.eligibility.label,
      v.eligibility.why,
      AsepPedagogicalProof.summary('pedagogical'),
      EaeSensoryProof.summary(),
      AsepDeAcademic.trainingSummary('asepDeAcademic')
    ].filter(Boolean).join('\n');
  }

  document.addEventListener('input',e=>{sanitizeLocalInteger(e.target);calc();});
  document.addEventListener('change',e=>{sanitizeLocalInteger(e.target);calc();});

  $('copyBtn').addEventListener('click',async()=>{
    const text=summary(calc());
    try{
      await navigator.clipboard.writeText(text);
      const old=$('copyBtn').textContent;
      $('copyBtn').textContent='Αντιγράφηκε ✓';
      setTimeout(()=>$('copyBtn').textContent=old,1400);
    }catch(e){alert(text);}
  });

  $('resetBtn').addEventListener('click',()=>{
    document.querySelectorAll('input[type="number"]').forEach(el=>el.value='0');
    $('degreeGrade').value='';
    document.querySelectorAll('input[type="text"]').forEach(el=>el.value='');
    document.querySelectorAll('input[type="checkbox"]').forEach(el=>el.checked=false);
    document.querySelectorAll('input[type="radio"]').forEach(el=>el.checked=false);
    $('specialty').value='';
    $('mainCriterion').value='none';
    AsepServiceController.reset('asepService',{silent:true});
    AsepDeAcademic.reset('asepDeAcademic',{silent:true});
    AsepEaeEligibility.reset('eaeEligibility',{silent:true});
    AsepPedagogicalProof.reset('pedagogical');
    EaeSensoryProof.reset();
    calc();
  });

  calc();
})();
</script>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
<script src="<?php echo htmlspecialchars(edu_asset_url('assets/common.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
</body>
</html>
