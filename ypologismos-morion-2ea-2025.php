<!DOCTYPE html>
<html lang="el">
<head>
<!-- UI consolidation v3.20: shared design system in assets/common.css -->
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="Υπολογισμός μορίων για την προκήρυξη ΑΣΕΠ 2ΕΑ/2025 για μέλη Ειδικού Εκπαιδευτικού Προσωπικού (ΕΕΠ).">
<title>Υπολογισμός μορίων 2ΕΑ/2025</title>
<link rel="stylesheet" href="assets/common.css?v=3.20.17">
</head>
<body class="edu-ui edu-calc-standard edu-page-ea2">
<?php require_once __DIR__ . '/includes/header.php'; ?>
<?php require_once __DIR__ . '/includes/components/training-proof.php'; ?>
<?php require_once __DIR__ . '/includes/components/asep-language-selector.php'; ?>
<?php require_once __DIR__ . '/includes/components/asep-computer-proof.php'; ?>
<?php require_once __DIR__ . '/includes/components/asep-social-criteria.php'; ?>
<?php require_once __DIR__ . '/includes/components/asep-three-month-service.php'; ?>
<?php require_once __DIR__ . '/includes/components/eae-sensory-priority.php'; ?>
<div class="app">
<section class="hero"><h1>Υπολογισμός μορίων 2ΕΑ/2025</h1><p>Ενδεικτικός υπολογισμός για τον αξιολογικό πίνακα Γ1΄ μελών <strong>Ειδικού Εκπαιδευτικού Προσωπικού (Ε.Ε.Π.)</strong>.</p><div class="meta"><span>2ΕΑ/2025</span><span>ΠΕ21–ΠΕ31</span><span>Ακαδημαϊκά έως 120</span><span>Προϋπηρεσία έως 120</span><span>Έλεγχος πρόταξης</span><span>Άδειες &amp; βεβαιώσεις</span></div></section>
<div class="layout"><main>
<section class="card"><h2>1. Κλάδος και πρόταξη</h2><p class="cap">Ο κλάδος δεν αλλάζει τον βασικό πίνακα μοριοδότησης, αλλά ενεργοποιεί ειδικές επισημάνσεις και κανόνες πρόταξης.</p>
<div class="field"><label for="branch">Κλάδος ΕΕΠ</label><select id="branch"><option value="">— Επιλογή —</option><option value="PE21">ΠΕ21 — Θεραπευτών Λόγου</option><option value="PE22">ΠΕ22 — Επαγγελματικών Συμβούλων</option><option value="PE23">ΠΕ23 — Ψυχολόγων</option><option value="PE25">ΠΕ25 — Σχολικών Νοσηλευτών</option><option value="PE28">ΠΕ28 — Φυσιοθεραπευτών</option><option value="PE29">ΠΕ29 — Εργασιοθεραπευτών–Εργοθεραπευτών</option><option value="PE30">ΠΕ30 — Κοινωνικών Λειτουργών</option><option value="PE31">ΠΕ31 — Εξειδικευμένου</option></select></div>
<div id="branchNote" class="info">Επίλεξε κλάδο για να εμφανιστούν οι ειδικές επισημάνσεις.</div>
<div class="checkrow"><input type="checkbox" id="pedagogical"><label for="pedagogical">Πιστοποιημένη Παιδαγωγική και Διδακτική Επάρκεια<small>Δεν προσθέτει μόρια, αλλά αποτελεί κριτήριο πρόταξης.</small></label></div>
<div id="pe23Wrap" class="field hidden"><label for="schoolPsych">ΠΕ23 — Εξειδίκευση στη Σχολική Ψυχολογία<small>Προσόν πρόταξης, όχι ξεχωριστή μοριοδότηση από μόνο του.</small></label><select id="schoolPsych"><option value="none">Δεν διαθέτω εξειδίκευση</option><option value="degree">Διδακτορικό ή μεταπτυχιακό στη Σχολική Ψυχολογία</option><option value="experience">Τουλάχιστον 50 μήνες προϋπηρεσίας ως ψυχολόγος στην Π/θμια ή Δ/θμια</option></select><div id="schoolPsychReminder" class="note hidden">Η επιλογή εξειδίκευσης μέσω τίτλου δεν προσθέτει αυτόματα μόρια. Δήλωσε τον αντίστοιχο διδακτορικό/μεταπτυχιακό τίτλο και στην ενότητα Ακαδημαϊκών.</div></div>
<?php
renderEaeSensoryPriority(array(
    'context' => '2ea-2025',
    'eng_enabled' => true,
    'braille_enabled' => true,
    'eng_id' => 'signLanguage',
    'braille_id' => 'braille'
));
?>
</section>
<section class="card" id="licenseCard"><h2>2. Υποχρεωτικές άδειες &amp; βεβαιώσεις συμμετοχής</h2><p class="cap">Δεν μοριοδοτούνται, αλλά όπου προβλέπονται αποτελούν πρόσθετα τυπικά προσόντα ένταξης του κλάδου.</p>
<div id="licenseIntro" class="info">Επίλεξε κλάδο για να εμφανιστούν τα απαιτούμενα πρόσθετα επαγγελματικά δικαιολογητικά.</div>
<div id="pe25RouteWrap" class="field hidden"><label for="pe25Route">ΠΕ25 — Διαδρομή βασικού επαγγελματικού τίτλου<small>Η απαιτούμενη άδεια και η επαγγελματική εγγραφή διαφέρουν ανάλογα με τον τίτλο.</small></label><select id="pe25Route"><option value="">— Επιλογή διαδρομής —</option><option value="nursing">Τίτλος Νοσηλευτικής</option><option value="health_visitor">Τίτλος Επισκέπτη/Επισκέπτριας Υγείας</option></select></div>
<div id="licenseRequirements"></div>
<div id="licenseStatus" class="priority">Δεν έχει επιλεγεί κλάδος.</div>
<div class="note"><strong>Σημαντικό:</strong> ο έλεγχος αυτός αφορά μόνο τις ειδικές άδειες, βεβαιώσεις και εγγραφές σε επαγγελματικούς φορείς. Δεν επιβεβαιώνει τον βασικό τίτλο σπουδών ή όλα τα λοιπά τυπικά προσόντα της προκήρυξης. Τα απαιτούμενα προσόντα πρέπει να υφίστανται έως τη λήξη της προθεσμίας των αιτήσεων.</div>
</section>
<section class="card"><h2>3. Ακαδημαϊκά προσόντα</h2><p class="cap">Μέγιστο κατηγορίας Α: 120 μόρια.</p>
<div class="field"><label for="degreeGrade">Βαθμός βασικού πτυχίου (5–10)<small>Βαθμός × 2,5 · ανώτατο 25. Αν η κλίμακα είναι διαφορετική, γίνεται αναγωγή στην 10βάθμια.</small></label><input id="degreeGrade" type="number" min="5" max="10" step="0.01" inputmode="decimal" placeholder="π.χ. 7,84"></div><div id="gradeWarning" class="warning hidden">Ο βαθμός πρέπει να είναι από 5,00 έως 10,00.</div>
<div class="checkrow"><input type="checkbox" id="secondDegree"><label for="secondDegree">Δεύτερο πτυχίο ΑΕΙ<small>+7 μόρια, εφόσον δεν αποτελεί τυπικό προσόν διορισμού.</small></label></div>
<div class="checkrow"><input type="checkbox" id="phd"><label for="phd">Μοριοδοτούμενο διδακτορικό δίπλωμα<small>+40 μόρια.</small></label></div>
<div class="field"><label for="masters">Μοριοδοτούμενοι μεταπτυχιακοί τίτλοι / integrated master<small>1 τίτλος: 20 · 2 ή περισσότεροι: 28 συνολικά. Μην συμπεριλάβεις τίτλο που αποτελεί τυπικό προσόν διορισμού.</small></label><select id="masters"><option value="0">Κανένας — 0</option><option value="1">Ένας — 20</option><option value="2">Δύο ή περισσότεροι — 28</option></select></div>
<div id="formalMasterNote" class="note hidden"><strong>Προσοχή:</strong> για ΠΕ21, ΠΕ22 και ΠΕ31, όταν μεταπτυχιακός τίτλος αποτελεί τυπικό προσόν διορισμού στη συγκεκριμένη διαδρομή ένταξης, ο τίτλος αυτός δεν μοριοδοτείται. Δήλωσε παραπάνω μόνο τους τίτλους που πράγματι μοριοδοτούνται.</div>
<?php
renderAsepLanguageSelector(array(
    'id' => 'asepLanguages',
    'profile' => 'eep'
));
?>
<?php
renderAsepComputerProof(array(
    'input_id' => 'computer',
    'control_type' => 'checkbox',
    'points_text' => '4 μόρια'
));
?><div class="checkrow"><input type="checkbox" id="training"><label for="training">Επιμόρφωση ≥300 ωρών και ≥7 μηνών<small>+2 μόρια · μοριοδοτείται μία επιμόρφωση.</small></label></div>
<?php
renderTrainingProof([
    'id' => 'trainingProof',
    'radio_name' => 'trainingDates',
    'yes_id' => 'trainingDatesYes',
    'no_id' => 'trainingDatesNo',
    'status_id' => 'trainingDatesStatus',
    'context' => '2ea-2025-300h-7m',
    'legal_html' => <<<'HTML'
Σε περίπτωση που στο πιστοποιητικό δεν αναγράφεται η ημεροχρονολογία έναρξης και λήξης του σεμιναρίου, απαιτείται η προσκόμιση σχετικής βεβαίωσης από τον οικείο φορέα. <strong>Σε κάθε περίπτωση πρέπει να προκύπτει ολόκληρο το χρονικό διάστημα των 7 μηνών (6 μήνες και 29 ημέρες δεν γίνεται δεκτό).</strong>
HTML
]);
?>
<div class="subtot"><span>Ακαδημαϊκά</span><span class="pill" id="academicSubtotal">0,00</span></div></section>
<section class="card"><h2>4. Εκπαιδευτική προϋπηρεσία</h2><p class="cap">Μέγιστο 120 μόρια. Μην δηλώνεις τον ίδιο μήνα σε περισσότερα από ένα πεδία.</p>
<div class="field-grid"><div class="field"><label for="publicMonths">Κανονική δημόσια προϋπηρεσία<small>1 μόριο/μήνα · έως 120 μήνες.</small></label><input id="publicMonths" class="service-months" type="number" min="0" max="120" step="1" value="0"></div><div class="field"><label for="hardMonths">Δυσπρόσιτα / καταστήματα κράτησης από 2020–21<small>2 μόρια/μήνα · έως 60 μήνες.</small></label><input id="hardMonths" class="service-months" type="number" min="0" max="60" step="1" value="0"></div></div>
<?php
renderAsepThreeMonthService(array(
    'regular_2020_id' => 'covid2020',
    'difficult_2020_id' => 'covidHard2020',
    'regular_2021_id' => 'covid2021',
    'difficult_2021_id' => 'covidHard2021'
));
?>
<div id="serviceBranchNote" class="info">Η ειδική μοριοδότηση ιδιωτικής σχολικής προϋπηρεσίας με συντελεστή 0,9 δεν αφορά τα μέλη ΕΕΠ-ΕΒΠ.</div><div id="serviceWarning" class="note hidden"></div><div class="subtot"><span>Προϋπηρεσία</span><span class="pill" id="serviceSubtotal">0,00</span></div></section>
<?php
renderAsepSocialCriteria(array(
    'title' => '5. Κοινωνικά κριτήρια',
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
</main><aside class="results"><section class="card"><h2>Αποτέλεσμα</h2><div class="total"><div class="num" id="totalPoints">0,00</div><div class="label">συνολικά μόρια</div></div><div class="result-row"><span>Βαθμός πτυχίου</span><strong id="degreePoints">0,00</strong></div><div class="result-row"><span>Ακαδημαϊκά</span><strong id="academicPoints">0,00</strong></div><div class="result-row"><span>Προϋπηρεσία</span><strong id="servicePoints">0,00</strong></div><div class="result-row"><span>Κοινωνικά</span><strong id="socialPoints">0,00</strong></div><div class="result-row"><span>Άδειες / βεβαιώσεις</span><strong id="licenseResult">—</strong></div><div id="priorityBox" class="priority">Επίλεξε κλάδο για έλεγχο πρόταξης</div><div id="specialPriorityBox" class="priority hidden"></div><div class="actions"><button type="button" id="copyBtn">Αντιγραφή</button><button type="button" class="secondary" id="resetBtn">Μηδενισμός</button></div></section></aside></div>
<section class="edu-source-card" aria-labelledby="sourcesTitle"><h2 id="sourcesTitle">Πηγές / Νομική βάση</h2><p><strong>Βάση υπολογισμού:</strong> Προκήρυξη ΑΣΕΠ 2ΕΑ/2025, Κεφάλαιο Β΄ — τυπικά προσόντα ένταξης, Κεφάλαιο Δ΄ παρ. 10 — Άδειες και Βεβαιώσεις, και Κεφάλαιο Γ΄ — κριτήρια αξιολογικού πίνακα Γ1΄ ΕΕΠ.</p><div class="source-links"><a href="https://info.asep.gr/node/76177" target="_blank" rel="noopener noreferrer">2ΕΑ/2025 — ΑΣΕΠ ↗</a></div><p class="source-disclaimer">Οι ενδείξεις «πρόταξης» είναι βοηθητικές και δεν αποτελούν επιπλέον μόρια.</p></section><div class="credits">Εργαλείο υπολογισμού μορίων · 2ΕΑ/2025</div></div>
<script src="includes/service-calculations.js?v=3.20.14-rc2"></script><script src="includes/social-calculations.js"></script><script src="includes/language-calculations.js?v=3.20.24"></script>
<script src="includes/asep-language-selector.js?v=3.20.24"></script><script src="includes/eep-eligibility-calculations.js"></script><script src="includes/training-proof.js?v=3.20.18"></script>
<script>
(function(){"use strict";const $=id=>document.getElementById(id);const n=id=>Number($(id).value)||0;const fmt=v=>(Math.round((Number(v)||0)*100)/100).toLocaleString('el-GR',{minimumFractionDigits:2,maximumFractionDigits:2});
function academic(){const languages=AsepLanguageSelector.calculate('asepLanguages');const grade=n('degreeGrade');const valid=$('degreeGrade').value!==''&&grade>=5&&grade<=10;const degree=valid?Math.round(grade*2.5*100)/100:0;const masters=n('masters')===1?20:n('masters')>=2?28:0;const raw=degree+($('secondDegree').checked?7:0)+($('phd').checked?40:0)+masters+languages.points+($('computer').checked?4:0)+($('training').checked?2:0);return{degree,raw,points:Math.min(raw,120),valid,masters,languages};}
function service(){const parts=[EducationService.regularPublic(n('publicMonths')),EducationService.difficult(n('hardMonths')),EducationService.threeMonthRegular2020(n('covid2020')),EducationService.threeMonthRegular2021(n('covid2021')),EducationService.threeMonthDifficult2020(n('covidHard2020')),EducationService.threeMonthDifficult2021(n('covidHard2021'))];const raw=parts.reduce((s,x)=>s+x.points,0),months=parts.reduce((s,x)=>s+x.months,0);return{raw,points:Math.min(raw,120),months};}
function social(){return EducationSocial.calculate({children:n('children'),candidateDisability:n('candidateDisability'),spouseDisability:n('spouseDisability'),childDisability:n('childDisability'),candidateMentalCondition:$('mental').checked,marriageYears4Plus:$('marriage4').checked});}
let eligibilityUiKey='';
function selectedEligibilityIds(){return Array.from(document.querySelectorAll('#licenseRequirements input[data-license-id]:checked')).map(x=>x.dataset.licenseId);}
function syncEligibilityUI(){const b=$('branch').value;const route=b==='PE25'?$('pe25Route').value:'';$('pe25RouteWrap').classList.toggle('hidden',b!=='PE25');const key=b+'|'+route;if(key===eligibilityUiKey)return;eligibilityUiKey=key;const host=$('licenseRequirements'),intro=$('licenseIntro');host.innerHTML='';if(!b){intro.textContent='Επίλεξε κλάδο για να εμφανιστούν τα απαιτούμενα πρόσθετα επαγγελματικά δικαιολογητικά.';return;}const req=EEPEligibility.getRequirements(b,route);if(req.routeRequired&&!req.route){intro.innerHTML='<strong>'+req.label+':</strong> '+req.note;return;}if(req.items.length===0){intro.innerHTML='<strong>'+req.label+':</strong> '+req.note;return;}intro.innerHTML='<strong>'+req.label+(req.routeLabel?' — '+req.routeLabel:'')+':</strong> έλεγξε ότι διαθέτεις όλα τα παρακάτω δικαιολογητικά.';req.items.forEach((item,i)=>{const row=document.createElement('div');row.className='checkrow';const id='licenseReq_'+i;row.innerHTML='<input type="checkbox" id="'+id+'" data-license-id="'+item.id+'"><label for="'+id+'">'+item.label+'<small>Υποχρεωτικό πρόσθετο δικαιολογητικό — δεν προσθέτει μόρια.</small></label>';host.appendChild(row);});}
function eligibility(){syncEligibilityUI();const b=$('branch').value,route=b==='PE25'?$('pe25Route').value:'';return EEPEligibility.evaluate(b,route,selectedEligibilityIds());}
function renderEligibility(){const e=eligibility(),box=$('licenseStatus'),side=$('licenseResult');box.className='priority';if(e.status==='unselected'){box.textContent='Δεν έχει επιλεγεί κλάδος.';side.textContent='—';}else if(e.status==='route-required'){box.textContent='ΠΕ25 — επίλεξε διαδρομή τίτλου για να ελεγχθούν τα σωστά δικαιολογητικά.';side.textContent='ΕΚΚΡΕΜΕΙ';}else if(e.status==='not-applicable'){box.className='priority yes';box.textContent='Δεν προβλέπεται ειδική άδεια/εγγραφή επαγγελματικού φορέα για τον επιλεγμένο κλάδο στην ενότητα «Άδειες και Βεβαιώσεις».';side.textContent='ΔΕΝ ΑΠΑΙΤΕΙΤΑΙ';}else if(e.complete){box.className='priority yes';box.textContent='✓ Δηλώθηκαν όλα τα απαιτούμενα πρόσθετα δικαιολογητικά ('+e.checked+'/'+e.required+').';side.textContent='ΠΛΗΡΗ';}else{box.className='priority';box.textContent='Λείπουν '+e.missing.length+' από '+e.required+' απαιτούμενα πρόσθετα δικαιολογητικά.';side.textContent='ΕΛΛΙΠΗ '+e.checked+'/'+e.required;}return e;}
function branchUI(){const b=$('branch').value,bc=b?b.replace(/^PE/,'ΠΕ'):'';const note=$('branchNote'),sn=$('serviceBranchNote');$('pe23Wrap').classList.toggle('hidden',b!=='PE23');$('formalMasterNote').classList.toggle('hidden',!['PE21','PE22','PE31'].includes(b));let t='Επίλεξε κλάδο για να εμφανιστούν οι ειδικές επισημάνσεις.';if(b==='PE23')t='<strong>ΠΕ23:</strong> η εξειδίκευση στη Σχολική Ψυχολογία δημιουργεί πρόταξη. Η Παιδαγωγική/Διδακτική Επάρκεια καθορίζει περαιτέρω τη σειρά μέσα στις ομάδες πρόταξης.';else if(b==='PE25')t='<strong>ΠΕ25:</strong> προϋπηρεσία αναπληρωτή εκπαιδευτικού ΠΕ87.02-Νοσηλευτικής που αποκτήθηκε με τις ειδικές διατάξεις της προκήρυξης προσμετράται και στον ΠΕ25.';else if(['PE21','PE22'].includes(b))t='<strong>'+bc+':</strong> όταν μεταπτυχιακός τίτλος αποτελεί τυπικό προσόν διορισμού, δεν μοριοδοτείται και η προϋπηρεσία προσμετράται μετά την κτήση του.';else if(b==='PE31')t='<strong>ΠΕ31:</strong> όταν μεταπτυχιακός τίτλος αποτελεί τυπικό προσόν διορισμού, δεν μοριοδοτείται και η προϋπηρεσία προσμετράται μετά την κτήση του. Επαγγελματική εμπειρία τριών ετών που αποτελεί τυπικό προσόν δεν μοριοδοτείται.';else if(b)t='Για τον '+bc+' εφαρμόζεται ο κοινός πίνακας μοριοδότησης ΕΕΠ. Η Παιδαγωγική/Διδακτική Επάρκεια αποτελεί κριτήριο πρόταξης.';note.innerHTML=t;let st='Η ειδική μοριοδότηση ιδιωτικής σχολικής προϋπηρεσίας με συντελεστή 0,9 δεν αφορά τα μέλη ΕΕΠ-ΕΒΠ.';if(b==='PE25')st+='<br><strong>ΠΕ25:</strong> έλεγξε και τυχόν επιλέξιμη προϋπηρεσία ΠΕ87.02-Νοσηλευτικής.';if(['PE21','PE22','PE31'].includes(b))st+='<br><strong>'+bc+':</strong> αν ο μεταπτυχιακός είναι τυπικό προσόν διορισμού, δήλωσε μόνο προϋπηρεσία μετά την κτήση του.';if(b==='PE31')st+='<br>Μην περιλάβεις την τριετή επαγγελματική εμπειρία όταν αυτή αποτελεί τυπικό προσόν διορισμού.';sn.innerHTML=st;$('schoolPsychReminder').classList.toggle('hidden',!(b==='PE23'&&$('schoolPsych').value==='degree'));syncEligibilityUI();}
function priority(){const b=$('branch').value,ped=$('pedagogical').checked,pb=$('priorityBox');pb.className='priority';if(!b){pb.textContent='Επίλεξε κλάδο για έλεγχο πρόταξης';}else if(b==='PE23'){const spec=$('schoolPsych').value!=='none';pb.className='priority '+(spec?'special':ped?'yes':'');if(spec&&ped)pb.textContent='ΠΕ23 — Ομάδα πρόταξης 1: εξειδίκευση Σχολικής Ψυχολογίας + ΠΔΕ';else if(spec)pb.textContent='ΠΕ23 — Ομάδα πρόταξης 2: εξειδίκευση Σχολικής Ψυχολογίας χωρίς ΠΔΕ';else if(ped)pb.textContent='ΠΕ23 — Ομάδα πρόταξης 3: χωρίς εξειδίκευση, με ΠΔΕ';else pb.textContent='ΠΕ23 — Ομάδα 4: χωρίς εξειδίκευση και χωρίς ΠΔΕ';}else{pb.className='priority'+(ped?' yes':'');pb.textContent=ped?'Πρόταξη λόγω Παιδαγωγικής και Διδακτικής Επάρκειας':'Χωρίς δηλωμένη πρόταξη Παιδαγωγικής/Διδακτικής Επάρκειας';}const sp=$('specialPriorityBox');const extras=[];if($('braille').checked)extras.push('Braille — προτεραιότητα για μαθητές με προβλήματα όρασης');if($('signLanguage').checked)extras.push('ΕΝΓ — προτεραιότητα για κωφούς/βαρήκοους μαθητές');sp.textContent=extras.join(' · ');sp.className='priority special'+(extras.length?'':' hidden');}
function render(){TrainingProof.syncAll();branchUI();const a=academic(),s=service(),c=social(),total=a.points+s.points+c.total;$('gradeWarning').classList.toggle('hidden',$('degreeGrade').value===''||a.valid);$('academicSubtotal').textContent=fmt(a.points);$('serviceSubtotal').textContent=fmt(s.points);$('socialSubtotal').textContent=fmt(c.total);$('totalPoints').textContent=fmt(total);$('degreePoints').textContent=fmt(a.degree);$('academicPoints').textContent=fmt(a.points)+(a.raw>120?' (όριο 120)':'');$('servicePoints').textContent=fmt(s.points)+(s.raw>120?' (όριο 120)':'');$('socialPoints').textContent=fmt(c.total);const sw=$('serviceWarning');const msgs=[];if(s.months>120)msgs.push('Έχουν δηλωθεί '+s.months+' μήνες σε ξεχωριστές κατηγορίες. Έλεγξε ότι δεν έχει δηλωθεί ο ίδιος μήνας δύο φορές και ότι η συνολική πραγματική προϋπηρεσία δεν υπερβαίνει τους 120 μήνες που λαμβάνονται υπόψη.');if(s.raw>120)msgs.push('Η μοριοδότηση προϋπηρεσίας περιορίστηκε στο ανώτατο όριο των 120 μορίων.');sw.textContent=msgs.join(' ');sw.classList.toggle('hidden',msgs.length===0);const soc=$('socialWarning');soc.textContent=c.warnings.join(' ');soc.classList.toggle('hidden',c.warnings.length===0);priority();const e=renderEligibility();return{a,s,c,e,total};}
function summary(r){const branch=$('branch'),branchText=branch.value?branch.options[branch.selectedIndex].textContent.trim():'κλάδος μη επιλεγμένος';return[`Υπολογισμός μορίων 2ΕΑ/2025 — ${branchText}`,`Βαθμός πτυχίου: ${fmt(r.a.degree)}`,`Ακαδημαϊκά: ${fmt(r.a.points)}`,`Προϋπηρεσία: ${fmt(r.s.points)}`,`Κοινωνικά: ${fmt(r.c.total)}`,`ΣΥΝΟΛΟ: ${fmt(r.total)}`,`Άδειες/βεβαιώσεις: ${$('licenseResult').textContent}`,`Πρόταξη: ${$('priorityBox').textContent}${$('specialPriorityBox').classList.contains('hidden')?'':'\nΕιδική προτεραιότητα: '+$('specialPriorityBox').textContent}`,TrainingProof.summary('trainingProof')].filter(Boolean).join('\n');}
function sanitizeServiceMonthInput(el){if(!el||!el.classList.contains('service-months')||el.value==='')return;let value=Math.max(0,Math.floor(Number(el.value)||0));const max=el.getAttribute('max');if(max!==null&&max!=='')value=Math.min(value,Number(max));el.value=String(value);}
document.addEventListener('input',e=>{sanitizeServiceMonthInput(e.target);render();});document.addEventListener('change',render);$('copyBtn').addEventListener('click',async()=>{const txt=summary(render());try{await navigator.clipboard.writeText(txt);$('copyBtn').textContent='Αντιγράφηκε';setTimeout(()=>$('copyBtn').textContent='Αντιγραφή',1200)}catch(e){alert(txt)}});$('resetBtn').addEventListener('click',()=>{document.querySelectorAll('input[type=number]').forEach(x=>x.value=x.id==='degreeGrade'?'':'0');document.querySelectorAll('input[type=text]').forEach(x=>x.value='');document.querySelectorAll('input[type=checkbox]').forEach(x=>x.checked=false);document.querySelectorAll('input[name="trainingDates"]').forEach(x=>x.checked=false);document.querySelectorAll('select').forEach(x=>x.selectedIndex=0);AsepLanguageSelector.reset('asepLanguages',{silent:true});eligibilityUiKey='';render();});render();})();
</script>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
  <script src="includes/asep-computer-proof.js?v=3.20.15-rc2"></script>
  <script src="includes/eae-sensory-proof.js?v=3.20.23"></script>
  <script src="assets/common.js?v=3.20.13"></script>
</body>
</html>
