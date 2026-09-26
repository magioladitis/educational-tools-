/*
 * Browser-side pure calculations for includes/personnel-workload.php.
 *
 * Phase 1 covers person normalization / compulsory-hours rules. Phase 2 adds
 * slot validation and assignment-route parity. Phase 3A extracts the browser
 * atomic optimizer into this pure module while PHP remains the reference.
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


  function priorityForSlotCode(slot, specialtyCode) {
    var code = canonicalSpecialtyCode(specialtyCode);
    if (!code || !slot || !slot.eligible_by_priority) return null;
    var order = ['A', 'B', 'C', 'SPECIAL'];
    for (var i = 0; i < order.length; i++) {
      var priority = order[i];
      var values = Array.isArray(slot.eligible_by_priority[priority]) ? slot.eligible_by_priority[priority] : [];
      for (var j = 0; j < values.length; j++) {
        if (canonicalSpecialtyCode(values[j]) === code) return priority;
      }
    }
    return null;
  }

  function priorityRank(priority) {
    var order = { A: 1, SPECIAL: 1, B: 2, C: 3 };
    return Object.prototype.hasOwnProperty.call(order, priority) ? order[priority] : 99;
  }

  function bestAssignmentForSlot(slot, person) {
    person = person || {};
    var primary = canonicalSpecialtyCode(person.specialty_code);
    var secondary = canonicalSpecialtyCode(person.secondary_specialty_code);
    var candidates = [];
    var priority;
    if (primary) {
      priority = priorityForSlotCode(slot, primary);
      if (priority !== null) candidates.push({ priority: priority, used_specialty_code: primary, specialty_source: 'primary' });
    }
    if (secondary && secondary !== primary) {
      priority = priorityForSlotCode(slot, secondary);
      if (priority !== null) candidates.push({ priority: priority, used_specialty_code: secondary, specialty_source: 'secondary' });
    }
    if (!candidates.length) return null;
    candidates.sort(function (a, b) {
      var rank = priorityRank(a.priority) - priorityRank(b.priority);
      if (rank !== 0) return rank;
      if (a.specialty_source === b.specialty_source) return 0;
      return a.specialty_source === 'primary' ? -1 : 1;
    });
    return candidates[0];
  }

  function keyedPeople(people) {
    var index = {};
    if (Array.isArray(people)) {
      people.forEach(function (person) {
        var id = String(person && person.person_id != null ? person.person_id : '').trim();
        if (id) index[id] = person;
      });
      return index;
    }
    if (people && typeof people === 'object') {
      Object.keys(people).forEach(function (key) {
        var person = people[key];
        var id = String(person && person.person_id != null ? person.person_id : key).trim();
        if (id) index[id] = person;
      });
    }
    return index;
  }

  /*
   * Pure browser equivalent of the slot-level validation phase in
   * personnelWorkloadRosterSlotPlan(). It deliberately stops before the
   * aggregate personnel optimizer/evaluator; that remains the PHP reference
   * until Phase 3.
   */
  function validateRosterSlotAllocations(slots, people, allocations) {
    slots = slots && typeof slots === 'object' ? slots : {};
    allocations = Array.isArray(allocations) ? allocations : [];
    var peopleIndex = keyedPeople(people);
    var slotAttempted = {};
    var slotAssigned = {};
    var personAssigned = {};
    var personPriority = {};
    var personSource = {};
    Object.keys(slots).forEach(function (slotId) {
      slotAttempted[slotId] = 0;
      slotAssigned[slotId] = 0;
    });
    Object.keys(peopleIndex).forEach(function (personId) {
      personAssigned[personId] = 0;
      personPriority[personId] = { A: 0, B: 0, C: 0, SPECIAL: 0 };
      personSource[personId] = { primary: 0, secondary: 0 };
    });

    var rowResults = allocations.map(function (allocation, i) {
      allocation = allocation || {};
      var personId = String(allocation.person_id == null ? '' : allocation.person_id).trim();
      var slotId = String(allocation.slot_id == null ? '' : allocation.slot_id).trim();
      var hours = nonNegativeInt(allocation.hours);
      var row = {
        row_index: i,
        person_id: personId,
        slot_id: slotId,
        hours: hours,
        valid: true,
        errors: [],
        warnings: [],
        priority: null,
        used_specialty_code: '',
        specialty_source: ''
      };
      if (!personId || !Object.prototype.hasOwnProperty.call(peopleIndex, personId)) {
        row.valid = false; row.errors.push('unknown_person');
      }
      if (!slotId || !Object.prototype.hasOwnProperty.call(slots, slotId)) {
        row.valid = false; row.errors.push('unknown_slot');
      }
      if (hours < 1) {
        row.valid = false; row.errors.push('positive_hours_required');
      }
      if (Object.prototype.hasOwnProperty.call(slots, slotId)) {
        var slot = slots[slotId] || {};
        var capacity = Math.max(0, Math.floor(Number(slot.capacity_hours) || 0));
        row.unit_id = slot.unit_id;
        row.slot_label = slot.slot_label;
        row.subject = slot.subject;
        row.capacity_hours = capacity;
        if (hours > capacity) {
          row.valid = false; row.errors.push('hours_exceed_slot_capacity');
        }
        if (hours > 0 && hours !== capacity) {
          row.valid = false; row.errors.push('atomic_slot_requires_full_hours');
        }
        if (Object.prototype.hasOwnProperty.call(peopleIndex, personId)) {
          var assignment = bestAssignmentForSlot(slot, peopleIndex[personId]);
          if (assignment === null) {
            row.valid = false; row.errors.push('specialty_not_eligible');
          } else {
            row.priority = assignment.priority;
            row.used_specialty_code = assignment.used_specialty_code;
            row.specialty_source = assignment.specialty_source;
            if (slot.top_priority !== undefined && slot.top_priority !== null && row.priority !== slot.top_priority) {
              row.warnings.push('uses_lower_priority_assignment');
            }
          }
        }
      }
      if (row.valid) slotAttempted[slotId] = (slotAttempted[slotId] || 0) + hours;
      return row;
    });

    var overallocatedSlots = {};
    var rawOver = 0;
    Object.keys(slots).forEach(function (slotId) {
      var capacity = Math.max(0, Math.floor(Number(slots[slotId] && slots[slotId].capacity_hours) || 0));
      var attempted = slotAttempted[slotId] || 0;
      var overage = Math.max(0, attempted - capacity);
      if (overage > 0) {
        overallocatedSlots[slotId] = overage;
        rawOver += overage;
      }
    });
    if (Object.keys(overallocatedSlots).length) {
      rowResults.forEach(function (row) {
        if (row.slot_id && Object.prototype.hasOwnProperty.call(overallocatedSlots, row.slot_id)) {
          row.valid = false;
          if (row.errors.indexOf('slot_overallocated_across_roster') < 0) row.errors.push('slot_overallocated_across_roster');
        }
      });
    }

    rowResults.forEach(function (row) {
      if (!row.valid) return;
      var hours = row.hours;
      personAssigned[row.person_id] = (personAssigned[row.person_id] || 0) + hours;
      if (!personPriority[row.person_id]) personPriority[row.person_id] = { A: 0, B: 0, C: 0, SPECIAL: 0 };
      personPriority[row.person_id][row.priority] = (personPriority[row.person_id][row.priority] || 0) + hours;
      if (!personSource[row.person_id]) personSource[row.person_id] = { primary: 0, secondary: 0 };
      personSource[row.person_id][row.specialty_source] = (personSource[row.person_id][row.specialty_source] || 0) + hours;
      slotAssigned[row.slot_id] = (slotAssigned[row.slot_id] || 0) + hours;
    });

    var peopleOverBLimit = 0;
    var bHoursOverLimitTotal = 0;
    Object.keys(personPriority).forEach(function (personId) {
      var bHours = personPriority[personId].B || 0;
      if (bHours <= 10) return;
      peopleOverBLimit++;
      bHoursOverLimitTotal += bHours - 10;
      rowResults.forEach(function (row) {
        if (row.valid && row.person_id === personId && row.priority === 'B' && row.warnings.indexOf('b_assignment_hours_exceed_10_limit') < 0) {
          row.warnings.push('b_assignment_hours_exceed_10_limit');
        }
      });
    });

    var assignedTotal = 0;
    var unassigned = 0;
    Object.keys(slots).forEach(function (slotId) {
      var capacity = Math.max(0, Math.floor(Number(slots[slotId] && slots[slotId].capacity_hours) || 0));
      var assigned = slotAssigned[slotId] || 0;
      assignedTotal += assigned;
      unassigned += Math.max(0, capacity - assigned);
    });
    var invalidRows = rowResults.reduce(function (count, row) { return count + (row.valid ? 0 : 1); }, 0);

    return {
      valid: rawOver === 0 && invalidRows === 0,
      allocation_rows: rowResults,
      slot_attempted: slotAttempted,
      slot_assigned: slotAssigned,
      person_assigned: personAssigned,
      person_priority: personPriority,
      person_source: personSource,
      overallocated_slots: overallocatedSlots,
      summary: {
        assigned_slot_hours_total: assignedTotal,
        unassigned_slot_hours: unassigned,
        overallocated_slot_hours: rawOver,
        invalid_allocation_row_count: invalidRows,
        people_over_b_assignment_limit_count: peopleOverBLimit,
        b_assignment_hours_over_limit_total: bHoursOverLimitTotal
      }
    };
  }


  function objectiveCompare(a, b) {
    a = a || {}; b = b || {};
    var keys = ['covered', 'top', 'b', 'primary'];
    for (var i = 0; i < keys.length; i++) {
      var key = keys[i];
      var av = Math.floor(Number(a[key]) || 0);
      var bv = Math.floor(Number(b[key]) || 0);
      if (av === bv) continue;
      return av > bv ? 1 : -1;
    }
    return 0;
  }

  function objectiveForRows(rows) {
    var o = { covered: 0, top: 0, b: 0, primary: 0 };
    (Array.isArray(rows) ? rows : []).forEach(function (row) {
      var h = nonNegativeInt(row && row.hours);
      var p = row && row.priority ? String(row.priority) : '';
      o.covered += h;
      if (p === 'A' || p === 'SPECIAL') o.top += h;
      else if (p === 'B') o.b += h;
      if (row && row.specialty_source === 'primary') o.primary += h;
    });
    return o;
  }

  function cloneJson(value) {
    return JSON.parse(JSON.stringify(value == null ? {} : value));
  }

  function naturalCompare(a, b) {
    return String(a == null ? '' : a).localeCompare(String(b == null ? '' : b), 'el', { numeric: true });
  }

  /*
   * Pure browser optimizer corresponding to teachingAllocationEngineSolveRemaining().
   * It operates only on already-built slots/person state and does not read DOM/global
   * staffing state. This is intentionally extracted before the PHP reference is removed.
   */
  function optimizeRemaining(slotsInput, peopleInput, personStateInput, slotStateInput) {
    var slots = slotsInput && typeof slotsInput === 'object' ? slotsInput : {};
    var peopleIndex = keyedPeople(peopleInput);
    var originalPeople = cloneJson(personStateInput || {});
    var originalSlots = cloneJson(slotStateInput || {});
    var peopleIds = Object.keys(originalPeople).filter(function (pid) {
      return peopleIndex[pid] && nonNegativeInt(originalPeople[pid].remaining_hours) > 0;
    }).sort(naturalCompare);

    var routesBySlot = {};
    var routeCount = 0;
    var openSlotCount = 0;
    Object.keys(slots).forEach(function (sid) {
      var state = originalSlots[sid] || {};
      var need = nonNegativeInt(state.remaining_hours);
      if (need < 1 || state.atomic_blocked) return;
      openSlotCount++;
      var routes = {};
      peopleIds.forEach(function (pid) {
        var match = bestAssignmentForSlot(slots[sid], peopleIndex[pid]);
        if (!match) return;
        var ps = originalPeople[pid] || {};
        if (nonNegativeInt(ps.remaining_hours) < need) return;
        if (match.priority === 'B' && nonNegativeInt(ps.b_remaining_hours) < need) return;
        routes[pid] = match;
        routeCount++;
      });
      if (Object.keys(routes).length) routesBySlot[sid] = routes;
    });

    // Fast atomic lower bound. Exact search below may improve it.
    var seedPeople = cloneJson(originalPeople);
    var seedSlots = cloneJson(originalSlots);
    var seed = [];
    Object.keys(routesBySlot).sort(function (a, b) {
      var ca = Object.keys(routesBySlot[a]).length, cb = Object.keys(routesBySlot[b]).length;
      if (ca !== cb) return ca - cb;
      var ha = nonNegativeInt(seedSlots[a] && seedSlots[a].remaining_hours);
      var hb = nonNegativeInt(seedSlots[b] && seedSlots[b].remaining_hours);
      if (ha !== hb) return hb - ha;
      return naturalCompare(a, b);
    }).forEach(function (sid) {
      var need = nonNegativeInt(seedSlots[sid] && seedSlots[sid].remaining_hours);
      var candidates = Object.keys(routesBySlot[sid]).filter(function (pid) {
        var m = routesBySlot[sid][pid], ps = seedPeople[pid];
        return ps && nonNegativeInt(ps.remaining_hours) >= need && (m.priority !== 'B' || nonNegativeInt(ps.b_remaining_hours) >= need);
      }).sort(function (a, b) {
        var ma = routesBySlot[sid][a], mb = routesBySlot[sid][b];
        var r = priorityRank(ma.priority) - priorityRank(mb.priority); if (r) return r;
        if (ma.specialty_source !== mb.specialty_source) return ma.specialty_source === 'primary' ? -1 : 1;
        var la = nonNegativeInt(seedPeople[a].remaining_hours) - need;
        var lb = nonNegativeInt(seedPeople[b].remaining_hours) - need;
        if (la !== lb) return la - lb;
        return naturalCompare(a, b);
      });
      if (!candidates.length) return;
      var pid = candidates[0], m = routesBySlot[sid][pid], slot = slots[sid] || {};
      seed.push({
        person_id: pid, slot_id: sid, slot_label: slot.slot_label || slot.label || sid, subject: slot.subject || '',
        hours: need, priority: m.priority, used_specialty_code: m.used_specialty_code,
        specialty_source: m.specialty_source, source: 'automatic_proposal'
      });
      seedPeople[pid].remaining_hours = nonNegativeInt(seedPeople[pid].remaining_hours) - need;
      if (m.priority === 'B') {
        seedPeople[pid].b_assignment_hours = nonNegativeInt(seedPeople[pid].b_assignment_hours) + need;
        seedPeople[pid].b_remaining_hours = Math.max(0, 10 - seedPeople[pid].b_assignment_hours);
      }
      seedSlots[sid].remaining_hours = 0;
    });

    var groupMap = new Map();
    Object.keys(routesBySlot).forEach(function (sid) {
      var need = nonNegativeInt(originalSlots[sid] && originalSlots[sid].remaining_hours);
      var routes = routesBySlot[sid];
      var sig = Object.keys(routes).sort(naturalCompare).map(function (pid) {
        var m = routes[pid];
        return pid + '=' + m.priority + '/' + m.specialty_source + '/' + m.used_specialty_code;
      }).join(';');
      var key = need + '|' + sig;
      if (!groupMap.has(key)) groupMap.set(key, { need: need, routes: routes, slot_ids: [] });
      groupMap.get(key).slot_ids.push(sid);
    });
    var groups = Array.from(groupMap.values()).sort(function (a, b) {
      var ca = Object.keys(a.routes).length, cb = Object.keys(b.routes).length;
      if (ca !== cb) return ca - cb;
      if (a.need !== b.need) return b.need - a.need;
      return naturalCompare(a.slot_ids[0], b.slot_ids[0]);
    });

    var personToGroups = {};
    groups.forEach(function (g, gi) {
      Object.keys(g.routes).forEach(function (pid) {
        if (!personToGroups[pid]) personToGroups[pid] = [];
        personToGroups[pid].push(gi);
      });
    });
    var visited = new Set(), components = [];
    groups.forEach(function (_g, start) {
      if (visited.has(start)) return;
      var queue = [start], gis = [], pids = new Set();
      visited.add(start);
      while (queue.length) {
        var gi = queue.shift(); gis.push(gi);
        Object.keys(groups[gi].routes).forEach(function (pid) {
          pids.add(pid);
          (personToGroups[pid] || []).forEach(function (ngi) {
            if (!visited.has(ngi)) { visited.add(ngi); queue.push(ngi); }
          });
        });
      }
      components.push({ group_indexes: gis, person_ids: Array.from(pids) });
    });

    var seedBySlot = {};
    seed.forEach(function (row) { seedBySlot[row.slot_id] = row; });
    var finalRows = [], allCertified = true, totalNodes = 0, fallbackComponents = 0;

    components.forEach(function (component) {
      var cg = component.group_indexes.map(function (i) { return groups[i]; });
      var cpids = component.person_ids.slice().sort(naturalCompare);
      if (cpids.length === 1) {
        var pid = cpids[0];
        var cap = nonNegativeInt(originalPeople[pid] && originalPeople[pid].remaining_hours);
        var bcap = nonNegativeInt(originalPeople[pid] && originalPeople[pid].b_remaining_hours);
        var items = [];
        cg.forEach(function (g) {
          var m = g.routes[pid]; if (!m) return;
          g.slot_ids.forEach(function (sid) { items.push({ sid: sid, need: g.need, m: m }); });
        });
        var dp = new Map();
        dp.set('0:0', { objective: { covered: 0, top: 0, b: 0, primary: 0 }, rows: [], used: 0, bused: 0 });
        items.forEach(function (item) {
          var next = new Map(dp);
          dp.forEach(function (st) {
            totalNodes++;
            var nu = st.used + item.need;
            var nb = st.bused + (item.m.priority === 'B' ? item.need : 0);
            if (nu > cap || nb > bcap) return;
            var o = Object.assign({}, st.objective);
            o.covered += item.need;
            if (item.m.priority === 'A' || item.m.priority === 'SPECIAL') o.top += item.need;
            else if (item.m.priority === 'B') o.b += item.need;
            if (item.m.specialty_source === 'primary') o.primary += item.need;
            var slot = slots[item.sid] || {};
            var rows = st.rows.concat([{
              person_id: pid, slot_id: item.sid, slot_label: slot.slot_label || slot.label || item.sid,
              subject: slot.subject || '', hours: item.need, priority: item.m.priority,
              used_specialty_code: item.m.used_specialty_code, specialty_source: item.m.specialty_source,
              source: 'automatic_optimizer_dp'
            }]);
            var key = nu + ':' + nb, prev = next.get(key);
            if (!prev || objectiveCompare(o, prev.objective) > 0) next.set(key, { objective: o, rows: rows, used: nu, bused: nb });
          });
          dp = next;
        });
        var best = { objective: { covered: 0, top: 0, b: 0, primary: 0 }, rows: [] };
        dp.forEach(function (st) { if (objectiveCompare(st.objective, best.objective) > 0) best = st; });
        finalRows = finalRows.concat(best.rows);
        return;
      }

      var counts = cg.map(function (g) { return g.slot_ids.length; });
      var originalCounts = counts.slice();
      var rem = {}, brem = {};
      cpids.forEach(function (pid2) {
        rem[pid2] = nonNegativeInt(originalPeople[pid2] && originalPeople[pid2].remaining_hours);
        brem[pid2] = nonNegativeInt(originalPeople[pid2] && originalPeople[pid2].b_remaining_hours);
      });
      var equiv = {};
      cpids.forEach(function (pid2) {
        equiv[pid2] = cg.map(function (g) {
          var m = g.routes[pid2];
          return m ? (m.priority + '/' + m.specialty_source + '/' + m.used_specialty_code) : '-';
        }).join(';');
      });
      var bestRows = [];
      cg.forEach(function (g) { g.slot_ids.forEach(function (sid) { if (seedBySlot[sid]) bestRows.push(seedBySlot[sid]); }); });
      var bestObj = objectiveForRows(bestRows), currentRows = [], cur = { covered: 0, top: 0, b: 0, primary: 0 };
      var nodes = 0, aborted = false, memo = new Map(), nodeLimit = 30000;

      function search() {
        if (aborted) return;
        if (++nodes > nodeLimit) { aborted = true; return; }
        var remainingHours = 0, done = true;
        cg.forEach(function (g, gi) { if (counts[gi] > 0) { done = false; remainingHours += counts[gi] * g.need; } });
        if (done) {
          if (objectiveCompare(cur, bestObj) > 0) {
            bestObj = Object.assign({}, cur);
            bestRows = currentRows.map(function (r) { return Object.assign({}, r); });
          }
          return;
        }
        var personHours = cpids.reduce(function (t, pid2) { return t + (rem[pid2] || 0); }, 0);
        var upper = cur.covered + Math.min(remainingHours, personHours);
        if (upper < bestObj.covered) return;
        if (upper === bestObj.covered && cur.top + Math.min(remainingHours, personHours) < bestObj.top) return;

        var equivStates = {};
        cpids.forEach(function (pid2) {
          var sig = equiv[pid2] || pid2;
          if (!equivStates[sig]) equivStates[sig] = [];
          equivStates[sig].push(rem[pid2] + ':' + brem[pid2]);
        });
        var memoKey = counts.join(',') + '|' + Object.keys(equivStates).sort(naturalCompare).map(function (sig) {
          return sig + '=' + equivStates[sig].sort(naturalCompare).join(',');
        }).join('|');
        var seen = memo.get(memoKey);
        if (seen && (seen.top > cur.top || (seen.top === cur.top && seen.primary >= cur.primary))) return;
        memo.set(memoKey, { top: cur.top, primary: cur.primary });

        var chosen = -1, cands = [], few = 1e9;
        cg.forEach(function (g, gi) {
          if (counts[gi] < 1) return;
          var local = Object.keys(g.routes).filter(function (pid2) {
            var m = g.routes[pid2];
            return rem[pid2] >= g.need && (m.priority !== 'B' || brem[pid2] >= g.need);
          }).map(function (pid2) { return { pid: pid2, m: g.routes[pid2], left: rem[pid2] - g.need }; });
          if (chosen < 0 || local.length < few || (local.length === few && g.need > cg[chosen].need)) {
            chosen = gi; cands = local; few = local.length;
          }
        });
        if (chosen < 0) return;
        if (!cands.length) {
          var old = counts[chosen]; counts[chosen] = 0; search(); counts[chosen] = old; return;
        }
        cands.sort(function (a, b) {
          var r = priorityRank(a.m.priority) - priorityRank(b.m.priority); if (r) return r;
          if (a.m.specialty_source !== b.m.specialty_source) return a.m.specialty_source === 'primary' ? -1 : 1;
          if (a.left !== b.left) return a.left - b.left;
          return naturalCompare(a.pid, b.pid);
        });
        var g = cg[chosen], idx = originalCounts[chosen] - counts[chosen], sid = g.slot_ids[idx], slot = slots[sid] || {};
        counts[chosen]--;
        var sym = new Set();
        cands.forEach(function (c) {
          var pid2 = c.pid, m = c.m, sk = equiv[pid2] + '|' + rem[pid2] + '|' + brem[pid2];
          if (sym.has(sk)) return; sym.add(sk);
          rem[pid2] -= g.need;
          if (m.priority === 'B') brem[pid2] -= g.need;
          currentRows.push({
            person_id: pid2, slot_id: sid, slot_label: slot.slot_label || slot.label || sid,
            subject: slot.subject || '', hours: g.need, priority: m.priority,
            used_specialty_code: m.used_specialty_code, specialty_source: m.specialty_source,
            source: 'automatic_optimizer'
          });
          cur.covered += g.need;
          if (m.priority === 'A' || m.priority === 'SPECIAL') cur.top += g.need;
          else if (m.priority === 'B') cur.b += g.need;
          if (m.specialty_source === 'primary') cur.primary += g.need;
          search();
          if (m.specialty_source === 'primary') cur.primary -= g.need;
          if (m.priority === 'A' || m.priority === 'SPECIAL') cur.top -= g.need;
          else if (m.priority === 'B') cur.b -= g.need;
          cur.covered -= g.need;
          currentRows.pop();
          if (m.priority === 'B') brem[pid2] += g.need;
          rem[pid2] += g.need;
        });
        search();
        counts[chosen]++;
      }

      search();
      totalNodes += nodes;
      if (aborted) { allCertified = false; fallbackComponents++; }
      finalRows = finalRows.concat(bestRows);
    });

    var finalPeople = cloneJson(originalPeople), finalSlots = cloneJson(originalSlots);
    finalRows.forEach(function (row) {
      var ps = finalPeople[row.person_id], ss = finalSlots[row.slot_id], h = nonNegativeInt(row.hours);
      if (!ps || !ss) return;
      ps.remaining_hours = Math.max(0, nonNegativeInt(ps.remaining_hours) - h);
      if (row.priority === 'B') {
        ps.b_assignment_hours = nonNegativeInt(ps.b_assignment_hours) + h;
        ps.b_remaining_hours = Math.max(0, 10 - ps.b_assignment_hours);
      }
      ss.remaining_hours = 0;
    });
    finalRows.sort(function (a, b) {
      var sa = slots[a.slot_id] || {}, sb = slots[b.slot_id] || {};
      var g = naturalCompare(sa.grade || '', sb.grade || ''); if (g) return g;
      var s = naturalCompare(sa.subject || '', sb.subject || ''); if (s) return s;
      return naturalCompare(a.slot_id, b.slot_id);
    });
    var covered = finalRows.reduce(function (t, r) { return t + nonNegativeInt(r.hours); }, 0);
    var remainingSlotHours = 0;
    Object.keys(finalSlots).forEach(function (sid) {
      var state = finalSlots[sid] || {};
      if (state.atomic_blocked) return;
      remainingSlotHours += nonNegativeInt(state.remaining_hours);
    });
    return {
      allocations: finalRows,
      people: finalPeople,
      slots: finalSlots,
      summary: {
        covered_hours: covered,
        auto_covered_hours: covered,
        remaining_slot_hours: remainingSlotHours,
        route_count: routeCount,
        optimization_group_count: openSlotCount,
        atomic: true,
        optimizer_component_count: components.length,
        optimizer_search_nodes: totalNodes,
        maximum_coverage_certified: allCertified,
        optimizer_safety_fallback_components: fallbackComponents
      }
    };
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
    normalizePerson: normalizePerson,
    priorityForSlotCode: priorityForSlotCode,
    priorityRank: priorityRank,
    bestAssignmentForSlot: bestAssignmentForSlot,
    validateRosterSlotAllocations: validateRosterSlotAllocations,
    objectiveCompare: objectiveCompare,
    objectiveForRows: objectiveForRows,
    optimizeRemaining: optimizeRemaining
  });
})(typeof window !== 'undefined' ? window : globalThis);
