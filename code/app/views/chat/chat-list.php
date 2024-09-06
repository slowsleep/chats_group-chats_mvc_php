<?php if (isset($_SESSION['user']) && $_SESSION['user']) : ?>
    <div class="chat-list">
        <div class="chat-list__dialogs">
            <?php if ($data['chats']) : ?>
            <ul>
                <?php foreach ($data['chats'] as $chat) : ?>
                    <li>
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
            <p>2</p>
        </div>
    </div>
    <hr>
<?php endif; ?>
