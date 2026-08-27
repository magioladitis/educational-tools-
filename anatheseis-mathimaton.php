<?php require_once __DIR__ . '/includes/config.php'; ?>
<?php require_once __DIR__ . '/includes/teaching-assignments-data.php'; ?>
<!doctype html>
<html lang="el">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Αναθέσεις Μαθημάτων ανά Ειδικότητα</title>
  <link rel="stylesheet" href="<?php echo htmlspecialchars(edu_asset_url('assets/common.css'), ENT_QUOTES, 'UTF-8'); ?>">
  <style>
    /* Page-specific only: the shared calculator UI comes from assets/common.css. */
    .edu-page-teaching-assignments .school-type-group{
      margin-top:12px;
    }

    .edu-page-teaching-assignments .school-type-group__title{
      margin:0 0 7px;
      color:var(--edu-muted);
      font-size:13px;
      font-weight:800;
      text-transform:uppercase;
      letter-spacing:.03em;
    }

    .edu-page-teaching-assignments .school-type-options{
      display:grid;
      grid-template-columns:repeat(2,minmax(0,1fr));
      gap:10px;
      margin-bottom:4px;
    }

    .edu-page-teaching-assignments .school-type-group--all .school-type-options{
      grid-template-columns:1fr;
    }

    .edu-page-teaching-assignments .school-type-group--all .checkrow{
      background:var(--edu-primary-soft);
      border-color:var(--edu-primary);
    }

    .edu-page-teaching-assignments .school-type-options .checkrow{
      margin:0;
      padding:12px 14px;
      border:1px solid var(--edu-border);
      border-radius:12px;
      background:var(--edu-surface-soft);
    }

    .edu-page-teaching-assignments #assignmentResults{
      margin-top:14px;
    }

    .edu-page-teaching-assignments #assignmentResults > section{
      margin-top:18px;
    }

    .edu-page-teaching-assignments #assignmentResults > section:first-child{
      margin-top:8px;
    }

    .edu-page-teaching-assignments #assignmentResults h3{
      display:flex;
      align-items:center;
      justify-content:space-between;
      gap:10px;
      margin:0 0 8px;
      padding-bottom:8px;
      border-bottom:1px solid var(--edu-result-row-separator);
      font-size:1.04rem;
    }

    .edu-page-teaching-assignments #assignmentResults .result-row{
      align-items:flex-start;
    }

    .edu-page-teaching-assignments #assignmentResults .result-row > span{
      min-width:0;
      line-height:1.35;
    }

    .edu-page-teaching-assignments #assignmentResults .result-row > span > strong{
      display:block;
      margin-bottom:3px;
      text-align:left;
    }

    .edu-page-teaching-assignments #assignmentResults .result-row small{
      display:block;
      color:var(--edu-muted);
      line-height:1.35;
    }

    .edu-page-teaching-assignments .assignment-badge{
      flex:0 0 auto;
      min-width:34px;
      padding:4px 8px;
      border-radius:999px;
      text-align:center !important;
      font-size:.82rem;
      line-height:1.2;
    }

    .edu-page-teaching-assignments .assignment-a .assignment-badge{
      background:var(--edu-success-soft);
      color:var(--edu-success);
    }

    .edu-page-teaching-assignments .assignment-b .assignment-badge{
      background:var(--edu-primary-soft);
      color:var(--edu-primary-dark);
    }

    .edu-page-teaching-assignments .assignment-c .assignment-badge{
      background:var(--edu-warning-soft);
      color:var(--edu-warning);
    }

    .edu-page-teaching-assignments .assignment-s .assignment-badge{
      background:var(--edu-neutral-soft);
      color:var(--edu-muted);
      min-width:64px;
    }

    @media (max-width:700px){
      .edu-page-teaching-assignments .school-type-options{
        grid-template-columns:1fr;
      }
    }
  </style>
</head>
<body class="edu-ui edu-calc-standard edu-page-teaching-assignments">
<?php require_once __DIR__ . '/includes/header.php'; ?>
<?php require_once __DIR__ . '/includes/components/calculator-layout.php'; ?>

