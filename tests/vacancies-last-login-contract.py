from pathlib import Path
import re

root = Path(__file__).resolve().parents[1]
auth = (root / 'includes' / 'vacancies-auth.php').read_text(encoding='utf-8')
admin = (root / 'kena-sxoleion-admin.php').read_text(encoding='utf-8')
css = (root / 'assets' / 'vacancies.css').read_text(encoding='utf-8')

checks = []
def check(cond, msg):
    checks.append((bool(cond), msg))

check('last_login_at FROM vacancy_users WHERE username=? LIMIT 1' in auth,
      'login query reads the previous last_login_at value')
check("'previous_login_at' =>" in auth,
      'previous login timestamp is copied into the authenticated session')
check(auth.find("'previous_login_at' =>") < auth.find('UPDATE vacancy_users SET last_login_at=NOW()'),
      'previous timestamp is captured before last_login_at is advanced')
check('function vacanciesActorPreviousLoginAt()' in auth,
      'previous-login session accessor exists')
check('function vacanciesFormatLoginDateTime($value)' in auth,
      'login timestamp formatter exists')
check("date('d/m/Y H:i', $timestamp)" in auth,
      'timestamp is rendered as day/month/year hour:minute')
check('Τελευταία επιτυχής σύνδεση:' in admin,
      'admin dashboard labels the value as the previous successful login')
check('Πρώτη καταγεγραμμένη σύνδεση' in admin,
      'first-login state is explicit')
check("$adminActor['auth_mode'] === 'account'" in admin,
      'login history is only shown for real account authentication')
check('vacancy-last-login' in css,
      'last-login line has a dedicated discreet style')

failed = [msg for ok, msg in checks if not ok]
for ok, msg in checks:
    print(('PASS' if ok else 'FAIL') + ': ' + msg)
if failed:
    raise SystemExit(f'{len(failed)} failure(s)')
print(f'RESULT: vacancies last-login contract PASS ({len(checks)}/{len(checks)})')
