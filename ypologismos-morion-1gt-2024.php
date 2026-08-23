<!DOCTYPE html>
<html lang="el">
<head>
<!-- UI consolidation v3.20: shared design system in assets/common.css?v=3.20.9"UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="Υπολογισμός μορίων για την προκήρυξη ΑΣΕΠ 1ΓΤ/2024 για τους κλάδους ΤΕ01, ΤΕ02 και ΤΕ16.">
  <title>Υπολογισμός μορίων 1ΓΤ/2024</title>
<link rel="stylesheet" href="assets/common.css?v=3.20.9">
</head>
<body class="edu-ui edu-calc-standard edu-page-gt1">
<?php require_once __DIR__ . '/includes/header.php'; ?>
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
          <div class="field">
            <label for="gradeScale">Κλίμακα βαθμού τίτλου</label>
            <select id="gradeScale">
              <option value="20">Κλίμακα 10–20</option>
              <option value="10">Κλίμακα 5–10</option>
              <option value="te16text">ΤΕ16 — περιγραφικός βαθμός</option>
            </select>
          </div>
        </div>

        <div id="numericGradeWrap" class="field">
          <label for="degreeGrade">Βαθμός βασικού τίτλου
            <small>Ο βαθμός ανάγεται σε κλίμακα 20 και πολλαπλασιάζεται ×3. Μέγιστο: 60 μόρια.</small>
          </label>
          <input type="number" id="degreeGrade" min="10" max="20" step="0.01" value="" placeholder="π.χ. 15,00">
        </div>

        <div id="te16TextWrap" class="field hidden">
          <label for="te16TextGrade">Χαρακτηρισμός βαθμού ΤΕ16</label>
          <select id="te16TextGrade">
            <option value="0">Δεν αναγράφεται βαθμολογία → 5,00</option>
            <option value="5">ΚΑΛΩΣ → 5,00</option>
            <option value="6.5">ΛΙΑΝ ΚΑΛΩΣ → 6,50</option>
            <option value="8.5">ΑΡΙΣΤΑ → 8,50</option>
          </select>
          <div class="help">Οι τιμές 5,00 / 6,50 / 8,50 αναφέρονται στην κλίμακα 10 και ο υπολογιστής τις ανάγει αυτόματα σε κλίμακα 20.</div>
        </div>

        <div class="info-note" id="normalizedGradeInfo">Αναγμένος βαθμός: 0,00 / 20 · Μόρια βαθμού: 0,00 / 60</div>
        <div id="gradeWarning" class="warning hidden">Ο βαθμός δεν βρίσκεται στα επιτρεπτά όρια της επιλεγμένης κλίμακας.</div>
      </section>

      <section class="card">
        <h2>Α. Ακαδημαϊκά προσόντα</h2>
        <p class="cap">Μέγιστο κατηγορίας: 120 μόρια</p>

        <div class="checkrow">
          <input type="checkbox" id="secondTitle">
          <label for="secondTitle"><span id="secondTitleLabel">Πτυχίο επιπέδου 5 / Ι.Ε.Κ. ίδιας ειδικότητας</span><small>10 μόρια</small></label>
        </div>

        <h3>Ξένη γλώσσα — μοριοδοτείται μία</h3>
        <div class="info-note">Επίλεξε <strong>ποια γλώσσα</strong> και το επίπεδό της. Η 1ΓΤ/2024 μοριοδοτεί μία μόνο ξένη γλώσσα και, αν υπάρχουν περισσότεροι τίτλοι της ίδιας γλώσσας, λαμβάνεται μόνο ο ανώτερος.</div>
        <div class="field-grid">
          <div class="field">
            <label for="langName">Ξένη γλώσσα</label>
            <select id="langName">
              <option value="">— Επιλογή γλώσσας —</option>
              <option value="en">Αγγλική</option><option value="fr">Γαλλική</option><option value="de">Γερμανική</option>
              <option value="it">Ιταλική</option><option value="es">Ισπανική</option><option value="other">Άλλη ξένη γλώσσα</option>
            </select>
          </div>
          <div class="field">
            <label for="langLevel">Επίπεδο</label>
            <select id="langLevel">
              <option value="0">Καμία / χωρίς μόρια</option>
              <option value="10">Καλή γνώση — 10 μόρια</option>
              <option value="15">Πολύ καλή γνώση — 15 μόρια</option>
              <option value="20">Άριστη γνώση — 20 μόρια</option>
            </select>
          </div>
          <div class="field hidden" id="langOtherWrap">
            <label for="langOther">Ονομασία άλλης ξένης γλώσσας</label>
            <input id="langOther" type="text" placeholder="π.χ. Πορτογαλική">
          </div>
        </div>
        <div id="languageWarning" class="note hidden"></div>

        <div class="checkrow">
          <input type="checkbox" id="computer">
          <label for="computer">Πιστοποιημένη γνώση χειρισμού Η/Υ Α΄ επιπέδου<small>Επεξεργασία κειμένου, υπολογιστικά φύλλα και υπηρεσίες διαδικτύου — 20 μόρια</small></label>
        </div>

        <div class="checkrow">
          <input type="checkbox" id="training">
          <label for="training">Επιμόρφωση ≥300 ωρών και διάρκειας ≥7 μηνών<small>Α.Ε.Ι. ή εποπτευόμενος δημόσιος φορέας — μοριοδοτείται μία επιμόρφωση — 10 μόρια</small></label>
        </div>

        <div class="training-proof hidden" id="trainingProof">
          <div class="training-proof-title">Έλεγχος πιστοποιητικού σεμιναρίου</div>
          <div class="training-proof-question">Στο πιστοποιητικό αναγράφονται η ημερομηνία έναρξης και η ημερομηνία λήξης του σεμιναρίου;</div>
          <div class="segmented-choice" role="radiogroup" aria-label="Ημερομηνίες έναρξης και λήξης στο πιστοποιητικό">
            <label><input type="radio" name="trainingDates" id="trainingDatesYes" value="yes"><span>✓ Ναι</span></label>
            <label><input type="radio" name="trainingDates" id="trainingDatesNo" value="no"><span>Όχι</span></label>
          </div>
          <div class="training-proof-status neutral" id="trainingDatesStatus">Έλεγξε το πιστοποιητικό πριν την υποβολή των δικαιολογητικών.</div>
          <small class="training-proof-legal">Σε περίπτωση που στο πιστοποιητικό δεν αναγράφεται η ημεροχρονολογία έναρξης και λήξης του σεμιναρίου, απαιτείται η προσκόμιση σχετικής βεβαίωσης από τον οικείο φορέα. <strong>Πρέπει να προκύπτει ολόκληρο το χρονικό διάστημα των 7 μηνών· 6 μήνες και 29 ημέρες δεν γίνονται δεκτοί.</strong></small>
        </div>

        <div class="subtot"><span>Σύνολο Ακαδημαϊκών</span><span class="pill" id="academicSubtotal">0,00 / 120</span></div>
      </section>

      <section class="card">
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
            <small>1 μόριο ανά μήνα. Εδώ μπορεί να συμπεριληφθεί και αναγνωρισμένη ιδιωτική προϋπηρεσία που πληροί τις προϋποθέσεις της προκήρυξης.</small>
          </label>
          <input type="number" id="regularMonths" min="0" step="1" value="0">
        </div>

        <div class="field">
          <label for="difficultMonths">Δυσπρόσιτα / σχολικές μονάδες σε καταστήματα κράτησης
            <small>Από το σχολικό έτος 2020–2021 και μετά · 2 μόρια ανά μήνα · έως 60 μήνες.</small>
          </label>
          <input type="number" id="difficultMonths" min="0" max="60" step="1" value="0">
        </div>

        <h3>Τρίμηνες συμβάσεις 2020–2021</h3>
        <div class="field-grid">
          <div class="field">
            <label for="covid20Regular">Λοιπές τρίμηνες — μήνες<small>1,5 μόριο/μήνα · έως 8 μήνες · έως 10 μόρια στο έτος</small></label>
            <input type="number" id="covid20Regular" min="0" max="8" step="1" value="0">
          </div>
          <div class="field">
            <label for="covid20Difficult">Δυσπρόσιτες τρίμηνες — μήνες<small>3 μόρια/μήνα · έως 8 μήνες · έως 20 μόρια στο έτος</small></label>
            <input type="number" id="covid20Difficult" min="0" max="8" step="1" value="0">
          </div>
        </div>

        <h3>Τρίμηνες συμβάσεις 2021–2022</h3>
        <div class="field-grid">
          <div class="field">
            <label for="covid21Regular">Λοιπές τρίμηνες — μήνες<small>1,5 μόριο/μήνα · έως 7 μήνες · έως 10 μόρια στο έτος</small></label>
            <input type="number" id="covid21Regular" min="0" max="7" step="1" value="0">
          </div>
          <div class="field">
            <label for="covid21Difficult">Δυσπρόσιτες τρίμηνες — μήνες<small>3 μόρια/μήνα · έως 7 μήνες · έως 20 μόρια στο έτος</small></label>
            <input type="number" id="covid21Difficult" min="0" max="7" step="1" value="0">
          </div>
        </div>

        <div class="subtot"><span>Σύνολο Προϋπηρεσίας</span><span class="pill" id="serviceSubtotal">0,00 / 120</span></div>
      </section>

      <section class="card">
        <h2>Γ. Κοινωνικά κριτήρια</h2>
        <div class="field"><label for="children">Αριθμός επιλέξιμων τέκνων<small>3 μόρια ανά τέκνο.</small></label><input id="children" type="number" min="0" step="1" value="0"></div>
        <h3>Αναπηρία — λαμβάνεται μόνο το υψηλότερο επιλέξιμο ποσοστό</h3>
        <div class="field-grid">
          <div class="field"><label for="candidateDisability">Αναπηρία υποψηφίου (%)<small>Από 50% και άνω, εφόσον δεν οφείλεται έστω κατά ποσοστό σε ψυχική πάθηση.</small></label><input id="candidateDisability" type="number" min="0" max="100" step="0.01" value="0"></div>
          <div class="field"><label for="spouseDisability">Αναπηρία συζύγου (%)<small>Από 50% και άνω, με έγγαμο βίο τουλάχιστον 4 ετών.</small></label><input id="spouseDisability" type="number" min="0" max="100" step="0.01" value="0"></div>
          <div class="field"><label for="childDisability">Υψηλότερο ποσοστό αναπηρίας τέκνου (%)<small>Από 50% και άνω, ανεξαρτήτως ηλικίας.</small></label><input id="childDisability" type="number" min="0" max="100" step="0.01" value="0"></div>
        </div>
        <div class="info-note">Αν υπάρχουν περισσότερα επιλέξιμα πρόσωπα, λαμβάνεται αυτόματα μόνο το υψηλότερο ποσοστό. Μόρια αναπηρίας = ποσοστό × 0,4.</div>
        <div class="checkrow"><input id="marriageYears4Plus" type="checkbox"><label for="marriageYears4Plus">Ο έγγαμος βίος έχει διαρκέσει τουλάχιστον 4 έτη<small>Απαιτείται για τη μοριοδότηση αναπηρίας συζύγου.</small></label></div>
        <div class="checkrow"><input id="candidateMentalCondition" type="checkbox"><label for="candidateMentalCondition">Η αναπηρία του/της υποψηφίου οφείλεται, έστω και κατά ποσοστό, σε ψυχική πάθηση<small>Αν επιλεγεί, η αναπηρία του/της υποψηφίου δεν μοριοδοτείται.</small></label></div>
        <div id="socialWarning" class="note hidden"></div>
        <div class="subtot"><span>Σύνολο Κοινωνικών</span><span class="pill" id="socialSubtotal">0,00</span></div>
      </section>

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

  <section class="source">
    <strong>Πηγή:</strong> Προκήρυξη ΑΣΕΠ 1ΓΤ/2024, ΦΕΚ Α.Σ.Ε.Π. 25/10.07.2024, Κεφάλαιο Γ΄ «Κριτήρια Κατάταξης».<br>
    Το εργαλείο είναι ενημερωτικό. Η τελική μοριοδότηση προκύπτει από τον έλεγχο της αίτησης και των δικαιολογητικών από τα αρμόδια όργανα.
  </section>

  <div class="credits">Υλοποίηση / επεξεργασία: Μάριος Μαγιολαδίτης</div>
