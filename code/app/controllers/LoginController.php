<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;

class LoginController extends Controller
{
    public function index()
    {
        $this->view->render(['content_view' => 'login_view.php']);
    }

    public function login()
    {
        $user = User::login($_POST['email'], $_POST['password']);
        if ($user) {
            $_SESSION['user'] = $user;
            header('Location: /home');
        } else {
            $this->view->render(['content_view' => 'login_view.php', 'data' => ['message' => 'Неверные данные']]);
        }
    }
}
