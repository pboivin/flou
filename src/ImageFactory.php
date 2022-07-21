<?php

namespace Pboivin\Flou;

use League\Glide\Server;
use League\Glide\ServerFactory;

class ImageFactory
{
    const DEFAULT_GLIDE_PARAMS = ['h' => 10];

    protected $sourcePath;

    protected $cachePath;

    protected $sourceUrlBase;

    protected $cacheUrlBase;

    protected $glideParams;

    protected $glideServer;

    public function __construct(array $options = [])
    {
        if ($options) {
            $this->acceptOptions($options);
        }
    }

    /**
     * @throws \InvalidArgumentException
     */
    protected function acceptOptions(array $options): void
    {
        foreach ($options as $key => $value) {
            if (method_exists($this, $method = "set{$key}")) {
                $this->$method($value);
            } else {
                throw new \InvalidArgumentException("Invalid option '$key'", 1);
            }
        }
    }

    public function sourcePath(): ?string
    {
        return $this->sourcePath;
    }

    public function setSourcePath(string $path): self
    {
        $this->sourcePath = $path;

        return $this;
    }

    public function cachePath(): ?string
    {
        return $this->cachePath;
    }

    public function setCachePath(string $path): self
    {
        $this->cachePath = $path;

        return $this;
    }

    public function sourceUrlBase(): ?string
    {
        return $this->sourceUrlBase;
    }

    public function setSourceUrlBase(string $path): self
    {
        $this->sourceUrlBase = $path;

        return $this;
    }

    public function cacheUrlBase(): ?string
    {
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
        if (! $this->glideServer) {
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

    public function image(string $sourceFileName, ?array $glideParams = null): Image
    {
        $glideParams ??= $this->glideParams();

        $server = $this->glideServer();

        $cacheFileName = $server->makeImage($sourceFileName, $glideParams);

        return new Image(
            $this->sourceUrl($sourceFileName),
            $this->cacheUrl($cacheFileName)
        );
    }

    protected function sourceUrl(string $fileName): string
    {
        return $this->sourceUrlBase() . '/' . $fileName;
    }

    protected function cacheUrl(string $fileName): string
    {
        return $this->cacheUrlBase() . '/' . $fileName;
    }
}
