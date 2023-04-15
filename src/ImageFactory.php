<?php

namespace Pboivin\Flou;

use InvalidArgumentException;
use Pboivin\Flou\Concerns\AcceptsConfig;
use Pboivin\Flou\Concerns\HasGlideParams;
use Pboivin\Flou\Concerns\CreatesGlideServer;
use Pboivin\Flou\Concerns\HasInspector;
use Pboivin\Flou\Concerns\HasRenderOptions;
use Pboivin\Flou\Concerns\HasResampler;
use Pboivin\Flou\Contracts\ImageMaker;
use Pboivin\Flou\Contracts\ImageResampler;
use Pboivin\Flou\Contracts\ImageSetMaker;

class ImageFactory implements ImageMaker, ImageSetMaker, ImageResampler
{
    use AcceptsConfig;
    use CreatesGlideServer;
    use HasGlideParams;
    use HasInspector;
    use HasRenderOptions;
    use HasResampler;

    protected $sourcePath;

    protected $cachePath;

    protected $sourceUrlBase;

    protected $cacheUrlBase;

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

    public function resample(string $sourceFileName, array $glideParams): ResampledImage
    {
        $image = $this->image($sourceFileName, $glideParams);

        return new ResampledImage($image->cached(), $this->resampler());
    }
}
