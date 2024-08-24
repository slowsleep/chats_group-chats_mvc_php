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
}
