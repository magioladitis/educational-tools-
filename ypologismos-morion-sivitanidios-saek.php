<?php require_once __DIR__ . '/includes/config.php'; ?>
<!DOCTYPE html>
<html lang="el">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="Υπολογισμός μορίων για την πρόσκληση ωρομίσθιων εκπαιδευτών της ΣΑΕΚ Σιβιτανιδείου 2026-2027, αρ. πρωτ. 7903/21-08-2026.">
<title>Μόρια ωρομίσθιου εκπαιδευτή ΣΑΕΚ Σιβιτανιδείου 2026–2027</title>
<link rel="stylesheet" href="<?php echo htmlspecialchars(edu_asset_url('assets/common.css'), ENT_QUOTES, 'UTF-8'); ?>">
</head>
<body class="edu-ui edu-calc-standard edu-page-sivitanidios-saek">
<?php require_once __DIR__ . '/includes/header.php'; ?>
<?php require_once __DIR__ . '/includes/components/calculator-layout.php'; ?>
<?php require_once __DIR__ . '/includes/components/deadline-card.php'; ?>

<div class="app">
<?php calculatorHeroStart(); ?>
  <h1>Μόρια ωρομίσθιου εκπαιδευτή ΣΑΕΚ Σιβιτανιδείου 2026–2027</h1>
  <p>Ενδεικτικός υπολογισμός της μοριοδότησης της πρόσκλησης 7903/21-08-2026 για το 2026Β και το 2027Α.</p>
  <div class="meta"><span>ΣΑΕΚ Σιβιτανιδείου</span><span>2026Β + 2027Α</span><span>Βασική βαθμολογία έως 60</span><span>Κοινωνικές προσαυξήσεις</span><span>ΑΔΑ ΨΞ0Ο469ΒΨ1-3ΥΚ</span></div>
<?php calculatorHeroEnd(); ?>

<?php renderDeadlineCard(array(
  'title' => '📅 Προθεσμία αιτήσεων',
  'items' => array(array(
    'title' => 'Ωρομίσθιοι εκπαιδευτές ΣΑΕΚ Σιβιτανιδείου 2026–2027',
    'meta_html' => 'Από <strong>24/08/2026, 12:00</strong> έως <strong>04/09/2026, 12:00</strong>.',
    'start' => '2026-08-24T12:00:00+03:00',
    'end' => '2026-09-04T12:00:00+03:00',
    'source_url' => 'https://ek.sivitanidios.edu.gr/download/2026/7903.pdf',
    'source_label' => 'Επίσημη πρόσκληση ↗'
  ))
)); ?>

<div class="warning edu-mb-16"><strong>Σημαντικό:</strong> τα προσοντολόγια των πιστοποιημένων Οδηγών Κατάρτισης ανά ειδικότητα και μάθημα <strong>υπερισχύουν της σειράς κατάταξης</strong>. Ο calculator υπολογίζει μόρια, αλλά δεν μπορεί να ελέγξει αν ο τίτλος σου σε καθιστά επιλέξιμο για συγκεκριμένο μάθημα.</div>

<?php calculatorColumnsStart(); ?>
<?php calculatorMainStart(); ?>

<?php calculatorCardStart(array('header_variant'=>'section-head','title_html'=>'1. Κατηγορία υποψηφίου','subtitle_html'=>'Η πρόσκληση διακρίνει πτυχιούχους και εμπειροτέχνες.')); ?>
  <div class="field-grid">
    <div class="field full"><label for="candidateType">Συμμετέχω ως</label><select id="candidateType"><option value="">— Επιλογή —</option><option value="graduate">Πτυχιούχος</option><option value="craft">Εμπειροτέχνης χωρίς σχετικό τυπικό τίτλο</option></select></div>
  </div>
  <div id="eligibilityBox" class="priority">Επίλεξε κατηγορία υποψηφίου.</div>
  <div id="craftNote" class="info-note hidden"><strong>Εμπειροτέχνης:</strong> απαιτούνται τουλάχιστον 3 έτη συναφούς επαγγελματικής εμπειρίας. Στη μοριοδότηση της εργασιακής εμπειρίας μετρά μόνο το μέρος <strong>πέραν των πρώτων 3 ετών</strong>.</div>
