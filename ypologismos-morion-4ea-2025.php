<!DOCTYPE html>
<html lang="el">
<head>
<!-- UI consolidation v3.20: shared design system in assets/common.css -->
<meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="Υπολογισμός μορίων για την προκήρυξη ΑΣΕΠ 4ΕΑ/2025 για εκπαιδευτικούς Ειδικής Αγωγής κατηγορίας ΤΕ (ΤΕ01, ΤΕ02, ΤΕ16).">
  <title>Υπολογισμός μορίων 4ΕΑ/2025</title>
<link rel="stylesheet" href="assets/common.css?v=3.20.13">
</head>
<body class="edu-ui edu-calc-standard edu-page-ea4">
<?php require_once __DIR__ . '/includes/header.php'; ?>
<?php require_once __DIR__ . '/includes/components/training-proof.php'; ?>
<?php require_once __DIR__ . '/includes/components/asep-social-criteria.php'; ?>
<?php require_once __DIR__ . '/includes/components/asep-three-month-service.php'; ?>
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
          <input type="number" id="degreeGrade" min="10" max="20" step="0.01" value="" placeholder="π.χ. 15,40">
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

        <div class="field-grid">
          <div class="checkrow">
            <input type="checkbox" id="braille">
            <label for="braille">Πιστοποιημένη επάρκεια Braille<small>Προτεραιότητα για μαθητές με προβλήματα όρασης.</small></label>
          </div>
          <div class="checkrow">
            <input type="checkbox" id="signLanguage">
            <label for="signLanguage">Πιστοποιημένη επάρκεια Ε.Ν.Γ.<small>Προτεραιότητα για κωφούς και βαρήκοους μαθητές.</small></label>
          </div>
        </div>

        <div class="priority" id="tableStatus">Δεν έχει δηλωθεί ακόμη κριτήριο ένταξης.</div>
        <div class="info-note" id="eligibilityWhy">Συμπλήρωσε κλάδο και προσόντα για αναλυτικό έλεγχο ένταξης.</div>

        <div class="note">
          Η προϋπηρεσία Ε.Α.Ε. που χρησιμοποιείται για την ένταξη στον Επικουρικό Πίνακα δεν προστίθεται αυτόματα στα μόρια.
          Καταχώρισέ την και στο κατάλληλο πεδίο προϋπηρεσίας παρακάτω, χωρίς διπλή μέτρηση.
        </div>
      </section>

      <section class="card">
        <h2>Α. Ακαδημαϊκά προσόντα</h2>
        <p class="cap">Μέγιστο κατηγορίας: 120 μόρια</p>

        <div class="checkrow">
          <input type="checkbox" id="secondTitle">
          <label for="secondTitle"><span id="secondTitleLabel">Πτυχίο επιπέδου 5 / Ι.Ε.Κ. ίδιας ειδικότητας</span><small>10 μόρια</small></label>
        </div>

        <h3>Ξένη γλώσσα — μοριοδοτείται μία</h3>
        <div class="info-note">Επίλεξε <strong>ποια γλώσσα</strong> και το επίπεδό της. Η 4ΕΑ/2025 μοριοδοτεί μία μόνο ξένη γλώσσα.</div>
        <div class="field-grid">
          <div class="field">
            <label for="langName">Ξένη γλώσσα</label>
            <select id="langName">
              <option value="">— Επιλογή γλώσσας —</option>
              <option value="en">Αγγλική</option>
              <option value="fr">Γαλλική</option>
              <option value="de">Γερμανική</option>
              <option value="it">Ιταλική</option>
              <option value="es">Ισπανική</option>
              <option value="other">Άλλη ξένη γλώσσα</option>
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
          <label for="training">Επιμόρφωση ≥300 ωρών και διάρκειας ≥7 μηνών<small>Α.Ε.Ι. ή εποπτευόμενος δημόσιος φορέας — μοριοδοτείται μία επιμόρφωση — 10 μόρια. Το σεμινάριο Ε.Α.Ε. ≥400 ωρών του Επικουρικού καλύπτει και αυτό το κριτήριο.</small></label>
        </div>

        <?php
renderTrainingProof([
    'id' => 'trainingProof',
    'radio_name' => 'trainingDates',
    'yes_id' => 'trainingDatesYes',
    'no_id' => 'trainingDatesNo',
    'status_id' => 'trainingDatesStatus',
    'context' => '4ea-2025-general-300h-or-eae-400h-7m',
    'legal_html' => <<<'HTML'
Σε περίπτωση που στο πιστοποιητικό δεν αναγράφεται η ημεροχρονολογία έναρξης και λήξης του σεμιναρίου, απαιτείται η προσκόμιση σχετικής βεβαίωσης από τον οικείο φορέα. <strong>Πρέπει να προκύπτει ολόκληρο το χρονικό διάστημα των 7 μηνών· 6 μήνες και 29 ημέρες δεν γίνονται δεκτοί.</strong>
HTML
]);
?>

        <div class="subtot"><span>Σύνολο Ακαδημαϊκών</span><span class="pill" id="academicSubtotal">0,00 / 120</span></div>
      </section>

      <section class="card">
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
          <input type="number" id="regularMonths" min="0" max="120" step="1" value="0">
        </div>

        <div class="field">
          <label for="difficultMonths">Δυσπρόσιτα / σχολικές μονάδες σε καταστήματα κράτησης
            <small>Από το σχολικό έτος 2020–2021 και μετά · 2 μόρια ανά μήνα · έως 60 μήνες.</small>
          </label>
          <input type="number" id="difficultMonths" min="0" max="60" step="1" value="0">
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
          <input type="number" id="privateMonths" min="0" step="1" value="0">
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

