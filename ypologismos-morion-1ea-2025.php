<?php require_once __DIR__ . '/includes/config.php'; ?>
<!DOCTYPE html>
<html lang="el">
<head>
<!-- UI consolidation v3.20: shared design system in assets/common.css -->
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="Υπολογισμός μορίων για την προκήρυξη ΑΣΕΠ 1ΕΑ/2025 για μέλη Ειδικού Βοηθητικού Προσωπικού (ΕΒΠ) κλάδου ΔΕ01.">
<title>Υπολογισμός μορίων 1ΕΑ/2025</title>
<link rel="stylesheet" href="<?php echo htmlspecialchars(edu_asset_url('assets/common.css'), ENT_QUOTES, 'UTF-8'); ?>">
</head>
<body class="edu-ui edu-calc-standard edu-page-ea1">
<?php require_once __DIR__ . '/includes/header.php'; ?>
<?php require_once __DIR__ . '/includes/components/calculator-layout.php'; ?>
<?php require_once __DIR__ . '/includes/components/deadline-card.php'; ?>
<?php require_once __DIR__ . '/includes/components/training-proof.php'; ?>
<?php require_once __DIR__ . '/includes/components/asep-language-selector.php'; ?>
<?php require_once __DIR__ . '/includes/components/asep-computer-proof.php'; ?>
<?php require_once __DIR__ . '/includes/components/asep-social-criteria.php'; ?>
<?php require_once __DIR__ . '/includes/components/asep-three-month-service.php'; ?>
<?php require_once __DIR__ . '/includes/components/eae-sensory-priority.php'; ?>
<div class="app">
<?php calculatorHeroStart(); ?>
<h1>Υπολογισμός μορίων 1ΕΑ/2025</h1>
<p>Ενδεικτικός υπολογισμός για τον αξιολογικό πίνακα Γ2΄ μελών <strong>Ειδικού Βοηθητικού Προσωπικού (Ε.Β.Π.) — ΔΕ01</strong>.</p>
<div class="meta"><span>1ΕΑ/2025</span><span>ΔΕ01 — ΕΒΠ</span><span>Ακαδημαϊκά έως 64 ή 96</span><span>Προϋπηρεσία έως 120</span></div>
<?php calculatorHeroEnd(); ?>
<?php
renderDeadlineCard(array(
    'title' => '📅 Δήλωση προτιμήσεων ΕΕΠ–ΕΒΠ 2026–2027',
    'intro' => 'Η πρόσκληση για υποψήφια μέλη ΕΕΠ–ΕΒΠ αφορά τις προσλήψεις αναπληρωτών για το διδακτικό έτος 2026–2027.',
    'items' => array(array(
        'title' => '1ΕΑ/2025 — ΔΕ01 Ειδικού Βοηθητικού Προσωπικού (ΕΒΠ)',
        'meta_html' => 'Δήλωση προτιμήσεων στο <strong>ΟΠΣΥΔ</strong> από <strong>Παρασκευή 14</strong> έως και <strong>Δευτέρα 24 Αυγούστου 2026</strong>.',
        'start' => '2026-08-14T00:00:00+03:00',
        'end_exclusive' => '2026-08-25T00:00:00+03:00',
        'source_url' => 'https://diavgeia.gov.gr/doc/%CE%A8%CE%970%CE%9C46%CE%9D%CE%9A%CE%A0%CE%94-553?inline=true',
        'source_label' => 'Επίσημη πρόσκληση — ΑΔΑ ΨΗ0Μ46ΝΚΠΔ-553 ↗'
    )),
    'note_html' => '<strong>Σημείωση ώρας:</strong> η επίσημη πρόσκληση αναφέρει την καταληκτική ημερομηνία 24/08/2026 χωρίς συγκεκριμένη ώρα. Το countdown χρησιμοποιεί τεχνικά το τέλος της ημέρας σε ώρα Ελλάδας· υπερισχύει πάντοτε η επίσημη πρόσκληση και το ΟΠΣΥΔ.'
));
?>
<?php calculatorColumnsStart(); ?><?php calculatorMainStart(array('tag' => 'main')); ?>
<?php calculatorCardStart(); ?>
<h2>1. Βασικός τίτλος σπουδών</h2>
<p class="cap">Ο τύπος τίτλου αλλάζει τόσο τον συντελεστή του βαθμού όσο και το ανώτατο όριο των ακαδημαϊκών μορίων.</p>
<div class="field"><label for="titleType">Τύπος βασικού τίτλου</label><select id="titleType"><option value="secondary">Πτυχίο ΕΠΑΛ / ΤΕΕ Β΄ κύκλου / ΤΕΛ / ΕΠΛ</option><option value="postsecondary">Δίπλωμα ΙΕΚ / Τάξη Μαθητείας ΕΠΑΛ</option></select></div>
<div class="field"><label for="degreeGrade">Βαθμός τίτλου σε 20βάθμια κλίμακα<small>Αν ο τίτλος έχει διαφορετική κλίμακα, απαιτείται προηγουμένως αναγωγή στην 20βάθμια.</small></label><input id="degreeGrade" type="number" min="10" max="20" step="0.01" inputmode="decimal" placeholder="π.χ. 17,25"></div>
<div id="gradeInfo" class="info">Τίτλος δευτεροβάθμιας: 4 μόρια για κάθε βαθμό πάνω από 10 · ακαδημαϊκό όριο 64.</div>
<div id="gradeWarning" class="warning hidden">Ο βαθμός πρέπει να είναι από 10,00 έως 20,00.</div>
<?php calculatorCardEnd(); ?>
<?php calculatorCardStart(); ?>
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
    'radio_name' => 'trainingProofDates',
    'yes_id' => 'trainingProofDatesYes',
    'no_id' => 'trainingProofDatesNo',
    'status_id' => 'trainingProofDatesStatus',
    'context' => '1ea-2025-300h-7m',
    'legal_html' => <<<'HTML'
