<!DOCTYPE html>
<html lang="cs">
<head>
    <base href="<?= REDIRECT_PATH ?>">
    <meta charset="UTF-8">
    <title><?=$title ?></title>
    <meta name="description" content="<?= $description ?>" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="public/tailwind.css">
    <link href="https://unpkg.com/tailwindcss@1.9.0/dist/tailwind.min.css" rel="stylesheet">
    <link rel="icon" href="public/logo.svg">
    <link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css"
          rel="stylesheet" type="text/css" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <script src="public/js/main.js"></script>
</head>
<body class="text-white  min-h-screen">

<a class="inline-flex z-50 items-center h-10 px-5 text-indigo-100 transition-colors duration-150 bg-red-500 rounded-lg focus:shadow-outline hover:bg-red-800 focus:ring-4 focus:ring-yellow-300 fixed m-2 right-0 bottom-0" href="<?=REDIRECT_PATH?>help">
    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-question-square-fill mr-2" viewBox="0 0 16 16">
        <path d="M2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H2zm3.496 6.033a.237.237 0 0 1-.24-.247C5.35 4.091 6.737 3.5 8.005 3.5c1.396 0 2.672.73 2.672 2.24 0 1.08-.635 1.594-1.244 2.057-.737.559-1.01.768-1.01 1.486v.105a.25.25 0 0 1-.25.25h-.81a.25.25 0 0 1-.25-.246l-.004-.217c-.038-.927.495-1.498 1.168-1.987.59-.444.965-.736.965-1.371 0-.825-.628-1.168-1.314-1.168-.803 0-1.253.478-1.342 1.134-.018.137-.128.25-.266.25h-.825zm2.325 6.443c-.584 0-1.009-.394-1.009-.927 0-.552.425-.94 1.01-.94.609 0 1.028.388 1.028.94 0 .533-.42.927-1.029.927z"/>
    </svg> <span><b> Nápověda</b></span>
</a>
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
        <img class=" h-6" src="public/logo.png" alt="logo">
        <p class="text-xs">AN-MA SOFT. All Rights Reserved.</p>
    </footer>
</body>

</html>