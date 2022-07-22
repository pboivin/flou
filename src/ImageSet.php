<?php

namespace Pboivin\Flou;

use InvalidArgumentException;

class ImageSet
{
    const DEFAULT_SIZES_VALUE = '100vw';

    protected $image;

    protected $sizes;

    protected $sources;

    public function __construct(protected array $config, protected ImageFactory $factory)
    {
        $this->acceptConfig($config);

        if (!$this->sizes) {
            $this->setSizes(static::DEFAULT_SIZES_VALUE);
        }
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

    public function setImage(string $sourceFileName): self
    {
        $this->image = $sourceFileName;

        return $this;
    }

    public function setSizes(mixed $sizes): self
    {
        $this->sizes = $sizes;

        return $this;
    }

    public function setSources(array $sources): self
    {
        $this->sources = $sources;

        return $this;
    }

    public function render()
    {
        // TODO
    }
}
