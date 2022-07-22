<?php

namespace Pboivin\Flou;

use Pboivin\Flou\Image;

class ImageRender
{
    public function __construct(protected Image $image, protected $baseClass = 'lazyload')
    {
    }

    public function setBaseClass(string $baseClass): self
    {
        $this->baseClass = $baseClass;

        return $this;
    }

    public function img(array $attributes = []): string
    {
        $attributes['class'] = implode(' ', [$this->baseClass, $attributes['class'] ?? '']);
        $attributes['alt'] = $attributes['alt'] ?? '';
        $attributes['src'] = $this->image->cached()->url();
        $attributes['data-src'] = $this->image->source()->url();
        $attributes['width'] = $this->image->source()->width();
        $attributes['height'] = $this->image->source()->height();

        $output = [];

        foreach ($attributes as $key => $value) {
            $output[] = $key . '="' . $value . '"';
        }

        return '<img ' . implode(' ', $output) . '>';
    }
}