<?php calculatorCardEnd(); ?>

<div id="graduateOnly">
<?php calculatorCardStart(array('header_variant'=>'section-head','title_html'=>'2. Εκπαίδευση – Επιμόρφωση','subtitle_html'=>'Τυπική εκπαίδευση, Εκπαίδευση Ενηλίκων και επιμορφώσεις.','cap_html'=>'έως 23')); ?>
  <div class="subsection-head"><h3>1.1 Τυπική εκπαίδευση</h3><span class="subsection-max">έως 14</span></div>
  <div class="field-grid">
    <div class="field"><label for="qualifyingTitle">Ανώτερος σχετικός βασικός τίτλος</label><select id="qualifyingTitle"><option value="none">— Δεν έχει επιλεγεί —</option><option value="aei">Πτυχίο ΑΕΙ / ΤΕΙ — 10</option><option value="higher_state">Ανώτερη Κρατική Σχολή — 7</option><option value="saek">Δίπλωμα ΣΑΕΚ / πρώην ΙΕΚ — 6</option><option value="secondary">Πτυχίο Β/θμιας ΤΕ/ΔΕ — 5</option></select></div>
    <div class="field"><label for="relatedPostgrad">Συναφής μεταπτυχιακός τίτλος</label><select id="relatedPostgrad"><option value="none">Δεν διαθέτω</option><option value="master">Μεταπτυχιακό — 3</option><option value="phd">Διδακτορικό — 4</option></select><small>Αν υπάρχουν και τα δύο, μοριοδοτείται μόνο το διδακτορικό.</small></div>
  </div>
  <div class="subsection-head"><h3>1.2 Εκπαίδευση Ενηλίκων</h3><span class="subsection-max">έως 3</span></div>
  <div class="field"><label for="adultEducationPostgrad">Μεταπτυχιακό / διδακτορικό στην Εκπαίδευση Ενηλίκων</label><select id="adultEducationPostgrad"><option value="none">Δεν διαθέτω</option><option value="master">Μεταπτυχιακό — 2</option><option value="phd">Διδακτορικό — 3</option></select><small>Αν υπάρχουν και τα δύο, μοριοδοτείται μόνο το διδακτορικό.</small></div>
  <div class="subsection-head"><h3>1.3 Επιμόρφωση</h3><span class="subsection-max">έως 6</span></div>
  <p class="note">0,25 μόρια ανά συμπληρωμένο 25ωρο, έως 2 μόρια ανά κατηγορία. Επιμορφώσεις κάτω των 25 ωρών, ημερίδες, διημερίδες και συνέδρια δεν μοριοδοτούνται.</p>
  <div class="field-grid">
    <div class="field"><label for="trainingSubjectHours">Στο διδακτικό αντικείμενο — ώρες</label><input id="trainingSubjectHours" type="number" min="25" step="1" value="" inputmode="numeric" placeholder="≥25" data-training-hours></div>
    <div class="field"><label for="trainingVetHours">Επαγγελματική εκπαίδευση / κατάρτιση — ώρες</label><input id="trainingVetHours" type="number" min="25" step="1" value="" inputmode="numeric" placeholder="≥25" data-training-hours></div>
    <div class="field"><label for="trainingAdultHours">Αρχές Εκπαίδευσης Ενηλίκων — ώρες</label><input id="trainingAdultHours" type="number" min="25" step="1" value="" inputmode="numeric" placeholder="≥25" data-training-hours></div>
  </div>
  <div id="trainingHoursWarning" class="warning hidden" aria-live="polite">Οι επιμορφώσεις κάτω από 25 ώρες δεν μοριοδοτούνται και δεν καταχωρίζονται.</div>
