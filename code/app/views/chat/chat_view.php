<?php
function isOwnMessage($message)
{
    return $message['user_id'] == $_SESSION['user']['id'];
}
?>
<div class="chat-page">
    <?php include_once APP_DIR . '/views/chat/chat-list.php' ?>
    <div class="chat main-content">

        <?php if (!isset($_GET['user']) && (!isset($_GET['id'])) || (isset($data['errors']) && $data['errors'])): ?>
            <div>
                <h1>Выберите чат для начала общения</h1>
                <?php if (isset($data['errors']) && $data['errors']): ?>
                    <div>
                        <?php foreach ($data['errors'] as $error): ?>
                            <p><?= $error ?></p>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <?php if (isset($_GET['user'])): ?>
                <h3>Чат с пользователем <?= $data['contact']['name'] ?></h3>
            <?php elseif (isset($_GET['id'])): ?>
                <h3>Групповой чат <?= $_GET['id'] ?></h3>
            <?php endif; ?>
            <?= 'chat_id: ' . $data['chat_id'] ?? '' ?>

            <div class="messages" id="messages">
                <?php if (isset($data['messages']) && $data['messages']): ?>
                    <?php foreach ($data['messages'] as $message): ?>
                        <div
                            class="message <?= isOwnMessage($message) ? 'message--own' : '' ?>"
                            data-msgid="<?= $message['id'] ?>"
                        >
                            <?php if ($message['is_forwarded']): ?>
                                <p class="message__forwarded">&#9166; Пересланное сообщение</p>
                            <?php endif; ?>
                            <p class="message__content"><?= htmlspecialchars($message['content'], ENT_QUOTES, 'UTF-8'); ?></p>
                            <div class="message__footer">
                                <p><?= $message['updated_at'] ?></p>
                                <?php if ($message['created_at'] != $message['updated_at']): ?>
                                    <p>(ред.)</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <form class="form form-chat" method="post" id="chat-form">
                <input type="hidden" name="chat_id" value="<?= $data['chat_id'] ?? '' ?>">
                <input type="hidden" name="user_id" value="<?= $_SESSION['user']['id'] ?? '' ?>">
                <input type="hidden" name="contact_id" value="<?= $data['contact']['id'] ?? '' ?>">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                <input type="text" name="content" placeholder="Наберите Ваше сообщение здесь">
                <input type="submit" value="" name="send" disabled>
            </form>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="message-menu">
    <ul>
        <li id="msg-edit">Редактировать</li>
        <li id="msg-delete">Удалить</li>
        <li id="openForwardMessageModalBtn">Переслать</li>
    </ul>
</div>

<div class="modal" id="forwardMessageModal">
    <div class="modal__content">
        <button class="modal-close">х</button>
        <div>
            <h2>Переслать сообщение</h2>
            <?php include APP_DIR . '/views/layout/searchContacts.php' ?>
            <button id="msg-forward">Переслать</button>
        </div>
    </div>
</div>

<script>
    let messages = document.querySelector('#messages');
</script>
<script src="/app/assets/js/scrollChat.js"></script>
<script src="/app/assets/js/chat.js"></script>
<script src="/app/assets/js/msgHandler.js"></script>
