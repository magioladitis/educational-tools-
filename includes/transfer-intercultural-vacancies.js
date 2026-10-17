/* Intercultural D.E. advertised organic vacancies — 2026–2027.
 * Source: ORGANIKA KENA DIAPOLITISMIKA DE 2026_2027 (1).xlsx (uploaded annual vacancy table).
 * The Primary Education (Π.Ε.) annex is a separate procedure and is not included.
 * Client-side static lookup. Specialty codes are normalized through the shared
 * specialty-code-normalization.js module. The display names come from the
 * shared teacher-specialties.php registry, never from this annual vacancy file.
 * Review/update positions annually; never claim live availability.
 */
(function(global) {
  'use strict';
  var rows = [{"branch":"ΠΕ01","school":"2ο ΓΕΝΙΚΟ ΛΥΚΕΙΟ ΔΙΑΠΟΛΙΤΙΣΜΙΚΗΣ ΕΚΠΑΙΔΕΥΣΗΣ ΕΛΛΗΝΙΚΟΥ","directorate":"ΔΙΕΥΘΥΝΣΗ Δ.Ε. ΑΘΗΝΑΣ Δ΄","vacancies":1},{"branch":"ΠΕ03","school":"2ο ΓΕΝΙΚΟ ΛΥΚΕΙΟ ΔΙΑΠΟΛΙΤΙΣΜΙΚΗΣ ΕΚΠΑΙΔΕΥΣΗΣ ΕΛΛΗΝΙΚΟΥ","directorate":"ΔΙΕΥΘΥΝΣΗ Δ.Ε. ΑΘΗΝΑΣ Δ΄","vacancies":1},{"branch":"ΠΕ05","school":"2ο ΓΕΝΙΚΟ ΛΥΚΕΙΟ ΔΙΑΠΟΛΙΤΙΣΜΙΚΗΣ ΕΚΠΑΙΔΕΥΣΗΣ ΕΛΛΗΝΙΚΟΥ","directorate":"ΔΙΕΥΘΥΝΣΗ Δ.Ε. ΑΘΗΝΑΣ Δ΄","vacancies":1},{"branch":"ΠΕ04.02","school":"2ο ΓΕΝΙΚΟ ΛΥΚΕΙΟ ΔΙΑΠΟΛΙΤΙΣΜΙΚΗΣ ΕΚΠΑΙΔΕΥΣΗΣ ΕΛΛΗΝΙΚΟΥ","directorate":"ΔΙΕΥΘΥΝΣΗ Δ.Ε. ΑΘΗΝΑΣ Δ΄","vacancies":1},{"branch":"ΠΕ04.04","school":"2ο ΓΕΝΙΚΟ ΛΥΚΕΙΟ ΔΙΑΠΟΛΙΤΙΣΜΙΚΗΣ ΕΚΠΑΙΔΕΥΣΗΣ ΕΛΛΗΝΙΚΟΥ","directorate":"ΔΙΕΥΘΥΝΣΗ Δ.Ε. ΑΘΗΝΑΣ Δ΄","vacancies":1},{"branch":"ΠΕ04.04","school":"2ο ΓΥΜΝΑΣΙΟ ΔΙΑΠΟΛΙΤΙΣΜΙΚΗΣ ΕΚΠΑΙΔΕΥΣΗΣ ΕΛΛΗΝΙΚΟΥ","directorate":"ΔΙΕΥΘΥΝΣΗ Δ.Ε. ΑΘΗΝΑΣ Δ΄","vacancies":1},{"branch":"ΠΕ88.04","school":"2ο ΓΥΜΝΑΣΙΟ ΔΙΑΠΟΛΙΤΙΣΜΙΚΗΣ ΕΚΠΑΙΔΕΥΣΗΣ ΕΛΛΗΝΙΚΟΥ","directorate":"ΔΙΕΥΘΥΝΣΗ Δ.Ε. ΑΘΗΝΑΣ Δ΄","vacancies":1},{"branch":"ΠΕ80","school":"ΓΕΝΙΚΟ ΛΥΚΕΙΟ ΔΙΑΠΟΛΙΤΙΣΜΙΚΗΣ ΕΚΠΑΙΔΕΥΣΗΣ ΕΥΟΣΜΟΥ","directorate":"ΔΙΕΥΘΥΝΣΗ Δ.Ε. Β΄ ΘΕΣΣΑΛΟΝΙΚΗΣ","vacancies":1},{"branch":"ΠΕ06","school":"ΓΕΝΙΚΟ ΛΥΚΕΙΟ ΔΙΑΠΟΛΙΤΙΣΜΙΚΗΣ ΕΚΠΑΙΔΕΥΣΗΣ ΣΑΠΩΝ","directorate":"ΔΙΕΥΘΥΝΣΗ Δ.Ε. ΡΟΔΟΠΗΣ","vacancies":1},{"branch":"ΠΕ11","school":"ΓΕΝΙΚΟ ΛΥΚΕΙΟ ΔΙΑΠΟΛΙΤΙΣΜΙΚΗΣ ΕΚΠΑΙΔΕΥΣΗΣ ΣΑΠΩΝ","directorate":"ΔΙΕΥΘΥΝΣΗ Δ.Ε. ΡΟΔΟΠΗΣ","vacancies":1},{"branch":"ΠΕ04.02","school":"ΓΕΝΙΚΟ ΛΥΚΕΙΟ ΔΙΑΠΟΛΙΤΙΣΜΙΚΗΣ ΕΚΠΑΙΔΕΥΣΗΣ ΣΑΠΩΝ","directorate":"ΔΙΕΥΘΥΝΣΗ Δ.Ε. ΡΟΔΟΠΗΣ","vacancies":1},{"branch":"ΠΕ84","school":"ΓΥΜΝΑΣΙΟ ΔΙΑΠΟΛΙΤΙΣΜΙΚΗΣ ΕΚΠΑΙΔΕΥΣΗ ΑΧΑΡΝΩΝ","directorate":"ΔΙΕΥΘΥΝΣΗ Δ.Ε. ΑΝΑΤΟΛΙΚΗΣ ΑΤΤΙΚΗΣ","vacancies":1},{"branch":"ΠΕ02","school":"ΓΥΜΝΑΣΙΟ ΔΙΑΠΟΛΙΤΙΣΜΙΚΗΣ ΕΚΠΑΙΔΕΥΣΗΣ Α ΑΘΗΝΑΣ","directorate":"ΔΙΕΥΘΥΝΣΗ Δ.Ε. ΑΘΗΝΑΣ Α΄","vacancies":1},{"branch":"ΠΕ06","school":"ΓΥΜΝΑΣΙΟ ΔΙΑΠΟΛΙΤΙΣΜΙΚΗΣ ΕΚΠΑΙΔΕΥΣΗΣ Α ΑΘΗΝΑΣ","directorate":"ΔΙΕΥΘΥΝΣΗ Δ.Ε. ΑΘΗΝΑΣ Α΄","vacancies":1},{"branch":"ΠΕ11","school":"ΓΥΜΝΑΣΙΟ ΔΙΑΠΟΛΙΤΙΣΜΙΚΗΣ ΕΚΠΑΙΔΕΥΣΗΣ Α ΑΘΗΝΑΣ","directorate":"ΔΙΕΥΘΥΝΣΗ Δ.Ε. ΑΘΗΝΑΣ Α΄","vacancies":1},{"branch":"ΠΕ03","school":"ΓΥΜΝΑΣΙΟ ΔΙΑΠΟΛΙΤΙΣΜΙΚΗΣ ΕΚΠΑΙΔΕΥΣΗΣ ΕΥΟΣΜΟΥ","directorate":"ΔΙΕΥΘΥΝΣΗ Δ.Ε. Β΄ ΘΕΣΣΑΛΟΝΙΚΗΣ","vacancies":2},{"branch":"ΠΕ06","school":"ΓΥΜΝΑΣΙΟ ΔΙΑΠΟΛΙΤΙΣΜΙΚΗΣ ΕΚΠΑΙΔΕΥΣΗΣ ΕΥΟΣΜΟΥ","directorate":"ΔΙΕΥΘΥΝΣΗ Δ.Ε. Β΄ ΘΕΣΣΑΛΟΝΙΚΗΣ","vacancies":1},{"branch":"ΠΕ11","school":"ΓΥΜΝΑΣΙΟ ΔΙΑΠΟΛΙΤΙΣΜΙΚΗΣ ΕΚΠΑΙΔΕΥΣΗΣ ΕΥΟΣΜΟΥ","directorate":"ΔΙΕΥΘΥΝΣΗ Δ.Ε. Β΄ ΘΕΣΣΑΛΟΝΙΚΗΣ","vacancies":1},{"branch":"ΠΕ78","school":"ΓΥΜΝΑΣΙΟ ΔΙΑΠΟΛΙΤΙΣΜΙΚΗΣ ΕΚΠΑΙΔΕΥΣΗΣ ΕΥΟΣΜΟΥ","directorate":"ΔΙΕΥΘΥΝΣΗ Δ.Ε. Β΄ ΘΕΣΣΑΛΟΝΙΚΗΣ","vacancies":1},{"branch":"ΠΕ82","school":"ΓΥΜΝΑΣΙΟ ΔΙΑΠΟΛΙΤΙΣΜΙΚΗΣ ΕΚΠΑΙΔΕΥΣΗΣ ΕΥΟΣΜΟΥ","directorate":"ΔΙΕΥΘΥΝΣΗ Δ.Ε. Β΄ ΘΕΣΣΑΛΟΝΙΚΗΣ","vacancies":1},{"branch":"ΠΕ82","school":"ΓΥΜΝΑΣΙΟ ΔΙΑΠΟΛΙΤΙΣΜΙΚΗΣ ΕΚΠΑΙΔΕΥΣΗΣ ΣΑΠΩΝ","directorate":"ΔΙΕΥΘΥΝΣΗ Δ.Ε. ΡΟΔΟΠΗΣ","vacancies":1},{"branch":"ΠΕ03","school":"ΚΑΡΟΥΤΕΙΟ ΓΥΜΝΑΣΙΟ ΔΙΑΠΟΛΙΤΙΣΜΙΚΗΣ ΕΚΠΑΙΔΕΥΣΗΣ ΠΕΝΤΑΛΟΦΟΥ ΜΕ ΛΥΚΕΙΑΚΕΣ ΤΑΞΕΙΣ","directorate":"ΔΙΕΥΘΥΝΣΗ Δ.Ε. ΚΟΖΑΝΗΣ","vacancies":1}];
  var specialtyCodes = global.EducationSpecialtyCodes;
  if (!specialtyCodes || typeof specialtyCodes.normalize !== 'function') {
    throw new Error('EducationSpecialtyCodes must be loaded before intercultural vacancies.');
  }
  function normalizeBranch(value) { return specialtyCodes.normalize(value); }
  var branches = {};
  rows.forEach(function(row) {
    row.branch = normalizeBranch(row.branch);
    if (!row.branch) throw new Error('Invalid specialty code in intercultural vacancy table.');
    if (!branches[row.branch]) branches[row.branch] = {code:row.branch, positions:0};
    branches[row.branch].positions += row.vacancies;
  });
  function availableBranches() {
    var labels = global.EducationTransferTeacherSpecialtyLabels || {};
    return Object.keys(branches).sort(function(a,b){return a.localeCompare(b,'el');}).map(function(code) {
      // Code-only fallback: never reintroduce a second, manually maintained label list.
      return {code:code, label:labels[code] || '', positions:branches[code].positions};
    });
  }
  function schoolsForBranch(branch) {
    var code = normalizeBranch(branch);
    return rows.filter(function(row){return row.branch===code && row.vacancies>0;});
  }
  function count(branch,school) {
    var code = normalizeBranch(branch);
    return rows.filter(function(row){return row.branch===code && row.school===school;})
      .reduce(function(n,row){return n+row.vacancies;},0);
  }
  global.EducationInterculturalVacancies=Object.freeze({
    year:'2026–2027',level:'ΔΕ',totalVacancies:23,
    rows:rows,availableBranches:availableBranches,schoolsForBranch:schoolsForBranch,
    hasBranch:function(branch){return !!branches[normalizeBranch(branch)];},count:count
  });
})(window);