<?php calculatorCardEnd(); ?>

<?php calculatorCardStart(array('header_variant'=>'section-head','title_html'=>'3. Διδακτική εμπειρία','subtitle_html'=>'Η ίδια προϋπηρεσία δεν πρέπει να δηλώνεται σε περισσότερες κατηγορίες.','cap_html'=>'έως 21')); ?>
  <div class="subsection-head"><h3>2.1 ΣΑΕΚ / ΣΕΚ</h3><span class="subsection-max">έως 12</span></div>
  <div class="field-grid">
    <div class="field"><label for="saekOutsideHours">ΣΑΕΚ / ΣΕΚ εκτός Σιβιτανιδείου — ώρες <small>1 μόριο / 150 ώρες, έως 8.</small></label><input id="saekOutsideHours" type="number" min="0" step="1" value="0"></div>
    <div class="field"><label for="saekSivitanidiosHours">ΣΑΕΚ / ΣΕΚ Σιβιτανιδείου — ώρες <small>1,2 μόρια / 150 ώρες, έως 10.</small></label><input id="saekSivitanidiosHours" type="number" min="0" step="1" value="0"></div>
  </div>
  <div class="note">Οι δύο παραπάνω κατηγορίες μοιράζονται το ίδιο ανώτατο όριο.</div>
  <div class="subsection-head"><h3>2.2 Τυπική εκπαίδευση</h3><span class="subsection-max">έως 4</span></div>
  <div class="field-grid">
    <div class="field"><label for="tertiaryTeachingYears">Τριτοβάθμια — έτη <small>0,5 μόριο / έτος.</small></label><input id="tertiaryTeachingYears" type="number" min="0" step="0.01" value="0"></div>
    <div class="field"><label for="primarySecondaryTeachingYears">Πρωτοβάθμια / Δευτεροβάθμια — έτη <small>1 μόριο / έτος.</small></label><input id="primarySecondaryTeachingYears" type="number" min="0" step="0.01" value="0"></div>
  </div>
  <div class="note">Η πρόσκληση δέχεται δεκαδική αναγωγή σε έτη (π.χ. 11 μήνες = 0,92).</div>
  <div class="subsection-head"><h3>2.3 Άλλα προγράμματα μη τυπικής εκπαίδευσης</h3><span class="subsection-max">έως 5</span></div>
  <div class="field"><label for="otherNonFormalHours">Ώρες <small>1 μόριο / 200 ώρες, έως 5.</small></label><input id="otherNonFormalHours" type="number" min="0" step="1" value="0"></div>
<?php calculatorCardEnd(); ?>
</div>

<?php calculatorCardStart(array('header_variant'=>'section-head','title_html'=>'4. Εργασιακή εμπειρία','subtitle_html'=>'Συναφής με το αντικείμενο των προκηρυσσόμενων ειδικοτήτων.','cap_html'=>'έως 10')); ?>
  <div class="field"><label for="workMonths">Συνολικοί μήνες συναφούς επαγγελματικής εμπειρίας <small>1 μόριο ανά έτος, αναλογικά.</small></label><input id="workMonths" type="number" min="0" max="600" step="1" value="0"></div>
  <div id="workRuleNote" class="note">Για πτυχιούχο μοριοδοτείται η συναφής εργασιακή εμπειρία έως 10 έτη.</div>
<?php calculatorCardEnd(); ?>

