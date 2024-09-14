let chatMenuSound = document.querySelector(".chat-menu-sound");
let soundOn = chatMenuSound.querySelector(".chat-menu-sound__on");
let soundOff = chatMenuSound.querySelector(".chat-menu-sound__off");

document.querySelectorAll(".chat-list__item").forEach(function(item) {
    item.addEventListener("contextmenu", function(event) {
        event.preventDefault();

        fetch(`/api/chat/getsound?id=${item.dataset.chatid}`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json'
            }
        })
        .then((response) => response.json())
        .then((data) => {
            if (data.status === 'success') {
                let isSound = data.notifications_enabled;

                if (isSound == 1) {
                    soundOff.style.display = "block";
                    soundOff.setAttribute("data-chatid", item.dataset.chatid);
                    soundOn.style.display = "none";
                } else {
                    soundOn.style.display = "block";
                    soundOn.setAttribute("data-chatid", item.dataset.chatid);
                    soundOff.style.display = "none";
                }

            }
        })

        // Показываем меню
        chatMenuSound.style.display = "block";
        chatMenuSound.style.left = `${event.pageX}px`;
        chatMenuSound.style.top = `${event.pageY}px`;
    });
})

soundOff.addEventListener("click", function(event) {
    let chatId = event.currentTarget.dataset.chatid;

    fetch(`/api/chat/setsound`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            chat_id: chatId,
            notifications_enabled: false,
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
        // Меняем картинку
        let sound = document.querySelector(`.chat-list__item[data-chatid="${chatId}"]`).querySelector(".chat-list__item__sound");
        sound.firstElementChild.src = "/app/assets/img/sound-off.svg";
        updateCsrfToken(data.csrf_token);
    })
})

soundOn.addEventListener("click", function(event) {
    let chatId = event.currentTarget.dataset.chatid;

    fetch(`/api/chat/setsound`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            chat_id: chatId,
            notifications_enabled: true,
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
        let sound = document.querySelector(`.chat-list__item[data-chatid="${chatId}"]`).querySelector(".chat-list__item__sound");
        sound.firstElementChild.src = "/app/assets/img/sound-on.svg";
        updateCsrfToken(data.csrf_token);
    })
})

// Чтобы закрывалось меню при клике за его пределами
document.addEventListener('click', function () {
    chatMenuSound.style.display = 'none';
});
