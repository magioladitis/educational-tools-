from pathlib import Path
import re
import subprocess
from lxml import html

ROOT=Path(__file__).resolve().parents[1]
PAGE=ROOT/'ypologismos-morion-sivitanidios-saek.php'
CSS=ROOT/'assets/common.css'
JS=ROOT/'includes/sivitanidios-saek-calculations.js'
passes=fails=0

def check(name, cond):
    global passes,fails
    print(('PASS' if cond else 'FAIL'), name)
    if cond: passes+=1
    else: fails+=1

src=PAGE.read_text()
css=CSS.read_text()
js=JS.read_text()
proc=subprocess.run(['php', PAGE.name], cwd=ROOT, stdout=subprocess.PIPE, stderr=subprocess.PIPE, text=True)
check('PHP render', proc.returncode==0)
doc=html.document_fromstring(proc.stdout)
ids=doc.xpath('//*[@id]/@id')
check('no duplicate ids', len(ids)==len(set(ids)))
check('six subsection score badges', len(doc.xpath("//*[contains(concat(' ', normalize-space(@class), ' '), ' subsection-max ')]"))==6)
for text in ('έως 14','έως 3','έως 6','έως 12','έως 4','έως 5'):
    check('subsection badge '+text, text in [x.text_content().strip() for x in doc.xpath("//*[contains(concat(' ', normalize-space(@class), ' '), ' subsection-max ')]")])
check('main section max badge CSS', 'body.edu-page-sivitanidios-saek .section-head .max' in css)
check('language selector has six choices plus empty', len(doc.xpath("//*[@id='languageName']/option"))==7)
check('article 28 explicit status selector exists', bool(doc.xpath("//*[@id='languageArticle28Status']")))
check('title registration checkbox exists', bool(doc.xpath("//*[@id='languageTitleRegistered']")))
check('official translation checkbox exists', bool(doc.xpath("//*[@id='languageOfficialTranslation']")))
check('official note mentions five languages', all(x in src for x in ['αγγλική','γαλλική','γερμανική','ιταλική','ισπανική']))
check('official note cites article 28 PD 50/2001', 'άρθρου 28 του Π.Δ. 50/2001' in src)
check('engine recognizes five translation-exempt language names', "['english', 'french', 'german', 'italian', 'spanish']" in js)
check('engine warns for missing translation package', 'επίσημη μετάφραση' in js)
check('cache 3.20.49', "define('EDU_TOOLS_VERSION', '3.20.49')" in (ROOT/'includes/config.php').read_text())
print(f'RESULT {passes} PASS / {fails} FAIL')
raise SystemExit(1 if fails else 0)
