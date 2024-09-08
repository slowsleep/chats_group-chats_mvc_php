<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;
use App\Models\Chat;

class ChatController extends Controller
{
    public function index()
    {
        parent::auth();

        [$chats, $groups] = $this->getChatsAndGroups();

        if (!isset($_GET['user'])) {
            $this->view->render([
                'content_view' => '/chat/chat_view.php',
                'title' => 'Чат',
                'data' => ['chats' => $chats, 'groups' => $groups]
            ]);
            exit;
        }

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
                $chatId = Chat::create(['user_id' => $_SESSION['user']['id'], 'is_admin' => 0, 'is_group' => 0]);
                if (!$chatId) {
                    $errors['user'] = 'Не удалось создать чат';
                } else {
                    $addUser = Chat::addMember(['chat_id' => $chatId, 'user_id' => $contactId]);
                    if (!$addUser) {
                        Chat::destroy($chatId);
                        $errors['user'] = 'Не удалось добавить в чат';
                    }
                }
            }

            $messages = Chat::getMessages(['chat_id' => $chatId]);
        } else {
            $errors['user'] = 'Пользователь не найден';
            header('HTTP/1.0 404 Not Found');
        }

        $this->view->render([
            'content_view' => '/chat/chat_view.php',
            'title' => 'Чат',
            'data' => [
                'chat_id' => $chatId,
                'chats' => $chats,
                'groups' => $groups,
                'contact' => [
                    'id' => $contactId,
                    'name' => $contactName
                ],
                'messages' => $messages,
                'errors' => $errors
            ]
        ]);
    }

    public function group()
    {
        parent::auth();

        $errors = null;

        [$chats, $groups] = $this->getChatsAndGroups();

        if (!isset($_GET['id'])) {
            $this->view->render([
                'content_view' => '/chat/chat_view.php',
                'title' => 'Чат',
                'data' => ['chats' => $chats, 'groups' => $groups]
            ]);
            exit;
        }

        $isMember = Chat::isMember(['user_id' => $_SESSION['user']['id'], 'chat_id' => $_GET['id']]);

        if (!$isMember) {
            $errors['user'] = 'Групповой чат не найден';
            header('HTTP/1.0 404 Not Found');
            $this->view->render([
                'content_view' => '/chat/chat_view.php',
                'title' => '404',
                'data' => [
                    'errors' => $errors,
                    'chats' => $chats,
                    'groups' => $groups
                ]
                ]);
            exit;
        }

        $groupId = $_GET['id'];
        $messages = Chat::getMessages(['chat_id' => $groupId]);

        $this->view->render([
            'content_view' => '/chat/chat_view.php',
            'title' => 'Чат',
            'data' => [
                'chat_id' => $groupId,
                'chats' => $chats,
                'groups' => $groups,
                'messages' => $messages,
                'errors' => $errors
            ]
        ]);
    }

    private function getChatsAndGroups()
    {
        $usersChats = User::getChats(['user_id' => $_SESSION['user']['id'], 'is_group' => false]);
        $chats = [];
        $chatsWithNames = [];

        if ($usersChats) {
            foreach ($usersChats as $chatId) {
                $chatMembers = Chat::getMembers($chatId['chat_id']);
                $chats[] = ['chat_id' => $chatId['chat_id'], 'members' => $chatMembers];
            }

            foreach ($chats as $chat) {
                foreach ($chat['members'] as $member) {
                    $memberInfo = User::getUser($member['user_id']);
                    if ($memberInfo['id'] != $_SESSION['user']['id']) {
                        $chatsWithNames[] = [
                            'id' => $chat['chat_id'],
                            'user_id' => $memberInfo['id'],
                            'title' => $memberInfo['username'] != '' ? $memberInfo['username'] : $memberInfo['email'],
                            'avatar' => $memberInfo['avatar'],
                        ];
                    }
                }
            }
        }

        $groups = User::getChats(['user_id' => $_SESSION['user']['id'], 'is_group' => true]);

        return [$chatsWithNames, $groups];
    }
}
