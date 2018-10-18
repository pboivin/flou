# flou


<p align='center'>
<img src='./demos/img/basic-demo.gif' width="500" alt='Animation of the provided basic demo'>
</p>


An implementation of the Medium-style progressive image loading technique.

This package provides a simple wrapper around PHP's Imagick class to generate
scaled-down, blurry versions of your images. These compressed images are used
as temporary placeholders while the browser is loading the original images.

A thin front-end layer is provided: a simple ES5/UMD module and a few lines of
CSS. It can be used as-is to quickly add the effect to a website, or as a
starting point to integrate with external lazy-loading libraries.


### Requirements

- PHP >= 7.0
- `imagick` PHP extension (ImageMagick)
- Composer


### Getting started

#### Installing from the Github repository

Add the following to your `composer.json`:

```
{
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/pboi20/flou"
        }
    ],
    "require": {
        "pboi20/flou": "dev-master"
    }
}

```

Then, run `composer install`


#### Simple usage

```php
use Flou\Image;

$image1 = (new Image())
    ->load("/path/to/project/static/images/image1.jpg")
    ->process();
```

This generates the placeholder image at `/path/to/project/static/images/image1.flou.jpg`
when the page is loaded for the first time. Any subsequent page load will not
attempt to regenerate the placeholder; the existing image will be used instead.

Have a look at the `demos/` folder for complete examples.


#### Running the demos

Clone the repository and run PHP's built-in web server:

```
git clone https://github.com/pboi20/flou.git
cd flou
composer dumpautoload
php -S 0.0.0.0:8080 -t demos
```

Then, visit `http://localhost:8080`.


### Running the tests

```
composer install
./vendor/bin/phpunit -v tests
```


### Disclaimer

This is a work in progress :)

[MIT License](https://github.com/pboi20/flou/blob/master/LICENSE)
