<?php

require __DIR__ . '/../vendor/autoload.php';
require_once 'config/config.php';

use function App\Tools\checkAllTables;
use function App\Tools\createAllTables;

if (!checkAllTables()) {
    echo 'All tables are not exist. Creating...<br>';
    createAllTables();
} else {
    echo 'All tables are exist.';
}
