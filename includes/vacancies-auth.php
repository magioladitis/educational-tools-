<?php
require_once __DIR__ . '/vacancies-db.php';

function vacanciesSessionStart()
{
    if (session_status() !== PHP_SESSION_NONE) return;
    $secure = !empty($_SERVER['HTTPS']) && strtolower((string) $_SERVER['HTTPS']) !== 'off';
    if (!headers_sent()) session_set_cookie_params(0, '/', '', $secure, true);
    session_start();
}

function vacanciesRandomToken()
{
    if (function_exists('random_bytes')) return bin2hex(random_bytes(24));
    if (function_exists('openssl_random_pseudo_bytes')) return bin2hex(openssl_random_pseudo_bytes(24));
    return sha1(uniqid(mt_rand(), true)) . sha1(uniqid(mt_rand(), true));
}

function vacanciesCsrfToken()
{
    vacanciesSessionStart();
    if (empty($_SESSION['vacancies_csrf'])) $_SESSION['vacancies_csrf'] = vacanciesRandomToken();
    return $_SESSION['vacancies_csrf'];
}

function vacanciesCsrfValid($token)
{
    vacanciesSessionStart();
    $known = isset($_SESSION['vacancies_csrf']) ? (string) $_SESSION['vacancies_csrf'] : '';
    $token = (string) $token;
    if ($known === '' || $token === '') return false;
    return function_exists('hash_equals') ? hash_equals($known, $token) : $known === $token;
}

function vacanciesActor()
{
    vacanciesSessionStart();
    return isset($_SESSION['vacancies_actor']) && is_array($_SESSION['vacancies_actor'])
        ? $_SESSION['vacancies_actor'] : null;
}

function vacanciesIsAdmin()
{
    $actor = vacanciesActor();
    return $actor && isset($actor['role']) && $actor['role'] === 'admin';
}

function vacanciesActorSchoolId()
{
    $actor = vacanciesActor();
    if (!$actor || !isset($actor['role']) || !isset($actor['school_id'])) return 0;
    return in_array($actor['role'], array('school', 'school_director'), true) ? (int) $actor['school_id'] : 0;
}

function vacanciesActorNeedsPasswordChange()
{
    $actor = vacanciesActor();
    return $actor && !empty($actor['user_id']) && !empty($actor['must_change_password']);
}

function vacanciesActorDisplayName()
{
    $actor = vacanciesActor();
    if (!$actor) return '';
    if (!empty($actor['display_name'])) return (string) $actor['display_name'];
    if (!empty($actor['username'])) return (string) $actor['username'];
    return $actor['role'] === 'admin' ? 'Διαχειριστής' : 'Διευθυντής Σχολείου';
}

function vacanciesRedirectForActor($actor)
{
    if (!$actor || !is_array($actor)) return 'kena-sxoleion-login.php';
    if (!empty($actor['user_id']) && !empty($actor['must_change_password'])) return 'kena-sxoleion-password.php';
    return isset($actor['role']) && $actor['role'] === 'admin' ? 'kena-sxoleion-admin.php' : 'kena-sxoleion.php';
}

function vacanciesAccountsReady()
{
    static $ready = null;
    if ($ready !== null) return $ready;
    $db = vacanciesDb();
    if (!$db) return $ready = false;
    $result = $db->query("SHOW TABLES LIKE 'vacancy_users'");
    if (!$result) return $ready = false;
    $ready = $result->num_rows > 0;
    $result->free();
    return $ready;
}

