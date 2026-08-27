<?php require_once __DIR__ . '/includes/config.php'; ?>
<?php require_once __DIR__ . '/includes/teaching-assignments-data.php'; ?>
<?php require_once __DIR__ . '/includes/teacher-specialties.php'; ?>
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
    'badges' => array('2026–2027', 'Ημερήσια & Εσπερινά', 'ΕΠΑΛ', 'Ε.Α.Ε.', 'ΕΝ.Ε.Ε.ΓΥ.-Λ.', 'Α΄ · Β΄ · Γ΄ ανάθεση')
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
                <option value="<?php echo htmlspecialchars($code, ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars(teacherSpecialtyDisplay($code), ENT_QUOTES, 'UTF-8'); ?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>

        <div class="field">
          <label>Τύπος σχολείου</label>
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
                <label for="schoolEpal">ΕΠΑΛ <small>(Α΄ πλήρης + Β΄/Γ΄ Γενικής Παιδείας)</small></label>
              </div>
              <div class="checkrow">
                <input type="checkbox" id="schoolEveningEpal">
                <label for="schoolEveningEpal">Εσπερινό ΕΠΑΛ <small>(Α΄ πλήρης + Β΄/Γ΄ Γενικής Παιδείας)</small></label>
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
                <label for="schoolEneegylLykeio">Λύκειο ΕΝ.Ε.Ε.ΓΥ.-Λ. <small>(Α΄ + Β΄/Γ΄ + Δ΄ Γενικής Παιδείας + 8 κοινοί Τομείς Β΄–Γ΄)</small></label>
              </div>
            </div>
          </div>
          <p class="help"><strong>ΕΠΑΛ:</strong> έχει πλέον περαστεί <strong>ολόκληρη η Α΄ τάξη</strong> (Γενικής Παιδείας, Προσανατολισμού και Επιλογής) και τα <strong>Μαθήματα Γενικής Παιδείας της Β΄ και Γ΄ τάξης</strong>, για Ημερήσιο και Εσπερινό ΕΠΑΛ. Οι Τομείς της Β΄ και οι Ειδικότητες της Γ΄ θα προστεθούν χωριστά.</p>
          <p class="help"><strong>ΕΝ.Ε.Ε.ΓΥ.-Λ.:</strong> έχουν περαστεί το Γυμνάσιο, η <strong>Α΄ τάξη Λυκείου</strong> (Γενική Παιδεία, Προσανατολισμός και Επιλογής) και, στο παρόν ελεγχόμενο βήμα, <strong>τα Μαθήματα Γενικής Παιδείας της Β΄, Γ΄ και Δ΄ τάξης και μόνο οι οκτώ πρώτοι κοινοί Τομείς Β΄–Γ΄: Γεωπονίας, Τροφίμων και Περιβάλλοντος, Διοίκησης και Οικονομίας, Δομικών Έργων, Δομημένου Περιβάλλοντος και Αρχιτεκτονικού Σχεδιασμού, Εφαρμοσμένων Τεχνών, Ηλεκτρολογίας, Ηλεκτρονικής και Αυτοματισμού, Μηχανολογίας, Πληροφορικής και Υγείας – Πρόνοιας – Ευεξίας</strong>. Οι υπόλοιποι κοινοί τομείς Β΄–Γ΄ και οι πίνακες ειδικοτήτων της Δ΄ τάξης θα προστεθούν χωριστά.</p>
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
  const schoolEpal = document.getElementById('schoolEpal');
  const schoolEveningEpal = document.getElementById('schoolEveningEpal');
  const schoolEaeGym = document.getElementById('schoolEaeGym');
  const schoolEaeLykeio = document.getElementById('schoolEaeLykeio');
  const schoolEneegylGym = document.getElementById('schoolEneegylGym');
  const schoolEneegylLykeio = document.getElementById('schoolEneegylLykeio');
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
    if (row.school === 'epal') return row.grade ? `${row.grade} ΕΠΑΛ` : 'ΕΠΑΛ';
    if (row.school === 'evening_epal') return row.grade ? `${row.grade} Εσπερινού ΕΠΑΛ` : 'Εσπερινό ΕΠΑΛ';
    if (row.school === 'eae_gymnasio') return 'Γυμνάσιο Ε.Α.Ε.';
    if (row.school === 'eae_lykeio') return row.grade ? `${row.grade} Λύκειο Ε.Α.Ε.` : 'Λύκειο Ε.Α.Ε.';
    if (row.school === 'eneegyl_gymnasio') return 'Γυμνάσιο ΕΝ.Ε.Ε.ΓΥ.-Λ.';
    if (row.school === 'eneegyl_lykeio') {
      const shownGrade = (row.grades && row.grades.length)
        ? (gradeFilter.value !== 'all' && row.grades.includes(gradeFilter.value) ? gradeFilter.value : row.grades.join('/'))
        : row.grade;
      return shownGrade ? `${shownGrade} Λυκείου ΕΝ.Ε.Ε.ΓΥ.-Λ.` : 'Λύκειο ΕΝ.Ε.Ε.ΓΥ.-Λ.';
    }
    return row.school || '';
  }

  function render(){
    const code = normalize(specialty.value);
    const includeGymnasio = schoolGymnasio.checked;
    const includeGel = schoolGel.checked;
    const includeEveningGym = schoolEveningGym.checked;
    const includeEveningGel = schoolEveningGel.checked;
    const includeEpal = schoolEpal.checked;
    const includeEveningEpal = schoolEveningEpal.checked;
    const includeEaeGym = schoolEaeGym.checked;
    const includeEaeLykeio = schoolEaeLykeio.checked;
    const includeEneegylGym = schoolEneegylGym.checked;
    const includeEneegylLykeio = schoolEneegylLykeio.checked;
    const grade = gradeFilter.value;
    gradeWrap.classList.toggle('hidden', !(includeGel || includeEveningGel || includeEpal || includeEveningEpal || includeEaeLykeio || includeEneegylLykeio));

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
      if (row.school === 'epal' && !includeEpal) return;
      if (row.school === 'evening_epal' && !includeEveningEpal) return;
      if (row.school === 'eae_gymnasio' && !includeEaeGym) return;
      if (row.school === 'eae_lykeio' && !includeEaeLykeio) return;
      if (row.school === 'eneegyl_gymnasio' && !includeEneegylGym) return;
      if (row.school === 'eneegyl_lykeio' && !includeEneegylLykeio) return;
      if (row.school === 'gel' || row.school === 'evening_gel' || row.school === 'epal' || row.school === 'evening_epal' || row.school === 'eae_lykeio' || row.school === 'eneegyl_lykeio') {
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
  schoolEpal.addEventListener('change', render);
  schoolEveningEpal.addEventListener('change', render);
  schoolEaeGym.addEventListener('change', render);
  schoolEaeLykeio.addEventListener('change', render);
  schoolEneegylGym.addEventListener('change', render);
  schoolEneegylLykeio.addEventListener('change', render);
  gradeFilter.addEventListener('change', render);
  render();
})();
</script>

