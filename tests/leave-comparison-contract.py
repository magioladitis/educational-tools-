from pathlib import Path
import re

root = Path(__file__).resolve().parents[1]
comparison_page = (root / 'adeies-sygkrisi.php').read_text(encoding='utf-8')
comparison_map = (root / 'includes/leave-comparison-map.php').read_text(encoding='utf-8')
shared_page = (root / 'includes/leave-guide-page.php').read_text(encoding='utf-8')
hub = (root / 'adeies-ekpaideutikon.php').read_text(encoding='utf-8')
css = (root / 'assets/leave-guide.css').read_text(encoding='utf-8')
permanent_data = (root / 'includes/permanent-leaves-data.php').read_text(encoding='utf-8')
substitute_data = (root / 'includes/substitute-leaves-data.php').read_text(encoding='utf-8')

checks = {
    'comparison page exists': 'Σύγκριση Αδειών' in comparison_page,
    'uses permanent dataset': 'permanent-leaves-data.php' in comparison_page,
    'uses substitute dataset': 'substitute-leaves-data.php' in comparison_page,
    'uses explicit mapping': 'leave-comparison-map.php' in comparison_page,
    'comparison is linkable by query string': "$_GET['leave']" in comparison_page,
    'comparison has official sources': 'Επίσημες πηγές' in comparison_page,
    'comparison has side-by-side grid': 'leave-compare-grid' in comparison_page and 'grid-template-columns: repeat(2' in css,
    'mobile comparison stacks': re.search(r'@media \(max-width: 760px\)[\s\S]*?leave-compare-grid \{ grid-template-columns: 1fr;', css) is not None,
    'guides link to comparison': 'adeies-sygkrisi.php?leave=' in shared_page,
    'hub links to comparison': 'href="adeies-sygkrisi.php"' in hub,
    'mapping includes child illness': "'id' => 'astheneia-teknou'" in comparison_map,
    'mapping includes school performance': "'id' => 'sxoliki-epidosi'" in comparison_map,
    'mapping includes child-rearing comparison': "'id' => 'anatrofis'" in comparison_map,
    'mapping avoids automatic title matching': 'auto-match' in comparison_map,
    'duration comparison is semantic not raw text': 'same_base_duration' in comparison_map and "array_key_exists('same_base_duration', $entry)" in comparison_page,
    'summary distinguishes proportionality': 'Αναλογικότητα:' in comparison_page,
    'marriage wording normalized': permanent_data.count("'duration' => '5 εργάσιμες ημέρες'") >= 1 and substitute_data.count("'duration' => '5 εργάσιμες ημέρες'") >= 1,
    'blood donation wording normalized': "2 εργάσιμες ημέρες πέραν της ημέρας αιμοδοσίας · έως 6 φορές/έτος" in permanent_data and "2 εργάσιμες ημέρες πέραν της ημέρας αιμοδοσίας · έως 6 φορές/έτος" in substitute_data,
    'special illness wording normalized': "22 εργάσιμες ημέρες · έως 32 σε ειδικές περιπτώσεις" in permanent_data and "22 εργάσιμες ημέρες · έως 32 σε ειδικές περιπτώσεις" in substitute_data,
    'disability wording normalized': "6 εργάσιμες ημέρες · έως 10 σε ειδικές περιπτώσεις" in permanent_data and "6 εργάσιμες ημέρες · έως 10 σε ειδικές περιπτώσεις" in substitute_data,
}

failed = [name for name, ok in checks.items() if not ok]
for name, ok in checks.items():
    print(('PASS' if ok else 'FAIL') + ': ' + name)
if failed:
    raise SystemExit(f'{len(failed)} failed checks')
print(f'{len(checks)}/{len(checks)} checks passed')
