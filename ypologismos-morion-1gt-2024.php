<!DOCTYPE html>
<html lang="el">
<head>
<!-- UI consolidation v3.20: shared design system in assets/common.css -->
<meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="Υπολογισμός μορίων για την προκήρυξη ΑΣΕΠ 1ΓΤ/2024 για τους κλάδους ΤΕ01, ΤΕ02 και ΤΕ16.">
  <title>Υπολογισμός μορίων 1ΓΤ/2024</title>
<link rel="stylesheet" href="assets/common.css?v=3.20.15-rc1">
</head>
<body class="edu-ui edu-calc-standard edu-page-gt1">
<?php require_once __DIR__ . '/includes/header.php'; ?>
<?php require_once __DIR__ . '/includes/components/asep-te-academic.php'; ?>
<?php require_once __DIR__ . '/includes/components/asep-social-criteria.php'; ?>
<?php require_once __DIR__ . '/includes/components/asep-three-month-service.php'; ?>
<div class="app">
<section class="hero">
    <h1>Υπολογισμός μορίων 1ΓΤ/2024</h1>
    <p>Ενδεικτικός υπολογισμός για τους αξιολογικούς πίνακες Γενικής Εκπαίδευσης κατηγορίας Τ.Ε.</p>
    <div class="meta">
      <span>ΤΕ01</span><span>ΤΕ02</span><span>ΤΕ16</span>
      <span>Ακαδημαϊκά έως 120</span><span>Προϋπηρεσία έως 120</span>
    </div>
  </section>

  <div class="layout">
    <div>
      <section class="card">
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
      </section>

      <?php
renderAsepTeAcademic(array(
    'part' => 'qualifications',
    'id' => 'asepTeAcademic',
    'training_context' => '1gt-2024-300h-7m'
));
?>

      <section id="asepService class="card" data-component="asep-service-criteria" data-subtotal-id="serviceSubtotal" data-subtotal-with-cap="true">
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
    <p><strong>Πηγή:</strong> Προκήρυξη ΑΣΕΠ 1ΓΤ/2024, ΦΕΚ Α.Σ.Ε.Π. 25/10.07.2024, Κεφάλαιο Γ΄ «Κριτήρια Κατάταξης».</p>
    <div class="source-links"><a href="https://info.asep.gr/node/73068" target="_blank" rel="noopener noreferrer">1ΓΤ/2024 — ΑΣΕΠ ↗</a></div>
    <p class="source-disclaimer">Το εργαλείο είναι ενημερωτικό. Η τελική μοριοδότηση προκύπτει από τον έλεγχο της αίτησης και των δικαιολογητικών από τα αρμόδια όργανα.</p>
  </section>

  <div class="credits">Υλοποίηση / επεξεργασία: Μάριος Μαγιολαδίτης</div>
</div>

<script src="includes/service-calculations.js?v=3.20.31"></script>
<script src="includes/asep-service-controller.js?v=3.20.26"></script>
<script src="includes/social-calculations.js?v=3.20.32"></script>
<script src="includes/asep-social-criteria.js?v=3.20.32"></script>
<script src="includes/language-calculations.js?v=3.20.31"></script>
<script src="includes/asep-language-selector.js?v=3.20.31"></script>
<script src="includes/te-academic-calculations.js?v=3.20.31"></script>
<script src="includes/training-proof.js?v=3.20.18"></script>
<script src="includes/asep-computer-proof.js?v=3.20.27"></script>
<script src="includes/asep-te-academic.js?v=3.20.31"></script>
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
    `Κοινωνικά: ${fmt(v.social.points)}`,`Ξένη γλώσσα: ${languageSummary(v)}`,`Παιδαγωγική επάρκεια: ${v.ped?'ΝΑΙ — ΠΡΟΤΑΞΗ':'ΟΧΙ / ΔΕΝ ΔΗΛΩΘΗΚΕ'}`,
    AsepTeAcademic.trainingSummary('asepTeAcademic'),'','Ενδεικτικός υπολογισμός βάσει της Προκήρυξης ΑΣΕΠ 1ΓΤ/2024.'
  ].filter((x,i,a)=>x!==''||a[i-1]!=='').join('\n');}

  ['regularMonths','difficultMonths','threeMonthRegular2020','threeMonthDifficult2020','threeMonthRegular2021','threeMonthDifficult2021'].forEach(id=>{
    const el=$(id);el.addEventListener('input',()=>{if(el.value==='')return;let value=Math.max(0,Math.floor(Number(el.value)||0));const max=el.getAttribute('max');if(max!==null&&max!=='')value=Math.min(value,Number(max));el.value=value;});
  });
  document.addEventListener('input',calc);document.addEventListener('change',calc);
  $('resetBtn').addEventListener('click',()=>{
    document.querySelectorAll('input[type="number"]').forEach(el=>el.value='0');$('degreeGrade').value='';
    document.querySelectorAll('input[type="text"]').forEach(el=>el.value='');document.querySelectorAll('input[type="checkbox"],input[type="radio"]').forEach(el=>el.checked=false);
    $('branch').value='te01';AsepTeAcademic.reset('asepTeAcademic',{silent:true});calc();
  });
  $('copyBtn').addEventListener('click',async()=>{const text=summary(calc());try{await navigator.clipboard.writeText(text);const old=$('copyBtn').textContent;$('copyBtn').textContent='Αντιγράφηκε';setTimeout(()=>$('copyBtn').textContent=old,1400);}catch(e){alert(text);}});
  AsepTeAcademic.sync('asepTeAcademic');calc();
})();
</script>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
  <script src="assets/common.js?v=3.20.13"></script>
</body>
</html>
