<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;

class RegistrationController extends Controller
{
    public function index()
    {
        $this->view->render(['content_view' => 'registration_view.php', 'title' => 'Регистрация']);
    }

    public function register()
    {
        $errors = [];
        if (User::checkUsernameExists($_POST['username'])) {
            $errors['username'] = 'Пользователь с таким именем уже существует!';
        } else {
            $user = User::create($_POST);
        }

        if (isset($user) && $user) {
            $this->view->render(['content_view' => 'registration_view.php', 'data' => ['message' => 'Регистрация прошла успешно!']]);
        } else {
            $this->view->render(['content_view' => 'registration_view.php', 'data' => ['message' => 'Регистрация не прошла!', 'errors' => $errors]]);
        }
    }
}
