<!DOCTYPE html>
<html lang="cs">
<head>
    <base href="/">
    <meta charset="UTF-8">
    <title><?=$title ?></title>
    <meta name="description" content="<?= $description ?>" />
    <link rel="stylesheet" href="../public/style.css">
    <link rel="icon" href="../public/logo.svg">

</head>
<body>

    <div class="container">
        <?php
            $this->controller->loadView();
        ?>
    </div>
    <footer>

        <p>Copyright © 2021 <img src="../public/logo.svg" alt="logo"> AN-MA SOFT. All Rights Reserved.</p>
    </footer>
</body>
</html>