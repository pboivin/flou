# flou

<p>
<a href="https://github.com/pboivin/flou/actions"><img src="https://github.com/pboivin/flou/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/pboivin/flou"><img src="https://img.shields.io/packagist/v/pboivin/flou" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/pboivin/flou"><img src="https://img.shields.io/packagist/l/pboivin/flou" alt="License"></a>
</p>


Flou is a PHP package integrating the [Glide (PHP)](https://github.com/thephpleague/glide) and the [vanilla-lazyload (JS)](https://github.com/verlok/vanilla-lazyload) libraries. It is optimized to quickly implement image lazy loading on prototypes and static sites, using a local folder of source images.

**Features:**

- Transforms images on initial page load — does not expose Glide URLs
- Generates simple markup for `img` and `picture` HTML elements
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

**Demo project:**

See the [flou-jigsaw-demo](https://github.com/pboivin/flou-jigsaw-demo) repository for an example project integrating Flou with the [Jigsaw](https://github.com/tighten/jigsaw) PHP static site generator.


## Installing 

The package can be installed via Composer:

```
composer require pboivin/flou
```

This also installs Glide as a Composer dependency.

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

First, initialize the `LazyLoad` JS object. Add the following script in your page template:

```html
<script>
    document.addEventListener("DOMContentLoaded", () => {
        new LazyLoad({
            elements_selector: ".lazyload",
        });
    });
</script>
```

Then, initialize the `ImageFactory` PHP object with your project-specific configuration:

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

- `sourcePath`: The full path to the source images.
- `cachePath`: The full path where Glide will store the image transformations.
- `sourceUrlBase`: The base URL for the source images.
- `cacheUrlBase`:  The base URL for the transformed images.

If you're using a framework like Laravel, you can register the `$flou` instance as a singleton for your entire application. This will be your entry point to transform and render images. ([See Laravel Integration example.](#laravel-integration))


#### Extra JS and CSS

Some examples below require additional JS and CSS. You'll find a more complete sample in the [assets directory](./assets).


## Working with Single Images


#### Transforming source images

Use the `image()` method to transform a single image into a low-quality image placeholder (LQIP):

```php
$image = $flou->image('01.jpg');
```

You can also provide custom Glide parameters for the image transformation:

```php
$image = $flou->image('01.jpg', [
    'w' => 10,
    'h' => 10,
    'fit' => 'crop',
]);
```

You'll find all available parameters in the [Glide documentation](https://glide.thephpleague.com/2.0/api/quick-reference/).

As you can see, the default parameters are used to generate LQIP from source images, but you are not restricted to that. You may generate as many transformation as you need from the source image:

```php
$phone = $flou->image('01.jpg', ['w' => 500]);
$tablet = $flou->image('01.jpg', ['w' => 900]);
$desktop = $flou->image('01.jpg', ['w' => 1300]);
```

If you're interested in responsive images with `srcset`, have a look at the next section ([Working with Image Sets](#working-with-image-sets-responsive-images)).


#### Image objects

The `image()` method returns an [`Image`](./src/Image.php) object, from which you can conveniently access the source image file and the transformed (cached) image file:

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

Use the `toArray()` method to export the image to a plain array:

```php
$data = $image->toArray();

# [
#     "source" => [
#         "url" => "/images/source/01.jpg",
#         "path" => "/home/user/my-site.com/public/images/source/01.jpg",
#         "fileName" => "01.jpg",
#         "width" => 3840,
#         "height" => 2160,
#         "ratio" => 1.77777778,
#     ],
#     "cached" => [
#         "url" => "/images/cache/01.jpg/de828e8798017be816f79e131e41dcc9.jpg",
#         ...
#     ],
# ]
```

#### Rendering single images

The `render()` method on the image returns an [`ImageRender`](./src/ImageRender.php) object, which prepares HTML suitable for the vanilla-lazyload library. Then, `img()` is used to render an `img` element:

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

Options passed into `img()` are included as HTML attributes on the element.

The `ImageRender` object can be configured to optimize the HTML output:

- **`useAspectRatio()`:** Prevents content shifting when the LQIP is replaced with the source image:

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

- **`usePaddingTop()`:** A workaround for older browsers not supporting the `aspect-ratio` CSS property:

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

- **`useWrapper()`:** Wraps the image with an extra `div` and separates the LQIP element from the main `img` element. This is used to add a fade-in effect when the image is loaded.

    (Requires additional JS and CSS. [See fade-in example.](#fade-in-image-on-load))

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

Use the `noScript()` method to render an `img` element without any lazy loading behavior:

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

This is used to add a `noscript` image fallback. ([See Noscript image fallback example](#noscript-fallback))

It can also be used creatively to work on the source image with CSS classes and HTML attributes. ([See Browser native lazy loading example](#native-lazy-loading-example))


## Working with Image Sets (Responsive Images)


#### Single source (`img` element)

Use the `imageSet()` method to transform a source image into a set of responsive images:

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
```

This returns an [`ImageSet`](./src/ImageSet.php) object, which prepares all variations of the source image. The `render()` method on the image set returns a [`ImageSetRender`](./src/ImageSetRender.php) instance, as seen before with single images:

```php
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

Like `ImageRender`, you can optimize `ImageSetRender` with the same methods:

- `useAspectRatio()`
- `usePaddingTop()`
- `useWrapper()`
- `noScript()`


#### Multiple sources (`picture` element)

With a tweak in configuration, `imageSet()` can handle multiple source images:

```php
$imageSet = $flou->imageSet([
    'sources' => [
        [
            'image' => 'portrait.jpg',
            'width' => '1023',
            'media' => '(max-width: 1023px)',
        ],
        [
            'image' => 'landscape.jpg',
            'width' => '1024',
            'media' => '(min-width: 1024px)',
        ],
    ],
]);
```

Then, the `picture()` method is used to render a `picture` element:

```php
echo $imageSet
        ->render()
        ->useAspectRatio()
        ->picture(['class' => 'my-image', 'alt' => 'Lorem ipsum']);
```

Output:

```html
<picture>
  <source 
    media="(max-width: 1023px)" 
    data-srcset="/images/cache/portrait.jpg/1422c06dea2257858f6437b9675fba1c.jpg"
  >
  <source 
    media="(min-width: 1024px)" 
    data-srcset="/images/cache/landscape.jpg/1e147b93856eef676f00989ba28365f1.jpg"
  >
  <img 
    class="lazyload my-image" 
    alt="Lorem ipsum" 
    style="aspect-ratio: 1.77777778; object-fit: cover; object-position: center;" 
    src="/images/cache/landscape.jpg/23a733056cc32e360e9cdef3e0be8fb4.jpg" 
    data-src="/images/cache/landscape.jpg/1e147b93856eef676f00989ba28365f1.jpg" 
    width="1024" 
    height="576"
  >
</picture>
```

See also: [Art-directed `picture` element example](#art-directed-picture-element)


## Examples


#### Fade-in image on load

*Extra JS and CSS:*

```html
<script src="https://cdn.jsdelivr.net/npm/vanilla-lazyload@17.8.3/dist/lazyload.min.js"></script>
<script>
    /**
     * vanilla-lazyload API reference: 
     * https://github.com/verlok/vanilla-lazyload#options
     */

    document.addEventListener("DOMContentLoaded", () => {
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
<?= $flou
        ->image('01.jpg')
        ->render()
        ->useAspectRatio()
        ->useWrapper()
        ->img(['class' => 'w-full', 'alt' => 'Lorem ipsum']);
?>
```

<hr>

#### Art-directed `picture` element

*CSS:*

```html
<style>
    .my-image {
        max-width: 1024px;
        width: 100%;
        height: auto;
        aspect-ratio: calc(3 / 4);
        object-fit: cover;
        object-position: center;
    }

    @media screen and (min-width: 1024px) {
        .my-image {
            aspect-ratio: calc(16 / 9);
        }
    }
</style>
```

*Usage:*

```php
<?= $flou->imageSet([
        'sources' => [
            [
                'image' => 'portrait.jpg',
                'width' => '1023',
                'media' => '(max-width: 1023px)',
            ],
            [
                'image' => 'landscape.jpg',
                'width' => '1024',
                'media' => '(min-width: 1024px)',
            ],
        ],
    ])
    ->render()
    ->picture(['class' => 'my-image', 'alt' => 'Lorem ipsum']);
?>
```

<hr>

#### Noscript fallback

*CSS:*

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

<a name="native-lazy-loading-example"></a>
#### Native lazy loading (no JS, with LQIP)

*Usage:*

```php
<?= ($image = $flou->image('01.jpg'))
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
?>
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
composer run format
```


## License

Flou is open-sourced software licensed under the [MIT license](LICENSE.md).
