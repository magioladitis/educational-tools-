#!/usr/bin/env python3
from pathlib import Path
import re, subprocess, sys
ROOT=Path(__file__).resolve().parents[1]
PASS=FAIL=0

def check(cond,msg):
    global PASS,FAIL
    if cond:
        PASS+=1; print('PASS',msg)
    else:
        FAIL+=1; print('FAIL',msg)

header=(ROOT/'includes/header.php').read_text(encoding='utf-8')
home=(ROOT/'ergaleia.php').read_text(encoding='utf-8')
app=(ROOT/'assets/app-experience.js').read_text(encoding='utf-8')
css=(ROOT/'assets/common.css').read_text(encoding='utf-8')
saek=(ROOT/'dikaioma-ypodiefthynti-saek.php').read_text(encoding='utf-8')
saek_js=(ROOT/'includes/saek-deputy-eligibility-ui.js').read_text(encoding='utf-8')
config=(ROOT/'includes/config.php').read_text(encoding='utf-8')
sw=(ROOT/'service-worker.js').read_text(encoding='utf-8')

# Shared app experience is centralized, progressive, and client-only.
check('assets/app-experience.js' in header and ' defer ' in header, 'shared app-experience module loads once from common header')
check("require __DIR__ . '/tools-catalog.php'" in header, 'header resolves current tool from canonical tools catalog')
check('data-edu-current-tool-href' in header, 'tool pages expose canonical href to client experience layer')
check('data-edu-favorite-toggle' in header, 'favorite action is present for catalog tools')
check('data-edu-share' in header, 'share action is present in global menu')
check('data-edu-install' in header, 'install action is present in global menu')
check('>Μενού<' in header and 'edu-tools-global-menu__heading">Κατηγορίες' in header, 'hamburger remains a compact menu with categorized actions')

check('data-edu-personal-tools' in home, 'home has optional personal tools section')
check('data-edu-personal-group="recent"' in home, 'home has recents host')
check('data-edu-personal-group="favorites"' in home, 'home has favorites host')
check('eduToolsRecentV1' in app and 'eduToolsFavoritesV1' in app, 'recents/favorites use versioned local-only storage keys')
check('STORAGE_NAMESPACE' in app and "link[rel=\"manifest\"]" in app, 'local personalization is namespaced to the app path on shared origins')
check('MAX_RECENTS = 5' in app, 'recents are intentionally bounded')
check('favoriteSet' in app and "return !favoriteSet[href]" in app, 'recent list excludes tools already shown as favorites')
check('window.localStorage' in app, 'personalization is localStorage-based')
check('navigator.share' in app and 'navigator.clipboard' in app, 'share uses native API with clipboard fallback')
check('beforeinstallprompt' in app and 'prompt.prompt()' in app, 'Chromium native install prompt is supported')
check("navigator.standalone === true" in app and "display-mode: standalone" in app, 'installed PWA mode is detected on iOS and standards path')
check('Προσθήκη στην οθόνη Αφετηρίας' in app and 'Άνοιγμα ως εφαρμογή ιστού' in app, 'iPhone/Safari install instructions match production-tested flow')
check('Εγκατάσταση εφαρμογής' in app and 'Προσθήκη στην αρχική οθόνη' in app, 'Android/Chrome fallback instructions are present')
check('fetch(' not in app and 'XMLHttpRequest' not in app, 'frontend polish adds no server request path')
check('edu-personal-tools__grid' in css and 'edu-install-help' in css and 'edu-app-toast' in css, 'new UI has shared responsive styling')
check('@media (display-mode: standalone)' in css, 'install action is hidden in standalone display mode')

# Home hero stays introductory instead of duplicating nearby navigation.
check('5 βασικές κατηγορίες' not in home, 'home hero removes redundant category-count badge')
check('hero-actions' not in home and 'Δες κατηγορίες' not in home and 'Όλα τα εργαλεία' not in home, 'home hero removes redundant CTA row')
check(home.count('διαθέσιμα εργαλεία') == 1, 'home hero keeps only the useful tool-count badge')

# SAEK deputy form: consistent binary order, no unnecessary progress bar.
check('progress-panel' not in saek and 'progressText' not in saek and 'progressFill' not in saek, 'SAEK page no longer renders progress UI')
check('updateProgress' not in saek_js and 'progressText' not in saek_js and 'progressFill' not in saek_js, 'SAEK controller has no dead progress logic')
for field in ('requiredDegree','experience','evaluationRefusal','unsuitable','retirement'):
    m=re.search(r'<select id="'+re.escape(field)+r'">(.*?)</select>',saek,re.S)
    check(m is not None, f'{field} select exists')
    if m:
        values=re.findall(r'<option value="([^"]*)">',m.group(1))
        check(values[:4] == ['', 'yes', 'no', 'unknown'], f'{field} uses consistent yes/no/unknown order')

# Directory categories are the single filtering surface; redundant chips are gone.
check('class="filters"' not in home and 'class="filter-btn' not in home, 'directory removes duplicated category filter chips and All button')
check('data-directory-label' in home and 'activeCategoryFilter' in home and 'clearCategoryFilter' in home, 'category cards feed the active-filter summary and clear action')
check('active-category-filter' in css and '.category-card.is-active' in css, 'active category has compact shared visual state')

# Runtime version/cache alignment and syntax.
check("define('EDU_TOOLS_VERSION', '3.22.19');" in config, 'runtime asset version bumped to 3.22.19')
check("CACHE_NAME = CACHE_PREFIX + '3.22.19'" in sw, 'service-worker cache version matches runtime version')
check(subprocess.run(['node','--check',str(ROOT/'assets/app-experience.js')],capture_output=True).returncode==0, 'app-experience JS syntax')
check(subprocess.run(['node','--check',str(ROOT/'includes/saek-deputy-eligibility-ui.js')],capture_output=True).returncode==0, 'SAEK JS syntax')
check(subprocess.run(['php','-l',str(ROOT/'includes/header.php')],capture_output=True).returncode==0, 'header PHP syntax')
check(subprocess.run(['php','-l',str(ROOT/'ergaleia.php')],capture_output=True).returncode==0, 'home PHP syntax')
check(subprocess.run(['php','-l',str(ROOT/'dikaioma-ypodiefthynti-saek.php')],capture_output=True).returncode==0, 'SAEK PHP syntax')

print(f'RESULT {PASS} PASS / {FAIL} FAIL')
sys.exit(1 if FAIL else 0)
