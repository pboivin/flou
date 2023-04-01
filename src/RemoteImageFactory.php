<?php

namespace Pboivin\Flou;

use InvalidArgumentException;
use Pboivin\Flou\Contracts\ImageMaker;
use Pboivin\Flou\Contracts\ImageSetMaker;

class RemoteImageFactory implements ImageMaker, ImageSetMaker
{
    public const DEFAULT_GLIDE_PARAMS = ['h' => 10, 'fm' => 'gif'];

    protected $glideUrlBase;

    protected $sourceUrlBase;

    protected $glideParams;

    protected $inspector;

    protected $renderOptions;

    final public function __construct(array $config = [])
    {
        if ($config) {
            $this->acceptConfig($config);
        }
    }

    public static function create(array $config = [])
    {
        return new static($config);
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

    public function glideUrlBase(): ?string
    {
        if (!$this->glideUrlBase) {
            throw new InvalidArgumentException("'glideUrlBase' is not set.");
        }

        return $this->glideUrlBase;
    }

    public function setGlideUrlBase(string $path): self
    {
        $this->glideUrlBase = $path;

        return $this;
    }

    public function sourceUrlBase(): ?string
    {
        if (!$this->sourceUrlBase) {
            return $this->glideUrlBase();
        }

        return $this->sourceUrlBase;
    }

    public function setSourceUrlBase(string $path): self
    {
        $this->sourceUrlBase = $path;

        return $this;
    }

    public function glideParams(): array
    {
        if (!$this->glideParams) {
            return static::DEFAULT_GLIDE_PARAMS;
        }

        return $this->glideParams;
    }

    public function setGlideParams(array $params): self
    {
        $this->glideParams = $params;

        return $this;
    }

    public function setRenderOptions(array $options): void
    {
        $this->renderOptions = $options;
    }

    public function image(string $source, ?array $glideParams = null): Image
    {
        $glideParams ??= $this->glideParams();

        $image = new Image(
            $this->sourceImageFile($source),
            $this->cachedImageFile($source, $glideParams)
        );

        if ($this->renderOptions) {
            $image->setRenderOptions($this->renderOptions);
        }

        return $image;
    }

    public function imageSet(array $config, ?array $glideParams = null): ImageSet
    {
        $set = new ImageSet($config, $this, $glideParams);

        if ($this->renderOptions) {
            $set->setRenderOptions($this->renderOptions);
        }

        return $set;
    }

    public function sourceImageFile(string $fileName): RemoteImageFile
    {
        return new RemoteImageFile(
            $fileName,
            $fileName,
            $this->sourceUrlBase() . "/{$fileName}"
        );
    }

    public function cachedImageFile(string $fileName, array $glideParams): RemoteImageFile
    {
        $query = http_build_query($glideParams);

        return new RemoteImageFile(
            $fileName,
            $fileName,
            $this->glideUrlBase() . "/{$fileName}?{$query}"
        );
    }
}
