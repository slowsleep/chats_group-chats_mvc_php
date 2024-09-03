<?php
function isOwnMessage($message)
{
    return $message['user_id'] == $_SESSION['user']['id'];
}
?>

<div class="chat">
    <h1>Диалог с <?= $data['contact']['contact_name'] ?? 'никем' ?></h1>

    <?php if (isset($data['errors']) && $data['errors']): ?>
        <div>
            <?php foreach ($data['errors'] as $error): ?>
                <p><?= $error ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <div class="messages">
        <?php if (isset($data['messages']) && $data['messages']): ?>
            <?php foreach ($data['messages'] as $message): ?>
                <div class="message <?= isOwnMessage($message) ? 'message--own' : '' ?>">

                <div class="message__header">
                    <p><?= isOwnMessage($message) ? $_SESSION['user']['username'] : $data['contact']['contact_name'] ?></p>
                    <p><?= $message['created_at'] ?></p>
                </div>

                    <p><?= $message['content'] ?></p>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <form class="form" action="chat/send" method="post">
        <input type="hidden" name="chat_id" value="<?= $data['chat_id'] ?? '' ?>">
        <input type="hidden" name="contact_id" value="<?= $data['contact']['contact_id'] ?? '' ?>">
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
        <input type="text" name="content" placeholder="Ваше сообщение...">
        <button>Отправить</button>
    </form>
</div>
