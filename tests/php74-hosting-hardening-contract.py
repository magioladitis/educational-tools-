#!/usr/bin/env python3
from pathlib import Path
import subprocess
import sys

ROOT = Path(__file__).resolve().parents[1]
checks = []

def check(cond, label):
    checks.append((bool(cond), label))

config = (ROOT / 'includes/config.php').read_text(encoding='utf-8')
auth = (ROOT / 'includes/vacancies-auth.php').read_text(encoding='utf-8')
service_worker = (ROOT / 'service-worker.js').read_text(encoding='utf-8')

check("date_default_timezone_set('Europe/Athens');" in config,
      'shared config pins Europe/Athens')
check("define('EDU_TOOLS_VERSION', '3.22.84');" in config,
      'release version is 3.22.84')
check("session.use_strict_mode', '1'" in auth,
      'vacancy sessions enable strict mode')
check("session.cookie_httponly', '1'" in auth,
      'vacancy sessions force HttpOnly')
check("session.cookie_secure', $secure ? '1' : '0'" in auth,
      'Secure cookie follows HTTPS detection')
check(auth.index("session.use_strict_mode") < auth.index('session_start();'),
      'strict mode configured before session_start')
check(auth.index("session.cookie_httponly") < auth.index('session_start();'),
      'HttpOnly configured before session_start')
check("CACHE_PREFIX + '3.22.84'" in service_worker,
      'service worker cache bumped to 3.22.84')

php_code = r"""
$_SERVER['HTTPS'] = 'on';
require $argv[1] . '/includes/config.php';
require $argv[1] . '/includes/vacancies-auth.php';
vacanciesSessionStart();
$p = session_get_cookie_params();
$values = array(
    date_default_timezone_get(),
    ini_get('session.use_strict_mode'),
    ini_get('session.cookie_httponly'),
    ini_get('session.cookie_secure'),
    !empty($p['httponly']) ? '1' : '0',
    !empty($p['secure']) ? '1' : '0'
);
echo implode('|', $values);
"""
try:
    proc = subprocess.run(
        ['php', '-r', php_code, str(ROOT)],
        stdout=subprocess.PIPE,
        stderr=subprocess.PIPE,
        text=True,
        timeout=15,
    )
    runtime = proc.stdout.strip()
    check(proc.returncode == 0, 'runtime PHP smoke test exits successfully')
    check(runtime == 'Europe/Athens|1|1|1|1|1',
          'runtime timezone/session hardening values are effective over HTTPS')
except Exception as exc:
    check(False, 'runtime PHP smoke test: %s' % exc)

failed = [label for ok, label in checks if not ok]
for ok, label in checks:
    print(('PASS' if ok else 'FAIL') + ': ' + label)
if failed:
    print('RESULT: PHP 7.4 hosting hardening contract FAIL (%d/%d)' % (len(failed), len(checks)))
    sys.exit(1)
print('RESULT: PHP 7.4 hosting hardening contract PASS (%d/%d)' % (len(checks), len(checks)))
