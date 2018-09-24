<?php

require_once "../ProgressiveImages.php";
ProgressiveImages::init([
    "blur_path" => "img/blur",
    "blur_url" => "img/blur",
    "base_path" => "img",
    "base_url" => "img",
]);

?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Demo</title>
    <link rel="stylesheet" href="main.css">
</head>
<body>
    <div class="container">
        <h1>Demo</h1>

        <!--
        <div>
            <h2>Original image</h2>
            <?= ProgressiveImages::getOriginal("/img/image1.jpg", "demo-image") ?>
        </div>
        -->

        <div>
            <?= ProgressiveImages::generate("/img/image1.jpg", "demo-image") ?>
        </div>
    </div>
</body>
</html>
