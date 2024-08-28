<?php
use function App\Tools\generateCsrfToken;
?>
<h1>Настройки</h1>

<form class="form" method="post" action="/settings/save" enctype="multipart/form-data">
    <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
    <div class="form__row">
        <label for="username">Ник:</label>
        <input type="text" name="username" id="username" value="<?= $_SESSION['user']['username'] ?? '' ?>">
    </div>

    <div class="form__row">
        <label for="avatar">Аватар:</label>
        <div class="form__row__avatar">
            <img src="<?= $_SESSION['user']['avatar'] ?'/app/uploads/' . $_SESSION['user']['avatar'] : '/app/assets/img/avatar.png' ?>" alt="Avatar" id="avatar-img">
            <input type="file" name="avatar" id="avatar">
            <div class="form__row__avatar__del">
                <label for="del-avatar">Удалить аватар:</label>
                <input type="checkbox" name="del-avatar" id="del-avatar">
            </div>
        </div>
    </div>

    <div class="form__row">
        <label for="email-hide">Скрыть email:</label>
        <input class="form__row__checkbox" type="checkbox" name="email-hide" id="email-hide">
    </div>

    <input type="submit" value="Сохранить">
</form>

<?php if (!empty($data['message'])) echo $data['message']; ?>
