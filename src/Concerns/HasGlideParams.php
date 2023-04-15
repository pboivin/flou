<?php

namespace Pboivin\Flou\Concerns;

use Pboivin\Flou\Contracts\ImageMaker;

trait HasGlideParams
{
    protected $glideParams;

    public function glideParams(): array
    {
        if (!$this->glideParams) {
            return ImageMaker::DEFAULT_GLIDE_PARAMS;
        }

        return $this->glideParams;
    }

    public function setGlideParams(array $params): self
    {
        $this->glideParams = $params;

        return $this;
    }
}
