<hr>
<div class="side-menu">
    <?php if (isset($_SESSION['user']) && $_SESSION['user']) : ?>
        <ul class="side-menu__list">
            <li class="side-menu__list__item">Профиль</li>
            <li class="side-menu__list__item"><a href="/settings">Настройки</a></li>
            <li class="side-menu__list__item">Создать группу</li>
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
