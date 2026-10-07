#!/usr/bin/env python3
from pathlib import Path
import re

ROOT = Path(__file__).resolve().parents[1]
legal = (ROOT / 'includes' / 'legal-audit.php').read_text(encoding='utf-8')

tools = [
    'ypologismos-morion-apospasis-dimos.php',
    'ypologismos-morion-apospasis-psifiako-frontistirio.php',
    'ypologismos-morion-anapliroti-psifiako-frontistirio.php',
    'ypologismos-morion-apospasis-sde.php',
    'ypologismos-morion-mitroo-sde.php',
    'ypologismos-morion-diefthynton-ypodiefthynton-sde.php',
    'ypologismos-morion-sivitanidios-saek.php',
    'dikaioma-ypodiefthynti-saek.php',
    'ypologismos-morion-scholeio-evropaikis-paideias-irakleiou.php',
    'ypologismos-morion-apospasis-evropaika-scholeia.php',
]

for tool in tools:
    pattern = re.escape("'%s' => array(" % tool) + r".*?'status'\s*=>\s*'verified'.*?'last_verified'\s*=>\s*'2026-10-07'.*?'version'\s*=>\s*'3\.22\.91'"
    assert re.search(pattern, legal, re.S), f'missing verified v3.22.91 audit entry: {tool}'

# DIMOS stale legal basis must be gone and actual 2026 amendment present.
dimos = (ROOT / 'ypologismos-morion-apospasis-dimos.php').read_text(encoding='utf-8')
assert '169485/Δ6/30-12-2025' not in dimos
assert '9789/Δ6/27-01-2026' in dimos and 'Β΄ 315' in dimos
assert '71071/Δ6/19-06-2025' in dimos and '81473/Δ6/03-07-2025' in dimos

# Official source strengthening.
assert '70332-30-06-26' in (ROOT / 'ypologismos-morion-apospasis-psifiako-frontistirio.php').read_text(encoding='utf-8')
assert '70891-24-09-26' in (ROOT / 'ypologismos-morion-anapliroti-psifiako-frontistirio.php').read_text(encoding='utf-8')
assert '65085-28-05-26' in (ROOT / 'ypologismos-morion-scholeio-evropaikis-paideias-irakleiou.php').read_text(encoding='utf-8')
assert '64487-18-03-26' in (ROOT / 'ypologismos-morion-apospasis-evropaika-scholeia.php').read_text(encoding='utf-8')
assert '/proskliseis-prokirykseis/810-' in (ROOT / 'ypologismos-morion-apospasis-sde.php').read_text(encoding='utf-8')

config = (ROOT / 'includes' / 'config.php').read_text(encoding='utf-8')
sw = (ROOT / 'service-worker.js').read_text(encoding='utf-8')
assert "EDU_TOOLS_VERSION', '3.22.91'" in config
assert "CACHE_PREFIX + '3.22.91'" in sw
print('legal-audit-wave4-v32291-contract: PASS (%d tools)' % len(tools))
