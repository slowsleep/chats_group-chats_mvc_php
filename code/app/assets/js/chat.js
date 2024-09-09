// let message from chat view
const wsUrl = 'ws://webchat.local:3000/server.php';
let chatForm = document.querySelector('#chat-form');
if (chatForm) {
    let chat_id = chatForm.elements['chat_id'].value;
    let user_id = chatForm.elements['user_id'].value;

    chatForm.addEventListener('submit', sendMessage);

    if (window.location.search) {
        let getUserIdOrChatId = window.location.search.split('?')[1].split('=')[1];
        console.log(getUserIdOrChatId);

        if (getUserIdOrChatId) {
            websocket = new WebSocket(wsUrl);

            websocket.onopen = function (event) {
                messages.innerHTML += '<p>Соединение установлено</p>';
                websocket.send(JSON.stringify({
                    type: 'start',
                    chat_id
                }));
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
                        msgDiv.innerHTML = '<p class="message__content">' + message.content + '</p>' +
                            '<div class="message__footer"><p>' + message.updated_at + '</p>';
                        if (message.created_at != message.updated_at) msgDiv.innerHTML += '<p>(ред.)</p></div>';
                        messages.appendChild(msgDiv);
                        break;
                    case 'edit-message':
                        messages.querySelector('[data-msgid="' + message.id + '"]').innerHTML = '<p class="message__content">' + message.content + '</p>' + '<div class="message__footer"><p>' + message.updated_at + '</p><p>(ред.)</p></div>';
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
                messages.innerHTML += '<p>Произошла ошибка: ' + event.data + '</p>';
            }

            websocket.onclose = function (event) {
                messages.innerHTML += '<p>Соединение закрыто</p>';
            }
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
                        chat_id
                    };
                    websocket.send(JSON.stringify(wsMsg));
                } else {
                    console.error('Create message error:', data.message);
                }
                updateCsrfToken(data.csrf_token);
            })
    }
}
