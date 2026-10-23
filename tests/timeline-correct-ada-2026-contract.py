from pathlib import Path
root=Path(__file__).resolve().parents[1]
files=[root/'includes/service-timeline-data.php',root/'includes/deadlines.php',root/'xronodiagramma-ypiresiakon-metavolon.php']
for f in files:
    s=f.read_text(encoding='utf-8')
    assert 'ΨΚΩΦ46ΝΚΠΔ-63Κ' not in s, f
    assert 'ΨΚΩΖ46ΝΚΠΔ-63Κ' in s, f
print('PASS: official secondary transfers circular ADA matched across timeline and deadlines')