<main id="teachingAssignmentsTool" class="app">
  <?php calculatorHero(array(
    'title_html' => 'Αναθέσεις Μαθημάτων ανά Ειδικότητα',
    'intro' => 'Επίλεξε τον κλάδο / την ειδικότητά σου και δες ποια μαθήματα έχεις σε Α΄, Β΄ ή Γ΄ ανάθεση.',
    'meta_class' => 'meta',
    'badges' => array('2026–2027', 'Ημερήσια & Εσπερινά', 'ΕΠΑ.Λ.', 'Ε.Α.Ε.', 'ΕΝ.Ε.Ε.ΓΥ.-Λ.', 'Καλλιτεχνικά', 'Α΄ · Β΄ · Γ΄ ανάθεση')
  )); ?>

  <?php calculatorColumnsStart(); ?>
    <?php calculatorMainStart(); ?>
      <?php calculatorCardStart(); ?>
        <h2>Κλάδος / ειδικότητα</h2>
        <p class="cap">Επίλεξε τον κλάδο σου για να εμφανιστούν αυτόματα οι αναθέσεις μαθημάτων που αντιστοιχούν στην ειδικότητά σου.</p>

        <div class="field-grid">
          <div class="field">
            <label for="specialty">Κλάδος / ειδικότητα</label>
            <select id="specialty">
              <option value="">— Επιλογή κλάδου —</option>
              <?php foreach (teachingAssignmentKnownSpecialties() as $code): ?>
                <option value="<?php echo htmlspecialchars($code, ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($code, ENT_QUOTES, 'UTF-8'); ?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>

        <div class="field">
          <label>Τύπος σχολείου</label>
          <div class="school-type-group school-type-group--all">
            <div class="school-type-options">
              <div class="checkrow">
                <input type="checkbox" id="schoolAll">
                <label for="schoolAll"><strong>Όλα</strong> <small>(επιλογή / αποεπιλογή όλων των δομών)</small></label>
              </div>
            </div>
          </div>
          <div class="school-type-group">
            <div class="school-type-group__title">Γενική Εκπαίδευση</div>
            <div class="school-type-options">
              <div class="checkrow">
                <input type="checkbox" id="schoolGymnasio" checked>
                <label for="schoolGymnasio">Γυμνάσιο</label>
              </div>
              <div class="checkrow">
                <input type="checkbox" id="schoolEveningGym">
                <label for="schoolEveningGym">Εσπερινό Γυμνάσιο</label>
              </div>
              <div class="checkrow">
                <input type="checkbox" id="schoolGel" checked>
                <label for="schoolGel">Γενικό Λύκειο (ΓΕΛ)</label>
              </div>
              <div class="checkrow">
                <input type="checkbox" id="schoolEveningGel">
                <label for="schoolEveningGel">Εσπερινό ΓΕΛ</label>
              </div>
            </div>
          </div>
          <div class="school-type-group">
            <div class="school-type-group__title">Επαγγελματική Εκπαίδευση</div>
            <div class="school-type-options">
              <div class="checkrow">
                <input type="checkbox" id="schoolEpal">
                <label for="schoolEpal">ΕΠΑ.Λ. <small>(Α΄ πλήρης · Β΄/Γ΄ Γενικής Παιδείας · 2 Τομείς Β΄)</small></label>
              </div>
              <div class="checkrow">
                <input type="checkbox" id="schoolEveningEpal">
                <label for="schoolEveningEpal">Εσπερινό ΕΠΑ.Λ. <small>(ίδιες αναθέσεις)</small></label>
              </div>
            </div>
          </div>
          <div class="school-type-group">
            <div class="school-type-group__title">Ειδική Αγωγή και Εκπαίδευση</div>
            <div class="school-type-options">
              <div class="checkrow">
                <input type="checkbox" id="schoolEaeGym">
                <label for="schoolEaeGym">Γυμνάσιο Ε.Α.Ε.</label>
              </div>
              <div class="checkrow">
                <input type="checkbox" id="schoolEaeLykeio">
                <label for="schoolEaeLykeio">Λύκειο Ε.Α.Ε.</label>
              </div>
              <div class="checkrow">
                <input type="checkbox" id="schoolEneegylGym">
                <label for="schoolEneegylGym">Γυμνάσιο ΕΝ.Ε.Ε.ΓΥ.-Λ.</label>
              </div>
              <div class="checkrow">
                <input type="checkbox" id="schoolEneegylLykeio">
                <label for="schoolEneegylLykeio">Λύκειο ΕΝ.Ε.Ε.ΓΥ.-Λ. <small>(πλήρης κάλυψη Α΄–Δ΄)</small></label>
              </div>
            </div>
          </div>
          <div class="school-type-group">
            <div class="school-type-group__title">Καλλιτεχνικά Σχολεία</div>
            <div class="school-type-options">
              <div class="checkrow">
                <input type="checkbox" id="schoolKallitexnikoGym">
                <label for="schoolKallitexnikoGym">Καλλιτεχνικό Γυμνάσιο <small>(καλλιτεχνικά μαθήματα)</small></label>
              </div>
            </div>
          </div>
          <p class="help"><strong>ΕΠΑ.Λ.:</strong> ενεργοποιήθηκε η υπάρχουσα βάση για Α΄ τάξη (Γενικής Παιδείας, Προσανατολισμού και Επιλογής), Β΄/Γ΄ Γενικής Παιδείας και προστέθηκαν οι <strong>Τομείς Γεωπονίας, Τροφίμων και Περιβάλλοντος</strong> και <strong>Διοίκησης και Οικονομίας της Β΄ τάξης</strong>. Οι υπόλοιποι Τομείς Β΄ και οι Ειδικότητες Γ΄ θα προστεθούν σταδιακά με έλεγχο ανά πίνακα.</p>
          <p class="help"><strong>Καλλιτεχνικό Γυμνάσιο:</strong> έχουν περαστεί οι αναθέσεις του ισχύοντος πίνακα για τους κλάδους <strong>ΠΕ/ΤΕ</strong>. Οι ειδικοί πίνακες <strong>ΚΙΝΗΣΗ–ΧΟΡΟΣ, ΚΙΝΗΜΑΤΟΓΡΑΦΟΥ, ΚΛΑΣΙΚΟΥ ΧΟΡΟΥ και ΣΥΓΧΡΟΝΟΥ ΧΟΡΟΥ</strong> θα ενσωματωθούν χωριστά, ώστε να αποτυπωθούν σωστά ως εξειδικευμένοι πίνακες και όχι ως συμβατικοί κλάδοι.</p>
          <p class="help"><strong>ΕΝ.Ε.Ε.ΓΥ.-Λ.:</strong> πλήρης κάλυψη της Υ.Α. 69785/Δ3/2026: Γυμνάσιο, <strong>Α΄ τάξη Λυκείου</strong> (Γενική Παιδεία, Προσανατολισμός και Επιλογής), <strong>Γενική Παιδεία Β΄, Γ΄ και Δ΄</strong>, οι <strong>8 κοινοί Τομείς Β΄–Γ΄</strong> και οι <strong>33 ειδικότητες της Δ΄ τάξης</strong>, με τις κατά προτεραιότητα αναθέσεις και τις ειδικές προϋποθέσεις του ΦΕΚ.</p>
        </div>

        <div class="field" id="gradeWrap">
          <label for="gradeFilter">Τάξη Λυκείου</label>
          <select id="gradeFilter">
            <option value="all">Όλες οι τάξεις</option>
            <option value="Α΄">Α΄ τάξη</option>
            <option value="Β΄">Β΄ τάξη</option>
            <option value="Γ΄">Γ΄ τάξη</option>
            <option value="Δ΄">Δ΄ τάξη</option>
          </select>
        </div>

        <div class="note">
          Στα <strong>Εργαστήρια Δεξιοτήτων</strong> η Β΄ ανάθεση μπορεί να αφορά «όλες τις άλλες ειδικότητες», σύμφωνα με τον αντίστοιχο πίνακα αναθέσεων.
        </div>
      <?php calculatorCardEnd(); ?>

      <?php calculatorCardStart(array('id' => 'fullAssignmentsCard')); ?>
        <h2>Αναλυτικές αναθέσεις</h2>
        <p class="cap" id="fullResultsStatus">Επίλεξε κλάδο / ειδικότητα για να εμφανιστεί η πλήρης λίστα μαθημάτων.</p>
        <div id="assignmentResults" aria-live="polite"></div>
      <?php calculatorCardEnd(); ?>

      <?php calculatorCardStart(); ?>
        <h2>Τι σημαίνουν οι αναθέσεις;</h2>
        <p><strong>Α΄ ανάθεση:</strong> με αυτά τα μαθήματα καλύπτεται το υποχρεωτικό ωράριο.</p>
        <p><strong>Β΄ ανάθεση:</strong> χρησιμοποιείται για συμπλήρωση υποχρεωτικού ωραρίου ή κάλυψη εκπαιδευτικών αναγκών. Οι ώρες Β΄ ανάθεσης της βασικής ειδικότητας, καθώς και Α΄/Β΄ δεύτερης ειδικότητας, δεν πρέπει κανονικά να ξεπερνούν τις 11 ώρες.</p>
        <p><strong>Γ΄ ανάθεση:</strong> αφορά τη βασική ειδικότητα και ενεργοποιείται <strong>μετά τις 30 Σεπτεμβρίου</strong>, με απόφαση ΠΥΣΔΕ, για κενά που παραμένουν ακάλυπτα.</p>
      <?php calculatorCardEnd(); ?>
    <?php calculatorMainEnd(); ?>

    <?php calculatorResultsStart(array('attrs' => array('aria-live' => 'polite'))); ?>
      <?php calculatorScoreHeader(array(
        'variant' => 'capped',
        'class' => 'total',
        'value_id' => 'resultCount',
        'value_html' => '—',
        'value_class' => 'num',
        'cap_html' => 'μαθήματα / αναθέσεις',
        'cap_class' => 'outof'
      )); ?>

      <?php calculatorResultRow(array('label_html' => 'Α΄ ανάθεση', 'value_html' => '0', 'value_id' => 'countA')); ?>
      <?php calculatorResultRow(array('label_html' => 'Β΄ ανάθεση', 'value_html' => '0', 'value_id' => 'countB')); ?>
      <?php calculatorResultRow(array('label_html' => 'Γ΄ ανάθεση', 'value_html' => '0', 'value_id' => 'countC')); ?>
      <?php calculatorResultRow(array('label_html' => 'Ειδικές προβλέψεις', 'value_html' => '0', 'value_id' => 'countSpecial')); ?>

      <?php calculatorResultMessage(array('variant' => 'status', 'id' => 'statusMessage', 'text' => 'Επίλεξε ειδικότητα για συνοπτικά αποτελέσματα.')); ?>
    <?php calculatorResultsEnd(); ?>
  <?php calculatorColumnsEnd(); ?>
