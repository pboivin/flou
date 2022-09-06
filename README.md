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

**Laravel integration:**

See the [flou-laravel](https://github.com/pboivin/flou-laravel) repository.


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


#### Configuration

The required options are:

| Name | Type | Description |
|---|---|---|
| `sourcePath` | string | The full path to the source images. |
| `cachePath` | string | The full path where Glide will store the image transformations. |
| `sourceUrlBase` | string | The base URL for the source images. |
| `cacheUrlBase` | string | The base URL for the transformed images. |

Other options:

| Name | Type | Description |
|---|---|---|
| `glideParams` | array | [Default Glide parameters for LQIP elements.](#default-glide-parameters) |
| `renderOptions` | array | [Default render options for all images.](#default-render-options) |


#### Framework Integration

If you're using a framework with a Service Container, you can register the `$flou` instance as a singleton for your entire application. This will be your entry point to transform and render images.

If you're using Laravel, have a look at the [flou-laravel](https://github.com/pboivin/flou-laravel) companion repository. It registers a singleton instance for you and provides a `Flou` facade class.


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


#### Default Glide parameters

You can customize the default Glide parameters in the `ImageFactory` configuration:

```php
$flou = new ImageFactory([
    // ...
    'glideParams' => [
        'h' => 10,
        'fm' => 'gif',
    ],
]);
```


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

<details>
<summary>See HTML Output</summary>

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
</details>
<br>

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

    <details>
    <summary>See HTML Output</summary>
    
    ```html
    <img 
      class="lazyload w-full" 
      alt="Lorem ipsum" 
      style="aspect-ratio: 1.77777778; object-fit: cover; object-position: center;" 
      ...
    >
    ```
    </details>
    <br>

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

    <details>
    <summary>See HTML Output</summary>
    
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
    </details>
    <br>

- **`useWrapper()`:** Wraps the image with an extra `div` and separates the LQIP element from the main `img` element. This is used to add a fade-in effect when the image is loaded.

    (Requires additional JS and CSS. [See fade-in example.](#fade-in-image-on-load))

    ```php
    echo $image
            ->render()
            ->useWrapper()
            ->img(['class' => 'w-full', 'alt' => 'Lorem ipsum']);
    ```


    <details>
    <summary>See HTML Output</summary>
    
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
    </details>
    <br>

- **`useBase64Lqip()`:** Inlines a Base64 version of the LQIP in the `src` attribute of the `img` element. This reduces the number of HTTP requests needed to display a page, at the cost of making the HTML a bit heavier.

    ```php
    echo $image
            ->render()
            ->useBase64Lqip()
            ->img(['class' => 'w-full', 'alt' => 'Lorem ipsum']);
    ```

    <details>
    <summary>See HTML Output</summary>
    
    ```html
    <img 
      class="lazyload w-full" 
      alt="Lorem ipsum" 
      src="data:image/jpg;base64,/9j/4AAQSkZJRgABAQEAYABgAAD//gA7Q1JFQ..." 
      data-src="/images/source/01.jpg" 
      width="2932" 
      height="2000"
    >
    ```
    </details>
    <br>


#### Default render options

You can customize the default render options for all images in the `ImageFactory` configuration:

```php
$flou = new ImageFactory([
    // ...
    'renderOptions' => [
        'aspectRatio' => true,
        'wrapper' => true,
        'base64Lqip' => true,
        // ...
    ],
]);
```

<details>
<summary>See Available Options</summary>

| Name | Type | Description |
|---|---|---|
| `baseClass` | string | CSS class for `img` element. Default: `'lazyload'` |
| `wrapperClass` | string | CSS class for wrapper element. Default: `'lazyload-wrapper'` |
| `lqipClass` | string | CSS class for LQIP element. Default: `'lazyload-lqip'` |
| `paddingClass` | string | CSS class for padding-specific wrapper element. Default: `'lazyload-padding'` |
| `aspectRatio` | boolean or number | Use aspect ratio. Default: `false` |
| `paddingTop` | boolean or number | Use padding-top workaround. Default: `false` |
| `wrapper` | boolean | Use wrapper element. Default: `false` |
| `base64Lqip` | boolean | Use Base64 LQIP value. Default: `false` |
</details>
<br>


#### Noscript variation

Use the `noScript()` method to render an `img` element without any lazy loading behavior:

```php
echo $image
        ->render()
        ->noScript(['class' => 'w-full', 'alt' => 'Lorem ipsum']);
```

<details>
<summary>See HTML Output</summary>

```html
<img 
  class="lazyload-noscript w-full" 
  alt="Lorem ipsum" 
  src="/images/source/01.jpg" 
  width="2932" 
  height="2000"
>
```
</details>
<br>

This is used to add a `noscript` image fallback. ([See Noscript image fallback example](#noscript-fallback))

It can also be used creatively to work on the source image with CSS classes and HTML attributes. ([See Browser native lazy loading example](#native-lazy-loading-example))


## Working with Image Sets (Responsive Images)


#### Single source (`img` element)

Use the `imageSet()` method to transform a source image into a set of responsive images:

```php
$imageSet = $flou->imageSet([
    'image' => '01.jpg',
    'sizes' => '(max-width: 500px) 100vw, 50vw',
    'widths' => [500, 900, 1300, 1700],
]);
```

This returns an [`ImageSet`](./src/ImageSet.php) object, which prepares all variations of the source image. The `render()` method on the image set returns a [`ImageSetRender`](./src/ImageSetRender.php) instance, as seen before with single images:

```php
echo $imageSet
        ->render()
        ->useAspectRatio()
        ->img(['class' => 'w-full', 'alt' => 'Lorem ipsum']);
```


<details>
<summary>See HTML Output</summary>

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
  width="1700" 
  height="956" 
>
```
</details>
<br>

Like `ImageRender`, you can optimize `ImageSetRender` with the same methods:

- `useAspectRatio()`
- `usePaddingTop()`
- `useWrapper()`
- `useBase64Lqip()`
- `noScript()`


#### Multiple sources (`picture` element)

With a tweak in configuration, `imageSet()` can handle multiple source images:

```php
$imageSet = $flou->imageSet([
    [
        'image' => 'portrait.jpg',
        'media' => '(max-width: 1023px)',
        'sizes' => '100vw',
        'widths' => [400, 800, 1200],
    ],
    [
        'image' => 'landscape.jpg',
        'media' => '(min-width: 1024px)',
        'sizes' => '66vw',
        'widths' => [800, 1200, 1600],
    ],
]);
```

Then, the `picture()` method is used to render a `picture` element:

```php
echo $imageSet
        ->render()
        ->picture(['class' => 'my-image', 'alt' => 'Lorem ipsum']);
```

<details>
<summary>See HTML Output</summary>

```html
<picture>
  <source
    media="(max-width: 1023px)"
    data-sizes="100vw"
    data-srcset="/images/cache/portrait.jpg/a50df0a8c8a84cfc6a77cf74b414d020.jpg 400w,
                 /images/cache/portrait.jpg/1422c06dea2257858f6437b9675fba1c.jpg 800w,
                 /images/cache/portrait.jpg/de828e8798017be816f79e131e41dcc9.jpg 1200w"
  >
  <source 
    media="(min-width: 1024px)" 
    data-sizes="66vw" 
    data-srcset="/images/cache/landscape.jpg/c6f9c52bea237b64cc98fc9f5f3f15c6.jpg 800w,
                 /images/cache/landscape.jpg/fcc882305b523e823c7a24df05045c5a.jpg 1200w,
                 /images/cache/landscape.jpg/a50df0a8c8a84cfc6a77cf74b414d020.jpg 1600w"
  >
  <img
    class="lazyload my-image"
    alt="Lorem ipsum"
    src="/images/cache/landscape.jpg/66d1d4a938d99f2b0234e08008af09a8.gif"
    data-src="/images/cache/landscape.jpg/a50df0a8c8a84cfc6a77cf74b414d020.jpg"
    width="1600"
    height="900"
  >
</picture>
```
</details>
<br>

See also: [Art-directed `picture` element example](#art-directed-picture-element)


#### Multiple formats (`picture` element)

Similarly, `imageSet()` can also handle multiple image formats for each source:

```php
$imageSet = $flou->imageSet([
    'image' => 'portrait.jpg',
    'sizes' => '100vw',
    'widths' => [400, 800, 1200, 1600],
    'formats' => ['webp', 'jpg'],
]);

echo $imageSet
        ->render()
        ->picture(['class' => 'my-image', 'alt' => 'Lorem ipsum']);
```

<details>
<summary>See HTML Output</summary>

```html
<picture>
  <source
    type="image/webp"
    data-srcset="/images/cache/01.jpg/7c7086baa60bb4b3876b14dd577fa9e8.webp 400w,
                 /images/cache/01.jpg/49ada5db20e72d539b611e5d17640d2f.webp 800w,
                 /images/cache/01.jpg/cb48c273b44bd0f00155d4932231fe28.webp 1200w,
                 /images/cache/01.jpg/a50df0a8c8a84cfc6a77cf74b414d020.webp 1600w"
    data-sizes="100vw"
  >
  <source
    type="image/jpeg"
    data-srcset="/images/cache/01.jpg/27e8a3f7fb4abe60654117a34f2007e1.jpg 400w,
                 /images/cache/01.jpg/f319ea155d0009a7e842f50fcc020fe3.jpg 800w,
                 /images/cache/01.jpg/cfdad3b69ae3a15ba479aa85868e75f3.jpg 1200w,
                 /images/cache/01.jpg/1422c06dea2257858f6437b9675fba1c.webp 1600w"
    data-sizes="100vw"
  >
  <img
    class="lazyload w-full"
    alt="Lorem ipsum"
    src="/images/cache/01.jpg/bd0dc309cfc3b71731b2e2df3d6e130b.gif"
    data-src="/images/cache/01.jpg/cfdad3b69ae3a15ba479aa85868e75f3.jpg"
    width="1600"
    height="900"
  >
</picture>
```
</details>
<br>


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
        width: 100%;
        height: auto;
        aspect-ratio: calc(3 / 4);
        object-fit: cover;
        object-position: center;
    }

    @media screen and (min-width: 1024px) {
        .my-image {
            max-width: 66vw;
            aspect-ratio: calc(16 / 9);
        }
    }
</style>
```

*Usage:*

```php
<?= $flou->imageSet([
        [
            'image' => 'portrait.jpg',
            'media' => '(max-width: 1023px)',
            'sizes' => '100vw',
            'widths' => [400, 800, 1200],
        ],
        [
            'image' => 'landscape.jpg',
            'media' => '(min-width: 1024px)',
            'sizes' => '66vw',
            'widths' => [800, 1200, 1600],
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

#### CLI script

*Preprocess all images in a source directory and prepare a JSON inventory file:*

```php
<?php

require 'vendor/autoload.php';

$flou = new Pboivin\Flou\ImageFactory([
    'sourcePath' => './public/images/source',
    'cachePath' => './public/images/cache',
    'sourceUrlBase' => '/images/source',
    'cacheUrlBase' => '/images/cache',
]);

$data = [];

foreach (glob('./public/images/source/*.jpg') as $path) {
    $file = basename($path);

    echo "Processing image: $file\n";

    $data[$file] = [
        'source' => $flou->image($file)->source()->toArray(),
        'lqip' => $flou->image($file)->cached()->toArray(),
        'responsive' => array_map(
            fn ($width) => $flou->image($file, ['w' => $width])->cached()->toArray(),
            [500, 900, 1300, 1700]
        ),
    ];
}

file_put_contents('./data/images.json', json_encode($data, JSON_PRETTY_PRINT));

echo "Done!\n";
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
