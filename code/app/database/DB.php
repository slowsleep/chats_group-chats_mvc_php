<?php

include __DIR__ . '/../config/config.php';

class DB {

    private static $connection;

    public static function connect() {
        if (!isset(DB::$connection)) {
            echo "new connection" . PHP_EOL;
            try {
                DB::$connection = new PDO('mysql:host=mysql;dbname=' . DB_NAME, DB_USER, DB_PASS);
                echo 'Connected' . PHP_EOL;
            } catch (PDOException $e) {
                echo 'Connection failed: ' . $e->getMessage() . PHP_EOL;
            }
        }
        echo "returning connection" . PHP_EOL;
        return DB::$connection;
    }
}
