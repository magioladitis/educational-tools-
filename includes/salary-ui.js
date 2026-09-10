/*
 * Browser UI controller for ypologismos-misthologikou-klimakiou.php.
 * Salary/business rules stay in salary-scale-calculations.js and
 * salary-net-calculations.js so the same engines can be reused by a mobile client.
 */
(function (global) {
  'use strict';
  const byId = id => document.getElementById(id);
  function integer(id, max) {
    const n = Math.max(0, Math.floor(Number(byId(id).value) || 0));
    return Number.isFinite(max) ? Math.min(max, n) : n;
  }

  function money(id) {
    const n = Number(byId(id).value);
    return Number.isFinite(n) ? Math.max(0, n) : 0;
  }

  function escapeHtml(value) {
    return String(value == null ? '' : value)
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#039;');
  }

  function selectedText(id) {
    const el = byId(id);
    if (!el || !el.options || el.selectedIndex < 0) return '';
    return String(el.options[el.selectedIndex].textContent || '').trim();
  }

  function setResultRowVisible(valueId, visible) {
    const value = byId(valueId);
    const row = value && value.closest ? value.closest('.result-row') : null;
    if (row) row.hidden = !visible;
  }

  function renderDeductionDetails(net) {
    const details = byId('deductionBreakdownDetails');
    if (!details) return;
    let html = '';
    (net.deductionComponents || []).forEach(function (component) {
      let note = component.note || '';
      if (component.rate != null && component.base != null) {
        note = formatPercent(component.rate) + ' · βάση ' + formatEuroCents(component.base);
      }
      html += '<div class="payroll-deduction-detail-row">' +
        '<span>' + escapeHtml(component.label) + (note ? '<small class="payroll-deduction-detail-note">' + escapeHtml(note) + '</small>' : '') + '</span>' +
        '<strong>' + formatEuroCents(component.amount) + '</strong>' +
      '</div>';
    });
    if (net.maternityPensionReductionAmount > 0) {
      html += '<div class="payroll-deduction-detail-row">' +
        '<span>Μείωση εισφοράς κύριας σύνταξης λόγω μητρότητας<small class="payroll-deduction-detail-note">50% της εργατικής εισφοράς κύριας σύνταξης · βάση ' + escapeHtml(formatEuroCents(net.maternityPensionContributionBase)) + '</small></span>' +
        '<strong>−' + formatEuroCents(net.maternityPensionReductionAmount) + '</strong>' +
      '</div>';
    }
    details.innerHTML = html || '<div class="payroll-deduction-detail-row"><span>Δεν υπάρχουν επιμέρους τακτικές κρατήσεις.</span><strong>—</strong></div>';
  }

  function printAmountRow(label, amount, options) {
    options = options || {};
    const prefix = options.negative ? '−' : '';
    const note = options.note ? '<small>' + escapeHtml(options.note) + '</small>' : '';
    return '<tr><td>' + escapeHtml(label) + (note ? '<br>' + note : '') + '</td><td class="amount">' + prefix + formatEuroCents(amount) + '</td></tr>';
  }

  function renderPrintSheet(result, net, payroll) {
    const content = byId('payrollPrintContent');
    if (!content) return;
    const totalDeductions = window.EducationSalaryNet.roundMoney(
      net.standardDeductions + net.registrationDeduction + net.otherDeductions + net.monthlyTax
    );
    const grossRows = [
      printAmountRow('Βασικός μισθός — Μ.Κ. ' + result.finalMK, result.basicGrossSalary),
      printAmountRow('Οικογενειακή παροχή', payroll.familyAllowance),
      printAmountRow('Επίδομα θέσης ευθύνης', payroll.positionAllowance, { note: payroll.positionLabel }),
      printAmountRow('Επίδομα απομακρυσμένων - παραμεθορίων περιοχών', payroll.remoteAllowance)
    ].join('');

    let deductionRows = '';
    (net.deductionComponents || []).forEach(function (component) {
      let componentNote = component.note || '';
      if (component.rate != null && component.base != null) {
        componentNote = formatPercent(component.rate) + ' · βάση ' + formatEuroCents(component.base);
      }
      deductionRows += printAmountRow(component.label, component.amount, {
        note: componentNote
      });
    });
    if (net.maternityPensionReductionAmount > 0) {
      deductionRows += printAmountRow('Μείωση εισφοράς κύριας σύνταξης λόγω μητρότητας', net.maternityPensionReductionAmount, {
        negative: true, note: '50% της εργατικής εισφοράς κύριας σύνταξης · βάση ' + formatEuroCents(net.maternityPensionContributionBase)
      });
    }
    if (net.registrationDeduction > 0) {
      deductionRows += printAmountRow('Δικαίωμα εγγραφής ΜΤΠΥ — μηνιαία δόση', net.registrationDeduction, { note: '1/12 των μικτών αποδοχών της εκτίμησης' });
    }
    payroll.otherDeductionParts.forEach(function (item) {
      if (item[1] > 0) deductionRows += printAmountRow(item[0], item[1]);
    });
    deductionRows += printAmountRow('Φόρος εισοδήματος — μηνιαία παρακράτηση', net.monthlyTax);

    const totalService = integer('serviceYears', 50) * 12 + integer('serviceMonths', 11);
    const qualificationText = selectedText('qualification');
    content.innerHTML =
      '<div class="print-grid">' +
        '<div class="print-box"><h2>Μισθολογική κατάταξη</h2>' +
          '<div><strong>Κατηγορία:</strong> ' + escapeHtml(result.category) + '</div>' +
          '<div><strong>Τελικό Μ.Κ.:</strong> Μ.Κ. ' + escapeHtml(result.finalMK) + '</div>' +
          '<div><strong>Αναγνωρισμένη υπηρεσία:</strong> ' + escapeHtml(formatServiceMonths(totalService)) + '</div>' +
          '<div><strong>Αφαιρούμενος χρόνος 2016–2017:</strong> ' + escapeHtml(formatServiceMonths(result.suspendedServiceMonths)) + '</div>' +
          '<div><strong>Μετρήσιμος χρόνος:</strong> ' + escapeHtml(formatServiceMonths(result.countableServiceMonths)) + '</div>' +
          '<div><strong>Τίτλος / προώθηση:</strong> ' + escapeHtml(qualificationText) + '</div>' +
        '</div>' +
        '<div class="print-box"><h2>Παράμετροι μισθοδοσίας</h2>' +
          '<div><strong>Προφίλ:</strong> ' + escapeHtml(net.profileLabel) + '</div>' +
          '<div><strong>Ασφαλιστική ιδιότητα:</strong> ' + escapeHtml(net.insuredStatusLabel) + '</div>' +
          '<div><strong>Βάσεις εισφορών:</strong> ' + escapeHtml(net.insuranceBasesLabel) + '</div>' +
          '<div><strong>Ηλικιακή κατηγορία:</strong> ' + escapeHtml(net.ageGroupLabel) + '</div>' +
          '<div><strong>Εξαρτώμενα τέκνα:</strong> ' + escapeHtml(net.children) + '</div>' +
          '<div><strong>Αναπηρία — φορολογία:</strong> ' + escapeHtml(net.disabilityTaxTreatmentLabel) + '</div>' +
          '<div><strong>Μείωση μητρότητας:</strong> ' + (net.maternityPensionReduction ? 'Ναι' : 'Όχι') + '</div>' +
          '<div><strong>Ισχύς βασικού μισθού:</strong> ' + escapeHtml(result.basicSalaryEffectiveDate) + '</div>' +
        '</div>' +
      '</div>' +
      '<table><thead><tr><th>ΑΠΟΔΟΧΕΣ</th><th class="amount">Ποσό</th></tr></thead><tbody>' +
        grossRows +
        '<tr class="total-row"><td>ΣΥΝΟΛΟ ΜΙΚΤΩΝ ΑΠΟΔΟΧΩΝ</td><td class="amount">' + formatEuroCents(payroll.grossForNet) + '</td></tr>' +
      '</tbody></table>' +
      '<table><thead><tr><th>ΚΡΑΤΗΣΕΙΣ</th><th class="amount">Ποσό</th></tr></thead><tbody>' +
        deductionRows +
        '<tr class="total-row"><td>ΣΥΝΟΛΟ ΚΡΑΤΗΣΕΩΝ</td><td class="amount">' + formatEuroCents(totalDeductions) + '</td></tr>' +
      '</tbody></table>' +
      '<div class="print-grid">' +
        '<div class="print-box"><h2>Φορολογική ανάλυση</h2>' +
          '<div><strong>Μηνιαίο φορολογητέο:</strong> ' + formatEuroCents(net.taxableMonthly) + '</div>' +
          '<div><strong>Ετήσιο φορολογητέο (12μηνο):</strong> ' + formatEuroCents(net.taxableAnnual) + '</div>' +
          '<div><strong>Φόρος κλίμακας πριν τη μείωση:</strong> ' + formatEuroCents(net.taxBeforeCredit) + '</div>' +
          '<div><strong>Μείωση φόρου άρθρου 16 ΚΦΕ:</strong> −' + formatEuroCents(net.taxCredit) + '</div>' +
          '<div><strong>Μείωση / απαλλαγή λόγω αναπηρίας:</strong> −' + formatEuroCents(net.disabilityTaxRelief) + '</div>' +
          '<div><strong>Ετήσιος φόρος:</strong> ' + formatEuroCents(net.annualTax) + '</div>' +
        '</div>' +
        '<div class="print-box"><h2>Σύνοψη</h2>' +
          '<div><strong>Τακτικές κρατήσεις μετά τυχόν μείωση:</strong> ' + formatEuroCents(net.standardDeductions) + '</div>' +
          '<div><strong>Λοιπές κρατήσεις:</strong> ' + formatEuroCents(net.otherDeductions) + '</div>' +
          '<div><strong>Μηνιαία παρακράτηση φόρου:</strong> ' + formatEuroCents(net.monthlyTax) + '</div>' +
        '</div>' +
      '</div>' +
      '<div class="print-net"><span>ΕΚΤΙΜΩΜΕΝΟ ΠΛΗΡΩΤΕΟ</span><span>' + formatEuroCents(net.estimatedNet) + '</span></div>' ;

    const stamp = byId('payrollPrintGeneratedAt');
    if (stamp) {
      try {
        stamp.textContent = 'Παραγωγή: ' + new Intl.DateTimeFormat('el-GR', { dateStyle: 'short', timeStyle: 'short' }).format(new Date());
      } catch (e) {
        stamp.textContent = 'Παραγωγή: ' + new Date().toLocaleString();
      }
    }
  }

  function clampBoundedIntegerInput(el, max) {
    if (!el || el.value === '') return;
    const n = Number(el.value);
    if (!Number.isFinite(n)) {
      el.value = '';
      return;
    }
    el.value = String(Math.max(0, Math.min(max, Math.floor(n))));
  }

  function calculate() {
    const result = window.EducationSalaryScale.calculate({
      category: byId('category').value,
      years: integer('serviceYears', 50),
      months: integer('serviceMonths', 11),
      qualification: byId('qualification').value,
      suspendedYears: integer('suspendedYears', 2),
      suspendedMonths: integer('suspendedMonths', 11)
    });

    byId('finalMkResult').textContent = 'Μ.Κ. ' + result.finalMK;
    const categoryLabels = { PE: 'ΠΕ', TE: 'ΤΕ', DE: 'ΔΕ', YE: 'ΥΕ' };
    byId('categoryResult').textContent = categoryLabels[result.categoryCode] || categoryLabels[byId('category').value] || result.category || 'ΠΕ';
    byId('basicSalaryResult').textContent = formatEuro(result.basicGrossSalary);

    const children = integer('dependentChildren', 20);
    const familyAllowance = window.EducationSalaryNet.familyAllowanceMonthly(children);
    const positionKey = byId('positionAllowance').value;
    const positionAllowance = window.EducationSalaryNet.positionAllowanceMonthly(positionKey);
    const remoteAllowance = byId('remoteAreaAllowance').checked
      ? window.EducationSalaryNet.REMOTE_AREA_ALLOWANCE_MONTHLY
      : 0;
    const grossForNet = result.basicGrossSalary + familyAllowance + positionAllowance + remoteAllowance;
    const otherDeductionParts = [
      ['ΑΔΕΔΥ', money('adedYDeduction')],
      ['ΟΛΜΕ / ΔΟΕ', money('federationDeduction')],
      ['Σύλλογος', money('associationDeduction')],
      ['Άλλο ποσό', money('otherPayrollDeduction')]
    ];
    const otherDeductions = otherDeductionParts.reduce((sum, item) => sum + item[1], 0);
    const net = window.EducationSalaryNet.calculate({
      grossMonthly: grossForNet,
      basicMonthly: result.basicGrossSalary,
      familyAllowanceMonthly: familyAllowance,
      positionAllowanceMonthly: positionAllowance,
      remoteAllowanceMonthly: remoteAllowance,
      profile: byId('payrollProfile').value,
      insuredStatus: byId('insuredStatus').value,
      ageGroup: byId('ageGroup').value,
      children: children,
      disabilityTaxTreatment: byId('disabilityTaxTreatment').value,
      otherDeductions: otherDeductions,
      maternityPensionReduction: byId('maternityPensionReduction').checked
    });
    byId('familyAllowanceResult').textContent = formatEuroCents(familyAllowance);
    byId('positionAllowanceResult').textContent = formatEuroCents(positionAllowance);
    byId('remoteAllowanceResult').textContent = formatEuroCents(remoteAllowance);
    byId('grossForNetResult').textContent = formatEuroCents(grossForNet);
    byId('payrollProfileResult').textContent = net.profileLabel;
    const insuredStatusSelect = byId('insuredStatus');
    insuredStatusSelect.disabled = !net.insuredStatusApplies;
    insuredStatusSelect.setAttribute('aria-disabled', net.insuredStatusApplies ? 'false' : 'true');
    byId('insuredStatusResult').textContent = net.insuredStatusLabel;
    byId('insuranceBasesResult').textContent = net.insuranceBasesLabel;
    byId('standardDeductionsResult').textContent = formatEuroCents(net.standardDeductions) + ' (≈ ' + formatPercent(net.standardDeductionRate) + ' επί μικτών)';
    byId('maternityPensionReductionResult').textContent = net.maternityPensionReductionAmount > 0
      ? '−' + formatEuroCents(net.maternityPensionReductionAmount) + ' (50% κύριας σύνταξης · βάση ' + formatEuroCents(net.maternityPensionContributionBase) + ')'
      : '0,00 €';
    byId('deductionBreakdownResult').textContent = net.deductionBreakdown;
    renderDeductionDetails(net);
    byId('registrationDeductionResult').textContent = net.registrationDeduction > 0
      ? formatEuroCents(net.registrationDeduction) + ' (1/12 μισθού)'
      : '0,00 €';
    byId('otherDeductionsResult').textContent = formatEuroCents(net.otherDeductions);
    const activeOtherDeductions = otherDeductionParts.filter(item => item[1] > 0);
    byId('otherDeductionsBreakdownResult').textContent = activeOtherDeductions.length
      ? activeOtherDeductions.map(item => item[0] + ' ' + formatEuroCents(item[1])).join(' · ')
      : '—';
    byId('taxableAnnualResult').textContent = formatEuroCents(net.taxableAnnual);
    byId('taxBeforeCreditResult').textContent = formatEuroCents(net.taxBeforeCredit);
    byId('taxCreditResult').textContent = net.taxCredit > 0 ? '−' + formatEuroCents(net.taxCredit) : '0,00 €';
    byId('disabilityTaxReliefResult').textContent = net.disabilityTaxRelief > 0
      ? '−' + formatEuroCents(net.disabilityTaxRelief) + (net.salaryTaxExemptDueToDisability ? ' (πλήρης απαλλαγή)' : '')
      : '0,00 €';
    byId('annualTaxResult').textContent = formatEuroCents(net.annualTax);
    byId('monthlyTaxResult').textContent = formatEuroCents(net.monthlyTax);
    byId('estimatedNetResult').textContent = formatEuroCents(net.estimatedNet);

    // Keep the on-screen summary compact: zero-value rows stay available in the print sheet,
    // but are omitted from the right-hand results panel until they become relevant.
    setResultRowVisible('suspendedServiceResult', result.suspendedServiceMonths > 0);
    setResultRowVisible('countableServiceResult', result.countableServiceMonths > 0);
    setResultRowVisible('promotionResult', result.promotionMK > 0);
    setResultRowVisible('familyAllowanceResult', familyAllowance > 0);
    setResultRowVisible('positionAllowanceResult', positionAllowance > 0);
    setResultRowVisible('remoteAllowanceResult', remoteAllowance > 0);
    setResultRowVisible('maternityPensionReductionResult', net.maternityPensionReductionAmount > 0);
    setResultRowVisible('registrationDeductionResult', net.registrationDeduction > 0);
    setResultRowVisible('otherDeductionsResult', net.otherDeductions > 0);
    setResultRowVisible('otherDeductionsBreakdownResult', activeOtherDeductions.length > 0);
    setResultRowVisible('taxBeforeCreditResult', net.taxBeforeCredit > 0);
    setResultRowVisible('taxCreditResult', net.taxCredit > 0);
    setResultRowVisible('disabilityTaxReliefResult', net.disabilityTaxRelief > 0);
    setResultRowVisible('annualTaxResult', net.annualTax > 0);
    setResultRowVisible('monthlyTaxResult', net.monthlyTax > 0);

    renderPrintSheet(result, net, {
      familyAllowance: familyAllowance,
      positionAllowance: positionAllowance,
      positionLabel: window.EducationSalaryNet.positionAllowanceLabel(positionKey),
      remoteAllowance: remoteAllowance,
      grossForNet: grossForNet,
      otherDeductionParts: otherDeductionParts.map(function (item) {
        return [item[0], window.EducationSalaryNet.roundMoney(item[1])];
      })
    });

    byId('suspendedServiceResult').textContent = formatServiceMonths(result.suspendedServiceMonths);
    byId('countableServiceResult').textContent = formatServiceMonths(result.countableServiceMonths);
    byId('baseMkResult').textContent = 'Μ.Κ. ' + result.baseMK;
    byId('promotionResult').textContent = result.promotionMK + ' Μ.Κ.';
    byId('nextMkResult').textContent = result.capped ? 'Καταληκτικό Μ.Κ.' : result.monthsToNext + ' μήνες';

    const status = byId('statusResult');
    if (result.suspendedServiceAdjusted) {
      status.textContent = 'Ο δηλωμένος χρόνος της διετίας 2016–2017 υπερέβαινε τη συνολική υπηρεσία και περιορίστηκε στον διαθέσιμο χρόνο.';
      status.className = 'result-message edu-message result-message--warning edu-message--warning';
    } else if (result.promotionCapped) {
      status.textContent = 'Η προώθηση περιορίζεται στο καταληκτικό Μ.Κ. ' + result.maxMK + ' της κατηγορίας.';
      status.className = 'result-message edu-message result-message--warning edu-message--warning';
    } else if (result.qualification !== 'none') {
      status.textContent = result.qualificationLabel + '. Ο χρόνος 2016–2017 που δηλώθηκε έχει αφαιρεθεί από τη μισθολογική εξέλιξη. Χρησιμοποίησε την επιλογή τίτλου μόνο αν η προώθηση έχει αναγνωριστεί υπηρεσιακά.';
      status.className = 'result-message edu-message result-message--success edu-message--success';
    } else {
      status.textContent = result.suspendedServiceMonths > 0
        ? 'Ο χρόνος 2016–2017 αφαιρέθηκε. Ο υπολογισμός γίνεται με τον υπόλοιπο μισθολογικά μετρήσιμο χρόνο.'
        : 'Υπολογισμός με βάση τον αναγνωρισμένο μισθολογικό χρόνο.';
      status.className = 'result-message edu-message result-message--status edu-message--status';
    }
  }

  function formatEuro(value) {
    const amount = Math.max(0, Math.round(Number(value) || 0));
    try {
      return new Intl.NumberFormat('el-GR', { maximumFractionDigits: 0 }).format(amount) + ' €';
    } catch (e) {
      return String(amount).replace(/\B(?=(\d{3})+(?!\d))/g, '.') + ' €';
    }
  }

  function formatEuroCents(value) {
    const amount = Math.max(0, Number(value) || 0);
    try {
      return new Intl.NumberFormat('el-GR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(amount) + ' €';
    } catch (e) {
      return amount.toFixed(2).replace('.', ',') + ' €';
    }
  }

  function formatPercent(value) {
    const percentage = Math.max(0, Number(value) || 0) * 100;
    return percentage.toFixed(2).replace('.', ',') + '%';
  }

  function formatServiceMonths(totalMonths) {
    const total = Math.max(0, Math.floor(Number(totalMonths) || 0));
    const years = Math.floor(total / 12);
    const months = total % 12;
    if (years && months) return years + ' έτη ' + months + ' μήνες';
    if (years) return years + (years === 1 ? ' έτος' : ' έτη');
    return months + (months === 1 ? ' μήνας' : ' μήνες');
  }

  function clampSuspendedInputs() {
    const yearsEl = byId('suspendedYears');
    const monthsEl = byId('suspendedMonths');
    clampBoundedIntegerInput(yearsEl, 2);
    clampBoundedIntegerInput(monthsEl, 11);
    if (Number(yearsEl.value) >= 2 && Number(monthsEl.value) > 0) monthsEl.value = '0';
  }

  function reset() {
    byId('category').value = 'PE';
    byId('serviceYears').value = '0';
    byId('serviceMonths').value = '0';
    byId('suspendedYears').value = '0';
    byId('suspendedMonths').value = '0';
    byId('qualification').value = 'none';
    byId('payrollProfile').value = 'permanent';
    byId('insuredStatus').value = 'new_efka';
    byId('insuredStatus').disabled = false;
    byId('insuredStatus').setAttribute('aria-disabled', 'false');
    byId('ageGroup').value = 'over30';
    byId('disabilityTaxTreatment').value = 'none';
    byId('dependentChildren').value = '0';
    byId('positionAllowance').value = 'none';
    byId('remoteAreaAllowance').checked = false;
    byId('maternityPensionReduction').checked = false;
    byId('adedYDeduction').value = '0';
    byId('federationDeduction').value = '0';
    byId('associationDeduction').value = '0';
    byId('otherPayrollDeduction').value = '0';
    calculate();
  }

  function init() {
    document.querySelectorAll('input, select').forEach(el => {
      el.addEventListener('input', () => {
        if (el.id === 'serviceYears') clampBoundedIntegerInput(el, 50);
        if (el.id === 'serviceMonths') clampBoundedIntegerInput(el, 11);
        if (el.id === 'dependentChildren') clampBoundedIntegerInput(el, 20);
        if (el.id === 'suspendedYears' || el.id === 'suspendedMonths') clampSuspendedInputs();
        calculate();
      });
      el.addEventListener('change', () => {
        if (el.id === 'serviceYears') clampBoundedIntegerInput(el, 50);
        if (el.id === 'serviceMonths') clampBoundedIntegerInput(el, 11);
        if (el.id === 'dependentChildren') clampBoundedIntegerInput(el, 20);
        if (el.id === 'suspendedYears' || el.id === 'suspendedMonths') clampSuspendedInputs();
        calculate();
      });
    });
    const deductionBreakdownToggle = byId('deductionBreakdownToggle');
    if (deductionBreakdownToggle) {
      deductionBreakdownToggle.addEventListener('click', function () {
        const details = byId('deductionBreakdownDetails');
        if (!details) return;
        const expanded = deductionBreakdownToggle.getAttribute('aria-expanded') === 'true';
        deductionBreakdownToggle.setAttribute('aria-expanded', expanded ? 'false' : 'true');
        details.hidden = expanded;
      });
    }
    byId('printBtn').addEventListener('click', function () {
      calculate();
      window.print();
    });
    byId('resetBtn').addEventListener('click', reset);
    calculate();
  }

  global.EducationSalaryUI = Object.freeze({
    init: init,
    calculate: calculate,
    reset: reset
  });
})(window);
