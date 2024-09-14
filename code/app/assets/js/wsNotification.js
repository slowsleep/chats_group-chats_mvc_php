// curUserId from script layout_view.php
// wsUrl from script layout_view.php
let wsNotifications = new WebSocket(wsUrl);
let numSoundLS = localStorage.getItem('msgSound');

if (numSoundLS == null) {
    localStorage.setItem('msgSound', 1);
}

wsNotifications.onopen = function (event) {
    wsNotifications.send(JSON.stringify({
        type: 'start-notification',
        user_id: curUserId
    }));

    // Отправляем пинги каждые 30 секунд
    const pingInterval = setInterval(() => {
        if (wsNotifications.readyState === WebSocket.OPEN) {
            wsNotifications.send(JSON.stringify({
                type: 'ping'
            }));
        } else {
            clearInterval(pingInterval); // Останавливаем пинги, если соединение закрыто
        }
    }, 30000);
}

wsNotifications.onmessage = function (event) {
    const response = JSON.parse(event.data);
    const res_type = response.type;

    switch (res_type) {
        case 'notification':
            let numSound = localStorage.getItem('msgSound');
            var audio = new Audio('/app/assets/audio/'+ numSound + '.mp3');
            audio.play();
            break;
    }
}

wsNotifications.onerror = function (event) {
    console.error('Websocket error: ' + event);
}

wsNotifications.onclose = function (event) {
    console.error('Websocket close: ' + event);
}
