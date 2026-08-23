# Safety limits audit — 2026-08-23

User-requested defensive input ceilings. These are application safety/validation limits, not a claim about legal maxima unless separately documented by each tool.

## Implemented limits

### Disability percentages
- All shared ASEP disability percentage fields: `max=100` in HTML.
- Shared `EducationSocial` engine clamps candidate/spouse/child disability to `0..100` even if the DOM is bypassed.
- `AsepSocialCriteria` clamps numeric inputs live from their `min`/`max` attributes.
- `dikaiologitika-tekna-anapiria.php`: `disabilityPercent` remains `max=100`, now also clamps live to `0..100`; calculation already clamps to 100.

### Children
- Shared ASEP social component: `0..20`, integer.
- Shared `EducationSocial` engine clamps to 20 and emits a defensive warning if raw input is above 20.
- `ypologismos-morion-apospasis.php`: `eligibleChildren` is `0..20`, integer, with live clamp and calculation guard.

### SDE Director / Deputy experience in years
In `ypologismos-morion-diefthynton-ypodiefthynton-sde.php`, every field expressed in years has `max=40`:
- `educationalServiceYears`
- `sdeTeachingYears`
- `sdeTransferredYears`
- `schoolTeachingYears`
- `schoolTransferredYears`
- `sdeDirectorYears`
- `sdeDeputyYears`
- `otherAdminYears`

The page clamps these live to `0..40`, and `SDELeadership` independently clamps the same values to 40 so programmatic bypass cannot exceed the ceiling.

Hours fields are intentionally not affected.

## Cache bust
- `social-calculations.js` → `v=3.20.32`
- `asep-social-criteria.js` → `v=3.20.32`
- `sde-leadership-calculations.js` → `v=3.20.32`

## Verification
- Existing regression/architecture tests: 309 PASS / 0 FAIL.
- New safety calculation tests: 11 PASS / 0 FAIL.
- New rendered/static safety checks: 34 PASS / 0 FAIL.
- PHP lint: 36 PASS / 0 FAIL.
- JS syntax: 25 PASS / 0 FAIL.
- Rendered affected pages: 9 PASS / 0 FAIL.
- Local asset dependencies: 171 checked / 0 missing.

## Cleanup preserved
`includes/sde-calculations-v3.16.js` is absent from this master, matching the server cleanup already performed by the user.
