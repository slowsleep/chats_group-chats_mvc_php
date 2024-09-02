<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;
use App\Models\Chat;

class ChatController extends Controller
{
    public function index()
    {
        parent::auth();

        $contactId = $_GET['user'];
        $contact = User::getUser($contactId);
        $contactName = null;

        if ($contact) {
            $contactName =  $contact['username'] != '' ? $contact['username'] : $contact['email'];
        }

        $chatId = null;
        $messages = null;
        $errors = null;

        if ($contact){
            $chatId = Chat::getDialog(['user_id' => $_SESSION['user']['id'], 'contact_id' => $contactId]);

            if (!$chatId) {
                $chatId = Chat::create(['user_id' => $_SESSION['user']['id'], 'contact_id' => $contactId, 'is_group' => 0]);
            }
        } else {
            $errors['user'] = 'Пользователь не найден';
        }

        $this->view->render(['content_view' => 'chat_view.php', 'title' => 'Чат', 'data' => ['chat_id' => $chatId, 'contact_name' => $contactName, 'messages' => $messages, 'errors' => $errors]]);
    }
}
