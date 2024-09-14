<?php

namespace App\Models;

use App\Core\Model;
use App\Database\DB;

use PDO;
use PDOException;

class UserChatSettings extends Model
{
    /**
     * Create record to the table user_chat_settings
     * @param array $data - associative array. keys - [user_id, chat_id, notifications_enabled]
     * @return bool
     */
    public static function create(array $data)
    {
        try {
            $db = DB::connect();
            $query = 'INSERT INTO user_chat_settings (user_id, chat_id, notifications_enabled) VALUES (:user_id, :chat_id, :notifications_enabled)';
            $stmt = $db->prepare($query);
            $stmt->bindParam(':user_id', $data['user_id']);
            $stmt->bindParam(':chat_id', $data['chat_id']);
            $stmt->bindParam(':notifications_enabled', $data['notifications_enabled']);
            $stmt->execute();
            if ($stmt) {
                return true;
            }
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
        return false;
    }

    /**
     * Update record to the table user_chat_settings
     * @param array $data - associative array. keys - [user_id, chat_id, notifications_enabled]
     * @return bool
     */
    public static function update(array $data)
    {
        try {
            $db = DB::connect();
            $query = 'UPDATE user_chat_settings SET notifications_enabled = :notifications_enabled WHERE user_id = :user_id AND chat_id = :chat_id';
            $stmt = $db->prepare($query);
            $stmt->bindParam(':user_id', $data['user_id']);
            $stmt->bindParam(':chat_id', $data['chat_id']);
            $stmt->bindParam(':notifications_enabled', $data['notifications_enabled']);
            $stmt->execute();
            if ($stmt) {
                return true;
            }
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
        return false;
    }

    /**
     * Get record from the table user_chat_settings
     * @param array $data - associative array. keys - [user_id, chat_id]
     * @return array|bool
     */
    public static function get(array $data)
    {
        try {
            $db = DB::connect();
            $query = 'SELECT * FROM user_chat_settings WHERE user_id = :user_id AND chat_id = :chat_id';
            $stmt = $db->prepare($query);
            $stmt->bindParam(':user_id', $data['user_id']);
            $stmt->bindParam(':chat_id', $data['chat_id']);
            $stmt->execute();
            if ($stmt) {
                return $stmt->fetch(PDO::FETCH_ASSOC);
            }
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
        return false;
    }
}
