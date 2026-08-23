from pathlib import Path
import sys
root=Path(sys.argv[1]) if len(sys.argv)>1 else Path('.')
files=sorted(root.glob('*.php'))
errors=[]
for p in files:
    s=p.read_text(encoding='utf-8',errors='replace')
    op=s.count('<!--'); cl=s.count('-->')
    if op != cl:
        errors.append(f'{p.name}: HTML comments unbalanced ({op} opens / {cl} closes)')
    if '<head>' in s and '<meta charset=' not in s.lower():
        errors.append(f'{p.name}: missing meta charset')
if errors:
    print('\n'.join(errors)); raise SystemExit(1)
print(f'PASS: {len(files)} PHP files have balanced HTML comments and meta charset')
