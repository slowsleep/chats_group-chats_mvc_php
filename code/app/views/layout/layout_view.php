<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/app/assets/css/style.css">
    <title><?php echo $title; ?></title>
</head>

<body>
    <main>
        <div class="chat-list">
            <div class="chat-list__dialogs">
                <p>1</p>
            </div>
            <hr>
            <div class="chat-list__groups">
                <p>2</p>
            </div>
        </div>
        <hr>
        <div class="content">
            <?php include_once APP_DIR . '/views/' . $content_view; ?>
        </div>
        <hr>
        <div class="side-menu">
            <ul class="side-menu__list">
                <li class="side-menu__list__item">Профиль</li>
                <li class="side-menu__list__item">Настройки</li>
                <li class="side-menu__list__item">Создать группу</li>
                <li class="side-menu__list__item">Выйти</li>
            </ul>
            <ul>
                <li>
                    <a href="login">Войти</a>
                    <a href="registration">Зарегестрироваться</a>
                </li>
            </ul>
        </div>
    </main>
</body>

</html>
