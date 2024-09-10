<div class="modal" id="createGroupChatModal">
    <div class="modal__content">
        <button class="modal-close">х</button>
        <div>
            <h2>Создать группу</h2>
            <?php include APP_DIR . '/views/layout/searchContacts.php' ?>
            <form action="" id="create-group-form">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                <button>Создать</button>
            </form>
        </div>
    </div>
</div>
