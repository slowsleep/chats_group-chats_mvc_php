<?php

namespace App\Api;

use App\Core\Controller;
use App\Models\Message;
use function App\Tools\validateCsrfToken;
use function App\Tools\refreshCsrfToken;

class ChatController extends Controller
{
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