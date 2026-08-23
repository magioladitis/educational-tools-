<!DOCTYPE html>
<html lang="el">
<head>
<!-- UI consolidation v3.20: shared design system in assets/common.css -->
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="Υπολογισμός μορίων για την προκήρυξη ΑΣΕΠ 1ΕΑ/2025 για μέλη Ειδικού Βοηθητικού Προσωπικού (ΕΒΠ) κλάδου ΔΕ01.">
<title>Υπολογισμός μορίων 1ΕΑ/2025</title>
<link rel="stylesheet" href="assets/common.css?v=3.20.17">
</head>
<body class="edu-ui edu-calc-standard edu-page-ea1">
<?php require_once __DIR__ . '/includes/header.php'; ?>
<?php require_once __DIR__ . '/includes/components/training-proof.php'; ?>
<?php require_once __DIR__ . '/includes/components/asep-language-selector.php'; ?>
<?php require_once __DIR__ . '/includes/components/asep-computer-proof.php'; ?>
<?php require_once __DIR__ . '/includes/components/asep-social-criteria.php'; ?>
<?php require_once __DIR__ . '/includes/components/asep-three-month-service.php'; ?>
<?php require_once __DIR__ . '/includes/components/eae-sensory-priority.php'; ?>
<div class="app">
<section class="hero">
<h1>Υπολογισμός μορίων 1ΕΑ/2025</h1>
<p>Ενδεικτικός υπολογισμός για τον αξιολογικό πίνακα Γ2΄ μελών <strong>Ειδικού Βοηθητικού Προσωπικού (Ε.Β.Π.) — ΔΕ01</strong>.</p>
<div class="meta"><span>1ΕΑ/2025</span><span>ΔΕ01 — ΕΒΠ</span><span>Ακαδημαϊκά έως 64 ή 96</span><span>Προϋπηρεσία έως 120</span></div>
</section>
<div class="layout"><main>
<section class="card">
<h2>1. Βασικός τίτλος σπουδών</h2>
<p class="cap">Ο τύπος τίτλου αλλάζει τόσο τον συντελεστή του βαθμού όσο και το ανώτατο όριο των ακαδημαϊκών μορίων.</p>
<div class="field"><label for="titleType">Τύπος βασικού τίτλου</label><select id="titleType"><option value="secondary">Πτυχίο ΕΠΑΛ / ΤΕΕ Β΄ κύκλου / ΤΕΛ / ΕΠΛ</option><option value="postsecondary">Δίπλωμα ΙΕΚ / Τάξη Μαθητείας ΕΠΑΛ</option></select></div>
<div class="field"><label for="degreeGrade">Βαθμός τίτλου σε 20βάθμια κλίμακα<small>Αν ο τίτλος έχει διαφορετική κλίμακα, απαιτείται προηγουμένως αναγωγή στην 20βάθμια.</small></label><input id="degreeGrade" type="number" min="10" max="20" step="0.01" inputmode="decimal" placeholder="π.χ. 17,25"></div>
<div id="gradeInfo" class="info">Τίτλος δευτεροβάθμιας: 4 μόρια για κάθε βαθμό πάνω από 10 · ακαδημαϊκό όριο 64.</div>
<div id="gradeWarning" class="warning hidden">Ο βαθμός πρέπει να είναι από 10,00 έως 20,00.</div>
</section>
<section class="card">
<h2>2. Ακαδημαϊκά προσόντα</h2>
<p class="cap">Το συνολικό όριο είναι 64 μόρια για τίτλο δευτεροβάθμιας και 96 για τίτλο μεταδευτεροβάθμιας εκπαίδευσης.</p>
<div class="checkrow"><input type="checkbox" id="secondTitle"><label for="secondTitle">Δεύτερος τίτλος σπουδών<small>+10 μόρια.</small></label></div>
<?php
renderAsepLanguageSelector(array(
    'id' => 'asepLanguages',
    'profile' => 'ebp'
));
?>
<?php
renderAsepComputerProof(array(
    'input_id' => 'computer',
    'control_type' => 'checkbox',
    'points_text' => '4 μόρια'
));
?>
<div class="checkrow"><input type="checkbox" id="training"><label for="training">Επιμόρφωση ≥300 ωρών και ≥7 μηνών<small>ΑΕΙ ή εποπτευόμενος δημόσιος φορέας · +2 μόρια · μοριοδοτείται μία επιμόρφωση.</small></label></div>
<?php
renderTrainingProof([
    'id' => 'trainingProof',
    'radio_name' => 'trainingDates',
    'yes_id' => 'trainingDatesYes',
    'no_id' => 'trainingDatesNo',
    'status_id' => 'trainingDatesStatus',
    'context' => '1ea-2025-300h-7m',
    'legal_html' => <<<'HTML'
Σε περίπτωση που στο πιστοποιητικό δεν αναγράφεται η ημεροχρονολογία έναρξης και λήξης του σεμιναρίου, απαιτείται η προσκόμιση σχετικής βεβαίωσης από τον οικείο φορέα. <strong>Σε κάθε περίπτωση πρέπει να προκύπτει ολόκληρο το χρονικό διάστημα των 7 μηνών (6 μήνες και 29 ημέρες δεν γίνεται δεκτό).</strong>
HTML
]);
?>
<div class="subtot"><span>Ακαδημαϊκά</span><span class="pill" id="academicSubtotal">0,00</span></div>
</section>
<section id="asepService" class="card" data-component="asep-service-criteria" data-warning-id="serviceWarning" data-subtotal-id="serviceSubtotal" data-warn-months="true">
<h2>3. Εκπαιδευτική προϋπηρεσία</h2><p class="cap">Μέγιστο 120 μόρια. Μην δηλώνεις τον ίδιο μήνα σε περισσότερα από ένα πεδία.</p>
<div class="field-grid"><div class="field"><label for="publicMonths">Κανονική δημόσια προϋπηρεσία<small>1 μόριο/μήνα · έως 120 μήνες.</small></label><input id="publicMonths" class="service-months" data-service-role="regular" type="number" min="0" max="120" step="1" value="0"></div><div class="field"><label for="hardMonths">Δυσπρόσιτα / καταστήματα κράτησης από 2020–21<small>2 μόρια/μήνα · έως 60 μήνες.</small></label><input id="hardMonths" class="service-months" data-service-role="difficult" type="number" min="0" max="60" step="1" value="0"></div></div>
<?php
renderAsepThreeMonthService(array(
    'regular_2020_id' => 'covid2020',
    'difficult_2020_id' => 'covidHard2020',
    'regular_2021_id' => 'covid2021',
    'difficult_2021_id' => 'covidHard2021'
));
?>
<div class="info">Η προϋπηρεσία αναπληρωτή ΤΕ01.30 Βοηθών Βρεφοκόμων–Παιδοκόμων προσμετράται και στον ΔΕ01-ΕΒΠ. Η ειδική μοριοδότηση ιδιωτικής σχολικής προϋπηρεσίας με συντελεστή 0,9 <strong>δεν αφορά τα μέλη ΕΕΠ-ΕΒΠ</strong>.</div>
<div id="serviceWarning" class="note hidden"></div>
<div class="subtot"><span>Προϋπηρεσία</span><span class="pill" id="serviceSubtotal">0,00</span></div>
</section>
<?php
renderAsepSocialCriteria(array(
    'title' => '4. Κοινωνικά κριτήρια',
    'children_id' => 'children',
    'candidate_id' => 'candidateDisability',
    'spouse_id' => 'spouseDisability',
    'child_id' => 'childDisability',
    'marriage_id' => 'marriage4',
    'mental_id' => 'mental',
    'input_step' => '0.01',
    'child_points' => 3,
    'min_disability_percent' => 50,
    'disability_rate' => '0,4',
    'spouse_min_marriage_years' => 4,
    'child_extra_note' => '',
    'child_auxiliary_note' => '',
    'warning_id' => 'socialWarning',
    'subtotal_id' => 'socialSubtotal',
    'subtotal_label' => 'Σύνολο Κοινωνικών'
));
?>
<section class="card">
<h2>5. Ειδική προτεραιότητα</h2>
<?php
renderEaeSensoryPriority(array(
    'context' => '1ea-2025',
    'eng_enabled' => true,
    'braille_enabled' => false,
    'eng_id' => 'signLanguage'
));
?>
</section>
</main><aside class="results">
<section class="card"><h2>Αποτέλεσμα</h2><div class="total"><div class="num" id="totalPoints">0,00</div><div class="label">συνολικά μόρια</div></div><div class="result-row"><span>Βαθμός τίτλου</span><strong id="degreePoints">0,00</strong></div><div class="result-row"><span>Ακαδημαϊκά</span><strong id="academicPoints">0,00</strong></div><div class="result-row"><span>Προϋπηρεσία</span><strong id="servicePoints">0,00</strong></div><div class="result-row"><span>Κοινωνικά</span><strong id="socialPoints">0,00</strong></div><div class="result-row"><span>Όριο ακαδημαϊκών</span><strong id="academicCap">64</strong></div><div id="priorityBox" class="priority">Χωρίς δηλωμένη ειδική προτεραιότητα ΕΝΓ</div><div class="actions"><button type="button" id="copyBtn">Αντιγραφή</button><button type="button" class="secondary" id="resetBtn">Μηδενισμός</button></div></section>
</aside></div>
<section class="edu-source-card" aria-labelledby="sourcesTitle"><h2 id="sourcesTitle">Πηγές / Νομική βάση</h2><p><strong>Βάση υπολογισμού:</strong> Προκήρυξη ΑΣΕΠ 1ΕΑ/2025, Κεφάλαιο Γ΄ — κριτήρια αξιολογικού πίνακα Γ2΄ ΕΒΠ.</p><div class="source-links"><a href="https://info.asep.gr/node/76176" target="_blank" rel="noopener noreferrer">1ΕΑ/2025 — ΑΣΕΠ ↗</a></div><p class="source-disclaimer">Το εργαλείο είναι βοηθητικό και δεν υποκαθιστά τον επίσημο έλεγχο ΑΣΕΠ/ΟΠΣΥΔ.</p></section>
<div class="credits">Εργαλείο υπολογισμού μορίων · 1ΕΑ/2025</div>
</div>
<script src="includes/service-calculations.js?v=3.20.26"></script>
<script src="includes/asep-service-controller.js?v=3.20.26"></script>
<script src="includes/social-calculations.js?v=3.20.26"></script>
<script src="includes/asep-social-criteria.js?v=3.20.26"></script>
<script src="includes/language-calculations.js?v=3.20.24"></script>
<script src="includes/asep-language-selector.js?v=3.20.24"></script>
<script src="includes/training-proof.js?v=3.20.18"></script>
<script>
(function(){"use strict";const $=id=>document.getElementById(id);const n=id=>Number($(id).value)||0;const fmt=v=>(Math.round((Number(v)||0)*100)/100).toLocaleString('el-GR',{minimumFractionDigits:2,maximumFractionDigits:2});const round2=v=>Math.round((v+Number.EPSILON)*100)/100;
function academic(){const languages=AsepLanguageSelector.calculate('asepLanguages');const type=$('titleType').value;const grade=n('degreeGrade');const valid=$('degreeGrade').value!==''&&grade>=10&&grade<=20;const rate=type==='postsecondary'?7.2:4;const cap=type==='postsecondary'?96:64;const degree=valid?round2(Math.max(0,grade-10)*rate):0;const raw=degree+($('secondTitle').checked?10:0)+languages.points+($('computer').checked?4:0)+($('training').checked?2:0);return{degree,raw,points:Math.min(raw,cap),cap,valid,languages};}
function service(){return AsepServiceController.getState('asepService',fmt);}
function social(){return AsepSocialCriteria.getState('socialCriteria',fmt);}
function render(){TrainingProof.syncAll();const a=academic(),s=service(),c=social(),total=a.points+s.points+c.total;const post=$('titleType').value==='postsecondary';$('gradeInfo').innerHTML=post?'Τίτλος μεταδευτεροβάθμιας: <strong>7,2 μόρια</strong> για κάθε βαθμό πάνω από 10 · ακαδημαϊκό όριο <strong>96</strong>.':'Τίτλος δευτεροβάθμιας: <strong>4 μόρια</strong> για κάθε βαθμό πάνω από 10 · ακαδημαϊκό όριο <strong>64</strong>.';$('gradeWarning').classList.toggle('hidden',$('degreeGrade').value===''||a.valid);$('academicSubtotal').textContent=fmt(a.points);$('totalPoints').textContent=fmt(total);$('degreePoints').textContent=fmt(a.degree);$('academicPoints').textContent=fmt(a.points)+(a.raw>a.cap?' (όριο '+a.cap+')':'');$('servicePoints').textContent=fmt(s.points)+(s.raw>120?' (όριο 120)':'');$('socialPoints').textContent=fmt(c.total);$('academicCap').textContent=a.cap;const pb=$('priorityBox');pb.className='priority'+($('signLanguage').checked?' yes':'');pb.textContent=$('signLanguage').checked?'Ειδική προτεραιότητα ΕΝΓ για υποστήριξη κωφών/βαρήκοων μαθητών':'Χωρίς δηλωμένη ειδική προτεραιότητα ΕΝΓ';return{a,s,c,total};}
function summary(r){return[`Υπολογισμός μορίων 1ΕΑ/2025 — ΔΕ01 ΕΒΠ`,`Βαθμός τίτλου: ${fmt(r.a.degree)}`,`Ακαδημαϊκά: ${fmt(r.a.points)}`,`Προϋπηρεσία: ${fmt(r.s.points)}`,`Κοινωνικά: ${fmt(r.c.total)}`,`ΣΥΝΟΛΟ: ${fmt(r.total)}`,$('signLanguage').checked?'ΕΝΓ: δηλώθηκε ειδική προτεραιότητα':'ΕΝΓ: δεν δηλώθηκε',TrainingProof.summary('trainingProof')].filter(Boolean).join('\n');}
function sanitizeServiceMonthInput(el){if(!el||!el.classList.contains('service-months')||el.value==='')return;let value=Math.max(0,Math.floor(Number(el.value)||0));const max=el.getAttribute('max');if(max!==null&&max!=='')value=Math.min(value,Number(max));el.value=String(value);}
document.addEventListener('input',e=>{sanitizeServiceMonthInput(e.target);render();});document.addEventListener('change',render);$('copyBtn').addEventListener('click',async()=>{const txt=summary(render());try{await navigator.clipboard.writeText(txt);$('copyBtn').textContent='Αντιγράφηκε';setTimeout(()=>$('copyBtn').textContent='Αντιγραφή',1200)}catch(e){alert(txt)}});$('resetBtn').addEventListener('click',()=>{document.querySelectorAll('input[type=number]').forEach(x=>x.value=x.id==='degreeGrade'?'':'0');document.querySelectorAll('input[type=text]').forEach(x=>x.value='');document.querySelectorAll('input[type=checkbox]').forEach(x=>x.checked=false);document.querySelectorAll('input[name="trainingDates"]').forEach(x=>x.checked=false);document.querySelectorAll('select').forEach(x=>x.selectedIndex=0);AsepLanguageSelector.reset('asepLanguages',{silent:true});render();});render();})();
</script>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
  <script src="includes/asep-computer-proof.js?v=3.20.15-rc2"></script>
  <script src="includes/eae-sensory-proof.js?v=3.20.23"></script>
  <script src="assets/common.js?v=3.20.13"></script>
</body>
</html>
