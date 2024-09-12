// Очищаем список выбранных пользователей чтобы они не портили запросы
clearSelectedUsers();

// Обработка поиска контактов
function handleSearchOwnContactsForm(modal) {
    let searchOwnContactsForm = modal.querySelector(".search-own-contacts-form");
    let usersList = modal.querySelector(".search-own-contacts__list");

    searchOwnContactsForm.addEventListener("submit", (event) => {
        event.preventDefault();
        const form = event.target;
        const formData = new FormData(form); // Собираем данные из формы
        // Преобразуем FormData в строку параметров
        const params = new URLSearchParams(formData);

        fetch(`/api/user/searchcontacts?${params.toString()}`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json'
            }
        })
        .then((response) => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then((data) => {
            if (data.status === 'success') {
                console.log(data);
                let contacts = data.contacts;
                usersList.innerHTML = "";
                contacts.forEach(element => {
                    let userTitle = element.username ? element.username : element.email;
                    let user = createUserElement(userTitle, element.id);
                    usersList.appendChild(user);
                });
            } else {
                usersList.innerHTML = "";
                console.log(data.message);
            }
        })
    });
}

// Получаем контакты пользователя и добавляем их в переданный список контактов
function getUserContacts(modal) {
    let usersList = modal.querySelector(".search-own-contacts__list");
    usersList.innerHTML = "";
    // Получение списка контактов при открытии окна
    fetch('/api/user/contacts', {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json'
            }
        })
        .then((response) => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then((data) => {
            if (data.status === 'success') {
                let contacts = data.contacts;
                contacts.forEach(element => {
                    let userTitle = element.username ? element.username : element.email;
                    let user = createUserElement(userTitle, element.id);
                    usersList.appendChild(user);
                });
            } else {
                console.log(data.message);
            }
        })
}

// Создание элемента списка
function createUserElement(username, id) {
    let user = document.createElement('li');
    user.className = "search-own-contacts__list__item";
    user.innerHTML = `<p>${username}</p>`;
    user.setAttribute("data-user-id", id);

    let btnWrapper = document.createElement('div');
    let addBtn = document.createElement('button');
    addBtn.className = "add-user";
    addBtn.innerHTML = "+";
    addBtn.addEventListener("click", addUser);
    btnWrapper.appendChild(addBtn);

    let remBtn = document.createElement('button');
    remBtn.className = "remove-user";
    remBtn.innerHTML = "-";
    remBtn.addEventListener("click", removeUser);
    btnWrapper.appendChild(remBtn);
    user.appendChild(btnWrapper);

    return user;
}

// Добавление пользователя в список
function addUser(event) {
    event.preventDefault();
    let userId = event.currentTarget.parentNode.parentNode.getAttribute("data-user-id");
    addUserToSelection(userId);
    event.currentTarget.style.display = "none";
    let remBtn = event.currentTarget.nextElementSibling;
    remBtn.style.display = "block";
}

// Удаление пользователя из списка
function removeUser(event) {
    event.preventDefault();
    event.currentTarget.style.display = "none";
    let userId = event.currentTarget.parentNode.parentNode.getAttribute("data-user-id");
    removeUserFromSelection(userId);
    let addBtn = event.currentTarget.previousElementSibling;
    addBtn.style.display = "block";
}

// Добавление пользователя в localStorage
function addUserToSelection(userId) {
    let selectedUsers = getSelectedUsers();
    if (!selectedUsers.includes(userId)) {
        selectedUsers.push(userId);
        localStorage.setItem('selectedUsers', JSON.stringify(selectedUsers));
    }
}

// Удаление пользователя из localStorage
function removeUserFromSelection(userId) {
    let selectedUsers = JSON.parse(localStorage.getItem('selectedUsers')) || [];
    selectedUsers = selectedUsers.filter(id => id !== userId);
    localStorage.setItem('selectedUsers', JSON.stringify(selectedUsers));
}

// Получение списка выбранных пользователей из localStorage
function getSelectedUsers() {
    return JSON.parse(localStorage.getItem('selectedUsers')) || [];
}

// Очистка списка выбранных пользователей из localStorage
function clearSelectedUsers() {
    localStorage.removeItem('selectedUsers');
}