function vacanciesAccountLogin($username, $password)
{
    if (!vacanciesAccountsReady()) return false;
    $db = vacanciesDb();
    $username = trim((string) $username);
    $password = (string) $password;
    if ($username === '' || $password === '') return false;

    $stmt = $db->prepare("SELECT id, username, password_hash, role, school_id, display_name, active, must_change_password FROM vacancy_users WHERE username=? LIMIT 1");
    if (!$stmt) return false;
    $stmt->bind_param('s', $username);
    if (!$stmt->execute()) { $stmt->close(); return false; }
    $id = 0; $dbUsername = ''; $hash = ''; $role = ''; $schoolId = null; $displayName = ''; $active = 0; $mustChange = 0;
    $stmt->bind_result($id, $dbUsername, $hash, $role, $schoolId, $displayName, $active, $mustChange);
    $found = $stmt->fetch();
    $stmt->close();
    if (!$found || !$active || !password_verify($password, (string) $hash)) return false;
    $user = array(
        'id' => (int) $id,
        'username' => (string) $dbUsername,
        'password_hash' => (string) $hash,
        'role' => (string) $role,
        'school_id' => $schoolId === null ? null : (int) $schoolId,
        'display_name' => (string) $displayName,
        'active' => (int) $active,
        'must_change_password' => (int) $mustChange,
    );
    if ($user['role'] === 'school_director' && (int) $user['school_id'] <= 0) return false;

    vacanciesSessionStart();
    session_regenerate_id(true);
    $_SESSION['vacancies_actor'] = array(
        'user_id' => (int) $user['id'],
        'username' => (string) $user['username'],
        'display_name' => (string) $user['display_name'],
        'role' => (string) $user['role'],
        'school_id' => $user['role'] === 'school_director' ? (int) $user['school_id'] : null,
        'must_change_password' => !empty($user['must_change_password']) ? 1 : 0,
        'auth_mode' => 'account',
    );
    $db->query("UPDATE vacancy_users SET last_login_at=NOW() WHERE id=" . (int) $user['id']);
    return true;
}

function vacanciesGenerateTemporaryPassword($length)
{
    $length = max(16, (int) $length);
    $alphabet = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz23456789!@#%';
    $max = strlen($alphabet) - 1;
    $out = '';
    for ($i = 0; $i < $length; $i++) {
        $n = function_exists('random_int') ? random_int(0, $max) : mt_rand(0, $max);
        $out .= $alphabet[$n];
    }
    return $out;
}

function vacanciesUsernameValid($username)
{
    return (bool) preg_match('/^[A-Za-z0-9._-]{3,80}$/', (string) $username);
}

function vacanciesCreateDirectorAccount($schoolId)
{
    if (!vacanciesAccountsReady()) return array(false, 'Δεν έχει εγκατασταθεί ακόμη το schema λογαριασμών.');
    $db = vacanciesDb();
    $schoolId = (int) $schoolId;
    $school = vacanciesQueryOne("SELECT id, ministry_code, name FROM vacancy_schools WHERE active=1 AND id=".$schoolId);
    if (!$school) return array(false, 'Μη έγκυρη σχολική μονάδα.');
    $existing = vacanciesQueryOne("SELECT id FROM vacancy_users WHERE school_id=".$schoolId." LIMIT 1");
    if ($existing) return array(false, 'Υπάρχει ήδη λογαριασμός διευθυντή για αυτό το σχολείο.');
    $username = (string) $school['ministry_code'];
    if (!vacanciesUsernameValid($username)) $username = 'school_' . $schoolId;
    $password = vacanciesGenerateTemporaryPassword(18);
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $role = 'school_director';
    $displayName = 'Διευθυντής — ' . (string) $school['name'];
    $stmt = $db->prepare("INSERT INTO vacancy_users (username,password_hash,role,school_id,display_name,active,must_change_password) VALUES (?,?,?,?,?,1,1)");
    if (!$stmt) return array(false, 'Αποτυχία δημιουργίας λογαριασμού.');
    $stmt->bind_param('sssis', $username, $hash, $role, $schoolId, $displayName);
    $ok = $stmt->execute();
    $error = $stmt->error;
    $stmt->close();
    if (!$ok) return array(false, 'Δεν δημιουργήθηκε ο λογαριασμός: ' . $error);
    return array(true, 'Ο λογαριασμός δημιουργήθηκε.', $username, $password);
}

function vacanciesCreateAdminAccount($username, $displayName)
{
    if (!vacanciesAccountsReady()) return array(false, 'Δεν έχει εγκατασταθεί ακόμη το schema λογαριασμών.');
    $db = vacanciesDb();
    $username = trim((string) $username);
    $displayName = trim((string) $displayName);
    if (!vacanciesUsernameValid($username)) return array(false, 'Το username πρέπει να έχει 3–80 λατινικούς χαρακτήρες, αριθμούς, τελεία, κάτω παύλα ή παύλα.');
    if ($displayName === '') $displayName = 'Διαχειριστής Διεύθυνσης';
    $password = vacanciesGenerateTemporaryPassword(18);
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $role = 'admin';
    $stmt = $db->prepare("INSERT INTO vacancy_users (username,password_hash,role,school_id,display_name,active,must_change_password) VALUES (?,?,?,NULL,?,1,1)");
    if (!$stmt) return array(false, 'Αποτυχία δημιουργίας λογαριασμού.');
    $stmt->bind_param('ssss', $username, $hash, $role, $displayName);
    $ok = $stmt->execute();
    $error = $stmt->error;
    $stmt->close();
    if (!$ok) return array(false, 'Δεν δημιουργήθηκε ο λογαριασμός: ' . $error);
    return array(true, 'Ο λογαριασμός διαχειριστή δημιουργήθηκε.', $username, $password);
}