<?php calculatorCardStart(array('header_variant'=>'section-head','title_html'=>'5. Άλλα προσόντα','subtitle_html'=>'Συμπληρώνονται από πτυχιούχους και εμπειροτέχνες.','cap_html'=>'έως 6')); ?>
  <div class="field-grid">
    <div class="field"><label for="languageName">Ξένη γλώσσα</label><select id="languageName"><option value="none">— Επιλογή γλώσσας —</option><option value="english">Αγγλική</option><option value="french">Γαλλική</option><option value="german">Γερμανική</option><option value="italian">Ιταλική</option><option value="spanish">Ισπανική</option><option value="other">Άλλη γλώσσα</option></select></div>
    <div class="field"><label for="languageLevel">Ανώτερο πιστοποιημένο επίπεδο ξένης γλώσσας</label><select id="languageLevel"><option value="none">Δεν δηλώνω</option><option value="B2">Β2 — 1</option><option value="C1">C1 — 1,5</option><option value="C2">C2 — 2</option></select></div>
    <div class="field full"><div class="check-row"><input id="languageTeachingExcluded" type="checkbox"><label for="languageTeachingExcluded">Η γλώσσα είναι αυτή που διδάσκω ως εκπαιδευτικός κλάδου ξένης γλώσσας <small>Δεν μοριοδοτείται.</small></label></div></div>
    <div class="field"><div class="check-row"><input id="computer" type="checkbox"><label for="computer">Πιστοποιημένη γνώση χειρισμού Η/Υ / ΤΠΕ επιπέδου 1 — 2</label></div></div>
    <div class="field"><div class="check-row"><input id="pe86" type="checkbox"><label for="pe86">Είμαι εκπαιδευτικός κλάδου ΠΕ86 <small>Δεν μοριοδοτείται η κατηγορία Η/Υ.</small></label></div></div>
    <div class="field full"><div class="check-row"><input id="adultTrainer" type="checkbox"><label for="adultTrainer">Πιστοποιημένος Εκπαιδευτής Ενηλίκων — 2</label></div></div>
  </div>
  <div class="note language-proof-note"><strong>Δικαιολογητικά γλωσσομάθειας:</strong> Τίτλοι γλωσσομάθειας στις γλώσσες αγγλική, γαλλική, γερμανική, ιταλική και ισπανική που πληρούν αποκλειστικά τους όρους του άρθρου 28 του Π.Δ. 50/2001 (Α΄ 39) γίνονται δεκτοί χωρίς να απαιτείται μετάφρασή τους. Σε κάθε άλλη περίπτωση απαιτείται η καταχώρηση του ξενόγλωσσου τίτλου και η επίσημη μετάφραση του τίτλου.</div>
  <div id="languageProofBox" class="language-proof hidden" aria-live="polite">
    <div class="language-proof-title">Έλεγχος δικαιολογητικού ξένης γλώσσας</div>
    <div id="article28Row" class="field hidden">
      <label for="languageArticle28Status">Ο τίτλος πληροί αποκλειστικά τους όρους του άρθρου 28 του Π.Δ. 50/2001 (Α΄ 39);</label>
      <select id="languageArticle28Status">
        <option value="">— Επίλεξε Ναι ή Όχι —</option>
        <option value="yes">Ναι — δεν απαιτείται μετάφραση</option>
        <option value="no">Όχι — απαιτούνται τίτλος και επίσημη μετάφραση</option>
      </select>
      <small>Η ερώτηση εμφανίζεται μόνο για Αγγλικά, Γαλλικά, Γερμανικά, Ιταλικά και Ισπανικά.</small>
    </div>
    <div id="translationChecks" class="hidden">
      <div class="check-row"><input id="languageTitleRegistered" type="checkbox"><label for="languageTitleRegistered">Έχω καταχωρίσει τον ξενόγλωσσο τίτλο.</label></div>
      <div class="check-row"><input id="languageOfficialTranslation" type="checkbox"><label for="languageOfficialTranslation">Έχω επίσημη μετάφραση του τίτλου.</label></div>
    </div>
    <div id="languageProofStatus" class="language-proof-status">Επίλεξε γλώσσα και επίπεδο.</div>
  </div>
<?php calculatorCardEnd(); ?>

