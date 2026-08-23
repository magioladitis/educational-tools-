<!DOCTYPE html>
<html lang="el">
<head>
<!-- UI consolidation v3.20: page-specific 3EA layout + shared components -->
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="Υπολογισμός μορίων 3ΕΑ/2025 και ενδεικτικός έλεγχος ένταξης στον Αξιολογικό Πίνακα Β΄ ή στον Επικουρικό Πίνακα Ειδικής Αγωγής.">
<title>Υπολογισμός μορίων 3ΕΑ/2025</title>
<link rel="stylesheet" href="assets/common.css?v=3.20.17">
</head>
<body class="edu-ui edu-calc-ea3 edu-page-ea3">
<?php require_once __DIR__ . '/includes/header.php'; ?>
<?php require_once __DIR__ . '/includes/components/training-proof.php'; ?>
<?php require_once __DIR__ . '/includes/components/asep-language-selector.php'; ?>
<?php require_once __DIR__ . '/includes/components/asep-computer-proof.php'; ?>
<?php require_once __DIR__ . '/includes/components/asep-social-criteria.php'; ?>
<?php require_once __DIR__ . '/includes/components/asep-three-month-service.php'; ?>
<?php require_once __DIR__ . '/includes/components/eae-sensory-priority.php'; ?>
<?php require_once __DIR__ . '/includes/components/asep-digital-tutoring-service.php'; ?>
<div class="page">
<section class="hero">
<h1>Υπολογισμός μορίων 3ΕΑ/2025</h1>
<p>Ενδεικτικός υπολογισμός μορίων για εκπαιδευτικούς ΠΕ Ειδικής Αγωγής και ταυτόχρονος έλεγχος ένταξης στον <strong>ΚΥΡΙΟ – Αξιολογικό Πίνακα Β΄</strong> ή στον <strong>ΕΠΙΚΟΥΡΙΚΟ Πίνακα</strong>.</p>
<div class="hero-tags"><span>Ακαδημαϊκά έως 120</span><span>Προϋπηρεσία έως 120</span><span>Κύριος / Επικουρικός</span><span>3ΕΑ/2025</span></div>
</section>

<div class="grid">
<main>
<section class="card">
<h2>1. Κλάδος και ένταξη σε πίνακα</h2>
<p class="cap">Ο κλάδος επηρεάζει ειδικούς κανόνες (ΠΕ61/ΠΕ71, ΠΕ11, ΠΕ86 και κλάδοι ξένων γλωσσών).</p>
<div class="field"><label for="specialty">Κλάδος / ειδικότητα</label><select id="specialty"><option value="">-- Επιλογή --</option>
<option>ΠΕ01</option><option>ΠΕ02</option><option>ΠΕ03</option><option>ΠΕ04.01</option><option>ΠΕ04.02</option><option>ΠΕ04.03</option><option>ΠΕ04.04</option><option>ΠΕ04.05</option><option>ΠΕ05</option><option>ΠΕ06</option><option>ΠΕ07</option><option>ΠΕ08</option><option>ΠΕ11</option><option>ΠΕ33</option><option>ΠΕ34</option><option>ΠΕ40</option><option>ΠΕ41</option><option>ΠΕ60</option><option>ΠΕ61</option><option>ΠΕ70</option><option>ΠΕ71</option><option>ΠΕ78</option><option>ΠΕ79.01</option><option>ΠΕ80</option><option>ΠΕ81</option><option>ΠΕ82</option><option>ΠΕ83</option><option>ΠΕ84</option><option>ΠΕ85</option><option>ΠΕ86</option><option>ΠΕ87</option><option>ΠΕ88</option><option>ΠΕ89</option><option>ΠΕ90</option><option>ΠΕ91</option>
</select></div>
<div class="info">Για ΠΕ61 και ΠΕ71 ο βασικός κλάδος είναι κλάδος Ε.Α.Ε. και οδηγεί στον Αξιολογικό Πίνακα Β΄. Για τους λοιπούς κλάδους απαιτείται προσόν εξειδίκευσης στην Ε.Α.Ε.</div>

