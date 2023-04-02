<?php

namespace Pboivin\Flou\Concerns;

trait HasRenderOptions
{
    protected $renderOptions;

    public function setRenderOptions(array $options): void
    {
        $this->renderOptions = $options;
    }
}