<script src="includes/service-calculations.js?v=3.20.14-rc2"></script>
<script src="includes/social-calculations.js"></script>
<script src="includes/language-calculations.js"></script>
<script src="includes/te-academic-calculations.js"></script>
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

  function updateBranchUI(){
    const family = branchFamily();
    if(family === 'ΤΕ16'){
      $('secondTitleLabel').textContent = 'Δεύτερο πτυχίο από το οποίο προκύπτει μουσική ειδίκευση, αναγνωρισμένου μη Ανώτατου Εκπαιδευτικού Ιδρύματος';
      if($('gradeScale').dataset.auto !== 'off') $('gradeScale').value = '10';
    } else {
      $('secondTitleLabel').textContent = 'Πτυχίο επιπέδου 5 / Ι.Ε.Κ. ίδιας ειδικότητας';
      if($('gradeScale').dataset.auto !== 'off') $('gradeScale').value = '20';
    }
    const te16Option = Array.from($('gradeScale').options).find(o=>o.value==='te16text');
    if(te16Option) te16Option.disabled = family !== 'ΤΕ16';
    if(family !== 'ΤΕ16' && $('gradeScale').value === 'te16text') $('gradeScale').value='20';
    updateGradeUI();
  }

  function updateGradeUI(){
    const scale = $('gradeScale').value;
    const textual = scale === 'te16text';
    $('numericGradeWrap').classList.toggle('hidden', textual);
    $('te16TextWrap').classList.toggle('hidden', !textual);
    if(!textual){
      const minGrade = scale === '10' ? 5 : 10;
      const maxGrade = scale === '10' ? 10 : 20;
      $('degreeGrade').min = String(minGrade);
      $('degreeGrade').max = String(maxGrade);
      $('degreeGrade').placeholder = scale === '10' ? 'π.χ. 7,50' : 'π.χ. 15,00';
    }
  }

  function syncLanguageUI(){
    $('langOtherWrap').classList.toggle('hidden', $('langName').value !== 'other');
  }

  function languageResult(){
    syncLanguageUI();
    return EducationLanguages.calculatePair([{
      language:$('langName').value,
      otherText:$('langOther').value,
      points:num('langLevel')
    }],{cap:20});
  }

  function trainingDatesSelection(){
    const selected=document.querySelector('input[name="trainingDates"]:checked');
    return selected?selected.value:'';
  }

  function trainingActive(){
    return $('training').checked || $('auxSeminar400').checked;
  }

  function updateTrainingProofUI(){
    const active=trainingActive();
    $('trainingProof').classList.toggle('hidden',!active);
    if(!active) return;
    const value=trainingDatesSelection();
    const status=$('trainingDatesStatus');
    status.className='training-proof-status '+(value==='yes'?'success':value==='no'?'warning':'neutral');
    if(value==='yes') status.textContent='✓ Οι ημερομηνίες έναρξης και λήξης αναγράφονται στο πιστοποιητικό.';
    else if(value==='no') status.textContent='⚠️ Απαιτείται πρόσθετη βεβαίωση από τον οικείο φορέα με την ημερομηνία έναρξης και λήξης.';
    else status.textContent='Έλεγξε το πιστοποιητικό πριν την υποβολή των δικαιολογητικών.';
  }

  function trainingProofSummary(){
    if(!trainingActive()) return '';
    const value=trainingDatesSelection();
    if(value==='yes') return 'Πιστοποιητικό σεμιναρίου: αναγράφονται ημερομηνία έναρξης και λήξης.';
    if(value==='no') return 'ΔΙΚΑΙΟΛΟΓΗΤΙΚΟ: απαιτείται βεβαίωση φορέα με ημερομηνία έναρξης και λήξης του σεμιναρίου.';
    return 'Έλεγχος πιστοποιητικού σεμιναρίου: εκκρεμεί ο έλεγχος ημερομηνίας έναρξης και λήξης.';
  }

  function socialResult(){
    return EducationSocial.calculate({
      children:num('children'),
      candidateDisability:num('candidateDisability'),
      spouseDisability:num('spouseDisability'),
      childDisability:num('childDisability'),
      marriageYears4Plus:$('marriageYears4Plus').checked,
      candidateMentalCondition:$('candidateMentalCondition').checked
    });
  }

  function serviceResult(){
    const parts=[
      EducationService.regularPublic(intNum('regularMonths')),
      EducationService.difficult(intNum('difficultMonths')),
      EducationService.threeMonthRegular2020(intNum('covid20Regular')),
      EducationService.threeMonthDifficult2020(intNum('covid20Difficult')),
      EducationService.threeMonthRegular2021(intNum('covid21Regular')),
      EducationService.threeMonthDifficult2021(intNum('covid21Difficult')),
      EducationService.privateSchool(intNum('privateMonths'))
    ];
    const raw=parts.reduce((sum,p)=>sum+p.points,0);
    const months=parts.reduce((sum,p)=>sum+p.months,0);
    return {raw,points:Math.min(raw,120),months};
  }

  function calc(){
    updateBranchUI();
    syncLanguageUI();
    updateTrainingProofUI();

    const currentScale = $('gradeScale').value;
    const rawDegreeGrade = num('degreeGrade');
    const minDegreeGrade = currentScale === '10' ? 5 : (currentScale === '20' ? 10 : 0);
    const maxDegreeGrade = currentScale === '10' ? 10 : (currentScale === '20' ? 20 : 20);
    const numericGradeValid = currentScale === 'te16text' || ($('degreeGrade').value!=='' && rawDegreeGrade >= minDegreeGrade && rawDegreeGrade <= maxDegreeGrade);

    const languages=languageResult();
    const academicResult = TEAcademic.calculate({
      gradeScale: currentScale,
      degreeGrade: numericGradeValid ? rawDegreeGrade : 0,
      te16TextGrade: Number($('te16TextGrade').value || 0),
      secondTitle: $('secondTitle').checked,
      languagePoints: languages.points,
      computer: $('computer').checked,
      training: trainingActive()
    });
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

    if(currentScale !== 'te16text' && $('degreeGrade').value!=='' && !numericGradeValid){
      $('normalizedGradeInfo').textContent=`Μη έγκυρος βαθμός: επιτρέπεται ${minDegreeGrade}–${maxDegreeGrade}. Δεν υπολογίζονται μόρια βαθμού.`;
    } else {
      $('normalizedGradeInfo').textContent=`Αναγμένος βαθμός: ${fmt(academicResult.normalizedGrade)} / 20 · Μόρια βαθμού: ${fmt(academicResult.degreePoints)} / 60`;
    }
    $('gradeWarning').classList.toggle('hidden', currentScale==='te16text' || $('degreeGrade').value==='' || numericGradeValid);

    $('academicSubtotal').textContent=`${fmt(academicResult.points)} / 120`;
    $('serviceSubtotal').textContent=`${fmt(service.points)} / 120`;
    $('socialSubtotal').textContent=fmt(social.total);
    $('grandTotal').textContent=fmt(total);
    $('resAcademic').textContent=`${fmt(academicResult.points)} / 120`;
    $('resService').textContent=`${fmt(service.points)} / 120`;
    $('resSocial').textContent=fmt(social.total);
    $('resDegree').textContent=fmt(academicResult.degreePoints);
    $('resLanguage').textContent=fmt(languages.points);
    $('resChildren').textContent=fmt(social.childrenPoints);
    $('resDisability').textContent=fmt(social.disabilityPoints);
    $('resTable').textContent=tableLabel;

    const lw=$('languageWarning');
    lw.textContent=languages.warnings.join(' ');
    lw.classList.toggle('hidden',languages.warnings.length===0);
    const sw=$('socialWarning');
    sw.textContent=social.warnings.join(' ');
    sw.classList.toggle('hidden',social.warnings.length===0);

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
      trainingProofSummary()
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
    if(e.target && e.target.id==='gradeScale') $('gradeScale').dataset.auto='off';
    calc();
  });

  $('branch').addEventListener('change',()=>{
    $('gradeScale').dataset.auto='on';
    updateBranchUI();
    calc();
  });

  $('degreeGrade').addEventListener('change',()=>{
    if($('degreeGrade').value==='') return;
    const scale=$('gradeScale').value;
    if(scale==='te16text') return;
    const min=scale==='10'?5:10, max=scale==='10'?10:20;
    const value=Number(String($('degreeGrade').value).replace(',', '.'));
    if(!Number.isFinite(value)) $('degreeGrade').value='';
    else $('degreeGrade').value=String(Math.min(max,Math.max(min,value)));
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
    $('gradeScale').dataset.auto='on';
    $('gradeScale').value='20';
    $('langName').value='';
    $('langLevel').value='0';
    $('te16TextGrade').value='0';
    $('mainCriterion').value='none';
    updateBranchUI();
    calc();
  });

  $('gradeScale').dataset.auto='on';
  updateBranchUI();
  calc();
})();
</script>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
  <script src="assets/common.js?v=3.20.13"></script>
</body>
</html>
