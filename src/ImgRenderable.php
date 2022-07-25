<?php

namespace Pboivin\Flou;

abstract class ImgRenderable
{
    protected $baseClass = 'lazyload';

    protected $wrapperClass = 'lazyload-wrapper';

    protected $lqipClass = 'lazyload-lqip';

    protected $aspectRatio = false;

    protected $wrapper = false;

    public function setBaseClass(string $cls): self
    {
        $this->baseClass = $cls;

        return $this;
    }

    public function setWrapperClass(string $cls): self
    {
        $this->wrapperClass = $cls;

        return $this;
    }

    public function setLqipClass(string $cls): self
    {
        $this->lqipClass = $cls;

        return $this;
    }

    public function useWrapper(bool $value = true): self
    {
        $this->wrapper = $value;

        return $this;
    }

    public function useAspectRatio(?float $value = null): self
    {
        $this->aspectRatio = is_null($value) ? $this->source()->ratio() : $value;

        return $this;
    }

    abstract public function source(): ImageFile;

    abstract public function lqip(): ImageFile;

    abstract public function img(array $attributes = []): string;

    protected function prepareAttributes(array $attributes = []): array
    {
        $style = [];

        if ($this->aspectRatio) {
            $style[] = "aspect-ratio: {$this->aspectRatio};";
        }

        if ($attributes['style'] ?? false) {
            $style[] = $attributes['style'];
        }

        if ($style) {
            $attributes['style'] = implode(' ', $style);
        }

        $attributes['class'] = implode(' ', [$this->baseClass, $attributes['class'] ?? '']);

        $attributes['alt'] = $attributes['alt'] ?? '';

        return $attributes;
    }

    protected function renderImg(array $attributes = []): string
    {
        if ($this->wrapper) {
            return implode('', [
                '<div class="lazyload-wrapper">',
                $this->imgTag($attributes),
                $this->imgTag([
                    'class' => $this->lqipClass,
                    'src' => $this->lqip()->url(),
                ]),
                '</div>',
            ]);
        }

        return $this->imgTag($attributes);
    }

    protected function imgTag(array $attributes = []): string
    {
        $output = [];

        foreach ($attributes as $key => $value) {
            $output[] = $key . '="' . $value . '"';
        }

        return '<img ' . implode(' ', $output) . '>';
    }
}
