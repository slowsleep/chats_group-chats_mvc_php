let msgMenu = document.querySelector(".message-menu");
let msgEditBtn = msgMenu.querySelector("#msg-edit");
let msgDeleteBtn = msgMenu.querySelector("#msg-delete");
let msgOpenForwardBtn = msgMenu.querySelector("#openForwardMessageModalBtn");

// Хранение ссылок на текущие обработчики манипулирования сообщением
let currentEditHandler;
let currentDeleteHandler;
let currentForwardHandler;
let forwardMsgHandler;

if (messages) {
    // Вешаем событие на контейнер сообщений
    messages.addEventListener("contextmenu", (event) => {
        // Проверяем, является ли элемент, на который кликнули, сообщением
        if (event.target.closest('.message')) {
            event.preventDefault();
            let curElem = event.target;

            // Если нажали на не div, а на p, то берем div
            if (curElem.tagName == "P") {
                curElem = curElem.parentElement;
            }

            let msgId = curElem.getAttribute("data-msgid");
            let isOwnMsg = false;

            // Сначала проверяем принадлежность сообщения к текущему юзеру
            fetch(`/api/message/check?id=${msgId}`, {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json'
                    }
                })
                .then((response) => response.json())
                .then((data) => {
                    isOwnMsg = data.is_own;

                    if (isOwnMsg == true) {
                        // Если принадлежит, то показываем меню
                        msgMenu.style.display = "block";
                        msgMenu.style.left = `${event.pageX}px`;
                        msgMenu.style.top = `${event.pageY}px`;

                        // Редактирование
                        // Удаляем предыдущий обработчик
                        if (currentEditHandler) {
                            msgEditBtn.removeEventListener("click", currentEditHandler);
                        }

                        // Сохраняем текущий обработчик, чтобы его можно было удалить при следующем вызове
                        currentEditHandler = handlerEditMessageBtn;
                        msgEditBtn.addEventListener("click", handlerEditMessageBtn);

                        // Удаление
                        if (currentDeleteHandler) {
                            msgDeleteBtn.removeEventListener("click", currentDeleteHandler);
                        }

                        currentDeleteHandler = deleteMessage;
                        msgDeleteBtn.addEventListener("click", deleteMessage);

                        // Пересылка
                        if (currentForwardHandler) {
                            msgOpenForwardBtn.removeEventListener("click", currentForwardHandler);
                        }

                        currentForwardHandler = openForwardMessageModal;
                        msgOpenForwardBtn.addEventListener("click", openForwardMessageModal);

                        function handlerEditMessageBtn(event) {
                            event.preventDefault();
                            let content = curElem.querySelector(".message__content").innerText;
                            chatForm.elements['content'].value = content;
                            // Удаляем предыдущий евент, чтобы сообщение не отправлялось
                            chatForm.removeEventListener("submit", sendMessage);
                            chatForm.addEventListener("submit", editMessage);

                            function editMessage(event) {
                                event.preventDefault();
                                let newContent = chatForm.elements['content'].value;

                                // Отправляем запрос на редактирование сообщения
                                fetch(`/api/message/edit`, {
                                        method: 'POST',
                                        headers: {
                                            'Content-Type': 'application/json'
                                        },
                                        body: JSON.stringify({
                                            message_id: msgId,
                                            content: newContent,
                                            csrf_token: csrfToken.getAttribute('content')
                                        })
                                    })
                                    .then((response) => {
                                        if (!response.ok) {
                                            throw new Error(`HTTP error! status: ${response.status}`);
                                        }
                                        return response.json();
                                    })
                                    .then((data) => {
                                        if (data.status === 'success') {
                                            let message = {
                                                id: msgId,
                                                content: chatForm.elements['content'].value,
                                                updated_at: data.updated_at
                                            };
                                            const wsMsg = {
                                                type: 'edit-message',
                                                message,
                                                chat_id: chatForm.elements['chat_id'].value
                                            };
                                            websocket.send(JSON.stringify(wsMsg));
                                        }
                                        updateCsrfToken(data.csrf_token);
                                        chatForm.elements['content'].value = '';
                                        // Удаляем обработчик редактирования сообщения
                                        chatForm.removeEventListener("submit", editMessage);
                                        // Возвращаем обработчик, чтобы снова можно было отправлять сообщения
                                        chatForm.addEventListener("submit", sendMessage);
                                    })
                            }
                        }

                        function deleteMessage() {
                            let isDelete = confirm('Удалить сообщение?');
                            if (isDelete) {
                                fetch(`/api/message/delete`, {
                                        method: 'POST',
                                        headers: {
                                            'Content-Type': 'application/json'
                                        },
                                        body: JSON.stringify({
                                            message_id: msgId,
                                            csrf_token: csrfToken.getAttribute('content')
                                        })
                                    })
                                    .then((response) => {
                                        if (!response.ok) {
                                            throw new Error(`HTTP error! status: ${response.status}`);
                                        }
                                        return response.json();
                                    })
                                    .then((data) => {
                                        if (data.status === 'success') {
                                            let message = {
                                                id: msgId,
                                            };
                                            const wsMsg = {
                                                type: 'delete-message',
                                                message,
                                                chat_id: chatForm.elements['chat_id'].value
                                            };
                                            websocket.send(JSON.stringify(wsMsg));
                                            updateCsrfToken(data.csrf_token);
                                        }
                                    })
                            }
                        }

                        function openForwardMessageModal(event) {
                            event.preventDefault();
                            openModal("forwardMessageModal");
                            let msgForwardBtn = document.querySelector("#msg-forward");

                            if (forwardMsgHandler) {
                                msgForwardBtn.removeEventListener("click", forwardMsgHandler);
                            }

                            forwardMsgHandler = forwardMessage;
                            msgForwardBtn.addEventListener("click", forwardMessage);
                        }

                        function forwardMessage() {
                            let userList = getSelectedUsers();

                            let message = {
                                id: msgId,
                                chat_id: chatForm.elements['chat_id'].value,
                                content: curElem.querySelector(".message__content").innerText,
                                updated_at: curElem.querySelector(".message__footer").innerText
                            }

                            asyncForwardMessages(userList);
                            closeModal("forwardMessageModal");

                            async function asyncForwardMessages() {
                                for (const user of userList) {
                                    try {
                                        const response = await fetch(`/api/message/forward`, {
                                            method: 'POST',
                                            headers: {
                                                'Content-Type': 'application/json'
                                            },
                                            body: JSON.stringify({
                                                message,
                                                contact_id: user,
                                                csrf_token: csrfToken.getAttribute('content')
                                            })
                                        });

                                        if (!response.ok) {
                                            throw new Error(`HTTP error! status: ${response.status}`);
                                        }

                                        const data = await response.json();

                                        if (data.status === 'success') {
                                            // TODO: Переслать сообщение через websocket
                                            // const wsMsg = {
                                            //     type: 'forward-message',
                                            //     message,
                                            //     chat_id: data.chat_id
                                            // };
                                            // websocket.send(JSON.stringify(wsMsg));
                                        }

                                        updateCsrfToken(data.csrf_token);

                                    } catch (error) {
                                        console.error('Ошибка при пересылке сообщения:', error);
                                        break;
                                    }
                                }
                            }
                        }
                    }
                })
        }
    });
}

// Чтобы закрывалось меню при клике за его пределами
document.addEventListener('click', function () {
    msgMenu.style.display = 'none';
});
