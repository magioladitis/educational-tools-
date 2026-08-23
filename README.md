# Training proof component — v3.20.11-rc2

Hotfix over rc1 for PHP compatibility on older hosting environments.

## Cause fixed
The rc1 component used a PHP arrow function (`fn`), which requires PHP 7.4+. On a server running an older PHP version this can cause a fatal parse error immediately after `header.php`, leaving the rest of the page blank.

## What changed
Only `includes/components/training-proof.php` changed. It now uses conservative PHP syntax (no arrow functions and no return type declaration). The rendered HTML contract and all page-specific scoring/eligibility logic are unchanged.

## Safety boundary
The component remains presentation-only. It contains no 300h/400h/7-month/EAE/scoring rules. Those remain in each calculator.
