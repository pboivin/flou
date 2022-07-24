<?php

namespace Pboivin\Flou;

abstract class ImgRenderable
{
    protected $baseClass = 'lazyload';

    protected $aspectRatio = false;

    public function setBaseClass(string $baseClass): self
    {
        $this->baseClass = $baseClass;

        return $this;
    }

    abstract public function useAspectRatio(?float $value = null): self;

    abstract public function img(array $attributes = []): string;

    protected function prepareAttributes(array $attributes = []): array
    {
        if ($this->aspectRatio) {
            $attributes['style'] = implode(' ', [
                "aspect-ratio: {$this->aspectRatio};",
                $attributes['style'] ?? '',
            ]);
        }

        $attributes['class'] = implode(' ', [$this->baseClass, $attributes['class'] ?? '']);
        $attributes['alt'] = $attributes['alt'] ?? '';

        return $attributes;
    }

    protected function renderImg(array $attributes = []): string
    {
        $output = [];

        foreach ($attributes as $key => $value) {
            $output[] = $key . '="' . $value . '"';
        }

        return '<img ' . implode(' ', $output) . '>';
    }
}
