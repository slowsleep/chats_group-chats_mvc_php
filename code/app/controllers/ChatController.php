<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;
use App\Models\Chat;
use App\Models\Message;
use function App\Tools\validateCsrfToken;
use function App\Tools\refreshCsrfToken;

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

            $messages = Chat::getMessages(['chat_id' => $chatId]);
        } else {
            $errors['user'] = 'Пользователь не найден';
        }

        $this->view->render([
            'content_view' => 'chat_view.php',
            'title' => 'Чат',
            'data' => [
                'chat_id' => $chatId,
                'contact' => [
                    'contact_id' => $contactId,
                    'contact_name' => $contactName
                ],
                'messages' => $messages,
                'errors' => $errors
                ]
        ]);
    }

    public function send()
    {
        parent::auth();

        $csrfToken = $_POST['csrf_token'] ?? '';

        if (!validateCsrfToken($csrfToken)) {
            refreshCsrfToken();
            $this->view->render(['content_view' => 'chat_view.php', 'data' => ['errors' => ['message' => 'Проверка токена CSRF не удалась.']]]);
            exit;
        }

        $chatId = $_POST['chat_id'];
        $content = $_POST['content'];
        $userId = $_SESSION['user']['id'];

        $newMsg = Message::create(['chat_id' => $chatId, 'user_id' => $userId, 'content' => $content]);

        refreshCsrfToken();

        if ($newMsg) {
            header('Location: /chat?user=' . $_POST['contact_id']);
        } else {
            $this->view->render(['content_view' => 'chat_view.php', 'data' => ['message' => 'Не удалось отправить сообщение']]);
        }
    }
}
