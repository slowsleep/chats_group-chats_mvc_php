<?php

namespace App\Api;

use App\Core\Controller;
use App\Models\Subscription;

class UserController extends Controller
{
    public function contacts()
    {

        if (!isset($_SESSION['user'])) {
            $response['status'] = 'failed';
            $response['message'] = 'Пользователь не авторизован';
            http_response_code(403);
            echo json_encode($response);
            exit;
        }

        $usersContacts = Subscription::getSubscriptions($_SESSION['user']['id']);

        if (!$usersContacts) {
            $response['status'] = 'failed';
            $response['message'] = 'Пользователь не подписан ни на одного контакта или произошла ошибка чтения БД.';
        } else {
            $response['status'] = 'success';
            $response['contacts'] = $usersContacts;
        }

        echo json_encode($response);
        exit;
    }
}
