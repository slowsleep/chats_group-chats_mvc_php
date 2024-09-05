<?php
function isOwnMessage($message)
{
    return $message['user_id'] == $_SESSION['user']['id'];
}
?>

<div class="chat">
    <h3>Чат с пользователем <?= $data['contact']['name'] ?? 'никем' ?></h3>

    <?php if (isset($data['errors']) && $data['errors']): ?>
        <div>
            <?php foreach ($data['errors'] as $error): ?>
                <p><?= $error ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <div class="messages" id="messages">
        <?php if (isset($data['messages']) && $data['messages']): ?>
            <?php foreach ($data['messages'] as $message): ?>
                <div class="message <?= isOwnMessage($message) ? 'message--own' : '' ?>">
                    <p><?= $message['content'] ?></p>
                    <p class="message__date"><?= $message['created_at'] ?></p>
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
        <input type="submit" value="">
    </form>
</div>

<script>
    let messages = document.querySelector('#messages');
</script>
<script src="/app/assets/js/scrollChat.js"></script>
<script src="/app/assets/js/chat.js"></script>
