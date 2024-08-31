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
     * @return bool|string
     */
    public static function create($data)
    {
        try {
            $password = $data['password'];

            $db = DB::connect();

            $query = 'INSERT INTO users (username, email, password, email_confirm_token) VALUES (:username, :email, :password, :email_confirm_token)';
            $stmt = $db->prepare($query);
            $stmt->bindParam(':username', $data['username']);
            $stmt->bindParam(':email', $data['email']);
            $hash_password = password_hash($password . SALT, PASSWORD_DEFAULT);
            $stmt->bindParam(':password', $hash_password);
            $email_confirm_token = bin2hex(random_bytes(32));
            $stmt->bindParam(':email_confirm_token', $email_confirm_token);
            $stmt->execute();

            if ($stmt) {
                return $email_confirm_token;
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
                        'hide_email'=> $user['hide_email'],
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
    public static function update($username, $avatar, $hideEmail=0)
    {
        try {
            $db = DB::connect();
            $query = 'UPDATE users SET username = :username, avatar = :avatar, hide_email = :hide_email WHERE id = :id';
            $stmt = $db->prepare($query);
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':avatar', $avatar);
            $stmt->bindParam(':hide_email', $hideEmail);
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

    /**
     * Activates user in the database
     * @param string $email_confirm_token
     * @return bool
     */
    public static function activate($email_confirm_token)
    {
        try {
            $db = DB::connect();
            $query = 'SELECT * FROM users WHERE email_confirm_token = :email_confirm_token';
            $stmt = $db->prepare($query);
            $stmt->bindParam(':email_confirm_token', $email_confirm_token);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                $query = 'UPDATE users SET email_confirmed = :email_confirmed, email_confirm_token = :email_confirm_token_after WHERE email_confirm_token = :email_confirm_token_before';
                $stmt = $db->prepare($query);
                $email_confirmed = 1;
                $email_confirm_token_after = '';
                $stmt->bindParam(':email_confirmed', $email_confirmed);
                $stmt->bindParam(':email_confirm_token_before', $email_confirm_token);
                $stmt->bindParam(':email_confirm_token_after', $email_confirm_token_after);
                $res = $stmt->execute();
                if ($res) {
                    return true;
                }
            }
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
        return false;
    }

    /**
     * Gets user by id
     * @param mixed $username
     * @return array|bool
     */
    public static function getUser($id)
    {
        try {
            $db = DB::connect();
            $query = 'SELECT * FROM users WHERE id = :id';
            $stmt = $db->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            $user = $stmt->fetch();

            if ($user) {
                return ['id' => $user['id'], 'username' => $user['username'], 'avatar' => $user['avatar']];
            }
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
        return false;
    }

    public static function getUsers($search)
    {
        try {
            $db = DB::connect();
            $query = 'SELECT * FROM users WHERE (username LIKE :username OR (email LIKE :email AND hide_email = 0))';
            $stmt = $db->prepare($query);
            $stmt->bindValue(':username', "%$search%");
            $stmt->bindValue(':email', "%$search%");
            $stmt->execute();
            $users = $stmt->fetchAll();
            return $users;
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
        return false;
    }
}
