<?php

namespace Pboivin\Flou;

use InvalidArgumentException;
use League\Glide\Server;
use League\Glide\ServerFactory;

class ImageFactory
{
    public const DEFAULT_GLIDE_PARAMS = ['h' => 10, 'fm' => 'gif'];

    protected $sourcePath;

    protected $cachePath;

    protected $sourceUrlBase;

    protected $cacheUrlBase;

    protected $glideParams;

    protected $glideServer;

    protected $inspector;

    protected $imageRenderClass;

    protected $imageSetRenderClass;

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

    public function sourcePath(): ?string
    {
        if (!$this->sourcePath) {
            throw new InvalidArgumentException("'sourcePath' is not set.");
        }

        return $this->sourcePath;
    }

    public function setSourcePath(string $path): self
    {
        $this->sourcePath = $path;

        return $this;
    }

    public function cachePath(): ?string
    {
        if (!$this->cachePath) {
            throw new InvalidArgumentException("'cachePath' is not set.");
        }

        return $this->cachePath;
    }

    public function setCachePath(string $path): self
    {
        $this->cachePath = $path;

        return $this;
    }

    public function sourceUrlBase(): ?string
    {
        if (!$this->sourceUrlBase) {
            throw new InvalidArgumentException("'sourceUrlBase' is not set.");
        }

        return $this->sourceUrlBase;
    }

    public function setSourceUrlBase(string $path): self
    {
        $this->sourceUrlBase = $path;

        return $this;
    }

    public function cacheUrlBase(): ?string
    {
        if (!$this->cacheUrlBase) {
            throw new InvalidArgumentException("'cacheUrlBase' is not set.");
        }

        return $this->cacheUrlBase;
    }

    public function setCacheUrlBase(string $path): self
    {
        $this->cacheUrlBase = $path;

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

    public function glideServer(): Server
    {
        if (!$this->glideServer) {
            $this->glideServer = $this->createGlideServer();
        }

        return $this->glideServer;
    }

    public function setGlideServer(Server $server): self
    {
        $this->glideServer = $server;

        return $this;
    }

    protected function createGlideServer(): Server
    {
        $glideServer = ServerFactory::create([
            'source' => $this->sourcePath(),
            'cache' => $this->cachePath(),
        ]);

        $glideServer->setCacheWithFileExtensions(true);

        return $glideServer;
    }

    public function inspector(): ImageFileInspector
    {
        if (!$this->inspector) {
            $this->inspector = new ImageFileInspector();
        }

        return $this->inspector;
    }

    public function setInspector(ImageFileInspector $inspector): self
    {
        $this->inspector = $inspector;

        return $this;
    }

    public function setImageRenderClass(string $cls): void
    {
        $this->imageRenderClass = $cls;
    }

    public function setImageSetRenderClass(string $cls): void
    {
        $this->imageSetRenderClass = $cls;
    }

    public function setRenderOptions(array $options): void
    {
        $this->renderOptions = $options;
    }

    public function image(string $sourceFileName, ?array $glideParams = null): Image
    {
        $glideParams ??= $this->glideParams();

        $server = $this->glideServer();

        $cachedFileName = $server->makeImage($sourceFileName, $glideParams);

        $image = new Image(
            $this->sourceImageFile($sourceFileName),
            $this->cachedImageFile($cachedFileName)
        );

        if ($this->imageRenderClass) {
            $image->setRenderClass($this->imageRenderClass);
        }

        if ($this->renderOptions) {
            $image->setRenderOptions($this->renderOptions);
        }

        return $image;
    }

    public function imageSet(array $config): ImageSet
    {
        $set = new ImageSet($config, $this);

        if ($this->imageSetRenderClass) {
            $set->setRenderClass($this->imageSetRenderClass);
        }

        if ($this->renderOptions) {
            $set->setRenderOptions($this->renderOptions);
        }

        return $set;
    }

    public function sourceImageFile(string $fileName): ImageFile
    {
        return new ImageFile(
            $fileName,
            $this->sourcePath() . '/' . $fileName,
            $this->sourceUrlBase() . '/' . $fileName,
            $this->inspector()
        );
    }

    public function cachedImageFile(string $fileName): ImageFile
    {
        return new ImageFile(
            $fileName,
            $this->cachePath() . '/' . $fileName,
            $this->cacheUrlBase() . '/' . $fileName,
            $this->inspector()
        );
    }
}
