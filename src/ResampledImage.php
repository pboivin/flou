<?php

namespace Pboivin\Flou;

class ResampledImage
{
    public function __construct(protected ImageFile $source, protected ImageFactory $resampler)
    {
    }

    public function source(): ImageFile
    {
        return $this->source;
    }

    public function make(?array $glideParams = null): Image
    {
        return $this->resampler->image($this->source->fileName(), $glideParams);
    }
}
