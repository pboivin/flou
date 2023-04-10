<?php

namespace Pboivin\Flou;

use InvalidArgumentException;
use Pboivin\Flou\Concerns\AcceptsConfig;
use Pboivin\Flou\Concerns\HasGlideParams;
use Pboivin\Flou\Concerns\HasOptionalGlideServer;
use Pboivin\Flou\Concerns\HasRenderOptions;
use Pboivin\Flou\Contracts\ImageMaker;
use Pboivin\Flou\Contracts\ImageSetMaker;

class RemoteImageFactory implements ImageMaker, ImageSetMaker
{
    use AcceptsConfig;
    use HasGlideParams;
    use HasRenderOptions;
    use HasOptionalGlideServer;

    protected $glideUrlBase;

    protected $sourceUrlBase;

    public function glideUrlBase(): ?string
    {
        if ($this->glideUrlBase) {
            return $this->glideUrlBase;
        }

        if ($this->glideServer) {
            return $this->glideServer->getBaseUrl();
        }

        throw new InvalidArgumentException("'glideUrlBase' is not set.");
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
            $this->buildSourceUrl($fileName),
        );
    }

    public function cachedImageFile(string $fileName, array $glideParams): RemoteImageFile
    {
        return new RemoteImageFile(
            $fileName,
            $fileName,
            $this->buildGlideUrl($fileName, $glideParams),
        );
    }

    public function buildSourceUrl(string $fileName): string
    {
        if ($this->sourceUrlBase) {
            return $this->sourceUrlBase . '/' . $fileName;
        }

        return $this->buildGlideUrl($fileName, []);
    }

    public function buildGlideUrl(string $fileName, array $glideParams): string
    {
        return $this
            ->glideUrlBuilder($this->glideUrlBase())
            ->getUrl($fileName, $glideParams);
    }
}
