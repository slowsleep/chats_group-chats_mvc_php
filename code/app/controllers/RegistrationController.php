<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;
use function App\Tools\validateCsrfToken;
use function App\Tools\refreshCsrfToken;

class RegistrationController extends Controller
{
    public function index()
    {
        $this->view->render(['content_view' => 'registration_view.php', 'title' => 'Регистрация']);
    }

    public function register()
    {
        $csrfToken = $_POST['csrf_token'] ?? '';

        if (!validateCsrfToken($csrfToken)) {
            refreshCsrfToken();
            $this->view->render(['content_view' => 'registration_view.php', 'data' => ['message' => 'Проверка токена CSRF не удалась.']]);
            exit;
        }

        $errors = [];

        if (User::checkUsernameExists($_POST['username'])) {
            $errors['username'] = 'Пользователь с таким именем уже существует!';
        } else if (User::checkEmailExists($_POST['email'])) {
            $errors['email'] = 'Пользователь с таким email уже существует!';
        } else {
            $user = User::create($_POST);
        }

        if (isset($user) && $user) {
            $this->view->render(['content_view' => 'registration_view.php', 'data' => ['message' => 'Регистрация прошла успешно!']]);
        } else {
            $this->view->render(['content_view' => 'registration_view.php', 'data' => ['message' => 'Регистрация не прошла!', 'errors' => $errors]]);
        }

        refreshCsrfToken();
    }
}
