# Digital Tutoring unification — final integration

Scope: 1ΓΕ/2026, 2ΓΕ/2026 and 3ΕΑ/2025.

- Both calculators call the same PHP component: `includes/components/asep-digital-tutoring-service.php`.
- Both load the same `includes/service-calculations.js` and `includes/asep-digital-tutoring.js`.
- The obsolete `asep-digital-tutoring-php56.php` name is removed from both pages.
- School-year limits 2024–2025 = 9 months + 16 days and 2025–2026 = 8 months + 2 days live only in `service-calculations.js`.
- Day remainders are pooled across school years; every 30 days yields one additional month.
- Rate 1.5 points/month and 15 points/school-year cap live only in `service-calculations.js`.
- Dynamic rows, duplicate-year lock, details, warnings and copied-result summary are centralized in `asep-digital-tutoring.js`.
- The 1ΓΕ/2ΓΕ page no longer builds Digital Tutoring year details or cap warnings locally.
