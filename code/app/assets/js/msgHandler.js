let msgMenu = document.querySelector(".message-menu");

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

                        let msgEditBtn = msgMenu.querySelector("#msg-edit");
                        let msgDeleteBtn = msgMenu.querySelector("#msg-delete");
                        let msgForwardBtn = msgMenu.querySelector("#msg-forward");

                        // Обработка нажатия на кнопки редактирования
                        msgEditBtn.addEventListener("click", () => {
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
                                            csrf_token: chatForm.elements['csrf_token'].value
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
                                            curElem.querySelector(".message__content").innerText = chatForm.elements['content'].value;
                                            curElem.querySelector(".message__footer").innerHTML = '<p>' + data.updated_at + '</p><p>(ред.)</p>';
                                        }
                                        chatForm.elements['csrf_token'].value = data.csrf_token;
                                        chatForm.elements['content'].value = '';
                                        // Удаляем обработчик редактирования сообщения
                                        chatForm.removeEventListener("submit", editMessage);
                                        // Возвращаем обработчик, чтобы снова можно было отправлять сообщения
                                        chatForm.addEventListener("submit", sendMessage);
                                    })
                            }
                        });

                        // Обработка нажатия на кнопки удаления
                        msgDeleteBtn.addEventListener("click", () => {
                            let isDelete = confirm('Удалить сообщение?');
                            if (isDelete) {
                                fetch(`/api/message/delete`, {
                                        method: 'POST',
                                        headers: {
                                            'Content-Type': 'application/json'
                                        },
                                        body: JSON.stringify({
                                            message_id: msgId
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
                                            curElem.remove();
                                            console.log("is deleted");
                                        }
                                    })
                            }
                        });
                    }
                })
        }
    });
}

// Чтобы закрывалось меню при клике за его пределами
document.addEventListener('click', function () {
    msgMenu.style.display = 'none';
});