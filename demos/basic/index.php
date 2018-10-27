<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . "/../../vendor/autoload.php";

use Flou\Image;

// Load and process the image
$image1 = (new Image())
    ->setBasePath(__DIR__ . "/../img")
    ->setBaseURL("/img")
    ->load("image1.jpg")
    ->process();

?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Basic Demo</title>

    <link rel="stylesheet" href="/common.css">

    <!--
        The assets are inlined here for the sake simplicity. You can manually
        copy both `flou.css` and `flou.js` into your project if you wish to
        include them in your CSS and JavaScript bundles.
    -->

    <style>
        <?= file_get_contents(__DIR__ . "/../../assets/flou.css") ?>
    </style>

    <script>
        <?= file_get_contents(__DIR__ . "/../../assets/flou.js") ?>

        document.addEventListener("DOMContentLoaded", Flou.loadImages);
    </script>
</head>
<body>
    <div class="demo-container">
        <h1>Basic Demo</h1>

        <!-- Return the HTML snippet as needed by `flou.js` -->
        <?= $image1->getHTML() ?>
    </div>
</body>
</html>
