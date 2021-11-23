<!DOCTYPE html>
<html lang="cs">
<head>
    <base href="/">
    <meta charset="UTF-8">
    <title><?=$title ?></title>
    <meta name="description" content="<?= $description ?>" />
    <meta name="keywords" content="<?php //echo $key_words; ?>" />
</head>
<body>
    <?php require_once('inc/nav.php');?>
    <div class="container">
        <?php
            $this->controller->loadView();
        ?>
    </div>
</body>
</html>