</div>

<script src="includes/service-calculations.js"></script>
<script src="includes/social-calculations.js"></script>
<script src="includes/language-calculations.js"></script>
<script src="includes/te-academic-calculations.js"></script>
<script>
(function(){
  "use strict";
  const $=id=>document.getElementById(id);
  const num=id=>Math.max(0,Number($(id)?.value||0));
  const intNum=id=>Math.max(0,Math.floor(Number($(id)?.value||0)));
  const fmt=v=>(Math.round((Number(v)+Number.EPSILON)*100)/100).toLocaleString('el-GR',{minimumFractionDigits:2,maximumFractionDigits:2});

  function updateBranchUI(){
    const branch=$('branch').value;
    if(branch==='te16'){
      $('secondTitleLabel').textContent='Δεύτερο πτυχίο από το οποίο προκύπτει μουσική ειδίκευση, αναγνωρισμένου μη Ανώτατου Εκπαιδευτικού Ιδρύματος';
      if($('gradeScale').dataset.auto!=='off') $('gradeScale').value='10';
    }else{
      $('secondTitleLabel').textContent='Πτυχίο επιπέδου 5 / Ι.Ε.Κ. ίδιας ειδικότητας';
      if($('gradeScale').dataset.auto!=='off') $('gradeScale').value='20';
    }
    const te16Text=Array.from($('gradeScale').options).find(o=>o.value==='te16text');
    if(te16Text) te16Text.disabled=branch!=='te16';
    if(branch!=='te16'&&$('gradeScale').value==='te16text') $('gradeScale').value='20';
    updateGradeUI();
  }

  function updateGradeUI(){
    const scale=$('gradeScale').value;
    const textual=scale==='te16text';
    $('numericGradeWrap').classList.toggle('hidden',textual);
    $('te16TextWrap').classList.toggle('hidden',!textual);
    if(!textual){
      const min=scale==='10'?5:10,max=scale==='10'?10:20;
      $('degreeGrade').min=String(min);$('degreeGrade').max=String(max);
      $('degreeGrade').placeholder=scale==='10'?'π.χ. 7,50':'π.χ. 15,00';
    }
  }

  function syncLanguageUI(){ $('langOtherWrap').classList.toggle('hidden',$('langName').value!=='other'); }
  function languageResult(){
    syncLanguageUI();
    return EducationLanguages.calculatePair([{language:$('langName').value,otherText:$('langOther').value,points:num('langLevel')}],{cap:20});
  }

  function trainingDatesSelection(){const x=document.querySelector('input[name="trainingDates"]:checked');return x?x.value:'';}
  function updateTrainingProofUI(){
    const active=$('training').checked;
    $('trainingProof').classList.toggle('hidden',!active);
    if(!active)return;
    const value=trainingDatesSelection(),status=$('trainingDatesStatus');
    status.className='training-proof-status '+(value==='yes'?'success':value==='no'?'warning':'neutral');
    if(value==='yes')status.textContent='✓ Οι ημερομηνίες έναρξης και λήξης αναγράφονται στο πιστοποιητικό.';
    else if(value==='no')status.textContent='⚠️ Απαιτείται πρόσθετη βεβαίωση από τον οικείο φορέα με την ημερομηνία έναρξης και λήξης.';
    else status.textContent='Έλεγξε το πιστοποιητικό πριν την υποβολή των δικαιολογητικών.';
  }
  function trainingProofSummary(){
    if(!$('training').checked)return'';
    const value=trainingDatesSelection();
    if(value==='yes')return'Πιστοποιητικό σεμιναρίου: αναγράφονται ημερομηνία έναρξης και λήξης.';
    if(value==='no')return'ΔΙΚΑΙΟΛΟΓΗΤΙΚΟ: απαιτείται βεβαίωση φορέα με ημερομηνία έναρξης και λήξης του σεμιναρίου.';
    return'Έλεγχος πιστοποιητικού σεμιναρίου: εκκρεμεί ο έλεγχος ημερομηνίας έναρξης και λήξης.';
  }

  function socialResult(){return EducationSocial.calculate({
    children:num('children'),candidateDisability:num('candidateDisability'),spouseDisability:num('spouseDisability'),childDisability:num('childDisability'),
    marriageYears4Plus:$('marriageYears4Plus').checked,candidateMentalCondition:$('candidateMentalCondition').checked
  });}
  function serviceResult(){
    const parts=[EducationService.regularPublic(intNum('regularMonths')),EducationService.difficult(intNum('difficultMonths')),
      EducationService.threeMonthRegular2020(intNum('covid20Regular')),EducationService.threeMonthDifficult2020(intNum('covid20Difficult')),
      EducationService.threeMonthRegular2021(intNum('covid21Regular')),EducationService.threeMonthDifficult2021(intNum('covid21Difficult'))];
    const raw=parts.reduce((a,p)=>a+p.points,0),months=parts.reduce((a,p)=>a+p.months,0);
    return{raw,points:Math.min(raw,120),months};
  }

  function calc(){
    updateBranchUI();syncLanguageUI();updateTrainingProofUI();
    const scale=$('gradeScale').value,rawGrade=num('degreeGrade');
    const min=scale==='10'?5:(scale==='20'?10:0),max=scale==='10'?10:20;
    const valid=scale==='te16text'||($('degreeGrade').value!==''&&rawGrade>=min&&rawGrade<=max);
    const languages=languageResult();
    const academicResult=TEAcademic.calculate({gradeScale:scale,degreeGrade:valid?rawGrade:0,te16TextGrade:Number($('te16TextGrade').value||0),
      secondTitle:$('secondTitle').checked,languagePoints:languages.points,computer:$('computer').checked,training:$('training').checked});
    const service=serviceResult(),social=socialResult();
    const total=academicResult.points+service.points+social.total;

    if(scale!=='te16text'&&$('degreeGrade').value!==''&&!valid){
      $('normalizedGradeInfo').textContent=`Μη έγκυρος βαθμός: επιτρέπεται ${min}–${max}. Δεν υπολογίζονται μόρια βαθμού.`;
      $('gradeWarning').classList.remove('hidden');
    }else{
      $('normalizedGradeInfo').textContent=`Αναγμένος βαθμός: ${fmt(academicResult.normalizedGrade)} / 20 · Μόρια βαθμού: ${fmt(academicResult.degreePoints)} / 60`;
      $('gradeWarning').classList.add('hidden');
    }
    $('academicSubtotal').textContent=`${fmt(academicResult.points)} / 120`;$('serviceSubtotal').textContent=`${fmt(service.points)} / 120`;$('socialSubtotal').textContent=fmt(social.total);
    $('grandTotal').textContent=fmt(total);$('resAcademic').textContent=`${fmt(academicResult.points)} / 120`;$('resService').textContent=`${fmt(service.points)} / 120`;$('resSocial').textContent=fmt(social.total);
    $('resDegree').textContent=fmt(academicResult.degreePoints);$('resLanguage').textContent=fmt(languages.points);$('resChildren').textContent=fmt(social.childrenPoints);$('resDisability').textContent=fmt(social.disabilityPoints);

    const lw=$('languageWarning');lw.textContent=languages.warnings.join(' ');lw.classList.toggle('hidden',languages.warnings.length===0);
    const sw=$('socialWarning');sw.textContent=social.warnings.join(' ');sw.classList.toggle('hidden',social.warnings.length===0);

    const ped=$('pedagogical').checked;$('priorityBox').classList.toggle('yes',ped);$('priorityBox').textContent=ped?'ΠΡΟΤΑΞΗ λόγω Παιδαγωγικής & Διδακτικής Επάρκειας':'Χωρίς δηλωμένη πρόταξη Π.Δ.Ε.';
    return{academic:academicResult,service,social,languages,total,ped};
  }

  function languageSummary(v){const item=v.languages.accepted[0];return item?`${item.label} — ${fmt(v.languages.points)} μόρια`:'δεν δηλώθηκε';}
  function summary(v){return[
    'Υπολογισμός μορίων 1ΓΤ/2024',`Σύνολο: ${fmt(v.total)}`,`Ακαδημαϊκά: ${fmt(v.academic.points)} / 120`,`Προϋπηρεσία: ${fmt(v.service.points)} / 120`,
    `Κοινωνικά: ${fmt(v.social.total)}`,`Ξένη γλώσσα: ${languageSummary(v)}`,`Παιδαγωγική επάρκεια: ${v.ped?'ΝΑΙ — ΠΡΟΤΑΞΗ':'ΟΧΙ / ΔΕΝ ΔΗΛΩΘΗΚΕ'}`,
    trainingProofSummary(),'','Ενδεικτικός υπολογισμός βάσει της Προκήρυξης ΑΣΕΠ 1ΓΤ/2024.'
  ].filter((x,i,a)=>x!==''||a[i-1]!=='').join('\n');}

  ['regularMonths','difficultMonths','covid20Regular','covid20Difficult','covid21Regular','covid21Difficult'].forEach(id=>{
    const el=$(id);el.addEventListener('input',()=>{if(el.value==='')return;el.value=Math.max(0,Math.floor(Number(el.value)||0));});
  });
  document.addEventListener('input',calc);document.addEventListener('change',calc);
  $('branch').addEventListener('change',()=>{$('gradeScale').dataset.auto='on';updateBranchUI();calc();});
  $('gradeScale').addEventListener('change',()=>{$('gradeScale').dataset.auto='off';updateGradeUI();calc();});
  $('degreeGrade').addEventListener('change',()=>{if($('degreeGrade').value==='')return;const scale=$('gradeScale').value;if(scale==='te16text')return;const min=scale==='10'?5:10,max=scale==='10'?10:20;let v=Number(String($('degreeGrade').value).replace(',','.'));if(!Number.isFinite(v)){$('degreeGrade').value='';return;}v=Math.min(max,Math.max(min,v));$('degreeGrade').value=v;calc();});
  $('resetBtn').addEventListener('click',()=>{
    document.querySelectorAll('input[type="number"]').forEach(el=>el.value='0');$('degreeGrade').value='';
    document.querySelectorAll('input[type="text"]').forEach(el=>el.value='');document.querySelectorAll('input[type="checkbox"],input[type="radio"]').forEach(el=>el.checked=false);
    $('branch').value='te01';$('gradeScale').dataset.auto='on';$('gradeScale').value='20';$('langName').value='';$('langLevel').value='0';$('te16TextGrade').value='0';updateBranchUI();calc();
  });
  $('copyBtn').addEventListener('click',async()=>{const text=summary(calc());try{await navigator.clipboard.writeText(text);const old=$('copyBtn').textContent;$('copyBtn').textContent='Αντιγράφηκε';setTimeout(()=>$('copyBtn').textContent=old,1400);}catch(e){alert(text);}});
  $('gradeScale').dataset.auto='on';updateBranchUI();calc();
})();
</script>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
  <script src="assets/common.js?v=3.20.9"></script>
</body>
</html>
