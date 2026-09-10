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
<body class="edu-ui edu-calc-standard">
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
            <div class="info-note"><strong>Υπηρεσία στη διετία 01-01-2016 έως 31-12-2017</strong><br>Δήλωσε πόση από τη συνολική αναγνωρισμένη υπηρεσία διανύθηκε στη διετία που δεν λαμβάνεται υπόψη για μισθολογική εξέλιξη.</div>
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
              <option value="new">Νέος ασφαλισμένος — από 01/01/1993</option>
              <option value="old">Παλαιός ασφαλισμένος — έως 31/12/1992</option>
            </select>
            <small id="insuredStatusHelp">Κριτήριο είναι η <strong>πρώτη ασφάλιση για κύρια σύνταξη</strong>, όχι η ημερομηνία διορισμού στο Δημόσιο. Στον αναπληρωτή / ΙΔΟΧ με ΚΠΚ 101 η επιλογή δεν μεταβάλλει την παρούσα εκτίμηση.</small>
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
          <details class="info-note edu-field--full" id="insuredStatusInfoPanel">
            <summary><strong>Τι αλλάζει μεταξύ παλαιού και νέου ασφαλισμένου;</strong></summary>
            <p><strong>Παλαιός ασφαλισμένος</strong> είναι όποιος ασφαλίστηκε πρώτη φορά για κύρια σύνταξη έως 31/12/1992. Για υπάλληλο του ενιαίου μισθολογίου, στην παρούσα εκτίμηση η κύρια και επικουρική σύνταξη υπολογίζονται σε <strong>βασικό μισθό + επίδομα θέσης ευθύνης</strong>, η εισφορά ΤΠΔΥ 4% στον <strong>βασικό μισθό</strong>, ενώ για ΜΤΠΥ εφαρμόζεται 4,5% σε βασικό + θέση και 1% στην οικογενειακή παροχή και στο παραμεθόριο.</p>
            <p><strong>Νέος ασφαλισμένος</strong> είναι όποιος ασφαλίστηκε πρώτη φορά από 01/01/1993. Στα στοιχεία αποδοχών που υποστηρίζει το εργαλείο, στη βάση κύριας/επικουρικής σύνταξης περιλαμβάνονται <strong>βασικός μισθός + οικογενειακή παροχή + θέση ευθύνης + παραμεθόριο</strong>. Η ίδια βάση χρησιμοποιείται για ΤΠΔΥ 4% και ΜΤΠΥ 4,5%.</p>
            <small>Η υγειονομική περίθαλψη 2,05% και η εισφορά 2% για την καταπολέμηση της ανεργίας υπολογίζονται στις αντίστοιχες τακτικές αποδοχές και δεν διαφοροποιούνται εδώ μόνο λόγω χαρακτηρισμού «παλαιός/νέος». Ειδικά ασφαλιστικά καθεστώτα μπορεί να απαιτούν διαφορετική αντιμετώπιση.</small>
          </details>
          <details class="info-note edu-field--full" id="maternityEligibilityPanel">
            <summary><strong>Ποια μισθωτή μητέρα δικαιούται τη μείωση;</strong></summary>
            <p>Σύμφωνα με τον e-ΕΦΚΑ, οι <strong>μητέρες μισθωτές ασφαλισμένες</strong>, εφόσον πληρούν τις λοιπές προϋποθέσεις, δικαιούνται μείωση κατά <strong>50% της εργατικής εισφοράς του κλάδου κύριας σύνταξης</strong> για το σχετικό δωδεκάμηνο.</p>
            <p>Αν έχει ληφθεί <strong>επιδότηση λόγω λοχείας</strong>, η μείωση εφαρμόζεται από τη λήξη της επιδότησης για το επόμενο δωδεκάμηνο. Αν δεν έχει ληφθεί επιδότηση λοχείας, το κρίσιμο δωδεκάμηνο αρχίζει από την <strong>1η του επόμενου μήνα του τοκετού</strong>. Ως χρόνος απασχόλησης μπορεί να λογίζεται και άδεια <strong>με αποδοχές</strong>, σύμφωνα με τις διευκρινίσεις του e-ΕΦΚΑ.</p>
            <small>Το εργαλείο δεν αποφασίζει αν θεμελιώνεται το δικαίωμα. Αν η μισθοδοσία παρακράτησε ολόκληρη την εισφορά, ο e-ΕΦΚΑ προβλέπει διαδικασία επιστροφής αχρεωστήτως καταβληθεισών εισφορών, όταν πληρούνται οι προϋποθέσεις.</small>
          </details>
          <details class="info-note edu-field--full" id="disabilityTaxInfoPanel">
            <summary><strong>Τι αλλάζει φορολογικά λόγω αναπηρίας;</strong></summary>
            <p>Για πιστοποιημένη αναπηρία <strong>67%–79,99%</strong>, το εργαλείο εφαρμόζει πρόσθετη <strong>μείωση φόρου έως 200 € τον χρόνο</strong>, μέχρι το ύψος του φόρου που απομένει μετά τη μείωση του άρθρου 16 ΚΦΕ.</p>
            <p>Για ποσοστό αναπηρίας <strong>τουλάχιστον 80%</strong>, οι μισθοί απαλλάσσονται από τον φόρο εισοδήματος. Στην εκτίμηση η <strong>μηνιαία παρακράτηση φόρου γίνεται 0 €</strong>.</p>
            <small>Η ειδική φορολογική μεταχείριση δεν μηδενίζει τις ασφαλιστικές ή λοιπές κρατήσεις. Το εργαλείο δεν ελέγχει το είδος ή την ισχύ της πιστοποίησης αναπηρίας ούτε αν αυτή έχει καταχωριστεί στη μισθοδοσία.</small>
          </details>
          <details class="info-note edu-field--full" id="otherDeductionsPanel">
            <summary><strong>Λοιπές κρατήσεις</strong> <small>προαιρετικά — ποσά ανά μήνα</small></summary>
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
          </details>
        </div>
        <div class="info-note">
          Η εκτίμηση καθαρών γίνεται πάνω στον <strong>βασικό μισθό του Μ.Κ.</strong>, στην <strong>οικογενειακή παροχή</strong> που αντιστοιχεί στον δηλωμένο αριθμό τέκνων, στο τυχόν <strong>επίδομα θέσης ευθύνης</strong> και, αν επιλεγεί, στο <strong>επίδομα απομακρυσμένων - παραμεθορίων περιοχών</strong>, με 12μηνη φορολογική αναγωγή. Δεν προστίθενται προσωπική διαφορά ή άλλες αποδοχές. Η προαιρετική <strong>μειωμένη εισφορά κύριας σύνταξης λόγω μητρότητας</strong> μειώνει μόνο το αντίστοιχο ασφαλιστικό σκέλος. Η <strong>φορολογική ρύθμιση λόγω αναπηρίας</strong> επηρεάζει μόνο τον φόρο εισοδήματος και όχι τις ασφαλιστικές κρατήσεις. Οι προαιρετικές <strong>λοιπές κρατήσεις</strong> αφαιρούνται μόνο από το τελικό πληρωτέο και δεν μεταβάλλουν τον υπολογισμό φόρου. Αν ο αριθμός τέκνων που λαμβάνεται υπόψη για φορολογία διαφέρει από εκείνον της οικογενειακής παροχής, η εκτίμηση χρειάζεται διοικητικό έλεγχο.
        </div>

        <div class="info-note">
          <strong>Αναστολή μισθολογικής εξέλιξης 2016–2017:</strong> ο δηλωμένος χρόνος της συγκεκριμένης διετίας (έως 24 μήνες) <strong>δεν λαμβάνεται υπόψη για μισθολογική εξέλιξη</strong> και αφαιρείται αυτόματα από τον υπολογισμό του Μ.Κ. Αν δεν έχεις υπηρεσία μέσα στη διετία, άφησε Έτη και Μήνες στο 0.
        </div>
        <div class="info-note">
          Καταχώρισε <strong>μόνο</strong> χρόνο προϋπηρεσίας και τίτλο που έχουν ήδη αναγνωριστεί μισθολογικά από την αρμόδια υπηρεσία. Το εργαλείο δεν κρίνει αν μια προϋπηρεσία είναι αναγνωρίσιμη ούτε αν ένας τίτλος είναι συναφής.
        </div>
        <div class="info-note">
          Από <strong>01-01-2026</strong>, η ειδική προώθηση κατά <strong>2 Μ.Κ.</strong> για Integrated Master αφορά ενιαίο και αδιάσπαστο τίτλο <strong>ελληνικού Α.Ε.Ι.</strong> που εμπίπτει στο άρθρο 46 του ν. 4485/2017 ή στο άρθρο 78 του ν. 4957/2022. <strong>Integrated Master αλλοδαπής δεν καλύπτεται από αυτή την ειδική ρύθμιση.</strong> Επίσης δεν χορηγείται δεύτερη προώθηση όταν έχει ήδη δοθεί προώθηση λόγω διακριτού μεταπτυχιακού τίτλου.
        </div>
        <div class="info-note">
          <strong>Ειδικές περιπτώσεις:</strong> η δυνατότητα μισθολογικής προώθησης δεν προκύπτει μόνο από την κατηγορία ΠΕ/ΤΕ/ΔΕ ή από την απλή κατοχή ενός τίτλου. Για μεταπτυχιακό ή διδακτορικό απαιτούνται οι νόμιμες προϋποθέσεις και, όπου απαιτείται, αναγνώριση συνάφειας από το αρμόδιο υπηρεσιακό όργανο. Η κατηγορία <strong>ΔΕ</strong> δεν αποκλείεται αυτομάτως, αλλά η αναγνώριση πρέπει να έχει προηγηθεί υπηρεσιακά. Ιδιαίτερη προσοχή απαιτείται σε περιπτώσεις όπως ο κλάδος <strong>ΤΕ16</strong>, όπου έχουν τεθεί ειδικά ζητήματα ως προς τον βασικό τίτλο διορισμού και τη μισθολογική αναγνώριση μεταπτυχιακών τίτλων. <strong>Μην επιλέγεις +2 ή +6 Μ.Κ. μόνο επειδή κατέχεις τον τίτλο· επίλεξέ το μόνο αν η αντίστοιχη μισθολογική προώθηση έχει ήδη αναγνωριστεί από την υπηρεσία σου.</strong>
        </div>

        <?php calculatorActions(array(
          array('id' => 'printBtn', 'class' => 'edu-btn-primary', 'label' => 'Εκτύπωση'),
          array('id' => 'resetBtn', 'class' => 'secondary', 'label' => 'Καθαρισμός')
        )); ?>
      <?php calculatorCardEnd(); ?>

      <?php calculatorResultMessage(array(
        'variant' => 'disclaimer',
        'html' => '<strong>Η εκτίμηση καθαρών είναι ενδεικτική.</strong> Υπολογίζεται από τον βασικό μισθό του Μ.Κ., την οικογενειακή παροχή με βάση τον δηλωμένο αριθμό τέκνων, το τυχόν επίδομα θέσης ευθύνης, το προαιρετικό επίδομα απομακρυσμένων - παραμεθορίων περιοχών, το επιλεγμένο τυπικό προφίλ κρατήσεων και —όπου εφαρμόζεται— την ασφαλιστική ιδιότητα παλαιού/νέου ασφαλισμένου και τις αντίστοιχες βάσεις εισφορών. Αν δηλωθεί δικαίωμα μειωμένης εισφοράς μητρότητας, μειώνεται μόνο το σκέλος κύριας σύνταξης. Αν επιλεγεί ειδική φορολογική ρύθμιση λόγω αναπηρίας, μεταβάλλεται μόνο ο φόρος εισοδήματος και όχι οι ασφαλιστικές κρατήσεις. Δεν περιλαμβάνει προσωπική διαφορά ή αναδρομικά. Ειδικές μικρές κρατήσεις μπορούν να δηλωθούν προαιρετικά στην ενότητα «Λοιπές κρατήσεις». Η πραγματική μισθοδοσία και φορολογική εκκαθάριση μπορεί να διαφέρουν.'
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
      <?php calculatorResultRow(array('label' => 'Αφαιρούμενος χρόνος 2016–2017', 'value' => '0 μήνες', 'value_id' => 'suspendedServiceResult')); ?>
      <?php calculatorResultRow(array('label' => 'Μετρήσιμος χρόνος για Μ.Κ.', 'value' => '0 μήνες', 'value_id' => 'countableServiceResult')); ?>
      <?php calculatorResultRow(array('label' => 'Μ.Κ. από υπηρεσία', 'value' => 'Μ.Κ. 1', 'value_id' => 'baseMkResult')); ?>
      <?php calculatorResultRow(array('label' => 'Προώθηση τίτλου', 'value' => '0 Μ.Κ.', 'value_id' => 'promotionResult')); ?>
      <?php calculatorResultRow(array('label' => 'Χρόνος προς επόμενο Μ.Κ.', 'value' => '24 μήνες', 'value_id' => 'nextMkResult')); ?>

      <h3>Ενδεικτικές καθαρές αποδοχές</h3>
      <?php calculatorResultRow(array('label' => 'Οικογενειακή παροχή', 'value' => '0,00 €', 'value_id' => 'familyAllowanceResult')); ?>
      <?php calculatorResultRow(array('label' => 'Επίδομα θέσης ευθύνης', 'value' => '0,00 €', 'value_id' => 'positionAllowanceResult')); ?>
      <?php calculatorResultRow(array('label' => 'Επίδομα απομακρυσμένων - παραμεθορίων', 'value' => '0,00 €', 'value_id' => 'remoteAllowanceResult')); ?>
      <?php calculatorResultRow(array('label' => 'Σύνολο μικτών για εκτίμηση', 'value' => '1.232,00 €', 'value_id' => 'grossForNetResult')); ?>
      <?php calculatorResultRow(array('label' => 'Προφίλ κρατήσεων', 'value' => 'Μόνιμος δημόσιος υπάλληλος', 'value_id' => 'payrollProfileResult')); ?>
      <?php calculatorResultRow(array('label' => 'Ασφαλιστική ιδιότητα', 'value' => 'Νέος ασφαλισμένος — από 01/01/1993', 'value_id' => 'insuredStatusResult')); ?>
      <?php calculatorResultRow(array('label' => 'Βάσεις ασφαλιστικών κρατήσεων', 'value' => '—', 'value_id' => 'insuranceBasesResult')); ?>
      <?php calculatorResultRow(array('label' => 'Τακτικές κρατήσεις', 'value' => '0,00 €', 'value_id' => 'standardDeductionsResult')); ?>
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
      <?php calculatorResultRow(array('label' => 'Μηνιαία παρακράτηση φόρου', 'value' => '0,00 €', 'value_id' => 'monthlyTaxResult')); ?>
      <?php calculatorSubtotalRow(array('label' => 'Εκτιμώμενο καθαρό', 'value' => '0,00 €', 'value_id' => 'estimatedNetResult')); ?>
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

<?php sourceCardStart(); ?>
  <p>Ο ν. 4354/2015 προβλέπει 19 Μ.Κ. για ΠΕ/ΤΕ και 13 για ΔΕ/ΥΕ. Για τη συνήθη μισθολογική εξέλιξη απαιτούνται δύο έτη ανά Μ.Κ. για ΠΕ/ΤΕ και τρία έτη για ΔΕ/ΥΕ. Αναγνωρισμένος συναφής μεταπτυχιακός τίτλος προωθεί κατά 2 Μ.Κ. και διδακτορικό κατά 6 Μ.Κ. στην κατηγορία όπου ανήκει ο υπάλληλος. Από 01-01-2026, ο ν. 5246/2025 προσθέτει ειδική προώθηση +2 Μ.Κ. για Integrated Master που εμπίπτει στις διατάξεις των άρθρων 46 ν. 4485/2017 και 78 ν. 4957/2022.</p>
  <p>Με το άρθρο 26 παρ. 2 του ν. 4354/2015 η μισθολογική εξέλιξη ανεστάλη έως 31-12-2017. Από 01-01-2018 ενεργοποιήθηκε εκ νέου, χωρίς να λαμβάνεται υπόψη για την εξέλιξη το χρονικό διάστημα 01-01-2016 έως 31-12-2017.</p>
  <p><strong>Βασικοί μισθοί 2026:</strong> η εγκύκλιος ΥΠΕΘΟΟ <strong>54692 ΕΞ 2026/03-04-2026</strong> (ΑΔΑ: <strong>ΨΕ7ΨΗ-ΚΧΧ</strong>) αναπροσαρμόζει από 01-04-2026 τους βασικούς μισθούς και στο Παράρτημα, Πίνακες 1–4, αποτυπώνει τα ποσά ανά Μ.Κ. για ΠΕ, ΤΕ, ΔΕ και ΥΕ.</p>
  <p><strong>Οικογενειακή παροχή:</strong> το άρθρο 15 του ν. 4354/2015, όπως ισχύει μετά τον ν. 5045/2023, προβλέπει μηνιαία παροχή <strong>70 € για 1 τέκνο, 120 € για 2, 170 € για 3, 220 € για 4 και +70 € για κάθε επιπλέον τέκνο</strong>. Στην εκτίμηση το ποσό προστίθεται στις μικτές αποδοχές πριν από τον υπολογισμό κρατήσεων και φόρου.</p>
  <p><strong>Επίδομα θέσης ευθύνης:</strong> το άρθρο 16 του ν. 4354/2015, όπως ισχύει για τα στελέχη εκπαίδευσης μετά τον ν. 4823/2021, προβλέπει μηνιαίο επίδομα ανά θέση. Με το άρθρο 22 του ν. 5045/2023 τα ποσά αυξήθηκαν κατά <strong>30%</strong> από 01-01-2024 και στρογγυλοποιούνται στην πλησιέστερη μονάδα ευρώ. Ενδεικτικά: Διευθυντής ΓΕΛ/ΕΠΑΛ <strong>429 € ή 501 €</strong>, Διευθυντής Γυμνασίου <strong>358 € ή 429 €</strong>, Υποδιευθυντής <strong>195 €</strong>. Σε συρροή αξιώσεων από δύο βαθμίδες καταβάλλεται μόνο το ποσό της ανώτερης βαθμίδας. Το εργαλείο προσθέτει μία μόνο επιλεγμένη θέση στις μικτές αποδοχές.</p>
  <p><strong>Επίδομα απομακρυσμένων - παραμεθορίων περιοχών:</strong> το άρθρο 19 του ν. 4354/2015 διατηρεί το επίδομα στο ίδιο ύψος και με τις ίδιες προϋποθέσεις· για τους δικαιούχους το ποσό είναι <strong>100 € μικτά τον μήνα</strong>. Η επιλογή στο εργαλείο είναι προαιρετική και δεν ελέγχει αν η συγκεκριμένη περιοχή/υπηρεσία θεμελιώνει δικαίωμα. Το ποσό προστίθεται στις μικτές αποδοχές, αλλά η <strong>βάση κάθε ασφαλιστικής κράτησης</strong> εξαρτάται πλέον και από την ασφαλιστική ιδιότητα: για παράδειγμα στο ΜΤΠΥ το παραμεθόριο επιβαρύνεται με 4,5% για νέο ασφαλισμένο και 1% για παλαιό.</p>
  <p><strong>Φορολογία 2026:</strong> ο ν. 5246/2025 τροποποίησε από το φορολογικό έτος 2026 την κλίμακα μισθωτών, με ειδικούς συντελεστές ανά αριθμό εξαρτώμενων τέκνων και για ηλικίες έως 25 και 26–30 ετών. Εφαρμόζεται επίσης η μείωση φόρου του άρθρου 16 ΚΦΕ.</p>
  <p><strong>Αναπηρία και φόρος εισοδήματος:</strong> για πιστοποιημένη αναπηρία <strong>67% και άνω</strong> προβλέπεται πρόσθετη μείωση φόρου <strong>200 €</strong> σύμφωνα με το άρθρο 17 ΚΦΕ. Για μισθούς, συντάξεις και πάγια αντιμισθία που χορηγούνται σε πρόσωπα με αναπηρία <strong>τουλάχιστον 80%</strong>, το άρθρο 14 παρ. 2 περ. ε΄ ΚΦΕ προβλέπει απαλλαγή από τον φόρο. Στον υπολογιστή το 67%–79,99% εφαρμόζεται ως μείωση έως 200 € στον ετήσιο φόρο, ενώ το ≥80% μηδενίζει τον φόρο μισθωτής εργασίας. Οι ασφαλιστικές κρατήσεις δεν αλλάζουν από αυτή την επιλογή.</p>
  <p><strong>Παλαιός / νέος ασφαλισμένος και βάσεις εισφορών:</strong> κριτήριο είναι η <strong>πρώτη υπαγωγή σε κύρια ασφάλιση</strong>: παλαιός έως 31-12-1992, νέος από 01-01-1993. Για υπαλλήλους του ενιαίου μισθολογίου ν. 4354/2015, οι συντάξιμες αποδοχές του παλαιού ασφαλισμένου περιλαμβάνουν, για τις συνήθεις περιπτώσεις εκπαιδευτικών, <strong>βασικό μισθό + επίδομα θέσης ευθύνης</strong>, ενώ του νέου περιλαμβάνουν <strong>βασικό + οικογενειακή παροχή + θέση ευθύνης + επίδομα απομακρυσμένων/παραμεθορίων</strong>. Η επικουρική ακολουθεί τις αντίστοιχες βάσεις. Για το τ. ΤΠΔΥ η εισφορά είναι 4%: στον παλαιό επί του <strong>βασικού μισθού</strong>, στον νέο επί των ασφαλιστέων αποδοχών κύριας σύνταξης. Στο ΜΤΠΥ εφαρμόζεται 4,5% στις συντάξιμες αποδοχές· για τον παλαιό, στις μη συντάξιμες οικογενειακή παροχή και παραμεθόριο εφαρμόζεται 1%. Η υγειονομική περίθαλψη του ασφαλισμένου είναι από 01-01-2025 συνολικά 2,05% επί των πάσης φύσεως αποδοχών και η εισφορά ανεργίας 2% επί των τακτικών αποδοχών/πρόσθετων αμοιβών που εμπίπτουν στη ρύθμιση.</p>
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
    <?php sourceCardLink('https://myportal.mtpy.gr/backend1/uploads/ODIGOS_KRATISEON_IOYNIOS_2020_5dc7497fbd.pdf', 'ΜΤΠΥ — Επικαιροποιημένος Οδηγός Κρατήσεων Ιουνίου 2020 ↗'); ?>
    <?php sourceCardLink('https://www.taxheaven.gr/law/4670/2020/arthro/31', 'Ν. 4670/2020 άρθρο 31 — εισφορές Κλάδου Εφάπαξ Παροχών ↗'); ?>
    <?php sourceCardLink('https://ypergasias.gov.gr/koinoniki-asfalisi/asfalismenoi-eisfores-kai-paroches/asfalistikes-eisfores/', 'Υπουργείο Εργασίας — Ασφαλιστικές εισφορές ↗'); ?>
    <?php sourceCardLink('https://www.e-efka.gov.gr/el/sychnes-eroteseis/asphalisi-eisphores/asphalismenoi/misthotoi-0/meiomenes-eisphores-gia-meteres-misthotes', 'e-ΕΦΚΑ — Μειωμένες εισφορές για μητέρες μισθωτές ↗'); ?>
  <?php sourceCardLinksEnd(); ?>
  <?php sourceCardDisclaimerStart(); ?>Ο υπολογιστής δεν αποφαίνεται αν ένας τίτλος θεμελιώνει δικαίωμα προώθησης. Για τη συνάφεια τίτλου, την αναγνώριση προϋπηρεσίας, ειδικές περιπτώσεις όπως ΤΕ16 και την ημερομηνία οικονομικών αποτελεσμάτων υπερισχύει η ισχύουσα απόφαση του αρμόδιου υπηρεσιακού οργάνου.<?php sourceCardDisclaimerEnd(); ?>
<?php sourceCardEnd(); ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
<script src="<?php echo htmlspecialchars(edu_asset_url('includes/salary-scale-calculations.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
<script src="<?php echo htmlspecialchars(edu_asset_url('includes/salary-net-calculations.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
<script src="<?php echo htmlspecialchars(edu_asset_url('assets/common.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
<script>
(function () {
  'use strict';
  const byId = id => document.getElementById(id);
  function integer(id, max) {
    const n = Math.max(0, Math.floor(Number(byId(id).value) || 0));
    return Number.isFinite(max) ? Math.min(max, n) : n;
  }

  function money(id) {
    const n = Number(byId(id).value);
    return Number.isFinite(n) ? Math.max(0, n) : 0;
  }

  function escapeHtml(value) {
    return String(value == null ? '' : value)
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#039;');
  }

  function selectedText(id) {
    const el = byId(id);
    if (!el || !el.options || el.selectedIndex < 0) return '';
    return String(el.options[el.selectedIndex].textContent || '').trim();
  }

  function setResultRowVisible(valueId, visible) {
    const value = byId(valueId);
    const row = value && value.closest ? value.closest('.result-row') : null;
    if (row) row.hidden = !visible;
  }

  function renderDeductionDetails(net) {
    const details = byId('deductionBreakdownDetails');
    if (!details) return;
    let html = '';
    (net.deductionComponents || []).forEach(function (component) {
      let note = component.note || '';
      if (component.rate != null && component.base != null) {
        note = formatPercent(component.rate) + ' · βάση ' + formatEuroCents(component.base);
      }
      html += '<div class="payroll-deduction-detail-row">' +
        '<span>' + escapeHtml(component.label) + (note ? '<small class="payroll-deduction-detail-note">' + escapeHtml(note) + '</small>' : '') + '</span>' +
        '<strong>' + formatEuroCents(component.amount) + '</strong>' +
      '</div>';
    });
    if (net.maternityPensionReductionAmount > 0) {
      html += '<div class="payroll-deduction-detail-row">' +
        '<span>Μείωση εισφοράς κύριας σύνταξης λόγω μητρότητας<small class="payroll-deduction-detail-note">50% της εργατικής εισφοράς κύριας σύνταξης · βάση ' + escapeHtml(formatEuroCents(net.maternityPensionContributionBase)) + '</small></span>' +
        '<strong>−' + formatEuroCents(net.maternityPensionReductionAmount) + '</strong>' +
      '</div>';
    }
    details.innerHTML = html || '<div class="payroll-deduction-detail-row"><span>Δεν υπάρχουν επιμέρους τακτικές κρατήσεις.</span><strong>—</strong></div>';
  }

  function printAmountRow(label, amount, options) {
    options = options || {};
    const prefix = options.negative ? '−' : '';
    const note = options.note ? '<small>' + escapeHtml(options.note) + '</small>' : '';
    return '<tr><td>' + escapeHtml(label) + (note ? '<br>' + note : '') + '</td><td class="amount">' + prefix + formatEuroCents(amount) + '</td></tr>';
  }

  function renderPrintSheet(result, net, payroll) {
    const content = byId('payrollPrintContent');
    if (!content) return;
    const totalDeductions = window.EducationSalaryNet.roundMoney(
      net.standardDeductions + net.registrationDeduction + net.otherDeductions + net.monthlyTax
    );
    const grossRows = [
      printAmountRow('Βασικός μισθός — Μ.Κ. ' + result.finalMK, result.basicGrossSalary),
      printAmountRow('Οικογενειακή παροχή', payroll.familyAllowance),
      printAmountRow('Επίδομα θέσης ευθύνης', payroll.positionAllowance, { note: payroll.positionLabel }),
      printAmountRow('Επίδομα απομακρυσμένων - παραμεθορίων περιοχών', payroll.remoteAllowance)
    ].join('');

    let deductionRows = '';
    (net.deductionComponents || []).forEach(function (component) {
      let componentNote = component.note || '';
      if (component.rate != null && component.base != null) {
        componentNote = formatPercent(component.rate) + ' · βάση ' + formatEuroCents(component.base);
      }
      deductionRows += printAmountRow(component.label, component.amount, {
        note: componentNote
      });
    });
    if (net.maternityPensionReductionAmount > 0) {
      deductionRows += printAmountRow('Μείωση εισφοράς κύριας σύνταξης λόγω μητρότητας', net.maternityPensionReductionAmount, {
        negative: true, note: '50% της εργατικής εισφοράς κύριας σύνταξης · βάση ' + formatEuroCents(net.maternityPensionContributionBase)
      });
    }
    if (net.registrationDeduction > 0) {
      deductionRows += printAmountRow('Δικαίωμα εγγραφής ΜΤΠΥ — μηνιαία δόση', net.registrationDeduction, { note: '1/12 των μικτών αποδοχών της εκτίμησης' });
    }
    payroll.otherDeductionParts.forEach(function (item) {
      if (item[1] > 0) deductionRows += printAmountRow(item[0], item[1]);
    });
    deductionRows += printAmountRow('Φόρος εισοδήματος — μηνιαία παρακράτηση', net.monthlyTax);

    const totalService = integer('serviceYears', 50) * 12 + integer('serviceMonths', 11);
    const qualificationText = selectedText('qualification');
    content.innerHTML =
      '<div class="print-grid">' +
        '<div class="print-box"><h2>Μισθολογική κατάταξη</h2>' +
          '<div><strong>Κατηγορία:</strong> ' + escapeHtml(result.category) + '</div>' +
          '<div><strong>Τελικό Μ.Κ.:</strong> Μ.Κ. ' + escapeHtml(result.finalMK) + '</div>' +
          '<div><strong>Αναγνωρισμένη υπηρεσία:</strong> ' + escapeHtml(formatServiceMonths(totalService)) + '</div>' +
          '<div><strong>Αφαιρούμενος χρόνος 2016–2017:</strong> ' + escapeHtml(formatServiceMonths(result.suspendedServiceMonths)) + '</div>' +
          '<div><strong>Μετρήσιμος χρόνος:</strong> ' + escapeHtml(formatServiceMonths(result.countableServiceMonths)) + '</div>' +
          '<div><strong>Τίτλος / προώθηση:</strong> ' + escapeHtml(qualificationText) + '</div>' +
        '</div>' +
        '<div class="print-box"><h2>Παράμετροι μισθοδοσίας</h2>' +
          '<div><strong>Προφίλ:</strong> ' + escapeHtml(net.profileLabel) + '</div>' +
          '<div><strong>Ασφαλιστική ιδιότητα:</strong> ' + escapeHtml(net.insuredStatusLabel) + '</div>' +
          '<div><strong>Βάσεις εισφορών:</strong> ' + escapeHtml(net.insuranceBasesLabel) + '</div>' +
          '<div><strong>Ηλικιακή κατηγορία:</strong> ' + escapeHtml(net.ageGroupLabel) + '</div>' +
          '<div><strong>Εξαρτώμενα τέκνα:</strong> ' + escapeHtml(net.children) + '</div>' +
          '<div><strong>Αναπηρία — φορολογία:</strong> ' + escapeHtml(net.disabilityTaxTreatmentLabel) + '</div>' +
          '<div><strong>Μείωση μητρότητας:</strong> ' + (net.maternityPensionReduction ? 'Ναι' : 'Όχι') + '</div>' +
          '<div><strong>Ισχύς βασικού μισθού:</strong> ' + escapeHtml(result.basicSalaryEffectiveDate) + '</div>' +
        '</div>' +
      '</div>' +
      '<table><thead><tr><th>ΑΠΟΔΟΧΕΣ</th><th class="amount">Ποσό</th></tr></thead><tbody>' +
        grossRows +
        '<tr class="total-row"><td>ΣΥΝΟΛΟ ΜΙΚΤΩΝ ΑΠΟΔΟΧΩΝ</td><td class="amount">' + formatEuroCents(payroll.grossForNet) + '</td></tr>' +
      '</tbody></table>' +
      '<table><thead><tr><th>ΚΡΑΤΗΣΕΙΣ</th><th class="amount">Ποσό</th></tr></thead><tbody>' +
        deductionRows +
        '<tr class="total-row"><td>ΣΥΝΟΛΟ ΚΡΑΤΗΣΕΩΝ</td><td class="amount">' + formatEuroCents(totalDeductions) + '</td></tr>' +
      '</tbody></table>' +
      '<div class="print-grid">' +
        '<div class="print-box"><h2>Φορολογική ανάλυση</h2>' +
          '<div><strong>Μηνιαίο φορολογητέο:</strong> ' + formatEuroCents(net.taxableMonthly) + '</div>' +
          '<div><strong>Ετήσιο φορολογητέο (12μηνο):</strong> ' + formatEuroCents(net.taxableAnnual) + '</div>' +
          '<div><strong>Φόρος κλίμακας πριν τη μείωση:</strong> ' + formatEuroCents(net.taxBeforeCredit) + '</div>' +
          '<div><strong>Μείωση φόρου άρθρου 16 ΚΦΕ:</strong> −' + formatEuroCents(net.taxCredit) + '</div>' +
          '<div><strong>Μείωση / απαλλαγή λόγω αναπηρίας:</strong> −' + formatEuroCents(net.disabilityTaxRelief) + '</div>' +
          '<div><strong>Ετήσιος φόρος:</strong> ' + formatEuroCents(net.annualTax) + '</div>' +
        '</div>' +
        '<div class="print-box"><h2>Σύνοψη</h2>' +
          '<div><strong>Τακτικές κρατήσεις μετά τυχόν μείωση:</strong> ' + formatEuroCents(net.standardDeductions) + '</div>' +
          '<div><strong>Λοιπές κρατήσεις:</strong> ' + formatEuroCents(net.otherDeductions) + '</div>' +
          '<div><strong>Μηνιαία παρακράτηση φόρου:</strong> ' + formatEuroCents(net.monthlyTax) + '</div>' +
        '</div>' +
      '</div>' +
      '<div class="print-net"><span>ΕΚΤΙΜΩΜΕΝΟ ΠΛΗΡΩΤΕΟ</span><span>' + formatEuroCents(net.estimatedNet) + '</span></div>' ;

    const stamp = byId('payrollPrintGeneratedAt');
    if (stamp) {
      try {
        stamp.textContent = 'Παραγωγή: ' + new Intl.DateTimeFormat('el-GR', { dateStyle: 'short', timeStyle: 'short' }).format(new Date());
      } catch (e) {
        stamp.textContent = 'Παραγωγή: ' + new Date().toLocaleString();
      }
    }
  }

  function clampBoundedIntegerInput(el, max) {
    if (!el || el.value === '') return;
    const n = Number(el.value);
    if (!Number.isFinite(n)) {
      el.value = '';
      return;
    }
    el.value = String(Math.max(0, Math.min(max, Math.floor(n))));
  }

  function calculate() {
    const result = window.EducationSalaryScale.calculate({
      category: byId('category').value,
      years: integer('serviceYears', 50),
      months: integer('serviceMonths', 11),
      qualification: byId('qualification').value,
      suspendedYears: integer('suspendedYears', 2),
      suspendedMonths: integer('suspendedMonths', 11)
    });

    byId('finalMkResult').textContent = 'Μ.Κ. ' + result.finalMK;
    const categoryLabels = { PE: 'ΠΕ', TE: 'ΤΕ', DE: 'ΔΕ', YE: 'ΥΕ' };
    byId('categoryResult').textContent = categoryLabels[result.categoryCode] || categoryLabels[byId('category').value] || result.category || 'ΠΕ';
    byId('basicSalaryResult').textContent = formatEuro(result.basicGrossSalary);

    const children = integer('dependentChildren', 20);
    const familyAllowance = window.EducationSalaryNet.familyAllowanceMonthly(children);
    const positionKey = byId('positionAllowance').value;
    const positionAllowance = window.EducationSalaryNet.positionAllowanceMonthly(positionKey);
    const remoteAllowance = byId('remoteAreaAllowance').checked
      ? window.EducationSalaryNet.REMOTE_AREA_ALLOWANCE_MONTHLY
      : 0;
    const grossForNet = result.basicGrossSalary + familyAllowance + positionAllowance + remoteAllowance;
    const otherDeductionParts = [
      ['ΑΔΕΔΥ', money('adedYDeduction')],
      ['ΟΛΜΕ / ΔΟΕ', money('federationDeduction')],
      ['Σύλλογος', money('associationDeduction')],
      ['Άλλο ποσό', money('otherPayrollDeduction')]
    ];
    const otherDeductions = otherDeductionParts.reduce((sum, item) => sum + item[1], 0);
    const net = window.EducationSalaryNet.calculate({
      grossMonthly: grossForNet,
      basicMonthly: result.basicGrossSalary,
      familyAllowanceMonthly: familyAllowance,
      positionAllowanceMonthly: positionAllowance,
      remoteAllowanceMonthly: remoteAllowance,
      profile: byId('payrollProfile').value,
      insuredStatus: byId('insuredStatus').value,
      ageGroup: byId('ageGroup').value,
      children: children,
      disabilityTaxTreatment: byId('disabilityTaxTreatment').value,
      otherDeductions: otherDeductions,
      maternityPensionReduction: byId('maternityPensionReduction').checked
    });
    byId('familyAllowanceResult').textContent = formatEuroCents(familyAllowance);
    byId('positionAllowanceResult').textContent = formatEuroCents(positionAllowance);
    byId('remoteAllowanceResult').textContent = formatEuroCents(remoteAllowance);
    byId('grossForNetResult').textContent = formatEuroCents(grossForNet);
    byId('payrollProfileResult').textContent = net.profileLabel;
    const insuredStatusSelect = byId('insuredStatus');
    insuredStatusSelect.disabled = !net.insuredStatusApplies;
    insuredStatusSelect.setAttribute('aria-disabled', net.insuredStatusApplies ? 'false' : 'true');
    byId('insuredStatusResult').textContent = net.insuredStatusLabel;
    byId('insuranceBasesResult').textContent = net.insuranceBasesLabel;
    byId('standardDeductionsResult').textContent = formatEuroCents(net.standardDeductions) + ' (≈ ' + formatPercent(net.standardDeductionRate) + ' επί μικτών)';
    byId('maternityPensionReductionResult').textContent = net.maternityPensionReductionAmount > 0
      ? '−' + formatEuroCents(net.maternityPensionReductionAmount) + ' (50% κύριας σύνταξης · βάση ' + formatEuroCents(net.maternityPensionContributionBase) + ')'
      : '0,00 €';
    byId('deductionBreakdownResult').textContent = net.deductionBreakdown;
    renderDeductionDetails(net);
    byId('registrationDeductionResult').textContent = net.registrationDeduction > 0
      ? formatEuroCents(net.registrationDeduction) + ' (1/12 μισθού)'
      : '0,00 €';
    byId('otherDeductionsResult').textContent = formatEuroCents(net.otherDeductions);
    const activeOtherDeductions = otherDeductionParts.filter(item => item[1] > 0);
    byId('otherDeductionsBreakdownResult').textContent = activeOtherDeductions.length
      ? activeOtherDeductions.map(item => item[0] + ' ' + formatEuroCents(item[1])).join(' · ')
      : '—';
    byId('taxableAnnualResult').textContent = formatEuroCents(net.taxableAnnual);
    byId('taxBeforeCreditResult').textContent = formatEuroCents(net.taxBeforeCredit);
    byId('taxCreditResult').textContent = net.taxCredit > 0 ? '−' + formatEuroCents(net.taxCredit) : '0,00 €';
    byId('disabilityTaxReliefResult').textContent = net.disabilityTaxRelief > 0
      ? '−' + formatEuroCents(net.disabilityTaxRelief) + (net.salaryTaxExemptDueToDisability ? ' (πλήρης απαλλαγή)' : '')
      : '0,00 €';
    byId('annualTaxResult').textContent = formatEuroCents(net.annualTax);
    byId('monthlyTaxResult').textContent = formatEuroCents(net.monthlyTax);
    byId('estimatedNetResult').textContent = formatEuroCents(net.estimatedNet);

    // Keep the on-screen summary compact: zero-value rows stay available in the print sheet,
    // but are omitted from the right-hand results panel until they become relevant.
    setResultRowVisible('suspendedServiceResult', result.suspendedServiceMonths > 0);
    setResultRowVisible('countableServiceResult', result.countableServiceMonths > 0);
    setResultRowVisible('promotionResult', result.promotionMK > 0);
    setResultRowVisible('familyAllowanceResult', familyAllowance > 0);
    setResultRowVisible('positionAllowanceResult', positionAllowance > 0);
    setResultRowVisible('remoteAllowanceResult', remoteAllowance > 0);
    setResultRowVisible('maternityPensionReductionResult', net.maternityPensionReductionAmount > 0);
    setResultRowVisible('registrationDeductionResult', net.registrationDeduction > 0);
    setResultRowVisible('otherDeductionsResult', net.otherDeductions > 0);
    setResultRowVisible('otherDeductionsBreakdownResult', activeOtherDeductions.length > 0);
    setResultRowVisible('taxBeforeCreditResult', net.taxBeforeCredit > 0);
    setResultRowVisible('taxCreditResult', net.taxCredit > 0);
    setResultRowVisible('disabilityTaxReliefResult', net.disabilityTaxRelief > 0);
    setResultRowVisible('annualTaxResult', net.annualTax > 0);
    setResultRowVisible('monthlyTaxResult', net.monthlyTax > 0);

    renderPrintSheet(result, net, {
      familyAllowance: familyAllowance,
      positionAllowance: positionAllowance,
      positionLabel: window.EducationSalaryNet.positionAllowanceLabel(positionKey),
      remoteAllowance: remoteAllowance,
      grossForNet: grossForNet,
      otherDeductionParts: otherDeductionParts.map(function (item) {
        return [item[0], window.EducationSalaryNet.roundMoney(item[1])];
      })
    });

    byId('suspendedServiceResult').textContent = formatServiceMonths(result.suspendedServiceMonths);
    byId('countableServiceResult').textContent = formatServiceMonths(result.countableServiceMonths);
    byId('baseMkResult').textContent = 'Μ.Κ. ' + result.baseMK;
    byId('promotionResult').textContent = result.promotionMK + ' Μ.Κ.';
    byId('nextMkResult').textContent = result.capped ? 'Καταληκτικό Μ.Κ.' : result.monthsToNext + ' μήνες';

    const status = byId('statusResult');
    if (result.suspendedServiceAdjusted) {
      status.textContent = 'Ο δηλωμένος χρόνος της διετίας 2016–2017 υπερέβαινε τη συνολική υπηρεσία και περιορίστηκε στον διαθέσιμο χρόνο.';
      status.className = 'result-message edu-message result-message--warning edu-message--warning';
    } else if (result.promotionCapped) {
      status.textContent = 'Η προώθηση περιορίζεται στο καταληκτικό Μ.Κ. ' + result.maxMK + ' της κατηγορίας.';
      status.className = 'result-message edu-message result-message--warning edu-message--warning';
    } else if (result.qualification !== 'none') {
      status.textContent = result.qualificationLabel + '. Ο χρόνος 2016–2017 που δηλώθηκε έχει αφαιρεθεί από τη μισθολογική εξέλιξη. Χρησιμοποίησε την επιλογή τίτλου μόνο αν η προώθηση έχει αναγνωριστεί υπηρεσιακά.';
      status.className = 'result-message edu-message result-message--success edu-message--success';
    } else {
      status.textContent = result.suspendedServiceMonths > 0
        ? 'Ο χρόνος 2016–2017 αφαιρέθηκε. Ο υπολογισμός γίνεται με τον υπόλοιπο μισθολογικά μετρήσιμο χρόνο.'
        : 'Υπολογισμός με βάση τον αναγνωρισμένο μισθολογικό χρόνο.';
      status.className = 'result-message edu-message result-message--status edu-message--status';
    }
  }

  function formatEuro(value) {
    const amount = Math.max(0, Math.round(Number(value) || 0));
    try {
      return new Intl.NumberFormat('el-GR', { maximumFractionDigits: 0 }).format(amount) + ' €';
    } catch (e) {
      return String(amount).replace(/\B(?=(\d{3})+(?!\d))/g, '.') + ' €';
    }
  }

  function formatEuroCents(value) {
    const amount = Math.max(0, Number(value) || 0);
    try {
      return new Intl.NumberFormat('el-GR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(amount) + ' €';
    } catch (e) {
      return amount.toFixed(2).replace('.', ',') + ' €';
    }
  }

  function formatPercent(value) {
    const percentage = Math.max(0, Number(value) || 0) * 100;
    return percentage.toFixed(2).replace('.', ',') + '%';
  }

  function formatServiceMonths(totalMonths) {
    const total = Math.max(0, Math.floor(Number(totalMonths) || 0));
    const years = Math.floor(total / 12);
    const months = total % 12;
    if (years && months) return years + ' έτη ' + months + ' μήνες';
    if (years) return years + (years === 1 ? ' έτος' : ' έτη');
    return months + (months === 1 ? ' μήνας' : ' μήνες');
  }

  function clampSuspendedInputs() {
    const yearsEl = byId('suspendedYears');
    const monthsEl = byId('suspendedMonths');
    clampBoundedIntegerInput(yearsEl, 2);
    clampBoundedIntegerInput(monthsEl, 11);
    if (Number(yearsEl.value) >= 2 && Number(monthsEl.value) > 0) monthsEl.value = '0';
  }

  function reset() {
    byId('category').value = 'PE';
    byId('serviceYears').value = '0';
    byId('serviceMonths').value = '0';
    byId('suspendedYears').value = '0';
    byId('suspendedMonths').value = '0';
    byId('qualification').value = 'none';
    byId('payrollProfile').value = 'permanent';
    byId('insuredStatus').value = 'new';
    byId('insuredStatus').disabled = false;
    byId('insuredStatus').setAttribute('aria-disabled', 'false');
    byId('ageGroup').value = 'over30';
    byId('disabilityTaxTreatment').value = 'none';
    byId('dependentChildren').value = '0';
    byId('positionAllowance').value = 'none';
    byId('remoteAreaAllowance').checked = false;
    byId('maternityPensionReduction').checked = false;
    byId('adedYDeduction').value = '0';
    byId('federationDeduction').value = '0';
    byId('associationDeduction').value = '0';
    byId('otherPayrollDeduction').value = '0';
    calculate();
  }

  document.querySelectorAll('input, select').forEach(el => {
    el.addEventListener('input', () => {
      if (el.id === 'serviceYears') clampBoundedIntegerInput(el, 50);
      if (el.id === 'serviceMonths') clampBoundedIntegerInput(el, 11);
      if (el.id === 'dependentChildren') clampBoundedIntegerInput(el, 20);
      if (el.id === 'suspendedYears' || el.id === 'suspendedMonths') clampSuspendedInputs();
      calculate();
    });
    el.addEventListener('change', () => {
      if (el.id === 'serviceYears') clampBoundedIntegerInput(el, 50);
      if (el.id === 'serviceMonths') clampBoundedIntegerInput(el, 11);
      if (el.id === 'dependentChildren') clampBoundedIntegerInput(el, 20);
      if (el.id === 'suspendedYears' || el.id === 'suspendedMonths') clampSuspendedInputs();
      calculate();
    });
  });
  const deductionBreakdownToggle = byId('deductionBreakdownToggle');
  if (deductionBreakdownToggle) {
    deductionBreakdownToggle.addEventListener('click', function () {
      const details = byId('deductionBreakdownDetails');
      if (!details) return;
      const expanded = deductionBreakdownToggle.getAttribute('aria-expanded') === 'true';
      deductionBreakdownToggle.setAttribute('aria-expanded', expanded ? 'false' : 'true');
      details.hidden = expanded;
    });
  }
  byId('printBtn').addEventListener('click', function () {
    calculate();
    window.print();
  });
  byId('resetBtn').addEventListener('click', reset);
  calculate();
})();
</script>
</body>
</html>
