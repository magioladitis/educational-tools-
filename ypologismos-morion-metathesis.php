<?php require_once __DIR__ . '/includes/config.php'; ?>
<!DOCTYPE html>
<html lang="el">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <?php require __DIR__ . '/includes/head-pwa.php'; ?>
  <title>Μόρια Μετάθεσης</title>
  <link rel="stylesheet" href="<?php echo htmlspecialchars(edu_asset_url('assets/common.css'), ENT_QUOTES, 'UTF-8'); ?>">
</head>
<body class="edu-ui edu-calc-standard edu-page-transfer">
<?php require_once __DIR__ . '/includes/header.php'; ?>
<?php require_once __DIR__ . '/includes/components/calculator-layout.php'; ?>

<?php calculatorContainerStart(array('class' => 'app')); ?>
  <?php calculatorHero(array(
    'title' => 'Μεταθέσεις Εκπαιδευτικών — Μόρια & Δικαιολογητικά',
    'intro_html' => 'Εργαλείο για εκπαιδευτικούς <strong>Δευτεροβάθμιας Εκπαίδευσης</strong>: ενδεικτική μοριοδότηση όπου εφαρμόζεται και προσωποποιημένος κατάλογος ελέγχου δικαιολογητικών ανά τύπο μετάθεσης.',
    'badges' => array('Βάση: εγκύκλιος 2025–2026 (αναμένεται 2026–2027)', 'Μ.Σ.Δ.', 'Δυσπρόσιτα / ειδικές υπηρετήσεις')
  )); ?>

  <?php calculatorColumnsStart(); ?>
    <?php calculatorMainStart(); ?>
      <?php calculatorCardStart(array('title' => 'Α. Τύπος διαδικασίας και βασικά κριτήρια')); ?>
        <div class="field-grid">
          <div class="field">
            <label for="mode">Τύπος διαδικασίας</label>
            <select id="mode">
              <option value="region">Μετάθεση από περιοχή σε περιοχή — Γενική Εκπαίδευση</option>
              <option value="local">Οριστική τοποθέτηση / βελτίωση εντός περιοχής</option>
              <option value="smeae">Μετάθεση σε ΣΜΕΑΕ / ΕΕΕΕΚ (Ειδική Αγωγή)</option>
              <option value="music">Μετάθεση σε Μουσικό Σχολείο</option>
              <option value="art">Μετάθεση σε Καλλιτεχνικό Σχολείο</option>
            </select>
          </div>
          <div class="field" id="transferModeGuidance" role="status" aria-live="polite" style="grid-column:1/-1; padding:12px; border:1px solid #b8c5d5; border-radius:8px;" >Επιλέξτε τύπο διαδικασίας για τις αντίστοιχες οδηγίες.</div>
        </div>
        <section id="transferGeneralFromEaePanel" style="padding:14px; margin:14px 0; border:1px solid #b8c5d5; border-radius:10px;" aria-label="Προέλευση εκπαιδευτικού για μετάθεση στη Γενική Εκπαίδευση">
          <h3 style="margin:0 0 8px">Προέλευση εκπαιδευτικού — μετάθεση στη Γενική Εκπαίδευση</h3>
          <p class="small-note">Ο ειδικός έλεγχος αφορά όσους <strong>διορίστηκαν στην ΕΑΕ</strong> ή <strong>μετατέθηκαν στην ΕΑΕ από τη Γενική</strong>. Οι δύο περιπτώσεις έχουν διαφορετικές προϋποθέσεις· η πενταετία δεν ισχύει για όλους.</p>
          <div class="field-grid">
            <div class="field">
              <label for="transferGeneralOrigin">Υπηρεσιακή προέλευση</label>
              <select id="transferGeneralOrigin">
                <option value="general">Γενική / Επαγγελματική Εκπαίδευση (δεν έχω μετατεθεί στην ΕΑΕ)</option>
                <option value="appointed_eae">Διορίστηκα απευθείας στην Ειδική Αγωγή</option>
                <option value="transferred_eae">Μετατέθηκα στην Ειδική Αγωγή από τη Γενική</option>
                <option value="other">Άλλη περίπτωση / δεν είμαι βέβαιος</option>
              </select>
            </div>
          </div>
          <div id="transferGeneralEaeAppointed" style="display:none">
            <div class="field-grid">
              <div class="field"><label for="transferGeneralEaeYears">Έτη υπηρεσίας σε σχολικές μονάδες ΕΑΕ από τον διορισμό</label>
                <input id="transferGeneralEaeYears" type="number" min="0" max="50" step="0.1" value="0" inputmode="decimal"></div>
              <div class="field"><label for="transferGeneralEaeAreaYears">Σχολικά έτη στην περιοχή διορισμού</label>
                <input id="transferGeneralEaeAreaYears" type="number" min="0" max="50" step="0.1" value="0" inputmode="decimal"></div>
            </div>
          </div>
          <div id="transferGeneralEaeTransferred" style="display:none">
            <div class="field-grid">
              <div class="field"><label for="transferGeneralEaeSinceTransfer">Έτη διδακτικής υπηρεσίας στην ΕΑΕ μετά τη μετάθεση</label>
                <input id="transferGeneralEaeSinceTransfer" type="number" min="0" max="50" step="0.1" value="0" inputmode="decimal"></div>
            </div>
            <div class="check-row"><label><input id="transferGeneralEaeOrganic" type="checkbox"> Έχω υπηρετήσει την οργανική μου θέση στην ΕΑΕ για ένα έτος</label></div>
          </div>
          <div id="transferGeneralEaeAdvice" role="status" aria-live="polite" class="small-note" style="padding:10px; border-left:3px solid #687991; margin-top:12px;"></div>
          <p class="small-note">Νομική βάση: εγκύκλιος <strong>129787/Ε2/15-10-2025</strong>, Μέρος Πρώτο, Κεφ. Γ΄ §7 (σελ. 11), άρθρο 48 §3 ν. 4415/2016 και άρθρο 62 §5α ν. 4589/2019. Για τον κύκλο 2026–2027 απαιτείται έλεγχος της νέας εγκυκλίου. Ο προέλεγχος δεν πιστοποιεί δικαίωμα μετάθεσης.</p>
        </section>
        <div id="transferNumericalInputs">
        <div class="field-grid">
          <div class="field">
            <label for="serviceYears">Συνολική αναγνωρισμένη υπηρεσία — έτη</label>
            <input id="serviceYears" type="number" min="0" max="50" step="1" value="0" inputmode="numeric">
          </div>
          <div class="field">
            <label for="serviceMonths">Επιπλέον μήνες</label>
            <input id="serviceMonths" type="number" min="0" max="11" step="1" value="0" inputmode="numeric">
          </div>
          <div class="field">
            <label for="serviceDays">Επιπλέον ημέρες <small>15+ → 1 μήνας</small></label>
            <input id="serviceDays" type="number" min="0" max="29" step="1" value="0" inputmode="numeric">
          </div>
          <div class="field">
            <label for="eligibleChildren">Τέκνα που μοριοδοτούνται</label>
            <input id="eligibleChildren" type="number" min="0" max="20" step="1" value="0" inputmode="numeric">
            <div class="field-hint">4 + 4 + 6 + 7 μόρια για κάθε επόμενο τέκνο.</div>
          </div>
        </div>

        <div class="check-row"><label><input id="familyStatusEligible" type="checkbox"> Δικαιούμαι τις 4 μονάδες οικογενειακής κατάστασης (γάμος/σύμφωνο συμβίωσης ή προβλεπόμενη περίπτωση γονέα με επιμέλεια)</label></div>
        <div class="check-row"><label><input id="coService" type="checkbox"> Δικαιούμαι συνυπηρέτηση για την περιοχή/τον δήμο που εξετάζω (+4)</label></div>
        <div class="check-row"><label><input id="locality" type="checkbox"> Δικαιούμαι εντοπιότητα για την περιοχή/τον δήμο που εξετάζω (+4)</label></div>
        <div class="check-row" id="firstPreferenceRow"><label><input id="firstPreference" type="checkbox"> Η περιοχή είναι η πρώτη προτίμησή μου (+2)</label></div>

        <?php calculatorDisclosure(array(
          'id' => 'modeNote',
          'summary' => 'Πώς αλλάζουν συνυπηρέτηση, εντοπιότητα και πρώτη προτίμηση;',
          'html' => 'Στη μετάθεση από περιοχή σε περιοχή, συνυπηρέτηση και εντοπιότητα εξετάζονται για την <strong>περιοχή μετάθεσης</strong>. Στην οριστική τοποθέτηση/βελτίωση συνδέονται με τον <strong>συγκεκριμένο Δήμο</strong> όπου βρίσκεται το σχολείο. Η πρώτη προτίμηση μοριοδοτείται μόνο στη μετάθεση από περιοχή σε περιοχή.',
          'open' => true,
          'attrs' => array('data-mobile-collapsed' => 'true')
        )); ?>
        </div>
      <?php calculatorCardEnd(); ?>

      <div id="transferMsdCard">
      <?php calculatorCardStart(array('title' => 'Β. Μονάδες Συνθηκών Διαβίωσης (Μ.Σ.Δ.)')); ?>
        <div class="field" style="margin-bottom:14px">
          <label for="msdMode">Τρόπος καταχώρισης Μ.Σ.Δ.</label>
          <select id="msdMode">
            <option value="periods">Υπολογισμός από τις υπηρετήσεις μου</option>
            <option value="direct">Απευθείας καταχώριση συνολικών μορίων Μ.Σ.Δ.</option>
          </select>
          <div class="field-hint">Μπορείτε να δείτε τις υπηρετήσεις και τα ήδη καταχωρισμένα μόρια μετάθεσης στο <a href="https://teachers.minedu.gov.gr/" target="_blank" rel="noopener noreferrer">teachers.minedu.gov.gr ↗</a> (σύνδεση με λογαριασμό ΠΣΔ).</div>
        </div>
        <div id="msdPeriodsPanel">
          <p class="small-note">Πρόσθεσε μία γραμμή για κάθε διαφορετική υπηρέτηση/κατηγορία. Οι ημέρες ≥15 λογίζονται ως πλήρης μήνας. Για παράλληλη υπηρεσία μπορείς να δηλώσεις τις ημέρες ανά εβδομάδα (σύνολο 5/5).</p>
          <div id="msdRows"></div>
          <button class="add-row" id="addMsdRow" type="button">+ Προσθήκη υπηρέτησης</button>

        <?php calculatorDisclosure(array(
          'summary' => 'Πότε διπλασιάζονται οι Μ.Σ.Δ. απομακρυσμένων σχολείων;',
          'html' => '<strong>Διπλασιασμός απομακρυσμένων:</strong> από το 2018–2019 αφορά πραγματική υπηρεσία σε σχολεία κατηγοριών Ι΄, ΙΑ΄, ΙΒ΄ και ΙΓ΄, όταν έχει συμπληρωθεί τουλάχιστον διετής συνεχόμενη υπηρεσία. Στον υπολογιστή ενεργοποίησέ τον μόνο όταν το δικαίωμα έχει πράγματι θεμελιωθεί.',
          'open' => true,
          'attrs' => array('data-mobile-collapsed' => 'true')
        )); ?>
        </div>
        <div id="msdDirectPanel" style="display:none">
          <div class="field">
            <label for="msdDirectPoints">Συνολικές μονάδες συνθηκών διαβίωσης από το επίσημο μητρώο</label>
            <input id="msdDirectPoints" type="text" inputmode="decimal" autocomplete="off" placeholder="π.χ. 48,75" aria-describedby="msdDirectHelp msdDirectStatus">
            <div class="field-hint" id="msdDirectHelp">Πληκτρολογήστε τον ακριβή αριθμό Μ.Σ.Δ. με έως δύο δεκαδικά (κόμμα ή τελεία). Οι υπηρετήσεις που συμπληρώθηκαν στην άλλη καρτέλα δεν θα προστεθούν.</div>
            <div class="field-hint" id="msdDirectStatus" role="status" aria-live="polite"></div>
          </div>
        </div>
      <?php calculatorCardEnd(); ?>
      </div>

      <section id="transferEaePanel" class="calculator-card" style="display:none; padding:18px; border:1px solid #b8c5d5; border-radius:12px; margin-bottom:16px;">
        <h2>Β.1 Θεμελίωση δικαιώματος μετάθεσης στην ΕΑΕ</h2>
        <p class="small-note">Επιλέξτε την κύρια διαδρομή τεκμηρίωσης. Το δικαίωμα εξετάζεται <strong>συνδυαστικά</strong> με την προέλευση, τον χρόνο υπηρεσίας και τον τύπο θέσης. Οι ενδείξεις αποτελούν προέλεγχο, όχι διοικητική απόφαση.</p>
        <div class="field-grid">
          <div class="field"><label for="transferEaeOrigin">Από ποια εκπαίδευση ζητείται η μετάθεση;</label>
            <select id="transferEaeOrigin">
              <option value="general">Από Γενική / Επαγγελματική Εκπαίδευση προς ΕΑΕ</option>
              <option value="eae">Από δομή ΕΑΕ προς άλλη δομή ΕΑΕ</option>
            </select>
          </div>
          <div class="field"><label for="transferEaeArea">Περιοχή της μετάθεσης</label>
            <select id="transferEaeArea"><option value="other">Άλλη περιοχή μετάθεσης</option><option value="same">Ίδια περιοχή μετάθεσης / βελτίωση</option></select>
          </div>
          <div class="field"><label for="transferEaeDestination">Δομή προορισμού</label>
            <select id="transferEaeDestination"><option value="smeae">ΣΜΕΑΕ / ΕΕΕΕΚ</option><option value="deaf">Σχολείο Κωφών</option><option value="blind">Σχολείο Τυφλών</option></select>
          </div>
          <div class="field"><label for="transferEaeBranch">Κλάδος</label>
            <select id="transferEaeBranch"><option value="other">Άλλος κλάδος Δ.Ε.</option><option value="pe11">ΠΕ11 Φυσικής Αγωγής</option></select>
          </div>
          <div class="field"><label for="transferEaeQualification">Βασικό αποδεικτικό εξειδίκευσης στην ΕΑΕ</label>
            <select id="transferEaeQualification">
              <option value="none">Δεν έχει επιλεγεί ακόμη</option>
              <option value="doctorate">Διδακτορικό στην ΕΑΕ ή Σχολική Ψυχολογία</option>
              <option value="masters">Μεταπτυχιακό στην ΕΑΕ ή Σχολική Ψυχολογία</option>
              <option value="two_year">Πτυχίο διετούς μετεκπαίδευσης στην ΕΑΕ</option>
              <option value="experience">Βασικό πτυχίο ΑΕΙ και τουλάχιστον 5ετής υπηρεσία στην ΕΑΕ</option>
              <option value="pe11">ΠΕ11: κύρια ειδικότητα ΤΕΦΑΑ στην Ειδική/Προσαρμοσμένη Φυσική Αγωγή</option>
            </select>
          </div>
          <div class="field" id="transferEaeExperienceRow" style="display:none"><label for="transferEaeExperienceYears">Αποδεδειγμένα έτη διδακτικής υπηρεσίας στην ΕΑΕ</label><input id="transferEaeExperienceYears" type="number" min="0" max="50" step="0.1" inputmode="decimal" value="0"></div>
          <div class="field" id="transferEaeGeneralYearsRow"><label for="transferEaeTeachingYears">Διδακτικά έτη από διορισμό (Γενική → ΕΑΕ)</label><input id="transferEaeTeachingYears" type="number" min="0" max="50" step="0.1" inputmode="decimal" value="0"></div>
        </div>
        <div id="transferEaeNewlyRow" class="check-row"><label><input type="checkbox" id="transferEaeNewly"> Νεοδιόριστος: χρειάζεται έλεγχος διετίας στην περιοχή διορισμού</label></div>
        <div class="field" id="transferEaeAreaYearsRow" style="display:none; margin-top:10px"><label for="transferEaeAreaYears">Έτη υπηρεσίας στην περιοχή διορισμού</label><input id="transferEaeAreaYears" type="number" min="0" max="50" step="0.1" inputmode="decimal" value="0"></div>
        <div class="field-grid" style="margin-top:14px">
          <div class="field"><label for="transferEaePreviousYear">Έχετε αποστείλει ηλεκτρονικό φάκελο ΕΑΕ σε προηγούμενο κύκλο;</label>
            <select id="transferEaePreviousYear"><option value="none">Όχι / δεν γνωρίζω</option><option value="2024">Ναι, το 2024</option><option value="2025">Ναι, το 2025</option><option value="other">Ναι, σε άλλο έτος</option></select>
          </div>
        </div>
        <div class="check-row"><label><input type="checkbox" id="transferEaeNewDocuments"> Έχω νέα στοιχεία ή δικαιολογητικά στον φάκελο ΕΑΕ που δεν είχαν υποβληθεί.</label></div>
        <div id="transferEaeDossierAdvice" role="status" aria-live="polite" class="small-note" style="margin:10px 0"></div>
        <div id="transferEaeAdvice" role="status" aria-live="polite" style="padding:12px; border-left:3px solid #687991; background:rgba(109,127,160,.08); margin-top:14px;"></div>
        <p class="small-note">Νομική βάση: εγκύκλιος 129787/Ε2/15-10-2025, Μέρος Γ΄, κεφάλαιο Δ΄, παρ. 2–5 (σελ. 34–37). Ελέγξτε την εγκύκλιο του έτους της αίτησης.</p>
      </section>

      <section id="transferQualificationPanel" class="calculator-card" style="display:none; padding:18px; border:1px solid #b8c5d5; border-radius:12px; margin-bottom:16px;">
        <h2 id="transferQualificationTitle">Ειδική διαδικασία προσόντων</h2>
        <p id="transferQualificationIntro"></p>
        <div class="field-grid">
          <div class="field"><label for="transferSpecialty">Κλάδος / ειδικότητα (προαιρετικά)</label><input id="transferSpecialty" type="text" maxlength="100" placeholder="π.χ. ΠΕ79.01, ΠΕ08"></div>
          <div class="field" id="transferEducationTypeField"><label for="transferEducationType">Κατηγορία αντικειμένου</label><select id="transferEducationType"><option value="general">Γενική παιδεία / κοινός κλάδος</option><option value="specialized">Μουσική / καλλιτεχνική ειδίκευση</option></select></div>
        </div>
        <div id="transferMusicEligibility" style="display:none">
          <div class="field-grid">
            <div class="field"><label for="transferMusicTeachingYears">Έτη διδακτικής εμπειρίας σε σχολεία Δευτεροβάθμιας</label><input id="transferMusicTeachingYears" type="number" min="0" max="50" step="0.1" inputmode="decimal" value="0"></div>
            <div class="field"><label for="transferMusicException">Εξαίρεση από την πενταετία</label><select id="transferMusicException"><option value="none">Δεν επικαλούμαι εξαίρεση</option><option value="direct_music">Εκπαιδευτικός μουσικής παιδείας με απευθείας διορισμό σε Μουσικό</option><option value="primary_transfer">Μετάταξη από Πρωτοβάθμια σε Μουσικό ή Καλλιτεχνικό</option></select></div>
          </div>
          <div class="check-row"><label><input id="transferMusicOrganic" type="checkbox"> Έχω υπηρετήσει την οργανική μου θέση (ή υπάρχει ειδική νόμιμη εξαίρεση προς έλεγχο)</label></div>
          <div id="transferMusicSpecializationPanel" style="display:none; margin-top:14px">
            <label for="transferMusicSubject">Αντικείμενο μουσικής εξειδίκευσης</label>
            <select id="transferMusicSubject">
              <option value="unspecified">Δεν επιλέχθηκε ακόμη</option>
              <option value="theory">Θεωρητικά της μουσικής</option>
              <option value="traditional">Ελληνική παραδοσιακή μουσική</option>
              <option value="ensemble">Διεύθυνση χορωδιακών / ορχηστρικών συνόλων</option>
              <option value="instrument">Ευρωπαϊκό μουσικό όργανο</option>
              <option value="other">Άλλο μουσικό αντικείμενο — έλεγχος ειδικότητας</option>
            </select>
          </div>
          <div class="field-grid" style="margin-top:14px"><div class="field"><label for="transferMusicPreviousYear">Έχει υποβληθεί προηγούμενος ηλεκτρονικός φάκελος για Μουσικά;</label>
            <select id="transferMusicPreviousYear"><option value="none">Όχι / δεν γνωρίζω</option><option value="2021">2021</option><option value="2022">2022</option><option value="2023">2023</option><option value="2024">2024</option><option value="2025">2025</option><option value="other">Άλλο έτος</option></select></div></div>
          <div class="check-row"><label><input id="transferMusicNewDocuments" type="checkbox"> Υπάρχουν νέα στοιχεία/δικαιολογητικά για τον φάκελο Μουσικών.</label></div>
          <div id="transferMusicDossierAdvice" role="status" aria-live="polite" class="small-note" style="margin:10px 0"></div>
          <div id="transferMusicAdvice" role="status" aria-live="polite" style="padding:12px; border-left:3px solid #687991; background:rgba(109,127,160,.08); margin-top:14px;"></div>
        </div>
        <div id="transferArtEligibility" style="display:none">
          <div class="field-grid">
            <div class="field"><label for="transferArtBranch">Κλάδος στην Καλλιτεχνική Παιδεία</label>
              <select id="transferArtBranch">
                <option value="general">Κλάδος γενικής παιδείας</option>
                <option value="pe08">ΠΕ08 — Εικαστικών</option><option value="pe11">ΠΕ11 — Φυσικής Αγωγής</option>
                <option value="pe79">ΠΕ79.01 — Μουσικής Επιστήμης</option>
                <option value="pe9101">ΠΕ91.01 — Θεατρικών Σπουδών</option><option value="pe9102">ΠΕ91.02 — Δραματικής Τέχνης</option>
                <option value="other">Άλλος / απαιτείται έλεγχος κλάδου</option>
              </select>
            </div>
            <div class="field" id="transferArtOrientationRow" style="display:none"><label for="transferArtOrientation">Κατεύθυνση / σχετικό αντικείμενο</label>
              <select id="transferArtOrientation"><option value="">Επιλέξτε (για συμβουλές)</option>
                <option value="visual">Εικαστικές Τέχνες</option><option value="theater">Θέατρο – Κινηματογράφος</option>
                <option value="dance">Χορός</option><option value="other">Άλλο / διαθεματικό αντικείμενο</option>
              </select>
            </div>
            <div class="field"><label for="transferArtTeachingYears">Διδακτική εμπειρία σε σχολεία Δ.Ε. (έτη)</label>
              <input id="transferArtTeachingYears" type="number" min="0" max="50" step="0.1" inputmode="decimal" value="0">
            </div>
            <div class="field"><label for="transferArtException">Εξαίρεση από την πενταετία</label>
              <select id="transferArtException"><option value="none">Δεν επικαλούμαι εξαίρεση</option>
                <option value="direct_music">Μουσική παιδεία με απευθείας διορισμό σε Μουσικό Σχολείο</option>
                <option value="primary_transfer">Μετάταξη από Πρωτοβάθμια σε Μουσικό ή Καλλιτεχνικό Σχολείο</option>
              </select>
            </div>
          </div>
          <div class="check-row"><label><input id="transferArtOrganic" type="checkbox"> Έχω υπηρετήσει την οργανική μου θέση (ή υπάρχει ειδική εξαίρεση προς υπηρεσιακό έλεγχο)</label></div>
          <div class="field-grid" style="margin-top:14px"><div class="field"><label for="transferArtPreviousYear">Προηγούμενος ηλεκτρονικός φάκελος για Καλλιτεχνικά</label>
            <select id="transferArtPreviousYear"><option value="none">Όχι / δεν γνωρίζω</option>
              <option value="2021">2021</option><option value="2022">2022</option><option value="2023">2023</option>
              <option value="2024">2024</option><option value="2025">2025</option><option value="other">Άλλο έτος</option>
            </select></div></div>
          <div class="check-row"><label><input id="transferArtNewDocuments" type="checkbox"> Έχω νέα στοιχεία ή δικαιολογητικά που δεν είχαν συμπεριληφθεί στον παλαιότερο φάκελο.</label></div>
          <div id="transferArtDossierAdvice" role="status" aria-live="polite" class="small-note" style="margin:10px 0"></div>
          <div id="transferArtAdvice" role="status" aria-live="polite" style="padding:12px; border-left:3px solid #687991; background:rgba(109,127,160,.08); margin-top:14px;"></div>
          <p class="small-note">Βάση αναφοράς: εγκύκλιος 129787/Ε2/15-10-2025, Μέρος Τρίτο, Κεφ. ΣΤ΄ (σελ. 40–42). Η ημερομηνία 31-08-2026 αφορά αποκλειστικά τον κύκλο 2025–2026. Ο τελικός έλεγχος γίνεται από τα αρμόδια όργανα.</p>
        </div>
        <p class="small-note" id="transferQualificationFootnote">Ο έλεγχος δεν αντικαθιστά την εξέταση από το αρμόδιο υπηρεσιακό συμβούλιο ή την Επιστημονική Επιτροπή Μουσικής Παιδείας. Εγκύκλιος 2025–2026, Μέρος Γ΄, κεφάλαιο Ε΄, σελ. 38–39.</p>
      </section>

      <?php calculatorActions(array(
        array('id' => 'calculateBtn', 'class' => 'primary', 'label' => 'Υπολογισμός μορίων'),
        array('id' => 'resetBtn', 'class' => 'secondary', 'label' => 'Καθαρισμός')
      )); ?>

      <section id="transferDocumentsPanel" style="padding:16px; border:1px solid #b8c5d5; border-radius:12px; margin:16px 0;">
        <h2 style="margin-top:0">Γ. Σύμβουλος δικαιολογητικών</h2>
        <p class="small-note">Εξατομικευμένες οδηγίες προετοιμασίας με βάση τα δηλωμένα στοιχεία. Η <strong>ηλεκτρονική αίτηση μετάθεσης και η οριστικοποίησή της</strong> γίνονται στο <a href="https://teachers.minedu.gov.gr/" target="_blank" rel="noopener noreferrer">teachers.minedu.gov.gr ↗</a>. Τα δικαιολογητικά μπορεί να υποβάλλονται με διαφορετική διαδικασία, σύμφωνα με την εγκύκλιο. Καμία ένδειξη δεν υποκαθιστά τον έλεγχο της αρμόδιας ΔΔΕ.</p>
        <div id="transferDocumentChecklist" aria-live="polite"></div>
        <button type="button" class="secondary" id="transferPrintChecklist">Εκτύπωση λίστας δικαιολογητικών</button>
      </section>
      <?php calculatorResultMessage(array(
        'variant' => 'disclaimer',
        'html' => '<strong>Τι περιλαμβάνει:</strong> υπολογίζει πληροφοριακά τα βασικά μόρια και παρέχει <strong>προέλεγχο</strong> θεμελίωσης για ορισμένες περιπτώσεις ΕΑΕ/Μουσικών/Καλλιτεχνικών. Δεν πιστοποιεί δικαίωμα μετάθεσης, σειρά κατάταξης, όλες τις εξαιρέσεις ή ειδικές κατηγορίες προτεραιότητας. Οι υπηρεσιακές πράξεις και η νεότερη εγκύκλιος υπερισχύουν.'
      )); ?>
    <?php calculatorMainEnd(); ?>

    <div id="transferScoringResults">
    <?php calculatorResultsStart(array('aria_live' => 'polite')); ?>
      <?php calculatorScoreHeader(array(
        'value' => '0',
        'value_id' => 'totalResult',
        'label' => 'συνολικές μονάδες',
        'cap' => 'Ενδεικτική βαθμολογία μετάθεσης'
      )); ?>
      <?php calculatorResultRow(array('label' => 'Συνολική υπηρεσία', 'value' => '0', 'value_id' => 'servicePointsResult')); ?>
      <?php calculatorResultRow(array('label' => 'Μ.Σ.Δ.', 'value' => '0', 'value_id' => 'msdPointsResult')); ?>
      <?php calculatorResultRow(array('label' => 'Συνυπηρέτηση', 'value' => '0', 'value_id' => 'coServiceResult')); ?>
      <?php calculatorResultRow(array('label' => 'Οικογενειακοί λόγοι', 'value' => '0', 'value_id' => 'familyResult')); ?>
      <?php calculatorResultRow(array('label' => 'Εντοπιότητα', 'value' => '0', 'value_id' => 'localityResult')); ?>
      <?php calculatorResultRow(array('label' => 'Πρώτη προτίμηση', 'value' => '0', 'value_id' => 'firstPreferenceResult')); ?>
      <?php calculatorResultMessage(array('variant' => 'status', 'id' => 'statusResult', 'html' => 'Πρόσθεσε τα στοιχεία και τις υπηρετήσεις σου.')); ?>
    <?php calculatorResultsEnd(); ?>
    </div>
  <?php calculatorColumnsEnd(); ?>
