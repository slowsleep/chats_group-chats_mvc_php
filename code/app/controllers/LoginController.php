<?php

namespace App\Controllers;

use App\Core\Controller;

class LoginController extends Controller
{
    public function index()
    {
        $this->view->render('login_view.php');
    }
}
