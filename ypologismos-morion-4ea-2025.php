<!DOCTYPE html>
<html lang="el">
<head>
<!-- UI consolidation v3.20: shared design system in assets/common.css -->
<meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="Υπολογισμός μορίων για την προκήρυξη ΑΣΕΠ 4ΕΑ/2025 για εκπαιδευτικούς Ειδικής Αγωγής κατηγορίας ΤΕ (ΤΕ01, ΤΕ02, ΤΕ16).">
  <title>Υπολογισμός μορίων 4ΕΑ/2025</title>
<link rel="stylesheet" href="assets/common.css?v=3.20.17">
</head>
<body class="edu-ui edu-calc-standard edu-page-ea4">
<?php require_once __DIR__ . '/includes/header.php'; ?>
<?php require_once __DIR__ . '/includes/components/asep-te-academic.php'; ?>
<?php require_once __DIR__ . '/includes/components/asep-social-criteria.php'; ?>
<?php require_once __DIR__ . '/includes/components/asep-three-month-service.php'; ?>
<?php require_once __DIR__ . '/includes/components/eae-sensory-priority.php'; ?>
<div class="app">
<section class="hero">
    <h1>Υπολογισμός μορίων 4ΕΑ/2025</h1>
    <p>Ενδεικτικός υπολογισμός μορίων και ελέγχου ένταξης στους πίνακες Ειδικής Αγωγής και Εκπαίδευσης κατηγορίας Τ.Ε.</p>
    <div class="meta">
      <span>4ΕΑ/2025</span><span>ΤΕ01</span><span>ΤΕ02</span><span>ΤΕ16</span>
      <span>Κύριος / Επικουρικός Πίνακας</span><span>Ακαδημαϊκά έως 120</span><span>Προϋπηρεσία έως 120</span>
    </div>
  </section>

  <div class="layout">
    <div>
      <section class="card">
        <h2>Κλάδος και βασικός τίτλος</h2>
        <p class="cap">Η 4ΕΑ/2025 αφορά τους κλάδους ΤΕ01, ΤΕ02 και ΤΕ16 με εξειδίκευση στην Ειδική Αγωγή και Εκπαίδευση.</p>

        <div class="field-grid">
          <div class="field">
            <label for="branch">Κλάδος</label>
            <select id="branch">
              <option value="">— Επιλογή κλάδου / ειδικότητας —</option>
              <optgroup label="ΤΕ01">
                <option value="ΤΕ01.04">ΤΕ01.04 — Ψυκτικοί</option>
                <option value="ΤΕ01.06">ΤΕ01.06 — Ηλεκτρολόγοι</option>
                <option value="ΤΕ01.07">ΤΕ01.07 — Ηλεκτρονικοί</option>
                <option value="ΤΕ01.13">ΤΕ01.13 — Προγραμματιστές Η/Υ</option>
                <option value="ΤΕ01.19">ΤΕ01.19 — Κομμωτικής</option>
                <option value="ΤΕ01.20">ΤΕ01.20 — Αισθητικής</option>
                <option value="ΤΕ01.25">ΤΕ01.25 — Αργυροχρυσοχοΐας</option>
                <option value="ΤΕ01.26">ΤΕ01.26 — Οδοντοτεχνικής</option>
                <option value="ΤΕ01.29">ΤΕ01.29 — Βοηθών Ιατρικών &amp; Βιολογικών Εργαστηρίων</option>
                <option value="ΤΕ01.30">ΤΕ01.30 — Βοηθοί Βρεφοκόμων – Παιδοκόμων</option>
                <option value="ΤΕ01.31">ΤΕ01.31 — Χειριστές Ιατρικών Συσκευών (Βοηθοί Ακτινολόγοι)</option>
              </optgroup>
              <optgroup label="ΤΕ02">
                <option value="ΤΕ02.01">ΤΕ02.01 — Σχεδιαστές – Δομικοί</option>
                <option value="ΤΕ02.02">ΤΕ02.02 — Μηχανολόγοι</option>
                <option value="ΤΕ02.03">ΤΕ02.03 — Χημικοί Εργαστηρίων</option>
                <option value="ΤΕ02.04">ΤΕ02.04 — Οικονομίας – Διοίκησης</option>
                <option value="ΤΕ02.05">ΤΕ02.05 — Εφαρμοσμένων Τεχνών</option>
                <option value="ΤΕ02.06">ΤΕ02.06 — Σχεδιασμού και Παραγωγής Προϊόντων</option>
                <option value="ΤΕ02.07">ΤΕ02.07 — Γεωπονίας</option>
              </optgroup>
              <optgroup label="ΤΕ16">
                <option value="ΤΕ16">ΤΕ16 — Μουσικής μη Ανώτατων Ιδρυμάτων</option>
              </optgroup>
            </select>
          </div>
          <?php renderAsepTeAcademic(array('part' => 'grade-scale')); ?>
        </div>

        <?php renderAsepTeAcademic(array(
            'part' => 'degree-details',
            'id' => 'asepTeAcademic',
            'branch_id' => 'branch',
            'extra_training_ids' => array('auxSeminar400'),
            'degree_placeholder_20' => 'π.χ. 15,40'
        )); ?>
      </section>

      <section class="card">
        <h2>Ένταξη σε πίνακα Ε.Α.Ε.</h2>
        <p class="cap">Ενδεικτικός έλεγχος των ειδικών κριτηρίων ένταξης της 4ΕΑ/2025.</p>

        <div class="field">
          <label for="mainCriterion">Κριτήριο ένταξης στον Αξιολογικό Πίνακα Β΄ (Κύριος)</label>
          <select id="mainCriterion">
            <option value="none">Δεν διαθέτω κάποιο από τα παρακάτω</option>
            <option value="phd">Διδακτορικό στην Ε.Α.Ε. ή Σχολική Ψυχολογία, με βασικές σπουδές σε Α.Ε.Ι.</option>
            <option value="msc">Μεταπτυχιακό στην Ε.Α.Ε. ή Σχολική Ψυχολογία, με βασικές σπουδές σε Α.Ε.Ι.</option>
            <option value="retraining">Πτυχίο διετούς μετεκπαίδευσης στην Ε.Α.Ε. Διδασκαλείου, με βασικές σπουδές σε Α.Ε.Ι.</option>
            <option value="aei5years">Πτυχίο Α.Ε.Ι. και τουλάχιστον 5 έτη αποδεδειγμένης προϋπηρεσίας στην Ε.Α.Ε.</option>
          </select>
          <div class="help">Αρκεί ένα από τα παραπάνω κριτήρια για ένταξη στον Πίνακα Β΄.</div>
        </div>

        <h3>Κριτήρια Επικουρικού Πίνακα</h3>
        <div class="note">Για τον Επικουρικό Πίνακα αρκεί <strong>ένα από τα τρία</strong> παρακάτω κριτήρια.</div>

        <div class="checkrow">
          <input type="checkbox" id="auxSeminar400">
          <label for="auxSeminar400">Σεμινάριο εξειδίκευσης στην Ε.Α.Ε. ≥400 ωρών και διάρκειας ≥7 μηνών
            <small>Α.Ε.Ι. ή εποπτευόμενος φορέας του δημόσιου τομέα.</small>
          </label>
        </div>

        <div class="field">
          <label for="eaeMonths">Μήνες προϋπηρεσίας στην Ε.Α.Ε.
            <small>Για το κριτήριο του Επικουρικού απαιτούνται τουλάχιστον 10 μήνες.</small>
          </label>
          <input type="number" id="eaeMonths" min="0" step="1" value="0">
        </div>

        <div class="info-note">
          Το κριτήριο <strong>εκπαιδευτικού γονέα παιδιού με αναπηρία 67% και άνω</strong> ελέγχεται αυτόματα από το ποσοστό αναπηρίας τέκνου που δηλώνεται στα Κοινωνικά Κριτήρια παρακάτω.
        </div>

        <?php
        renderEaeSensoryPriority(array(
    'context' => '4ea-2025',
            'eng_enabled' => true,
            'braille_enabled' => true,
            'eng_id' => 'signLanguage',
            'braille_id' => 'braille'
        ));
        ?>

        <div class="priority" id="tableStatus">Δεν έχει δηλωθεί ακόμη κριτήριο ένταξης.</div>
        <div class="info-note" id="eligibilityWhy">Συμπλήρωσε κλάδο και προσόντα για αναλυτικό έλεγχο ένταξης.</div>

        <div class="note">
          Η προϋπηρεσία Ε.Α.Ε. που χρησιμοποιείται για την ένταξη στον Επικουρικό Πίνακα δεν προστίθεται αυτόματα στα μόρια.
          Καταχώρισέ την και στο κατάλληλο πεδίο προϋπηρεσίας παρακάτω, χωρίς διπλή μέτρηση.
        </div>
      </section>

      <?php
