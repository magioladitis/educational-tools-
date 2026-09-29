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
run_py tests/accessibility-production-contract.py
run_py tests/frontend-polish-v32218-contract.py
run_py tests/mobile-ux-phase1-v32223-contract.py
run_py tests/mobile-ux-phase2-v32224-contract.py
run_py tests/mobile-ux-phase3-v32225-contract.py
run_py tests/ux-consistency-v32226-contract.py
run_py tests/shared-page-hero-v32249-contract.py
run_py tests/deadline-lifecycle-v32227-contract.py
run_py tests/service-timeline-v32236-contract.py
run_py tests/service-timeline-v32238-contract.py
run_py tests/service-timeline-v32239-contract.py
run_py tests/service-timeline-v32240-contract.py
run_py tests/service-timeline-v32242-contract.py
run_py tests/service-timeline-v32243-contract.py
run_py tests/service-timeline-v32244-contract.py
run_py tests/service-timeline-v32245-export-contract.py
run_py tests/service-timeline-v32248-contract.py
run_py tests/service-timeline-v32250-contract.py
run_py tests/service-timeline-v32251-contract.py
run_py tests/service-timeline-v32252-contract.py
run_py tests/public-copy-audit-v32241-contract.py
run_py tests/vacancies-public-unavailable-contract.py
run_py tests/php-inline-js-separation-contract.py
run_py tests/layout-phase4a-contract.py
run_py tests/abroad-mobile-refactor-contract.py
run_py tests/salary-mobile-refactor-contract.py
run_py tests/transfer-mobile-refactor-contract.py
run_py tests/weekly-timetable-2026-contract.py
run_py tests/digital-tutoring-substitute-2026-contract.py
run_py tests/specialty-code-normalization-parity-contract.py
run_py tests/specialty-code-canonicalization-contract.py
echo "==> education core contract"
node tests/education-core-contract.js
echo "==> personnel CSV import regression"
node tests/personnel-csv-import-regression.js
run_py tests/personnel-workload-client-parity-contract.py
run_py tests/personnel-stage-client-transition-contract.py
run_py tests/personnel-workload-allocation-client-parity-contract.py
run_py tests/personnel-workload-optimizer-client-parity-contract.py
run_py tests/personnel-workload-optimizer-real-profile-contract.py
run_py tests/personnel-workload-optimizer-mutation-contract.py
run_py tests/personnel-workload-optimizer-policy-contract.py
echo "==> personnel workload performance budget"
python3 tests/personnel-workload-performance-benchmark.py --contract
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