<div id="eaeEligibility" data-eae-profile="pe" data-specialty-id="specialty" data-social-id="socialCriteria">
<h3>Προσόντα για ΚΥΡΙΟ – Αξιολογικό Πίνακα Β΄</h3>
<div class="check"><input type="checkbox" id="phdEae" data-eae-main="phd"><label for="phdEae">Διδακτορικό στην Ε.Α.Ε. ή στη Σχολική Ψυχολογία<small>Αποτελεί κριτήριο ένταξης στον κύριο πίνακα και μοριοδοτείται ως διδακτορικό.</small></label></div>
<div class="check"><input type="checkbox" id="masterEae" data-eae-main="msc"><label for="masterEae">Μεταπτυχιακό στην Ε.Α.Ε. ή στη Σχολική Ψυχολογία<small>Αποτελεί κριτήριο ένταξης στον κύριο πίνακα και μοριοδοτείται ως μεταπτυχιακό.</small></label></div>
<div class="check"><input type="checkbox" id="didaskaleio" data-eae-main="retraining"><label for="didaskaleio">Πτυχίο διετούς μετεκπαίδευσης στην Ε.Α.Ε. (Διδασκαλείο)<small>Κριτήριο ένταξης. Η προκήρυξη δεν ορίζει χωριστή πρόσθετη μοριοδότηση γι’ αυτό το πτυχίο στον πίνακα κριτηρίων.</small></label></div>
<div class="check"><input type="checkbox" id="fiveYearEae" data-eae-main="fiveYear"><label for="fiveYearEae">Τουλάχιστον πενταετής αποδεδειγμένη προϋπηρεσία στην Ε.Α.Ε.<small>Χρησιμοποιείται για τον έλεγχο ένταξης. Τους μήνες προϋπηρεσίας τους δηλώνεις χωριστά παρακάτω για τη μοριοδότηση.</small></label></div>
<div class="check hidden" id="pe11QualWrap"><input type="checkbox" id="pe11Qual" data-eae-main="pe11"><label for="pe11Qual">ΠΕ11 με προβλεπόμενη κύρια ειδικότητα Ε.Α.Ε. / Ειδικής Φυσικής Αγωγής κ.λπ.<small>Κριτήριο ένταξης και +8 μόρια.</small></label></div>

<h3>Προσόντα για ΕΠΙΚΟΥΡΙΚΟ πίνακα</h3>
<div class="check"><input type="checkbox" id="seminar400" data-eae-aux="seminar400"><label for="seminar400">Σεμινάριο εξειδίκευσης Ε.Α.Ε. ≥400 ωρών και ≥7 μηνών<small>Κριτήριο ένταξης στον επικουρικό. Καλύπτει παράλληλα και το γενικό κριτήριο επιμόρφωσης ≥300 ωρών / ≥7 μηνών (+2).</small></label></div>
<div class="field"><label for="eaeMonths">Αναγνωρισμένοι μήνες προϋπηρεσίας ειδικά στην Ε.Α.Ε.<small>Μόνο για έλεγχο του ορίου των 10 μηνών του επικουρικού· δεν προστίθενται δεύτερη φορά στα μόρια.</small></label><input id="eaeMonths" class="service-months" data-eae-aux="months" type="number" min="0" max="480" step="1" inputmode="numeric" value="0"></div>
</div>
</section>

