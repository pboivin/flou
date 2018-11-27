<?php

require_once "_initialize.php";

use Flou\Image;
use Flou\DefaultImageProcessor;

/**
 * Custom image processor to add pink colorization
 *
 * @see https://imagine.readthedocs.io/en/stable/usage/effects.html
 */
class PinkImageProcessor extends DefaultImageProcessor
{
    protected $resize_width = 100;

    public function process($save=false)
    {
        parent::process(false);
        $imagine_image = $this->getImagineImage();
        $pink = $imagine_image->palette()->color('#FF00D0');
        $imagine_image->effects()->colorize($pink);
        $this->save();
    }
}

/**
 * Load and process the image using the custom processor
 */
$image1 = (new Image())
    ->setImageProcessor(new PinkImageProcessor())
    ->setBasePath(__DIR__ . "/img")
    ->setBaseURL("/img")
    ->setProcessedFile("image1.pink.jpg")
    ->load("image1.jpg")
    ->process();

?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Custom image processing</title>

    <link rel="stylesheet" href="css/common.css">

    <!--
        The assets are inlined here for the sake of simplicity. You can manually
        copy both `flou.css` and `flou.js` into your project if you wish to
        include them in your CSS and JavaScript bundles.
    -->

    <style>
        .flou-container {
            overflow: hidden;
        }
        .flou-image {
            max-width: 100%;
            height: auto;
            filter: blur(5px);
        }
        .flou-image-loaded {
            filter: none;
        }
    </style>

    <script>
        <?= file_get_contents(__DIR__ . "/../assets/flou.js") ?>

        document.addEventListener("DOMContentLoaded", Flou.loadAllImages);
    </script>
</head>
<body>
    <div class="demo-container">
        <h1>Custom image processing</h1>

        <!-- Return the HTML snippet as needed by `flou.js` -->
        <?= $image1->render() ?>
    </div>
</body>
</html>
