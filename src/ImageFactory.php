<?php

namespace Pboivin\Flou;

use InvalidArgumentException;
use League\Glide\Server;
use League\Glide\ServerFactory;
use Pboivin\Flou\Contracts\ImageMaker;
use Pboivin\Flou\Contracts\ImageSetMaker;

class ImageFactory implements ImageMaker, ImageSetMaker
{
    public const DEFAULT_GLIDE_PARAMS = ['h' => 10, 'fm' => 'gif'];

    public const DEFAULT_RESAMPLING_DIR = '_r';

    protected $sourcePath;

    protected $cachePath;

    protected $sourceUrlBase;

    protected $cacheUrlBase;

    protected $glideParams;

    protected $glideServer;

    protected $inspector;

    protected $renderOptions;

    protected $resampler;

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

    public function setRenderOptions(array $options): void
    {
        $this->renderOptions = $options;
    }

    public function image(string|ResampledImage $source, ?array $glideParams = null): Image
    {
        if ($source instanceof ResampledImage) {
            return $source->make($glideParams);
        }

        $glideParams ??= $this->glideParams();

        $server = $this->glideServer();

        $cachedFileName = $server->makeImage($source, $glideParams);

        $image = new Image(
            $this->sourceImageFile($source),
            $this->cachedImageFile($cachedFileName)
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

    public function resampler(): ImageFactory
    {
        if (!$this->resampler) {
            $this->resampler = new static([
                'sourcePath' => $this->cachePath(),
                'cachePath' => $this->cachePath() . '/' . static::DEFAULT_RESAMPLING_DIR,
                'sourceUrlBase' => $this->cacheUrlBase(),
                'cacheUrlBase' => $this->cacheUrlBase() . '/' . static::DEFAULT_RESAMPLING_DIR,
                'glideParams' => $this->glideParams(),
                'renderOptions' => $this->renderOptions ?: [],
            ]);
        }

        return $this->resampler;
    }

    public function setResampler(ImageFactory $resamplingFactory): void
    {
        $this->resampler = $resamplingFactory;
    }

    public function resample(string $sourceFileName, array $glideParams): ResampledImage
    {
        $image = $this->image($sourceFileName, $glideParams);

        return new ResampledImage($image->cached(), $this->resampler());
    }
}
