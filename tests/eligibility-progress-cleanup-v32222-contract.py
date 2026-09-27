#!/usr/bin/env python3
from pathlib import Path

ROOT = Path(__file__).resolve().parents[1]
php = (ROOT / "dikaioma-symmetoxis.php").read_text(encoding="utf-8")
js = (ROOT / "includes" / "eligibility-guide-ui.js").read_text(encoding="utf-8")
css = (ROOT / "assets" / "common.css").read_text(encoding="utf-8")

checks = [
    ("eligibility page has no progress panel", "progress-panel" not in php and "progressText" not in php and "progressFill" not in php),
    ("eligibility controller has no progress logic", "updateProgress" not in js and "progressText" not in js and "progressFill" not in js),
    ("incomplete submit reports remaining answers", "Απομένουν ${missingIds.length}" in js),
    ("missing fields are highlighted only by validation state", "validationAttempted" in js and "has-missing" in js and ".question.has-missing" in css),
]

failed = False
for label, ok in checks:
    print(("PASS" if ok else "FAIL") + " | " + label)
    failed = failed or not ok

raise SystemExit(1 if failed else 0)
