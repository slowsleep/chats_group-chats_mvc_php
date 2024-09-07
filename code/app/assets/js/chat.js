// let message from chat view
const wsUrl = 'ws://webchat.local:3000/server.php';
let chatForm = document.querySelector('#chat-form');
if (chatForm) {
    let chat_id = chatForm.elements['chat_id'].value;
    let user_id = chatForm.elements['user_id'].value;

    chatForm.addEventListener('submit', sendMessage);

    if (window.location.search) {
        let getUserId = window.location.search.split('?')[1].split('=')[1];

        if (getUserId) {
            websocket = new WebSocket(wsUrl);

            websocket.onopen = function (event) {
                messages.innerHTML += '<p>Соединение установлено</p>';
                websocket.send(JSON.stringify({
                    chat_id
                }));
            }

            websocket.onmessage = function (event) {
                const response = JSON.parse(event.data);
                const res_type = response.type;
                const message = response.message;

                switch (res_type) {
                    case 'usermsg':
                        let msgDiv = document.createElement('div');
                        msgDiv.className = 'message ' + (message.user_id == user_id ? 'message--own' : '');
                        msgDiv.innerHTML = '<p>' + message.content + '</p><p class="message__date">' + message.updated_at + '</p>';
                        messages.appendChild(msgDiv);
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
                        message,
                        chat_id
                    };
                    websocket.send(JSON.stringify(wsMsg));
                } else {
                    console.error('Create message error:', data.message);
                }
                chatForm.elements['csrf_token'].value = data.csrf_token;
            })
    }
}
