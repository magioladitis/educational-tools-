from pathlib import Path
s=(Path(__file__).resolve().parents[1]/"includes/substitute-leaves-data.php").read_text(encoding="utf-8")
assert "'id' => 'goniki'" in s
assert "νέα αναγγελία Ε.14" in s
assert "δεν έχει συμπληρώσει τα 8 έτη" in s
assert "δεν σημαίνει ότι η άδεια παρατείνει αυτοδικαίως τη σύμβαση" in s
assert "'dypa_parental'" in s
print('parental continuation contract PASS')
