<?php

namespace Pboivin\Flou\Concerns;

use InvalidArgumentException;

trait AcceptsConfig
{
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
}
