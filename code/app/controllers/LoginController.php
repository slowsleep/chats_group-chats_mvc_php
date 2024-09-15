<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;
use function App\Tools\validateCsrfToken;
use function App\Tools\refreshCsrfToken;

class LoginController extends Controller
{
    public function index()
    {
        if (isset($_SESSION['user'])) {
            header('Location: /home');
            exit;
        }
        refreshCsrfToken();
        $this->view->render(['content_view' => 'login_view.php']);
    }

    public function login()
    {
        $csrfToken = $_POST['csrf_token'] ?? '';

        if (!validateCsrfToken($csrfToken)) {
            refreshCsrfToken();
            $this->view->render(['content_view' => 'login_view.php', 'data' => ['message' => 'Проверка токена CSRF не удалась.']]);
            exit;
        }

        $user = User::login($_POST['email'], $_POST['password']);

        refreshCsrfToken();

        if ($user) {
            $_SESSION['user'] = $user;
            header('Location: /chat');
        } else {
            $this->view->render(['content_view' => 'login_view.php', 'data' => ['message' => 'Неверные данные']]);
        }

    }
}
