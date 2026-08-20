
(function(){
  "use strict";
  const $ = id => document.getElementById(id);
  const fmt = n => Number(n || 0).toLocaleString('el-GR', {maximumFractionDigits: 1});

  const SPECIALTY_LABELS = Object.freeze({
    "PE01":"ΠΕ01 — Θεολόγοι", "PE02":"ΠΕ02 — Φιλόλογοι", "PE03":"ΠΕ03 — Μαθηματικοί",
    "PE04.01":"ΠΕ04.01 — Φυσικοί", "PE04.02":"ΠΕ04.02 — Χημικοί", "PE04.03":"ΠΕ04.03 — Φυσιογνώστες",
    "PE04.04":"ΠΕ04.04 — Βιολόγοι", "PE04.05":"ΠΕ04.05 — Γεωλόγοι", "PE05":"ΠΕ05 — Γαλλικής",
    "PE06":"ΠΕ06 — Αγγλικής", "PE07":"ΠΕ07 — Γερμανικής", "PE08":"ΠΕ08 — Καλλιτεχνικών",
    "PE11":"ΠΕ11 — Φυσικής Αγωγής", "PE60":"ΠΕ60 — Νηπιαγωγοί", "PE70":"ΠΕ70 — Δάσκαλοι",
    "PE78":"ΠΕ78 — Κοινωνικών Επιστημών", "PE79.01":"ΠΕ79.01 — Μουσικής Επιστήμης",
    "PE80":"ΠΕ80 — Οικονομίας", "PE82":"ΠΕ82 — Μηχανολόγων", "PE83":"ΠΕ83 — Ηλεκτρολόγων",
    "PE85":"ΠΕ85 — Χημικών Μηχανικών", "PE86":"ΠΕ86 — Πληροφορικής", "PE88.04":"ΠΕ88.04 — Διατροφής"
  });

  // Παράρτημα ΙΙΙ — Πίνακας χωρών/ειδικοτήτων, πρόσκληση 11771/Η2/30-01-2026.
  const DESTINATIONS = Object.freeze([
    // Ασία
    {id:"az", name:"Αζερμπαϊτζάν", continent:"Ασία", specs:["PE70","PE02"]},
    {id:"am", name:"Αρμενία", continent:"Ασία", specs:["PE70","PE02"]},
    {id:"ge", name:"Γεωργία", continent:"Ασία", specs:["PE70","PE02","PE11","PE79.01"]},
    {id:"kz", name:"Καζακστάν", continent:"Ασία", specs:["PE70","PE02"]},
    {id:"uae", name:"Η.Α.Ε.", continent:"Ασία", specs:["PE70","PE02"]},
    {id:"jo", name:"Ιορδανία", continent:"Ασία", specs:["PE70","PE02"]},
    {id:"il", name:"Ισραήλ", continent:"Ασία", specs:["PE70","PE02","PE03","PE04.01","PE04.02","PE04.03","PE04.04","PE04.05","PE06","PE86"]},
    {id:"qa", name:"Κατάρ", continent:"Ασία", specs:["PE60","PE70","PE02"]},
    {id:"uz", name:"Ουζμπεκιστάν", continent:"Ασία", specs:["PE70"]},
    {id:"tr", name:"Τουρκία", continent:"Ασία", specs:["PE02","PE03","PE04.01","PE04.02","PE04.04","PE06","PE11","PE79.01","PE60","PE70","PE86"]},

    // Αφρική
    {id:"eg", name:"Αίγυπτος", continent:"Αφρική", specs:["PE01","PE02","PE03","PE04.01","PE04.02","PE04.03","PE04.04","PE06","PE11","PE60","PE70","PE78","PE79.01","PE86"]},
    {id:"et", name:"Αιθιοπία", continent:"Αφρική", specs:["PE70"]},
    {id:"zm", name:"Ζάμπια", continent:"Αφρική", specs:["PE70"]},
    {id:"zw", name:"Ζιμπάμπουε", continent:"Αφρική", specs:["PE70"]},
    {id:"cd", name:"Λ. Δ. Κονγκό", continent:"Αφρική", specs:["PE60","PE70","PE02","PE03","PE04.01","PE04.02","PE86","PE06","PE05","PE11","PE80"]},
    {id:"mg", name:"Μαδαγασκάρη", continent:"Αφρική", specs:["PE70","PE02"]},
    {id:"za", name:"Νότια Αφρική", continent:"Αφρική", specs:["PE60","PE70","PE02","PE79.01","PE06","PE86","PE11"]},
    {id:"tn", name:"Τυνησία", continent:"Αφρική", specs:["PE70","PE02"]},

    // Ωκεανία
    {id:"au", name:"Αυστραλία", continent:"Ωκεανία", specs:["PE02","PE06","PE60","PE70"]},
    {id:"nz", name:"Νέα Ζηλανδία", continent:"Ωκεανία", specs:["PE70"]},

    // Ευρώπη
    {id:"al", name:"Αλβανία", continent:"Ευρώπη", specs:["PE60","PE70","PE02","PE04.04","PE08","PE11","PE79.01","PE83","PE86","PE06"]},
    {id:"at", name:"Αυστρία", continent:"Ευρώπη", specs:["PE60","PE70","PE02"]},
    {id:"be", name:"Βέλγιο", continent:"Ευρώπη", specs:["PE60","PE70","PE01","PE02","PE03","PE04.01","PE04.02","PE04.04","PE05","PE06","PE08","PE11","PE78","PE79.01","PE80","PE86"]},
    {id:"bg", name:"Βουλγαρία", continent:"Ευρώπη", specs:["PE60","PE70","PE02","PE11"]},
    {id:"fr", name:"Γαλλία", continent:"Ευρώπη", specs:["PE60","PE70","PE02"]},
    {id:"de_du", name:"Γερμανία — Σ.Γ.Ε. Ντίσελντορφ", continent:"Ευρώπη", specs:["PE01","PE02","PE03","PE04.01","PE04.02","PE04.04","PE04.05","PE06","PE07","PE11","PE79.01","PE78","PE80","PE86","PE60","PE70"]},
    {id:"de_mu", name:"Γερμανία — Σ.Γ.Ε. Μονάχου", continent:"Ευρώπη", specs:["PE01","PE02","PE03","PE04.01","PE04.02","PE04.04","PE06","PE07","PE08","PE11","PE60","PE70","PE78","PE79.01","PE80","PE82","PE85","PE86","PE88.04"]},
    {id:"dk", name:"Δανία", continent:"Ευρώπη", specs:["PE70","PE02"]},
    {id:"ch", name:"Ελβετία", continent:"Ευρώπη", specs:["PE60","PE70","PE02"]},
    {id:"ie", name:"Ιρλανδία", continent:"Ευρώπη", specs:["PE70","PE02"]},
    {id:"es", name:"Ισπανία", continent:"Ευρώπη", specs:["PE60","PE70","PE02"]},
    {id:"it", name:"Ιταλία", continent:"Ευρώπη", specs:["PE70","PE02"]},
    {id:"hr", name:"Κροατία", continent:"Ευρώπη", specs:["PE02"]},
    {id:"lt", name:"Λιθουανία", continent:"Ευρώπη", specs:["PE70","PE02"]},
    {id:"lu", name:"Λουξεμβούργο", continent:"Ευρώπη", specs:["PE70","PE02"]},
    {id:"mt", name:"Μάλτα", continent:"Ευρώπη", specs:["PE70"]},
    {id:"me", name:"Μαυροβούνιο", continent:"Ευρώπη", specs:["PE70","PE02"]},
    {id:"md", name:"Μολδαβία", continent:"Ευρώπη", specs:["PE70","PE02"]},
    {id:"nl", name:"Ολλανδία", continent:"Ευρώπη", specs:["PE60","PE70","PE02"]},
    {id:"hu", name:"Ουγγαρία", continent:"Ευρώπη", specs:["PE60","PE70","PE02","PE11"]},
    {id:"pl", name:"Πολωνία", continent:"Ευρώπη", specs:["PE70"]},
    {id:"pt", name:"Πορτογαλία", continent:"Ευρώπη", specs:["PE70","PE02"]},
    {id:"ro", name:"Ρουμανία", continent:"Ευρώπη", specs:["PE70","PE60","PE01","PE02","PE03","PE04.01","PE04.02","PE04.04","PE06","PE11","PE79.01","PE80","PE86"]},
    {id:"rs", name:"Σερβία", continent:"Ευρώπη", specs:["PE70","PE60","PE02","PE11"]},
    {id:"sk", name:"Σλοβακία", continent:"Ευρώπη", specs:["PE70","PE02"]},
    {id:"si", name:"Σλοβενία", continent:"Ευρώπη", specs:["PE70","PE02"]},
    {id:"se", name:"Σουηδία", continent:"Ευρώπη", specs:["PE70","PE02"]},
    {id:"no", name:"Νορβηγία", continent:"Ευρώπη", specs:["PE70","PE02"]},

    // Αμερική
    {id:"ar", name:"Αργεντινή", continent:"Αμερική", specs:["PE02","PE60","PE70"]},
    {id:"ve", name:"Βενεζουέλα", continent:"Αμερική", specs:["PE70","PE02"]},
    {id:"br", name:"Βραζιλία", continent:"Αμερική", specs:["PE70","PE02"]},
    {id:"us", name:"Η.Π.Α.", continent:"Αμερική", specs:["PE01","PE02","PE03","PE06","PE08","PE11","PE60","PE70","PE79.01"]},
    {id:"ca", name:"Καναδάς", continent:"Αμερική", specs:["PE60","PE70","PE02"]},
    {id:"mx", name:"Μεξικό", continent:"Αμερική", specs:["PE70"]},
    {id:"uy", name:"Ουρουγουάη", continent:"Αμερική", specs:["PE70","PE02","PE11"]},
    {id:"pa", name:"Παναμάς", continent:"Αμερική", specs:["PE70"]},
    {id:"pe", name:"Περού", continent:"Αμερική", specs:["PE70"]},
    {id:"cl", name:"Χιλή", continent:"Αμερική", specs:["PE70","PE02"]}
  ]);

  // Παράρτημα V — μηνιαίο επιμίσθιο εκπαιδευτικών σε ευρώ.
  // Για χώρες Κ.Α.Κ. εφαρμόζεται το κοινό ποσό 1.425 €, ενώ Σερβία/Μαυροβούνιο το κοινό ποσό 1.062 €.
  const STIPEND_BY_DESTINATION_ID = Object.freeze({
    az:1425, am:1425, ge:1425, kz:1425, uae:1237, jo:697, il:946, qa:1166, uz:1425, tr:1328,
    eg:971, et:926, zm:855, zw:855, cd:971, mg:914, za:914, tn:887,
    au:1230, nz:1230,
    al:784, at:1438, be:1224, bg:822, fr:1431, de_du:1334, de_mu:1334, dk:1230, ch:2350, ie:1334, es:822,
    it:939, hr:1062, lt:790, lu:1205, mt:939, me:1062, md:1425, nl:1224, hu:1192, pl:1192, pt:822, ro:1192,
    rs:1062, sk:1166, si:1017, se:950, no:960,
    ar:1295, ve:1004, br:817, us:1943, ca:1220, mx:1580, uy:1290, pa:1580, pe:900, cl:1000
  });

  const DESTINATION_BY_ID = Object.freeze(Object.fromEntries(DESTINATIONS.map(d => [d.id, d])));
  const CONTINENT_ORDER = ["Ασία","Αφρική","Ωκεανία","Ευρώπη","Αμερική"];

  const allIds = [
    'specialty','preference1','preference2','preference3','educationYears','teachingYears','blockingIssue',
    'tableType','bilingualPosition','phd','master','secondMaster','secondDegree',
    'primaryLevel','alternativeLanguage','alternativeDifferentFromCountry',
    'hostBilingualLevel','secondLanguageLevel','secondLanguageDistinct'
  ];

  function normalizeYears(id){
    const el = $(id);
    if(!el || el.value === '') return;
    el.value = String(Math.max(0, Math.floor(Number(el.value) || 0)));
  }

  function eligibleDestinations(specialty){
    if(!specialty) return [];
    return DESTINATIONS.filter(d => d.specs.includes(specialty));
  }

  function destinationName(id){
    return DESTINATION_BY_ID[id]?.name || "";
  }

  function stipendFor(id){
    return Number(STIPEND_BY_DESTINATION_ID[id] || 0);
  }

  function euro(value){
    return Number(value || 0).toLocaleString('el-GR', {style:'currency', currency:'EUR', maximumFractionDigits:0});
  }

  function updateStipendComparison(){
    const preferenceIds = [$('preference1').value, $('preference2').value, $('preference3').value];
    const selected = preferenceIds
      .map((id, index) => id ? {id, index:index+1, name:destinationName(id), stipend:stipendFor(id)} : null)
      .filter(Boolean);
    const box = $('stipendComparison');

    if(!selected.length){
      box.classList.add('hidden');
      box.innerHTML = '';
      return;
    }

    const rows = selected.map(item => {
      const annual = item.stipend * 12;
      return `<tr><td><strong>${item.index}η</strong></td><td>${item.name}</td><td class="amount">${euro(item.stipend)} / μήνα</td><td class="amount">${euro(annual)}</td></tr>`;
    }).join('');

    let highlight = '';
    if(selected.length > 1){
      const highest = selected.reduce((best, item) => item.stipend > best.stipend ? item : best, selected[0]);
      highlight = `<div class="stipend-highlight"><strong>Μεγαλύτερο ονομαστικό επιμίσθιο από τις επιλογές σου:</strong> ${highest.name} — ${euro(highest.stipend)} / μήνα.</div>`;
    }

    box.innerHTML = `
      <h3>💶 Επιμίσθια Παραρτήματος V</h3>
      <p class="stipend-intro">Τα ποσά είναι τα μηνιαία επιμίσθια της πρόσκλησης 11771/Η2/30-01-2026. Το 12μηνο είναι ενδεικτικός υπολογισμός ×12.</p>
      <div style="overflow-x:auto">
        <table class="stipend-table">
          <thead><tr><th>Προτίμηση</th><th>Χώρα / περιοχή</th><th>Μηνιαίο</th><th>Ενδεικτικό 12μηνο</th></tr></thead>
          <tbody>${rows}</tbody>
        </table>
      </div>
      ${highlight}
      <div class="stipend-footnote">Παράρτημα V: το επιμίσθιο είναι αφορολόγητο από 01-01-2012. Η σύγκριση αφορά μόνο το ονομαστικό ποσό και δεν συνυπολογίζει κόστος ζωής ή άλλες οικονομικές παραμέτρους.</div>`;
    box.classList.remove('hidden');
  }

  function rebuildPreferenceOptions(){
    const specialty = $('specialty').value;
    const selects = [$('preference1'), $('preference2'), $('preference3')];
    const eligible = eligibleDestinations(specialty);

    if(!specialty){
      selects.forEach(sel => {
        sel.disabled = true;
        sel.innerHTML = '<option value="">— Επίλεξε πρώτα ειδικότητα —</option>';
      });
      $('specialtyAvailability').classList.add('hidden');
      $('specialtyAvailability').innerHTML = '';
      $('preferenceNotes').innerHTML = '';
      updateStipendComparison();
      return;
    }

    // Καθαρισμός παλιών/μη επιτρεπτών ή διπλών επιλογών, με προτεραιότητα 1η → 2η → 3η.
    const allowedIds = new Set(eligible.map(d => d.id));
    const values = selects.map(sel => allowedIds.has(sel.value) ? sel.value : '');
    if(values[1] && values[1] === values[0]) values[1] = '';
    if(values[2] && (values[2] === values[0] || values[2] === values[1])) values[2] = '';

    selects.forEach((sel, index) => {
      const ownValue = values[index];
      const selectedElsewhere = new Set(values.filter((v, i) => i !== index && v));
      sel.disabled = false;
      sel.innerHTML = '';

      const blank = document.createElement('option');
      blank.value = '';
      blank.textContent = index === 0 ? '— Επίλεξε 1η προτίμηση —' : '— Καμία —';
      sel.appendChild(blank);

      CONTINENT_ORDER.forEach(continent => {
        const items = eligible.filter(d => d.continent === continent);
        if(!items.length) return;
        const group = document.createElement('optgroup');
        group.label = continent;
        items.forEach(d => {
          const option = document.createElement('option');
          option.value = d.id;
          const stipend = stipendFor(d.id);
          option.textContent = stipend ? `${d.name} · ${euro(stipend)}/μήνα` : d.name;
          option.disabled = selectedElsewhere.has(d.id) && d.id !== ownValue;
          group.appendChild(option);
        });
        sel.appendChild(group);
      });
      sel.value = ownValue;
    });

    const list = eligible.map(d => `<li>${d.name}${stipendFor(d.id) ? ` — <strong>${euro(stipendFor(d.id))}/μήνα</strong>` : ''}</li>`).join('');
    const specialtyLabel = SPECIALTY_LABELS[specialty] || specialty;
    $('specialtyAvailability').innerHTML =
      `<strong>${specialtyLabel}</strong>: το Παράρτημα ΙΙΙ προβλέπει <strong>${eligible.length}</strong> διαθέσιμες χώρες/περιοχές.` +
      `<details><summary>Προβολή όλων των διαθέσιμων επιλογών</summary><ul class="criteria-list">${list}</ul></details>`;
    $('specialtyAvailability').classList.remove('hidden');

    updatePreferenceNotes();
    updateStipendComparison();
  }

  function updatePreferenceNotes(){
    const specialty = $('specialty').value;
    const selected = [$('preference1').value, $('preference2').value, $('preference3').value].filter(Boolean);
    const notes = [];

    if(selected.includes('de_mu')){
      notes.push('Για τη Γερμανία — Σ.Γ.Ε. Μονάχου ισχύουν ειδικές προϋποθέσεις για τα επιχορηγούμενα σχολεία της Βαυαρίας και προηγούμενη άδεια διδασκαλίας από τη γερμανική υπηρεσία, όπου απαιτείται.');
      if(specialty === 'PE78') notes.push('Στο Σ.Γ.Ε. Μονάχου η ΠΕ78 αφορά ειδικά Κοινωνιολόγους.');
      if(specialty === 'PE80') notes.push('Στο Σ.Γ.Ε. Μονάχου η ΠΕ80 αφορά ειδικά Οικονομολόγους και, για τα γερμανόφωνα μαθήματα, απαιτείται αυξημένη γερμανομάθεια.');
      if(specialty === 'PE82') notes.push('Για ΠΕ82 στη Βαυαρία επισημαίνεται αυξημένη γερμανομάθεια (Γ1) για τη διδασκαλία των μαθημάτων στη γερμανική.');
      if(specialty === 'PE03') notes.push('Στη Βαυαρία τα Μαθηματικά διδάσκονται και στη γερμανική· για διδασκαλία γερμανόφωνων μαθημάτων απαιτείται Γ1.');
      if(specialty === 'PE11') notes.push('Στα Γυμνάσια Μονάχου/Νυρεμβέργης η Φυσική Αγωγή κατανέμεται ανά φύλο μαθητών και οι αποσπάσεις εξαρτώνται από τις αντίστοιχες κενές θέσεις.');
    }

    if(selected.includes('ch')){
      notes.push('Για την Ελβετία απαιτείται τουλάχιστον Β1 στην ομιλούμενη γλώσσα του τόπου εργασίας (προφορικός και γραπτός λόγος), πέρα από τον γενικό έλεγχο του πίνακα.');
    }

    $('preferenceNotes').innerHTML = notes.length
      ? '<div class="warning"><strong>Ειδικές επισημάνσεις για τις προτιμήσεις σου:</strong><ul style="margin:7px 0 0;padding-left:20px"><li>' + notes.join('</li><li>') + '</li></ul></div>'
      : '';
  }

  function updateUI(){
    rebuildPreferenceOptions();
    const tableType = $('tableType').value;
    const bilingual = $('bilingualPosition').value;

    $('alternativeFields').classList.toggle('hidden', tableType !== 'alternative');

    if(tableType === 'main'){
      $('primaryLevelLabel').textContent = 'Επίπεδο γλώσσας χώρας υποδοχής';
      $('primaryLanguageHelp').textContent =
        'Βασικός Πίνακας: Β2 = 0 μόρια, Γ1/C1 = 30 μόρια, Γ2/C2 = 50 μόρια.';
    } else if(tableType === 'alternative'){
      $('primaryLevelLabel').textContent = 'Επίπεδο εναλλακτικής γλώσσας (Αγγλικά / Γαλλικά / Γερμανικά)';
      $('primaryLanguageHelp').textContent =
        'Εναλλακτικός Πίνακας: Β2 = 0 μόρια, Γ1/C1 = 20 μόρια, Γ2/C2 = 30 μόρια.';
    } else {
      $('primaryLevelLabel').textContent = 'Επίπεδο γλώσσας αξιολογικού πίνακα';
      $('primaryLanguageHelp').textContent = 'Επίλεξε πρώτα Βασικό ή Εναλλακτικό Πίνακα.';
    }

    $('hostBilingualWrap').classList.toggle(
      'hidden',
      !(tableType === 'alternative' && bilingual === 'yes')
    );

    const secondUsed = $('secondLanguageLevel').value !== 'none';
    $('secondLanguageDistinctWrap').classList.toggle('hidden', !secondUsed);
    if(!secondUsed) $('secondLanguageDistinct').value = '';
  }

  function values(){
    normalizeYears('educationYears');
    normalizeYears('teachingYears');

    return {
      specialty: $('specialty').value,
      specialtySelected: $('specialty').value !== '',
      preference1: $('preference1').value,
      preference2: $('preference2').value,
      preference3: $('preference3').value,
      preferenceSelected: $('preference1').value !== '',
      branchAllowed: ($('specialty').value && $('preference1').value) ? 'yes' : '',
      educationYears: $('educationYears').value,
      educationYearsAnswered: $('educationYears').value !== '',
      teachingYears: $('teachingYears').value,
      teachingYearsAnswered: $('teachingYears').value !== '',
      blockingIssue: $('blockingIssue').value,
      tableType: $('tableType').value,
      bilingualPosition: $('bilingualPosition').value,

      phd: $('phd').checked,
      master: $('master').checked,
      secondMaster: $('secondMaster').checked,
      secondDegree: $('secondDegree').checked,

      primaryLevel: $('primaryLevel').value,
      alternativeLanguage: $('alternativeLanguage').value,
      alternativeDifferentFromCountry: $('alternativeDifferentFromCountry').value,
      hostBilingualLevel: $('hostBilingualLevel').value,

      secondLanguageLevel: $('secondLanguageLevel').value,
      secondLanguageDistinct: $('secondLanguageDistinct').value
    };
  }

  function calculate(){
    updateUI();
    const r = AbroadSecondment.calculate(values());

    $('grandTotal').textContent = fmt(r.total);
    $('academicResult').textContent = fmt(r.academic);
    $('primaryResult').textContent = fmt(r.primaryLanguagePoints);
    $('secondLanguageResult').textContent = fmt(r.secondLanguagePoints);

    if(r.tableType === 'main'){
      $('tableResult').textContent = 'Βασικός';
      $('primaryResultLabel').textContent = 'Γλώσσα χώρας';
      $('totalOutOf').textContent = 'θεωρητικά έως 185';
    } else if(r.tableType === 'alternative'){
      $('tableResult').textContent = 'Εναλλακτικός';
      $('primaryResultLabel').textContent = 'Εναλλακτική γλώσσα';
      $('totalOutOf').textContent = 'θεωρητικά έως 165';
    } else {
      $('tableResult').textContent = '—';
      $('primaryResultLabel').textContent = 'Γλώσσα πίνακα';
      $('totalOutOf').textContent = 'Επίλεξε αξιολογικό πίνακα';
    }

    const pct = r.theoreticalMax ? Math.min(100, (r.total / r.theoreticalMax) * 100) : 0;
    $('scoreBar').style.width = pct + '%';

    const boxes = [];
    if(r.unanswered.length){
      boxes.push(
        '<div class="info"><strong>Χρειάζονται ακόμη στοιχεία:</strong><ul style="margin:7px 0 0;padding-left:20px"><li>'
        + r.unanswered.join('</li><li>') + '</li></ul></div>'
      );
    }

    if(r.issues.length){
      boxes.push(
        '<div class="danger"><strong>Ο βασικός έλεγχος δεν είναι θετικός:</strong><ul style="margin:7px 0 0;padding-left:20px"><li>'
        + r.issues.join('</li><li>') + '</li></ul></div>'
      );
    } else if(r.eligible){
      boxes.push(
        '<div class="success"><strong>Ο βασικός έλεγχος είναι θετικός.</strong> Με τα δηλωμένα στοιχεία καλύπτονται οι βασικές προϋποθέσεις για τον επιλεγμένο πίνακα. Ο συνδυασμός ειδικότητας και 1ης προτίμησης έχει ήδη ελεγχθεί αυτόματα στο Παράρτημα ΙΙΙ. Απαιτείται πάντως έλεγχος όλων των δικαιολογητικών και των ειδικών τοπικών προϋποθέσεων.</div>'
      );
    }

    if(r.warnings.length){
      boxes.push(
        '<div class="warning"><strong>Παρατηρήσεις:</strong><ul style="margin:7px 0 0;padding-left:20px"><li>'
        + r.warnings.join('</li><li>') + '</li></ul></div>'
      );
    }

    $('eligibilityStatus').innerHTML = boxes.join('');
    return r;
  }

  async function copySummary(){
    const r = calculate();
    const table = r.tableType === 'main'
      ? 'Βασικός Πίνακας'
      : (r.tableType === 'alternative' ? 'Εναλλακτικός Πίνακας' : 'Δεν επιλέχθηκε');

    const lines = [
      'Μόρια Απόσπασης στο Εξωτερικό',
      `Πίνακας: ${table}`,
      `Ειδικότητα: ${SPECIALTY_LABELS[$('specialty').value] || 'Δεν επιλέχθηκε'}`,
      `1η προτίμηση: ${destinationName($('preference1').value) || 'Δεν επιλέχθηκε'}`,
      `2η προτίμηση: ${destinationName($('preference2').value) || '—'}`,
      `3η προτίμηση: ${destinationName($('preference3').value) || '—'}`,
      `Επιμίσθιο 1ης: ${$('preference1').value ? euro(stipendFor($('preference1').value)) + ' / μήνα' : '—'}`,
      `Επιμίσθιο 2ης: ${$('preference2').value ? euro(stipendFor($('preference2').value)) + ' / μήνα' : '—'}`,
      `Επιμίσθιο 3ης: ${$('preference3').value ? euro(stipendFor($('preference3').value)) + ' / μήνα' : '—'}`,
      `Τίτλοι σπουδών: ${fmt(r.academic)}`,
      `Γλώσσα πίνακα: ${fmt(r.primaryLanguagePoints)}`,
      `Δεύτερη ξένη γλώσσα: ${fmt(r.secondLanguagePoints)}`,
      `Σύνολο: ${fmt(r.total)}`,
      `Βασικός έλεγχος: ${r.eligible ? 'ΘΕΤΙΚΟΣ' : 'ΜΗ ΟΛΟΚΛΗΡΩΜΕΝΟΣ / ΜΗ ΘΕΤΙΚΟΣ'}`,
      'Ενδεικτικός υπολογισμός βάσει της Υ.Α. 83046/Η2/30-06-2020 (Β΄ 2687).'
    ];

    try{
      await navigator.clipboard.writeText(lines.join('\n'));
      $('copyBtn').textContent = 'Αντιγράφηκε ✓';
      setTimeout(() => $('copyBtn').textContent = 'Αντιγραφή σύνοψης', 1400);
    }catch(e){
      alert(lines.join('\n'));
    }
  }

  function reset(){
    document.querySelectorAll('input[type="checkbox"]').forEach(el => el.checked = false);
    document.querySelectorAll('input[type="number"]').forEach(el => el.value = '');
    document.querySelectorAll('select').forEach(el => el.selectedIndex = 0);
    $('secondLanguageLevel').value = 'none';
    updateUI();
    calculate();
    $('specialty').focus();
  }

  allIds.forEach(id => {
    const el = $(id);
    if(!el) return;
    el.addEventListener('input', calculate);
    el.addEventListener('change', calculate);
  });

  $('copyBtn').addEventListener('click', copySummary);
  $('resetBtn').addEventListener('click', reset);

  updateUI();
  calculate();
})();
