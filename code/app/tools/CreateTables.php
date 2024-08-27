<?php

namespace App\Tools;

use App\Database\DB;
use PDOException;

function createTableUsers()
{
    try {
        $db = DB::connect();

        $query = "CREATE TABLE IF NOT EXISTS users (
            id int(11) NOT NULL AUTO_INCREMENT,
            username varchar(25) NOT NULL,
            email varchar(255) NOT NULL,
            password varchar(255) NOT NULL,
            avatar varchar(255),
            email_confirmed boolean NOT NULL DEFAULT FALSE,
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


function createTableChats()
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

function createTableMessages()
{
    try {
        $db = DB::connect();

        $query = "CREATE TABLE IF NOT EXISTS messages (
            id int(11) NOT NULL AUTO_INCREMENT,
            content text NOT NULL,
            user_id int(11) NOT NULL REFERENCES users (id) ON DELETE CASCADE,
            chat_id int(11) NOT NULL REFERENCES chats (id) ON DELETE CASCADE,
            created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
        )";

        $db->exec($query);

        echo "Table messages created successfully.<br>";
    } catch (PDOException $e) {
        echo $e->getMessage();
    }
}

function createTableChatMembers()
{
    try {
        $db = DB::connect();

        $query = "CREATE TABLE IF NOT EXISTS chat_members (
            user_id int(11) NOT NULL REFERENCES users (id) ON DELETE CASCADE,
            chat_id int(11) NOT NULL REFERENCES chats (id) ON DELETE CASCADE,
            is_admin boolean NOT NULL
        )";

        $db->exec($query);

        echo "Table chat_members created successfully.<br>";
    } catch (PDOException $e) {
        echo $e->getMessage();
    }
}

function createTableChatMessages()
{
    try {
        $db = DB::connect();

        $query = "CREATE TABLE IF NOT EXISTS chat_messages (
            chat_id int(11) NOT NULL REFERENCES chats (id) ON DELETE CASCADE,
            message_id int(11) NOT NULL REFERENCES messages (id) ON DELETE CASCADE
        )";

        $db->exec($query);

        echo "Table chat_messages created successfully.<br>";
    } catch (PDOException $e) {
        echo $e->getMessage();
    }
}

function createAllTables()
{
    createTableUsers();
    createTableChats();
    createTableMessages();
    createTableChatMembers();
    createTableChatMessages();
}
