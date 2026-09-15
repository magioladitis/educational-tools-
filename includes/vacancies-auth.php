<?php
require_once __DIR__ . '/vacancies-db.php';

function vacanciesSessionStart()
{
    if (session_status() === PHP_SESSION_NONE) session_start();
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
    return ($actor && isset($actor['role']) && $actor['role'] === 'school' && isset($actor['school_id']))
        ? (int) $actor['school_id'] : 0;
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
    $role = $role === 'admin' ? 'admin' : 'school';
    $expected = $role === 'admin'
        ? (isset($config['dev_admin_key']) ? (string) $config['dev_admin_key'] : '')
        : (isset($config['dev_school_key']) ? (string) $config['dev_school_key'] : '');
    if ($expected === '' || $expected === 'CHANGE_ME_LONG_RANDOM_VALUE' || $expected === 'CHANGE_ME_DIFFERENT_LONG_RANDOM_VALUE') return false;
    $ok = function_exists('hash_equals') ? hash_equals($expected, (string) $key) : $expected === (string) $key;
    if (!$ok) return false;
    if ($role === 'school' && (int) $schoolId <= 0) return false;
    vacanciesSessionStart();
    $_SESSION['vacancies_actor'] = array('role'=>$role, 'school_id'=>$role === 'school' ? (int) $schoolId : null);
    return true;
}

function vacanciesLogout()
{
    vacanciesSessionStart();
    unset($_SESSION['vacancies_actor']);
}
