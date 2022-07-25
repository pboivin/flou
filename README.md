# flou

(This is a draft.)

Flou is a PHP package integrating the [Glide (PHP)](#) and the [vanilla-lazyload (JS)](#) libraries.

It is optimized to quickly implement image lazyloading on prototypes and static sites, using a local folder of source images.

Features:
- Uses Glide for image processing
- Framework agnostic — a set of plain PHP classes
- Useable in static site generators and CLI scripts
- Transforms images on initial page load — does not expose Glide URLs

TOC:
- Installing
- Getting Started
- Working with Single Images
- Working with Image Sets (Responsive Images)
- Examples


## Installing 

Can be installed via Composer:

```
composer require pboivin/flou
```

This will install Glide.

You can pull-in the vanilla-lazyload package via a CDN:

```html
<script src="https://unpkg.com/vanilla-lazyload@17.x"></script>
```

or via NPM:

```
npm install --save vanilla-lazyload
```

Consult the package's documentation for more information on how to install and configure it.


## Getting Started

First, initialize the `LazyLoad` JS object:

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

If you're using a framework like Laravel, you can register the `$flou` instance as a singleton for your entire application. This will be your entry point to process and render images into HMTL.

In the following code examples, we'll use a global helper function to this effect:

```php
function flou()
{
    static $instance;

    if (!$instance) {
        $instance = new ImageFactory([
            'sourcePath' => '/home/user/my-site.com/public/images/source',
            'cachePath' => '/home/user/my-site.com/public/images/cache',
            'sourceUrlBase' => '/images/source',
            'cacheUrlBase' => '/images/cache',
        ]);
    }

    return $instance
}
```


## Working with Single Images


#### Transforming source images

To load a source image and generate a low-quality image placeholder (LQIP):

```php
$image = flou()->image('01.jpg');
```

You can also provide custom Glide parameters for your image transformation:

```php
$image = flou()->image('01.jpg', ['w' => 10, 'h' => 4]);
```

You'll find all available parameters in the [Glide documentation](#).

As you can see, the default parameters are used to generate LQIP from source images, but you are not restricted to only generating LQIP. You can generate as many transformation as you need from the same image:

```php
$phone = flou()->image('01.jpg', ['w' => 500]);
$tablet = flou()->image('01.jpg', ['w' => 900]);
$desktop = flou()->image('01.jpg', ['w' => 1300]);
```

If you're interested in responsive images with `srcset`, have a look at the next section ([Working with Image Sets](#)).

The `image()` method will return a `Image` object, from which you can access the information associated to the source images, and the cached (transformed) image:

```php 
# Source image data:
echo $image->source()->url();       # /images/source/01.jpg
echo $image->source()->width();     # 3840 
echo $image->source()->height();    # 2160
echo $image->source()->ratio();     # 1.7777777777777777

# Transformed image data:
echo $image->cached()->url();       # /images/cache/01.jpg/de828e8798017be816f79e131e41dcc9.jpg
```


#### Rendering single images

The `render()` method on your image will return a `ImageRender` object, which is a basic helper to generate HTML suitable for the `vanilla-lazyload` library. Here's a basic example rendering an `<img>` element:

```php
echo $image->render()->img(['class' => 'w-full', 'alt' => 'Lorem ipsum']);
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

Any options passed into the `img()` method will be included as an HTML attribute on the element.











# Working with Image Sets (Responsive Images)







# Examples








==================================================

## Single image rendering

Basic example:

```php
$image = $flou->image('01.jpg');

echo $image->render()->img(['class' => 'w-full', 'alt' => 'Lorem ipsum']);

#  <img 
#    class="lazyload w-full" 
#    alt="Lorem ipsum" 
#    src="/images/cache/01.jpg/de828e8798017be816f79e131e41dcc9.jpg" 
#    data-src="/images/source/01.jpg" 
#    width="3840" 
#    height="2160"
#  >
```

Aspect-ratio (prevent content shifting on load):

```php
$image = $flou->image('01.jpg');

echo $image
        ->render()
        ->useAspectRatio()
        ->img(['class' => 'w-full', 'alt' => 'Lorem ipsum']);

#  <img 
#    class="lazyload w-full" 
#    alt="Lorem ipsum" 
#    style="aspect-ratio: 1.7777777777777777" 
#    ...
#  >
```

Padding-top (workaround for older browsers):

```php
$image = $flou->image('01.jpg');

echo $image
        ->render()
        ->usePaddingTop()
        ->img(['class' => 'w-full', 'alt' => 'Lorem ipsum']);

#  <div class="lazyload-padding" style="position: relative; padding-top: 56.25%;">
#    <img
#      class="lazyload w-full"
#      alt="Lorem ipsum" 
#      style="position: absolute; top: 0; height:0; width: 100%; height: 100%; 
#             object-fit: cover; object-position: center;"
#      ...
#  </div>
```

Wrapper + fade-in (needs extra JS and CSS):

```html
<script src="https://unpkg.com/vanilla-lazyload@17.x"></script>
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

```php
$image = $flou->image('01.jpg');

echo $image
        ->render()
        ->useAspectRatio()
        ->useWrapper()
        ->img(['class' => 'w-full', 'alt' => 'Lorem ipsum']);

#  <div class="lazyload-wrapper">
#    <img 
#      class="lazyload w-full" 
#      alt="Lorem ipsum" 
#      style="aspect-ratio: 1.7777777777778;" 
#      ...
#    >
#    <img 
#      class="lazyload-lqip" 
#      src="/images/cache/01.jpg/de828e8798017be816f79e131e41dcc9.jpg"
#    >
#  </div>
```

## Responsive image set rendering

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
        ->img(['class' => 'w-full', 'alt' => 'Lorem ipsum'])

#  <img 
#    class="lazyload w-full" 
#    alt="Lorem ipsum" 
#    style="aspect-ratio: 1.7777777777777777;" 
#    src="/images/cache/01.jpg/de828e8798017be816f79e131e41dcc9.jpg" 
#    data-src="/images/cache/01.jpg/b8648e93b40b56d5c5a78acc7a23e3d9.jpg" 
#    data-srcset="/images/cache/01.jpg/a50df0a8c8a84cfc6a77cf74b414d020.jpg 500w, 
#                 /images/cache/01.jpg/1422c06dea2257858f6437b9675fba1c.jpg 900w, 
#                 /images/cache/01.jpg/1eac615f1a50f20c434e5944225bdd4f.jpg 1300w, 
#                 /images/cache/01.jpg/b8648e93b40b56d5c5a78acc7a23e3d9.jpg 1700w" 
#    data-sizes="(max-width: 500px) 100vw, 50vw"
#    width="3840" 
#    height="2160" 
#  >
```

## Image source and transformation

```php
# Transform an image using built-in LQIP settings:
$image = $flou->image('01.jpg');

# or using custom Glide params:
$image = $flou->image('01.jpg', ['w' => 10, 'h' => 4]);

# Source image data:
echo $image->source()->url();       # /images/source/01.jpg
echo $image->source()->width();     # 3840 
echo $image->source()->height();    # 2160
echo $image->source()->ratio();     # 1.7777777777777777

# Transformed image data:
echo $image->cached()->url();       # /images/cache/01.jpg/de828e8798017be816f79e131e41dcc9.jpg
```
