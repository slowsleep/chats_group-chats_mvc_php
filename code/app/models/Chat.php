<?php

namespace App\Models;

use App\Core\Model;
use App\Database\DB;
use PDOException;

class Chat extends Model {
    /**
     * Create the chat
     * @param array $data - associative array. keys - [user_id, contact_id, is_group]
     * @return bool|int
     */
    public static function create($data)
    {
        try {
            $db = DB::connect();
            $db->beginTransaction();
            $query = 'INSERT INTO chats (is_group) VALUES (:is_group)';
            $stmt = $db->prepare($query);
            $stmt->bindParam(':is_group', $data['is_group']);
            $stmt->execute();
            $chatId = $db->lastInsertId();
            if ($chatId) {
                $query = 'INSERT INTO chat_members (user_id, chat_id, is_admin) VALUES (:user_id, :chat_id, 0)';
                $stmt = $db->prepare($query);
                $stmt->bindParam(':user_id', $data['user_id']);
                $stmt->bindParam(':chat_id', $chatId);
                $stmt->execute();
                if ($stmt) {
                    $query = 'INSERT INTO chat_members (user_id, chat_id, is_admin) VALUES (:contact_id, :chat_id, 0)';
                    $stmt = $db->prepare($query);
                    $stmt->bindParam(':contact_id', $data['contact_id']);
                    $stmt->bindParam(':chat_id', $chatId);
                    $stmt->execute();
                    if ($stmt) {
                        $db->commit();
                        return $chatId;
                    } else {
                        $db->rollBack();
                    }
                } else {
                    $db->rollBack();
                }
            } else {
                $db->rollBack();
            }
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
        return false;
    }

    /**
     * Get user dialog with contact
     * @param array $data - associative array. keys - [user_id, contact_id]
     * @return int|bool
     */
    public static function getDialog($data)
    {
        try {
            $db = DB::connect();
            $query = 'SELECT chat_id from chat_members WHERE user_id = :user_id AND chat_id IN (SELECT chat_id FROM chat_members JOIN chats ON chat_members.chat_id = chats.id WHERE chat_members.user_id = :contact_id AND chats.is_group = 0)';
            $stmt = $db->prepare($query);
            $stmt->bindParam(':contact_id', $data['contact_id']);
            $stmt->bindParam(':user_id', $data['user_id']);
            $stmt->execute();
            if ($stmt->rowCount() > 0) {
                return $stmt->fetch()['chat_id'];
            }
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
        return false;
    }
}
