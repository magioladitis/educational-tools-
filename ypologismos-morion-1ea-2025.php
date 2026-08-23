<!DOCTYPE html>
<html lang="el">
<head>
<!-- UI consolidation v3.20: shared design system in assets/common.css?v=3.20.9"UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="Υπολογισμός μορίων για την προκήρυξη ΑΣΕΠ 1ΕΑ/2025 για μέλη Ειδικού Βοηθητικού Προσωπικού (ΕΒΠ) κλάδου ΔΕ01.">
<title>Υπολογισμός μορίων 1ΕΑ/2025</title>
<link rel="stylesheet" href="assets/common.css?v=3.20.9">
</head>
<body class="edu-ui edu-calc-standard edu-page-ea1">
<?php require_once __DIR__ . '/includes/header.php'; ?>
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
<h3>Ξένες γλώσσες — έως δύο</h3>
<div class="info">Επίλεξε <strong>ποια γλώσσα</strong> και το επίπεδό της. Αν δηλωθεί η ίδια γλώσσα δύο φορές, μοριοδοτείται αυτόματα μόνο το ανώτερο επίπεδο.</div>
<div class="field-grid">
<div class="field"><label for="langName1">1η ξένη γλώσσα</label><select id="langName1"><option value="">— Επιλογή γλώσσας —</option><option value="en">Αγγλική</option><option value="fr">Γαλλική</option><option value="de">Γερμανική</option><option value="it">Ιταλική</option><option value="es">Ισπανική</option><option value="other">Άλλη ξένη γλώσσα</option></select></div>
<div class="field"><label for="lang1">Επίπεδο 1ης γλώσσας</label><select id="lang1"><option value="0">Καμία / χωρίς μόρια</option><option value="4">Καλή — 4</option><option value="6">Πολύ καλή — 6</option><option value="8">Άριστη — 8</option></select></div>
<div class="field hidden" id="langOther1Wrap"><label for="langOther1">Ονομασία άλλης 1ης γλώσσας</label><input id="langOther1" type="text" placeholder="π.χ. Πορτογαλική"></div>
<div class="field"><label for="langName2">2η ξένη γλώσσα</label><select id="langName2"><option value="">— Επιλογή γλώσσας —</option><option value="en">Αγγλική</option><option value="fr">Γαλλική</option><option value="de">Γερμανική</option><option value="it">Ιταλική</option><option value="es">Ισπανική</option><option value="other">Άλλη ξένη γλώσσα</option></select></div>
<div class="field"><label for="lang2">Επίπεδο 2ης γλώσσας</label><select id="lang2"><option value="0">Καμία / χωρίς μόρια</option><option value="4">Καλή — 4</option><option value="6">Πολύ καλή — 6</option><option value="8">Άριστη — 8</option></select></div>
<div class="field hidden" id="langOther2Wrap"><label for="langOther2">Ονομασία άλλης 2ης γλώσσας</label><input id="langOther2" type="text" placeholder="π.χ. Πορτογαλική"></div>
</div>
<div id="languageWarning" class="note hidden"></div>
<div class="checkrow"><input type="checkbox" id="computer"><label for="computer">Πιστοποιημένη γνώση χειρισμού Η/Υ Α΄ επιπέδου<small>+4 μόρια.</small></label></div>
<div class="checkrow"><input type="checkbox" id="training"><label for="training">Επιμόρφωση ≥300 ωρών και ≥7 μηνών<small>ΑΕΙ ή εποπτευόμενος δημόσιος φορέας · +2 μόρια · μοριοδοτείται μία επιμόρφωση.</small></label></div>
<div class="training-proof hidden" id="trainingProof">
<div class="training-proof-title">Έλεγχος πιστοποιητικού σεμιναρίου</div>
<div class="training-proof-question">Στο πιστοποιητικό αναγράφονται η ημερομηνία έναρξης και η ημερομηνία λήξης του σεμιναρίου;</div>
<div class="segmented-choice" role="radiogroup" aria-label="Ημερομηνίες έναρξης και λήξης στο πιστοποιητικό">
<label><input type="radio" name="trainingDates" id="trainingDatesYes" value="yes"><span>✓ Ναι</span></label>
<label><input type="radio" name="trainingDates" id="trainingDatesNo" value="no"><span>Όχι</span></label>
</div>
<div class="training-proof-status neutral" id="trainingDatesStatus">Έλεγξε το πιστοποιητικό πριν την υποβολή των δικαιολογητικών.</div>
<small class="training-proof-legal">Σε περίπτωση που στο πιστοποιητικό δεν αναγράφεται η ημεροχρονολογία έναρξης και λήξης του σεμιναρίου, απαιτείται η προσκόμιση σχετικής βεβαίωσης από τον οικείο φορέα. <strong>Σε κάθε περίπτωση πρέπει να προκύπτει ολόκληρο το χρονικό διάστημα των 7 μηνών (6 μήνες και 29 ημέρες δεν γίνεται δεκτό).</strong></small>
</div>
<div class="subtot"><span>Ακαδημαϊκά</span><span class="pill" id="academicSubtotal">0,00</span></div>
</section>
<section class="card">
<h2>3. Εκπαιδευτική προϋπηρεσία</h2><p class="cap">Μέγιστο 120 μόρια. Μην δηλώνεις τον ίδιο μήνα σε περισσότερα από ένα πεδία.</p>
<div class="field-grid"><div class="field"><label for="publicMonths">Κανονική δημόσια προϋπηρεσία<small>1 μόριο/μήνα.</small></label><input id="publicMonths" class="service-months" type="number" min="0" step="1" value="0"></div><div class="field"><label for="hardMonths">Δυσπρόσιτα / καταστήματα κράτησης από 2020–21<small>2 μόρια/μήνα · έως 60 μήνες.</small></label><input id="hardMonths" class="service-months" type="number" min="0" max="60" step="1" value="0"></div></div>
<h3>Τρίμηνες συμβάσεις 2020–2021 και 2021–2022</h3>
<div class="field-grid"><div class="field"><label for="covid2020">Κανονικές 2020–2021<small>1,5/μήνα · έως 10 μόρια έτους.</small></label><input id="covid2020" class="service-months" type="number" min="0" step="1" value="0"></div><div class="field"><label for="covid2021">Κανονικές 2021–2022<small>1,5/μήνα · έως 10 μόρια έτους.</small></label><input id="covid2021" class="service-months" type="number" min="0" step="1" value="0"></div><div class="field"><label for="covidHard2020">Δυσπρόσιτες 2020–2021<small>3/μήνα · έως 20 μόρια έτους.</small></label><input id="covidHard2020" class="service-months" type="number" min="0" step="1" value="0"></div><div class="field"><label for="covidHard2021">Δυσπρόσιτες 2021–2022<small>3/μήνα · έως 20 μόρια έτους.</small></label><input id="covidHard2021" class="service-months" type="number" min="0" step="1" value="0"></div></div>
<div class="info">Η προϋπηρεσία αναπληρωτή ΤΕ01.30 Βοηθών Βρεφοκόμων–Παιδοκόμων προσμετράται και στον ΔΕ01-ΕΒΠ. Η ειδική μοριοδότηση ιδιωτικής σχολικής προϋπηρεσίας με συντελεστή 0,9 <strong>δεν αφορά τα μέλη ΕΕΠ-ΕΒΠ</strong>.</div>
<div id="serviceWarning" class="note hidden"></div>
<div class="subtot"><span>Προϋπηρεσία</span><span class="pill" id="serviceSubtotal">0,00</span></div>
</section>
<section class="card">
<h2>4. Κοινωνικά κριτήρια</h2>
<div class="field"><label for="children">Αριθμός μοριοδοτούμενων τέκνων<small>3 μόρια ανά τέκνο.</small></label><input id="children" type="number" min="0" step="1" value="0"></div>
<h3>Αναπηρία — λαμβάνεται μόνο το μεγαλύτερο επιλέξιμο ποσοστό</h3>
<div class="field-grid"><div class="field"><label for="candidateDisability">Υποψήφιος/α (%)</label><input id="candidateDisability" type="number" min="0" max="100" step="0.01" value="0"></div><div class="field"><label for="spouseDisability">Σύζυγος (%)</label><input id="spouseDisability" type="number" min="0" max="100" step="0.01" value="0"></div><div class="field"><label for="childDisability">Τέκνο (%)</label><input id="childDisability" type="number" min="0" max="100" step="0.01" value="0"></div></div>
<div class="checkrow"><input type="checkbox" id="marriage4"><label for="marriage4">Έγγαμος βίος τουλάχιστον 4 έτη<small>Απαιτείται για μοριοδότηση αναπηρίας συζύγου.</small></label></div>
<div class="checkrow"><input type="checkbox" id="mental"><label for="mental">Η αναπηρία του/της υποψηφίου οφείλεται, έστω κατά ποσοστό, σε ψυχική πάθηση<small>Σε αυτή την περίπτωση δεν μοριοδοτείται η αναπηρία του/της υποψηφίου.</small></label></div>
<div id="socialWarning" class="note hidden"></div><div class="subtot"><span>Κοινωνικά</span><span class="pill" id="socialSubtotal">0,00</span></div>
</section>
<section class="card">
<h2>5. Ειδική προτεραιότητα</h2>
<div class="checkrow"><input type="checkbox" id="signLanguage"><label for="signLanguage">Πιστοποιημένη επάρκεια στην Ελληνική Νοηματική Γλώσσα (ΕΝΓ)<small>Προτεραιότητα για την υποστήριξη κωφών και βαρήκοων μαθητών. Δεν προσθέτει μόρια.</small></label></div>
</section>
</main><aside class="results">
<section class="card"><h2>Αποτέλεσμα</h2><div class="total"><div class="num" id="totalPoints">0,00</div><div class="label">συνολικά μόρια</div></div><div class="result-row"><span>Βαθμός τίτλου</span><strong id="degreePoints">0,00</strong></div><div class="result-row"><span>Ακαδημαϊκά</span><strong id="academicPoints">0,00</strong></div><div class="result-row"><span>Προϋπηρεσία</span><strong id="servicePoints">0,00</strong></div><div class="result-row"><span>Κοινωνικά</span><strong id="socialPoints">0,00</strong></div><div class="result-row"><span>Όριο ακαδημαϊκών</span><strong id="academicCap">64</strong></div><div id="priorityBox" class="priority">Χωρίς δηλωμένη ειδική προτεραιότητα ΕΝΓ</div><div class="actions"><button type="button" id="copyBtn">Αντιγραφή</button><button type="button" class="secondary" id="resetBtn">Μηδενισμός</button></div></section>
</aside></div>
<div class="source"><strong>Πηγές / Νομική βάση:</strong><br><strong>Βάση υπολογισμού:</strong> Προκήρυξη ΑΣΕΠ 1ΕΑ/2025, Κεφάλαιο Γ΄ — κριτήρια αξιολογικού πίνακα Γ2΄ ΕΒΠ. Το εργαλείο είναι βοηθητικό και δεν υποκαθιστά τον επίσημο έλεγχο ΑΣΕΠ/ΟΠΣΥΔ.</div>
<div class="credits">Εργαλείο υπολογισμού μορίων · 1ΕΑ/2025</div>
</div>
<script src="includes/service-calculations.js"></script>
<script src="includes/social-calculations.js"></script>
<script src="includes/language-calculations.js"></script>
<script>
(function(){"use strict";const $=id=>document.getElementById(id);const n=id=>Number($(id).value)||0;const fmt=v=>(Math.round((Number(v)||0)*100)/100).toLocaleString('el-GR',{minimumFractionDigits:2,maximumFractionDigits:2});const round2=v=>Math.round((v+Number.EPSILON)*100)/100;
function languageResult(){return EducationLanguages.calculatePair([{language:$('langName1').value,otherText:$('langOther1').value,points:n('lang1')},{language:$('langName2').value,otherText:$('langOther2').value,points:n('lang2')}]);}
function syncLanguageUI(){const s1=$('langName1'),s2=$('langName2');$('langOther1Wrap').classList.toggle('hidden',s1.value!=='other');$('langOther2Wrap').classList.toggle('hidden',s2.value!=='other');[s1,s2].forEach(s=>Array.from(s.options).forEach(o=>o.disabled=false));if(s1.value&&s1.value!=='other'){const o=Array.from(s2.options).find(x=>x.value===s1.value);if(o)o.disabled=true;}if(s2.value&&s2.value!=='other'){const o=Array.from(s1.options).find(x=>x.value===s2.value);if(o)o.disabled=true;}}

function trainingDatesSelection(){const selected=document.querySelector('input[name="trainingDates"]:checked');return selected?selected.value:'';}
function updateTrainingProofUI(){const proof=$('trainingProof'),status=$('trainingDatesStatus');if(!proof||!status)return;const active=$('training').checked;proof.classList.toggle('hidden',!active);if(!active)return;const value=trainingDatesSelection();status.className='training-proof-status '+(value==='yes'?'success':value==='no'?'warning':'neutral');if(value==='yes')status.textContent='✓ Οι ημερομηνίες έναρξης και λήξης αναγράφονται στο πιστοποιητικό.';else if(value==='no')status.textContent='⚠️ Απαιτείται πρόσθετη βεβαίωση από τον οικείο φορέα με την ημερομηνία έναρξης και λήξης.';else status.textContent='Έλεγξε το πιστοποιητικό πριν την υποβολή των δικαιολογητικών.';}
function trainingProofSummary(){if(!$('training').checked)return'';const value=trainingDatesSelection();if(value==='yes')return'Πιστοποιητικό σεμιναρίου: αναγράφονται ημερομηνία έναρξης και λήξης.';if(value==='no')return'ΔΙΚΑΙΟΛΟΓΗΤΙΚΟ: απαιτείται βεβαίωση φορέα με ημερομηνία έναρξης και λήξης του σεμιναρίου.';return'Έλεγχος πιστοποιητικού σεμιναρίου: εκκρεμεί ο έλεγχος ημερομηνίας έναρξης και λήξης.';}
function academic(){syncLanguageUI();const languages=languageResult();const type=$('titleType').value;const grade=n('degreeGrade');const valid=$('degreeGrade').value!==''&&grade>=10&&grade<=20;const rate=type==='postsecondary'?7.2:4;const cap=type==='postsecondary'?96:64;const degree=valid?round2(Math.max(0,grade-10)*rate):0;const raw=degree+($('secondTitle').checked?10:0)+languages.points+($('computer').checked?4:0)+($('training').checked?2:0);return{degree,raw,points:Math.min(raw,cap),cap,valid,languages};}
function service(){const m=v=>Math.max(0,Math.floor(Number(v)||0));const tri=(v,rate,cap)=>({months:m(v),points:Math.min(m(v)*rate,cap)});const parts=[EducationService.regularPublic(n('publicMonths')),EducationService.difficult(n('hardMonths')),tri(n('covid2020'),1.5,10),tri(n('covid2021'),1.5,10),tri(n('covidHard2020'),3,20),tri(n('covidHard2021'),3,20)];const raw=parts.reduce((s,x)=>s+x.points,0);const months=parts.reduce((s,x)=>s+x.months,0);return{raw,points:Math.min(raw,120),months};}
function social(){return EducationSocial.calculate({children:n('children'),candidateDisability:n('candidateDisability'),spouseDisability:n('spouseDisability'),childDisability:n('childDisability'),candidateMentalCondition:$('mental').checked,marriageYears4Plus:$('marriage4').checked});}
function render(){updateTrainingProofUI();const a=academic(),s=service(),c=social(),total=a.points+s.points+c.total;const post=$('titleType').value==='postsecondary';$('gradeInfo').innerHTML=post?'Τίτλος μεταδευτεροβάθμιας: <strong>7,2 μόρια</strong> για κάθε βαθμό πάνω από 10 · ακαδημαϊκό όριο <strong>96</strong>.':'Τίτλος δευτεροβάθμιας: <strong>4 μόρια</strong> για κάθε βαθμό πάνω από 10 · ακαδημαϊκό όριο <strong>64</strong>.';$('gradeWarning').classList.toggle('hidden',$('degreeGrade').value===''||a.valid);$('academicSubtotal').textContent=fmt(a.points);$('serviceSubtotal').textContent=fmt(s.points);$('socialSubtotal').textContent=fmt(c.total);$('totalPoints').textContent=fmt(total);$('degreePoints').textContent=fmt(a.degree);$('academicPoints').textContent=fmt(a.points)+(a.raw>a.cap?' (όριο '+a.cap+')':'');$('servicePoints').textContent=fmt(s.points)+(s.raw>120?' (όριο 120)':'');$('socialPoints').textContent=fmt(c.total);$('academicCap').textContent=a.cap;const sw=$('serviceWarning');const msgs=[];if(s.months>120)msgs.push('Έχουν δηλωθεί '+s.months+' μήνες σε ξεχωριστές κατηγορίες. Έλεγξε ότι δεν έχει δηλωθεί ο ίδιος μήνας δύο φορές και ότι η συνολική πραγματική προϋπηρεσία δεν υπερβαίνει τους 120 μήνες που λαμβάνονται υπόψη.');if(s.raw>120)msgs.push('Η μοριοδότηση προϋπηρεσίας περιορίστηκε στο ανώτατο όριο των 120 μορίων.');sw.textContent=msgs.join(' ');sw.classList.toggle('hidden',msgs.length===0);const soc=$('socialWarning');soc.textContent=c.warnings.join(' ');soc.classList.toggle('hidden',c.warnings.length===0);const lw=$('languageWarning');lw.textContent=a.languages.warnings.join(' ');lw.classList.toggle('hidden',a.languages.warnings.length===0);const pb=$('priorityBox');pb.className='priority'+($('signLanguage').checked?' yes':'');pb.textContent=$('signLanguage').checked?'Ειδική προτεραιότητα ΕΝΓ για υποστήριξη κωφών/βαρήκοων μαθητών':'Χωρίς δηλωμένη ειδική προτεραιότητα ΕΝΓ';return{a,s,c,total};}
function summary(r){return[`Υπολογισμός μορίων 1ΕΑ/2025 — ΔΕ01 ΕΒΠ`,`Βαθμός τίτλου: ${fmt(r.a.degree)}`,`Ακαδημαϊκά: ${fmt(r.a.points)}`,`Προϋπηρεσία: ${fmt(r.s.points)}`,`Κοινωνικά: ${fmt(r.c.total)}`,`ΣΥΝΟΛΟ: ${fmt(r.total)}`,$('signLanguage').checked?'ΕΝΓ: δηλώθηκε ειδική προτεραιότητα':'ΕΝΓ: δεν δηλώθηκε',trainingProofSummary()].filter(Boolean).join('\n');}
document.addEventListener('input',render);document.addEventListener('change',render);$('copyBtn').addEventListener('click',async()=>{const txt=summary(render());try{await navigator.clipboard.writeText(txt);$('copyBtn').textContent='Αντιγράφηκε';setTimeout(()=>$('copyBtn').textContent='Αντιγραφή',1200)}catch(e){alert(txt)}});$('resetBtn').addEventListener('click',()=>{document.querySelectorAll('input[type=number]').forEach(x=>x.value=x.id==='degreeGrade'?'':'0');document.querySelectorAll('input[type=text]').forEach(x=>x.value='');document.querySelectorAll('input[type=checkbox]').forEach(x=>x.checked=false);document.querySelectorAll('input[name="trainingDates"]').forEach(x=>x.checked=false);document.querySelectorAll('select').forEach(x=>x.selectedIndex=0);render();});render();})();
</script>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
  <script src="assets/common.js?v=3.20.9"></script>
</body>
</html>
