<?php

require __DIR__ . '/../vendor/autoload.php';
require_once 'config/config.php';
use App\Tools\TableManager;

$tableManager = new TableManager();
$isAllTablesExists = $tableManager->checkAllTables();

echo '<pre>';
var_dump($isAllTablesExists);

if (!$isAllTablesExists['isAllTablesExists']) {
    echo 'Not all tables are exists<br>';
    
    foreach ($isAllTablesExists['resultByTable'] as $table => $isExist) {
        $tableManager->createTableIfNotExists($table, $isExist);
    }
} else {
    echo 'All tables are exist.';
}
