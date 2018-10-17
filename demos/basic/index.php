<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . "/../../vendor/autoload.php";

use Flou\Image;

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
        <?= $image1->getHTML() ?>
    </div>
</body>
</html>
