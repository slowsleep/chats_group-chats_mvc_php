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
    <?php include_once APP_DIR . '/views/layout/popup.php' ?>
    <script src="/app/assets/js/createGroup.js"></script>
</body>

</html>
