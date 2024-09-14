<?php

require_once __DIR__ . '/vendor/autoload.php';
require_once 'app/config/config.php';

use App\Database\DB;

$db = DB::connect();

$host = 'localhost';
$port = '3000';
$null = NULL;

echo "WebSocket-server started.\n";

$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
socket_set_option($socket, SOL_SOCKET, SO_REUSEADDR, 1);
socket_bind($socket, 0, $port);
socket_listen($socket);

$clients = array($socket);
$chats = []; // Массив с чатами и пользователями внутри
$client_info = []; // Массив для хранения информации о подключённых клиентах
$notification_sockets = [];

while (true) {
    $changed = $clients;
    socket_select($changed, $null, $null, 0, 10);

    if (in_array($socket, $changed)) {
        $socket_new = socket_accept($socket);
        $clients[] = $socket_new;
        $header = socket_read($socket_new, 1024);
        perform_handshaking($header, $socket_new, $host, $port);
        socket_getpeername($socket_new, $ip);
        echo "New connection from " . $ip . ":" . $port . "\n";

        // Инициализируем информацию о новом клиенте
        $client_info[spl_object_hash($socket_new)] = [
            'socket' => $socket_new,
            'joined' => false,
            'chat_id' => 0,
            'user_id' => 0,
        ];

        $found_socket = array_search($socket, $changed);
        unset($changed[$found_socket]);
    }

    foreach ($changed as $changed_socket) {
        while (socket_recv($changed_socket, $buf, 1024, 0) >= 1) {
            $start_time = microtime(true); // Время начала приёма сообщения
            $received_text = unmask($buf);
            $tst_msg = json_decode($received_text, true);

            if (isset($tst_msg['type']) && $tst_msg['type'] == 'start') {
                print_r("start" . PHP_EOL);
                $chat_id = $tst_msg['chat_id'];

                if (!$client_info[spl_object_hash($changed_socket)]['joined']) {
                    $client_info[spl_object_hash($changed_socket)]['joined'] = true;
                    $client_info[spl_object_hash($changed_socket)]['chat_id'] = $chat_id;

                    // Если пользователь еще не в комнате, добавляем его
                    if (!isset($chats[$chat_id])) {
                        $chats[$chat_id] = [];
                    }
                    if (!in_array($changed_socket, $chats[$chat_id])) {
                        $chats[$chat_id][] = $changed_socket;
                    }

                    $response = mask(json_encode(array('type' => 'system', 'message' => $ip . ' connected')));
                    send_message_to_chat($chat_id, $response);
                    echo 'User joined chat ' . $chat_id . "\n";
                }
            }

            if (isset($tst_msg['type']) && $tst_msg['type'] == 'start-notification') {
                $user_id = $tst_msg['user_id'];
                $notification_sockets[$user_id] = $changed_socket;
                $client_info[spl_object_hash($changed_socket)]['user_id'] = $user_id;
                echo 'start-notification user:' . $user_id . PHP_EOL;
            }

            if (isset($tst_msg['type']) && $tst_msg['type'] == 'ping') {
                $response = mask(json_encode(array('type' => 'pong')));
                socket_write($changed_socket, $response, strlen($response));
            }

            if (isset($tst_msg['type']) && $tst_msg['type'] == 'send-message') {
                print_r('send-message to ' . $tst_msg['chat_id'] . PHP_EOL);
                print_r('msg: ' . $tst_msg['message'][0] . ' ' . $tst_msg['message'][1] . PHP_EOL);
                $message = $tst_msg['message'];
                $response = mask(json_encode(array('type' => 'send-message', 'message' => $message)));
                send_message_to_chat($tst_msg['chat_id'], $response);
                $users_to_notify = get_users_to_notify($tst_msg['chat_id']);

                foreach ($users_to_notify as $user_id) {
                    if ($tst_msg['message']['user_id'] != $user_id) {
                        send_notification_to_user($user_id);
                    }
                }
            }

            if (isset($tst_msg['type']) && $tst_msg['type'] == 'edit-message') {
                print_r('edit-message' . PHP_EOL);
                $response = mask(json_encode(array('type' => 'edit-message', 'message' => $tst_msg['message'])));
                send_message_to_chat($tst_msg['chat_id'], $response);
            }

            if (isset($tst_msg['type']) && $tst_msg['type'] == 'delete-message') {
                print_r('delete-message' . PHP_EOL);
                $response = mask(json_encode(array('type' => 'delete-message', 'message' => $tst_msg['message'])));
                send_message_to_chat($tst_msg['chat_id'], $response);
            }

            if (isset($tst_msg['type']) && $tst_msg['type'] == 'close') {
                echo "Received close message from client.\n";
                handleDisconnect($changed_socket, 'Chat closed!');
            }

            $end_time = microtime(true);
            $execution_time = ($end_time - $start_time);
            echo "Время обработки сообщения: " . $execution_time . " секунд\n";
            echo "-----------------------------\n\n";
            break 2;
        }

        $buf = @socket_read($changed_socket, 1024, PHP_NORMAL_READ);

        if ($buf === false) {
            echo "Buffer is false\n";
            handleDisconnect($changed_socket, 'User disconnected');
        }
    }
}

