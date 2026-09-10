/*
 * Browser UI controller for anatheseis-mathimaton.php.
 * PHP supplies the assignment dataset as neutral JSON; this file owns
 * filtering, conditional controls and result rendering.
 */
(function(){
  'use strict';
  const dataTemplate = document.getElementById('teachingAssignmentsData');
  const dataText = dataTemplate && dataTemplate.content ? dataTemplate.content.textContent : '[]';
  const DATA = JSON.parse(dataText || '[]');
  const specialty = document.getElementById('specialty');
  const schoolGymnasio = document.getElementById('schoolGymnasio');
  const schoolGel = document.getElementById('schoolGel');
  const schoolEcclesiasticalGym = document.getElementById('schoolEcclesiasticalGym');
  const schoolEcclesiasticalLykeio = document.getElementById('schoolEcclesiasticalLykeio');
  const schoolEveningGym = document.getElementById('schoolEveningGym');
  const schoolEveningGel = document.getElementById('schoolEveningGel');
  const schoolEaeGym = document.getElementById('schoolEaeGym');
  const schoolEaeLykeio = document.getElementById('schoolEaeLykeio');
  const schoolEneegylGym = document.getElementById('schoolEneegylGym');
  const schoolEneegylLykeio = document.getElementById('schoolEneegylLykeio');
  const schoolEeeek = document.getElementById('schoolEeeek');
  const schoolKallitexnikoGym = document.getElementById('schoolKallitexnikoGym');
  const schoolKallitexnikoLykeio = document.getElementById('schoolKallitexnikoLykeio');
  const schoolMousikoGym = document.getElementById('schoolMousikoGym');
  const schoolMousikoLykeio = document.getElementById('schoolMousikoLykeio');
  const schoolEpal = document.getElementById('schoolEpal');
  const schoolEveningEpal = document.getElementById('schoolEveningEpal');
  const schoolPepal = document.getElementById('schoolPepal');
  const schoolAll = document.getElementById('schoolAll');
  const gradeFilter = document.getElementById('gradeFilter');
  const gradeWrap = document.getElementById('gradeWrap');
  const musicSpecializationWrap = document.getElementById('musicSpecializationWrap');
  const musicSpecialization = document.getElementById('musicSpecialization');
  const musicSpecializationRelationWrap = document.getElementById('musicSpecializationRelationWrap');
  const musicSpecializationRelation = document.getElementById('musicSpecializationRelation');
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
    if (item === 'ΤΕ' && /^ΤΕ\d/.test(code)) return true;
    if (/^ΤΕ\d+$/.test(item) && code.indexOf(item + '.') === 0) return true;
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

  function isSpecialHourlyTableChoice(code){
    return code.indexOf(normalize('Ειδικός πίνακας')) === 0;
  }

  function musicRuleMatches(row, level, code){
    const requirement = row[level + '_music_requirement'];
    const specializations = row[level + '_music_specializations'];
    const exemptCodes = row[level + '_music_rule_exempt_codes'];
    const excludedSpecializations = row[level + '_music_excluded_specializations'];
    const specializationRequired = row[level + '_music_specialization_required'] === true;

    if (Array.isArray(exemptCodes) && exemptCodes.some(function(x){ return codeMatches(x, code); })) return true;
    if (requirement && musicSpecializationRelation.value !== requirement) return false;
    if (specializationRequired && !musicSpecialization.value) return false;
    if (Array.isArray(specializations) && specializations.length && !specializations.includes(musicSpecialization.value)) return false;
    if (Array.isArray(excludedSpecializations) && excludedSpecializations.includes(musicSpecialization.value)) return false;
    return true;
  }

  function assignmentFor(row, code){
    for (const level of ['A','B','C']) {
      if (matchesExact(row[level], code) && musicRuleMatches(row, level, code)) {
        return {level, note: noteFor(row[level + '_notes'], code)};
      }
    }
    if (row.A_all_pe === true && code.indexOf('ΠΕ') === 0) {
      return {level:'A', note: row.A_all_pe_note || 'όλοι οι κλάδοι-ειδικότητες Π.Ε.'};
    }
    if (row.B_all_others === true
        && !isSpecialHourlyTableChoice(code)
        && !matchesExact(row.A || [], code)) {
      return {level:'B', note:'όλες οι άλλες ειδικότητες'};
    }
    if (matchesExact(row.special_codes, code)) {
      return {level:'S', note: noteFor(row.special_notes, code) || row.special_note || 'διαθεματική ανάθεση / ειδική πρόβλεψη της απόφασης'};
    }
    if (row.special_all_pe === true && code.indexOf('ΠΕ') === 0) {
      return {level:'S', note: row.special_note || 'ειδική πρόβλεψη της απόφασης'};
    }
    return null;
  }

  function schoolLabel(row){
    if (row.school === 'gymnasio') return 'Γυμνάσιο';
    if (row.school === 'gel') return row.grade ? `${row.grade} ΓΕΛ` : 'ΓΕΛ';
    if (row.school === 'esperino_gymnasio') return 'Εσπερινό Γυμνάσιο';
    if (row.school === 'protypo_ekklisiastiko_gymnasio') {
      const shownGrade = (row.grades && row.grades.length)
        ? (gradeFilter.value !== 'all' && row.grades.includes(gradeFilter.value) ? gradeFilter.value : row.grades.join('/'))
        : row.grade;
      return shownGrade ? `${shownGrade} Πρότυπου Εκκλησιαστικού Γυμνασίου` : 'Πρότυπο Εκκλησιαστικό Γυμνάσιο';
    }
    if (row.school === 'protypo_ekklisiastiko_lykeio') return row.grade ? `${row.grade} Πρότυπου Εκκλησιαστικού Λυκείου` : 'Πρότυπο Εκκλησιαστικό Λύκειο';
    if (row.school === 'esperino_gel') return row.grade ? `${row.grade} Εσπερινού ΓΕΛ` : 'Εσπερινό ΓΕΛ';
    if (row.school === 'eae_gymnasio') return 'Γυμνάσιο Ε.Α.Ε.';
    if (row.school === 'eae_lykeio') return row.grade ? `${row.grade} Λύκειο Ε.Α.Ε.` : 'Λύκειο Ε.Α.Ε.';
    if (row.school === 'eneegyl_gymnasio') return 'Γυμνάσιο ΕΝ.Ε.Ε.ΓΥ.-Λ.';
    if (row.school === 'eeeek') return 'Ε.Ε.Ε.ΕΚ.';
    if (row.school === 'kallitexniko_gymnasio') return 'Καλλιτεχνικό Γυμνάσιο';
    if (row.school === 'kallitexniko_gel') return row.grade ? `${row.grade} Καλλιτεχνικού Λυκείου` : 'Καλλιτεχνικό Λύκειο';
    if (row.school === 'mousiko_gymnasio') {
      const shownGrade = (row.grades && row.grades.length)
        ? (gradeFilter.value !== 'all' && row.grades.includes(gradeFilter.value) ? gradeFilter.value : row.grades.join('/'))
        : row.grade;
      return shownGrade ? `${shownGrade} Μουσικού Γυμνασίου` : 'Μουσικό Γυμνάσιο';
    }
    if (row.school === 'mousiko_gel') {
      const shownGrade = (row.grades && row.grades.length)
        ? (gradeFilter.value !== 'all' && row.grades.includes(gradeFilter.value) ? gradeFilter.value : row.grades.join('/'))
        : row.grade;
      return shownGrade ? `${shownGrade} Γενικού Μουσικού Λυκείου` : 'Γενικό Μουσικό Λύκειο';
    }
    if (row.school === 'epal') return row.grade ? `${row.grade} ΕΠΑ.Λ.` : 'ΕΠΑ.Λ.';
    if (row.school === 'esperino_epal') return row.grade ? `${row.grade} Εσπερινού ΕΠΑ.Λ.` : 'Εσπερινό ΕΠΑ.Λ.';
    if (row.school === 'pepal') return row.grade ? `${row.grade} Π.ΕΠΑ.Λ.` : 'Π.ΕΠΑ.Λ.';
    if (row.school === 'eneegyl_lykeio') {
      const shownGrade = (row.grades && row.grades.length)
        ? (gradeFilter.value !== 'all' && row.grades.includes(gradeFilter.value) ? gradeFilter.value : row.grades.join('/'))
        : row.grade;
      return shownGrade ? `${shownGrade} Λυκείου ΕΝ.Ε.Ε.ΓΥ.-Λ.` : 'Λύκειο ΕΝ.Ε.Ε.ΓΥ.-Λ.';
    }
    return row.school || '';
  }

  const schoolCheckboxes = [schoolGymnasio, schoolEveningGym, schoolGel, schoolEveningGel, schoolEcclesiasticalGym, schoolEcclesiasticalLykeio, schoolEpal, schoolEveningEpal, schoolPepal, schoolEaeGym, schoolEaeLykeio, schoolEneegylGym, schoolEneegylLykeio, schoolEeeek, schoolKallitexnikoGym, schoolKallitexnikoLykeio, schoolMousikoGym, schoolMousikoLykeio];

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
    const includeEcclesiasticalGym = schoolEcclesiasticalGym.checked;
    const includeEcclesiasticalLykeio = schoolEcclesiasticalLykeio.checked;
    const includeEaeGym = schoolEaeGym.checked;
    const includeEaeLykeio = schoolEaeLykeio.checked;
    const includeEneegylGym = schoolEneegylGym.checked;
    const includeEneegylLykeio = schoolEneegylLykeio.checked;
    const includeEeeek = schoolEeeek.checked;
    const includeKallitexnikoGym = schoolKallitexnikoGym.checked;
    const includeKallitexnikoLykeio = schoolKallitexnikoLykeio.checked;
    const includeEpal = schoolEpal.checked;
    const includeEveningEpal = schoolEveningEpal.checked;
    const includePepal = schoolPepal.checked;
    const includeMousikoGym = schoolMousikoGym.checked;
    const includeMousikoLykeio = schoolMousikoLykeio.checked;
    const grade = gradeFilter.value;
    gradeWrap.classList.toggle('hidden', !(includeGel || includeEveningGel || includeEaeLykeio || includeEneegylLykeio || includeEpal || includeEveningEpal || includePepal || includeKallitexnikoLykeio || includeMousikoGym || includeMousikoLykeio || includeEcclesiasticalGym || includeEcclesiasticalLykeio));
    const isMusicTeacher = code === 'ΠΕ79.01' || code === 'ΠΕ79.02' || code === 'ΤΕ16';
    const showMusicSpecialization = isMusicTeacher && (includeMousikoGym || includeMousikoLykeio);
    musicSpecializationWrap.classList.toggle('hidden', !showMusicSpecialization);
    const needsMusicRelation = showMusicSpecialization && (musicSpecialization.value === 'piano' || musicSpecialization.value === 'tambouras' || musicSpecialization.value === 'other_instrument');
    musicSpecializationRelationWrap.classList.toggle('hidden', !needsMusicRelation);

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
      if (row.school === 'esperino_gymnasio' && !includeEveningGym) return;
      if (row.school === 'esperino_gel' && !includeEveningGel) return;
      if (row.school === 'protypo_ekklisiastiko_gymnasio' && !includeEcclesiasticalGym) return;
      if (row.school === 'protypo_ekklisiastiko_lykeio' && !includeEcclesiasticalLykeio) return;
      if (row.school === 'eae_gymnasio' && !includeEaeGym) return;
      if (row.school === 'eae_lykeio' && !includeEaeLykeio) return;
      if (row.school === 'eneegyl_gymnasio' && !includeEneegylGym) return;
      if (row.school === 'eneegyl_lykeio' && !includeEneegylLykeio) return;
      if (row.school === 'eeeek' && !includeEeeek) return;
      if (row.school === 'kallitexniko_gymnasio' && !includeKallitexnikoGym) return;
      if (row.school === 'kallitexniko_gel' && !includeKallitexnikoLykeio) return;
      if (row.school === 'epal' && !includeEpal) return;
      if (row.school === 'esperino_epal' && !includeEveningEpal) return;
      if (row.school === 'pepal' && !includePepal) return;
      if (row.school === 'mousiko_gymnasio' && !includeMousikoGym) return;
      if (row.school === 'mousiko_gel' && !includeMousikoLykeio) return;
      if (row.school === 'gel' || row.school === 'esperino_gel' || row.school === 'eae_lykeio' || row.school === 'eneegyl_lykeio' || row.school === 'epal' || row.school === 'esperino_epal' || row.school === 'pepal' || row.school === 'kallitexniko_gel' || row.school === 'mousiko_gymnasio' || row.school === 'mousiko_gel' || row.school === 'protypo_ekklisiastiko_gymnasio' || row.school === 'protypo_ekklisiastiko_lykeio') {
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
    fullResultsStatus.textContent = `${code} · ${found.length} ${found.length === 1 ? 'αποτέλεσμα' : 'αποτελέσματα'} με τα επιλεγμένα φίλτρα.`;

    results.innerHTML = ['A','B','C','S'].map(level => {
      const label = level === 'A' ? 'Α΄ ανάθεση' : level === 'B' ? 'Β΄ ανάθεση' : level === 'C' ? 'Γ΄ ανάθεση' : 'Ειδική / διαθεματική ανάθεση';
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
  schoolEcclesiasticalGym.addEventListener('change', render);
  schoolEcclesiasticalLykeio.addEventListener('change', render);
  schoolEaeGym.addEventListener('change', render);
  schoolEaeLykeio.addEventListener('change', render);
  schoolEneegylGym.addEventListener('change', render);
  schoolEneegylLykeio.addEventListener('change', render);
  schoolEeeek.addEventListener('change', render);
  schoolKallitexnikoGym.addEventListener('change', render);
  schoolKallitexnikoLykeio.addEventListener('change', render);
  schoolMousikoGym.addEventListener('change', render);
  schoolMousikoLykeio.addEventListener('change', render);
  schoolEpal.addEventListener('change', render);
  schoolEveningEpal.addEventListener('change', render);
  schoolPepal.addEventListener('change', render);
  schoolAll.addEventListener('change', function(){
    const target = schoolAll.checked;
    schoolCheckboxes.forEach(function(box){ box.checked = target; });
    render();
  });
  gradeFilter.addEventListener('change', render);
  musicSpecialization.addEventListener('change', render);
  musicSpecializationRelation.addEventListener('change', render);
  render();
})();
