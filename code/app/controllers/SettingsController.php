<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;
use function App\Tools\validateCsrfToken;
use function App\Tools\refreshCsrfToken;

class SettingsController extends Controller
{
    public function index()
    {
        parent::auth();

        $this->view->render(['content_view' => 'settings_view.php']);
    }

    public function save()
    {
        parent::auth();

        $csrfToken = $_POST['csrf_token'] ?? '';

        if (!validateCsrfToken($csrfToken)) {
            refreshCsrfToken();
            $this->view->render(['content_view' => 'settings_view.php', 'data' => ['message' => 'Проверка токена CSRF не удалась.']]);
            exit;
        }

        if ($_POST['username'] !== $_SESSION['user']['username'] && User::checkUsernameExists($_POST['username'])) {
            $this->view->render(['content_view' => 'settings_view.php', 'data' => ['message' => 'Пользователь с таким именем уже существует']]);
            exit;
        }

        if (isset($_POST['del-avatar']) && $_POST['del-avatar'] === 'on') {
            if ($_SESSION['user']['avatar'] && file_exists(APP_DIR . '/uploads/' . $_SESSION['user']['avatar'])) {
                unlink(APP_DIR . '/uploads/' . $_SESSION['user']['avatar']);
            }
            $uniqueName = '';
        } else {
            if ($_FILES['avatar']['size'] > 0) {
                if ($_SESSION['user']['avatar'] && file_exists(APP_DIR . '/uploads/' . $_SESSION['user']['avatar'])) {
                    unlink(APP_DIR . '/uploads/' . $_SESSION['user']['avatar']);
                }
                $uniqueName = uniqid() . '.' . pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION);
                $uploadDirectory = APP_DIR . '/uploads/';
                move_uploaded_file($_FILES['avatar']['tmp_name'], $uploadDirectory . $uniqueName);
            } elseif ($_FILES['avatar']['size'] == 0) {
                $uniqueName = $_SESSION['user']['avatar'];
            }
        }

        $updateUser = User::update($_POST['username'], $uniqueName);

        if (!$updateUser) {
            $this->view->render(['content_view' => 'settings_view.php', 'data' => ['message' => 'Не удалось обновить профиль']]);
            exit;
        }

        $_SESSION['user']['username'] = $_POST['username'];
        $_SESSION['user']['avatar'] = $uniqueName;

        $this->view->render(['content_view' => 'settings_view.php', 'data' => ['message' => 'Профиль обновлен']]);
    }
}