renderAsepTeAcademic(array(
    'part' => 'qualifications',
    'id' => 'asepTeAcademic',
    'training_context' => '4ea-2025-general-300h-or-eae-400h-7m',
    'extra_training_ids' => array('auxSeminar400'),
    'training_help_suffix' => 'Το σεμινάριο Ε.Α.Ε. ≥400 ωρών του Επικουρικού καλύπτει και αυτό το κριτήριο.'
));
?>

      <section id="asepService class="card" data-component="asep-service-criteria" data-subtotal-id="serviceSubtotal" data-subtotal-with-cap="true">
        <h2>Β. Εκπαιδευτική προϋπηρεσία</h2>
        <p class="cap">Μέγιστο κατηγορίας: 120 μόρια</p>

        <div class="note">
          Βάλε τους μήνες σε <strong>ένα μόνο</strong> από τα αντίστοιχα πεδία. Μήνας που δηλώνεται ως δυσπρόσιτος ή ως τρίμηνη σύμβαση δεν πρέπει να ξαναμπεί στους απλούς μήνες, ώστε να μη γίνει διπλή μέτρηση.
        </div>

        <div class="note">
          <strong>Σημείωση 4ΕΑ/2025:</strong> Λαμβάνεται υπόψη η εκπαιδευτική προϋπηρεσία σε μήνες χωρίς να υπολογίζονται τα υπόλοιπα ημερών. Για τον λόγο αυτό, όλα τα πεδία προϋπηρεσίας δέχονται μόνο ακέραιους μήνες.
        </div>

        <div class="field">
          <label for="regularMonths">Μήνες δημόσιας εκπαιδευτικής προϋπηρεσίας
            <small>1 μόριο ανά μήνα πραγματικής εκπαιδευτικής προϋπηρεσίας · έως 120 μήνες.</small>
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
    'regular_2020_id' => 'covid20Regular',
    'difficult_2020_id' => 'covid20Difficult',
    'regular_2021_id' => 'covid21Regular',
    'difficult_2021_id' => 'covid21Difficult'
));
?>

        <div class="field">
          <label for="privateMonths">Μήνες προϋπηρεσίας στην ιδιωτική εκπαίδευση
            <small>0,9 μόρια ανά μήνα, εφόσον πληρούνται οι ειδικές προϋποθέσεις της προκήρυξης.</small>
          </label>
          <input type="number" id="privateMonths" data-service-role="private" min="0" step="1" value="0">
        </div>

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
    'child_extra_note' => '',
    'child_auxiliary_note' => 'Από 67% και άνω θεμελιώνει και κριτήριο ένταξης στον Επικουρικό Πίνακα.',
    'warning_id' => 'socialWarning',
    'subtotal_id' => 'socialSubtotal',
    'subtotal_label' => 'Σύνολο Κοινωνικών'
));
?>

      <section class="card">
        <h2>Παιδαγωγική και Διδακτική Επάρκεια</h2>
        <div class="checkrow">
          <input type="checkbox" id="pedagogical">
          <label for="pedagogical">Διαθέτω πιστοποιημένη Παιδαγωγική και Διδακτική Επάρκεια ή την προβλεπόμενη βεβαίωση τρίμηνης παιδαγωγικής επιμόρφωσης Α.Σ.ΠΑΙ.Τ.Ε.<small>Δεν προσθέτει μόρια. Δίνει πρόταξη έναντι υποψηφίων που δεν τη διαθέτουν.</small></label>
        </div>
      </section>
    </div>

    <aside class="card results">
      <div class="total">
        <div class="num" id="grandTotal">0,00</div>
        <div class="label">συνολικά μόρια</div>
      </div>

      <div class="result-row"><span>Ακαδημαϊκά</span><strong id="resAcademic">0,00 / 120</strong></div>
      <div class="result-row"><span>Προϋπηρεσία</span><strong id="resService">0,00 / 120</strong></div>
      <div class="result-row"><span>Κοινωνικά</span><strong id="resSocial">0,00</strong></div>
      <div class="result-row"><span>Βαθμός τίτλου</span><strong id="resDegree">0,00</strong></div>
      <div class="result-row"><span>Ξένη γλώσσα</span><strong id="resLanguage">0,00</strong></div>
      <div class="result-row"><span>Τέκνα</span><strong id="resChildren">0,00</strong></div>
      <div class="result-row"><span>Αναπηρία</span><strong id="resDisability">0,00</strong></div>

      <div class="result-row"><span>Πίνακας Ε.Α.Ε.</span><strong id="resTable">—</strong></div>

      <div class="priority" id="priorityBox">Χωρίς δηλωμένη πρόταξη Π.Δ.Ε.</div>

      <div class="actions">
        <button type="button" id="copyBtn">Αντιγραφή αποτελέσματος</button>
        <button type="button" class="secondary" id="resetBtn">Μηδενισμός</button>
      </div>

      <div class="info-note edu-mt-14">
        Σε ισοβαθμία προηγούνται κατά σειρά: περισσότερα κοινωνικά μόρια (και ειδικότερα αναπηρία), έπειτα περισσότερα ακαδημαϊκά / μεγαλύτερος βαθμός πτυχίου και τέλος περισσότερη προϋπηρεσία.
      </div>
    </aside>
  </div>

  <section class="edu-source-card" aria-labelledby="sourcesTitle">
    <h2 id="sourcesTitle">Πηγές / Νομική βάση</h2>
    <p>Προκήρυξη ΑΣΕΠ <strong>4ΕΑ/2025</strong>, <strong>ΦΕΚ Α.Σ.Ε.Π. 42/18.08.2025</strong>, ιδίως Κεφάλαια Β΄, Γ΄ και Δ΄.</p>
    <div class="source-links"><a href="https://info.asep.gr/node/77020" target="_blank" rel="noopener noreferrer">Επίσημη σελίδα 4ΕΑ/2025 στο ΑΣΕΠ ↗</a></div>
    <p class="source-disclaimer">Το εργαλείο είναι ενημερωτικό. Η τελική ένταξη σε πίνακα και η μοριοδότηση προκύπτουν από τον έλεγχο της αίτησης, του ΟΠΣΥΔ και των δικαιολογητικών από τα αρμόδια όργανα.</p>
  </section>
