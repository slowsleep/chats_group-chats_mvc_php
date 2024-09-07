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
            console.log(contacts);

            let usersList = document.querySelector(".popup__content__users__list");
            contacts.forEach(element => {
                let userTitle = element.username ? element.username : element.email;
                let user = createUserElement(userTitle, element.id);
                usersList.appendChild(user);
            });
            console.log("контакты получены");
        } else {
            console.log(data.message);
        }
    })

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

    let userList = [];

    addUserBtn.forEach(element => {
        element.addEventListener("click", addUser);
    })

    function addUser(event) {
        event.preventDefault();
        let userId = event.currentTarget.parentNode.parentNode.getAttribute("data-user-id");
        userList.push(userId);
        event.currentTarget.style.display = "none";
        let remBtn = event.currentTarget.nextElementSibling;
        remBtn.style.display = "block";
    }

    removeUserBtn.forEach(element => {
        element.addEventListener("click", removeUser);
    })

    function removeUser(event) {
        event.preventDefault();
        event.currentTarget.style.display = "none";
        let userId = event.currentTarget.parentNode.parentNode.getAttribute("data-user-id");
        let index = userList.indexOf(userId);
        userList.splice(index, 1);
        let addBtn = event.currentTarget.previousElementSibling;
        addBtn.style.display = "block";
    }

    createGroupBtn.addEventListener("click", (event) => {
        event.preventDefault();
        console.log(userList);
    });
}
