<?php

namespace App\Database;

use PDO;
use PDOException;

class DB
{

    private static $connection;

    public static function connect()
    {
        if (!isset(DB::$connection)) {
            try {
                DB::$connection = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASS);
            } catch (PDOException $e) {
                // TODO: print to error log
                echo 'Connection failed: ' . $e->getMessage() . PHP_EOL;
            }
        }
        return DB::$connection;
    }
}
