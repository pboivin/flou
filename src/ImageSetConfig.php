<?php

namespace Pboivin\Flou;

use InvalidArgumentException;

class ImageSetConfig
{
    protected $preparedConfig;

    public function __construct(protected array $config)
    {
        if (isset($config['sources'])) {
            $this->acceptLegacyConfig($config);
        } else {
            $this->acceptConfig($config);
        }
    }

    protected function acceptLegacyConfig(array $config): void
    {
        $this->preparedConfig = [];
    }

    protected function acceptConfig(array $config): void
    {
        $this->preparedConfig = [];
    }

    public function get(): array
    {
        return $this->preparedConfig;
    }
}
