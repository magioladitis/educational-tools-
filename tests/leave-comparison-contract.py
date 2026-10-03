from pathlib import Path
import json
import re
import subprocess

root = Path(__file__).resolve().parents[1]
comparison_page = (root / 'adeies-sygkrisi.php').read_text(encoding='utf-8')
comparison_map = (root / 'includes/leave-comparison-map.php').read_text(encoding='utf-8')
comparison_js = (root / 'includes/leave-comparison-ui.js').read_text(encoding='utf-8')
shared_page = (root / 'includes/leave-guide-page.php').read_text(encoding='utf-8')
hub = (root / 'adeies-ekpaideutikon.php').read_text(encoding='utf-8')
css = (root / 'assets/leave-guide.css').read_text(encoding='utf-8')
permanent_data = (root / 'includes/permanent-leaves-data.php').read_text(encoding='utf-8')
substitute_data = (root / 'includes/substitute-leaves-data.php').read_text(encoding='utf-8')

php_probe = r'''
$p=require "includes/permanent-leaves-data.php";
$s=require "includes/substitute-leaves-data.php";
$m=require "includes/leave-comparison-map.php";
$pi=[]; foreach($p["leaves"] as $x){$pi[$x["id"]]=$x;}
$si=[]; foreach($s["leaves"] as $x){$si[$x["id"]]=$x;}
$out=[];
foreach($m as $e){
  $a=$pi[$e["permanent"]]??null; $b=$si[$e["substitute"]]??null;
  $out[]=[
    "id"=>$e["id"],
    "permanent_meta"=>is_array($a["comparison"]??null) && is_array($a["comparison"]["duration"]??null),
    "substitute_meta"=>is_array($b["comparison"]??null) && is_array($b["comparison"]["duration"]??null)
  ];
}
echo json_encode($out, JSON_UNESCAPED_UNICODE);
'''
probe = subprocess.run(['php', '-r', php_probe], cwd=root, capture_output=True, text=True)
meta_rows = json.loads(probe.stdout) if probe.returncode == 0 and probe.stdout else []
all_have_semantics = bool(meta_rows) and all(r['permanent_meta'] and r['substitute_meta'] for r in meta_rows)

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
    'map no longer hardcodes duration equality': 'same_base_duration' not in comparison_map,
    'all mapped leaves expose structured comparison semantics': all_have_semantics,
    'comparison payload is serialized for client engine': 'id="leaveComparisonData"' in comparison_page and 'json_encode($comparisonPayload' in comparison_page,
    'summary is rendered client-side': 'data-leave-compare-summary' in comparison_page and "summaryItem('Βασική διάρκεια'" in comparison_js,
    'client engine compares structured base duration': 'baseDurationSignature' in comparison_js and 'duration.kind' in comparison_js,
    'client engine compares special cases': 'extraDurationSignature' in comparison_js and "summaryItem('Ειδικές περιπτώσεις'" in comparison_js,
    'client engine compares frequency separately': 'frequencySignature' in comparison_js and "summaryItem('Συχνότητα'" in comparison_js,
    'client engine compares scope separately': 'scopeSignature' in comparison_js and "summaryItem('Πεδίο δικαιώματος'" in comparison_js,
    'client engine distinguishes proportionality': "summaryItem('Αναλογικότητα'" in comparison_js,
    'comparison offers differences filter': 'id="leaveCompareView"' in comparison_page and "view === 'differences'" in comparison_js,
    'comparison offers same-base filter': 'value="same-base"' in comparison_page and "view === 'same-base'" in comparison_js,
    'comparison remains request-free after load': 'fetch(' not in comparison_js and 'XMLHttpRequest' not in comparison_js,
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
