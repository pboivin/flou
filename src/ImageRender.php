<?php

namespace Pboivin\Flou;

class ImageRender extends ImgRenderable
{
    final public function __construct(protected array|Image $image, protected array $config = [])
    {
        if ($config) {
            $this->acceptRenderConfig($config);
        }
    }

    public static function fromArray(array $image, array $config = []): static
    {
        return new static($image, $config);
    }

    public function main(): ImageFile
    {
        return $this->resolveImageFile($this->image);
    }

    public function lqip(): ImageFile
    {
        return $this->resolveImageFile($this->image, cached: true);
    }

    public function img(array $attributes = []): string
    {
        $attributes = $this->prepareAttributes($attributes);

        if (!$this->wrapper) {
            $attributes['src'] = $this->lqipUrl();
        }

        $attributes['data-src'] = $this->main()->url();

        $attributes = $this->handleSizes($attributes, $this->main());

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

        $attributes = $noScript->handleSizes($attributes, $noScript->main());

        $attributes = $this->handleAttributeOverrides($attributes);

        return $noScript->renderImg($attributes);
    }
}