<?php calculatorCardStart(array('header_variant'=>'section-head','title_html'=>'6. Κοινωνικά κριτήρια','subtitle_html'=>'Οι προσαυξήσεις εφαρμόζονται επί της βασικής βαθμολογίας.')); ?>
  <div class="field"><label for="unemploymentBand">Ανεργία</label><select id="unemploymentBand"><option value="none">Δεν δηλώνω ανεργία — 0%</option><option value="m0_6">0–6 μήνες — 2%</option><option value="m6_12">6–12 μήνες — 4%</option><option value="m12_18">12–18 μήνες — 6%</option><option value="m18_24">18–24 μήνες — 8%</option><option value="m24plus">24 μήνες και άνω — 10%</option></select><small>Απαιτείται πρόσφατη βεβαίωση ανεργίας, όχι παλαιότερη των 5 εργάσιμων ημερών από την αίτηση.</small></div>
  <h3>Ειδικές κατηγορίες — +10% η καθεμία</h3>
  <div class="field-grid">
    <div class="field"><div class="check-row"><input id="threeChildrenParent" type="checkbox"><label for="threeChildrenParent">Γονέας τρίτεκνης οικογένειας</label></div></div>
    <div class="field"><div class="check-row"><input id="manyChildrenMember" type="checkbox"><label for="manyChildrenMember">Μέλος πολύτεκνης οικογένειας (γονέας ή τέκνο)</label></div></div>
    <div class="field"><div class="check-row"><input id="singleParentMember" type="checkbox"><label for="singleParentMember">Μέλος μονογονεϊκής οικογένειας (γονέας ή τέκνο)</label></div></div>
    <div class="field"><div class="check-row"><input id="disabilityCategory" type="checkbox"><label for="disabilityCategory">ΑμεΑ — γονέας ή ο ίδιος</label></div></div>
  </div>
  <div class="note">Η πρόσκληση ορίζει τις ειδικές κατηγορίες ως ποσοστό επί του συνόλου της βαθμολογίας. Ο υπολογιστής προσθέτει αθροιστικά τα δηλωμένα ποσοστά πάνω στη βασική βαθμολογία, χωρίς ανατοκισμό.</div>
<?php calculatorCardEnd(); ?>

<?php calculatorMainEnd(); ?>

<?php calculatorResultsStart(array('aria_live'=>'polite')); ?>
  <div class="total"><div class="num" id="finalTotal">0,00</div><div class="label">τελική βαθμολογία</div></div>
  <?php calculatorResultRow(array('label_html'=>'Βασική βαθμολογία','value_html'=>'0,00','value_id'=>'resBase')); ?>
  <?php calculatorResultRow(array('label_html'=>'Εκπαίδευση – Επιμόρφωση','value_html'=>'0,00 / 23','value_id'=>'resEducation')); ?>
  <?php calculatorResultRow(array('label_html'=>'Διδακτική εμπειρία','value_html'=>'0,00 / 21','value_id'=>'resTeaching')); ?>
  <?php calculatorResultRow(array('label_html'=>'Εργασιακή εμπειρία','value_html'=>'0,00 / 10','value_id'=>'resWork')); ?>
  <?php calculatorResultRow(array('label_html'=>'Άλλα προσόντα','value_html'=>'0,00 / 6','value_id'=>'resOther')); ?>
  <?php calculatorResultRow(array('label_html'=>'Κοινωνική προσαύξηση','value_html'=>'+0,00','value_id'=>'resSocial')); ?>
  <?php calculatorResultRow(array('label_html'=>'Συνολικό ποσοστό κοινωνικών','value_html'=>'0%','value_id'=>'resSocialPercent')); ?>
  <div id="resultStatus" class="priority">Συμπλήρωσε τα βασικά στοιχεία.</div>
  <div id="warningBox" class="note hidden"></div>
  <div id="breakdownBox" class="note">Η αναλυτική κατανομή θα εμφανιστεί εδώ.</div>
  <?php calculatorActions(array(
    array('attrs'=>array('type'=>'button','id'=>'copyBtn'),'html'=>'Αντιγραφή αποτελέσματος'),
    array('attrs'=>array('type'=>'button','id'=>'resetBtn','class'=>'secondary'),'html'=>'Μηδενισμός')
  )); ?>
