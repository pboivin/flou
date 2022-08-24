<?php

namespace Pboivin\Flou;

class ImageSetRender extends ImgRenderable
{
    protected $data;

    public function __construct(protected ImageSet $imageSet, protected array $config = [])
    {
        $this->data = $this->imageSet->data();

        if ($config) {
            $this->acceptRenderConfig($config);
        }
    }

    public function main(): ImageFile
    {
        $source = end($this->data['srcset']);

        return $source['image']->cached();
    }

    public function lqip(): ImageFile
    {
        return $this->data['lqip']->cached();
    }

    public function picture(array $attributes = []): string
    {
        $attributes = $this->prepareAttributes($attributes);

        $attributes['src'] = $this->lqipUrl();
        $attributes['data-src'] = $this->main()->url();
        $attributes['width'] = $this->main()->width();
        $attributes['height'] = $this->main()->height();

        $srcset = $this->data['srcset'];

        $sources = [];
        $end = end($srcset);
        $endWidth = 0;

        foreach ($srcset as $src) {
            $media =
                $src === $end ? "(min-width: {$endWidth}px)" : "(max-width: {$src['width']}px)";

            $sources[] = $this->htmlTag('source', [
                'media' => $src['media'] ?? $media,
                'data-srcset' => $src['image']->cached()->url(),
            ]);

            $endWidth = $src['width'] + 1;
        }

        $sources[] = $this->htmlTag('img', $attributes);
        $picture = $this->htmlWrap('picture', [], implode('', $sources));
        $picture = $this->handlePaddingTop($picture);
        $picture = $this->handleWrapper($picture);

        return $picture;
    }

    public function img(array $attributes = []): string
    {
        $attributes = $this->prepareAttributes($attributes);

        $attributes['src'] = $this->lqipUrl();
        $attributes['width'] = $this->main()->width();
        $attributes['height'] = $this->main()->height();
        $attributes['data-src'] = $this->main()->url();
        $attributes['data-srcset'] = $this->getSrcset();
        $attributes['data-sizes'] = $this->data['sizes'];

        return $this->renderImg($attributes);
    }

    public function noScript(array $attributes = []): string
    {
        $noScript = clone $this;

        $noScript->baseClass = $noScript->baseClass . '-noscript';
        $noScript->wrapperClass = $noScript->wrapperClass . '-noscript';
        $noScript->paddingClass = $noScript->paddingClass . '-noscript';
        $noScript->includeLqip = false;

        $attributes = $noScript->prepareAttributes($attributes);

        $attributes['width'] = $noScript->main()->width();
        $attributes['height'] = $noScript->main()->height();
        $attributes['src'] = $noScript->main()->url();
        $attributes['srcset'] = $noScript->getSrcset();
        $attributes['sizes'] = $noScript->data['sizes'];

        return $noScript->renderImg($attributes);
    }

    protected function getSrcset(): string
    {
        $srcset = [];

        foreach ($this->data['srcset'] as $source) {
            $url = $source['image']->cached()->url();
            $width = $source['width'];

            $srcset[] = "{$url} {$width}w";
        }

        return implode(', ', $srcset);
    }
}
