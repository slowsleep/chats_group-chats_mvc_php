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
        $user = User::create($_POST);

        if ($user) {
            $this->view->render(['content_view' => 'registration_view.php', 'data' => 'Регистрация прошла успешно!']);
        } else {
            $this->view->render(['content_view' => 'registration_view.php', 'data' => 'Регистрация не прошла!']);
        }
    }
}