Σε περίπτωση που στο πιστοποιητικό δεν αναγράφεται η ημεροχρονολογία έναρξης και λήξης του σεμιναρίου, απαιτείται η προσκόμιση σχετικής βεβαίωσης από τον οικείο φορέα. <strong>Σε κάθε περίπτωση πρέπει να προκύπτει ολόκληρο το χρονικό διάστημα των 7 μηνών (6 μήνες και 29 ημέρες δεν γίνεται δεκτό).</strong>
HTML
]);
?>
<div class="subtot"><span>Ακαδημαϊκά</span><span class="pill" id="academicSubtotal">0,00</span></div>
<?php calculatorCardEnd(); ?>
<?php calculatorCardStart(array('id' => 'asepService', 'attrs' => array('data-component' => 'asep-service-criteria', 'data-warning-id' => 'serviceWarning', 'data-subtotal-id' => 'serviceSubtotal', 'data-warn-months' => 'true'))); ?>
<h2>3. Εκπαιδευτική προϋπηρεσία</h2><p class="cap">Μέγιστο 120 μόρια. Μην δηλώνεις τον ίδιο μήνα σε περισσότερα από ένα πεδία.</p>
<div class="field-grid"><div class="field"><label for="regularMonths">Κανονική δημόσια προϋπηρεσία<small>1 μόριο/μήνα · έως 120 μήνες.</small></label><input id="regularMonths" class="service-months" data-service-role="regular" type="number" min="0" max="120" step="1" value="0"></div><div class="field"><label for="difficultMonths">Δυσπρόσιτα / καταστήματα κράτησης από 2020–21<small>2 μόρια/μήνα · έως 60 μήνες.</small></label><input id="difficultMonths" class="service-months" data-service-role="difficult" type="number" min="0" max="60" step="1" value="0"></div></div>
<?php
renderAsepThreeMonthService(array(
    'regular_2020_id' => 'threeMonthRegular2020',
    'difficult_2020_id' => 'threeMonthDifficult2020',
    'regular_2021_id' => 'threeMonthRegular2021',
    'difficult_2021_id' => 'threeMonthDifficult2021'
));
?>
<div class="info">Η προϋπηρεσία αναπληρωτή ΤΕ01.30 Βοηθών Βρεφοκόμων–Παιδοκόμων προσμετράται και στον ΔΕ01-ΕΒΠ. Η ειδική μοριοδότηση ιδιωτικής σχολικής προϋπηρεσίας με συντελεστή 0,9 <strong>δεν αφορά τα μέλη ΕΕΠ-ΕΒΠ</strong>.</div>
<div id="serviceWarning" class="note hidden"></div>
<div class="subtot"><span>Προϋπηρεσία</span><span class="pill" id="serviceSubtotal">0,00</span></div>
<?php calculatorCardEnd(); ?>
<?php
renderAsepSocialCriteria(array(
    'title' => '4. Κοινωνικά κριτήρια',
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
    'child_auxiliary_note' => '',
    'warning_id' => 'socialWarning',
    'subtotal_id' => 'socialSubtotal',
    'subtotal_label' => 'Σύνολο Κοινωνικών'
));
?>
<?php calculatorCardStart(); ?>
<h2>5. Ειδική προτεραιότητα</h2>
<?php
renderEaeSensoryPriority(array(
    'context' => '1ea-2025',
    'eng_enabled' => true,
    'braille_enabled' => false
));
?>
<?php calculatorCardEnd(); ?>
<?php calculatorMainEnd(); ?><?php calculatorResultsStart(array('class' => 'results')); ?>
<?php calculatorCardStart(); ?><h2>Αποτέλεσμα</h2><div class="total"><div class="num" id="totalPoints">0,00</div><div class="label">συνολικά μόρια</div></div><?php calculatorResultRow(array('label_html' => 'Βαθμός τίτλου', 'value_html' => '0,00', 'value_id' => 'degreePoints')); ?><?php calculatorResultRow(array('label_html' => 'Ακαδημαϊκά', 'value_html' => '0,00', 'value_id' => 'academicPoints')); ?><?php calculatorResultRow(array('label_html' => 'Προϋπηρεσία', 'value_html' => '0,00', 'value_id' => 'servicePoints')); ?><?php calculatorResultRow(array('label_html' => 'Κοινωνικά', 'value_html' => '0,00', 'value_id' => 'socialPoints')); ?><?php calculatorResultRow(array('label_html' => 'Όριο ακαδημαϊκών', 'value_html' => '64', 'value_id' => 'academicCap')); ?><div id="priorityBox" class="priority">Χωρίς δηλωμένη ειδική προτεραιότητα ΕΝΓ</div><?php calculatorActions(array(array('attrs' => array('type' => 'button', 'id' => 'copyBtn'), 'html' => 'Αντιγραφή'), array('attrs' => array('type' => 'button', 'class' => 'secondary', 'id' => 'resetBtn'), 'html' => 'Μηδενισμός'))); ?><?php calculatorCardEnd(); ?>
<?php calculatorResultsEnd(); ?><?php calculatorColumnsEnd(); ?>
<?php sourceCardStart(); ?><p><strong>Βάση υπολογισμού:</strong> Προκήρυξη ΑΣΕΠ 1ΕΑ/2025, Κεφάλαιο Γ΄ — κριτήρια αξιολογικού πίνακα Γ2΄ ΕΒΠ.</p><?php sourceCardLinksStart(); ?><?php sourceCardLink('https://info.asep.gr/node/76176', '1ΕΑ/2025 — ΑΣΕΠ ↗'); ?><?php sourceCardLinksEnd(); ?><?php sourceCardDisclaimerStart(); ?>Το εργαλείο είναι βοηθητικό και δεν υποκαθιστά τον επίσημο έλεγχο ΑΣΕΠ/ΟΠΣΥΔ.<?php sourceCardDisclaimerEnd(); ?><?php sourceCardEnd(); ?>
<div class="credits">Εργαλείο υπολογισμού μορίων · 1ΕΑ/2025</div>
</div>
<script src="<?php echo htmlspecialchars(edu_asset_url('includes/service-calculations.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
<script src="<?php echo htmlspecialchars(edu_asset_url('includes/asep-service-controller.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
<script src="<?php echo htmlspecialchars(edu_asset_url('includes/social-calculations.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
<script src="<?php echo htmlspecialchars(edu_asset_url('includes/asep-social-criteria.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
<script src="<?php echo htmlspecialchars(edu_asset_url('includes/language-calculations.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
<script src="<?php echo htmlspecialchars(edu_asset_url('includes/asep-language-selector.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
<script src="<?php echo htmlspecialchars(edu_asset_url('includes/training-proof.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
<script src="<?php echo htmlspecialchars(edu_asset_url('includes/eae-sensory-proof.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
<script>
(function(){"use strict";const $=id=>document.getElementById(id);const n=id=>Number($(id).value)||0;const fmt=v=>(Math.round((Number(v)||0)*100)/100).toLocaleString('el-GR',{minimumFractionDigits:2,maximumFractionDigits:2});const round2=v=>Math.round((v+Number.EPSILON)*100)/100;
function academic(){const languages=AsepLanguageSelector.calculate('asepLanguages');const type=$('titleType').value;const grade=n('degreeGrade');const valid=$('degreeGrade').value!==''&&grade>=10&&grade<=20;const rate=type==='postsecondary'?7.2:4;const cap=type==='postsecondary'?96:64;const degree=valid?round2(Math.max(0,grade-10)*rate):0;const raw=degree+($('secondTitle').checked?10:0)+languages.points+($('computer').checked?4:0)+($('training').checked?2:0);return{degree,raw,points:Math.min(raw,cap),cap,valid,languages};}
function service(){return AsepServiceController.getState('asepService',fmt);}
function social(){return AsepSocialCriteria.getState('socialCriteria',fmt);}
function render(){TrainingProof.syncAll();const a=academic(),s=service(),c=social(),total=a.points+s.points+c.points;const post=$('titleType').value==='postsecondary';$('gradeInfo').innerHTML=post?'Τίτλος μεταδευτεροβάθμιας: <strong>7,2 μόρια</strong> για κάθε βαθμό πάνω από 10 · ακαδημαϊκό όριο <strong>96</strong>.':'Τίτλος δευτεροβάθμιας: <strong>4 μόρια</strong> για κάθε βαθμό πάνω από 10 · ακαδημαϊκό όριο <strong>64</strong>.';$('gradeWarning').classList.toggle('hidden',$('degreeGrade').value===''||a.valid);$('academicSubtotal').textContent=fmt(a.points);$('totalPoints').textContent=fmt(total);$('degreePoints').textContent=fmt(a.degree);$('academicPoints').textContent=fmt(a.points)+(a.raw>a.cap?' (όριο '+a.cap+')':'');$('servicePoints').textContent=fmt(s.points)+(s.rawPoints>120?' (όριο 120)':'');$('socialPoints').textContent=fmt(c.points);$('academicCap').textContent=a.cap;const sensory=EaeSensoryProof.getState(),eng=!!(sensory.eng&&sensory.eng.selected),pb=$('priorityBox');pb.className='priority'+(eng?' yes':'');pb.textContent=eng?'Ειδική προτεραιότητα ΕΝΓ για υποστήριξη κωφών/βαρήκοων μαθητών':'Χωρίς δηλωμένη ειδική προτεραιότητα ΕΝΓ';return{a,s,c,total};}
function summary(r){return[`Υπολογισμός μορίων 1ΕΑ/2025 — ΔΕ01 ΕΒΠ`,`Βαθμός τίτλου: ${fmt(r.a.degree)}`,`Ακαδημαϊκά: ${fmt(r.a.points)}`,`Προϋπηρεσία: ${fmt(r.s.points)}`,`Κοινωνικά: ${fmt(r.c.points)}`,`ΣΥΝΟΛΟ: ${fmt(r.total)}`,EaeSensoryProof.summary(),TrainingProof.summary('trainingProof')].filter(Boolean).join('\n');}
EducationCore.bindBoundedNumberInput($('degreeGrade'),{min:10,max:20});
document.addEventListener('input',render);document.addEventListener('change',render);$('copyBtn').addEventListener('click',async()=>{const txt=summary(render());try{await navigator.clipboard.writeText(txt);$('copyBtn').textContent='Αντιγράφηκε';setTimeout(()=>$('copyBtn').textContent='Αντιγραφή',1200)}catch(e){alert(txt)}});$('resetBtn').addEventListener('click',()=>{document.querySelectorAll('input[type=number]').forEach(x=>x.value=x.id==='degreeGrade'?'':'0');document.querySelectorAll('input[type=text]').forEach(x=>x.value='');document.querySelectorAll('input[type=checkbox]').forEach(x=>x.checked=false);document.querySelectorAll('input[name="trainingProofDates"]').forEach(x=>x.checked=false);document.querySelectorAll('select').forEach(x=>x.selectedIndex=0);AsepServiceController.reset('asepService',{silent:true});AsepLanguageSelector.reset('asepLanguages',{silent:true});EaeSensoryProof.reset();render();});render();})();
</script>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
  <script src="<?php echo htmlspecialchars(edu_asset_url('includes/asep-computer-proof.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
  <script src="<?php echo htmlspecialchars(edu_asset_url('assets/common.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
</body>
</html>
