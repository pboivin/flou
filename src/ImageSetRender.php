<?php

namespace Pboivin\Flou;

class ImageSetRender extends ImgRenderable
{
    protected $data;

    public function __construct(protected ImageSet $imageSet)
    {
        $this->data = $this->imageSet->toArray();
    }

    public function source(): ImageFile
    {
        return $this->data['lqip']->source();
    }

    public function lqip(): ImageFile
    {
        return $this->data['lqip']->cached();
    }

    public function img(array $attributes = []): string
    {
        $attributes = $this->prepareAttributes($attributes);

        $attributes['src'] = $this->lqip()->url();
        $attributes['width'] = $this->source()->width();
        $attributes['height'] = $this->source()->height();
        $attributes['data-src'] = $this->getSrc();
        $attributes['data-srcset'] = $this->getSrcset();
        $attributes['data-sizes'] = $this->data['sizes'];

        return $this->renderImg($attributes);
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

    protected function getSrc(): string
    {
        $source = end($this->data['srcset']);

        return $source['image']->cached()->url();
    }
}
