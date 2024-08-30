<h1>Profile</h1>
<div>
    <?php if (!empty($data['user'])): ?>
    <img src="<?= $data['user']['avatar'] ? '/app/uploads/' . $data['user']['avatar'] : '/app/assets/img/avatar.png' ?>" alt="Avatar">
    <p>Username: <?= $data['user']['username'] ?></p>
    <?php else: ?>
        <p>Такой пользователь не найден.</p>
    <?php endif; ?>
</div>
