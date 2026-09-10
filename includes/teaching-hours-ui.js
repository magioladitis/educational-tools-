/*
 * Browser UI controller for ypologismos-didaktikou-orariou.php.
 * Calculation/business rules stay in teaching-hours-calculations.js so the
 * same engine can be reused by a future mobile client.
 */
(function (global) {
  'use strict';
  const byId = id => document.getElementById(id);
  const level = byId('level');
  const primaryFields = byId('primaryFields');
  const secondaryFields = byId('secondaryFields');
  const eepFields = byId('eepFields');
  const ebpFields = byId('ebpFields');
  const serviceInfo = byId('serviceInfo');
  const serviceYearField = byId('serviceYearsField');
  const serviceMonthField = byId('serviceMonthsField');
  const serviceDayField = byId('serviceDaysField');
  const secondaryRole = byId('secondaryRole');
  const sectionsField = byId('sectionsField');
  const branchField = byId('branchField');

  function value(id) { return byId(id).value; }
  function numberValue(id, max) {
    const n = Math.max(0, Math.floor(Number(value(id)) || 0));
    return Number.isFinite(max) ? Math.min(max, n) : n;
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

  function syncFields() {
    const isSecondary = level.value === 'secondary';
    const isEep = level.value === 'eep';
    const isEbp = level.value === 'ebp';
    primaryFields.classList.toggle('hidden', isSecondary || isEep || isEbp);
    secondaryFields.classList.toggle('hidden', !isSecondary);
    eepFields.classList.toggle('hidden', !isEep);
    ebpFields.classList.toggle('hidden', !isEbp);
    [serviceYearField, serviceMonthField, serviceDayField, serviceInfo].forEach(el => el.classList.toggle('hidden', isEbp));
    const role = secondaryRole.value;
    sectionsField.classList.toggle('hidden', !isSecondary || role !== 'director');
    branchField.classList.toggle('hidden', !isSecondary || ['director', 'lab_director', 'vice_or_sector'].includes(role));
  }

  function calculate() {
    syncFields();
    const options = {
      level: level.value,
      years: numberValue('serviceYears', 50),
      months: numberValue('serviceMonths', 11),
      days: numberValue('serviceDays', 29)
    };
    if (level.value === 'primary') {
      options.schoolType = value('schoolType');
      options.role = value('primaryRole');
      options.organicity = numberValue('organicity', 30);
    } else if (level.value === 'secondary') {
      options.role = value('secondaryRole');
      options.branch = value('hourCategory');
      options.sections = value('sections');
    }

    const result = window.EducationTeachingHours.calculate(options);
    const status = byId('statusResult');
    byId('hoursCap').textContent = ['eep', 'ebp'].includes(level.value) ? 'Υποχρεωτικό ωράριο υποστηρικτικού έργου' : 'Υποχρεωτικό διδακτικό ωράριο';
    if (!result.valid) {
      byId('hoursResult').textContent = '—';
      byId('levelResult').textContent = level.value === 'secondary' ? 'Δευτεροβάθμια' : (level.value === 'eep' ? 'ΕΕΠ' : (level.value === 'ebp' ? 'ΕΒΠ' : 'Πρωτοβάθμια'));
      byId('serviceResult').textContent = level.value === 'ebp' ? 'Δεν εφαρμόζεται' : window.EducationTeachingHours.serviceLabel(window.EducationTeachingHours.serviceMonths(options.years, options.months, options.days));
      byId('nextReductionResult').textContent = '—';
      byId('ruleResult').textContent = '—';
      status.textContent = result.error || 'Δεν είναι δυνατός ο υπολογισμός.';
      status.className = 'result-message edu-message result-message--warning edu-message--warning';
      return;
    }

    byId('hoursResult').textContent = result.hours;
    byId('levelResult').textContent = result.level === 'eep' ? 'ΕΕΠ' : (result.level === 'ebp' ? 'ΕΒΠ' : (result.level === 'secondary' ? 'Δευτεροβάθμια' : (result.schoolType === 'kindergarten' ? 'Νηπιαγωγείο' : 'Δημοτικό')));
    byId('serviceResult').textContent = result.serviceLabel;
    byId('nextReductionResult').textContent = result.nextReductionLabel || 'Δεν προβλέπεται περαιτέρω μείωση';
    byId('ruleResult').textContent = result.rule;
    status.textContent = 'Υπολογισμός σύμφωνα με τα δηλωμένα στοιχεία.';
    status.className = 'result-message edu-message result-message--success edu-message--success';
  }

  function reset() {
    level.value = 'primary';
    byId('serviceYears').value = '0';
    byId('serviceMonths').value = '0';
    byId('serviceDays').value = '0';
    byId('schoolType').value = 'primary';
    byId('primaryRole').value = 'teacher';
    byId('organicity').value = '6';
    secondaryRole.value = 'teacher';
    byId('hourCategory').value = 'PE';
    byId('sections').value = '3-5';
    calculate();
  }


  function init() {
    document.querySelectorAll('input, select').forEach(el => {
      el.addEventListener('input', () => {
        if (el.id === 'serviceYears') clampBoundedIntegerInput(el, 50);
        if (el.id === 'serviceMonths') clampBoundedIntegerInput(el, 11);
        if (el.id === 'serviceDays') clampBoundedIntegerInput(el, 29);
        calculate();
      });
      el.addEventListener('change', () => {
        if (el.id === 'serviceYears') clampBoundedIntegerInput(el, 50);
        if (el.id === 'serviceMonths') clampBoundedIntegerInput(el, 11);
        if (el.id === 'serviceDays') clampBoundedIntegerInput(el, 29);
        calculate();
      });
    });
    byId('resetBtn').addEventListener('click', reset);
      calculate();
  }

  global.EducationTeachingHoursUI = Object.freeze({
    init: init,
    calculate: calculate,
    reset: reset
  });
})(window);
