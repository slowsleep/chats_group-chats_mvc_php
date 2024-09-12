<?php

namespace App\Tools;

use App\Database\DB;
use PDO;
use PDOException;

class TableManager
{
    public function createTableUsers()
    {
        try {
            $db = DB::connect();

            $query = "CREATE TABLE IF NOT EXISTS users (
                id int(11) NOT NULL AUTO_INCREMENT,
                username varchar(25),
                email varchar(255) NOT NULL UNIQUE,
                password varchar(255) NOT NULL,
                avatar varchar(255),
                email_confirmed boolean NOT NULL DEFAULT FALSE,
                email_confirm_token varchar(255),
                hide_email boolean NOT NULL DEFAULT FALSE,
                created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (id)
            )";

            $db->exec($query);

            echo "Table users created successfully.<br>";
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }


    public function createTableChats()
    {
        try {
            $db = DB::connect();

            $query = "CREATE TABLE IF NOT EXISTS chats (
                id int(11) NOT NULL AUTO_INCREMENT,
                is_group boolean NOT NULL,
                created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (id)
            )";

            $db->exec($query);

            echo "Table chats created successfully.<br>";
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    public function createTableMessages()
    {
        try {
            $db = DB::connect();
            $query = "CREATE TABLE IF NOT EXISTS messages (
                id int(11) NOT NULL AUTO_INCREMENT,
                content text NOT NULL,
                user_id int(11) NOT NULL REFERENCES users (id) ON DELETE CASCADE,
                is_forwarded boolean DEFAULT FALSE,
                original_message_id int NULL,
                created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (id),
                FOREIGN KEY (original_message_id) REFERENCES messages(id)
            )";
            
            $db->exec($query);

            echo "Table messages created successfully.<br>";
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    public function createTableChatMembers()
    {
        try {
            $db = DB::connect();

            $query = "CREATE TABLE IF NOT EXISTS chat_members (
                chat_id int(11) NOT NULL REFERENCES chats (id) ON DELETE CASCADE,
                user_id int(11) NOT NULL REFERENCES users (id) ON DELETE CASCADE,
                is_admin boolean NOT NULL,
                UNIQUE (chat_id, user_id)
            )";

            $db->exec($query);

            echo "Table chat_members created successfully.<br>";
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    public function createTableChatMessages()
    {
        try {
            $db = DB::connect();

            $query = "CREATE TABLE IF NOT EXISTS chat_messages (
                chat_id int(11) NOT NULL REFERENCES chats (id) ON DELETE CASCADE,
                message_id int(11) NOT NULL REFERENCES messages (id) ON DELETE CASCADE,
                UNIQUE (chat_id, message_id)
            )";

            $db->exec($query);

            echo "Table chat_messages created successfully.<br>";
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    public function createTableSubscriptions()
    {
        try {
            $db = DB::connect();
            $query = "CREATE TABLE IF NOT EXISTS subscriptions (
                user_id int(11) NOT NULL REFERENCES users (id) ON DELETE CASCADE,
                subscribed_to_user_id int(11) NOT NULL REFERENCES users (id) ON DELETE CASCADE,
                UNIQUE (user_id, subscribed_to_user_id)
            )";

            $db->exec($query);

            echo "Table subscriptions created successfully.<br>";
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    public function createAllTables()
    {
        $this->createTableUsers();
        $this->createTableChats();
        $this->createTableMessages();
        $this->createTableChatMembers();
        $this->createTableChatMessages();
        $this->createTableSubscriptions();
    }

    public function createTableIfNotExists($table, $isExists)
    {
        $tablesFunc = [
            'users' => 'createTableUsers',
            'chats' => 'createTableChats',
            'messages' => 'createTableMessages',
            'chat_members' => 'createTableChatMembers',
            'chat_messages' => 'createTableChatMessages',
            'subscriptions' => 'createTableSubscriptions'
        ];

        if (!$isExists) {
            $method = $tablesFunc[$table];
            $this->$method();
        }
    }

    public function checkAllTables()
    {
        try {
            $db = DB::connect();
            $tables = $db->query('SHOW TABLES')->fetchAll(PDO::FETCH_CLASS);
            $tables = array_map(function ($table) {
                return $table->Tables_in_myapp;
            }, $tables);
            $myTables = ['users', 'chats', 'messages', 'chat_members', 'chat_messages', 'subscriptions'];
            $isAllTablesExists = true;
            $isExistTable = [];

            foreach ($myTables as $myTable) {
                $isAllTablesExists = $isAllTablesExists && in_array($myTable, $tables) ? true : false;
                $isExistTable[$myTable] = in_array($myTable, $tables) ? true : false;
            }

            return ['isAllTablesExists' => $isAllTablesExists, 'resultByTable' => $isExistTable];
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

}
