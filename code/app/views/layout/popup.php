<div class="popup">
    <div class="popup__content">
        <button class="popup__close" id="pupup-close">х</button>
        <div>
            <h2>Создать группу</h2>
            <form id="search-own-contacts-form">
                <input type="text" name="search">
                <input type="submit" value="Найти">
            </form>
            <div class="popup__content__users">
                <ul class="popup__content__users__list"></ul>
            </div>
            <form action="" id="create-group-form">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                <button class="popup__content__create">Создать</button>
            </form>
        </div>
    </div>
</div>
