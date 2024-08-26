<h1>Вход</h1>
<form class="form-login" method="post" action="/login/login">
    <div class="form-login__row">
        <label for="login-email">
            Email:
        </label>
        <input type="email" name="email" id="login-email" required>
    </div>
    <div class="form-login__row">
        <label for="login-password">
            Пароль:
        </label>
        <input type="password" name="password" id="login-password" required>
    </div>
    <input type="submit" value="Войти">
</form>