<?php calculatorContainerEnd(); ?>

<?php sourceCardStart(); ?>
  <p>Οι λίστες δικαιολογητικών είναι οδηγός προετοιμασίας και απαιτούν επαλήθευση με την εγκύκλιο της αντίστοιχης περιόδου αιτήσεων. Ο υπολογισμός βασίζεται στην εγκύκλιο μεταθέσεων εκπαιδευτικών Δευτεροβάθμιας Εκπαίδευσης σχολικού έτους 2025–2026 (129787/Ε2/15-10-2025) και στο π.δ. 50/1996 όπως ισχύει.</p>
  <?php sourceCardLinksStart(); ?>
    <?php sourceCardLink('https://www.minedu.gov.gr/publications/docs2023/%CE%95%CE%93%CE%9A%CE%A5%CE%9A%CE%9B%CE%99%CE%9F%CE%A3_%CE%9C%CE%95%CE%A4%CE%91%CE%98%CE%95%CE%A3%CE%95%CE%A9%CE%9D_%CE%94%CE%95_2025-2026_6%CE%94%CE%A7%CE%9F46%CE%9D%CE%9A%CE%A0%CE%94-%CE%A4%CE%954.pdf', 'ΥΠΑΙΘΑ — Εγκύκλιος μεταθέσεων Δ.Ε. 2025–2026 ↗'); ?>
    <?php sourceCardLink('https://www.minedu.gov.gr/monimoi-metatakseis-metatheseis-apospaseis', 'ΥΠΑΙΘΑ — Νομοθεσία κινητικότητας / Π.Δ. 50/1996 ↗'); ?>
    <?php sourceCardLink('https://www.minedu.gov.gr/kinitikotita/metatheseis-egkyklioi-proskliseis', 'ΥΠΑΙΘΑ — Τρέχουσες εγκύκλιοι μεταθέσεων ↗'); ?>
    <?php sourceCardLink('https://teachers.minedu.gov.gr/', 'Online Σύστημα Μητρώου & Αιτήσεων Μετάθεσης ↗'); ?>
  <?php sourceCardLinksEnd(); ?>
  <?php sourceCardDisclaimerStart(); ?>Το αποτέλεσμα είναι ενημερωτικό. Οι καταχωρισμένες υπηρετήσεις και οι μονάδες στο επίσημο Μητρώο, καθώς και οι αποφάσεις της αρμόδιας Διεύθυνσης Εκπαίδευσης, υπερισχύουν.<?php sourceCardDisclaimerEnd(); ?>
<?php sourceCardEnd(); ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
<script src="<?php echo htmlspecialchars(edu_asset_url('includes/transfer-calculations.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
<script src="<?php echo htmlspecialchars(edu_asset_url('assets/common.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
<script src="<?php echo htmlspecialchars(edu_asset_url('includes/transfer-eligibility.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
<script src="<?php echo htmlspecialchars(edu_asset_url('includes/transfer-documents.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
<script src="<?php echo htmlspecialchars(edu_asset_url('includes/transfer-ui.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
</body>
</html>
