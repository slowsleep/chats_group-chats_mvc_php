<?php

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

while (true) {
    $changed = $clients;
    socket_select($changed, $null, $null, 0, 10);

    if (in_array($socket, $changed)) {
        $socket_new = socket_accept($socket);
        $clients[] = $socket_new;
        $header = socket_read($socket_new, 1024);
        perform_handshaking($header, $socket_new, $host, $port);
        socket_getpeername($socket_new, $ip);
        echo "New connection from ".$ip.":".$port."\n";

        // Инициализируем информацию о новом клиенте
        $client_info[(int)$socket_new] = [
            'socket' => $socket_new,
            'joined' => false,
            'chat_id' => null
        ];

        $found_socket = array_search($socket, $changed);
        unset($changed[$found_socket]);
    }

    foreach ($changed as $changed_socket) {
        while (socket_recv($changed_socket, $buf, 1024, 0) >= 1) {
            $start_time = microtime(true); // Время начала приёма сообщения
            $received_text = unmask($buf);
            $tst_msg = json_decode($received_text, true);

            if (isset($tst_msg['chat_id']) && $tst_msg['chat_id']) {
                $chat_id = $tst_msg['chat_id'];

                if (!$client_info[(int)$changed_socket]['joined']) {
                    $client_info[(int)$changed_socket]['joined'] = true;
                    $client_info[(int)$changed_socket]['chat_id'] = $chat_id;

                    // Если пользователь еще не в комнате, добавляем его
                    if (!isset($chats[$chat_id])) {
                        $chats[$chat_id] = [];
                    }
                    // if (!in_array($changed_socket, $chats[$chat_id])) {
                    $chats[$chat_id][] = $changed_socket;
                    // }

                    $response = mask(json_encode(array('type' => 'system', 'message' => $ip.' connected')));
                    send_message_to_chat($chat_id, $response);
                    echo 'User joined chat '.$chat_id."\n";
                }
            }

            if (isset($tst_msg['message']) && $tst_msg['message']) {
                print_r('msg: '. $tst_msg['message'][0] .' '. $tst_msg['message'][1] . PHP_EOL);
                $message = $tst_msg['message'];
                $response = mask(json_encode(array('type' => 'usermsg', 'message' => $message)));
                send_message_to_chat($chat_id, $response);
            }

            $end_time = microtime(true);
            $execution_time = ($end_time - $start_time);
            echo "Время обработки сообщения: " . $execution_time . " секунд\n";

            break 2;
        }

        $buf = @socket_read($changed_socket, 1024, PHP_NORMAL_READ);

        if ($buf === false) {
            $chat_id = null;

            // Удаляем пользователя из массива чатов
            foreach ($chats as $chat => $sockets) {
                if (($key = array_search($changed_socket, $sockets)) !== false) {
                    $chat_id = $chat;
                    unset($chats[$chat][$key]);
                }
            }

            print_r('User disconnected from chat: ' . $chat_id  . PHP_EOL);

            // Удаляем пользователя из массива клиентов и массива информации о клиентах
            $found_socket = array_search($changed_socket, $clients);
            socket_getpeername($changed_socket, $ip);
            unset($clients[$found_socket]);
            unset($client_info[(int)$changed_socket]);


            $response = mask(json_encode(array('type' => 'system', 'message' => $ip.' disconnected')));
            send_message_to_chat($chat_id, $response);
        }

    }
}

socket_close($socket);

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
        "WebSocket-Location: ws://$host:$port/shout.php\r\n".
        "Sec-WebSocket-Accept:$secAccept\r\n\r\n";

    socket_write($client_conn, $upgrade, strlen($upgrade));
}
