/* Extracted UI/controller logic from metatropi-klimakas.php. */
(function(){
(function(){
  const decimalTab = document.getElementById('decimalTab');
  const lexicalTab = document.getElementById('lexicalTab');
  const decimalPanel = document.getElementById('decimalPanel');
  const lexicalPanel = document.getElementById('lexicalPanel');
  const decimalGrade = document.getElementById('decimalGrade');
  const lexicalGrade = document.getElementById('lexicalGrade');
  const decimalError = document.getElementById('decimalError');
  const result = document.getElementById('result');
  const sourceSummary = document.getElementById('sourceSummary');
  const copyStatus = document.getElementById('copyStatus');
  let mode = 'decimal';
  let current = null;

  function round2(value){
    return Math.round((value + Number.EPSILON) * 100) / 100;
  }

  function formatEl(value){
    return new Intl.NumberFormat('el-GR', {maximumFractionDigits:2, minimumFractionDigits:0}).format(value);
  }

  function mixedFraction(value){
    let integer = Math.floor(value + 1e-9);
    let numerator = Math.round((value - integer) * 100);
    if(numerator >= 100){
      integer += 1;
      numerator = 0;
    }
    return {integer, numerator, denominator:100};
  }

  function parseDecimal(){
    const raw = decimalGrade.value.trim().replace(',', '.');
    if(raw === '') return null;
    if(!/^\d+(?:\.\d+)?$/.test(raw)) return NaN;
    return Number(raw);
  }

  function setMode(newMode){
    mode = newMode;
    const isDecimal = mode === 'decimal';
    decimalTab.classList.toggle('active', isDecimal);
    lexicalTab.classList.toggle('active', !isDecimal);
    decimalTab.setAttribute('aria-pressed', isDecimal ? 'true' : 'false');
    lexicalTab.setAttribute('aria-pressed', isDecimal ? 'false' : 'true');
    decimalPanel.classList.toggle('hidden', !isDecimal);
    lexicalPanel.classList.toggle('hidden', isDecimal);
    decimalError.style.display = 'none';
    copyStatus.textContent = '';
    update();
  }

  function update(){
    let grade;
    let sourceLabel;
    decimalError.style.display = 'none';
    copyStatus.textContent = '';

    if(mode === 'decimal'){
      grade = parseDecimal();
      if(grade === null){
        result.classList.add('hidden');
        current = null;
        return;
      }
      if(Number.isNaN(grade) || grade < 1 || grade > 10){
        result.classList.add('hidden');
        current = null;
        decimalError.textContent = 'Καταχώρισε έγκυρο βαθμό από 1 έως 10.';
        decimalError.style.display = 'block';
        return;
      }
      sourceLabel = 'Δεκαδικός βαθμός: ' + formatEl(grade) + '/10';
    } else {
      if(!lexicalGrade.value){
        result.classList.add('hidden');
        current = null;
        return;
      }
      grade = Number(lexicalGrade.value);
      sourceLabel = 'Λεκτικός βαθμός: ' + lexicalGrade.options[lexicalGrade.selectedIndex].text;
    }

    const grade10 = round2(grade);
    const grade20 = round2(grade10 * 2);
    const frac10 = mixedFraction(grade10);
    const frac20 = mixedFraction(grade20);
    const points10 = Math.min(25, round2(grade10 * 2.5));
    const points20 = Math.min(60, round2(grade20 * 3));

    current = {grade10, grade20, frac10, frac20, points10, points20};

    sourceSummary.textContent = sourceLabel + ' → ' + formatEl(grade10) + '/10 → ' + formatEl(grade20) + '/20';
    document.getElementById('grade10').textContent = formatEl(grade10) + ' / 10';
    document.getElementById('grade20').textContent = formatEl(grade20) + ' / 20';
    document.getElementById('int10').textContent = frac10.integer;
    document.getElementById('num10').textContent = frac10.numerator;
    document.getElementById('int20').textContent = frac20.integer;
    document.getElementById('num20').textContent = frac20.numerator;
    document.getElementById('points10').textContent = 'Μόρια 1ΓΕ/2026: ' + formatEl(points10);
    document.getElementById('points20').textContent = 'Μόρια 1ΓΤ/2024: ' + formatEl(points20);
    result.classList.remove('hidden');
  }

  async function copyFields(){
    if(!current) return;
    const text = [
      '1ΓΤ/2024 – Βαθμός σε 20βάθμια κλίμακα: ' + formatEl(current.grade20),
      'Ακέραιο μέρος: ' + current.frac20.integer,
      'Αριθμητής: ' + current.frac20.numerator,
      'Παρονομαστής: ' + current.frac20.denominator
    ].join('\n');

    try{
      await navigator.clipboard.writeText(text);
      copyStatus.textContent = '✓ Αντιγράφηκε';
    }catch(e){
      const area = document.createElement('textarea');
      area.value = text;
      document.body.appendChild(area);
      area.select();
      document.execCommand('copy');
      area.remove();
      copyStatus.textContent = '✓ Αντιγράφηκε';
    }
  }

  function resetAll(){
    decimalGrade.value = '';
    lexicalGrade.value = '';
    decimalError.style.display = 'none';
    result.classList.add('hidden');
    copyStatus.textContent = '';
    current = null;
    setMode('decimal');
    decimalGrade.focus();
  }

  decimalTab.addEventListener('click', () => setMode('decimal'));
  lexicalTab.addEventListener('click', () => setMode('lexical'));
  decimalGrade.addEventListener('input', update);
  lexicalGrade.addEventListener('change', update);
  document.getElementById('copy20').addEventListener('click', copyFields);
  document.getElementById('resetBtn').addEventListener('click', resetAll);
})();

  document.getElementById('printBtn').addEventListener('click', () => window.print());
})();
