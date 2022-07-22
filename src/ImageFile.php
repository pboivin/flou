<?php

namespace Pboivin\Flou;

class ImageFile
{
    public function __construct(
        protected string $fileName,
        protected string $path,
        protected string $url,
        protected int $width,
        protected int $height
    )
    {
    }

    public function fileName(): string
    {
        return $this->fileName;
    }

    public function path(): string
    {
        return $this->path;
    }

    public function url(): string
    {
        return $this->url;
    }

    public function width(): int
    {
        return $this->width;
    }

    public function height(): int
    {
        return $this->height;
    }
}