function vacanciesListUsers()
{
    if (!vacanciesAccountsReady()) return array();
    return vacanciesQueryAll("SELECT u.id,u.username,u.role,u.school_id,u.display_name,u.active,u.must_change_password,u.last_login_at,u.created_at,sc.name school_name,sc.ministry_code FROM vacancy_users u LEFT JOIN vacancy_schools sc ON sc.id=u.school_id ORDER BY CASE WHEN u.role='admin' THEN 0 ELSE 1 END, sc.name, u.username");
}

function vacanciesGetUser($userId)
{
    if (!vacanciesAccountsReady()) return null;
    $userId = (int) $userId;
    if ($userId <= 0) return null;
    return vacanciesQueryOne("SELECT u.id,u.username,u.role,u.school_id,u.display_name,u.active,u.must_change_password,u.last_login_at,sc.name school_name,sc.ministry_code FROM vacancy_users u LEFT JOIN vacancy_schools sc ON sc.id=u.school_id WHERE u.id=".$userId." LIMIT 1");
}

function vacanciesActiveAdminCount()
{
    if (!vacanciesAccountsReady()) return 0;
    $row = vacanciesQueryOne("SELECT COUNT(*) AS c FROM vacancy_users WHERE role='admin' AND active=1");
    return $row ? (int) $row['c'] : 0;
}

function vacanciesUpdateAccount($userId, $username, $displayName, $role, $schoolId)
{
    if (!vacanciesAccountsReady()) return array(false, 'Δεν έχει εγκατασταθεί το schema λογαριασμών.');
    $db = vacanciesDb();
    $userId = (int) $userId;
    $username = trim((string) $username);
    $displayName = trim((string) $displayName);
    $role = (string) $role;
    $schoolId = (int) $schoolId;
    if ($userId <= 0) return array(false, 'Μη έγκυρος λογαριασμός.');

    $current = vacanciesGetUser($userId);
    if (!$current) return array(false, 'Ο λογαριασμός δεν βρέθηκε.');

    $actor = vacanciesActor();
    if (!empty($actor['user_id']) && (int) $actor['user_id'] === $userId) {
        return array(false, 'Για λόγους ασφαλείας δεν μπορείς να αλλάξεις τον ρόλο ή το username του λογαριασμού με τον οποίο είσαι συνδεδεμένος.');
    }
    if (!vacanciesUsernameValid($username)) {
        return array(false, 'Το username πρέπει να έχει 3–80 λατινικούς χαρακτήρες, αριθμούς, τελεία, κάτω παύλα ή παύλα.');
    }
    if (!in_array($role, array('admin', 'school_director'), true)) {
        return array(false, 'Μη έγκυρος ρόλος.');
    }

    if ($current['role'] === 'admin' && !empty($current['active']) && $role !== 'admin' && vacanciesActiveAdminCount() <= 1) {
        return array(false, 'Δεν μπορείς να αλλάξεις τον ρόλο του τελευταίου ενεργού διαχειριστή.');
    }

    $stmt = $db->prepare('SELECT id FROM vacancy_users WHERE username=? AND id<>? LIMIT 1');
    if (!$stmt) return array(false, 'Αποτυχία ελέγχου username.');
    $stmt->bind_param('si', $username, $userId);
    $stmt->execute();
    $existingId = 0;
    $foundUsername = false;
    if ($stmt->bind_result($existingId) && $stmt->fetch()) $foundUsername = true;
    $stmt->close();
    if ($foundUsername) return array(false, 'Το username χρησιμοποιείται ήδη από άλλον λογαριασμό.');

    if ($role === 'admin') {
        $schoolId = 0;
        if ($displayName === '') $displayName = 'Διαχειριστής Διεύθυνσης';
        $stmt = $db->prepare('UPDATE vacancy_users SET username=?, display_name=?, role=?, school_id=NULL WHERE id=?');
        if (!$stmt) return array(false, 'Αποτυχία ενημέρωσης λογαριασμού.');
        $stmt->bind_param('sssi', $username, $displayName, $role, $userId);
    } else {
        if ($schoolId <= 0) return array(false, 'Ο Διευθυντής Σχολείου πρέπει να συνδεθεί με συγκεκριμένη σχολική μονάδα.');
        $school = vacanciesQueryOne('SELECT id,name,ministry_code FROM vacancy_schools WHERE active=1 AND id='.$schoolId.' LIMIT 1');
        if (!$school) return array(false, 'Η σχολική μονάδα δεν βρέθηκε ή δεν είναι ενεργή.');
        $other = vacanciesQueryOne('SELECT id FROM vacancy_users WHERE school_id='.$schoolId.' AND id<>'.$userId.' LIMIT 1');
        if ($other) return array(false, 'Η σχολική μονάδα έχει ήδη συνδεδεμένο λογαριασμό διευθυντή.');
        if ($displayName === '') $displayName = 'Διευθυντής — ' . (string) $school['name'];
        $stmt = $db->prepare('UPDATE vacancy_users SET username=?, display_name=?, role=?, school_id=? WHERE id=?');
        if (!$stmt) return array(false, 'Αποτυχία ενημέρωσης λογαριασμού.');
        $stmt->bind_param('sssii', $username, $displayName, $role, $schoolId, $userId);
    }

    $ok = $stmt->execute();
    $error = $stmt->error;
    $stmt->close();
    return $ok ? array(true, 'Ο λογαριασμός ενημερώθηκε.') : array(false, 'Δεν ενημερώθηκε ο λογαριασμός: ' . $error);
}

