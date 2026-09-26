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
run_py tests/personnel-workload-client-parity-contract.py
run_py tests/personnel-workload-allocation-client-parity-contract.py
run_py tests/personnel-workload-optimizer-client-parity-contract.py
run_py tests/personnel-workload-optimizer-real-profile-contract.py
run_py tests/personnel-workload-optimizer-mutation-contract.py
run_py tests/staffing-client-allocation-actions-contract.py

echo "==> teaching assignments legacy-specialty contract"
php tests/teaching-assignments-legacy-specialties-contract.php

echo "==> legal sources contract"
php tests/legal-sources-contract-test.php

echo "==> source registry audit"
php tools/source-registry-audit.php

echo "==> top-level PHP syntax"
for file in ./*.php; do
  php -l "$file" >/dev/null
done

echo "PRE-PWA REGRESSION GATE: PASS"
