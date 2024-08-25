<?php

namespace App\Controllers;

use App\Core\Controller;

class RegistrationController extends Controller
{
    public function index()
    {
        $this->view->render('registration_view.php');
    }
}
