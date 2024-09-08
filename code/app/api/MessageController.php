<?php

namespace App\Api;

use App\Core\ApiController;
use App\Models\Message;

use function App\Tools\refreshCsrfToken;

class MessageController extends ApiController
{
    public function check()
    {
        parent::auth();
        $isOwn = Message::isOwn(['id' => $_GET['id'], 'user_id' => $_SESSION['user']['id']]);
        $response['is_own'] = $isOwn;
        echo json_encode($response);
        exit;
    }

    public function edit()
    {
        parent::auth();
        $data = json_decode(file_get_contents('php://input'), true);
        parent::checkData($data);
        parent::csrf($data['csrf_token']);

        $messageId = $data['message_id'];
        $content = $data['content'];

        $isEdited = Message::update(['id' => $messageId, 'content' => $content]);

        refreshCsrfToken();
        $response['csrf_token'] = $_SESSION['csrf_token'];

        if (!$isEdited) {
            $response['status'] = 'failed';
            $response['message'] = 'Не удалось отредактировать сообщение';
        } else {
            $response['status'] = 'success';
            $response['message'] = 'Сообщение отредактировано';
            $response['updated_at'] = $isEdited['updated_at'];
        }

        echo json_encode($response);
        exit;
    }

    public function delete()
    {
        parent::auth();
        $data = json_decode(file_get_contents('php://input'), true);
        parent::checkData($data);
        $isDelete = Message::destroy($data['message_id']);

        if (!$isDelete) {
            $response['status'] = 'failed';
            $response['message'] = 'Не удалось удалить сообщение';
        } else {
            $response['status'] = 'success';
            $response['message'] = 'Сообщение удалено';
        }

        echo json_encode($response);
        exit;
    }
}
