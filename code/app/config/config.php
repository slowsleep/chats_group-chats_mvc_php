<?php

$env = parse_ini_file(realpath(__DIR__ . '/../../.env'));

define('APP_DIR', realpath(__DIR__ . '/..'));
define('APP_NAME', $env['APP_NAME']);
define('SALT', $env['APP_SECRET_KEY']);

define('DB_HOST', $env['DB_HOST']);
define('DB_NAME', $env['DB_NAME']);
define('DB_USER', $env['DB_USER']);
define('DB_PASS', $env['DB_PASSWORD']);

define('MAIL_HOST', $env['MAIL_HOST']);
define('MAIL_USERNAME', $env['MAIL_USERNAME']);
define('MAIL_PASSWORD', $env['MAIL_PASSWORD']);
define('MAIL_DEBUG', $env['MAIL_DEBUG']);
