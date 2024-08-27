<?php
use function App\Tools\generateCsrfToken;
?>
<h1>Регистрация</h1>
<form class="form" method="post" action="/registration/register">
    <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
    <div class="form__row">
        <label for="reg-username">
            Username:
        </label>
        <input type="text" name="username" id="reg-username" required>
    </div>
    <div class="form__row">
        <label for="reg-email">
            Email:
        </label>
        <input type="email" name="email" id="reg-email" required>
    </div>
    <div class="form__row">
        <label for="reg-password">
            Пароль:
        </label>
        <input type="password" name="password" id="reg-password" required>
    </div>
    <div class="form__row">
        <label for="reg-password-repeat">
            Повтор пароля:
        </label>
        <input type="password" name="password-repeat" id="reg-password-repeat" required>
    </div>
    <input type="submit" value="Зарегестрироваться">
</form>

<?php if (!empty($data['message'])) echo $data['message']; ?>

<?php if (!empty($data['errors'])): ?>
    <?php foreach ($data['errors'] as $key => $value): ?>
        <p><?= $value ?></p>
    <?php endforeach; ?>
<?php endif; ?>

<script src="/app/assets/js/checkRegistrationFields.js"></script>
