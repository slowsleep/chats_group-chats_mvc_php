<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/app/assets/css/style.css">
    <meta name="csrf-token" content="<?= $_SESSION['csrf_token'] ?? '' ?>">
    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        function updateCsrfToken(newToken) {
            document.querySelector('meta[name="csrf-token"]').setAttribute('content', newToken);

            const csrfInputs = document.querySelectorAll('input[name="csrf_token"]');
            csrfInputs.forEach(input => {
                input.value = newToken;
            });
        }
        const wsUrl = 'ws://webchat.local:3000/server.php';
    </script>
    <title><?php echo $title; ?></title>
</head>

<body>
    <main>
        <div class="content">
            <?php include_once APP_DIR . '/views/' . $content_view; ?>
        </div>
        <?php include_once APP_DIR . '/views/layout/side-menu.php' ?>
    </main>
    <?php include_once APP_DIR . '/views/layout/modalCreateGroup.php' ?>
    <script src="/app/assets/js/modal.js"></script>
    <script src="/app/assets/js/searchContacts.js"></script>
    <script src="/app/assets/js/createGroup.js"></script>
    <?php if (isset($_SESSION['user'])) : ?>
        <script>
            let curUserId = <?php echo json_encode($_SESSION['user']['id']); ?>;
        </script>
        <script src="/app/assets/js/wsNotification.js"></script>
    <?php endif; ?>
</body>

</html>