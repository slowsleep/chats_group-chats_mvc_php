<?php if (isset($_SESSION['user']) && $_SESSION['user']) : ?>
    <div class="chat-list">
        <div class="chat-list__dialogs">
            <?php if ($data['chats']) : ?>
            <ul>
                <?php foreach ($data['chats'] as $chat) : ?>
                    <li class="chat-list__item chat-list__dialogs__item" data-chatid="<?= $chat['id'] ?>">
                        <div>
                            <a href="/profile?user=<?= $chat['user_id'] ?>">
                                <img src="<?= $chat['avatar'] ? '/app/uploads/' . $chat['avatar'] : '/app/assets/img/avatar.png' ?>" alt="Avatar" width="30">
                            </a>
                            <a href="/chat?user=<?= $chat['user_id'] ?>""><?= $chat['title'] ?></a>
                        </div>
                        <div class="chat-list__item__sound">
                            <img src="/app/assets/img/<?= $chat['notifications_enabled'] == 1 ? 'sound-on.svg' : 'sound-off.svg' ?>" alt="">
                        </div>
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
                        <li class="chat-list__item chat-list__groups__item" data-chatid="<?= $group['chat_id'] ?>">
                            <a href="/chat/group?id=<?= $group['chat_id'] ?>">Групповой чат <?= $group['chat_id'] ?></a>
                            <div class="chat-list__item__sound">
                                <img src="/app/assets/img/<?= $group['notifications_enabled'] == 1 ? 'sound-on.svg' : 'sound-off.svg' ?>" alt="">
                            </div>
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

<div class="chat-menu-sound">
    <p class="chat-menu-sound__on">Включить уведомления</p>
    <p class="chat-menu-sound__off">Выключить уведомления</p>
</div>

<script src="/app/assets/js/chatList.js"></script>
