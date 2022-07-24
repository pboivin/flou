<?php

namespace Pboivin\Flou;

class ImageSetRender extends ImgRenderable
{
    public function __construct(protected ImageSet $imageSet)
    {
    }

    public function useAspectRatio(?float $value = null): self
    {
        $data = $this->imageSet->toArray();

        $this->aspectRatio = is_null($value) ? $data['lqip']->source()->ratio() : $value;

        return $this;
    }

    public function img(array $attributes = []): string
    {
        $data = $this->imageSet->toArray();

        $attributes = $this->prepareAttributes($attributes);

        $attributes['src'] = $data['lqip']->cached()->url();
        $attributes['width'] = $data['lqip']->source()->width();
        $attributes['height'] = $data['lqip']->source()->height();
        $attributes['data-src'] = $this->getSrc($data);
        $attributes['data-srcset'] = $this->getSrcset($data);
        $attributes['data-sizes'] = $data['sizes'];

        return $this->renderImg($attributes);
    }

    protected function getSrcset(array $data): string
    {
        $srcset = [];

        foreach ($data['srcset'] as $source) {
            $url = $source['image']->cached()->url();
            $width = $source['width'];

            $srcset[] = "{$url} {$width}w";
        }

        return implode(', ', $srcset);
    }

    protected function getSrc(array $data): string
    {
        $source = end($data['srcset']);

        return $source['image']->cached()->url();
    }
}
