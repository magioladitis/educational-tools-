<?php require_once __DIR__ . '/includes/config.php'; ?>
<!DOCTYPE html>
<html lang="el">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Υπολογισμός Μισθολογικού Κλιμακίου (Μ.Κ.) / Μισθοδοσίας</title>
  <link rel="stylesheet" href="<?php echo htmlspecialchars(edu_asset_url('assets/common.css'), ENT_QUOTES, 'UTF-8'); ?>">
  <style>
    .payroll-print-sheet { display:none; }
    body.edu-ui.edu-calc-standard .result-row[hidden],
    body.edu-ui.edu-calc-standard .payroll-deduction-details[hidden] { display:none !important; }
    .payroll-deduction-toggle {
      border:0; background:transparent; padding:0; margin:0; color:var(--blue-dark);
      font:inherit; font-weight:700; cursor:pointer; display:inline-flex; align-items:center; gap:6px;
    }
    .payroll-deduction-toggle:hover, .payroll-deduction-toggle:focus-visible { text-decoration:underline; }
    .payroll-deduction-toggle-icon { display:inline-block; transition:transform .16s ease; }
    .payroll-deduction-toggle[aria-expanded="true"] .payroll-deduction-toggle-icon { transform:rotate(180deg); }
    .payroll-deduction-details {
      margin:0 0 8px; padding:4px 0 7px 12px; border-left:3px solid var(--edu-result-row-separator);
    }
    .payroll-deduction-detail-row {
      display:grid; grid-template-columns:minmax(0,1fr) auto; gap:10px; padding:6px 0;
      border-top:1px dashed var(--edu-result-row-separator); font-size:13px;
    }
    .payroll-deduction-detail-row:first-child { border-top:0; }
    .payroll-deduction-detail-row strong { text-align:right; white-space:nowrap; font-variant-numeric:tabular-nums; }
    .payroll-deduction-detail-note { display:block; margin-top:2px; color:var(--muted); font-size:11px; font-weight:400; line-height:1.35; }
    @media print {
      @page { size:A4; margin:12mm; }
      body > * { display:none !important; }
      body > #payrollPrintSheet { display:block !important; }
      #payrollPrintSheet {
        color:#111; background:#fff; font:12px/1.38 Arial, Helvetica, sans-serif;
        width:100%; margin:0; padding:0;
      }
      #payrollPrintSheet h1 { margin:0 0 3px; text-align:center; font-size:18px; letter-spacing:.2px; }
      #payrollPrintSheet .print-subtitle { text-align:center; margin:0 0 14px; color:#444; }
      #payrollPrintSheet .print-grid { display:grid; grid-template-columns:1fr 1fr; gap:12px; margin-bottom:12px; }
      #payrollPrintSheet .print-box { border:1px solid #777; padding:8px; break-inside:avoid; }
      #payrollPrintSheet .print-box h2 { margin:0 0 6px; font-size:12px; text-transform:uppercase; }
      #payrollPrintSheet table { width:100%; border-collapse:collapse; margin:0 0 12px; break-inside:avoid; }
      #payrollPrintSheet th, #payrollPrintSheet td { border:1px solid #999; padding:5px 6px; vertical-align:top; }
      #payrollPrintSheet th { background:#eee !important; -webkit-print-color-adjust:exact; print-color-adjust:exact; text-align:left; }
      #payrollPrintSheet td.amount { text-align:right; white-space:nowrap; font-variant-numeric:tabular-nums; }
      #payrollPrintSheet tr.total-row td { font-weight:700; border-top:2px solid #555; }
      #payrollPrintSheet .print-net { border:2px solid #333; padding:9px 12px; margin-top:10px; display:flex; justify-content:space-between; font-size:16px; font-weight:700; }
      #payrollPrintSheet .print-note { margin-top:12px; color:#444; font-size:10.5px; }
      #payrollPrintSheet .print-meta { margin-top:8px; text-align:right; color:#666; font-size:10px; }
    }
  </style>
</head>
<body class="edu-ui edu-calc-standard edu-page-salary">
<?php require_once __DIR__ . '/includes/header.php'; ?>
<?php require_once __DIR__ . '/includes/components/calculator-layout.php'; ?>

