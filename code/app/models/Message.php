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
                    return $msgId;
                    // return true;
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
     * Get messages by id
     * @param int $id
     * @return array|bool
     */
    public static function getMessage($id)
    {
        try {
            $db = DB::connect();
            $query = 'SELECT * FROM messages WHERE id = :id';
            $stmt = $db->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            $result = $stmt->fetch();
            if ($result) {
                return $result;
            }
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
        return false;
    }

    /**
     * Check if message belongs to user
     * @param array $data - associative array. keys - [id, user_id]
     * @return bool
     */
    public static function isOwn($data)
    {
        try {
            $db = DB::connect();
            $query = 'SELECT * FROM messages WHERE id = :id AND user_id = :user_id';
            $stmt = $db->prepare($query);
            $stmt->bindParam(':id', $data['id']);
            $stmt->bindParam(':user_id', $data['user_id']);
            $stmt->execute();
            $result = $stmt->fetch();
            if ($result) {
                return true;
            }
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
        return false;
    }

    /**
     * Update message
     * @param array $data - associative array. keys - [id, content]
     * @return array|bool - associative array. keys - [updated_at]
     */
    public static function update($data)
    {
        try {
            $db = DB::connect();
            $db->beginTransaction();
            $query = 'UPDATE messages SET content = :content WHERE id = :id';
            $stmt = $db->prepare($query);
            $stmt->bindParam(':content', $data['content']);
            $stmt->bindParam(':id', $data['id']);
            $stmt->execute();
            if ($stmt) {
                $query = 'SELECT updated_at FROM messages WHERE id = :id';
                $stmt = $db->prepare($query);
                $stmt->bindParam(':id', $data['id']);
                $stmt->execute();
                $result = $stmt->fetch();
                if ($result) {
                    $db->commit();
                    return $result;
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
     * Delete message
     * @param int $id
     * @return bool
     */
    public static function destroy($id)
    {
        try {
            $db = DB::connect();
            $query = 'DELETE FROM messages WHERE id = :id';
            $stmt = $db->prepare($query);
            $stmt->bindParam(':id', $id);
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
     * Forward message to chat
     * @param array $data - associative array. keys - [chat_id, message_id]
     * @return bool
     */
    public static function forward($data)
    {
        try {
            $oldMsg = Message::getMessage($data['message_id']);
            $db = DB::connect();
            $db->beginTransaction();
            $query = 'INSERT INTO messages (content, user_id, is_forwarded, original_message_id) VALUES (:content, :user_id, 1, :original_message_id)';
            $stmt = $db->prepare($query);
            $stmt->bindParam(':content', $oldMsg['content']);
            $stmt->bindParam(':user_id', $oldMsg['user_id']);
            $stmt->bindParam(':original_message_id', $data['message_id']);
            $stmt->execute();
            if ($stmt) {
                $msgId = $db->lastInsertId();
                $query = 'INSERT INTO chat_messages (chat_id, message_id) VALUES (:chat_id, :message_id)';
                $stmt = $db->prepare($query);
                $stmt->bindParam(':chat_id', $data['chat_id']);
                $stmt->bindParam(':message_id', $msgId);
                $stmt->execute();
                if ($stmt) {
                    $db->commit();
                    return $msgId;
                } else {
                    $db->rollBack();
                }
            }
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
        return false;
    }
}
