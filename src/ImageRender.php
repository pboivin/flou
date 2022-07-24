<?php

namespace Pboivin\Flou;

use Pboivin\Flou\Image;

class ImageRender
{
    protected $baseClass = 'lazyload';

    protected $aspectRatio = false;

    public function __construct(protected Image $image)
    {
    }

    public function setBaseClass(string $baseClass): self
    {
        $this->baseClass = $baseClass;

        return $this;
    }

    public function useAspectRatio(?float $value = null): self
    {
        $this->aspectRatio = is_null($value) ? $this->image->source()->ratio() : $value;

        return $this;
    }

    public function img(array $attributes = []): string
    {
        if ($this->aspectRatio) {
            $attributes['style'] = implode(' ', [
                "aspect-ratio: {$this->aspectRatio};",
                $attributes['style'] ?? '',
            ]);
        }

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
