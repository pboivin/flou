# flou 1.0

This is a draft.

## Basic setup

JS:

```html
<script src="https://unpkg.com/vanilla-lazyload@17.x"></script>
<script>
    document.addEventListener("DOMContentLoaded", function(){
        new LazyLoad({
            elements_selector: ".lazyload",
        });
    });
</script>
```

Factory:

```php
use Pboivin\Flou\ImageFactory;

$flou = new ImageFactory([
    'sourcePath' => '/home/user/my-site.com/public/images/source',
    'cachePath' => '/home/user/my-site.com/public/images/cache',
    'sourceUrlBase' => '/images/source',
    'cacheUrlBase' => '/images/cache',
]);
```

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
                const wrapper = el.closest('.lazyload-wrapper');
                if (wrapper) {
                    wrapper.classList.add('loaded');
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