<?php calculatorResultsEnd(); ?>
<?php calculatorColumnsEnd(); ?>

<?php sourceCardStart(); ?>
  <p><strong>Βάση υπολογισμού:</strong> Πρόσκληση Σιβιτανιδείου <strong>7903/21-08-2026</strong>, ΑΔΑ <strong>ΨΞ0Ο469ΒΨ1-3ΥΚ</strong>, ιδίως ο πίνακας «Μοριοδότηση Υποψηφίων Εκπαιδευτών ΣΑΕΚ».</p>
  <?php sourceCardLinksStart(); ?>
    <?php sourceCardLink('https://ek.sivitanidios.edu.gr/download/2026/7903.pdf','Πλήρης επίσημη πρόσκληση — Σιβιτανίδειος ↗'); ?>
    <?php sourceCardLink('https://www.sivitanidios.edu.gr/search/label/%CE%A0%CF%81%CE%BF%CF%83%CE%BB%CE%AE%CF%88%CE%B5%CE%B9%CF%82','Ανακοινώσεις προσλήψεων — Σιβιτανίδειος ↗'); ?>
  <?php sourceCardLinksEnd(); ?>
  <?php sourceCardDisclaimerStart(); ?>Ο υπολογισμός είναι ενδεικτικός. Τα προσοντολόγια των Οδηγών Κατάρτισης, ο έλεγχος συνάφειας και τα επίσημα αποτελέσματα της Σιβιτανιδείου υπερισχύουν.<?php sourceCardDisclaimerEnd(); ?>
<?php sourceCardEnd(); ?>

<div class="credits">Εργαλείο υπολογισμού μορίων · ΣΑΕΚ Σιβιτανιδείου 2026–2027</div>
</div>