</main>

<script>
(function(){
  'use strict';
  const DATA = <?php echo json_encode(teachingAssignmentsData(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>;
  const specialty = document.getElementById('specialty');
  const schoolGymnasio = document.getElementById('schoolGymnasio');
  const schoolGel = document.getElementById('schoolGel');
  const schoolEveningGym = document.getElementById('schoolEveningGym');
  const schoolEveningGel = document.getElementById('schoolEveningGel');
  const schoolEaeGym = document.getElementById('schoolEaeGym');
  const schoolEaeLykeio = document.getElementById('schoolEaeLykeio');
  const schoolEneegylGym = document.getElementById('schoolEneegylGym');
  const schoolEneegylLykeio = document.getElementById('schoolEneegylLykeio');
  const schoolKallitexnikoGym = document.getElementById('schoolKallitexnikoGym');
  const schoolEpal = document.getElementById('schoolEpal');
  const schoolEveningEpal = document.getElementById('schoolEveningEpal');
  const schoolAll = document.getElementById('schoolAll');
  const gradeFilter = document.getElementById('gradeFilter');
  const gradeWrap = document.getElementById('gradeWrap');
  const results = document.getElementById('assignmentResults');
  const status = document.getElementById('statusMessage');
  const count = document.getElementById('resultCount');
  const countA = document.getElementById('countA');
  const countB = document.getElementById('countB');
  const countC = document.getElementById('countC');
  const countSpecial = document.getElementById('countSpecial');
  const fullResultsStatus = document.getElementById('fullResultsStatus');

  const esc = (s) => String(s ?? '').replace(/[&<>'"]/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;',"'":'&#39;','"':'&quot;'}[c]));
  const normalize = (value) => String(value || '').trim().toUpperCase().replace(/\s+/g, '');

  function codeMatches(entry, code){
    const item = normalize(entry);
    if (item === code) return true;
    if (/^ΠΕ\d+$/.test(item) && code.indexOf(item + '.') === 0) return true;
    return false;
  }

  function matchesExact(list, code){
    return Array.isArray(list) && list.some(function(x){ return codeMatches(x, code); });
  }

  function noteFor(notes, code){
    if (!notes) return '';
    const keys = Object.keys(notes);
    for (let i = 0; i < keys.length; i++) {
      if (codeMatches(keys[i], code)) return notes[keys[i]] || '';
    }
    return '';
  }

  function assignmentFor(row, code){
    for (const level of ['A','B','C']) {
      if (matchesExact(row[level], code)) {
        return {level, note: noteFor(row[level + '_notes'], code)};
      }
    }
    if (row.A_all_pe === true && code.indexOf('ΠΕ') === 0) {
      return {level:'A', note: row.A_all_pe_note || 'όλοι οι κλάδοι-ειδικότητες Π.Ε.'};
    }
    if (row.B_all_others === true && !matchesExact(row.A || [], code)) {
      return {level:'B', note:'όλες οι άλλες ειδικότητες'};
    }
    if (row.special_all_pe === true && code.indexOf('ΠΕ') === 0) {
      return {level:'S', note: row.special_note || 'ειδική πρόβλεψη της απόφασης'};
    }
    return null;
  }

  function schoolLabel(row){
    if (row.school === 'gymnasio') return 'Γυμνάσιο';
    if (row.school === 'gel') return row.grade ? `${row.grade} ΓΕΛ` : 'ΓΕΛ';
    if (row.school === 'evening_gymnasio') return 'Εσπερινό Γυμνάσιο';
    if (row.school === 'evening_gel') return row.grade ? `${row.grade} Εσπερινού ΓΕΛ` : 'Εσπερινό ΓΕΛ';
    if (row.school === 'eae_gymnasio') return 'Γυμνάσιο Ε.Α.Ε.';
    if (row.school === 'eae_lykeio') return row.grade ? `${row.grade} Λύκειο Ε.Α.Ε.` : 'Λύκειο Ε.Α.Ε.';
    if (row.school === 'eneegyl_gymnasio') return 'Γυμνάσιο ΕΝ.Ε.Ε.ΓΥ.-Λ.';
    if (row.school === 'kallitexniko_gymnasio') return 'Καλλιτεχνικό Γυμνάσιο';
    if (row.school === 'epal') return row.grade ? `${row.grade} ΕΠΑ.Λ.` : 'ΕΠΑ.Λ.';
    if (row.school === 'evening_epal') return row.grade ? `${row.grade} Εσπερινού ΕΠΑ.Λ.` : 'Εσπερινό ΕΠΑ.Λ.';
    if (row.school === 'eneegyl_lykeio') {
      const shownGrade = (row.grades && row.grades.length)
        ? (gradeFilter.value !== 'all' && row.grades.includes(gradeFilter.value) ? gradeFilter.value : row.grades.join('/'))
        : row.grade;
      return shownGrade ? `${shownGrade} Λυκείου ΕΝ.Ε.Ε.ΓΥ.-Λ.` : 'Λύκειο ΕΝ.Ε.Ε.ΓΥ.-Λ.';
    }
    return row.school || '';
  }

  const schoolCheckboxes = [schoolGymnasio, schoolEveningGym, schoolGel, schoolEveningGel, schoolEpal, schoolEveningEpal, schoolEaeGym, schoolEaeLykeio, schoolEneegylGym, schoolEneegylLykeio, schoolKallitexnikoGym];

  function syncSchoolAll(){
    const checkedCount = schoolCheckboxes.filter(function(box){ return box.checked; }).length;
    schoolAll.checked = checkedCount === schoolCheckboxes.length;
    schoolAll.indeterminate = checkedCount > 0 && checkedCount < schoolCheckboxes.length;
  }

  function render(){
    syncSchoolAll();
    const code = normalize(specialty.value);
    const includeGymnasio = schoolGymnasio.checked;
    const includeGel = schoolGel.checked;
    const includeEveningGym = schoolEveningGym.checked;
    const includeEveningGel = schoolEveningGel.checked;
    const includeEaeGym = schoolEaeGym.checked;
    const includeEaeLykeio = schoolEaeLykeio.checked;
    const includeEneegylGym = schoolEneegylGym.checked;
    const includeEneegylLykeio = schoolEneegylLykeio.checked;
    const includeKallitexnikoGym = schoolKallitexnikoGym.checked;
    const includeEpal = schoolEpal.checked;
    const includeEveningEpal = schoolEveningEpal.checked;
    const grade = gradeFilter.value;
    gradeWrap.classList.toggle('hidden', !(includeGel || includeEveningGel || includeEaeLykeio || includeEneegylLykeio || includeEpal || includeEveningEpal));

    if (!code) {
      results.innerHTML = '';
      count.textContent = '—';
      countA.textContent = '0';
      countB.textContent = '0';
      countC.textContent = '0';
      countSpecial.textContent = '0';
      fullResultsStatus.textContent = 'Επίλεξε κλάδο / ειδικότητα για να εμφανιστεί η πλήρης λίστα μαθημάτων.';
      status.textContent = 'Επίλεξε ειδικότητα για συνοπτικά αποτελέσματα.';
      status.classList.remove('hidden');
      return;
    }

    const found = [];
    DATA.forEach(row => {
      if (row.school === 'gymnasio' && !includeGymnasio) return;
      if (row.school === 'gel' && !includeGel) return;
      if (row.school === 'evening_gymnasio' && !includeEveningGym) return;
      if (row.school === 'evening_gel' && !includeEveningGel) return;
      if (row.school === 'eae_gymnasio' && !includeEaeGym) return;
      if (row.school === 'eae_lykeio' && !includeEaeLykeio) return;
      if (row.school === 'eneegyl_gymnasio' && !includeEneegylGym) return;
      if (row.school === 'eneegyl_lykeio' && !includeEneegylLykeio) return;
      if (row.school === 'kallitexniko_gymnasio' && !includeKallitexnikoGym) return;
      if (row.school === 'epal' && !includeEpal) return;
      if (row.school === 'evening_epal' && !includeEveningEpal) return;
      if (row.school === 'gel' || row.school === 'evening_gel' || row.school === 'eae_lykeio' || row.school === 'eneegyl_lykeio' || row.school === 'epal' || row.school === 'evening_epal') {
        const rowGrades = Array.isArray(row.grades) ? row.grades : (row.grade ? [row.grade] : []);
        if (grade !== 'all' && !rowGrades.includes(grade)) return;
      }
      const hit = assignmentFor(row, code);
      if (hit) found.push({...row, assignment: hit.level, assignmentNote: hit.note});
    });

    count.textContent = String(found.length);
    if (!found.length) {
      results.innerHTML = '';
      count.textContent = '0';
      countA.textContent = '0';
      countB.textContent = '0';
      countC.textContent = '0';
      countSpecial.textContent = '0';
      fullResultsStatus.textContent = `Δεν βρέθηκε ανάθεση για ${code} με τα επιλεγμένα φίλτρα.`;
      status.textContent = `Δεν βρέθηκε ανάθεση για ${code} με τα επιλεγμένα φίλτρα.`;
      status.classList.remove('hidden');
      return;
    }

    const groups = {A:[], B:[], C:[], S:[]};
    found.forEach(row => groups[row.assignment].push(row));

    countA.textContent = String(groups.A.length);
    countB.textContent = String(groups.B.length);
    countC.textContent = String(groups.C.length);
    countSpecial.textContent = String(groups.S.length);
    status.textContent = `${code} · Α΄ ${groups.A.length} · Β΄ ${groups.B.length} · Γ΄ ${groups.C.length}${groups.S.length ? ' · Ειδικές ' + groups.S.length : ''}`;
    status.classList.remove('hidden');
    fullResultsStatus.textContent = `${code} · ${found.length} ${found.length === 1 ? 'καταχώριση' : 'καταχωρίσεις'} στα επιλεγμένα σχολεία.`;

    results.innerHTML = ['A','B','C','S'].map(level => {
      const label = level === 'A' ? 'Α΄ Ανάθεση' : level === 'B' ? 'Β΄ Ανάθεση' : level === 'C' ? 'Γ΄ Ανάθεση' : 'Ειδική πρόβλεψη';
      const rows = groups[level];
      if (!rows.length) return `
        <section>
          <h3>${label} <span class="pill">0</span></h3>
          <p class="cap">Δεν βρέθηκαν μαθήματα.</p>
        </section>`;

      return `
        <section>
          <h3>${label} <span class="pill">${rows.length}</span></h3>
          ${rows.map(row => {
            const context = [schoolLabel(row), row.section].filter(Boolean).join(' · ');
            const extra = [row.assignmentNote, row.note].filter(Boolean).join(' — ');
            return `<div class="result-row assignment-${level.toLowerCase()}">
              <span><strong>${esc(row.subject)}</strong><small>${esc(context)}${extra ? '<br>' + esc(extra) : ''}</small></span>
              <strong class="assignment-badge">${label.replace(' Ανάθεση','')}</strong>
            </div>`;
          }).join('')}
        </section>`;
    }).join('');
  }

  specialty.addEventListener('input', render);
  specialty.addEventListener('change', render);
  schoolGymnasio.addEventListener('change', render);
  schoolGel.addEventListener('change', render);
  schoolEveningGym.addEventListener('change', render);
  schoolEveningGel.addEventListener('change', render);
  schoolEaeGym.addEventListener('change', render);
  schoolEaeLykeio.addEventListener('change', render);
  schoolEneegylGym.addEventListener('change', render);
  schoolEneegylLykeio.addEventListener('change', render);
  schoolKallitexnikoGym.addEventListener('change', render);
  schoolEpal.addEventListener('change', render);
  schoolEveningEpal.addEventListener('change', render);
  schoolAll.addEventListener('change', function(){
    const target = schoolAll.checked;
    schoolCheckboxes.forEach(function(box){ box.checked = target; });
    render();
  });
  gradeFilter.addEventListener('change', render);
  render();
})();
</script>

<?php sourceCardStart(); ?>
  <p><strong>Γυμνάσιο / Εσπερινό Γυμνάσιο / ΓΕΛ / Εσπερινό ΓΕΛ:</strong> Υ.Α. 54058/Δ2/05-05-2026, ΦΕΚ Β΄ 2583/07-05-2026. Η απόφαση έχει ενιαίο τίτλο «Αναθέσεις μαθημάτων Γυμνασίου και Γενικού Λυκείου» και δεν δημοσιεύει χωριστό πίνακα αναθέσεων για τα εσπερινά, γι’ αυτό στο εργαλείο τα εσπερινά χρησιμοποιούν τον αντίστοιχο πίνακα Γυμνασίου/ΓΕΛ. <strong>Γυμνάσια / Λύκεια Ε.Α.Ε.:</strong> Υ.Α. 72559/Δ3, ΦΕΚ Β΄ 3275/11-06-2026. <strong>ΕΝ.Ε.Ε.ΓΥ.-Λ.:</strong> Υ.Α. 69785/Δ3/29-05-2026, ΦΕΚ Β΄ 3216/05-06-2026. Έχει ενσωματωθεί <strong>ολόκληρος ο πίνακας ΕΝ.Ε.Ε.ΓΥ.-Λ.</strong>: Γυμνάσιο, Α΄ τάξη Λυκείου, Γενική Παιδεία Β΄/Γ΄/Δ΄, οι 8 κοινοί Τομείς Β΄–Γ΄ και οι 33 ειδικότητες της Δ΄ τάξης, μαζί με τις κατά προτεραιότητα αναθέσεις και τις ειδικές προϋποθέσεις. Οι αποφάσεις ισχύουν για το σχολικό έτος 2026-2027 και περιλαμβάνουν το μάθημα <strong>Ηθική</strong>. <strong>ΕΠΑ.Λ. / Εσπερινά ΕΠΑ.Λ.:</strong> Υ.Α. Φ22/75401/Δ4/10-05-2018, ΦΕΚ Β΄ 1664/15-05-2018, όπως τροποποιήθηκε και ισχύει. Στην παρούσα φάση έχουν ενσωματωθεί η Α΄ τάξη, τα Γενικής Παιδείας Β΄/Γ΄ και οι Τομείς Γεωπονίας, Τροφίμων και Περιβάλλοντος και Διοίκησης και Οικονομίας της Β΄ τάξης. Η ίδια δέσμη αναθέσεων εφαρμόζεται και στο Εσπερινό ΕΠΑ.Λ. <strong>Καλλιτεχνικά Σχολεία:</strong> Υ.Α. 65409/Δ2/12-06-2024, ΦΕΚ Β΄ 3418/13-06-2024 (ΑΔΑ 99ΓΦ46ΝΚΠΔ-9Γ1), η οποία τροποποιεί και διαμορφώνει τον ισχύοντα πίνακα της Υ.Α. 148262/Δ2/10-09-2018 (ΦΕΚ Β΄ 4077). Στην παρούσα φάση έχουν ενσωματωθεί οι αναθέσεις του <strong>Καλλιτεχνικού Γυμνασίου για τους κλάδους ΠΕ/ΤΕ</strong>, με τις ειδικεύσεις και τις συνδιδασκαλίες που ορίζει η απόφαση· οι ειδικοί πίνακες Θεάτρου–Κινηματογράφου και Χορού θα προστεθούν χωριστά.</p>
  <?php sourceCardLinksStart(); ?>
    <?php sourceCardLink('https://www.minedu.gov.gr/protovathmia-defterovathmia/dioikitika-themata-geniko-lykeio', 'ΥΠΑΙΘΑ — Αναθέσεις Γυμνασίου / ΓΕΛ ↗'); ?>
    <?php sourceCardLink('https://www.minedu.gov.gr/protovathmia-defterovathmia/anatheseis-mathimaton---eidiki-kai-entaksiaki-ekpaidefsi', 'ΥΠΑΙΘΑ — Αναθέσεις Ειδικής & Ενταξιακής Εκπαίδευσης ↗'); ?>
    <?php sourceCardLink('https://www.minedu.gov.gr/images/joomlart/PDFs/PHEK_3275_B_2026_ANATHESEIS%20EAE_GYMN_LYK_2026-2027.pdf', 'ΦΕΚ Β΄ 3275/2026 — Ε.Α.Ε. ↗'); ?>
    <?php sourceCardLink('https://www.minedu.gov.gr/images/joomlart/PDFs/PHEK_3216_B_2026_ANATHESEIS%20ENEEGYL_2026-2027.pdf', 'ΦΕΚ Β΄ 3216/2026 — ΕΝ.Ε.Ε.ΓΥ.-Λ. ↗'); ?>
    <?php sourceCardLink('https://www.minedu.gov.gr/panelladikes-eksetaseis-pistopoiitika/gel-mixanografiko?catid=1524&id=35699%3Athesmiko-plaisio-leitourgias-epal-sp-299&view=article', 'ΥΠΑΙΘΑ — Θεσμικό πλαίσιο ΕΠΑ.Λ. / Αναθέσεις ↗'); ?>
    <?php sourceCardLink('https://gsvetlly.minedu.gov.gr/publications/mathiteia/thesmiko/%CE%91%CE%9D%CE%91%CE%98%CE%95%CE%A3%CE%95%CE%99%CE%A3_%CE%9C%CE%91%CE%98%CE%97%CE%9C%CE%91%CE%A4%CE%A9%CE%9D_%CE%95%CE%A0%CE%91%CE%9B_%CE%A6%CE%95%CE%9A_1664-4-15-5-18.pdf', 'ΦΕΚ Β΄ 1664/2018 — Αναθέσεις ΕΠΑ.Λ. ↗'); ?>
    <?php sourceCardLink('https://www.minedu.gov.gr/defterovathmia/kallitexnika', 'ΥΠΑΙΘΑ — Καλλιτεχνικά Σχολεία ↗'); ?>
    <?php sourceCardLink('https://diavgeia.gov.gr/doc/99%CE%93%CE%A646%CE%9D%CE%9A%CE%A0%CE%94-9%CE%931?inline=true', 'Υ.Α. 65409/Δ2/2024 — ΑΔΑ 99ΓΦ46ΝΚΠΔ-9Γ1 ↗'); ?>
  <?php sourceCardLinksEnd(); ?>
<?php sourceCardEnd(); ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
<script src="<?php echo htmlspecialchars(edu_asset_url('assets/common.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
</body>
</html>
