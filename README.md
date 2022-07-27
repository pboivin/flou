# flou

<p>
<a href="https://github.com/pboivin/flou/actions"><img src="https://github.com/pboivin/flou/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/pboivin/flou"><img src="https://img.shields.io/packagist/v/pboivin/flou" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/pboivin/flou"><img src="https://img.shields.io/packagist/l/pboivin/flou" alt="License"></a>
</p>


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
- [Development](#development)
- [License](#license)


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
$image = $flou->image('01.jpg', [
    'w' => 10,
    'h' => 10,
    'fit' => 'crop',
]);
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
echo $image->source()->ratio();     # 1.77777778

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

- **`useAspectRatio()`:** Prevent content shifting when the image is loaded:

    ```php
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
      style="aspect-ratio: 1.77777778; object-fit: cover; object-position: center;" 
      ...
    >
    ```

- **`usePaddingTop()`:** A workaround for older browsers not supporting `aspect-ratio`:

    ```php
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
        style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; 
               object-fit: cover; object-position: center;"
        ...
      >
    </div>
    ```

- **`useWrapper()`:** Wraps the `img` element with an extra `div` and separates the LQIP element form the main image element. This can be used to add a fade-in effect when the image is loaded.

    (Requires extra JS and CSS. [See fade-in example.](#fade-in-image-on-load))

    ```php
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
        ...
      >
      <img 
        class="lazyload-lqip" 
        src="/images/cache/01.jpg/de828e8798017be816f79e131e41dcc9.jpg"
      >
    </div>
    ```

#### Noscript variation

Use the `noScript()` method of the `ImageRender` object to generate an `img` element without any lazy loading behavior:

```php
echo $image
        ->render()
        ->noScript(['class' => 'w-full', 'alt' => 'Lorem ipsum']);
```

Output:

```html
<img 
  class="lazyload-noscript w-full" 
  alt="Lorem ipsum" 
  src="/images/source/01.jpg" 
  width="2932" 
  height="2000"
>
```

This can be used to implement a `noscript` image fallback. It can also be used to create customized variations of the source image with CSS classes and HTML attributes:

- [Noscript fallback example](#noscript-fallback)
- [Native lazy loading example](#native-lazy-loading-no-js-fallback)


## Working with Image Sets (Responsive Images)


#### Single source

Use the `imageSet()` method to transform a source image into a set of responsive images. Then, use the `render()` method of the `ImageSet` to render a lazy-loaded `img` element with the `sizes` and `srcset` attributes:

```php
$imageSet = $flou->imageSet([
    'image' => '01.jpg',
    'sizes' => '(max-width: 500px) 100vw, 50vw',
    'sources' => [
        ['width' => '500'],
        ['width' => '900'],
        ['width' => '1300'],
        ['width' => '1700'],
    ],
]);

echo $imageSet
        ->render()
        ->useAspectRatio()
        ->img(['class' => 'w-full', 'alt' => 'Lorem ipsum']);
```

Output:

```html
<img 
  class="lazyload w-full" 
  alt="Lorem ipsum" 
  style="aspect-ratio: 1.77777778; object-fit: cover; object-position: center;" 
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
- `noScript()`


#### Multiple sources (art-directed `picture` element)

Use the `picture()` method of the `ImageSetRender` object to generate a `picture` element with multiple sources and media queries:

```php
$imageSet = $flou->imageSet([
    'sources' => [
        [
            'image' => '01.jpg',
            'width' => '900',
            'media' => '(max-width: 900px)',
        ],
        [
            'image' => '02.jpg',
            'width' => '1300',
            'media' => '(min-width: 901px)',
        ],
    ],
]);

echo $imageSet
        ->render()
        ->picture(['class' => 'w-full', 'alt' => 'Lorem ipsum']);
```

Output:

```html
<picture>
  <source 
    media="(max-width: 900px)" 
    data-srcset="/images/cache/01.jpg/1422c06dea2257858f6437b9675fba1c.jpg"
  >
  <source 
    media="(min-width: 901px)" 
    data-srcset="/images/cache/02.jpg/1e147b93856eef676f00989ba28365f1.jpg"
  >
  <img 
    class="lazyload my-image" 
    alt="Lorem ipsum" 
    src="/images/cache/02.jpg/23a733056cc32e360e9cdef3e0be8fb4.jpg" 
    data-src="/images/cache/02.jpg/1e147b93856eef676f00989ba28365f1.jpg" 
    width="3000" 
    height="2000"
  >
</picture>
```

See also: [Art-directed `picture` element example](#art-directed-picture-element-manual)


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

<hr>

#### Art-directed `picture` element (manual)

*Usage:*

```php
<?php
    $image1 = $flou->image('01.jpg', ['w' => 800]);
    $image2 = $flou->image('02.jpg', ['w' => 1200]);
    $lqip = $flou->image('02.jpg');
?>

<picture>
    <source
        media="(max-width: 800px)"
        data-srcset="<?= $image1->cached()->url() ?>"
    />
    <img
        class="lazyload"
        alt="Lorem ipsum"
        width="<?= $image2->source()->width() ?>"
        height="<?= $image2->source()->height() ?>"
        data-src="<?= $image2->source()->url() ?>"
        src="<?= $lqip->cached()->url() ?>"
    />
</picture>
```

<hr>

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
<div>
    <?= ($image = $flou->image('01.jpg'))
            ->render()
            ->img(['class' => 'w-full', 'alt' => 'Lorem ipsum'])
    ?>
    <noscript>
        <?= $image
                ->render()
                ->noScript(['class' => 'w-full', 'alt' => 'Lorem ipsum'])
        ?>
    </noscript>
</div>
```

<hr>

#### Native lazy loading (no JS)

*Usage:*

```php
echo ($image = $flou->image('01.jpg'))
        ->render()
        ->useAspectRatio()
        ->noScript([
            'class' => 'w-full', 
            'alt' => 'Lorem ipsum',
            'loading' => 'lazy',
            'decoding' => 'async',
            'style' => "background-image: url({$image->cached()->url()});
                        background-size: cover;"
        ]);
```

<hr>

#### Laravel integration

*`config/flou.php`:*

```php
<?php

return [
    'sourcePath' => base_path('public/images/source'),
    'cachePath' => base_path('public/images/cache'),
    'sourceUrlBase' => '/images/source',
    'cacheUrlBase' => '/images/cache',
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
    protected static function getFacadeAccessor() 
    {
        return 'flou';
    }
}
```

*Usage in views:*

```blade
@php use App\Facades\Flou; @endphp

{!! Flou::image('01.jpg')
        ->render()
        ->img(['class' => 'w-full', 'alt' => 'Lorem ipsum']);
!!}
```


## Development

#### Test suite ([phpunit](https://phpunit.de/))

```
composer run test
```

#### Static analysis ([phpstan](https://phpstan.org/))

```
composer run analyse
```

#### Code formatting ([pint](https://laravel.com/docs/9.x/pint))

```
composer run test
```


## License

Flou is open-sourced software licensed under the [MIT license](LICENSE.md).
