<?php

namespace Pboivin\Flou;

class ImageRender extends ImgRenderable
{
    public function __construct(protected Image $image, protected array $config = [])
    {
        if ($config) {
            $this->acceptRenderConfig($config);
        }
    }

    public function main(): ImageFile
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

        $attributes['src'] = $this->lqipUrl();
        $attributes['data-src'] = $this->main()->url();
        $attributes['width'] = $this->main()->width();
        $attributes['height'] = $this->main()->height();

        $attributes = $this->handleAttributeOverrides($attributes);

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

        $attributes['src'] = $noScript->main()->url();
        $attributes['width'] = $noScript->main()->width();
        $attributes['height'] = $noScript->main()->height();

        $attributes = $this->handleAttributeOverrides($attributes);

        return $noScript->renderImg($attributes);
    }
}
