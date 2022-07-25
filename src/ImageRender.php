<?php

namespace Pboivin\Flou;

use Pboivin\Flou\Image;

class ImageRender extends ImgRenderable
{
    public function __construct(protected Image $image)
    {
    }

    public function source(): ImageFile
    {
        return $this->image->source();
    }

    public function lqip(): ImageFile
    {
        return $this->image->cached();
    }

    public function img(array $attributes = []): string
    {
        $attributes = $this->prepareAttributes($attributes);

        $attributes['src'] = $this->lqip()->url();
        $attributes['data-src'] = $this->source()->url();
        $attributes['width'] = $this->source()->width();
        $attributes['height'] = $this->source()->height();

        return $this->renderImg($attributes);
    }
}
