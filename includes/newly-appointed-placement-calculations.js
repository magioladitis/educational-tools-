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

  function familyPoints(familyStatusEligible, children) {
    return (familyStatusEligible ? 4 : 0) + childPoints(children);
  }

  function calculate(input) {
    input = input || {};
    var familyStatus = input.familyStatusEligible ? 4 : 0;
    var children = childPoints(input.eligibleChildren);
    var coService = input.coService ? 4 : 0;
    var locality = input.locality ? 4 : 0;
    var family = familyStatus + children;

    return {
      total: family + coService + locality,
      familyPoints: family,
      familyStatusPoints: familyStatus,
      childPoints: children,
      coServicePoints: coService,
      localityPoints: locality,
      eligibleChildren: normalizeChildren(input.eligibleChildren)
    };
  }

  return {
    normalizeChildren: normalizeChildren,
    childPoints: childPoints,
    familyPoints: familyPoints,
    calculate: calculate
  };
}));
