<div class="main-content">
    <h1>Контакты</h1>
    <form action="/contacts/search" method="get">
        <input type="text" name="search">
        <input type="submit" value="Search">
    </form>

    <div>
        <?php if (!empty($data['users'])): ?>
        <ul class="contacts__list">
            <?php foreach ($data['users'] as $user): ?>
                <li class="contacts__list__item" ><a href="/profile?user=<?= $user['id'] ?>"><?= strlen($user['username']) > 0 ? $user['username'] : $user['email'] ?></a></li>
            <?php endforeach; ?>
        </ul>
        <?php else: ?>
            <p>Пользователи не найдены</p>
        <?php endif; ?>
    </div>
</div>
