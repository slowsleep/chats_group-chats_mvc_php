<?php

namespace App\Models;

use App\Core\Model;
use App\Database\DB;
use PDOException;

class Message extends Model {
    /**
     * Creates a new message
     * @param array $data - associative array. keys - [chat_id, user_id, content]
     * @return bool
     */
    public static function create($data)
    {
        try {
            $db = DB::connect();
            $db->beginTransaction();
            $query = 'INSERT INTO messages (user_id, content) VALUES (:user_id, :content)';
            $stmt = $db->prepare($query);
            $stmt->bindParam(':user_id', $data['user_id']);
            $stmt->bindParam(':content', $data['content']);
            $stmt->execute();
            $msgId = $db->lastInsertId();

            if ($msgId) {
                $query = 'INSERT INTO chat_messages (chat_id, message_id) VALUES (:chat_id, :message_id)';
                $stmt = $db->prepare($query);
                $stmt->bindParam(':chat_id', $data['chat_id']);
                $stmt->bindParam(':message_id', $msgId);
                $stmt->execute();
                
                if ($stmt) {
                    $db->commit();
                    return true;
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
}
