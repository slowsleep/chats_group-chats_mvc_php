<?php

namespace App\Models;

use App\Core\Model;
use App\Database\DB;

use PDOException;

class User extends Model
{

    /**
     * Creates a new user in the database
     * @param array $data - associative array. keys - [username, email, password]
     * @return bool
     */
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

    /**
     * Checks if there is a user with the same name in the database
     * @param string $username
     * @return bool
     */
    public static function checkUsernameExists($username)
    {
        try {
            $db = DB::connect();
            $query = 'SELECT id FROM users WHERE username = :username';
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

    /**
     * Checks if there is a user with the same email in the database
     * @param string $email
     * @return bool
     */
    public static function checkEmailExists($email)
    {
        try {
            $db = DB::connect();
            $query = 'SELECT email FROM users WHERE email = :email';
            $stmt = $db->prepare($query);
            $stmt->bindParam(':email', $email);
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
     * Checks if there is a user with the same email and password in the database.
     * And returns user data if it exists.
     * @param string $email
     * @param string $password
     * @return array|bool
     */
    public static function login($email, $password)
    {
        try {
            $db = DB::connect();
            $query = 'SELECT * FROM users WHERE email = :email';
            $stmt = $db->prepare($query);
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            if ($stmt->rowCount() > 0) {
                $user = $stmt->fetch();
                if (password_verify($password . SALT, $user['password'])) {
                    $user = [
                        'id'=> $user['id'],
                        'username'=> $user['username'],
                        'email'=> $user['email'],
                        'avatar'=> $user['avatar'],
                    ];
                    return $user;
                }
            }
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
        return false;
    }

    /**
     * Updates user in the database
     * @param string $username
     * @param string $avatar
     * @return bool
     */
    public static function update($username, $avatar)
    {
        try {
            $db = DB::connect();
            $query = 'UPDATE users SET username = :username, avatar = :avatar WHERE id = :id';
            $stmt = $db->prepare($query);
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':avatar', $avatar);
            $stmt->bindParam(':id', $_SESSION['user']['id']);
            $stmt->execute();
            if ($stmt) {
                return true;
            }
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
        return false;
    }
}
