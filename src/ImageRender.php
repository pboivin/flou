<?php

namespace Pboivin\Flou;

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

    public function noScript(array $attributes = []): string
    {
        $noScript = clone $this;

        $noScript->baseClass = $noScript->baseClass . '-noscript';
        $noScript->wrapperClass = $noScript->wrapperClass . '-noscript';
        $noScript->paddingClass = $noScript->paddingClass . '-noscript';
        $noScript->includeLqip = false;

        $attributes = $noScript->prepareAttributes($attributes);

        $attributes['src'] = $noScript->source()->url();
        $attributes['width'] = $noScript->source()->width();
        $attributes['height'] = $noScript->source()->height();

        return $noScript->renderImg($attributes);
    }
}