<?php calculatorContainerStart(array('class' => 'app')); ?>
  <?php calculatorHero(array(
    'title' => 'Υπολογισμός Μισθολογικού Κλιμακίου (Μ.Κ.) / Μισθοδοσίας',
    'intro_html' => 'Βρες ενδεικτικά το Μισθολογικό Κλιμάκιο, τον αντίστοιχο <strong>βασικό μικτό μισθό</strong> και μια <strong>εκτίμηση καθαρών αποδοχών</strong> με τη φορολογία 2026, με βάση την κατηγορία, τον <strong>ήδη αναγνωρισμένο μισθολογικό χρόνο</strong> και τον ανώτερο τίτλο που έχει ήδη αναγνωριστεί για μισθολογική προώθηση.',
    'badges' => array('ΠΕ / ΤΕ', 'ΔΕ / ΥΕ', 'Αποδοχές από 01/04/2026', 'Φορολογία 2026')
  )); ?>

  <?php calculatorColumnsStart(); ?>
    <?php calculatorMainStart(); ?>
      <?php calculatorCardStart(array('title' => 'Στοιχεία μισθολογικής κατάταξης')); ?>
        <div class="field-grid">
          <div class="field">
            <label for="category">Κατηγορία</label>
            <select id="category">
              <option value="PE">ΠΕ — Πανεπιστημιακής Εκπαίδευσης</option>
              <option value="TE">ΤΕ — Τεχνολογικής Εκπαίδευσης</option>
              <option value="DE">ΔΕ — Δευτεροβάθμιας Εκπαίδευσης</option>
              <option value="YE">ΥΕ — Υποχρεωτικής Εκπαίδευσης</option>
            </select>
          </div>
          <div class="field">
            <label for="qualification">Ανώτερο προσόν που έχει ήδη αναγνωριστεί για μισθολογική προώθηση από την υπηρεσία σας</label>
            <select id="qualification">
              <option value="none">Χωρίς αναγνωρισμένη προώθηση</option>
              <option value="master">Αναγνωρισμένο συναφές μεταπτυχιακό — +2 Μ.Κ.</option>
              <option value="integrated">Integrated Master ελληνικού Α.Ε.Ι. που πληροί τις προϋποθέσεις — +2 Μ.Κ. από 01-01-2026</option>
              <option value="phd">Αναγνωρισμένο συναφές διδακτορικό — +6 Μ.Κ.</option>
            </select>
          </div>
          <div class="field">
            <label for="serviceYears">Συνολικά αναγνωρισμένα έτη υπηρεσίας</label>
            <input id="serviceYears" type="number" min="0" max="50" step="1" value="0" inputmode="numeric">
          </div>
          <div class="field">
            <label for="serviceMonths">Επιπλέον αναγνωρισμένοι μήνες</label>
            <input id="serviceMonths" type="number" min="0" max="11" step="1" value="0" inputmode="numeric">
          </div>
          <div class="edu-field--full">
            <p class="edu-help"><strong>Χρόνος 2016–2017:</strong> δήλωσε μόνο το μέρος της συνολικής αναγνωρισμένης υπηρεσίας που διανύθηκε στη διετία και δεν προσμετράται στη μισθολογική εξέλιξη.</p>
            <div class="field-grid">
              <div class="field">
                <label for="suspendedYears">Έτη υπηρεσίας στη διετία</label>
                <input id="suspendedYears" type="number" min="0" max="2" step="1" value="0" inputmode="numeric">
              </div>
              <div class="field">
                <label for="suspendedMonths">Επιπλέον μήνες στη διετία</label>
                <input id="suspendedMonths" type="number" min="0" max="11" step="1" value="0" inputmode="numeric">
              </div>
            </div>
          </div>
        </div>

        <h3>Προαιρετική εκτίμηση καθαρών αποδοχών</h3>
        <div class="field-grid">
          <div class="field">
            <label for="payrollProfile">Προφίλ κρατήσεων</label>
            <select id="payrollProfile">
              <option value="permanent">Μόνιμος δημόσιος υπάλληλος</option>
              <option value="newly_appointed">Νεοδιόριστος — 1ο έτος ΜΤΠΥ</option>
              <option value="substitute">Αναπληρωτής / ΙΔΟΧ — ΚΠΚ 101</option>
            </select>
          </div>
          <div class="field">
            <label for="insuredStatus">Ασφαλιστική ιδιότητα (πρώτη ασφάλιση)</label>
            <select id="insuredStatus" aria-describedby="insuredStatusHelp">
              <option value="new_efka">Νέος ασφαλισμένος — από 01/01/1993 · επικουρική e-ΕΦΚΑ</option>
              <option value="new_teka">Νέος ασφαλισμένος — από 01/01/1993 · επικουρική ΤΕΚΑ</option>
              <option value="old">Παλαιός ασφαλισμένος — έως 31/12/1992</option>
            </select>
            <small id="insuredStatusHelp">Κριτήριο είναι η <strong>πρώτη ασφάλιση για κύρια σύνταξη</strong>, όχι η ημερομηνία διορισμού. Η επιλογή e-ΕΦΚΑ/ΤΕΚΑ δεν αλλάζει το 3% της επικουρικής.</small>
          </div>
          <div class="field">
            <label for="ageGroup">Ηλικιακή κατηγορία για τη φορολογία 2026</label>
            <select id="ageGroup">
              <option value="over30">Άνω των 30 ετών</option>
              <option value="age26to30">26–30 ετών</option>
              <option value="upTo25">Έως 25 ετών</option>
            </select>
          </div>
          <div class="field">
            <label for="disabilityTaxTreatment">Φορολογική ρύθμιση λόγω αναπηρίας</label>
            <select id="disabilityTaxTreatment">
              <option value="none">Χωρίς ειδική ρύθμιση</option>
              <option value="disability67_79">Αναπηρία 67%–79,99% — μείωση φόρου έως 200 €/έτος</option>
              <option value="disability80plus">Αναπηρία ≥80% — απαλλαγή φόρου μισθωτής εργασίας</option>
            </select>
            <small>Αφορά τη φορολογία εισοδήματος και όχι τις ασφαλιστικές κρατήσεις. Επίλεξέ το μόνο όταν υπάρχει η απαιτούμενη πιστοποίηση/τεκμηρίωση.</small>
          </div>
          <div class="field">
            <label for="dependentChildren">Εξαρτώμενα τέκνα</label>
            <input id="dependentChildren" type="number" min="0" max="20" step="1" value="0" inputmode="numeric">
          </div>
          <div class="field edu-field--full">
            <label for="positionAllowance">Θέση ευθύνης — άρθρο 16 ν. 4354/2015</label>
            <select id="positionAllowance">
              <option value="none">Χωρίς θέση ευθύνης</option>
              <optgroup label="Στελέχη εκπαίδευσης">
                <option value="regional_director">Περιφερειακός Διευθυντής Εκπαίδευσης — 1.170 €</option>
                <option value="regional_quality_supervisor">Περιφερειακός Επόπτης Ποιότητας — 780 €</option>
                <option value="education_director">Διευθυντής Π/θμιας ή Δ/θμιας Εκπαίδευσης — 715 €</option>
                <option value="quality_supervisor">Επόπτης Ποιότητας της Εκπαίδευσης — 650 €</option>
                <option value="education_counselor">Σύμβουλος Εκπαίδευσης — 455 €</option>
                <option value="kedasy_head">Προϊστάμενος ΚΕ.Δ.Α.Σ.Υ. / Γραφείου Μειονοτικής Εκπαίδευσης — 455 €</option>
                <option value="education_matters_head">Προϊστάμενος Τμήματος Εκπαιδευτικών Θεμάτων — 390 €</option>
              </optgroup>
              <optgroup label="Σχολικές μονάδες / δομές">
                <option value="lyceum_director">Διευθυντής ΓΕΛ / ΕΠΑΛ / αντίστοιχης δομής — 429 €</option>
                <option value="lyceum_director_large">Διευθυντής ΓΕΛ / ΕΠΑΛ / αντίστοιχης δομής ≥120 μαθητές (Σ.Μ.Ε.Α.Ε. ≥30) — 501 €</option>
                <option value="gymnasium_director">Διευθυντής Γυμνασίου / Ε.Κ. / αντίστοιχης δομής — 358 €</option>
                <option value="gymnasium_director_large">Διευθυντής Γυμνασίου / αντίστοιχης δομής ≥120 μαθητές (Σ.Μ.Ε.Α.Ε. ≥30) — 429 €</option>
                <option value="vice_director">Υποδιευθυντής / Υπεύθυνος Τομέα Ε.Κ. / αντίστοιχη θέση — 195 €</option>
                <option value="small_school_head">Προϊστάμενος 1θέσιου–3θέσιου Δημοτικού / Νηπιαγωγείου — 215 €</option>
              </optgroup>
            </select>
            <small>Τα ποσά είναι τα ισχύοντα μετά την αύξηση 30% από 01-01-2024. Σε συρροή θέσεων επιλέγεται μόνο η ανώτερη θέση.</small>
          </div>
          <div class="edu-field--full">
            <div class="checkrow">
              <input id="remoteAreaAllowance" type="checkbox">
              <label for="remoteAreaAllowance">Επίδομα απομακρυσμένων - παραμεθορίων περιοχών <strong>(+100 € μικτά / μήνα)</strong><small>Επίλεξέ το μόνο αν υπηρετείς σε περιοχή/μονάδα που θεμελιώνει δικαίωμα καταβολής του επιδόματος.</small></label>
            </div>
          </div>
          <div class="edu-field--full">
            <div class="checkrow">
              <input id="maternityPensionReduction" type="checkbox">
              <label for="maternityPensionReduction">Μειωμένη εισφορά κύριας σύνταξης λόγω μητρότητας <strong>(50%)</strong><small>Επίλεξέ το μόνο όταν η μείωση εφαρμόζεται ή έχει αναγνωριστεί στη μισθοδοσία σου. Μειώνει μόνο το σκέλος κύριας σύνταξης από 6,67% σε 3,335%.</small></label>
            </div>
          </div>
          <?php calculatorDisclosure(array(
            'id' => 'insuredStatusInfoPanel',
            'class' => 'edu-field--full',
            'summary' => 'Τι αλλάζει μεταξύ παλαιού και νέου ασφαλισμένου;',
            'html' => '<p><strong>Παλαιός ασφαλισμένος</strong> είναι όποιος ασφαλίστηκε πρώτη φορά για κύρια σύνταξη έως 31/12/1992. Για υπάλληλο του ενιαίου μισθολογίου, στην παρούσα εκτίμηση η κύρια και επικουρική σύνταξη υπολογίζονται σε <strong>βασικό μισθό + επίδομα θέσης ευθύνης</strong>, η εισφορά ΤΠΔΥ 4% στον <strong>βασικό μισθό</strong>, ενώ για ΜΤΠΥ εφαρμόζεται 4,5% σε βασικό + θέση και 1% στην οικογενειακή παροχή και στο παραμεθόριο.</p><p><strong>Νέος ασφαλισμένος</strong> είναι όποιος ασφαλίστηκε πρώτη φορά από 01/01/1993. Στα στοιχεία αποδοχών που υποστηρίζει το εργαλείο, στη βάση κύριας/επικουρικής σύνταξης περιλαμβάνονται <strong>βασικός μισθός + οικογενειακή παροχή + θέση ευθύνης + παραμεθόριο</strong>. Η ίδια βάση χρησιμοποιείται για ΤΠΔΥ 4% και ΜΤΠΥ 4,5%.</p><p><strong>Επικουρική e-ΕΦΚΑ ή ΤΕΚΑ:</strong> για τον νέο ασφαλισμένο η εργατική εισφορά επικουρικής παραμένει <strong>3%</strong> και η <strong>βάση υπολογισμού παραμένει ίδια</strong>. Η επιλογή ΤΕΚΑ αλλάζει μόνο τον φορέα που αναγράφεται στη σύνθεση κρατήσεων και στην εκτύπωση· δεν αλλάζει το ποσό των κρατήσεων από μόνη της.</p><small>Η υγειονομική περίθαλψη 2,05% και η εισφορά 2% για την καταπολέμηση της ανεργίας υπολογίζονται στις αντίστοιχες τακτικές αποδοχές και δεν διαφοροποιούνται εδώ μόνο λόγω χαρακτηρισμού «παλαιός/νέος» ή φορέα επικουρικής. Ειδικά ασφαλιστικά καθεστώτα μπορεί να απαιτούν διαφορετική αντιμετώπιση.</small>'
          )); ?>
          <?php calculatorDisclosure(array(
            'id' => 'maternityEligibilityPanel',
            'class' => 'edu-field--full',
            'summary' => 'Ποια μισθωτή μητέρα δικαιούται τη μείωση;',
            'html' => '<p>Σύμφωνα με τον e-ΕΦΚΑ, οι <strong>μητέρες μισθωτές ασφαλισμένες</strong>, εφόσον πληρούν τις λοιπές προϋποθέσεις, δικαιούνται μείωση κατά <strong>50% της εργατικής εισφοράς του κλάδου κύριας σύνταξης</strong> για το σχετικό δωδεκάμηνο.</p><p>Αν έχει ληφθεί <strong>επιδότηση λόγω λοχείας</strong>, η μείωση εφαρμόζεται από τη λήξη της επιδότησης για το επόμενο δωδεκάμηνο. Αν δεν έχει ληφθεί επιδότηση λοχείας, το κρίσιμο δωδεκάμηνο αρχίζει από την <strong>1η του επόμενου μήνα του τοκετού</strong>. Ως χρόνος απασχόλησης μπορεί να λογίζεται και άδεια <strong>με αποδοχές</strong>, σύμφωνα με τις διευκρινίσεις του e-ΕΦΚΑ.</p><small>Το εργαλείο δεν αποφασίζει αν θεμελιώνεται το δικαίωμα. Αν η μισθοδοσία παρακράτησε ολόκληρη την εισφορά, ο e-ΕΦΚΑ προβλέπει διαδικασία επιστροφής αχρεωστήτως καταβληθεισών εισφορών, όταν πληρούνται οι προϋποθέσεις.</small>'
          )); ?>
          <?php calculatorDisclosure(array(
            'id' => 'disabilityTaxInfoPanel',
            'class' => 'edu-field--full',
            'summary' => 'Τι αλλάζει φορολογικά λόγω αναπηρίας;',
            'html' => '<p>Για πιστοποιημένη αναπηρία <strong>67%–79,99%</strong>, το εργαλείο εφαρμόζει πρόσθετη <strong>μείωση φόρου έως 200 € τον χρόνο</strong>, μέχρι το ύψος του φόρου που απομένει μετά τη μείωση του άρθρου 16 ΚΦΕ.</p><p>Για ποσοστό αναπηρίας <strong>τουλάχιστον 80%</strong>, οι μισθοί απαλλάσσονται από τον φόρο εισοδήματος. Στην εκτίμηση η <strong>μηνιαία παρακράτηση φόρου γίνεται 0 €</strong>.</p><small>Η ειδική φορολογική μεταχείριση δεν μηδενίζει τις ασφαλιστικές ή λοιπές κρατήσεις. Το εργαλείο δεν ελέγχει το είδος ή την ισχύ της πιστοποίησης αναπηρίας ούτε αν αυτή έχει καταχωριστεί στη μισθοδοσία.</small>'
          )); ?>
          <?php calculatorDisclosureStart(array(
            'id' => 'otherDeductionsPanel',
            'class' => 'edu-field--full',
            'summary_html' => 'Λοιπές κρατήσεις <small>· προαιρετικά</small>'
          )); ?>
            <div class="field-grid payroll-extra-deductions-grid">
              <div class="field">
                <label for="adedYDeduction">ΑΔΕΔΥ</label>
                <input id="adedYDeduction" type="number" min="0" step="0.01" value="0" inputmode="decimal">
              </div>
              <div class="field">
                <label for="federationDeduction">ΟΛΜΕ / ΔΟΕ</label>
                <input id="federationDeduction" type="number" min="0" step="0.01" value="0" inputmode="decimal">
              </div>
              <div class="field">
                <label for="associationDeduction">Σύλλογος</label>
                <input id="associationDeduction" type="number" min="0" step="0.01" value="0" inputmode="decimal">
              </div>
              <div class="field">
                <label for="otherPayrollDeduction">Άλλο ποσό</label>
                <input id="otherPayrollDeduction" type="number" min="0" step="0.01" value="0" inputmode="decimal">
              </div>
            </div>
            <small>Οι λοιπές κρατήσεις αφαιρούνται από το τελικό πληρωτέο και δεν μειώνουν το φορολογητέο εισόδημα της εκτίμησης.</small>
          <?php calculatorDisclosureEnd(); ?>
        </div>
        <?php calculatorDisclosure(array(
          'summary' => 'Τι περιλαμβάνει η εκτίμηση καθαρών;',
          'html' => 'Η εκτίμηση καθαρών γίνεται πάνω στον <strong>βασικό μισθό του Μ.Κ.</strong>, στην <strong>οικογενειακή παροχή</strong> που αντιστοιχεί στον δηλωμένο αριθμό τέκνων, στο τυχόν <strong>επίδομα θέσης ευθύνης</strong> και, αν επιλεγεί, στο <strong>επίδομα απομακρυσμένων - παραμεθορίων περιοχών</strong>, με 12μηνη φορολογική αναγωγή. Δεν προστίθενται προσωπική διαφορά ή άλλες αποδοχές. Η προαιρετική <strong>μειωμένη εισφορά κύριας σύνταξης λόγω μητρότητας</strong> μειώνει μόνο το αντίστοιχο ασφαλιστικό σκέλος. Η <strong>φορολογική ρύθμιση λόγω αναπηρίας</strong> επηρεάζει μόνο τον φόρο εισοδήματος και όχι τις ασφαλιστικές κρατήσεις. Οι προαιρετικές <strong>λοιπές κρατήσεις</strong> αφαιρούνται μόνο από το τελικό πληρωτέο και δεν μεταβάλλουν τον υπολογισμό φόρου. Αν ο αριθμός τέκνων που λαμβάνεται υπόψη για φορολογία διαφέρει από εκείνον της οικογενειακής παροχής, η εκτίμηση χρειάζεται διοικητικό έλεγχο.'
        )); ?>
        <?php calculatorDisclosure(array(
          'summary' => 'Τι γίνεται με τη διετία 2016–2017;',
          'html' => '<strong>Αναστολή μισθολογικής εξέλιξης 2016–2017:</strong> ο δηλωμένος χρόνος της συγκεκριμένης διετίας (έως 24 μήνες) <strong>δεν λαμβάνεται υπόψη για μισθολογική εξέλιξη</strong> και αφαιρείται αυτόματα από τον υπολογισμό του Μ.Κ. Αν δεν έχεις υπηρεσία μέσα στη διετία, άφησε Έτη και Μήνες στο 0.'
        )); ?>
        <?php calculatorDisclosure(array(
          'summary' => 'Τι πρέπει να έχει ήδη αναγνωριστεί;',
          'html' => 'Καταχώρισε <strong>μόνο</strong> χρόνο προϋπηρεσίας και τίτλο που έχουν ήδη αναγνωριστεί μισθολογικά από την αρμόδια υπηρεσία. Το εργαλείο δεν κρίνει αν μια προϋπηρεσία είναι αναγνωρίσιμη ούτε αν ένας τίτλος είναι συναφής.'
        )); ?>
        <?php calculatorDisclosure(array(
          'summary' => 'Integrated Master από 01/01/2026',
          'html' => 'Από <strong>01-01-2026</strong>, η ειδική προώθηση κατά <strong>2 Μ.Κ.</strong> για Integrated Master αφορά ενιαίο και αδιάσπαστο τίτλο <strong>ελληνικού Α.Ε.Ι.</strong> που εμπίπτει στο άρθρο 46 του ν. 4485/2017 ή στο άρθρο 78 του ν. 4957/2022. <strong>Integrated Master αλλοδαπής δεν καλύπτεται από αυτή την ειδική ρύθμιση.</strong> Επίσης δεν χορηγείται δεύτερη προώθηση όταν έχει ήδη δοθεί προώθηση λόγω διακριτού μεταπτυχιακού τίτλου.'
        )); ?>
        <?php calculatorDisclosure(array(
          'summary' => 'Ειδικές περιπτώσεις μισθολογικής προώθησης',
          'html' => '<strong>Ειδικές περιπτώσεις:</strong> η δυνατότητα μισθολογικής προώθησης δεν προκύπτει μόνο από την κατηγορία ΠΕ/ΤΕ/ΔΕ ή από την απλή κατοχή ενός τίτλου. Για μεταπτυχιακό ή διδακτορικό απαιτούνται οι νόμιμες προϋποθέσεις και, όπου απαιτείται, αναγνώριση συνάφειας από το αρμόδιο υπηρεσιακό όργανο. Η κατηγορία <strong>ΔΕ</strong> δεν αποκλείεται αυτομάτως, αλλά η αναγνώριση πρέπει να έχει προηγηθεί υπηρεσιακά. Ιδιαίτερη προσοχή απαιτείται σε περιπτώσεις όπως ο κλάδος <strong>ΤΕ16</strong>, όπου έχουν τεθεί ειδικά ζητήματα ως προς τον βασικό τίτλο διορισμού και τη μισθολογική αναγνώριση μεταπτυχιακών τίτλων. <strong>Μην επιλέγεις +2 ή +6 Μ.Κ. μόνο επειδή κατέχεις τον τίτλο· επίλεξέ το μόνο αν η αντίστοιχη μισθολογική προώθηση έχει ήδη αναγνωριστεί από την υπηρεσία σου.</strong>'
        )); ?>

        <?php calculatorActions(array(
          array('id' => 'printBtn', 'class' => 'edu-btn-primary', 'label' => 'Εκτύπωση'),
          array('id' => 'resetBtn', 'class' => 'secondary', 'label' => 'Καθαρισμός')
        )); ?>
      <?php calculatorCardEnd(); ?>

      <?php calculatorResultMessage(array(
        'variant' => 'disclaimer',
        'html' => '<strong>Η εκτίμηση καθαρών είναι ενδεικτική.</strong> Δεν υποκαθιστά επίσημη μισθοδοσία ή φορολογική εκκαθάριση. Οι παραδοχές, οι εξαιρέσεις και οι ασφαλιστικές βάσεις αναλύονται στις παραπάνω επεξηγήσεις και στις πηγές.'
      )); ?>
    <?php calculatorMainEnd(); ?>

    <?php calculatorResultsStart(array('aria_live' => 'polite')); ?>
      <?php calculatorScoreHeader(array(
        'value' => 'Μ.Κ. 1',
        'value_id' => 'finalMkResult',
        'label' => 'ενδεικτικό Μισθολογικό Κλιμάκιο',
        'cap' => 'Με βάση τα αναγνωρισμένα στοιχεία'
      )); ?>
      <?php calculatorResultRow(array('label' => 'Κατηγορία', 'value' => 'ΠΕ', 'value_id' => 'categoryResult')); ?>
      <?php calculatorResultRow(array('label' => 'Βασικός μισθός (μικτά) από 01/04/2026', 'value' => '1.232 €', 'value_id' => 'basicSalaryResult')); ?>

      <?php calculatorDisclosureStart(array(
        'class' => 'edu-result-disclosure',
        'summary' => 'Ανάλυση μισθολογικής κατάταξης',
        'open' => true,
        'attrs' => array('data-mobile-collapsed' => 'true')
      )); ?>
        <?php calculatorResultRow(array('label' => 'Αφαιρούμενος χρόνος 2016–2017', 'value' => '0 μήνες', 'value_id' => 'suspendedServiceResult')); ?>
        <?php calculatorResultRow(array('label' => 'Μετρήσιμος χρόνος για Μ.Κ.', 'value' => '0 μήνες', 'value_id' => 'countableServiceResult')); ?>
        <?php calculatorResultRow(array('label' => 'Μ.Κ. από υπηρεσία', 'value' => 'Μ.Κ. 1', 'value_id' => 'baseMkResult')); ?>
        <?php calculatorResultRow(array('label' => 'Προώθηση τίτλου', 'value' => '0 Μ.Κ.', 'value_id' => 'promotionResult')); ?>
        <?php calculatorResultRow(array('label' => 'Χρόνος προς επόμενο Μ.Κ.', 'value' => '24 μήνες', 'value_id' => 'nextMkResult')); ?>
      <?php calculatorDisclosureEnd(); ?>

      <h3>Ενδεικτικές καθαρές αποδοχές</h3>
      <?php calculatorResultRow(array('label' => 'Σύνολο μικτών για εκτίμηση', 'value' => '1.232,00 €', 'value_id' => 'grossForNetResult')); ?>
      <?php calculatorResultRow(array('label' => 'Τακτικές κρατήσεις', 'value' => '0,00 €', 'value_id' => 'standardDeductionsResult')); ?>
      <?php calculatorResultRow(array('label' => 'Μηνιαία παρακράτηση φόρου', 'value' => '0,00 €', 'value_id' => 'monthlyTaxResult')); ?>
      <?php calculatorSubtotalRow(array('label' => 'Εκτιμώμενο καθαρό', 'value' => '0,00 €', 'value_id' => 'estimatedNetResult')); ?>

      <?php calculatorDisclosureStart(array(
        'class' => 'edu-result-disclosure',
        'summary' => 'Σύνθεση αποδοχών & ασφαλιστικές βάσεις',
        'open' => true,
        'attrs' => array('data-mobile-collapsed' => 'true')
      )); ?>
        <?php calculatorResultRow(array('label' => 'Οικογενειακή παροχή', 'value' => '0,00 €', 'value_id' => 'familyAllowanceResult')); ?>
        <?php calculatorResultRow(array('label' => 'Επίδομα θέσης ευθύνης', 'value' => '0,00 €', 'value_id' => 'positionAllowanceResult')); ?>
        <?php calculatorResultRow(array('label' => 'Επίδομα απομακρυσμένων - παραμεθορίων', 'value' => '0,00 €', 'value_id' => 'remoteAllowanceResult')); ?>
        <?php calculatorResultRow(array('label' => 'Προφίλ κρατήσεων', 'value' => 'Μόνιμος δημόσιος υπάλληλος', 'value_id' => 'payrollProfileResult')); ?>
        <?php calculatorResultRow(array('label' => 'Ασφαλιστική ιδιότητα', 'value' => 'Νέος ασφαλισμένος — από 01/01/1993 · επικουρική e-ΕΦΚΑ', 'value_id' => 'insuredStatusResult')); ?>
        <?php calculatorResultRow(array('label' => 'Βάσεις ασφαλιστικών κρατήσεων', 'value' => '—', 'value_id' => 'insuranceBasesResult')); ?>
      <?php calculatorDisclosureEnd(); ?>

      <?php calculatorDisclosureStart(array(
        'class' => 'edu-result-disclosure',
        'summary' => 'Κρατήσεις & φορολογική ανάλυση',
        'open' => true,
        'attrs' => array('data-mobile-collapsed' => 'true')
      )); ?>
        <?php calculatorResultRow(array('label' => 'Μείωση εισφοράς μητρότητας', 'value' => '0,00 €', 'value_id' => 'maternityPensionReductionResult')); ?>
        <?php calculatorResultRow(array(
          'label' => 'Σύνθεση κρατήσεων',
          'value_html' => '<button type="button" class="payroll-deduction-toggle" id="deductionBreakdownToggle" aria-expanded="false" aria-controls="deductionBreakdownDetails"><span>Ανάλυση</span><span class="payroll-deduction-toggle-icon" aria-hidden="true">▾</span></button>'
        )); ?>
        <div id="deductionBreakdownDetails" class="payroll-deduction-details" hidden></div>
        <span id="deductionBreakdownResult" hidden></span>
        <?php calculatorResultRow(array('label' => 'Δικαίωμα εγγραφής ΜΤΠΥ', 'value' => '0,00 €', 'value_id' => 'registrationDeductionResult')); ?>
        <?php calculatorResultRow(array('label' => 'Λοιπές κρατήσεις', 'value' => '0,00 €', 'value_id' => 'otherDeductionsResult')); ?>
        <?php calculatorResultRow(array('label' => 'Ανάλυση λοιπών κρατήσεων', 'value' => '—', 'value_id' => 'otherDeductionsBreakdownResult')); ?>
        <?php calculatorResultRow(array('label' => 'Ετήσιο φορολογητέο (12μηνη αναγωγή)', 'value' => '0,00 €', 'value_id' => 'taxableAnnualResult')); ?>
        <?php calculatorResultRow(array('label' => 'Φόρος κλίμακας πριν τη μείωση', 'value' => '0,00 €', 'value_id' => 'taxBeforeCreditResult')); ?>
        <?php calculatorResultRow(array('label' => 'Μείωση φόρου άρθρου 16 ΚΦΕ', 'value' => '0,00 €', 'value_id' => 'taxCreditResult')); ?>
        <?php calculatorResultRow(array('label' => 'Μείωση / απαλλαγή φόρου λόγω αναπηρίας', 'value' => '0,00 €', 'value_id' => 'disabilityTaxReliefResult')); ?>
        <?php calculatorResultRow(array('label' => 'Ετήσιος φόρος', 'value' => '0,00 €', 'value_id' => 'annualTaxResult')); ?>
      <?php calculatorDisclosureEnd(); ?>
      <?php calculatorResultMessage(array('variant' => 'status', 'id' => 'statusResult', 'html' => 'Συμπλήρωσε τα αναγνωρισμένα στοιχεία για να δεις το Μ.Κ.')); ?>
    <?php calculatorResultsEnd(); ?>
  <?php calculatorColumnsEnd(); ?>
