<!DOCTYPE html>
<html lang="cs">
<head>
    <base href="/">
    <meta charset="UTF-8">
    <title><?=$title ?></title>
    <meta name="description" content="<?= $description ?>" />
</head>
<body>

    <div class="container">
        <?php
            $this->controller->loadView();
        ?>
    </div>
</body>
</html>