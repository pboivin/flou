<?php

require_once "_initialize.php";

use Flou\Image;

// Load and process the image
$image1 = (new Image())
    ->setBasePath(__DIR__ . "/img")
    ->setBaseURL("/img")
    ->load("image1.jpg")
    ->process();

?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Basic Demo</title>

    <link rel="stylesheet" href="css/common.css">

    <!--
        The assets are inlined here for the sake of simplicity. You can manually
        copy both `flou.css` and `flou.js` into your project if you wish to
        include them in your CSS and JavaScript bundles.
    -->

    <style>
        <?= file_get_contents(__DIR__ . "/../assets/flou.css") ?>
    </style>

    <script>
        <?= file_get_contents(__DIR__ . "/../assets/flou.js") ?>

        document.addEventListener("DOMContentLoaded", Flou.loadAllImages);
    </script>
</head>
<body>
    <div class="demo-container">
        <h1>Basic Demo</h1>

        <!-- Return the HTML snippet as needed by `flou.js` -->
        <?= $image1->render() ?>
    </div>
</body>
</html>
