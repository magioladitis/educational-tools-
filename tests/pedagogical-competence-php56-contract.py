#!/usr/bin/env python3
from pathlib import Path
import re

ROOT = Path(__file__).resolve().parents[1]
FILES = [
    ROOT / "paidagogiki-eparkeia.php",
    ROOT / "includes" / "pedagogical-competence-data.php",
]

for path in FILES:
    text = path.read_text(encoding="utf-8")
    forbidden = {
        "null coalescing operator (PHP 7+)": r"\?\?",
        "spaceship operator (PHP 7+)": r"<=>",
        "arrow function (PHP 7.4+)": r"\bfn\s*\(",
        "match expression (PHP 8+)": r"\bmatch\s*\(",
        "nullsafe operator (PHP 8+)": r"\?->",
    }
    for label, pattern in forbidden.items():
        if re.search(pattern, text):
            raise SystemExit(f"FAIL: {path.name}: {label}")

print("PASS: pedagogical competence production PHP keeps conservative PHP 5.6 syntax")
