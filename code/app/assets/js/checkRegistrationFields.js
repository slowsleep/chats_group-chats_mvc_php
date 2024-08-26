let formRegistration = document.querySelector(".form-registration");
let username = formRegistration.elements["reg-username"];
let email = formRegistration.elements["reg-email"];
let password = formRegistration.elements["reg-password"];
let passwordRepeat = formRegistration.elements["reg-password-repeat"];

formRegistration.addEventListener("submit", (event) => {
    username.style.borderColor = "#c4c4c4";
    email.style.borderColor = "#c4c4c4";
    password.style.borderColor = "#c4c4c4";
    passwordRepeat.style.borderColor = "#c4c4c4";

    let regexpUsername = /^[a-zA-Z0-9]{3,25}$/;
    let regexpEmail = /^([a-zA-Z0-9_.-])+@([a-zA-Z0-9_.-])+\.([a-zA-Z])+/;
    let regexpPassword = /^[a-zA-Z0-9]{8,25}$/;

    if (!regexpUsername.test(username.value)) {
        alert("Недопустимое имя пользователя");
        username.style.borderColor = "red";
        event.preventDefault();
    }

    if (!regexpEmail.test(email.value)) {
        alert("Недопустимая почта");
        email.style.borderColor = "red";
        event.preventDefault();
    }

    if (!regexpPassword.test(password.value)) {
        alert("Недопустимый пароль");
        password.style.borderColor = "red";
        passwordRepeat.style.borderColor = "red";
        event.preventDefault();
    }

    if (password.value !== passwordRepeat.value) {
        alert("Пароли не совпадают");
        password.style.borderColor = "red";
        passwordRepeat.style.borderColor = "red";
        event.preventDefault();
    }
});
