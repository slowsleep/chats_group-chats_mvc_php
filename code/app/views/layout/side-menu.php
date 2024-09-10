<hr>
<div class="side-menu">
    <?php if (isset($_SESSION['user']) && $_SESSION['user']) : ?>
        <ul class="side-menu__list">
            <li class="side-menu__list__item"><a href="/">Главная</a></li>
            <li class="side-menu__list__item"><a href="/profile?user=<?= $_SESSION['user']['id'] ?>">Профиль</a></li>
            <li class="side-menu__list__item"><a href="/contacts">Контакты</a></li>
            <li class="side-menu__list__item"><a href="/chat">Чаты</a></li>
            <li class="side-menu__list__item"><a href="/settings">Настройки</a></li>
            <li class="side-menu__list__item" id="openCreateGroupChatModalBtn"">Создать группу</li>
            <li class="side-menu__list__item"><a href="/logout">Выйти</a></li>
        </ul>
    <?php else : ?>
        <ul class="side-menu__list">
            <li class="side-menu__list__item">
                <a href="/login">Войти</a>
            </li>
            <li class="side-menu__list__item">
                <a href="/registration">Зарегестрироваться</a>
            </li>
        </ul>
    <?php endif; ?>
</div>
