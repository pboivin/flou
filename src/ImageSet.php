<?php

namespace Pboivin\Flou;

use InvalidArgumentException;

class ImageSet
{
    public const DEFAULT_SIZES_VALUE = '100vw';

    protected $renderClass = ImageSetRender::class;

    protected $renderOptions = [];

    protected $image;

    protected $sizes;

    protected $sources;

    protected $data;

    public function __construct(protected array $config, protected ImageFactory $factory)
    {
        $this->acceptConfig($config);

        if (!$this->sources) {
            throw new InvalidArgumentException("'sources' is not set.");
        }

        $this->prepareData();
    }

    protected function acceptConfig(array $config): void
    {
        foreach ($config as $key => $value) {
            if (method_exists($this, $method = "set{$key}")) {
                $this->$method($value);
            } else {
                throw new InvalidArgumentException("Invalid option '$key'.");
            }
        }
    }

    protected function prepareData()
    {
        $sizes = $this->sizes ?: static::DEFAULT_SIZES_VALUE;
        $preparedSources = [];
        $imageForLqip = '';

        foreach ($this->sources as $source) {
            $preparedSources[] = (new ImageSetSource($source, $this))->data();
            $imageForLqip = $source['image'] ?? $this->image;
        }

        $this->data = [
            'sizes' => $sizes,
            'srcset' => $preparedSources,
            'lqip' => $this->factory->image($imageForLqip),
        ];
    }

    public function factory()
    {
        return $this->factory;
    }

    public function image()
    {
        return $this->image;
    }

    protected function setImage(string $sourceFileName): self
    {
        $this->image = $sourceFileName;

        return $this;
    }

    protected function setSizes(string $sizes): self
    {
        $this->sizes = $sizes;

        return $this;
    }

    protected function setSources(array $sources): self
    {
        $this->sources = $sources;

        return $this;
    }

    protected function setWidths(array $widths): self
    {
        $this->sources = array_map(fn ($i) => ['width' => (int) $i], $widths);

        return $this;
    }

    public function data(): array
    {
        return $this->data;
    }

    public function toArray(): array
    {
        return [
            'sizes' => $this->data['sizes'],
            'srcset' => array_map(function ($item) {
                $item['image'] = $item['image']->toArray();
                return $item;
            }, $this->data['srcset']),
            'lqip' => $this->data['lqip']->toArray(),
        ];
    }

    public function setRenderClass(string $cls): void
    {
        $this->renderClass = $cls;
    }

    public function setRenderOptions(array $options): void
    {
        $this->renderOptions = $options;
    }

    public function render(): ImageSetRender
    {
        return new ($this->renderClass)($this, $this->renderOptions);
    }
}