socket_close($socket);


function handleDisconnect($socket, $reason) {
    global $clients, $client_info, $chats, $notification_sockets;

    $chat_id = deleteUser($socket);
    if ($chat_id) {
        echo "User disconnected from chat $chat_id.\n";
        send_message_to_chat($chat_id, mask(json_encode([
            'type' => 'system',
            'message' => socket_getpeername($socket, $ip) ? "$ip disconnected" : 'User disconnected'
        ])));
    }

    $clientsFound = array_search($socket, $clients);
    if ($clientsFound !== false) {
        unset($clients[$clientsFound]);
        unset($client_info[spl_object_hash($socket)]);
    }

    foreach ($notification_sockets as $user_id => $notif_socket) {
        if ($socket == $notif_socket) {
            unset($notification_sockets[$user_id]);
        }
    }

    close_websocket_connection($socket, 1001, $reason);
}

function deleteUser($socket) {
    global $clients, $chats, $client_info;

    try {
        $chat_id = $client_info[spl_object_hash($socket)]['chat_id'] ?? null;
        if ($chat_id && isset($chats[$chat_id])) {
            $found_chat = array_search($socket, $chats[$chat_id]);
            if ($found_chat !== false) unset($chats[$chat_id][$found_chat]);
        }
        unset($clients[array_search($socket, $clients)], $client_info[spl_object_hash($socket)]);
        return $chat_id;
    } catch (Exception $e) {
        echo $e->getMessage() . PHP_EOL;
    }
    return false;
}

// Функция отправки сообщений всем участникам чата
function send_message_to_chat($chat_id, $msg)
{
    global $chats;
    if (isset($chats[$chat_id])) {
        foreach ($chats[$chat_id] as $changed_socket) {
            @socket_write($changed_socket, $msg, strlen($msg));
        }
    }
    return true;
}

function get_users_to_notify($chat_id)
{
    global $db;
    $query = $db->prepare("SELECT user_id FROM user_chat_settings WHERE chat_id = ? AND notifications_enabled = 1");
    $query->execute([$chat_id]);
    $users_with_notifications = $query->fetchAll(PDO::FETCH_COLUMN);
    return $users_with_notifications;
}

function send_notification_to_user($user_id)
{
    global $notification_sockets;
    if (isset($notification_sockets[$user_id])) {
        echo 'send_notification_to_user: ' . $user_id . PHP_EOL;
        $response = mask(json_encode(array('type' => 'notification')));
        @socket_write($notification_sockets[$user_id], $response, strlen($response));
    }
}

function mask($text)
{
    $b1 = 0x80 | (0x1 & 0x0f);
    $length = strlen($text);

    if ($length <= 125) {
        $header = pack('CC', $b1, $length);
    } elseif ($length > 125 && $length < 65536) {
        $header = pack('CCn', $b1, 126, $length);
    } elseif ($length >= 65536) {
        $header = pack('CCNN', $b1, 127, $length);
    }

    return $header . $text;
}

function unmask($text)
{
    $length = ord($text[1]) & 127;

    if ($length == 126) {
        $mask = substr($text, 4, 4);
        $data = substr($text, 8);
    } elseif ($length == 127) {
        $mask = substr($text, 10, 4);
        $data = substr($text, 14);
    } else {
        $mask = substr($text, 2, 4);
        $data = substr($text, 6);
    }

    $text = "";

    for ($i = 0; $i < strlen($data); ++$i) {
        $text .= $data[$i] ^ $mask[$i % 4];
    }

    return $text;
}

function perform_handshaking($receved_header, $client_conn, $host, $port)
{
    $header = array();
    $lines = preg_split("/\r\n/", $receved_header);

    foreach ($lines as $line) {
        $line = chop($line);
        if (preg_match('/\A(\S+): (.*)\z/', $line, $matches)) {
            $header[$matches[1]] = $matches[2];
        }
    }

    $secKey = $header['Sec-WebSocket-Key'];
    $secAccept = base64_encode(pack('H*', sha1($secKey . '258EAFA5-E914-47DA-95CA-C5AB0DC85B11')));

    // hand shaking header
    $upgrade = "HTTP/1.1 101 Web Socket Protocol HandShake\r\n" .
        "Upgrade: websocket\r\n" .
        "Connection: Upgrade\r\n" .
        "WebSocket-Origin: $host\r\n" .
        "WebSocket-Location: ws://$host:$port/server.php\r\n" .
        "Sec-WebSocket-Accept:$secAccept\r\n\r\n";

    socket_write($client_conn, $upgrade, strlen($upgrade));
}

function close_websocket_connection($client_conn, $code = 1000, $reason = '')
{
    $close_payload = pack('n', $code) . $reason;

    // Формируем фрейм закрытия WebSocket (финальный фрейм, опкод 0x8 для закрытия)
    $close_frame = chr(0x88) . chr(strlen($close_payload)) . $close_payload;

    // Отправляем фрейм закрытия клиенту
    socket_write($client_conn, $close_frame, strlen($close_frame));

    // Закрываем сам TCP-сокет
    socket_close($client_conn);
}
