<div class="main-content">
    <h1>Вход</h1>
    <form class="form" method="post" action="/login/login">
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
        <div class="form__row">
            <label for="login-email">
                Email:
            </label>
            <input type="email" name="email" id="login-email" required>
        </div>
        <div class="form__row">
            <label for="login-password">
                Пароль:
            </label>
            <input type="password" name="password" id="login-password" required>
        </div>
        <input type="submit" value="Войти">
    </form>
</div>

<?php if (!empty($data['message'])) echo $data['message']; ?>
