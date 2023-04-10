<?php

namespace Pboivin\Flou\Concerns;

use League\Glide\Server;
use League\Glide\Urls\UrlBuilder;
use League\Glide\Urls\UrlBuilderFactory;

trait HasOptionalGlideServer
{
    protected $glideServer;

    protected $glideUrlBuilder;

    protected $glideUrlSignKey;

    public function glideServer(): ?Server
    {
        return $this->glideServer;
    }

    public function setGlideServer(Server $server): self
    {
        $this->glideServer = $server;

        return $this;
    }

    public function glideUrlBuilder(string $urlBase): UrlBuilder
    {
        if (!$this->glideUrlBuilder) {
            $urlBase = rtrim($urlBase, '/');

            $this->glideUrlBuilder = UrlBuilderFactory::create("$urlBase/", $this->glideUrlSignKey);
        }

        return $this->glideUrlBuilder;
    }

    public function setGlideUrlSignKey(string $key): self
    {
        $this->glideUrlSignKey = $key;

        return $this;
    }
}