function vacanciesResetAccountPassword($userId)
{
    if (!vacanciesAccountsReady()) return array(false, 'Δεν έχει εγκατασταθεί το schema λογαριασμών.');
    $db = vacanciesDb();
    $userId = (int) $userId;
    if ($userId <= 0) return array(false, 'Μη έγκυρος λογαριασμός.');
    $actor = vacanciesActor();
    if (!empty($actor['user_id']) && (int) $actor['user_id'] === $userId) {
        return array(false, 'Για τον συνδεδεμένο λογαριασμό χρησιμοποίησε την «Αλλαγή κωδικού».');
    }
    if (!vacanciesGetUser($userId)) return array(false, 'Ο λογαριασμός δεν βρέθηκε.');
    $password = vacanciesGenerateTemporaryPassword(18);
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $db->prepare("UPDATE vacancy_users SET password_hash=?, must_change_password=1 WHERE id=?");
    if (!$stmt) return array(false, 'Αποτυχία επαναφοράς κωδικού.');
    $stmt->bind_param('si', $hash, $userId);
    $ok = $stmt->execute();
    $stmt->close();
    return $ok ? array(true, 'Δημιουργήθηκε νέος προσωρινός κωδικός.', $password) : array(false, 'Δεν έγινε επαναφορά κωδικού.');
}

function vacanciesSetAccountActive($userId, $active)
{
    if (!vacanciesAccountsReady()) return array(false, 'Δεν έχει εγκατασταθεί το schema λογαριασμών.');
    $db = vacanciesDb();
    $userId = (int) $userId;
    $active = $active ? 1 : 0;
    if ($userId <= 0) return array(false, 'Μη έγκυρος λογαριασμός.');
    $current = vacanciesGetUser($userId);
    if (!$current) return array(false, 'Ο λογαριασμός δεν βρέθηκε.');
    $actor = vacanciesActor();
    if (!$active && !empty($actor['user_id']) && (int) $actor['user_id'] === $userId) {
        return array(false, 'Δεν μπορείς να απενεργοποιήσεις τον λογαριασμό με τον οποίο είσαι συνδεδεμένος.');
    }
    if (!$active && $current['role'] === 'admin' && !empty($current['active']) && vacanciesActiveAdminCount() <= 1) {
        return array(false, 'Δεν μπορείς να απενεργοποιήσεις τον τελευταίο ενεργό διαχειριστή.');
    }
    $stmt = $db->prepare("UPDATE vacancy_users SET active=? WHERE id=?");
    if (!$stmt) return array(false, 'Αποτυχία ενημέρωσης.');
    $stmt->bind_param('ii', $active, $userId);
    $ok = $stmt->execute();
    $stmt->close();
    return $ok ? array(true, $active ? 'Ο λογαριασμός ενεργοποιήθηκε.' : 'Ο λογαριασμός απενεργοποιήθηκε.') : array(false, 'Δεν ενημερώθηκε ο λογαριασμός.');
}

