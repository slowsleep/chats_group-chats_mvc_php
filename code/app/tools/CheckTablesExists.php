<?php

namespace App\Tools;

use App\Database\DB;
use PDO;
use PDOException;

function checkAllTables()
{
    try {
        $db = DB::connect();
        $tables = $db->query('SHOW TABLES')->fetchAll(PDO::FETCH_CLASS);
        $tables = array_map(function ($table) {
            return $table->Tables_in_myapp;
        }, $tables);
        $myTables = ['users', 'chats', 'messages', 'chat_members', 'chat_messages'];

        foreach ($myTables as $myTable) {
            $res = in_array($myTable, $tables) ? true : false;
        }

        return $res;
    } catch (PDOException $e) {
        echo $e->getMessage();
    }
}
