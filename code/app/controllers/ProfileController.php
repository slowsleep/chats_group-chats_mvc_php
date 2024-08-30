<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;

class ProfileController extends Controller
{
    public function index()
    {
        parent::auth();
        
        if (isset($_GET['user'])) {
            $user = User::getUser($_GET['user']);
            $this->view->render(['content_view' => 'profile_view.php', 'title' => 'Профиль', 'data' => ['user' => $user]]);
        } else {
            $this->view->render(['content_view' => 'profile_view.php', 'title' => 'Профиль']);
        }
    }
}
