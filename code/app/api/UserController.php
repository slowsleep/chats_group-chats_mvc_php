<?php

namespace App\Api;

use App\Core\ApiController;
use App\Models\Subscription;

class UserController extends ApiController
{
    public function contacts()
    {
        parent::auth();

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

    public function searchContacts()
    {
        parent::auth();

        $contacts = Subscription::searchSubscriptions(['search' => $_GET['search'], 'user_id' => $_SESSION['user']['id']]);

        if (!$contacts) {
            $response['status'] = 'failed';
            $response['message'] = 'Контакты не найдены или произошла ошибка чтения БД.';
        } else {
            $response['status'] = 'success';
            $response['contacts'] = $contacts;
        }

        echo json_encode($response);
        exit;
    }
}