<section id="asepPeAcademic" class="card" data-component="asep-pe-academic" data-profile="eae" data-degree-required="false" data-specialty-id="specialty" data-degree-id="degreeGrade" data-second-degree-id="secondDegree" data-phd-id="phd" data-msc-id="mscCount" data-language-id="asepLanguages" data-computer-id="computer" data-training-id="training" data-training-proof-id="trainingProof" data-phd-overlay-id="phdEae" data-msc-overlay-id="masterEae" data-training-overlay-id="seminar400" data-eae-pe11-special-id="pe11Qual" data-pe11-wrap-id="pe11QualWrap" data-pe6171-note-id="pe6171Auto">
<h2>2. Ακαδημαϊκά προσόντα</h2><p class="cap">Μέγιστο κατηγορίας Α: 120 μόρια.</p><div id="degreeValidation" class="note hidden">Ο βαθμός βασικού πτυχίου πρέπει να είναι από 5,00 έως 10,00.</div>
<div class="field"><label for="degreeGrade">Βαθμός βασικού πτυχίου (5–10)<small>Βαθμός × 2,5 · ανώτατο 25.</small></label><input id="degreeGrade" type="number" min="5" max="10" step="0.01" value="" placeholder="π.χ. 7,50"></div>
<div class="check"><input type="checkbox" id="secondDegree"><label for="secondDegree">Δεύτερο πτυχίο ΑΕΙ <small>+7 μόρια, εφόσον δεν αποτελεί τυπικό προσόν διορισμού.</small></label></div>
<div class="check"><input type="checkbox" id="phd"><label for="phd">Διδακτορικό δίπλωμα <small>+40 μόρια. Αν τσεκάρεις «Διδακτορικό ΕΑΕ» παραπάνω, ενεργοποιείται αυτόματα.</small></label></div>
<div class="field"><label for="mscCount">Μεταπτυχιακοί τίτλοι / integrated master<small>1 τίτλος: 20 · 2 ή περισσότεροι: 28 συνολικά. Για ΠΕ61/ΠΕ71 το βασικό πτυχίο δίνει αυτοδικαίως 20 και με επιπλέον μεταπτυχιακό η σχετική μοριοδότηση γίνεται 28.</small></label><select id="mscCount"><option value="0">Κανένας</option><option value="1">Ένας</option><option value="2">Δύο ή περισσότεροι</option></select></div>
<div id="pe6171Auto" class="info hidden">ΠΕ61/ΠΕ71: προστίθενται αυτοδικαίως 20 μόρια λόγω βασικού πτυχίου Ειδικής Αγωγής· με έναν ή περισσότερους επιπλέον μεταπτυχιακούς, η συγκεκριμένη μοριοδότηση γίνεται 28.</div>

<?php
renderAsepLanguageSelector(array(
    'id' => 'asepLanguages',
    'profile' => 'pe',
    'specialty_id' => 'specialty'
));
?>
<?php
renderAsepComputerProof(array(
    'input_id' => 'computer',
    'control_type' => 'checkbox',
    'points_text' => '4 μόρια',
    'restriction_note' => 'Δεν μοριοδοτείται στον ΠΕ86.'
));
?>
<div class="check"><input type="checkbox" id="training"><label for="training">Επιμόρφωση ≥300 ωρών και ≥7 μηνών <small>+2 μόρια. Μοριοδοτείται μόνο μία επιμόρφωση. Το 400ωρο ΕΑΕ του επικουρικού καλύπτει αυτό το κριτήριο.</small></label></div>
<?php
renderTrainingProof([
    'id' => 'trainingProof',
    'radio_name' => 'trainingDates',
    'yes_id' => 'trainingDatesYes',
    'no_id' => 'trainingDatesNo',
    'status_id' => 'trainingDatesStatus',
    'context' => '3ea-2025-general-300h-or-eae-400h-7m',
    'input_ids' => array('training', 'seminar400'),
    'legal_html' => <<<'HTML'
Σε περίπτωση που στο πιστοποιητικό δεν αναγράφεται η ημεροχρονολογία έναρξης και λήξης του σεμιναρίου, απαιτείται η προσκόμιση σχετικής βεβαίωσης από τον οικείο φορέα. <strong>Σε κάθε περίπτωση πρέπει να προκύπτει ολόκληρο το χρονικό διάστημα των 7 μηνών (6 μήνες και 29 ημέρες δεν γίνεται δεκτό).</strong>
HTML
]);
?>
</section>

