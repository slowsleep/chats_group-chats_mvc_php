<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;
use App\Models\Subscription;
use function App\Tools\validateCsrfToken;
use function App\Tools\refreshCsrfToken;

class ProfileController extends Controller
{
    public function index()
    {
        parent::auth();
        refreshCsrfToken();

        if (isset($_GET['user'])) {
            $user = User::getUser($_GET['user']);
            $isSubscribed = $user && Subscription::read([
                'user_id' => $_SESSION['user']['id'],
                'subscribed_to_user_id' => $user['id']
            ]);
            $this->view->render(['content_view' => 'profile_view.php', 'title' => 'Профиль', 'data' => ['user' => $user, 'isSubscribed' => $isSubscribed]]);
        } else {
            $this->view->render(['content_view' => 'profile_view.php', 'title' => 'Профиль']);
        }
    }

    public function subscribe()
    {
        parent::auth();
        $csrfToken = $_POST['csrf_token'] ?? '';

        if (!validateCsrfToken($csrfToken)) {
            refreshCsrfToken();
            $this->view->render(['content_view' => 'profile_view.php', 'data' => ['message' => 'Проверка токена CSRF не удалась.']]);
            exit;
        }

        $data = [
            'user_id' => $_SESSION['user']['id'],
            'subscribed_to_user_id' => $_POST['user']
        ];

        Subscription::create($data);
        header("Location: /profile?user=" . $_POST['user']);
    }

    public function unsubscribe()
    {
        parent::auth();

        $csrfToken = $_POST['csrf_token'] ?? '';

        if (!validateCsrfToken($csrfToken)) {
            refreshCsrfToken();
            $this->view->render(['content_view' => 'profile_view.php', 'data' => ['message' => 'Проверка токена CSRF не удалась.']]);
            exit;
        }

        $data = [
            'user_id' => $_SESSION['user']['id'],
            'subscribed_to_user_id' => $_POST['user']
        ];

        Subscription::destroy($data);
        header("Location: /profile?user=" . $_POST['user']);
    }
}
