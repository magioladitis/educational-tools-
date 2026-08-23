# SDE module version drift

The uploaded `ypologismos-morion-apospasis-sde.php` is already written for the corrected scoring table from FEK B' 4199/10.07.2026.

During the audit, the Library's unversioned `sde-calculations.js` was found to still contain the older pre-correction values:
- formal qualifications max 19
- education max 23
- other qualifications max 4
- older doctoral/master scoring
- training below 15 hours still scoring proportionally
- formal-education experience counted only beyond the two-year threshold
- computer knowledge worth 1 point

The known `sde-calculations-v3.16.js` matches the corrected page:
- formal qualifications max 18
- education max 22
- other qualifications max 5
- corrected title scoring
- <15h training = 0
- formal education scores from the first full school year
- computer knowledge = 2 points, including the PE86 rule
- eligibility uses the dedicated `eligibilitySchoolYears` input

For this candidate patch, the canonical `includes/sde-calculations.js` is byte-identical to `sde-calculations-v3.16.js`.

The old unversioned Library module is preserved in `backup/sde-calculations-unversioned-library.js`.
