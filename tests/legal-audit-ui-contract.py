from pathlib import Path
ROOT = Path(__file__).resolve().parents[1]
checks = []
def check(ok, msg):
    checks.append((ok,msg))

sc=(ROOT/'includes/components/source-card.php').read_text()
css=(ROOT/'assets/common.css').read_text()
page=(ROOT/'nomiki-epikairotita.php').read_text()
footer=(ROOT/'includes/footer.php').read_text()
config=(ROOT/'includes/config.php').read_text()
sw=(ROOT/'service-worker.js').read_text()
check("legal-audit.php" in sc, "source card loads legal audit registry")
check("legalAuditIsPubliclyVisible" in sc, "source card only exposes explicit audit states")
check("edu-source-audit" in css, "shared audit badge CSS exists")
check("Χωρίς καταγραφή" in page and "δεν σημαίνει ότι ένα εργαλείο είναι λανθασμένο" in page, "dashboard explains pending semantics")
check("nomiki-epikairotita.php" in footer, "footer links to legal freshness dashboard")
version_match = __import__('re').search(r"define\('EDU_TOOLS_VERSION',\s*'([^']+)'\)", config)
cache_match = __import__('re').search(r"CACHE_NAME\s*=\s*CACHE_PREFIX\s*\+\s*'([^']+)'", sw)
check(bool(version_match and cache_match and version_match.group(1) == cache_match.group(1)), "version and service-worker cache remain synchronized")
for ok,msg in checks:
    print(("PASS" if ok else "FAIL")+": "+msg)
raise SystemExit(0 if all(ok for ok,_ in checks) else 1)
