from pathlib import Path
import io, json, subprocess, zipfile
ROOT=Path(__file__).resolve().parents[1]
urls={
 'de':'https://www.minedu.gov.gr/site/71035-15-10-26-metatheseis-ekpaideutikon-deuterobathmias-ekpaideuses-scholikou-etous-2026-2027',
 'pe':'https://www.minedu.gov.gr/site/71034-15-10-26-metatheseis-ekpaideutikon-protobathmias-ekpaideuses-scholikou-etous-2026-2027',
 'eep-evp':'https://www.minedu.gov.gr/site/71033-15-10-26-prosklese-melon-eidikou-ekpaideutikou-prosopikou-eep-kai-eidikou-boethetikou-prosopikou-ebp-gia-ypobole-aiteseon-metatheses-scholikou-etous-2026-2027',
}
ada={'de':'ΨΚΩΦ46ΝΚΠΔ-63Κ', 'pe':'9Ρ7Ν46ΝΚΠΔ-5Κ5','eep-evp':'6ΑΒΑ46ΝΚΠΔ-ΠΡΘ'}
def php(arg):
    p=subprocess.run(['php',*arg],cwd=ROOT,capture_output=True)
    assert p.returncode==0,p.stderr.decode(errors='replace')
    return p.stdout
records=json.loads(php(['-r','echo json_encode(require "includes/service-timeline-data.php", JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);']).decode())
events=records['events']; original_count=59
assert len(events)==original_count
count={'de':0,'pe':0,'eep-evp':0}
for event in events:
    ss=event.get('sources',[])
    if not ss: continue
    for lvl,tag in ada.items():
        if lvl in event.get('levels',[]) and any(tag in s['url'] for s in ss):
            count[lvl]+=1
            assert ss[0]['url']==urls[lvl],event['id']
            assert ss[0]['label'].startswith('ΥΠΑΙΘΑ —'),event['id']
            assert any('diavgeia.gov.gr' in s['url'] and tag in s['url'] and 'Διαύγεια' in s['label'] for s in ss),event['id']
for lvl in count: assert count[lvl]>=3,(lvl,count)
html=php(['xronodiagramma-ypiresiakon-metavolon.php']).decode()
for url in urls.values(): assert html.count(url)>=2,url
for tag in ada.values(): assert tag in html,tag
xlsx=php(['xronodiagramma-ypiresiakon-metavolon-export.php'])
assert xlsx.startswith(b'PK')
with zipfile.ZipFile(io.BytesIO(xlsx)) as z:
    assert z.testzip() is None
    sources=z.read('xl/worksheets/sheet2.xml').decode()
    for url in urls.values(): assert url in sources,('missing excel',url)
    for tag in ada.values(): assert tag in sources,('missing excel ADA',tag)
assert "'3.22.116'" in (ROOT/'includes/config.php').read_text()
assert "'3.22.116'" in (ROOT/'service-worker.js').read_text()
print('PASS: official ΥΠΑΙΘΑ primary links + Diavgeia preserved in summary, events, XLSX; events',len(events),'covered',count)