<section id="asepService" class="card" data-component="asep-service-criteria">
<h2>3. Εκπαιδευτική προϋπηρεσία</h2><p class="cap">Μέγιστο κατηγορίας Β: 120 μόρια. Δήλωσε τους μήνες χωρίς επικάλυψη μεταξύ των ειδικών κατηγοριών.</p>
<div class="note"><strong>Σημείωση 3ΕΑ/2025:</strong> Στις γενικές κατηγορίες λαμβάνεται υπόψη η προϋπηρεσία σε ακέραιους μήνες.</div>
<div class="note">Οι μήνες δυσπρόσιτων, τρίμηνων συμβάσεων και Ψηφιακού Φροντιστηρίου πρέπει να δηλώνονται στις αντίστοιχες ειδικές γραμμές και όχι ξανά ως κανονική δημόσια προϋπηρεσία.</div>
<div class="field"><label for="regularMonths">Κανονική δημόσια προϋπηρεσία<small>1 μόριο ανά μήνα · έως 120 μήνες.</small></label><input id="regularMonths" class="service-months" data-service-role="regular" type="number" min="0" max="120" step="1" inputmode="numeric" value="0"></div>
<div class="field"><label for="difficultMonths">Δυσπρόσιτα / καταστήματα κράτησης από 2020–21<small>2 μόρια ανά μήνα · έως 60 μήνες.</small></label><input id="difficultMonths" class="service-months" data-service-role="difficult" type="number" min="0" max="60" step="1" inputmode="numeric" value="0"></div>

<?php
renderAsepThreeMonthService(array(
    'regular_2020_id' => 'threeMonthRegular2020',
    'difficult_2020_id' => 'threeMonthDifficult2020',
    'regular_2021_id' => 'threeMonthRegular2021',
    'difficult_2021_id' => 'threeMonthDifficult2021'
));
?>

<div class="field"><label for="privateMonths">Ιδιωτική εκπαιδευτική προϋπηρεσία<small>0,9 μόρια ανά μήνα, εφόσον πληρούνται οι νόμιμες προϋποθέσεις.</small></label><input id="privateMonths" class="service-months" data-service-role="private" type="number" min="0" max="480" step="1" inputmode="numeric" value="0"></div>
<?php renderAsepDigitalTutoringService(array('container_id' => 'digitalTutoring', 'input_class' => 'service-months')); ?>
</section>

<?php
renderAsepSocialCriteria(array(
    'title' => '4. Κοινωνικά κριτήρια',
    'children_id' => 'children',
    'candidate_id' => 'candidateDisability',
    'spouse_id' => 'spouseDisability',
    'child_id' => 'childDisability',
    'marriage_id' => 'marriageYears4Plus',
    'mental_id' => 'candidateMentalCondition',
    'input_step' => '1',
    'child_points' => 3,
    'min_disability_percent' => 50,
    'disability_rate' => '0,4',
    'spouse_min_marriage_years' => 4,
    'child_extra_note' => '',
    'child_auxiliary_note' => 'Από 67% και άνω μπορεί να θεμελιώνει και ένταξη στον Επικουρικό Πίνακα.',
    'warning_id' => 'socialWarnings',
    'warning_mode' => 'bullets',
    'subtotal_id' => '',
    'subtotal_label' => 'Σύνολο Κοινωνικών'
));
?>

<section class="card">
<h2>5. Προτάξεις / ειδικές προτεραιότητες</h2>
<div class="check"><input type="checkbox" id="pedagogical"><label for="pedagogical">Πιστοποιημένη Παιδαγωγική και Διδακτική Επάρκεια<small>Δεν προσθέτει μόρια· ο υποψήφιος προτάσσεται έναντι υποψηφίων που δεν τη διαθέτουν.</small></label></div>
<?php
renderEaeSensoryPriority(array(
    'context' => '3ea-2025',
    'eng_enabled' => true,
    'braille_enabled' => true,
    'eng_id' => 'signLanguage',
    'braille_id' => 'braille'
));
?>
</section>
</main>

