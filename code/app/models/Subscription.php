<?php

namespace App\Models;

use App\Core\Model;
use App\Database\DB;
use PDOException;

class Subscription extends Model {

    /**
     * Creates a new user subscription
     * @param array $data - associative array. keys - [user_id, subscribed_to_user_id]
     * @return bool
     */
    public static function create($data)
    {
        try {
            $db = DB::connect();
            $query = 'INSERT INTO subscriptions (user_id, subscribed_to_user_id) VALUES (:user_id, :subscribed_to_user_id)';
            $stmt = $db->prepare($query);
            $stmt->bindParam(':user_id', $data['user_id']);
            $stmt->bindParam(':subscribed_to_user_id', $data['subscribed_to_user_id']);
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
     * Checks if there is a user subscription
     * @param array $data - associative array. keys - [user_id, subscribed_to_user_id]
     * @return bool
     */
    public static function read($data)
    {
        try {
            $db = DB::connect();
            $query = 'SELECT * FROM subscriptions WHERE user_id = :user_id AND subscribed_to_user_id = :subscribed_to_user_id';
            $stmt = $db->prepare($query);
            $stmt->bindParam(':user_id', $data['user_id']);
            $stmt->bindParam(':subscribed_to_user_id', $data['subscribed_to_user_id']);
            $stmt->execute();
            if ($stmt->rowCount() > 0) {
                return true;
            }
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
        return false;
    }

    /**
     * Destroy a row with user subscription
     * @param array $data - associative array. keys - [user_id, subscribed_to_user_id]
     * @return bool
     */
    public static function destroy($data)
    {
        try {
            $db = DB::connect();
            $query = 'DELETE FROM subscriptions WHERE user_id = :user_id AND subscribed_to_user_id = :subscribed_to_user_id';
            $stmt = $db->prepare($query);
            $stmt->bindParam(':user_id', $data['user_id']);
            $stmt->bindParam(':subscribed_to_user_id', $data['subscribed_to_user_id']);
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
     * Get all the user's subscriptions
     * @param string $user_id
     * @return mixed
     */
    public static function getSubscriptions($user_id)
    {
        try {
            $db = DB::connect();
            $query = 'SELECT id, username, email FROM users WHERE id IN (SELECT subscribed_to_user_id FROM subscriptions WHERE user_id = :user_id)';
            $stmt = $db->prepare($query);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->execute();
            if ($stmt->rowCount() > 0) {
                return $stmt->fetchAll();
            }
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
        return false;
    }

    /**
     * Search subscriptions by username or email
     * @param array $data - associative array. keys - [search, user_id]
     * @return array|bool
     */
    public static function searchSubscriptions($data)
    {
        try {
            $db = DB::connect();
            $query = 'SELECT id, username, email FROM users WHERE (username LIKE :search OR email LIKE :search) AND id IN (SELECT subscribed_to_user_id FROM subscriptions WHERE user_id = :user_id)';
            $stmt = $db->prepare($query);
            $search = '%' . $data['search'] . '%';
            $stmt->bindParam(':search', $search);
            $stmt->bindParam(':user_id', $data['user_id']);
            $stmt->execute();
            if ($stmt->rowCount() > 0) {
                return $stmt->fetchAll();
            }
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
        return false;
    }
}
