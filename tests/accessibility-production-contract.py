#!/usr/bin/env python3
from __future__ import annotations
import html.parser, pathlib, socket, subprocess, time, urllib.request, sys

ROOT = pathlib.Path(__file__).resolve().parents[1]
FAIL=[]; OK=[]
def check(label, cond):
    (OK if cond else FAIL).append(label)

class AuditParser(html.parser.HTMLParser):
    def __init__(self):
        super().__init__(); self.labels_for=set(); self.controls=[]; self._label_depth=0
    def handle_starttag(self, tag, attrs):
        a=dict(attrs)
        if tag=='label':
            self._label_depth += 1
            if a.get('for'): self.labels_for.add(a['for'])
        if tag in ('input','select','textarea'):
            if tag=='input' and a.get('type','').lower()=='hidden': return
            if 'hidden' in a: return
            self.controls.append((tag,a,self._label_depth>0))
    def handle_endtag(self, tag):
        if tag=='label' and self._label_depth: self._label_depth -= 1

def free_port():
    s=socket.socket(); s.bind(('127.0.0.1',0)); p=s.getsockname()[1]; s.close(); return p

pages = ['ergaleia.php','anatheseis-mathimaton.php','prothesmies.php','ypologismos-didaktikon-anagkon.php','ypologismos-misthologikou-klimakiou.php','ypologismos-morion-onaseia.php','metatropi-klimakas.php']
port=free_port()
proc=subprocess.Popen(['php','-S',f'127.0.0.1:{port}'],cwd=ROOT,stdout=subprocess.DEVNULL,stderr=subprocess.DEVNULL)
try:
    time.sleep(.25)
    for page in pages:
        data=urllib.request.urlopen(f'http://127.0.0.1:{port}/{page}',timeout=8).read().decode('utf-8','replace')
        parser=AuditParser(); parser.feed(data)
        unlabeled=[]
        for tag,a,nested in parser.controls:
            cid=a.get('id','')
            named=bool(a.get('aria-label') or a.get('aria-labelledby') or nested or (cid and cid in parser.labels_for))
            if not named: unlabeled.append(a.get('name') or cid or tag)
        check(f'{page}: all visible form controls have accessible names', not unlabeled)
        check(f'{page}: document language el', '<html lang="el"' in data or '<html lang="el-GR"' in data)
        if 'includes/header.php' in (ROOT/page).read_text(errors='ignore'):
            check(f'{page}: skip link rendered', 'class="edu-skip-link"' in data and 'href="#main-content"' in data)
finally:
    proc.terminate(); proc.wait(timeout=3)

css=(ROOT/'assets/common.css').read_text()
js=(ROOT/'assets/common.js').read_text()
header=(ROOT/'includes/header.php').read_text()
pwa=(ROOT/'assets/pwa.js').read_text()
sw=(ROOT/'service-worker.js').read_text()
check('solid visible focus indicator', 'outline: 3px solid #1f6feb' in css)
check('reduced motion CSS exists', '@media (prefers-reduced-motion: reduce)' in css)
check('back-to-top respects reduced motion', "prefersReducedMotion() ? 'auto' : 'smooth'" in js)
check('main landmark target installed', "main.id = 'main-content'" in js)
check('global skip link lives in shared header', 'edu-skip-link' in header)
check('navigation exposes aria-current', 'aria-current="page"' in header)
check('SW registration bypasses HTTP cache for updates', "updateViaCache: 'none'" in pwa)
check('service worker never intercepts navigation explicitly by asset allowlist', 'STATIC_ASSET_RE' in sw and 'Never cache navigations/PHP/HTML' in sw)

for label in OK: print('  ✔',label)
for label in FAIL: print('  ✘',label)
print(f'Accessibility / production PWA contract: {len(OK)}/{len(OK)+len(FAIL)} passed')
sys.exit(1 if FAIL else 0)
