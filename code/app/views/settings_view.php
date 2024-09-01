<h1>Настройки</h1>

<form class="form" method="post" action="/settings/save" enctype="multipart/form-data" id="settings-form">
    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
    <div class="form__row">
        <label for="username">Ник:</label>
        <input type="text" name="username" id="username" value="<?= $_SESSION['user']['username'] ?? '' ?>">
    </div>

    <div class="form__row">
        <label for="avatar">Аватар:</label>
        <div class="form__row__avatar">
            <img src="<?= $_SESSION['user']['avatar'] ? '/app/uploads/' . $_SESSION['user']['avatar'] : '/app/assets/img/avatar.png' ?>" alt="Avatar" id="avatar-img">
            <input type="file" name="avatar" id="avatar">
            <div class="form__row__avatar__del">
                <label for="del-avatar">Удалить аватар:</label>
                <input type="checkbox" name="del-avatar" id="del-avatar">
            </div>
        </div>
    </div>

    <div class="form__row">
        <label for="hide-email">Скрыть email:</label>
        <input class="form__row__checkbox" type="checkbox" name="hide-email" id="hide-email" <?= $_SESSION['user']['hide_email'] == '1' ? 'checked' : '' ?> >
    </div>

    <input type="submit" value="Сохранить">
</form>

<?php if (!empty($data['message'])) echo $data['message']; ?>

<script src="/app/assets/js/checkSettingsFields.js"></script>
