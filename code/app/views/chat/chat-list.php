<?php if (isset($_SESSION['user']) && $_SESSION['user']) : ?>
    <div class="chat-list">
        <div class="chat-list__dialogs">
            <?php if ($data['chats']) : ?>
            <ul>
                <?php foreach ($data['chats'] as $chat) : ?>
                    <li>
                        <a href="/profile?user=<?= $chat['user_id'] ?>">
                            <img src="<?= $chat['avatar'] ? '/app/uploads/' . $chat['avatar'] : '/app/assets/img/avatar.png' ?>" alt="Avatar" width="30">
                        </a>
                        <a href="/chat?user=<?= $chat['user_id'] ?>""><?= $chat['title'] ?></a>
                    </li>
                <?php endforeach; ?>
            </ul>
            <?php else : ?>
                <p>Нет чатов</p>
            <?php endif; ?>
        </div>
        <hr>
        <div class="chat-list__groups">
            <?php if ($data['groups']) : ?>
                <ul>
                    <?php foreach ($data['groups'] as $group) : ?>
                        <li>
                            <a href="/chat/group?id=<?= $group['chat_id'] ?>">Групповой чат <?= $group['chat_id'] ?></a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else : ?>
                <p>Нет групп</p>
            <?php endif; ?>
        </div>
    </div>
    <hr>
<?php endif; ?>
