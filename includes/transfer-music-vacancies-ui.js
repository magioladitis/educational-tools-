(function () {
  'use strict';
  var root = document.getElementById('musicVacancies');
  var data = window.EducationTransferMusicVacancies;
  if (!root || !Array.isArray(data)) return;
  var branch = document.getElementById('musicVacancyBranch');
  var specialty = document.getElementById('musicVacancySpecialty');
  var search = document.getElementById('musicVacancySearch');
  var body = document.getElementById('musicVacancyRows');
  var summary = document.getElementById('musicVacancySummary');
  var empty = document.getElementById('musicVacancyEmpty');
  function normalize(value) {
    return String(value).normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLocaleLowerCase('el').replace(/ς/g, 'σ');
  }
  function options(select, values, label) {
    select.textContent = '';
    select.add(new Option(label, ''));
    Array.from(new Set(values.filter(Boolean))).sort(function (a, b) { return a.localeCompare(b, 'el'); }).forEach(function (value) {
      select.add(new Option(value, value));
    });
  }
  options(branch, data.map(function (r) { return r.branch; }), 'Όλοι οι κλάδοι');
  function updateSpecialties() {
    var previous = specialty.value;
    var values = data.filter(function (r) { return !branch.value || r.branch === branch.value; }).map(function (r) { return r.specialty; });
    options(specialty, values, 'Όλες οι ειδικεύσεις');
    if (values.indexOf(previous) !== -1) specialty.value = previous;
    specialty.disabled = !values.some(Boolean);
  }
  var indexed = data.map(function (r) { return {row: r, text: normalize(r.school + ' ' + r.directory)}; });
  function render() {
    var terms = normalize(search.value.trim()).split(/\s+/).filter(Boolean);
    var rows = indexed.filter(function (item) {
      var r = item.row;
      return (!branch.value || branch.value === r.branch) && (!specialty.value || specialty.value === r.specialty) && terms.every(function (term) { return item.text.indexOf(term) !== -1; });
    }).map(function (item) { return item.row; });
    var fragment = document.createDocumentFragment();
    rows.forEach(function (r) {
      var tr = document.createElement('tr');
      [r.school, r.directory, r.branch, r.specialty || '—', r.vacancies].forEach(function (value) {
        var td = document.createElement('td'); td.textContent = value; tr.appendChild(td);
      });
      fragment.appendChild(tr);
    });
    body.textContent = ''; body.appendChild(fragment);
    var count = rows.reduce(function (total, r) { return total + r.vacancies; }, 0);
    summary.textContent = count + ' οργανικά κενά · ' + new Set(rows.map(function (r) { return r.school; })).size + ' σχολεία · ' + rows.length + ' εγγραφές';
    empty.hidden = rows.length !== 0;
    root.querySelector('.music-vacancies__scroll').hidden = rows.length === 0;
  }
  function clear() { branch.value = ''; search.value = ''; specialty.value = ''; updateSpecialties(); render(); }
  branch.addEventListener('change', function () { updateSpecialties(); render(); });
  specialty.addEventListener('change', render);
  search.addEventListener('input', render);
  document.getElementById('musicVacancyClear').addEventListener('click', clear);
  var reset = document.getElementById('resetBtn');
  if (reset) reset.addEventListener('click', clear);
  updateSpecialties(); render();
}());
