<div class="main-content">
    <h1>Profile</h1>
    <div>
        <?php if (!empty($data['user'])): ?>
        <img src="<?= $data['user']['avatar'] ? '/app/uploads/' . $data['user']['avatar'] : '/app/assets/img/avatar.png' ?>" alt="Avatar" width="200">
        <p>Username: <?= $data['user']['username'] ?></p>
        <p>Email: <?= $data['user']['email'] ?></p>
        <?php else: ?>
            <p>Такой пользователь не найден.</p>
        <?php endif; ?>
    </div>

    <?php if (isset($data['user']) && $data['user'] && $data['user']['id'] != $_SESSION['user']['id']): ?>
    <div style="display: flex;">
        <a href="/chat?user=<?= $data['user']['id'] ?>" style="padding: .3em; background-color: #337fcc; color: #fff; text-decoration: none; border-radius: 5px;">В диалог</a>

        <?php if ($data['isSubscribed']): ?>
            <form action="/profile/unsubscribe" method="post">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                <input type="hidden" name="user" value="<?= $data['user']['id'] ?>">
                <button>Отписаться</button>
            </form>
        <?php else: ?>
            <form action="/profile/subscribe" method="post">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                <input type="hidden" name="user" value="<?= $data['user']['id'] ?>">
                <button>Подписаться</button>
            </form>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</div>
