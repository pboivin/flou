<?php

namespace Pboivin\Flou;

class ImageSetRender extends ImgRenderable
{
    protected $data;

    final public function __construct(protected array|ImageSet $imageSet, protected array $config = [])
    {
        if (is_array($imageSet)) {
            $this->data = $imageSet;
        } else {
            $this->data = $this->imageSet->data();
        }

        if ($config) {
            $this->acceptRenderConfig($config);
        }
    }

    public static function fromArray(array $imageSet, array $config = []): static
    {
        return new static($imageSet, $config);
    }

    public function main(): ImageFile
    {
        $source = end($this->data['sources']);

        $item = end($source['srcset']);

        return $this->resolveImageFile($item['image'], cached: true);
    }

    public function lqip(): ImageFile
    {
        return $this->resolveImageFile($this->data['lqip'], cached: true);
    }

    public function picture(array $attributes = []): string
    {
        $attributes = $this->prepareAttributes($attributes);

        if (!$this->wrapper) {
            $attributes['src'] = $this->lqipUrl();
        }
        $attributes['data-src'] = $this->main()->url();

        $attributes = $this->handleSizes($attributes, $this->main());

        $attributes = $this->handleAttributeOverrides($attributes);

        $output = [];

        foreach ($this->data['sources'] as $source) {
            $attrs = [];

            if ($source['format'] ?? false) {
                $attrs['type'] = ImageFormats::getType($source['format']);
            }

            if ($source['media'] ?? false) {
                $attrs['media'] = $source['media'];
            }

            if ($source['sizes'] ?? false) {
                $attrs['data-sizes'] = $source['sizes'];
            }

            $attrs['data-srcset'] = $this->getSrcset($source);

            $output[] = $this->htmlTag('source', $attrs);
        }

        $output[] = $this->htmlTag('img', $attributes);

        $picture = $this->htmlWrap('picture', [], implode('', $output));
        $picture = $this->handlePaddingTop($picture);
        $picture = $this->handleWrapper($picture);

        return $picture;
    }

    public function img(array $attributes = []): string
    {
        $attributes = $this->prepareAttributes($attributes);

        if (!$this->wrapper) {
            $attributes['src'] = $this->lqipUrl();
        }

        $attributes = $this->handleSizes($attributes, $this->main());

        $attributes['data-src'] = $this->main()->url();
        $attributes['data-srcset'] = $this->getSrcset($this->data['sources'][0]);
        $attributes['data-sizes'] = $this->getSizes($this->data['sources'][0]);

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

        $attributes = $noScript->handleSizes($attributes, $noScript->main());

        $attributes['src'] = $noScript->main()->url();
        $attributes['srcset'] = $noScript->getSrcset($noScript->data['sources'][0]);
        $attributes['sizes'] = $noScript->getSizes($noScript->data['sources'][0]);

        $attributes = $this->handleAttributeOverrides($attributes);

        return $noScript->renderImg($attributes);
    }

    protected function getSrcset($source): string
    {
        $includeWidth = count($source['srcset']) > 1;

        $srcset = [];

        foreach ($source['srcset'] as $item) {
            $cached = $this->resolveImageFile($item['image'], cached: true);

            $url = $cached->url();

            $width = $item['width'];

            $srcset[] = "{$url}" . ($includeWidth ? " {$width}w" : '');
        }

        return implode(', ', $srcset);
    }

    protected function getSizes($source): string
    {
        return $source['sizes'] ?? ImageSet::DEFAULT_SIZES_VALUE;
    }
}
