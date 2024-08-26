<?php

namespace App\Controllers;

use App\Core\Controller;

class HomeController extends Controller
{
    public function index()
    {
        $this->view->render(['content_view' => 'home_view.php']);
    }
}
