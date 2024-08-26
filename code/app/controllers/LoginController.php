<?php

namespace App\Controllers;

use App\Core\Controller;

class LoginController extends Controller
{
    public function index()
    {
        $this->view->render(['content_view' => 'login_view.php']);
    }
}
