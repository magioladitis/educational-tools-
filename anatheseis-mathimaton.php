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
    'badges' => array('2026–2027', 'Ημερήσια & Εσπερινά', 'Ε.Α.Ε.', 'Α΄ · Β΄ · Γ΄ ανάθεση')
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
            </div>
          </div>
        </div>

        <div class="field" id="gradeWrap">
          <label for="gradeFilter">Τάξη Λυκείου</label>
          <select id="gradeFilter">
            <option value="all">Όλες οι τάξεις</option>
            <option value="Α΄">Α΄ τάξη</option>
            <option value="Β΄">Β΄ τάξη</option>
            <option value="Γ΄">Γ΄ τάξη</option>
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
  const gradeFilter = document.getElementById('gradeFilter');
  const gradeWrap = document.getElementById('gradeWrap');
  const results = document.getElementById('assignmentResults');
  const status = document.getElementById('statusMessage');
  const count = document.getElementById('resultCount');
  const countA = document.getElementById('countA');
  const countB = document.getElementById('countB');
  const countC = document.getElementById('countC');
  const fullResultsStatus = document.getElementById('fullResultsStatus');

  const esc = (s) => String(s ?? '').replace(/[&<>'"]/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;',"'":'&#39;','"':'&quot;'}[c]));
  const normalize = (value) => String(value || '').trim().toUpperCase().replace(/\s+/g, '');

  function matchesExact(list, code){
    return Array.isArray(list) && list.some(x => normalize(x) === code);
  }

  function assignmentFor(row, code){
    for (const level of ['A','B','C']) {
      if (matchesExact(row[level], code)) {
        const notes = row[level + '_notes'] || {};
        return {level, note: notes[code] || notes[Object.keys(notes).find(k => normalize(k) === code)] || ''};
      }
    }
    if (row.B_all_others === true) {
      const a = (row.A || []).map(normalize);
      if (!a.includes(code)) return {level:'B', note:'όλες οι άλλες ειδικότητες'};
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
    return row.school || '';
  }

  function render(){
    const code = normalize(specialty.value);
    const includeGymnasio = schoolGymnasio.checked;
    const includeGel = schoolGel.checked;
    const includeEveningGym = schoolEveningGym.checked;
    const includeEveningGel = schoolEveningGel.checked;
    const includeEaeGym = schoolEaeGym.checked;
    const includeEaeLykeio = schoolEaeLykeio.checked;
    const grade = gradeFilter.value;
    gradeWrap.classList.toggle('hidden', !(includeGel || includeEveningGel || includeEaeLykeio));

    if (!code) {
      results.innerHTML = '';
      count.textContent = '—';
      countA.textContent = '0';
      countB.textContent = '0';
      countC.textContent = '0';
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
      if ((row.school === 'gel' || row.school === 'evening_gel' || row.school === 'eae_lykeio') && grade !== 'all' && row.grade !== grade) return;
      const hit = assignmentFor(row, code);
      if (hit) found.push({...row, assignment: hit.level, assignmentNote: hit.note});
    });

    count.textContent = String(found.length);
    if (!found.length) {
      results.innerHTML = '';
      countA.textContent = '0';
      countB.textContent = '0';
      countC.textContent = '0';
      fullResultsStatus.textContent = `Δεν βρέθηκε ανάθεση για ${code} με τα επιλεγμένα φίλτρα.`;
      status.textContent = `Δεν βρέθηκε ανάθεση για ${code} με τα επιλεγμένα φίλτρα.`;
      status.classList.remove('hidden');
      return;
    }

    const groups = {A:[], B:[], C:[]};
    found.forEach(row => groups[row.assignment].push(row));

    countA.textContent = String(groups.A.length);
    countB.textContent = String(groups.B.length);
    countC.textContent = String(groups.C.length);
    status.textContent = `${code} · Α΄ ${groups.A.length} · Β΄ ${groups.B.length} · Γ΄ ${groups.C.length}`;
    status.classList.remove('hidden');
    fullResultsStatus.textContent = `${code} · ${found.length} ${found.length === 1 ? 'καταχώριση' : 'καταχωρίσεις'} στα επιλεγμένα σχολεία.`;

    results.innerHTML = ['A','B','C'].map(level => {
      const label = level === 'A' ? 'Α΄ Ανάθεση' : level === 'B' ? 'Β΄ Ανάθεση' : 'Γ΄ Ανάθεση';
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
  gradeFilter.addEventListener('change', render);
  render();
})();
</script>

<?php sourceCardStart(); ?>
  <p><strong>Γυμνάσιο / Εσπερινό Γυμνάσιο / ΓΕΛ / Εσπερινό ΓΕΛ:</strong> Υ.Α. 54058/Δ2/05-05-2026, ΦΕΚ Β΄ 2583/07-05-2026. Η απόφαση έχει ενιαίο τίτλο «Αναθέσεις μαθημάτων Γυμνασίου και Γενικού Λυκείου» και δεν δημοσιεύει χωριστό πίνακα αναθέσεων για τα εσπερινά, γι’ αυτό στο εργαλείο τα εσπερινά χρησιμοποιούν τον αντίστοιχο πίνακα Γυμνασίου/ΓΕΛ. <strong>Γυμνάσια / Λύκεια Ε.Α.Ε.:</strong> <strong>Γυμνάσια / Λύκεια Ε.Α.Ε.:</strong> Υ.Α. 72559/Δ3, ΦΕΚ Β΄ 3275/11-06-2026. Οι αποφάσεις ισχύουν για το σχολικό έτος 2026-2027 και περιλαμβάνουν το μάθημα <strong>Ηθική</strong>.</p>
  <?php sourceCardLinksStart(); ?>
    <?php sourceCardLink('https://www.minedu.gov.gr/protovathmia-defterovathmia/dioikitika-themata-geniko-lykeio', 'ΥΠΑΙΘΑ — Αναθέσεις Γυμνασίου / ΓΕΛ ↗'); ?>
    <?php sourceCardLink('https://www.minedu.gov.gr/protovathmia-defterovathmia/anatheseis-mathimaton---eidiki-kai-entaksiaki-ekpaidefsi', 'ΥΠΑΙΘΑ — Αναθέσεις Ειδικής & Ενταξιακής Εκπαίδευσης ↗'); ?>
    <?php sourceCardLink('https://www.minedu.gov.gr/images/joomlart/PDFs/PHEK_3275_B_2026_ANATHESEIS%20EAE_GYMN_LYK_2026-2027.pdf', 'ΦΕΚ Β΄ 3275/2026 — Ε.Α.Ε. ↗'); ?>
  <?php sourceCardLinksEnd(); ?>
<?php sourceCardEnd(); ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
<script src="<?php echo htmlspecialchars(edu_asset_url('assets/common.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
</body>
</html>
