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
                    'id' => $contactId,
                    'name' => $contactName
                ],
                'messages' => $messages,
                'errors' => $errors
            ]
        ]);
    }

    public function send()
    {
        $data = json_decode(file_get_contents('php://input'), true);
        $csrfToken = $data['csrf_token'] ?? '';

        if (!validateCsrfToken($csrfToken)) {
            refreshCsrfToken();
            $response['status'] = 'failed';
            $response['message'] = 'Проверка токена CSRF не удалась.';
            $response['csrf_token'] = $_SESSION['csrf_token'];
            http_response_code(403);
            echo json_encode($response);
            exit;
        }

        $chatId = $data['chat_id'];
        $content = $data['content'];
        $userId = $data['user_id'];

        $newMsgId = Message::create(['chat_id' => $chatId, 'user_id' => $userId, 'content' => $content]);
        $newMsg = Message::getMessage($newMsgId);

        refreshCsrfToken();
        $response['csrf_token'] = $_SESSION['csrf_token'];

        if (!$newMsg) {
            http_response_code(500);
            $response['status'] = 'failed';
            $response['message'] = 'Не удалось отправить сообщение';
        } else {
            http_response_code(200);
            $response['status'] = 'success';
            $response['message'] = $newMsg;
        }

        echo json_encode($response);
        exit;
    }
}
