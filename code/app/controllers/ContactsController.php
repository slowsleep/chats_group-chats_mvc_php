<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;
use App\Models\Subscription;

class ContactsController extends Controller
{
    public function index()
    {
        parent::auth();
        $subscribers = Subscription::getSubscriptions($_SESSION['user']['id']);
        $this->view->render(['content_view' => 'contacts_view.php', 'title' => 'Контакты', 'data' => ['users' => $subscribers]]);
    }

    public function search()
    {
        parent::auth();
        $users = User::getUsers($_GET['search']);
        $this->view->render(['content_view' => 'contacts_view.php', 'title' => 'Контакты', 'data' => ['users' => $users]]);
    }
}
