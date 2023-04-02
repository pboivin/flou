<?php

namespace Pboivin\Flou\Concerns;

use League\Glide\Server;
use League\Glide\ServerFactory;

trait CreatesGlideServer
{
    protected $glideServer;

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
}