<script src="<?php echo htmlspecialchars(edu_asset_url('includes/sivitanidios-saek-calculations.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
<script>
(function(){
'use strict';
const $=id=>document.getElementById(id);
const fmt=v=>(Math.round((Number(v)+Number.EPSILON)*100)/100).toLocaleString('el-GR',{minimumFractionDigits:2,maximumFractionDigits:2});
function data(){return {
  candidateType:$('candidateType').value, qualifyingTitle:$('qualifyingTitle').value, relatedPostgrad:$('relatedPostgrad').value,
  adultEducationPostgrad:$('adultEducationPostgrad').value, trainingSubjectHours:$('trainingSubjectHours').value, trainingVetHours:$('trainingVetHours').value, trainingAdultHours:$('trainingAdultHours').value,
  saekOutsideHours:$('saekOutsideHours').value, saekSivitanidiosHours:$('saekSivitanidiosHours').value, tertiaryTeachingYears:$('tertiaryTeachingYears').value, primarySecondaryTeachingYears:$('primarySecondaryTeachingYears').value, otherNonFormalHours:$('otherNonFormalHours').value,
  workMonths:$('workMonths').value, languageName:$('languageName').value, languageLevel:$('languageLevel').value, languageTeachingExcluded:$('languageTeachingExcluded').checked, languageArticle28Status:$('languageArticle28Status').value, languageArticle28:$('languageArticle28Status').value==='yes', languageTitleRegistered:$('languageTitleRegistered').checked, languageOfficialTranslation:$('languageOfficialTranslation').checked, computer:$('computer').checked, pe86:$('pe86').checked, adultTrainer:$('adultTrainer').checked,
  unemploymentBand:$('unemploymentBand').value, threeChildrenParent:$('threeChildrenParent').checked, manyChildrenMember:$('manyChildrenMember').checked, singleParentMember:$('singleParentMember').checked, disabilityCategory:$('disabilityCategory').checked
};}
function syncLanguageProof(d){
  const hasLevel=d.languageLevel && d.languageLevel!=='none';
  const hasLanguage=d.languageName && d.languageName!=='none';
  const named=['english','french','german','italian','spanish'].includes(d.languageName);
  $('languageProofBox').classList.toggle('hidden',!hasLevel);
  $('article28Row').classList.toggle('hidden',!(hasLevel&&hasLanguage&&named));
  const article28Status=d.languageArticle28Status||'';
  const translationRequired=hasLevel&&hasLanguage&&(!named||(named&&article28Status==='no'));
  $('translationChecks').classList.toggle('hidden',!translationRequired);
  let text='Επίλεξε τη γλώσσα για να ελεγχθούν τα δικαιολογητικά.', ok=false;
  if(hasLevel&&hasLanguage){
    if(named&&article28Status===''){text='ℹ Δήλωσε αν ο τίτλος πληροί αποκλειστικά τους όρους του άρθρου 28 του Π.Δ. 50/2001.';}
    else if(named&&article28Status==='yes'){text='✓ Δεν απαιτείται μετάφραση του τίτλου.';ok=true;}
    else if(d.languageTitleRegistered&&d.languageOfficialTranslation){text='✓ Έχουν δηλωθεί ο ξενόγλωσσος τίτλος και η επίσημη μετάφραση.';ok=true;}
    else{text='⚠ Απαιτούνται καταχώρηση του ξενόγλωσσου τίτλου και επίσημη μετάφραση.';}
  }
  $('languageProofStatus').textContent=text;
  $('languageProofStatus').classList.toggle('yes',ok);
}
function calc(){
  if(typeof window.SivitanidiosSaekCalc==='undefined'){
    const box=$('warningBox');
    if(box){box.classList.remove('hidden');box.textContent='⚠ Δεν φορτώθηκε ο υπολογιστικός μηχανισμός. Κάνε ανανέωση της σελίδας (Ctrl+F5) και, αν επιμένει, έλεγξε ότι έχει ανέβει το includes/sivitanidios-saek-calculations.js.';}
    return null;
  }
  const d=data(), r=SivitanidiosSaekCalc.calculateAll(d), graduate=d.candidateType==='graduate';
  syncLanguageProof(d);
  $('graduateOnly').classList.toggle('hidden',!graduate);
  $('craftNote').classList.toggle('hidden',d.candidateType!=='craft');
  $('workRuleNote').innerHTML=d.candidateType==='craft'?'Για εμπειροτέχνη τα πρώτα <strong>36 μήνες</strong> είναι προαπαιτούμενο και δεν μοριοδοτούνται. Δηλώνεται η συνολική εμπειρία· ο calculator αφαιρεί αυτόματα την τριετία.':'Για πτυχιούχο μοριοδοτείται η συναφής εργασιακή εμπειρία έως 10 έτη.';
  $('finalTotal').textContent=fmt(r.final); $('resBase').textContent=fmt(r.base)+(graduate?' / 60':' / 16');
  $('resEducation').textContent=fmt(r.education.points)+' / 23'; $('resTeaching').textContent=fmt(r.teaching.points)+' / 21'; $('resWork').textContent=fmt(r.work.points)+' / 10'; $('resOther').textContent=fmt(r.other.points)+' / 6';
  $('resSocial').textContent='+'+fmt(r.social.increase); $('resSocialPercent').textContent=fmt(r.social.totalPercent).replace(',00','')+'%';
  const st=r.eligibility; $('eligibilityBox').classList.toggle('yes',st.eligible); $('eligibilityBox').textContent=st.blockers.length?'⚠ '+st.blockers.join(' '):st.pending.length?'ℹ '+st.pending.join(' '):'✓ Τα βασικά στοιχεία της επιλεγμένης κατηγορίας έχουν επιβεβαιωθεί.';
  $('resultStatus').classList.toggle('yes',st.eligible); $('resultStatus').textContent=st.eligible?'Υπολογισμός ολοκληρωμένος — έλεγξε και το προσοντολόγιο του μαθήματος.':'Ο υπολογισμός είναι προσωρινός μέχρι να συμπληρωθούν τα βασικά στοιχεία.';
  const warnings=(r.other.warnings||[]).slice(); if(d.candidateType==='craft'&&Number(d.workMonths||0)<36) warnings.push('Ο εμπειροτέχνης δεν συμπληρώνει την απαιτούμενη τριετή εμπειρία.');
  $('warningBox').classList.toggle('hidden',warnings.length===0); $('warningBox').textContent=warnings.join(' ');
  const lines=[]; r.education.details.forEach(x=>lines.push(x.label+': '+fmt(x.points))); r.teaching.details.forEach(x=>lines.push(x.label+': '+fmt(x.points)));
  if(r.work.points) lines.push('Εργασιακή εμπειρία: '+fmt(r.work.points)); if(r.other.languagePoints) lines.push('Ξένη γλώσσα: '+fmt(r.other.languagePoints)); if(r.other.computerPoints) lines.push('Η/Υ: 2,00'); if(r.other.adultTrainerPoints) lines.push('Πιστοποιημένος Εκπαιδευτής Ενηλίκων: 2,00');
  if(r.social.totalPercent) lines.push('Κοινωνική προσαύξηση: '+fmt(r.social.totalPercent).replace(',00','')+'% = +'+fmt(r.social.increase)); $('breakdownBox').innerHTML=lines.length?lines.map(x=>'<div>'+x.replace(/</g,'&lt;')+'</div>').join(''):'Δεν έχουν δηλωθεί ακόμη μοριοδοτούμενα κριτήρια.';
  return r;
}
$('languageName').addEventListener('change',()=>{
  $('languageArticle28Status').value='';
  $('languageTitleRegistered').checked=false;
  $('languageOfficialTranslation').checked=false;
});
function normalizeTrainingHours(el){
  const raw=String(el.value||'').trim();
  if(raw==='') return false;
  const value=Number(raw.replace(',','.'));
  if(Number.isFinite(value) && value>0 && value<25){
    el.value='';
    return true;
  }
  return false;
}
const scoreControls=Array.from(document.querySelectorAll('.app input, .app select'));
scoreControls.forEach(el=>{
  el.addEventListener('input',calc);
  el.addEventListener('change',()=>{
    let rejected=false;
    if(el.matches('[data-training-hours]')) rejected=normalizeTrainingHours(el);
    const warn=$('trainingHoursWarning');
    if(warn){
      warn.classList.toggle('hidden',!rejected);
      if(rejected) window.setTimeout(()=>warn.classList.add('hidden'),3500);
    }
    calc();
  });
});
$('copyBtn').addEventListener('click',async()=>{const r=calc(), text=['ΣΑΕΚ Σιβιτανιδείου 2026–2027','Βασική βαθμολογία: '+fmt(r.base),'Κοινωνική προσαύξηση: +'+fmt(r.social.increase)+' ('+fmt(r.social.totalPercent).replace(',00','')+'%)','Τελική βαθμολογία: '+fmt(r.final)].join('\n'); try{await navigator.clipboard.writeText(text); const old=$('copyBtn').textContent;$('copyBtn').textContent='Αντιγράφηκε';setTimeout(()=>$('copyBtn').textContent=old,1400);}catch(e){alert(text);}});
$('resetBtn').addEventListener('click',()=>{document.querySelectorAll('input[type="number"]').forEach(el=>el.value=el.matches('[data-training-hours]')?'':'0');document.querySelectorAll('input[type="checkbox"]').forEach(el=>el.checked=false);document.querySelectorAll('select').forEach(el=>el.selectedIndex=0);$('trainingHoursWarning').classList.add('hidden');calc();});
calc();
})();
</script>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
<script src="<?php echo htmlspecialchars(edu_asset_url('assets/common.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
</body>
</html>
