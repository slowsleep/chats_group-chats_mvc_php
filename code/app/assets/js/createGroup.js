let createGroup = document.querySelector("#create-group");
let popup = document.querySelector(".popup");
let popupCloseBtn = document.querySelector("#pupup-close");

createGroup.addEventListener("click", (event) => {
    event.preventDefault();
    popup.style.display = "flex";

    popupCloseBtn.addEventListener("click", () => {
        popup.style.display = "none";
    });
});

if (popup) {
    let addUserBtn = document.querySelectorAll(".add-user");
    let removeUserBtn = document.querySelectorAll(".remove-user");
    let createGroupBtn = document.querySelector(".popup__content__create");
    let usersList = document.querySelector(".popup__content__users__list");
    let userList = [];

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

    addUserBtn.forEach(element => {
        element.addEventListener("click", addUser);
    })

    removeUserBtn.forEach(element => {
        element.addEventListener("click", removeUser);
    })

    // Обработка поиска контактов
    let createGroupForm = document.querySelector("#create-group-form");

    createGroupForm.addEventListener("submit", (event) => {
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
                let contacts = data.contacts;
                usersList.innerHTML = "";
                contacts.forEach(element => {
                    let userTitle = element.username ? element.username : element.email;
                    let user = createUserElement(userTitle, element.id);
                    usersList.appendChild(user);
                });
            } else {
                console.log(data.message);
            }
        })
    });

    createGroupBtn.addEventListener("click", (event) => {
        event.preventDefault();
        console.log(userList);
    });

    function createUserElement(username, id) {
        let user = document.createElement('li');
        user.className = "popup__content__users__list__item";
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

    function addUser(event) {
        event.preventDefault();
        let userId = event.currentTarget.parentNode.parentNode.getAttribute("data-user-id");
        userList.push(userId);
        event.currentTarget.style.display = "none";
        let remBtn = event.currentTarget.nextElementSibling;
        remBtn.style.display = "block";
    }

    function removeUser(event) {
        event.preventDefault();
        event.currentTarget.style.display = "none";
        let userId = event.currentTarget.parentNode.parentNode.getAttribute("data-user-id");
        let index = userList.indexOf(userId);
        userList.splice(index, 1);
        let addBtn = event.currentTarget.previousElementSibling;
        addBtn.style.display = "block";
    }
}
