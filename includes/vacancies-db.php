<?php
/** MySQL access layer for the school vacancies module. PHP 5.6 compatible. */

function vacanciesConfigPath()
{
    return __DIR__ . '/vacancies-config.php';
}

function vacanciesConfig()
{
    static $config = null;
    static $loaded = false;
    if ($loaded) return $config;
    $loaded = true;
    $path = vacanciesConfigPath();
    if (!is_file($path)) return null;
    $value = require $path;
    $config = is_array($value) ? $value : null;
    return $config;
}

function vacanciesDb()
{
    static $db = null;
    if ($db instanceof mysqli) return $db;
    $config = vacanciesConfig();
    if (!$config || !isset($config['db']) || !is_array($config['db'])) return null;
    $c = $config['db'];
    foreach (array('host','name','user','pass') as $required) {
        if (!isset($c[$required]) || (string) $c[$required] === '') return null;
    }
    $port = isset($c['port']) ? (int) $c['port'] : 3306;
    $db = @new mysqli((string) $c['host'], (string) $c['user'], (string) $c['pass'], (string) $c['name'], $port);
    if ($db->connect_errno) {
        $db = null;
        return null;
    }
    $charset = isset($c['charset']) ? (string) $c['charset'] : 'utf8mb4';
    @$db->set_charset($charset);
    return $db;
}

function vacanciesDbReady()
{
    return vacanciesDb() instanceof mysqli;
}

function vacanciesQueryAll($sql)
{
    $db = vacanciesDb();
    if (!$db) return array();
    $result = $db->query($sql);
    if (!$result) return array();
    $rows = array();
    while ($row = $result->fetch_assoc()) $rows[] = $row;
    $result->free();
    return $rows;
}

function vacanciesQueryOne($sql)
{
    $rows = vacanciesQueryAll($sql);
    return isset($rows[0]) ? $rows[0] : null;
}

function vacanciesDbEscape($value)
{
    $db = vacanciesDb();
    return $db ? $db->real_escape_string((string) $value) : '';
}
