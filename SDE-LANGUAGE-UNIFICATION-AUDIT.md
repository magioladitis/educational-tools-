# SDE foreign-language unification audit — 2026-08-23

## Scope

Migration of `ypologismos-morion-apospasis-sde.php` to the shared foreign-language engine already used by the ASEP calculators.

## Legal/scoring contract locked

For SDE secondment 2026-2027:

- Up to two foreign languages are scored.
- The strongest declared language is ranked as the 1st foreign language:
  - B2 = 1 point
  - C1 = 1.5 points
  - C2 = 2 points
- The next distinct language is ranked as the 2nd foreign language:
  - B2 = 0.5 points
  - C1 = 0.75 points
  - C2 = 1 point
- The highest certified level of the same language is counted only once.
- Foreign-language teachers do not receive points for the language they teach.
- The SDE page uses Latin specialty codes (`PE06`), while ASEP pages may use Greek (`ΠΕ06`); the central mapping now recognizes both spellings.

The change is intentionally limited to the language subsystem. Existing SDE rules for the rest of the calculator are unchanged.

## Architecture after refactor

### Single source of truth

`includes/language-calculations.js`

New profile:

`EducationLanguages.PROFILES.sde_secondment`

The profile uses ranked/position-dependent scoring. It does not reuse the uniform ASEP points because SDE assigns different points to the 1st and 2nd language.

### Shared UI/controller

`includes/components/asep-language-selector.php`

`includes/asep-language-selector.js`

The SDE page now uses the same shared language selector as the ASEP pages. It gains:

- duplicate named-language lock,
- normalized duplicate checking for custom “Other language” entries,
- branch-language exclusion in the UI,
- protection against bypassing the PE06 exclusion by typing “English/Αγγλικά” as an “Other” language,
- automatic re-ranking by level before the 1st/2nd-language points are assigned.

### SDE adapter

`includes/sde-calculations.js`

`SDECalculator.calculateLanguages()` remains as a compatibility adapter, but the local scoring tables were removed. It delegates all language scoring to:

`EducationLanguages.calculate('sde_secondment', ...)`

Legacy SDE input names remain supported for old tests/callers, but no language scoring numbers remain in `sde-calculations.js`.

## Regression results

### Existing shared-language profiles

Full old-vs-new parity for:

- `pe`
- `eep`
- `ebp`
- `te`

Cases: **21,952**

Result: **21,952 PASS / 0 FAIL**

### SDE old-vs-new scoring parity

Exhaustive combinations across:

- PE02 and PE06,
- both language rows,
- English/French/German/Italian/Spanish/Other/empty,
- none/B2/C1/C2,
- duplicates and reversed order.

Cases: **1,568**

Result: **1,568 PASS / 0 FAIL** for points and breakdown.

### SDE edge locks

8 additional locked cases:

- C2 + B2 = 2.5
- B2 + C2 = 2.5 regardless of input order
- C1 + C1 = 2.25
- duplicate same language counts once at the higher level
- C2 + C2 = 3 maximum language points
- PE06 English exclusion
- PE06 cannot bypass exclusion through Other = English
- custom-language duplicates are normalized

Result: **8 PASS / 0 FAIL**

### Combined dedicated language suite

**23,528 PASS / 0 FAIL**

## Cross-regression

- PE Academic: 20/20 numeric + 24/24 architecture
- TE Academic: 19/19 numeric + 42/42 architecture
- Service/Social: 14/14 numeric + 67/67 architecture
- EAE eligibility: 2,747/2,747 parity
- PHP syntax: 36/36
- JavaScript syntax: 28/28
- SDE PHP render: PASS
- Rendered duplicate HTML ids: 0

## Production diff

Changed files only:

1. `ypologismos-morion-apospasis-sde.php`
2. `includes/sde-calculations.js`
3. `includes/language-calculations.js`
4. `includes/asep-language-selector.js`

Existing shared dependency used by the SDE page:

5. `includes/components/asep-language-selector.php` (unchanged, included in the upload package for completeness)
