<?php

namespace App\Core;

use function App\Tools\refreshCsrfToken;
use function App\Tools\validateCsrfToken;

class ApiController
{
    public static function csrf($csrf)
    {
        $csrfToken = $csrf ?? '';

        if (!validateCsrfToken($csrfToken)) {
            refreshCsrfToken();
            $response['status'] = 'failed';
            $response['message'] = 'Проверка токена CSRF не удалась.';
            $response['csrf_token'] = $_SESSION['csrf_token'];
            http_response_code(403);
            echo json_encode($response);
            exit;
        }
    }

    public static function auth()
    {
        if (!isset($_SESSION['user'])) {
            $response['status'] = 'failed';
            $response['message'] = 'User not authorized';
            http_response_code(401);
            echo json_encode($response);
            exit;
        }
    }

    /**
     * Check if post data is exists
     * @param mixed $data
     * @return mixed
     */
    public static function checkData($data)
    {
        if (!$data) {
            $response['status'] = 'failed';
            $response['message'] = "Payload not received";
            http_response_code(400);
            echo json_encode($response);
            exit;
        }
    }
}
