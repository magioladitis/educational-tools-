from pathlib import Path
import re
ROOT = Path(__file__).resolve().parents[1]
legal=(ROOT/'includes/legal-audit.php').read_text(encoding='utf-8')
page=(ROOT/'nomiki-epikairotita.php').read_text(encoding='utf-8')
config=(ROOT/'includes/config.php').read_text(encoding='utf-8')
sw=(ROOT/'service-worker.js').read_text(encoding='utf-8')
verified=[
 'posa-paravola.php','dikaiologitika-titlon-spoudon.php','ypologismos-morion-onaseia.php',
 'odigos-enstasis.php','dikaiologitika-tekna-anapiria.php','ypologismos-morion-apospasis-exoteriko.php',
 'metatropi-klimakas.php'
]
for tool in verified:
    pat=rf"'{re.escape(tool)}'\s*=>\s*array\(.*?'status'\s*=>\s*'verified'.*?'version'\s*=>\s*'3\.22\.92'"
    assert re.search(pat,legal,re.S), f'missing verified v3.22.92 audit entry: {tool}'
for tool in ['kena-sxoleion-login.php','adeies-ekpaideutikon.php']:
    pat=rf"'{re.escape(tool)}'\s*=>\s*array\(.*?'status'\s*=>\s*'not_applicable'.*?'version'\s*=>\s*'3\.22\.92'"
    assert re.search(pat,legal,re.S), f'missing not_applicable v3.22.92 entry: {tool}'
assert "'not_applicable' => 'Δεν απαιτεί αυτοτελή νομικό έλεγχο'" in legal
assert "'not_applicable' => 0" in legal
assert "χωρίς αυτοτελή νομική λογική" in page
assert "legal-audit-status--not_applicable" in page
m=re.search(r"EDU_TOOLS_VERSION', '([^']+)'", config); assert m
assert ("CACHE_PREFIX + '%s'" % m.group(1)) in sw
# specific current rules preserved by the verified tools
par=(ROOT/'posa-paravola.php').read_text(encoding='utf-8')
parjs=(ROOT/'includes/paravola-ui.js').read_text(encoding='utf-8')
assert 'δεκαπέντε (15) ευρώ' in par and 'costPerParavolo = 15' in parjs
obj=(ROOT/'includes/objection-guide-ui.js').read_text(encoding='utf-8')
assert "2026-08-12T08:00:00+03:00" in obj and "2026-08-21T14:00:00+03:00" in obj and '50,00 €' in obj
onas=(ROOT/'ypologismos-morion-onaseia.php').read_text(encoding='utf-8')
onjs=(ROOT/'includes/onaseia-ui.js').read_text(encoding='utf-8')
assert '1,5 μόριο ανά αναγνωρισμένο μήνα' in onas and '15 μόρια ανά σχολικό έτος' in onas
assert 'Math.min(10, enteredMonths)' in onjs and 'months * 1.5' in onjs
scale=(ROOT/'metatropi-klimakas.php').read_text(encoding='utf-8')
assert 'ΚΑΛΩΣ (5)' in scale and 'ΛΙΑΝ ΚΑΛΩΣ (6,5)' in scale and 'ΑΡΙΣΤΑ (8,5)' in scale
print('PASS: legal freshness wave 5 / v3.22.92')