<?php sourceCardStart(); ?>
  <p><strong>ΕΠΑΛ / Εσπερινό ΕΠΑΛ:</strong> Υ.Α. Φ22/75401/Δ4/10-05-2018, ΦΕΚ Β΄ 1664/15-05-2018, όπως ισχύει. Για την Α΄ τάξη έχει ενσωματωθεί η τροποποίηση Φ22/40504/Δ4/10-04-2025, ΦΕΚ Β΄ 1975/23-04-2025 για την Ιστορία, και για Α΄/Β΄ η Φ22/55785/Δ4/07-05-2026, ΦΕΚ Β΄ 2625/11-05-2026 που προσθέτει την <strong>Ηθική</strong> και αναδιατυπώνει τους ισχύοντες πίνακες Γενικής Παιδείας. Στην παρούσα φάση έχει ενσωματωθεί <strong>ολόκληρη η Α΄ τάξη</strong> (Γενικής Παιδείας, Προσανατολισμού και Επιλογής) και τα Μαθήματα Γενικής Παιδείας της Β΄ και Γ΄. </p>
  <p><strong>Γυμνάσιο / Εσπερινό Γυμνάσιο / ΓΕΛ / Εσπερινό ΓΕΛ:</strong> Υ.Α. 54058/Δ2/05-05-2026, ΦΕΚ Β΄ 2583/07-05-2026. Η απόφαση έχει ενιαίο τίτλο «Αναθέσεις μαθημάτων Γυμνασίου και Γενικού Λυκείου» και δεν δημοσιεύει χωριστό πίνακα αναθέσεων για τα εσπερινά, γι’ αυτό στο εργαλείο τα εσπερινά χρησιμοποιούν τον αντίστοιχο πίνακα Γυμνασίου/ΓΕΛ. <strong>Γυμνάσια / Λύκεια Ε.Α.Ε.:</strong> Υ.Α. 72559/Δ3, ΦΕΚ Β΄ 3275/11-06-2026. <strong>ΕΝ.Ε.Ε.ΓΥ.-Λ.:</strong> Υ.Α. 69785/Δ3/29-05-2026, ΦΕΚ Β΄ 3216/05-06-2026. Στην παρούσα ελεγχόμενη φάση έχουν ενσωματωθεί ο πίνακας του Γυμνασίου, η Α΄ τάξη του Λυκείου ΕΝ.Ε.Ε.ΓΥ.-Λ. (Γενική Παιδεία, Προσανατολισμού και Επιλογής) και τα Μαθήματα Γενικής Παιδείας της Β΄, Γ΄ και Δ΄ τάξης, μαζί με τους οκτώ πρώτους ελεγχόμενους κοινούς τομείς Β΄–Γ΄: <strong>Γεωπονίας, Τροφίμων και Περιβάλλοντος</strong>, <strong>Διοίκησης και Οικονομίας</strong>, <strong>Δομικών Έργων, Δομημένου Περιβάλλοντος και Αρχιτεκτονικού Σχεδιασμού</strong>, <strong>Εφαρμοσμένων Τεχνών</strong>, <strong>Ηλεκτρολογίας, Ηλεκτρονικής και Αυτοματισμού</strong>, <strong>Μηχανολογίας</strong>, <strong>Πληροφορικής</strong> και <strong>Υγείας – Πρόνοιας – Ευεξίας</strong>. Οι αποφάσεις ισχύουν για το σχολικό έτος 2026-2027 και περιλαμβάνουν το μάθημα <strong>Ηθική</strong>.</p>
  <p><strong>Έλεγχος δεδομένων:</strong> Ο πίνακας Γυμνασίου και οι πίνακες Α΄, Β΄ και Γ΄ ΓΕΛ έχουν ελεγχθεί γραμμή-γραμμή απέναντι στο ΦΕΚ Β΄ 2583/2026.</p>
  <?php sourceCardLinksStart(); ?>
    <?php sourceCardLink('https://www.minedu.gov.gr/protovathmia-defterovathmia/lykeio-draseis?id=1524&view=category', 'ΥΠΑΙΘΑ — Θεσμικό πλαίσιο ΕΠΑΛ / Αναθέσεις ↗'); ?>
    <?php sourceCardLink('https://www.minedu.gov.gr/publications/docs2018/EPAL_FEK_1664%CE%92_15-05-2018.pdf', 'Υ.Α. Φ22/75401/Δ4 — ΦΕΚ Β΄ 1664/15-05-2018 — Βασικές αναθέσεις ΕΠΑΛ ↗'); ?>
    <?php sourceCardLink('https://www.minedu.gov.gr/publications/docs2018/FEK_2637B.pdf', 'Υ.Α. Φ22/107970/Δ4 — ΦΕΚ Β΄ 2637/05-07-2018 — Ναυτιλιακές Γνώσεις Α΄ ΕΠΑΛ ↗'); ?>
    <?php sourceCardLink('https://www.minedu.gov.gr/publications/docs2023/2025_04_10_%CE%95%CE%9E%CE%95_40504_%CF%84%CF%81%CE%BF%CF%80%CE%BF%CF%80_%CE%A5%CE%91_%CE%91%CE%BD%CE%B1%CE%B8%CE%AD%CF%83%CE%B5%CE%B9%CF%82_%CE%BC%CE%B1%CE%B8%CE%AE%CE%BC_%CE%99%CE%A3%CE%A4%CE%9F%CE%A1%CE%99%CE%91_%CE%91_%CF%84%CE%AC%CE%BE%CE%B7%CF%82_%CE%95%CE%A0%CE%91%CE%9B_%CE%A6%CE%95%CE%9A_1975%CE%92_23.04.2025.pdf', 'Υ.Α. Φ22/40504/Δ4 — ΦΕΚ Β΄ 1975/23-04-2025 — Ιστορία Α΄ ΕΠΑΛ ↗'); ?>
    <?php sourceCardLink('https://www.mydocman.gr/f22-75401-d4-2018', 'Φ22/75401/Δ4/2018 όπως τροποποιήθηκε — περιλαμβάνει ΦΕΚ Β΄ 2625/2026 (Ηθική) ↗'); ?>
    <?php sourceCardLink('https://ia37rg02wpsa01.blob.core.windows.net/fek/02/2026/20260202583.pdf', 'Υ.Α. 54058/Δ2 — ΦΕΚ Β΄ 2583/07-05-2026 — Αναθέσεις Γυμνασίου & ΓΕΛ ↗'); ?>
    <?php sourceCardLink('https://www.mydocman.gr/54058-d2-2026', '54058/Δ2/2026 — καταχώριση απόφασης / εύκολη αναφορά ↗'); ?>
    <?php sourceCardLink('https://www.minedu.gov.gr/protovathmia-defterovathmia/dioikitika-themata-geniko-lykeio', 'ΥΠΑΙΘΑ — Θεσμικό αρχείο αναθέσεων Γυμνασίου / ΓΕΛ ↗'); ?>
    <?php sourceCardLink('https://www.minedu.gov.gr/protovathmia-defterovathmia/anatheseis-mathimaton---eidiki-kai-entaksiaki-ekpaidefsi', 'ΥΠΑΙΘΑ — Αναθέσεις Ειδικής & Ενταξιακής Εκπαίδευσης ↗'); ?>
    <?php sourceCardLink('https://www.minedu.gov.gr/images/joomlart/PDFs/PHEK_3275_B_2026_ANATHESEIS%20EAE_GYMN_LYK_2026-2027.pdf', 'Υ.Α. 72559/Δ3 — ΦΕΚ Β΄ 3275/11-06-2026 — Αναθέσεις Γυμνασίων & Λυκείων Ε.Α.Ε. ↗'); ?>
    <?php sourceCardLink('https://www.minedu.gov.gr/images/joomlart/PDFs/PHEK_3216_B_2026_ANATHESEIS%20ENEEGYL_2026-2027.pdf', 'ΦΕΚ Β΄ 3216/2026 — ΕΝ.Ε.Ε.ΓΥ.-Λ. ↗'); ?>
  <?php sourceCardLinksEnd(); ?>
<?php sourceCardEnd(); ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
<script src="<?php echo htmlspecialchars(edu_asset_url('assets/common.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
</body>
</html>
