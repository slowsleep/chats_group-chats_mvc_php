let settingsForm = document.querySelector("#settings-form");
let username = settingsForm.elements["username"];
let hideEmail = settingsForm.elements["hide-email"];

settingsForm.addEventListener("submit", (event) => {
    username.style.borderColor = "#c4c4c4";

    let regexpUsername = /^[a-zA-Z0-9]{0,25}$/;

    if (!regexpUsername.test(username.value)) {
        alert("Недопустимое имя пользователя");
        username.style.borderColor = "red";
        event.preventDefault();
    }

    if (hideEmail.checked && username.value == "") {
        alert("Нельзя скрыть email без username");
        username.style.borderColor = "red";
        event.preventDefault();
    }
});
