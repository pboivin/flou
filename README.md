# flou

[![phpunit](https://github.com/pboivin/flou/actions/workflows/phpunit.yml/badge.svg)](https://github.com/pboivin/flou/actions/workflows/phpunit.yml)

(This is a draft.)

Flou is a PHP package integrating the [Glide (PHP)](https://github.com/thephpleague/glide) and the [vanilla-lazyload (JS)](https://github.com/verlok/vanilla-lazyload) libraries. It is optimized to quickly implement image lazy loading on prototypes and static sites, using a local folder of source images.

**Features:**

- Transforms images on initial page load — does not expose Glide URLs
- Useable with static site generators and in CLI scripts
- Framework agnostic — a set of plain PHP classes

**Requirements:**

- PHP >= 8.0

**Table of contents:**

- [Installing](#installing)
- [Getting Started](#getting-started)
- [Working with Single Images](#working-with-single-images)
- [Working with Image Sets (Responsive Images)](#working-with-image-sets-responsive-images)
- [Examples](#examples)


## Installing 

The package can be installed via Composer:

```
composer require pboivin/flou
```

This will also install Glide as a Composer dependency.

You can pull in the vanilla-lazyload library via a CDN:

```html
<script src="https://cdn.jsdelivr.net/npm/vanilla-lazyload@17.8.3/dist/lazyload.min.js"></script>
```

or via NPM:

```
npm install --save vanilla-lazyload
```

Consult the [vanilla-lazyload documentation](https://github.com/verlok/vanilla-lazyload#-getting-started---script) for more installation options.


## Getting Started

First, initialize the `LazyLoad` JS object. This can be done by adding the following script in your page template:

```html
<script>
    document.addEventListener("DOMContentLoaded", () => {
        new LazyLoad({
            elements_selector: ".lazyload",
        });
    });
</script>
```

Then, setup the `ImageFactory` PHP object with your project-specific configuration:

```php
use Pboivin\Flou\ImageFactory;

$flou = new ImageFactory([
    'sourcePath' => '/home/user/my-site.com/public/images/source',
    'cachePath' => '/home/user/my-site.com/public/images/cache',
    'sourceUrlBase' => '/images/source',
    'cacheUrlBase' => '/images/cache',
]);
```

The options are:

- `sourcePath`: The full path to your source images.
- `cachePath`: The full path where Glide will store the image transformations.
- `sourceUrlBase`: The base URL for the source images.
- `cacheUrlBase`:  The base URL for the transformed images.

If you're using a framework like Laravel, you can register the `$flou` instance as a singleton for your entire application. This will be your entry point to transform and render images. ([See Laravel Integration example.](#laravel-integration))


## Working with Single Images


#### Transforming source images

To load a source image and generate a low-quality image placeholder (LQIP):

```php
$image = $flou->image('01.jpg');
```

You can also provide custom Glide parameters for your image transformation:

```php
$image = $flou->image('01.jpg', ['w' => 10, 'h' => 4]);
```

You'll find all available parameters in the [Glide documentation](https://glide.thephpleague.com/2.0/api/quick-reference/).

As you can see, the default parameters are used to generate LQIP from source images, but you are not restricted to that. You can generate as many transformation as you need from the same image:

```php
$phone = $flou->image('01.jpg', ['w' => 500]);
$tablet = $flou->image('01.jpg', ['w' => 900]);
$desktop = $flou->image('01.jpg', ['w' => 1300]);
```

If you're interested in responsive images with `srcset`, have a look at the next section ([Working with Image Sets](#working-with-image-sets-responsive-images)).

The `image()` method will return an `Image` object, from which you can conveniently access the source image file and the transformed (cached) image file:

```php 
$image = $flou->image('01.jpg');

# Source image data:
echo $image->source()->url();       # /images/source/01.jpg
echo $image->source()->path();      # /home/user/my-site.com/public/images/source/01.jpg
echo $image->source()->width();     # 3840 
echo $image->source()->height();    # 2160
echo $image->source()->ratio();     # 1.7777777777777777

# Transformed image data:
echo $image->cached()->url();       # /images/cache/01.jpg/de828e8798017be816f79e131e41dcc9.jpg

...
```


#### Rendering single images

The `render()` method on your image will return an `ImageRender` object, which can generate HTML suitable for the vanilla-lazyload library. Here's a basic example rendering an `img` element:

```php
$image = $flou->image('01.jpg');

echo $image
        ->render()
        ->img(['class' => 'w-full', 'alt' => 'Lorem ipsum']);
```

Output:

```html
<img 
  class="lazyload w-full" 
  alt="Lorem ipsum" 
  src="/images/cache/01.jpg/de828e8798017be816f79e131e41dcc9.jpg" 
  data-src="/images/source/01.jpg" 
  width="3840" 
  height="2160"
>
```

Options passed into the `img()` method will be included as HTML attributes on the `img` element.

The `ImageRender` object can be configured in a few ways to optimize the generated HTML:

- `useAspectRatio()`: Prevent content shifting when the image is loaded:

    ```php
    $image = $flou->image('01.jpg');

    echo $image
            ->render()
            ->useAspectRatio()
            ->img(['class' => 'w-full', 'alt' => 'Lorem ipsum']);

    # or use a custom aspect-ratio:

    echo $image
            ->render()
            ->useAspectRatio(16 / 9)
            ->img(['class' => 'w-full', 'alt' => 'Lorem ipsum']);
    ```

    Output:

    ```html
    <img 
      class="lazyload w-full" 
      alt="Lorem ipsum" 
      style="aspect-ratio: 1.7777777777777777" 
      ...
    >
    ```

- `usePaddingTop()`: A workaround for older browsers not supporting `aspect-ratio`:

    ```php
    $image = $flou->image('01.jpg');

    echo $image
            ->render()
            ->usePaddingTop()
            ->img(['class' => 'w-full', 'alt' => 'Lorem ipsum']);

    # or use a custom aspect-ratio:

    echo $image
            ->render()
            ->usePaddingTop(16 / 9)
            ->img(['class' => 'w-full', 'alt' => 'Lorem ipsum']);
    ```

    Output:

    ```html
    <div class="lazyload-padding" style="position: relative; padding-top: 56.25%;">
      <img
        class="lazyload w-full"
        alt="Lorem ipsum" 
        style="position: absolute; top: 0; height:0; width: 100%; height: 100%; 
               object-fit: cover; object-position: center;"
        ...
      >
    </div>
    ```

- `useWrapper()`: Wraps the `img` element with an extra `div`. This can be used to add a fade-in effect when the image is loaded. (Requires extra JS and CSS. [See fade-in example.](#fade-in-image-on-load))

    ```php
    $image = $flou->image('01.jpg');

    echo $image
            ->render()
            ->useWrapper()
            ->img(['class' => 'w-full', 'alt' => 'Lorem ipsum']);
    ```

    Output:

    ```html
    <div class="lazyload-wrapper">
      <img 
        class="lazyload w-full" 
        alt="Lorem ipsum" 
        style="aspect-ratio: 1.7777777777778;" 
        ...
      >
      <img 
        class="lazyload-lqip" 
        src="/images/cache/01.jpg/de828e8798017be816f79e131e41dcc9.jpg"
      >
    </div>
    ```


## Working with Image Sets (Responsive Images)

Use the `imageSet()` method to transform a source image into a set of responsive images. Then, use the `render()` method of the `ImageSet` to render a lazy-loaded `img` element with the `sizes` and `srcset` attributes:

```php
$set = $flou->imageSet([
    'image' => '01.jpg',
    'sizes' => '(max-width: 500px) 100vw, 50vw',
    'sources' => [
        ['width' => '500'],
        ['width' => '900'],
        ['width' => '1300'],
        ['width' => '1700'],
    ],
]);

echo $set
        ->render()
        ->useAspectRatio()
        ->img(['class' => 'w-full', 'alt' => 'Lorem ipsum']);
```

```html
<img 
  class="lazyload w-full" 
  alt="Lorem ipsum" 
  style="aspect-ratio: 1.7777777777777777;" 
  src="/images/cache/01.jpg/de828e8798017be816f79e131e41dcc9.jpg" 
  data-src="/images/cache/01.jpg/b8648e93b40b56d5c5a78acc7a23e3d9.jpg" 
  data-srcset="/images/cache/01.jpg/a50df0a8c8a84cfc6a77cf74b414d020.jpg 500w, 
               /images/cache/01.jpg/1422c06dea2257858f6437b9675fba1c.jpg 900w, 
               /images/cache/01.jpg/1eac615f1a50f20c434e5944225bdd4f.jpg 1300w, 
               /images/cache/01.jpg/b8648e93b40b56d5c5a78acc7a23e3d9.jpg 1700w" 
  data-sizes="(max-width: 500px) 100vw, 50vw"
  width="3840" 
  height="2160" 
>
```

Just like `Image`, you can optimize `ImageSet` rendering with the same options:

- `useAspectRatio()`
- `usePaddingTop()`
- `useWrapper()`


## Examples


#### Fade-in image on load

*Extra JS and CSS:*

```html
<script src="https://cdn.jsdelivr.net/npm/vanilla-lazyload@17.8.3/dist/lazyload.min.js"></script>
<script>
    /*
     * vanilla-lazyload API reference: 
     * https://github.com/verlok/vanilla-lazyload#options
     */
    document.addEventListener("DOMContentLoaded", function(){
        new LazyLoad({
            elements_selector: ".lazyload",

            callback_loaded: (el) => {
                const wrapper = el.closest(".lazyload-wrapper");
                if (wrapper) {
                    wrapper.classList.add("loaded");
                }
            }
        });
    });
</script>

<style>
    /* Example styles — adjust to taste */

    .lazyload-wrapper {
        position: relative;
        overflow: hidden;
    }

    .lazyload-wrapper .lazyload-lqip {
        filter: blur(10px);
        transform: scale(1.1);
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
    }

    .lazyload-wrapper.loaded .lazyload-lqip {
        opacity: 0;
        transition: opacity 0.5s;
    }
</style>
```

*Usage:*

```php
echo $flou
        ->image('01.jpg')
        ->render()
        ->useAspectRatio()
        ->useWrapper()
        ->img(['class' => 'w-full', 'alt' => 'Lorem ipsum']);
```


#### Art-directed `picture` element




#### Native lazy loading (no js)




#### Noscript fallback

*Extra CSS:*

```html
<noscript>
    <style>
        .lazyload {
            display: none;
        }
    </style>
</noscript>
```

*Usage:*

```php
<?= $image->render()
        ->useAspectRatio()
        ->img(['class' => 'w-full', 'alt' => 'Lorem ipsum'])
?>
<noscript>
    <?= $image->render()
            ->useAspectRatio()
            ->noScript(['class' => 'w-full', 'alt' => 'Lorem ipsum'])
    ?>
</noscript>
```


#### Laravel integration

*`config/flou.php`:*

```php
<?php

return [
    "sourcePath" => base_path('public/images/source'),
    "cachePath" => base_path('public/images/cache'),
    "sourceUrlBase" => '/images/source',
    "cacheUrlBase" => '/images/cache',
];
```

*`app/Providers/AppServiceProvider.php`:*

```php
<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Pboivin\Flou\ImageFactory;

class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton('flou', function ($app) {
            return new ImageFactory(config('flou'));
        });
    }
}
```

*`app/Facades/Flou.php`:*

```php
<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class Flou extends Facade
{
    protected static function getFacadeAccessor() {
        return 'flou';
    }
}
```

*Usage in views:*

```php
use App\Facades\Flou;

$image = Flou::image('01.jpg');

...
```
