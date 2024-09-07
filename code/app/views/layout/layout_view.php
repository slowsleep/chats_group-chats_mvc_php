<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/app/assets/css/style.css">
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
