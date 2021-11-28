<!DOCTYPE html>
<html lang="cs">
<head>
    <base href="/">
    <meta charset="UTF-8">
    <title><?=$title ?></title>
    <meta name="description" content="<?= $description ?>" />
    <link rel="stylesheet" href="../public/style.css">
    <link href="https://unpkg.com/tailwindcss@^2/dist/tailwind.min.css" rel="stylesheet">


    <link rel="icon" href="../public/logo.svg">

</head>
<body>

    <div class="container  bg-opacity-80">
        <?php
            $this->controller->loadView();
        ?>
    </div>
    <footer class=" ">
        <p>Copyright © 2021 <img src="../public/logo.svg" alt="logo"> AN-MA SOFT. All Rights Reserved.</p>
    </footer>
</body>
</html>