<?php calculatorContainerEnd(); ?>

<section id="payrollPrintSheet" class="payroll-print-sheet" aria-hidden="true">
  <h1>ΕΝΔΕΙΚΤΙΚΟ ΜΙΣΘΟΛΟΓΙΚΟ ΣΗΜΕΙΩΜΑ</h1>
  <p class="print-subtitle">Υπολογισμός Μισθολογικού Κλιμακίου (Μ.Κ.) / Μισθοδοσίας</p>
  <div id="payrollPrintContent"></div>
  <p class="print-note"><strong>Σημείωση:</strong> Το παρόν είναι υπολογιστικό βοήθημα και δεν αποτελεί επίσημο ενημερωτικό σημείωμα αποδοχών, πράξη μισθοδοσίας ή φορολογική εκκαθάριση.</p>
  <div class="print-meta" id="payrollPrintGeneratedAt"></div>
</section>

<?php sourceCardDisclosureStart(array('mobile_collapsed' => true, 'open' => true)); ?>
  <p>Ο ν. 4354/2015 προβλέπει 19 Μ.Κ. για ΠΕ/ΤΕ και 13 για ΔΕ/ΥΕ. Για τη συνήθη μισθολογική εξέλιξη απαιτούνται δύο έτη ανά Μ.Κ. για ΠΕ/ΤΕ και τρία έτη για ΔΕ/ΥΕ. Αναγνωρισμένος συναφής μεταπτυχιακός τίτλος προωθεί κατά 2 Μ.Κ. και διδακτορικό κατά 6 Μ.Κ. στην κατηγορία όπου ανήκει ο υπάλληλος. Από 01-01-2026, ο ν. 5246/2025 προσθέτει ειδική προώθηση +2 Μ.Κ. για Integrated Master που εμπίπτει στις διατάξεις των άρθρων 46 ν. 4485/2017 και 78 ν. 4957/2022.</p>
  <p>Με το άρθρο 26 παρ. 2 του ν. 4354/2015 η μισθολογική εξέλιξη ανεστάλη έως 31-12-2017. Από 01-01-2018 ενεργοποιήθηκε εκ νέου, χωρίς να λαμβάνεται υπόψη για την εξέλιξη το χρονικό διάστημα 01-01-2016 έως 31-12-2017.</p>
  <p><strong>Βασικοί μισθοί 2026:</strong> η εγκύκλιος ΥΠΕΘΟΟ <strong>54692 ΕΞ 2026/03-04-2026</strong> (ΑΔΑ: <strong>ΨΕ7ΨΗ-ΚΧΧ</strong>) αναπροσαρμόζει από 01-04-2026 τους βασικούς μισθούς και στο Παράρτημα, Πίνακες 1–4, αποτυπώνει τα ποσά ανά Μ.Κ. για ΠΕ, ΤΕ, ΔΕ και ΥΕ.</p>
  <p><strong>Οικογενειακή παροχή:</strong> το άρθρο 15 του ν. 4354/2015, όπως ισχύει μετά τον ν. 5045/2023, προβλέπει μηνιαία παροχή <strong>70 € για 1 τέκνο, 120 € για 2, 170 € για 3, 220 € για 4 και +70 € για κάθε επιπλέον τέκνο</strong>. Στην εκτίμηση το ποσό προστίθεται στις μικτές αποδοχές πριν από τον υπολογισμό κρατήσεων και φόρου.</p>
  <p><strong>Επίδομα θέσης ευθύνης:</strong> το άρθρο 16 του ν. 4354/2015, όπως ισχύει για τα στελέχη εκπαίδευσης μετά τον ν. 4823/2021, προβλέπει μηνιαίο επίδομα ανά θέση. Με το άρθρο 22 του ν. 5045/2023 τα ποσά αυξήθηκαν κατά <strong>30%</strong> από 01-01-2024 και στρογγυλοποιούνται στην πλησιέστερη μονάδα ευρώ. Ενδεικτικά: Διευθυντής ΓΕΛ/ΕΠΑΛ <strong>429 € ή 501 €</strong>, Διευθυντής Γυμνασίου <strong>358 € ή 429 €</strong>, Υποδιευθυντής <strong>195 €</strong>. Σε συρροή αξιώσεων από δύο βαθμίδες καταβάλλεται μόνο το ποσό της ανώτερης βαθμίδας. Το εργαλείο προσθέτει μία μόνο επιλεγμένη θέση στις μικτές αποδοχές.</p>
  <p><strong>Επίδομα απομακρυσμένων - παραμεθορίων περιοχών:</strong> το άρθρο 19 του ν. 4354/2015 διατηρεί το επίδομα στο ίδιο ύψος και με τις ίδιες προϋποθέσεις· για τους δικαιούχους το ποσό είναι <strong>100 € μικτά τον μήνα</strong>. Η επιλογή στο εργαλείο είναι προαιρετική και δεν ελέγχει αν η συγκεκριμένη περιοχή/υπηρεσία θεμελιώνει δικαίωμα. Το ποσό προστίθεται στις μικτές αποδοχές, αλλά η <strong>βάση κάθε ασφαλιστικής κράτησης</strong> εξαρτάται πλέον και από την ασφαλιστική ιδιότητα: για παράδειγμα στο ΜΤΠΥ το παραμεθόριο επιβαρύνεται με 4,5% για νέο ασφαλισμένο και 1% για παλαιό.</p>
  <p><strong>Φορολογία 2026:</strong> ο ν. 5246/2025 τροποποίησε από το φορολογικό έτος 2026 την κλίμακα μισθωτών, με ειδικούς συντελεστές ανά αριθμό εξαρτώμενων τέκνων και για ηλικίες έως 25 και 26–30 ετών. Εφαρμόζεται επίσης η μείωση φόρου του άρθρου 16 ΚΦΕ.</p>
  <p><strong>Αναπηρία και φόρος εισοδήματος:</strong> για πιστοποιημένη αναπηρία <strong>67% και άνω</strong> προβλέπεται πρόσθετη μείωση φόρου <strong>200 €</strong> σύμφωνα με το άρθρο 17 ΚΦΕ. Για μισθούς, συντάξεις και πάγια αντιμισθία που χορηγούνται σε πρόσωπα με αναπηρία <strong>τουλάχιστον 80%</strong>, το άρθρο 14 παρ. 2 περ. ε΄ ΚΦΕ προβλέπει απαλλαγή από τον φόρο. Στον υπολογιστή το 67%–79,99% εφαρμόζεται ως μείωση έως 200 € στον ετήσιο φόρο, ενώ το ≥80% μηδενίζει τον φόρο μισθωτής εργασίας. Οι ασφαλιστικές κρατήσεις δεν αλλάζουν από αυτή την επιλογή.</p>
  <p><strong>Παλαιός / νέος ασφαλισμένος και βάσεις εισφορών:</strong> κριτήριο είναι η <strong>πρώτη υπαγωγή σε κύρια ασφάλιση</strong>: παλαιός έως 31-12-1992, νέος από 01-01-1993. Για υπαλλήλους του ενιαίου μισθολογίου ν. 4354/2015, οι συντάξιμες αποδοχές του παλαιού ασφαλισμένου περιλαμβάνουν, για τις συνήθεις περιπτώσεις εκπαιδευτικών, <strong>βασικό μισθό + επίδομα θέσης ευθύνης</strong>, ενώ του νέου περιλαμβάνουν <strong>βασικό + οικογενειακή παροχή + θέση ευθύνης + επίδομα απομακρυσμένων/παραμεθορίων</strong>. Η επικουρική ακολουθεί τις αντίστοιχες βάσεις. Για το τ. ΤΠΔΥ η εισφορά είναι 4%: στον παλαιό επί του <strong>βασικού μισθού</strong>, στον νέο επί των ασφαλιστέων αποδοχών κύριας σύνταξης. Στο ΜΤΠΥ εφαρμόζεται 4,5% στις συντάξιμες αποδοχές· για τον παλαιό, στις μη συντάξιμες οικογενειακή παροχή και παραμεθόριο εφαρμόζεται 1%. Η υγειονομική περίθαλψη του ασφαλισμένου είναι από 01-01-2025 συνολικά 2,05% επί των πάσης φύσεως αποδοχών και η εισφορά ανεργίας 2% επί των τακτικών αποδοχών/πρόσθετων αμοιβών που εμπίπτουν στη ρύθμιση.</p>
  <p><strong>ΤΕΚΑ:</strong> για μισθωτούς που υπάγονται στην επικουρική ασφάλιση του ΤΕΚΑ, το ύψος της εισφοράς παραμένει ίδιο με την επικουρική του e-ΕΦΚΑ: <strong>3% ασφαλισμένου + 3% εργοδότη</strong> από 01-06-2022. Σύμφωνα με το ΤΕΚΑ, παραμένουν ίδια και η <strong>βάση υπολογισμού</strong> και το ανώτατο όριο ασφαλιστέων αποδοχών. Στο εργαλείο, συνεπώς, η επιλογή ΤΕΚΑ δεν αλλάζει το ποσό της εργατικής επικουρικής εισφοράς· αλλάζει την ονομασία του φορέα στην ανάλυση και στην εκτύπωση.</p>
  <p><strong>Τυπικά προφίλ κρατήσεων:</strong> για μόνιμο ή νεοδιόριστο το εργαλείο δεν χρησιμοποιεί πλέον ένα ενιαίο 22,22% πάνω σε όλα τα μικτά όταν υπάρχουν επιδόματα, αλλά υπολογίζει <strong>κάθε κράτηση στη νόμιμη βάση της</strong> ανάλογα με την ασφαλιστική ιδιότητα. Όταν οι αποδοχές αποτελούνται μόνο από βασικό μισθό, το τυπικό άθροισμα παραμένει 22,22%. Για νεοδιόριστο προστίθεται ενδεικτικά το δικαίωμα εγγραφής ΜΤΠΥ — οι ακαθάριστες αποδοχές του πρώτου ολόκληρου μήνα σε 12 ισόποσες δόσεις. Για αναπληρωτή/ΙΔΟΧ χρησιμοποιείται το ποσοστό ασφαλισμένου 13,37% του ΚΠΚ 101 και η επιλογή παλαιού/νέου δεν μεταβάλλει αυτό το προφίλ στην παρούσα έκδοση.</p>
  <p><strong>Μειωμένη εισφορά μητρότητας:</strong> ο e-ΕΦΚΑ προβλέπει για τις δικαιούχες <strong>μητέρες μισθωτές ασφαλισμένες</strong> μείωση κατά <strong>50%</strong> της εργατικής εισφοράς του κλάδου κύριας σύνταξης για το σχετικό δωδεκάμηνο. Αν έχει ληφθεί επιδότηση λόγω λοχείας, το δωδεκάμηνο ακολουθεί τη λήξη της επιδότησης· αν δεν έχει ληφθεί, αρχίζει από την 1η του επόμενου μήνα του τοκετού. Ως χρόνος απασχόλησης μπορεί να θεωρείται και άδεια με αποδοχές. Στην παρούσα εκτίμηση η εισφορά κύριας σύνταξης μειώνεται από <strong>6,67% σε 3,335%</strong> πάνω στην <strong>αντίστοιχη βάση κύριας σύνταξης παλαιού ή νέου ασφαλισμένου</strong>, όχι κατ’ ανάγκη πάνω στο σύνολο των μικτών. Η επιλογή είναι χειροκίνητη και το εργαλείο δεν κρίνει αν πληρούνται οι προϋποθέσεις.</p>
  <p><strong>Λοιπές κρατήσεις:</strong> ποσά όπως ΑΔΕΔΥ, ΟΛΜΕ/ΔΟΕ, οικείος σύλλογος ή άλλη ειδική κράτηση δεν θεωρούνται καθολικά ίδια για κάθε υπάλληλο. Για αυτό εισάγονται προαιρετικά ως πραγματικά μηνιαία ποσά και αφαιρούνται από το πληρωτέο <strong>μετά</strong> τον υπολογισμό των τυπικών ασφαλιστικών κρατήσεων και της φορολογίας.</p>
  <?php sourceCardLinksStart(); ?>
    <?php sourceCardLink('https://www.e-nomothesia.gr/kat-demosion-upallelon/nomos-4354-2015.html', 'Ν. 4354/2015 — Μισθολογικά κλιμάκια & άρθρο 26 ↗'); ?>
    <?php sourceCardLink('https://www.e-nomothesia.gr/kat-oikonomia/n-5045-2023.html', 'Ν. 5045/2023 — Αναπροσαρμογή οικογενειακής παροχής ↗'); ?>
    <?php sourceCardLink('https://www.taxheaven.gr/circulars/50271/2-97758-dep-19-10-2023', 'ΥΠΕΘΟΟ 2/97758/ΔΕΠ/19-10-2023 — Επίδομα θέσης ευθύνης από 01/01/2024 ↗'); ?>
    <?php sourceCardLink('https://www.gsis.gr/sites/default/files/2024-05/27-5-2024_Odigies_Simplirosis_e-DAYK.pdf', 'ΓΓΠΣ/e-ΔΑΥΚ — Πίνακας επιδομάτων θέσης εκπαιδευτικών ↗'); ?>
    <?php sourceCardLink('https://www.taxheaven.gr/circulars/23568/ar-prwt-2-31029-dep-6-5-2016', 'Εγκύκλιος ΓΛΚ 2/31029/ΔΕΠ/06-05-2016 ↗'); ?>
    <?php sourceCardLink('https://www.e-nomothesia.gr/index.php/n-5246-2025.html', 'Ν. 5246/2025 — Integrated Master ↗'); ?>
    <?php sourceCardLink('https://www.taxheaven.gr/circulars/52538/54692-ex-03-04-2026', 'ΥΠΕΘΟΟ 54692 ΕΞ 2026 — Βασικοί μισθοί από 01/04/2026 ↗'); ?>
    <?php sourceCardLink('https://www.minedu.gov.gr/site/18335-26-02-16-epidoma-apomakrysmenon-paramethorion-perioxon', 'ΥΠΑΙΘ — Επίδομα απομακρυσμένων / παραμεθορίων 100 € ↗'); ?>
    <?php sourceCardLink('https://www.gsis.gr/polites-epiheiriseis/pliromes-kai-eispraxeis/e-DAYK/e-dayk-announcements/epidomata-neon-asfalismenon-yper-mtpy', 'ΓΓΠΣ — Κράτηση ΜΤΠΥ στα επιδόματα νέων ασφαλισμένων ↗'); ?>
    <?php sourceCardLink('https://diavgeia.gov.gr/doc/%CE%A955%CE%98%CE%97-%CE%A60%CE%91?inline=true', 'ΜΤΠΥ 77088/03-10-2017 — Νέο ποσοστό κρατήσεων (ΑΔΑ: Ψ55ΘΗ-Φ0Α) ↗'); ?>
    <?php sourceCardLink('https://minfin.gov.gr/forologiki-politiki/forologikos-odigos/forologia-eisodimatos/', 'ΥΠΕΘΟΟ — Φορολογία εισοδήματος 2026 ↗'); ?>
    <?php sourceCardLink('https://www.aade.gr/egkyklioi-kai-apofaseis/o-3068-18-11-2025', 'ΑΑΔΕ Ο.3068/2025 — Ν. 5246/2025 ↗'); ?>
    <?php sourceCardLink('https://www.aade.gr/exypiretisi-enimerosi/hristikoi-odigoi/hristikos-odigos-gia-ta-basika-forologika-dikaiomata-ton-amea/foros-eisodimatos-fysikon', 'ΑΑΔΕ — Φορολογικά δικαιώματα ΑμεΑ: απαλλαγή ≥80% & μείωση 200 € ≥67% ↗'); ?>
    <?php sourceCardLink('https://www.e-efka.gov.gr/sites/default/files/2020-03/%CE%95%CE%93%CE%9A%CE%A5%CE%9A%CE%9B%CE%99%CE%9F%CE%A3%2010_2020_%CE%91%CE%A0%CE%94%20%CE%94%CE%97%CE%9C%CE%9F%CE%A3%CE%99%CE%9F%CE%A5%20%28%CE%A1%CE%A8%CE%A8%CE%9C465%CE%A7%CE%A0%CE%99-4%CE%A43%29.pdf', 'e-ΕΦΚΑ Εγκ. 10/2020 — παλαιοί/νέοι ασφαλισμένοι, βάσεις σύνταξης & ΤΠΔΥ ↗'); ?>
    <?php sourceCardLink('https://teka.gov.gr/ergodotes', 'ΤΕΚΑ — Εισφορές μισθωτών: ίδιο ποσοστό και ίδια βάση με την επικουρική e-ΕΦΚΑ ↗'); ?>
    <?php sourceCardLink('https://myportal.mtpy.gr/backend1/uploads/ODIGOS_KRATISEON_IOYNIOS_2020_5dc7497fbd.pdf', 'ΜΤΠΥ — Επικαιροποιημένος Οδηγός Κρατήσεων Ιουνίου 2020 ↗'); ?>
    <?php sourceCardLink('https://www.taxheaven.gr/law/4670/2020/arthro/31', 'Ν. 4670/2020 άρθρο 31 — εισφορές Κλάδου Εφάπαξ Παροχών ↗'); ?>
    <?php sourceCardLink('https://ypergasias.gov.gr/koinoniki-asfalisi/asfalismenoi-eisfores-kai-paroches/asfalistikes-eisfores/', 'Υπουργείο Εργασίας — Ασφαλιστικές εισφορές ↗'); ?>
    <?php sourceCardLink('https://www.e-efka.gov.gr/el/sychnes-eroteseis/asphalisi-eisphores/asphalismenoi/misthotoi-0/meiomenes-eisphores-gia-meteres-misthotes', 'e-ΕΦΚΑ — Μειωμένες εισφορές για μητέρες μισθωτές ↗'); ?>
  <?php sourceCardLinksEnd(); ?>
  <?php sourceCardDisclaimerStart(); ?>Ο υπολογιστής δεν αποφαίνεται αν ένας τίτλος θεμελιώνει δικαίωμα προώθησης. Για τη συνάφεια τίτλου, την αναγνώριση προϋπηρεσίας, ειδικές περιπτώσεις όπως ΤΕ16 και την ημερομηνία οικονομικών αποτελεσμάτων υπερισχύει η ισχύουσα απόφαση του αρμόδιου υπηρεσιακού οργάνου.<?php sourceCardDisclaimerEnd(); ?>
<?php sourceCardDisclosureEnd(); ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
<script src="<?php echo htmlspecialchars(edu_asset_url('includes/salary-scale-calculations.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
<script src="<?php echo htmlspecialchars(edu_asset_url('includes/salary-net-calculations.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
<script src="<?php echo htmlspecialchars(edu_asset_url('assets/common.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
<script src="<?php echo htmlspecialchars(edu_asset_url('includes/salary-ui.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
<script>window.EducationSalaryUI.init();</script>
</body>
</html>
