<?php

namespace Pboivin\Flou;

use Pboivin\Flou\Contracts\ImageMaker;

class ResampledImage
{
    public function __construct(protected ImageFile $source, protected ImageMaker $resampler)
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
