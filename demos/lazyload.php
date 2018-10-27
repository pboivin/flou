<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . "/../vendor/autoload.php";

use Flou\Image;

function show_image($filename)
{
    // Load and process the image
    $image = (new Image())
        ->setBasePath(__DIR__ . "/img")
        ->setBaseURL("/img")
        ->load($filename)
        ->process();

    // Return the HTML snippet as needed by LazyLoad
    return <<<EOT
        <div class="lazy-container">
            <img
                class="lazy"
                src="{$image->getProcessedURL()}"
                data-src="{$image->getOriginalURL()}"
                width="{$image->getOriginalWidth()}"
                height="{$image->getOriginalHeight()}"
            >
        </div>
EOT;
}

?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Integration with LazyLoad</title>

    <link rel="stylesheet" href="css/common.css">

    <!-- @see https://github.com/verlok/lazyload -->
    <script src="https://cdn.jsdelivr.net/npm/vanilla-lazyload@10.19.0/dist/lazyload.min.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function(){
            new LazyLoad({
                elements_selector: ".lazy"
            });
        });
    </script>
</head>
<body>
    <div class="demo-container">
        <h1>Integration with LazyLoad</h1>

        <p>Scroll down to see LazyLoad in action.</p>

        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Mauris quis sodales enim. Fusce quis dictum mauris. Curabitur facilisis tincidunt mollis. Morbi vehicula mauris massa, vel tincidunt lectus commodo malesuada. Sed nec tortor blandit arcu facilisis posuere. Fusce viverra consectetur mi, condimentum semper urna ullamcorper eget. Donec iaculis ac nunc id accumsan. Quisque lectus augue, feugiat quis mi vel, viverra eleifend enim. Curabitur sit amet urna arcu.</p>

        <?= show_image("image1.jpg") ?>

        <p>Proin facilisis orci et sem volutpat, in rhoncus odio facilisis. Maecenas elit ante, accumsan quis malesuada vel, tristique sit amet dolor. In posuere erat a dolor congue vehicula. Nam auctor porttitor dolor, id eleifend mi. Cras sed felis nec purus tincidunt blandit sit amet in nisi. Ut mollis ante nec odio pellentesque bibendum. Pellentesque id commodo erat, id commodo odio. Quisque rutrum metus in ipsum tristique mollis sed in nibh. Phasellus vel purus eget magna fermentum vulputate.</p>
        <p>Quisque quis velit non leo consequat hendrerit sit amet a orci. Cras ut ligula quam. Curabitur sollicitudin dui non mi luctus, ac consequat orci venenatis. In hac habitasse platea dictumst. Suspendisse eleifend condimentum erat in fringilla. Nam a elit ut odio varius consectetur. Donec eleifend libero ipsum, quis vestibulum felis consequat non. Maecenas a imperdiet lacus, in feugiat dui. Pellentesque orci elit, pharetra in tellus et, rhoncus dignissim nisl. Nunc lacinia eros ut urna pretium maximus.</p>
        <p>Sed venenatis elementum augue, lobortis maximus neque cursus vel. Morbi est massa, hendrerit in urna eget, pellentesque mollis nisl. Vivamus aliquet velit egestas dui lobortis, vitae cursus lacus bibendum. Vestibulum et enim scelerisque, tristique lectus vitae, fringilla arcu. Nullam non eros non diam luctus tempus sit amet in urna. Phasellus euismod orci bibendum est hendrerit pulvinar. Maecenas quis varius urna, quis semper turpis. Suspendisse sapien mauris, consequat non suscipit a, cursus eu augue. Nam ut venenatis felis.</p>
        <p>Nunc ut finibus erat. Praesent pellentesque arcu rhoncus turpis venenatis, et finibus purus vehicula. Nulla eget suscipit quam, sit amet auctor ipsum. Donec commodo sollicitudin ullamcorper. Sed scelerisque laoreet tellus et imperdiet. Ut feugiat nisi eu neque volutpat, vel auctor metus scelerisque. Vivamus eu arcu at diam porta lobortis. Donec scelerisque tempus tempus. Quisque quis lorem eget erat mattis molestie. Donec mattis pretium eros a ultricies. Donec velit mauris, volutpat non erat sit amet, mollis vehicula velit.</p>

        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Mauris quis sodales enim. Fusce quis dictum mauris. Curabitur facilisis tincidunt mollis. Morbi vehicula mauris massa, vel tincidunt lectus commodo malesuada. Sed nec tortor blandit arcu facilisis posuere. Fusce viverra consectetur mi, condimentum semper urna ullamcorper eget. Donec iaculis ac nunc id accumsan. Quisque lectus augue, feugiat quis mi vel, viverra eleifend enim. Curabitur sit amet urna arcu.</p>

        <?= show_image("image2.jpg") ?>

        <p>Proin facilisis orci et sem volutpat, in rhoncus odio facilisis. Maecenas elit ante, accumsan quis malesuada vel, tristique sit amet dolor. In posuere erat a dolor congue vehicula. Nam auctor porttitor dolor, id eleifend mi. Cras sed felis nec purus tincidunt blandit sit amet in nisi. Ut mollis ante nec odio pellentesque bibendum. Pellentesque id commodo erat, id commodo odio. Quisque rutrum metus in ipsum tristique mollis sed in nibh. Phasellus vel purus eget magna fermentum vulputate.</p>
        <p>Quisque quis velit non leo consequat hendrerit sit amet a orci. Cras ut ligula quam. Curabitur sollicitudin dui non mi luctus, ac consequat orci venenatis. In hac habitasse platea dictumst. Suspendisse eleifend condimentum erat in fringilla. Nam a elit ut odio varius consectetur. Donec eleifend libero ipsum, quis vestibulum felis consequat non. Maecenas a imperdiet lacus, in feugiat dui. Pellentesque orci elit, pharetra in tellus et, rhoncus dignissim nisl. Nunc lacinia eros ut urna pretium maximus.</p>
        <p>Sed venenatis elementum augue, lobortis maximus neque cursus vel. Morbi est massa, hendrerit in urna eget, pellentesque mollis nisl. Vivamus aliquet velit egestas dui lobortis, vitae cursus lacus bibendum. Vestibulum et enim scelerisque, tristique lectus vitae, fringilla arcu. Nullam non eros non diam luctus tempus sit amet in urna. Phasellus euismod orci bibendum est hendrerit pulvinar. Maecenas quis varius urna, quis semper turpis. Suspendisse sapien mauris, consequat non suscipit a, cursus eu augue. Nam ut venenatis felis.</p>
        <p>Nunc ut finibus erat. Praesent pellentesque arcu rhoncus turpis venenatis, et finibus purus vehicula. Nulla eget suscipit quam, sit amet auctor ipsum. Donec commodo sollicitudin ullamcorper. Sed scelerisque laoreet tellus et imperdiet. Ut feugiat nisi eu neque volutpat, vel auctor metus scelerisque. Vivamus eu arcu at diam porta lobortis. Donec scelerisque tempus tempus. Quisque quis lorem eget erat mattis molestie. Donec mattis pretium eros a ultricies. Donec velit mauris, volutpat non erat sit amet, mollis vehicula velit.</p>

        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Mauris quis sodales enim. Fusce quis dictum mauris. Curabitur facilisis tincidunt mollis. Morbi vehicula mauris massa, vel tincidunt lectus commodo malesuada. Sed nec tortor blandit arcu facilisis posuere. Fusce viverra consectetur mi, condimentum semper urna ullamcorper eget. Donec iaculis ac nunc id accumsan. Quisque lectus augue, feugiat quis mi vel, viverra eleifend enim. Curabitur sit amet urna arcu.</p>

        <?= show_image("image3.jpg") ?>

        <p>Proin facilisis orci et sem volutpat, in rhoncus odio facilisis. Maecenas elit ante, accumsan quis malesuada vel, tristique sit amet dolor. In posuere erat a dolor congue vehicula. Nam auctor porttitor dolor, id eleifend mi. Cras sed felis nec purus tincidunt blandit sit amet in nisi. Ut mollis ante nec odio pellentesque bibendum. Pellentesque id commodo erat, id commodo odio. Quisque rutrum metus in ipsum tristique mollis sed in nibh. Phasellus vel purus eget magna fermentum vulputate.</p>
        <p>Quisque quis velit non leo consequat hendrerit sit amet a orci. Cras ut ligula quam. Curabitur sollicitudin dui non mi luctus, ac consequat orci venenatis. In hac habitasse platea dictumst. Suspendisse eleifend condimentum erat in fringilla. Nam a elit ut odio varius consectetur. Donec eleifend libero ipsum, quis vestibulum felis consequat non. Maecenas a imperdiet lacus, in feugiat dui. Pellentesque orci elit, pharetra in tellus et, rhoncus dignissim nisl. Nunc lacinia eros ut urna pretium maximus.</p>
        <p>Sed venenatis elementum augue, lobortis maximus neque cursus vel. Morbi est massa, hendrerit in urna eget, pellentesque mollis nisl. Vivamus aliquet velit egestas dui lobortis, vitae cursus lacus bibendum. Vestibulum et enim scelerisque, tristique lectus vitae, fringilla arcu. Nullam non eros non diam luctus tempus sit amet in urna. Phasellus euismod orci bibendum est hendrerit pulvinar. Maecenas quis varius urna, quis semper turpis. Suspendisse sapien mauris, consequat non suscipit a, cursus eu augue. Nam ut venenatis felis.</p>
        <p>Nunc ut finibus erat. Praesent pellentesque arcu rhoncus turpis venenatis, et finibus purus vehicula. Nulla eget suscipit quam, sit amet auctor ipsum. Donec commodo sollicitudin ullamcorper. Sed scelerisque laoreet tellus et imperdiet. Ut feugiat nisi eu neque volutpat, vel auctor metus scelerisque. Vivamus eu arcu at diam porta lobortis. Donec scelerisque tempus tempus. Quisque quis lorem eget erat mattis molestie. Donec mattis pretium eros a ultricies. Donec velit mauris, volutpat non erat sit amet, mollis vehicula velit.</p>

    </div>
</body>
</html>