function vacanciesDeleteAccount($userId)
{
    if (!vacanciesAccountsReady()) return array(false, 'Δεν έχει εγκατασταθεί το schema λογαριασμών.');
    $db = vacanciesDb();
    $userId = (int) $userId;
    if ($userId <= 0) return array(false, 'Μη έγκυρος λογαριασμός.');
    $current = vacanciesGetUser($userId);
    if (!$current) return array(false, 'Ο λογαριασμός δεν βρέθηκε.');
    $actor = vacanciesActor();
    if (!empty($actor['user_id']) && (int) $actor['user_id'] === $userId) {
        return array(false, 'Δεν μπορείς να διαγράψεις τον λογαριασμό με τον οποίο είσαι συνδεδεμένος.');
    }
    if ($current['role'] === 'admin' && !empty($current['active']) && vacanciesActiveAdminCount() <= 1) {
        return array(false, 'Δεν μπορείς να διαγράψεις τον τελευταίο ενεργό διαχειριστή.');
    }
    $stmt = $db->prepare('DELETE FROM vacancy_users WHERE id=? LIMIT 1');
    if (!$stmt) return array(false, 'Αποτυχία διαγραφής λογαριασμού.');
    $stmt->bind_param('i', $userId);
    $ok = $stmt->execute();
    $affected = $stmt->affected_rows;
    $stmt->close();
    if (!$ok || $affected < 1) return array(false, 'Ο λογαριασμός δεν διαγράφηκε.');
    return array(true, 'Ο λογαριασμός διαγράφηκε.');
}

function vacanciesChangeOwnPassword($newPassword)
{
    $actor = vacanciesActor();
    if (!$actor || empty($actor['user_id']) || !vacanciesAccountsReady()) return array(false, 'Δεν υπάρχει ενεργός λογαριασμός.');
    $newPassword = (string) $newPassword;
    if (strlen($newPassword) < 12) return array(false, 'Ο νέος κωδικός πρέπει να έχει τουλάχιστον 12 χαρακτήρες.');
    $db = vacanciesDb();
    $hash = password_hash($newPassword, PASSWORD_DEFAULT);
    $userId = (int) $actor['user_id'];
    $stmt = $db->prepare("UPDATE vacancy_users SET password_hash=?, must_change_password=0 WHERE id=?");
    if (!$stmt) return array(false, 'Αποτυχία αλλαγής κωδικού.');
    $stmt->bind_param('si', $hash, $userId);
    $ok = $stmt->execute();
    $stmt->close();
    if (!$ok) return array(false, 'Δεν άλλαξε ο κωδικός.');
    vacanciesSessionStart();
    $_SESSION['vacancies_actor']['must_change_password'] = 0;
    return array(true, 'Ο κωδικός άλλαξε επιτυχώς.');
}

function vacanciesDevLoginEnabled()
{
    $config = vacanciesConfig();
    return $config && !empty($config['dev_mode']);
}

function vacanciesDevLogin($role, $key, $schoolId)
{
    $config = vacanciesConfig();
    if (!$config || empty($config['dev_mode'])) return false;
    $role = $role === 'admin' ? 'admin' : 'school_director';
    $expected = $role === 'admin'
        ? (isset($config['dev_admin_key']) ? (string) $config['dev_admin_key'] : '')
        : (isset($config['dev_school_key']) ? (string) $config['dev_school_key'] : '');
    if ($expected === '' || $expected === 'CHANGE_ME_LONG_RANDOM_VALUE' || $expected === 'CHANGE_ME_DIFFERENT_LONG_RANDOM_VALUE') return false;
    $ok = function_exists('hash_equals') ? hash_equals($expected, (string) $key) : $expected === (string) $key;
    if (!$ok) return false;
    if ($role === 'school_director' && (int) $schoolId <= 0) return false;
    vacanciesSessionStart();
    session_regenerate_id(true);
    $_SESSION['vacancies_actor'] = array(
        'role'=>$role,
        'school_id'=>$role === 'school_director' ? (int) $schoolId : null,
        'auth_mode'=>'pilot',
        'must_change_password'=>0,
        'display_name'=>$role === 'admin' ? 'Pilot διαχειριστής' : 'Pilot διευθυντής',
    );
    return true;
}

function vacanciesLogout()
{
    vacanciesSessionStart();
    unset($_SESSION['vacancies_actor']);
    unset($_SESSION['vacancies_csrf']);
    session_regenerate_id(true);
}
