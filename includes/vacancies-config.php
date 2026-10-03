<?php
/**
 * Copy this file to vacancies-config.php and fill in the MySQL credentials.
 * Do not commit or share the real vacancies-config.php.
 */
return array(
    'db' => array(
                            'host' => '10.2.49.45',
                            'port' => 3306,
                            'name' => 'mmagiolad_vacancies',
                            'user' => 'vacancies_app',
                            'pass' => '4Fo6i3^8k',
    ),
    // Pilot mode only. Keep false before giving the URL to schools.
    'dev_mode' => true,
    'dev_admin_key' => 'kbelerchas',
    'dev_school_key' => 'schooldirector',
    'default_school_year' => '2026-2027',
);
