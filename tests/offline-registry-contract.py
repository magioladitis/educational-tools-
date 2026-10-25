#!/usr/bin/env python3
"""Static security/coverage guard for v3.22.203 offline snapshots."""
import re, json
from pathlib import Path
root=Path(__file__).resolve().parents[1]
entries=re.findall(r"'href'\s*=>\s*'([^']+)'",(root/'includes/tools-catalog.php').read_text())
registry=(root/'assets/offline-registry.js').read_text()
public=json.loads(re.search(r'pages: Object.freeze\((\[.*?\])\),',registry,re.S).group(1))
excluded=json.loads(re.search(r'excluded: Object.freeze\((\{.*?\})\)',registry,re.S).group(1))
assert len(entries)==38 and len(set(entries))==len(entries)
assert set(entries) - set(public) == set(excluded), 'catalog mismatch'
assert not set(public)&set(excluded)
assert all((root/name).is_file() for name in public)
assert all(re.fullmatch(r'[a-z0-9-]+\.php', name) for name in public)
assert 'kena-sxoleion-login.php' not in public
assert 'ypologismos-didaktikon-anagkon.php' not in public
assert 'kena-sxoleion-admin.php' not in public
assert 'analytics-collect.php' not in public
assert len(public)==43
worker=(root/'service-worker.js').read_text()
assert 'request.method !== \'GET\'' in worker
assert 'request.mode === \'navigate\'' in worker
assert 'credentials: \'omit\'' in worker
assert 'private|no-store' in worker
assert "X-Edu-Offline-Prefetch" in worker
assert "publicPages.has(rel)" in worker
print('PASS: 38 catalog entries, 43 public snapshots, 2 intentional exclusions and security gates.')
