<div class="main-content">
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
                <img src="<?= $_SESSION['user']['avatar'] ? '/app/uploads/' . $_SESSION['user']['avatar'] : '/app/assets/img/avatar.png' ?>" alt="Avatar" id="avatar-img" width="200">
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

    <fieldset class="settings-change-msg-sound">
        <legend>Настройки чатов</legend>
        <form action="" id="msg-sound-form">
            <label for="msg-sound">Звук уведомления</label>
            <select name="msg-sound" id="msg-sound">
                <option value="1">1</option>
                <option value="2">2</option>
                <option value="3">3</option>
                <option value="4">4</option>
                <option value="5">5</option>
                <option value="6">6</option>
            </select>
        </form>
    </fieldset>

    <?php if (!empty($data['message'])) echo $data['message']; ?>
</div>

<script src="/app/assets/js/checkSettingsFields.js"></script>
<script src="/app/assets/js/msgSoundSettings.js"></script>
