// let message from chat view
// wsUrl from script layout_view.php
let chatForm = document.querySelector('#chat-form');
let websocket;

if (chatForm) {
    let chat_id = chatForm.elements['chat_id'].value;
    let user_id = chatForm.elements['user_id'].value;

    chatForm.addEventListener('submit', sendMessage);

    if (window.location.search) {
        let getUserIdOrChatId = window.location.search.split('?')[1].split('=')[1];

        if (getUserIdOrChatId) {
            websocket = new WebSocket(wsUrl);

            websocket.onopen = function (event) {
                let output = document.createElement('p');
                output.textContent = 'Подключение установлено';
                messages.appendChild(output);
                chatForm.elements['send'].disabled = false;
                websocket.send(JSON.stringify({
                    type: 'start',
                    chat_id
                }));

                // Отправляем пинги каждые 30 секунд
                const pingInterval = setInterval(() => {
                    if (websocket.readyState === WebSocket.OPEN) {
                        websocket.send(JSON.stringify({
                            type: 'ping'
                        }));
                    } else {
                        clearInterval(pingInterval); // Останавливаем пинги, если соединение закрыто
                    }
                }, 30000);
            }

            websocket.onmessage = function (event) {
                const response = JSON.parse(event.data);
                const res_type = response.type;
                const message = response.message;

                switch (res_type) {
                    case 'send-message':
                        let msgDiv = document.createElement('div');
                        msgDiv.setAttribute('data-msgid', message.id);
                        msgDiv.className = 'message ' + (message.user_id == user_id ? 'message--own' : '');

                        if (message.is_forwarded) {
                            let msgForwarded = document.createElement('p');
                            msgForwarded.className = 'message__forwarded';
                            msgForwarded.innerText = 'Пересланное сообщение';
                            msgDiv.appendChild(msgForwarded);
                        }

                        let msgContent = document.createElement('p');
                        msgContent.className = 'message__content';
                        msgContent.innerText = message.content;
                        msgDiv.appendChild(msgContent);

                        let msgFooter = document.createElement('div');
                        msgFooter.className = 'message__footer';
                        let msgFooterTime = document.createElement('p');
                        msgFooterTime.innerText = message.updated_at;
                        msgFooter.appendChild(msgFooterTime);

                        if (message.created_at != message.updated_at) {
                            let msgFooterRedact = document.createElement('p');
                            msgFooterRedact.innerText = '(ред.)';
                            msgFooter.appendChild(msgFooterRedact);
                        }

                        msgDiv.appendChild(msgFooter);
                        messages.appendChild(msgDiv);
                        break;
                    case 'edit-message':
                        let findMsg = messages.querySelector('[data-msgid="' + message.id + '"]');
                        findMsg.innerHTML = "";
                        let editedMsgContent = document.createElement('p');
                        editedMsgContent.className = 'message__content';
                        editedMsgContent.innerText = message.content;
                        findMsg.appendChild(editedMsgContent);

                        let editedMsgFooter = document.createElement('div');
                        editedMsgFooter.className = 'message__footer';
                        let editedMsgFooterTime = document.createElement('p');
                        editedMsgFooterTime.innerText = message.updated_at;
                        editedMsgFooter.appendChild(editedMsgFooterTime);
                        let editedMsgFooterRedact = document.createElement('p');
                        editedMsgFooterRedact.innerText = '(ред.)';
                        editedMsgFooter.appendChild(editedMsgFooterRedact);
                        findMsg.appendChild(editedMsgFooter);
                        break;
                    case 'delete-message':
                        messages.querySelector('[data-msgid="' + message.id + '"]').remove();
                        break;
                    case 'system':
                        let sysMsgDiv = document.createElement('div');
                        sysMsgDiv.innerText = message;
                        messages.appendChild(sysMsgDiv);
                        break;
                }
            }

            websocket.onerror = function (event) {
                console.error('Websocket error: ' + event);
                let output = document.createElement('p');
                output.textContent = 'Произошла ошибка: ' + event.data;
                messages.appendChild(output);
            }

            websocket.onclose = function (event) {
                let output = document.createElement('p');
                output.textContent = 'Соединение закрыто';
                messages.appendChild(output);
                chatForm.elements['send'].disabled = true;
            }

            // Закрываем соединение, если пользователь ушел со страницы чата
            window.addEventListener('beforeunload', function (event) {
                event.preventDefault();

                if (websocket.readyState === WebSocket.OPEN) {
                    websocket.send(JSON.stringify({
                        type: 'close'
                    }));
                }
            });
        }
    }

    function sendMessage(e) {
        e.preventDefault();
        let content = chatForm.elements['content'].value;
        let csrf_token = chatForm.elements['csrf_token'].value;

        if (content === '') {
            alert('Сообщение пустое');
            return;
        }

        chatForm.elements['content'].value = '';

        const transportData = {
            content,
            chat_id,
            user_id,
            csrf_token
        };

        let message;

        // отправляем запрос к контроллеру
        fetch('/api/chat/send', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(transportData)
            })
            .then((response) => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then((data) => {
                if (data.status === 'success') {
                    message = data.message;
                    const wsMsg = {
                        type: 'send-message',
                        message,
                        chat_id,
                    };
                    websocket.send(JSON.stringify(wsMsg));
                } else {
                    console.error('Create message error:', data.message);
                }
                updateCsrfToken(data.csrf_token);
            })
    }
}
