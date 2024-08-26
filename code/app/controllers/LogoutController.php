<?php

namespace App\Controllers;

use App\Core\Controller;

class LogoutController extends Controller
{
    public function index()
    {
        session_destroy();
        header('Location: /login');
    }
}
