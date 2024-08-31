<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;
use App\Tools\Mail;
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

        if ($_POST['username'] !== '' && User::checkUsernameExists($_POST['username'])) {
            $errors['username'] = 'Пользователь с таким именем уже существует!';
        } else if (User::checkEmailExists($_POST['email'])) {
            $errors['email'] = 'Пользователь с таким email уже существует!';
        } else {
            $email_confirm_token = User::create($_POST);
            $mail = new Mail();
            $mail->send($_POST['email'], $email_confirm_token);
        }

        if (isset($email_confirm_token) && $email_confirm_token) {
            $this->view->render(['content_view' => 'registration_view.php', 'data' => ['message' => 'Регистрация прошла успешно!']]);
        } else {
            $this->view->render(['content_view' => 'registration_view.php', 'data' => ['message' => 'Регистрация не прошла!', 'errors' => $errors]]);
        }

        refreshCsrfToken();
    }

    public function activate()
    {
        $token = $_GET['token'] ?? '';
        $user = User::activate($token);
        if ($user) {
            $this->view->render(['content_view' => 'registration_view.php', 'data' => ['message' => 'Аккаунт успешно активирован!']]);
        } else {
            $this->view->render(['content_view' => 'registration_view.php', 'data' => ['message' => 'Активация аккаунта не удалась!']]);
        }
    }
}
