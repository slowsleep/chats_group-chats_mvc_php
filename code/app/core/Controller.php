<?php

namespace App\Core;

class Controller
{

    public $model;
    public $view;

    public function __construct()
    {
        $this->view = new View();
    }

    public static function auth()
    {
        if (!isset($_SESSION['user'])) {
            header('Location: /login');
            exit;
        }
    }
}
