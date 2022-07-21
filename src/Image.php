<?php

namespace Pboivin\Flou;

class Image
{
    public function __construct(
        protected string $sourceUrl,
        protected string $cacheUrl
    )
    {
    }

    public function sourceUrl(): string
    {
        return $this->sourceUrl;
    }

    public function url(): string
    {
        return $this->cacheUrl;
    }
}
