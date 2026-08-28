(function (root, factory) {
  'use strict';
  var api = factory();
  if (typeof module === 'object' && module.exports) module.exports = api;
  root.EducationTransfer = api;
}(typeof globalThis !== 'undefined' ? globalThis : this, function () {
  'use strict';

  var CATEGORY_POINTS = {
    A: 1, B: 2, G: 3, D: 4, E: 5, ST: 6, Z: 7,
    H: 8, TH: 9, I: 10, IA: 11, IB: 12, IG: 14
  };

  var REMOTE_DOUBLE_CATEGORIES = { I: true, IA: true, IB: true, IG: true };

  function number(value, fallback) {
    var n = Number(value);
    return Number.isFinite(n) ? n : (fallback || 0);
  }

  function bounded(value, min, max) {
    var n = number(value, min);
    return Math.min(max, Math.max(min, n));
  }

  function integer(value, min, max) {
    return Math.floor(bounded(value, min, max));
  }

  function truncate2(value) {
    var n = number(value, 0);
    return Math.trunc((n + Number.EPSILON) * 100) / 100;
  }

  function normalizeDuration(years, months, days) {
    var y = integer(years, 0, 60);
    var m = integer(months, 0, 11);
    var d = integer(days, 0, 29);
    if (d >= 15) m += 1;
    y += Math.floor(m / 12);
    m = m % 12;
    return {
      years: y,
      months: m,
      ignoredDays: d < 15 ? d : 0,
      roundedDays: d >= 15 ? d : 0,
      totalMonths: y * 12 + m
    };
  }

  function servicePoints(years, months, days) {
    var duration = normalizeDuration(years, months, days);
    return {
      duration: duration,
      points: truncate2(duration.totalMonths * 2.5 / 12)
    };
  }

  function childPoints(count) {
    var n = integer(count, 0, 20);
    var total = 0;
    var i;
    for (i = 1; i <= n; i += 1) {
      if (i <= 2) total += 4;
      else if (i === 3) total += 6;
      else total += 7;
    }
    return total;
  }

  function familyPoints(eligibleFamilyStatus, eligibleChildren) {
    return (eligibleFamilyStatus ? 4 : 0) + childPoints(eligibleChildren);
  }

  function baseAnnualForPeriod(period) {
    var type = period.type || 'school';
    if (type === 'abroad_europe') return 1;
    if (type === 'abroad_america') return 2;
    if (type === 'abroad_other') return 3;
    if (type === 'study_leave') return 1;
    return CATEGORY_POINTS[period.category] || 0;
  }

  function bonusAnnualForPeriod(period) {
    var type = period.type || 'school';
    if (type === 'prison') return 5;
    if (type === 'digital_tutoring') return 6;
    if (type === 'listed_service_bonus2') return 2;
    return 0;
  }

  function canRemoteDouble(period) {
    return (period.type || 'school') === 'school' && !!REMOTE_DOUBLE_CATEGORIES[period.category];
  }

  function isFixedFullWeekType(type) {
    return /^abroad_/.test(type || '') || type === 'study_leave';
  }

  function calculateMsdPeriod(period) {
    period = period || {};
    var duration = normalizeDuration(period.years, period.months, period.days);
    var daysPerWeek = isFixedFullWeekType(period.type) ? 5 : bounded(period.daysPerWeek == null ? 5 : period.daysPerWeek, 0, 5);
    var baseAnnual = baseAnnualForPeriod(period);
    var bonusAnnual = bonusAnnualForPeriod(period);
    var remoteDouble = !!period.remoteDouble && canRemoteDouble(period);
    var baseMultiplier = remoteDouble ? 2 : 1;
    var proportion = daysPerWeek / 5;
    var yearFraction = duration.totalMonths / 12;
    var basePoints = baseAnnual * baseMultiplier * proportion * yearFraction;
    var bonusPoints = bonusAnnual * proportion * yearFraction;
    var points = truncate2(basePoints + bonusPoints);

    return {
      duration: duration,
      daysPerWeek: daysPerWeek,
      baseAnnual: baseAnnual,
      bonusAnnual: bonusAnnual,
      remoteDouble: remoteDouble,
      annualEquivalent: baseAnnual * baseMultiplier + bonusAnnual,
      basePoints: truncate2(basePoints),
      bonusPoints: truncate2(bonusPoints),
      points: points
    };
  }

  function calculate(input) {
    input = input || {};
    var service = servicePoints(input.serviceYears, input.serviceMonths, input.serviceDays);
    var periods = Array.isArray(input.msdPeriods) ? input.msdPeriods : [];
    var msdRows = periods.map(calculateMsdPeriod);
    var msd = truncate2(msdRows.reduce(function (sum, row) { return sum + row.points; }, 0));
    var coService = input.coService ? 4 : 0;
    var family = familyPoints(!!input.familyStatusEligible, input.eligibleChildren);
    var locality = input.locality ? 4 : 0;
    var firstPreference = input.mode === 'local' ? 0 : (input.firstPreference ? 2 : 0);
    var total = truncate2(service.points + msd + coService + family + locality + firstPreference);

    return {
      total: total,
      servicePoints: service.points,
      serviceDuration: service.duration,
      msdPoints: msd,
      msdRows: msdRows,
      coServicePoints: coService,
      familyPoints: family,
      localityPoints: locality,
      firstPreferencePoints: firstPreference,
      childPoints: childPoints(input.eligibleChildren)
    };
  }

  return {
    CATEGORY_POINTS: CATEGORY_POINTS,
    REMOTE_DOUBLE_CATEGORIES: REMOTE_DOUBLE_CATEGORIES,
    truncate2: truncate2,
    normalizeDuration: normalizeDuration,
    servicePoints: servicePoints,
    childPoints: childPoints,
    familyPoints: familyPoints,
    isFixedFullWeekType: isFixedFullWeekType,
    calculateMsdPeriod: calculateMsdPeriod,
    calculate: calculate
  };
}));
