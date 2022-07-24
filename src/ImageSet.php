<?php

namespace Pboivin\Flou;

use InvalidArgumentException;

class ImageSet
{
    const DEFAULT_SIZES_VALUE = '100vw';

    protected $image;

    protected $sizes;

    protected $sources;

    protected $preparedOutput;

    public function __construct(protected array $config, protected ImageFactory $factory)
    {
        $this->acceptConfig($config);

        if (!$this->sources) {
            throw new InvalidArgumentException("'sources' is not set.");
        }

        $this->prepareOutput();
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

    protected function prepareOutput()
    {
        $src = '';
        $sizes = $this->sizes ?: static::DEFAULT_SIZES_VALUE;
        $srcset = [];

        foreach ($this->sources as $source) {
            if (!isset($source['width'])) {
                throw new InvalidArgumentException("Source is missing required 'width' option.");
            }

            if (!isset($source['image']) && !$this->image) {
                throw new InvalidArgumentException("Missing required 'image' option on source or imageset.");
            }

            $image = $source['image'] ?? $this->image;

            $src = $this->factory
                ->image($image, ['w' => $source['width']])
                ->cached()
                ->url();

            $srcset[] = "{$src} {$source['width']}w";
        }

        $this->preparedOutput = [
            'src' => $src,
            'sizes' => $sizes,
            'srcset' => $srcset,
        ];
    }

    public function setImage(string $sourceFileName): self
    {
        $this->image = $sourceFileName;

        return $this;
    }

    public function setSizes(string $sizes): self
    {
        $this->sizes = $sizes;

        return $this;
    }

    public function setSources(array $sources): self
    {
        $this->sources = $sources;

        return $this;
    }

    public function toArray(): array
    {
        return $this->preparedOutput;
    }

    public function render()
    {
        // TODO
    }
}