<aside class="card result-card" aria-live="polite">
<h2 class="edu-text-center">Αποτέλεσμα</h2>
<div class="total" id="grandTotal">0.00</div><div class="total-label">συνολικά μόρια</div>
<div id="tableStatus" class="status none">Επίλεξε κλάδο</div>
<div id="eligibilityWhy" class="eligibility-box"><strong>Έλεγχος ένταξης</strong>Συμπλήρωσε τα προσόντα σου.</div>
<table class="table"><tr><td>Ακαδημαϊκά</td><td id="resAcademic">0.00 / 120</td></tr><tr><td>Προϋπηρεσία</td><td id="resService">0.00 / 120</td></tr><tr><td>Κοινωνικά</td><td id="resSocial">0.00</td></tr></table>
<div id="priorities"></div>
<div class="actions"><button type="button" id="copyBtn">Αντιγραφή</button><button type="button" class="secondary" id="resetBtn">Μηδενισμός</button></div>
<div class="note edu-mt-14">Ενημερωτικός υπολογισμός βάσει της 3ΕΑ/2025. Η τελική ένταξη και μοριοδότηση προκύπτει από τον έλεγχο ΑΣΕΠ/ΟΠΣΥΔ και τα επίσημα δικαιολογητικά.</div>
</aside>
</div>

<section class="edu-source-card" aria-labelledby="sourcesTitle">
  <h2 id="sourcesTitle">Πηγές / Νομική βάση</h2>
  <p>Προκήρυξη ΑΣΕΠ <strong>3ΕΑ/2025</strong> για εκπαιδευτικούς Ειδικής Αγωγής και Εκπαίδευσης κατηγορίας ΠΕ — <strong>ΦΕΚ 22/23.05.2025/τ. Α.Σ.Ε.Π.</strong> και <strong>ΦΕΚ 25/02.06.2025/τ. Α.Σ.Ε.Π.</strong>, ιδίως τα Κεφάλαια Β΄ και Γ΄.</p>
  <div class="source-links"><a href="https://info.asep.gr/node/76185" target="_blank" rel="noopener noreferrer">Επίσημη σελίδα 3ΕΑ/2025 στο ΑΣΕΠ ↗</a> <a href="https://info.asep.gr/sites/default/files/2025-05/3%CE%95%CE%91_2025%20%CE%A4%CF%85%CF%80%CE%B9%CE%BA%CE%AC%20%CE%A0%CF%81%CE%BF%CF%83%CF%8C%CE%BD%CF%84%CE%B1%20%CE%88%CE%BD%CF%84%CE%B1%CE%BE%CE%B7%CF%82.pdf" target="_blank" rel="noopener noreferrer">Τυπικά Προσόντα Ένταξης 3ΕΑ/2025 ↗</a></div>
  <p class="source-disclaimer">Το εργαλείο είναι ενημερωτικό και δεν υποκαθιστά τον επίσημο έλεγχο της αίτησης, του ΟΠΣΥΔ και των δικαιολογητικών από το ΑΣΕΠ και τα αρμόδια όργανα.</p>
