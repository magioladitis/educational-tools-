# ASEP Computer Proof audit — v3.20.15-rc1

Scope: only the six ASEP calculators that already contain the `computer` academic criterion:

- 1ΓΕ/2026 & 2ΓΕ/2026
- 1ΓΤ/2024
- 1ΕΑ/2025
- 2ΕΑ/2025
- 3ΕΑ/2025
- 4ΕΑ/2025

Excluded on purpose: SDE Registry, SDE secondment, Onaseia and every non-ASEP calculator.

## Shared presentation

New component: `includes/components/asep-computer-proof.php`

Unified wording:
- «Γνώση πληροφορικής ή χειρισμού Η/Υ»
- three objects: word processing, spreadsheets, internet services
- quick proof check with the six proof routes of ASEP Appendix B

The component contains no score calculation and no branch/specialty decision.
Each calculator passes its visible points explicitly: 4 points or 20 points.

## Six proof routes in the quick check

1. Certificate of Informatics/computer-use knowledge.
2. Informatics/computer-use study title covered by Tables 1–3 of Annex A of P.D. 85/2022.
3. University/technological undergraduate/postgraduate/doctoral title whose transcript shows at least four Informatics/computer-use courses.
4. State Certificate of Informatics (Article 28, Law 4653/2020).
5. Graduation certificate from ESDDA.
6. Teacher ICT skills/knowledge certification, Level A, Ministry of Education.

The proof selection is advisory only. It does not award points on its own.

## PE86 lock

PE86 restrictions are intentionally outside the component.

- 1ΓΕ/2ΓΕ: `includes/academic-calculations.js` remains byte-for-byte unchanged and keeps `computerPoints = 0` for specialty `ΠΕ86`.
- 3ΕΑ/2025: the page still disables the computer checkbox for `ΠΕ86`, clears it, and the scoring condition remains `sp !== 'ΠΕ86'`.
- The component contains only a user-facing reminder that special restrictions such as PE86 are applied separately.

## Regression isolation

Inline JavaScript in all six calculators is byte-for-byte unchanged from v3.20.14-rc2.
Calculation modules are unchanged, including academic, TE academic, service, social and SDE Registry modules.
The new JavaScript file `includes/asep-computer-proof.js` only shows/hides the proof panel and updates its status message.

## Responsive UI

CSS is scoped under `.asep-computer-proof*` and `body.edu-ui`.
The proof dropdown is width-safe and long legal descriptions use `overflow-wrap:anywhere`.
A 650px mobile rule reduces padding and list indentation.

Chromium visual smoke was attempted in the sandbox but did not complete due the local headless Chromium/DBus environment, so browser E2E is not claimed as PASS.
