<h1>Search user</h1>
<div>
    <ul>
        <li>Друзья</li>
        <li>Все</li>
    </ul>
</div>
<form action="/contacts/search" method="get">
    <input type="text" name="search">
    <input type="submit" value="Search">
</form>

<div>
    <?php if (!empty($data['users'])): ?>
    <ul>
        <?php foreach ($data['users'] as $user): ?>
            <li><a href="/profile?user=<?= $user['id'] ?>"><?= strlen($user['username']) > 0 ? $user['username'] : $user['email'] ?></a></li>
        <?php endforeach; ?>
    </ul>
    <?php else: ?>
        <p>Пользователи не найдены</p>
    <?php endif; ?>
</div>
