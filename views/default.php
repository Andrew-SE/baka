<!DOCTYPE html>
<html lang="cs">
<head>
    <base href="http://baka.team/bakateam/">
    <meta charset="UTF-8">
    <title><?=$title ?></title>
    <meta name="description" content="<?= $description ?>" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="/bakateam/public/tailwind.css">
    <link rel="stylesheet" href="/bakateam/public/style.css">
    <link rel="icon" href="/bakateam/public/logo.svg">

</head>
<body class="text-white  min-h-screen">

    <div class="flex flex-col justify-center  items-center  container md:w-full max-w-4xl mx-auto min-h-95vh  bg-blue-bgop">
        <div>
            <h1 class="text-orange font-semibold text-4xl mb-6 lg:mb-12">BAKATEAM</h1>
        </div>
        <div class="text-center w-full">
            <?php
                $this->controller->loadView();
            ?>
        </div>

    </div>
    <footer class="bg-gray-footer text-white p-2  min-h-5vh flex flex-col sm:flex-row text-sm justify-center items-center">
        <p class="text-xs">Copyright © <?php echo date("Y"); ?></p>
        <img class=" h-6" src="/bakateam/public/logo.svg" alt="logo">
        <p class="text-xs">AN-MA SOFT. All Rights Reserved.</p>
    </footer>
</body>
</html>