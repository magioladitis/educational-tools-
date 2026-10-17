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
    // Avoid losing a cent to IEEE-754 representation (e.g. 138.2*100
    // can be 13819.999999999998). Keep truncation, never ordinary rounding.
    var scaled = n * 100;
    return Math.trunc(scaled + Math.max(1e-9, 4 * Number.EPSILON * Math.abs(scaled))) / 100;
  }

  function normalizeDuration(years, months, days) {
    var y = integer(years, 0, 50);
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


  function parseDirectServicePoints(raw) {
    var text = String(raw == null ? '' : raw).trim().replace(',', '.');
    if (!/^\d+(?:\.\d{1,2})?$/.test(text)) return { valid: false, points: null };
    var n = Number(text);
    if (!Number.isFinite(n) || n < 0 || n > 125) return { valid: false, points: null };
    return { valid: true, points: truncate2(n) };
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

  // Εισαγωγή Μ.Σ.Δ. από το επίσημο μητρώο (δεκαδικό κόμμα ή τελεία).
  // Δεν μετατρέπουμε σιωπηρά μη έγκυρες τιμές σε μηδέν.
  function parseDirectMsd(raw) {
    var text = String(raw == null ? '' : raw).trim().replace(',', '.');
    if (!/^\d+(?:\.\d{1,2})?$/.test(text)) return { valid: false, points: null };
    var n = Number(text);
    if (!Number.isFinite(n) || n > Number.MAX_SAFE_INTEGER) return { valid: false, points: null };
    return { valid: true, points: truncate2(n) };
  }

  // Independent alternative to direct total-service entry. Durations must not
  // overlap; aggregate day remainders before applying the 15-day rule.
  function serviceFromPeriods(periods) {
    var list = Array.isArray(periods) ? periods : [];
    var totalMonths = 0;
    var totalDays = 0;
    var valid = list.length > 0;
    list.forEach(function(period) {
      period = period || {};
      [['years',50],['months',11],['days',29]].forEach(function(pair) {
        var raw = period[pair[0]];
        var numeric = Number(raw);
        if (raw === '' || raw == null || !Number.isInteger(numeric) || numeric < 0 || numeric > pair[1]) valid = false;
      });
      totalMonths += Number(period.years || 0) * 12 + Number(period.months || 0);
      totalDays += Number(period.days || 0);
    });
    totalMonths += Math.floor(totalDays / 30);
    var remainingDays = totalDays % 30;
    if (remainingDays >= 15) totalMonths += 1;
    if (totalMonths > 600) valid = false;
    return { valid: valid, duration: normalizeDuration(Math.floor(totalMonths/12),totalMonths%12,remainingDays),
      years: Math.floor(totalMonths/12), months: totalMonths%12, days: 0, totalMonths: totalMonths };
  }

  function calculate(input) {
    input = input || {};
    var serviceMode = input.serviceMode === 'periods' ? 'periods' : (input.serviceMode === 'direct' ? 'direct' : 'points');
    var detailedService = serviceMode === 'periods';
    var directDurationService = serviceMode === 'direct';
    var directPointsService = serviceMode === 'points';
    var breakdown = detailedService ? serviceFromPeriods(input.servicePeriods) : null;
    var parsedServicePoints = directPointsService ? parseDirectServicePoints(input.serviceDirectPoints) : null;
    var service = directPointsService
      ? { duration: null, points: parsedServicePoints.points }
      : (detailedService ? servicePoints(breakdown.years,breakdown.months,0) : servicePoints(input.serviceYears, input.serviceMonths, input.serviceDays));
    var serviceValid = directPointsService ? parsedServicePoints.valid : (!detailedService || breakdown.valid);
    var manualMsd = input.msdMode === 'direct';
    var periods = Array.isArray(input.msdPeriods) ? input.msdPeriods : [];
    var msdRows = manualMsd ? [] : periods.map(calculateMsdPeriod);
    var parsedMsd = manualMsd ? parseDirectMsd(input.msdDirectPoints) : null;
    var msdValid = !manualMsd || parsedMsd.valid;
    // Οι δύο τρόποι είναι εναλλακτικοί: ποτέ δεν προστίθενται μεταξύ τους.
    var msd = manualMsd ? parsedMsd.points : truncate2(msdRows.reduce(function (sum, row) { return sum + row.points; }, 0));
    var coService = input.coService ? 4 : 0;
    var family = familyPoints(!!input.familyStatusEligible, input.eligibleChildren);
    var locality = input.locality ? 4 : 0;
    var firstPreference = input.mode === 'local' ? 0 : (input.firstPreference ? 2 : 0);
    var total = msdValid && serviceValid ? truncate2(service.points + msd + coService + family + locality + firstPreference) : null;

    return {
      total: total,
      servicePoints: service.points,
      serviceDuration: service.duration,
      serviceDurationKnown: !directPointsService,
      serviceMode: serviceMode,
      serviceDirectPoints: directPointsService ? parsedServicePoints.points : null,
      serviceInputValid: serviceValid,
      msdPoints: msd,
      msdRows: msdRows,
      msdMode: manualMsd ? 'direct' : 'periods',
      msdInputValid: msdValid,
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
    parseDirectServicePoints: parseDirectServicePoints,
    serviceFromPeriods: serviceFromPeriods,
    childPoints: childPoints,
    familyPoints: familyPoints,
    isFixedFullWeekType: isFixedFullWeekType,
    calculateMsdPeriod: calculateMsdPeriod,
    parseDirectMsd: parseDirectMsd,
    calculate: calculate
  };
}));