</div>

<script src="includes/service-calculations.js?v=3.20.26"></script>
<script src="includes/asep-service-controller.js?v=3.20.26"></script>
<script src="includes/social-calculations.js?v=3.20.26"></script>
<script src="includes/asep-social-criteria.js?v=3.20.26"></script>
<script src="includes/language-calculations.js?v=3.20.24"></script>
<script src="includes/asep-language-selector.js?v=3.20.24"></script>
<script src="includes/te-academic-calculations.js?v=3.20.27"></script>
<script src="includes/training-proof.js?v=3.20.18"></script>
<script src="includes/asep-computer-proof.js?v=3.20.27"></script>
<script src="includes/asep-te-academic.js?v=3.20.27"></script>
<script>
(function(){
  "use strict";
  const $ = id => document.getElementById(id);
  const num = id => Math.max(0, Number($(id)?.value || 0));
  const intNum = id => Math.max(0, Math.floor(Number($(id)?.value || 0)));
  const fmt = v => (Math.round((Number(v)+Number.EPSILON)*100)/100).toLocaleString('el-GR',{minimumFractionDigits:2,maximumFractionDigits:2});

  function branchFamily(){
    const value = $('branch').value;
    if(value === 'ΤΕ16') return 'ΤΕ16';
    if(value.startsWith('ΤΕ01')) return 'ΤΕ01';
    if(value.startsWith('ΤΕ02')) return 'ΤΕ02';
    return '';
  }

  function selectedBranchLabel(){
    const option = $('branch').selectedOptions[0];
    return option && option.value ? option.textContent.trim() : 'κλάδος/ειδικότητα μη επιλεγμένος/η';
  }


  function socialResult(){return AsepSocialCriteria.getState('socialCriteria',fmt);}

  function serviceResult(){return AsepServiceController.getState('asepService',fmt);}

  function calc(){
    const academic=AsepTeAcademic.getState('asepTeAcademic',fmt);
    const academicResult=academic.result, languages=academic.languages;
    const service=serviceResult();
    const social=socialResult();

    const mainEligible = $('mainCriterion').value !== 'none';
    const auxReasons=[];
    if($('auxSeminar400').checked) auxReasons.push('σεμινάριο Ε.Α.Ε. ≥400 ωρών / ≥7 μηνών');
    if(intNum('eaeMonths') >= 10) auxReasons.push('τουλάχιστον 10 μήνες προϋπηρεσίας στην Ε.Α.Ε.');
    if(social.childDisability67) auxReasons.push('γονέας παιδιού με αναπηρία ≥67%');
    const auxEligible=auxReasons.length>0;

    let tableCode='none', tableLabel='Δεν προκύπτει ένταξη', why='';
    if(!$('branch').value){
      why='Επίλεξε κλάδο/ειδικότητα για να ολοκληρωθεί ο έλεγχος ένταξης.';
    } else if(mainEligible){
      tableCode='main';
      tableLabel='Αξιολογικός Πίνακας Β΄ (Κύριος)';
      why='Δηλώθηκε προσόν που θεμελιώνει ένταξη στον Αξιολογικό Πίνακα Β΄.';
    } else if(auxEligible){
      tableCode='aux';
      tableLabel='Επικουρικός Πίνακας';
      why='Κριτήριο/α Επικουρικού: '+auxReasons.join(' · ')+'.';
    } else {
      why='Δεν έχει δηλωθεί προσόν Κύριου Πίνακα ούτε ένα από τα τρία κριτήρια Επικουρικού.';
    }

    const total = academicResult.points + service.points + social.total;

    $('grandTotal').textContent=fmt(total);
    $('resAcademic').textContent=`${fmt(academicResult.points)} / 120`;
    $('resService').textContent=`${fmt(service.points)} / 120`;
    $('resSocial').textContent=fmt(social.total);
    $('resDegree').textContent=fmt(academicResult.degreePoints);
    $('resLanguage').textContent=fmt(languages.points);
    $('resChildren').textContent=fmt(social.childrenPoints);
    $('resDisability').textContent=fmt(social.disabilityPoints);
    $('resTable').textContent=tableLabel;


    $('tableStatus').classList.toggle('yes',tableCode==='main'||tableCode==='aux');
    $('tableStatus').textContent=tableLabel;
    $('eligibilityWhy').textContent=why;

    const ped=$('pedagogical').checked;
    const priorities=[];
    if(ped) priorities.push('ΠΡΟΤΑΞΗ λόγω Παιδαγωγικής και Διδακτικής Επάρκειας');
    if($('braille').checked) priorities.push('Braille — προτεραιότητα για μαθητές με προβλήματα όρασης');
    if($('signLanguage').checked) priorities.push('Ε.Ν.Γ. — προτεραιότητα για κωφούς/βαρήκοους μαθητές');
    $('priorityBox').classList.toggle('yes',priorities.length>0);
    $('priorityBox').textContent=priorities.length?priorities.join(' · '):'Χωρίς δηλωμένη ειδική πρόταξη / προτεραιότητα';

    return {academicResult,languages,service,social,total,ped,tableCode,tableLabel,why,priorities};
  }

  function summary(v){
    return [
      `Υπολογισμός μορίων 4ΕΑ/2025 — ${selectedBranchLabel()}`,
      `Σύνολο: ${fmt(v.total)}`,
      `Ακαδημαϊκά: ${fmt(v.academicResult.points)} / 120`,
      `Ξένη γλώσσα: ${fmt(v.languages.points)}`,
      `Προϋπηρεσία: ${fmt(v.service.points)} / 120`,
      `Κοινωνικά: ${fmt(v.social.total)}`,
      `Πίνακας Ε.Α.Ε.: ${v.tableLabel}`,
      v.why,
      `Παιδαγωγική επάρκεια: ${v.ped?'ΝΑΙ — ΠΡΟΤΑΞΗ':'ΟΧΙ / ΔΕΝ ΔΗΛΩΘΗΚΕ'}`,
      v.priorities.length?'Προτεραιότητες: '+v.priorities.join(' · '):'',
      AsepTeAcademic.trainingSummary('asepTeAcademic')
    ].filter(Boolean).join('\n');
  }

  function sanitizeIntegerInput(el){
    if(!el) return;
    const ids=['regularMonths','difficultMonths','covid20Regular','covid20Difficult','covid21Regular','covid21Difficult','privateMonths','eaeMonths','children'];
    if(!ids.includes(el.id) || el.value==='') return;
    let value=Math.max(0,Math.floor(Number(el.value)||0));
    const max=el.getAttribute('max');
    if(max!==null && max!=='') value=Math.min(value,Number(max));
    el.value=String(value);
  }

  document.addEventListener('input',e=>{sanitizeIntegerInput(e.target);calc();});
  document.addEventListener('change',e=>{
    sanitizeIntegerInput(e.target);
    calc();
  });


  $('copyBtn').addEventListener('click',async()=>{
    const text=summary(calc());
    try{
      await navigator.clipboard.writeText(text);
      const old=$('copyBtn').textContent;
      $('copyBtn').textContent='Αντιγράφηκε';
      setTimeout(()=>$('copyBtn').textContent=old,1400);
    }catch(e){alert(text);}
  });

  $('resetBtn').addEventListener('click',()=>{
    document.querySelectorAll('input[type="number"]').forEach(el=>el.value='0');
    $('degreeGrade').value='';
    document.querySelectorAll('input[type="text"]').forEach(el=>el.value='');
    document.querySelectorAll('input[type="checkbox"]').forEach(el=>el.checked=false);
    document.querySelectorAll('input[name="trainingDates"]').forEach(el=>el.checked=false);
    $('branch').value='';
    $('mainCriterion').value='none';
    AsepTeAcademic.reset('asepTeAcademic',{silent:true});
    calc();
  });

  AsepTeAcademic.sync('asepTeAcademic');
  calc();
})();
</script>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
  <script src="includes/eae-sensory-proof.js?v=3.20.23"></script>
  <script src="assets/common.js?v=3.20.13"></script>
</body>
</html>
