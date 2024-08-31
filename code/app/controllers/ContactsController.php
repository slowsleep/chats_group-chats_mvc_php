<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;

class ContactsController extends Controller
{
    public function index()
    {
        parent::auth();

        $this->view->render(['content_view' => 'contacts_view.php', 'title' => 'Контакты']);
    }

    public function search()
    {
        parent::auth();
        
        $users = User::getUsers($_GET['search']);
        $this->view->render(['content_view' => 'contacts_view.php', 'title' => 'Контакты', 'data' => ['users' => $users]]);
    }
}
