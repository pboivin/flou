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
    <title>Custom Assets Demo</title>
    <link rel="stylesheet" href="/common.css">
    <link rel="stylesheet" href="demo.css">
    <script src="demo.js"></script>
</head>
<body>
    <div class="container">
        <h1>Custom Assets Demo</h1>
        <div class="flou-container">
            <img
                class="flou-processed-image"
                src="<?= $image1->getProcessedURL() ?>"
                alt=""
            />
            <img
                class="flou-original-image"
                src="<?= $image1->getOriginalURL() ?>"
                alt="My Vacation Photo"
                onload="handleFlouImageLoaded(this)"
            />
        </div>
    </div>
</body>
</html>
