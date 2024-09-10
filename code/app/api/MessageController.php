<?php

namespace App\Api;

use App\Core\ApiController;
use App\Models\Message;
use App\Models\Chat;

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
        parent::csrf($data['csrf_token'] ?? '');
        $isDelete = Message::destroy($data['message_id']);

        refreshCsrfToken();
        $response['csrf_token'] = $_SESSION['csrf_token'];

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


    public function forward()
    {
        parent::auth();
        $data = json_decode(file_get_contents('php://input'), true);
        parent::checkData($data);
        parent::csrf($data['csrf_token']);

        $errors = [];
        $messageId = $data['message']['id'];
        $messageFromChat = $data['message']['chat_id'];
        $contactId = $data['contact_id'];
        $chatId = Chat::getDialog(['contact_id' => $contactId, 'user_id' => $_SESSION['user']['id']]);

        if (!$chatId) {
            $chatId = Chat::create(['user_id' => $_SESSION['user']['id'], 'is_admin' => 0, 'is_group' => 0]);
            if ($chatId) {
                $addMember = Chat::addMember(['chat_id' => $chatId, 'user_id' => $contactId]);
                if (!$addMember) {
                    $errors['chat'] = 'Не удалось добавить контакт в новый чат';
                }
            } else {
                $errors['chat'] = 'Не удалось создать чат';
            }
        }
        
        if ($messageFromChat == $chatId) {
            $newMessage = Message::create(['chat_id' => $chatId, 'user_id' => $_SESSION['user']['id'], 'content' => $data['message']['content']]);
            if (!$newMessage) {
                $errors['message'] = 'Не удалось создать сообщение';
            }
        } else {
            $isForward = Message::forward(['message_id' => $messageId, 'chat_id' => $chatId]);
            if (!$isForward) {
                $errors['message'] = 'Не удалось переслать сообщение';
            }
        }

        refreshCsrfToken();
        $response['csrf_token'] = $_SESSION['csrf_token'];

        if ($errors) {
            $response['status'] = 'failed';
            $response['message'] = 'Не удалось переслать сообщение';
            $response['errors'] = $errors;
        } else {
            $response['status'] = 'success';
            $response['message'] = 'Сообщение переслано';
        }

        echo json_encode($response);
        exit;
    }
}
