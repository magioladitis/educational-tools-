#!/usr/bin/env bash
set -euo pipefail

ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$ROOT"

run_py() {
  local file="$1"
  echo "==> $file"
  python3 "$file"
}

run_py tests/mobile-hardening-contract.py
run_py tests/mobile-browser-usability-contract.py
run_py tests/pwa-manifest-contract.py
run_py tests/php-inline-js-separation-contract.py
run_py tests/layout-phase4a-contract.py
run_py tests/abroad-mobile-refactor-contract.py
run_py tests/salary-mobile-refactor-contract.py
run_py tests/transfer-mobile-refactor-contract.py
run_py tests/weekly-timetable-2026-contract.py
run_py tests/digital-tutoring-substitute-2026-contract.py

echo "==> source registry audit"
php tools/source-registry-audit.php

echo "==> top-level PHP syntax"
for file in ./*.php; do
  php -l "$file" >/dev/null
done

echo "PRE-PWA REGRESSION GATE: PASS"
