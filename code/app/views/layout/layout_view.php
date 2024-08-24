<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?></title>
</head>

<body>
    <p>header</p>
    <hr>
    <?php include_once APP_DIR . '/views/' . $content_view; ?>
    <hr>
    <p>footer</p>
</body>

</html>
