<?php

namespace Pboivin\Flou;

use Pboivin\Flou\Image;

class ImageRender extends ImgRenderable
{
    public function __construct(protected Image $image)
    {
    }

    public function useAspectRatio(?float $value = null): self
    {
        $this->aspectRatio = is_null($value) ? $this->image->source()->ratio() : $value;

        return $this;
    }

    public function img(array $attributes = []): string
    {
        $attributes = $this->prepareAttributes($attributes);

        $attributes['src'] = $this->image->cached()->url();
        $attributes['data-src'] = $this->image->source()->url();
        $attributes['width'] = $this->image->source()->width();
        $attributes['height'] = $this->image->source()->height();

        return $this->renderImg($attributes);
    }
}
