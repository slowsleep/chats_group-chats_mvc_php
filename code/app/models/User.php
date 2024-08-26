<?php

namespace App\Models;

use App\Core\Model;
use App\Database\DB;

use PDOException;

class User extends Model
{

    public static function create($data)
    {
        try {
            $password = $data['password'];

            $db = DB::connect();

            $query = 'INSERT INTO users (username, email, password) VALUES (:username, :email, :password)';
            $stmt = $db->prepare($query);
            $stmt->bindParam(':username', $data['username']);
            $stmt->bindParam(':email', $data['email']);
            $hash_password = password_hash($password . SALT, PASSWORD_DEFAULT);
            $stmt->bindParam(':password', $hash_password);
            $stmt->execute();

            if ($stmt) {
                return true;
            }
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
        return false;
    }

    public static function checkUsernameExists($username)
    {
        try {
            $db = DB::connect();
            $query = 'SELECT username FROM users WHERE username = :username';
            $stmt = $db->prepare($query);
            $stmt->bindParam(':username', $username);
            $stmt->execute();
            if ($stmt->rowCount() > 0) {
                return true;
            }
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
        return false;
    }
}
