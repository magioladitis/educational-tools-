(function (root, factory) {
  if (typeof module === 'object' && module.exports) {
    module.exports = factory();
  } else {
    root.NewlyAppointedPlacementCalculations = factory();
  }
}(typeof self !== 'undefined' ? self : this, function () {
  'use strict';

  function normalizeChildren(value) {
    var n = Number(value);
    if (!Number.isFinite(n)) return 0;
    return Math.max(0, Math.min(20, Math.floor(n)));
  }

  function childPoints(children) {
    var n = normalizeChildren(children);
    if (n === 0) return 0;
    if (n === 1) return 4;
    if (n === 2) return 8;
    if (n === 3) return 14;
    return 14 + ((n - 3) * 7);
  }

  function familyStatusRequiresChild(status) {
    return status === 'widowed_parent' || status === 'single_parent' || status === 'divorced_custody';
  }

  function familyStatusPoints(status, children) {
    if (status === 'married') return 4;
    if (familyStatusRequiresChild(status) && normalizeChildren(children) > 0) return 4;
    return 0;
  }

  function familyPoints(status, children) {
    return familyStatusPoints(status, children) + childPoints(children);
  }

  function calculate(input) {
    input = input || {};
    var status = input.familyStatus || 'none';
    var normalizedChildren = normalizeChildren(input.eligibleChildren);
    var familyStatus = familyStatusPoints(status, normalizedChildren);
    var children = childPoints(normalizedChildren);
    var coService = input.coService ? 4 : 0;
    var locality = input.locality ? 4 : 0;
    var family = familyStatus + children;

    return {
      total: family + coService + locality,
      familyPoints: family,
      familyStatusPoints: familyStatus,
      familyStatusRequiresChild: familyStatusRequiresChild(status),
      childPoints: children,
      coServicePoints: coService,
      localityPoints: locality,
      eligibleChildren: normalizedChildren
    };
  }

  return {
    normalizeChildren: normalizeChildren,
    childPoints: childPoints,
    familyStatusRequiresChild: familyStatusRequiresChild,
    familyStatusPoints: familyStatusPoints,
    familyPoints: familyPoints,
    calculate: calculate
  };
}));
