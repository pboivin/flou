# flou

<p align='center'>
<img src='./demos/img/basic-demo.gif' width="500" alt='Animation of the provided basic demo'>
</p>


An implementation of the Medium-style progressive image loading technique.

This package provides a simple wrapper around the [Imagine library](https://github.com/avalanche123/Imagine)
to generate scaled-down, blurry versions of your images. These compressed images are used
as temporary placeholders while the browser is loading the original images.

A thin front-end layer is provided: a simple ES5/UMD module and a few lines of
CSS. It can be used as-is to quickly add the effect to a website, or as a
starting point to integrate with external lazy-loading libraries.


### Requirements

- PHP >= 7.0
- One of: GD2, Imagick (>= 6.2.9) or Gmagick PHP extensions
- Composer


### Getting started

#### Installing via Composer

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


#### Integrating to your project

[`demos/basic.php`](https://github.com/pboi20/flou/tree/master/demos/basic.php)
is an example of a simple integration using the built-in front-end helper.

[`demos/lazyload.php`](https://github.com/pboi20/flou/tree/master/demos/lazyload.php)
is an example of how to extend the `DefaultImageRenderer` class to integrate with
the [LazyLoad](https://github.com/verlok/lazyload) JavaScript library.

[`demos/processor.php`](https://github.com/pboi20/flou/tree/master/demos/processor.php)
is an example of how to extend the `DefaultImageProcessor` class to add custom image
processing steps.


### Development

#### Cloning the repository and setting up

```
git clone https://github.com/pboi20/flou.git
cd flou
composer install
```


#### Running the demos

```
php -S 0.0.0.0:8080 -t demos
```

Then, visit `http://localhost:8080`.


#### Running the tests

```
./vendor/bin/phpunit -v tests
```


#### Running PHP-CS-Fixer (PHP Coding Standards Fixer)

```
# Dry run (see what needs to be changed)
./vendor/bin/php-cs-fixer -vvv fix --dry-run --diff

# Fix
./vendor/bin/php-cs-fixer fix

```


### Disclaimer

This is a work in progress :)

[MIT License](https://github.com/pboi20/flou/blob/master/LICENSE)
