/*
 * Browser UI controller for ypologismos-morion-apospasis-exoteriko.php.
 * Calculation/business rules stay in abroad-calculations.js so the same
 * engine can be reused by a future mobile client.
 */
(function(global){

  "use strict";
  const $ = id => document.getElementById(id);
  let initialized = false;
  const fmt = n => Number(n || 0).toLocaleString('el-GR', {maximumFractionDigits: 1});

  function normalizeSpecialtyCode(value){
    if(global.EducationCore && typeof global.EducationCore.normalizeSpecialtyCode === 'function'){
      return global.EducationCore.normalizeSpecialtyCode(value);
    }
    return String(value == null ? '' : value).trim().toUpperCase()
      .replace(/^(?:PE|PΕ|ΠE|ΠΕ)/, 'ΠΕ')
      .replace(/^(?:TE|TΕ|ΤE|ΤΕ)/, 'ΤΕ')
      .replace(/^(?:DE|DΕ|ΔE|ΔΕ)/, 'ΔΕ');
  }

  // Παράρτημα ΙΙΙ — Πίνακας χωρών/ειδικοτήτων, πρόσκληση 11771/Η2/30-01-2026.
  const DESTINATIONS = Object.freeze([
    // Ασία
    {id:"az", name:"Αζερμπαϊτζάν", continent:"Ασία", specs:["ΠΕ70","ΠΕ02"]},
    {id:"am", name:"Αρμενία", continent:"Ασία", specs:["ΠΕ70","ΠΕ02"]},
    {id:"ge", name:"Γεωργία", continent:"Ασία", specs:["ΠΕ70","ΠΕ02","ΠΕ11","ΠΕ79.01"]},
    {id:"kz", name:"Καζακστάν", continent:"Ασία", specs:["ΠΕ70","ΠΕ02"]},
    {id:"uae", name:"Η.Α.Ε.", continent:"Ασία", specs:["ΠΕ70","ΠΕ02"]},
    {id:"jo", name:"Ιορδανία", continent:"Ασία", specs:["ΠΕ70","ΠΕ02"]},
    {id:"il", name:"Ισραήλ", continent:"Ασία", specs:["ΠΕ70","ΠΕ02","ΠΕ03","ΠΕ04.01","ΠΕ04.02","ΠΕ04.03","ΠΕ04.04","ΠΕ04.05","ΠΕ06","ΠΕ86"]},
    {id:"qa", name:"Κατάρ", continent:"Ασία", specs:["ΠΕ60","ΠΕ70","ΠΕ02"]},
    {id:"uz", name:"Ουζμπεκιστάν", continent:"Ασία", specs:["ΠΕ70"]},
    {id:"tr", name:"Τουρκία", continent:"Ασία", specs:["ΠΕ02","ΠΕ03","ΠΕ04.01","ΠΕ04.02","ΠΕ04.04","ΠΕ06","ΠΕ11","ΠΕ79.01","ΠΕ60","ΠΕ70","ΠΕ86"]},

    // Αφρική
    {id:"eg", name:"Αίγυπτος", continent:"Αφρική", specs:["ΠΕ01","ΠΕ02","ΠΕ03","ΠΕ04.01","ΠΕ04.02","ΠΕ04.03","ΠΕ04.04","ΠΕ06","ΠΕ11","ΠΕ60","ΠΕ70","ΠΕ78","ΠΕ79.01","ΠΕ86"]},
    {id:"et", name:"Αιθιοπία", continent:"Αφρική", specs:["ΠΕ70"]},
    {id:"zm", name:"Ζάμπια", continent:"Αφρική", specs:["ΠΕ70"]},
    {id:"zw", name:"Ζιμπάμπουε", continent:"Αφρική", specs:["ΠΕ70"]},
    {id:"cd", name:"Λ. Δ. Κονγκό", continent:"Αφρική", specs:["ΠΕ60","ΠΕ70","ΠΕ02","ΠΕ03","ΠΕ04.01","ΠΕ04.02","ΠΕ86","ΠΕ06","ΠΕ05","ΠΕ11","ΠΕ80"]},
    {id:"mg", name:"Μαδαγασκάρη", continent:"Αφρική", specs:["ΠΕ70","ΠΕ02"]},
    {id:"za", name:"Νότια Αφρική", continent:"Αφρική", specs:["ΠΕ60","ΠΕ70","ΠΕ02","ΠΕ79.01","ΠΕ06","ΠΕ86","ΠΕ11"]},
    {id:"tn", name:"Τυνησία", continent:"Αφρική", specs:["ΠΕ70","ΠΕ02"]},

    // Ωκεανία
    {id:"au", name:"Αυστραλία", continent:"Ωκεανία", specs:["ΠΕ02","ΠΕ06","ΠΕ60","ΠΕ70"]},
    {id:"nz", name:"Νέα Ζηλανδία", continent:"Ωκεανία", specs:["ΠΕ70"]},

    // Ευρώπη
    {id:"al", name:"Αλβανία", continent:"Ευρώπη", specs:["ΠΕ60","ΠΕ70","ΠΕ02","ΠΕ04.04","ΠΕ08","ΠΕ11","ΠΕ79.01","ΠΕ83","ΠΕ86","ΠΕ06"]},
    {id:"at", name:"Αυστρία", continent:"Ευρώπη", specs:["ΠΕ60","ΠΕ70","ΠΕ02"]},
    {id:"be", name:"Βέλγιο", continent:"Ευρώπη", specs:["ΠΕ60","ΠΕ70","ΠΕ01","ΠΕ02","ΠΕ03","ΠΕ04.01","ΠΕ04.02","ΠΕ04.04","ΠΕ05","ΠΕ06","ΠΕ08","ΠΕ11","ΠΕ78","ΠΕ79.01","ΠΕ80","ΠΕ86"]},
    {id:"bg", name:"Βουλγαρία", continent:"Ευρώπη", specs:["ΠΕ60","ΠΕ70","ΠΕ02","ΠΕ11"]},
    {id:"fr", name:"Γαλλία", continent:"Ευρώπη", specs:["ΠΕ60","ΠΕ70","ΠΕ02"]},
    {id:"de_du", name:"Γερμανία — Σ.Γ.Ε. Ντίσελντορφ", continent:"Ευρώπη", specs:["ΠΕ01","ΠΕ02","ΠΕ03","ΠΕ04.01","ΠΕ04.02","ΠΕ04.04","ΠΕ04.05","ΠΕ06","ΠΕ07","ΠΕ11","ΠΕ79.01","ΠΕ78","ΠΕ80","ΠΕ86","ΠΕ60","ΠΕ70"]},
    {id:"de_mu", name:"Γερμανία — Σ.Γ.Ε. Μονάχου", continent:"Ευρώπη", specs:["ΠΕ01","ΠΕ02","ΠΕ03","ΠΕ04.01","ΠΕ04.02","ΠΕ04.04","ΠΕ06","ΠΕ07","ΠΕ08","ΠΕ11","ΠΕ60","ΠΕ70","ΠΕ78","ΠΕ79.01","ΠΕ80","ΠΕ82","ΠΕ85","ΠΕ86","ΠΕ88.04"]},
    {id:"dk", name:"Δανία", continent:"Ευρώπη", specs:["ΠΕ70","ΠΕ02"]},
    {id:"ch", name:"Ελβετία", continent:"Ευρώπη", specs:["ΠΕ60","ΠΕ70","ΠΕ02"]},
    {id:"ie", name:"Ιρλανδία", continent:"Ευρώπη", specs:["ΠΕ70","ΠΕ02"]},
    {id:"es", name:"Ισπανία", continent:"Ευρώπη", specs:["ΠΕ60","ΠΕ70","ΠΕ02"]},
    {id:"it", name:"Ιταλία", continent:"Ευρώπη", specs:["ΠΕ70","ΠΕ02"]},
    {id:"hr", name:"Κροατία", continent:"Ευρώπη", specs:["ΠΕ02"]},
    {id:"lt", name:"Λιθουανία", continent:"Ευρώπη", specs:["ΠΕ70","ΠΕ02"]},
    {id:"lu", name:"Λουξεμβούργο", continent:"Ευρώπη", specs:["ΠΕ70","ΠΕ02"]},
    {id:"mt", name:"Μάλτα", continent:"Ευρώπη", specs:["ΠΕ70"]},
    {id:"me", name:"Μαυροβούνιο", continent:"Ευρώπη", specs:["ΠΕ70","ΠΕ02"]},
    {id:"md", name:"Μολδαβία", continent:"Ευρώπη", specs:["ΠΕ70","ΠΕ02"]},
    {id:"nl", name:"Ολλανδία", continent:"Ευρώπη", specs:["ΠΕ60","ΠΕ70","ΠΕ02"]},
    {id:"hu", name:"Ουγγαρία", continent:"Ευρώπη", specs:["ΠΕ60","ΠΕ70","ΠΕ02","ΠΕ11"]},
    {id:"pl", name:"Πολωνία", continent:"Ευρώπη", specs:["ΠΕ70"]},
    {id:"pt", name:"Πορτογαλία", continent:"Ευρώπη", specs:["ΠΕ70","ΠΕ02"]},
    {id:"ro", name:"Ρουμανία", continent:"Ευρώπη", specs:["ΠΕ70","ΠΕ60","ΠΕ01","ΠΕ02","ΠΕ03","ΠΕ04.01","ΠΕ04.02","ΠΕ04.04","ΠΕ06","ΠΕ11","ΠΕ79.01","ΠΕ80","ΠΕ86"]},
    {id:"rs", name:"Σερβία", continent:"Ευρώπη", specs:["ΠΕ70","ΠΕ60","ΠΕ02","ΠΕ11"]},
    {id:"sk", name:"Σλοβακία", continent:"Ευρώπη", specs:["ΠΕ70","ΠΕ02"]},
    {id:"si", name:"Σλοβενία", continent:"Ευρώπη", specs:["ΠΕ70","ΠΕ02"]},
    {id:"se", name:"Σουηδία", continent:"Ευρώπη", specs:["ΠΕ70","ΠΕ02"]},
    {id:"no", name:"Νορβηγία", continent:"Ευρώπη", specs:["ΠΕ70","ΠΕ02"]},

    // Αμερική
    {id:"ar", name:"Αργεντινή", continent:"Αμερική", specs:["ΠΕ02","ΠΕ60","ΠΕ70"]},
    {id:"ve", name:"Βενεζουέλα", continent:"Αμερική", specs:["ΠΕ70","ΠΕ02"]},
    {id:"br", name:"Βραζιλία", continent:"Αμερική", specs:["ΠΕ70","ΠΕ02"]},
    {id:"us", name:"Η.Π.Α.", continent:"Αμερική", specs:["ΠΕ01","ΠΕ02","ΠΕ03","ΠΕ06","ΠΕ08","ΠΕ11","ΠΕ60","ΠΕ70","ΠΕ79.01"]},
    {id:"ca", name:"Καναδάς", continent:"Αμερική", specs:["ΠΕ60","ΠΕ70","ΠΕ02"]},
    {id:"mx", name:"Μεξικό", continent:"Αμερική", specs:["ΠΕ70"]},
    {id:"uy", name:"Ουρουγουάη", continent:"Αμερική", specs:["ΠΕ70","ΠΕ02","ΠΕ11"]},
    {id:"pa", name:"Παναμάς", continent:"Αμερική", specs:["ΠΕ70"]},
    {id:"pe", name:"Περού", continent:"Αμερική", specs:["ΠΕ70"]},
    {id:"cl", name:"Χιλή", continent:"Αμερική", specs:["ΠΕ70","ΠΕ02"]}
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

  function selectedSpecialtyLabel(){
    const select = $('specialty');
    if(!select || !select.value) return '';
    const option = select.options[select.selectedIndex];
    return option ? option.textContent.trim() : select.value;
  }

  function normalizeYears(id){
    const el = $(id);
    if(!el || el.value === '') return;
    el.value = String(Math.min(50, Math.max(0, Math.floor(Number(el.value) || 0))));
  }

  function eligibleDestinations(specialty){
    specialty = normalizeSpecialtyCode(specialty);
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

  function euroCents(value){
    return Number(value || 0).toLocaleString('el-GR', {style:'currency', currency:'EUR', minimumFractionDigits:2, maximumFractionDigits:2});
  }

  const CURRENT_STIPEND_DEDUCTION_RATE = 0.02;

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

    const estimatedNetRows = selected.map(item => {
      const deduction = item.stipend * CURRENT_STIPEND_DEDUCTION_RATE;
      const afterDeduction = item.stipend - deduction;
      return `<li><strong>${item.name}:</strong> ${euroCents(item.stipend)} − ${euroCents(deduction)} (2%) = <strong>${euroCents(afterDeduction)} / μήνα</strong></li>`;
    }).join('');

    const taxInfo = `
      <div class="stipend-tax-info">
        <strong class="title">🧾 Φορολογία &amp; ειδικές κρατήσεις του επιμισθίου</strong>
        <div class="stipend-tax-grid">
          <span>Φόρος εισοδήματος στην Ελλάδα</span><b>0% — αφορολόγητο</b>
          <span>Ειδική εισφορά ΤΠΔΥ</span><b>0% — καταργήθηκε</b>
          <span>Ειδική εισφορά αλληλεγγύης ν. 3986/2011</span><b>2%</b>
        </div>
        <div class="stipend-footnote">Η κράτηση 2% είναι η ειδική εισφορά για την καταπολέμηση της ανεργίας του άρθρου 38 ν. 3986/2011· είναι διαφορετική από την παλαιά φορολογική «εισφορά αλληλεγγύης» του ΚΦΕ.</div>
        <ul class="stipend-net-list">${estimatedNetRows}</ul>
        <div class="stipend-footnote"><strong>Προσοχή:</strong> το ποσό μετά το 2% είναι ενδεικτικό ως προς την ελληνική μισθοδοσία. Η πρόσκληση 2026 επιβεβαιώνει ρητά το αφορολόγητο, αλλά δεν επαναλαμβάνει αναλυτικό πίνακα κρατήσεων. Δεν συνυπολογίζονται τυχόν υποχρεώσεις στη χώρα υποδοχής ή τραπεζικά έξοδα.</div>
      </div>`;

    box.innerHTML = `
      <h3>💶 Επιμίσθια Παραρτήματος V</h3>
      <p class="stipend-intro">Τα ποσά είναι τα μηνιαία επιμίσθια της πρόσκλησης 11771/Η2/30-01-2026. Το 12μηνο είναι ενδεικτικός υπολογισμός ×12.</p>
      <div class="edu-overflow-x-auto">
        <table class="stipend-table">
          <thead><tr><th>Προτίμηση</th><th>Χώρα / περιοχή</th><th>Μηνιαίο</th><th>Ενδεικτικό 12μηνο</th></tr></thead>
          <tbody>${rows}</tbody>
        </table>
      </div>
      ${highlight}
      ${taxInfo}
      <div class="stipend-footnote">Παράρτημα V: το επιμίσθιο είναι αφορολόγητο από 01-01-2012. Η σύγκριση εξακολουθεί να βασίζεται στα ονομαστικά ποσά της πρόσκλησης και δεν συνυπολογίζει κόστος ζωής ή άλλες οικονομικές παραμέτρους.</div>`;
    box.classList.remove('hidden');
  }

  function rebuildPreferenceOptions(){
    const specialty = normalizeSpecialtyCode($('specialty').value);
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
    const specialtyLabel = selectedSpecialtyLabel() || specialty;
    $('specialtyAvailability').innerHTML =
      `<strong>${specialtyLabel}</strong>: το Παράρτημα ΙΙΙ προβλέπει <strong>${eligible.length}</strong> διαθέσιμες χώρες/περιοχές.` +
      `<details><summary>Προβολή όλων των διαθέσιμων επιλογών</summary><ul class="criteria-list">${list}</ul></details>`;
    $('specialtyAvailability').classList.remove('hidden');

    updatePreferenceNotes();
    updateStipendComparison();
  }

  function updatePreferenceNotes(){
    const specialty = normalizeSpecialtyCode($('specialty').value);
    const selected = [$('preference1').value, $('preference2').value, $('preference3').value].filter(Boolean);
    const notes = [];

    if(selected.includes('de_mu')){
      notes.push('Για τη Γερμανία — Σ.Γ.Ε. Μονάχου ισχύουν ειδικές προϋποθέσεις για τα επιχορηγούμενα σχολεία της Βαυαρίας και προηγούμενη άδεια διδασκαλίας από τη γερμανική υπηρεσία, όπου απαιτείται.');
      if(specialty === 'ΠΕ78') notes.push('Στο Σ.Γ.Ε. Μονάχου η ΠΕ78 αφορά ειδικά Κοινωνιολόγους.');
      if(specialty === 'ΠΕ80') notes.push('Στο Σ.Γ.Ε. Μονάχου η ΠΕ80 αφορά ειδικά Οικονομολόγους και, για τα γερμανόφωνα μαθήματα, απαιτείται αυξημένη γερμανομάθεια.');
      if(specialty === 'ΠΕ82') notes.push('Για ΠΕ82 στη Βαυαρία επισημαίνεται αυξημένη γερμανομάθεια (Γ1) για τη διδασκαλία των μαθημάτων στη γερμανική.');
      if(specialty === 'ΠΕ03') notes.push('Στη Βαυαρία τα Μαθηματικά διδάσκονται και στη γερμανική· για διδασκαλία γερμανόφωνων μαθημάτων απαιτείται Γ1.');
      if(specialty === 'ΠΕ11') notes.push('Στα Γυμνάσια Μονάχου/Νυρεμβέργης η Φυσική Αγωγή κατανέμεται ανά φύλο μαθητών και οι αποσπάσεις εξαρτώνται από τις αντίστοιχες κενές θέσεις.');
    }

    if(selected.includes('ch')){
      notes.push('Για την Ελβετία απαιτείται τουλάχιστον Β1 στην ομιλούμενη γλώσσα του τόπου εργασίας (προφορικός και γραπτός λόγος), πέρα από τον γενικό έλεγχο του πίνακα.');
    }

    $('preferenceNotes').innerHTML = notes.length
      ? '<div class="warning"><strong>Ειδικές επισημάνσεις για τις προτιμήσεις σου:</strong><ul class="edu-list-compact"><li>' + notes.join('</li><li>') + '</li></ul></div>'
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

    const specialty = normalizeSpecialtyCode($('specialty').value);

    return {
      specialty,
      specialtySelected: specialty !== '',
      preference1: $('preference1').value,
      preference2: $('preference2').value,
      preference3: $('preference3').value,
      preferenceSelected: $('preference1').value !== '',
      branchAllowed: (specialty && $('preference1').value) ? 'yes' : '',
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
        '<div class="info"><strong>Χρειάζονται ακόμη στοιχεία:</strong><ul class="edu-list-compact"><li>'
        + r.unanswered.join('</li><li>') + '</li></ul></div>'
      );
    }

    if(r.issues.length){
      boxes.push(
        '<div class="danger"><strong>Ο βασικός έλεγχος δεν είναι θετικός:</strong><ul class="edu-list-compact"><li>'
        + r.issues.join('</li><li>') + '</li></ul></div>'
      );
    } else if(r.eligible){
      boxes.push(
        '<div class="success"><strong>Ο βασικός έλεγχος είναι θετικός.</strong> Με τα δηλωμένα στοιχεία καλύπτονται οι βασικές προϋποθέσεις για τον επιλεγμένο πίνακα. Ο συνδυασμός ειδικότητας και 1ης προτίμησης έχει ήδη ελεγχθεί αυτόματα στο Παράρτημα ΙΙΙ. Απαιτείται πάντως έλεγχος όλων των δικαιολογητικών και των ειδικών τοπικών προϋποθέσεων.</div>'
      );
    }

    if(r.warnings.length){
      boxes.push(
        '<div class="warning"><strong>Παρατηρήσεις:</strong><ul class="edu-list-compact"><li>'
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
      `Ειδικότητα: ${selectedSpecialtyLabel() || 'Δεν επιλέχθηκε'}`,
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
      setTimeout(() => $('copyBtn').textContent = 'Αντιγραφή', 1400);
    }catch(e){
      alert(lines.join('\n'));
    }
  }

  function reset(){
    document.querySelectorAll('input[type="checkbox"]').forEach(el => el.checked = false);
    document.querySelectorAll('input[type="number"]').forEach(el => el.value = '');
    document.querySelectorAll('select').forEach(el => el.selectedIndex = 0);
    $('secondLanguageLevel').value = 'none';
    calculate();
    $('specialty').focus();
  }

  function init(){
    if(initialized) return;
    initialized = true;

    allIds.forEach(id => {
      const el = $(id);
      if(!el) return;
      el.addEventListener('input', calculate);
      el.addEventListener('change', calculate);
    });

    const copyBtn = $('copyBtn');
    const resetBtn = $('resetBtn');
    if(copyBtn) copyBtn.addEventListener('click', copySummary);
    if(resetBtn) resetBtn.addEventListener('click', reset);

    calculate();
  }

  global.AbroadSecondmentUI = Object.freeze({ init });
  if(document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init);
  else init();

})(window);
