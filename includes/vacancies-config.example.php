<?php
/**
 * Copy this file to vacancies-config.php and fill in the MySQL credentials.
 * Do not commit or share the real vacancies-config.php.
 */
return array(
    'db' => array(
        'host' => 'localhost',
        'port' => 3306,
        'name' => 'CHANGE_ME',
        'user' => 'CHANGE_ME',
        'pass' => 'CHANGE_ME',
        'charset' => 'utf8mb4',
    ),
    // Pilot/bootstrap mode only. Keep false before giving real account credentials to schools.
    'dev_mode' => false,
    'dev_admin_key' => 'CHANGE_ME_LONG_RANDOM_VALUE',
    'dev_school_key' => 'CHANGE_ME_DIFFERENT_LONG_RANDOM_VALUE',
    'default_school_year' => '2026-2027',
);
