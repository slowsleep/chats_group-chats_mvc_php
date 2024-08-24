<?php

$env = parse_ini_file(realpath(__DIR__ . '/../../.env'));

define('DB_NAME', $env['DB_NAME']);
define('DB_USER', $env['DB_USER']);
define('DB_PASS', $env['DB_PASSWORD']);
define('APP_DIR', realpath(__DIR__ . '/..'));
