<div>
    <h1>Диалог с <?= $data['contact_name'] ?? 'никем' ?></h1>

    <?php if ($data['errors']): ?>
        <?php foreach ($data['errors'] as $error): ?>
            <p><?= $error ?></p>
        <?php endforeach; ?>
    <?php endif; ?>

    <form action="">
        <input type="text" placeholder="Ваше сообщение...">
        <button>Отправить</button>
    </form>
</div>
