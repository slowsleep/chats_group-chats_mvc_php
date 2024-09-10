let openCreateGroupChatModalBtn = document.querySelector("#openCreateGroupChatModalBtn");

if (openCreateGroupChatModalBtn) {
    openCreateGroupChatModalBtn.addEventListener("click", function() {
        openModal("createGroupChatModal");
    });
}

let modalCreateGroup = document.querySelector("#createGroupChatModal");

if (modalCreateGroup) {
    let createGroupForm = document.querySelector("#create-group-form");

    // Создание группового чата
    createGroupForm.addEventListener("submit", (event) => {
        let csrf_token = createGroupForm.elements['csrf_token'].value;
        event.preventDefault();
        let userList = getSelectedUsers();

        if (userList.length < 2) {
            alert("Необходимо добавить 2 или более контактов");
            return;
        }

        fetch('/api/chat/creategroup', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({users: userList, csrf_token})
        })
        .then((response) => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then((data) => {
            createGroupForm.elements['csrf_token'].value = data.csrf_token;
            if (data.status === 'success') {
                clearSelectedUsers();
                window.location.href = '/chat/group?id=' + data.chat_id;
            } else {
                console.log(data.message);
            }
        })
    });
}