</section>
</div>
<script src="includes/service-calculations.js?v=3.20.33"></script>
<script src="includes/asep-service-controller.js?v=3.20.26"></script>
<script src="includes/asep-digital-tutoring.js?v=3.20.22"></script>
<script src="includes/social-calculations.js?v=3.20.32"></script>
<script src="includes/asep-social-criteria.js?v=3.20.32"></script>
<script src="includes/eae-table-eligibility.js?v=3.20.33"></script>
<script src="includes/asep-eae-eligibility.js?v=3.20.28"></script>
<script src="includes/language-calculations.js?v=3.20.31"></script>
<script src="includes/asep-language-selector.js?v=3.20.31"></script>
<script src="includes/academic-calculations.js?v=3.20.31"></script>
<script src="includes/asep-pe-academic.js?v=3.20.31"></script>
<script src="includes/training-proof.js?v=3.20.18"></script>
<script>
(function(){
 const $=id=>document.getElementById(id); const num=id=>Math.max(0,Number($(id)?.value||0)); const cap=(v,m)=>Math.min(v,m); const fmt=v=>(Math.round((v+Number.EPSILON)*100)/100).toFixed(2);
 function calcService(){return AsepServiceController.getState('asepService',fmt).points;}
 function calcSocial(){return AsepSocialCriteria.getState('socialCriteria',fmt);}
 function render(){
   TrainingProof.syncAll();
   const degreeGrade=num('degreeGrade');
   const degreeInvalid=degreeGrade>0 && (degreeGrade<5 || degreeGrade>10);
   $('degreeValidation').classList.toggle('hidden', !degreeInvalid);
   const academic=AsepPeAcademic.calculate('asepPeAcademic'), a=academic.points, b=calcService(), socialResult=calcSocial(), c=socialResult.points, t=a+b+c, e=AsepEaeEligibility.getState('eaeEligibility',{socialResult:socialResult});
   $('grandTotal').textContent=fmt(t); $('resAcademic').textContent=fmt(a)+' / 120'; $('resService').textContent=fmt(b)+' / 120'; $('resSocial').textContent=fmt(c);
   $('tableStatus').className='status '+e.type; $('tableStatus').textContent=e.label; $('eligibilityWhy').innerHTML='<strong>Έλεγχος ένταξης</strong>'+e.why;
   let p=[]; if($('pedagogical').checked) p.push('Πρόταξη λόγω Παιδαγωγικής & Διδακτικής Επάρκειας'); if($('braille').checked) p.push('Προτεραιότητα Braille για μαθητές με προβλήματα όρασης'); if($('signLanguage').checked) p.push('Προτεραιότητα Ε.Ν.Γ. για κωφούς/βαρήκοους μαθητές');
   $('priorities').innerHTML=p.map(x=>'<div class="priority">✓ '+x+'</div>').join('');
   return {a,b,c,t,e,p};
 }
 function summary(v){return ['Υπολογισμός μορίων 3ΕΑ/2025',`Πίνακας: ${v.e.label}`,v.e.why,`Ακαδημαϊκά: ${fmt(v.a)} / 120`,`Προϋπηρεσία: ${fmt(v.b)} / 120`,`Κοινωνικά: ${fmt(v.c)}`,`ΣΥΝΟΛΟ: ${fmt(v.t)}`,AsepDigitalTutoring.summary('digitalTutoring',fmt),v.p.length?'Προτάξεις/προτεραιότητες: '+v.p.join(' · '):'',TrainingProof.summary('trainingProof')].filter(Boolean).join('\n');}
 function sanitizeServiceMonthInput(el){
   if(!el || !el.classList.contains('service-months')) return;
   const maxAttr=el.getAttribute('max');
   let v=el.value === '' ? 0 : Number(el.value);
   if(!Number.isFinite(v)) v=0;
   v=Math.max(0,Math.trunc(v));
   if(maxAttr!==null && maxAttr!=='') v=Math.min(v,Number(maxAttr));
   if(String(v)!==el.value) el.value=String(v);
 }
 document.addEventListener('input',e=>{
   sanitizeServiceMonthInput(e.target);
   render();
 });
 document.addEventListener('change',e=>{
   sanitizeServiceMonthInput(e.target);
   if(e.target && e.target.id==='degreeGrade' && e.target.value!==''){
     let v=Number(String(e.target.value).replace(',', '.'));
     if(Number.isFinite(v)) e.target.value=String(Math.min(10,Math.max(5,v)));
     else e.target.value='';
   }
   render();
 });
 $('copyBtn').addEventListener('click',async()=>{const txt=summary(render());try{await navigator.clipboard.writeText(txt);$('copyBtn').textContent='Αντιγράφηκε';setTimeout(()=>$('copyBtn').textContent='Αντιγραφή',1200)}catch(e){alert(txt)}});
 document.addEventListener('asep-digital-tutoring-change',render);
 $('resetBtn').addEventListener('click',()=>{document.querySelectorAll('input[type=number]').forEach(x=>x.value=0);$('degreeGrade').value='';document.querySelectorAll('input[type=text]').forEach(x=>x.value='');document.querySelectorAll('input[type=checkbox]').forEach(x=>x.checked=false);document.querySelectorAll('input[name="trainingDates"]').forEach(x=>x.checked=false);document.querySelectorAll('select').forEach(x=>x.selectedIndex=0);AsepPeAcademic.reset('asepPeAcademic',{silent:true});AsepServiceController.reset('asepService',{silent:true});render();});
 render();
})();
</script>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
  <script src="includes/asep-computer-proof.js?v=3.20.15-rc2"></script>
  <script src="includes/eae-sensory-proof.js?v=3.20.23"></script>
  <script src="assets/common.js?v=3.20.13"></script>
</body>
</html>
