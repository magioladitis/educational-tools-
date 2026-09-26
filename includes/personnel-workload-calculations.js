/*
 * Browser-side pure calculations for includes/personnel-workload.php.
 *
 * Phase 1 intentionally covers person normalization / compulsory-hours rules
 * only. Allocation slots, roster planning and the optimizer remain server-side
 * reference implementations until their own parity contracts are complete.
 */
(function (global) {
  'use strict';

  var ALLOWED_BRANCHES = ['PE', 'TE01', 'DE01_ARCH', 'DE01_TECH'];
  var ALLOWED_ROLES = ['teacher', 'director', 'lab_director', 'vice_or_sector', 'lab_responsible', 'epal_ek_lab_sector'];

  function canonicalSpecialtyCode(value) {
    if (global.EducationCore && typeof global.EducationCore.normalizeSpecialtyCode === 'function') {
      return global.EducationCore.normalizeSpecialtyCode(value);
    }
    var code = String(value == null ? '' : value).trim().toUpperCase().replace(/\s+/g, '');
    if (!code) return '';
    code = code.replace(/^(?:PE|PΕ|ΠE|ΠΕ)/, 'ΠΕ');
    code = code.replace(/^(?:TE|TΕ|ΤE|ΤΕ)/, 'ΤΕ');
    code = code.replace(/^(?:DE|DΕ|ΔE|ΔΕ)/, 'ΔΕ');
    return code;
  }

  function nonNegativeInt(value, max) {
    var n = Math.floor(Number(value) || 0);
    if (n < 0) n = 0;
    if (max !== undefined && max !== null && n > Number(max)) n = Math.floor(Number(max));
    return n;
  }

  function serviceDays(years, months, days) {
    return nonNegativeInt(years, 50) * 360 + nonNegativeInt(months, 11) * 30 + nonNegativeInt(days, 29);
  }

  function serviceLabel(totalServiceDays) {
    var total = Math.max(0, Math.floor(Number(totalServiceDays) || 0));
    var years = Math.floor(total / 360);
    var remainder = total % 360;
    var months = Math.floor(remainder / 30);
    var days = remainder % 30;
    var label = years + ' έτη';
    if (months) label += ' και ' + months + ' μήν.';
    if (days) label += ' και ' + days + ' ημ.';
    return label;
  }

  function specialtyKnown(code, options) {
    options = options || {};
    if (!code) return false;
    if (typeof options.isKnownSpecialty === 'function') return !!options.isKnownSpecialty(code);
    if (options.specialtyLabels && Object.prototype.hasOwnProperty.call(options.specialtyLabels, code)) return true;
    if (options.assumeKnownSpecialty === true) return /^(?:ΠΕ|ΤΕ|ΔΕ)/.test(code);
    // Browser forms only expose registry-backed specialty options. The strict
    // mode remains available to tests/callers by supplying specialtyLabels or
    // isKnownSpecialty.
    return /^(?:ΠΕ|ΤΕ|ΔΕ)/.test(code);
  }

  function specialtyLabel(code, options) {
    options = options || {};
    if (typeof options.specialtyLabel === 'function') return String(options.specialtyLabel(code) || '');
    if (options.specialtyLabels && Object.prototype.hasOwnProperty.call(options.specialtyLabels, code)) {
      return String(options.specialtyLabels[code] || '');
    }
    return code;
  }

  function hoursBranchForSpecialty(specialtyCode, explicitBranch) {
    if (explicitBranch !== undefined && explicitBranch !== null && explicitBranch !== '') {
      explicitBranch = String(explicitBranch);
      return ALLOWED_BRANCHES.indexOf(explicitBranch) >= 0
        ? { status: 'resolved', branch: explicitBranch, mode: 'explicit' }
        : { status: 'invalid', branch: null, mode: 'explicit', reason: 'unknown_hours_branch' };
    }

    var code = canonicalSpecialtyCode(specialtyCode);
    if (code.indexOf('ΠΕ') === 0) return { status: 'resolved', branch: 'PE', mode: 'inferred_from_specialty' };
    if (code.indexOf('ΤΕ') === 0) return { status: 'resolved', branch: 'TE01', mode: 'inferred_from_specialty' };
    if (code.indexOf('ΔΕ') === 0) {
      return {
        status: 'needs_input', branch: null, mode: 'not_safely_inferred',
        reason: 'de_hours_scale_requires_explicit_architect_or_technician'
      };
    }
    return { status: 'invalid', branch: null, mode: 'not_safely_inferred', reason: 'unsupported_specialty_for_secondary_hours' };
  }

  function directorSectionsBandFromCount(sectionCount) {
    var count = Math.max(0, Math.floor(Number(sectionCount) || 0));
    if (count < 3) return null;
    if (count <= 5) return '3-5';
    if (count <= 9) return '6-9';
    if (count <= 12) return '10-12';
    return '13+';
  }

  function secondaryTeacherBaseHours(branch, totalServiceDays) {
    var days = Math.max(0, Math.floor(Number(totalServiceDays) || 0));
    if (branch === 'PE') {
      if (days <= 6 * 360) return { hours: 23, label: 'έως 6 έτη' };
      if (days <= 12 * 360) return { hours: 21, label: 'πάνω από 6 έως 12 έτη' };
      if (days < 20 * 360) return { hours: 20, label: 'πάνω από 12 έως κάτω από 20 έτη' };
      return { hours: 18, label: '20 έτη και άνω' };
    }
    if (branch === 'TE01') {
      if (days <= 7 * 360) return { hours: 24, label: 'έως 7 έτη' };
      if (days <= 13 * 360) return { hours: 21, label: 'πάνω από 7 έως 13 έτη' };
      if (days < 20 * 360) return { hours: 20, label: 'πάνω από 13 έως κάτω από 20 έτη' };
      return { hours: 18, label: '20 έτη και άνω' };
    }
    if (branch === 'DE01_ARCH') return days < 20 * 360 ? { hours: 28, label: 'κάτω από 20 έτη' } : { hours: 26, label: '20 έτη και άνω' };
    if (branch === 'DE01_TECH') return days < 20 * 360 ? { hours: 30, label: 'κάτω από 20 έτη' } : { hours: 28, label: '20 έτη και άνω' };
    return null;
  }

  function hasOwn(obj, key) {
    return !!obj && Object.prototype.hasOwnProperty.call(obj, key);
  }

  function integerHours(raw) {
    var text = String(raw == null ? '' : raw).trim();
    if (!/^\d+$/.test(text)) return null;
    return parseInt(text, 10);
  }

  function secondaryObligation(person, options) {
    person = person || {};
    options = options || {};
    var specialty = canonicalSpecialtyCode(person.specialty_code);
    if (!specialty || !specialtyKnown(specialty, options)) {
      return { status: 'invalid', valid: false, reason: 'unknown_specialty_code' };
    }

    var role = person.role == null ? 'teacher' : String(person.role);
    if (ALLOWED_ROLES.indexOf(role) < 0) return { status: 'invalid', valid: false, reason: 'unknown_secondary_role' };

    var service = person.service && typeof person.service === 'object' ? person.service : {};
    var totalDays = serviceDays(service.years || 0, service.months || 0, service.days || 0);
    var twentyYears = totalDays >= 20 * 360;
    var baseCommon = {
      specialty_code: specialty,
      service_days: totalDays,
      service_label: serviceLabel(totalDays)
    };
    var obligationSource = String(person.obligation_source == null ? '' : person.obligation_source).trim();

    if (obligationSource === 'myschool_stat4_8' && hasOwn(person, 'required_teaching_hours')) {
      var sourceHours = integerHours(person.required_teaching_hours);
      if (sourceHours === null || sourceHours < 1 || sourceHours > 35) {
        return Object.assign({ status: 'invalid', valid: false, reason: 'required_teaching_hours_invalid' }, baseCommon);
      }
      if (specialty.indexOf('ΠΕ') === 0 && sourceHours > 23) {
        return Object.assign({ status: 'invalid', valid: false, reason: 'required_teaching_hours_exceeds_pe_max' }, baseCommon);
      }
      var base = hasOwn(person, 'source_base_required_hours') ? nonNegativeInt(person.source_base_required_hours) : null;
      var reduction = hasOwn(person, 'source_reduction_hours') ? nonNegativeInt(person.source_reduction_hours) : null;
      var atUnit = hasOwn(person, 'source_hours_at_unit') ? nonNegativeInt(person.source_hours_at_unit) : null;
      var sourceRule = 'Υ.Ω. από myschool stat4_8.';
      if (base !== null && reduction !== null) sourceRule += ' Βάση ' + base + ' − μείωση ' + reduction + ' = ' + sourceHours + ' ώρες.';
      if (atUnit !== null) sourceRule += ' Ώρες Υ.Ω. στον φορέα: ' + atUnit + '.';
      return Object.assign({
        status: 'resolved', valid: true, specialty_code: specialty, specialty_label: specialtyLabel(specialty, options),
        role: role, hours_branch: null, hours_branch_mode: 'myschool_source_required_hours',
        service_days: totalDays, service_label: serviceLabel(totalDays), required_teaching_hours: sourceHours,
        obligation_source: obligationSource, source_base_required_hours: base, source_reduction_hours: reduction,
        source_hours_at_unit: atUnit, rule: sourceRule
      });
    }

    if (role === 'teacher' && hasOwn(person, 'required_teaching_hours')) {
      var manualText = String(person.required_teaching_hours == null ? '' : person.required_teaching_hours).trim();
      if (manualText === '') return Object.assign({ status: 'needs_input', valid: false, reason: 'required_teaching_hours_required' }, baseCommon);
      var manualHours = integerHours(manualText);
      if (manualHours === null || manualHours < 1 || manualHours > 35) {
        return Object.assign({ status: 'invalid', valid: false, reason: 'required_teaching_hours_invalid' }, baseCommon);
      }
      if (specialty.indexOf('ΠΕ') === 0 && manualHours > 23) {
        return Object.assign({ status: 'invalid', valid: false, reason: 'required_teaching_hours_exceeds_pe_max' }, baseCommon);
      }
      return {
        status: 'resolved', valid: true, specialty_code: specialty, specialty_label: specialtyLabel(specialty, options),
        role: role, hours_branch: null, hours_branch_mode: 'manual_required_hours', service_days: totalDays,
        service_label: serviceLabel(totalDays), required_teaching_hours: manualHours,
        rule: 'Το υποχρεωτικό διδακτικό ωράριο δηλώθηκε από τον χρήστη.'
      };
    }

    if (role === 'director' || role === 'vice_or_sector') {
      var managementHours;
      var managementRule;
      var result;
      if (role === 'director') {
        var hasSectionCount = hasOwn(person, 'school_general_section_count');
        var sectionCount = hasSectionCount ? Math.max(0, Math.floor(Number(person.school_general_section_count) || 0)) : null;
        var sections = sectionCount !== null ? directorSectionsBandFromCount(sectionCount) : String(person.director_sections_band || '');
        var bases = { '3-5': 10, '6-9': 9, '10-12': 7, '13+': 5 };
        if (!hasOwn(bases, sections)) return { status: 'needs_input', valid: false, reason: 'director_sections_band_required' };
        managementHours = bases[sections] - (twentyYears ? 2 : 0);
        managementRule = 'Διευθυντής/ντρια Γυμνασίου/Λυκείου — ' + (sectionCount !== null ? sectionCount + ' κανονικά τμήματα, ' : '') + 'κλίμακα ' + sections + (twentyYears ? ', με συμπληρωμένα 20 έτη.' : '.');
        result = {
          status: 'resolved', valid: true, specialty_code: specialty, specialty_label: specialtyLabel(specialty, options), role: role,
          hours_branch: null, hours_branch_mode: 'role_specific', service_days: totalDays, service_label: serviceLabel(totalDays),
          required_teaching_hours: managementHours, rule: managementRule, director_sections_band: sections
        };
        if (sectionCount !== null) result.school_general_section_count = sectionCount;
        return result;
      }
      managementHours = twentyYears ? 14 : 16;
      managementRule = 'Υποδιευθυντής/ντρια ή Υπεύθυνος/η Τομέα' + (twentyYears ? ' με συμπληρωμένα 20 έτη.' : '.');
      return {
        status: 'resolved', valid: true, specialty_code: specialty, specialty_label: specialtyLabel(specialty, options), role: role,
        hours_branch: null, hours_branch_mode: 'role_specific', service_days: totalDays, service_label: serviceLabel(totalDays),
        required_teaching_hours: managementHours, rule: managementRule
      };
    }

    var branchResolution = hoursBranchForSpecialty(specialty, person.hours_branch);
    if (branchResolution.status !== 'resolved') {
      return {
        status: branchResolution.status, valid: false, reason: branchResolution.reason, specialty_code: specialty,
        service_days: totalDays, service_label: serviceLabel(totalDays), hours_branch_resolution: branchResolution
      };
    }
    var branch = branchResolution.branch;
    var band = secondaryTeacherBaseHours(branch, totalDays);
    if (!band) return { status: 'invalid', valid: false, reason: 'unsupported_hours_branch' };

    var hours;
    var rule;
    var extra = {};
    if (role === 'lab_director') {
      hours = twentyYears ? 8 : 10;
      rule = 'Διευθυντής/ντρια Εργαστηριακού Κέντρου' + (twentyYears ? ' με συμπληρωμένα 20 έτη.' : '.');
    } else if (role === 'lab_responsible') {
      var limit = twentyYears ? 18 : 20;
      hours = Math.min(band.hours, limit);
      rule = 'Υπεύθυνος/η Εργαστηρίου: έως ' + limit + ' ώρες, με εφαρμογή του μικρότερου ατομικού ωραρίου.';
      extra.base_teacher_hours = band.hours;
      extra.role_limit = limit;
    } else if (role === 'epal_ek_lab_sector') {
      hours = Math.max(18, band.hours - 2);
      rule = 'Υπεύθυνος/η εργαστηρίου τομέα ή ειδικότητας Ε.Κ./ΕΠΑ.Λ.: μείωση 2 ωρών, με κατώτερο όριο 18 ώρες.';
      extra.base_teacher_hours = band.hours;
    } else {
      hours = band.hours;
      rule = 'Εκπαιδευτικός — ' + band.label + '.';
    }
    return Object.assign({
      status: 'resolved', valid: true, specialty_code: specialty, specialty_label: specialtyLabel(specialty, options), role: role,
      hours_branch: branch, hours_branch_mode: branchResolution.mode, service_days: totalDays, service_label: serviceLabel(totalDays),
      required_teaching_hours: hours, rule: rule
    }, extra);
  }

  function normalizePerson(person, options) {
    person = person || {};
    var id = String(person.person_id == null ? '' : person.person_id).trim();
    if (!id) return { status: 'invalid', reason: 'person_id_required' };
    var specialty = canonicalSpecialtyCode(person.specialty_code);
    var secondarySpecialty = canonicalSpecialtyCode(person.secondary_specialty_code);
    var obligation = secondaryObligation(person, options);
    var external = nonNegativeInt(person.assigned_external_hours || 0);
    var result = {
      status: obligation.status,
      person_id: id,
      display_name: String(person.display_name == null ? '' : person.display_name).trim(),
      specialty_code: specialty,
      specialty_label: specialtyLabel(specialty, options),
      secondary_specialty_code: secondarySpecialty,
      secondary_specialty_label: secondarySpecialty ? specialtyLabel(secondarySpecialty, options) : '',
      assigned_external_hours: external,
      obligation: obligation
    };
    if (!obligation.valid) {
      result.reason = obligation.reason || 'obligation_unresolved';
      return result;
    }
    var required = Math.floor(Number(obligation.required_teaching_hours) || 0);
    result.required_teaching_hours = required;
    result.remaining_before_profile_hours = Math.max(0, required - external);
    result.external_overage_hours = Math.max(0, external - required);
    return result;
  }

  global.PersonnelWorkloadCalculations = Object.freeze({
    canonicalSpecialtyCode: canonicalSpecialtyCode,
    nonNegativeInt: nonNegativeInt,
    serviceDays: serviceDays,
    serviceLabel: serviceLabel,
    hoursBranchForSpecialty: hoursBranchForSpecialty,
    directorSectionsBandFromCount: directorSectionsBandFromCount,
    secondaryTeacherBaseHours: secondaryTeacherBaseHours,
    secondaryObligation: secondaryObligation,
    normalizePerson: normalizePerson
  });
})(typeof window !== 'undefined' ? window : globalThis);
