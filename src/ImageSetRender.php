<?php

namespace Pboivin\Flou;

class ImageSetRender
{
    public function __construct(protected ImageSet $imageSet, protected $baseClass = 'lazyload')
    {
    }

    public function setBaseClass(string $baseClass): self
    {
        $this->baseClass = $baseClass;

        return $this;
    }

    public function img(array $attributes = []): string
    {
        $data = $this->imageSet->toArray();

        $attributes['class'] = implode(' ', [$this->baseClass, $attributes['class'] ?? '']);
        $attributes['alt'] = $attributes['alt'] ?? '';
        $attributes['src'] = $data['lqip']->cached()->url();
        $attributes['width'] = $data['lqip']->source()->width();
        $attributes['height'] = $data['lqip']->source()->height();
        $attributes['data-src'] = $this->getSrc($data);
        $attributes['data-srcset'] = $this->getSrcset($data);
        $attributes['data-sizes'] = $data['sizes'];

        $output = [];

        foreach ($attributes as $key => $value) {
            $output[] = $key . '="' . $value . '"';
        }

        return '<img ' . implode(' ', $output) . '>';
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
